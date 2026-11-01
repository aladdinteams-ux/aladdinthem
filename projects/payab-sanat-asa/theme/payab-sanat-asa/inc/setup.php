<?php
/**
 * Theme setup.
 *
 * @package PayabSanatAsa
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	static function () {
		load_theme_textdomain( 'payab-sanat-asa', ALADDIN_PAYAB_SANAT_ASA_DIR . '/languages' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'custom-logo', array( 'height' => 96, 'width' => 108, 'flex-height' => true, 'flex-width' => true ) );
		add_theme_support( 'woocommerce' );
		add_theme_support( 'editor-styles' );
		register_nav_menus(
			array(
				'primary' => __( 'Primary (optional; header uses the Texts panel by default)', 'payab-sanat-asa' ),
				'footer'  => __( 'Footer (optional)', 'payab-sanat-asa' ),
			)
		);
		$GLOBALS['content_width'] = 1240;
	}
);

/** <html lang dir> follow the page language, not the site locale. */
add_filter(
	'language_attributes',
	static function () {
		$lang  = aladdin_payab_sanat_asa_lang();
		$langs = aladdin_payab_sanat_asa_languages();
		return 'lang="' . esc_attr( $lang ) . '" dir="' . esc_attr( $langs[ $lang ]['dir'] ) . '"';
	},
	20
);

/** Search results: keep to the current language when the plugin supports it. */
add_action(
	'pre_get_posts',
	static function ( $q ) {
		if ( ! is_admin() && $q->is_main_query() && $q->is_search() ) {
			$q->set( 'posts_per_page', 12 );
		}
	}
);

/** Body classes used by the approved CSS. */
add_filter(
	'body_class',
	static function ( $classes ) {
		$classes[] = 'aladdin-payab-sanat-asa';
		$classes[] = 'lang-' . aladdin_payab_sanat_asa_lang();
		return $classes;
	}
);
