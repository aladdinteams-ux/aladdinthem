<?php
/**
 * SEO output: title, description, canonical, hreflang, Open Graph, stored JSON-LD.
 * Stays silent when a known SEO plugin is active or the option is switched off.
 *
 * @package Aladdin\PayabSanatAsa
 */

namespace Aladdin\PayabSanatAsa;

defined( 'ABSPATH' ) || exit;

class Seo {

	public static function init() {
		add_filter( 'document_title_parts', array( __CLASS__, 'title_parts' ), 20 );
		add_action( 'wp_head', array( __CLASS__, 'head' ), 3 );
		// Core prints its own canonical; ours is language-aware, so only one may stay.
		add_action( 'wp', static function () {
			if ( self::active() ) {
				remove_action( 'wp_head', 'rel_canonical' );
			}
		} );
	}

	private static function active() {
		if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' ) || defined( 'AIOSEO_VERSION' ) ) {
			return false;
		}
		$o = get_option( 'aladdin_payab_sanat_asa_settings', array() );
		return ! isset( $o['seo'] ) || 'no' !== $o['seo'];
	}

	public static function title_parts( $parts ) {
		if ( ! self::active() || ! is_singular() ) {
			return $parts;
		}
		$t = get_post_meta( get_queried_object_id(), '_aladdin_payab_seo_title', true );
		if ( $t ) {
			return array( 'title' => $t );
		}
		return $parts;
	}

	public static function head() {
		if ( ! self::active() ) {
			return;
		}
		$lang = Languages::current_lang( '' );
		$locs = array( 'fa' => 'fa_IR', 'en' => 'en_US', 'ar' => 'ar' );
		if ( is_singular() ) {
			$id   = get_queried_object_id();
			$desc = get_post_meta( $id, '_aladdin_payab_seo_description', true );
			if ( $desc ) {
				printf( '<meta name="description" content="%s"/>' . "\n", esc_attr( $desc ) );
			}
			printf( '<link rel="canonical" href="%s"/>' . "\n", esc_url( get_permalink( $id ) ) );
			$group = get_post_meta( $id, Languages::META_GROUP, true );
			if ( metadata_exists( 'post', $id, Languages::META_GROUP ) ) {
				foreach ( Languages::codes() as $l ) {
					$pid = Languages::post_id( $l, (string) $group );
					if ( $pid ) {
						printf( '<link rel="alternate" hreflang="%s" href="%s"/>' . "\n", esc_attr( $l ), esc_url( get_permalink( $pid ) ) );
					}
				}
				$fa = Languages::post_id( 'fa', (string) $group );
				if ( $fa ) {
					printf( '<link rel="alternate" hreflang="x-default" href="%s"/>' . "\n", esc_url( get_permalink( $fa ) ) );
				}
			}
			printf( '<meta property="og:locale" content="%s"/>' . "\n", esc_attr( $locs[ $lang ] ) );
			printf( '<meta property="og:type" content="website"/>' . "\n" );
			printf( '<meta property="og:title" content="%s"/>' . "\n", esc_attr( wp_get_document_title() ) );
			if ( $desc ) {
				printf( '<meta property="og:description" content="%s"/>' . "\n", esc_attr( $desc ) );
			}
			$ld = get_post_meta( $id, '_aladdin_payab_jsonld', true );
			if ( $ld ) {
				foreach ( (array) json_decode( $ld, true ) as $block ) {
					echo '<script type="application/ld+json">' . wp_json_encode( $block, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput
				}
			}
		}
	}
}
