import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { createService } from '../src/service.mjs';
import { createApp } from '../src/http.mjs';

// Responses captured from the real Skillvation shop and MySkool course APIs on 24 October 2026.
const fixture = JSON.parse(readFileSync(new URL('./fixtures/skillvation.json', import.meta.url), 'utf8'));
function fakeApis({ products = fixture.products.data, failProducts = false, emptyProducts = false } = {}) {
  const calls = [];
  const fetchFn = async url => {
    calls.push(url); const u = new URL(url);
    const json = value => new Response(JSON.stringify(value), { status: 200, headers: { 'Content-Type': 'application/json' } });
    if (u.pathname.endsWith('/api/categories')) return json(fixture.categories);
    if (u.pathname.endsWith('/api/products')) {
      if (failProducts) return new Response('down', { status: 503 });
      return json({ status: 'success', data: emptyProducts ? [] : products, pagination: { total: emptyProducts ? 0 : products.length, per_page: 100, page: 1, pages: 1 } });
    }
    const detail = /\/api\/products\/(\d+)$/.exec(u.pathname);
    if (detail) { const id = Number(detail[1]); const item = products.find(p => p.id === id); return json({ status: 'success', data: fixture.details[id] ? { ...fixture.details[id], price: item.price, price_discounted: item.price_discounted } : { ...item, is_bundle: false, bundle_components: [] } }); }
    if (u.pathname.endsWith('/api/collab-courses')) {
      const key = u.searchParams.get('type') === '10' ? 'ttt' : u.searchParams.get('upskill') === '1' ? 'u4t' : 's2s';
      return json(fixture.courses[key]);
    }
    return new Response('not found', { status: 404 });
  };
  return { fetchFn, calls };
}
async function shopService(t, extra = {}, apis = fakeApis()) {
  const s = await createService({ aiMode: 'demo', checkoutMode: 'request', catalogSource: 'skillvation', skillvationFetch: apis.fetchFn, ...extra });
  t.after(() => s.close()); return s;
}
const labProfile = extra => ({ board: 'CBSE', setup: 'infrastructure_materials', layout: 'single', rooms: [{ areaSqFt: 650 }], classes: [6, 7, 8, 9, 10], totalStudents: 120, batchSize: 30, skillIds: ['agriculture-and-gardening', 'pottery', 'healthcare'], budgetMinor: null, goals: '', readiness: {}, tier: 'basic', ...extra });

test('sync turns shop and course APIs into a published catalog with paise amounts and per-item GST', async t => {
  const s = await shopService(t);
  const status = await s.syncSkillvation();
  assert.equal(status.ok, true, status.error); assert.equal(status.published, true);
  assert.deepEqual(status.summary, { products: 88, bundles: 4, courses: 42, skills: 10, categories: 18 });
  const catalog = s.db.catalog();
  assert.equal(catalog.model, 'shop'); assert.equal(catalog.demo, false); assert.match(catalog.revision, /^skillvation-/);
  const robotics = catalog.products.find(p => p.id === 'sv-82'); assert.equal(robotics.unitAmount, 790800); assert.equal(robotics.taxBasisPoints, 0);
  const bundle = catalog.products.find(p => p.id === 'sv-68'); assert.equal(bundle.kind, 'bundle'); assert.equal(bundle.unitAmount, 11369600); assert.equal(bundle.taxBasisPoints, 1800);
  const course = catalog.products.find(p => p.id === 'course-72'); assert.equal(course.unit, 'participant'); assert.ok(course.features.includes('pottery'));
  assert.ok(status.warnings.some(w => w.includes('CBSE Composite Skill Lab Basic') && w.includes('add up to')));
  assert.ok(status.warnings.some(w => w.includes('ECE') && w.includes('#85')));
  assert.ok(s.db.documents().some(d => d.id.startsWith('skillvation-shop-')));
});

