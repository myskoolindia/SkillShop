import test from 'node:test';
import assert from 'node:assert/strict';
import { mkdtemp, rm } from 'node:fs/promises';
import { tmpdir } from 'node:os';
import { join } from 'node:path';
import { fileURLToPath } from 'node:url';
import { createServer } from 'node:net';
import { spawn } from 'node:child_process';

test('standalone launcher finds a compatible runtime and serves all customer/admin assets', { timeout: 20000 }, async t => {
  const directory = await mkdtemp(join(tmpdir(), 'kalpana-launch-'));
  const socket = createServer(); await new Promise(resolve => socket.listen(0, '127.0.0.1', resolve));
  const port = socket.address().port; await new Promise(resolve => socket.close(resolve));
  const root = fileURLToPath(new URL('../', import.meta.url));
  const child = spawn('sh', ['./run.sh', '--demo'], { cwd: root, env: { ...process.env, PORT: String(port), DATA_DIR: directory, HOST: '127.0.0.1', ADMIN_TOKEN: 'launcher-kalpana-test-token-000000000000000000' }, stdio: ['ignore', 'pipe', 'pipe'] });
  let output = ''; child.stdout.on('data', b => { output += b; }); child.stderr.on('data', b => { output += b; });
  t.after(async () => {
    if (child.exitCode === null) { const done = new Promise(resolve => child.once('exit', resolve)); child.kill('SIGTERM'); await done; }
    await rm(directory, { recursive: true, force: true });
  });
  const base = `http://127.0.0.1:${port}`;
  let health;
  for (let i = 0; i < 60; i++) {
    assert.equal(child.exitCode, null, output);
    try { const response = await fetch(base + '/api/health'); if (response.ok) { health = await response.json(); break; } } catch {}
    await new Promise(resolve => setTimeout(resolve, 100));
  }
  assert.equal(health?.name, 'Kalpana', output); assert.equal(health.aiMode, 'demo');
  for (const path of ['/', '/admin', '/checkout', '/styles.css', '/app.js', '/admin.js', '/checkout.js', '/widget.js']) {
    const response = await fetch(base + path); assert.equal(response.status, 200, path); assert.ok((await response.text()).length > 100);
  }
  assert.ok(!output.includes('launcher-kalpana-test-token-000000000000000000'));
});
