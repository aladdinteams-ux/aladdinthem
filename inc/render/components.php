<?php
/**
 * Reusable markup components: section headings, product / project / post cards.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Section heading.
 *
 * @param array $h { eyebrow, title, desc, link_text, link, align (split|center|start), dark, tag, title_class }.
 * @return string
 */
function ls_section_heading( $h ) {
	$h = wp_parse_args(
		$h,
		array(
			'eyebrow'   => '',
			'title'     => '',
			'desc'      => '',
			'link_text' => '',
			'link'      => '',
			'align'     => 'split',
			'dark'      => false,
			'tag'       => 'h2',
			'style'     => 'classic', // classic (home) | token (inner pages).
			'mb'        => 'mb-8 sm:mb-10',
		)
	);
	if ( ! $h['eyebrow'] && ! $h['title'] && ! $h['desc'] && ! $h['link_text'] ) {
		return '';
	}
	$tag      = in_array( $h['tag'], array( 'h1', 'h2', 'h3', 'h4', 'div' ), true ) ? $h['tag'] : 'h2';
	$title_c  = $h['dark'] ? 'text-white' : 'text-surface-dark';
	$desc_c   = $h['dark'] ? 'text-slate-300' : ( 'token' === $h['style'] ? 'text-on-surface-variant' : 'text-slate-500' );
	$eyebrow  = $h['eyebrow'] ? '<span class="' . ( 'token' === $h['style'] ? 'font-label-badge text-label-badge' : 'text-xs font-bold uppercase' ) . ' text-primary-container tracking-wider">' . esc_html( $h['eyebrow'] ) . '</span>' : '';
	$title    = $h['title'] ? '<' . $tag . ' class="' . ( 'token' === $h['style'] ? 'font-headline-lg text-headline-lg' : 'text-xl sm:text-2xl lg:text-3xl font-black' ) . ' ' . $title_c . ' mt-1 leading-snug">' . ls_kses( $h['title'] ) . '</' . $tag . '>' : '';
	$desc     = $h['desc'] ? '<p class="' . ( 'token' === $h['style'] ? 'font-body-md text-body-md' : 'text-xs sm:text-sm' ) . ' ' . $desc_c . ' mt-1 leading-relaxed">' . ls_kses( $h['desc'] ) . '</p>' : '';
	$link     = '';
	if ( $h['link_text'] ) {
		$link = '<a class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-primary-container hover:text-primary transition-colors self-start sm:self-auto group" ' . ls_link_attrs( $h['link'] ) . '><span>' . esc_html( $h['link_text'] ) . '</span><i class="bi bi-arrow-left transition-transform group-hover:-translate-x-1" aria-hidden="true"></i></a>';
	}

	if ( 'center' === $h['align'] ) {
		return '<div class="text-center max-w-3xl mx-auto flex flex-col items-center gap-1 ' . esc_attr( $h['mb'] ) . '">' . $eyebrow . $title . ( $h['desc'] ? '<div class="max-w-2xl">' . $desc . '</div>' : '' ) . ( $link ? '<div class="mt-3">' . $link . '</div>' : '' ) . '</div>';
	}
	if ( 'split-desc' === $h['align'] ) {
		return '<div class="flex flex-col md:flex-row md:items-end justify-between gap-space-md ' . esc_attr( $h['mb'] ) . '"><div class="flex flex-col gap-space-xs">' . $eyebrow . $title . ( $link ? '<div class="mt-2">' . $link . '</div>' : '' ) . '</div>' . ( $h['desc'] ? '<div class="max-w-md">' . $desc . '</div>' : '' ) . '</div>';
	}
	if ( 'start' === $h['align'] ) {
		return '<div class="flex flex-col gap-1 ' . esc_attr( $h['mb'] ) . '">' . $eyebrow . $title . $desc . ( $link ? '<div class="mt-2">' . $link . '</div>' : '' ) . '</div>';
	}
	return '<div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 ' . esc_attr( $h['mb'] ) . '"><div class="max-w-2xl">' . $eyebrow . $title . $desc . '</div>' . $link . '</div>';
}

/**
 * Collect heading settings from a widget settings array (keys prefixed with "heading_").
 *
 * @param array $s Settings.
 * @param array $extra Extra.
 * @return array
 */
function ls_heading_from_settings( $s, $extra = array() ) {
	return array_merge(
		array(
			'eyebrow'   => $s['heading_eyebrow'] ?? '',
			'title'     => $s['heading_title'] ?? '',
			'desc'      => $s['heading_desc'] ?? '',
			'link_text' => $s['heading_link_text'] ?? '',
			'link'      => $s['heading_link'] ?? '',
			'align'     => $s['heading_align'] ?? 'split',
			'tag'       => $s['heading_tag'] ?? 'h2',
			'style'     => $s['heading_style'] ?? 'classic',
		),
		$extra
	);
}

/**
 * Responsive grid column classes.
 *
 * @param int|string $desktop Desktop columns.
 * @param int|string $tablet Tablet columns.
 * @param int|string $mobile Mobile columns.
 * @return string
 */
