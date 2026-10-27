import { assert } from './core.mjs';
import { validateLabProfile, assessLab, LAB_CATEGORIES, LAB_RULES_VERSION, LAB_SOURCES } from './lab-rules.mjs';
import { calculateShop, shopLabItems } from './shop.mjs';

export function plannerOptions(catalog) {
  return {
    rulesVersion: LAB_RULES_VERSION, boards: ['CBSE', 'State Board', 'ICSE', 'Other'], categories: LAB_CATEGORIES,
    setups: [{ id: 'infrastructure_materials', label: 'Infrastructure + materials' }, { id: 'materials_only', label: 'Materials only' }, { id: 'existing_room', label: 'Existing room with lab setup' }],
    skills: catalog.labPlanner?.skills || [], pricingConfigured: !!catalog.labPlanner, currency: catalog.currency,
    demo: catalog.demo, sources: LAB_SOURCES, mode: catalog.labPlanner?.mode || 'stations', tiers: catalog.labPlanner?.tiers || [], kitsPerRoom: catalog.labPlanner?.kitsPerRoom ?? null,
    catalogRevision: catalog.revision, syncedAt: catalog.source?.fetchedAt || null,
    courses: catalog.products.filter(p => p.kind === 'course' && p.active).map(p => ({ productId: p.id, name: p.name, unitAmount: p.unitAmount, courseType: p.courseType, startDate: p.startDate, endDate: p.endDate, location: p.location })),
  };
}

export function buildLabPlan(catalog, input) {
  const config = catalog.labPlanner;
  assert(config, 503, 'The approved catalog has no lab-planner pricing yet. Ask the pricing administrator to publish a planner catalog.');
  if (config.mode === 'shop') return buildShopLabPlan(catalog, input);
  const profile = validateLabProfile(input, config.skills);
  const assessment = assessLab(profile, config.skills);
  const stationsPerRoom = Math.ceil(profile.batchSize / config.studentsPerStation);
  const stations = stationsPerRoom * assessment.activeRooms;
  assert(stations <= 100, 400, 'This batch/room combination exceeds the 100-station automatic quotation limit; request a team quote');
  const items = [{ productId: config.coreProductId, quantity: stations }];
  for (const skill of assessment.selectedSkills) items.push({ productId: skill.productId, quantity: stations });
  if (profile.setup === 'infrastructure_materials') items.push({ productId: config.infrastructureProductId, quantity: assessment.activeRooms });
  const optionalIds = input.optionalProductIds ?? [];
  assert(Array.isArray(optionalIds) && optionalIds.length <= config.optionalProductIds.length && optionalIds.every(id => config.optionalProductIds.includes(id)) && new Set(optionalIds).size === optionalIds.length, 400, 'Invalid optional upgrades');
  for (const productId of optionalIds) items.push({ productId, quantity: stations });
  const requiredItems = [
    { name: 'Shared station foundation', quantity: stations, unit: 'stations', detail: `${config.studentsPerStation} learners per station; ${stationsPerRoom} stations per active room`, productId: config.coreProductId },
    ...assessment.selectedSkills.map(skill => ({ name: `${skill.label} materials`, quantity: stations, unit: 'station sets', detail: skill.items.join('; '), productId: skill.productId })),
  ];
  if (profile.setup === 'infrastructure_materials') requiredItems.push({ name: 'Room fit-out allowance', quantity: assessment.activeRooms, unit: 'rooms', detail: 'Sample scope: furniture, storage and installation allowance. Civil work, electrical alterations and final site-specific scope need review.', productId: config.infrastructureProductId });
  const gaps = assessment.observations.filter(o => o.status === 'gap').map(o => `${o.title}: ${o.detail}`);
  const constraints = {
    budgetMinor: profile.budgetMinor, learners: profile.batchSize * assessment.activeRooms, currency: catalog.currency,
    features: [], packageIds: [config.coreProductId], unsupportedRequirements: gaps,
    labProfile: profile, optionalProductIds: optionalIds, labRulesVersion: LAB_RULES_VERSION,
  };
  return {
    profile, assessment, constraints, items, requiredItems,
    schedule: { totalStudents: profile.totalStudents, batchSize: profile.batchSize, batches: assessment.batches, activeRooms: assessment.activeRooms, stationsPerRoom, stations, capacityBasis: 'Equipment is sized for a batch in each active room, reused across scheduled batches—not one equipment set per enrolled student.' },
    optionalUpgrades: config.optionalProductIds.map(id => { const p = catalog.products.find(p => p.id === id); return { productId: p.id, name: p.name, unitAmount: p.unitAmount, quantity: stations, selected: optionalIds.includes(id) }; }),
  };
}

