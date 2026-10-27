const $ = selector => document.querySelector(selector);
const el = (tag, className, text) => { const node = document.createElement(tag); if (className) node.className = className; if (text !== undefined) node.textContent = text; return node; };
const paths = window.KalpanaPaths;
// Inside a host page (e.g. the shop's Design My Lab popup) the planner uses a compact, host-styled layout.
if (window.self !== window.top || new URLSearchParams(location.search).has('embed')) document.body.classList.add('embedded');
// Embedded in the shop, Kalpana interviews the visitor and waits for confirmation before planning (guided mode).
const guidedMode = document.body.classList.contains('embedded');
const integrationSession = new URLSearchParams(location.search).get('session');
const sessionKey = `${paths.sessionKey}:lab${integrationSession ? `:${integrationSession}` : ''}`;
const calm = matchMedia('(prefers-reduced-motion: reduce)').matches;
const stagger = nodes => [...nodes].forEach((node, i) => node.style.setProperty('--i', i));
// Animate a figure up to its verified value; the final frame always shows the exact amount.
function countUp(node, target, format, ms = 900) {
  if (calm) { node.textContent = format(target); return; }
  const start = performance.now();
  const tick = time => { const p = Math.min(1, (time - start) / ms); node.textContent = format(p < 1 ? Math.round(target * (1 - (1 - p) ** 3)) : target); if (p < 1) requestAnimationFrame(tick); };
  requestAnimationFrame(tick);
}
let chipsShown = new Set();
const mascot = window.KalpanaMascot?.mount($('#mascot'), { size: 104, bubble: $('#mascot-bubble'), greeting: 'Hi, I’m Kalpana! Tell me about your school and I’ll design your lab.' });
let session, options, health, plan, step = 0, pilotDraft = null, pilotBusy = false;
const money = (amount, currency = options.currency) => new Intl.NumberFormat('en-IN', { style: 'currency', currency }).format(amount / 100);
function error(message) { $('#error').textContent = message || ''; $('#error').hidden = !message; }
async function api(path, body) { const response = await fetch(paths.url(path), { method: body === undefined ? 'GET' : 'POST', headers: { 'Content-Type': 'application/json', ...(session ? { Authorization: `Bearer ${session.token}` } : {}) }, ...(body === undefined ? {} : { body: JSON.stringify(body) }), signal: AbortSignal.timeout(60000) }); const data = await response.json(); if (!response.ok) throw Error(data.error || 'Request failed'); return data; }
function showStep(value) {
  step = value; document.querySelectorAll('[data-step]').forEach((section, i) => { section.hidden = i !== step; });
  document.querySelectorAll('.steps li').forEach((node, i) => node.classList.toggle('active', i === step));
  $('#previous').hidden = step === 0; $('#next').hidden = step === 2; $('#generate').hidden = step !== 2;
  $('#next').textContent = step === 0 ? 'Next: space & setup →' : 'Next: skills & students →';
}
function validStep() { const section = document.querySelector(`[data-step="${step}"]`); for (const input of section.querySelectorAll('input,select,textarea')) if (!input.checkValidity()) { input.reportValidity(); return false; } return true; }
function guidance() {
  const cbse = $('#board').value === 'CBSE', dual = $('#layout').value === 'dual';
  $('#board-guidance').textContent = cbse ? 'CBSE room planning: one room of at least 600 sq. ft. for VI–XII, or two separate rooms of at least 400 sq. ft. each for VI–X and XI–XII. Kalpana also checks the planning brief’s three-course/category baseline.' : 'We will plan around your objectives and space. CBSE-specific room and course checks are not applied to this board.';
  $('#goals').required = !cbse;
  $('#room-two-label').hidden = !dual; $('#room-two').required = dual;
  $('#space-guidance').textContent = `${dual ? 'Room 1 serves VI–X; room 2 serves XI–XII. Each area is assessed separately.' : 'A shared composite room serves your selected classes.'} ${$('#setup').value === 'infrastructure_materials' ? 'A room fit-out allowance is included, subject to site review.' : 'Room fit-out is excluded. Existing infrastructure must be verified.'}`;
}
function values() {
  return { board: $('#board').value, setup: $('#setup').value, layout: $('#layout').value, rooms: [ { areaSqFt: Number($('#room-one').value) }, ...($('#layout').value === 'dual' ? [{ areaSqFt: Number($('#room-two').value) }] : []) ], classes: [...document.querySelectorAll('#classes input:checked')].map(input => Number(input.value)), totalStudents: Number($('#total').value), batchSize: Number($('#batch').value), skillIds: [...document.querySelectorAll('#skills input:checked')].map(input => input.value), optionalProductIds: [...document.querySelectorAll('#upgrades input:checked')].map(input => input.value), budgetMinor: $('#budget').value ? Math.round(Number($('#budget').value) * 100) : null, goals: $('#goals').value.trim(), readiness: Object.fromEntries([...document.querySelectorAll('#readiness select')].map(input => [input.name, input.value])), ...(options.tiers?.length ? { tier: $('#tier').value } : {}), ...(options.mode === 'shop' ? { courses: pilotDraft?.courses || [] } : {}) };
}
function fill(profile, optional = []) {
  $('#board').value = profile.board; $('#setup').value = profile.setup; $('#layout').value = profile.layout;
  $('#room-one').value = profile.rooms[0].areaSqFt; if (profile.rooms[1]) $('#room-two').value = profile.rooms[1].areaSqFt;
  $('#total').value = profile.totalStudents; $('#batch').value = profile.batchSize; $('#budget').value = profile.budgetMinor == null ? '' : profile.budgetMinor / 100; $('#goals').value = profile.goals; if (profile.tier) $('#tier').value = profile.tier;
  for (const input of document.querySelectorAll('#classes input')) input.checked = profile.classes.includes(Number(input.value));
  for (const input of document.querySelectorAll('#skills input')) input.checked = profile.skillIds.includes(input.value);
  for (const input of document.querySelectorAll('#upgrades input')) input.checked = optional.includes(input.value);
  for (const input of document.querySelectorAll('#readiness select')) input.value = profile.readiness[input.name];
  guidance();
}
function render(result, scroll = true) {
  const format = amount => money(amount, result.quote.currency);
  plan = result; $('#results').hidden = false; $('#results').classList.remove('reveal'); void $('#results').offsetWidth; $('#results').classList.add('reveal');
  document.querySelectorAll('.steps li').forEach((node, i) => node.classList.toggle('active', i === 3));
  const ai = $('#ai'); ai.replaceChildren(el('div', 'eyebrow', result.ai.status === 'gemini' ? 'KALPANA · GEMINI EXPLANATION' : 'RULE-BASED PLAN · ONLINE AI NOT USED'), el('p', '', result.ai.explanation));
  if (result.ai.notice) ai.append(el('div', 'warning', result.ai.notice));
  const nextSteps = el('ul'); for (const text of result.ai.nextSteps) nextSteps.append(el('li', 'muted', text)); ai.append(nextSteps);
  const summary = $('#summary'); summary.replaceChildren(); for (const [value, label] of [[result.schedule.activeRooms, 'Active rooms equipped'], [result.schedule.stations, 'Reusable stations'], [result.schedule.batches, 'Scheduled batches'], [format(result.quote.total), result.quote.demo ? 'Demo one-time total' : 'Quoted one-time total']]) { const tile = el('div', 'summary-tile'); tile.append(el('strong', '', value), el('span', '', label)); summary.append(tile); }
  const D = window.KalpanaDiagrams;
  if (D) { $('#flow').replaceChildren(D.flow(result, countUp)); $('#floorplan').replaceChildren(D.floorPlan(result)); $('#cost').replaceChildren(D.costDonut(result.quote, result.profile.budgetMinor, format, countUp)); }
  stagger(summary.querySelectorAll('.summary-tile')); const totalTile = summary.querySelectorAll('.summary-tile strong')[3]; countUp(totalTile, result.quote.total, format, 1100);
  summary.append(el('p', 'muted', result.schedule.capacityBasis));
  const observations = $('#observations'); observations.replaceChildren(); for (const o of result.assessment.observations) { const card = el('article', `observation ${o.status}`); card.append(el('strong', '', o.title), el('p', '', o.detail), el('small', '', `${o.status.replaceAll('_', ' ')} · ${o.basis}`)); observations.append(card); }
  stagger(observations.children);
  const sources = $('#sources'); sources.replaceChildren(el('p', '', `Rules revision: ${result.assessment.rulesVersion}`)); for (const source of result.assessment.sources) { const node = el(source.url ? 'a' : 'p', '', source.title); if (source.url) { node.href = source.url; node.target = '_blank'; node.rel = 'noopener noreferrer'; } sources.append(node, el('p', '', source.scope)); }
  const items = $('#required-items'); items.replaceChildren(); for (const item of result.requiredItems) { const card = el('article', 'required-item'); card.append(el('strong', '', `${item.name} · ${item.quantity} ${item.unit}`), el('p', '', item.detail)); items.append(card); }
  stagger(items.children);
  const optional = $('#optional-summary'); optional.replaceChildren(); for (const item of result.optionalUpgrades) optional.append(el('p', 'muted', `${item.selected ? 'Included' : 'Not included'}: ${item.name} · ${format(item.unitAmount)} × ${item.quantity}`));
  // Shop catalogs: ready-made bundles to compare and matching courses (informational, not quoted).
  $('#ready-made-panel').hidden = !result.readyMade?.length; $('#courses-panel').hidden = !result.relatedCourses?.length && !result.allCourses?.length;
  const ready = $('#ready-made'); ready.replaceChildren();
  for (const bundle of result.readyMade || []) {
    const card = el('article', 'ready-card'); card.append(el('div', 'eyebrow', bundle.matchingSkills ? `COVERS ${bundle.matchingSkills} OF YOUR SKILL AREAS` : 'READY-MADE BUNDLE'), el('strong', '', bundle.name), el('span', 'ready-price', format(bundle.total)), el('small', '', `${bundle.includes} items · includes ${format(bundle.tax)} GST`));
    if (bundle.url) { const link = el('a', 'shop-link', 'View in shop ↗'); link.href = bundle.url; link.target = '_blank'; link.rel = 'noopener noreferrer'; card.append(link); }
    ready.append(card);
  }
  stagger(ready.children);
  const courses = $('#related-courses'); courses.replaceChildren();
  for (const course of result.relatedCourses || []) {
    const card = el('article', `course-card${course.selected ? ' selected' : ''}`); card.dataset.course = course.productId;
    const pick = el('label', 'check-label course-pick'); const box = el('input'); box.type = 'checkbox'; box.checked = course.selected; pick.append(box, el('span', '', course.selected ? 'In your quote' : 'Add to quote'));
    const count = el('label', 'course-count', 'Participants'); const number = el('input'); number.type = 'number'; number.min = '1'; number.max = '100000'; number.step = '1';
    number.value = course.participants ?? (course.courseType === 'ttt' ? 1 : result.profile.totalStudents); count.append(number); count.hidden = !course.selected;
    box.onchange = () => { count.hidden = !box.checked; card.classList.toggle('selected', box.checked); pilotSelect({ courses: selectedCourses() }); };
    number.onchange = () => { if (box.checked) pilotSelect({ courses: selectedCourses() }); };
    card.append(el('strong', '', course.name), el('span', '', `${format(course.unitAmount)} per participant`), el('small', '', [course.courseType?.toUpperCase(), course.location, course.startDate && course.endDate ? `${course.startDate} → ${course.endDate}` : ''].filter(Boolean).join(' · ')), pick, count);
    courses.append(card);
  }
  const browse = $('#course-select'); browse.replaceChildren();
  for (const course of (result.allCourses || []).filter(c => !(result.relatedCourses || []).some(r => r.productId === c.productId))) { const option = el('option', '', `${course.name} · ${course.courseType?.toUpperCase() || ''} · ${format(course.unitAmount)}`); option.value = course.productId; browse.append(option); }
  $('#course-add').disabled = !browse.options.length;
  stagger(courses.children);
  const quote = result.quote; const root = $('#quotation'); root.replaceChildren(); if (quote.demo) root.append(el('div', 'warning', 'Fictional sample pricing. This is not a commercial offer or a compliance certificate.'));
  for (const line of quote.lines) { const row = el('div', 'line-item'); row.append(el('span', '', `${line.name} × ${line.quantity}`), el('strong', '', format(line.lineAmount))); root.append(row); }
  for (const [label, amount] of [['Subtotal', quote.subtotal], ['Configured tax', quote.tax], ['Delivery', quote.delivery], ['Total', quote.total]]) { const row = el('div', label === 'Total' ? 'total-line' : 'line-item'); row.append(el('span', '', label), el('strong', '', format(amount))); root.append(row); }
  stagger(root.querySelectorAll('.line-item,.total-line'));
  root.append(el('p', 'quotation-meta', `Catalog ${quote.catalogRevision} · Expires ${new Date(quote.expiresAt).toLocaleString()} · Quote ${quote.id}`));
  for (const source of quote.sourceDocuments) root.append(el('p', 'quotation-meta', `${source.title} · ${source.sha256.slice(0, 12)}`));
  if (quote.overBudget) root.append(el('div', 'warning', `${format(quote.overBudget)} above your budget. Edit the scope or budget before proceeding.`));
  if (!quote.purchasable) root.append(el('div', 'warning', 'This plan cannot proceed until the highlighted room, category or budget gaps are resolved.'));
  $('#purchase-button').disabled = !quote.purchasable;
  $('#purchase-note').textContent = 'The payment provider has not been connected. This sends a request for team follow-up; no money is charged. Manual site and scope review are still required.';
  $('#purchase').hidden = false; $('#purchase').reset(); $('#order-result').replaceChildren();
  if (scroll) $('#results').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// Autopilot conversation: the server extracts and validates the profile; this page mirrors it.
function pilotSay(role, text) {
  const bubble = el('article', `message ${role}`); bubble.append(el('span', 'speaker', role === 'user' ? 'YOU' : '✦ KALPANA'), el('div', '', text));
  $('#pilot-log').append(bubble); bubble.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); return bubble;
}
function thinking(stages) {
  $('#autopilot').classList.add('busy'); mascot?.setState('thinking');
  const bubble = pilotSay('assistant', ''); bubble.classList.add('thinking'); const line = bubble.lastChild; let i = 0;
  line.textContent = stages[0]; const timer = setInterval(() => { i = Math.min(i + 1, stages.length - 1); line.textContent = stages[i]; mascot?.say(stages[i]); }, 1100); mascot?.say(stages[0]);
  return () => { clearInterval(timer); bubble.remove(); $('#autopilot').classList.remove('busy'); };
}
function renderUnderstood(chips) {
  const root = $('#pilot-chips'); root.replaceChildren();
  const seen = new Set(chips.map(chip => `${chip.label}:${chip.value}`));
  for (const chip of chips) { const key = `${chip.label}:${chip.value}`; const node = el('span', `chip${chip.assumed ? ' assumed' : ''}${chipsShown.size && !chipsShown.has(key) ? ' fresh' : ''}`); node.append(el('small', '', chip.label), document.createTextNode(chip.value)); if (chip.assumed) node.title = 'Assumed. Tell Kalpana if this is different.'; root.append(node); }
  stagger(root.children); chipsShown = seen;
  $('#pilot-understood').hidden = !chips.length;
}
function fillDraft(draft) {
  for (const id of ['board', 'setup', 'layout']) if (draft[id]) $(`#${id}`).value = draft[id];
  if (draft.rooms?.[0]) $('#room-one').value = draft.rooms[0].areaSqFt; if (draft.rooms?.[1]) $('#room-two').value = draft.rooms[1].areaSqFt;
  if (draft.tier) $('#tier').value = draft.tier;
  if (draft.totalStudents) $('#total').value = draft.totalStudents; if (draft.batchSize) $('#batch').value = draft.batchSize;
  $('#budget').value = draft.budgetMinor == null ? '' : draft.budgetMinor / 100; $('#goals').value = draft.goals || '';
  if (draft.classes?.length) for (const input of document.querySelectorAll('#classes input')) input.checked = draft.classes.includes(Number(input.value));
  if (draft.skillIds?.length) for (const input of document.querySelectorAll('#skills input')) input.checked = draft.skillIds.includes(input.value);
  for (const input of document.querySelectorAll('#upgrades input')) input.checked = (draft.optionalProductIds || []).includes(input.value);
  for (const input of document.querySelectorAll('#readiness select')) input.value = draft.readiness?.[input.name] || 'unknown';
  guidance();
}
function applyResponse(response, bubble) {
  pilotDraft = response.draft; fillDraft(response.draft); renderUnderstood(response.understood); syncPicker(); renderLiveCourses(); prefillBudget(response.draft?.budgetMinor);
  if (bubble) {
    if (response.notes.length) { const list = el('ul', 'pilot-notes'); for (const note of response.notes) list.append(el('li', '', note)); bubble.append(list); }
    for (const question of response.questions) bubble.append(el('p', 'pilot-question', question));
  }
  $('#pilot-status').textContent = response.plan ? '✧ Ask for any change: “materials only”, “add AI”, “two rooms”…' : '✧ Answer in your own words. I’ll keep everything else.';
  if (response.plan) { render(response.plan, !!bubble); mascot?.setState('happy', { hold: 3000 }); mascot?.say(bubble ? 'Your lab is ready! Scroll down to watch it come together. ✨' : 'Updated! The plan and quote below reflect your choice. ✨'); }
  else if (response.confirm) { plan = null; $('#results').hidden = true; mascot?.setState('happy', { hold: 3000 }); mascot?.say('All set! Check the summary and confirm when you are happy.'); }
  else { plan = null; $('#results').hidden = true; $('#purchase-button').disabled = true; mascot?.setState('asking'); mascot?.say(response.questions[0] || 'Tell me a little more.'); }
  $('#pilot-confirm').hidden = !response.confirm; $('#pilot-review').hidden = true; // a change makes an open review outdated
  if (response.confirm) $('#pilot-confirm').scrollIntoView({ behavior: calm ? 'auto' : 'smooth', block: 'center' });
}
async function pilotSend(message) {
  if (pilotBusy || !message) return;
  pilotBusy = true; $('#pilot-send').disabled = true; error(''); $('#pilot-examples').hidden = true;
  pilotSay('user', message); $('#pilot-message').value = '';
  const done = thinking(['Understanding your school…', 'Checking room and course planning rules…', 'Sizing stations and materials…', 'Calculating a verified quote…']);
  try {
    // Keep the thinking animation on screen briefly even when the answer is instant.
    const [response] = await Promise.all([api('/api/lab-planner/interpret', { message, draft: pilotDraft, guided: guidedMode }), new Promise(resolve => setTimeout(resolve, calm ? 0 : 1400))]);
    done(); applyResponse(response, pilotSay('assistant', response.reply));
  } catch (e) { done(); error(e.message); $('#pilot-message').value = message; mascot?.setState('sad', { hold: 3000 }); mascot?.say('Oops, that didn’t work. The details are just below.'); }
  finally { pilotBusy = false; $('#pilot-send').disabled = false; $('#pilot-message').focus(); }
}
// Direct picks (courses, tier, MySkool courses) re-plan without typing.
let selectTimer = null, pendingSelect = {}, inFlightSelect = {};
function pilotSelect(change) {
  // Levels per skill area accumulate, so two quick changes are both kept.
  pendingSelect = { ...pendingSelect, ...change, ...(change.skillTiers ? { skillTiers: { ...pendingSelect.skillTiers, ...change.skillTiers } } : {}) };
  clearTimeout(selectTimer);
  selectTimer = setTimeout(async () => {
    if (pilotBusy) { pilotSelect({}); return; }
    const select = pendingSelect; pendingSelect = {}; inFlightSelect = select;
    pilotBusy = true; error(''); mascot?.setState('thinking'); mascot?.say('Re-planning with your selection…'); $('#autopilot').classList.add('busy');
    try { applyResponse(await api('/api/lab-planner/interpret', { draft: pilotDraft, select, guided: guidedMode })); }
    catch (e) { error(e.message); mascot?.setState('sad', { hold: 3000 }); mascot?.say('I couldn’t apply that change. The details are just below.'); syncPicker(); renderLiveCourses(); }
    finally { pilotBusy = false; inFlightSelect = {}; $('#autopilot').classList.remove('busy'); }
  }, 450);
}
function currentSkills() { return [...document.querySelectorAll('#skill-picker [aria-pressed="true"]')].map(button => button.dataset.skill); }
function buildPicker() {
  const root = $('#skill-picker'); root.replaceChildren();
  for (const category of options.categories) {
    const skills = options.skills.filter(s => s.category === category.id); if (!skills.length) continue;
    const group = el('div', 'picker-group'); group.append(el('small', '', category.name));
    const row = el('div', 'picker-row');
    for (const skill of skills) {
      const button = el('button', 'pick', skill.label); button.type = 'button'; button.dataset.skill = skill.id; button.setAttribute('aria-pressed', 'false');
      button.onclick = () => {
        const on = button.getAttribute('aria-pressed') === 'true';
        if (on && currentSkills().length === 1) { button.classList.remove('nope'); void button.offsetWidth; button.classList.add('nope'); mascot?.say('Keep at least one course in the lab.'); return; }
        button.setAttribute('aria-pressed', String(!on)); pilotSelect({ skillIds: currentSkills() });
      };
      row.append(button);
    }
    group.append(row); root.append(group);
  }
  stagger(root.querySelectorAll('.pick'));
  const tiers = $('#tier-toggle'); tiers.replaceChildren(); tiers.hidden = !options.tiers?.length;
  for (const tier of options.tiers || []) {
    const button = el('button', 'tier', tier[0].toUpperCase() + tier.slice(1)); button.type = 'button'; button.dataset.tier = tier; button.setAttribute('aria-pressed', 'false');
    button.onclick = () => { for (const b of tiers.children) b.setAttribute('aria-pressed', String(b === button)); pilotSelect({ tier }); };
    tiers.append(button);
  }
  $('#pilot-picker').hidden = !options.skills.length;
  syncPicker();
}
// One row per selected skill area with its own level; levels the shop does not sell for that area are disabled.
function renderSkillLevels() {
  const root = $('#skill-levels'); const ids = pilotDraft?.skillIds || [];
  root.hidden = !ids.length || !options?.tiers?.length;
  if (root.hidden) return;
  const overall = pilotDraft.tier || 'basic';
  const head = el('div', 'skill-levels-head'); head.append(el('span', 'eyebrow', 'LEVEL FOR EACH SKILL AREA'), el('span', 'legend', 'The buttons above set every area at once.'));
  const rows = ids.map(id => {
    const skill = options.skills.find(s => s.id === id); if (!skill) return null;
    const current = pilotDraft.skillTiers?.[id] || overall;
    const row = el('div', 'skill-level-row'); row.append(el('span', 'skill-level-name', skill.label));
    const group = el('div', 'skill-level-toggle'); group.setAttribute('role', 'group'); group.setAttribute('aria-label', `Level for ${skill.label}`);
    for (const tier of options.tiers) {
      const button = el('button', 'tier', tier[0].toUpperCase() + tier.slice(1)); button.type = 'button';
      button.setAttribute('aria-pressed', String(tier === current));
      if (!skill.tiers?.[tier]) { button.disabled = true; button.title = `The shop has no ${tier} set for ${skill.label}`; }
      button.onclick = () => { for (const b of group.children) b.setAttribute('aria-pressed', String(b === button)); pilotSelect({ skillTiers: { [id]: tier } }); };
      group.append(button);
    }
    row.append(group); return row;
  }).filter(Boolean);
  root.replaceChildren(head, ...rows);
}
function syncPicker() {
  renderSkillLevels();
  const chosen = new Set(pilotDraft?.skillIds || []);
  for (const button of document.querySelectorAll('#skill-picker .pick')) button.setAttribute('aria-pressed', String(chosen.has(button.dataset.skill)));
  for (const button of document.querySelectorAll('#tier-toggle .tier')) button.setAttribute('aria-pressed', String(button.dataset.tier === (pilotDraft?.tier || 'basic')));
}

