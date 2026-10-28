<?php

namespace App\Services;

use App\Models\ShopOrder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShopApiService
{
    /**
     * Get the normalized API base URL.
     */
    public static function getBaseUrl(): string
    {
        $raw = env('STUDENT_SHOP_API_BASE_URL', 'https://test.myskill.club/api');
        $raw = rtrim($raw, '/');
        // If raw already ends with /api, use it; otherwise append /api
        if (preg_match('#/api$#i', $raw)) {
            return $raw;
        }
        return $raw . '/api';
    }

    /**
     * Authenticate with the remote API and return a Bearer token.
     */
    public static function getAuthToken(): ?string
    {
        $baseUrl = self::getBaseUrl();
        $email = env('STUDENT_SHOP_API_EMAIL');
        $password = env('STUDENT_SHOP_API_PASSWORD');
        if (!$email || !$password) return null;
        $cacheKey = 'student_shop_api_token_' . hash('sha256', $baseUrl . '|' . $email);
        if (Cache::has($cacheKey)) return Cache::get($cacheKey);

        try {
            $response = Http::timeout(10)->post($baseUrl . '/login', [
                'email'    => $email,
                'password' => $password,
            ]);

            if ($response->successful()) {
                $json = $response->json();
                $token = $json['token']
                    ?? $json['data']['token']
                    ?? $json['access_token']
                    ?? $json['data']['access_token']
                    ?? $json['authorisation']['token']
                    ?? null;

                if ($token) {
                    // Cache token for 50 minutes (assuming standard 60-min TTL)
                    Cache::put($cacheKey, $token, now()->addMinutes(50));
                    return $token;
                }

                Log::warning('ShopApiService: Login succeeded but no token found in response', ['response' => $json]);
            } else {
                Log::error('ShopApiService: Login failed', [
                    'url'    => $baseUrl . '/login',
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('ShopApiService: Exception during login: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return null;
    }

    /**
     * Normalize payment method to allowed POS API values:
     * Allowed: 'UPI', 'Card', 'Cash', 'Bank Transfer', 'Cheque'
     */
    public static function normalizePaymentMethod(?string $method): string
    {
        $m = strtolower(trim((string)$method));
        if (str_contains($m, 'cash') || str_contains($m, 'cod')) {
            return 'Cash';
        }
        if (str_contains($m, 'bank') || str_contains($m, 'transfer') || str_contains($m, 'neft') || str_contains($m, 'rtgs') || str_contains($m, 'imps')) {
            return 'Bank Transfer';
        }
        if (str_contains($m, 'cheque') || str_contains($m, 'check')) {
            return 'Cheque';
        }
        if (str_contains($m, 'card') || str_contains($m, 'credit') || str_contains($m, 'debit')) {
            return 'Card';
        }
        // Razorpay / online / UPI / default
        return 'UPI';
    }

    /**
     * Post completed order details to POS create-order API.
     */
    public static function syncOrderToPos(ShopOrder $order): array
    {
        // Load items if not already eager-loaded
        if (!$order->relationLoaded('items')) {
            $order->load('items');
        }

        $token = self::getAuthToken();
        if (!$token) {
            $msg = 'Failed to authenticate with Shop API; order POS sync skipped.';
            Log::error("ShopApiService [Order #{$order->order_number}]: {$msg}");
            return ['success' => false, 'message' => $msg];
        }

        $baseUrl = self::getBaseUrl();
        $url = $baseUrl . '/pos/create-order';

        $itemsPayload = [];
        foreach ($order->items as $item) {
            $itemsPayload[] = [
                'product_name' => $item->product_title ?? 'Product',
                'unit_price'   => (float) $item->unit_price,
                'quantity'     => (int) $item->quantity,
            ];
        }

        $payload = [
            'customer_name'     => $order->customer_name,
            'customer_phone'    => $order->customer_phone,
            'customer_email'    => $order->customer_email ?: 'noemail@example.com',
            'payment_date'      => $order->created_at ? $order->created_at->format('Y-m-d') : date('Y-m-d'),
            'payment_method'    => self::normalizePaymentMethod($order->payment_method),
            'payment_reference' => $order->payment_id ?: $order->order_number,
            'grand_total'       => (float) $order->total_amount,
            'amount_paid'       => (float) $order->total_amount,
            'subtotal'          => (float) ($order->subtotal > 0 ? $order->subtotal : $order->total_amount),
            'total_tax'         => 0,
            'shipping_charges'  => (float) ($order->shipping_cost ?? 0),
            'billing_address'   => [
                'line1'   => (string) ($order->address ?: 'N/A'),
                'city'    => (string) ($order->city ?: 'N/A'),
                'state'   => (string) ($order->state ?: 'N/A'),
                'pincode' => (string) ($order->pincode ?: 'N/A'),
            ],
            'items'             => $itemsPayload,
            'source'            => 'devproskillvation',
        ];

        try {
            $response = Http::withToken($token)
                ->timeout(15)
                ->post($url, $payload);

            // If token expired (401), clear cache and retry once with fresh token
            if ($response->status() === 401) {
                Log::info("ShopApiService [Order #{$order->order_number}]: Token expired (401), refreshing token and retrying...");
                Cache::forget('shop_api_bearer_token');
                $freshToken = self::getAuthToken();
                if ($freshToken) {
                    $response = Http::withToken($freshToken)
                        ->timeout(15)
                        ->post($url, $payload);
                }
            }

            if ($response->successful()) {
                Log::info("ShopApiService [Order #{$order->order_number}]: POS order synced successfully", [
                    'response' => $response->json(),
                ]);
                return [
                    'success'  => true,
                    'message'  => 'POS order created successfully',
                    'data'     => $response->json(),
                ];
            }

            Log::error("ShopApiService [Order #{$order->order_number}]: POS order sync failed", [
                'url'      => $url,
                'status'   => $response->status(),
                'response' => $response->body(),
                'payload'  => $payload,
            ]);

            return [
                'success'  => false,
                'status'   => $response->status(),
                'message'  => 'POS sync API returned error: ' . $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error("ShopApiService [Order #{$order->order_number}]: Exception during POS order sync: " . $e->getMessage(), [
                'payload' => $payload,
            ]);

            return [
                'success' => false,
                'message' => 'Exception during POS sync: ' . $e->getMessage(),
            ];
        }
    }
}
