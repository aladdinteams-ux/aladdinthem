<?php
/**
 * Built-in theme builder (no Elementor Pro): lets the owner replace the shared header/footer per language
 * with a saved free-Elementor template (Settings > Aladdin Payab > General).
 *
 * @package Aladdin\PayabSanatAsa
 */

namespace Aladdin\PayabSanatAsa;

defined( 'ABSPATH' ) || exit;

class Theme_Builder {

	public static function init() {
		add_filter( 'aladdin_payab_sanat_asa_location_html', array( __CLASS__, 'render' ), 10, 3 );
	}

	public static function render( $html, $location, $lang ) {
		if ( ! did_action( 'elementor/loaded' ) || ! class_exists( '\Elementor\Plugin' ) ) {
			return $html;
		}
		$o  = get_option( Settings::OPT, array() );
		$id = isset( $o[ $location . '_tpl' ][ $lang ] ) ? (int) $o[ $location . '_tpl' ][ $lang ] : 0;
		if ( ! $id || 'publish' !== get_post_status( $id ) || 'elementor_library' !== get_post_type( $id ) ) {
			return $html;
		}
		$out = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $id );
		return $out ? $out : $html;
	}
}
