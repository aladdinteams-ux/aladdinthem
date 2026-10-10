<?php defined( 'ABSPATH' ) || exit; if ( post_password_required() ) { return; } ?>
<section class="article-section"><h2><?php esc_html_e( 'دیدگاه‌ها', 'frpmesh-hybrid' ); ?></h2><?php if ( have_comments() ) { echo '<ol>'; wp_list_comments( array( 'style'=>'ol', 'short_ping'=>true ) ); echo '</ol>'; the_comments_pagination(); } comment_form(); ?></section>
