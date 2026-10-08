<?php
/**
 * Single portfolio project.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	if ( larijani_do_location( 'single' ) ) {
		continue;
	}
	if ( larijani_is_built_with_elementor( get_the_ID() ) ) {
		the_content();
		continue;
	}
	$larijani_p = larijani_project_data( get_post() );
	?>
	<div class="ls-root bg-surface-canvas">
		<section class="w-full py-space-md">
			<div class="max-w-[80rem] mx-auto px-margin-mobile lg:px-margin">
				<nav class="flex items-center flex-wrap gap-2 text-on-surface-variant font-body-sm text-body-sm"><?php echo larijani_breadcrumb_html(); // phpcs:ignore ?></nav>
			</div>
		</section>
		<section class="w-full pb-space-2xl">
			<div class="max-w-[80rem] mx-auto px-margin-mobile lg:px-margin grid grid-cols-1 lg:grid-cols-12 gap-space-xl">
				<div class="lg:col-span-8 flex flex-col gap-space-lg">
					<div class="relative rounded-3xl overflow-hidden shadow-xl aspect-[16/10] bg-surface-dark">
						<?php the_post_thumbnail( 'large', array( 'class' => 'w-full h-full object-cover' ) ); ?>
						<div class="absolute inset-0 bg-gradient-to-t from-surface-dark/80 via-transparent to-transparent"></div>
						<div class="absolute top-4 right-4 flex flex-wrap gap-2">
							<?php if ( $larijani_p['badge_1'] ) : ?><span class="px-3 py-1 rounded-full bg-white/90 text-primary text-label-badge font-label-badge"><?php echo esc_html( $larijani_p['badge_1'] ); ?></span><?php endif; ?>
							<?php if ( $larijani_p['badge_2'] ) : ?><span class="px-3 py-1 rounded-full bg-surface-dark/80 text-white text-label-badge font-label-badge"><?php echo esc_html( $larijani_p['badge_2'] ); ?></span><?php endif; ?>
						</div>
						<div class="absolute bottom-5 right-5 left-5 flex items-center justify-between text-white gap-3">
							<?php if ( $larijani_p['location'] ) : ?><span class="flex items-center gap-1.5 text-sm"><i class="bi bi-geo-alt-fill text-primary-fixed" aria-hidden="true"></i><?php echo esc_html( $larijani_p['location'] ); ?></span><?php endif; ?>
							<?php if ( $larijani_p['code'] ) : ?><span class="font-label-badge text-label-badge bg-primary-container/90 px-2.5 py-1 rounded"><?php echo esc_html( $larijani_p['code'] ); ?></span><?php endif; ?>
						</div>
					</div>
					<article class="bg-surface-card rounded-3xl p-6 sm:p-10 shadow-sm">
						<h1 class="font-headline-lg text-headline-lg text-surface-dark mb-6"><?php the_title(); ?></h1>
						<div class="ls-prose"><?php the_content(); ?></div>
					</article>
				</div>
				<aside class="lg:col-span-4 flex flex-col gap-space-md">
					<?php if ( $larijani_p['specs'] ) : ?>
					<div class="bg-surface-card rounded-3xl p-6 shadow-sm flex flex-col gap-3">
						<h2 class="font-headline-sm text-headline-sm text-surface-dark"><?php esc_html_e( 'مشخصات فنی پروژه', 'larijani-stone' ); ?></h2>
						<?php foreach ( $larijani_p['specs'] as $larijani_spec ) : ?>
						<div class="bg-surface-canvas p-3 rounded-xl flex items-center justify-between gap-3"><span class="text-sm text-on-surface-variant"><?php echo esc_html( $larijani_spec[0] ); ?></span><span class="font-bold text-surface-dark text-sm"><?php echo esc_html( $larijani_spec[1] ); ?></span></div>
						<?php endforeach; ?>
						<?php if ( $larijani_p['note'] ) : ?><p class="text-sm text-accent-emerald flex items-center gap-1.5 pt-2"><?php echo larijani_icon( $larijani_p['note_icon'] ); // phpcs:ignore ?><?php echo esc_html( $larijani_p['note'] ); ?></p><?php endif; ?>
					</div>
					<?php endif; ?>
					<?php larijani_render_sidebar_cta( array( 'title' => __( 'پروژه مشابه می‌خواهید؟', 'larijani-stone' ), 'desc' => __( 'برای انتخاب قالب، فرمولاسیون و ماشین‌آلات متناسب با پروژه خود با کارشناسان ما مشورت کنید.', 'larijani-stone' ) ) ); ?>
				</aside>
			</div>
		</section>
	</div>
	<?php
endwhile;

get_footer();
