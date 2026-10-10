<?php
$_SERVER['HTTP_HOST'] = 'localhost:8085';
define( 'LS_TEST_USER', 1 );
require __DIR__ . '/../wpz/wp-load.php';
wp_set_current_user( 1 );
switch_theme( 'twentytwentyfive' );
delete_option( 'ls_auto_setup_done' );
update_option( 'fresh_site', 0 );
$own = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'درباره ما (صفحه مشتری)', 'post_content' => 'محتوای مشتری' ) );
update_option( 'show_on_front', 'page' ); update_option( 'page_on_front', $own );
$before = wp_count_posts( 'page' )->publish;
$j0 = get_option( 'larijani_import_journal' );
switch_theme( 'larijani-stone' );
do_action( 'after_switch_theme', 'twentytwentyfive', wp_get_theme( 'twentytwentyfive' ) );
wp_cache_flush();
$after = wp_count_posts( 'page' )->publish;
$j1 = get_option( 'larijani_import_journal' );
echo json_encode( array( 'pages_before' => $before, 'pages_after' => $after, 'front_kept' => (int) get_option( 'page_on_front' ) === $own, 'journal_unchanged' => $j0 === $j1, 'notice' => get_transient( 'ls_show_setup_notice' ) ) ), "\n";
