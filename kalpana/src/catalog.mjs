import { assert, integer, id, string, hash } from './core.mjs';
import { validatePlannerCatalog } from './lab-rules.mjs';
import { validateShopCatalog, calculateShop, recommendShop } from './shop.mjs';

export function validateCatalog(input, db) {
  assert(input && typeof input === 'object', 400, 'Catalog is required');
  if (input.model === 'shop') return validateShopCatalog(input, db);
  id(input.revision, 'catalog revision'); string(input.title, 'catalog title');
  assert(typeof input.demo === 'boolean', 400, 'Set catalog.demo explicitly');
  assert(['INR', 'USD', 'EUR', 'GBP'].includes(input.currency), 400, 'Supported currencies: INR, USD, EUR, GBP');
  integer(input.taxBasisPoints, 'tax basis points', 0, 10000); integer(input.deliveryAmount, 'delivery charge', 0, 100000000);
  assert(typeof input.validUntil === 'string' && Number.isFinite(Date.parse(input.validUntil)) && Date.parse(input.validUntil) > Date.now(), 400, 'Set a future catalog.validUntil');
  assert(Array.isArray(input.products) && input.products.length > 0 && input.products.length <= 48, 400, 'Catalog needs 1–48 products');
  const ids = new Set(); let addonCount = 0;
  const products = input.products.map(p => {
    id(p.id, 'product ID'); assert(!ids.has(p.id), 400, 'Duplicate product ID'); ids.add(p.id);
    string(p.name, 'product name', 120);
    assert(['package', 'addon'].includes(p.kind), 400, 'Product kind must be package or addon');
    integer(p.unitAmount, 'unit amount in minor currency units', 1, 100000000);
    assert(typeof p.active === 'boolean', 400, 'Set product.active');
    assert(Array.isArray(p.features) && p.features.length <= 30 && p.features.every(f => typeof f === 'string' && /^[a-z][a-z0-9-]{0,39}$/.test(f)), 400, 'Invalid product features');
    assert(Array.isArray(p.sourceIds) && p.sourceIds.length > 0 && p.sourceIds.length <= 10 && p.sourceIds.every(source => typeof source === 'string' && db.getDocument(source)), 400, 'Every product needs existing source documents');
    const value = { id: p.id, name: p.name, kind: p.kind, unitAmount: p.unitAmount, active: p.active, features: [...new Set(p.features)], sourceIds: [...new Set(p.sourceIds)] };
    if (p.salesChannel !== undefined) { assert(['general', 'lab_planner'].includes(p.salesChannel), 400, 'Unknown sales channel'); value.salesChannel = p.salesChannel; }
    if (p.kind === 'package') value.capacity = integer(p.capacity, 'learner capacity', 1, 100000);
    else {
      addonCount++;
      assert(Array.isArray(p.compatibleWith) && p.compatibleWith.length > 0 && p.compatibleWith.every(v => typeof v === 'string'), 400, 'Add-ons need compatible package IDs');
      value.compatibleWith = [...new Set(p.compatibleWith)];
    }
    return value;
  });
  assert(products.some(p => p.kind === 'package' && p.active), 400, 'At least one active package is required');
  assert(addonCount <= 8, 400, 'At most eight add-ons per catalog');
  for (const p of products.filter(p => p.kind === 'addon')) assert(p.compatibleWith.every(packageId => products.some(v => v.id === packageId && v.kind === 'package')), 400, 'Unknown compatible package');
  const catalog = { revision: input.revision, title: input.title, demo: input.demo, currency: input.currency, taxBasisPoints: input.taxBasisPoints, deliveryAmount: input.deliveryAmount, validUntil: input.validUntil, products };
  const planner = validatePlannerCatalog(input.labPlanner, products);
  if (planner) catalog.labPlanner = planner;
  return catalog;
}

export function publishCatalog(input, db) {
  const catalog = validateCatalog(input, db);
  const payload = JSON.stringify(catalog); const digest = hash(payload);
  const previous = db.sql.prepare('SELECT digest FROM catalogs WHERE revision=?').get(catalog.revision);
  assert(!previous || previous.digest === digest, 409, 'Catalog revisions are immutable; publish a new revision');
  db.transaction(() => {
    db.sql.prepare('INSERT INTO catalogs VALUES (?,?,?,?) ON CONFLICT(revision) DO NOTHING').run(catalog.revision, digest, payload, new Date().toISOString());
    db.set('catalog', catalog.revision); db.audit('catalog.publish', catalog.revision);
  });
  return catalog;
}

