<?php
// phpcs:ignoreFile -- Development-only CLI assertions.
if (!defined('ABSPATH') && PHP_SAPI === 'cli') {
	define('ABSPATH', dirname(__DIR__) . '/');
}

if (!defined('ABSPATH')) {
	exit;
}

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

if ($settings['channel'] !== 'wordpress') {
	$failures[] = 'sanitize_settings should force wordpress channel.';
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

$GLOBALS['ytct_site_transient_store']['update_plugins'] = (object) [];
$checked = YTCT_Updater::check_for_updates(false);
if (!empty($checked['updateAvailable'])) {
	$failures[] = 'check_for_updates should not mark update available when metadata is missing.';
}

if ($checked['status'] !== 'up_to_date') {
	$failures[] = 'check_for_updates should default status to up_to_date when metadata is missing.';
}

$update_transient = new stdClass();
$update_transient->response = [
	YTCT_PLUGIN_BASENAME => (object) [
		'new_version' => '9.9.9'
	]
];
$GLOBALS['ytct_site_transient_store']['update_plugins'] = $update_transient;

$checked = YTCT_Updater::check_for_updates(false);
if (empty($checked['updateAvailable'])) {
	$failures[] = 'check_for_updates should mark updateAvailable when metadata version is newer.';
}

if ($checked['latestVersion'] !== '9.9.9') {
	$failures[] = 'check_for_updates should persist latestVersion from update metadata.';
}

if ($checked['status'] !== 'update_available') {
	$failures[] = 'check_for_updates should set status update_available when a newer version exists.';
}

$up_to_date_transient = new stdClass();
$up_to_date_transient->no_update = [
	YTCT_PLUGIN_BASENAME => (object) [
		'new_version' => YTCT_VERSION
	]
];
$GLOBALS['ytct_site_transient_store']['update_plugins'] = $up_to_date_transient;

$checked = YTCT_Updater::check_for_updates(false);
if ($checked['status'] !== 'up_to_date') {
	$failures[] = 'check_for_updates should set status up_to_date when no update is available.';
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
