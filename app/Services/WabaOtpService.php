<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WabaOtpService
{
    /**
     * Send OTP via WhatsApp Business API (WabaConnect).
     *
     * @param string $phone Phone number with country code (e.g. 919876543210)
     * @param string $otp 6-digit OTP code
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendOtp(string $phone, string $otp): array
    {
        $cleanPhone = $this->sanitizePhone($phone);
        $driver     = config('services.waba.driver', env('WABA_DRIVER', 'wabaconnect'));

        try {
            switch ($driver) {
                case 'wabaconnect':
                    return $this->sendViaWabaConnect($cleanPhone, $otp);

                case 'meta':
                    return $this->sendViaMetaCloudApi($cleanPhone, $otp);

                case 'custom':
                    return $this->sendViaCustomEndpoint($cleanPhone, $otp);

                case 'mock':
                default:
                    Log::info("[WABA Mock OTP] Sent OTP '{$otp}' to phone: {$cleanPhone}");
                    return [
                        'success'  => true,
                        'message'  => 'OTP sent successfully (Mock Mode)',
                        'mock_otp' => config('app.debug') ? $otp : null,
                    ];
            }
        } catch (\Throwable $e) {
            Log::error("[WABA OTP Exception] " . $e->getMessage(), ['phone' => $cleanPhone]);
            return [
                'success' => false,
                'message' => 'Failed to send WhatsApp OTP: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send via WabaConnect REST API (as configured in devleaddemo_21082026).
     *
     * GET https://login.wabaconnect.com/api/sendtextmessage.php?LicenseNumber=...&APIKey=...&Contact=...&Message=...
     */
    protected function sendViaWabaConnect(string $phone, string $otp): array
    {
        $baseUrl       = rtrim(config('services.waba.base_url', env('WABA_BASE_URL', 'https://login.wabaconnect.com/api')), '/');
        $licenseNumber = config('services.waba.license_number', env('WABA_LICENSE_NUMBER', '79242162270'));
        $apiKey        = config('services.waba.api_key', env('WABA_API_KEY', 'MbeBNmpHqXVT7OJL3wtxKRkyd'));
        $templateName  = config('services.waba.template_name', env('WABA_TEMPLATE_NAME', 'otp'));

        if (!$licenseNumber || !$apiKey) {
            Log::warning("[WabaConnect] Missing license or API key, falling back to mock");
            return [
                'success'  => true,
                'message'  => 'OTP sent (Mock)',
                'mock_otp' => $otp,
            ];
        }

        // WhatsApp OTP requires registered template 'otp' with Param and URLParam
        $endpoint = "{$baseUrl}/sendtemplate.php";
        $params = [
            'LicenseNumber' => $licenseNumber,
            'APIKey'        => $apiKey,
            'Contact'       => $phone,
            'Template'      => $templateName ?: 'otp',
            'Param'         => $otp,
            'URLParam'      => $otp,
        ];

        $response = Http::withoutVerifying()
            ->timeout(10)
            ->get($endpoint, $params);

        $decoded = $response->json() ?? [];
        $apiResponse = strtolower($decoded['ApiResponse'] ?? '');
        $apiStatus   = strtolower($decoded['ApiMessage']['Status'] ?? $decoded['Status'] ?? '');

        $isSuccess = ($apiResponse === 'success' || $apiStatus === 'success') && $response->successful();

        if ($isSuccess) {
            Log::info("[WabaConnect OTP Sent]", ['phone' => $phone, 'response' => $decoded]);
            return [
                'success' => true,
                'message' => 'Verification code sent to your WhatsApp number.',
            ];
        }

        $errorMsg = $decoded['Reason']
            ?? $decoded['ApiMessage']['ErrorMessage']['error']['message']
            ?? $decoded['ApiMessage']['ErrorMessage']
            ?? $response->body()
            ?? 'Failed to send WhatsApp message.';

        if (is_array($errorMsg)) {
            $errorMsg = json_encode($errorMsg);
        }

        Log::error("[WabaConnect Error]", ['phone' => $phone, 'error' => $errorMsg, 'body' => $response->body()]);

        return [
            'success' => false,
            'message' => 'WhatsApp Gateway Error: ' . $errorMsg,
        ];
    }

    /**
     * Send via Meta WhatsApp Cloud API (Graph API v18.0+).
     */
    protected function sendViaMetaCloudApi(string $phone, string $otp): array
    {
        $phoneNumberId = config('services.waba.phone_number_id', env('WABA_PHONE_NUMBER_ID'));
        $token         = config('services.waba.api_token', env('WABA_API_TOKEN'));
        $templateName  = config('services.waba.template_name', env('WABA_TEMPLATE_NAME', 'auth_otp_code'));
        $languageCode  = config('services.waba.language_code', env('WABA_LANG_CODE', 'en_US'));

        if (!$phoneNumberId || !$token) {
            return [
                'success'  => true,
                'message'  => 'OTP sent (Mock)',
                'mock_otp' => $otp,
            ];
        }

        $url = "https://graph.facebook.com/v18.0/{$phoneNumberId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $phone,
            'type'              => 'template',
            'template'          => [
                'name'       => $templateName,
                'language'   => ['code' => $languageCode],
                'components' => [
                    [
                        'type'       => 'body',
                        'parameters' => [['type' => 'text', 'text' => $otp]],
                    ],
                ],
            ],
        ];

        $response = Http::withToken($token)
            ->timeout(10)
            ->post($url, $payload);

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => 'OTP sent successfully via WhatsApp.',
            ];
        }

        return [
            'success' => false,
            'message' => 'WhatsApp API Error: ' . ($response->json()['error']['message'] ?? 'Could not deliver OTP'),
        ];
    }

    /**
     * Send via Custom WABA Webhook or HTTP API Gateway.
     */
    protected function sendViaCustomEndpoint(string $phone, string $otp): array
    {
        $apiUrl = config('services.waba.api_url', env('WABA_API_URL'));
        $token  = config('services.waba.api_token', env('WABA_API_TOKEN'));

        if (!$apiUrl) {
            throw new \Exception('WABA_API_URL is not configured.');
        }

        $request = Http::timeout(10);
        if ($token) {
            $request = $request->withToken($token);
        }

        $response = $request->post($apiUrl, [
            'phone'   => $phone,
            'otp'     => $otp,
            'message' => "Your Skillvation verification code is: {$otp}.",
        ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => 'OTP sent successfully.',
            ];
        }

        return [
            'success' => false,
            'message' => 'Gateway error: ' . $response->body(),
        ];
    }

    /**
     * Clean and normalize a phone number to international format (digits only, no leading + or 0).
     */
    public function sanitizePhone(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);

        // Strip leading 0
        if (substr($clean, 0, 1) === '0') {
            $clean = substr($clean, 1);
        }

        // If 10 digits starting with 6,7,8,9, prepend 91 (India)
        if (strlen($clean) === 10 && in_array(substr($clean, 0, 1), ['6', '7', '8', '9'])) {
            $clean = '91' . $clean;
        }

        return $clean;
    }
}
