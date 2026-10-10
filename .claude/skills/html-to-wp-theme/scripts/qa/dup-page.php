<?php
$_SERVER['HTTP_HOST'] = $argv[2];
require $argv[1] . '/wp-load.php';
$src = (int) get_option( 'page_on_front' );
$id  = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'QA copy ' . time(), 'post_name' => 'qa-copy-' . time() ) );
foreach ( array( '_elementor_data', '_elementor_edit_mode', '_elementor_template_type', '_elementor_version', '_wp_page_template', '_elementor_page_settings' ) as $k ) {
	$v = get_post_meta( $src, $k, true );
	if ( '' !== $v ) { update_post_meta( $id, $k, wp_slash( $v ) ); }
}
echo $src, ' ', $id, "\n";
