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
function larijani_setup() {
	load_theme_textdomain( 'larijani-stone', LARIJANI_DIR . '/languages' );

	// Page excerpts double as the meta description when no SEO plugin is active.
	add_post_type_support( 'page', 'excerpt' );

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
			'primary'           => __( 'منوی اصلی (هدر)', 'larijani-stone' ),
			'drawer_categories' => __( 'دسته‌بندی‌های منوی موبایل', 'larijani-stone' ),
			'footer_quick'      => __( 'فوتر – دسترسی سریع', 'larijani-stone' ),
			'footer_categories' => __( 'فوتر – دسته‌بندی تجهیزات', 'larijani-stone' ),
			'footer_bottom'     => __( 'فوتر – لینک‌های پایین', 'larijani-stone' ),
		)
	);

	add_image_size( 'ls-card', 640, 480, true );
	add_image_size( 'ls-wide', 1400, 600, true );
	add_image_size( 'ls-thumb', 160, 160, true );
}
add_action( 'after_setup_theme', 'larijani_setup' );

/**
 * Content width.
 */
function larijani_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'ls_content_width', 1280 );
}
add_action( 'after_setup_theme', 'larijani_content_width', 0 );

/**
 * Widget areas.
 */
function larijani_widgets_init() {
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
				'name'        => __( 'سایدبار وبلاگ', 'larijani-stone' ),
				'id'          => 'blog-sidebar',
				'description' => __( 'در آرشیو و نوشته‌ها، زیر ابزارک‌های پیش‌فرض قالب نمایش داده می‌شود.', 'larijani-stone' ),
			)
		)
	);
	register_sidebar(
		array_merge(
			$common,
			array(
				'name'        => __( 'سایدبار فروشگاه', 'larijani-stone' ),
				'id'          => 'shop-sidebar',
				'description' => __( 'فیلترهای ووکامرس (قیمت، ویژگی‌ها و …) را اینجا قرار دهید.', 'larijani-stone' ),
			)
		)
	);
}
add_action( 'widgets_init', 'larijani_widgets_init' );

/**
 * Body classes.
 *
 * @param array $classes Classes.
 * @return array
 */
function larijani_body_classes( $classes ) {
	$classes[] = 'ls-theme';
	if ( larijani_opt( 'header_sticky' ) ) {
		$classes[] = 'ls-has-sticky-header';
	}
	return $classes;
}
add_filter( 'body_class', 'larijani_body_classes' );

/**
 * Persian excerpt "more".
 *
 * @return string
 */
function larijani_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'larijani_excerpt_more' );

/**
 * Excerpt length.
 *
 * @return int
 */
function larijani_excerpt_length() {
	return 28;
}
add_filter( 'excerpt_length', 'larijani_excerpt_length' );

/**
 * Count single post views (simple, cache friendly enough for small sites).
 */
function larijani_track_views() {
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
add_action( 'template_redirect', 'larijani_track_views' );

/**
 * Show an admin notice recommending Elementor.
 */
function larijani_admin_notice_plugins() {
	if ( larijani_has_elementor() || ! current_user_can( 'install_plugins' ) ) {
		return;
	}
	if ( isset( $_GET['page'] ) && in_array( $_GET['page'], array( 'ls-setup', 'ls-settings' ), true ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	printf(
		'<div class="notice notice-info is-dismissible"><p>%s <a href="%s">%s</a></p></div>',
		esc_html__( 'برگه‌های قالب لاریجانی استون ساخته شده‌اند و نمایش داده می‌شوند؛ برای ویرایش بصری آن‌ها افزونه رایگان المنتور را نصب کنید.', 'larijani-stone' ),
		esc_url( admin_url( 'plugin-install.php?s=elementor&tab=search&type=term' ) ),
		esc_html__( 'نصب المنتور', 'larijani-stone' )
	);
}
add_action( 'admin_notices', 'larijani_admin_notice_plugins' );

/**
 * Force right-to-left direction (the design is Persian-first). Can be disabled in
 * Customizer › تنظیمات قالب لاریجانی › برند و رنگ‌ها.
 */
function larijani_force_rtl() {
	global $wp_locale;
	if ( larijani_opt( 'force_rtl' ) && $wp_locale instanceof WP_Locale && ! is_admin() ) {
		$wp_locale->text_direction = 'rtl';
	}
}
// Must run before "init": block styles (e.g. WooCommerce Cart/Checkout) pick
// their -rtl.css variant when they are registered, based on is_rtl().
add_action( 'after_setup_theme', 'larijani_force_rtl', 1 );
add_action( 'change_locale', 'larijani_force_rtl' );
add_action( 'elementor/preview/init', 'larijani_force_rtl' );

/**
 * With forced RTL the content is Persian even when the site language is not
 * (e.g. English with no fa_IR pack): declare dir="rtl" and lang="fa-IR" so
 * screen readers, hyphenation and search engines treat the text correctly.
 * Sites whose locale is already an RTL language keep their own lang value.
 *
 * @param string $output Attributes.
 * @return string
 */
function larijani_language_attributes( $output ) {
	if ( ! larijani_opt( 'force_rtl' ) || is_admin() ) {
		return $output;
	}
	if ( false === strpos( $output, 'dir=' ) ) {
		$output = 'dir="rtl" ' . $output;
	}
	$rtl_locale = (bool) preg_match( '/^(fa|ar|he|ur|ckb|ps|ug|azb|haz|sd|dv|yi)(_|$)/', determine_locale() );
	$lang       = (string) apply_filters( 'ls_content_lang', $rtl_locale ? '' : 'fa-IR' );
	if ( '' !== $lang ) {
		$output = preg_replace( '/lang="[^"]*"/', 'lang="' . esc_attr( $lang ) . '"', $output, 1, $count );
		if ( ! $count ) {
			$output .= ' lang="' . esc_attr( $lang ) . '"';
		}
	}
	return $output;
}
add_filter( 'language_attributes', 'larijani_language_attributes' );
