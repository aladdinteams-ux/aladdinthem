<?php
/**
 * Render mode of the imported design pages.
 *  - "html" (default): the public page prints the approved design markup exactly (identical to the delivered HTML);
 *    Elementor is still used to edit, but saved Elementor edits show on the site only in "elementor" mode.
 *  - "elementor": the public page prints the Elementor output (edits made in Elementor appear on the site).
 *
 * @package Aladdin\PayabSanatAsa
 */

namespace Aladdin\PayabSanatAsa;

defined( 'ABSPATH' ) || exit;

class Render_Mode {

	/** @var array|null lang:group => html */
	private static $design = null;

	public static function init() {
		add_filter( 'the_content', array( __CLASS__, 'content' ), 98 );
	}

	public static function mode() {
		$o = get_option( Settings::OPT, array() );
		return isset( $o['render_mode'] ) && 'elementor' === $o['render_mode'] ? 'elementor' : 'html';
	}

	private static function design_html( $lang, $group ) {
		if ( null === self::$design ) {
			self::$design = array();
			$file         = ALADDIN_PAYAB_SANAT_ASA_CORE_DIR . 'data/pages.json';
			$data         = is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : array(); // phpcs:ignore WordPress.WP_FileSystem.FileGet
			foreach ( (array) $data as $p ) {
				self::$design[ $p['lang'] . ':' . $p['group'] ] = $p['fallback'];
			}
		}
		return isset( self::$design[ $lang . ':' . $group ] ) ? self::$design[ $lang . ':' . $group ] : '';
	}

	public static function content( $content ) {
		if ( 'html' !== self::mode() || ! in_the_loop() || ! is_singular() ) {
			return $content;
		}
		if ( isset( $_GET['elementor-preview'] ) || ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->preview ) && \Elementor\Plugin::$instance->preview->is_preview_mode() ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return $content;
		}
		$id = get_the_ID();
		if ( ! metadata_exists( 'post', $id, Languages::META_GROUP ) || get_post_meta( $id, '_aladdin_payab_use_elementor', true ) ) {
			return $content;
		}
		$html = self::design_html( (string) get_post_meta( $id, Languages::META_LANG, true ), (string) get_post_meta( $id, Languages::META_GROUP, true ) );
		return '' !== $html ? $html : $content;
	}
}
