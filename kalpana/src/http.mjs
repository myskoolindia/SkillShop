import { createServer } from 'node:http';
import { readFile } from 'node:fs/promises';
import { createService } from './service.mjs';
import { createIntegration } from './integration.mjs';
import { assert, bearer, equal, readBody } from './core.mjs';

const assets = new Map([
  ['/', ['index.html', 'text/html']], ['/admin', ['admin.html', 'text/html']], ['/checkout', ['checkout.html', 'text/html']],
  ['/styles.css', ['styles.css', 'text/css']], ['/app.js', ['app.js', 'text/javascript']],
  ['/admin.js', ['admin.js', 'text/javascript']], ['/checkout.js', ['checkout.js', 'text/javascript']], ['/widget.js', ['widget.js', 'text/javascript']],
  ['/paths.js', ['paths.js', 'text/javascript']], ['/voice.js', ['voice.js', 'text/javascript']], ['/mascot.js', ['mascot.js', 'text/javascript']], ['/diagrams.js', ['diagrams.js', 'text/javascript']], ['/lab-planner', ['lab-planner.html', 'text/html']], ['/lab-planner.js', ['lab-planner.js', 'text/javascript']], ['/lab-planner.css', ['lab-planner.css', 'text/css']],
]);

export async function createApp(config = {}) {
  config = { aiMode: 'gemini', checkoutMode: 'request', ...config };
  assert(['gemini', 'demo'].includes(config.aiMode), 400, 'AI_MODE must be gemini or demo');
  assert(['request', 'demo'].includes(config.checkoutMode), 400, 'CHECKOUT_MODE must be request or demo; live provider is not selected yet');
  assert(config.checkoutMode !== 'demo' || config.seedDemo, 400, 'Demo checkout requires SEED_DEMO=true');
  assert(config.adminToken?.length >= 32 && !config.adminToken.startsWith('REPLACE_'), 400, 'Set a random ADMIN_TOKEN with at least 32 characters');
  const embedOrigins = (config.embedOrigins || '').split(/\s+/).filter(Boolean).map(origin => {
    const url = new URL(origin);
    assert(['http:', 'https:'].includes(url.protocol) && url.origin === origin, 400, 'EMBED_ORIGINS must contain exact HTTP(S) origins');
    return url.origin;
  });
  const service = await createService(config);
  if (config.checkoutMode === 'demo' && service.db.catalog() && !service.db.catalog().demo) {
    service.close(); assert(false, 409, 'The saved catalog is real; restart in request mode, not demo checkout mode');
  }
  const integration = createIntegration(service, config);
  const limits = new Map();
  const limited = (key, maximum) => {
    const time = Date.now();
    if (limits.size > 5000) for (const [key, value] of limits) if (value.until < time) limits.delete(key);
    const value = limits.get(key);
    if (!value || value.until < time) { limits.set(key, { count: 1, until: time + 60000 }); return; }
    assert(++value.count <= maximum, 429, 'Too many requests; please wait one minute');
  };
  const server = createServer(async (req, res) => {
    const json = (code, value) => { res.writeHead(code, { 'Content-Type': 'application/json; charset=utf-8' }); res.end(JSON.stringify(value)); };
    res.setHeader('Cache-Control', 'no-store'); res.setHeader('X-Content-Type-Options', 'nosniff'); res.setHeader('Referrer-Policy', 'no-referrer');
    try {
      assert(req.url.startsWith('/') && !req.url.startsWith('//') && !req.url.includes('\\'), 400, 'Invalid URL');
      const url = new URL(req.url, 'http://kalpana.local');
      const base = url.pathname === '/kalpana' || url.pathname.startsWith('/kalpana/') ? '/kalpana' : '';
      const path = url.pathname.slice(base.length) || '/';
      const adminPath = path === '/admin' || path.startsWith('/api/admin/');
      res.setHeader('Content-Security-Policy', `default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; connect-src 'self'; frame-ancestors ${adminPath ? "'none'" : "'self' " + embedOrigins.join(' ')}; base-uri 'none'; form-action 'self'; object-src 'none'`);
      if (req.method === 'POST') {
        const origin = req.headers.origin;
        assert(!origin || new URL(origin).host === req.headers.host, 403, 'Cross-origin API calls are not allowed; use the widget iframe');
        assert(req.headers['sec-fetch-site'] !== 'cross-site', 403, 'Cross-site requests are not allowed');
        limited(`ip:${req.socket.remoteAddress}`, 120);
      }
      const supplied = bearer(req);
      if (path === '/api/v1/launch/exchange' && req.method === 'POST') return json(200, integration.exchange(await readBody(req)));
      if (path.startsWith('/api/v1/')) {
        const client = integration.authenticate(supplied);
        limited(`integration:${client.id}`, 60);
        if (path === '/api/v1/options' && req.method === 'GET') return json(200, service.labOptions());
        if (path === '/api/v1/sessions' && req.method === 'POST') return json(201, integration.launch(client, await readBody(req)));
        if (path === '/api/v1/plans' && req.method === 'POST') { limited(`integration-ai:${client.id}`, 15); return json(201, await integration.plan(client, await readBody(req))); }
        const result = /^\/api\/v1\/sessions\/([\w-]+)\/(result|quote)$/.exec(path);
        if (result && req.method === 'GET') {
          const value = integration.result(client, result[1]);
          if (result[2] === 'quote') {
            assert(value.status === 'ready', 409, `Quotation is ${value.status}; fetch the session result for details`);
            return json(200, { apiVersion: value.apiVersion, sessionId: value.sessionId, reference: value.reference, amountUnit: value.amountUnit, quotation: value.quotation });
          }
          return json(200, value);
        }
      }
      if (path === '/api/health' && req.method === 'GET') return json(200, service.health());
      if (path === '/api/catalog' && req.method === 'GET') return json(200, service.catalog());
      if (path === '/api/lab-planner/options' && req.method === 'GET') return json(200, service.labOptions());
      if (path === '/api/lab-planner/recommend' && req.method === 'POST') { limited(`ai:${supplied?.slice(0, 64)}`, 15); return json(200, await service.labPlan(supplied, await readBody(req))); }
      if (path === '/api/lab-planner/compare-tiers' && req.method === 'POST') { limited(`compare:${supplied?.slice(0, 64)}`, 30); return json(200, service.labCompareTiers(supplied, await readBody(req))); }
      if (path === '/api/lab-planner/budget-options' && req.method === 'POST') { limited(`budget:${supplied?.slice(0, 64)}`, 30); return json(200, service.labBudgetOptions(supplied, await readBody(req))); }
      if (path === '/api/lab-planner/interpret' && req.method === 'POST') { limited(`ai:${supplied?.slice(0, 64)}`, 15); return json(200, await service.labInterpret(supplied, await readBody(req))); }
      if (path === '/api/conversations' && req.method === 'POST') return json(201, service.newConversation());
      if (path === '/api/conversation' && req.method === 'GET') return json(200, service.conversation(supplied));
      if (path === '/api/chat' && req.method === 'POST') { limited(`ai:${supplied?.slice(0, 64)}`, 15); return json(200, await service.chat(supplied, await readBody(req))); }
      if (path === '/api/quotes' && req.method === 'POST') return json(201, service.quote(supplied, await readBody(req)));
      const quote = /^\/api\/quotes\/([\w-]+)$/.exec(path);
      if (quote && req.method === 'GET') return json(200, { quote: service.getQuote(supplied, quote[1]) });
      if (path === '/api/orders' && req.method === 'POST') return json(201, service.order(supplied, await readBody(req)));
      const order = /^\/api\/orders\/([\w-]+)$/.exec(path);
      if (order && req.method === 'GET') return json(200, service.getOrder(supplied, order[1]));
      const complete = /^\/api\/orders\/([\w-]+)\/demo-complete$/.exec(path);
      if (complete && req.method === 'POST') return json(200, service.completeDemo(supplied, complete[1], await readBody(req)));
      if (path.startsWith('/api/admin/')) {
        assert(equal(supplied, config.adminToken), 401, 'Administrator token required');
        if (path === '/api/admin/integrations' && req.method === 'GET') return json(200, { clients: integration.clients() });
        if (path === '/api/admin/integrations' && req.method === 'POST') return json(201, integration.createClient(await readBody(req)));
        const revoke = /^\/api\/admin\/integrations\/([\w-]+)\/revoke$/.exec(path);
        if (revoke && req.method === 'POST') return json(200, integration.revoke(revoke[1]));
        if (path === '/api/admin/documents' && req.method === 'GET') return json(200, { documents: service.documents() });
        if (path === '/api/admin/documents' && req.method === 'POST') return json(201, { document: await service.addDocument(await readBody(req)) });
        const doc = /^\/api\/admin\/documents\/([\w-]+)$/.exec(path);
        if (doc && req.method === 'GET') { const value = service.db.getDocument(doc[1]); assert(value, 404, 'Document not found'); return json(200, { document: value }); }
        if (path === '/api/admin/catalog' && req.method === 'POST') return json(201, { catalog: service.publish(await readBody(req)) });
        if (path === '/api/admin/catalog' && req.method === 'GET') return json(200, { catalog: service.db.catalog() });
        if (path === '/api/admin/orders' && req.method === 'GET') return json(200, { orders: service.adminOrders() });
        if (path === '/api/admin/skillvation' && req.method === 'GET') return json(200, { enabled: config.catalogSource === 'skillvation', autoPublish: config.skillvationAutoPublish !== false, intervalMinutes: config.skillvationSyncMinutes || null, status: service.syncStatus(), staged: !!service.db.setting('skillvation.draft') });
        if (path === '/api/admin/skillvation/sync' && req.method === 'POST') { const body = await readBody(req); return json(200, { status: await service.syncSkillvation(body.publish === undefined ? {} : { publish: body.publish === true }) }); }
        if (path === '/api/admin/skillvation/publish' && req.method === 'POST') return json(201, { catalog: service.publishSkillvationDraft() });
        if (path === '/api/admin/sample-catalog' && req.method === 'GET') return json(200, JSON.parse(await readFile(new URL('../examples/catalog.json', import.meta.url), 'utf8')));
      }
      if (req.method === 'GET' && assets.has(path)) {
        const [file, type] = assets.get(path);
        if (path === '/widget.js') { res.removeHeader('Content-Security-Policy'); res.setHeader('Cross-Origin-Resource-Policy', 'cross-origin'); }
        res.writeHead(200, { 'Content-Type': `${type}; charset=utf-8` });
        let content = await readFile(new URL(`../public/${file}`, import.meta.url), 'utf8');
        if (type === 'text/html' && base) content = content.replace(/(href|src)="\/(?!\/)/g, `$1="${base}/`);
        return res.end(content);
      }
      return json(404, { error: 'Endpoint not found' });
    } catch (error) {
      if (!res.headersSent) json(error.status || 500, { error: error.status ? error.message : 'Internal server error' });
      else res.destroy();
      if (!error.status) console.error('Kalpana request failed:', error.message);
    }
  });
  server.headersTimeout = 15000; server.requestTimeout = 60000;
  // Keep the shop catalog fresh in the background; the first sync starts immediately.
  let timer = null;
  if (config.catalogSource === 'skillvation' && config.skillvationSyncOnStart !== false) {
    void service.syncSkillvation();
    const minutes = config.skillvationSyncMinutes || 60;
    timer = setInterval(() => void service.syncSkillvation(), minutes * 60000); timer.unref();
  }
  return { server, service, async close() { clearInterval(timer); await new Promise((resolve, reject) => server.close(error => error ? reject(error) : resolve())); service.close(); } };
}
