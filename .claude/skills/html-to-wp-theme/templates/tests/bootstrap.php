<?php
/**
 * PHPUnit bootstrap: loads a real WordPress install that has the theme active
 * and the Larijani Stone Core plugin activated.
 * Usage: LARIJANI_WP_LOAD=/path/to/wp-load.php LARIJANI_WP_HOST=localhost:8083 phpunit -c tests/phpunit.xml.dist
 */
$larijani_load = getenv( 'LARIJANI_WP_LOAD' );
if ( ! $larijani_load || ! file_exists( $larijani_load ) ) {
	fwrite( STDERR, "Set LARIJANI_WP_LOAD to a wp-load.php path.\n" );
	exit( 1 );
}
$_SERVER['HTTP_HOST']   = getenv( 'LARIJANI_WP_HOST' ) ? getenv( 'LARIJANI_WP_HOST' ) : 'localhost';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
require $larijani_load;
require_once ABSPATH . 'wp-admin/includes/file.php'; // wp_tempnam() for fixtures.
