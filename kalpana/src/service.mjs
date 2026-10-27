import { randomUUID } from 'node:crypto';
import { readFile } from 'node:fs/promises';
import { Database } from './database.mjs';
import { assert, hash, token, now, string, integer, id, equal } from './core.mjs';
import { publishCatalog, calculate, recommend } from './catalog.mjs';
import { Gemini, demoIntent } from './gemini.mjs';
import { retrieve } from './knowledge.mjs';
import { checkoutPlan } from './checkout.mjs';
import { buildLabPlan, budgetLabOptions, compareLabTiers, validateLabItems, plannerOptions } from './lab-planner.mjs';
import { demoLabIntent, mergeLabDraft, understood, safeReply, sanitizeExtraction } from './lab-intake.mjs';
import { fetchSkillvation, buildSkillvationCatalog, SKILLVATION_DEFAULTS } from './skillvation.mjs';
import { quoteFeatures } from './shop.mjs';

// Guided mode asks about each of these once (after the essentials) instead of silently assuming them.
// When Kalpana already has a value, the question asks the visitor to confirm or change it.
const GUIDED_QUESTIONS = [
  ['classes', value => value ? `The lab will serve classes ${value}. Is that right, or which classes should it serve?` : 'Which classes will use the lab — for example VI to X?'],
  ['setup', value => `${value ? `I have this as: ${value}. ` : ''}Do you need a complete lab (room fit-out and materials), materials only, or equipment for an existing lab room?`],
  ['skillIds', value => value ? `So far the skill areas are ${value}. Would you like to add or remove any? You can also tap them above.` : 'Which skill areas would you like — for example robotics, healthcare or pottery? You can also tap them above.'],
  ['tier', value => `Which level suits you: Basic, Advanced or Premium?${value ? ` (Currently ${value}.)` : ''}`],
  ['budgetMinor', value => value ? `I have noted a budget of ${value}. Is that right?` : 'Do you have a budget in mind? Tell me an amount, such as "5 lakh", or say "no fixed budget".'],
];

