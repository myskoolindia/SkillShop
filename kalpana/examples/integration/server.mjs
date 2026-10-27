// Run from Kalpana/: node --env-file=.env.integration examples/integration/server.mjs
// This is a local integration example. Apply your application's user authentication
// and persistent session store when adapting these routes to your production backend.
import { createServer } from 'node:http';
import { readFile } from 'node:fs/promises';
import { randomBytes } from 'node:crypto';

const base = new URL(process.env.KALPANA_URL || 'http://localhost:8787');
const key = process.env.INTEGRATION_API_KEY;
if (!key || key.length !== 64) throw Error('Set INTEGRATION_API_KEY in .env.integration');
const visitors = new Map();
const upstream = async (path, body) => {
  const response = await fetch(new URL(path, base), { method: body === undefined ? 'GET' : 'POST', headers: { Authorization: `Bearer ${key}`, 'Content-Type': 'application/json' }, ...(body === undefined ? {} : { body: JSON.stringify(body) }), signal: AbortSignal.timeout(60000) });
  return { status: response.status, body: await response.json() };
};
createServer(async (req, res) => {
  res.setHeader('Cache-Control', 'no-store'); res.setHeader('X-Content-Type-Options', 'nosniff');
  res.setHeader('Content-Security-Policy', "default-src 'self'; script-src 'self'; connect-src 'self'; frame-ancestors 'none'; base-uri 'none'");
  const json = (status, body) => { res.writeHead(status, { 'Content-Type': 'application/json' }); res.end(JSON.stringify(body)); };
  try {
    const path = new URL(req.url, 'http://example.local').pathname;
    for (const [id, value] of visitors) if (value.until < Date.now()) visitors.delete(id);
    const cookie = /(?:^|;\s*)kalpanaExample=([a-f0-9]{64})(?:;|$)/.exec(req.headers.cookie || '')?.[1];
    const visitor = visitors.get(cookie);
    if (req.method === 'POST' && path === '/launch') {
      if (req.headers['sec-fetch-site'] === 'cross-site' || !req.headers.origin || new URL(req.headers.origin).host !== req.headers.host) return json(403, { error: 'Open the example page to launch Kalpana' });
      const result = await upstream('/api/v1/sessions', {});
      if (result.status === 201) {
        const id = randomBytes(32).toString('hex');
        if (cookie) visitors.delete(cookie);
        visitors.set(id, { sessionId: result.body.sessionId, until: Date.now() + 24 * 3600000 });
        res.setHeader('Set-Cookie', `kalpanaExample=${id}; HttpOnly; SameSite=Strict; Path=/; Max-Age=86400`);
        return json(201, { launchUrl: result.body.launchUrl });
      }
      return json(result.status, result.body);
    }
    if (req.method === 'GET' && path === '/result') {
      if (!visitor) return json(404, { error: 'Launch Kalpana first' });
      const result = await upstream(`/api/v1/sessions/${visitor.sessionId}/result`);
      return json(result.status, result.body);
    }
    if (req.method === 'GET' && ['/', '/client.js'].includes(path)) {
      res.writeHead(200, { 'Content-Type': path === '/' ? 'text/html; charset=utf-8' : 'text/javascript; charset=utf-8' });
      return res.end(await readFile(new URL(path === '/' ? './index.html' : './client.js', import.meta.url)));
    }
    json(404, { error: 'Not found' });
  } catch { json(502, { error: 'Kalpana could not be reached' }); }
}).listen(Number(process.env.EXAMPLE_PORT || 9091), '127.0.0.1', () => console.log('Button integration example: http://localhost:9091'));