function ls_grid_cols( $desktop = 4, $tablet = 2, $mobile = 1 ) {
	$m = array( 1 => 'grid-cols-1', 2 => 'grid-cols-2', 3 => 'grid-cols-3', 4 => 'grid-cols-4' );
	$t = array( 1 => 'sm:grid-cols-1', 2 => 'sm:grid-cols-2', 3 => 'sm:grid-cols-3', 4 => 'sm:grid-cols-4' );
	$d = array( 1 => 'lg:grid-cols-1', 2 => 'lg:grid-cols-2', 3 => 'lg:grid-cols-3', 4 => 'lg:grid-cols-4', 5 => 'lg:grid-cols-5', 6 => 'lg:grid-cols-6' );
	return ( $m[ (int) $mobile ] ?? 'grid-cols-1' ) . ' ' . ( $t[ (int) $tablet ] ?? 'sm:grid-cols-2' ) . ' ' . ( $d[ (int) $desktop ] ?? 'lg:grid-cols-4' );
}

/**
 * Normalised product card defaults.
 *
 * @return array
 */
function ls_product_card_defaults() {
	return array(
		'image'          => '',
		'image_alt'      => '',
		'badge'          => '',
		'badge_tone'     => 'primary',
		'stock_badge'    => '',
		'stock_tone'     => 'emerald',
		'category'       => '',
		'title'          => '',
		'subtitle'       => '',
		'subtitle_icon'  => 'bi bi-check2-circle',
		'desc'           => '',
		'code'           => '',
		'specs'          => array(), // [ [label, value, accent], ... ]
		'price_label'    => '',
		'price'          => '',
		'price_html'     => '',
		'currency'       => 'تومان',
		'url'            => '#',
		'button_text'    => '',
		'button_style'   => 'primary', // primary | dark | icon.
		'button_icon'    => 'bi bi-cart-plus-fill',
		'button_url'     => '',
		'button_attrs'   => '',
		'button_class'   => '',
		'chat_button'    => false,
		'chat_url'       => '',
		'filter'         => '',
		'price_raw'      => 0,
	);
}

/**
 * Render a product card.
 *
 * @param array  $p     Product data (see ls_product_card_defaults()).
 * @param string $style classic | catalog | compact.
 * @return string
 */
