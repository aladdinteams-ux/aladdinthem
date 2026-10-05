<?php
/**
 * Versioned, ordered, idempotent data migrations.
 *
 * Runs once per version change in the admin (never on front-end requests).
 * The stored version is updated only after every pending step succeeded.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Migration steps: target version => callback. Each callback must be safe to re-run.
 *
 * @return array
 */
function ls_migrations() {
	return apply_filters(
		'ls_migrations',
		array(
			'1.2.0' => 'ls_migrate_1_2_0',
		)
	);
}

/**
 * 1.2.0: give theme-created sample content a stable ownership key.
 *
 * @return bool
 */
function ls_migrate_1_2_0() {
	$ids = get_posts(
		array(
			'post_type'      => array( 'post', 'ls_project', 'product' ),
			'post_status'    => 'any',
			'posts_per_page' => 500,
			'fields'         => 'ids',
			'meta_key'       => '_ls_demo_image', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		)
	);
	foreach ( $ids as $id ) {
		if ( get_post_meta( $id, '_ls_demo_key', true ) ) {
			continue;
		}
		$prefix = 'post' === get_post_type( $id ) ? 'post' : ( 'product' === get_post_type( $id ) ? 'product' : 'project' );
		update_post_meta( $id, '_ls_demo_key', $prefix . ':' . get_post_meta( $id, '_ls_demo_image', true ) );
	}
	return true;
}

/**
 * Run pending migrations.
 */
function ls_maybe_migrate() {
	$from = (string) get_option( 'ls_db_version', '0' );
	if ( version_compare( $from, LS_VERSION, '>=' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$steps = ls_migrations();
	uksort( $steps, 'version_compare' );
	foreach ( $steps as $version => $callback ) {
		if ( version_compare( $from, $version, '<' ) && version_compare( $version, LS_VERSION, '<=' ) && is_callable( $callback ) ) {
			if ( false === call_user_func( $callback ) ) {
				return; // Retry on the next admin request; keep the old version.
			}
			update_option( 'ls_db_version', $version, true );
		}
	}
	update_option( 'ls_db_version', LS_VERSION, true );
}
add_action( 'admin_init', 'ls_maybe_migrate', 1 );
