<?php
/**
 * Single post.
 *
 * @package PayabSanatAsa
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) {
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'section aladdin-single' ); ?>>
		<div class="container">
			<h1><?php the_title(); ?></h1>
			<?php the_content(); ?>
			<?php wp_link_pages(); ?>
			<?php comments_template(); ?>
		</div>
	</article>
	<?php
}
get_footer();
