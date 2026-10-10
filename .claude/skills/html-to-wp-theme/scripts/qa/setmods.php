<?php
$_SERVER['HTTP_HOST'] = 'localhost:8083';
require $argv[1] . '/wp-load.php';
foreach ( array_slice( $argv, 2 ) as $kv ) { list( $k, $v ) = explode( '=', $kv, 2 ); if ( '' === $v ) { remove_theme_mod( $k ); } else { set_theme_mod( $k, $v ); } }
echo "ok\n";
