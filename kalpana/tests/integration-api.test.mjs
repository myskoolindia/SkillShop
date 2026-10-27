import test from 'node:test';
import assert from 'node:assert/strict';
import { createApp } from '../src/http.mjs';

const admin = 'integration-test-admin-token-00000000000000';
const profile = { board: 'CBSE', setup: 'infrastructure_materials', layout: 'single', rooms: [{ areaSqFt: 600 }], classes: [6, 7, 8], totalStudents: 120, batchSize: 30, skillIds: ['horticulture', 'robotics-electronics', 'financial-literacy'], budgetMinor: null, goals: '', readiness: {} };
async function fixture(t, publicUrl = 'https://kalpana.example') {
  const app = await createApp({ adminToken: admin, aiMode: 'demo', checkoutMode: 'request', seedDemo: true, publicUrl });
  await new Promise(resolve => app.server.listen(0, '127.0.0.1', resolve));
  t.after(() => app.close());
  const base = `http://127.0.0.1:${app.server.address().port}`;
  const request = async (path, key, body, extra = {}) => {
    const r = await fetch(base + path, { method: body === undefined ? 'GET' : 'POST', headers: { ...(key ? { Authorization: `Bearer ${key}` } : {}), ...(body === undefined ? {} : { 'Content-Type': 'application/json' }), ...extra }, ...(body === undefined ? {} : { body: JSON.stringify(body) }) });
    return { status: r.status, body: await r.json() };
  };
  const a = (await request('/api/admin/integrations', admin, { name: 'CRM A' })).body;
  const b = (await request('/api/admin/integrations', admin, { name: 'CRM B' })).body;
  return { app, request, a, b };
}

test('button launch is single-use, persists results, isolates tenants, and exports exact quotation JSON', async t => {
  const { request, app, a, b } = await fixture(t);
  assert.equal((await request('/api/v1/sessions', admin, {})).status, 401);
  assert.equal((await request('/api/v1/options', a.apiKey)).status, 200);
  const launch = await request('/api/v1/sessions', a.apiKey, { reference: 'school-123' });
  assert.equal(launch.status, 201);
  const { sessionId, launchUrl } = launch.body; const url = new URL(launchUrl);
  assert.equal(url.origin, 'https://kalpana.example');
  assert.ok(!launchUrl.includes(a.apiKey)); assert.equal(launch.body.token, undefined);
  assert.equal((await request(`/api/v1/sessions/${sessionId}/result`, b.apiKey)).status, 404);
  assert.equal((await request(`/api/v1/sessions/${sessionId}/result`, a.apiKey)).body.status, 'pending');
  assert.equal((await request(`/api/v1/sessions/${sessionId}/quote`, a.apiKey)).status, 409);
  const body = { sessionId, launchToken: new URLSearchParams(url.hash.slice(1)).get('launch') };
  const exchange = await request('/api/v1/launch/exchange', null, body);
  assert.equal(exchange.status, 200); assert.equal((await request('/api/v1/launch/exchange', null, body)).status, 401);
  const generated = await request('/api/lab-planner/recommend', exchange.body.token, profile);
  assert.equal(generated.status, 200);
  const result = await request(`/api/v1/sessions/${sessionId}/result`, a.apiKey);
  assert.equal(result.body.status, 'ready'); assert.equal(result.body.reference, 'school-123');
  assert.equal(result.body.amountUnit, 'minor'); assert.deepEqual(result.body.plan, generated.body);
  const quote = await request(`/api/v1/sessions/${sessionId}/quote`, a.apiKey);
  assert.deepEqual(quote.body.quotation, generated.body.quote);
  assert.equal(quote.body.quotation.total, 9750000);
  assert.equal(JSON.stringify(result.body).includes(exchange.body.token), false);
  // A changed intake must never export an earlier quotation as the current result.
  app.service.db.sql.prepare('UPDATE conversations SET state=? WHERE id=?').run('{"labDraft":{}}', exchange.body.conversationId);
  assert.equal((await request(`/api/v1/sessions/${sessionId}/result`, a.apiKey)).body.status, 'needs_refresh');
  assert.equal((await request(`/api/v1/sessions/${sessionId}/quote`, a.apiKey)).status, 409);
});