export function validateLabItems(catalog, constraints, items) {
  if (catalog.labPlanner?.mode === 'shop') {
    // Shop items are also sold directly; only a lab-plan conversation is bound to its reviewed plan.
    if (!constraints.labProfile) return null;
    const plan = buildLabPlan(catalog, { ...constraints.labProfile, optionalProductIds: constraints.optionalProductIds, courses: constraints.courses });
    assert(Array.isArray(items) && items.length === plan.items.length && plan.items.every(required => items.some(item => item?.productId === required.productId && item.quantity === required.quantity)), 409, 'Lab items must match the reviewed plan. Update the planner to change quantities, courses or upgrades.');
    return plan;
  }
  const usesLabProducts = Array.isArray(items) && items.some(item => catalog.products.some(p => p.id === item?.productId && p.salesChannel === 'lab_planner'));
  if (!usesLabProducts) {
    assert(!constraints.labProfile, 409, 'This conversation is a lab plan; use the planner to change its scope');
    return null;
  }
  assert(constraints.labProfile, 409, 'Complete the lab planner before quoting these products');
  const plan = buildLabPlan(catalog, { ...constraints.labProfile, optionalProductIds: constraints.optionalProductIds });
  assert(Array.isArray(items) && items.length === plan.items.length && plan.items.every(required => items.some(item => item.productId === required.productId && item.quantity === required.quantity)), 409, 'Lab items must match the reviewed plan. Update the planner to change quantities, courses or upgrades.');
  return plan;
}

const tierName = tier => tier[0].toUpperCase() + tier.slice(1);
// The shop checks a designed lab out as its CBSE composite bundle of that tier, so that bundle's GST rate applies.
export const shopGstBasisPoints = (catalog, tier) => catalog.products.find(p => p.kind === 'bundle' && /cbse/i.test(p.name) && p.tier === tier)?.taxBasisPoints || 0;

