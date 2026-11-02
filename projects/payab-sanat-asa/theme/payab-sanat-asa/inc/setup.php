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
		register_nav_menus( aladdin_payab_sanat_asa_menu_locations() );
		$GLOBALS['content_width'] = 1240;
	}
);

/** <html lang dir> follow the page language, not the site locale. */
add_filter(
	'language_attributes',
	static function ( $output ) {
		if ( is_admin() ) {
			return $output;       // the dashboard keeps the user's own language and direction.
		}
		$lang  = aladdin_payab_sanat_asa_lang();
		$langs = aladdin_payab_sanat_asa_languages();
		return 'lang="' . esc_attr( $lang ) . '" dir="' . esc_attr( $langs[ $lang ]['dir'] ) . '"';
	},
	20,
	1
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

/** Class of <main>: the approved design styles every inner page through `.ip-page` (home pages have none). */
function aladdin_payab_sanat_asa_main_class() {
	$class = 'ip-page';
	if ( is_singular() && metadata_exists( 'post', get_queried_object_id(), '_aladdin_payab_group' ) ) {
		$class = '' === (string) get_post_meta( get_queried_object_id(), '_aladdin_payab_group', true ) ? '' : 'ip-page';
	} elseif ( is_front_page() ) {
		$class = '';
	}
	return (string) apply_filters( 'aladdin_payab_sanat_asa_main_class', $class );
}

/** Menu locations: header navigation and the two footer link lists, for each language. */
function aladdin_payab_sanat_asa_menu_locations() {
	$names = array( 'fa' => 'فارسی', 'en' => 'English', 'ar' => 'العربية' );
	$out   = array();
	foreach ( $names as $l => $n ) {
		$out[ 'payab-primary-' . $l ] = sprintf( 'Header menu — %s', $n );
		$out[ 'payab-footer1-' . $l ] = sprintf( 'Footer: quick links — %s', $n );
		$out[ 'payab-footer2-' . $l ] = sprintf( 'Footer: useful information — %s', $n );
	}
	return $out;
}
