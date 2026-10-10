<?php
$_SERVER['HTTP_HOST'] = 'localhost:8083';
define( 'LS_TEST_USER', 1 );
require __DIR__ . '/wpel/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
foreach ( array_slice( $argv, 1 ) as $p ) { $r = activate_plugin( $p ); echo $p, ': ', is_wp_error( $r ) ? $r->get_error_message() : 'ok', "\n"; }
echo 'ELEMENTOR_VERSION=', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : 'n/a', "\n";
