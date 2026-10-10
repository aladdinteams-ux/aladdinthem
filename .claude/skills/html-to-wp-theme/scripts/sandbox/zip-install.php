<?php
$_SERVER['HTTP_HOST'] = 'localhost:8085';
define( 'LS_TEST_USER', 1 );
require __DIR__ . '/wpz/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/theme.php';
WP_Filesystem();
foreach ( array( 'larijani-stone' => __DIR__ . '/pkg/larijani-stone.zip', 'larijani-stone-child' => __DIR__ . '/pkg/larijani-stone-child.zip' ) as $slug => $zip ) {
	$u = new Theme_Upgrader( new Automatic_Upgrader_Skin() );
	$r = $u->install( $zip, array( 'overwrite_package' => true ) );
	$t = wp_get_theme( $slug );
	echo $slug, ': install=', is_wp_error( $r ) ? $r->get_error_message() : var_export( $r, true ), ' exists=', $t->exists() ? 1 : 0, ' errors=', $t->errors() ? $t->errors()->get_error_message() : 'none', ' version=', $t->get( 'Version' ), ' template=', $t->get_template(), "\n";
}