test('headless plan is JSON and can subsequently be opened; expired/revoked sessions cannot be reused', async t => {
  const { request, app, a } = await fixture(t);
  const bad = await request('/api/v1/plans', a.apiKey, { profile: {} });
  assert.equal(bad.status, 400);
  assert.equal(app.service.db.sql.prepare('SELECT count(*) n FROM integration_sessions').get().n, 0);
  const plan = await request('/api/v1/plans', a.apiKey, { reference: 'ERP-1', profile });
  assert.equal(plan.status, 201); assert.equal(plan.body.status, 'ready');
  const launch = new URL(plan.body.launchUrl);
  const body = { sessionId: plan.body.sessionId, launchToken: new URLSearchParams(launch.hash.slice(1)).get('launch') };
  app.service.db.sql.prepare('UPDATE integration_sessions SET launch_expires_at=0 WHERE id=?').run(body.sessionId);
  assert.equal((await request('/api/v1/launch/exchange', null, body)).status, 401);
  assert.equal((await request(`/api/v1/sessions/${body.sessionId}/result`, a.apiKey)).body.status, 'ready');
  const another = (await request('/api/v1/sessions', a.apiKey, {})).body;
  const exchanged = await request('/api/v1/launch/exchange', null, { sessionId: another.sessionId, launchToken: new URLSearchParams(new URL(another.launchUrl).hash.slice(1)).get('launch') });
  assert.equal((await request(`/api/admin/integrations/${a.client.id}/revoke`, admin, {})).status, 200);
  assert.equal((await request('/api/v1/options', a.apiKey)).status, 401);
  assert.equal((await request('/api/conversation', exchanged.body.token)).status, 401);
});

test('cross-origin browser calls cannot use server integration keys', async t => {
  const { request, a } = await fixture(t);
  assert.equal((await request('/api/v1/sessions', a.apiKey, {}, { Origin: 'https://untrusted.example' })).status, 403);
});

test('shared integration Postman collection runs its launch, JSON generation, result and revocation workflow', async t => {
  const { request } = await fixture(t);
  const { readFile } = await import('node:fs/promises');
  const { runInNewContext } = await import('node:vm');
  const collection = JSON.parse(await readFile(new URL('../postman/Kalpana_Integration.postman_collection.json', import.meta.url), 'utf8'));
  const vars = new Map([['adminToken', admin]]);
  const interpolate = text => text.replace(/\{\{([^}]+)\}\}/g, (_, key) => vars.get(key) ?? '');
  const items = collection.item.flatMap(item => item.item || [item]);
  for (const item of items) {
    const r = item.request;
    const result = await request(interpolate(r.url.replace('{{baseUrl}}', '')), interpolate(r.auth.bearer[0].value), r.body ? JSON.parse(interpolate(r.body.raw)) : undefined);
    assert.ok(result.status < 300, `${item.name}: ${JSON.stringify(result.body)}`);
    const pm = { collectionVariables: vars, response: { code: result.status, json: () => result.body, to: { have: { status: value => assert.equal(result.status, value) } } }, test: (_name, fn) => fn(), expect: value => ({ to: { eql: expected => assert.equal(value, expected), be: { a: type => assert.equal(typeof value, type) } } }) };
    for (const event of item.event || []) runInNewContext(event.script.exec.join('\n'), { pm });
  }
  assert.equal(items.length, 9);
});

test('launch links include the PUBLIC_URL path when Kalpana is served under a host site', async t => {
  const { request, a } = await fixture(t, 'https://shop.example/kalpana');
  const launch = (await request('/api/v1/sessions', a.apiKey, {})).body;
  const url = new URL(launch.launchUrl);
  assert.equal(`${url.origin}${url.pathname}`, 'https://shop.example/kalpana/lab-planner');
  assert.equal(launch.resultUrl, `https://shop.example/kalpana/api/v1/sessions/${launch.sessionId}/result`);
});
