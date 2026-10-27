import { createHash } from 'node:crypto';
import { ApiError } from './core.mjs';

// Skillvation Club Shop + MySkool collab courses are the source of products.
// Fetching is strict: a partial or malformed response throws, so a working catalog is never
// replaced by an incomplete one. Shop and course prices are whole rupees; Kalpana stores paise.
export const SKILLVATION_DEFAULTS = {
  shopUrl: 'https://devpro.skillvation.com/club-shop',
  coursesUrl: 'https://collab.myskool.club',
  kitsPerRoom: 4,
};
export const COURSE_TYPES = [
  { key: 's2s', query: 'type=8&upskill=0', feature: 's2s-course', label: 'S2S course' },
  { key: 'u4t', query: 'type=8&upskill=1', feature: 'u4t-course', label: 'U4T course' },
  { key: 'ttt', query: 'type=10', feature: 'ttt-course', label: 'TTT course (teacher training)' },
];
export const TIERS = ['basic', 'advanced', 'premium'];
const MAX_BYTES = 5 * 1024 * 1024;
const COURSE_STOPWORDS = new Set(['with', 'tools', 'skill', 'point', 'sale', 'reality', 'virtual', 'communication', 'production', 'thinking']);
const sha = value => createHash('sha256').update(value).digest('hex');
const fail = message => { throw new ApiError(502, `Skillvation sync failed: ${message}`); };

async function getJson(fetchFn, url, { retries = 2, timeout = 20000 } = {}) {
  let lastError;
  for (let attempt = 0; attempt <= retries; attempt++) {
    try {
      const response = await fetchFn(url, { headers: { Accept: 'application/json' }, signal: AbortSignal.timeout(timeout) });
      if (response.status >= 500) throw Error(`HTTP ${response.status}`);
      if (!response.ok) fail(`${url} returned HTTP ${response.status}`);
      const text = await response.text();
      if (text.length > MAX_BYTES) fail(`${url} response is too large`);
      if (!text.trim()) throw Error('empty response');
      try { return JSON.parse(text); } catch { throw Error('invalid JSON'); }
    } catch (error) {
      if (error instanceof ApiError) throw error;
      lastError = error;
      if (attempt < retries) await new Promise(resolve => setTimeout(resolve, 400 * (attempt + 1)));
    }
  }
  fail(`${url}: ${lastError?.message || 'unreachable'}`);
}

async function inBatches(items, size, task) {
  const results = [];
  for (let i = 0; i < items.length; i += size) results.push(...await Promise.all(items.slice(i, i + size).map(task)));
  return results;
}

const isRupees = value => typeof value === 'number' && Number.isFinite(value) && value >= 0 && value < 1e8;

