import { assert, integer, string } from './core.mjs';

export const LAB_RULES_VERSION = '2026-10-23-v1';
export const LAB_CATEGORIES = [
  { id: 'life_forms', name: 'Life forms' },
  { id: 'machines_materials', name: 'Machines & materials' },
  { id: 'human_services', name: 'Human services' },
];
export const LAB_SOURCES = [
  { title: 'CBSE Skill-13/2026 · Composite Skill Labs', url: 'https://cbseacademic.nic.in/web_material/Circulars/2026/13_Circular_2026.pdf', verifiedAt: '2026-10-23', scope: 'Room configurations and class groups; equipment lists are suggestive.' },
  { title: 'CBSE Skill-75/2024', url: 'https://www.cbse.gov.in/cbsenew/documents/75_Circular_2024_Composite_Skill_Labs_27082024.pdf', verifiedAt: '2026-10-23', scope: 'Original space guidance.' },
  { title: 'RoboQuest planning brief · 23 October 2026', url: null, scope: 'Product planning baseline: at least three selected courses, one in each work category. This is a business rule, not an independently verified CBSE course-count requirement.' },
];

export function validateLabProfile(input, skills) {
  assert(input && typeof input === 'object', 400, 'Lab profile is required');
  assert(['CBSE', 'State Board', 'ICSE', 'Other'].includes(input.board), 400, 'Choose a school board');
  assert(['infrastructure_materials', 'materials_only', 'existing_room'].includes(input.setup), 400, 'Choose a lab setup');
  assert(['single', 'dual'].includes(input.layout), 400, 'Choose one room or two separate rooms');
  assert(Array.isArray(input.rooms) && input.rooms.length === (input.layout === 'dual' ? 2 : 1), 400, 'Enter the area of each selected room');
  const rooms = input.rooms.map((room, i) => {
    assert(room && typeof room === 'object' && typeof room.areaSqFt === 'number' && Number.isFinite(room.areaSqFt) && room.areaSqFt > 0 && room.areaSqFt <= 100000, 400, 'Room areas must be positive square-foot values');
    return { areaSqFt: room.areaSqFt, classGroup: input.layout === 'single' ? 'VI–XII' : i === 0 ? 'VI–X' : 'XI–XII' };
  });
  assert(Array.isArray(input.classes) && input.classes.length > 0 && input.classes.length <= 7 && input.classes.every(c => Number.isInteger(c) && c >= 6 && c <= 12), 400, 'Choose classes VI–XII');
  assert(new Set(input.classes).size === input.classes.length, 400, 'Duplicate classes');
  const totalStudents = integer(input.totalStudents, 'total student strength', 1, 100000);
  const batchSize = integer(input.batchSize, 'batch size', 1, 1000);
  assert(batchSize <= totalStudents, 400, 'Batch size cannot exceed total student strength');
  assert(Array.isArray(input.skillIds) && input.skillIds.length > 0 && input.skillIds.length <= skills.length && input.skillIds.every(id => skills.some(s => s.id === id)), 400, 'Choose valid skill courses');
  assert(new Set(input.skillIds).size === input.skillIds.length, 400, 'Duplicate skill courses');
  const readiness = {};
  for (const key of ['ventilation', 'electrical', 'accessibility', 'water', 'safety']) {
    const value = input.readiness?.[key] ?? 'unknown';
    assert(['yes', 'no', 'unknown'].includes(value), 400, 'Readiness answers must be yes, no or unknown'); readiness[key] = value;
  }
  const goals = input.goals === undefined || input.goals === '' ? '' : string(input.goals, 'school objectives', 1600);
  assert(input.board === 'CBSE' || goals.length >= 8, 400, 'Describe the school objectives for a non-CBSE lab plan');
  const budgetMinor = input.budgetMinor == null ? null : integer(input.budgetMinor, 'total budget', 1, 10000000000);
  return { board: input.board, setup: input.setup, layout: input.layout, rooms, classes: [...input.classes].sort((a, b) => a - b), totalStudents, batchSize, skillIds: [...input.skillIds], readiness, goals, budgetMinor };
}

