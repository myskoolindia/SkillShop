import { createHash, randomBytes, timingSafeEqual } from 'node:crypto';

export class ApiError extends Error {
  constructor(status, message) { super(message); this.status = status; }
}
export const assert = (ok, status, message) => { if (!ok) throw new ApiError(status, message); };
export const hash = value => createHash('sha256').update(value).digest('hex');
export const token = () => randomBytes(32).toString('hex');
export const equal = (a, b) => typeof a === 'string' && typeof b === 'string' && timingSafeEqual(Buffer.from(hash(a)), Buffer.from(hash(b)));
export const now = () => new Date().toISOString();
export function string(value, name, max = 200, min = 1) {
  assert(typeof value === 'string' && value.trim().length >= min && value.length <= max, 400, `Invalid ${name}`);
  return value.trim();
}
export function integer(value, name, min = 0, max = 10000000000) {
  assert(Number.isSafeInteger(value) && value >= min && value <= max, 400, `Invalid ${name}`);
  return value;
}
export function id(value, name = 'ID') {
  string(value, name, 100);
  assert(/^[a-zA-Z0-9_-]+$/.test(value), 400, `Invalid ${name}`);
  return value;
}
export async function readBody(req, limit = 2 * 1024 * 1024) {
  assert((req.headers['content-type'] || '').split(';')[0] === 'application/json', 415, 'Use application/json');
  let size = 0; const chunks = [];
  for await (const chunk of req) {
    size += chunk.length; assert(size <= limit, 413, 'Request is too large'); chunks.push(chunk);
  }
  try {
    const body = JSON.parse(Buffer.concat(chunks).toString());
    assert(body && typeof body === 'object' && !Array.isArray(body), 400, 'Expected a JSON object');
    return body;
  } catch (error) { if (error.status) throw error; throw new ApiError(400, 'Invalid JSON'); }
}
export const bearer = req => /^Bearer (.+)$/.exec(req.headers.authorization || '')?.[1];
export const money = (amount, currency) => new Intl.NumberFormat('en-IN', { style: 'currency', currency, maximumFractionDigits: 2 }).format(amount / 100);