function renderLiveCourses() {
  const root = $('#live-course-browser');
  const courses = options?.courses || [];
  root.hidden = !courses.length;
  if (!courses.length) return;
  const picks = pendingSelect.courses || inFlightSelect.courses || pilotDraft?.courses || [];
  const selected = new Map(picks.map(c => [c.productId, c.participants]));
  $('#live-course-total').textContent = courses.length;
  $('#live-course-selected').textContent = `${selected.size} selected`;
  $('#live-course-source').textContent = `Source: MySkool course API${options.syncedAt ? ' · Synced ' + new Date(options.syncedAt).toLocaleString() : ''}`;
  const counts = ['s2s', 'u4t', 'ttt'].map(group => `${group.toUpperCase()}: ${courses.filter(c => c.courseType === group).length}`);
  $('#live-course-counts').textContent = counts.join(' · ');
  const query = $('#live-course-search').value.trim().toLowerCase();
  const group = $('#live-course-group').value;
  const list = $('#live-course-list'); list.replaceChildren();
  const change = (course, checked, participants) => {
    const current = new Map((pendingSelect.courses || inFlightSelect.courses || pilotDraft?.courses || []).map(c => [c.productId, c.participants]));
    if (checked) current.set(course.productId, participants); else current.delete(course.productId);
    if (current.size > 12) { error('Choose up to 12 courses for one quote.'); renderLiveCourses(); return; }
    pilotSelect({ courses: [...current].map(([productId, participants]) => ({ productId, participants })) });
  };
  for (const course of courses.filter(c => (!group || c.courseType === group) && c.name.toLowerCase().includes(query))) {
    const card = el('article', `course-card${selected.has(course.productId) ? ' selected' : ''}`);
    const label = el('label', 'check-label'); const box = el('input'); box.type = 'checkbox'; box.checked = selected.has(course.productId);
    label.append(box, el('strong', '', course.name));
    const fee = el('p', '', `${money(course.unitAmount)} per participant · ${course.courseType.toUpperCase()}`);
    const count = el('label', 'course-count', 'Participants'); const number = el('input'); number.type = 'number'; number.min = '1'; number.max = '100000'; number.step = '1'; number.value = selected.get(course.productId) || 1; count.append(number);
    box.onchange = () => change(course, box.checked, Math.max(1, Math.round(Number(number.value) || 1)));
    number.onchange = () => { if (box.checked) change(course, true, Math.max(1, Math.round(Number(number.value) || 1))); };
    card.append(label, fee, el('small', '', [course.location, course.startDate && course.endDate ? `${course.startDate} → ${course.endDate}` : ''].filter(Boolean).join(' · ')), count);
    list.append(card);
  }
  if (!list.children.length) list.append(el('p', 'muted', 'No courses match this search.'));
}
$('#live-course-search').oninput = renderLiveCourses;
$('#live-course-group').onchange = renderLiveCourses;

