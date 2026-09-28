<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class SsoTokenService
{
    /**
     * Generate an SSO URL with signed token to seamlessly log in to Club Shop.
     */
    public static function generateShopSsoUrl(User $user, ?string $target = null): string
    {
        $secretKey = config('sso.secret_key');
        $shopUrl = rtrim(config('sso.shop_url', url('/club-shop')), '/');

        $nameParts = explode(' ', trim($user->name ?? ''), 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        $payload = [
            'uid'        => (string) $user->id,
            'email'      => (string) $user->email,
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'phone'      => (string) ($user->phone ?? ''),
            'role'       => (string) ($user->role ?? 'student'),
            'target'     => $target ?: '',
            'iat'        => time(),
            'nonce'      => Str::random(16),
        ];

        $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $payloadEncoded = self::base64UrlEncode($payloadJson);
        $signature = hash_hmac('sha256', $payloadEncoded, $secretKey);

        $token = $payloadEncoded . '.' . $signature;

        return $shopUrl . '/sso-login?token=' . urlencode($token);
    }

    /**
     * Base64 URL safe encoder
     */
    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
