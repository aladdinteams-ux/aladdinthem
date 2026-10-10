<?php
$_SERVER['HTTP_HOST'] = $argv[2];
require $argv[1] . '/wp-load.php';
if ( isset( $argv[3] ) && 'del-projects' === $argv[3] ) { foreach ( get_posts( array( 'post_type' => 'ls_project', 'numberposts' => -1, 'fields' => 'ids' ) ) as $id ) { wp_delete_post( $id, true ); } }
$c = array();
foreach ( array( 'page', 'post', 'ls_project', 'elementor_library', 'nav_menu_item', 'attachment' ) as $t ) { $c[ $t ] = count( get_posts( array( 'post_type' => $t, 'numberposts' => -1, 'post_status' => 'any', 'fields' => 'ids' ) ) ); }
$c['front'] = get_the_title( get_option( 'page_on_front' ) );
$c['menus'] = count( wp_get_nav_menus() );
echo json_encode( $c, JSON_UNESCAPED_UNICODE ), "\n";
