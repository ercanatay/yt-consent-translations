<?php
// phpcs:ignoreFile -- Development-only CLI assertions.
require_once __DIR__ . '/bootstrap.php';
require_once dirname(__DIR__) . '/includes/class-updater.php';

$failures = [];

$settings = YTCT_Updater::sanitize_settings([
	'enabled' => false,
	'channel' => 'beta',
	'check_interval' => 'hourly'
]);

if ($settings['enabled'] !== false) {
	$failures[] = 'sanitize_settings should keep explicit enabled=false.';
}

if ($settings['channel'] !== 'stable') {
	$failures[] = 'sanitize_settings should force stable channel.';
}

if ($settings['check_interval'] !== 'twicedaily') {
	$failures[] = 'sanitize_settings should force twicedaily interval.';
}

if (YTCT_Updater::normalize_tag_version('v1.2.3') !== '1.2.3') {
	$failures[] = 'normalize_tag_version should strip v prefix.';
}

if (YTCT_Updater::normalize_tag_version('release-1.2.3') !== '') {
	$failures[] = 'normalize_tag_version should reject non-semver tags.';
}

if (!YTCT_Updater::is_newer_version('9.9.9')) {
	$failures[] = 'is_newer_version should return true for higher versions.';
}

if (YTCT_Updater::is_newer_version(YTCT_VERSION)) {
	$failures[] = 'is_newer_version should return false for equal version.';
}

$GLOBALS['ytct_remote_get_mock'] = new WP_Error('http_error', 'GitHub unreachable');
YTCT_Updater::check_for_updates(true);
$error_state = YTCT_Updater::get_state();
if ($error_state['status'] !== 'error') {
	$failures[] = 'check_for_updates should persist error status on HTTP errors.';
}

if ($error_state['last_error'] === '') {
	$failures[] = 'check_for_updates should persist last_error on HTTP errors.';
}

$release_payload = [
	'tag_name' => 'v9.9.9',
	'draft' => false,
	'prerelease' => false,
	'zipball_url' => 'https://api.github.com/repos/ercanatay/yt-consent-translations/zipball/v9.9.9',
	'html_url' => 'https://github.com/ercanatay/yt-consent-translations/releases/tag/v9.9.9',
	'body' => 'Test release body',
	'published_at' => '2026-02-09T00:00:00Z'
];

$GLOBALS['ytct_remote_get_mock'] = [
	'response' => ['code' => 200],
	'body' => json_encode($release_payload)
];

$checked = YTCT_Updater::check_for_updates(true);
if (empty($checked['updateAvailable'])) {
	$failures[] = 'check_for_updates should mark updateAvailable when remote version is newer.';
}

if ($checked['latestVersion'] !== '9.9.9') {
	$failures[] = 'check_for_updates should persist latestVersion from release tag.';
}

$GLOBALS['ytct_scheduled_events'] = [];
YTCT_Updater::update_settings(['enabled' => true]);
if (wp_next_scheduled(YTCT_Updater::CRON_HOOK) === false) {
	$failures[] = 'sync_schedule should schedule cron event when updater is enabled.';
}

YTCT_Updater::update_settings(['enabled' => false]);
if (wp_next_scheduled(YTCT_Updater::CRON_HOOK) !== false) {
	$failures[] = 'sync_schedule should clear cron event when updater is disabled.';
}

if (!empty($failures)) {
	fwrite(STDERR, "test_updater failed:\n- " . implode("\n- ", $failures) . "\n");
	exit(1);
}

echo "test_updater passed\n";
