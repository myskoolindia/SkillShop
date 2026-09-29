<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PhoneVerification;
use App\Models\User;
use App\Services\WabaOtpService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OtpAuthController extends Controller
{
    protected WabaOtpService $wabaService;

    public function __construct(WabaOtpService $wabaService)
    {
        $this->wabaService = $wabaService;
    }

    /**
     * Send OTP via WhatsApp to the provided phone number.
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'min:8', 'max:20'],
            'type'  => ['nullable', 'in:registration,login'],
        ]);

        $rawPhone   = trim($request->phone);
        $cleanPhone = $this->wabaService->sanitizePhone($rawPhone);
        $type       = $request->input('type', 'registration');

        // If login, ensure user exists
        if ($type === 'login') {
            $user = User::where('phone', $cleanPhone)
                ->orWhere('phone', $rawPhone)
                ->orWhere('phone', 'like', '%' . substr($cleanPhone, -10))
                ->first();

            if (!$user) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'No account found registered with this WhatsApp mobile number.',
                ], 404);
            }

            if ($user->status !== 'active') {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Your account is currently inactive. Please contact support.',
                ], 403);
            }

            if ($user->is_banned === 'yes') {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Your account has been suspended.',
                ], 403);
            }
        }

        // If registration, check if phone already registered
        if ($type === 'registration') {
            $exists = User::where('phone', $cleanPhone)
                ->orWhere('phone', $rawPhone)
                ->orWhere('phone', 'like', '%' . substr($cleanPhone, -10))
                ->exists();

            if ($exists) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'This mobile number is already registered. Please log in instead.',
                ], 422);
            }
        }

        // Rate limiting check (cooldown: 45 seconds)
        $recent = PhoneVerification::where('phone', $cleanPhone)
            ->where('created_at', '>=', Carbon::now()->subSeconds(45))
            ->first();

        if ($recent) {
            $secondsLeft = 45 - Carbon::now()->diffInSeconds($recent->created_at);
            return response()->json([
                'status'  => 'error',
                'message' => "Please wait {$secondsLeft}s before requesting a new OTP.",
            ], 429);
        }

        // Generate 6-digit OTP
        $otp = (string) random_int(100000, 999999);

        // Store OTP in database
        PhoneVerification::create([
            'phone'      => $cleanPhone,
            'otp'        => $otp,
            'type'       => $type,
            'expires_at' => Carbon::now()->addMinutes(10),
            'attempts'   => 0,
        ]);

        // Send via WhatsApp Business API
        $res = $this->wabaService->sendOtp($cleanPhone, $otp);

        if (!$res['success']) {
            return response()->json([
                'status'  => 'error',
                'message' => $res['message'],
            ], 500);
        }

        return response()->json([
            'status'   => 'success',
            'message'  => 'Verification code sent to your WhatsApp number.',
            'phone'    => $cleanPhone,
            'mock_otp' => $res['mock_otp'] ?? null,
        ]);
    }

    /**
     * Verify the received OTP.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'otp'   => ['required', 'string', 'size:6'],
        ]);

        $cleanPhone = $this->wabaService->sanitizePhone($request->phone);
        $otp        = trim($request->otp);

        $verification = PhoneVerification::where('phone', $cleanPhone)
            ->where('verified_at', null)
            ->where('expires_at', '>', Carbon::now())
            ->latest('id')
            ->first();

        if (!$verification) {
            return response()->json([
                'status'  => 'error',
                'message' => 'OTP has expired or is invalid. Please request a new code.',
            ], 422);
        }

        if ($verification->attempts >= 5) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Too many failed attempts. Please request a new OTP.',
            ], 429);
        }

        if ($verification->otp !== $otp) {
            $verification->increment('attempts');
            $remaining = 5 - $verification->attempts;
            return response()->json([
                'status'  => 'error',
                'message' => "Invalid OTP code. {$remaining} attempts remaining.",
            ], 422);
        }

        // OTP is valid - mark verified and generate verification token
        $token = Str::random(40);
        $verification->update([
            'verified_at' => Carbon::now(),
            'token'       => $token,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Mobile number verified successfully.',
            'token'   => $token,
            'phone'   => $cleanPhone,
        ]);
    }

    /**
     * Direct login using WhatsApp OTP for registered users.
     */
    public function loginWithOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'otp'   => ['required', 'string', 'size:6'],
        ]);

        $cleanPhone = $this->wabaService->sanitizePhone($request->phone);
        $rawPhone   = trim($request->phone);
        $otp        = trim($request->otp);

        $verification = PhoneVerification::where('phone', $cleanPhone)
            ->where('type', 'login')
            ->where('verified_at', null)
            ->where('expires_at', '>', Carbon::now())
            ->latest('id')
            ->first();

        if (!$verification) {
            return response()->json([
                'status'  => 'error',
                'message' => 'OTP expired or not found. Please request a new code.',
            ], 422);
        }

        if ($verification->attempts >= 5) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Too many failed attempts. Please request a new code.',
            ], 429);
        }

        if ($verification->otp !== $otp) {
            $verification->increment('attempts');
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid OTP code.',
            ], 422);
        }

        $verification->update(['verified_at' => Carbon::now()]);

        // Find the user
        $user = User::where('phone', $cleanPhone)
            ->orWhere('phone', $rawPhone)
            ->orWhere('phone', 'like', '%' . substr($cleanPhone, -10))
            ->first();

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'User not found.',
            ], 404);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Your account is inactive.',
            ], 403);
        }

        if ($user->is_banned === 'yes') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Your account has been suspended.',
            ], 403);
        }

        // Login user
        Auth::guard('web')->login($user, true);

        // Mark phone verified if not yet
        if (!$user->phone_verified_at) {
            $user->phone_verified_at = Carbon::now();
            $user->save();
        }

        if ($user->role === 'vendor') {
            $redirectUrl = \App\Services\SsoTokenService::generateShopSsoUrl($user, 'dashboard');
        } else {
            $redirectUrl = match ($user->role) {
                'school'     => route('school.dashboard'),
                'teacher'    => route('student.dashboard'),
                'instructor' => route('instructor.dashboard'),
                default      => route('student.dashboard'),
            };
        }

        return response()->json([
            'status'       => 'success',
            'message'      => 'Logged in successfully.',
            'redirect_url' => $redirectUrl,
        ]);
    }
}
