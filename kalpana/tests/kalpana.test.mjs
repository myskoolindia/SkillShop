import test from 'node:test';
import assert from 'node:assert/strict';
import { readFile, mkdtemp, rm } from 'node:fs/promises';
import { tmpdir } from 'node:os';
import { join } from 'node:path';
import { createService } from '../src/service.mjs';
import { createApp } from '../src/http.mjs';
import { calculate, recommend } from '../src/catalog.mjs';
import { validateIntent, Gemini } from '../src/gemini.mjs';
import { retrieve } from '../src/knowledge.mjs';

const adminToken = 'kalpana-test-administrator-token-000000000000';
const defaults = { aiMode: 'demo', checkoutMode: 'demo', seedDemo: true, adminToken };
const customer = { name: 'Test Customer', email: 'customer@example.test' };
const payload = (extra = {}) => ({ answer: 'The catalog offers a suitable learning package.', budgetMinor: 3000000, learners: 30, currency: 'INR', features: ['robotics'], packageIds: [], unsupportedRequirements: [], questions: [], sourceIds: ['demo-products'], ...extra });
function fakeGemini(value, status = 200) {
  return async (url, options) => Response.json({ candidates: [{ finishReason: 'STOP', content: { parts: [{ text: JSON.stringify(value) }] } }] }, { status });
}
async function fixture(t, extra = {}) { const service = await createService({ ...defaults, ...extra }); t.after(() => service.close()); return service; }

