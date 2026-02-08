<?php
// phpcs:ignoreFile -- Development-only CLI test runner.
$tests = [
	__DIR__ . '/test_strings.php',
	__DIR__ . '/test_options.php',
	__DIR__ . '/test_health.php'
];

foreach ($tests as $test) {
	$cmd = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($test);
	passthru($cmd, $exitCode);
	if ($exitCode !== 0) {
		exit($exitCode);
	}
}

echo "All tests passed\n";
