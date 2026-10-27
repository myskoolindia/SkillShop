import { DatabaseSync } from 'node:sqlite';
import { mkdirSync } from 'node:fs';
import { dirname } from 'node:path';
import { randomUUID } from 'node:crypto';
import { hash, now } from './core.mjs';

export class Database {
  constructor(path = ':memory:') {
    if (path !== ':memory:') mkdirSync(dirname(path), { recursive: true, mode: 0o700 });
    this.sql = new DatabaseSync(path);
    this.sql.exec(`PRAGMA journal_mode=WAL; PRAGMA foreign_keys=ON; PRAGMA busy_timeout=5000;
      CREATE TABLE IF NOT EXISTS documents(id TEXT PRIMARY KEY,title TEXT NOT NULL,content TEXT NOT NULL,sha256 TEXT NOT NULL,mime_type TEXT NOT NULL,created_at TEXT NOT NULL);
      CREATE TABLE IF NOT EXISTS catalogs(revision TEXT PRIMARY KEY,digest TEXT NOT NULL,payload TEXT NOT NULL,created_at TEXT NOT NULL);
      CREATE TABLE IF NOT EXISTS settings(key TEXT PRIMARY KEY,value TEXT NOT NULL);
      CREATE TABLE IF NOT EXISTS conversations(id TEXT PRIMARY KEY,token_hash TEXT NOT NULL,expires_at INTEGER NOT NULL,state TEXT NOT NULL,created_at TEXT NOT NULL);
      CREATE TABLE IF NOT EXISTS messages(id INTEGER PRIMARY KEY AUTOINCREMENT,conversation_id TEXT NOT NULL REFERENCES conversations(id),role TEXT NOT NULL,payload TEXT NOT NULL,created_at TEXT NOT NULL);
      CREATE TABLE IF NOT EXISTS quotes(id TEXT PRIMARY KEY,conversation_id TEXT NOT NULL REFERENCES conversations(id),catalog_revision TEXT NOT NULL REFERENCES catalogs(revision),payload TEXT NOT NULL,expires_at INTEGER NOT NULL,created_at TEXT NOT NULL);
      CREATE TABLE IF NOT EXISTS orders(id TEXT PRIMARY KEY,conversation_id TEXT NOT NULL REFERENCES conversations(id),quote_id TEXT NOT NULL UNIQUE REFERENCES quotes(id),status TEXT NOT NULL,customer TEXT NOT NULL,provider_reference TEXT,checkout_url TEXT,created_at TEXT NOT NULL,updated_at TEXT NOT NULL);
      CREATE TABLE IF NOT EXISTS audit(id INTEGER PRIMARY KEY AUTOINCREMENT,action TEXT NOT NULL,target TEXT NOT NULL,detail TEXT NOT NULL,created_at TEXT NOT NULL);
      CREATE TABLE IF NOT EXISTS integration_clients(id TEXT PRIMARY KEY,name TEXT NOT NULL,key_hash TEXT NOT NULL UNIQUE,enabled INTEGER NOT NULL DEFAULT 1,created_at TEXT NOT NULL);
      CREATE TABLE IF NOT EXISTS integration_sessions(id TEXT PRIMARY KEY,client_id TEXT NOT NULL REFERENCES integration_clients(id),conversation_id TEXT NOT NULL REFERENCES conversations(id),reference TEXT,launch_hash TEXT UNIQUE,launch_expires_at INTEGER NOT NULL,created_at TEXT NOT NULL);
      CREATE INDEX IF NOT EXISTS messages_conversation ON messages(conversation_id,id);
    `);
  }
  transaction(fn) {
    this.sql.exec('BEGIN IMMEDIATE');
    try { const result = fn(); this.sql.exec('COMMIT'); return result; }
    catch (error) { this.sql.exec('ROLLBACK'); throw error; }
  }
  audit(action, target, detail = {}) { this.sql.prepare('INSERT INTO audit(action,target,detail,created_at) VALUES (?,?,?,?)').run(action, target, JSON.stringify(detail), now()); }
  setting(key) { return this.sql.prepare('SELECT value FROM settings WHERE key=?').get(key)?.value; }
  set(key, value) { this.sql.prepare('INSERT INTO settings VALUES (?,?) ON CONFLICT(key) DO UPDATE SET value=excluded.value').run(key, value); }
  document(input) {
    const documentId = input.id || randomUUID();
    this.sql.prepare('INSERT INTO documents VALUES (?,?,?,?,?,?)').run(documentId, input.title, input.content, hash(input.content), input.mimeType || 'text/plain', now());
    return this.getDocument(documentId);
  }
  getDocument(id) { return this.sql.prepare('SELECT * FROM documents WHERE id=?').get(id); }
  documents() { return this.sql.prepare('SELECT id,title,sha256,mime_type,created_at FROM documents ORDER BY created_at DESC').all(); }
  catalog() {
    const revision = this.setting('catalog');
    if (!revision) return null;
    const row = this.sql.prepare('SELECT payload FROM catalogs WHERE revision=?').get(revision);
    return row ? JSON.parse(row.payload) : null;
  }
  history(conversationId) { return this.sql.prepare('SELECT role,payload FROM messages WHERE conversation_id=? ORDER BY id DESC LIMIT 12').all(conversationId).reverse().map(r => ({ role: r.role, ...JSON.parse(r.payload) })); }
  message(conversationId, role, payload) { this.sql.prepare('INSERT INTO messages(conversation_id,role,payload,created_at) VALUES (?,?,?,?)').run(conversationId, role, JSON.stringify(payload), now()); }
  quote(conversationId, payload) {
    const quote = { ...payload, id: randomUUID(), expiresAt: new Date(Date.now() + 30 * 60 * 1000).toISOString() };
    this.sql.prepare('INSERT INTO quotes VALUES (?,?,?,?,?,?)').run(quote.id, conversationId, quote.catalogRevision, JSON.stringify(quote), Date.parse(quote.expiresAt), now());
    return quote;
  }
  getQuote(id) { if (typeof id !== 'string') return null; const row = this.sql.prepare('SELECT * FROM quotes WHERE id=?').get(id); return row ? { ...row, quote: JSON.parse(row.payload) } : null; }
  getOrder(id) { if (typeof id !== 'string') return null; const row = this.sql.prepare('SELECT * FROM orders WHERE id=?').get(id); return row ? { ...row, customer: JSON.parse(row.customer) } : null; }
  close() { this.sql.close(); }
}