export async function fetchSkillvation({ fetch: fetchFn = fetch, shopUrl = SKILLVATION_DEFAULTS.shopUrl, coursesUrl = SKILLVATION_DEFAULTS.coursesUrl } = {}) {
  const shop = shopUrl.replace(/\/$/, ''); const collab = coursesUrl.replace(/\/$/, '');
  const categories = await getJson(fetchFn, `${shop}/api/categories`);
  if (categories?.status !== 'success' || !Array.isArray(categories.data) || !categories.data.length) fail('categories response is not a non-empty list');

  const products = [];
  let expectedProducts = null;
  for (let page = 1, pages = 1; page <= pages && page <= 50; page++) {
    const body = await getJson(fetchFn, `${shop}/api/products?per_page=100&page=${page}`);
    if (body?.status !== 'success' || !Array.isArray(body.data)) fail(`products page ${page} is malformed`);
    products.push(...body.data);
    pages = Number.isInteger(body.pagination?.pages) ? body.pagination.pages : 1;
    if (pages < 1 || pages > 50) fail('product pagination exceeds the supported limit');
    if (Number.isInteger(body.pagination?.total)) {
      if (expectedProducts !== null && expectedProducts !== body.pagination.total) fail('product total changed while fetching; retry the sync');
      expectedProducts = body.pagination.total;
    }
    if (page < pages && !body.data.length) fail(`products page ${page} is unexpectedly empty`);
    if (page === 1 && Number.isInteger(body.pagination?.total) && body.pagination.total > 0 && !body.data.length) fail('products page 1 is empty although products exist');
  }
  if (!products.length) fail('the shop returned no products');
  if (expectedProducts !== null && products.length !== expectedProducts) fail('the product list is incomplete');
  const ids = new Set(products.map(p => p.id));
  if (ids.size !== products.length) fail('the shop returned duplicate product IDs across pages');

  // Details carry bundle components and descriptions that the list endpoint leaves out.
  const details = await inBatches(products, 8, async product => {
    const body = await getJson(fetchFn, `${shop}/api/products/${encodeURIComponent(product.id)}`);
    if (body?.status !== 'success' || !body.data || body.data.id !== product.id) fail(`product ${product.id} details are malformed`);
    return body.data;
  });

  const courses = {};
  for (const type of COURSE_TYPES) {
    courses[type.key] = [];
    let url = `${collab}/api/collab-courses?${type.query}`;
    const seenPages = new Set(); let expectedCourses = null;
    for (let page = 0; url && page < 20; page++) {
      if (seenPages.has(url)) fail(`${type.label} pagination repeats a page`);
      seenPages.add(url);
      const body = await getJson(fetchFn, url);
      const list = body?.courses?.data;
      if (!Array.isArray(list)) fail(`${type.label} response is malformed`);
      if (Number.isInteger(body.courses.total)) {
        if (expectedCourses !== null && expectedCourses !== body.courses.total) fail(`${type.label} total changed during sync`);
        expectedCourses = body.courses.total;
      }
      courses[type.key].push(...list);
      const next = body.courses.next_page_url;
      // Only follow pagination on the configured course host, keeping the type filter.
      if (next) {
        const nextUrl = new URL(next, collab);
        if (nextUrl.origin !== new URL(collab).origin) fail(`${type.label} pagination changed hosts`);
        for (const [key, value] of new URLSearchParams(type.query)) nextUrl.searchParams.set(key, value);
        url = nextUrl.href;
      } else url = null;
    }
    if (url) fail(`${type.label} pagination exceeds the supported limit`);
    const rows = courses[type.key];
    if (expectedCourses !== null && rows.length !== expectedCourses) fail(`${type.label} list is incomplete`);
    if (new Set(rows.map(c => c.id)).size !== rows.length) fail(`${type.label} list contains duplicate IDs`);
  }
  return { fetchedAt: new Date().toISOString(), shopUrl: shop, coursesUrl: collab, categories: categories.data, products, details, courses };
}

const featureId = slug => String(slug).toLowerCase().replace(/[^a-z0-9-]+/g, '-').replace(/^-+/, '').slice(0, 40).replace(/-+$/, '') || 'general';
const tierOf = title => /premium/i.test(title) ? 'premium' : /advanc/i.test(title) ? 'advanced' : /basic/i.test(title) ? 'basic' : null;
const componentOf = title => /consumabl/i.test(title) ? 'consumable' : /\bkit\b/i.test(title) ? 'kit' : /\blab\b/i.test(title) ? 'lab' : 'set';
const rupees = amount => new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 2 }).format(amount);
// CBSE composite skill labs group skills into three work categories. This mapping is a planning
// default derived from category names; categories that match nothing count as machines & materials.
function workCategory(slug) {
  if (/agricult|garden|horticult|food|animal|fisher|forest/.test(slug)) return 'life_forms';
  if (/health|beauty|wellness|media|retail|pos|tourism|financ|hospitality|care/.test(slug)) return 'human_services';
  return 'machines_materials';
}
const needsWater = slug => /agricult|garden|food|pottery|beauty/.test(slug);