// Lab plan from real shop products. Quantities follow the shop's bundle convention, scaled by the
// number of active rooms; equipment is shared across scheduled batches rather than bought per student.
function buildShopLabPlan(catalog, input) {
  const config = catalog.labPlanner;
  const profile = validateLabProfile(input, config.skills);
  const tier = input.tier ?? 'basic';
  assert(config.tiers.includes(tier), 400, 'Choose a Basic, Advanced or Premium tier');
  const assessment = assessLab(profile, config.skills);
  const rooms = assessment.activeRooms; const kits = config.kitsPerRoom;
  const product = productId => catalog.products.find(p => p.id === productId);
  // A level per skill area is optional; the plan's tier still sets shared equipment, curriculum and the shop bundle.
  const skillTiers = {};
  if (input.skillTiers !== undefined && input.skillTiers !== null) {
    assert(typeof input.skillTiers === 'object' && !Array.isArray(input.skillTiers), 400, 'Invalid levels per skill area');
    for (const [skillId, level] of Object.entries(input.skillTiers)) {
      assert(config.tiers.includes(level), 400, 'Choose Basic, Advanced or Premium for each skill area');
      if (profile.skillIds.includes(skillId) && level !== tier) skillTiers[skillId] = level;
    }
  }
  const { items, notes, groups, pickTier } = shopLabItems(config, profile.skillIds, tier, rooms, skillTiers);
  const perRoom = component => component === 'lab' || component === 'set' ? 1 : kits;
  const requiredItems = groups.map(group => ({
    name: `${group.label} · ${tierName(group.tier)}`, quantity: rooms, unit: rooms === 1 ? 'room set' : 'room sets', productId: group.set.kit || group.set.set || group.set.lab,
    detail: `Per room: ${Object.entries(group.set).map(([component, productId]) => `${perRoom(component)} × ${product(productId).name}`).join('; ')}`,
  }));
  if (profile.board === 'CBSE' && profile.setup !== 'materials_only') {
    const mandatory = pickTier(config.mandatory);
    if (mandatory) { items.set(mandatory, rooms); requiredItems.push({ name: product(mandatory).name, quantity: rooms, unit: rooms === 1 ? 'room' : 'rooms', detail: 'Mandatory composite-lab equipment from the shop, one set per active room.', productId: mandatory }); }
  }
  const curriculum = pickTier(config.curriculum);
  if (curriculum) { items.set(curriculum, 1); requiredItems.push({ name: product(curriculum).name, quantity: 1, unit: 'programme', detail: 'Curriculum and teacher training for the selected tier.', productId: curriculum }); }
  if (config.installation) { items.set(config.installation, 1); requiredItems.push({ name: product(config.installation).name, quantity: 1, unit: 'order', detail: 'Installation and transport, once per order.', productId: config.installation }); }
  const optionalIds = input.optionalProductIds ?? [];
  assert(Array.isArray(optionalIds) && optionalIds.every(productId => config.optionalProductIds.includes(productId)) && new Set(optionalIds).size === optionalIds.length, 400, 'Invalid optional upgrades');
  for (const productId of optionalIds) items.set(productId, rooms);
  const courses = Array.isArray(input.courses) ? input.courses : [];
  assert(courses.length <= 12 && new Set(courses.map(c => c?.productId)).size === courses.length && courses.every(c => product(c?.productId)?.kind === 'course' && product(c.productId).active && Number.isInteger(c.participants) && c.participants >= 1 && c.participants <= 100000), 400, 'Invalid course selection');
  for (const course of courses) { items.set(course.productId, course.participants); requiredItems.push({ name: product(course.productId).name, quantity: course.participants, unit: 'participants', detail: `MySkool ${product(course.productId).courseType?.toUpperCase() || ''} course, priced per participant.`, productId: course.productId }); }
  assert(items.size <= 80, 400, 'This plan has too many line items for an automatic quote; request a team quote');
  notes.forEach((note, i) => assessment.observations.push({ id: `tier-${i}`, status: 'review', title: 'Tier availability', detail: note, basis: 'Skillvation shop catalog' }));
  const gaps = assessment.observations.filter(o => o.status === 'gap').map(o => `${o.title}: ${o.detail}`);
  const fullProfile = { ...profile, tier, skillTiers };
  const constraints = {
    budgetMinor: profile.budgetMinor, learners: profile.batchSize * rooms, currency: catalog.currency, features: [], packageIds: [], unsupportedRequirements: gaps,
    labProfile: fullProfile, optionalProductIds: optionalIds, courses: courses.map(({ productId, participants }) => ({ productId, participants })), labRulesVersion: LAB_RULES_VERSION,
  };
  const readyMade = config.bundleIds.map(productId => {
    const p = product(productId); const quote = calculateShop(catalog, [{ productId, quantity: 1 }]);
    return { productId, name: p.name, tier: p.tier, total: quote.total, tax: quote.tax, url: p.url, includes: p.includes?.length || 0, matchingSkills: profile.skillIds.filter(id => p.features.includes(id)).length };
  }).sort((a, b) => b.matchingSkills - a.matchingSkills || a.total - b.total);
  const picked = new Map(courses.map(c => [c.productId, c.participants]));
  const relatedCourses = catalog.products.filter(p => p.kind === 'course' && p.active && (picked.has(p.id) || p.features.some(f => profile.skillIds.includes(f) || f === 'teacher-training')))
    .sort((a, b) => Number(b.features.some(f => profile.skillIds.includes(f))) - Number(a.features.some(f => profile.skillIds.includes(f))))
    .sort((a, b) => Number(picked.has(b.id)) - Number(picked.has(a.id)))
    .slice(0, Math.max(8, picked.size)).map(p => ({ productId: p.id, name: p.name, unitAmount: p.unitAmount, unit: p.unit, courseType: p.courseType, startDate: p.startDate, endDate: p.endDate, location: p.location, selected: picked.has(p.id), participants: picked.get(p.id) ?? null }));
  // Every active course stays selectable, so schools can add any MySkool course.
  const allCourses = catalog.products.filter(p => p.kind === 'course' && p.active).map(p => ({ productId: p.id, name: p.name, unitAmount: p.unitAmount, courseType: p.courseType, selected: picked.has(p.id), participants: picked.get(p.id) ?? null }));
  return {
    profile: fullProfile, assessment, constraints, tier, title: `Custom ${tierName(tier)} lab plan`, gstBasisPoints: shopGstBasisPoints(catalog, tier),
    items: [...items].map(([productId, quantity]) => ({ productId, quantity })), requiredItems,
    schedule: { totalStudents: profile.totalStudents, batchSize: profile.batchSize, batches: assessment.batches, activeRooms: rooms, stationsPerRoom: kits, stations: kits * rooms, capacityBasis: `Shop standard per skill area: one lab set plus ${kits} kit and ${kits} consumable sets per active room, shared across ${assessment.batches} scheduled batches.` },
    optionalUpgrades: config.optionalProductIds.map(productId => { const p = product(productId); return { productId, name: p.name, unitAmount: p.unitAmount, quantity: rooms, selected: optionalIds.includes(productId) }; }),
    readyMade, relatedCourses, allCourses,
  };
}

