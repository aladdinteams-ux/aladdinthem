<?php
/**
 * 404 template.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( ! ls_do_location( '404' ) ) :
	?>
	<div class="ls-root">
		<section class="w-full py-space-2xl">
			<div class="max-w-3xl mx-auto px-4 text-center flex flex-col items-center gap-5">
				<div class="w-24 h-24 rounded-3xl bg-surface-dark text-primary-fixed flex items-center justify-center text-5xl shadow-xl"><i class="bi bi-cone-striped" aria-hidden="true"></i></div>
				<span class="font-display-hero text-display-hero text-primary-container">۴۰۴</span>
				<h1 class="font-headline-lg text-headline-lg text-surface-dark"><?php esc_html_e( 'صفحه مورد نظر پیدا نشد', 'larijani' ); ?></h1>
				<p class="font-body-lg text-body-lg text-on-surface-variant"><?php esc_html_e( 'ممکن است آدرس تغییر کرده باشد. از جستجو استفاده کنید یا به صفحه اصلی برگردید.', 'larijani' ); ?></p>
				<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="w-full max-w-xl bg-surface-card rounded-2xl shadow-sm p-2 flex items-center gap-2">
					<i class="bi bi-search text-outline text-lg px-3" aria-hidden="true"></i>
					<input class="flex-1 py-3 border-0 focus:ring-0 text-on-surface placeholder:text-outline" type="search" name="s" placeholder="<?php esc_attr_e( 'جستجو در سایت…', 'larijani' ); ?>">
					<button class="px-5 py-3 rounded-xl bg-primary-container hover:bg-primary text-white font-bold text-sm" type="submit"><?php esc_html_e( 'جستجو', 'larijani' ); ?></button>
				</form>
				<div class="flex flex-wrap items-center justify-center gap-3">
					<a class="inline-flex items-center gap-2 bg-primary-container hover:bg-primary text-white font-bold text-sm px-6 py-3 rounded-full" href="<?php echo esc_url( home_url( '/' ) ); ?>"><i class="bi bi-house-door" aria-hidden="true"></i><?php esc_html_e( 'صفحه اصلی', 'larijani' ); ?></a>
					<a class="inline-flex items-center gap-2 bg-white text-surface-dark font-bold text-sm px-6 py-3 rounded-full shadow-sm" href="<?php echo esc_url( ls_tel( ls_opt( 'phone_1' ) ) ); ?>"><i class="bi bi-telephone" aria-hidden="true"></i><?php esc_html_e( 'تماس با ما', 'larijani' ); ?></a>
				</div>
			</div>
		</section>
	</div>
	<?php
endif;

get_footer();
