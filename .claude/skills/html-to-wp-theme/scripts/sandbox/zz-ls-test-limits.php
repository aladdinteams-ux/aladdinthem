<?php
// SANDBOX ONLY (not shipped): lets the QA script run many file cases from one IP.
// Only active when the request carries the X-LS-QA header.
if ( isset( $_SERVER['HTTP_X_LS_QA'] ) && 'limits' === $_SERVER['HTTP_X_LS_QA'] ) {
	add_filter( 'larijani_core_form_settings', function ( $s ) { $s['ip_attempts'] = array( 200, 600 ); $s['ip_success'] = array( 100, 600 ); $s['ip_daily'] = array( 100, 86400 ); return $s; } );
}
