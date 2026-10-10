<?php
$_SERVER['HTTP_HOST'] = 'localhost:8083';
require $argv[1] . '/wp-load.php';
global $wpdb;
$n = $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_larijani_rl_%' OR option_name LIKE '_transient_timeout_larijani_rl_%' OR option_name LIKE '_transient_larijani_dup_%' OR option_name LIKE '_transient_timeout_larijani_dup_%'" );
wp_cache_flush();
echo "reset $n\n";
