import { assert, integer, id, string } from './core.mjs';
import { LAB_CATEGORIES } from './lab-rules.mjs';

// The "shop" catalog model: real storefront data (items, bundles, per-participant courses) with
// per-item GST. All amounts are integers in paise; the language model never sets any of them.
const TIERS = ['basic', 'advanced', 'premium'];
const KINDS = ['item', 'bundle', 'course'];
const optionalString = (value, name, max) => value === null || value === undefined ? null : string(value, name, max);

export function validateShopCatalog(input, db) {
  id(input.revision, 'catalog revision'); string(input.title, 'catalog title');
  assert(typeof input.demo === 'boolean', 400, 'Set catalog.demo explicitly');
  assert(['INR', 'USD', 'EUR', 'GBP'].includes(input.currency), 400, 'Supported currencies: INR, USD, EUR, GBP');
  integer(input.taxBasisPoints, 'tax basis points', 0, 10000); integer(input.deliveryAmount, 'delivery charge', 0, 100000000);
  assert(typeof input.validUntil === 'string' && Date.parse(input.validUntil) > Date.now(), 400, 'Set a future catalog.validUntil');
  assert(Array.isArray(input.products) && input.products.length > 0 && input.products.length <= 1000, 400, 'Shop catalog needs 1–1000 products');
  const ids = new Set();
  const products = input.products.map(p => {
    id(p.id, 'product ID'); assert(!ids.has(p.id), 400, 'Duplicate product ID'); ids.add(p.id);
    assert(KINDS.includes(p.kind), 400, 'Shop product kind must be item, bundle or course');
    assert(typeof p.active === 'boolean', 400, 'Set product.active');
    assert(Array.isArray(p.features) && p.features.length <= 30 && p.features.every(f => typeof f === 'string' && /^[a-z][a-z0-9-]{0,39}$/.test(f)), 400, 'Invalid product features');
    assert(Array.isArray(p.sourceIds) && p.sourceIds.length > 0 && p.sourceIds.every(source => typeof source === 'string' && db.getDocument(source)), 400, 'Every product needs existing source documents');
    assert(['item', 'participant'].includes(p.unit), 400, 'Product unit must be item or participant');
    assert(p.tier === null || p.tier === undefined || TIERS.includes(p.tier), 400, 'Unknown product tier');
    assert(p.url === null || p.url === undefined || (typeof p.url === 'string' && p.url.startsWith('https://') && p.url.length <= 500), 400, 'Product URLs must be HTTPS');
    const value = {
      id: p.id, name: string(p.name, 'product name', 120), kind: p.kind, unitAmount: integer(p.unitAmount, 'unit amount in minor currency units', 1, 1000000000),
      active: p.active, taxBasisPoints: integer(p.taxBasisPoints ?? 0, 'product tax basis points', 0, 10000), features: [...new Set(p.features)], sourceIds: [...new Set(p.sourceIds)], unit: p.unit,
      role: optionalString(p.role, 'product role', 40), category: optionalString(p.category, 'product category', 120), tier: p.tier ?? null, component: optionalString(p.component, 'product component', 40),
      url: p.url ?? null, shopId: p.shopId === undefined ? null : integer(p.shopId, 'shop product ID', 0, 1e12),
    };
    if (p.contents !== undefined) {
      assert(Array.isArray(p.contents) && p.contents.length <= 120, 400, 'Product contents must be a list of up to 120 items');
      value.contents = p.contents.map(item => string(item, 'content item', 120));
    }
    if (p.kind === 'bundle') {
      assert(Array.isArray(p.includes) && p.includes.length <= 200, 400, 'Bundle contents must be a list');
      value.includes = p.includes.map(item => ({ shopId: integer(item.shopId, 'bundle component ID', 0, 1e12), quantity: integer(item.quantity, 'bundle component quantity', 1, 10000), name: string(item.name, 'bundle component name', 120) }));
    }
    if (p.kind === 'course') for (const key of ['courseType', 'startDate', 'endDate', 'location']) value[key] = optionalString(p[key], `course ${key}`, 120);
    return value;
  });
  assert(products.some(p => p.active && p.kind !== 'course'), 400, 'At least one active shop product is required');
  const featureLabels = {};
  for (const [key, label] of Object.entries(input.featureLabels || {})) { assert(/^[a-z][a-z0-9-]{0,39}$/.test(key), 400, 'Invalid feature label key'); featureLabels[key] = string(label, 'feature label', 120); }
  const catalog = { revision: input.revision, title: input.title, demo: input.demo, model: 'shop', currency: input.currency, taxBasisPoints: input.taxBasisPoints, deliveryAmount: input.deliveryAmount, validUntil: input.validUntil, products, featureLabels };
  if (input.source) catalog.source = Object.fromEntries(['name', 'digest', 'fetchedAt', 'shopUrl', 'coursesUrl'].map(key => [key, optionalString(input.source[key], `source ${key}`, 300)]));
  if (input.labPlanner) catalog.labPlanner = validateShopPlanner(input.labPlanner, products);
  return catalog;
}

