<?php

namespace App\Libraries;

final class RoboquestPaymentReturn
{
    public static function destination(object $order, array $session, string $url): ?string
    {
        $orderBuyerId = (int)($order->buyer_id ?? 0);
        if (!$orderBuyerId) {
            return null;
        }

        // Only redirect if payment was successfully received
        if (($order->payment_status ?? '') !== 'payment_received') {
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

        // Resolve user role: check session first, or query LMS user database
        $role = $session['roboquest_shop_role'] ?? null;
        $roleExpires = (int)($session['roboquest_shop_role_expires'] ?? 0);

        if (empty($role) || $roleExpires < time()) {
            try {
                $shopUser = \Config\Database::connect()->table('users')->where('id', $orderBuyerId)->get()->getRow();
                if ($shopUser && !empty($shopUser->email)) {
                    $lmsDb = function_exists('getLaravelDb') ? getLaravelDb() : null;
                    if (!$lmsDb) {
                        try {
                            $lmsDb = \Config\Database::connect('laravel', false);
                        } catch (\Throwable $e) {
                            $lmsDb = null;
                        }
                    }
                    if ($lmsDb) {
                        $lmsUser = $lmsDb->table('users')->where('email', $shopUser->email)->get()->getRow();
                        if ($lmsUser && in_array($lmsUser->role, ['school', 'teacher', 'instructor', 'admin'], true)) {
                            $role = ($lmsUser->role === 'instructor') ? 'teacher' : $lmsUser->role;
                            session()->set('roboquest_shop_role', $role);
                            session()->set('roboquest_shop_user_id', $orderBuyerId);
                            session()->set('roboquest_shop_role_expires', time() + 10800);
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Ignore DB error
            }
        }

        if (!in_array($role, ['school', 'teacher', 'admin', 'instructor'], true)) {
            return null;
        }

        return $url;
    }
}
