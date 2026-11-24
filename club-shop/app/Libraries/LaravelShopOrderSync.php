<?php

namespace App\Libraries;

class LaravelShopOrderSync
{
    /**
     * Safely sync a Club Shop order to Laravel shop_orders table.
     */
    public static function syncOrderSafely($orderId): void
    {
        try {
            $db = \Config\Database::connect();
            $order = $db->table('orders')->where('id', (int)$orderId)->get()->getRow();
            if (empty($order)) {
                return;
            }

            $laravel = \Config\Database::connect('laravel');

            $orderNumber = 'SKILLSHOP-' . $order->order_number;

            $shipping = @unserialize($order->shipping);
            if (!is_object($shipping)) {
                $shipping = (object)[];
            }

            $firstName = $shipping->sFirstName ?? ($shipping->bFirstName ?? '');
            $lastName  = $shipping->sLastName ?? ($shipping->bLastName ?? '');
            $fullName  = trim("{$firstName} {$lastName}");

            $email = $shipping->sEmail ?? ($shipping->bEmail ?? null);
            $phone = $shipping->sPhoneNumber ?? ($shipping->bPhoneNumber ?? null);

            $userId = null;
            if ($order->buyer_id) {
                $shopUser = $db->table('users')->where('id', $order->buyer_id)->get()->getRow();
                if ($shopUser) {
                    if (empty($fullName)) {
                        $fullName = trim(($shopUser->first_name ?? '') . ' ' . ($shopUser->last_name ?? ''));
                    }
                    if (empty($email)) {
                        $email = $shopUser->email ?? null;
                    }
                    if (empty($phone)) {
                        $phone = $shopUser->phone_number ?? null;
                    }
                    if (!empty($shopUser->email)) {
                        $lmsUser = $laravel->table('users')->select('id')->where('email', $shopUser->email)->get()->getRow();
                        if ($lmsUser) {
                            $userId = $lmsUser->id;
                        }
                    }
                }
            }

            $fullName = !empty($fullName) ? $fullName : 'School Representative';
            $phone    = !empty($phone) ? $phone : '9999999999';
            $email    = !empty($email) ? $email : 'procurement@school.local';

            $address     = $shipping->sAddress ?? ($shipping->bAddress ?? 'Address pending confirmation');
            $city        = $shipping->sCity ?? ($shipping->bCity ?? 'Bengaluru');
            $state       = $shipping->sState ?? ($shipping->bState ?? 'Karnataka');
            $pincode     = $shipping->sZipCode ?? ($shipping->bZipCode ?? '560001');
            $country     = $shipping->sCountry ?? ($shipping->bCountry ?? 'India');
            $addressType = $shipping->sTitle ?? ($shipping->bTitle ?? 'School / Institution');

            $paymentStatus = match ($order->payment_status) {
                'payment_received', 'completed', 'paid' => 'paid',
                'pending_payment', 'pending'            => 'pending',
                'payment_failed', 'failed'              => 'failed',
                'refunded'                              => 'refunded',
                default                                 => 'paid',
            };

            $orderStatus = match ((string)$order->status) {
                '1', 'completed', 'delivered'           => 'delivered',
                '2', 'cancelled'                        => 'cancelled',
                'processing', 'order_processing'        => 'processing',
                'shipped'                               => 'shipped',
                default                                 => 'pending',
            };

            $subtotal     = (float)($order->price_subtotal ?? 0);
            $shippingCost = (float)($order->price_shipping ?? 0);
            $discount     = (float)($order->coupon_discount ?? 0);
            $totalAmount  = (float)($order->price_total ?? ($subtotal + $shippingCost - $discount));

            $existing = $laravel->table('shop_orders')->where('order_number', $orderNumber)->get()->getRow();
            $orderData = [
                'user_id'         => $userId,
                'customer_name'   => $fullName,
                'customer_email'  => $email,
                'customer_phone'  => $phone,
                'pincode'         => $pincode,
                'city'            => $city,
                'state'           => $state,
                'country'         => $country,
                'address'         => $address,
                'address_type'    => $addressType,
                'payment_method'  => $order->payment_method ?? 'razorpay',
                'payment_id'      => $order->checkout_token ?? $order->bank_transaction_number ?? null,
                'payment_status'  => $paymentStatus,
                'order_status'    => $orderStatus,
                'subtotal'        => $subtotal,
                'shipping_cost'   => $shippingCost,
                'discount'        => $discount,
                'total_amount'    => $totalAmount,
                'currency'        => $order->price_currency ?? 'INR',
                'updated_at'      => date('Y-m-d H:i:s'),
            ];

            if ($existing) {
                $laravel->table('shop_orders')->where('id', $existing->id)->update($orderData);
                $shopOrderId = $existing->id;
            } else {
                $orderData['order_number'] = $orderNumber;
                $orderData['created_at']   = $order->created_at ?? date('Y-m-d H:i:s');
                $laravel->table('shop_orders')->insert($orderData);
                $shopOrderId = $laravel->insertID();
            }

            // Sync items
            $items = $db->table('order_items')->where('order_id', (int)$orderId)->get()->getResult();
            $laravel->table('shop_order_items')->where('shop_order_id', $shopOrderId)->delete();

            foreach ($items as $item) {
                $imageData = null;
                if (!empty($item->image_data)) {
                    $img = json_decode($item->image_data, true);
                    $imageData = $img['path'] ?? null;
                }

                $laravel->table('shop_order_items')->insert([
                    'shop_order_id' => $shopOrderId,
                    'product_id'    => $item->product_id ?? null,
                    'product_title' => $item->product_title ?? 'Lab Component Item',
                    'product_image' => $imageData,
                    'unit_price'    => (float)($item->product_unit_price ?? 0),
                    'quantity'      => (int)($item->product_quantity ?? 1),
                    'total_price'   => (float)($item->product_total_price ?? ($item->product_unit_price * ($item->product_quantity ?? 1))),
                    'created_at'    => $item->created_at ?? date('Y-m-d H:i:s'),
                    'updated_at'    => $item->updated_at ?? date('Y-m-d H:i:s'),
                ]);
            }

            // Sync to LeadDemo CRM API
            self::postToLeadDemo($order, $orderNumber, $fullName, $phone, $email, $address, $city, $state, $pincode, $paymentStatus, $totalAmount, $items);
        } catch (\Throwable $e) {
            log_message('error', '[LaravelShopOrderSync] syncOrderSafely error: ' . $e->getMessage());
        }
    }