test('an unchanged shop keeps the revision; a price change publishes a new one', async t => {
  let products = fixture.products.data;
  const apis = { fetchFn: (...args) => fakeApis({ products }).fetchFn(...args) };
  const s = await shopService(t, {}, apis);
  const first = await s.syncSkillvation(); const again = await s.syncSkillvation();
  assert.equal(again.changed, false); assert.equal(again.published, false); assert.equal(s.db.catalog().revision, first.revision);
  products = products.map(p => p.id === 82 ? { ...p, price: 8500, price_discounted: 8500 } : p);
  const changed = await s.syncSkillvation();
  assert.equal(changed.published, true); assert.notEqual(changed.revision, first.revision);
  assert.equal(s.db.catalog().products.find(p => p.id === 'sv-82').unitAmount, 850000);
});

test('failed or empty API responses never replace the working catalog', async t => {
  let mode = {};
  const s = await shopService(t, {}, { fetchFn: (...args) => fakeApis(mode).fetchFn(...args) });
  const good = await s.syncSkillvation();
  mode = { failProducts: true };
  const failed = await s.syncSkillvation();
  assert.equal(failed.ok, false); assert.match(failed.error, /Skillvation sync failed/); assert.equal(failed.lastSuccessAt, good.at);
  assert.equal(s.db.catalog().revision, good.revision);
  mode = { emptyProducts: true };
  const empty = await s.syncSkillvation();
  assert.equal(empty.ok, false); assert.match(empty.error, /no products/); assert.equal(s.db.catalog().revision, good.revision);
});

test('review mode stages a draft that an administrator publishes', async t => {
  const s = await shopService(t, { skillvationAutoPublish: false });
  const status = await s.syncSkillvation();
  assert.equal(status.staged, true); assert.equal(s.db.catalog(), null);
  const catalog = s.publishSkillvationDraft(); assert.equal(s.db.catalog().revision, catalog.revision);
});

test('bundles are quoted at the listed shop price plus the GST the shop lists', async t => {
  const s = await shopService(t); await s.syncSkillvation();
  const c = s.newConversation();
  const quote = s.quote(c.token, { items: [{ productId: 'sv-68', quantity: 1 }] });
  assert.equal(quote.subtotal, 11369600); assert.equal(quote.tax, 2046528); assert.equal(quote.total, 13416128);
  assert.equal(quote.purchasable, true);
});

test('lab planner builds from real shop products: lab + 4 kits + 4 consumables per skill, equipment, curriculum, installation', async t => {
  const s = await shopService(t); await s.syncSkillvation();
  const c = s.newConversation();
  const result = await s.labPlan(c.token, labProfile());
  // Agriculture 400 + 4×2505 + 4×2431, Pottery 6800 + 4×12420 + 4×3280, Healthcare 7000 + 4×5370 + 4×1210,
  // mandatory basic 130508, curriculum basic 15000, installation 25000 = ₹2,93,572.
  assert.equal(result.quote.total, 29357200); assert.equal(result.quote.tax, 0);
  assert.equal(result.quote.purchasable, true); assert.equal(result.schedule.stationsPerRoom, 4);
  assert.ok(result.readyMade.length === 4 && result.readyMade[0].matchingSkills >= 2);
  assert.ok(result.relatedCourses.some(course => course.name === 'Pottery'));
  const materialsOnly = await s.labPlan(c.token, labProfile({ setup: 'materials_only' }));
  assert.equal(materialsOnly.quote.total, 29357200 - 13050800);
});

test('a tier missing in the shop falls back visibly; a CBSE category gap blocks the request', async t => {
  const s = await shopService(t); await s.syncSkillvation();
  const c = s.newConversation();
  const premium = await s.labPlan(c.token, labProfile({ tier: 'premium' }));
  assert.ok(premium.assessment.observations.some(o => o.title === 'Tier availability' && o.detail.includes('Agriculture')));
  assert.ok(premium.quote.lines.some(l => l.productId === 'sv-7'), 'advanced agriculture lab used for premium');
  const gap = await s.labPlan(c.token, labProfile({ skillIds: ['robotics', 'pottery'] }));
  assert.equal(gap.quote.purchasable, false);
});

