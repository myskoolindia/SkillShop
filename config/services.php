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
        'driver'                 => env('WABA_DRIVER', 'wabaconnect'),
        'base_url'               => env('WABA_BASE_URL', 'https://login.wabaconnect.com/api'),
        'license_number'         => env('WABA_LICENSE_NUMBER', '79242162270'),
        'api_key'                => env('WABA_API_KEY', 'MbeBNmpHqXVT7OJL3wtxKRkyd'),
        'proposal_template'      => env('WABA_PROPOSAL_TEMPLATE', 'sv_skill_lab_proposal'),
        'proposal_template_lang' => env('WABA_PROPOSAL_TEMPLATE_LANG', 'en'),
        'pdf_base_url'           => env('PROPOSAL_PDF_BASE_URL', 'https://devpro.skillvation.com'),
        'template_name'          => env('WABA_TEMPLATE_NAME', ''),
        'timeout'                => (int) env('WABA_TIMEOUT', 30),
    ],

    // "Design My Lab" external session API
    'labdesign' => [
        'base_url' => env('LABDESIGN_API_BASE', 'https://kalpana.skillvation.com'),
        'api_key'  => env('LABDESIGN_API_KEY', ''),
    ],

    'roboquest'=>['platform_key'=>env('ROBOQUEST_PLATFORM_KEY','')],
    // Mapper API: Club Shop lab product -> provider courses. Paid lab orders unlock the mapped courses in RoboQuest.
    'collab'=>[
        'product_course_map_url'=>env('COLLAB_PRODUCT_COURSE_MAP_URL','https://collab.myskool.club/api/product-course-map'),
        'lab_course_access'=>env('LAB_COURSE_ACCESS_ENABLED',true),
    ],
];
