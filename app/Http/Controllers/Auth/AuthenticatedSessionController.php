<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\CustomRecaptcha;
use App\Services\WabaOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        if ($request->filled('redirect')) {
            session()->put('url.intended', $request->input('redirect'));
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request (Email or Phone + Password).
     */
    public function store(Request $request): RedirectResponse
    {
        $roboquest = $request->routeIs('roboquest.signin.store');
        $setting = Cache::get('setting');

        $rules = [
            'login'                => 'required|string',
            'password'             => 'required|string',
            'g-recaptcha-response' => ($setting && $setting->recaptcha_status == 'active') ? ['required', new CustomRecaptcha()] : 'nullable',
        ];

        if ($roboquest) $rules['account_role'] = 'required|in:teacher,student,school';
        $customMessages = [
            'login.required'    => __('Email or mobile number is required'),
            'password.required' => __('Password is required'),
            'g-recaptcha-response.required' => __('Please complete the recaptcha to submit the form'),
        ];
        $this->validate($request, $rules, $customMessages);

        $loginInput = trim($request->login);

        // Check if login input is email or phone
        $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);

        if ($isEmail) {
            $user = User::where('email', $loginInput)->first();
        } else {
            $wabaService = app(WabaOtpService::class);
            $cleanPhone = $wabaService->sanitizePhone($loginInput);
            $user = User::where('phone', $cleanPhone)
                ->orWhere('phone', $loginInput)
                ->orWhere('phone', 'like', '%' . substr($cleanPhone, -10))
                ->first();
        }

        // Check if user exists and password match
        if (!$user || !Hash::check($request->password, $user->password)) {
            $notification = __('Invalid credentials. Please check your email/mobile and password.');
            throw ValidationException::withMessages(['login' => $notification]);
        }
        if ($roboquest && $user->role !== $request->input('account_role')) {
            throw ValidationException::withMessages(['login'=>__('This account does not match the selected sign-in option. Choose your account type and try again.')]);
        }

        // Check if user active
        if ($user->status != UserStatus::ACTIVE->value && $user->status !== 'active') {
            $notification = __('Your account is inactive.');
            throw ValidationException::withMessages(['login' => $notification]);
        }

        // Check if user is banned
        if ($user->is_banned == UserStatus::BANNED->value || $user->is_banned === 'yes') {
            $notification = __('Your account has been banned.');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];

            return redirect()->back()->with($notification);
        }

        // Authenticate user
        Auth::guard('web')->login($user, (bool)$request->remember);
        $request->session()->regenerate();
        if ($roboquest) {
            $request->session()->forget('url.intended');
            return redirect()->route('roboquest.launch');
        }

        // Redirect user to dashboard based on role
        $notification = __('Logged in successfully.');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        $intendedUrl = session()->pull('url.intended');

        // Check redirect parameter in request or referer
        if (!$intendedUrl && $request->filled('redirect')) {
            $intendedUrl = $request->input('redirect');
        }

        if (!$intendedUrl && $request->headers->get('referer')) {
            $referer = $request->headers->get('referer');
            $parsedRef = parse_url($referer);
            if (!empty($parsedRef['query'])) {
                parse_str($parsedRef['query'], $queryParams);
                if (!empty($queryParams['redirect'])) {
                    $intendedUrl = $queryParams['redirect'];
                }
            }
        }

        // Validate intendedUrl is safe (same host or relative, no auth loops)
        if ($intendedUrl) {
            $parsed = parse_url($intendedUrl);
            $host = $parsed['host'] ?? null;
            $currentHost = $request->getHost();
            $isSameHost = empty($host) || $host === $currentHost || in_array($host, ['localhost', '127.0.0.1'], true);
            $isSafePath = !\Str::contains($intendedUrl, ['/admin', '/login', '/register', '/user-login', '/user-register']);

            if ($isSameHost && $isSafePath) {
                return redirect()->to($intendedUrl)->with($notification);
            }
        }

        if ($intendedUrl && \Str::contains($intendedUrl, '/admin')) {
            if ($user->role == 'instructor') return redirect()->route('instructor.dashboard');
            if ($user->role == 'school')     return redirect()->route('school.dashboard');
            return redirect()->route('student.dashboard');
        }

        if ($user->role === 'vendor') {
            $ssoUrl = \App\Services\SsoTokenService::generateShopSsoUrl($user, 'dashboard');
            return redirect()->away($ssoUrl)->with($notification);
        }

        $defaultRoute = match ($user->role) {
            'school'     => route('school.dashboard'),
            'teacher'    => route('student.dashboard'),
            'instructor' => route('instructor.dashboard'),
            default      => route('student.dashboard'),
        };

        return redirect()->to($defaultRoute)->with($notification);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $notification = __('Logged out successfully.');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->route('login')->with($notification);
    }
}
