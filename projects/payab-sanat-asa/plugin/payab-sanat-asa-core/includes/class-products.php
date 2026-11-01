<?php
/**
 * Products: WooCommerce catalog mode (no price, no cart) and /en/product/… /ar/product/… URLs.
 * Without WooCommerce the importer creates product pages instead, so nothing here is required for display.
 *
 * @package Aladdin\PayabSanatAsa
 */

namespace Aladdin\PayabSanatAsa;

defined( 'ABSPATH' ) || exit;

class Products {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'rewrites' ) );
		add_filter( 'post_type_link', array( __CLASS__, 'permalink' ), 10, 2 );
		if ( self::catalog_mode() ) {
			add_filter( 'woocommerce_is_purchasable', '__return_false' );
			add_filter( 'woocommerce_get_price_html', '__return_empty_string' );
			add_filter( 'woocommerce_variable_sale_price_html', '__return_empty_string' );
			add_filter( 'woocommerce_variable_price_html', '__return_empty_string' );
			add_action( 'template_redirect', array( __CLASS__, 'block_cart_checkout' ), 5 );
		}
	}

	public static function catalog_mode() {
		$o = get_option( 'aladdin_payab_sanat_asa_settings', array() );
		return ! isset( $o['catalog_mode'] ) || 'no' !== $o['catalog_mode'];
	}

	public static function rewrites() {
		if ( ! post_type_exists( 'product' ) ) {
			return;
		}
		add_rewrite_rule( '^(en|ar)/product/([^/]+)/?$', 'index.php?post_type=product&product=$matches[2]-$matches[1]', 'top' );
	}

	/** Non-Persian products carry a language suffix in their slug; the public URL does not. */
	public static function permalink( $link, $post ) {
		if ( 'product' !== $post->post_type ) {
			return $link;
		}
		$lang = get_post_meta( $post->ID, Languages::META_LANG, true );
		if ( in_array( $lang, array( 'en', 'ar' ), true ) && preg_match( '/^(.*)-(en|ar)$/', $post->post_name, $m ) ) {
			return home_url( '/' . $lang . '/product/' . $m[1] . '/' );
		}
		return $link;
	}

	public static function block_cart_checkout() {
		if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() ) ) {
			wp_safe_redirect( home_url( '/' ), 302 );
			exit;
		}
	}
}
