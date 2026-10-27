(() => {
  const script = new URL(document.currentScript.src);
  const base = script.pathname.slice(0, -'/paths.js'.length);
  const scope = document.body.dataset.sessionScope || 'standalone';
  window.KalpanaPaths = {
    base,
    url: path => `${base}${path}`,
    sessionKey: `kalpana-session-v2:${base}:${scope}`,
  };
})();
