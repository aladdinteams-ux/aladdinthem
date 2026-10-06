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
			'1.3.0' => 'ls_migrate_1_3_0',
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
 * 1.3.0: layouts are native Elementor containers/widgets now. Pages created by
 * an older version are left untouched; flag them so the admin is told about
 * the opt-in rebuild (Larijani Stone › Setup › rebuild).
 *
 * @return bool
 */
function ls_migrate_1_3_0() {
	$ids = get_posts(
		array(
			'post_type'      => array( 'page', 'elementor_library' ),
			'post_status'    => 'any',
			'posts_per_page' => 100,
			'fields'         => 'ids',
			'meta_key'       => '_ls_demo_page', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		)
	);
	foreach ( $ids as $id ) {
		$data = get_post_meta( $id, '_elementor_data', true );
		if ( is_string( $data ) && false !== strpos( $data, '"elType":"section"' ) ) {
			update_option( 'ls_native_rebuild_notice', 1, false );
			break;
		}
	}
	return true;
}

/**
 * One-time notice about rebuilding older demo pages with native widgets.
 */
function ls_native_rebuild_notice() {
	if ( ! get_option( 'ls_native_rebuild_notice' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_GET['ls_dismiss_native'] ) && check_admin_referer( 'ls_dismiss_native' ) ) {
		delete_option( 'ls_native_rebuild_notice' );
		return;
	}
	$setup   = admin_url( 'admin.php?page=ls-setup' );
	$dismiss = wp_nonce_url( add_query_arg( 'ls_dismiss_native', 1 ), 'ls_dismiss_native' );
	echo '<div class="notice notice-info"><p>' . esc_html__( 'لاریجانی استون ۱.۳: بخش‌های ثابت صفحات اکنون با ویجت‌های بومی المنتور (عنوان، متن، دکمه، آیکون، تصویر…) ساخته می‌شوند. برگه‌های فعلی شما تغییر نکرده‌اند؛ برای به‌روزرسانی آن‌ها گزینه «بازسازی چیدمان» را در صفحه راه‌اندازی بزنید (نسخه قبلی پشتیبان‌گیری می‌شود).', 'larijani' ) . ' <a href="' . esc_url( $setup ) . '">' . esc_html__( 'راه‌اندازی و درون‌ریزی', 'larijani' ) . '</a> · <a href="' . esc_url( $dismiss ) . '">' . esc_html__( 'بستن', 'larijani' ) . '</a></p></div>';
}
add_action( 'admin_notices', 'ls_native_rebuild_notice' );

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