test('sample documents and catalog are seeded with traceable immutable sources', async t => {
  const s = await fixture(t); assert.equal(s.catalog().demo, true); assert.equal(s.documents().length, 3);
  assert.ok(s.catalog().sourceDocuments.every(d => d.sha256.length === 64));
  await assert.rejects(s.addDocument({ id: 'demo-pricing', title: 'Changed', content: 'Changed price' }), /immutable/);
});
test('budget recommendations include every configured charge and enough capacity', async t => {
  const s = await fixture(t); const c = s.newConversation();
  const reply = await s.chat(c.token, { message: 'I need robotics for 30 students under INR 30,000.' });
  assert.equal(reply.constraints.budgetMinor, 3000000); assert.equal(reply.constraints.learners, 30);
  assert.equal(reply.quotes.length, 1); assert.equal(reply.quotes[0].total, 2500000);
  assert.equal(reply.quotes[0].purchasable, true); assert.ok(reply.sources.length > 0);
  const more = await s.chat(c.token, { message: 'Now I need 60 students', budgetMinor: 6000000 });
  assert.equal(more.constraints.learners, 60); assert.ok(more.quotes.every(q => q.capacity >= 60));
});
test('customization chooses a compatible addon instead of inventing a package', async t => {
  const s = await fixture(t); const c = s.newConversation();
  const reply = await s.chat(c.token, { message: 'Robotics and AI for 30 students under INR 40,000' });
  assert.equal(reply.quotes[0].total, 3100000);
  assert.deepEqual(reply.quotes[0].lines.map(l => l.productId), ['robotics-classroom', 'ai-activities']);
  assert.throws(() => s.quote(c.token, { items: [{ productId: 'digital-foundations', quantity: 1 }, { productId: 'ai-activities', quantity: 1 }] }), /compatible/);
  assert.throws(() => s.quote(c.token, { items: [{ productId: 'digital-foundations', quantity: 1 }, { productId: 'robotics-classroom', quantity: 1 }] }), /one core/);
});
test('no fitting budget yields a visibly over-budget alternative that cannot be ordered', async t => {
  const s = await fixture(t); const c = s.newConversation();
  const reply = await s.chat(c.token, { message: 'Robotics for 30 students under INR 10,000' });
  assert.equal(reply.quotes.length, 0); assert.equal(reply.nearest.overBudget, 1500000); assert.equal(reply.nearest.purchasable, false);
  assert.throws(() => s.order(c.token, { quoteId: reply.nearest.id, customer, confirm: true }), /requirements/);
});
test('removing features or discounts outside catalog rules requires team review', async t => {
  const s = await fixture(t); const c = s.newConversation();
  const reply = await s.chat(c.token, { message: 'Give me a discount on robotics for 30 students under INR 50,000' });
  assert.ok(reply.warnings.length); assert.ok(reply.quotes.every(q => !q.purchasable));
});
test('tax and delivery are calculated using integers and included in budget checks', async t => {
  const s = await fixture(t); const catalog = { ...s.db.catalog(), taxBasisPoints: 1800, deliveryAmount: 10000 };
  const quote = calculate(catalog, [{ productId: 'robotics-classroom', quantity: 1 }]);
  assert.equal(quote.subtotal, 2500000); assert.equal(quote.tax, 450000); assert.equal(quote.total, 2960000);
  assert.equal(recommend(catalog, { learners: 30, features: ['robotics'], budgetMinor: 2950000 }).matches.length, 0);
});
test('custom quote prices ignore client amounts and quantities have strict limits', async t => {
  const s = await fixture(t); const c = s.newConversation();
  const quote = s.quote(c.token, { items: [{ productId: 'robotics-classroom', quantity: 1, unitAmount: 1 }], total: 1 });
  assert.equal(quote.total, 2500000);
  for (const quantity of [0, 101, -1, 1.5, '1']) assert.throws(() => s.quote(c.token, { items: [{ productId: 'robotics-classroom', quantity }] }), /quantity/);
  assert.throws(() => s.order(c.token, { quoteId: quote.id, total: 1, customer, confirm: true }), /server/);
});
test('required addons must cover all package units before a custom quote is purchasable', async t => {
  const s = await fixture(t); const c = s.newConversation();
  await s.chat(c.token, { message: 'I need coding and training for 60 students under INR 100,000' });
  const quote = s.quote(c.token, { items: [{ productId: 'digital-foundations', quantity: 2 }, { productId: 'teacher-training', quantity: 1 }] });
  assert.equal(quote.purchasable, false); assert.ok(quote.missingFeatures.includes('training'));
});
test('quotes and orders are private to a conversation, not enumerable by another visitor', async t => {
  const s = await fixture(t); const a = s.newConversation(); const b = s.newConversation();
  const quote = s.quote(a.token, { items: [{ productId: 'digital-foundations', quantity: 1 }] });
  assert.throws(() => s.getQuote(b.token, quote.id), /not found/);
  assert.throws(() => s.order(b.token, { quoteId: quote.id, customer, confirm: true }), /not found/);
  const order = s.order(a.token, { quoteId: quote.id, customer, confirm: true });
  assert.throws(() => s.getOrder(b.token, order.order.id), /not found/);
  assert.throws(() => s.completeDemo(b.token, order.order.id, { confirm: true }), /not found/);
});
test('purchase requires confirmation and retries cannot create duplicate orders', async t => {
  const s = await fixture(t); const c = s.newConversation();
  const quote = s.quote(c.token, { items: [{ productId: 'digital-foundations', quantity: 1 }] });
  assert.throws(() => s.order(c.token, { quoteId: quote.id, customer }), /confirmation/);
  const a = s.order(c.token, { quoteId: quote.id, customer, confirm: true });
  const b = s.order(c.token, { quoteId: quote.id, customer, confirm: true });
  assert.equal(a.order.id, b.order.id); assert.equal(a.order.status, 'demo_pending');
  const complete = s.completeDemo(c.token, a.order.id, { confirm: true }); assert.equal(complete.order.status, 'demo_completed');
  assert.notEqual(complete.order.status, 'paid');
});
test('provider-undecided mode stores requests and never offers a fake payment link', async t => {
  const s = await fixture(t, { checkoutMode: 'request' }); const c = s.newConversation();
  const quote = s.quote(c.token, { items: [{ productId: 'digital-foundations', quantity: 1 }] });
  const result = s.order(c.token, { quoteId: quote.id, customer, confirm: true });
  assert.equal(result.order.status, 'requested'); assert.equal(result.checkout, null);
  assert.throws(() => s.completeDemo(c.token, result.order.id, { confirm: true }), /not enabled/);
});
test('expired quotes and superseded catalog revisions cannot initiate checkout', async t => {
  const s = await fixture(t); const c = s.newConversation();
  const quote = s.quote(c.token, { items: [{ productId: 'digital-foundations', quantity: 1 }] });
  s.db.sql.prepare('UPDATE quotes SET expires_at=0 WHERE id=?').run(quote.id);
  assert.throws(() => s.order(c.token, { quoteId: quote.id, customer, confirm: true }), /expired/);
  const another = s.quote(c.token, { items: [{ productId: 'digital-foundations', quantity: 1 }] });
  const next = { ...s.db.catalog(), revision: 'demo-v2' }; s.publish(next);
  assert.throws(() => s.order(c.token, { quoteId: another.id, customer, confirm: true }), /Pricing changed/);
  assert.throws(() => s.publish({ ...next, deliveryAmount: 1000 }), /immutable/);
});
test('changed customer requirements invalidate earlier quote snapshots', async t => {
  const s = await fixture(t); const c = s.newConversation();
  const before = await s.chat(c.token, { message: 'Robotics for 30 students under INR 30,000' });
  await s.chat(c.token, { message: 'Now I need 60 students under INR 60,000' });
  assert.throws(() => s.order(c.token, { quoteId: before.quotes[0].id, customer, confirm: true }), /Requirements changed/);
});
test('training alone does not accidentally require the AI add-on', async t => {
  const s = await fixture(t); const c = s.newConversation();
  const reply = await s.chat(c.token, { message: 'Coding and training for 30 students under INR 20,000' });
  assert.ok(!reply.constraints.features.includes('ai'));
  assert.equal(reply.quotes[0].total, 1700000);
});
test('unpublished source documents do not enter customer retrieval', async t => {
  const s = await fixture(t); await s.addDocument({ title: 'Private draft', content: 'Private experimental discount and secret pricing', id: 'private-draft' });
  const chunks = retrieve(s.db, s.db.catalog(), 'Private experimental discount');
  assert.ok(!chunks.some(c => c.id === 'private-draft'));
});
test('PDF attachment calls Gemini extraction but never publishes an inferred price', async t => {
  let request;
  const s = await fixture(t, { aiMode: 'gemini', geminiKey: 'test', geminiFetch: async (_url, options) => {
    request = JSON.parse(options.body); return fakeGemini({ title: 'Uploaded price sheet', content: 'Unreviewed price: INR 1.' })();
  } });
  const pdf = Buffer.from('%PDF-1.4\nSample test document').toString('base64');
  const doc = await s.addDocument({ title: 'Uploaded price sheet', pdfBase64: pdf });
  assert.equal(doc.reviewRequired, true); assert.equal(doc.mime_type, 'application/pdf');
  assert.equal(request.contents[0].parts[1].inlineData.mimeType, 'application/pdf');
  assert.equal(s.db.catalog().products[0].unitAmount, 1200000);
  assert.ok(!retrieve(s.db, s.db.catalog(), 'price').some(chunk => chunk.id === doc.id));
});
test('Gemini transport uses server-side key, strict schema and document context', async t => {
  let called;
  const s = await fixture(t, { aiMode: 'gemini', geminiKey: 'test-key', geminiModel: 'configured-model', geminiFetch: async (url, options) => { called = { url, options, payload: JSON.parse(options.body) }; return fakeGemini(payload())(); } });
  const c = s.newConversation(); const response = await s.chat(c.token, { message: 'Robotics for our school' });
  assert.equal(response.aiMode, 'gemini'); assert.equal(response.quotes[0].total, 2500000);
  assert.ok(called.url.endsWith('/configured-model:generateContent')); assert.ok(!called.url.includes('test-key'));
  assert.equal(called.options.headers['x-goog-api-key'], 'test-key'); assert.equal(called.payload.generationConfig.responseMimeType, 'application/json');
  assert.ok(called.payload.generationConfig.responseJsonSchema.required.includes('sourceIds'));
  assert.ok(called.payload.systemInstruction.parts[0].text.includes('untrusted data'));
});
test('missing key, provider failures and blocked model output never silently become demo answers', async t => {
  const missing = await fixture(t, { aiMode: 'gemini', geminiKey: '' }); const c = missing.newConversation();
  await assert.rejects(missing.chat(c.token, { message: 'Hello' }), /GEMINI_API_KEY/);
  const failure = new Gemini({ geminiKey: 'test', geminiFetch: fakeGemini(payload(), 429) });
  await assert.rejects(failure.generate('system', [{ text: 'hi' }], {}), /HTTP 429/);
  const blocked = new Gemini({ geminiKey: 'test', geminiFetch: async () => Response.json({ candidates: [{ finishReason: 'SAFETY' }] }) });
  await assert.rejects(blocked.generate('system', [{ text: 'hi' }], {}), /could not complete/);
  assert.equal(missing.db.sql.prepare('SELECT count(*) AS n FROM quotes').get().n, 0);
});
test('unknown model products and citations fail closed; price-like model prose is replaced', async t => {
  const s = await fixture(t); const catalog = s.db.catalog(); const sources = retrieve(s.db, catalog, 'robotics');
  assert.throws(() => validateIntent(payload({ packageIds: ['imaginary-package'] }), catalog, sources), /unknown package/);
  assert.throws(() => validateIntent(payload({ sourceIds: ['fabricated-document'] }), catalog, sources), /unknown source/);
  const value = validateIntent(payload({ answer: 'Buy now for INR 1 at https://evil.example' }), catalog, sources);
  assert.ok(!value.answer.includes('evil.example')); assert.ok(!value.answer.includes('INR 1'));
});
test('concurrent requests in one conversation do not overwrite newer constraints', async t => {
  let release; let started;
  const ready = new Promise(resolve => { started = resolve; });
  const s = await fixture(t, { aiMode: 'gemini', geminiKey: 'test', geminiFetch: async () => { started(); await new Promise(resolve => { release = resolve; }); return fakeGemini(payload())(); } });
  const c = s.newConversation(); const first = s.chat(c.token, { message: 'robotics' }); await ready;
  await assert.rejects(s.chat(c.token, { message: 'another request' }), /wait/); release(); await first;
  assert.equal(s.conversation(c.token).messages.length, 2);
});
test('documents, conversation history and purchase requests survive server restart', async () => {
  const directory = await mkdtemp(join(tmpdir(), 'kalpana-test-')); const database = join(directory, 'kalpana.sqlite');
  try {
    let s = await createService({ ...defaults, database }); const c = s.newConversation();
    const result = await s.chat(c.token, { message: 'Coding for 30 students under INR 20,000' });
    const order = s.order(c.token, { quoteId: result.quotes[0].id, customer, confirm: true }); s.close();
    s = await createService({ ...defaults, database });
    assert.equal(s.conversation(c.token).messages.length, 2); assert.equal(s.getOrder(c.token, order.order.id).order.status, 'demo_pending'); s.close();
  } finally { await rm(directory, { recursive: true, force: true }); }
});

