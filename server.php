<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @author   Taylor Otwell <taylor@laravel.com>
 */
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH)
);

// If installed in a subdirectory (e.g. /skillvation.comphp), strip the base folder prefix
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$relativeUri = $uri;
if (!empty($basePath) && strpos($uri, $basePath) === 0) {
    $relativeUri = substr($uri, strlen($basePath));
}

if ($relativeUri !== '' && $relativeUri !== '/' && is_file(__DIR__.'/public'.$relativeUri)) {
    $filePath = __DIR__.'/public'.$relativeUri;
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $mimes = [
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'svg'   => 'image/svg+xml',
        'webp'  => 'image/webp',
        'ico'   => 'image/x-icon',
        'css'   => 'text/css',
        'js'    => 'application/javascript',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'eot'   => 'application/vnd.ms-fontobject',
        'pdf'   => 'application/pdf',
        'json'  => 'application/json'
    ];
    $mime = $mimes[$ext] ?? mime_content_type($filePath);
    if ($mime) {
        header("Content-Type: " . $mime);
    }
    header("Content-Length: " . filesize($filePath));
    readfile($filePath);
    exit;
}

require_once __DIR__.'/public/index.php';