export function calculate(catalog, items, title) {
  if (catalog.model === 'shop') return calculateShop(catalog, items, title);
  assert(Date.parse(catalog.validUntil) > Date.now(), 409, 'Pricing has expired; ask the team to publish current pricing');
  assert(Array.isArray(items) && items.length > 0 && items.length <= 20, 400, 'Choose 1–20 line items');
  const seen = new Set();
  const lines = items.map(item => {
    assert(item && typeof item === 'object' && !Array.isArray(item), 400, 'Invalid quote line');
    id(item.productId, 'product ID'); assert(!seen.has(item.productId), 400, 'Duplicate quote line'); seen.add(item.productId);
    const p = catalog.products.find(p => p.id === item.productId && p.active); assert(p, 400, 'Unknown or unavailable product');
    const quantity = integer(item.quantity, 'quantity', 1, 100);
    return { productId: p.id, name: p.name, kind: p.kind, quantity, unitAmount: p.unitAmount, lineAmount: p.unitAmount * quantity, sourceIds: p.sourceIds };
  });
  const packages = lines.filter(l => l.kind === 'package'); assert(packages.length > 0, 400, 'A core package is required');
  assert(packages.length === 1, 400, 'Choose one core package per quote; increase its quantity to cover more learners');
  for (const line of lines.filter(l => l.kind === 'addon')) {
    const addon = catalog.products.find(p => p.id === line.productId);
    const compatibleUnits = packages.filter(l => addon.compatibleWith.includes(l.productId)).reduce((sum, l) => sum + l.quantity, 0);
    assert(compatibleUnits >= line.quantity, 400, 'Add-on quantity exceeds compatible package units');
  }
  const subtotal = lines.reduce((sum, line) => sum + line.lineAmount, 0);
  const tax = Math.round(subtotal * catalog.taxBasisPoints / 10000);
  const total = subtotal + tax + catalog.deliveryAmount;
  integer(total, 'quote total', 1, 10000000000);
  const capacity = packages.reduce((sum, line) => sum + line.quantity * catalog.products.find(p => p.id === line.productId).capacity, 0);
  return { catalogRevision: catalog.revision, demo: catalog.demo, currency: catalog.currency, billing: 'one-time', lines, capacity, subtotal, tax, delivery: catalog.deliveryAmount, total, sourceIds: [...new Set(lines.flatMap(l => l.sourceIds))] };
}

// Exhaustive bounded combinations: choose a core package and optional compatible add-ons.
// Price, tax, capacity and compatibility are computed here, never by the language model.
export function recommend(catalog, constraints) {
  if (catalog.model === 'shop') return recommendShop(catalog, constraints);
  const candidates = [];
  const packages = catalog.products.filter(p => p.active && p.kind === 'package' && p.salesChannel !== 'lab_planner');
  const wanted = constraints.features || [];
  for (const pack of packages) {
    if (constraints.packageIds?.length && !constraints.packageIds.includes(pack.id)) continue;
    const quantity = Math.ceil((constraints.learners || 1) / pack.capacity);
    if (quantity > 100) continue;
    const addons = catalog.products.filter(p => p.active && p.kind === 'addon' && p.compatibleWith.includes(pack.id));
    for (let mask = 0; mask < 2 ** addons.length; mask++) {
      const selected = addons.filter((_, i) => mask & (1 << i));
      const features = new Set([ ...pack.features, ...selected.flatMap(p => p.features) ]);
      if (!wanted.every(f => features.has(f))) continue;
      const items = [pack, ...selected].map(p => ({ productId: p.id, quantity }));
      const quote = calculate(catalog, items);
      candidates.push({ ...quote, features: [...features], items });
    }
  }
  candidates.sort((a, b) => a.total - b.total || a.lines.length - b.lines.length);
  // Cheapest valid customization of each distinct core package.
  const unique = candidates.filter((q, index, all) => all.findIndex(other => other.lines[0].productId === q.lines[0].productId) === index);
  const within = constraints.budgetMinor == null ? unique : unique.filter(q => q.total <= constraints.budgetMinor);
  return { matches: within.slice(0, 3), nearest: within.length ? null : unique[0] || null };
}
