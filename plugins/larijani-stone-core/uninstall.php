<?php
/**
 * Uninstall. Content is kept unless the administrator explicitly enabled
 * "delete data on uninstall". Projects are never deleted.
 *
 * @package Larijani_Core
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb;

$larijani_core_purge = (bool) get_option( 'larijani_core_delete_on_uninstall' );

if ( $larijani_core_purge ) {
	$larijani_core_ids = get_posts(
		array(
			'post_type'      => 'ls_lead',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);
	foreach ( $larijani_core_ids as $larijani_core_id ) {
		wp_delete_post( $larijani_core_id, true );
	}
	$larijani_core_name = get_option( 'larijani_core_private_dirname' );
	$larijani_core_up   = wp_upload_dir( null, false );
	if ( $larijani_core_name && preg_match( '/^larijani-private-[a-z0-9]{24}$/', $larijani_core_name ) && empty( $larijani_core_up['error'] ) ) {
		$larijani_core_dir = $larijani_core_up['basedir'] . '/' . $larijani_core_name;
		if ( is_dir( $larijani_core_dir ) ) {
			$larijani_core_it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $larijani_core_dir, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );
			foreach ( $larijani_core_it as $larijani_core_f ) {
				$larijani_core_f->isDir() ? rmdir( $larijani_core_f->getPathname() ) : wp_delete_file( $larijani_core_f->getPathname() ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_rmdir
			}
			rmdir( $larijani_core_dir ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_rmdir
		}
		delete_option( 'larijani_core_private_dirname' );
	}
	delete_option( 'larijani_core_leads_email' );
}

// Settings and temporary rows are always removed.
delete_option( 'larijani_core_delete_on_uninstall' );
delete_option( 'larijani_core_allow_svg' );
delete_option( 'larijani_core_needs_migration' );
$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $wpdb->esc_like( 'larijani_tok_' ) . '%' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
wp_clear_scheduled_hook( 'larijani_core_cleanup' );
