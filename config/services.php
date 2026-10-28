<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'waba' => [
        'driver'         => env('WABA_DRIVER', 'wabaconnect'),
        'base_url'       => env('WABA_BASE_URL', 'https://login.wabaconnect.com/api'),
        'license_number' => env('WABA_LICENSE_NUMBER', '79242162270'),
        'api_key'        => env('WABA_API_KEY', 'MbeBNmpHqXVT7OJL3wtxKRkyd'),
        'template_name'  => env('WABA_TEMPLATE_NAME', ''),
    ],

    // "Design My Lab" external session API
    'labdesign' => [
        'base_url' => env('LABDESIGN_API_BASE', 'https://kalpana.skillvation.com'),
        'api_key'  => env('LABDESIGN_API_KEY', ''),
    ],

    'roboquest'=>['platform_key'=>env('ROBOQUEST_PLATFORM_KEY','')],
];
