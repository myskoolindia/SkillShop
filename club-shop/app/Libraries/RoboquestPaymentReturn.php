<?php

namespace App\Libraries;

final class RoboquestPaymentReturn
{
    public static function destination(object $order, array $session, string $url): ?string
    {
        // For offline bank transfers pending payment, user must see bank transfer account info first
        if (($order->payment_method ?? '') === 'bank_transfer' && ($order->payment_status ?? '') !== 'payment_received') {
            return null;
        }

        // Validate destination URL format and path
        $origin = parse_url($url);
        if (!$origin || isset($origin['user']) || isset($origin['pass']) || isset($origin['query']) || isset($origin['fragment'])) {
            return null;
        }

        $path = rtrim($origin['path'] ?? '', '/');
        // Support both root domain (/roboquest/open) and subdirectory installs (/skillvation.comphp/roboquest/open)
        if (!str_ends_with($path, '/roboquest/open')) {
            return null;
        }

        $isHttps = (($origin['scheme'] ?? '') === 'https');
        $isLocalHttp = (($origin['scheme'] ?? '') === 'http' && in_array($origin['host'] ?? '', ['localhost', '127.0.0.1', '[::1]'], true));
        if (!$isHttps && !$isLocalHttp) {
            return null;
        }

        $orderBuyerId = (int)($order->buyer_id ?? 0);
        $email = null;
        $firstName = '';
        $lastName = '';
        $phone = '';
        $role = $session['roboquest_shop_role'] ?? null;
        $roleExpires = (int)($session['roboquest_shop_role_expires'] ?? 0);

        // 1. Resolve from registered shop user
        if ($orderBuyerId > 0) {
            try {
                $shopUser = \Config\Database::connect()->table('users')->where('id', $orderBuyerId)->get()->getRow();
                if ($shopUser) {
                    $email = $shopUser->email ?? null;
                    $firstName = $shopUser->first_name ?? '';
                    $lastName = $shopUser->last_name ?? '';
                    $phone = $shopUser->phone_number ?? '';
                    if (empty($role) || $roleExpires < time()) {
                        $role = ((int)($shopUser->role_id ?? 0) === 1) ? 'admin' : 'student';
                    }
                }
            } catch (\Throwable $e) {}
        }

        // 2. Resolve from order shipping details (e.g. guest checkout)
        if (empty($email) && !empty($order->shipping)) {
            $shipping = is_string($order->shipping) ? @unserialize($order->shipping) : (is_object($order->shipping) ? $order->shipping : null);
            if ($shipping) {
                $email = $shipping->sEmail ?? null;
                $firstName = $shipping->sFirstName ?? '';
                $lastName = $shipping->sLastName ?? '';
                $phone = $shipping->sPhoneNumber ?? '';
                if (empty($role)) {
                    $role = 'student';
                }
            }
        }

        // 3. Resolve from active shop session user
        if (empty($email) && function_exists('user') && ($currUser = user())) {
            $email = $currUser->email ?? null;
            $firstName = $currUser->first_name ?? '';
            $lastName = $currUser->last_name ?? '';
            $phone = $currUser->phone_number ?? '';
            if (empty($role)) {
                $role = ((int)($currUser->role_id ?? 0) === 1) ? 'admin' : 'student';
            }
        }

        // 4. Check LMS database for matched user role
        if (!empty($email)) {
            try {
                $lmsDb = function_exists('getLaravelDb') ? getLaravelDb() : null;
                if (!$lmsDb) {
                    try {
                        $lmsDb = \Config\Database::connect('laravel', false);
                    } catch (\Throwable $e) {
                        $lmsDb = null;
                    }
                }
                if ($lmsDb) {
                    $lmsUser = $lmsDb->table('users')->where('email', $email)->get()->getRow();
                    if ($lmsUser && !empty($lmsUser->role)) {
                        $role = ($lmsUser->role === 'instructor') ? 'teacher' : $lmsUser->role;
                        if ($orderBuyerId > 0) {
                            session()->set('roboquest_shop_role', $role);
                            session()->set('roboquest_shop_user_id', $orderBuyerId);
                            session()->set('roboquest_shop_role_expires', time() + 10800);
                        }
                    }
                }
            } catch (\Throwable $e) {}
        }

        // 5. Generate SSO authentication URL to seamlessly log into LMS and open RoboQuest
        if (!empty($email)) {
            $secretKey = env('SSO_SECRET_KEY') ?: 'd895fc43619de9914397dbbbf823e7825b5b408c3fba022620c12c638ce6b6be';
            if (is_string($secretKey) && strlen($secretKey) >= 32) {
                $payload = [
                    'uid'        => (string) ($orderBuyerId ?: 0),
                    'email'      => (string) $email,
                    'first_name' => (string) $firstName,
                    'last_name'  => (string) $lastName,
                    'phone'      => (string) $phone,
                    'role'       => (string) ($role ?: 'student'),
                    'target'     => 'roboquest/open',
                    'iat'        => time(),
                    'nonce'      => bin2hex(random_bytes(8)),
                ];
                $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
                $payloadEncoded = rtrim(strtr(base64_encode($payloadJson), '+/', '-_'), '=');
                $signature = hash_hmac('sha256', $payloadEncoded, $secretKey);
                $token = $payloadEncoded . '.' . $signature;

                return mainAppUrl('sso/login') . '?token=' . urlencode($token);
            }
        }

        return $url;
    }
}
