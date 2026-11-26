<?php
namespace App\Services;

use App\Models\ShopOrder;
use Illuminate\Support\Facades\{DB, Http, Log, Schema};
use RuntimeException;

/**
 * Confirms a storefront order (product pages → /api/shop-orders/create) was really paid before it counts:
 *   - each item is priced from the shop API (the same price the product page charges: price_discounted, else price);
 *   - Razorpay confirms the payment ID was captured, in INR, for exactly that total (an authorised payment is captured);
 *   - a payment ID pays for one order only.
 * A verified order is marked paid and payment_verified_at is set: only then is it sent to the POS, and only then do its
 * products unlock content (ProductCourseMapService). Anything else stays pending with the reason in admin_notes.
 */
class ShopPaymentVerifier
{
    public static function shopApiBase(): string
    {
        return rtrim((string) env('SHOP_API_BASE_URL', env('STUDENT_SHOP_API_BASE_URL', 'https://test.myskill.club/api/')), '/') . '/';
    }

    /** Server prices: product_id => unit price. Throws when an item has no product or the shop cannot price it. */
    public function prices(array $items): array
    {
        $prices = [];
        foreach ($items as $item) {
            $id = (string) ($item['product_id'] ?? '');
            if (!ctype_digit($id) || (int) $id < 1) throw new RuntimeException('an item has no shop product');
            if (isset($prices[$id])) continue;
            $response = Http::acceptJson()->timeout(15)->get(self::shopApiBase() . 'products/' . $id);
            $product = $response->successful() ? ($response->json('data') ?? $response->json()) : null;
            if (!is_array($product) || (string) ($product['id'] ?? '') !== $id) throw new RuntimeException("product {$id} could not be priced");
            $unit = (float) (($product['price_discounted'] ?? 0) > 0 ? $product['price_discounted'] : ($product['price'] ?? 0));
            if ($unit <= 0) throw new RuntimeException("product {$id} has no price");
            $prices[$id] = $unit;
        }
        return $prices;
    }

    private function razorpay(): array
    {
        $key = (string) DB::table('payment_gateways')->where('key', 'razorpay_key')->value('value');
        $secret = (string) DB::table('payment_gateways')->where('key', 'razorpay_secret')->value('value');
        if ($key === '' || $secret === '' || $key === 'razorpay_key') throw new RuntimeException('Razorpay is not configured');
        return [$key, $secret];
    }

    /**
     * Verifies the order and records the result. Returns null when verified, otherwise the reason. $hold: a new order
     * that fails stays pending (not paid); re-checking existing orders ($hold=false) never changes their status.
     */
    public function verify(ShopOrder $order, bool $hold = true): ?string
    {
        try {
            if (!Schema::hasColumn('shop_orders', 'payment_verified_at')) throw new RuntimeException('run the migrations (payment_verified_at)');
            if ($order->payment_verified_at) return null;
            $paymentId = (string) $order->payment_id;
            if (!preg_match('/^pay_[A-Za-z0-9]{6,40}$/', $paymentId)) throw new RuntimeException('no Razorpay payment ID');
            if (ShopOrder::where('payment_id', $paymentId)->where('id', '<>', $order->id)->whereNotNull('payment_verified_at')->exists()) {
                throw new RuntimeException('this payment already paid for another order');
            }
            $items = $order->items()->get();
            $prices = $this->prices($items->map(fn ($i) => ['product_id' => $i->product_id])->all());
            $total = round($items->sum(fn ($i) => $prices[(string) $i->product_id] * $i->quantity), 2);
            $paise = (int) round($total * 100);

            [$key, $secret] = $this->razorpay();
            $api = Http::withBasicAuth($key, $secret)->acceptJson()->timeout(15);
            $payment = $api->get('https://api.razorpay.com/v1/payments/' . $paymentId);
            if (!$payment->successful()) throw new RuntimeException('Razorpay does not know this payment');
            if ($payment->json('status') === 'authorized' && (int) $payment->json('amount') === $paise) {
                $payment = $api->post('https://api.razorpay.com/v1/payments/' . $paymentId . '/capture', ['amount' => $paise, 'currency' => 'INR']);
                if (!$payment->successful()) throw new RuntimeException('the payment could not be captured');
            }
            if ($payment->json('status') !== 'captured') throw new RuntimeException('the payment is ' . ($payment->json('status') ?: 'unknown'));
            if ($payment->json('currency') !== 'INR' || (int) $payment->json('amount') !== $paise) {
                throw new RuntimeException('paid ₹' . ((int) $payment->json('amount') / 100) . ' for an order of ₹' . $total);
            }

            DB::transaction(function () use ($order, $items, $prices, $total) {
                foreach ($items as $item) {
                    $unit = $prices[(string) $item->product_id];
                    $item->update(['unit_price' => $unit, 'total_price' => $unit * $item->quantity]);
                }
                $order->forceFill(['payment_status' => 'paid', 'payment_verified_at' => now(), 'subtotal' => $total, 'total_amount' => $total])->save();
            });
            return null;
        } catch (\Throwable $error) {
            $reason = $error instanceof RuntimeException ? $error->getMessage() : 'verification failed';
            if (!$error instanceof RuntimeException) Log::warning('[ShopPayment] ' . $error->getMessage());
            $note = 'Payment not verified automatically: ' . $reason . '.';
            $order->forceFill(['payment_status' => $hold && $order->payment_status === 'paid' ? 'pending' : $order->payment_status,
                'admin_notes' => trim(str_replace($note, '', (string) $order->admin_notes) . "\n" . $note)])->save();
            return $reason;
        }
    }
}
