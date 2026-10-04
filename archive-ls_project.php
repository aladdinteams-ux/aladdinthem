<?php
/**
 * Portfolio archive.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! ls_do_location( 'archive' ) ) :
	$ls_filters = array( array( 'all', __( 'همه پروژه‌ها', 'larijani' ) ) );
	$ls_terms   = get_terms( array( 'taxonomy' => 'ls_project_cat', 'hide_empty' => true ) );
	foreach ( is_wp_error( $ls_terms ) ? array() : $ls_terms as $ls_t ) {
		$ls_filters[] = array( $ls_t->slug, $ls_t->name );
	}
	?>
	<div class="ls-root bg-surface-canvas">
		<section class="w-full pt-space-xl pb-space-lg">
			<div class="max-w-[80rem] mx-auto px-margin-mobile lg:px-margin flex flex-col gap-3">
				<span class="inline-flex items-center gap-1.5 self-start px-3 py-1 rounded-full bg-secondary-container text-on-secondary-fixed text-label-badge font-label-badge"><span class="w-2 h-2 rounded-full bg-accent-emerald animate-pulse"></span><?php esc_html_e( 'پورتفولیو پروژه‌ها', 'larijani' ); ?></span>
				<h1 class="font-headline-lg text-headline-lg lg:text-display-hero text-surface-dark"><?php echo esc_html( post_type_archive_title( '', false ) ? post_type_archive_title( '', false ) : single_term_title( '', false ) ); ?></h1>
			</div>
		</section>
		<section class="w-full pb-space-2xl" data-ls-filter-scope data-ls-count-suffix="<?php esc_attr_e( 'پروژه', 'larijani' ); ?>">
			<div class="max-w-[80rem] mx-auto px-margin-mobile lg:px-margin">
				<?php if ( count( $ls_filters ) > 1 ) : ?>
				<div class="flex items-center gap-2 overflow-x-auto bg-surface-card p-2 rounded-2xl shadow-sm mb-space-xl scrollbar-none"><?php echo ls_filter_buttons( $ls_filters ); // phpcs:ignore ?></div>
				<?php endif; ?>
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-lg">
					<?php
					while ( have_posts() ) {
						the_post();
						echo ls_project_card( ls_project_data( get_post() ) ); // phpcs:ignore
					}
					?>
				</div>
				<div class="pt-8"><?php echo ls_pagination(); // phpcs:ignore ?></div>
			</div>
		</section>
	</div>
	<?php
endif;

get_footer();
