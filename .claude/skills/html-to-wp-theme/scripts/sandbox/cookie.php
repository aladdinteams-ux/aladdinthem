<?php
$_SERVER['HTTP_HOST'] = 'localhost:8083';
require __DIR__ . '/wpel/wp-load.php';
$exp = time() + 86400;
echo AUTH_COOKIE, '=', wp_generate_auth_cookie( 1, $exp, 'auth' ), '; ', LOGGED_IN_COOKIE, '=', wp_generate_auth_cookie( 1, $exp, 'logged_in' );
