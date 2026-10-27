const $ = selector => document.querySelector(selector);
const orderId = new URLSearchParams(location.search).get('order');
let session;
try { session = JSON.parse(sessionStorage.getItem(window.KalpanaPaths.sessionKey) || 'null'); } catch {}
async function api(path, body) { const response = await fetch(window.KalpanaPaths.url(path), { method: body ? 'POST' : 'GET', headers: { Authorization: `Bearer ${session?.token || ''}`, 'Content-Type': 'application/json' }, ...(body ? { body: JSON.stringify(body) } : {}) }); const result = await response.json(); if (!response.ok) throw Error(result.error); return result; }
function error(e) { $('#error').textContent = e.message; $('#error').hidden = false; }
async function load() {
  if (!orderId || !session) throw Error('Return to the studio and select a package first.');
  const data = await api(`/api/orders/${encodeURIComponent(orderId)}`); const root = $('#detail'); root.replaceChildren();
  const money = amount => new Intl.NumberFormat('en-IN', { style: 'currency', currency: data.quote.currency }).format(amount / 100);
  const title = document.createElement('h2'); title.textContent = 'Review your demo order'; root.append(title);
  for (const line of data.quote.lines) { const p = document.createElement('p'); p.textContent = `${line.name} × ${line.quantity} — ${money(line.lineAmount)}`; root.append(p); }
  const total = document.createElement('strong'); total.textContent = `Total: ${money(data.quote.total)} · One-time · DEMO ONLY`; root.append(total);
  const ref = document.createElement('p'); ref.textContent = `Reference: ${data.order.id}`; root.append(ref);
  if (data.order.status === 'demo_completed') { $('#result').className = 'success'; $('#result').textContent = 'Demo already completed. No money was charged.'; }
  else if (data.order.status === 'demo_pending') $('#complete').hidden = false;
  else throw Error('This order does not support demo checkout.');
}
$('#complete').onsubmit = async e => { e.preventDefault(); const button = e.target.querySelector('button'); button.disabled = true; try { const result = await api(`/api/orders/${encodeURIComponent(orderId)}/demo-complete`, { confirm: true }); $('#complete').hidden = true; $('#result').className = 'success'; $('#result').textContent = result.message; } catch (e) { error(e); } finally { button.disabled = false; } };
void load().catch(error);
