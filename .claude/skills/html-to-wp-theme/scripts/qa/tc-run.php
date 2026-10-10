<?php
// Run Theme Check (WordPress.org's official checker) against a theme, print results as text.
$_SERVER['HTTP_HOST'] = $argv[2] ?? 'localhost:8085';
define( 'LS_TEST_USER', 1 );
require $argv[1] . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
define( 'TC_PLUGIN_DIR', $argv[3] . '/' );
require_once $argv[3] . '/checkbase.php';
$slug  = $argv[4] ?? 'larijani-stone';
$theme = wp_get_theme( $slug );
run_themechecks_against_theme( $theme, $slug );
global $themechecks;
$out = array();
foreach ( $themechecks as $check ) {
	if ( $check instanceof themecheck ) {
		foreach ( (array) $check->getError() as $e ) {
			$out[] = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $e ) ) );
		}
	}
}
$out = array_unique( $out );
sort( $out );
echo implode( "\n", $out ), "\n", 'TOTAL ', count( $out ), "\n";
