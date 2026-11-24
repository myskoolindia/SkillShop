<?php

namespace App\Services;

use App\Models\CourseEnquiry;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeadDemoSyncService
{
    /**
     * Check if a shop order belongs to Lab products (Composite Skill Lab / STEM Lab).
     */
    public static function isLabOrder(ShopOrder $order): bool
    {
        if (!$order->relationLoaded('items')) {
            $order->load('items');
        }

        foreach ($order->items as $item) {
            $title = strtolower($item->product_title ?? '');
            if (
                str_contains($title, 'lab') ||
                str_contains($title, 'composite') ||
                str_contains($title, 'stem') ||
                str_contains($title, 'skillvation') ||
                str_contains($title, 'robotic') ||
                in_array((int)$item->product_id, [68, 72, 73, 74, 75, 76], true)
            ) {
                return true;
            }
        }

        // Check if order number prefix indicates shop lab order
        return str_starts_with($order->order_number, 'SKILLSHOP-');
    }

    /**
     * Sync a single shop order to LeadDemo CRM (as a converted lead + sale).
     */
    public static function syncOrder(ShopOrder $order): array
    {
        $api = env('LEADDEMO_API_URL', 'http://localhost/leaddemo');
        if (empty($api)) {
            return ['success' => false, 'message' => 'LEADDEMO_API_URL not configured'];
        }

        if (!$order->relationLoaded('items')) {
            $order->load('items');
        }

        $itemsPayload = [];
        $productTitles = [];
        foreach ($order->items as $item) {
            $productTitles[] = $item->product_title;
            $itemsPayload[] = [
                'product_name' => $item->product_title ?? 'Lab Equipment',
                'unit_price'   => (float) $item->unit_price,
                'quantity'     => (int) $item->quantity,
                'total_price'  => (float) ($item->total_price ?: ($item->unit_price * $item->quantity)),
                'discount'     => 0,
            ];
        }

        $productSummary = !empty($productTitles) ? implode(' + ', array_unique($productTitles)) : 'Skill Lab Equipment';

        $payload = [
            'order_number'    => $order->order_number,
            'customer_name'   => $order->customer_name ?: 'School Representative',
            'customer_phone'  => $order->customer_phone ?: '',
            'customer_email'  => $order->customer_email ?: '',
            'school_name'     => $order->customer_name,
            'address'         => $order->address ?: '',
            'city'            => $order->city ?: '',
            'state'           => $order->state ?: '',
            'pincode'         => $order->pincode ?: '',
            'payment_method'  => $order->payment_method ?: 'UPI',
            'payment_status'  => $order->payment_status ?: 'pending',
            'payment_date'    => $order->created_at ? $order->created_at->format('Y-m-d') : date('Y-m-d'),
            'total_amount'    => (float) $order->total_amount,
            'amount_paid'     => ($order->payment_status === 'paid') ? (float) $order->total_amount : 0.0,
            'product_name'    => $productSummary,
            'source'          => 'Website',
            'assigned_to'     => (int) env('LEADDEMO_ASSIGNED_TO', 116),
            'items'           => $itemsPayload,
        ];

        $targetUrl = rtrim($api, '/') . '/api/orders/create';
        $res = self::postWithAuth($targetUrl, $payload);

        if ($res['success']) {
            Log::info("[LeadDemoSyncService] Synced Order #{$order->order_number} to LeadDemo", [
                'sale_id' => $res['data']['sale_id'] ?? null,
                'lead_id' => $res['data']['lead_id'] ?? null,
            ]);
        } else {
            Log::warning("[LeadDemoSyncService] Failed to sync Order #{$order->order_number} to LeadDemo", [
                'error' => $res['message'] ?? 'Unknown error',
            ]);
        }

        return $res;
    }

    /**
     * Sync a CourseEnquiry (Proposal) to LeadDemo CRM.
     */
    public static function syncProposal(CourseEnquiry $enquiry): array
    {
        $api = env('LEADDEMO_API_URL', 'http://localhost/leaddemo');
        if (empty($api)) {
            return ['success' => false, 'message' => 'LEADDEMO_API_URL not configured'];
        }

        $quotationData = $enquiry->quotation_data;
        $packageName = $enquiry->course_title ?: 'Skill Lab Package';

        $remarks = trim(implode(' — ', array_filter([
            $packageName,
            $enquiry->message ?? 'Request Detailed Proposal',
        ])));

        $notes = trim(implode(', ', array_filter([
            $enquiry->school  ?? '',
            $enquiry->city    ?? '',
            $enquiry->address ?? '',
        ])));

        $payload = [
            'lead_name'   => $enquiry->name,
            'phone'       => $enquiry->phone ?? '',
            'email'       => $enquiry->email ?? '',
            'status'      => 'NEW',
            'source'      => 'Website',
            'language'    => 'English',
            'remarks'     => $remarks,
            'notes'       => $notes ?: $packageName,
            'assigned_to' => (int) env('LEADDEMO_ASSIGNED_TO', 116),
            'pdf_json'    => $quotationData ?? [],
            'lead_value'  => $quotationData['grand_total'] ?? null,
        ];

        $targetUrl = rtrim($api, '/') . '/api/leads/create-with-demo';
        $res = self::postWithAuth($targetUrl, $payload);

        if ($res['success']) {
            Log::info("[LeadDemoSyncService] Synced Proposal #{$enquiry->id} ({$packageName}) to LeadDemo", [
                'lead_id'  => $res['data']['lead']['id'] ?? null,
                'prop_id'  => $res['data']['proposal']['id'] ?? null,
            ]);
        } else {
            Log::warning("[LeadDemoSyncService] Failed to sync Proposal #{$enquiry->id} to LeadDemo", [
                'error' => $res['message'] ?? 'Unknown error',
            ]);
        }

        return $res;
    }

    /**
     * Sync all existing Lab orders to LeadDemo.
     */
    public static function syncAllLabOrders(): int
    {
        $orders = ShopOrder::with('items')->get();
        $count = 0;
        foreach ($orders as $order) {
            if (self::isLabOrder($order)) {
                $res = self::syncOrder($order);
                if ($res['success']) {
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
     * Sync all existing Proposals to LeadDemo.
     */
    public static function syncAllProposals(): int
    {
        $enquiries = CourseEnquiry::all();
        $count = 0;
        foreach ($enquiries as $enquiry) {
            $res = self::syncProposal($enquiry);
            if ($res['success']) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Send HTTP POST with Bearer token authentication, re-minting if 401.
     */
    private static function postWithAuth(string $url, array $payload): array
    {
        $token = (string) env('LEADDEMO_TOKEN', '');

        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->withToken($token)
                ->post($url, $payload);

            if ($response->status() === 401) {
                Log::warning('[LeadDemoSyncService] Token 401, re-minting token...');
                $newToken = self::mintToken();
                if ($newToken) {
                    $response = Http::timeout(10)
                        ->withoutVerifying()
                        ->withToken($newToken)
                        ->post($url, $payload);
                }
            }

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return [
                'success' => false,
                'status'  => $response->status(),
                'message' => $response->body(),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Mint a fresh LeadDemo Bearer token using LEADDEMO_EMAIL / LEADDEMO_PASSWORD.
     */
    public static function mintToken(): ?string
    {
        $api      = env('LEADDEMO_API_URL', 'http://localhost/leaddemo');
        $email    = (string) env('LEADDEMO_EMAIL', '');
        $password = (string) env('LEADDEMO_PASSWORD', '');

        if (empty($email) || empty($password)) {
            Log::error('[LeadDemoSyncService] Cannot mint token — LEADDEMO_EMAIL / LEADDEMO_PASSWORD not set.');
            return null;
        }

        try {
            $resp = Http::timeout(8)
                ->withoutVerifying()
                ->post(rtrim($api, '/') . '/api/auth/token', [
                    'email'    => $email,
                    'password' => $password,
                    'label'    => 'Auto-minted by LMS ' . now()->toDateTimeString(),
                ]);

            if ($resp->successful() && !empty($resp->json('token'))) {
                $newToken = (string) $resp->json('token');
                self::persistToken($newToken);
                return $newToken;
            }
        } catch (\Throwable $e) {
            Log::error('[LeadDemoSyncService] Token mint failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Persist the new token to .env.
     */
    private static function persistToken(string $token): void
    {
        try {
            $envPath = base_path('.env');
            if (file_exists($envPath)) {
                $contents = file_get_contents($envPath);
                $line = 'LEADDEMO_TOKEN=' . $token;
                if (preg_match('/^LEADDEMO_TOKEN=.*$/m', $contents)) {
                    $contents = preg_replace('/^LEADDEMO_TOKEN=.*$/m', $line, $contents);
                } else {
                    $contents .= "\n" . $line;
                }
                file_put_contents($envPath, $contents);
            }
            $_ENV['LEADDEMO_TOKEN'] = $token;
        } catch (\Throwable $e) {
            Log::warning('[LeadDemoSyncService] Could not persist token to .env: ' . $e->getMessage());
        }
    }
}

