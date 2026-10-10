<?php
define( 'WP_INSTALLING', true );
$_SERVER['HTTP_HOST'] = 'localhost:8083';
require __DIR__ . '/wpel/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
wp_install( 'لاریجانی استون', 'admin', 'admin@example.com', true, '', 'admin123' );
update_option( 'permalink_structure', '/%postname%/' );
echo 'WP ', get_bloginfo( 'version' ), ' fresh=', get_option( 'fresh_site' ), "\n";
