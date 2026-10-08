<?php
/**
 * Optional update channel (disabled by default).
 *
 * The theme never contacts any server on its own. A marketplace or the seller
 * can connect an authorised update/licence service (for example the SDK of the
 * marketplace where the theme was bought) by returning a provider object from
 * the "larijani_update_provider" filter, typically from a small plugin:
 *
 *     add_filter( 'larijani_update_provider', function () {
 *         return new My_Marketplace_Provider(); // implements check( $version ): ?array
 *     } );
 *
 * check() receives the installed version and returns null (no update) or
 * array( 'new_version' => '1.4.1', 'package' => 'https://…/larijani-stone.zip', 'url' => 'https://…/changelog' ).
 * Package URLs must be HTTPS; licence keys are the provider's responsibility
 * and are never stored or transmitted by the theme itself.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registered provider, if any.
 *
 * @return object|null
 */
function larijani_update_provider() {
	$provider = apply_filters( 'larijani_update_provider', null );
	return ( is_object( $provider ) && method_exists( $provider, 'check' ) ) ? $provider : null;
}

/**
 * Inject an update offered by the provider into WordPress' theme updates.
 *
 * @param object $transient Update transient.
 * @return object
 */
function larijani_inject_theme_update( $transient ) {
	$provider = larijani_update_provider();
	if ( ! $provider || ! is_object( $transient ) ) {
		return $transient;
	}
	$info = $provider->check( LARIJANI_VERSION );
	if ( ! is_array( $info ) || empty( $info['new_version'] ) || empty( $info['package'] ) ) {
		return $transient;
	}
	if ( version_compare( $info['new_version'], LARIJANI_VERSION, '<=' ) || 'https' !== wp_parse_url( $info['package'], PHP_URL_SCHEME ) ) {
		return $transient;
	}
	$slug                          = get_template();
	$transient->response[ $slug ] = array(
		'theme'       => $slug,
		'new_version' => sanitize_text_field( $info['new_version'] ),
		'package'     => esc_url_raw( $info['package'] ),
		'url'         => isset( $info['url'] ) ? esc_url_raw( $info['url'] ) : '',
	);
	return $transient;
}
add_filter( 'pre_set_site_transient_update_themes', 'larijani_inject_theme_update' );