function ls_product_card( $p, $style = 'classic' ) {
	$p    = wp_parse_args( $p, ls_product_card_defaults() );
	$img  = ls_img( $p['image'], 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500', $p['image_alt'] ? $p['image_alt'] : wp_strip_all_tags( $p['title'] ), 'ls-card' );
	// Manual cards without a link point to the sample product page (or the shop), never to "#".
	$url  = ( $p['url'] && '#' !== $p['url'] ) ? $p['url'] : ls_page_url( 'product-sample', ls_page_url( 'shop', home_url( '/' ) ) );
	$burl = ( $p['button_url'] && '#' !== $p['button_url'] ) ? $p['button_url'] : $url;
	$price = $p['price_html'] ? $p['price_html'] : esc_html( $p['price'] );
	$data = ' data-category="' . esc_attr( $p['filter'] ) . '" data-price="' . esc_attr( (float) $p['price_raw'] ) . '" data-title="' . esc_attr( wp_strip_all_tags( $p['title'] ) ) . '"';

	ob_start();
	if ( 'store' === $style ) :
		$store_badge = array(
			'dark'    => 'bg-surface-dark text-on-tertiary',
			'primary' => 'bg-primary text-on-primary',
			'amber'   => 'bg-accent-amber text-white',
			'emerald' => 'bg-accent-emerald text-white',
			'cobalt'  => 'bg-accent-cobalt text-white',
			'sage'    => 'bg-secondary-container text-on-secondary-container',
			'light'   => 'bg-white/90 text-on-surface',
		);
		?>
		<article class="ls-filter-item product-card flex flex-col justify-between bg-surface-card rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group"<?php echo $data; // phpcs:ignore ?>>
			<div>
				<div class="relative w-full aspect-[4/3] bg-surface-container-high overflow-hidden">
					<a href="<?php echo esc_url( $url ); ?>" class="block w-full h-full"><?php echo $img; // phpcs:ignore ?></a>
					<?php if ( $p['badge'] ) : ?><span class="absolute top-3 right-3 <?php echo esc_attr( $store_badge[ $p['badge_tone'] ] ?? $store_badge['dark'] ); ?> px-space-sm py-1 rounded-md text-label-badge font-label-badge shadow-sm"><?php echo esc_html( $p['badge'] ); ?></span><?php endif; ?>
					<?php if ( $p['stock_badge'] ) : ?><span class="absolute bottom-3 left-3 <?php echo 'emerald' === $p['stock_tone'] ? 'bg-accent-emerald/90 text-white' : 'bg-surface-dark/80 text-on-tertiary'; ?> backdrop-blur-md px-space-xs py-0.5 rounded text-label-badge font-label-badge flex items-center gap-1"><?php if ( 'emerald' === $p['stock_tone'] ) : ?><i class="bi bi-check-circle-fill text-[12px]" aria-hidden="true"></i><?php endif; ?><?php echo esc_html( $p['stock_badge'] ); ?></span><?php endif; ?>
				</div>
				<div class="p-space-md flex flex-col gap-space-xs">
					<?php if ( $p['category'] ) : ?><span class="text-body-sm font-body-sm text-outline"><?php echo esc_html( $p['category'] ); ?></span><?php endif; ?>
					<h3 class="font-title-card text-title-card text-on-surface group-hover:text-primary transition-colors"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $p['title'] ); ?></a></h3>
					<?php if ( $p['desc'] ) : ?><p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-2"><?php echo esc_html( $p['desc'] ); ?></p><?php endif; ?>
					<?php if ( $p['specs'] ) : ?>
					<div class="grid grid-cols-3 gap-1 bg-surface-canvas p-2 rounded-xl mt-space-xs text-center">
						<?php foreach ( array_slice( $p['specs'], 0, 3 ) as $sp ) : ?>
						<div class="flex flex-col"><span class="font-body-sm text-body-sm text-outline"><?php echo esc_html( $sp[0] ); ?></span><span class="font-label-nav text-label-nav text-on-surface"><?php echo esc_html( $sp[1] ); ?></span></div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="p-space-md pt-0 flex flex-col gap-space-sm mt-space-sm">
				<div class="flex items-center justify-between">
					<div class="flex flex-col">
						<?php if ( $p['price_label'] ) : ?><span class="text-body-sm font-body-sm text-outline"><?php echo esc_html( $p['price_label'] ); ?></span><?php endif; ?>
						<div class="flex items-baseline gap-1"><span class="font-headline-sm text-headline-sm text-on-surface [&_del]:text-xs [&_del]:text-outline [&_ins]:no-underline"><?php echo $price; // phpcs:ignore ?></span><?php if ( $p['currency'] && ! $p['price_html'] && '' !== $p['price'] ) : ?><span class="text-body-sm font-body-sm text-outline"><?php echo esc_html( $p['currency'] ); ?></span><?php endif; ?></div>
					</div>
					<button class="w-10 h-10 rounded-full bg-surface-canvas hover:bg-secondary-container text-on-surface flex items-center justify-center transition-colors" type="button" title="<?php esc_attr_e( 'افزودن به علاقه‌مندی', 'larijani' ); ?>" aria-pressed="false" data-ls-wishlist="<?php echo esc_attr( sanitize_title( wp_strip_all_tags( $p['title'] ) ) ); ?>"><i class="bi bi-heart text-[18px]" aria-hidden="true"></i></button>
				</div>
				<?php if ( $p['button_text'] ) : ?>
				<a class="w-full py-2.5 rounded-xl bg-primary text-on-primary hover:bg-primary-container font-label-nav text-label-nav flex items-center justify-center gap-space-xs transition-all shadow-sm <?php echo esc_attr( $p['button_class'] ); ?>" href="<?php echo esc_url( $burl ); ?>" <?php echo $p['button_attrs']; // phpcs:ignore ?>><?php echo ls_icon( $p['button_icon'] ? $p['button_icon'] : 'bi bi-cart-check', 'text-[18px]' ); // phpcs:ignore ?><span><?php echo esc_html( $p['button_text'] ); ?></span></a>
				<?php endif; ?>
			</div>
		</article>
		<?php
	elseif ( 'showcase' === $style ) :
		?>
		<article class="ls-filter-item bg-white rounded-2xl border border-gray-200/90 overflow-hidden shadow-sm hover:shadow-xl transition group flex flex-col"<?php echo $data; // phpcs:ignore ?>>
			<div class="relative h-48 sm:h-52 overflow-hidden bg-gray-100">
				<a href="<?php echo esc_url( $url ); ?>" class="block w-full h-full"><?php echo $img; // phpcs:ignore ?></a>
				<?php if ( $p['badge'] ) : ?>
				<div class="absolute top-3 right-3 flex items-center gap-1.5"><span class="<?php echo esc_attr( ls_tone( $p['badge_tone'], 'badge' ) ); ?> text-[11px] font-bold px-2.5 py-1 rounded-lg shadow-sm"><?php echo esc_html( $p['badge'] ); ?></span></div>
				<?php endif; ?>
				<button aria-label="<?php esc_attr_e( 'نشان کردن محصول', 'larijani' ); ?>" aria-pressed="false" class="absolute top-3 left-3 w-8 h-8 rounded-full bg-white/90 text-gray-600 hover:text-red-500 flex items-center justify-center transition shadow" type="button" data-ls-wishlist="<?php echo esc_attr( sanitize_title( wp_strip_all_tags( $p['title'] ) ) ); ?>"><i class="bi bi-heart text-[14px]" aria-hidden="true"></i></button>
			</div>
			<div class="p-4 flex-1 flex flex-col justify-between">
				<div>
					<h3 class="font-bold text-gray-900 text-sm sm:text-base group-hover:text-primary-container transition"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $p['title'] ); ?></a></h3>
					<?php if ( $p['subtitle'] ) : ?>
					<p class="text-xs text-gray-500 mt-1 flex items-center gap-1"><?php echo ls_icon( $p['subtitle_icon'], 'text-[13px] text-primary-container' ); // phpcs:ignore ?><?php echo esc_html( $p['subtitle'] ); ?></p>
					<?php endif; ?>
					<?php if ( $p['specs'] ) : ?>
					<div class="mt-4 pt-3 border-t border-gray-100 grid grid-cols-3 text-center text-xs text-gray-500">
						<?php foreach ( array_slice( $p['specs'], 0, 3 ) as $i => $sp ) : ?>
						<div class="<?php echo 1 === $i ? 'border-x border-gray-100' : ''; ?>"><span class="block font-bold text-gray-800"><?php echo esc_html( $sp[1] ); ?></span><span class="text-[10px] text-gray-500"><?php echo esc_html( $sp[0] ); ?></span></div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
				</div>
				<div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between gap-2">
					<div>
						<?php if ( $p['price_label'] ) : ?><span class="text-xs text-gray-500 block"><?php echo esc_html( $p['price_label'] ); ?></span><?php endif; ?>
						<span class="text-sm sm:text-base font-black text-primary-container"><?php echo $price; // phpcs:ignore ?> <?php if ( $p['currency'] && ! $p['price_html'] && '' !== $p['price'] ) : ?><span class="text-[10px] sm:text-[11px] font-normal text-gray-500"><?php echo esc_html( $p['currency'] ); ?></span><?php endif; ?></span>
					</div>
					<?php if ( $p['button_text'] ) : ?>
					<a class="px-3.5 py-1.5 rounded-lg bg-[#F4F6F3] text-primary-container font-semibold text-xs hover:bg-primary-container hover:text-white transition text-center max-w-[60%] <?php echo esc_attr( $p['button_class'] ); ?>" href="<?php echo esc_url( $burl ); ?>" <?php echo $p['button_attrs']; // phpcs:ignore ?>><?php echo esc_html( $p['button_text'] ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</article>
		<?php
	elseif ( 'catalog' === $style ) :
		?>
		<article class="ls-filter-item product-item group rounded-2xl bg-surface-card shadow-sm hover:shadow-md transition-all flex flex-col overflow-hidden"<?php echo $data; // phpcs:ignore ?>>
			<a href="<?php echo esc_url( $url ); ?>" class="relative block w-full aspect-[4/3] bg-surface-container-lowest overflow-hidden">
				<?php echo $img; // phpcs:ignore ?>
				<?php if ( $p['badge'] ) : ?>
				<span class="absolute top-3 right-3 px-space-sm py-1 rounded-full <?php echo esc_attr( ls_tone( $p['badge_tone'] ) ); ?> text-body-sm font-bold shadow-sm"><?php echo esc_html( $p['badge'] ); ?></span>
				<?php endif; ?>
				<?php if ( $p['code'] ) : ?>
				<span class="absolute bottom-3 left-3 px-2 py-0.5 rounded-lg bg-surface-dark/80 text-surface-bright text-body-sm"><?php echo esc_html( $p['code'] ); ?></span>
				<?php endif; ?>
			</a>
			<div class="p-space-md flex flex-col flex-1 justify-between gap-space-md">
				<div class="flex flex-col gap-space-xs">
					<h3 class="font-title-card text-title-card text-on-surface leading-snug"><a class="hover:text-primary-container transition-colors" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $p['title'] ); ?></a></h3>
					<?php if ( $p['desc'] ) : ?>
					<p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-2"><?php echo esc_html( $p['desc'] ); ?></p>
					<?php endif; ?>
				</div>
				<?php if ( $p['specs'] ) : ?>
				<div class="grid grid-cols-3 gap-1 py-2 bg-surface-canvas rounded-xl text-center">
					<?php foreach ( array_slice( $p['specs'], 0, 3 ) as $spec ) : ?>
					<div class="flex flex-col px-1">
						<span class="font-body-sm text-body-sm text-outline"><?php echo esc_html( $spec[0] ); ?></span>
						<span class="font-label-nav text-label-nav <?php echo ! empty( $spec[2] ) ? 'text-accent-emerald' : 'text-on-surface'; ?>"><?php echo esc_html( $spec[1] ); ?></span>
					</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
				<div class="flex items-center justify-between pt-space-xs gap-2">
					<div class="flex flex-col">
						<?php if ( $p['price_label'] ) : ?><span class="font-body-sm text-body-sm text-outline"><?php echo esc_html( $p['price_label'] ); ?></span><?php endif; ?>
						<div class="flex items-baseline gap-1">
							<span class="font-headline-sm text-headline-sm text-primary [&_del]:text-xs [&_del]:text-outline [&_ins]:no-underline"><?php echo $price; // phpcs:ignore ?></span>
							<?php if ( $p['currency'] && ! $p['price_html'] && '' !== $p['price'] ) : ?><span class="font-body-sm text-body-sm text-on-surface-variant"><?php echo esc_html( $p['currency'] ); ?></span><?php endif; ?>
						</div>
					</div>
					<div class="flex items-center gap-space-xs">
						<?php if ( $p['chat_button'] ) : ?>
						<a class="w-10 h-10 rounded-full bg-surface-canvas hover:bg-surface-container text-on-surface flex items-center justify-center transition-colors" href="<?php echo esc_url( $p['chat_url'] ? $p['chat_url'] : ls_whatsapp_url( '', wp_strip_all_tags( $p['title'] ) ) ); ?>" target="_blank" rel="noopener" title="<?php esc_attr_e( 'استعلام تیراژ', 'larijani' ); ?>"><i class="bi bi-chat-dots-fill" aria-hidden="true"></i></a>
						<?php endif; ?>
						<?php if ( $p['button_text'] ) : ?>
						<a class="px-space-md py-2 rounded-full <?php echo 'dark' === $p['button_style'] ? 'bg-surface-dark hover:bg-on-surface text-surface-bright' : 'bg-primary-container hover:bg-primary text-on-primary'; ?> text-body-sm font-label-nav flex items-center gap-1 shadow-sm transition-colors text-center min-w-0 <?php echo esc_attr( $p['button_class'] ); ?>" href="<?php echo esc_url( $burl ); ?>" <?php echo $p['button_attrs']; // phpcs:ignore ?>>
							<?php echo ls_icon( $p['button_icon'] ); // phpcs:ignore ?><span><?php echo esc_html( $p['button_text'] ); ?></span>
						</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</article>
		<?php
	elseif ( 'compact' === $style ) :
		?>
		<div class="ls-filter-item bg-surface-card rounded-2xl p-space-sm shadow-sm flex flex-col justify-between group hover:shadow-md transition-shadow"<?php echo $data; // phpcs:ignore ?>>
			<div>
				<a href="<?php echo esc_url( $url ); ?>" class="block w-full aspect-[4/3] rounded-xl overflow-hidden bg-surface-canvas relative mb-space-sm">
					<?php echo $img; // phpcs:ignore ?>
					<?php if ( $p['badge'] ) : ?>
					<span class="absolute top-2 right-2 px-2.5 py-1 rounded-full bg-surface-card/90 font-label-badge text-label-badge text-on-surface font-bold backdrop-blur-sm"><?php echo esc_html( $p['badge'] ); ?></span>
					<?php endif; ?>
				</a>
				<h3 class="font-title-card text-title-card text-on-surface font-black group-hover:text-primary-container transition-colors"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $p['title'] ); ?></a></h3>
				<?php if ( $p['desc'] ) : ?>
				<p class="font-body-sm text-body-sm text-on-surface-variant mt-1 line-clamp-2"><?php echo esc_html( $p['desc'] ); ?></p>
				<?php endif; ?>
			</div>
			<div class="mt-space-md pt-space-sm border-t border-slate-100 flex items-center justify-between">
				<div class="flex flex-col">
					<?php if ( $p['price_label'] ) : ?><span class="font-body-sm text-body-sm text-on-surface-variant"><?php echo esc_html( $p['price_label'] ); ?></span><?php endif; ?>
					<span class="font-headline-sm text-headline-sm text-on-surface font-black [&_del]:text-xs [&_del]:text-outline [&_ins]:no-underline"><?php echo $price; // phpcs:ignore ?> <?php if ( $p['currency'] && ! $p['price_html'] && '' !== $p['price'] ) : ?><span class="text-body-sm font-normal"><?php echo esc_html( $p['currency'] ); ?></span><?php endif; ?></span>
				</div>
				<a aria-label="<?php echo esc_attr( $p['button_text'] ? $p['button_text'] : __( 'خرید', 'larijani' ) ); ?>" class="w-10 h-10 rounded-full bg-surface-canvas hover:bg-primary-container hover:text-on-primary flex items-center justify-center text-on-surface transition-colors shadow-sm <?php echo esc_attr( $p['button_class'] ); ?>" href="<?php echo esc_url( $burl ); ?>" <?php echo $p['button_attrs']; // phpcs:ignore ?>>
					<?php echo ls_icon( $p['button_icon'] ? $p['button_icon'] : 'bi bi-cart-plus' ); // phpcs:ignore ?>
				</a>
			</div>
		</div>
		<?php
	else :
		?>
		<div class="ls-filter-item bg-white rounded-2xl border border-border-subtle overflow-hidden card-shadow card-shadow-hover transition-all duration-300 flex flex-col group"<?php echo $data; // phpcs:ignore ?>>
			<a href="<?php echo esc_url( $url ); ?>" class="relative block overflow-hidden aspect-[4/3]">
				<?php echo $img; // phpcs:ignore ?>
				<?php if ( $p['badge'] ) : ?>
				<span class="absolute top-3 right-3 <?php echo esc_attr( ls_tone( $p['badge_tone'] ) ); ?> text-[11px] font-bold px-3 py-1 rounded-full shadow"><?php echo esc_html( $p['badge'] ); ?></span>
				<?php endif; ?>
			</a>
			<div class="p-4 sm:p-5 flex-1 flex flex-col justify-between">
				<div>
					<?php if ( $p['category'] ) : ?><span class="text-[11px] font-bold text-slate-500"><?php echo esc_html( $p['category'] ); ?></span><?php endif; ?>
					<h3 class="text-sm sm:text-base font-black text-surface-dark mt-1 group-hover:text-primary-container transition-colors"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $p['title'] ); ?></a></h3>
					<?php if ( $p['subtitle'] ) : ?>
					<p class="text-xs text-slate-500 mt-1.5 flex items-center gap-1.5"><?php echo ls_icon( $p['subtitle_icon'], 'text-primary-container' ); // phpcs:ignore ?><span class="truncate"><?php echo esc_html( $p['subtitle'] ); ?></span></p>
					<?php endif; ?>
					<?php if ( $p['specs'] ) : ?>
					<div class="grid grid-cols-3 gap-1 sm:gap-2 border-y border-slate-100 py-3 my-3 text-[10px] sm:text-[11px] text-slate-600 text-center font-medium">
						<?php foreach ( array_slice( $p['specs'], 0, 3 ) as $spec ) : ?>
						<div><?php echo esc_html( trim( $spec[0] . ( '' !== $spec[0] && '' !== $spec[1] ? ': ' : '' ) . $spec[1] ) ); ?></div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
				</div>
				<div class="flex items-center justify-between pt-2">
					<div>
						<?php if ( $p['price_label'] ) : ?><div class="text-[10px] sm:text-[11px] text-slate-500"><?php echo esc_html( $p['price_label'] ); ?></div><?php endif; ?>
						<div class="text-xs sm:text-sm font-black text-surface-dark [&_del]:text-[10px] [&_del]:text-slate-500 [&_ins]:no-underline"><?php echo $price; // phpcs:ignore ?> <?php if ( $p['currency'] && ! $p['price_html'] && '' !== $p['price'] ) : ?><span class="text-[10px] text-slate-500"><?php echo esc_html( $p['currency'] ); ?></span><?php endif; ?></div>
					</div>
					<a aria-label="<?php echo esc_attr( $p['button_text'] ? $p['button_text'] : __( 'مشاهده مشخصات', 'larijani' ) ); ?>" class="w-9 h-9 rounded-xl bg-surface-canvas text-primary-container hover:bg-primary-container hover:text-white border border-border-subtle flex items-center justify-center transition-colors <?php echo esc_attr( $p['button_class'] ); ?>" href="<?php echo esc_url( $burl ); ?>" <?php echo $p['button_attrs']; // phpcs:ignore ?>>
						<?php echo ls_icon( $p['button_icon'] ? $p['button_icon'] : 'bi bi-arrow-left' ); // phpcs:ignore ?>
					</a>
				</div>
			</div>
		</div>
		<?php
	endif;
	return ob_get_clean();
}

