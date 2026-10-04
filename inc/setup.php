<?php
/**
 * Theme supports, menus, sidebars, image sizes.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme setup.
 */
function ls_setup() {
	load_theme_textdomain( 'larijani', LS_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 120,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// WooCommerce.
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 600,
			'single_image_width'    => 900,
			'product_grid'          => array(
				'default_columns' => 3,
				'default_rows'    => 4,
				'min_columns'     => 1,
				'max_columns'     => 4,
			),
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	add_editor_style( array( 'assets/css/tailwind.css', 'assets/css/editor.css' ) );

	register_nav_menus(
		array(
			'primary'           => __( 'منوی اصلی (هدر)', 'larijani' ),
			'drawer_categories' => __( 'دسته‌بندی‌های منوی موبایل', 'larijani' ),
			'footer_quick'      => __( 'فوتر – دسترسی سریع', 'larijani' ),
			'footer_categories' => __( 'فوتر – دسته‌بندی تجهیزات', 'larijani' ),
			'footer_bottom'     => __( 'فوتر – لینک‌های پایین', 'larijani' ),
		)
	);

	add_image_size( 'ls-card', 640, 480, true );
	add_image_size( 'ls-wide', 1400, 600, true );
	add_image_size( 'ls-thumb', 160, 160, true );
}
add_action( 'after_setup_theme', 'ls_setup' );

/**
 * Content width.
 */
function ls_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'ls_content_width', 1280 );
}
add_action( 'after_setup_theme', 'ls_content_width', 0 );

/**
 * Widget areas.
 */
function ls_widgets_init() {
	$common = array(
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	);
	register_sidebar(
		array_merge(
			$common,
			array(
				'name'        => __( 'سایدبار وبلاگ', 'larijani' ),
				'id'          => 'blog-sidebar',
				'description' => __( 'در آرشیو و نوشته‌ها، زیر ابزارک‌های پیش‌فرض قالب نمایش داده می‌شود.', 'larijani' ),
			)
		)
	);
	register_sidebar(
		array_merge(
			$common,
			array(
				'name'        => __( 'سایدبار فروشگاه', 'larijani' ),
				'id'          => 'shop-sidebar',
				'description' => __( 'فیلترهای ووکامرس (قیمت، ویژگی‌ها و …) را اینجا قرار دهید.', 'larijani' ),
			)
		)
	);
}
add_action( 'widgets_init', 'ls_widgets_init' );

/**
 * Body classes.
 *
 * @param array $classes Classes.
 * @return array
 */
function ls_body_classes( $classes ) {
	$classes[] = 'ls-theme';
	if ( ls_opt( 'header_sticky' ) ) {
		$classes[] = 'ls-has-sticky-header';
	}
	return $classes;
}
add_filter( 'body_class', 'ls_body_classes' );

/**
 * Persian excerpt "more".
 *
 * @return string
 */
function ls_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'ls_excerpt_more' );

/**
 * Excerpt length.
 *
 * @return int
 */
function ls_excerpt_length() {
	return 28;
}
add_filter( 'excerpt_length', 'ls_excerpt_length' );

/**
 * Count single post views (simple, cache friendly enough for small sites).
 */
function ls_track_views() {
	if ( ! is_singular( array( 'post', 'ls_project' ) ) || is_preview() || is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
		return;
	}
	$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
	if ( preg_match( '/bot|crawl|spider|slurp|preview/i', $ua ) ) {
		return;
	}
	$id = get_queried_object_id();
	update_post_meta( $id, 'ls_views', (int) get_post_meta( $id, 'ls_views', true ) + 1 );
}
add_action( 'template_redirect', 'ls_track_views' );

/**
 * Allow SVG logo uploads for administrators only.
 *
 * @param array $mimes Mimes.
 * @return array
 */
function ls_upload_mimes( $mimes ) {
	if ( current_user_can( 'manage_options' ) ) {
		$mimes['svg'] = 'image/svg+xml';
	}
	return $mimes;
}
add_filter( 'upload_mimes', 'ls_upload_mimes' );

/**
 * Show an admin notice recommending Elementor.
 */
function ls_admin_notice_plugins() {
	if ( ls_has_elementor() || ! current_user_can( 'install_plugins' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'appearance_page_ls-setup' === $screen->id ) {
		return;
	}
	printf(
		'<div class="notice notice-info is-dismissible"><p>%s <a href="%s">%s</a></p></div>',
		esc_html__( 'قالب لاریجانی استون برای ویرایش بصری صفحات به افزونه رایگان المنتور نیاز دارد.', 'larijani' ),
		esc_url( admin_url( 'plugin-install.php?s=elementor&tab=search&type=term' ) ),
		esc_html__( 'نصب المنتور', 'larijani' )
	);
}
add_action( 'admin_notices', 'ls_admin_notice_plugins' );

/**
 * Force right-to-left direction (the design is Persian-first). Can be disabled in
 * Customizer › تنظیمات قالب لاریجانی › برند و رنگ‌ها.
 */
function ls_force_rtl() {
	global $wp_locale;
	if ( ls_opt( 'force_rtl' ) && $wp_locale && ! is_admin() ) {
		$wp_locale->text_direction = 'rtl';
	}
}
add_action( 'wp_loaded', 'ls_force_rtl' );
add_action( 'elementor/preview/init', 'ls_force_rtl' );

/**
 * Persian lang attribute when the site language has no translation installed.
 *
 * @param string $output Attributes.
 * @return string
 */
function ls_language_attributes( $output ) {
	if ( ls_opt( 'force_rtl' ) && false === strpos( $output, 'dir=' ) ) {
		$output = 'dir="rtl" ' . $output;
	}
	return $output;
}
add_filter( 'language_attributes', 'ls_language_attributes' );
