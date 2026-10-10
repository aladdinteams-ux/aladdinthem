<?php
$_SERVER['HTTP_HOST'] = 'localhost:8083';
require $argv[1] . '/wp-load.php';
$out = array();
$lead = get_posts( array( 'post_type' => 'ls_lead', 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => '_ls_lead_private_files' ) );
$lead = $lead[0];
$files = get_post_meta( $lead, '_ls_lead_private_files', true );
$out['lead'] = $lead; $out['file'] = $files[0];
$root = larijani_core_private_dir();
$up = wp_upload_dir();
$out['direct_url'] = $up['baseurl'] . substr( $root, strlen( $up['basedir'] ) ) . '/' . $files[0]['path'];
$out['meta'] = array( 'phone' => get_post_meta( $lead, '_ls_lead_phone', true ), 'tracking' => get_post_meta( $lead, '_ls_lead_tracking', true ), 'ip_hash' => strlen( get_post_meta( $lead, '_ls_lead_ip_hash', true ) ), 'fields' => get_post_meta( $lead, '_ls_lead_fields', true ), 'status' => get_post_status( $lead ) );
$out['media_library_attachments_for_lead'] = count( get_children( array( 'post_parent' => $lead, 'post_type' => 'attachment' ) ) );
foreach ( array( 'administrator', 'editor', 'author', 'subscriber' ) as $role ) {
	$u = get_user_by( 'login', 'qa_' . $role );
	if ( ! $u ) { $id = wp_insert_user( array( 'user_login' => 'qa_' . $role, 'user_pass' => wp_generate_password(), 'role' => $role, 'user_email' => 'qa_' . $role . '@example.com' ) ); $u = get_user_by( 'id', $id ); }
	$exp = time() + 3600;
	$token = WP_Session_Tokens::get_instance( $u->ID )->create( $exp );
	$auth = wp_generate_auth_cookie( $u->ID, $exp, 'auth', $token );
	$li = wp_generate_auth_cookie( $u->ID, $exp, 'logged_in', $token );
	$_COOKIE[ LOGGED_IN_COOKIE ] = $li;
	wp_set_current_user( $u->ID );
	$out['users'][ $role ] = array(
		'cookie' => AUTH_COOKIE . '=' . $auth . '; ' . LOGGED_IN_COOKIE . '=' . $li,
		'url' => larijani_core_file_url( $lead, $files[0]['id'] ),
		'can_edit_lead' => current_user_can( 'edit_post', $lead ),
	);
}
echo json_encode( $out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