export function assessLab(profile, skills) {
  const selected = skills.filter(s => profile.skillIds.includes(s.id));
  const observations = [];
  const add = (id, status, title, detail, basis) => observations.push({ id, status, title, detail, basis });
  if (profile.board === 'CBSE') {
    const minimum = profile.layout === 'single' ? 600 : 400;
    profile.rooms.forEach((room, i) => add(`room-${i}`, room.areaSqFt >= minimum ? 'meets_planning_check' : 'gap', `Room ${i + 1} · ${room.classGroup}`, `${room.areaSqFt} sq. ft.; this configuration uses a minimum ${minimum} sq. ft. per room. Areas are checked individually, not summed.`, 'CBSE Skill-13/2026'));
    const missing = LAB_CATEGORIES.filter(category => !selected.some(skill => skill.category === category.id));
    add('course-coverage', selected.length >= 3 && !missing.length ? 'meets_planning_check' : 'gap', 'Three-category course coverage', missing.length ? `Missing categories: ${missing.map(c => c.name).join(', ')}. Select at least one course from each category and at least three courses in total.` : `${selected.length} courses span all three work categories.`, 'RoboQuest planning brief; course-count rule requires school/board confirmation');
    add('formal-review', 'review', 'School and site review', 'This checks selected planning inputs only. It does not certify affiliation, safety, course approval or full CBSE compliance.', 'Scope of this planner');
  } else {
    add('board-scope', 'not_assessed', `${profile.board} planning`, 'CBSE room-size and course-count checks are not applied. Confirm requirements with the relevant board and the school.', 'Board-specific review');
    add('objectives', profile.goals ? 'recorded' : 'review', 'School objectives', profile.goals || 'Add the school’s objectives to refine the recommendation.', 'School input');
  }
  for (const [key, label] of [['ventilation', 'Ventilation'], ['electrical', 'Electrical readiness'], ['accessibility', 'Accessibility'], ['safety', 'Safety and supervision']]) {
    add(`readiness-${key}`, profile.readiness[key] === 'yes' ? 'self_reported' : 'review', label, profile.readiness[key] === 'yes' ? 'Reported ready by the school; site verification is still needed.' : 'Needs assessment or improvement before commissioning.', 'School readiness declaration');
  }
  if (selected.some(s => s.needsWater)) add('readiness-water', profile.readiness.water === 'yes' ? 'self_reported' : 'review', 'Water and drainage', profile.readiness.water === 'yes' ? 'Reported available; confirm the selected activities’ needs.' : 'A selected course needs water; confirm supply and drainage.', 'Selected course planning assumption');
  if (profile.setup === 'materials_only') add('materials-scope', 'review', 'Materials-only scope', 'Furniture, electrical work, room preparation and installation are excluded from this quote; the school must provide them.', 'Selected commercial scope');
  if (profile.setup === 'existing_room') add('existing-scope', 'review', 'Existing-room scope', 'Room fit-out is excluded. Existing furniture, utility points and safety arrangements must be checked before installation.', 'Selected commercial scope');
  const activeRooms = profile.layout === 'single' ? 1 : Number(profile.classes.some(c => c <= 10)) + Number(profile.classes.some(c => c >= 11));
  return { rulesVersion: LAB_RULES_VERSION, status: observations.some(o => o.status === 'gap') ? 'planning_gaps' : 'manual_review_required', observations, selectedSkills: selected, activeRooms, batches: Math.ceil(profile.totalStudents / profile.batchSize), sources: LAB_SOURCES };
}

export function validatePlannerCatalog(input, products) {
  if (input === undefined) return undefined;
  assert(input && typeof input === 'object', 400, 'Invalid labPlanner catalog configuration');
  const core = products.find(p => p.id === input.coreProductId && p.kind === 'package' && p.active);
  assert(core, 400, 'Lab planner needs an active core package');
  assert(core.salesChannel === 'lab_planner', 400, 'Lab core products must use the lab_planner sales channel');
  integer(input.studentsPerStation, 'students per station', 1, 10);
  assert(core.capacity === input.studentsPerStation, 400, 'Lab core capacity must match studentsPerStation');
  const validAddon = productId => products.some(p => p.id === productId && p.active && p.kind === 'addon' && p.salesChannel === 'lab_planner' && p.compatibleWith.includes(core.id));
  assert(validAddon(input.infrastructureProductId), 400, 'Lab planner needs a compatible infrastructure product');
  assert(Array.isArray(input.skills) && input.skills.length > 0 && input.skills.length <= 12, 400, 'Lab planner needs 1–12 skill courses');
  const ids = new Set();
  const skills = input.skills.map(skill => {
    string(skill.id, 'skill ID', 60); assert(/^[a-z0-9-]+$/.test(skill.id) && !ids.has(skill.id), 400, 'Invalid or duplicate skill ID'); ids.add(skill.id);
    string(skill.label, 'skill name', 120);
    assert(LAB_CATEGORIES.some(c => c.id === skill.category), 400, 'Unknown skill category');
    assert(validAddon(skill.productId), 400, 'Skill needs a compatible materials product');
    assert(typeof skill.needsWater === 'boolean', 400, 'Set skill.needsWater');
    assert(Array.isArray(skill.items) && skill.items.length > 0 && skill.items.length <= 12 && skill.items.every(item => typeof item === 'string' && item.length <= 180), 400, 'Supply a concise required-item list for each skill');
    return { id: skill.id, label: skill.label, category: skill.category, productId: skill.productId, needsWater: skill.needsWater, items: skill.items };
  });
  assert(new Set(skills.map(s => s.productId)).size === skills.length, 400, 'Each skill must map to a distinct materials product');
  assert(!skills.some(s => s.productId === input.infrastructureProductId), 400, 'Infrastructure cannot also be a course product');
  assert(Array.isArray(input.optionalProductIds) && input.optionalProductIds.length <= 6 && input.optionalProductIds.every(validAddon), 400, 'Invalid optional upgrades');
  assert(!input.optionalProductIds.some(id => id === input.infrastructureProductId || skills.some(s => s.productId === id)), 400, 'Optional upgrades must be separate products');
  return { coreProductId: core.id, studentsPerStation: input.studentsPerStation, infrastructureProductId: input.infrastructureProductId, skills, optionalProductIds: [...new Set(input.optionalProductIds)] };
}
