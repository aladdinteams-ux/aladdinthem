<?php
/**
 * Larijani Stone child theme.
 *
 * The parent theme's styles are enqueued by the parent itself; this file only
 * loads the child stylesheet after them. Add your own hooks below, e.g.
 * add_filter( 'ls_auto_setup_on_activation', '__return_false' );
 *
 * @package Larijani_Child
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_enqueue_style(
			'larijani-child-style',
			get_stylesheet_uri(),
			array( 'larijani-tailwind' ),
			wp_get_theme()->get( 'Version' )
		);
	},
	20
);
