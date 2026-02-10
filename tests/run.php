<?php
// phpcs:ignoreFile -- Development-only CLI test runner.
if (!defined('ABSPATH') && PHP_SAPI === 'cli') {
	define('ABSPATH', dirname(__DIR__) . '/');
}

if (!defined('ABSPATH')) {
	exit;
}

$tests = [
	__DIR__ . '/test_strings.php',
	__DIR__ . '/test_options.php',
	__DIR__ . '/test_admin_headers.php',
	__DIR__ . '/test_health.php',
	__DIR__ . '/test_updater.php'
];

foreach ($tests as $test) {
	$cmd = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($test);
	passthru($cmd, $exitCode);
	if ($exitCode !== 0) {
		exit($exitCode);
	}
}

echo "All tests passed\n";
