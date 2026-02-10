<?php
// phpcs:ignoreFile -- Development-only CLI assertions.
if (!defined('ABSPATH') && PHP_SAPI === 'cli') {
	define('ABSPATH', dirname(__DIR__) . '/');
}

if (!defined('ABSPATH')) {
	exit;
}

require_once __DIR__ . '/bootstrap.php';
require_once dirname(__DIR__) . '/includes/class-health.php';

$failures = [];

YTCT_Health::reset_report();

YTCT_Health::record_unmatched('Random unrelated text');
$report = YTCT_Health::get_report();
if ((int) $report['unmatched_count'] !== 0) {
	$failures[] = 'Unrelated text should not be counted as consent mismatch.';
}

YTCT_Health::record_unmatched('Manage cookie settings');
$report = YTCT_Health::get_report();
if ((int) $report['unmatched_count'] < 1) {
	$failures[] = 'Consent-related unmatched text should increment unmatched_count.';
}

YTCT_Health::record_match();
$report = YTCT_Health::get_report();
if ((int) $report['matched_count'] < 1) {
	$failures[] = 'record_match should increment matched_count.';
}

$summary = YTCT_Health::build_summary(true);
if (!isset($summary['status'])) {
	$failures[] = 'Health summary should include status.';
}

YTCT_Health::persist();
if (!isset($GLOBALS['ytct_option_store'][YTCT_Health::OPTION_NAME])) {
	$failures[] = 'Persist should write report option.';
}

if (!empty($failures)) {
	fwrite(STDERR, "test_health failed:\n- " . implode("\n- ", $failures) . "\n");
	exit(1);
}

echo "test_health passed\n";