// Budget options: every tier and skill-area combination is priced, and up to four distinct plans that fit
// the budget are offered. The shop checks a designed lab out as its CBSE composite bundle, so GST is that
// bundle's rate; with includesGst the budget covers price plus GST. Selected skill areas are always included. A plan's cost is a fixed part
// (mandatory equipment, curriculum, installation) plus one amount per skill area, so combinations are
// priced from single-skill plans; each offered option is then priced exactly with the real planner.
const OPTION_LIMIT = 4;
export function budgetLabOptions(catalog, input) {
  const config = catalog.labPlanner;
  assert(config?.mode === 'shop', 503, 'Budget options need the shop lab catalog');
  assert(input && Number.isInteger(input.budgetMinor) && input.budgetMinor >= 100 && input.budgetMinor <= 1e12, 400, 'Enter a budget');
  const budget = input.budgetMinor;
  const includesGst = input.includesGst !== false;
  const gstRate = tier => shopGstBasisPoints(catalog, tier) / 10000;
  const payable = (amount, tier) => amount + (includesGst ? Math.round(amount * gstRate(tier)) : 0);
  const skills = config.skills;
  assert(skills.length <= 16, 503, 'Too many skill areas to compare automatically; request a team quote');
  const required = Array.isArray(input.skillIds) ? input.skillIds : [];
  const { includesGst: _, ...profileInput } = input;
  const base = { ...profileInput, budgetMinor: budget, optionalProductIds: [], courses: [], skillTiers: undefined };
  validateLabProfile({ ...base, skillIds: required.length ? required : [skills[0].id] }, skills);
  const total = (skillIds, tier) => calculateShop(catalog, buildLabPlan(catalog, { ...base, skillIds, tier }).items).total;

  const candidates = [];
  for (const tier of config.tiers) {
    const single = skills.map(s => total([s.id], tier));
    const fixed = skills.length > 1 ? single[0] + single[1] - total([skills[0].id, skills[1].id], tier) : 0;
    const perSkill = single.map(amount => amount - fixed);
    for (let mask = 1; mask < 1 << skills.length; mask++) {
      const picked = skills.filter((_, i) => mask & (1 << i));
      if (!required.every(id => picked.some(s => s.id === id))) continue;
      const estimate = fixed + skills.reduce((sum, _, i) => sum + (mask & (1 << i) ? perSkill[i] : 0), 0);
      if (payable(estimate, tier) > budget) continue;
      const categories = new Set(picked.map(s => s.category)).size;
      candidates.push({ tier, rank: config.tiers.indexOf(tier), skillIds: picked.map(s => s.id), size: picked.length, categories, estimate });
    }
  }
  assert(candidates.length, 422, 'No lab plan fits this budget. Try a higher budget or fewer selected skill areas.');
  // CBSE plans that cover all three work categories come first (Kalpana's planning baseline).
  const coverage = c => (base.board === 'CBSE' ? Math.min(c.categories, 3) : 0);
  const pickers = [
    ['most-skills', 'Most skill areas', (a, b) => coverage(b) - coverage(a) || b.size - a.size || b.rank - a.rank || a.estimate - b.estimate],
    ['highest-tier', 'Highest tier', (a, b) => coverage(b) - coverage(a) || b.rank - a.rank || b.size - a.size || a.estimate - b.estimate],
    ['best-fit', 'Best use of budget', (a, b) => coverage(b) - coverage(a) || b.estimate - a.estimate],
    ['lowest-cost', 'Lowest cost', (a, b) => coverage(b) - coverage(a) || a.estimate - b.estimate || b.size - a.size],
  ];
  const chosen = []; const seen = new Set();
  for (const [key, label, order] of pickers) {
    for (const candidate of [...candidates].sort(order)) {
      const id = `${candidate.tier}:${candidate.skillIds.join(',')}`;
      if (seen.has(id)) continue;
      // "Highest tier" is only worth offering when it is above the tier already offered.
      if (key === 'highest-tier' && chosen.some(c => config.tiers.indexOf(c.tier) >= candidate.rank)) break;
      const plan = buildLabPlan(catalog, { ...base, skillIds: candidate.skillIds, tier: candidate.tier });
      const quote = calculateShop(catalog, plan.items);
      if (payable(quote.total, candidate.tier) > budget) continue;
      seen.add(id);
      const gst = Math.round(quote.total * gstRate(candidate.tier));
      chosen.push({
        key, label, tier: candidate.tier, skillIds: candidate.skillIds, skills: candidate.skillIds.map(id => skills.find(s => s.id === id).label),
        subtotal: quote.subtotal, tax: quote.tax, total: quote.total, gst, gstRate: gstRate(candidate.tier), totalWithGst: quote.total + gst,
        remaining: budget - payable(quote.total, candidate.tier), coversAllCategories: candidate.categories >= 3,
        items: quote.lines.map(line => ({ name: line.name, quantity: line.quantity })),
      });
      break;
    }
    if (chosen.length === OPTION_LIMIT) break;
  }
  return { budgetMinor: budget, includesGst, currency: catalog.currency, options: chosen };
}