function validateShopPlanner(input, products) {
  const active = productId => products.some(p => p.id === productId && p.active);
  const tierMap = (map, name) => {
    assert(map && typeof map === 'object', 400, `Planner ${name} must map tiers to products`);
    const out = {};
    for (const [tier, productId] of Object.entries(map)) { assert(TIERS.includes(tier) && active(productId), 400, `Planner ${name} references an unknown product`); out[tier] = productId; }
    return out;
  };
  assert(Array.isArray(input.skills) && input.skills.length > 0 && input.skills.length <= 30, 400, 'Planner needs 1–30 skill areas');
  const skills = input.skills.map(skill => {
    assert(/^[a-z][a-z0-9-]{0,39}$/.test(skill.id), 400, 'Invalid skill ID');
    assert(LAB_CATEGORIES.some(c => c.id === skill.category), 400, 'Unknown skill work category');
    const tiers = {};
    for (const [tier, set] of Object.entries(skill.tiers || {})) {
      assert(TIERS.includes(tier) && set && typeof set === 'object', 400, 'Invalid skill tier');
      tiers[tier] = {};
      for (const [component, productId] of Object.entries(set)) { assert(['lab', 'kit', 'consumable', 'set'].includes(component) && active(productId), 400, 'Skill tier references an unknown product'); tiers[tier][component] = productId; }
    }
    assert(Object.keys(tiers).length, 400, 'Each skill area needs at least one tier');
    return { id: skill.id, label: string(skill.label, 'skill name', 120), category: skill.category, needsWater: skill.needsWater === true, tiers, items: [] };
  });
  assert(new Set(skills.map(s => s.id)).size === skills.length, 400, 'Duplicate skill IDs');
  const optionalProductIds = Array.isArray(input.optionalProductIds) ? input.optionalProductIds.filter(active) : [];
  return {
    mode: 'shop', kitsPerRoom: integer(input.kitsPerRoom, 'kits per room', 1, 50), tiers: TIERS, skills,
    mandatory: tierMap(input.mandatory || {}, 'mandatory equipment'), curriculum: tierMap(input.curriculum || {}, 'curriculum'), digital: tierMap(input.digital || {}, 'digital lab'),
    installation: input.installation && active(input.installation) ? input.installation : null, optionalProductIds,
    bundleIds: (input.bundleIds || []).filter(productId => products.some(p => p.id === productId && p.kind === 'bundle' && p.active)),
  };
}

export function calculateShop(catalog, items, title = null) {
  assert(Date.parse(catalog.validUntil) > Date.now(), 409, 'Pricing has expired; ask the team to publish current pricing');
  assert(Array.isArray(items) && items.length > 0 && items.length <= 80, 400, 'Choose 1–80 line items');
  const seen = new Set();
  const lines = items.map(item => {
    assert(item && typeof item === 'object' && !Array.isArray(item), 400, 'Invalid quote line');
    id(item.productId, 'product ID'); assert(!seen.has(item.productId), 400, 'Duplicate quote line'); seen.add(item.productId);
    const p = catalog.products.find(p => p.id === item.productId && p.active); assert(p, 400, 'Unknown or unavailable product');
    const quantity = integer(item.quantity, 'quantity', 1, 100000);
    const lineAmount = p.unitAmount * quantity;
    return { productId: p.id, name: p.name, kind: p.kind, unit: p.unit, quantity, unitAmount: p.unitAmount, lineAmount, taxBasisPoints: p.taxBasisPoints, tax: Math.round(lineAmount * p.taxBasisPoints / 10000), sourceIds: p.sourceIds };
  });
  const subtotal = lines.reduce((sum, line) => sum + line.lineAmount, 0);
  const tax = lines.reduce((sum, line) => sum + line.tax, 0) + Math.round(subtotal * catalog.taxBasisPoints / 10000);
  const total = subtotal + tax + catalog.deliveryAmount;
  integer(total, 'quote total', 1, 1e12);
  return { catalogRevision: catalog.revision, demo: catalog.demo, model: 'shop', currency: catalog.currency, billing: 'one-time', title: title || lines[0].name, lines, capacity: null, subtotal, tax, delivery: catalog.deliveryAmount, total, sourceIds: [...new Set(lines.flatMap(l => l.sourceIds))] };
}

