import test from 'node:test';
import assert from 'node:assert/strict';
import { createService } from '../src/service.mjs';
import { createApp } from '../src/http.mjs';
import { demoLabIntent, sanitizeExtraction, mergeLabDraft } from '../src/lab-intake.mjs';

const defaults = { aiMode: 'demo', checkoutMode: 'request', seedDemo: true };
async function fixture(t, extra = {}) { const s = await createService({ ...defaults, ...extra }); t.after(() => s.close()); return s; }
const options = { skills: [{ id: 'horticulture', label: 'Horticulture & plant care', category: 'life_forms' }, { id: 'robotics-electronics', label: 'Robotics & electronics', category: 'machines_materials' }, { id: 'financial-literacy', label: 'Financial literacy & enterprise', category: 'human_services' }], optionalProducts: [{ id: 'lab-ai-upgrade', name: 'AI extension activities', features: ['ai'] }] };

test('demo parser reads board, classes, strength, rooms, batch, budget and courses from plain language', () => {
  const v = demoLabIntent({ message: 'CBSE school, classes VI to X, 400 students in batches of 40, two rooms of 450 sq ft and 420 sqft, robotics + AI, budget 1.5 lakh', options });
  assert.equal(v.board, 'CBSE'); assert.deepEqual(v.classes, [6, 7, 8, 9, 10]); assert.equal(v.totalStudents, 400); assert.equal(v.batchSize, 40);
  assert.deepEqual(v.roomAreasSqFt, [450, 420]); assert.equal(v.budgetMinor, 15000000); assert.deepEqual(v.skillIds, ['robotics-electronics']); assert.deepEqual(v.optionalProductIds, ['lab-ai-upgrade']);
});

test('model output is sanitized: unknown courses, impossible counts and bad enums are dropped', () => {
  const v = sanitizeExtraction({ board: 'IB', setup: 'castle', roomAreasSqFt: [-5, 600, 'big'], classes: [3, 7, 7, 13], totalStudents: 0, batchSize: 2.5, skillIds: ['robotics-electronics', 'hacking'], optionalProductIds: ['ai-activities'], budgetMinor: -1, readiness: { water: 'maybe', electrical: 'yes' } }, options);
  assert.equal(v.board, null); assert.equal(v.setup, null); assert.deepEqual(v.roomAreasSqFt, [600]); assert.deepEqual(v.classes, [7]);
  assert.equal(v.totalStudents, null); assert.equal(v.batchSize, null); assert.deepEqual(v.skillIds, ['robotics-electronics']); assert.deepEqual(v.optionalProductIds, []);
  assert.equal(v.budgetMinor, null); assert.deepEqual(v.readiness, { electrical: 'yes' });
});

test('negative readiness declarations take precedence over ready keywords', () => {
  const v = demoLabIntent({ message: 'Electrical not ready, water missing, ventilation ready.', options });
  assert.equal(v.readiness.electrical, 'no');
  assert.equal(v.readiness.water, 'no');
  assert.equal(v.readiness.ventilation, 'yes');
});

test('an incomplete intake revision invalidates the previous purchasable quote', async t => {
  const s = await fixture(t); const c = s.newConversation();
  const first = await s.labInterpret(c.token, { message: 'CBSE, 120 students, 600 sq ft, all courses' });
  assert.equal(first.plan.quote.purchasable, true);
  const next = await s.labInterpret(c.token, { message: 'Make it two rooms', draft: first.draft });
  assert.deepEqual(next.missing, ['rooms']);
  assert.throws(() => s.getQuote(c.token, first.plan.quote.id), /Requirements changed/);
  assert.throws(() => s.order(c.token, { quoteId: first.plan.quote.id, confirm: true, customer: { name: 'Buyer', email: 'buyer@example.test' } }), /Requirements changed/);
});

test('missing essentials are asked for; room areas and student counts are never invented', () => {
  const { draft, missing } = mergeLabDraft(null, demoLabIntent({ message: 'We want a robotics lab', options }), options, 'We want a robotics lab');
  assert.deepEqual(missing.map(m => m.field), ['board', 'totalStudents', 'rooms']); assert.deepEqual(draft.rooms, []); assert.equal(draft.totalStudents, null);
  assert.ok(draft.assumed.includes('setup') && draft.assumed.includes('classes'));
});