    /**
     * Push order directly to LeadDemo CRM API.
     */
    private static function postToLeadDemo($order, $orderNumber, $fullName, $phone, $email, $address, $city, $state, $pincode, $paymentStatus, $totalAmount, $items): void
    {
        try {
            $api = rtrim((string) (env('LEADDEMO_API_URL') ?: self::getRootEnv('LEADDEMO_API_URL') ?: 'http://localhost/leaddemo'), '/');
            $apiUrl = $api . '/api/orders/create';

            $itemsPayload = [];
            $titles = [];
            foreach ($items as $item) {
                $titles[] = $item->product_title ?? 'Lab Equipment';
                $itemsPayload[] = [
                    'product_name' => $item->product_title ?? 'Lab Equipment',
                    'unit_price'   => (float)($item->product_unit_price ?? 0),
                    'quantity'     => (int)($item->product_quantity ?? 1),
                    'total_price'  => (float)($item->product_total_price ?? (($item->product_unit_price ?? 0) * ($item->product_quantity ?? 1))),
                    'discount'     => 0,
                ];
            }

            $productSummary = !empty($titles) ? implode(' + ', array_unique($titles)) : 'Skill Lab Equipment';

            $payload = [
                'order_number'   => $orderNumber,
                'customer_name'  => $fullName,
                'customer_phone' => $phone,
                'customer_email' => $email,
                'school_name'    => $fullName,
                'address'        => $address,
                'city'           => $city,
                'state'          => $state,
                'pincode'        => $pincode,
                'payment_method' => $order->payment_method ?? 'UPI',
                'payment_status' => $paymentStatus,
                'payment_date'   => !empty($order->created_at) ? date('Y-m-d', strtotime($order->created_at)) : date('Y-m-d'),
                'total_amount'   => (float)$totalAmount,
                'amount_paid'    => ($paymentStatus === 'paid') ? (float)$totalAmount : 0.0,
                'product_name'   => $productSummary,
                'source'         => 'Website',
                'assigned_to'    => (int) (env('LEADDEMO_ASSIGNED_TO') ?: self::getRootEnv('LEADDEMO_ASSIGNED_TO') ?: 116),
                'items'          => $itemsPayload,
            ];

            $res = self::postWithAuth($apiUrl, $payload);
            if ($res['success']) {
                log_message('info', "[LaravelShopOrderSync] LeadDemo synced successfully for Order #{$orderNumber}: " . json_encode($res['data'] ?? []));
            } else {
                log_message('error', "[LaravelShopOrderSync] LeadDemo sync failed for Order #{$orderNumber}: " . ($res['message'] ?? 'Unknown error'));
            }
        } catch (\Throwable $e) {
            log_message('error', '[LaravelShopOrderSync] postToLeadDemo failed: ' . $e->getMessage());
        }
    }