export async function createService(config) {
  const db = new Database(config.database);
  const gemini = new Gemini(config);
  const skillvation = config.catalogSource === 'skillvation';
  if (config.seedDemo && !skillvation && !db.catalog()) {
    const sample = JSON.parse(await readFile(new URL('../examples/catalog.json', import.meta.url), 'utf8'));
    db.transaction(() => { for (const doc of sample.documents) if (!db.getDocument(doc.id)) db.document(doc); });
    publishCatalog(sample, db);
  }
  const busy = new Set();
  // MySkool courses chosen for a lab plan, priced per participant. Invalid picks are rejected.
  function labCourses(catalog, list) {
    if (catalog.model !== 'shop') return [];
    assert(Array.isArray(list) && list.length <= 12, 400, 'Choose up to 12 courses');
    const seen = new Set();
    return list.map(item => {
      const course = catalog.products.find(p => p.id === item?.productId && p.kind === 'course' && p.active);
      assert(course && !seen.has(course.id), 400, 'Choose valid, distinct courses'); seen.add(course.id);
      return { productId: course.id, participants: integer(item.participants, 'course participants', 1, 100000) };
    });
  }

  // Skillvation sync: fetch → validate → build → publish only when sellable data changed.
  // Failures are recorded and leave the current catalog untouched.
  let syncing = null;
  const syncStatus = () => { const raw = db.setting('skillvation.status'); return raw ? JSON.parse(raw) : null; };
  const saveStatus = status => db.set('skillvation.status', JSON.stringify(status));
  function syncSkillvation({ publish = config.skillvationAutoPublish !== false } = {}) {
    if (syncing) return syncing;
    syncing = (async () => {
      const startedAt = new Date().toISOString(); const previous = syncStatus();
      try {
        const snapshot = await fetchSkillvation({ fetch: config.skillvationFetch || fetch, shopUrl: config.skillvationShopUrl || SKILLVATION_DEFAULTS.shopUrl, coursesUrl: config.skillvationCoursesUrl || SKILLVATION_DEFAULTS.coursesUrl });
        const built = buildSkillvationCatalog(snapshot, { kitsPerRoom: config.skillvationKitsPerRoom || SKILLVATION_DEFAULTS.kitsPerRoom });
        const current = db.catalog();
        const changed = current?.source?.digest !== built.digest;
        const expiring = current && Date.parse(current.validUntil) - Date.now() < 7 * 24 * 3600 * 1000;
        db.transaction(() => { for (const doc of built.documents) if (!db.getDocument(doc.id)) db.document({ ...doc, mimeType: 'text/markdown' }); });
        let published = false;
        if ((changed || expiring) && publish) {
          assert(config.checkoutMode !== 'demo', 409, 'Restart in request checkout mode before publishing live shop prices');
          publishCatalog(built.catalog, db); published = true;
          db.audit('skillvation.publish', built.catalog.revision, { ...built.summary, warnings: built.warnings.length });
        }
        if ((changed || expiring) && !publish) db.set('skillvation.draft', JSON.stringify(built.catalog));
        const status = { ok: true, at: new Date().toISOString(), startedAt, changed, published, staged: (changed || expiring) && !publish, revision: published ? built.catalog.revision : current?.revision || null, summary: built.summary, warnings: built.warnings, fetchedAt: snapshot.fetchedAt };
        saveStatus(status); return status;
      } catch (error) {
        const status = { ok: false, at: new Date().toISOString(), startedAt, error: error.status ? error.message : 'Unexpected sync error', lastSuccessAt: previous?.ok ? previous.at : previous?.lastSuccessAt || null, revision: db.catalog()?.revision || null, summary: previous?.summary || null, warnings: previous?.warnings || [] };
        saveStatus(status); db.audit('skillvation.sync_failed', 'skillvation', { error: status.error });
        if (!error.status) console.error('Skillvation sync failed:', error.message);
        return status;
      } finally { syncing = null; }
    })();
    return syncing;
  }
  function publishSkillvationDraft() {
    const draft = db.setting('skillvation.draft');
    assert(draft, 404, 'No staged Skillvation catalog. Run a sync first.');
    assert(config.checkoutMode !== 'demo', 409, 'Restart in request checkout mode before publishing live shop prices');
    const catalog = publishCatalog(JSON.parse(draft), db);
    db.sql.prepare('DELETE FROM settings WHERE key=?').run('skillvation.draft');
    saveStatus({ ...syncStatus(), published: true, staged: false, revision: catalog.revision });
    db.audit('skillvation.publish', catalog.revision, { manual: true });
    return catalog;
  }
  const currentCatalog = () => {
    const catalog = db.catalog();
    assert(catalog, 503, 'No approved catalog yet. An administrator must attach documents and publish pricing.');
    assert(!skillvation || (!catalog.demo && catalog.source?.name === 'skillvation'), 503, 'The live catalog is syncing. Retry shortly; sample prices are disabled.');
    return catalog;
  };
  const authenticate = supplied => {
    assert(typeof supplied === 'string' && supplied.length === 64, 401, 'Create a conversation first');
    const conversation = db.sql.prepare('SELECT * FROM conversations WHERE token_hash=? AND expires_at>?').get(hash(supplied), Date.now());
    assert(conversation, 401, 'Conversation expired; start a new conversation');
    return conversation;
  };
  const ownedQuote = (conversation, quoteId) => {
    const record = db.getQuote(quoteId);
    assert(record && record.conversation_id === conversation.id, 404, 'Quote not found');
    assert(record.expires_at > Date.now(), 409, 'Quote expired; request an updated quote');
    const catalog = currentCatalog();
    assert(record.catalog_revision === catalog.revision, 409, 'Pricing changed; request an updated quote');
    assert(Date.parse(catalog.validUntil) > Date.now(), 409, 'Catalog pricing expired');
    assert(equal(JSON.stringify(record.quote.constraints), JSON.stringify(JSON.parse(conversation.state))), 409, 'Requirements changed; review a new quote');
    return record.quote;
  };
  function saveQuote(conversation, catalog, items, constraints, title) {
    const labPlan = validateLabItems(catalog, constraints, items);
    const quote = calculate(catalog, items, title || labPlan?.title);
    if (catalog.model === 'shop') {
      // Shop quotes have no learner capacity; coverage comes from the features of the quoted products.
      const overBudget = constraints.budgetMinor == null ? 0 : Math.max(0, quote.total - constraints.budgetMinor);
      const features = quoteFeatures(catalog, quote);
      const missingFeatures = (constraints.features || []).filter(f => !features.has(f));
      return db.quote(conversation.id, { ...quote, constraints, shortage: 0, overBudget, missingFeatures, ...(labPlan ? { labAssessment: labPlan.assessment, labSchedule: labPlan.schedule } : {}), purchasable: !overBudget && !missingFeatures.length && !(constraints.unsupportedRequirements?.length), sourceDocuments: quote.sourceIds.map(sourceId => { const doc = db.getDocument(sourceId); return { id: sourceId, title: doc.title, sha256: doc.sha256 }; }) });
    }
    const shortage = Math.max(0, (constraints.learners || 0) - quote.capacity);
    const overBudget = constraints.budgetMinor === null || constraints.budgetMinor === undefined ? 0 : Math.max(0, quote.total - constraints.budgetMinor);
    const units = quote.lines.find(l => l.kind === 'package').quantity;
    const features = new Set(quote.lines.filter(l => l.kind === 'package' || l.quantity === units).flatMap(l => catalog.products.find(p => p.id === l.productId).features));
    const missingFeatures = (constraints.features || []).filter(f => !features.has(f));
    return db.quote(conversation.id, { ...quote, constraints, shortage, overBudget, missingFeatures, ...(labPlan ? { labAssessment: labPlan.assessment, labSchedule: labPlan.schedule } : {}), purchasable: !shortage && !overBudget && !missingFeatures.length && !(constraints.unsupportedRequirements?.length), sourceDocuments: quote.sourceIds.map(sourceId => { const doc = db.getDocument(sourceId); return { id: sourceId, title: doc.title, sha256: doc.sha256 }; }) });
  }
  return {
    db, config,
    health() { return { ok: true, name: 'Kalpana', aiMode: config.aiMode, geminiConfigured: !!config.geminiKey, checkoutMode: config.checkoutMode, catalogRevision: db.catalog()?.revision || null, demoCatalog: db.catalog()?.demo ?? null, catalogSource: config.catalogSource || 'manual', catalogModel: db.catalog()?.model || 'packages', lastSync: skillvation ? syncStatus()?.at ?? null : null }; },
    syncStatus, syncSkillvation, publishSkillvationDraft,
    newConversation() {
      const secret = token(); const conversationId = randomUUID(); const expires = Date.now() + 24 * 60 * 60 * 1000;
      db.sql.prepare('INSERT INTO conversations VALUES (?,?,?,?,?)').run(conversationId, hash(secret), expires, '{}', now());
      return { conversationId, token: secret, expiresAt: new Date(expires).toISOString() };
    },
    catalog() { const catalog = currentCatalog(); return { ...catalog, sourceDocuments: [...new Set(catalog.products.flatMap(p => p.sourceIds))].map(id => { const doc = db.getDocument(id); return { id, title: doc.title, sha256: doc.sha256 }; }) }; },
    conversation(supplied) { const c = authenticate(supplied); return { conversationId: c.id, constraints: JSON.parse(c.state), messages: db.history(c.id) }; },
    integrationResult(conversationId) {
      const c = db.sql.prepare('SELECT * FROM conversations WHERE id=?').get(conversationId);
      assert(c, 404, 'Session not found');
      if (c.expires_at <= Date.now()) return { status: 'expired', plan: null, quotation: null };
      const row = db.sql.prepare("SELECT payload FROM messages WHERE conversation_id=? AND role='assistant' AND json_type(payload,'$.labPlan')='object' ORDER BY id DESC LIMIT 1").get(c.id);
      if (!row) return { status: 'pending', plan: null, quotation: null };
      const plan = JSON.parse(row.payload).labPlan;
      try { ownedQuote(c, plan.quote.id); }
      catch (error) {
        if (error.status === 409 || error.status === 503) return { status: 'needs_refresh', reason: error.message, plan: null, quotation: null };
        throw error;
      }
      return { status: 'ready', plan, quotation: plan.quote };
    },
    labOptions() { return plannerOptions(currentCatalog()); },
    labBudgetOptions(supplied, input) { authenticate(supplied); return budgetLabOptions(currentCatalog(), input); },
    labCompareTiers(supplied, input) { authenticate(supplied); return compareLabTiers(currentCatalog(), input); },
    async labPlan(supplied, input) {
      const c = authenticate(supplied); assert(!busy.has(c.id), 409, 'Please wait for the current recommendation');
      const catalog = currentCatalog(); const plan = buildLabPlan(catalog, input);
      // Save the rule-based result even if the optional online explanation is unavailable.
      busy.add(c.id);
      try {
        const preview = calculate(catalog, plan.items, plan.title);
        let ai = { status: config.aiMode === 'demo' ? 'demo' : 'not_configured', explanation: 'This plan sizes reusable materials by batch and active room, checks your selected planning criteria, and leaves site verification to the school.', nextSteps: ['Review the room and course observations.', 'Confirm the equipment and infrastructure scope with the school.', 'Review the itemized quotation before requesting follow-up.'] };
        if (config.aiMode !== 'demo' && config.geminiKey) {
          try { ai = { status: 'gemini', ...await gemini.explainLab(plan, preview) }; }
          catch { ai = { ...ai, status: 'unavailable', notice: 'The online explanation is unavailable. The calculated planning observations and quote are still shown.' }; }
        }
        assert(db.catalog().revision === catalog.revision, 409, 'Pricing changed during planning; generate the plan again');
        return db.transaction(() => {
          const quote = saveQuote(c, catalog, plan.items, plan.constraints, plan.title);
          const result = { ...plan, quote, ai, demoCatalog: catalog.demo, rulesOnly: ai.status !== 'gemini' };
          db.sql.prepare('UPDATE conversations SET state=? WHERE id=?').run(JSON.stringify(plan.constraints), c.id);
          db.message(c.id, 'user', { message: `Lab plan: ${plan.profile.board}, ${plan.profile.setup}, ${plan.profile.skillIds.join(', ')}` });
          db.message(c.id, 'assistant', { answer: ai.explanation, labPlan: result, quotes: [quote], constraints: plan.constraints });
          db.audit('lab.plan', quote.id, { board: plan.profile.board, rulesVersion: plan.assessment.rulesVersion });
          return result;
        });
      } finally { busy.delete(c.id); }
    },
    // Autopilot: a description in plain language becomes a validated draft; once nothing essential
    // is missing, the same deterministic planner used by the manual form produces the plan and quote.
    async labInterpret(supplied, input) {
      const c = authenticate(supplied);
      // Direct picks from the UI (courses, tier, MySkool courses) need no message and skip the model.
      const select = input.select && typeof input.select === 'object' && !Array.isArray(input.select) ? input.select : null;
      const message = select && !input.message ? '' : string(input.message, 'message', 2000);
      assert(!busy.has(c.id), 409, 'Please wait for Kalpana to finish the current response');
      const catalog = currentCatalog(); const planner = catalog.labPlanner;
      assert(planner, 503, 'The approved catalog has no lab-planner pricing yet. Ask the pricing administrator to publish a planner catalog.');
      const options = {
        skills: planner.skills.map(({ id, label, category }) => ({ id, label, category })),
        optionalProducts: planner.optionalProductIds.map(productId => { const p = catalog.products.find(p => p.id === productId); return { id: p.id, name: p.name, features: p.features }; }),
        boards: ['CBSE', 'State Board', 'ICSE', 'Other'], setups: ['infrastructure_materials', 'materials_only', 'existing_room'], currency: catalog.currency,
        tiers: planner.tiers || [],
      };
      const previous = input.draft && typeof input.draft === 'object' && !Array.isArray(input.draft) ? input.draft : null;
      busy.add(c.id);
      let extracted;
      // Without a Gemini key the visible on-device parser is used and labelled as such (engine: 'local').
      // A configured key that fails still returns an error rather than switching silently.
      const local = config.aiMode === 'demo' || !config.geminiKey;
      try { extracted = !message ? sanitizeExtraction({}, options) : local ? demoLabIntent({ message, options }) : await gemini.interpretLab({ options, draft: previous, message }); }
      finally { busy.delete(c.id); }
      const merged = mergeLabDraft(previous, extracted, options, message, { fillCategories: input.guided !== true });
      const { draft, missing } = merged; const notes = message ? merged.notes : [];
      if (select?.skillIds !== undefined) {
        assert(Array.isArray(select.skillIds) && select.skillIds.length > 0 && select.skillIds.every(id => options.skills.some(s => s.id === id)) && new Set(select.skillIds).size === select.skillIds.length, 400, 'Choose at least one valid course');
        draft.skillIds = [...select.skillIds]; draft.assumed = draft.assumed.filter(key => key !== 'skillIds');
      }
      if (select?.tier !== undefined) { assert(options.tiers.includes(select.tier), 400, 'Choose a Basic, Advanced or Premium tier'); draft.tier = select.tier; draft.assumed = draft.assumed.filter(key => key !== 'tier'); }
      // Levels per skill area persist between turns; choosing an overall level resets every area to it.
      const levels = select?.tier !== undefined ? {} : { ...(previous?.skillTiers && typeof previous.skillTiers === 'object' ? previous.skillTiers : {}) };
      if (select?.skillTiers !== undefined) {
        assert(select.skillTiers && typeof select.skillTiers === 'object' && !Array.isArray(select.skillTiers) && Object.entries(select.skillTiers).every(([id, level]) => draft.skillIds.includes(id) && options.tiers.includes(level)), 400, 'Choose Basic, Advanced or Premium for a selected skill area');
        Object.assign(levels, select.skillTiers);
      }
      draft.skillTiers = Object.fromEntries(Object.entries(levels).filter(([id, level]) => draft.skillIds.includes(id) && options.tiers.includes(level) && level !== draft.tier));
      draft.courses = labCourses(catalog, select?.courses ?? previous?.courses ?? []);
      const chips = understood(draft, options, catalog.currency);
      if (draft.courses.length) chips.push({ field: 'courses', label: 'Add-on courses', value: draft.courses.map(item => `${catalog.products.find(p => p.id === item.productId).name} × ${item.participants}`).join(' · '), assumed: false });
      // Guided mode (the shop's Design My Lab popup): ask one question at a time, including about details
      // Kalpana would otherwise assume, then wait for the visitor to confirm before planning and pricing.
      if (input.guided === true) {
        const asked = new Set(Array.isArray(previous?.asked) ? previous.asked.filter(key => typeof key === 'string') : []);
        const current = field => chips.find(chip => chip.field === field)?.value || '';
        const open = [...missing, ...GUIDED_QUESTIONS.filter(([field]) => !asked.has(field)).map(([field, question]) => ({ field, question: question(current(field)) }))];
        const next = open[0];
        if (next) asked.add(next.field);
        draft.asked = [...asked];
        assert(db.catalog().revision === catalog.revision, 409, 'Pricing changed during planning; send your request again');
        db.sql.prepare('UPDATE conversations SET state=? WHERE id=?').run(JSON.stringify({ labDraft: draft }), c.id);
        // Assumptions about details that are asked about anyway would only confuse the conversation.
        const guidedNotes = notes.filter(note => !GUIDED_QUESTIONS.some(([field]) => merged.assumptionNotes[field] === note));
        const guided = { draft, understood: chips, notes: guidedNotes, missing: open.map(m => m.field), aiMode: config.aiMode, engine: local ? 'local' : 'gemini' };
        if (next) return { ...guided, questions: [next.question], reply: previous ? 'Got it.' : 'Thanks! Let’s design your lab together.' };
        return { ...guided, questions: [], confirm: true, reply: 'Here is everything I understood. Check the summary, change anything by typing, or confirm to put this lab in your cart.' };
      }
      const base = { draft, understood: chips, notes, missing: missing.map(m => m.field), questions: missing.map(m => m.question), aiMode: config.aiMode, engine: local ? 'local' : 'gemini' };
      if (missing.length) {
        // An incomplete revision supersedes the previous requirements too. Its old quote
        // must not remain purchasable while we wait for the missing details.
        assert(db.catalog().revision === catalog.revision, 409, 'Pricing changed during planning; send your request again');
        db.sql.prepare('UPDATE conversations SET state=? WHERE id=?').run(JSON.stringify({ labDraft: draft }), c.id);
        return { ...base, reply: safeReply(extracted.reply, `Got it. ${missing.length === 1 ? 'One more detail' : 'A few more details'} and I can design the lab.`) };
      }
      const { assumed, asked, ...profile } = draft;
      const plan = await this.labPlan(supplied, profile);
      return { ...base, plan, reply: safeReply(extracted.reply, 'I have designed your lab from that description. Review the plan, the assumptions I made, and the verified quote below.') };
    },
    async chat(supplied, input) {
      const c = authenticate(supplied); const message = string(input.message, 'message', 4000);
      assert(!busy.has(c.id), 409, 'Please wait for Kalpana to finish the current response');
      const catalog = currentCatalog();
      assert(Date.parse(catalog.validUntil) > Date.now(), 409, 'Catalog pricing has expired; ask the team to update it');
      const previous = JSON.parse(c.state); const overrides = {};
      if (input.budgetMinor !== undefined) overrides.budgetMinor = input.budgetMinor === null ? null : integer(input.budgetMinor, 'budget', 1, 10000000000);
      if (input.learners !== undefined) overrides.learners = input.learners === null ? null : integer(input.learners, 'learners', 1, 100000);
      const sources = retrieve(db, catalog, `${message} ${(previous.features || []).join(' ')}`);
      busy.add(c.id);
      try {
        const request = { catalog, sources, message, history: db.history(c.id).map(m => ({ role: m.role, text: m.message || m.answer || '' })), previous, overrides };
        const intent = config.aiMode === 'demo' ? demoIntent(request) : await gemini.interpret(request);
        const constraints = { budgetMinor: intent.budgetMinor, learners: intent.learners, currency: intent.currency, features: intent.features, packageIds: intent.packageIds, unsupportedRequirements: intent.unsupportedRequirements, ...overrides };
        let matches = [], nearest = null;
        const warnings = [...intent.unsupportedRequirements];
        if (constraints.currency !== catalog.currency) warnings.push(`This catalog is priced in ${catalog.currency}; currency conversion is not available. Please provide a budget in ${catalog.currency}.`);
        else {
          const result = recommend(catalog, constraints);
          matches = result.matches; nearest = result.nearest;
        }
        // Detect a catalog publication that happened while the model request was in flight.
        assert(db.catalog().revision === catalog.revision, 409, 'Catalog changed during the conversation; please send your request again');
        const response = db.transaction(() => {
          const quotes = matches.map(q => saveQuote(c, catalog, q.items, constraints, q.title));
          const alternative = nearest ? saveQuote(c, catalog, nearest.items, constraints, nearest.title) : null;
          const answer = {
            answer: intent.answer, constraints, questions: intent.questions, warnings,
            quotes, nearest: alternative, suggestions: intent.suggestions || [],
            sources: sources.filter(source => intent.sourceIds.includes(source.id)),
            aiMode: config.aiMode, demoCatalog: catalog.demo,
          };
          db.message(c.id, 'user', { message }); db.message(c.id, 'assistant', answer);
          db.sql.prepare('UPDATE conversations SET state=? WHERE id=?').run(JSON.stringify(constraints), c.id);
          return answer;
        });
        return response;
      } finally { busy.delete(c.id); }
    },
    quote(supplied, input) {
      const c = authenticate(supplied); const constraints = JSON.parse(c.state);
      assert(constraints.currency === undefined || constraints.currency === currentCatalog().currency, 409, 'Confirm your budget in the catalog currency first');
      return db.transaction(() => saveQuote(c, currentCatalog(), input.items, constraints));
    },
    getQuote(supplied, quoteId) { return ownedQuote(authenticate(supplied), quoteId); },
    order(supplied, input) {
      const c = authenticate(supplied);
      assert(input.confirm === true, 400, 'Explicit confirmation is required to submit a purchase request');
      assert(!Object.keys(input).some(k => ['total', 'amount', 'price', 'currency', 'checkoutUrl'].includes(k)), 400, 'Prices and checkout destinations are controlled by the server');
      const quote = ownedQuote(c, input.quoteId);
      assert(quote.purchasable, 409, 'This quote does not meet all confirmed requirements; update your requirements or choose another option');
      const customer = {
        name: string(input.customer?.name, 'customer name', 100),
        email: string(input.customer?.email, 'customer email', 200).toLowerCase(),
      };
      assert(/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(customer.email), 400, 'Enter a valid email');
      const previous = db.sql.prepare('SELECT id FROM orders WHERE quote_id=?').get(quote.id);
      if (previous) {
        const existing = db.getOrder(previous.id);
        assert(equal(JSON.stringify(existing.customer), JSON.stringify(customer)), 409, 'This quote already has a purchase request for another customer');
        const plan = checkoutPlan(config.checkoutMode, quote, existing.id);
        return { order: existing, quote, checkout: plan.checkout, message: plan.message };
      }
      const orderId = randomUUID();
      const plan = checkoutPlan(config.checkoutMode, quote, orderId);
      db.transaction(() => {
        db.sql.prepare('INSERT INTO orders VALUES (?,?,?,?,?,?,?,?,?)').run(orderId, c.id, quote.id, plan.status, JSON.stringify(customer), null, null, now(), now());
        db.audit('purchase.requested', orderId, { quoteId: quote.id, total: quote.total, currency: quote.currency, demo: quote.demo });
      });
      return { order: db.getOrder(orderId), quote, checkout: plan.checkout, message: plan.message };
    },
    getOrder(supplied, orderId) {
      const c = authenticate(supplied); const order = db.getOrder(orderId);
      assert(order && order.conversation_id === c.id, 404, 'Order not found');
      return { order, quote: db.getQuote(order.quote_id).quote };
    },
    completeDemo(supplied, orderId, input) {
      assert(config.checkoutMode === 'demo' && input.confirm === true, 400, 'Demo checkout is not enabled or confirmed');
      const c = authenticate(supplied); const order = db.getOrder(orderId);
      assert(order && order.conversation_id === c.id, 404, 'Order not found');
      assert(['demo_pending', 'demo_completed'].includes(order.status), 409, 'Order is not a demo checkout');
      ownedQuote(c, order.quote_id);
      db.transaction(() => { db.sql.prepare('UPDATE orders SET status=?,updated_at=? WHERE id=?').run('demo_completed', now(), orderId); db.audit('checkout.demo_completed', orderId); });
      return { order: db.getOrder(orderId), message: 'Demo completed. No money was charged and no live order was fulfilled.' };
    },
    documents() { return db.documents(); },
    async addDocument(input) {
      const title = string(input.title, 'document title', 200);
      const documentId = input.id ? id(input.id, 'document ID') : randomUUID();
      assert(!db.getDocument(documentId), 409, 'Documents are immutable; upload a new document ID for a revision');
      let content, mimeType = 'text/plain';
      if (input.pdfBase64 !== undefined) {
        assert(config.aiMode !== 'demo', 503, 'PDF extraction needs a configured Gemini API key; upload extracted text in demo mode');
        assert(typeof input.pdfBase64 === 'string' && /^[a-zA-Z0-9+/]*={0,2}$/.test(input.pdfBase64), 400, 'Invalid PDF base64');
        const bytes = Buffer.from(input.pdfBase64, 'base64');
        assert(bytes.length > 0 && bytes.length <= 1000000 && bytes.subarray(0, 5).toString() === '%PDF-', 400, 'PDF must be a valid PDF file up to 1 MB');
        content = await gemini.extractPdf(title, input.pdfBase64); mimeType = 'application/pdf';
      } else content = string(input.content, 'document text', 60000);
      return db.transaction(() => { const doc = db.document({ id: documentId, title, content, mimeType }); db.audit('document.added', documentId); return { ...doc, reviewRequired: true }; });
    },
    publish(input) {
      assert(config.checkoutMode !== 'demo' || input.demo === true, 409, 'Restart in request mode before publishing a real catalog');
      return publishCatalog(input, db);
    },
    adminOrders() { return db.sql.prepare('SELECT id,quote_id,status,customer,created_at FROM orders ORDER BY created_at DESC LIMIT 200').all().map(order => ({ ...order, customer: JSON.parse(order.customer), quote: db.getQuote(order.quote_id).quote })); },
    close() { db.close(); },
  };
}
