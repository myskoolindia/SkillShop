<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\WabaOtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PhoneOtpController extends Controller
{
    protected WabaOtpService $waba;

    public function __construct(WabaOtpService $waba)
    {
        $this->waba = $waba;
    }

    /**
     * Send a 6-digit OTP to the given phone via WhatsApp.
     * POST /phone-otp/send
     */
    public function send(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'min:7', 'max:20'],
        ]);

        $phone = $request->input('phone');
        $clean = $this->waba->sanitizePhone($phone);

        // Rate-limit: block resend within 60 seconds
        $lastSentAt = Session::get("otp_sent_at_{$clean}");
        if ($lastSentAt && now()->diffInSeconds($lastSentAt) < 60) {
            $wait = 60 - now()->diffInSeconds($lastSentAt);
            return response()->json([
                'status'  => 'rate_limited',
                'message' => "Please wait {$wait} seconds before requesting a new OTP.",
                'wait'    => $wait,
            ], 429);
        }

        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in session with 10-minute expiry
        Session::put("otp_{$clean}", [
            'code'       => $otp,
            'expires_at' => now()->addMinutes(10)->timestamp,
            'attempts'   => 0,
        ]);
        Session::put("otp_sent_at_{$clean}", now());

        $result = $this->waba->sendOtp($phone, $otp);

        if (!$result['success']) {
            return response()->json([
                'status'  => 'error',
                'message' => $result['message'] ?? 'Failed to send OTP. Please try again.',
            ], 500);
        }

        $response = [
            'status'  => 'sent',
            'message' => 'OTP sent to your WhatsApp number.',
        ];

        // Expose OTP in debug mode only
        if (config('app.debug') && isset($result['mock_otp'])) {
            $response['debug_otp'] = $result['mock_otp'];
        }

        return response()->json($response);
    }

    /**
     * Verify the OTP entered by the user.
     * POST /phone-otp/verify
     */
    public function verify(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'otp'   => ['required', 'digits:6'],
        ]);

        $clean      = $this->waba->sanitizePhone($request->input('phone'));
        $enteredOtp = $request->input('otp');
        $stored     = Session::get("otp_{$clean}");

        if (!$stored) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No OTP found. Please request a new one.',
            ], 422);
        }

        // Expiry check
        if (now()->timestamp > $stored['expires_at']) {
            Session::forget("otp_{$clean}");
            return response()->json([
                'status'  => 'expired',
                'message' => 'OTP has expired. Please request a new one.',
            ], 422);
        }

        // Max attempts: 5
        if ($stored['attempts'] >= 5) {
            Session::forget("otp_{$clean}");
            return response()->json([
                'status'  => 'blocked',
                'message' => 'Too many incorrect attempts. Please request a new OTP.',
            ], 429);
        }

        if ($enteredOtp !== $stored['code']) {
            $stored['attempts']++;
            Session::put("otp_{$clean}", $stored);
            $remaining = 5 - $stored['attempts'];
            return response()->json([
                'status'  => 'invalid',
                'message' => "Incorrect OTP. {$remaining} attempt(s) remaining.",
            ], 422);
        }

        // ✓ Verified — clear OTP, store verified flag
        Session::forget("otp_{$clean}");
        Session::put("phone_verified_{$clean}", true);

        return response()->json([
            'status'  => 'verified',
            'message' => 'Phone number verified successfully.',
        ]);
    }
}
