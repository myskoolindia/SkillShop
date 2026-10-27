import test from 'node:test';
import assert from 'node:assert/strict';
import { createService } from '../src/service.mjs';
import { buildLabPlan } from '../src/lab-planner.mjs';

const defaults = { aiMode: 'demo', checkoutMode: 'request', seedDemo: true };
const profile = extra => ({ board: 'CBSE', setup: 'infrastructure_materials', layout: 'single', rooms: [{ areaSqFt: 600 }], classes: [6, 7, 8, 9, 10], totalStudents: 120, batchSize: 30, skillIds: ['horticulture', 'robotics-electronics', 'financial-literacy'], budgetMinor: 10000000, goals: '', readiness: {}, ...extra });
async function fixture(t, extra = {}) { const s = await createService({ ...defaults, ...extra }); t.after(() => s.close()); return s; }

test('one 600 sq ft room and three categories meet the planning checks, never certify compliance', async t => {
  const s = await fixture(t); const c = s.newConversation(); const result = await s.labPlan(c.token, profile());
  assert.equal(result.assessment.status, 'manual_review_required'); assert.equal(result.assessment.observations.filter(o => o.status === 'gap').length, 0);
  assert.equal(result.quote.total, 9750000); assert.equal(result.quote.purchasable, true);
  assert.equal(result.schedule.stations, 15); assert.equal(result.schedule.batches, 4);
  assert.equal(result.quote.labAssessment.rulesVersion, '2026-10-23-v1'); assert.equal(result.ai.status, 'demo');
  assert.ok(result.assessment.sources.some(s => s.scope.includes('business rule')));
});
test('two rooms are checked individually; 600 plus 200 cannot pass a 400-each configuration', async t => {
  const s = await fixture(t); const c = s.newConversation();
  const result = await s.labPlan(c.token, profile({ layout: 'dual', rooms: [{ areaSqFt: 600 }, { areaSqFt: 200 }], classes: [6, 12], budgetMinor: null }));
  assert.equal(result.assessment.status, 'planning_gaps'); assert.equal(result.quote.purchasable, false);
  assert.ok(result.assessment.observations.some(o => o.id === 'room-1' && o.status === 'gap'));
});
test('dual 400 sq ft configuration equips both class groups and scales materials by batch', async t => {
  const s = await fixture(t); const c = s.newConversation();
  const result = await s.labPlan(c.token, profile({ layout: 'dual', rooms: [{ areaSqFt: 400 }, { areaSqFt: 400 }], classes: [6, 12], budgetMinor: null }));
  assert.equal(result.schedule.activeRooms, 2); assert.equal(result.schedule.stations, 30); assert.equal(result.quote.total, 19500000);
  const another = await s.labPlan(c.token, profile({ layout: 'dual', rooms: [{ areaSqFt: 400 }, { areaSqFt: 400 }], classes: [6, 12], totalStudents: 1200, budgetMinor: null }));
  assert.equal(another.quote.total, result.quote.total); assert.equal(another.schedule.batches, 40);
});
test('CBSE missing skill categories and short room prevent a purchase request', async t => {
  const s = await fixture(t); const c = s.newConversation();
  const result = await s.labPlan(c.token, profile({ rooms: [{ areaSqFt: 599 }], skillIds: ['robotics-electronics'] }));
  assert.equal(result.quote.purchasable, false); assert.ok(result.quote.constraints.unsupportedRequirements.length >= 2);
  assert.throws(() => s.order(c.token, { quoteId: result.quote.id, confirm: true, customer: { name: 'Test', email: 'test@example.test' } }), /requirements/);
});
test('non-CBSE planning does not apply CBSE space or course minimums', async t => {
  const s = await fixture(t); const c = s.newConversation();
  const result = await s.labPlan(c.token, profile({ board: 'ICSE', rooms: [{ areaSqFt: 200 }], skillIds: ['robotics-electronics'], goals: 'Introduce hands-on electronics', budgetMinor: null }));
  assert.ok(result.assessment.observations.some(o => o.status === 'not_assessed')); assert.ok(!result.assessment.observations.some(o => o.status === 'gap'));
  assert.equal(result.quote.purchasable, true);
});
test('materials-only and existing-room scopes exclude fit-out, optional upgrades are explicit', async t => {
  const s = await fixture(t); const c = s.newConversation();
  for (const setup of ['materials_only', 'existing_room']) {
    const result = await s.labPlan(c.token, profile({ setup }));
    assert.ok(!result.quote.lines.some(l => l.productId === 'lab-room-fitout')); assert.equal(result.quote.total, 6750000);
  }
  const upgraded = await s.labPlan(c.token, profile({ optionalProductIds: ['lab-ai-upgrade'], budgetMinor: null }));
  assert.equal(upgraded.quote.total, 10650000); assert.ok(upgraded.optionalUpgrades[0].selected);
});
test('invalid counts, unknown skills, malformed rooms and unsupported upgrades are rejected', async t => {
  const s = await fixture(t); const catalog = s.db.catalog();
  for (const extra of [{ batchSize: 0 }, { batchSize: 121 }, { totalStudents: 0 }, { rooms: [{ areaSqFt: '600' }] }, { skillIds: ['unknown'] }, { classes: [] }, { classes: [5] }, { optionalProductIds: ['ai-activities'] }, { layout: 'dual' }, { classes: [6, 6] }]) assert.throws(() => buildLabPlan(catalog, profile(extra)));
});
test('quote route cannot bypass the intake or remove required lab items', async t => {
  const s = await fixture(t); const c = s.newConversation();
  assert.throws(() => s.quote(c.token, { items: [{ productId: 'lab-station-foundation', quantity: 1 }] }), /Complete the lab planner/);
  const result = await s.labPlan(c.token, profile());
  assert.throws(() => s.quote(c.token, { items: [{ productId: 'lab-station-foundation', quantity: 15 }] }), /reviewed plan/);
  const quote = s.quote(c.token, { items: result.items }); assert.equal(quote.total, result.quote.total);
});
test('over-budget lab plan is explicit; changed intake invalidates old quote', async t => {
  const s = await fixture(t); const c = s.newConversation(); const result = await s.labPlan(c.token, profile({ budgetMinor: 5000000 }));
  assert.equal(result.quote.overBudget, 4750000); assert.equal(result.quote.purchasable, false);
  await s.labPlan(c.token, profile({ setup: 'materials_only' })); assert.throws(() => s.getQuote(c.token, result.quote.id), /Requirements changed/);
});
test('Gemini explanation failure preserves deterministic plan and reports its unavailability', async t => {
  const s = await fixture(t, { aiMode: 'gemini', geminiKey: 'test-key', geminiFetch: async () => { throw Error('offline'); } });
  const result = await s.labPlan(s.newConversation().token, profile());
  assert.equal(result.ai.status, 'unavailable'); assert.equal(result.rulesOnly, true); assert.equal(result.quote.total, 9750000);
});