test('autopilot and chat recommend real shop products from plain language', async t => {
  const s = await shopService(t); await s.syncSkillvation();
  const lab = await s.labInterpret(s.newConversation().token, { message: 'CBSE school, classes 6 to 10, 300 students, one 700 sq ft room, gardening, pottery and healthcare, premium lab' });
  assert.equal(lab.draft.tier, 'premium'); assert.deepEqual([...lab.draft.skillIds].sort(), ['agriculture-and-gardening', 'healthcare', 'pottery']);
  assert.ok(lab.plan.quote.total > 0); assert.ok(lab.understood.some(chip => chip.field === 'tier' && !chip.assumed));
  const c = s.newConversation();
  const chat = await s.chat(c.token, { message: 'We need a pottery and healthcare lab, budget 3 lakh' });
  assert.ok(chat.quotes.length > 0); assert.ok(chat.quotes.every(q => q.total <= 30000000 && q.model === 'shop'));
  assert.ok(chat.quotes.some(q => q.title.startsWith('Custom')));
  const courses = await s.chat(s.newConversation().token, { message: 'Pottery courses for 40 students' });
  const courseQuote = [...courses.quotes, courses.nearest].filter(Boolean).find(q => q.lines.some(l => l.kind === 'course'));
  assert.ok(courseQuote); assert.equal(courseQuote.lines.find(l => l.kind === 'course').quantity, 40);
});

