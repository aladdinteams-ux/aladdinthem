<?php
// prints "name=value; name=value" auth cookies for a user (sandbox only). usage: php cookie.php USER_ID_OR_LOGIN
$_SERVER['HTTP_HOST'] = getenv( 'ALADDIN_HOST' ) ?: 'localhost:8091';
require getenv( 'ALADDIN_WP_LOAD' );
$u = is_numeric( $argv[1] ) ? get_user_by( 'id', (int) $argv[1] ) : get_user_by( 'login', $argv[1] );
if ( ! $u && 'subscriber_qa' === $argv[1] ) { $id = wp_create_user( 'subscriber_qa', wp_generate_password(), 'sub@example.com' ); $u = get_user_by( 'id', $id ); $u->set_role( 'subscriber' ); }
$exp = time() + 86400;
$tok = WP_Session_Tokens::get_instance( $u->ID )->create( $exp );
echo AUTH_COOKIE, '=', rawurlencode( wp_generate_auth_cookie( $u->ID, $exp, 'auth', $tok ) ), '; ', LOGGED_IN_COOKIE, '=', rawurlencode( wp_generate_auth_cookie( $u->ID, $exp, 'logged_in', $tok ) );
