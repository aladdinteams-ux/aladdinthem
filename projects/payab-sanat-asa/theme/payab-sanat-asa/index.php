<?php
/**
 * Fallback listing (blog home, archives): uses the approved card classes.
 *
 * @package PayabSanatAsa
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="section aladdin-listing">
	<div class="container">
		<?php if ( is_archive() ) : ?>
			<header class="section-head"><h1><?php the_archive_title(); ?></h1></header>
		<?php elseif ( is_home() && ! is_front_page() ) : ?>
			<header class="section-head"><h1><?php single_post_title(); ?></h1></header>
		<?php endif; ?>
		<?php if ( have_posts() ) : ?>
			<div class="product-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'product-card' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium_large' ); ?></a>
						<?php endif; ?>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing found.', 'payab-sanat-asa' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