test('autopilot asks one follow-up, then produces the same verified plan as the manual planner', async t => {
  const s = await fixture(t); const c = s.newConversation();
  const first = await s.labInterpret(c.token, { message: 'CBSE school, classes 6 to 10, 120 students, batches of 30, all courses, budget 1 lakh' });
  assert.equal(first.plan, undefined); assert.deepEqual(first.missing, ['rooms']);
  const second = await s.labInterpret(c.token, { message: 'The room is 600 sq ft', draft: first.draft });
  assert.equal(second.plan.quote.total, 9750000); assert.equal(second.plan.quote.purchasable, true); assert.equal(second.plan.schedule.stations, 15);
  assert.ok(second.understood.some(chip => chip.field === 'setup' && chip.assumed));
  const followUp = await s.labInterpret(c.token, { message: 'Actually make it materials only', draft: second.draft });
  assert.equal(followUp.plan.quote.total, 6750000); assert.ok(!followUp.understood.find(chip => chip.field === 'setup').assumed);
  assert.throws(() => s.getQuote(c.token, second.plan.quote.id), /Requirements changed/);
});

test('CBSE autopilot completes the three-category baseline and the user can remove an added course', async t => {
  const s = await fixture(t); const c = s.newConversation();
  const r = await s.labInterpret(c.token, { message: 'CBSE, 200 students, one 700 sq ft room, robotics only please' });
  assert.equal(r.draft.skillIds.length, 3); assert.ok(r.notes.some(n => n.includes('Horticulture'))); assert.equal(r.plan.assessment.status, 'manual_review_required');
  const trimmed = await s.labInterpret(c.token, { message: 'without horticulture', draft: r.draft });
  assert.ok(!trimmed.draft.skillIds.includes('horticulture')); assert.equal(trimmed.plan.quote.purchasable, false);
  const again = await s.labInterpret(c.token, { message: 'batches of 20', draft: trimmed.draft });
  assert.ok(!again.draft.skillIds.includes('horticulture'));
});

test('Gemini lab extraction is validated and its prose cannot carry prices or compliance claims', async t => {
  const reply = { reply: 'Your school is fully compliant and this costs INR 5,000.', board: 'CBSE', setup: 'materials_only', roomAreasSqFt: [650], classes: [6, 7, 8], totalStudents: 90, batchSize: 30, skillIds: ['horticulture', 'robotics-electronics', 'financial-literacy', 'made-up'], optionalProductIds: [], budgetMinor: null, readiness: {}, goals: '' };
  let body;
  const s = await fixture(t, { aiMode: 'gemini', geminiKey: 'k', geminiFetch: async (_url, init) => { body = JSON.parse(init.body); return Response.json({ candidates: [{ finishReason: 'STOP', content: { parts: [{ text: JSON.stringify(body.generationConfig.responseJsonSchema.properties.roomAreasSqFt ? reply : { explanation: 'Plan explained.', nextSteps: [] }) }] } }] }); } });
  const r = await s.labInterpret(s.newConversation().token, { message: 'CBSE, 90 students, 650 sq ft, classes 6-8, materials only' });
  assert.ok(!/INR|compliant/.test(r.reply)); assert.deepEqual(r.draft.skillIds, ['horticulture', 'robotics-electronics', 'financial-literacy']);
  assert.equal(r.plan.quote.total, 6750000);
});

test('interpret endpoint is served over HTTP with the AI rate limit', async t => {
  const app = await createApp({ ...defaults, adminToken: 'lab-intake-test-admin-token-000000000000000' });
  await new Promise(resolve => app.server.listen(0, '127.0.0.1', resolve)); t.after(() => app.close());
  const base = `http://127.0.0.1:${app.server.address().port}`;
  const { token } = await (await fetch(`${base}/api/conversations`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: '{}' })).json();
  const response = await fetch(`${base}/api/lab-planner/interpret`, { method: 'POST', headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` }, body: JSON.stringify({ message: 'ICSE school, 300 students, one 500 sq ft room, introduce hands-on robotics' }) });
  const result = await response.json(); assert.equal(response.status, 200, JSON.stringify(result)); assert.equal(result.plan.profile.board, 'ICSE');
  assert.equal((await fetch(`${base}/voice.js`)).status, 200);
});

test('without a Gemini key the autopilot uses the labelled local parser instead of failing', async t => {
  const s = await fixture(t, { aiMode: 'gemini', geminiKey: '' });
  const r = await s.labInterpret(s.newConversation().token, { message: 'CBSE school, 120 students, one 600 sq ft room, all courses' });
  assert.equal(r.engine, 'local'); assert.equal(r.plan.quote.total, 9750000); assert.equal(r.plan.ai.status, 'not_configured');
});
