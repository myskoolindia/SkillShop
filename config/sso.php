<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Single Sign-On (SSO) Shared Secret Key
    |--------------------------------------------------------------------------
    |
    | Used to sign HMAC tokens between Laravel (Skillvation LMS) and
    | CodeIgniter 4 (Club Shop).
    |
    */
    'secret_key' => env('SSO_SECRET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Token Expiry Time (seconds)
    |--------------------------------------------------------------------------
    |
    | Prevents replay attacks by ensuring tokens are used within 120 seconds.
    |
    */
    'token_ttl' => (int) env('SSO_TOKEN_TTL', 120),

    /*
    |--------------------------------------------------------------------------
    | Club Shop Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL where Club Shop is hosted.
    |
    */
    'shop_url' => env('CLUB_SHOP_URL', rtrim(env('APP_URL', 'http://localhost'), '/') . '/club-shop'),
];
