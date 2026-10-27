import { assert } from './core.mjs';

// Payment integration boundary. These two modes never contact a payment provider.
// A future provider must create a server-priced checkout, reconcile verified provider
// callbacks and persist a provider reference before any order may become paid.
export function checkoutPlan(mode, quote, orderId) {
  if (mode === 'demo') {
    assert(quote.demo, 409, 'Demo checkout requires a demo catalog');
    return { status: 'demo_pending', checkout: `/checkout?order=${orderId}`, message: 'Simulation only. No payment will be collected.' };
  }
  assert(mode === 'request', 503, 'Payment provider is not configured');
  return { status: 'requested', checkout: null, message: 'Purchase request saved. Payment provider is not connected; the team must follow up to complete the sale.' };
}
