const $ = selector => document.querySelector(selector);
const make = (tag, className, text) => { const el = document.createElement(tag); if (className) el.className = className; if (text !== undefined) el.textContent = text; return el; };
if (new URLSearchParams(location.search).has('embed')) document.body.classList.add('embedded');
const calm = matchMedia('(prefers-reduced-motion: reduce)').matches;
const stagger = nodes => [...nodes].forEach((node, i) => node.style.setProperty('--i', i));
// Animate a figure up to its verified value; the final frame always shows the exact amount.
function countUp(node, target, format, ms = 900) {
  if (calm) { node.textContent = format(target); return; }
  const start = performance.now();
  const tick = time => { const p = Math.min(1, (time - start) / ms); node.textContent = format(p < 1 ? Math.round(target * (1 - (1 - p) ** 3)) : target); if (p < 1) requestAnimationFrame(tick); };
  requestAnimationFrame(tick);
}
const bigMascot = window.KalpanaMascot?.mount($('#mascot'), { size: 120, bubble: $('#mascot-bubble'), greeting: 'Hi, I’m Kalpana! What are you building?' });
const miniMascot = window.KalpanaMascot?.mount($('#mini-mascot'), { size: 40 });
// Both mascots mirror the same mood; the large one also speaks while the welcome is visible.
const feel = (mood, hold = 0, line = '') => { for (const m of [bigMascot, miniMascot]) m?.setState(mood, { hold }); if (line) bigMascot?.say(line); };
let session, catalog, health, selectedQuote, busy = false;
let budgetEdited = false, learnersEdited = false;
const storageKey = window.KalpanaPaths.sessionKey;
const price = (amount, currency = catalog.currency) => new Intl.NumberFormat('en-IN', { style: 'currency', currency, maximumFractionDigits: 2 }).format(amount / 100);
function error(message) { $('#error').textContent = message || ''; $('#error').hidden = !message; }
async function api(path, body) {
  const response = await fetch(window.KalpanaPaths.url(path), { method: body === undefined ? 'GET' : 'POST', headers: { 'Content-Type': 'application/json', ...(session ? { Authorization: `Bearer ${session.token}` } : {}) }, ...(body === undefined ? {} : { body: JSON.stringify(body) }), signal: AbortSignal.timeout(60000) });
  const data = await response.json(); if (!response.ok) throw Error(data.error || 'Request failed'); return data;
}
function setBusy(value) { busy = value; $('#send').disabled = value; $('#send').textContent = value ? '…' : '↑'; $('#requirements button').disabled = value; for (const chip of document.querySelectorAll('.suggestion-row button')) chip.disabled = value; }
const label = feature => catalog?.featureLabels?.[feature] || feature.replaceAll('-', ' ').replace(/^ai$/, 'AI');
function renderUnderstood(constraints) {
  const root = $('#understood-chips'); root.replaceChildren();
  const add = (name, value) => { const chip = make('span', 'chip'); chip.append(make('small', '', name), document.createTextNode(value)); root.append(chip); };
  if (constraints.budgetMinor != null) add('Budget', price(constraints.budgetMinor, constraints.currency || catalog.currency));
  if (constraints.learners != null) add('Learners', String(constraints.learners));
  for (const feature of constraints.features || []) add('Needs', label(feature));
  for (const id of constraints.packageIds || []) add('Package', catalog.products.find(p => p.id === id)?.name || id);
  for (const item of constraints.unsupportedRequirements || []) { const chip = make('span', 'chip assumed'); chip.append(make('small', '', 'Team review'), document.createTextNode(item.length > 60 ? `${item.slice(0, 57)}…` : item)); root.append(chip); }
  stagger(root.children);
  if (!root.children.length) root.append(make('span', 'chip ghost', 'Mention your budget, learners and goals in the conversation. I’ll pick them up here.'));
}
function thinking() {
  feel('thinking');
  const bubble = renderMessage('assistant', ''); bubble.classList.add('thinking'); const line = bubble.lastChild; let i = 0;
  const stages = ['Understanding what you need…', 'Reading the approved product documents…', 'Comparing every compatible combination…', 'Calculating verified quotes…'];
  line.textContent = stages[0]; const timer = setInterval(() => { i = Math.min(i + 1, stages.length - 1); line.textContent = stages[i]; }, 1100);
  return () => { clearInterval(timer); bubble.remove(); };
}
function renderMessage(role, text, response) {
  $('#welcome').hidden = true; $('#mini-mascot').hidden = false;
  const article = make('article', `message ${role}`); article.append(make('span', 'speaker', role === 'user' ? 'YOU' : '✦ KALPANA'), make('div', '', text));
  if (response) {
    for (const question of response.questions || []) article.append(make('p', 'muted', question));
    for (const warning of response.warnings || []) article.append(make('div', 'warning', warning));
    if (response.sources?.length) {
      const details = make('details', 'sources'); details.append(make('summary', '', `Product sources · ${new Set(response.sources.map(s => s.id)).size} documents`));
      for (const source of response.sources) details.append(make('p', 'source-title', `${source.title} · ${source.sha256.slice(0, 10)}`), make('blockquote', '', source.excerpt));
      article.append(details);
    }
    if (response.suggestions?.length) {
      for (const old of document.querySelectorAll('.suggestion-row')) old.remove();
      const row = make('div', 'suggestion-row');
      for (const text of response.suggestions) { const chip = make('button', '', `↗ ${text}`); chip.type = 'button'; chip.onclick = () => { void send(text); }; row.append(chip); }
      stagger(row.children); article.append(row);
    }
  }
  $('#messages').append(article);
  return article;
}
// Plain-language reasons computed from the verified quote fields, never from model prose.
function reasons(quote) {
  const format = amount => price(amount, quote.currency); const c = quote.constraints || {}; const out = [];
  if (c.learners && quote.capacity != null) out.push(quote.shortage ? [false, `Short by ${quote.shortage} learners`] : [true, `Fits ${c.learners} learners · capacity ${quote.capacity}`]);
  if (c.features?.length) out.push(quote.missingFeatures?.length ? [false, `Missing ${quote.missingFeatures.map(label).join(', ')}`] : [true, `Covers ${c.features.map(label).join(', ')}`]);
  if (c.budgetMinor != null) out.push(quote.overBudget ? [false, `${format(quote.overBudget)} over budget`] : [true, `${format(c.budgetMinor - quote.total)} under budget`]);
  return out;
}
function quoteCard(quote, nearest = false, best = false) {
  const format = amount => price(amount, quote.currency);
  const card = make('article', `quote-card${best ? ' best-match' : ''}`);
  if (best) card.append(make('div', 'best-badge', '✦ BEST MATCH'));
  card.append(make('div', 'eyebrow', nearest ? 'CLOSEST OPTION · ABOVE YOUR BUDGET' : quote.demo ? 'SAMPLE PACKAGE · DEMO PRICING' : 'VERIFIED CATALOG OPTION'));
  card.append(make('h3', '', quote.title || quote.lines.find(l => l.kind === 'package')?.name || quote.lines[0].name));
  const amount = make('div', 'price', format(0)); countUp(amount.firstChild, quote.total, format); amount.append(make('small', '', 'One-time total · configured tax and delivery included')); card.append(amount);
  const why = reasons(quote);
  if (why.length) { const row = make('div', 'reasons'); for (const [ok, text] of why) row.append(make('span', ok ? 'reason ok' : 'reason gap', `${ok ? '✓' : '!'} ${text}`)); stagger(row.children); card.append(row); }
  const list = make('ul'); for (const line of quote.lines) list.append(make('li', '', `${line.name} × ${line.quantity}`)); card.append(list);
  if (quote.constraints?.budgetMinor != null && window.KalpanaDiagrams) card.append(window.KalpanaDiagrams.budgetBar(quote.total, quote.constraints.budgetMinor, format));
  card.append(make('p', '', quote.capacity == null ? `${quote.lines.length} shop item${quote.lines.length === 1 ? '' : 's'}${quote.tax ? ' · GST included' : ''} · Revision ${quote.catalogRevision}` : `Capacity: ${quote.capacity} learners · Revision ${quote.catalogRevision}`));
  if (quote.overBudget) card.append(make('div', 'warning', `${format(quote.overBudget)} above the confirmed budget. Update your budget before proceeding.`));
  if (!quote.purchasable && !quote.overBudget) card.append(make('div', 'warning', 'Some requirements need team review before this quote can proceed.'));
  const button = make('button', 'primary-button', 'Customize & review ↗'); button.onclick = () => openQuote(quote); card.append(button);
  return card;
}
function renderQuotes(response) {
  $('#shortlist-count').textContent = String(response.quotes?.length || 0);
  const root = $('#shortlist'); root.replaceChildren();
  const best = (response.quotes || []).find(q => q.purchasable);
  for (const quote of response.quotes || []) root.append(quoteCard(quote, false, quote === best));
  if (response.nearest) root.append(quoteCard(response.nearest, true));
  stagger(root.children);
  if (!response.quotes?.length && !response.nearest) root.append(make('div', 'warning', 'No approved package meets these requirements. Try changing the learner count or requirements, or request a team-reviewed offer.'));
  if (response.constraints) {
    $('#budget').value = response.constraints.budgetMinor == null ? '' : response.constraints.budgetMinor / 100;
    $('#learners').value = response.constraints.learners ?? '';
    budgetEdited = false; learnersEdited = false; renderUnderstood(response.constraints);
  }
}
async function send(message, explicit = false) {
  if (busy) return;
  const input = { message };
  if (explicit || budgetEdited) input.budgetMinor = $('#budget').value ? Math.round(Number($('#budget').value) * 100) : null;
  if (explicit || learnersEdited) input.learners = $('#learners').value ? Number($('#learners').value) : null;
  error(''); setBusy(true); renderMessage('user', message); $('#message').value = '';
  const waiting = { remove: thinking() };
  try {
    const [response] = await Promise.all([api('/api/chat', input), new Promise(resolve => setTimeout(resolve, calm ? 0 : 900))]);
    if ([...response.quotes, ...(response.nearest ? [response.nearest] : [])].some(q => q.catalogRevision !== catalog.revision)) catalog = await api('/api/catalog');
    waiting.remove(); renderMessage('assistant', response.answer, response); renderQuotes(response);
    feel(response.quotes.length ? 'happy' : response.questions.length ? 'asking' : 'idle', 3000);
  } catch (e) { waiting.remove(); error(e.message); $('#message').value = message; feel('sad', 3000); bigMascot?.say('Hmm, I couldn’t answer that. See the note above the message box.'); }
  finally { setBusy(false); }
}
function detailQuote(quote) {
  const format = amount => price(amount, quote.currency);
  selectedQuote = quote; const root = $('#quote-detail'); root.replaceChildren();
  if (quote.demo) root.append(make('div', 'warning', 'Sample products and prices. This is not a live commercial offer.'));
  for (const line of quote.lines) {
    const row = make('div', 'line-item'); const left = make('div', '', `${line.name} × ${line.quantity}`); left.append(make('small', '', quote.model === 'shop' ? `${format(line.unitAmount)} ${line.unit === 'participant' ? 'per participant' : 'each'}${line.taxBasisPoints ? ` + ${line.taxBasisPoints / 100}% GST` : ''}` : `${format(line.unitAmount)} per package unit`)); const url = catalog.products.find(p => p.id === line.productId)?.url; if (url) { const link = make('a', 'shop-link', 'View in shop ↗'); link.href = url; link.target = '_blank'; link.rel = 'noopener noreferrer'; left.append(link); } row.append(left, make('span', '', format(line.lineAmount))); root.append(row);
  }
  for (const [label, value] of [['Subtotal', quote.subtotal], [quote.model === 'shop' ? 'GST (as listed by the shop)' : 'Configured tax', quote.tax], ['Delivery', quote.delivery]]) { const row = make('div', 'line-item'); row.append(make('span', '', label), make('span', '', format(value))); root.append(row); }
  const total = make('div', 'total-line'); total.append(make('strong', '', 'Total'), make('strong', '', format(quote.total))); root.append(total);
  root.append(make('p', 'muted', `One-time purchase${quote.capacity == null ? '' : ` · ${quote.capacity} learners`} · expires ${new Date(quote.expiresAt).toLocaleTimeString()} · catalog ${quote.catalogRevision}`));
  for (const doc of quote.sourceDocuments || []) root.append(make('p', 'muted', `Source: ${doc.title} (${doc.sha256.slice(0, 10)})`));
  if (!quote.purchasable) root.append(make('div', 'warning', [quote.overBudget ? `${format(quote.overBudget)} over budget.` : '', quote.shortage ? `Capacity is short by ${quote.shortage} learners.` : '', quote.missingFeatures?.length ? `Missing: ${quote.missingFeatures.join(', ')}.` : '', quote.constraints.unsupportedRequirements?.length ? 'Custom requirements need team review.' : ''].filter(Boolean).join(' ')));
  $('#purchase-button').disabled = !quote.purchasable;
  $('#purchase-note').textContent = health.checkoutMode === 'demo' ? 'Demo checkout only. No payment will be collected and no live order will be fulfilled.' : 'This submits a purchase request to the team. Payment checkout is not connected yet; no charge is made.';
  $('#purchase-button').textContent = health.checkoutMode === 'demo' ? 'Continue to demo checkout' : 'Submit purchase request';
  // Shop quotes are changed through the conversation; the package/add-on editor applies to package catalogs.
  if (quote.model === 'shop') return;
  const customize = make('details', 'sources'); customize.append(make('summary', '', 'Customize package and add-ons'));
  const form = make('form'); const label = make('label', '', 'Core package'); const select = make('select');
  for (const p of catalog.products.filter(p => p.active && p.kind === 'package')) { const option = make('option', '', p.name); option.value = p.id; select.append(option); }
  select.value = quote.lines.find(l => l.kind === 'package').productId; label.append(select); form.append(label);
  const quantityLabel = make('label', '', 'Number of package units'); const quantity = make('input'); quantity.type = 'number'; quantity.min = '1'; quantity.max = '100'; quantity.step = '1'; quantity.value = quote.lines.find(l => l.kind === 'package').quantity; quantityLabel.append(quantity); form.append(quantityLabel);
  const addons = make('div');
  function loadAddons() { addons.replaceChildren(); for (const p of catalog.products.filter(p => p.active && p.kind === 'addon' && p.compatibleWith.includes(select.value))) { const label = make('label', 'check-label'); const input = make('input'); input.type = 'checkbox'; input.value = p.id; input.checked = quote.lines.some(l => l.productId === p.id); label.append(input, make('span', '', `${p.name} · ${price(p.unitAmount)} per package unit`)); addons.append(label); } }
  select.onchange = loadAddons; loadAddons(); form.append(addons);
  const submit = make('button', 'secondary-button', 'Recalculate verified quote'); submit.type = 'submit'; form.append(submit);
  form.onsubmit = async e => { e.preventDefault(); submit.disabled = true; $('#purchase-error').hidden = true; try { const items = [{ productId: select.value, quantity: Number(quantity.value) }, ...[...addons.querySelectorAll('input:checked')].map(el => ({ productId: el.value, quantity: Number(quantity.value) }))]; const updated = await api('/api/quotes', { items }); detailQuote(updated); $('#purchase-form input[name=confirm]').checked = false; } catch (e) { $('#purchase-error').textContent = e.message; $('#purchase-error').hidden = false; } finally { submit.disabled = false; } };
  customize.append(form); root.append(customize);
}
function openQuote(quote) { $('#purchase-form').hidden = false; $('#order-result').hidden = true; $('#purchase-error').hidden = true; $('#purchase-form').reset(); detailQuote(quote); $('#quote-dialog').showModal(); }
$('#close-dialog').onclick = () => $('#quote-dialog').close();
$('#purchase-form').onsubmit = async e => {
  e.preventDefault(); $('#purchase-button').disabled = true; $('#purchase-error').hidden = true;
  try {
    const form = new FormData(e.target);
    const response = await api('/api/orders', { quoteId: selectedQuote.id, customer: { name: form.get('name'), email: form.get('email') }, confirm: form.get('confirm') === 'on' });
    if (response.checkout) { location.assign(window.KalpanaPaths.url(response.checkout)); return; }
    $('#purchase-form').hidden = true; $('#order-result').hidden = false; $('#order-result').className = 'success'; $('#order-result').textContent = `Purchase request saved. Reference: ${response.order.id}. No payment was collected. The team must follow up to complete the sale.`;
  } catch (e) { $('#purchase-error').textContent = e.message; $('#purchase-error').hidden = false; } finally { $('#purchase-button').disabled = !selectedQuote.purchasable; }
};
$('#chat-form').onsubmit = e => { e.preventDefault(); if ($('#message').value.trim()) void send($('#message').value.trim()); };
$('#message').onkeydown = e => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); $('#chat-form').requestSubmit(); } };
$('#budget').oninput = () => { budgetEdited = true; };
$('#learners').oninput = () => { learnersEdited = true; };
$('#requirements').onsubmit = e => { e.preventDefault(); void send('Please update the recommendations using the budget and learner count I entered.', true); };
for (const button of document.querySelectorAll('[data-prompt]')) button.onclick = () => { void send(button.dataset.prompt); };
$('#new-chat').onclick = async () => { if (busy) return; try { session = await api('/api/conversations', {}); sessionStorage.setItem(storageKey, JSON.stringify(session)); location.reload(); } catch (e) { error(e.message); } };
$('#message').addEventListener('input', () => { if (!busy && miniMascot?.state !== 'thinking') feel('listening', 1800); });
window.KalpanaVoice?.attach($('#mic'), $('#message'), { onFinal: () => $('#chat-form').requestSubmit() });
async function start() {
  setBusy(true);
  try {
    health = await api('/api/health'); catalog = await api('/api/catalog'); $('#currency-label').textContent = catalog.currency;
    $('#mode').textContent = health.aiMode === 'demo' ? '● Demo conversation' : health.geminiConfigured ? '✧ Powered by Gemini' : '○ Gemini key needed for chat';
    const notes = [];
    if (catalog.demo) notes.push('DEMO CATALOG — fictional products and sample prices.');
    if (health.aiMode === 'demo') notes.push('Deterministic demo responses; Gemini is not being called.');
    if (health.aiMode === 'gemini' && !health.geminiConfigured) notes.push('Add your GEMINI_API_KEY in .env and restart to enable conversations.');
    notes.push(health.checkoutMode === 'demo' ? 'Checkout is a simulation; no money is collected.' : 'Purchase requests only; live checkout is not connected.');
    $('#notice').textContent = notes.join(' '); $('#notice').hidden = false;
    try { session = JSON.parse(sessionStorage.getItem(storageKey) || 'null'); } catch {}
    let history;
    if (session) { try { history = await api('/api/conversation'); } catch { session = null; sessionStorage.removeItem(storageKey); } }
    if (history && history.messages.some(m => [...(m.quotes || []), ...(m.nearest ? [m.nearest] : [])].some(q => q.catalogRevision !== catalog.revision))) { history = null; session = null; }
    if (!session) { session = await api('/api/conversations', {}); sessionStorage.setItem(storageKey, JSON.stringify(session)); }
    if (history) {
      for (const message of history.messages) { renderMessage(message.role, message.message || message.answer, message.role === 'assistant' ? message : undefined); if (message.role === 'assistant') renderQuotes(message); }
      if (!history.messages.length) $('#welcome').hidden = false;
    }
  } catch (e) { error(e.message); $('#mode').textContent = 'Setup needed'; }
  finally { setBusy(false); }
}
void start();
