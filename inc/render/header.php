<?php
/**
 * Site header renderer (used by header.php fallback and the "LS Header" Elementor widget).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Header style for the current request: page override (meta box) or the theme option.
 *
 * @return string dark|light
 */
function larijani_header_style() {
	$style = '';
	if ( is_singular() ) {
		$style = (string) get_post_meta( get_queried_object_id(), '_ls_header_style', true );
	}
	if ( ! in_array( $style, array( 'dark', 'light' ), true ) ) {
		$style = (string) larijani_opt( 'header_style' );
	}
	return 'light' === $style ? 'light' : 'dark';
}

/**
 * Render the site header.
 *
 * @param array $s Settings (all optional – empty values fall back to Customizer options).
 */
function larijani_render_site_header( $s = array() ) {
	$s = wp_parse_args(
		array_filter(
			(array) $s,
			static function ( $v ) {
				return '' !== $v && null !== $v && array() !== $v;
			}
		),
		array(
			'style'           => larijani_header_style(),
			'show_topbar'     => larijani_opt( 'header_topbar' ) ? 'yes' : 'no',
			'address'         => larijani_opt( 'address_short' ),
			'hours'           => larijani_opt( 'hours' ),
			'phone'           => larijani_opt( 'phone_1' ),
			'whatsapp_label'  => larijani_opt( 'header_whatsapp_label' ),
			'whatsapp_url'    => larijani_whatsapp_url(),
			'logo'            => null,
			'brand'           => larijani_opt( 'brand_name' ),
			'tagline'         => larijani_opt( 'brand_tagline' ),
			'menu'            => 'primary',
			'show_search'     => larijani_opt( 'header_search' ) ? 'yes' : 'no',
			'cta_text'        => larijani_opt( 'header_cta_text' ),
			'cta_short'       => larijani_opt( 'header_cta_short' ),
			'cta_link'        => larijani_opt( 'header_cta_link' ),
			'sticky'          => larijani_opt( 'header_sticky' ) ? 'yes' : 'no',
			'drawer_main'     => __( 'بخش‌های اصلی', 'larijani-stone' ),
			'drawer_cats'     => __( 'دسته‌بندی تجهیزات', 'larijani-stone' ),
			'drawer_cats_menu' => 'drawer_categories',
			'drawer_whatsapp' => __( 'پیام در واتساپ', 'larijani-stone' ),
		)
	);

	$uid       = 'lsh' . wp_rand( 100, 99999 );
	$phone     = $s['phone'];
	$cta_link  = $s['cta_link'];
	$cta_attrs = is_array( $cta_link ) ? larijani_link_attrs( $cta_link ) : 'href="' . esc_url( $cta_link ? $cta_link : larijani_tel( $phone ) ) . '"';
	if ( is_array( $cta_link ) && empty( $cta_link['url'] ) ) {
		$cta_attrs = 'href="' . esc_url( larijani_tel( $phone ) ) . '"';
	}
	$nav       = larijani_menu_items( $s['menu'], larijani_default_nav() );
	$cats      = larijani_menu_items(
		$s['drawer_cats_menu'],
		array(
			array( __( 'قالب کفپوش', 'larijani-stone' ), '#' ),
			array( __( 'میز ویبره سنگین', 'larijani-stone' ), '#' ),
			array( __( 'قالب صراحی و نما', 'larijani-stone' ), '#' ),
			array( __( 'رزین روان‌کننده', 'larijani-stone' ), '#' ),
		)
	);
	$home      = home_url( '/' );
	$sticky    = 'yes' === $s['sticky'] ? 'sticky top-0' : 'relative';
	$light     = 'light' === $s['style'] || ( 'inherit' === $s['style'] && 'light' === larijani_header_style() );
	?>
	<div class="ls-root ls-site-header" data-ls-header>
		<?php if ( 'yes' === $s['show_topbar'] && $light ) : ?>
		<div class="hidden lg:block border-b border-[#E7EBE5] bg-white text-xs text-gray-600">
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5 flex justify-between items-center">
				<div class="flex items-center gap-6 divide-x divide-gray-200 divide-x-reverse">
					<?php if ( $s['address'] ) : ?>
					<div class="flex items-center gap-2"><i class="bi bi-geo-alt text-[14px] text-primary-container" aria-hidden="true"></i><span><?php echo esc_html( $s['address'] ); ?></span></div>
					<?php endif; ?>
					<?php foreach ( array( array( larijani_opt( 'phone_1_label' ), $phone ), array( larijani_opt( 'phone_2_label' ), larijani_opt( 'phone_2' ) ) ) as $i => $ph ) : ?>
						<?php if ( $ph[1] ) : ?>
					<div class="pr-6 flex items-center gap-2">
							<?php if ( 0 === $i ) : ?><i class="bi bi-telephone text-[13px] text-primary-container" aria-hidden="true"></i><?php endif; ?>
						<span class="<?php echo 0 === $i ? 'font-medium' : 'text-gray-500'; ?>"><?php echo esc_html( $ph[0] ); ?>:</span>
						<a class="font-semibold text-gray-900 inline-block hover:text-primary-container" dir="ltr" href="<?php echo esc_url( larijani_tel( $ph[1] ) ); ?>"><?php echo esc_html( larijani_fa_num( preg_replace( '/\D+/', '', $ph[1] ) ) ); ?></a>
					</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
				<div class="flex items-center gap-4 text-gray-500">
					<?php if ( larijani_opt( 'email' ) ) : ?>
					<span class="text-gray-500 text-[11px]"><?php esc_html_e( 'پشتیبانی فنی:', 'larijani-stone' ); ?></span>
					<a class="hover:text-primary-container" href="<?php echo esc_url( 'mailto:' . larijani_opt( 'email' ) ); ?>"><?php echo esc_html( larijani_opt( 'email' ) ); ?></a>
					<?php endif; ?>
					<div class="flex items-center gap-2.5 mr-2">
						<?php foreach ( array_slice( larijani_social_profiles(), 0, 4 ) as $so ) : ?>
						<a class="p-1 rounded hover:bg-gray-100 text-gray-600 hover:text-primary-container" href="<?php echo esc_url( $so[1] ); ?>" title="<?php echo esc_attr( $so[2] ); ?>" target="_blank" rel="noopener"><i class="<?php echo esc_attr( $so[0] ); ?> text-[14px]" aria-hidden="true"></i></a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
		<?php elseif ( 'yes' === $s['show_topbar'] ) : ?>
		<div class="hidden lg:block bg-surface-dark text-slate-300 text-[12px] border-b border-white/10 py-2">
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
				<div class="flex items-center gap-6">
					<?php if ( $s['address'] ) : ?>
					<span class="flex items-center gap-1.5 text-slate-400"><i class="bi bi-geo-alt text-[14px]" aria-hidden="true"></i><?php echo esc_html( $s['address'] ); ?></span>
					<?php endif; ?>
					<?php if ( $s['address'] && $s['hours'] ) : ?><span class="text-slate-500">|</span><?php endif; ?>
					<?php if ( $s['hours'] ) : ?>
					<span class="text-slate-400"><?php echo esc_html( $s['hours'] ); ?></span>
					<?php endif; ?>
				</div>
				<div class="flex items-center gap-5">
					<?php if ( $phone ) : ?>
					<a class="hover:text-white transition-colors flex items-center gap-1.5 font-medium" href="<?php echo esc_url( larijani_tel( $phone ) ); ?>">
						<i class="bi bi-telephone text-[14px]" aria-hidden="true"></i>
						<span class="persian-num" dir="ltr"><?php echo esc_html( larijani_phone_display( $phone ) ); ?></span>
					</a>
					<?php endif; ?>
					<?php if ( $s['whatsapp_label'] ) : ?>
					<a class="hover:text-brand-secondary transition-colors text-xs font-semibold" href="<?php echo esc_url( $s['whatsapp_url'] ); ?>" rel="noopener" target="_blank"><?php echo esc_html( $s['whatsapp_label'] ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<header class="<?php echo esc_attr( $sticky ); ?> z-40 <?php echo $light ? 'bg-white/95 backdrop-blur border-b border-gray-100 shadow-sm' : 'bg-surface-canvas/95 backdrop-blur-md border-b border-border-subtle'; ?> transition-all" role="banner">
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 sm:h-20 flex items-center justify-between gap-3">
				<div class="flex items-center gap-2 sm:gap-3 min-w-0">
					<a class="flex items-center gap-2.5 sm:gap-3 group min-w-0" href="<?php echo esc_url( $home ); ?>" title="<?php echo esc_attr( $s['brand'] ); ?>" rel="home">
						<div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-white p-1 border border-slate-200/80 shadow-sm flex items-center justify-center transition-transform group-hover:scale-105 flex-shrink-0">
							<?php echo larijani_logo_img( 'w-full h-full object-contain', $s['logo'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<div class="flex flex-col min-w-0">
							<span class="text-base sm:text-lg lg:text-xl font-black tracking-tight text-surface-dark whitespace-nowrap"><?php echo esc_html( $s['brand'] ); ?></span>
							<?php if ( $s['tagline'] ) : ?>
							<span class="text-[10px] sm:text-[11px] font-semibold text-primary-container tracking-wide truncate max-w-[150px] sm:max-w-none"><?php echo esc_html( $s['tagline'] ); ?></span>
							<?php endif; ?>
						</div>
					</a>
				</div>

				<nav class="hidden lg:flex flex-1 min-w-0 flex-wrap items-center justify-center gap-x-5 xl:gap-x-8 gap-y-1 text-[14px] xl:text-[14.5px] font-medium text-slate-600" aria-label="<?php esc_attr_e( 'منوی اصلی', 'larijani-stone' ); ?>">
					<?php foreach ( $nav as $item ) : ?>
						<?php if ( $item['children'] ) : ?>
						<div class="relative group py-2">
							<a class="flex items-center gap-1 <?php echo $item['active'] ? 'text-surface-dark font-bold' : 'hover:text-primary-container'; ?> whitespace-nowrap transition-colors" href="<?php echo esc_url( $item['url'] ); ?>">
								<span><?php echo esc_html( $item['title'] ); ?></span>
								<i class="bi bi-chevron-down text-[11px] text-slate-400 group-hover:text-primary-container transition-transform duration-200 group-hover:rotate-180" aria-hidden="true"></i>
							</a>
							<div class="absolute top-full right-0 w-64 bg-white rounded-xl shadow-xl border border-slate-100 p-2 hidden group-hover:block group-focus-within:block z-50">
								<?php foreach ( $item['children'] as $child ) : ?>
								<a class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-surface-canvas hover:text-primary-container transition whitespace-nowrap" href="<?php echo esc_url( $child['url'] ); ?>"<?php echo $child['target'] ? ' target="' . esc_attr( $child['target'] ) . '"' : ''; ?>>
									<span class="w-2 h-2 rounded-full bg-primary-container"></span><span><?php echo esc_html( $child['title'] ); ?></span>
								</a>
								<?php endforeach; ?>
							</div>
						</div>
						<?php else : ?>
						<a class="<?php echo $item['active'] ? 'text-surface-dark font-bold border-b-2 border-primary-container pb-1' : 'hover:text-primary-container'; ?> transition-colors whitespace-nowrap" href="<?php echo esc_url( $item['url'] ); ?>"<?php echo $item['target'] ? ' target="' . esc_attr( $item['target'] ) . '"' : ''; ?><?php echo $item['active'] ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $item['title'] ); ?></a>
						<?php endif; ?>
					<?php endforeach; ?>
				</nav>

				<div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
					<?php if ( 'yes' === $s['show_search'] ) : ?>
					<button aria-label="<?php esc_attr_e( 'جستجو', 'larijani-stone' ); ?>" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-600 hover:text-primary-container hover:border-primary-container transition-all" type="button" data-ls-open="<?php echo esc_attr( $uid ); ?>-search">
						<i class="bi bi-search text-[15px]" aria-hidden="true"></i>
					</button>
					<?php endif; ?>
					<?php if ( $light ) : ?>
					<a aria-label="<?php esc_attr_e( 'محصولات نشان‌شده', 'larijani-stone' ); ?>" class="hidden sm:flex w-9 h-9 sm:w-10 sm:h-10 rounded-full border border-gray-200 items-center justify-center text-gray-600 hover:text-primary-container hover:border-primary-container transition" href="<?php echo esc_url( larijani_page_url( 'shop' ) ); ?>"><i class="bi bi-heart text-[15px]" aria-hidden="true"></i></a>
						<?php if ( $s['cta_text'] ) : ?>
					<a class="hidden sm:inline-flex items-center justify-center px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-white bg-primary-container hover:bg-primary shadow-md shadow-primary-container/25 whitespace-nowrap transition" <?php echo $cta_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $s['cta_short'] ? $s['cta_short'] : $s['cta_text'] ); ?></a>
						<?php endif; ?>
					<?php elseif ( $s['cta_text'] ) : ?>
					<a class="hidden sm:inline-flex items-center gap-2 bg-primary-container hover:bg-primary text-white text-[12.5px] lg:text-[13.5px] font-bold px-4 lg:px-5 py-2 sm:py-2.5 rounded-full shadow-sm shadow-primary-container/20 transition-all flex-shrink-0" <?php echo $cta_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<i class="bi bi-telephone-outbound text-[14px]" aria-hidden="true"></i>
						<span class="hidden md:inline lg:hidden xl:inline"><?php echo esc_html( $s['cta_text'] ); ?></span>
						<span class="inline md:hidden lg:inline xl:hidden"><?php echo esc_html( $s['cta_short'] ? $s['cta_short'] : $s['cta_text'] ); ?></span>
					</a>
					<?php endif; ?>
					<?php if ( $phone ) : ?>
					<a aria-label="<?php esc_attr_e( 'تماس تلفنی', 'larijani-stone' ); ?>" class="sm:hidden w-9 h-9 rounded-full bg-primary-container text-white flex items-center justify-center shadow-sm" href="<?php echo esc_url( larijani_tel( $phone ) ); ?>">
						<i class="bi bi-telephone-fill text-[14px]" aria-hidden="true"></i>
					</a>
					<?php endif; ?>
					<button aria-label="<?php esc_attr_e( 'باز کردن منوی موبایل', 'larijani-stone' ); ?>" aria-controls="<?php echo esc_attr( $uid ); ?>-drawer" aria-expanded="false" class="lg:hidden w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-700 hover:text-primary-container hover:border-primary-container focus:outline-none transition-all" type="button" data-ls-open="<?php echo esc_attr( $uid ); ?>-drawer">
						<i class="bi bi-list text-[22px]" aria-hidden="true"></i>
					</button>
				</div>
			</div>
		</header>

		<!-- Mobile drawer -->
		<div aria-hidden="true" class="ls-backdrop fixed inset-0 bg-black/60 backdrop-blur-sm z-50 transition-opacity duration-300 opacity-0 pointer-events-none" data-ls-backdrop="<?php echo esc_attr( $uid ); ?>-drawer"></div>
		<div id="<?php echo esc_attr( $uid ); ?>-drawer" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'منوی موبایل', 'larijani-stone' ); ?>" class="ls-drawer fixed top-0 right-0 bottom-0 w-[84%] max-w-sm bg-white z-50 shadow-2xl flex flex-col justify-between transform transition-transform duration-300 ease-in-out border-l border-slate-200 translate-x-full" data-ls-drawer>
			<div class="p-5 border-b border-slate-100 flex items-center justify-between bg-surface-canvas">
				<div class="flex items-center gap-2.5">
					<div class="w-10 h-10 rounded-xl bg-white p-1 border border-slate-200 shadow-sm flex items-center justify-center">
						<?php echo larijani_logo_img( 'w-full h-full object-contain', $s['logo'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<div class="flex flex-col">
						<span class="text-base font-black text-surface-dark"><?php echo esc_html( $s['brand'] ); ?></span>
						<span class="text-[10px] text-primary-container font-bold"><?php echo esc_html( $s['tagline'] ); ?></span>
					</div>
				</div>
				<button aria-label="<?php esc_attr_e( 'بستن منو', 'larijani-stone' ); ?>" class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-800 focus:outline-none transition-colors" type="button" data-ls-close>
					<i class="bi bi-x-lg text-[16px]" aria-hidden="true"></i>
				</button>
			</div>
			<div class="flex-1 overflow-y-auto p-5 space-y-6">
				<div>
					<span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-2 px-2"><?php echo esc_html( $s['drawer_main'] ); ?></span>
					<nav class="flex flex-col space-y-1">
						<?php foreach ( $nav as $item ) : ?>
						<a class="flex items-center justify-between px-3 py-2.5 rounded-xl <?php echo $item['active'] ? 'font-bold text-surface-dark bg-slate-100/70' : 'font-medium text-slate-700 hover:bg-slate-50 hover:text-primary-container'; ?> transition-colors" href="<?php echo esc_url( $item['url'] ); ?>" data-ls-close>
							<span><?php echo esc_html( $item['title'] ); ?></span>
							<i class="bi bi-chevron-left text-[12px] text-slate-400" aria-hidden="true"></i>
						</a>
							<?php foreach ( $item['children'] as $child ) : ?>
							<a class="flex items-center gap-2 pr-6 pl-3 py-2 rounded-xl text-sm text-slate-600 hover:bg-slate-50 hover:text-primary-container transition-colors" href="<?php echo esc_url( $child['url'] ); ?>" data-ls-close>
								<span class="w-1.5 h-1.5 rounded-full bg-primary-container"></span><span><?php echo esc_html( $child['title'] ); ?></span>
							</a>
							<?php endforeach; ?>
						<?php endforeach; ?>
					</nav>
				</div>
				<?php if ( $cats ) : ?>
				<div class="border-t border-slate-100 pt-4">
					<span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-2 px-2"><?php echo esc_html( $s['drawer_cats'] ); ?></span>
					<div class="grid grid-cols-2 gap-2 text-xs">
						<?php foreach ( $cats as $cat ) : ?>
						<a class="p-2.5 rounded-xl bg-surface-canvas text-slate-700 hover:text-primary-container border border-slate-100 font-medium text-center" href="<?php echo esc_url( $cat['url'] ); ?>" data-ls-close><?php echo esc_html( $cat['title'] ); ?></a>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif; ?>
				<div class="border-t border-slate-100 pt-4 space-y-2 text-xs text-slate-600">
					<?php if ( larijani_opt( 'address' ) ) : ?>
					<div class="flex items-center gap-2 text-slate-500"><i class="bi bi-geo-alt text-primary-container" aria-hidden="true"></i><span><?php echo esc_html( larijani_opt( 'address' ) ); ?></span></div>
					<?php endif; ?>
					<?php if ( $s['hours'] ) : ?>
					<div class="flex items-center gap-2"><i class="bi bi-clock text-primary-container" aria-hidden="true"></i><span><?php echo esc_html( $s['hours'] ); ?></span></div>
					<?php endif; ?>
				</div>
			</div>
			<div class="p-5 border-t border-slate-100 bg-surface-canvas space-y-2">
				<?php if ( $phone ) : ?>
				<a class="w-full flex items-center justify-center gap-2 bg-primary-container text-white py-3 rounded-xl font-bold text-sm shadow-md shadow-primary-container/20 transition-all" href="<?php echo esc_url( larijani_tel( $phone ) ); ?>">
					<i class="bi bi-telephone-fill" aria-hidden="true"></i>
					<span><?php echo esc_html( sprintf( /* translators: %s phone */ __( 'تماس مستقیم: %s', 'larijani-stone' ), larijani_fa_num( preg_replace( '/\D+/', '', $phone ) ) ) ); ?></span>
				</a>
				<?php endif; ?>
				<?php if ( $s['drawer_whatsapp'] ) : ?>
				<a class="w-full flex items-center justify-center gap-2 bg-emerald-600 text-white py-2.5 rounded-xl font-medium text-xs transition-all" href="<?php echo esc_url( $s['whatsapp_url'] ); ?>" rel="noopener" target="_blank">
					<i class="bi bi-whatsapp" aria-hidden="true"></i><span><?php echo esc_html( $s['drawer_whatsapp'] ); ?></span>
				</a>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( 'yes' === $s['show_search'] ) : ?>
		<!-- Search modal -->
		<div aria-hidden="true" class="ls-backdrop fixed inset-0 bg-black/60 backdrop-blur-sm z-[60] transition-opacity duration-300 opacity-0 pointer-events-none" data-ls-backdrop="<?php echo esc_attr( $uid ); ?>-search"></div>
		<div id="<?php echo esc_attr( $uid ); ?>-search" class="ls-drawer fixed inset-x-0 top-0 z-[61] -translate-y-full transition-transform duration-300" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'جستجو', 'larijani-stone' ); ?>" data-ls-drawer>
			<div class="max-w-3xl mx-auto mt-6 px-4">
				<form role="search" method="get" action="<?php echo esc_url( $home ); ?>" class="bg-white rounded-2xl shadow-2xl p-2 flex items-center gap-2">
					<i class="bi bi-search text-outline text-lg px-3" aria-hidden="true"></i>
					<input class="flex-1 py-3 border-0 focus:ring-0 text-on-surface text-base placeholder:text-outline" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'جستجو در محصولات، قالب‌ها و مقالات…', 'larijani-stone' ); ?>" data-ls-autofocus>
					<?php if ( larijani_has_woo() ) : ?>
					<select name="post_type" class="hidden sm:block bg-surface-canvas rounded-xl py-2.5 pr-3 text-sm text-on-surface-variant">
						<option value=""><?php esc_html_e( 'همه', 'larijani-stone' ); ?></option>
						<option value="product"><?php esc_html_e( 'محصولات', 'larijani-stone' ); ?></option>
						<option value="post"><?php esc_html_e( 'مقالات', 'larijani-stone' ); ?></option>
					</select>
					<?php endif; ?>
					<button type="submit" class="px-5 py-3 rounded-xl bg-primary-container hover:bg-primary text-white font-bold text-sm transition-colors"><?php esc_html_e( 'جستجو', 'larijani-stone' ); ?></button>
					<button type="button" class="w-11 h-11 rounded-xl text-slate-500 hover:text-slate-800 flex items-center justify-center" data-ls-close aria-label="<?php esc_attr_e( 'بستن', 'larijani-stone' ); ?>"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
				</form>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<?php
}
