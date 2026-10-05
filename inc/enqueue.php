<?php
/**
 * Styles & scripts.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Asset version based on file modification time (cache busting during development).
 *
 * @param string $rel Relative path.
 * @return string
 */
function ls_asset_ver( $rel ) {
	$file = LS_DIR . '/' . ltrim( $rel, '/' );
	return file_exists( $file ) ? LS_VERSION . '.' . filemtime( $file ) : LS_VERSION;
}

/**
 * Register assets (so Elementor widgets can declare them as dependencies).
 */
function ls_register_assets() {
	wp_register_style( 'larijani-bootstrap-icons', LS_URI . '/assets/vendor/bootstrap-icons/bootstrap-icons.min.css', array(), '1.11.3' );
	wp_register_style( 'larijani-tailwind', LS_URI . '/assets/css/tailwind.css', array( 'larijani-bootstrap-icons' ), ls_asset_ver( 'assets/css/tailwind.css' ) );
	wp_register_style( 'larijani-style', LS_URI . '/style.css', array( 'larijani-tailwind' ), LS_VERSION );

	$js = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || ! file_exists( LS_DIR . '/assets/js/theme.min.js' ) ? 'assets/js/theme.js' : 'assets/js/theme.min.js';
	wp_register_script( 'larijani-theme', LS_URI . '/' . $js, array(), ls_asset_ver( $js ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_localize_script(
		'larijani-theme',
		'LarijaniTheme',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'i18n'    => array(
				'sending'  => __( 'در حال ارسال…', 'larijani' ),
				'error'    => __( 'خطایی رخ داد. لطفاً دوباره تلاش کنید یا تماس بگیرید.', 'larijani' ),
				'required' => __( 'لطفاً فیلدهای ستاره‌دار را تکمیل کنید.', 'larijani' ),
				'copied'   => __( 'پیوند کپی شد!', 'larijani' ),
				'projects' => __( 'پروژه منتخب', 'larijani' ),
				'kg'       => __( 'کیلوگرم', 'larijani' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'ls_register_assets', 5 );
add_action( 'elementor/frontend/before_register_styles', 'ls_register_assets' );

/**
 * Enqueue front-end assets.
 */
function ls_enqueue_assets() {
	wp_enqueue_style( 'larijani-tailwind' );
	wp_enqueue_style( 'larijani-style' );
	wp_add_inline_style( 'larijani-tailwind', ls_customizer_css() );
	wp_enqueue_script( 'larijani-theme' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'ls_enqueue_assets', 20 );

/**
 * Preload the main font for faster Persian text rendering.
 */
function ls_preload_font() {
	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( LS_URI . '/assets/fonts/vazirmatn/Vazirmatn-Variable.woff2' )
	);
}
add_action( 'wp_head', 'ls_preload_font', 1 );

/**
 * Icons inside the Elementor editor panel (icon picker preview).
 */
function ls_editor_styles() {
	wp_enqueue_style( 'larijani-bootstrap-icons', LS_URI . '/assets/vendor/bootstrap-icons/bootstrap-icons.min.css', array(), '1.11.3' );
	wp_enqueue_style( 'larijani-editor-panel', LS_URI . '/assets/css/elementor-editor.css', array(), ls_asset_ver( 'assets/css/elementor-editor.css' ) );
}
add_action( 'elementor/editor/after_enqueue_styles', 'ls_editor_styles' );

/**
 * Block editor (Gutenberg) styles.
 */
function ls_block_editor_assets() {
	wp_enqueue_style( 'larijani-bootstrap-icons', LS_URI . '/assets/vendor/bootstrap-icons/bootstrap-icons.min.css', array(), '1.11.3' );
}
add_action( 'enqueue_block_editor_assets', 'ls_block_editor_assets' );

/**
 * Pages composed with Elementor (or the theme's fallback renderer) contain no
 * blocks: skip the block-library / classic-theme block styles there.
 */
function ls_dequeue_unused_block_styles() {
	if ( ! is_singular() || ! apply_filters( 'ls_dequeue_block_styles', true ) ) {
		return;
	}
	$id = get_queried_object_id();
	if ( 'builder' !== get_post_meta( $id, '_elementor_edit_mode', true ) || has_blocks( get_post_field( 'post_content', $id ) ) ) {
		return;
	}
	foreach ( array( 'wp-block-library', 'wp-block-library-theme', 'classic-theme-styles', 'global-styles' ) as $handle ) {
		wp_dequeue_style( $handle );
	}
}
add_action( 'wp_enqueue_scripts', 'ls_dequeue_unused_block_styles', 100 );
