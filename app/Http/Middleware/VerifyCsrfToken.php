<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'logout',
        'admin/logout',
        'tinymce-upload-image',
        'tinymce-delete-image',
        'api/shop-orders/*',
        '*auth/send-otp*',
        '*auth/verify-otp*',
        '*auth/login-otp*',
    ];
}
