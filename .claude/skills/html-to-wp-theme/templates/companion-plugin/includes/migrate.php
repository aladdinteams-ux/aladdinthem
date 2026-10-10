<?php
/**
 * Move lead attachments saved by theme 1.3.x (public media library files)
 * into private storage. Runs only when an administrator presses the button;
 * the public copy is removed only after the private copy is verified by hash.
 *
 * @package Larijani_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Leads that still reference public attachments.
 *
 * @param int $limit Limit.
 * @return int[]
 */
function larijani_core_legacy_leads( $limit = 50 ) {
	return get_posts(
		array(
			'post_type'      => 'ls_lead',
			'post_status'    => 'any',
			'posts_per_page' => $limit,
			'fields'         => 'ids',
			'meta_key'       => '_ls_lead_files', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		)
	);
}

/**
 * Number of legacy public attachments.
 *
 * @return int
 */
function larijani_core_legacy_file_count() {
	$count = 0;
	foreach ( larijani_core_legacy_leads( 500 ) as $id ) {
		$count += count( array_filter( (array) get_post_meta( $id, '_ls_lead_files', true ) ) );
	}
	return $count;
}

/**
 * Migrate one lead.
 *
 * @param int $lead_id Lead.
 * @return array( moved, failed )
 */
function larijani_core_migrate_lead_files( $lead_id ) {
	$moved   = 0;
	$failed  = 0;
	$left    = array();
	$records = larijani_core_lead_files( $lead_id );
	$rules   = larijani_core_upload_rules();
	foreach ( array_filter( (array) get_post_meta( $lead_id, '_ls_lead_files', true ) ) as $att_id ) {
		$path = get_attached_file( $att_id );
		$ext  = $path ? strtolower( pathinfo( $path, PATHINFO_EXTENSION ) ) : '';
		if ( ! $path || ! is_file( $path ) || ! isset( $rules[ $ext ] ) ) {
			if ( ! get_post( $att_id ) ) {
				continue; // Already gone: nothing to protect.
			}
			$left[] = $att_id;
			++$failed;
			continue;
		}
		$hash = hash_file( 'sha256', $path );
		$rec  = larijani_core_store_file( $path, get_the_title( $att_id ) . '.' . $ext, $ext, $lead_id, false );
		if ( ! $rec || ! hash_equals( $hash, $rec['sha256'] ) ) {
			if ( $rec ) {
				larijani_core_delete_files( array( $rec ) );
			}
			$left[] = $att_id;
			++$failed;
			continue;
		}
		$records[] = $rec;
		update_post_meta( $lead_id, '_ls_lead_private_files', $records );
		wp_delete_attachment( $att_id, true );
		++$moved;
	}
	if ( $left ) {
		update_post_meta( $lead_id, '_ls_lead_files', $left );
	} else {
		delete_post_meta( $lead_id, '_ls_lead_files' );
	}
	return array( $moved, $failed );
}

/**
 * Admin action: migrate in batches.
 */
function larijani_core_migrate_files_action() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'دسترسی غیرمجاز.', 'larijani-stone-core' ), '', array( 'response' => 403 ) );
	}
	check_admin_referer( 'larijani_core_migrate_files' );
	$moved  = 0;
	$failed = 0;
	foreach ( larijani_core_legacy_leads( 50 ) as $id ) {
		list( $m, $f ) = larijani_core_migrate_lead_files( $id );
		$moved        += $m;
		$failed       += $f;
	}
	wp_safe_redirect(
		add_query_arg(
			array(
				'post_type' => 'ls_lead',
				'page'      => 'larijani-core',
				'migrated'  => $moved,
				'failed'    => $failed,
			),
			admin_url( 'edit.php' )
		)
	);
	exit;
}
add_action( 'admin_post_larijani_core_migrate_files', 'larijani_core_migrate_files_action' );

/**
 * Tell administrators when public legacy files exist.
 */
function larijani_core_legacy_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! get_option( 'larijani_core_needs_migration' ) ) {
		return;
	}
	$count = larijani_core_legacy_file_count();
	if ( ! $count ) {
		delete_option( 'larijani_core_needs_migration' );
		return;
	}
	printf(
		'<div class="notice notice-warning"><p>%1$s <a href="%2$s">%3$s</a></p></div>',
		/* translators: %d: number of files */
		esc_html( sprintf( __( 'لاریجانی استون: %d فایل پیوست مشتری از نسخه قبلی هنوز لینک عمومی دارد.', 'larijani-stone-core' ), $count ) ),
		esc_url( admin_url( 'edit.php?post_type=ls_lead&page=larijani-core' ) ),
		esc_html__( 'انتقال امن به فضای خصوصی', 'larijani-stone-core' )
	);
}
add_action( 'admin_notices', 'larijani_core_legacy_notice' );
