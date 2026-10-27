(() => {
  const script = document.currentScript;
  if (!script || document.querySelector('[data-kalpana-widget]')) return;
  const base = script.dataset.kalpanaUrl ? new URL(script.dataset.kalpanaUrl).href.replace(/\/$/, '') : new URL('.', script.src).href.replace(/\/$/, '');
  const host = document.createElement('div'); host.dataset.kalpanaWidget = 'true';
  host.style.position = 'fixed'; host.style.bottom = '24px'; host.style.right = '24px'; host.style.zIndex = '2147483000';
  const shadow = host.attachShadow({ mode: 'open' });
  const button = document.createElement('button'); button.textContent = '✦ Ask Kalpana'; button.setAttribute('aria-expanded', 'false');
  Object.assign(button.style, { background: '#7757d8', color: '#fff', border: '0', borderRadius: '32px', padding: '15px 23px', font: '14px system-ui,sans-serif', cursor: 'pointer', boxShadow: '0 8px 30px #30204b30' });
  const frame = document.createElement('iframe'); frame.title = 'Kalpana package advisor'; frame.src = `${base}/?embed=1`; frame.hidden = true;
  frame.setAttribute('referrerpolicy', 'no-referrer'); frame.setAttribute('allow', 'microphone'); frame.setAttribute('sandbox', 'allow-scripts allow-same-origin allow-forms allow-popups');
  Object.assign(frame.style, { width: 'min(440px, calc(100vw - 32px))', height: 'min(740px, calc(100dvh - 120px))', border: '1px solid #e4ddef', borderRadius: '18px', boxShadow: '0 15px 70px #24203633', display: 'none', marginBottom: '12px', background: '#f7f7fb' });
  button.onclick = () => { const open = frame.hidden; frame.hidden = !open; frame.style.display = open ? 'block' : 'none'; button.textContent = open ? '× Close Kalpana' : '✦ Ask Kalpana'; button.setAttribute('aria-expanded', String(open)); };
  shadow.append(frame, button); document.body.append(host);
})();
