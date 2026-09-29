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
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request (Email or Phone + Password).
     */
    public function store(Request $request): RedirectResponse
    {
        $setting = Cache::get('setting');

        $rules = [
            'login'                => 'required|string',
            'password'             => 'required|string',
            'g-recaptcha-response' => ($setting && $setting->recaptcha_status == 'active') ? ['required', new CustomRecaptcha()] : 'nullable',
        ];

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

        // Redirect user to dashboard based on role
        $notification = __('Logged in successfully.');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        $intendedUrl = session()->get('url.intended');
        if ($intendedUrl && \Str::contains($intendedUrl, '/admin')) {
            if ($user->role == 'instructor') return redirect()->route('instructor.dashboard');
            if ($user->role == 'school')     return redirect()->route('school.dashboard');
            return redirect()->route('student.dashboard');
        }

        $defaultRoute = match ($user->role) {
            'vendor'     => url('/club-shop/dashboard'),
            'school'     => route('school.dashboard'),
            'teacher'    => route('student.dashboard'),
            'instructor' => route('instructor.dashboard'),
            default      => route('student.dashboard'),
        };

        if ($user->role === 'vendor') {
            return redirect()->away($defaultRoute)->with($notification);
        }

        return redirect()->intended($defaultRoute)->with($notification);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $notification = __('Logged out successfully.');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->route('login')->with($notification);
    }
}
