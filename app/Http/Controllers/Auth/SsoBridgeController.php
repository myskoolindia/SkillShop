<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Services\SsoTokenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SsoBridgeController extends Controller
{
    /**
     * Bridge user from Laravel to Club Shop with authenticated SSO.
     */
    public function toShop(Request $request): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login', ['redirect' => $request->fullUrl()]));
        }

        $user = Auth::user();
        $target = $request->query('target', '');

        // If user has admin role in Laravel and target is admin or empty, allow direct shop admin access
        if ($user->role === 'admin' && empty($target) && $request->has('admin')) {
            $target = '/admin';
        }

        $ssoUrl = SsoTokenService::generateShopSsoUrl($user, $target);

        return redirect()->away($ssoUrl);
    }

    /**
     * Bridge user from Club Shop to Laravel LMS with authenticated SSO.
     */
    public function fromShop(Request $request): RedirectResponse
    {
        $token = $request->query('token');
        if (empty($token) || !is_string($token)) {
            $notification = ['messege' => __('Invalid SSO token.'), 'alert-type' => 'error'];
            return redirect()->route('login')->with($notification);
        }

        $parts = explode('.', $token, 2);
        if (count($parts) !== 2) {
            $notification = ['messege' => __('Malformed SSO token format.'), 'alert-type' => 'error'];
            return redirect()->route('login')->with($notification);
        }

        list($payloadEncoded, $signature) = $parts;

        $secretKey = config('sso.secret_key');
        $expectedSignature = hash_hmac('sha256', $payloadEncoded, $secretKey);

        if (!hash_equals($expectedSignature, $signature)) {
            $notification = ['messege' => __('Invalid SSO signature verification.'), 'alert-type' => 'error'];
            return redirect()->route('login')->with($notification);
        }

        $payloadJson = base64_decode(strtr($payloadEncoded, '-_', '+/'));
        $payload = json_decode($payloadJson, true);

        if (empty($payload) || !is_array($payload) || empty($payload['email'])) {
            $notification = ['messege' => __('Invalid SSO payload data.'), 'alert-type' => 'error'];
            return redirect()->route('login')->with($notification);
        }

        // Token expiry verification (default 120 seconds TTL)
        $iat = (int) ($payload['iat'] ?? 0);
        $ttl = (int) config('sso.token_ttl', 120);
        if (time() - $iat > $ttl || $iat > (time() + 60)) {
            $notification = ['messege' => __('SSO session expired. Please try logging in again.'), 'alert-type' => 'error'];
            return redirect()->route('login')->with($notification);
        }

        $email = trim($payload['email']);
        $user = User::where('email', $email)->first();

        if (!$user) {
            $fullName = trim(($payload['first_name'] ?? '') . ' ' . ($payload['last_name'] ?? ''));
            if (empty($fullName)) {
                $fullName = explode('@', $email)[0];
            }

            $isAdminPayload = (!empty($payload['role']) && $payload['role'] === 'admin');
            $role = $isAdminPayload ? 'admin' : 'student';

            $user = User::create([
                'name'              => $fullName,
                'email'             => $email,
                'phone'             => !empty($payload['phone']) ? $payload['phone'] : null,
                'role'              => $role,
                'status'            => 'active',
                'is_banned'         => 0,
                'password'          => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
            ]);
        } else {
            // Update phone if missing
            if (empty($user->phone) && !empty($payload['phone'])) {
                $user->phone = $payload['phone'];
                $user->save();
            }
        }

        // Check if user is banned or inactive
        if ($user->is_banned === 'yes' || $user->is_banned == 1) {
            $notification = ['messege' => __('Your account has been banned.'), 'alert-type' => 'error'];
            return redirect()->route('login')->with($notification);
        }

        if ($user->status !== 'active' && $user->status != 1) {
            $notification = ['messege' => __('Your account is inactive.'), 'alert-type' => 'error'];
            return redirect()->route('login')->with($notification);
        }

        // Check if admin login is needed
        $isAdmin = ($user->role === 'admin' || (!empty($payload['role']) && $payload['role'] === 'admin'));
        if ($isAdmin) {
            $adminUser = Admin::where('email', $email)->first();
            if ($adminUser) {
                Auth::guard('admin')->login($adminUser, true);
            }
        }

        // Log into web guard
        Auth::guard('web')->login($user, true);

        // Flash success
        $notification = ['messege' => __('Signed in via Single Sign-On successfully.'), 'alert-type' => 'success'];

        // Determine target redirect
        $target = trim($payload['target'] ?? '');
        if (!empty($target) && $target !== 'dashboard' && $target !== '/dashboard') {
            if (str_starts_with($target, 'http://') || str_starts_with($target, 'https://')) {
                return redirect()->away($target)->with($notification);
            }
            return redirect()->to(url(ltrim($target, '/')))->with($notification);
        }

        // Default role-based redirects
        if ($isAdmin && ($request->has('admin') || $user->role === 'admin' || $target === 'admin/dashboard')) {
            if (Auth::guard('admin')->check()) {
                return redirect()->route('admin.dashboard')->with($notification);
            }
        }

        $defaultRoute = match ($user->role) {
            'school'     => route('school.dashboard'),
            'teacher'    => route('student.dashboard'),
            'instructor' => route('instructor.dashboard'),
            'admin'      => (Auth::guard('admin')->check() ? route('admin.dashboard') : route('student.dashboard')),
            default      => route('student.dashboard'),
        };

        return redirect()->to($defaultRoute)->with($notification);
    }
}