/**
 * Portfolio project card.
 *
 * @param array $p { image, title, desc, badge_1, badge_2, badge_2_tone, location, code, specs[], note, note_icon, note_tone, url, filter }.
 * @return string
 */
function ls_project_card( $p ) {
	$p = wp_parse_args(
		$p,
		array(
			'image'        => '',
			'title'        => '',
			'desc'         => '',
			'badge_1'      => '',
			'badge_2'      => '',
			'badge_2_tone' => 'dark',
			'location'     => '',
			'code'         => '',
			'specs'        => array(),
			'note'         => '',
			'note_icon'    => 'bi bi-patch-check-fill',
			'note_tone'    => 'emerald',
			'url'          => '',
			'filter'       => '',
		)
	);
	$b2 = array(
		'dark'    => 'bg-surface-dark/80 text-on-tertiary',
		'sage'    => 'bg-secondary-container/90 text-on-secondary-container',
		'primary' => 'bg-primary-container/90 text-on-primary',
		'fixed'   => 'bg-secondary-fixed/90 text-on-secondary-fixed',
	);
	ob_start();
	?>
	<article class="ls-filter-item project-card group bg-surface-card rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden" data-category="<?php echo esc_attr( $p['filter'] ); ?>">
		<div class="relative w-full aspect-[4/3] bg-surface-container overflow-hidden">
			<?php echo ls_img( $p['image'], 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-105', wp_strip_all_tags( $p['title'] ), 'ls-card' ); // phpcs:ignore ?>
			<div class="absolute inset-0 bg-gradient-to-t from-surface-dark/80 via-transparent to-transparent opacity-90"></div>
			<?php if ( $p['badge_1'] || $p['badge_2'] ) : ?>
			<div class="absolute top-3 right-3 flex flex-wrap gap-1.5">
				<?php if ( $p['badge_1'] ) : ?><span class="px-2.5 py-1 rounded-full bg-surface-card/90 backdrop-blur text-primary text-label-badge font-label-badge shadow-sm"><?php echo esc_html( $p['badge_1'] ); ?></span><?php endif; ?>
				<?php if ( $p['badge_2'] ) : ?><span class="px-2.5 py-1 rounded-full <?php echo esc_attr( $b2[ $p['badge_2_tone'] ] ?? $b2['dark'] ); ?> backdrop-blur text-label-badge font-label-badge"><?php echo esc_html( $p['badge_2'] ); ?></span><?php endif; ?>
			</div>
			<?php endif; ?>
			<div class="absolute bottom-3 right-3 left-3 text-on-tertiary flex items-center justify-between gap-2">
				<?php if ( $p['location'] ) : ?>
				<div class="flex items-center gap-1.5 min-w-0"><i class="bi bi-geo-alt-fill text-[14px] text-primary-fixed" aria-hidden="true"></i><span class="font-body-sm text-body-sm truncate"><?php echo esc_html( $p['location'] ); ?></span></div>
				<?php endif; ?>
				<?php if ( $p['code'] ) : ?>
				<span class="font-label-badge text-label-badge bg-primary-container/80 px-2 py-0.5 rounded text-on-primary whitespace-nowrap"><?php echo esc_html( $p['code'] ); ?></span>
				<?php endif; ?>
			</div>
		</div>
		<div class="p-space-lg flex flex-col flex-1 justify-between gap-space-md">
			<div class="flex flex-col gap-space-xs">
				<h2 class="text-headline-sm font-headline-sm text-surface-dark group-hover:text-primary transition-colors"><?php echo $p['url'] ? '<a href="' . esc_url( $p['url'] ) . '">' . esc_html( $p['title'] ) . '</a>' : esc_html( $p['title'] ); ?></h2>
				<?php if ( $p['desc'] ) : ?><p class="text-body-md font-body-md text-tertiary line-clamp-2"><?php echo esc_html( $p['desc'] ); ?></p><?php endif; ?>
			</div>
			<?php if ( $p['specs'] ) : ?>
			<div class="grid grid-cols-3 gap-2 bg-surface-canvas p-space-sm rounded-xl text-center">
				<?php foreach ( array_slice( $p['specs'], 0, 3 ) as $spec ) : ?>
				<div class="flex flex-col">
					<span class="text-body-sm font-body-sm text-outline"><?php echo esc_html( $spec[0] ); ?></span>
					<span class="text-label-nav font-label-nav text-surface-dark"><?php echo esc_html( $spec[1] ); ?></span>
				</div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
			<div class="flex items-center justify-between pt-space-xs">
				<span class="text-body-sm font-body-sm <?php echo 'emerald' === $p['note_tone'] ? 'text-accent-emerald' : 'text-tertiary'; ?> flex items-center gap-1">
					<?php echo ls_icon( $p['note_icon'], 'text-[16px]' . ( 'emerald' === $p['note_tone'] ? '' : ' text-secondary' ) ); // phpcs:ignore ?>
					<?php echo esc_html( $p['note'] ); ?>
				</span>
				<?php if ( $p['url'] ) : ?>
				<a class="w-9 h-9 rounded-full bg-surface-canvas hover:bg-primary-container hover:text-on-primary text-surface-dark flex items-center justify-center transition-colors" href="<?php echo esc_url( $p['url'] ); ?>" aria-label="<?php esc_attr_e( 'مشاهده پروژه', 'larijani' ); ?>"><i class="bi bi-arrow-left text-[16px]" aria-hidden="true"></i></a>
				<?php else : ?>
				<span class="w-9 h-9 rounded-full bg-surface-canvas text-surface-dark flex items-center justify-center"><i class="bi bi-arrow-left text-[16px]" aria-hidden="true"></i></span>
				<?php endif; ?>
			</div>
		</div>
	</article>
	<?php
	return ob_get_clean();
}

/**
 * Project card data from a ls_project post.
 *
 * @param WP_Post|int $post Post.
 * @return array
 */
function ls_project_data( $post ) {
	$post  = get_post( $post );
	$m     = static function ( $k ) use ( $post ) {
		return get_post_meta( $post->ID, '_ls_' . $k, true );
	};
	$specs = array();
	for ( $i = 1; $i <= 3; $i++ ) {
		if ( $m( "spec_{$i}_label" ) || $m( "spec_{$i}_value" ) ) {
			$specs[] = array( $m( "spec_{$i}_label" ), $m( "spec_{$i}_value" ) );
		}
	}
	$terms = get_the_terms( $post, 'ls_project_cat' );
	return array(
		'image'    => ls_post_image_url( $post, 'ls-card' ),
		'title'    => get_the_title( $post ),
		'desc'     => has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 30 ),
		'badge_1'  => $m( 'badge_1' ),
		'badge_2'  => $m( 'badge_2' ),
		'location' => $m( 'location' ),
		'code'     => $m( 'code' ),
		'specs'    => $specs,
		'note'     => $m( 'note' ),
		'note_icon' => $m( 'note_icon' ) ? $m( 'note_icon' ) : 'bi bi-patch-check-fill',
		'url'      => get_permalink( $post ),
		'filter'   => $terms && ! is_wp_error( $terms ) ? implode( ' ', wp_list_pluck( $terms, 'slug' ) ) : '',
	);
}

