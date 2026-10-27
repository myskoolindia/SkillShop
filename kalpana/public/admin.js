const $ = selector => document.querySelector(selector);
let token = '';
function error(message) { $('#error').textContent = message || ''; $('#error').hidden = !message; }
async function api(path, body) {
  const response = await fetch(window.KalpanaPaths.url(path), { method: body === undefined ? 'GET' : 'POST', headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json' }, ...(body === undefined ? {} : { body: JSON.stringify(body) }), signal: AbortSignal.timeout(60000) });
  const value = await response.json(); if (!response.ok) throw Error(value.error || 'Request failed'); return value;
}
async function loadDocuments() {
  const data = await api('/api/admin/documents'); const root = $('#documents'); root.replaceChildren();
  for (const doc of data.documents) { const button = document.createElement('button'); button.className = 'secondary-button'; button.textContent = `${doc.title} · ${doc.id}`; button.onclick = async () => { try { $('#document-preview').textContent = JSON.stringify((await api(`/api/admin/documents/${encodeURIComponent(doc.id)}`)).document, null, 2); } catch (e) { error(e.message); } }; root.append(button); }
}
async function skillvation() {
  const data = await api('/api/admin/skillvation'); $('#skillvation-panel').hidden = !data.enabled; if (!data.enabled) return;
  const status = data.status; const root = $('#sync-status'); root.replaceChildren();
  const row = (label, value) => { const p = document.createElement('p'); const b = document.createElement('strong'); b.textContent = `${label}: `; p.append(b, document.createTextNode(value)); root.append(p); };
  if (!status) row('Status', 'Waiting for the first sync…');
  else {
    row('Status', status.ok ? (status.published ? 'Synced and published a new revision' : status.staged ? 'Changes staged for review' : 'Synced, no changes') : `Failed: ${status.error}`);
    row('Last attempt', new Date(status.at).toLocaleString()); if (!status.ok && status.lastSuccessAt) row('Last success', new Date(status.lastSuccessAt).toLocaleString());
    row('Active revision', status.revision || 'none');
    if (status.summary) row('Catalog', `${status.summary.products} shop products (${status.summary.bundles} bundles), ${status.summary.courses} courses, ${status.summary.skills} lab skill areas`);
    row('Mode', `${data.autoPublish ? 'Auto-publish' : 'Review before publishing'} · every ${data.intervalMinutes} minutes`);
  }
  $('#publish-staged').hidden = !data.staged;
  const list = $('#sync-warnings'); list.replaceChildren();
  for (const warning of status?.warnings || []) { const li = document.createElement('li'); li.textContent = warning; list.append(li); }
  if (!status?.warnings?.length) { const li = document.createElement('li'); li.textContent = 'No data issues found.'; list.append(li); }
}
async function orders() { $('#orders').textContent = JSON.stringify((await api('/api/admin/orders')).orders, null, 2); }
$('#connect').onsubmit = async e => { e.preventDefault(); token = $('#token').value.trim(); error(''); try { await loadDocuments(); await orders(); await skillvation(); $('#admin-content').hidden = false; } catch (e) { token = ''; $('#admin-content').hidden = true; error(e.message); } };
$('#document-form').onsubmit = async e => {
  e.preventDefault(); error(''); const button = e.target.querySelector('button'); button.disabled = true;
  try {
    const form = new FormData(e.target); const input = { title: form.get('title'), content: form.get('content') }; const file = $('#file').files[0];
    if (file) { if (file.size > 1000000) throw Error('Files must be no larger than 1 MB'); if (file.name.toLowerCase().endsWith('.pdf')) { const bytes = new Uint8Array(await file.arrayBuffer()); let binary = ''; for (let i = 0; i < bytes.length; i += 8192) binary += String.fromCharCode(...bytes.subarray(i, i + 8192)); input.pdfBase64 = btoa(binary); delete input.content; } else input.content = await file.text(); }
    const data = await api('/api/admin/documents', input); $('#uploaded').textContent = `Attached source ID: ${data.document.id}\nReview this extracted content before using it in an approved catalog:\n\n${data.document.content}`; await loadDocuments();
  } catch (e) { error(e.message); } finally { button.disabled = false; }
};
$('#current').onclick = async () => { try { const data = await api('/api/admin/catalog'); $('#catalog-json').value = JSON.stringify(data.catalog, null, 2); } catch (e) { error(e.message); } };
$('#sample').onclick = async () => { try { const sample = await api('/api/admin/sample-catalog'); const known = (await api('/api/admin/documents')).documents; for (const doc of sample.documents) if (!known.some(d => d.id === doc.id)) await api('/api/admin/documents', doc); delete sample.documents; $('#catalog-json').value = JSON.stringify(sample, null, 2); await loadDocuments(); } catch (e) { error(e.message); } };
$('#catalog-file').onchange = async e => { try { const file = e.target.files[0]; if (!file) return; if (file.size > 1000000) throw Error('Catalog file is too large'); const catalog = JSON.parse(await file.text()); if (catalog.documents) throw Error('Attach source documents first, then import catalog JSON without its documents array.'); $('#catalog-json').value = JSON.stringify(catalog, null, 2); } catch (e) { error(e.message); } };
$('#publish').onclick = async () => { error(''); try { const input = JSON.parse($('#catalog-json').value); if (!confirm(`Publish catalog revision ${input.revision}? Existing quotes cannot start new purchase requests after pricing changes.`)) return; const data = await api('/api/admin/catalog', input); $('#published').textContent = `Published ${data.catalog.revision}. ${data.catalog.demo ? 'Demo catalog — no live sales.' : 'Approved catalog is active.'}`; } catch (e) { error(e.message); } };
$('#refresh-orders').onclick = () => orders().catch(e => error(e.message));
$('#sync-now').onclick = async () => { const button = $('#sync-now'); button.disabled = true; button.textContent = 'Syncing…'; error(''); try { const { status } = await api('/api/admin/skillvation/sync', {}); if (!status.ok) error(status.error); await skillvation(); await loadDocuments(); } catch (e) { error(e.message); } finally { button.disabled = false; button.textContent = 'Sync now'; } };
$('#publish-staged').onclick = async () => { if (!confirm('Publish the staged Skillvation catalog? Quotes on the previous revision cannot start new purchase requests.')) return; try { await api('/api/admin/skillvation/publish', {}); await skillvation(); } catch (e) { error(e.message); } };
