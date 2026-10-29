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

        // Only enforce for checkout/cart routes
        if (!preg_match('~^/(?:cart|checkout|basic-cart|advance-cart|advanced-cart|premium-cart|skillvation-cart|lab-design-cart|add-to-cart|buy-now)(?:[-/]|$)~', $path)) {
            return;
        }

        $session = session();
        $authUserId = (int)$session->get('auth_user_id');

        // 1. If session is already verified and valid, allow access
        if ($authUserId > 0) {
            $role = $session->get('roboquest_shop_role');
            $roleExpires = (int)$session->get('roboquest_shop_role_expires');

            if ((int)$session->get('roboquest_shop_user_id') === $authUserId &&
                in_array($role, ['school', 'teacher', 'admin', 'instructor'], true) &&
                $roleExpires >= time()) {
                return;
            }

            // 2. If user is authenticated in shop as Admin, auto-bind admin role
            $user = user();
            if ($user && ((int)($user->role_id ?? 0) === 1 || ($user->username ?? '') === 'admin')) {
                $session->set('roboquest_shop_role', 'admin');
                $session->set('roboquest_shop_user_id', $authUserId);
                $session->set('roboquest_shop_role_expires', time() + 10800);
                return;
            }

            // 3. If user is authenticated in shop, verify their role against LMS database
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
                        if ($lmsUser && in_array($lmsUser->role, ['school', 'teacher', 'instructor', 'admin'], true)) {
                            $mappedRole = ($lmsUser->role === 'instructor') ? 'teacher' : $lmsUser->role;
                            $session->set('roboquest_shop_role', $mappedRole);
                            $session->set('roboquest_shop_user_id', $authUserId);
                            $session->set('roboquest_shop_role_expires', time() + 10800);
                            return;
                        }

                        if ($lmsUser) {
                            $member = $lmsDb->table('school_members')
                                ->where('user_id', $lmsUser->id)
                                ->where('role_in_school', 'teacher')
                                ->where('status', 'active')
                                ->get()->getRow();
                            if ($member) {
                                $session->set('roboquest_shop_role', 'teacher');
                                $session->set('roboquest_shop_user_id', $authUserId);
                                $session->set('roboquest_shop_role_expires', time() + 10800);
                                return;
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    // Ignore DB lookup failure
                }
            }
        }

        // 4. User is not authenticated or not authorized for lab checkout
        // For AJAX or JSON calls (e.g. JS add-to-cart), return structured JSON response
        $isAjax = $request->isAJAX() || str_contains($request->getHeaderLine('Accept') ?? '', 'application/json');
        if ($isAjax) {
            $mainLoginUrl = rtrim(env('MAIN_PORTAL_URL', 'http://localhost/skillvation.comphp'), '/') . '/sso/shop?target=' . urlencode(current_url());
            return service('response')
                ->setStatusCode(403)
                ->setJSON([
                    'status' => 0,
                    'result' => 0,
                    'message' => 'Lab checkout requires a verified school or teacher account. Open the lab shop from Skillvation while signed in.',
                    'redirect' => $mainLoginUrl,
                ]);
        }

        // For browser GET requests, seamlessly redirect through LMS SSO bridge to authenticate
        $method = strtoupper($request->getMethod());
        if ($method === 'GET') {
            $mainPortal = rtrim(env('MAIN_PORTAL_URL', 'http://localhost/skillvation.comphp'), '/');
            $target = ltrim(preg_replace('~^.*club-shop/?~i', '', $rawPath), '/');
            return redirect()->to($mainPortal . '/sso/shop?target=' . urlencode('/' . $target));
        }

        return service('response')
            ->setStatusCode(403)
            ->setBody('Lab checkout requires a verified school or teacher account. Open the lab shop from Skillvation while signed in.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
