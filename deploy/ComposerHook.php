<?php
// Composer hooks that run Laravel's post-install steps only when the server allows it.
// They run inside Composer's own PHP process (class callbacks in composer.json), because Hostinger's PHP has
// proc_open disabled and Composer cannot start a separate "@php …" script there.
// Hostinger's Git deploy may also run Composer with an older command-line PHP (7.4) than the website (8.x): then the
// committed package list (bootstrap/cache/packages.php, services.php), which matches composer.lock, is kept.
// Keep this file PHP 7.x compatible.

namespace Deploy;

class ComposerHook
{
    /** post-autoload-dump */
    public static function autoload($event = null)
    {
        self::run('autoload', 'package:discover --ansi');
    }

    /** post-update-cmd */
    public static function update($event = null)
    {
        self::run('update', 'vendor:publish --tag=laravel-assets --ansi --force');
    }

    public static function run($step, $command)
    {
        $root = dirname(__DIR__);
        // A cached config would hide .env changes made with this deploy.
        self::clear($root, array('config.php'));
        if (PHP_VERSION_ID < 80100) {
            self::say('Command-line PHP ' . PHP_VERSION . ' is older than 8.1: skipped "' . $step . '" Laravel steps; the committed package list is used.');
            return;
        }
        $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));
        if (!function_exists('passthru') || in_array('passthru', $disabled, true)) {
            self::say('This server cannot run commands from Composer: skipped "' . $step . '" Laravel steps; the committed package list is used.');
            return;
        }
        // Same files Illuminate\Foundation\ComposerScripts::postAutoloadDump clears; package:discover rebuilds them.
        self::clear($root, array('services.php', 'packages.php'));
        passthru(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($root . '/artisan') . ' ' . $command, $code);
        if ($code !== 0) {
            throw new \RuntimeException('"php artisan ' . $command . '" failed with exit code ' . $code);
        }
    }

    private static function clear($root, array $files)
    {
        foreach ($files as $file) {
            $path = $root . '/bootstrap/cache/' . $file;
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }

    private static function say($message)
    {
        fwrite(STDERR, $message . PHP_EOL);
    }
}