export const quoteFeatures = (catalog, quote) => new Set(quote.lines.flatMap(line => catalog.products.find(p => p.id === line.productId)?.features || []));
const tierLabel = tier => tier[0].toUpperCase() + tier.slice(1);

// Shop lab composition: per selected skill area, one Lab plus N Kit and N Consumable sets per room
// (the shop's own bundle convention), in the requested tier or the nearest available one.
// skillTiers optionally gives an individual level per skill area (skill ID -> tier); others use `tier`.
export function shopLabItems(planner, skillIds, tier, rooms, skillTiers = {}) {
  const items = new Map(); const notes = []; const groups = [];
  const add = (productId, quantity) => { if (productId) items.set(productId, (items.get(productId) || 0) + quantity); };
  const orderFor = want => [want, ...TIERS.slice(0, TIERS.indexOf(want)).reverse(), ...TIERS.slice(TIERS.indexOf(want) + 1)];
  const order = orderFor(tier);
  for (const skillId of skillIds) {
    const skill = planner.skills.find(s => s.id === skillId); if (!skill) continue;
    const want = skillTiers[skillId] || tier;
    const used = orderFor(want).find(t => skill.tiers[t]);
    if (used !== want) notes.push(`${skill.label} has no ${tierLabel(want)} tier in the shop; the ${tierLabel(used)} set is used.`);
    const set = skill.tiers[used];
    add(set.lab, rooms); add(set.kit, planner.kitsPerRoom * rooms); add(set.consumable, planner.kitsPerRoom * rooms); add(set.set, rooms);
    groups.push({ skillId, label: skill.label, tier: used, set });
  }
  const pickTier = map => map[order.find(t => map[t])];
  return { items, notes, groups, pickTier };
}

export function recommendShop(catalog, constraints) {
  const wanted = constraints.features || [];
  const planner = catalog.labPlanner;
  const candidates = [];
  const push = (items, title, group) => {
    const quote = calculateShop(catalog, items, title);
    candidates.push({ ...quote, items, group, features: [...quoteFeatures(catalog, quote)] });
  };
  for (const bundle of catalog.products.filter(p => p.kind === 'bundle' && p.active)) push([{ productId: bundle.id, quantity: 1 }], bundle.name, 'bundle');
  const skillIds = planner ? planner.skills.filter(s => wanted.includes(s.id)).map(s => s.id) : [];
  if (skillIds.length) {
    for (const tier of TIERS) {
      const { items, pickTier } = shopLabItems(planner, skillIds, tier, 1);
      const curriculum = pickTier(planner.curriculum); if (curriculum) items.set(curriculum, 1);
      if (planner.installation) items.set(planner.installation, 1);
      const labels = skillIds.map(id => planner.skills.find(s => s.id === id).label).join(' + ');
      push([...items].map(([productId, quantity]) => ({ productId, quantity })), `Custom ${tierLabel(tier)} lab · ${labels}`, `custom-${tier}`);
    }
  }
  const learners = constraints.learners || 1;
  const courses = catalog.products.filter(p => p.kind === 'course' && p.active && p.features.some(f => wanted.includes(f)));
  if (courses.length) {
    const chosen = courses.slice(0, 6);
    push(chosen.map(p => ({ productId: p.id, quantity: learners })), `Courses for ${learners} participant${learners === 1 ? '' : 's'} · ${chosen.map(p => p.name).join(', ')}`.slice(0, 200), 'courses');
    // Lab + matching courses together when both were asked for.
    const custom = candidates.find(c => c.group === 'custom-basic');
    if (custom) push([...custom.items, ...chosen.filter(p => !custom.items.some(i => i.productId === p.id)).map(p => ({ productId: p.id, quantity: learners }))], `${custom.title} + courses`, 'custom-courses');
  }
  const covers = q => wanted.every(f => q.features.includes(f));
  const covering = (wanted.length ? candidates.filter(covers) : candidates.filter(q => q.group === 'bundle')).sort((a, b) => a.total - b.total || a.lines.length - b.lines.length);
  const within = constraints.budgetMinor == null ? covering : covering.filter(q => q.total <= constraints.budgetMinor);
  const distinct = within.filter((q, i, all) => all.findIndex(o => o.group === q.group) === i);
  if (distinct.length) return { matches: distinct.slice(0, 3), nearest: null };
  if (covering.length) return { matches: [], nearest: covering[0] };
  const partial = [...candidates].sort((a, b) => wanted.filter(f => b.features.includes(f)).length - wanted.filter(f => a.features.includes(f)).length || a.total - b.total);
  return { matches: [], nearest: partial[0] || null };
}
