<?php
// Composer hook: runs Laravel's post-install steps only when the command-line PHP can run Laravel.
// Hostinger's Git deploy runs Composer with an older command-line PHP (7.4) than the website (8.x).
// On older PHP we skip the steps and keep the committed package list (bootstrap/cache), which matches
// composer.lock. Keep this file PHP 7.x compatible.
//   php deploy/composer-hook.php autoload   (post-autoload-dump)
//   php deploy/composer-hook.php update     (post-update-cmd)

if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }

$root = dirname(__DIR__);
$step = isset($argv[1]) ? $argv[1] : '';

$clear = function (array $files) use ($root) {
    foreach ($files as $file) {
        $path = $root . '/bootstrap/cache/' . $file;
        if (is_file($path)) @unlink($path);
    }
};
// A cached config would hide .env changes made with this deploy.
$clear(array('config.php'));

if (PHP_VERSION_ID < 80100) {
    // packages.php and services.php are kept: they are committed and match composer.lock.
    fwrite(STDERR, 'Command-line PHP ' . PHP_VERSION . ' is older than 8.1: skipped "' . $step . '" Laravel steps; the committed package list is used.' . PHP_EOL);
    exit(0);
}
// Same files Illuminate\Foundation\ComposerScripts::postAutoloadDump clears; package:discover rebuilds them.
$clear(array('services.php', 'packages.php'));

$commands = array(
    'autoload' => 'package:discover --ansi',
    'update' => 'vendor:publish --tag=laravel-assets --ansi --force',
);
if (!isset($commands[$step])) {
    fwrite(STDERR, 'Unknown step "' . $step . '"' . PHP_EOL);
    exit(1);
}
passthru(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($root . '/artisan') . ' ' . $commands[$step], $code);
exit($code);
