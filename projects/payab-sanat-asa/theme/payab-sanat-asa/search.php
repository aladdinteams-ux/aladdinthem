<?php
/**
 * Search results.
 *
 * @package PayabSanatAsa
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="section aladdin-listing">
	<div class="container">
		<header class="section-head">
			<h1>
			<?php
			/* translators: %s: search query */
			printf( esc_html__( 'Search results for: %s', 'payab-sanat-asa' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
			?>
			</h1>
		</header>
		<?php get_search_form(); ?>
		<?php if ( have_posts() ) : ?>
			<div class="product-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class( 'product-card' ); ?>>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing found. Try different words.', 'payab-sanat-asa' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
