<?php
/**
 * Site footer renderer (footer.php fallback and the "LS Footer" Elementor widget).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render the footer.
 *
 * @param array $s Settings (empty values fall back to Customizer options).
 */
function ls_render_site_footer( $s = array() ) {
	$s = wp_parse_args(
		array_filter(
			(array) $s,
			static function ( $v ) {
				return '' !== $v && null !== $v && array() !== $v;
			}
		),
		array(
			'logo'        => null,
			'brand'       => ls_opt( 'brand_name' ),
			'tagline'     => ls_opt( 'brand_tagline_alt' ),
			'about'       => ls_opt( 'footer_about' ),
			'hours'       => ls_opt( 'hours_full' ),
			'col1_title'  => ls_opt( 'footer_col1_title' ),
			'col1_menu'   => 'footer_quick',
			'col2_title'  => ls_opt( 'footer_col2_title' ),
			'col2_menu'   => 'footer_categories',
			'col3_title'  => ls_opt( 'footer_col3_title' ),
			'address'     => ls_opt( 'address' ),
			'phone_1'     => ls_opt( 'phone_1' ),
			'phone_2'     => ls_opt( 'phone_2' ),
			'email'       => ls_opt( 'email' ),
			'copyright'   => ls_opt( 'footer_copyright' ),
			'bottom_menu' => 'footer_bottom',
			'bg_image'    => ls_opt( 'footer_bg_image' ),
			'show_social' => 'yes',
		)
	);

	$bg = ls_img_url( $s['bg_image'], 'full' );
	if ( ! $bg ) {
		$bg = ls_demo_image( 'footer_bg' );
	}
	$style = $bg ? sprintf( "background-image: linear-gradient(rgba(18,23,21,.92), rgba(18,23,21,.96)), url('%s'); background-size: cover; background-position: center;", esc_url( $bg ) ) : '';

	$col1 = ls_menu_items(
		$s['col1_menu'],
		array(
			array( __( 'صفحه اصلی', 'larijani' ), home_url( '/' ) ),
			array( __( 'فروشگاه قالب‌های نشکن', 'larijani' ), ls_page_url( 'shop' ) ),
			array( __( 'خدمات و خطوط تولید', 'larijani' ), ls_page_url( 'services' ) ),
			array( __( 'نمونه کارها', 'larijani' ), ls_page_url( 'portfolio' ) ),
			array( __( 'فرمولاسیون و مقالات', 'larijani' ), ls_page_url( 'blog' ) ),
			array( __( 'تماس با واحد فروش', 'larijani' ), ls_page_url( 'contact' ) ),
		)
	);
	$col2 = ls_menu_items(
		$s['col2_menu'],
		array(
			array( __( 'قالب کفپوش و سنگفرش', 'larijani' ), '#' ),
			array( __( 'قالب جدول و دورباغچه', 'larijani' ), '#' ),
			array( __( 'قالب نما و صراحی رومی', 'larijani' ), '#' ),
			array( __( 'رزین روان‌کننده بتن', 'larijani' ), '#' ),
			array( __( 'رنگدانه‌های معدنی اکسید آهن', 'larijani' ), '#' ),
			array( __( 'روغن قالب پایه گیاهی', 'larijani' ), '#' ),
		)
	);
	$bottom = ls_menu_items(
		$s['bottom_menu'],
		array(
			array( __( 'قوانین و ضمانت محصولات', 'larijani' ), '#' ),
			array( __( 'حریم خصوصی', 'larijani' ), get_privacy_policy_url() ? get_privacy_policy_url() : '#' ),
			array( __( 'نقشه سایت', 'larijani' ), home_url( '/wp-sitemap.xml' ) ),
		)
	);
	?>
	<footer class="ls-root ls-site-footer bg-surface-footer text-slate-300 pt-12 sm:pt-16 pb-8 border-t border-slate-800" role="contentinfo" style="<?php echo esc_attr( $style ); ?>">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8 sm:gap-10 pb-10 sm:pb-12 border-b border-slate-800/80">
				<div class="sm:col-span-2 lg:col-span-2">
					<div class="flex items-center gap-3 mb-4">
						<div class="w-12 h-12 rounded-xl bg-white p-1 border border-slate-200/80 flex items-center justify-center flex-shrink-0">
							<?php echo ls_logo_img( 'w-full h-full object-contain', $s['logo'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<div>
							<span class="text-lg sm:text-xl font-black text-white"><?php echo esc_html( $s['brand'] ); ?></span>
							<div class="text-[11px] text-brand-secondary"><?php echo esc_html( $s['tagline'] ); ?></div>
						</div>
					</div>
					<p class="text-slate-400 text-xs sm:text-sm leading-relaxed mb-5 max-w-md"><?php echo esc_html( $s['about'] ); ?></p>
					<?php if ( $s['hours'] ) : ?>
					<div class="text-xs text-slate-400"><?php echo esc_html( $s['hours'] ); ?></div>
					<?php endif; ?>
					<?php $social = 'yes' === $s['show_social'] ? ls_social_profiles() : array(); ?>
					<?php if ( $social ) : ?>
					<div class="flex items-center gap-2 mt-5">
						<?php foreach ( $social as $p ) : ?>
						<a class="w-9 h-9 rounded-xl bg-white/5 hover:bg-primary-container text-slate-300 hover:text-white flex items-center justify-center transition-colors" href="<?php echo esc_url( $p[1] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $p[2] ); ?>"><i class="<?php echo esc_attr( $p[0] ); ?>" aria-hidden="true"></i></a>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
				</div>

				<?php foreach ( array( array( $s['col1_title'], $col1 ), array( $s['col2_title'], $col2 ) ) as $col ) : ?>
				<div>
					<h2 class="text-sm font-bold text-white mb-3 sm:mb-4 border-b border-slate-800 pb-2"><?php echo esc_html( $col[0] ); ?></h2>
					<ul class="space-y-2 sm:space-y-2.5 text-xs">
						<?php foreach ( $col[1] as $item ) : ?>
						<li><a class="hover:text-white transition-colors" href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['title'] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php endforeach; ?>

				<div class="sm:col-span-2 lg:col-span-1">
					<h2 class="text-sm font-bold text-white mb-3 sm:mb-4 border-b border-slate-800 pb-2"><?php echo esc_html( $s['col3_title'] ); ?></h2>
					<ul class="space-y-3 text-xs text-slate-300">
						<?php if ( $s['address'] ) : ?>
						<li class="flex items-start gap-2"><i class="bi bi-geo-alt text-brand-secondary mt-0.5" aria-hidden="true"></i><span><?php echo esc_html( $s['address'] ); ?></span></li>
						<?php endif; ?>
						<?php foreach ( array( $s['phone_1'], $s['phone_2'] ) as $ph ) : ?>
							<?php if ( $ph ) : ?>
						<li class="flex items-center gap-2"><i class="bi bi-telephone text-brand-secondary" aria-hidden="true"></i><a class="persian-num hover:text-white transition-colors" dir="ltr" href="<?php echo esc_url( ls_tel( $ph ) ); ?>"><?php echo esc_html( ls_phone_display( $ph ) ); ?></a></li>
							<?php endif; ?>
						<?php endforeach; ?>
						<?php if ( $s['email'] ) : ?>
						<li class="flex items-center gap-2"><i class="bi bi-envelope text-brand-secondary" aria-hidden="true"></i><a class="hover:text-white transition-colors" href="mailto:<?php echo esc_attr( antispambot( $s['email'] ) ); ?>"><?php echo esc_html( antispambot( $s['email'] ) ); ?></a></li>
						<?php endif; ?>
					</ul>
					<?php $seal = ls_opt( 'footer_enamad' ); ?>
					<?php if ( $seal ) : ?>
					<div class="mt-4 flex flex-wrap gap-2 [&_img]:bg-white [&_img]:rounded-xl [&_img]:p-1 [&_img]:max-h-24"><?php echo $seal; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- admin supplied trust seal. ?></div>
					<?php endif; ?>
				</div>
			</div>

			<div class="pt-6 sm:pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 text-center sm:text-right">
				<p><?php echo esc_html( $s['copyright'] ); ?></p>
				<div class="flex items-center gap-4 sm:gap-6 flex-wrap justify-center">
					<?php foreach ( $bottom as $item ) : ?>
					<a class="hover:text-slate-300 transition-colors" href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['title'] ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</footer>
	<?php
}
