<?php
/**
 * Uninstall cleanup. Only technical state (scan progress, locks, one-off flags) is removed.
 * Settings, edited section content, created pages and templates belong to the site owner and are kept.
 */
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;
foreach ( array( 'frpme_legacy_scan_state', 'frpme_legacy_scan_version', 'frpme_css_version', 'frpme_legacy_scan_lock', 'frpme_v3_bootstrap_lock', 'frpme_v3_setup_notice', 'frpme_v3_bootstrap_report' ) as $frpme_option ) {
	delete_option( $frpme_option );
}
delete_transient( 'frpme_v3_auto_setup_pause' );
wp_clear_scheduled_hook( 'frpme_legacy_scan_event' );
