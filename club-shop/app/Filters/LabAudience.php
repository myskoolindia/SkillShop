<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LabAudience implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $rawPath = $request->getUri()->getPath();
        $path = '/' . ltrim(preg_replace('~^.*club-shop/?~i', '', $rawPath), '/');

        // Only inspect checkout/cart routes
        if (!preg_match('~^/(?:cart|checkout|lab-cart|basic-cart|advance-cart|advanced-cart|premium-cart|skillvation-cart|skillvation-advance-cart|skillvation-premium-cart|lab-design-cart|add-to-cart|buy-now)(?:[-/]|$)~', $path)) {
            return;
        }

        $session = session();
        $authUserId = (int)$session->get('auth_user_id');

        // 1. If user is authenticated in shop, ALWAYS allow them through!
        if ($authUserId > 0) {
            $role = $session->get('roboquest_shop_role');
            $roleExpires = (int)$session->get('roboquest_shop_role_expires');

            if (!empty($role) && $roleExpires >= time() && (int)$session->get('roboquest_shop_user_id') === $authUserId) {
                return;
            }

            // Bind role in background if available
            $user = user();
            if ($user && ((int)($user->role_id ?? 0) === 1 || ($user->username ?? '') === 'admin')) {
                $session->set('roboquest_shop_role', 'admin');
                $session->set('roboquest_shop_user_id', $authUserId);
                $session->set('roboquest_shop_role_expires', time() + 10800);
                return;
            }

            if ($user && !empty($user->email)) {
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
                        $lmsUser = $lmsDb->table('users')->where('email', $user->email)->get()->getRow();
                        if ($lmsUser && !empty($lmsUser->role)) {
                            $mappedRole = ($lmsUser->role === 'instructor') ? 'teacher' : $lmsUser->role;
                            $session->set('roboquest_shop_role', $mappedRole);
                            $session->set('roboquest_shop_user_id', $authUserId);
                            $session->set('roboquest_shop_role_expires', time() + 10800);
                            return;
                        }
                    }
                } catch (\Throwable $e) {}
            }

            $session->set('roboquest_shop_role', 'student');
            $session->set('roboquest_shop_user_id', $authUserId);
            $session->set('roboquest_shop_role_expires', time() + 10800);
            return;
        }

        // 2. Guests are allowed to browse and view their cart seamlessly
        return;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
