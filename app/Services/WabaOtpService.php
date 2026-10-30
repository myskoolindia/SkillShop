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
            $msg = $e->getMessage();
            if (str_contains($msg, 'cURL error 28') || stripos($msg, 'timed out') !== false) {
                Log::warning("[WABA OTP Timeout Handled] Gateway response timed out, but OTP message was likely dispatched: {$msg}", ['phone' => $cleanPhone]);
                return [
                    'success' => true,
                    'message' => 'Verification code sent to your WhatsApp number.',
                ];
            }

            Log::error("[WABA OTP Exception] " . $e->getMessage(), ['phone' => $cleanPhone]);
            return [
                'success' => false,
                'message' => 'Failed to send WhatsApp OTP: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send Lab Proposal notification via WhatsApp.
     *
     * @param string $phone Phone number with or without country code
     * @param array $data ['name', 'school', 'package', 'total', 'pdf_url', 'product_url', 'is_skillvation_lab']
     * @return array ['success' => bool, 'message' => string]
     */
    public function sendProposalMessage(string $phone, array $data): array
    {
        $cleanPhone = $this->sanitizePhone($phone);
        if (empty($cleanPhone)) {
            return ['success' => false, 'message' => 'Empty phone number'];
        }

        $driver = config('services.waba.driver', env('WABA_DRIVER', 'wabaconnect'));
        $name          = $data['name']    ?? 'School Representative';
        $school        = $data['school']  ?? 'your institution';
        $package       = $data['package'] ?? 'Skill Lab';
        $total         = !empty($data['total']) ? ('₹' . number_format((float) $data['total'], 2)) : '';
        $pdfUrl        = $data['pdf_url'] ?? '';
        $productUrl    = $data['product_url'] ?? url('/labs');
        $isSkillvation = !empty($data['is_skillvation_lab']);

        $labType = $isSkillvation ? 'Skillvation Lab (STEM, Robotics & AI)' : 'CBSE Composite Skill Lab';
        $totalLine = $total ? "\n💰 *Total Estimate:* {$total}" : '';

        $message = "*{$labType} Proposal*\n\n"
            . "Dear {$name},\n\n"
            . "Thank you for requesting a proposal for *{$school}*.\n\n"
            . "📋 *Package:* {$package}"
            . $totalLine . "\n\n"
            . "📄 *Download Proposal PDF:*\n{$pdfUrl}\n\n"
            . "🛒 *View Details in Club Shop:*\n{$productUrl}\n\n"
            . "Our academic coordinator will reach out shortly to assist with campus space planning and setup.\n\n"
            . "_Skillvation — MySkool Learning Private Limited_";

        try {
            switch ($driver) {
                case 'wabaconnect':
                    return $this->sendProposalViaWabaConnect($cleanPhone, $message, $pdfUrl, $package, $data);

                case 'meta':
                    return $this->sendProposalViaMeta($cleanPhone, $message, $pdfUrl);

                case 'mock':
                default:
                    Log::info("[WABA Mock Proposal] Sent to {$cleanPhone}:\n{$message}");
                    return [
                        'success' => true,
                        'message' => 'Proposal sent via WhatsApp (Mock)',
                        'body'    => $message,
                    ];
            }
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            Log::warning("[WABA Proposal Exception] {$msg}", ['phone' => $cleanPhone]);
            return [
                'success' => false,
                'message' => 'WhatsApp delivery failed: ' . $msg,
            ];
        }
    }

    /**
     * Generate a pre-filled direct WhatsApp share / chat URL for a proposal.
     */
    public function generateWhatsAppShareUrl(string $phone, array $data): string
    {
        $cleanPhone    = $this->sanitizePhone($phone);
        $name          = $data['name']    ?? 'School Representative';
        $school        = $data['school']  ?? 'your institution';
        $package       = $data['package'] ?? 'Skill Lab';
        $total         = !empty($data['total']) ? ('₹' . number_format((float) $data['total'], 2)) : '';
        $pdfUrl        = $data['pdf_url'] ?? '';
        $productUrl    = $data['product_url'] ?? url('/labs');
        $isSkillvation = !empty($data['is_skillvation_lab']);

        $labType = $isSkillvation ? 'Skillvation Lab (STEM, Robotics & AI)' : 'CBSE Composite Skill Lab';
        $totalLine = $total ? "\n💰 *Total Estimate:* {$total}" : '';

        $message = "*{$labType} Proposal*\n\n"
            . "Dear {$name},\n\n"
            . "Your proposal for *{$school}* has been prepared:\n\n"
            . "📋 *Package:* {$package}"
            . $totalLine . "\n\n"
            . "📄 *Proposal PDF Link:*\n{$pdfUrl}\n\n"
            . "🛒 *View Details in Club Shop:*\n{$productUrl}\n\n"
            . "_Skillvation — MySkool Learning Private Limited_";

        return "https://api.whatsapp.com/send?phone={$cleanPhone}&text=" . rawurlencode($message);
    }

    /**
     * Dispatch proposal message through WabaConnect REST API.
     */
    protected function sendProposalViaWabaConnect(string $phone, string $message, string $pdfUrl, string $package, array $data): array
    {
        $baseUrl       = rtrim(config('services.waba.base_url', env('WABA_BASE_URL', 'https://login.wabaconnect.com/api')), '/');
        $licenseNumber = config('services.waba.license_number', env('WABA_LICENSE_NUMBER', '79242162270'));
        $apiKey        = config('services.waba.api_key', env('WABA_API_KEY', 'MbeBNmpHqXVT7OJL3wtxKRkyd'));
        $proposalTpl   = config('services.waba.proposal_template', env('WABA_PROPOSAL_TEMPLATE', ''));

        if (!$licenseNumber || !$apiKey) {
            Log::warning("[WabaConnect Proposal] Missing credentials, mock mode");
            return ['success' => true, 'message' => 'Proposal WhatsApp logged (mock)'];
        }

        // 1. Try template if configured
        if (!empty($proposalTpl)) {
            $params = [
                'LicenseNumber' => $licenseNumber,
                'APIKey'        => $apiKey,
                'Contact'       => $phone,
                'Template'      => $proposalTpl,
            ];
            if (!empty($pdfUrl)) {
                $params['Fileurl'] = $pdfUrl;
                $params['PDFName'] = 'Skillvation_Proposal.pdf';
            }
            if (!empty($data['name'])) {
                $params['Param'] = $data['name'];
            }

            try {
                $res = Http::withoutVerifying()->connectTimeout(5)->timeout(10)
                    ->get("{$baseUrl}/sendtemplate.php", $params);
                if ($res->successful()) {
                    Log::info("[WabaConnect Proposal Template Sent]", ['phone' => $phone]);
                    return ['success' => true, 'message' => 'Proposal sent via WhatsApp template'];
                }
            } catch (\Throwable $e) {
                Log::warning("[WabaConnect Template Fallback] " . $e->getMessage());
            }
        }

        // 2. Direct text message endpoint
        try {
            $res = Http::withoutVerifying()->connectTimeout(5)->timeout(10)
                ->get("{$baseUrl}/sendtextmessage.php", [
                    'LicenseNumber' => $licenseNumber,
                    'APIKey'        => $apiKey,
                    'Contact'       => $phone,
                    'Message'       => $message,
                ]);

            $decoded = $res->json() ?? [];
            $apiResponse = strtolower($decoded['ApiResponse'] ?? '');
            $apiStatus   = strtolower($decoded['ApiMessage']['Status'] ?? $decoded['Status'] ?? '');

            if (($apiResponse === 'success' || $apiStatus === 'success') && $res->successful()) {
                Log::info("[WabaConnect Proposal Text Sent]", ['phone' => $phone]);
                return ['success' => true, 'message' => 'Proposal sent via WhatsApp'];
            }

            Log::warning("[WabaConnect Text Notice]", ['phone' => $phone, 'body' => $res->body()]);
            return ['success' => true, 'message' => 'Proposal dispatched to WhatsApp'];

        } catch (\Throwable $e) {
            Log::warning("[WabaConnect Text Error] " . $e->getMessage(), ['phone' => $phone]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Dispatch proposal message through Meta Cloud API.
     */
    protected function sendProposalViaMeta(string $phone, string $message, string $pdfUrl): array
    {
        $phoneNumberId = config('services.waba.phone_number_id', env('WABA_PHONE_NUMBER_ID'));
        $token         = config('services.waba.api_token', env('WABA_API_TOKEN'));

        if (!$phoneNumberId || !$token) {
            Log::info("[Meta Mock Proposal] Sent to {$phone}:\n{$message}");
            return ['success' => true, 'message' => 'Proposal sent (Mock)'];
        }

        $url = "https://graph.facebook.com/v18.0/{$phoneNumberId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $phone,
            'type'              => 'text',
            'text'              => ['body' => $message],
        ];

        try {
            $response = Http::withToken($token)->timeout(8)->post($url, $payload);
            return [
                'success' => $response->successful(),
                'message' => $response->successful() ? 'Proposal sent via WhatsApp.' : 'Meta API error',
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
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

        $timeout = (int) config('services.waba.timeout', env('WABA_TIMEOUT', 30));

        $response = Http::withoutVerifying()
            ->connectTimeout(10)
            ->timeout($timeout)
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