const selectedCourses = () => [...document.querySelectorAll('#related-courses .course-card')].filter(card => card.querySelector('input[type=checkbox]').checked).map(card => ({ productId: card.dataset.course, participants: Math.max(1, Math.round(Number(card.querySelector('input[type=number]').value) || 1)) }));
$('#course-add').onclick = () => { const id = $('#course-select').value; if (!id || !plan) return; const course = plan.allCourses.find(c => c.productId === id); pilotSelect({ courses: [...selectedCourses(), { productId: id, participants: course.courseType === 'ttt' ? 1 : plan.profile.totalStudents }] }); };
$('#pilot-form').onsubmit = e => { e.preventDefault(); void pilotSend($('#pilot-message').value.trim()); };
$('#pilot-message').addEventListener('input', () => { if (pilotBusy || !mascot) return; if (mascot.state !== 'listening') mascot.say('I’m listening…'); mascot.setState('listening', { hold: 1800 }); });
$('#pilot-message').onkeydown = e => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); $('#pilot-form').requestSubmit(); } };
stagger(document.querySelectorAll('[data-pilot]'));
for (const button of document.querySelectorAll('[data-pilot]')) button.onclick = () => void pilotSend(button.dataset.pilot);
$('#next').onclick = () => { if (validStep()) showStep(Math.min(2, step + 1)); };
$('#previous').onclick = () => showStep(Math.max(0, step - 1));
for (const id of ['board', 'setup', 'layout']) $(`#${id}`).onchange = guidance;
$('#edit').onclick = () => { $('#manual').open = true; showStep(0); $('#manual').scrollIntoView({ behavior: 'smooth' }); };
$('#planner').onsubmit = async e => { e.preventDefault(); if (step < 2) { if (validStep()) showStep(step + 1); return; } error(''); const input = values(); if (!input.classes.length || !input.skillIds.length) { error('Choose at least one class and one skill course.'); return; } const button = $('#generate'); button.disabled = true; button.textContent = 'Preparing your plan…'; try { const response = await api('/api/lab-planner/recommend', input); pilotDraft = { ...input, assumed: [] }; render(response); renderLiveCourses(); } catch (e) { error(e.message); } finally { button.disabled = false; button.textContent = 'Generate my lab plan ✦'; } };
// Plan by budget: Kalpana lists lab plans that fit the budget. Choosing one saves it as the plan; inside a host
// page (the shop's Design My Lab popup) the host is told to take the visitor to checkout.
const embeddedHost = window.self !== window.top;
// CBSE composite labs are planned with at least three skill courses, one from each work category.
// Below that, the visitor is warned and decides whether to continue.
function cbseGap(board, skillIds) {
  if (board !== 'CBSE' || !options?.categories) return null;
  const chosen = skillIds.map(id => options.skills.find(s => s.id === id)).filter(Boolean);
  const missing = options.categories.filter(category => !chosen.some(skill => skill.category === category.id)).map(category => category.name);
  if (chosen.length >= 3 && !missing.length) return null;
  return `You have selected ${chosen.length} skill area${chosen.length === 1 ? '' : 's'}${chosen.length ? ` (${chosen.map(s => s.label).join(', ')})` : ''}.${missing.length ? ` Missing: ${missing.join(', ')}.` : ''} Do you still want to continue?`;
}
function confirmCbse(board, skillIds) {
  const gap = cbseGap(board, skillIds);
  if (!gap) return Promise.resolve(true);
  const dialog = $('#cbse-dialog'); $('#cbse-dialog-text').textContent = gap;
  return new Promise(resolve => {
    const finish = value => { dialog.close(); resolve(value); };
    $('#cbse-dialog-continue').onclick = () => finish(true);
    $('#cbse-dialog-back').onclick = () => { finish(false); $('#pilot-picker').scrollIntoView({ behavior: calm ? 'auto' : 'smooth', block: 'center' }); };
    dialog.oncancel = e => { e.preventDefault(); finish(false); };
    dialog.showModal();
  });
}
// Guided mode: confirming the summary plans and prices the lab, then shows every product for review.
// Proceeding from the review hands over to the shop's cart (or shows the full plan when Kalpana runs alone).
let reviewed = null;
$('#pilot-confirm-go').textContent = embeddedHost ? 'Confirm & review products →' : 'Confirm & create my plan →';
$('#pilot-confirm-go').onclick = async () => {
  const button = $('#pilot-confirm-go'); const label = button.textContent; error('');
  if (!(await confirmCbse(pilotDraft?.board, pilotDraft?.skillIds || []))) return;
  button.disabled = true; button.textContent = 'Preparing your products…';
  try {
    const { assumed, asked, ...profile } = pilotDraft;
    showReview(await api('/api/lab-planner/recommend', profile));
  } catch (e) { error(e.message); }
  button.disabled = false; button.textContent = label;
};
function showReview(result) {
  reviewed = result;
  const quote = result.quote; const fmt = amount => money(amount, quote.currency);
  const gst = Math.round(quote.total * (result.gstBasisPoints || 0) / 10000);
  $('#review-title').textContent = `${result.title} · ${quote.lines.length} products`;
  const rows = quote.lines.map(line => {
    const row = el('div', `review-line${line.kind === 'course' ? ' course' : ''}`);
    row.append(el('span', 'review-name', line.name), el('span', 'review-qty', `× ${line.quantity}`), el('span', 'review-amount', fmt(line.lineAmount)));
    if (line.kind === 'course') row.append(el('small', '', 'MySkool course · arranged separately, not added to the shop cart'));
    return row;
  });
  $('#review-lines').replaceChildren(...rows); stagger(rows);
  const total = (labelText, amount, strong) => { const row = el('div', strong ? 'review-total strong' : 'review-total'); row.append(el('span', '', labelText), el('span', '', fmt(amount))); return row; };
  $('#review-totals').replaceChildren(total('Subtotal', quote.total), ...(gst ? [total(`GST (${(result.gstBasisPoints / 100).toFixed(0)}%, shop lab rate)`, gst)] : []), total('Estimated total', quote.total + gst, true));
  $('#review-note').textContent = embeddedHost ? 'Request a quotation and our team will send a formal proposal for exactly this lab. Prices, taxes and availability are confirmed in the proposal.' : 'This is the verified quote for the plan.';
  $('#review-go').textContent = embeddedHost ? 'Request quotation →' : 'Show the full plan →';
  $('#pilot-confirm').hidden = true; $('#pilot-review').hidden = false;
  $('#pilot-review').scrollIntoView({ behavior: calm ? 'auto' : 'smooth', block: 'start' });
}
$('#review-back').onclick = () => { $('#pilot-review').hidden = true; $('#pilot-confirm').hidden = false; $('#pilot-message').focus(); };
$('#review-go').onclick = () => {
  // Inside the shop, the host page opens its proposal form filled with this design.
  if (embeddedHost && integrationSession) { window.parent.postMessage({ type: 'kalpana:quote', sessionId: integrationSession }, '*'); $('#review-go').disabled = true; $('#review-go').textContent = 'Opening the quotation request…'; setTimeout(() => { $('#review-go').disabled = false; $('#review-go').textContent = 'Request quotation →'; }, 4000); return; }
  $('#pilot-review').hidden = true; render(reviewed);
};
// Basic vs Advanced vs Premium for the skill areas chosen so far: price of each level, then per skill area
// what each level adds over the one below (from the shop's own kit and lab-set contents).
const tierLabel = tier => tier[0].toUpperCase() + tier.slice(1);
function itemChips(items, className, limit) {
  const box = el('div', 'cmp-items');
  const shown = items.slice(0, limit);
  for (const item of shown) box.append(el('span', className, className === 'cmp-add' ? `+ ${item}` : item));
  if (items.length > limit) {
    const more = el('button', 'cmp-more', `+${items.length - limit} more`); more.type = 'button';
    more.onclick = () => { more.remove(); for (const item of items.slice(limit)) box.append(el('span', className, className === 'cmp-add' ? `+ ${item}` : item)); };
    box.append(more);
  }
  return box;
}
$('#tier-compare-open').onclick = async () => {
  const dialog = $('#tier-dialog'); const draft = pilotDraft || values();
  const skillIds = draft.skillIds?.length ? draft.skillIds : currentSkills();
  error('');
  if (!skillIds.length) { error('Choose at least one skill area to compare levels.'); return; }
  $('#tier-columns').replaceChildren(el('p', 'muted', 'Comparing levels…')); $('#tier-dialog-intro').textContent = ''; dialog.showModal();
  try {
    const { assumed, asked, courses, ...profile } = draft;
    const result = await api('/api/lab-planner/compare-tiers', { ...profile, skillIds });
    const fmt = amount => money(amount, result.currency);
    const current = pilotDraft?.tier || 'basic';
    const names = { totalStudents: 'student numbers', rooms: 'room size', classes: 'classes', board: 'board', setup: 'scope', batchSize: 'batch size', layout: 'layout' };
    $('#tier-dialog-intro').textContent = `Every level covers the same ${skillIds.length} skill area${skillIds.length === 1 ? '' : 's'}. Higher levels add more advanced equipment and materials to what the level below includes.${result.assumed.length ? ` Prices use typical ${result.assumed.map(k => names[k] || k).join(', ')} until you tell me yours.` : ''}`;
    // Price cards
    const newItems = tier => result.rows.reduce((sum, row) => sum + (row.cells.find(c => c.tier === tier)?.added.length || 0), 0);
    const cards = el('div', 'cmp-cards');
    for (const [i, tier] of result.tiers.entries()) {
      const card = el('article', `cmp-card${tier.tier === current ? ' current' : ''}`);
      card.append(el('div', 'eyebrow', tier.tier === current ? 'YOUR CURRENT LEVEL' : `LEVEL ${i + 1}`), el('h3', '', tierLabel(tier.tier)), el('div', 'cmp-price', fmt(tier.totalWithGst)), el('p', 'cmp-sub', i ? `${fmt(tier.extraOverBasic)} more than Basic` : 'Starting level · incl. GST'));
      card.append(el('p', 'cmp-summary', i ? `Everything in ${tierLabel(result.tiers[i - 1].tier)} + ${newItems(tier.tier)} new tools & materials` : 'Core tools & materials for each skill area'));
      const pick = el('button', tier.tier === current ? 'secondary-button' : 'primary-button', tier.tier === current ? '✓ Selected' : `Choose ${tierLabel(tier.tier)}`); pick.type = 'button'; pick.disabled = tier.tier === current;
      pick.onclick = () => { dialog.close(); document.querySelector(`#tier-toggle .tier[data-tier="${tier.tier}"]`)?.click(); };
      card.append(pick); cards.append(card);
    }
    // What each level adds, per skill area
    const table = el('div', 'cmp-table'); table.style.setProperty('--cols', result.tiers.length);
    table.append(el('div', 'cmp-head', ''), ...result.tiers.map((tier, i) => el('div', 'cmp-head', i ? `${tierLabel(tier.tier)} adds` : `${tierLabel(tier.tier)} includes`)));
    for (const row of result.rows) {
      table.append(el('div', `cmp-row-label ${row.kind}`, row.label));
      row.cells.forEach((cell, i) => {
        const box = el('div', `cmp-cell${cell.tier === current ? ' current' : ''}`);
        box.dataset.tier = tierLabel(cell.tier);
        if (!cell.items.length) box.append(el('p', 'cmp-none', `${tierLabel(cell.usedTier)} set — the shop does not list its contents yet`));
        else if (!i) box.append(itemChips(cell.items, 'cmp-item', 6));
        else if (cell.sameAsPrevious) box.append(el('p', 'cmp-none', `Same as ${tierLabel(row.cells[i - 1].tier)} — the shop has no ${tierLabel(cell.tier)} set for this`));
        else if (!cell.added.length) box.append(el('p', 'cmp-none', `Same items as ${tierLabel(row.cells[i - 1].tier)}, in the ${tierLabel(cell.tier)} set`));
        else { box.append(el('p', 'cmp-plus', `Everything in ${tierLabel(row.cells[i - 1].tier)}, plus:`), itemChips(cell.added, 'cmp-add', 8)); }
        table.append(box);
      });
    }
    $('#tier-columns').replaceChildren(cards, el('h3', 'cmp-title', 'What you get at each level'), table);
  } catch (e) { $('#tier-columns').replaceChildren(el('p', 'error', e.message)); }
};
$('#tier-dialog-close').onclick = () => $('#tier-dialog').close();
const budgetWords = amount => amount >= 1e7 ? `${+(amount / 1e7).toFixed(2)} crore` : amount >= 1e5 ? `${+(amount / 1e5).toFixed(2)} lakh` : '';
const showBudgetWords = () => { const amount = Number($('#budget-amount').value); $('#budget-words').textContent = amount > 0 ? [money(Math.round(amount * 100), 'INR'), budgetWords(amount)].filter(Boolean).join(' · ') : ''; };
$('#budget-amount').oninput = showBudgetWords;
for (const chip of document.querySelectorAll('[data-budget]')) chip.onclick = () => { $('#budget-amount').value = chip.dataset.budget; showBudgetWords(); $('#budget-form').requestSubmit(); };
$('#budget-gst').onchange = () => { if ($('#budget-results').childElementCount && $('#budget-amount').value) $('#budget-form').requestSubmit(); };
// A budget mentioned in the conversation fills the box (without overwriting one typed here).
function prefillBudget(budgetMinor) { if (budgetMinor && !$('#budget-amount').value) { $('#budget-amount').value = budgetMinor / 100; showBudgetWords(); } }
function budgetCard(option, result, input) {
  const card = el('article', 'budget-card');
  const head = el('div', 'budget-card-head'); head.append(el('div', 'eyebrow', option.label.toUpperCase()));
  if (option.coversAllCategories) head.append(el('span', 'budget-badge', 'All 3 categories'));
  const pick = el('button', 'primary-button', embeddedHost ? 'Choose this lab →' : 'Use this plan →'); pick.type = 'button';
  pick.onclick = () => chooseBudgetOption(option, input, pick);
  const price = el('div', 'budget-price', money(result.includesGst ? option.totalWithGst : option.total, result.currency));
  const breakdown = el('p', 'muted', option.gst ? `${money(option.total, result.currency)} + ${Math.round(option.gstRate * 100)}% GST ${money(option.gst, result.currency)}` : 'GST, if any, is added at checkout');
  const items = el('details', 'budget-items'); items.append(el('summary', '', `What’s included · ${option.items.length} items`));
  const list = el('ul'); for (const item of option.items) list.append(el('li', '', `${item.quantity} × ${item.name}`)); items.append(list);
  card.append(head, el('h3', '', `${option.tier[0].toUpperCase()}${option.tier.slice(1)} · ${option.skills.length} skill area${option.skills.length === 1 ? '' : 's'}`), el('p', 'budget-skills', option.skills.join(' · ')), price, breakdown, el('p', 'budget-left', `${money(option.remaining, result.currency)} left in budget`), items, pick);
  return card;
}
$('#budget-form').onsubmit = async e => {
  e.preventDefault(); error('');
  const budgetMinor = Math.round(Number($('#budget-amount').value) * 100);
  const input = { ...values(), budgetMinor, optionalProductIds: [] };
  if (!input.classes.length) { error('Choose at least one class in the school details.'); return; }
  const button = $('#budget-submit'); button.disabled = true; button.textContent = 'Comparing…';
  try {
    const result = await api('/api/lab-planner/budget-options', { ...input, includesGst: $('#budget-gst').checked });
    const cards = result.options.map(option => budgetCard(option, result, input));
    $('#budget-results').replaceChildren(...cards); stagger(cards);
    $('#budget-note').textContent = result.includesGst ? 'Totals include GST at the shop’s lab rate. Skill areas you selected are always included.' : 'Totals are before GST; GST is added at checkout.';
    $('#budget-note').hidden = false;
  } catch (e) { $('#budget-results').replaceChildren(); $('#budget-note').hidden = true; error(e.message); }
  finally { button.disabled = false; button.textContent = 'Show options'; }
};
async function chooseBudgetOption(option, input, button) {
  error('');
  if (!(await confirmCbse(input.board, option.skillIds))) return; for (const b of document.querySelectorAll('#budget-results button')) b.disabled = true; button.textContent = 'Preparing…';
  try {
    const result = await api('/api/lab-planner/recommend', { ...input, skillIds: option.skillIds, tier: option.tier });
    pilotDraft = { ...input, skillIds: option.skillIds, tier: option.tier, assumed: [] };
    if (embeddedHost) { showReview(result); for (const b of document.querySelectorAll('#budget-results button')) b.disabled = false; button.textContent = 'Choose this lab →'; return; }
    render(result);
  } catch (e) { error(e.message); }
  for (const b of document.querySelectorAll('#budget-results button')) b.disabled = false; button.textContent = embeddedHost ? 'Choose this lab →' : 'Use this plan →';
}
$('#purchase').onsubmit = async e => { e.preventDefault(); error(''); const button = $('#purchase-button'); button.disabled = true; try { const form = new FormData(e.target); const response = await api('/api/orders', { quoteId: plan.quote.id, confirm: form.get('confirm') === 'on', customer: { name: form.get('name'), email: form.get('email') } }); $('#purchase').hidden = true; $('#order-result').className = 'success'; $('#order-result').textContent = `Request saved: ${response.order.id}. No payment was collected. ${response.message}`; if (response.checkout) { const link = el('a', '', 'Open optional demo simulation →'); link.href = paths.url(response.checkout); $('#order-result').append(document.createElement('br'), link); sessionStorage.setItem(paths.sessionKey, JSON.stringify(session)); } } catch (e) { error(e.message); } finally { button.disabled = !plan.quote.purchasable; } };
$('#download').onclick = () => { if (!plan) return; const url = URL.createObjectURL(new Blob([JSON.stringify(plan, null, 2)], { type: 'application/json' })); const link = el('a'); link.href = url; link.download = `kalpana-lab-${plan.quote.id}.json`; link.click(); setTimeout(() => URL.revokeObjectURL(url), 1000); };
async function start() {
  try {
    [health, options] = await Promise.all([api('/api/health'), api('/api/lab-planner/options')]);
    $('#mode').textContent = health.aiMode === 'gemini' && health.geminiConfigured ? '✦ Gemini autopilot + verified planning rules' : health.aiMode === 'demo' ? '✦ Autopilot (demo parser) + verified rules' : 'Rule-based planning available';
    $('#notice').textContent = `${options.demo ? 'DEMO: sample materials and fictional prices. ' : ''}${options.demo ? '' : 'Live Skillvation products and MySkool course fees. '}Planning observations require school/site review. The minimum three-course category check is a Kalpana planning rule, not a compliance certificate.`;
    $('#currency').textContent = options.currency;
    renderLiveCourses();
    if (!options.pricingConfigured) throw Error('Lab pricing is not published yet. Ask the pricing administrator to publish a lab planner catalog.');
    for (let grade = 6; grade <= 12; grade++) { const label = el('label'); const input = el('input'); input.type = 'checkbox'; input.value = grade; input.checked = grade <= 10; label.append(input, document.createTextNode(`Class ${grade}`)); $('#classes').append(label); }
    for (const [key, name] of [['ventilation', 'Ventilation'], ['electrical', 'Electrical readiness'], ['accessibility', 'Accessibility'], ['water', 'Water & drainage'], ['safety', 'Safety & supervision']]) { const label = el('label', '', name); const select = el('select'); select.name = key; for (const [value, text] of [['unknown', 'Needs assessment'], ['yes', 'Reported ready'], ['no', 'Improvement needed']]) { const option = el('option', '', text); option.value = value; select.append(option); } label.append(select); $('#readiness').append(label); }
    for (const category of options.categories) { const root = el('section', 'skill-category'); root.append(el('h3', '', category.name)); const choices = el('div', 'choices'); for (const skill of options.skills.filter(s => s.category === category.id)) { const label = el('label'); const input = el('input'); input.type = 'checkbox'; input.value = skill.id; input.checked = options.skills.length <= 4 || options.skills.find(s => s.category === category.id) === skill; label.append(input, document.createTextNode(skill.label)); choices.append(label); } root.append(choices); $('#skills').append(root); }
    const catalog = await api('/api/catalog');
    $('#tier-label').hidden = !options.tiers?.length;
    for (const productId of catalog.labPlanner.optionalProductIds) { const p = catalog.products.find(p => p.id === productId); const label = el('label'); const input = el('input'); input.type = 'checkbox'; input.value = p.id; label.append(input, document.createTextNode(`${p.name} · ${money(p.unitAmount)} ${options.mode === 'shop' ? 'per room' : 'per station'}`)); $('#upgrades').append(label); }
    try { session = JSON.parse(sessionStorage.getItem(sessionKey) || 'null'); } catch {}
    const launchToken = new URLSearchParams(location.hash.slice(1)).get('launch');
    if (launchToken) {
      session = await api('/api/v1/launch/exchange', { sessionId: integrationSession, launchToken });
      sessionStorage.setItem(sessionKey, JSON.stringify(session));
      window.history.replaceState(null, '', location.pathname + location.search);
    }
    if (integrationSession && !session) throw Error('This launch link has already been used or is missing. Open Kalpana again from your application.');
    let history; if (session) { try { history = await api('/api/conversation'); } catch (e) { if (integrationSession) throw e; session = null; } }
    if (history) { const lastQuote = [...history.messages].reverse().find(m => m.labPlan)?.labPlan?.quote; if (lastQuote && lastQuote.catalogRevision !== catalog.revision) { history = null; if (!integrationSession) session = null; pilotSay('assistant', 'The catalog has changed. Start a fresh plan using the current published courses and prices.'); } }
    if (!session) { session = await api('/api/conversations', {}); sessionStorage.setItem(sessionKey, JSON.stringify(session)); }
    if (history?.constraints.labProfile) { fill(history.constraints.labProfile, history.constraints.optionalProductIds); const last = [...history.messages].reverse().find(m => m.labPlan); if (last) { render(last.labPlan); $('#purchase-button').disabled = true; $('#purchase-note').textContent = 'Restored plan. Generate it again to confirm the current price and scope before proceeding.'; } }
    guidance(); showStep(0);
    if (health.aiMode === 'gemini' && !health.geminiConfigured) {
      // No key: the autopilot still works with the built-in local parser, and says so.
      $('#mode').textContent = '✦ Local autopilot + verified rules';
      $('#pilot-status').textContent = '✧ Using built-in local understanding. Add GEMINI_API_KEY for richer conversation.';
    }
    window.KalpanaVoice?.attach($('#pilot-mic'), $('#pilot-message'), { onFinal: () => $('#pilot-form').requestSubmit() });
    if (history?.constraints.labProfile) { pilotDraft = { ...history.constraints.labProfile, optionalProductIds: history.constraints.optionalProductIds, courses: history.constraints.courses || [], assumed: [] }; $('#pilot-examples').hidden = true; pilotSay('assistant', 'Welcome back. Your last plan is restored below. Tell me what to change and I will redesign it.'); }
    buildPicker(); renderLiveCourses();
    $('#pilot-message').focus();
  } catch (e) { error(e.message); $('#next').disabled = true; $('#generate').disabled = true; $('#pilot-send').disabled = true; }
}
void start();
