<?php
/**
 * Technical SEO / structured data that the theme owns only when no SEO
 * plugin is active (Yoast, Rank Math, SEOPress, AIOSEO, The SEO Framework …).
 *
 * Ownership map: see docs/SCHEMA-ENTITY-MAP.md.
 * - Title: WordPress core (`title-tag`) or the SEO plugin.
 * - Meta description / canonical / sitemaps: WordPress core or the SEO plugin
 *   (the theme never prints them).
 * - Organization JSON-LD: theme, only without an SEO plugin (filterable).
 * - FAQPage JSON-LD: FAQ widget, only without an SEO plugin (filterable).
 * - Search results: noindex,follow without an SEO plugin.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is a dedicated SEO plugin active?
 *
 * @return bool
 */
function larijani_seo_plugin_active() {
	$active = defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' )
		|| defined( 'AIOSEO_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' ) || class_exists( 'Slim_SEO\\Plugin' );
	return (bool) apply_filters( 'ls_seo_plugin_active', $active );
}

/**
 * Should the theme print a given structured-data type?
 *
 * @param string $type organization|faq.
 * @return bool
 */
function larijani_theme_schema_enabled( $type ) {
	return (bool) apply_filters( 'ls_theme_schema_enabled', ! larijani_seo_plugin_active(), $type );
}

/**
 * Print a JSON-LD block safely (HTML-significant characters are escaped so
 * user text can never close the script element).
 *
 * @param array $data Data.
 */
function larijani_print_json_ld( $data ) {
	$json = wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );
	if ( $json ) {
		echo '<script type="application/ld+json">' . $json . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON with HTML characters hex-escaped.
	}
}

/**
 * Organization entity from the theme settings (the same data the header,
 * footer and contact page display). Printed on the front page only.
 */
function larijani_organization_schema() {
	if ( ! is_front_page() || ! larijani_theme_schema_enabled( 'organization' ) ) {
		return;
	}
	$phones = array_filter( array( larijani_opt( 'phone_1' ), larijani_opt( 'phone_2' ) ) );
	$same   = array_values( array_filter( array( larijani_opt( 'instagram' ), larijani_opt( 'telegram' ), larijani_opt( 'linkedin' ), larijani_opt( 'aparat' ), larijani_opt( 'eitaa' ) ) ) );
	$logo   = get_theme_mod( 'custom_logo' ) ? wp_get_attachment_image_url( (int) get_theme_mod( 'custom_logo' ), 'full' ) : '';
	$org    = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Organization',
		'@id'         => home_url( '/#organization' ),
		'name'        => larijani_opt( 'brand_name' ) ? larijani_opt( 'brand_name' ) : get_bloginfo( 'name' ),
		'url'         => home_url( '/' ),
		'description' => wp_strip_all_tags( (string) larijani_opt( 'footer_about' ) ),
	);
	if ( $logo ) {
		$org['logo'] = $logo;
	}
	if ( larijani_opt( 'email' ) ) {
		$org['email'] = sanitize_email( larijani_opt( 'email' ) );
	}
	if ( $phones ) {
		$org['contactPoint'] = array();
		foreach ( $phones as $i => $ph ) {
			$org['contactPoint'][] = array(
				'@type'       => 'ContactPoint',
				'telephone'   => '+98' . ltrim( preg_replace( '/\D+/', '', larijani_en_num( $ph ) ), '0' ),
				'contactType' => 0 === $i ? 'technical support' : 'sales',
				'areaServed'  => 'IR',
			);
		}
	}
	if ( larijani_opt( 'address' ) ) {
		$org['address'] = array(
			'@type'          => 'PostalAddress',
			'streetAddress'  => wp_strip_all_tags( (string) larijani_opt( 'address' ) ),
			'addressCountry' => 'IR',
		);
	}
	if ( $same ) {
		$org['sameAs'] = $same;
	}
	larijani_print_json_ld( apply_filters( 'ls_organization_schema', $org ) );
}
add_action( 'wp_head', 'larijani_organization_schema', 30 );

/**
 * Keep internal search result pages out of the index (core adds nothing here).
 *
 * @param array $robots Robots directives.
 * @return array
 */
function larijani_search_robots( $robots ) {
	if ( is_search() && ! larijani_seo_plugin_active() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	return $robots;
}
add_filter( 'wp_robots', 'larijani_search_robots' );

/**
 * Basic meta description when no SEO plugin is active (plugins own it otherwise).
 * Source: excerpt/content on singular views, term description on archives, tagline on
 * the front page; falls back to the tagline and then the footer "about" text
 * (builder pages keep their content in Elementor data, not post_content).
 */
function larijani_meta_description() {
	if ( larijani_seo_plugin_active() || ! apply_filters( 'ls_meta_description_enabled', true ) || is_search() || is_404() ) {
		return;
	}
	$desc = '';
	if ( is_front_page() || is_home() ) {
		$desc = get_bloginfo( 'description' );
		if ( is_home() && ! is_front_page() ) {
			$blog = get_option( 'page_for_posts' );
			$desc = $blog && has_excerpt( $blog ) ? get_the_excerpt( $blog ) : $desc;
		}
	} elseif ( is_singular() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post && ! post_password_required( $post ) ) {
			$desc = has_excerpt( $post ) ? $post->post_excerpt : wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
		}
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$desc = term_description();
	}
	$desc = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( (string) $desc ) ) );
	if ( '' === $desc && function_exists( 'is_shop' ) && is_shop() ) {
		$shop = (int) wc_get_page_id( 'shop' );
		$desc = $shop > 0 && has_excerpt( $shop ) ? get_the_excerpt( $shop ) : '';
		$desc = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( (string) $desc ) ) );
	}
	if ( '' === $desc && ! is_paged() && ( is_front_page() || is_home() || is_page() || is_post_type_archive() ) ) {
		$desc = trim( (string) get_bloginfo( 'description' ) );
		$desc = '' !== $desc ? $desc : trim( (string) larijani_opt( 'footer_about' ) );
	}
	$desc = (string) apply_filters( 'ls_meta_description', wp_html_excerpt( $desc, 160, '…' ) );
	if ( '' !== $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'larijani_meta_description', 1 );
