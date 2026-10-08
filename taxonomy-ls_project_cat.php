<?php
/**
 * Portfolio archive.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! larijani_do_location( 'archive' ) ) :
	$larijani_filters = array( array( 'all', __( 'همه پروژه‌ها', 'larijani-stone' ) ) );
	$larijani_terms   = get_terms( array( 'taxonomy' => 'ls_project_cat', 'hide_empty' => true ) );
	foreach ( is_wp_error( $larijani_terms ) ? array() : $larijani_terms as $larijani_t ) {
		$larijani_filters[] = array( $larijani_t->slug, $larijani_t->name );
	}
	?>
	<div class="ls-root bg-surface-canvas">
		<section class="w-full pt-space-xl pb-space-lg">
			<div class="max-w-[80rem] mx-auto px-margin-mobile lg:px-margin flex flex-col gap-3">
				<span class="inline-flex items-center gap-1.5 self-start px-3 py-1 rounded-full bg-secondary-container text-on-secondary-fixed text-label-badge font-label-badge"><span class="w-2 h-2 rounded-full bg-accent-emerald animate-pulse"></span><?php esc_html_e( 'پورتفولیو پروژه‌ها', 'larijani-stone' ); ?></span>
				<h1 class="font-headline-lg text-headline-lg lg:text-display-hero text-surface-dark"><?php echo esc_html( post_type_archive_title( '', false ) ? post_type_archive_title( '', false ) : single_term_title( '', false ) ); ?></h1>
			</div>
		</section>
		<section class="w-full pb-space-2xl" data-ls-filter-scope data-ls-count-suffix="<?php esc_attr_e( 'پروژه', 'larijani-stone' ); ?>">
			<div class="max-w-[80rem] mx-auto px-margin-mobile lg:px-margin">
				<?php if ( count( $larijani_filters ) > 1 ) : ?>
				<div class="flex items-center gap-2 overflow-x-auto bg-surface-card p-2 rounded-2xl shadow-sm mb-space-xl scrollbar-none"><?php echo larijani_filter_buttons( $larijani_filters ); // phpcs:ignore ?></div>
				<?php endif; ?>
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-lg">
					<?php
					while ( have_posts() ) {
						the_post();
						echo larijani_project_card( larijani_project_data( get_post() ) ); // phpcs:ignore
					}
					?>
				</div>
				<div class="pt-8"><?php echo larijani_pagination(); // phpcs:ignore ?></div>
			</div>
		</section>
	</div>
	<?php
endif;

get_footer();
