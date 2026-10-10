<?php
$_SERVER['HTTP_HOST'] = 'localhost:8083';
require $argv[1] . '/wp-load.php';
$t = 0; $p = 0;
function ck( $name, $ok ) { global $t, $p; $t++; $p += $ok ? 1 : 0; echo ( $ok ? 'PASS ' : 'FAIL ' ), $name, "\n"; }
$cases = array(
	'script tag'      => '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script><path d="M0 0h10v10z"/></svg>',
	'onload attr'     => '<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)"><rect width="5" height="5" onclick="x()"/></svg>',
	'js href'         => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><a href="javascript:alert(1)"><text>x</text></a><use xlink:href="javascript:alert(1)"/></svg>',
	'foreignObject'   => '<svg xmlns="http://www.w3.org/2000/svg"><foreignObject><iframe src="https://evil"></iframe></foreignObject></svg>',
	'external use'    => '<svg xmlns="http://www.w3.org/2000/svg"><use href="https://evil.example/x.svg#a"/></svg>',
	'style import'    => '<svg xmlns="http://www.w3.org/2000/svg"><style>@import url(https://evil/x.css);</style></svg>',
	'entity-encoded'  => '<svg xmlns="http://www.w3.org/2000/svg"><a href="jav&#x61;script:alert(1)"><text>x</text></a></svg>',
);
foreach ( $cases as $n => $svg ) {
	$o = larijani_core_sanitize_svg( $svg );
	ck( "svg $n neutralised", false !== $o && ! preg_match( '/script|onload|onclick|javascript|foreignObject|iframe|evil/i', $o ) );
}
ck( 'svg XXE entity rejected', false === larijani_core_sanitize_svg( '<?xml version="1.0"?><!DOCTYPE svg [<!ENTITY x SYSTEM "file:///etc/passwd">]><svg xmlns="http://www.w3.org/2000/svg"><text>&x;</text></svg>' ) );
ck( 'svg non-svg root rejected', false === larijani_core_sanitize_svg( '<html><body>x</body></html>' ) );
$logo = file_get_contents( get_template_directory() . '/assets/images/logo.svg' );
$clean = larijani_core_sanitize_svg( $logo );
ck( 'theme logo.svg survives sanitising (paths kept)', $clean && substr_count( $clean, '<path' ) === substr_count( $logo, '<path' ) );
// Upload mimes per role.
wp_set_current_user( get_user_by( 'login', 'qa_editor' )->ID );
ck( 'editor cannot upload svg', ! isset( get_allowed_mime_types()['svg'] ) );
wp_set_current_user( 1 );
ck( 'admin can upload svg (sanitised)', isset( get_allowed_mime_types()['svg'] ) );
// Privacy.
$exp = larijani_core_privacy_export( 'old@example.com' );
ck( 'privacy export finds legacy lead by e-mail inside fields', count( $exp['data'] ) >= 1 );
$phone_lead = get_posts( array( 'post_type' => 'ls_lead', 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => '_ls_lead_private_files' ) )[0];
$phone = get_post_meta( $phone_lead, '_ls_lead_phone', true );
$files = larijani_core_lead_files( $phone_lead );
$path  = larijani_core_file_path( $files[0] );
$er = larijani_core_privacy_erase( $phone );
ck( 'privacy erase by phone removes lead', $er['items_removed'] && ! get_post( $phone_lead ) );
ck( 'privacy erase removes private file from disk', $path && ! file_exists( $path ) );
echo "SUMMARY $p / $t\n";
