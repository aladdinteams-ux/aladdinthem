<?php
/**
 * Comments.
 *
 * @package PayabSanatAsa
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
if ( have_comments() ) {
	echo '<ol class="comment-list">';
	wp_list_comments( array( 'style' => 'ol', 'short_ping' => true ) );
	echo '</ol>';
	the_comments_navigation();
}
if ( comments_open() ) {
	comment_form();
}
