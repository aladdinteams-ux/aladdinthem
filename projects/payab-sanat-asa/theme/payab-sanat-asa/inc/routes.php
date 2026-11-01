<?php
/**
 * Language + route helpers. The companion plugin overrides them through filters;
 * these fallbacks keep the theme usable without it.
 *
 * @package PayabSanatAsa
 */

defined( 'ABSPATH' ) || exit;

/** Supported languages. */
function aladdin_payab_sanat_asa_languages() {
	return apply_filters(
		'aladdin_payab_sanat_asa_languages',
		array(
			'fa' => array( 'label' => 'فارسی', 'dir' => 'rtl', 'locale' => 'fa_IR' ),
			'en' => array( 'label' => 'English', 'dir' => 'ltr', 'locale' => 'en_US' ),
			'ar' => array( 'label' => 'العربية', 'dir' => 'rtl', 'locale' => 'ar' ),
		)
	);
}

/** Current language code (fa|en|ar). */
function aladdin_payab_sanat_asa_lang() {
	$langs = aladdin_payab_sanat_asa_languages();
	$lang  = apply_filters( 'aladdin_payab_sanat_asa_current_lang', '' );
	if ( $lang && isset( $langs[ $lang ] ) ) {
		return $lang;
	}
	$path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
	$home = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	$rel  = trim( (string) substr( (string) $path, strlen( (string) $home ) ), '/' );
	$seg  = explode( '/', $rel );
	if ( isset( $seg[0] ) && 'fa' !== $seg[0] && isset( $langs[ $seg[0] ] ) ) {
		return $seg[0];
	}
	return 'fa';
}

/** URL of a route (group) in a language, e.g. ( 'en', 'shop-2' ). */
function aladdin_payab_sanat_asa_route_url( $lang, $group = '' ) {
	$group = trim( (string) $group, '/' );
	$url   = apply_filters( 'aladdin_payab_sanat_asa_route_url', '', $lang, $group );
	if ( $url ) {
		return $url;
	}
	$path = ( 'fa' === $lang ? '' : $lang . '/' ) . ( '' === $group ? '' : $group . '/' );
	return home_url( '/' . $path );
}

/** URL of the translation of the current page in $lang. */
function aladdin_payab_sanat_asa_translation_url( $lang ) {
	$url = apply_filters( 'aladdin_payab_sanat_asa_translation_url', '', $lang );
	return $url ? $url : aladdin_payab_sanat_asa_route_url( $lang, '' );
}

/** Replace {{theme}} / {{route:lang:group}} / {{search}} tokens used in stored content. */
function aladdin_payab_sanat_asa_resolve_tokens( $html ) {
	if ( false === strpos( $html, '{{' ) ) {
		return $html;
	}
	$html = str_replace( '{{theme}}', untrailingslashit( ALADDIN_PAYAB_SANAT_ASA_URI ), $html );
	$html = str_replace( '{{search}}', esc_url( home_url( '/' ) ), $html );
	return preg_replace_callback(
		'/\{\{route:(fa|en|ar):([^}]*)\}\}/',
		static function ( $m ) {
			return esc_url( aladdin_payab_sanat_asa_route_url( $m[1], $m[2] ) );
		},
		$html
	);
}
