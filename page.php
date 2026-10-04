<?php
/**
 * Page template. Elementor pages render full width; classic pages get a styled container.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	if ( ls_is_built_with_elementor( get_the_ID() ) ) {
		the_content();
		continue;
	}
	if ( ls_do_location( 'single' ) ) {
		continue;
	}
	?>
	<div class="ls-root bg-surface-canvas">
		<section class="w-full py-space-md">
			<div class="max-w-[80rem] mx-auto px-margin-mobile lg:px-margin">
				<nav class="flex items-center flex-wrap gap-2 text-on-surface-variant font-body-sm text-body-sm" aria-label="<?php esc_attr_e( 'مسیر صفحه', 'larijani' ); ?>"><?php echo ls_breadcrumb_html(); // phpcs:ignore ?></nav>
			</div>
		</section>
		<section class="w-full pb-space-2xl">
			<div class="max-w-[80rem] mx-auto px-margin-mobile lg:px-margin">
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'bg-surface-card rounded-3xl p-6 sm:p-10 shadow-sm' ); ?>>
					<h1 class="font-headline-lg text-headline-lg text-surface-dark mb-6"><?php the_title(); ?></h1>
					<?php if ( has_post_thumbnail() ) : ?>
					<div class="rounded-2xl overflow-hidden mb-8"><?php the_post_thumbnail( 'ls-wide', array( 'class' => 'w-full h-auto' ) ); ?></div>
					<?php endif; ?>
					<div class="ls-prose"><?php the_content(); ?></div>
					<?php wp_link_pages(); ?>
				</article>
				<?php
				if ( comments_open() || get_comments_number() ) {
					echo '<div class="mt-8">';
					comments_template();
					echo '</div>';
				}
				?>
			</div>
		</section>
	</div>
	<?php
endwhile;

get_footer();