/**
 * Post card data from a WP_Post.
 *
 * @param WP_Post|int $post Post.
 * @return array
 */
function ls_post_data( $post ) {
	$post = get_post( $post );
	$cat  = ls_primary_category( $post->ID );
	return array(
		'image'    => ls_post_image_url( $post, 'ls-card' ),
		'title'    => get_the_title( $post ),
		'excerpt'  => has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 26 ),
		'category' => $cat ? $cat->name : '',
		'date'     => ls_post_date( $post->ID ),
		'reading'  => ls_reading_time( $post ),
		'url'      => get_permalink( $post ),
		'badge_tone' => 'dark',
	);
}

/**
 * Post card.
 *
 * @param array  $p     { image, title, excerpt, category, date, reading, url, badge_tone }.
 * @param string $style home | archive | related.
 * @param array  $o     Options { read_more }.
 * @return string
 */
function ls_post_card( $p, $style = 'archive', $o = array() ) {
	$o   = wp_parse_args( $o, array( 'read_more' => __( 'ادامه مطلب تخصصی', 'larijani' ) ) );
	$url = ! empty( $p['url'] ) ? $p['url'] : '#';
	/* translators: %s minutes */
	$reading = ! empty( $p['reading'] ) ? sprintf( __( '%s دقیقه مطالعه', 'larijani' ), ls_fa_num( $p['reading'] ) ) : '';
	ob_start();
	if ( 'home' === $style ) :
		?>
		<article class="flex flex-col bg-surface-canvas rounded-2xl border border-border-subtle overflow-hidden group hover:bg-white transition-all">
			<a href="<?php echo esc_url( $url ); ?>" class="block aspect-[16/9] overflow-hidden"><?php echo ls_img( $p['image'], 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500', $p['title'], 'ls-card' ); // phpcs:ignore ?></a>
			<div class="p-4 sm:p-5 flex-1 flex flex-col justify-between">
				<div>
					<?php if ( ! empty( $p['category'] ) ) : ?><span class="text-[11px] font-bold text-primary-container"><?php echo esc_html( $p['category'] ); ?></span><?php endif; ?>
					<h3 class="text-sm sm:text-base font-black text-surface-dark mt-2 group-hover:text-primary-container transition-colors"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $p['title'] ); ?></a></h3>
					<?php if ( ! empty( $p['excerpt'] ) ) : ?><p class="text-xs text-slate-500 mt-2 leading-relaxed"><?php echo esc_html( $p['excerpt'] ); ?></p><?php endif; ?>
				</div>
				<div class="pt-4 mt-4 border-t border-slate-200/70 flex items-center justify-between text-xs font-semibold text-slate-500">
					<span><?php echo esc_html( $p['date'] ?? '' ); ?></span>
					<a class="text-primary-container" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $o['read_more'] ); ?> ←</a>
				</div>
			</div>
		</article>
		<?php
	elseif ( 'related' === $style ) :
		?>
		<a href="<?php echo esc_url( $url ); ?>" class="bg-surface-card rounded-2xl overflow-hidden shadow-sm flex flex-col justify-between group hover:shadow-md transition-shadow">
			<div class="aspect-[16/10] overflow-hidden"><?php echo ls_img( $p['image'], 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-300', $p['title'], 'ls-card' ); // phpcs:ignore ?></div>
			<div class="p-5 flex flex-col flex-1 justify-between gap-3">
				<div class="space-y-2">
					<?php if ( ! empty( $p['category'] ) ) : ?><span class="font-label-badge text-label-badge px-2.5 py-1 rounded bg-secondary-container text-on-secondary-container inline-block"><?php echo esc_html( $p['category'] ); ?></span><?php endif; ?>
					<h4 class="font-headline-sm text-title-card text-surface-dark font-bold leading-snug line-clamp-2"><?php echo esc_html( $p['title'] ); ?></h4>
				</div>
				<div class="flex items-center justify-between text-on-surface-variant font-body-sm text-body-sm pt-2">
					<span><?php echo esc_html( $reading ); ?></span>
					<i class="bi bi-chevron-left text-primary-container" aria-hidden="true"></i>
				</div>
			</div>
		</a>
		<?php
	else :
		?>
		<article class="bg-surface-card rounded-2xl shadow-sm overflow-hidden flex flex-col justify-between group hover:shadow-md transition-all duration-300">
			<div>
				<a href="<?php echo esc_url( $url ); ?>" class="relative block w-full aspect-[16/10] overflow-hidden">
					<?php echo ls_img( $p['image'], 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500', $p['title'], 'ls-card' ); // phpcs:ignore ?>
					<?php if ( ! empty( $p['category'] ) ) : ?>
					<span class="absolute top-3 right-3 px-3 py-1 rounded-full <?php echo 'amber' === ( $p['badge_tone'] ?? '' ) ? 'bg-accent-amber/90' : 'bg-surface-dark/80'; ?> backdrop-blur-md text-white font-label-badge text-label-badge"><?php echo esc_html( $p['category'] ); ?></span>
					<?php endif; ?>
				</a>
				<div class="p-5">
					<div class="flex items-center gap-2 text-outline text-xs mb-2.5">
						<span><?php echo esc_html( $p['date'] ?? '' ); ?></span>
						<?php if ( $reading ) : ?><span>•</span><span><?php echo esc_html( $reading ); ?></span><?php endif; ?>
					</div>
					<h3 class="font-headline-sm text-headline-sm text-on-surface mb-2.5 line-clamp-2 group-hover:text-primary transition-colors"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $p['title'] ); ?></a></h3>
					<?php if ( ! empty( $p['excerpt'] ) ) : ?><p class="font-body-md text-body-md text-on-surface-variant line-clamp-2 leading-relaxed"><?php echo esc_html( $p['excerpt'] ); ?></p><?php endif; ?>
				</div>
			</div>
			<div class="p-5 pt-0">
				<a class="inline-flex items-center gap-2 font-headline-sm text-headline-sm text-primary group-hover:text-primary-container transition-colors" href="<?php echo esc_url( $url ); ?>">
					<span><?php echo esc_html( $o['read_more'] ); ?></span><i class="bi bi-arrow-left transition-transform group-hover:-translate-x-1" aria-hidden="true"></i>
				</a>
			</div>
		</article>
		<?php
	endif;
	return ob_get_clean();
}

