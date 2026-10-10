<?php
/** Template Name: FRP Mesh Full Width */
defined('ABSPATH') || exit;
get_header();
while (have_posts()) {
    the_post();
    echo '<main id="main" class="frpmt-full-width">';
    the_content();
    wp_link_pages();
    echo '</main>';
}
get_footer();