// Basic vs Advanced vs Premium for the chosen skill areas, in terms a school can judge: the price of each level
// and, per skill area, the equipment and materials each level ADDS over the level below (from the shop's own
// product contents). Details the school has not given yet use typical values, listed in `assumed`.
const COMPARE_DEFAULTS = { board: 'CBSE', setup: 'infrastructure_materials', layout: 'single', rooms: [{ areaSqFt: 600 }], classes: [6, 7, 8, 9, 10], totalStudents: 120, batchSize: 30 };
export function compareLabTiers(catalog, input) {
  const config = catalog.labPlanner;
  assert(config?.mode === 'shop', 503, 'Level comparison needs the shop lab catalog');
  assert(input && Array.isArray(input.skillIds) && input.skillIds.length, 400, 'Choose at least one skill area to compare levels');
  const assumed = [];
  const profile = { ...input, optionalProductIds: [], courses: [], budgetMinor: null, skillTiers: undefined };
  for (const [key, value] of Object.entries(COMPARE_DEFAULTS)) {
    const missing = key === 'rooms' ? !Array.isArray(input.rooms) || !input.rooms.length : key === 'classes' ? !Array.isArray(input.classes) || !input.classes.length : !input[key];
    if (missing) { profile[key] = value; assumed.push(key); }
  }
  if (profile.layout === 'dual' && profile.rooms.length < 2) { profile.layout = 'single'; profile.rooms = profile.rooms.slice(0, 1); }
  if (profile.batchSize > profile.totalStudents) profile.batchSize = profile.totalStudents;
  const product = productId => catalog.products.find(p => p.id === productId);
  const contents = productIds => [...new Set(productIds.filter(Boolean).flatMap(productId => product(productId)?.contents || []))];
  const tiers = config.tiers.map(tier => {
    const plan = buildLabPlan(catalog, { ...profile, tier });
    const quote = calculateShop(catalog, plan.items);
    const gst = Math.round(quote.total * shopGstBasisPoints(catalog, tier) / 10000);
    return { tier, total: quote.total, gst, totalWithGst: quote.total + gst, lines: quote.lines.length };
  });
  tiers.forEach(t => { t.extraOverBasic = t.totalWithGst - tiers[0].totalWithGst; });
  // One row per skill area, then the shared lab equipment and curriculum; each cell lists what that level adds.
  const rows = profile.skillIds.map(skillId => {
    const skill = config.skills.find(s => s.id === skillId);
    return { label: skill?.label || skillId, kind: 'skill', cells: config.tiers.map(tier => { const group = shopLabItems(config, [skillId], tier, 1).groups[0]; return { tier, usedTier: group?.tier || tier, items: contents(Object.values(group?.set || {})) }; }) };
  });
  const sharedRow = (label, map) => ({ label, kind: 'shared', cells: config.tiers.map(tier => { const { pickTier } = shopLabItems(config, [], tier, 1); const productId = pickTier(map || {}); return { tier, usedTier: product(productId)?.tier || tier, items: contents([productId]) }; }) });
  if (profile.board === 'CBSE' && profile.setup !== 'materials_only') rows.push(sharedRow('Lab furniture & safety equipment', config.mandatory));
  rows.push(sharedRow('Curriculum & teacher training', config.curriculum));
  for (const row of rows) {
    row.cells.forEach((cell, i) => {
      // New compared with every lower level, so an item a middle level happens to omit is not "added" again.
      const before = i ? new Set(row.cells.slice(0, i).flatMap(prev => prev.items).map(item => item.toLowerCase())) : null;
      cell.added = before ? cell.items.filter(item => !before.has(item.toLowerCase())) : cell.items;
      cell.sameAsPrevious = !!before && cell.usedTier === row.cells[i - 1].usedTier;
    });
  }
  return { currency: catalog.currency, rooms: profile.rooms.length, assumed, tiers, rows };
}
