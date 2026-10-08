<?php
/**
 * 404 template.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! larijani_do_location( '404' ) ) :
	?>
	<div class="ls-root">
		<section class="w-full py-space-2xl">
			<div class="max-w-3xl mx-auto px-4 text-center flex flex-col items-center gap-5">
				<div class="w-24 h-24 rounded-3xl bg-surface-dark text-primary-fixed flex items-center justify-center text-5xl shadow-xl"><i class="bi bi-cone-striped" aria-hidden="true"></i></div>
				<span class="font-display-hero text-display-hero text-primary-container">۴۰۴</span>
				<h1 class="font-headline-lg text-headline-lg text-surface-dark"><?php esc_html_e( 'صفحه مورد نظر پیدا نشد', 'larijani-stone' ); ?></h1>
				<p class="font-body-lg text-body-lg text-on-surface-variant"><?php esc_html_e( 'ممکن است آدرس تغییر کرده باشد. از جستجو استفاده کنید یا به صفحه اصلی برگردید.', 'larijani-stone' ); ?></p>
				<?php get_search_form( array( 'ls_variant' => '404' ) ); ?>
				<div class="flex flex-wrap items-center justify-center gap-3">
					<a class="inline-flex items-center gap-2 bg-primary-container hover:bg-primary text-white font-bold text-sm px-6 py-3 rounded-full" href="<?php echo esc_url( home_url( '/' ) ); ?>"><i class="bi bi-house-door" aria-hidden="true"></i><?php esc_html_e( 'صفحه اصلی', 'larijani-stone' ); ?></a>
					<a class="inline-flex items-center gap-2 bg-white text-surface-dark font-bold text-sm px-6 py-3 rounded-full shadow-sm" href="<?php echo esc_url( larijani_tel( larijani_opt( 'phone_1' ) ) ); ?>"><i class="bi bi-telephone" aria-hidden="true"></i><?php esc_html_e( 'تماس با ما', 'larijani-stone' ); ?></a>
				</div>
			</div>
		</section>
	</div>
	<?php
endif;

get_footer();
