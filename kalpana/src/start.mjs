import { resolve, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';
import { randomBytes } from 'node:crypto';
import { readFile } from 'node:fs/promises';
import { hash, assert } from './core.mjs';
import { createApp } from './http.mjs';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const demo = process.argv.includes('--demo');
const port = Number(process.env.PORT || 8787);
if (!Number.isInteger(port) || port < 1 || port > 65535) throw Error('Invalid PORT');
for (const key of ['SKILLVATION_SHOP_URL', 'SKILLVATION_COURSES_URL']) if (process.env[key] && !/^https?:\/\/[^\s]+$/.test(process.env[key])) throw Error(`${key} must be an http(s) URL`);
for (const [key, min, max] of [['SKILLVATION_SYNC_MINUTES', 5, 1440], ['SKILLVATION_KITS_PER_ROOM', 1, 50]]) if (process.env[key] && !(Number.isInteger(Number(process.env[key])) && Number(process.env[key]) >= min && Number(process.env[key]) <= max)) throw Error(`${key} must be an integer from ${min} to ${max}`);
const ephemeralAdmin = randomBytes(32).toString('hex');
const configuredToken = process.env.ADMIN_TOKEN && !process.env.ADMIN_TOKEN.startsWith('REPLACE_') ? process.env.ADMIN_TOKEN : undefined;
const app = await createApp({
  database: resolve(process.env.DATA_DIR || resolve(root, 'data'), 'kalpana.sqlite'),
  adminToken: configuredToken || (demo ? ephemeralAdmin : undefined),
  aiMode: demo ? 'demo' : process.env.AI_MODE || 'gemini',
  checkoutMode: demo ? 'demo' : process.env.CHECKOUT_MODE || 'request',
  seedDemo: demo || process.env.SEED_DEMO === 'true',
  geminiKey: process.env.GEMINI_API_KEY, geminiModel: process.env.GEMINI_MODEL || 'gemini-3.8-flash',
  embedOrigins: process.env.EMBED_ORIGINS,
  publicUrl: process.env.PUBLIC_URL || `http://localhost:${port}`,
  catalogSource: process.env.CATALOG_SOURCE === 'skillvation' && !demo ? 'skillvation' : 'manual',
  skillvationShopUrl: process.env.SKILLVATION_SHOP_URL || undefined, skillvationCoursesUrl: process.env.SKILLVATION_COURSES_URL || undefined,
  skillvationSyncMinutes: Number(process.env.SKILLVATION_SYNC_MINUTES || 60), skillvationAutoPublish: process.env.SKILLVATION_AUTO_PUBLISH !== 'false',
  skillvationKitsPerRoom: Number(process.env.SKILLVATION_KITS_PER_ROOM || 4),
});
if (process.argv.includes('--upgrade-sample')) {
  const current = app.service.db.catalog();
  assert(!current || (current.demo && ['demo-v1', 'demo-v2-labs'].includes(current.revision)), 409, 'Sample upgrade only applies to the known demo catalogs; publish real pricing through the admin studio');
  const sample = JSON.parse(await readFile(new URL('../examples/catalog.json', import.meta.url), 'utf8'));
  for (const doc of sample.documents) {
    const existing = app.service.db.getDocument(doc.id);
    assert(!existing || existing.sha256 === hash(doc.content), 409, 'A sample source differs from this template; use new source IDs through the admin studio');
    if (!existing) await app.service.addDocument(doc);
  }
  app.service.publish(sample); app.service.close();
  console.log(`Published sample catalog ${sample.revision}. Existing conversations and orders were retained.`);
  process.exit(0);
}
let stopping = false;
async function stop() { if (stopping) return; stopping = true; await app.close(); }
app.server.listen(port, process.env.HOST || '127.0.0.1', () => {
  console.log(`Kalpana: http://localhost:${port}`);
  console.log(`AI: ${app.service.config.aiMode} | Checkout: ${app.service.config.checkoutMode} | Catalog: ${app.service.config.catalogSource}`);
  if (!configuredToken && demo) console.log('Demo admin is disabled for this session. Set ADMIN_TOKEN in .env and restart to use /admin.');
});
app.server.on('error', error => { console.error(error.message); app.service.close(); process.exitCode = 1; });
process.on('SIGINT', stop); process.on('SIGTERM', stop);
