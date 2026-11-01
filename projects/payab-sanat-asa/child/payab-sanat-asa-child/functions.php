<?php
/**
 * Child theme bootstrap.
 *
 * @package PayabSanatAsaChild
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_enqueue_style(
			'aladdin-payab-sanat-asa-child',
			get_stylesheet_uri(),
			array( 'aladdin-payab-sanat-asa-elementor-bridge' ),
			wp_get_theme()->get( 'Version' )
		);
	},
	30
);