// The shop describes each kit, lab set and consumable pack as an HTML table (Item | Specification | Qty …).
// Kalpana keeps the item names so it can show what each level actually contains. Rows with quantity 0 are skipped.
export function parseContents(html) {
  const text = value => String(value).replace(/<[^>]+>/g, ' ').replace(/&nbsp;/gi, ' ').replace(/&amp;/gi, '&').replace(/&quot;/gi, '"').replace(/&#39;/gi, "'").replace(/\s+/g, ' ').trim();
  const rows = [...String(html || '').matchAll(/<tr[^>]*>([\s\S]*?)<\/tr>/gi)].map(m => [...m[1].matchAll(/<t[dh][^>]*>([\s\S]*?)<\/t[dh]>/gi)].map(c => text(c[1])));
  if (rows.length < 2) return [];
  const header = rows[0].map(h => h.toLowerCase());
  const column = test => header.findIndex(test);
  const item = column(h => /^item/.test(h)); const qty = column(h => /^qty|quantity/.test(h));
  if (item < 0) return [];
  const seen = new Set(); const contents = [];
  for (const row of rows.slice(1)) {
    const name = (row[item] || '').replace(/\)$/, s => (/\(/.test(row[item]) ? s : '')).slice(0, 120);
    if (!name || (qty >= 0 && /^0+$/.test(row[qty] || '')) || seen.has(name.toLowerCase())) continue;
    seen.add(name.toLowerCase()); contents.push(name);
    if (contents.length === 120) break;
  }
  return contents;
}

export function buildSkillvationCatalog(snapshot, { kitsPerRoom = SKILLVATION_DEFAULTS.kitsPerRoom, now = new Date() } = {}) {
  const warnings = [];
  const categories = new Map(snapshot.categories.filter(c => Number.isInteger(c.id) && typeof c.name === 'string').map(c => [c.id, { ...c, slug: c.slug || c.name }]));
  const detailById = new Map(snapshot.details.map(d => [d.id, d]));
  const referenced = new Set(snapshot.details.flatMap(d => (d.bundle_components || []).filter(c => !Number(c.is_optional)).map(c => Number(c.component_product_id))));

  const products = []; const shopRows = [];
  for (const raw of snapshot.products) {
    const detail = detailById.get(raw.id) || {};
    const price = isRupees(raw.price_discounted) ? raw.price_discounted : raw.price;
    if (!Number.isInteger(raw.id) || typeof raw.title !== 'string' || !isRupees(price) || price <= 0) { warnings.push(`Skipped shop product ${raw.id ?? '?'}: missing title or a valid price.`); continue; }
    if (raw.currency !== 'INR') { warnings.push(`Skipped "${raw.title}": currency ${raw.currency} is not supported.`); continue; }
    const category = categories.get(raw.category_id) || categories.get(detail.category?.id);
    if (!category) { warnings.push(`Skipped "${raw.title}": unknown category ${raw.category_id}.`); continue; }
    const gst = raw.vat_rate > 0 ? raw.vat_rate : (raw.cgst_rate || 0) + (raw.sgst_rate || 0);
    const bundle = detail.is_bundle === true;
    const slug = String(category.slug).toLowerCase();
    const role = bundle ? 'bundle' : slug === 'general' ? (/curriculum|training/i.test(raw.title) ? 'curriculum' : /install|transport/i.test(raw.title) ? 'installation' : /digital/i.test(raw.title) ? 'digital' : 'general')
      : /mandatory/.test(slug) ? 'mandatory' : /skill-lab/.test(slug) ? 'general' : 'skill';
    const product = {
      id: `sv-${raw.id}`, name: raw.title.replace(/\s+/g, ' ').trim().slice(0, 120), kind: bundle ? 'bundle' : 'item',
      unitAmount: Math.round(price * 100), active: (raw.stock ?? 1) > 0, taxBasisPoints: Math.round(gst * 100),
      features: role === 'skill' ? [featureId(slug)] : role === 'bundle' ? [] : [role === 'curriculum' ? 'teacher-training' : role === 'digital' ? 'digital-lab' : featureId(slug)],
      sourceIds: [], unit: 'item', role, category: category.name, tier: tierOf(raw.title), component: role === 'skill' ? componentOf(raw.title) : null,
      url: typeof raw.url === 'string' && raw.url.startsWith('https://') ? raw.url : null, shopId: raw.id,
      contents: bundle ? [] : parseContents(detail.description),
    };
    if (!product.active) warnings.push(`"${product.name}" is out of stock in the shop and cannot be quoted.`);
    if (bundle) {
      const required = (detail.bundle_components || []).filter(c => !Number(c.is_optional) && Number(c.required_quantity) > 0);
      product.includes = required.map(c => ({ shopId: Number(c.component_product_id), quantity: Number(c.required_quantity), name: String(c.title || '').slice(0, 120) }));
      product.features = [...new Set(required.map(c => snapshot.products.find(p => p.id === Number(c.component_product_id))).filter(Boolean).map(p => categories.get(p.category_id)).filter(c => c && !/general|mandatory|skill-lab/.test(String(c.slug).toLowerCase())).map(c => featureId(c.slug)))];
      const listedSum = required.reduce((sum, c) => sum + Number(c.price_override || 0) * Number(c.required_quantity), 0);
      if (listedSum && Math.abs(listedSum - price) / price > 0.05) warnings.push(`Bundle "${product.name}" is listed at ${rupees(price)}, but its required components add up to ${rupees(listedSum)}. Kalpana quotes the listed bundle price.`);
      const tiersByArea = new Map();
      for (const c of required) { const p = snapshot.products.find(v => v.id === Number(c.component_product_id)); if (!p) continue; const key = `${p.category_id}:${componentOf(p.title)}`; tiersByArea.set(key, [...(tiersByArea.get(key) || []), tierOf(p.title)].filter(Boolean)); }
      for (const [key, tiers] of tiersByArea) if (new Set(tiers).size > 1) warnings.push(`Bundle "${product.name}" requires ${new Set(tiers).size} tiers of the same ${key.split(':')[1]} (${categories.get(Number(key.split(':')[0]))?.name}).`);
    }
    products.push(product);
    shopRows.push({ product, raw, detail, category });
  }

  const today = now.toISOString().slice(0, 10);
  for (const type of COURSE_TYPES) {
    for (const course of snapshot.courses[type.key] || []) {
      if (!Number.isInteger(course.id) || typeof course.title !== 'string' || !isRupees(course.price) || course.price <= 0) { warnings.push(`Skipped ${type.label} ${course.id ?? '?'}: missing title or price.`); continue; }
      if (course.end_date && course.end_date < today) continue;
      const words = course.title.toLowerCase();
      const matched = [...categories.values()].filter(c => !/general|mandatory|skill-lab/.test(String(c.slug).toLowerCase()) && String(c.name).toLowerCase().split(/[^a-z]+/).some(w => w.length >= 4 && !COURSE_STOPWORDS.has(w) && new RegExp(`\\b${w.length > 4 ? w.replace(/s$/, '') : w}`).test(words)));
      products.push({
        id: `course-${course.id}`, name: course.title.slice(0, 120), kind: 'course', unitAmount: Math.round(course.price * 100), active: Number(course.course_status) === 1,
        taxBasisPoints: 0, features: [...new Set([type.feature, ...(type.key === 'ttt' ? ['teacher-training'] : []), ...matched.map(c => featureId(c.slug))])], sourceIds: [], unit: 'participant',
        role: 'course', courseType: type.key, category: type.label, tier: null, component: null, url: null, shopId: course.id,
        startDate: course.start_date || null, endDate: course.end_date || null, location: course.online ? 'Online' : course.location_name || null,
      });
    }
  }
  if (!products.some(p => p.kind !== 'course' && p.active)) fail('no quotable shop products after validation');

  // Lab planner: tiers of Lab / Kit / Consumable per skill category, plus general components.
  const pick = (candidates, label) => {
    if (candidates.length > 1) {
      const chosen = candidates.find(p => referenced.has(p.shopId)) || [...candidates].sort((a, b) => a.shopId - b.shopId)[0];
      warnings.push(`${candidates.length} shop products match ${label} (${candidates.map(p => `#${p.shopId} ${rupees(p.unitAmount / 100)}`).join(', ')}); the planner uses #${chosen.shopId}.`);
      return chosen;
    }
    return candidates[0];
  };
  const skills = [];
  for (const category of categories.values()) {
    const items = products.filter(p => p.role === 'skill' && p.category === category.name && p.active);
    if (!items.length) continue;
    const tiers = {};
    for (const tier of TIERS) {
      const set = {};
      for (const component of ['lab', 'kit', 'consumable', 'set']) { const found = items.filter(p => p.tier === tier && p.component === component); if (found.length) set[component] = pick(found, `${category.name} ${tier} ${component}`).id; }
      if (Object.keys(set).length) tiers[tier] = set;
    }
    for (const p of items.filter(p => !p.tier)) warnings.push(`"${p.name}" has no Basic/Advanced/Premium tier, so the lab planner skips it; it can still be quoted directly.`);
    if (!Object.keys(tiers).length) continue;
    const prices = TIERS.filter(t => tiers[t]?.kit || tiers[t]?.set).map(t => products.find(p => p.id === (tiers[t].kit || tiers[t].set)).unitAmount);
    if (prices.length > 1 && new Set(prices).size === 1) warnings.push(`${category.name}: every tier has the same price.`);
    const slug = String(category.slug).toLowerCase();
    skills.push({ id: featureId(slug), label: category.name, category: workCategory(slug), needsWater: needsWater(slug), tiers });
  }
  const byRole = role => Object.fromEntries(TIERS.map(tier => [tier, products.filter(p => p.role === role && p.tier === tier && p.active)]).filter(([, list]) => list.length).map(([tier, list]) => [tier, pick(list, `${role} ${tier}`).id]));
  const mandatory = byRole('mandatory'); const curriculum = byRole('curriculum'); const digital = byRole('digital');
  for (const [role, map] of [['mandatory equipment', mandatory], ['curriculum and training', curriculum], ['digital lab', digital]]) {
    const amounts = TIERS.filter(t => map[t]).map(t => products.find(p => p.id === map[t]).unitAmount);
    if (amounts.some((amount, i) => i && amount < amounts[i - 1])) warnings.push(`Shop ${role} prices do not increase from Basic to Premium.`);
  }
  const bundleTier = TIERS.map(t => products.find(p => p.kind === 'bundle' && /cbse/i.test(p.name) && p.tier === t)).filter(Boolean);
  if (bundleTier.some((b, i) => i && b.unitAmount < bundleTier[i - 1].unitAmount)) warnings.push(`CBSE composite bundle prices do not increase from Basic to Premium (${bundleTier.map(b => `${b.tier} ${rupees(b.unitAmount / 100)}`).join(', ')}).`);
  const installation = products.find(p => p.role === 'installation' && p.active)?.id || null;

  // Readable source documents so answers can cite the shop and course listings.
  const shopLines = [`Skillvation Club Shop catalog, synced ${snapshot.fetchedAt} from ${snapshot.shopUrl}.`, 'Prices are listed in INR as published by the shop. GST is shown where the shop lists it.', ''];
  for (const category of categories.values()) {
    const rows = shopRows.filter(r => r.category.id === category.id);
    if (!rows.length) continue;
    shopLines.push(`## ${category.name}`);
    for (const { product, detail } of rows) {
      shopLines.push(`- ${product.name} (shop #${product.shopId}${product.tier ? `, ${product.tier} tier` : ''}): ${rupees(product.unitAmount / 100)}${product.taxBasisPoints ? ` + ${product.taxBasisPoints / 100}% GST` : ''}.${product.active ? '' : ' Out of stock.'}${detail.short_description && detail.short_description !== product.name ? ` ${String(detail.short_description).slice(0, 300)}` : ''}`);
      if (product.includes?.length) shopLines.push(`  Includes: ${product.includes.map(i => `${i.quantity} × ${i.name}`).join('; ')}.`);
    }
    shopLines.push('');
  }
  const courseLines = [`MySkool collab courses, synced ${snapshot.fetchedAt} from ${snapshot.coursesUrl}. Prices are listed per participant in INR.`, ''];
  for (const type of COURSE_TYPES) {
    courseLines.push(`## ${type.label}s`);
    for (const course of snapshot.courses[type.key] || []) if (Number.isInteger(course.id) && typeof course.title === 'string') courseLines.push(`- ${course.title} (course #${course.id}): ${rupees(course.price)} per participant. ${course.location_name || ''} ${course.start_date || ''} to ${course.end_date || ''}. ${String(course.short_description || course.description || '').replace(/\s+/g, ' ').slice(0, 300)}`);
    courseLines.push('');
  }
  const shopContent = shopLines.join('\n'); const courseContent = courseLines.join('\n');
  const documents = [
    { id: `skillvation-shop-${sha(shopContent).slice(0, 12)}`, title: 'Skillvation Club Shop products', content: shopContent },
    { id: `skillvation-courses-${sha(courseContent).slice(0, 12)}`, title: 'MySkool collab courses', content: courseContent },
  ];
  for (const p of products) p.sourceIds = [p.kind === 'course' ? documents[1].id : documents[0].id];

  const featureLabels = {};
  for (const skill of skills) featureLabels[skill.id] = skill.label;
  for (const p of products) for (const f of p.features) if (!featureLabels[f]) featureLabels[f] = { 'teacher-training': 'Teacher training', 'digital-lab': 'Digital lab', 's2s-course': 'S2S course', 'u4t-course': 'U4T course', 'ttt-course': 'TTT course' }[f] || categoriesLabel(categories, f) || f;

  // The digest covers sellable data only, so an unchanged shop does not create a new revision.
  const digest = sha(JSON.stringify(products.map(({ sourceIds, ...p }) => p))).slice(0, 16);
  const stamp = now.toISOString().replace(/[-:]/g, '').slice(0, 13);
  const catalog = {
    revision: `skillvation-${stamp}-${digest.slice(0, 8)}`, title: 'Skillvation Club Shop + MySkool courses', demo: false, model: 'shop', currency: 'INR',
    taxBasisPoints: 0, deliveryAmount: 0, validUntil: new Date(now.getTime() + 30 * 24 * 3600 * 1000).toISOString(), products, featureLabels,
    source: { name: 'skillvation', digest, fetchedAt: snapshot.fetchedAt, shopUrl: snapshot.shopUrl, coursesUrl: snapshot.coursesUrl },
    labPlanner: { mode: 'shop', kitsPerRoom, tiers: TIERS, skills, mandatory, curriculum, digital, installation, optionalProductIds: Object.values(digital), bundleIds: products.filter(p => p.kind === 'bundle' && p.active).map(p => p.id) },
  };
  const summary = { products: products.filter(p => p.kind !== 'course').length, bundles: products.filter(p => p.kind === 'bundle').length, courses: products.filter(p => p.kind === 'course').length, skills: skills.length, categories: categories.size };
  return { catalog, documents, warnings: [...new Set(warnings)], summary, digest };
}
function categoriesLabel(categories, feature) { for (const c of categories.values()) if (featureId(c.slug) === feature) return c.name; return null; }
