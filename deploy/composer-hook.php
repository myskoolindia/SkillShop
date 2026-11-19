<?php
// Runs the Laravel post-install steps by hand: php deploy/composer-hook.php autoload|update
// Composer itself calls Deploy\ComposerHook directly (see composer.json), with no separate process.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/ComposerHook.php';
$step = isset($argv[1]) ? $argv[1] : '';
if (!in_array($step, array('autoload', 'update'), true)) { fwrite(STDERR, 'Unknown step "' . $step . '"' . PHP_EOL); exit(1); }
\Deploy\ComposerHook::$step();