test('admin sync endpoints require the administrator token', async t => {
  const adminToken = 'skillvation-admin-token-0000000000000000000000';
  const app = await createApp({ aiMode: 'demo', checkoutMode: 'request', catalogSource: 'skillvation', skillvationFetch: fakeApis().fetchFn, skillvationSyncOnStart: false, adminToken });
  await new Promise(resolve => app.server.listen(0, '127.0.0.1', resolve)); t.after(() => app.close());
  const base = `http://127.0.0.1:${app.server.address().port}`;
  assert.equal((await fetch(`${base}/api/admin/skillvation/sync`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: '{}' })).status, 401);
  const response = await fetch(`${base}/api/admin/skillvation/sync`, { method: 'POST', headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${adminToken}` }, body: '{}' });
  const body = await response.json(); assert.equal(response.status, 200); assert.equal(body.status.ok, true);
  const health = await (await fetch(`${base}/api/health`)).json(); assert.equal(health.catalogSource, 'skillvation'); assert.equal(health.catalogModel, 'shop');
});

test('courses, tier and MySkool courses can be picked directly without typing', async t => {
  const s = await shopService(t); await s.syncSkillvation();
  const c = s.newConversation();
  const first = await s.labInterpret(c.token, { message: 'CBSE school, 120 students, one 650 sq ft room' });
  assert.ok(first.plan); assert.ok(first.understood.find(chip => chip.field === 'skillIds').assumed);
  const picked = await s.labInterpret(c.token, { draft: first.draft, select: { skillIds: ['agriculture-and-gardening', 'pottery', 'healthcare'], tier: 'basic' } });
  assert.equal(picked.plan.quote.total, 29357200); assert.ok(!picked.understood.find(chip => chip.field === 'skillIds').assumed);
  const withCourse = await s.labInterpret(c.token, { draft: picked.draft, select: { courses: [{ productId: 'course-72', participants: 120 }, { productId: 'course-102', participants: 2 }] } });
  assert.equal(withCourse.plan.quote.total, 29357200 + 120 * 100000 + 2 * 99900);
  assert.ok(withCourse.plan.relatedCourses.find(course => course.productId === 'course-72').selected);
  assert.equal(s.getQuote(c.token, withCourse.plan.quote.id).purchasable, true);
  const kept = await s.labInterpret(c.token, { message: 'make it materials only', draft: withCourse.draft });
  assert.ok(kept.plan.quote.lines.some(line => line.productId === 'course-72'), 'course picks survive later messages');
  await assert.rejects(s.labInterpret(c.token, { draft: kept.draft, select: { skillIds: [] } }), /at least one/);
  await assert.rejects(s.labInterpret(c.token, { draft: kept.draft, select: { courses: [{ productId: 'sv-82', participants: 1 }] } }), /valid, distinct courses/);
});

test('the live course browser exposes all 42 active courses, groups and authoritative fees before planning', async t => {
  const s = await shopService(t); await s.syncSkillvation();
  const options = s.labOptions();
  assert.equal(options.demo, false); assert.equal(options.courses.length, 42);
  assert.deepEqual(Object.fromEntries(['s2s', 'u4t', 'ttt'].map(type => [type, options.courses.filter(c => c.courseType === type).length])), { s2s: 5, u4t: 33, ttt: 4 });
  assert.equal(options.courses.find(c => c.productId === 'course-102').unitAmount, 99900);
  assert.match(options.catalogRevision, /^skillvation-/); assert.ok(options.syncedAt);
  const conversation = s.newConversation();
  const draft = await s.labInterpret(conversation.token, { select: { courses: [{ productId: 'course-102', participants: 2 }] } });
  assert.deepEqual(draft.draft.courses, [{ productId: 'course-102', participants: 2 }]);
  assert.ok(draft.missing.includes('board')); assert.equal(draft.plan, undefined);
});

test('truncated product or course pages never replace the last complete live catalog', async t => {
  const apis = fakeApis(); let truncate = null;
  const fetchFn = async (url, init) => {
    const response = await apis.fetchFn(url, init); const u = new URL(url);
    if ((truncate === 'products' && u.pathname.endsWith('/api/products')) || (truncate === 'courses' && u.pathname.endsWith('/api/collab-courses'))) {
      const body = await response.json();
      if (truncate === 'products') body.data = body.data.slice(0, 2);
      else body.courses.data = body.courses.data.slice(0, 1);
      return Response.json(body);
    }
    return response;
  };
  const s = await shopService(t, {}, { fetchFn }); const initial = await s.syncSkillvation(); assert.equal(initial.ok, true);
  for (const mode of ['products', 'courses']) {
    truncate = mode; const status = await s.syncSkillvation();
    assert.equal(status.ok, false); assert.match(status.error, /incomplete/); assert.equal(s.catalog().revision, initial.revision);
  }
});

test('live-source mode refuses to serve an old sample catalog while its first sync is pending', async t => {
  const s = await shopService(t);
  const sample = JSON.parse(readFileSync(new URL('../examples/catalog.json', import.meta.url), 'utf8'));
  for (const doc of sample.documents) s.db.document(doc);
  s.publish(sample);
  assert.throws(() => s.catalog(), /sample prices are disabled/);
  assert.throws(() => s.labOptions(), /sample prices are disabled/);
  const synced = await s.syncSkillvation(); assert.equal(synced.ok, true);
  assert.equal(s.catalog().demo, false);
});

test('budget options list distinct shop lab plans within the budget without saving a plan', async t => {
  const s = await shopService(t); await s.syncSkillvation();
  const c = s.newConversation();
  const budgetMinor = 50000000;
  const result = s.labBudgetOptions(c.token, labProfile({ skillIds: ['healthcare'], budgetMinor, tier: undefined }));
  assert.ok(result.options.length >= 2 && result.options.length <= 4);
  assert.equal(new Set(result.options.map(o => `${o.tier}:${o.skillIds}`)).size, result.options.length);
  assert.equal(result.includesGst, true);
  for (const option of result.options) {
    // By default the budget covers price plus GST at the CBSE bundle's rate (18% in the fixture).
    assert.equal(option.gst, Math.round(option.total * 0.18)); assert.equal(option.totalWithGst, option.total + option.gst);
    assert.ok(option.totalWithGst <= budgetMinor && option.remaining === budgetMinor - option.totalWithGst);
    assert.ok(option.skillIds.includes('healthcare'));
    assert.ok(option.items.length && option.items.every(item => item.name && item.quantity >= 1));
    assert.equal(typeof option.coversAllCategories, 'boolean');
    // The offered total is exactly what the planner quotes when the option is chosen.
    const plan = await s.labPlan(c.token, labProfile({ skillIds: option.skillIds, tier: option.tier, budgetMinor }));
    assert.equal(plan.quote.total, option.total);
  }
  const beforeGst = s.labBudgetOptions(c.token, { ...labProfile({ skillIds: ['healthcare'], budgetMinor, tier: undefined }), includesGst: false });
  assert.ok(beforeGst.options.every(o => o.total <= budgetMinor && o.remaining === budgetMinor - o.total));
  assert.ok(Math.max(...beforeGst.options.map(o => o.total)) > Math.max(...result.options.map(o => o.total)));
  const fresh = s.newConversation();
  s.labBudgetOptions(fresh.token, labProfile({ budgetMinor }));
  assert.equal(s.integrationResult(fresh.conversationId).status, 'pending');
  assert.throws(() => s.labBudgetOptions(c.token, labProfile({ budgetMinor: 100 })), { status: 422 });
  assert.throws(() => s.labBudgetOptions(c.token, labProfile({ budgetMinor: undefined })), { status: 400 });
});

test('guided mode asks one question at a time and waits for confirmation before planning', async t => {
  const s = await shopService(t); await s.syncSkillvation();
  const c = s.newConversation();
  let response = await s.labInterpret(c.token, { message: 'We are a CBSE school', guided: true });
  const asked = [];
  for (let turn = 0; turn < 12 && !response.confirm; turn++) {
    assert.equal(response.questions.length, 1); assert.equal(response.plan, undefined);
    asked.push(response.missing[0]);
    const answers = { totalStudents: '300 students', rooms: 'one 700 sq ft room', classes: 'classes 6 to 10', setup: 'complete lab', skillIds: 'robotics and healthcare', tier: 'advanced', budgetMinor: 'no fixed budget' };
    response = await s.labInterpret(c.token, { message: answers[response.missing[0]] || 'okay', draft: response.draft, guided: true });
  }
  assert.equal(response.confirm, true); assert.equal(response.plan, undefined);
  // Each question is asked once; the essentials come before the details Kalpana used to assume.
  assert.equal(new Set(asked).size, asked.length);
  assert.deepEqual(asked.slice(0, 2), ['totalStudents', 'rooms']);
  for (const field of ['classes', 'setup', 'skillIds', 'tier', 'budgetMinor']) assert.ok(asked.includes(field), `asked about ${field}`);
  assert.equal(s.integrationResult(c.conversationId).status, 'pending');
  const { assumed, asked: _, ...profile } = response.draft;
  assert.equal(profile.tier, 'advanced');
  const plan = await s.labPlan(c.token, profile);
  assert.ok(plan.quote.total > 0); assert.equal(s.integrationResult(c.conversationId).status, 'ready');
  // Without guided mode the planner still designs immediately once the essentials are known.
  const quick = await s.labInterpret(s.newConversation().token, { message: 'CBSE school, 300 students, one 700 sq ft room' });
  assert.ok(quick.plan);
});

const equipmentTable = items => `<table><tbody><tr><td>S.No</td><td>Item</td><td>Specification</td><td>Qty</td></tr>${items.map((item, i) => `<tr><td>${i + 1}</td><td>${item}</td><td>Pieces</td><td>${item === 'Smart TV' ? 0 : 2}</td></tr>`).join('')}</tbody></table>`;

test('shop equipment tables become product contents, skipping zero-quantity rows', async () => {
  const { parseContents } = await import('../src/skillvation.mjs');
  assert.deepEqual(parseContents(equipmentTable(['Stethoscope', 'Smart TV', 'First-aid kit', 'stethoscope'])), ['Stethoscope', 'First-aid kit']);
  assert.deepEqual(parseContents('<p>No table</p>'), []);
});

test('level comparison shows the price of each level and what each level adds, from the shop contents', async t => {
  // The recorded API has no descriptions; give the Healthcare products equipment tables like the live shop.
  const kit = { basic: ['Stethoscope', 'BP monitor', 'Thermometer', 'First-aid kit', 'Bandages', 'Gloves'], advanced: ['Stethoscope', 'BP monitor', 'Thermometer', 'First-aid kit', 'Bandages', 'Gloves', 'Pulse oximeter', 'Glucometer'], premium: ['Stethoscope', 'BP monitor', 'Thermometer', 'First-aid kit', 'Bandages', 'Gloves', 'Pulse oximeter', 'Glucometer', 'Nebuliser'] };
  const products = fixture.products.data.map(p => { const tier = /Healthcare/i.test(p.title) && /(Basic|Advanced|Premium)/i.exec(p.title)?.[1].toLowerCase(); return tier ? { ...p, description: equipmentTable(kit[tier]) } : p; });
  const s = await shopService(t, {}, fakeApis({ products })); await s.syncSkillvation();
  const c = s.newConversation();
  const result = s.labCompareTiers(c.token, { skillIds: ['healthcare', 'pottery'] });
  assert.deepEqual(result.tiers.map(x => x.tier), ['basic', 'advanced', 'premium']);
  assert.ok(result.assumed.includes('totalStudents') && result.assumed.includes('rooms'));
  assert.deepEqual(result.rows.map(r => r.label), ['Healthcare', 'Pottery', 'Lab furniture & safety equipment', 'Curriculum & teacher training']);
  for (const tier of result.tiers) {
    assert.equal(tier.totalWithGst, tier.total + tier.gst); assert.equal(tier.gst, Math.round(tier.total * 0.18));
    // The comparison price is exactly what the planner quotes for that level.
    const plan = await s.labPlan(c.token, labProfile({ skillIds: ['healthcare', 'pottery'], tier: tier.tier, rooms: [{ areaSqFt: 600 }], totalStudents: 120, batchSize: 30 }));
    assert.equal(plan.quote.total, tier.total); assert.equal(plan.gstBasisPoints, 1800);
  }
  assert.equal(result.tiers[0].extraOverBasic, 0); assert.ok(result.tiers[2].extraOverBasic > result.tiers[1].extraOverBasic);
  const healthcare = result.rows[0];
  assert.deepEqual(healthcare.cells[0].items, kit.basic); assert.deepEqual(healthcare.cells[0].added, kit.basic);
  assert.deepEqual(healthcare.cells.map(cell => cell.usedTier), ['basic', 'advanced', 'advanced'].slice(0, 2).concat(healthcare.cells[2].usedTier));
  assert.deepEqual(healthcare.cells[1].added, ['Pulse oximeter', 'Glucometer']);
  // "Added" lists only what is new compared with the level below.
  for (const [i, cell] of healthcare.cells.entries()) if (i) assert.ok(cell.added.every(item => !healthcare.cells.slice(0, i).some(prev => prev.items.some(known => known.toLowerCase() === item.toLowerCase()))));
  assert.ok(result.rows.some(row => row.cells[1].added.length > 0));
  assert.throws(() => s.labCompareTiers(c.token, { skillIds: [] }), { status: 400 });
});

test('guided mode keeps the skill areas a CBSE school names instead of filling missing categories', async t => {
  const s = await shopService(t); await s.syncSkillvation();
  const guided = await s.labInterpret(s.newConversation().token, { message: 'CBSE school, 300 students, one 700 sq ft room, only robotics', guided: true });
  assert.deepEqual(guided.draft.skillIds, ['robotics']);
  const quick = await s.labInterpret(s.newConversation().token, { message: 'CBSE school, 300 students, one 700 sq ft room, only robotics' });
  assert.ok(quick.draft.skillIds.length >= 3);
});

test('CBSE schools start with Agriculture, ECE, Design Thinking and Healthcare, and can change them', async t => {
  const s = await shopService(t); await s.syncSkillvation();
  const labels = draft => draft.skillIds.map(id => s.labOptions().skills.find(skill => skill.id === id).label);
  const first = await s.labInterpret(s.newConversation().token, { message: 'We want a composite skill lab', guided: true });
  assert.ok(!labels(first.draft).some(label => /design thinking/i.test(label)));
  // Once the board is known to be CBSE, Kalpana's own choice switches to the CBSE defaults.
  const cbse = await s.labInterpret(s.newConversation().token, { message: 'okay', draft: { ...first.draft, board: 'CBSE' }, guided: true });
  const cbseLabels = labels(cbse.draft);
  for (const re of [/agricult/i, /ece|electronics/i, /design thinking/i, /healthcare/i]) assert.ok(cbseLabels.some(label => re.test(label)), `${re} selected`);
  assert.equal(cbseLabels.length, 4); assert.ok(cbse.draft.assumed.includes('skillIds'));
  const changed = await s.labInterpret(s.newConversation().token, { draft: cbse.draft, select: { skillIds: ['pottery', 'healthcare'] }, guided: true });
  assert.deepEqual(changed.draft.skillIds, ['pottery', 'healthcare']);
});

test('each skill area can have its own level; the overall level sets shared items and resets them', async t => {
  const s = await shopService(t); await s.syncSkillvation();
  const c = s.newConversation();
  const plan = await s.labPlan(c.token, labProfile({ tier: 'basic', skillTiers: { healthcare: 'advanced' } }));
  const names = plan.requiredItems.map(item => item.name);
  assert.ok(names.includes('Healthcare · Advanced')); assert.ok(names.some(name => /Agriculture.*· Basic/.test(name)));
  assert.ok(names.some(name => /Mandatory Basic/.test(name)));
  assert.deepEqual(plan.profile.skillTiers, { healthcare: 'advanced' });
  assert.equal(s.integrationResult(c.conversationId).status, 'ready');
  await assert.rejects(s.labPlan(c.token, labProfile({ skillTiers: { healthcare: 'gold' } })), { status: 400 });
  // Choosing a level per area through the picker, then an overall level that resets it.
  const step = await s.labInterpret(c.token, { message: 'CBSE school, 300 students, one 700 sq ft room, healthcare, pottery and agriculture', guided: true });
  const perArea = await s.labInterpret(c.token, { draft: step.draft, select: { skillTiers: { healthcare: 'premium' } }, guided: true });
  assert.deepEqual(perArea.draft.skillTiers, { healthcare: 'premium' });
  assert.ok(perArea.understood.find(chip => chip.field === 'skillIds').value.includes('Healthcare (Premium)'));
  const overall = await s.labInterpret(c.token, { draft: perArea.draft, select: { tier: 'advanced' }, guided: true });
  assert.deepEqual(overall.draft.skillTiers, {});
});

test('an extraction that only repeats assumed skill areas does not stop the CBSE defaults', async t => {
  // A fake Gemini that reads the board from "CBSE" and, like the real model, echoes the previous draft's skill areas.
  const geminiFetch = async (_url, init) => {
    const body = JSON.parse(init.body);
    const input = JSON.parse(body.contents.flatMap(c => c.parts).map(p => p.text).find(text => text?.includes('"message"')));
    const reply = { reply: 'Noted.', board: /cbse/i.test(input.message) ? 'CBSE' : null, skillIds: input.previousDraft?.skillIds || [] };
    return Response.json({ candidates: [{ finishReason: 'STOP', content: { parts: [{ text: JSON.stringify(reply) }] } }] });
  };
  const s = await shopService(t, { aiMode: 'gemini', geminiKey: 'test-key', geminiFetch }); await s.syncSkillvation();
  const first = await s.labInterpret(s.newConversation().token, { message: 'We want a composite skill lab', guided: true });
  assert.ok(first.draft.assumed.includes('skillIds'));
  const cbse = await s.labInterpret(s.newConversation().token, { message: 'CBSE', draft: first.draft, guided: true });
  const labels = cbse.draft.skillIds.map(id => s.labOptions().skills.find(skill => skill.id === id).label);
  assert.equal(labels.length, 4); assert.ok(labels.some(label => /design thinking/i.test(label)) && labels.some(label => /healthcare/i.test(label)));
});
