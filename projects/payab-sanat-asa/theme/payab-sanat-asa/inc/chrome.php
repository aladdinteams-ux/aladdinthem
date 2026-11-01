<?php
/**
 * Shared chrome (top bar, header, mega menu, mobile drawer, footer, mobile nav).
 * Markup comes from the approved HTML (template-parts/chrome-*.php); every text, label and link
 * is a row of an editable string table (defaults in inc/data/chrome-strings.json,
 * overrides in the option `aladdin_payab_sanat_asa_chrome`, edited in Settings > Aladdin Payab).
 *
 * @package PayabSanatAsa
 */

defined( 'ABSPATH' ) || exit;

/** Default string table for a language: array( 'pre' => rows, 'post' => rows ); row = array( kind, value ). */
function aladdin_payab_sanat_asa_chrome_defaults( $lang ) {
	static $all = null;
	if ( null === $all ) {
		$file = ALADDIN_PAYAB_SANAT_ASA_DIR . '/inc/data/chrome-strings.json';
		$all  = is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : array(); // phpcs:ignore WordPress.WP_FileSystem.FileGet
	}
	return isset( $all[ $lang ] ) ? $all[ $lang ] : array( 'pre' => array(), 'post' => array() );
}

/** Script strings (mega menu data, favourite labels) with overrides. */
function aladdin_payab_sanat_asa_script_strings( $lang ) {
	static $all = null;
	if ( null === $all ) {
		$file = ALADDIN_PAYAB_SANAT_ASA_DIR . '/inc/data/script-strings.json';
		$all  = is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : array(); // phpcs:ignore WordPress.WP_FileSystem.FileGet
	}
	$s   = isset( $all[ $lang ] ) ? $all[ $lang ] : array();
	$opt = get_option( 'aladdin_payab_sanat_asa_chrome', array() );
	if ( isset( $opt[ $lang ]['mega'] ) && is_array( $opt[ $lang ]['mega'] ) && isset( $s['mega'] ) ) {
		foreach ( $opt[ $lang ]['mega'] as $key => $vals ) {
			if ( isset( $s['mega'][ $key ] ) && is_array( $vals ) ) {
				foreach ( $vals as $i => $v ) {
					if ( '' !== $v && isset( $s['mega'][ $key ][ $i ] ) && 5 !== (int) $i ) {
						$s['mega'][ $key ][ $i ] = $v;
					}
				}
			}
		}
	}
	return $s;
}

/** Resolved strings for one part. */
function aladdin_payab_sanat_asa_chrome_strings( $lang, $part ) {
	$defaults = aladdin_payab_sanat_asa_chrome_defaults( $lang );
	$opt      = get_option( 'aladdin_payab_sanat_asa_chrome', array() );
	$over     = isset( $opt[ $lang ][ $part ] ) && is_array( $opt[ $lang ][ $part ] ) ? $opt[ $lang ][ $part ] : array();
	$out      = array();
	foreach ( (array) ( $defaults[ $part ] ?? array() ) as $i => $row ) {
		list( $kind, $val ) = $row;
		if ( isset( $over[ $i ] ) && '' !== $over[ $i ] ) {
			$val = $over[ $i ];
		}
		if ( 'u' === $kind && false === strpos( $val, '://' ) && false !== strpos( $val, ':' ) && ! preg_match( '#^(tel|mailto):#', $val ) ) {
			list( $l, $g ) = array_pad( explode( ':', $val, 2 ), 2, '' );
			$val            = aladdin_payab_sanat_asa_route_url( $l, $g );
		}
		$out[ $i ] = $val;
	}
	return $out;
}

/** Render a chrome part to a string. */
function aladdin_payab_sanat_asa_chrome_html( $part, $lang = '' ) {
	$lang = $lang ? $lang : aladdin_payab_sanat_asa_lang();
	$s    = aladdin_payab_sanat_asa_chrome_strings( $lang, $part );

	$aladdin_assets        = ALADDIN_PAYAB_SANAT_ASA_URI . '/assets';
	$aladdin_search_action = home_url( '/' );
	ob_start();
	include ALADDIN_PAYAB_SANAT_ASA_DIR . '/template-parts/chrome-' . $part . '.php';
	$html = (string) ob_get_clean();

	$is_home = is_front_page() || ( is_page() && '' === (string) get_post_meta( get_queried_object_id(), '_aladdin_payab_group', true ) );
	$home    = aladdin_payab_sanat_asa_route_url( $lang, '' );
	// In-page anchors that only exist on the home page point to the language home elsewhere.
	if ( ! $is_home ) {
		$html = preg_replace( '/href="#(?!content")/', 'href="' . esc_url( $home ) . '#', $html );
	}
	// Language switchers point to the translation of the current page.
	$html = preg_replace_callback(
		'/<a\b([^>]*?)\blang="(fa|en|ar)"([^>]*)>/',
		static function ( $m ) use ( $lang ) {
			$full = $m[0];
			$url  = aladdin_payab_sanat_asa_translation_url( $m[2] );
			$full = preg_replace( '/href="[^"]*"/', 'href="' . esc_url( $url ) . '"', $full, 1 );
			if ( $m[2] === $lang ) {
				$full = preg_replace( '/<a\b/', '<a aria-current="page"', $full, 1 );
			}
			return $full;
		},
		$html
	);
	// Current page state in the menus.
	$current = trailingslashit( aladdin_payab_sanat_asa_translation_url( $lang ) );
	$html    = preg_replace_callback(
		'/<a\b([^>]*)\bhref="([^"#]*)"([^>]*)>/',
		static function ( $m ) use ( $current ) {
			if ( false !== strpos( $m[0], 'aria-current' ) || false !== strpos( $m[0], 'lang="' ) ) {
				return $m[0];
			}
			if ( trailingslashit( $m[2] ) !== $current ) {
				return $m[0];
			}
			$tag = '<a aria-current="page"' . $m[1] . 'href="' . $m[2] . '"' . $m[3] . '>';
			if ( false !== strpos( $m[0], 'class=' ) ) {
				return $tag;
			}
			return str_replace( '<a ', '<a class="current" ', $tag );
		},
		$html
	);
	// Search forms keep the language.
	if ( 'fa' !== $lang ) {
		$html = preg_replace( '/(<form\b[^>]*role="search"[^>]*>)/', '$1<input type="hidden" name="lang" value="' . esc_attr( $lang ) . '"/>', $html );
	}
	return apply_filters( 'aladdin_payab_sanat_asa_chrome_html', $html, $part, $lang );
}

/**
 * Print a chrome part, or the Elementor template the site owner assigned to this location
 * (theme builder without Elementor Pro: option set by the companion plugin).
 */
function aladdin_payab_sanat_asa_chrome( $part ) {
	$location = 'pre' === $part ? 'header' : 'footer';
	$custom   = apply_filters( 'aladdin_payab_sanat_asa_location_html', '', $location, aladdin_payab_sanat_asa_lang() );
	if ( '' !== $custom ) {
		echo $custom; // phpcs:ignore WordPress.Security.EscapeOutput -- Elementor output.
		return;
	}
	echo aladdin_payab_sanat_asa_chrome_html( $part ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside the template parts.
}
