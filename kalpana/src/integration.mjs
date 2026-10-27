import { randomUUID } from 'node:crypto';
import { assert, hash, token, now, string } from './core.mjs';

// Integrator keys stay on the calling server. Browser launch tokens are single-use.
export function createIntegration(service, config) {
  const db = service.db;
  // PUBLIC_URL may include a path when Kalpana is served under a host site, e.g. https://shop.example/kalpana/.
  const publicUrl = new URL(config.publicUrl || 'http://localhost:8787');
  if (!publicUrl.pathname.endsWith('/')) publicUrl.pathname += '/';
  assert(['http:', 'https:'].includes(publicUrl.protocol) && !publicUrl.search && !publicUrl.hash && !publicUrl.username && !publicUrl.password, 400, 'PUBLIC_URL must be an HTTP(S) URL without query, fragment or credentials');
  const clientView = row => ({ id: row.id, name: row.name, enabled: !!row.enabled, createdAt: row.created_at });
  const authenticate = key => {
    assert(typeof key === 'string' && key.length === 64, 401, 'Integration API key required');
    const row = db.sql.prepare('SELECT * FROM integration_clients WHERE key_hash=? AND enabled=1').get(hash(key));
    assert(row, 401, 'Invalid or revoked integration API key');
    return row;
  };
  function createSession(client, body = {}) {
    const reference = body.reference === undefined ? null : string(body.reference, 'reference', 200);
    return db.transaction(() => {
      const conversation = service.newConversation(); const launch = token(); const id = randomUUID();
      const launchExpires = Date.now() + 10 * 60 * 1000;
      db.sql.prepare('INSERT INTO integration_sessions VALUES (?,?,?,?,?,?,?)').run(id, client.id, conversation.conversationId, reference, hash(launch), launchExpires, now());
      const url = new URL('lab-planner', publicUrl); url.searchParams.set('session', id); url.hash = `launch=${launch}`;
      return { conversation, response: { sessionId: id, reference, launchUrl: url.href, launchExpiresAt: new Date(launchExpires).toISOString(), expiresAt: conversation.expiresAt, resultUrl: new URL(`api/v1/sessions/${id}/result`, publicUrl).href, quoteUrl: new URL(`api/v1/sessions/${id}/quote`, publicUrl).href } };
    });
  }
  function ownedSession(client, id) {
    const row = db.sql.prepare('SELECT * FROM integration_sessions WHERE id=? AND client_id=?').get(id, client.id);
    assert(row, 404, 'Session not found'); return row;
  }
  return {
    authenticate,
    clients() { return db.sql.prepare('SELECT * FROM integration_clients ORDER BY created_at DESC').all().map(clientView); },
    createClient(body) {
      const name = string(body.name, 'integration name', 100); const key = token(); const id = randomUUID();
      db.sql.prepare('INSERT INTO integration_clients VALUES (?,?,?,1,?)').run(id, name, hash(key), now());
      db.audit('integration.create', id);
      return { client: clientView(db.sql.prepare('SELECT * FROM integration_clients WHERE id=?').get(id)), apiKey: key };
    },
    revoke(id) {
      assert(db.sql.prepare('UPDATE integration_clients SET enabled=0 WHERE id=?').run(id).changes, 404, 'Integration not found');
      // Revocation also invalidates browser conversations and unredeemed launch links.
      db.sql.prepare('UPDATE conversations SET expires_at=0 WHERE id IN (SELECT conversation_id FROM integration_sessions WHERE client_id=?)').run(id);
      db.audit('integration.revoke', id); return { revoked: true };
    },
    launch(client, body) { return createSession(client, body).response; },
    exchange(body) {
      const secret = string(body.launchToken, 'launch token', 64, 64);
      return db.transaction(() => {
        const row = db.sql.prepare('SELECT s.*,c.expires_at FROM integration_sessions s JOIN integration_clients i ON i.id=s.client_id JOIN conversations c ON c.id=s.conversation_id WHERE s.id=? AND s.launch_hash=? AND i.enabled=1').get(string(body.sessionId, 'session ID', 100), hash(secret));
        assert(row && row.launch_expires_at > Date.now() && row.expires_at > Date.now(), 401, 'Launch link expired or already used; request a new session');
        const browserToken = token();
        db.sql.prepare('UPDATE integration_sessions SET launch_hash=NULL WHERE id=?').run(row.id);
        db.sql.prepare('UPDATE conversations SET token_hash=? WHERE id=?').run(hash(browserToken), row.conversation_id);
        return { sessionId: row.id, conversationId: row.conversation_id, token: browserToken, expiresAt: new Date(row.expires_at).toISOString() };
      });
    },
    result(client, id) {
      const row = ownedSession(client, id);
      const result = service.integrationResult(row.conversation_id);
      return { apiVersion: 'v1', sessionId: row.id, reference: row.reference, amountUnit: 'minor', ...result };
    },
    async plan(client, body) {
      assert(body.profile && typeof body.profile === 'object' && !Array.isArray(body.profile), 400, 'Provide a profile; see /api/v1/options');
      const { conversation, response } = createSession(client, body);
      try { await service.labPlan(conversation.token, body.profile); }
      catch (error) {
        db.transaction(() => {
          db.sql.prepare('DELETE FROM integration_sessions WHERE id=?').run(response.sessionId);
          db.sql.prepare('DELETE FROM conversations WHERE id=?').run(conversation.conversationId);
        });
        throw error;
      }
      return { ...response, ...this.result(client, response.sessionId) };
    },
  };
}