    /**
     * Send HTTP POST with Bearer token authentication, automatically re-minting on 401.
     */
    private static function postWithAuth(string $url, array $payload): array
    {
        $token = self::getLeadDemoToken();

        // 1. Initial attempt with current token
        $res = self::executePost($url, $payload, $token);

        // 2. If 401 Unauthorized, re-mint token and retry once
        if (($res['code'] ?? 0) === 401) {
            log_message('warning', '[LaravelShopOrderSync] LeadDemo token rejected (401), re-minting token...');
            $freshToken = self::mintToken();
            if (!empty($freshToken)) {
                $res = self::executePost($url, $payload, $freshToken);
            }
        }

        return $res;
    }

    /**
     * Perform HTTP POST request with cURL.
     */
    private static function executePost(string $url, array $payload, ?string $token): array
    {
        $headers = ['Content-Type: application/json'];
        if (!empty($token)) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($body === false) {
            return ['success' => false, 'code' => $code, 'message' => 'CURL error: ' . $err];
        }

        $data = json_decode($body, true);
        if ($code >= 200 && $code < 300) {
            return ['success' => true, 'code' => $code, 'data' => $data];
        }

        return ['success' => false, 'code' => $code, 'message' => $body, 'data' => $data];
    }

    /**
     * Get configured LeadDemo token, or mint a fresh one if not set.
     */
    public static function getLeadDemoToken(): ?string
    {
        $token = (string) (env('LEADDEMO_TOKEN') ?: self::getRootEnv('LEADDEMO_TOKEN') ?: '');
        if (!empty($token)) {
            return $token;
        }

        return self::mintToken();
    }

    /**
     * Mint a fresh LeadDemo Bearer token using LEADDEMO_EMAIL and LEADDEMO_PASSWORD.
     */
    public static function mintToken(): ?string
    {
        $api      = rtrim((string) (env('LEADDEMO_API_URL') ?: self::getRootEnv('LEADDEMO_API_URL') ?: 'http://localhost/leaddemo'), '/');
        $email    = (string) (env('LEADDEMO_EMAIL') ?: self::getRootEnv('LEADDEMO_EMAIL') ?: '');
        $password = (string) (env('LEADDEMO_PASSWORD') ?: self::getRootEnv('LEADDEMO_PASSWORD') ?: '');

        if (empty($email) || empty($password)) {
            log_message('error', '[LaravelShopOrderSync] Cannot mint LeadDemo token — LEADDEMO_EMAIL / LEADDEMO_PASSWORD not set.');
            return null;
        }

        $url = $api . '/api/auth/token';
        $payload = [
            'email'    => $email,
            'password' => $password,
            'label'    => 'SkillShop Order Sync ' . date('Y-m-d H:i:s'),
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code >= 200 && $code < 300) {
            $data = json_decode($body, true);
            if (!empty($data['token'])) {
                $newToken = $data['token'];
                self::persistLeadDemoToken($newToken);
                log_message('info', '[LaravelShopOrderSync] Successfully minted new LeadDemo token.');
                return $newToken;
            }
        }

        log_message('error', "[LaravelShopOrderSync] mintToken failed (HTTP {$code}): {$body}");
        return null;
    }

    /**
     * Persist fresh token to .env files for future requests.
     */
    private static function persistLeadDemoToken(string $token): void
    {
        putenv('LEADDEMO_TOKEN=' . $token);
        $_ENV['LEADDEMO_TOKEN'] = $token;
        $_SERVER['LEADDEMO_TOKEN'] = $token;

        $paths = [
            dirname(APPPATH, 2) . '/.env', // Root Laravel .env
            APPPATH . '../.env',           // Club shop .env
        ];

        foreach ($paths as $path) {
            try {
                $realPath = realpath($path);
                if ($realPath && is_writable($realPath)) {
                    $contents = file_get_contents($realPath);
                    $line = 'LEADDEMO_TOKEN=' . $token;
                    if (preg_match('/^LEADDEMO_TOKEN=.*$/m', $contents)) {
                        $contents = preg_replace('/^LEADDEMO_TOKEN=.*$/m', $line, $contents);
                    } else {
                        $contents .= PHP_EOL . $line . PHP_EOL;
                    }
                    file_put_contents($realPath, $contents);
                }
            } catch (\Throwable $e) {
                // Ignore write failures
            }
        }
    }

    /**
     * Read configuration value from root Laravel .env as fallback.
     */
    private static function getRootEnv(string $key): ?string
    {
        static $rootEnvCache = null;
        if ($rootEnvCache === null) {
            $rootEnvCache = [];
            $envPath = dirname(APPPATH, 2) . '/.env';
            if (file_exists($envPath) && is_readable($envPath)) {
                $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '' || str_starts_with($line, '#')) {
                        continue;
                    }
                    if (str_contains($line, '=')) {
                        [$k, $v] = explode('=', $line, 2);
                        $rootEnvCache[trim($k)] = trim(trim($v), '"\'');
                    }
                }
            }
        }

        return $rootEnvCache[$key] ?? null;
    }
}