test('HTTP integration, admin authorization, embedding and purchase request flow', async t => {
  const app = await createApp({ ...defaults, embedOrigins: 'http://localhost:8080' });
  await new Promise(resolve => app.server.listen(0, '127.0.0.1', resolve)); t.after(() => app.close());
  const base = `http://127.0.0.1:${app.server.address().port}`;
  async function call(path, method = 'GET', data, auth, headers = {}) {
    const response = await fetch(base + path, { method, headers: { 'Content-Type': 'application/json', ...(auth ? { Authorization: `Bearer ${auth}` } : {}), ...headers }, ...(data ? { body: JSON.stringify(data) } : {}) });
    return { status: response.status, body: await response.json() };
  }
  assert.equal((await call('/api/admin/orders')).status, 401);
  assert.equal((await call('/api/admin/documents', 'GET', undefined, adminToken)).body.documents.length, 3);
  const home = await fetch(base); assert.equal(home.status, 200); assert.match(await home.text(), /Your kind of package/);
  assert.match(home.headers.get('content-security-policy'), /frame-ancestors 'self' http:\/\/localhost:8080/);
  const admin = await fetch(base + '/admin'); assert.match(admin.headers.get('content-security-policy'), /frame-ancestors 'none'/);
  const widget = await fetch(base + '/widget.js'); assert.equal(widget.status, 200); assert.equal(widget.headers.get('cross-origin-resource-policy'), 'cross-origin');
  assert.equal((await call('/api/conversations', 'POST', {}, undefined, { Origin: 'https://evil.example' })).status, 403);
  const session = (await call('/api/conversations', 'POST', {})).body;
  const response = await call('/api/chat', 'POST', { message: 'Robotics for 30 students under INR 30,000' }, session.token);
  assert.equal(response.status, 200); const quote = response.body.quotes[0];
  const order = await call('/api/orders', 'POST', { quoteId: quote.id, customer, confirm: true }, session.token);
  assert.equal(order.status, 201); assert.equal(order.body.order.status, 'demo_pending');
  assert.equal((await call(`/api/orders/${order.body.order.id}/demo-complete`, 'POST', { confirm: true }, session.token)).body.order.status, 'demo_completed');
  const uploaded = await call('/api/admin/documents', 'POST', { title: 'New source', content: 'Approved product details', id: 'new-source' }, adminToken);
  assert.equal(uploaded.status, 201); assert.equal(uploaded.body.document.reviewRequired, true);
  const sample = (await call('/api/admin/sample-catalog', 'GET', undefined, adminToken)).body; assert.equal(sample.demo, true);
});