/**
 * Generic filter bar (chips) used by portfolio / catalog grids (client-side filtering).
 *
 * @param array  $filters [ [key, label, icon], ... ].
 * @param string $style   pill | chip.
 * @return string
 */
function ls_filter_buttons( $filters, $style = 'pill' ) {
	$out = '';
	foreach ( $filters as $i => $f ) {
		$active   = 0 === $i;
		$base     = 'pill' === $style ? 'px-space-md py-2 rounded-full font-label-nav text-label-nav transition-all whitespace-nowrap' : 'px-space-md py-2 rounded-xl text-body-sm font-semibold transition-all whitespace-nowrap';
		$on       = 'bg-primary-container text-on-primary shadow-sm';
		$off      = 'pill' === $style ? 'bg-surface-canvas text-tertiary hover:text-on-surface hover:bg-surface-container' : 'bg-surface-canvas text-on-surface-variant hover:text-on-surface hover:bg-surface-container';
		if ( 'store' === $style ) {
			$base = 'px-space-lg py-2 rounded-full font-label-nav text-label-nav transition-all shrink-0 whitespace-nowrap inline-flex items-center gap-1';
			$on   = 'bg-primary text-on-primary shadow-sm';
			$off  = 'bg-surface-card hover:bg-surface-container-high text-on-surface-variant';
		}
		if ( 'bi bi-dot-pulse' === ( $f[2] ?? '' ) ) {
			$f[2] = '';
			$dot  = '<span class="w-2 h-2 rounded-full bg-accent-emerald animate-pulse"></span>';
		} else {
			$dot = '';
		}
		$out     .= sprintf(
			'<button type="button" class="%s %s" data-ls-filter="%s" data-on="%s" data-off="%s" aria-pressed="%s">%s%s</button>',
			esc_attr( $base ),
			esc_attr( $active ? $on : $off ),
			esc_attr( $f[0] ),
			esc_attr( $on ),
			esc_attr( $off ),
			$active ? 'true' : 'false',
			$dot . ( ! empty( $f[2] ) ? ls_icon( $f[2], 'ml-1' ) . ' ' : '' ),
			esc_html( $f[1] )
		);
	}
	return $out;
}
