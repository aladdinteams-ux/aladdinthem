<?php
/**
 * CLI: export every demo layout as an Elementor template JSON file
 * (Templates › Saved Templates › Import).
 *
 * Usage: php build/export-templates.php
 *
 * @package Larijani
 */

if ( 'cli' !== PHP_SAPI ) {
	exit;
}

define( 'ABSPATH', __DIR__ );
define( 'LS_URI', 'https://example.com/wp-content/themes/larijani-stone' );

/** Minimal WordPress shims for the standalone exporter. */
function get_option( $k, $d = false ) { return $d; } // phpcs:ignore
function wp_rand( $a = 0, $b = 0 ) { return mt_rand(); } // phpcs:ignore
function wp_json_encode( $d, $f = 0 ) { return json_encode( $d, $f ); } // phpcs:ignore
function wp_slash( $v ) { return $v; } // phpcs:ignore
function ls_bi( $name ) { return array( 'value' => 'bi bi-' . $name, 'library' => 'bootstrap-icons' ); } // phpcs:ignore

$root = dirname( __DIR__ );
require $root . '/inc/demo/images.php';
require $root . '/inc/demo/content.php';
require $root . '/inc/demo/pages.php';

// Only the pure helpers are needed from the importer.
$src = file_get_contents( $root . '/inc/demo/importer.php' );
foreach ( array( 'ls_el_id', 'ls_el_normalize', 'ls_el_widget', 'ls_el_build' ) as $fn ) {
	if ( preg_match( '/function ' . $fn . '\(.*?\n}\n/s', $src, $m ) ) {
		eval( $m[0] ); // phpcs:ignore Squiz.PHP.Eval.Discouraged
	}
}

$out_dir = $root . '/elementor-templates';
if ( ! is_dir( $out_dir ) ) {
	mkdir( $out_dir );
}
$types = array(
	'page'            => 'page',
	'header'          => 'header',
	'footer'          => 'footer',
	'single-post'     => 'single-post',
	'archive'         => 'archive',
	'product'         => 'product',
	'product-archive' => 'product-archive',
	'error-404'       => 'error-404',
);
foreach ( ls_demo_layouts() as $key => $layout ) {
	$json = array(
		'version'       => '0.4',
		'title'         => $layout['title'],
		'type'          => $types[ $layout['kind'] ] ?? 'page',
		'content'       => ls_el_build( $layout['rows'] ),
		'page_settings' => array( 'hide_title' => 'yes' ),
	);
	file_put_contents( $out_dir . '/larijani-' . $key . '.json', json_encode( $json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) );
	echo 'exported ', $key, "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI output, not HTML.
}
