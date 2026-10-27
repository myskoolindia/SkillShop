import test from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { runInNewContext } from 'node:vm';
import { createApp } from '../src/http.mjs';

test('exported Postman collection executes all requests and assertions over HTTP', async t => {
  const adminToken = 'postman-kalpana-test-admin-000000000000000000';
  const app = await createApp({ adminToken, aiMode: 'demo', checkoutMode: 'demo', seedDemo: true });
  await new Promise(resolve => app.server.listen(0, '127.0.0.1', resolve)); t.after(() => app.close());
  const environment = new Map([['baseUrl', `http://127.0.0.1:${app.server.address().port}`], ['adminToken', adminToken]]);
  const vars = new Map(); const interpolate = value => value.replace(/\{\{([^}]+)\}\}/g, (_, key) => String(environment.get(key) ?? vars.get(key) ?? ''));
  const collection = JSON.parse(await readFile(new URL('../postman/Kalpana.postman_collection.json', import.meta.url), 'utf8'));
  let requests = 0;
  for (const folder of collection.item) for (const item of folder.item) {
    let skip = false;
    const pm = {
      collectionVariables: vars, execution: { skipRequest() { skip = true; } },
      test(name, fn) { try { fn(); } catch (error) { throw Error(`${item.name}: ${name}`, { cause: error }); } },
      expect(actual) { return { to: { eql: expected => assert.equal(actual, expected) } }; },
    };
    for (const event of item.event.filter(e => e.listen === 'prerequest')) runInNewContext(event.script.exec.join('\n'), { pm }, { timeout: 1000 });
    if (skip) continue;
    const r = item.request; const auth = r.auth.type === 'bearer' ? interpolate(r.auth.bearer[0].value) : undefined;
    const response = await fetch(interpolate(r.url), { method: r.method, headers: { 'Content-Type': 'application/json', ...(auth ? { Authorization: `Bearer ${auth}` } : {}) }, ...(r.body ? { body: interpolate(r.body.raw) } : {}) });
    const result = await response.json();
    pm.response = { code: response.status, json: () => result, to: { have: { status: expected => assert.equal(response.status, expected, JSON.stringify(result)) } } };
    for (const event of item.event.filter(e => e.listen === 'test')) runInNewContext(event.script.exec.join('\n'), { pm }, { timeout: 1000 });
    requests++;
  }
  assert.equal(requests, 24);
});
