<?php
/**
 * Shop renderers: catalog (archive) layout, single product showcase & tabs.
 * Every renderer accepts normalised arrays so it works with WooCommerce data
 * or with static content typed in Elementor (catalog sites without a cart).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Card data for a WooCommerce product.
 *
 * @param WC_Product $product Product.
 * @param array      $o Options { style, button_text, price_label }.
 * @return array
 */
function ls_wc_card_data( $product, $o = array() ) {
	$o     = wp_parse_args( $o, array( 'button_text' => '', 'price_label' => __( 'قیمت واحد', 'larijani' ), 'category_label' => true ) );
	$id    = $product->get_id();
	$cats  = wc_get_product_terms( $id, 'product_cat', array( 'fields' => 'all' ) );
	$specs = array();
	foreach ( $product->get_attributes() as $attr ) {
		if ( ! $attr->get_visible() ) {
			continue;
		}
		$values  = $attr->is_taxonomy() ? wc_get_product_terms( $id, $attr->get_name(), array( 'fields' => 'names' ) ) : $attr->get_options();
		$specs[] = array( wc_attribute_label( $attr->get_name() ), implode( '، ', $values ), false );
		if ( count( $specs ) >= 3 ) {
			break;
		}
	}
	$badge      = get_post_meta( $id, '_ls_badge', true );
	$badge_tone = get_post_meta( $id, '_ls_badge_tone', true );
	if ( ! $badge && $product->is_on_sale() ) {
		$badge      = __( 'فروش ویژه', 'larijani' );
		$badge_tone = 'amber';
	} elseif ( ! $badge && $product->is_featured() ) {
		$badge = __( 'پرفروش', 'larijani' );
	}
	$purchasable = $product->is_purchasable() && $product->is_in_stock() && $product->is_type( 'simple' ) && '' !== $product->get_price();
	$button_text = $o['button_text'] ? $o['button_text'] : ( $purchasable ? __( 'افزودن به سبد', 'larijani' ) : __( 'استعلام و سفارش', 'larijani' ) );
	$attrs       = '';
	$burl        = $product->get_permalink();
	if ( $purchasable ) {
		$burl  = $product->add_to_cart_url();
		$attrs = sprintf( 'data-quantity="1" data-product_id="%d" data-product_sku="%s" rel="nofollow"', $id, esc_attr( $product->get_sku() ) );
	}
	$filter = $cats ? implode( ' ', wp_list_pluck( $cats, 'slug' ) ) : '';
	return array(
		'image'        => wp_get_attachment_image_url( $product->get_image_id(), 'ls-card' ),
		'badge'        => $badge,
		'badge_tone'   => $badge_tone ? $badge_tone : 'primary',
		'category'     => $cats ? $cats[0]->name : '',
		'title'        => $product->get_name(),
		'subtitle'     => get_post_meta( $id, '_ls_subtitle', true ),
		'desc'         => wp_trim_words( wp_strip_all_tags( $product->get_short_description() ? $product->get_short_description() : $product->get_description() ), 20 ),
		'code'         => $product->get_sku() ? sprintf( /* translators: %s sku */ __( 'کد: %s', 'larijani' ), $product->get_sku() ) : '',
		'specs'        => $specs,
		'price_label'  => get_post_meta( $id, '_ls_price_label', true ) ? get_post_meta( $id, '_ls_price_label', true ) : $o['price_label'],
		'price_html'   => '' !== $product->get_price() ? wc_price( wc_get_price_to_display( $product ), array( 'decimals' => 0 ) ) : '<span class="text-sm">' . esc_html__( 'تماس بگیرید', 'larijani' ) . '</span>',
		'url'          => $product->get_permalink(),
		'button_text'  => $button_text,
		'button_style' => $purchasable ? 'primary' : 'dark',
		'button_icon'  => $purchasable ? 'bi bi-cart-plus-fill' : 'bi bi-telephone-outbound-fill',
		'button_url'   => $burl,
		'button_attrs' => $attrs,
		'button_class' => $purchasable ? 'add_to_cart_button ajax_add_to_cart product_type_simple' : '',
		'filter'       => $filter,
		'price_raw'    => (float) $product->get_price(),
	);
}

/**
 * Catalog layout (search, sort, category chips, sidebar filters, grid, pagination).
 *
 * @param array $s Settings.
 */
function ls_render_catalog( $s = array() ) {
	$s = wp_parse_args(
		$s,
		array(
			'source'           => ls_has_woo() ? 'woocommerce' : 'manual',
			'items'            => array(),
			'chips'            => array(),
			'use_main_query'   => 'auto',
			'posts_per_page'   => 12,
			'category'         => '',
			'search_placeholder' => __( 'جستجو در نام قالب، ابعاد، میکسر یا مواد...', 'larijani' ),
			'sort_label'       => __( 'مرتب‌سازی:', 'larijani' ),
			'all_label'        => __( 'همه محصولات', 'larijani' ),
			'show_sidebar'     => 'yes',
			'filter_boxes'     => array(),
			'show_price'       => 'yes',
			'price_title'      => __( 'محدوده قیمت', 'larijani' ),
			'advisory_title'   => __( 'پیشنهاد راه‌اندازی', 'larijani' ),
			'advisory_text'    => __( 'برای راه‌اندازی کارگاه سنگ مصنوعی در متراژ ۱۵۰ متر، بسته شامل ۲۵۰ قالب ABS، میز ویبره ۲×۱ و میکسر ۵۰۰ کیلویی اقتصادی‌ترین گزینه تولید است.', 'larijani' ),
			'advisory_link_text' => __( 'دریافت پکیج جامع خط تولید', 'larijani' ),
			'advisory_link'    => '',
			'columns'          => 3,
			/* translators: 1: first item number, 2: last item number, 3: total items */
			'count_text'       => __( 'نمایش %1$s تا %2$s از %3$s قلم کالا و تجهیزات سنگ مصنوعی', 'larijani' ),
			'layout'           => 'sidebar', // sidebar | store (design: full-width 4-column shop).
			'card_style'       => '',
			'results_label'    => __( 'تعداد نتایج:', 'larijani' ),
			'results_suffix'   => __( 'قلم کالا', 'larijani' ),
			'footer_note'      => '',
		)
	);
	$store = 'store' === $s['layout'];
	if ( $store ) {
		$s['show_sidebar'] = '';
	}
	$card_style = $s['card_style'] ? $s['card_style'] : ( $store ? 'store' : 'catalog' );

	$is_woo = 'woocommerce' === $s['source'] && ls_has_woo();
	$cards  = array();
	$query  = null;
	$chips  = array();
	$total  = 0;

	if ( $is_woo ) {
		$use_main = 'yes' === $s['use_main_query'] || ( 'auto' === $s['use_main_query'] && ( is_shop() || is_product_taxonomy() || ( is_search() && 'product' === get_query_var( 'post_type' ) ) ) );
		if ( $use_main ) {
			global $wp_query;
			$query = $wp_query;
		} else {
			$args = array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => (int) $s['posts_per_page'],
				'paged'          => max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) ),
			);
			if ( $s['category'] ) {
				$args['tax_query'] = array( array( 'taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => (int) $s['category'] ) ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			}
			$ordering = WC()->query->get_catalog_ordering_args();
			$args     = array_merge( $args, array_filter( array( 'orderby' => $ordering['orderby'], 'order' => $ordering['order'] ) ) );
			if ( ! empty( $ordering['meta_key'] ) ) {
				$args['meta_key'] = $ordering['meta_key']; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			}
			$query = new WP_Query( $args );
		}
		while ( $query->have_posts() ) {
			$query->the_post();
			$product = wc_get_product( get_the_ID() );
			if ( $product ) {
				$cards[] = ls_wc_card_data( $product );
			}
		}
		wp_reset_postdata();
		$total = (int) $query->found_posts;

		$shop_url = get_permalink( wc_get_page_id( 'shop' ) );
		$current  = is_product_category() ? get_queried_object_id() : 0;
		$chips[]  = array( 'url' => $shop_url, 'label' => sprintf( '%s (%s)', $s['all_label'], ls_fa_num( wp_count_posts( 'product' )->publish ) ), 'icon' => 'bi bi-boxes', 'active' => ! $current );
		$terms    = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => 0, 'hide_empty' => true, 'exclude' => array( (int) get_option( 'default_product_cat' ) ) ) );
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $t ) {
				$icon    = get_term_meta( $t->term_id, 'ls_icon', true );
				$chips[] = array( 'url' => get_term_link( $t ), 'label' => $t->name, 'icon' => $icon ? $icon : 'bi bi-bounding-box-circles', 'active' => $current === $t->term_id );
			}
		}
	} else {
		foreach ( $s['items'] as $it ) {
			$cards[] = $it;
		}
		$total = count( $cards );
	}

	$orderby_now = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : 'popularity'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$sorts       = array(
		'popularity' => __( 'پرفروش‌ترین', 'larijani' ),
		'date'       => __( 'جدیدترین', 'larijani' ),
		'price'      => __( 'ارزان‌ترین', 'larijani' ),
		'price-desc' => __( 'گران‌ترین', 'larijani' ),
	);
	$cols = array( 2 => 'xl:grid-cols-2', 3 => 'xl:grid-cols-3', 4 => 'xl:grid-cols-4' );
	?>
	<div class="ls-catalog" data-ls-catalog="<?php echo $is_woo ? 'server' : 'client'; ?>">
		<h2 class="screen-reader-text"><?php esc_html_e( 'فهرست محصولات', 'larijani' ); ?></h2>
		<?php if ( $store ) : ?>
		<div class="bg-surface-card p-space-md rounded-2xl shadow-sm mb-space-lg flex flex-col md:flex-row items-center justify-between gap-space-md">
			<form class="relative w-full md:w-96" role="search" method="get" action="<?php echo esc_url( $is_woo ? get_permalink( wc_get_page_id( 'shop' ) ) : '' ); ?>" data-ls-catalog-search>
				<?php if ( $is_woo ) : ?><input type="hidden" name="post_type" value="product"><?php endif; ?>
				<label class="screen-reader-text" for="ls-catalog-s"><?php esc_html_e( 'جستجو', 'larijani' ); ?></label>
				<i class="bi bi-search absolute right-3 top-1/2 -translate-y-1/2 text-outline" aria-hidden="true"></i>
				<input id="ls-catalog-s" class="w-full bg-surface-canvas text-on-surface pr-10 pl-4 py-2.5 rounded-xl font-body-md text-body-md placeholder:text-outline focus:outline-none focus:bg-surface-container-high transition-all" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php echo esc_attr( $s['search_placeholder'] ); ?>" type="search" data-ls-search-input>
			</form>
			<div class="flex flex-wrap items-center justify-between md:justify-end gap-space-md w-full md:w-auto min-w-0">
				<div class="flex items-center gap-space-xs font-body-sm text-body-sm text-outline">
					<span><?php echo esc_html( $s['results_label'] ); ?></span>
					<span class="font-bold text-on-surface text-label-nav" data-ls-filter-count><?php echo esc_html( ls_fa_num( $total ) . ' ' . $s['results_suffix'] ); ?></span>
				</div>
				<form class="flex items-center gap-space-xs min-w-0 max-w-full" method="get">
					<i class="bi bi-filter-right text-outline text-[20px]" aria-hidden="true"></i>
					<label class="screen-reader-text" for="ls-catalog-sort"><?php echo esc_html( $s['sort_label'] ); ?></label>
					<select id="ls-catalog-sort" class="min-w-0 max-w-full bg-surface-canvas text-on-surface px-space-md py-2 rounded-xl font-label-nav text-label-nav focus:outline-none" name="orderby" <?php echo $is_woo ? 'data-ls-autosubmit' : 'data-ls-sort-select'; ?>>
						<?php
						$store_sorts = array(
							'popularity' => __( 'پیشنهاد کارخانه (پرفروش‌ترین)', 'larijani' ),
							'price'      => __( 'ارزان‌ترین متریال', 'larijani' ),
							'price-desc' => __( 'گران‌ترین ماشین‌آلات', 'larijani' ),
							$is_woo ? 'date' : 'title' => $is_woo ? __( 'جدیدترین محصولات', 'larijani' ) : __( 'بر اساس نام محصول', 'larijani' ),
						);
						foreach ( $store_sorts as $key => $label ) :
							?>
						<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $orderby_now, $key ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php foreach ( array( 's', 'post_type' ) as $keep ) : ?>
						<?php if ( isset( $_GET[ $keep ] ) ) : // phpcs:ignore ?><input type="hidden" name="<?php echo esc_attr( $keep ); ?>" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_GET[ $keep ] ) ) ); // phpcs:ignore ?>"><?php endif; ?>
					<?php endforeach; ?>
				</form>
			</div>
		</div>
		<div class="flex items-center gap-space-xs overflow-x-auto pb-space-sm mb-space-lg scrollbar-none">
			<?php if ( $is_woo ) : ?>
				<?php foreach ( $chips as $c ) : ?>
				<a class="px-space-lg py-2 rounded-full font-label-nav text-label-nav transition-all shrink-0 whitespace-nowrap <?php echo $c['active'] ? 'bg-primary text-on-primary shadow-sm' : 'bg-surface-card hover:bg-surface-container-high text-on-surface-variant'; ?>" href="<?php echo esc_url( $c['url'] ); ?>"><?php echo esc_html( $c['label'] ); ?></a>
				<?php endforeach; ?>
			<?php else : ?>
				<?php
				$filters = array( array( 'all', sprintf( '%s (%s)', $s['all_label'], ls_fa_num( count( $cards ) ) ), '' ) );
				foreach ( $s['chips'] as $c ) {
					$filters[] = array( $c['key'] ?? '', $c['label'] ?? '', is_array( $c['icon'] ?? '' ) ? ( $c['icon']['value'] ?? '' ) : ( $c['icon'] ?? '' ) );
				}
				echo ls_filter_buttons( $filters, 'store' ); // phpcs:ignore
				?>
			<?php endif; ?>
		</div>
		<?php else : ?>
		<div class="p-space-md rounded-2xl bg-surface-card shadow-sm mb-space-lg flex flex-col gap-space-md">
			<div class="flex flex-col md:flex-row items-center justify-between gap-space-md">
				<form class="relative w-full md:w-96" role="search" method="get" action="<?php echo esc_url( $is_woo ? get_permalink( wc_get_page_id( 'shop' ) ) : '' ); ?>" data-ls-catalog-search>
					<?php if ( $is_woo ) : ?><input type="hidden" name="post_type" value="product"><?php endif; ?>
					<label class="screen-reader-text" for="ls-catalog-s"><?php esc_html_e( 'جستجو', 'larijani' ); ?></label>
					<input id="ls-catalog-s" class="w-full pr-11 pl-4 py-2.5 rounded-xl bg-surface-canvas text-on-surface font-body-md text-body-md focus:bg-surface-card transition-all placeholder:text-outline" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php echo esc_attr( $s['search_placeholder'] ); ?>" type="search" data-ls-search-input>
					<i class="bi bi-search absolute right-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant text-base" aria-hidden="true"></i>
				</form>
				<div class="flex items-center justify-between w-full md:w-auto gap-space-sm">
					<span class="font-body-sm text-body-sm text-on-surface-variant whitespace-nowrap"><?php echo esc_html( $s['sort_label'] ); ?></span>
					<div class="flex items-center gap-space-xs bg-surface-canvas p-1 rounded-xl overflow-x-auto scrollbar-none">
						<?php foreach ( $sorts as $key => $label ) : ?>
							<?php $on = $orderby_now === $key; ?>
							<?php if ( $is_woo ) : ?>
							<a class="px-space-sm py-1 rounded-lg text-body-sm font-label-nav whitespace-nowrap <?php echo $on ? 'bg-primary-container text-on-primary' : 'text-on-surface-variant hover:text-on-surface'; ?> transition-colors" href="<?php echo esc_url( add_query_arg( 'orderby', $key ) ); ?>"><?php echo esc_html( $label ); ?></a>
							<?php else : ?>
							<button type="button" class="px-space-sm py-1 rounded-lg text-body-sm font-label-nav whitespace-nowrap <?php echo 'popularity' === $key ? 'bg-primary-container text-on-primary' : 'text-on-surface-variant hover:text-on-surface'; ?> transition-colors" data-ls-sort="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></button>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<div class="flex items-center gap-space-xs overflow-x-auto pb-1 text-nowrap scrollbar-none">
				<?php if ( $is_woo ) : ?>
					<?php foreach ( $chips as $c ) : ?>
					<a class="px-space-md py-2 rounded-xl text-body-sm font-semibold transition-all whitespace-nowrap <?php echo $c['active'] ? 'bg-primary-container text-on-primary shadow-sm' : 'bg-surface-canvas text-on-surface-variant hover:text-on-surface hover:bg-surface-container'; ?>" href="<?php echo esc_url( $c['url'] ); ?>"><?php echo ls_icon( $c['icon'], 'ml-1' ); // phpcs:ignore ?> <?php echo esc_html( $c['label'] ); ?></a>
					<?php endforeach; ?>
				<?php else : ?>
					<?php
					$filters = array( array( 'all', sprintf( '%s (%s)', $s['all_label'], ls_fa_num( count( $cards ) ) ), 'bi bi-boxes' ) );
					foreach ( $s['chips'] as $c ) {
						$filters[] = array( $c['key'] ?? '', $c['label'] ?? '', is_array( $c['icon'] ?? '' ) ? ( $c['icon']['value'] ?? '' ) : ( $c['icon'] ?? '' ) );
					}
					echo ls_filter_buttons( $filters, 'chip' ); // phpcs:ignore
					?>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>

		<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg">
			<?php if ( 'yes' === $s['show_sidebar'] ) : ?>
			<aside class="lg:col-span-3 flex flex-col gap-space-md">
				<?php if ( $is_woo ) : ?>
					<?php $pcats = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'exclude' => array( (int) get_option( 'default_product_cat' ) ) ) ); ?>
					<?php if ( $pcats && ! is_wp_error( $pcats ) ) : ?>
					<div class="p-space-md rounded-2xl bg-surface-card shadow-sm flex flex-col gap-space-sm">
						<div class="flex items-center justify-between pb-space-xs"><span class="font-title-card text-title-card text-on-surface"><?php esc_html_e( 'دسته‌بندی محصولات', 'larijani' ); ?></span><i class="bi bi-layers-half text-primary" aria-hidden="true"></i></div>
						<?php foreach ( $pcats as $pc ) : ?>
						<a class="flex items-center justify-between py-1 group" href="<?php echo esc_url( get_term_link( $pc ) ); ?>">
							<span class="flex items-center gap-space-xs"><span class="w-4 h-4 rounded border <?php echo is_product_category( $pc->term_id ) ? 'bg-primary-container border-primary-container' : 'border-slate-300 group-hover:border-primary-container'; ?>"></span><span class="font-body-md text-body-md text-on-surface"><?php echo esc_html( $pc->name ); ?></span></span>
							<span class="font-body-sm text-body-sm text-outline"><?php echo esc_html( ls_fa_num( $pc->count ) ); ?></span>
						</a>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
					<?php if ( 'yes' === $s['show_price'] ) : ?>
					<form class="p-space-md rounded-2xl bg-surface-card shadow-sm flex flex-col gap-space-sm" method="get">
						<span class="font-title-card text-title-card text-on-surface"><?php echo esc_html( $s['price_title'] ); ?></span>
						<div class="grid grid-cols-2 gap-2 pt-2">
							<input class="w-full px-3 py-2 rounded-xl bg-surface-canvas text-sm" type="number" min="0" name="min_price" placeholder="<?php esc_attr_e( 'از', 'larijani' ); ?>" value="<?php echo isset( $_GET['min_price'] ) ? esc_attr( absint( $_GET['min_price'] ) ) : ''; // phpcs:ignore ?>">
							<input class="w-full px-3 py-2 rounded-xl bg-surface-canvas text-sm" type="number" min="0" name="max_price" placeholder="<?php esc_attr_e( 'تا', 'larijani' ); ?>" value="<?php echo isset( $_GET['max_price'] ) ? esc_attr( absint( $_GET['max_price'] ) ) : ''; // phpcs:ignore ?>">
						</div>
						<?php foreach ( array( 'orderby', 's', 'post_type' ) as $keep ) : ?>
							<?php if ( isset( $_GET[ $keep ] ) ) : // phpcs:ignore ?><input type="hidden" name="<?php echo esc_attr( $keep ); ?>" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_GET[ $keep ] ) ) ); // phpcs:ignore ?>"><?php endif; ?>
						<?php endforeach; ?>
						<div class="flex items-center justify-between pt-1">
							<span class="font-body-sm text-body-sm text-outline"><?php echo esc_html( get_woocommerce_currency_symbol() ); ?></span>
							<button class="text-body-sm font-semibold text-primary hover:underline" type="submit"><?php esc_html_e( 'اعمال', 'larijani' ); ?></button>
						</div>
					</form>
					<?php endif; ?>
					<?php if ( is_active_sidebar( 'shop-sidebar' ) ) : ?>
					<div class="ls-widget-area"><?php dynamic_sidebar( 'shop-sidebar' ); ?></div>
					<?php endif; ?>
				<?php else : ?>
					<?php foreach ( $s['filter_boxes'] as $box ) : ?>
					<div class="p-space-md rounded-2xl bg-surface-card shadow-sm flex flex-col gap-space-sm">
						<div class="flex items-center justify-between pb-space-xs"><span class="font-title-card text-title-card text-on-surface"><?php echo esc_html( $box['title'] ?? '' ); ?></span><?php echo ls_icon( $box['icon'] ?? '', 'text-primary' ); // phpcs:ignore ?></div>
						<?php foreach ( ls_lines( $box['options'] ?? '' ) as $line ) : ?>
							<?php $parts = array_map( 'trim', explode( '|', $line ) ); ?>
						<label class="flex items-center justify-between py-1 cursor-pointer">
							<span class="flex items-center gap-space-xs"><input class="rounded w-4 h-4" type="checkbox" value="<?php echo esc_attr( $parts[2] ?? '' ); ?>" data-ls-check-filter><span class="font-body-md text-body-md text-on-surface"><?php echo esc_html( $parts[0] ); ?></span></span>
							<span class="font-body-sm text-body-sm text-outline"><?php echo esc_html( $parts[1] ?? '' ); ?></span>
						</label>
						<?php endforeach; ?>
					</div>
					<?php endforeach; ?>
					<?php if ( 'yes' === $s['show_price'] && $cards ) : ?>
						<?php
						$ls_prices = array_filter( array_map( static function ( $c ) { return (float) ( $c['price_raw'] ?? 0 ); }, $cards ) );
						$ls_min    = $ls_prices ? min( $ls_prices ) : 0;
						$ls_max    = $ls_prices ? max( $ls_prices ) : 0;
						?>
						<?php if ( $ls_max > 0 ) : ?>
					<div class="p-space-md rounded-2xl bg-surface-card shadow-sm flex flex-col gap-space-sm">
						<span class="font-headline-sm text-title-card text-on-surface"><?php echo esc_html( $s['price_title'] ); ?></span>
						<div class="flex items-center justify-between text-body-sm text-on-surface-variant pt-2">
							<span><?php echo esc_html( sprintf( /* translators: %s price */ __( 'از %s تومان', 'larijani' ), ls_fa_number_format( $ls_min ) ) ); ?></span>
							<span><?php echo esc_html__( 'تا', 'larijani' ); ?> <span data-ls-price-max><?php echo esc_html( ls_fa_number_format( $ls_max ) ); ?></span> <?php esc_html_e( 'تومان', 'larijani' ); ?></span>
						</div>
						<input class="w-full accent-primary-container cursor-pointer mt-2" type="range" min="<?php echo esc_attr( $ls_min ); ?>" max="<?php echo esc_attr( $ls_max ); ?>" step="1000" value="<?php echo esc_attr( $ls_max ); ?>" aria-label="<?php echo esc_attr( $s['price_title'] ); ?>" data-ls-price-range>
						<div class="flex items-center justify-between pt-1">
							<span class="font-body-sm text-body-sm text-outline"><?php esc_html_e( 'نمایش کلیه سفارشات', 'larijani' ); ?></span>
							<button class="min-h-6 min-w-6 px-1 text-body-sm font-semibold text-primary hover:underline" type="button" data-ls-price-apply><?php esc_html_e( 'اعمال', 'larijani' ); ?></button>
						</div>
					</div>
						<?php endif; ?>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ( $s['advisory_title'] ) : ?>
				<div class="p-space-md rounded-2xl bg-surface-container-low flex flex-col gap-space-sm">
					<div class="flex items-center gap-space-xs text-primary-container"><i class="bi bi-lightbulb-fill text-lg" aria-hidden="true"></i><span class="font-title-card text-title-card"><?php echo esc_html( $s['advisory_title'] ); ?></span></div>
					<p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed"><?php echo esc_html( $s['advisory_text'] ); ?></p>
					<?php if ( $s['advisory_link_text'] ) : ?>
					<a class="inline-flex items-center gap-space-xs text-body-sm font-bold text-primary-container hover:text-primary pt-1" <?php echo ls_link_attrs( ! empty( $s['advisory_link']['url'] ) ? $s['advisory_link'] : ls_tel( ls_opt( 'phone_1' ) ) ); // phpcs:ignore ?>><span><?php echo esc_html( $s['advisory_link_text'] ); ?></span><i class="bi bi-arrow-left" aria-hidden="true"></i></a>
					<?php endif; ?>
				</div>
				<?php endif; ?>
			</aside>
			<?php endif; ?>

			<div class="<?php echo 'yes' === $s['show_sidebar'] ? 'lg:col-span-9' : 'lg:col-span-12'; ?> flex flex-col gap-space-lg">
				<?php if ( $cards ) : ?>
				<div class="grid grid-cols-1 <?php echo $store ? 'sm:grid-cols-2 lg:grid-cols-4 gap-space-lg' : 'md:grid-cols-2 ' . esc_attr( $cols[ (int) $s['columns'] ] ?? 'xl:grid-cols-3' ) . ' gap-space-md'; ?>" data-ls-catalog-grid>
					<?php foreach ( $cards as $card ) : ?>
						<?php echo ls_product_card( $card, $card_style ); // phpcs:ignore ?>
					<?php endforeach; ?>
				</div>
				<?php else : ?>
				<div class="rounded-2xl bg-surface-card shadow-sm p-10 text-center flex flex-col items-center gap-3">
					<i class="bi bi-search text-4xl text-outline" aria-hidden="true"></i>
					<h3 class="font-headline-sm text-headline-sm text-on-surface"><?php esc_html_e( 'محصولی مطابق با جستجوی شما یافت نشد!', 'larijani' ); ?></h3>
					<p class="font-body-md text-body-md text-on-surface-variant"><?php esc_html_e( 'عبارت دیگری را امتحان کنید یا برای استعلام با ما تماس بگیرید.', 'larijani' ); ?></p>
				</div>
				<?php endif; ?>
				<?php if ( $is_woo && $query ) : ?>
					<?php
					$per  = max( 1, (int) $query->get( 'posts_per_page' ) );
					$page = max( 1, (int) $query->get( 'paged' ) );
					$from = $total ? ( $page - 1 ) * $per + 1 : 0;
					$to   = min( $total, $from + count( $cards ) - 1 );
					?>
				<div class="flex flex-col sm:flex-row items-center justify-between gap-space-md pt-space-md pb-space-sm border-t border-border-subtle">
					<span class="font-body-sm text-body-sm text-on-surface-variant"><?php echo esc_html( sprintf( $s['count_text'], ls_fa_num( $from ), ls_fa_num( $to ), ls_fa_num( $total ) ) ); ?></span>
					<?php echo ls_pagination( $query ); // phpcs:ignore ?>
				</div>
				<?php elseif ( $s['footer_note'] ) : ?>
				<div class="flex flex-col sm:flex-row items-center justify-between gap-space-md mt-space-lg pt-space-lg">
					<span class="font-body-sm text-body-sm text-outline"><?php echo esc_html( $s['footer_note'] ); ?></span>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Product detail data from a WooCommerce product.
 *
 * @param WC_Product $product Product.
 * @return array
 */
function ls_wc_detail_data( $product ) {
	$id      = $product->get_id();
	$gallery = array();
	if ( $product->get_image_id() ) {
		$gallery[] = array( 'id' => $product->get_image_id(), 'url' => wp_get_attachment_image_url( $product->get_image_id(), 'large' ) );
	}
	foreach ( $product->get_gallery_image_ids() as $gid ) {
		$gallery[] = array( 'id' => $gid, 'url' => wp_get_attachment_image_url( $gid, 'large' ) );
	}
	$highlights = array();
	foreach ( $product->get_attributes() as $attr ) {
		if ( ! $attr->get_visible() ) {
			continue;
		}
		$values       = $attr->is_taxonomy() ? wc_get_product_terms( $id, $attr->get_name(), array( 'fields' => 'names' ) ) : $attr->get_options();
		$highlights[] = array( 'label' => wc_attribute_label( $attr->get_name() ), 'value' => implode( '، ', $values ) );
	}
	$parse_lines = static function ( $text, $keys ) {
		$out = array();
		foreach ( ls_lines( $text ) as $line ) {
			$parts = array_map( 'trim', explode( '|', $line ) );
			$row   = array();
			foreach ( $keys as $i => $k ) {
				$row[ $k ] = $parts[ $i ] ?? '';
			}
			$out[] = $row;
		}
		return $out;
	};
	$stock_text = $product->is_in_stock() ? ( get_post_meta( $id, '_ls_stock_text', true ) ? get_post_meta( $id, '_ls_stock_text', true ) : __( 'موجود در انبار', 'larijani' ) ) : __( 'ناموجود – تماس بگیرید', 'larijani' );
	return array(
		'product'       => $product,
		'gallery'       => $gallery,
		'image_badges'  => $parse_lines( get_post_meta( $id, '_ls_image_badges', true ), array( 'text', 'tone', 'icon' ) ),
		'stock_text'    => $stock_text,
		'in_stock'      => $product->is_in_stock(),
		'code'          => $product->get_sku(),
		'hot_text'      => $product->is_featured() ? __( 'پرفروش‌ترین طرح سال', 'larijani' ) : '',
		'title'         => $product->get_name(),
		'subtitle'      => wp_strip_all_tags( $product->get_short_description() ),
		'rating'        => (float) $product->get_average_rating(),
		'review_count'  => (int) $product->get_review_count(),
		'quality_note'  => get_post_meta( $id, '_ls_quality_note', true ),
		'highlights'    => array_slice( $highlights, 0, 4 ),
		'trust'         => $parse_lines( get_post_meta( $id, '_ls_trust', true ), array( 'title', 'text', 'icon' ) ),
		'price_label'   => get_post_meta( $id, '_ls_price_label', true ) ? get_post_meta( $id, '_ls_price_label', true ) : __( 'قیمت هر عدد (تک‌فروشی):', 'larijani' ),
		'unit_price'    => (float) wc_get_price_to_display( $product ),
		'bulk_price'    => (float) get_post_meta( $id, '_ls_bulk_price', true ),
		'bulk_min'      => (int) get_post_meta( $id, '_ls_bulk_threshold', true ),
		'area_per_unit' => (float) get_post_meta( $id, '_ls_area_per_unit', true ),
		'area_unit'     => get_post_meta( $id, '_ls_area_unit', true ) ? get_post_meta( $id, '_ls_area_unit', true ) : __( 'مترمربع', 'larijani' ),
		'currency'      => get_woocommerce_currency_symbol(),
		'guarantees'    => $parse_lines( get_post_meta( $id, '_ls_guarantees', true ), array( 'title', 'text', 'icon', 'tone' ) ),
		'qty_default'   => max( 1, (int) get_post_meta( $id, '_ls_default_qty', true ) ),
	);
}

/**
 * Product showcase: gallery + buy panel.
 *
 * @param array $d Data (see ls_wc_detail_data()) merged with widget labels.
 */
function ls_render_product_detail( $d ) {
	$d = wp_parse_args(
		$d,
		array(
			'product'       => null,
			'gallery'       => array(),
			'image_badges'  => array(),
			'stock_text'    => '',
			'in_stock'      => true,
			'code'          => '',
			'code_label'    => __( 'کد فنی:', 'larijani' ),
			'hot_text'      => '',
			'title'         => '',
			'subtitle'      => '',
			'rating'        => 0,
			'review_count'  => 0,
			'quality_note'  => '',
			'highlights'    => array(),
			'trust'         => array(),
			'price_label'   => '',
			'unit_price'    => 0,
			'bulk_price'    => 0,
			'bulk_min'      => 0,
			'bulk_title'    => __( 'تخفیف تیراژ کارخانه‌ای', 'larijani' ),
			'area_per_unit' => 0,
			'area_unit'     => __( 'مترمربع', 'larijani' ),
			'currency'      => 'تومان',
			'qty_label'     => __( 'تعداد:', 'larijani' ),
			'area_label'    => __( 'سطح تولید:', 'larijani' ),
			'total_label'   => __( 'مجموع:', 'larijani' ),
			'cart_text'     => __( 'افزودن به سبد خرید', 'larijani' ),
			'consult_text'  => __( 'مشاوره تیراژ و خط تولید', 'larijani' ),
			'consult_link'  => '',
			'guarantees'    => array(),
			'qty_default'   => 1,
			'show_wishlist' => 'yes',
			'cart_link'     => '',
		)
	);
	$product = $d['product'];
	$gallery = $d['gallery'] ? $d['gallery'] : array( array( 'url' => LS_URI . '/assets/images/placeholder.svg' ) );
	$main    = $gallery[0];
	$cur     = $d['currency'];
	$fmt     = static function ( $n ) {
		return ls_fa_number_format( $n );
	};
	$qty     = (int) $d['qty_default'];
	$unit    = (float) $d['unit_price'];
	$price   = ( $d['bulk_min'] && $d['bulk_price'] && $qty >= $d['bulk_min'] ) ? (float) $d['bulk_price'] : $unit;
	?>
	<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-start" data-ls-product data-unit="<?php echo esc_attr( $unit ); ?>" data-bulk="<?php echo esc_attr( (float) $d['bulk_price'] ); ?>" data-bulk-min="<?php echo esc_attr( (int) $d['bulk_min'] ); ?>" data-area="<?php echo esc_attr( (float) $d['area_per_unit'] ); ?>">
		<div class="lg:col-span-6 flex flex-col gap-space-md" data-ls-gallery>
			<div class="relative bg-surface-card rounded-2xl shadow-sm p-space-sm overflow-hidden group">
				<?php if ( $d['image_badges'] ) : ?>
				<div class="absolute top-4 right-4 z-10 flex flex-col gap-2">
					<?php foreach ( $d['image_badges'] as $b ) : ?>
					<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full <?php echo 'amber' === ( $b['tone'] ?? '' ) ? 'bg-accent-amber' : 'bg-primary-container'; ?> text-on-primary font-label-badge text-label-badge shadow-sm"><?php echo ls_icon( $b['icon'] ?? '', 'text-xs' ); // phpcs:ignore ?><?php echo esc_html( $b['text'] ?? '' ); ?></span>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
				<a href="<?php echo esc_url( ls_img_url( $main, 'full' ) ); ?>" target="_blank" aria-label="<?php esc_attr_e( 'بزرگ‌نمایی عکس', 'larijani' ); ?>" class="absolute top-4 left-4 z-10 w-10 h-10 rounded-full bg-surface-card/90 text-on-surface hover:text-primary-container flex items-center justify-center shadow-md backdrop-blur-sm transition-all" data-ls-zoom><i class="bi bi-arrows-fullscreen text-sm" aria-hidden="true"></i></a>
				<div class="w-full aspect-[4/3] rounded-xl overflow-hidden bg-surface-canvas relative flex items-center justify-center">
					<?php echo ls_img( $main, 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-105', $d['title'], 'large', false ); // phpcs:ignore ?>
				</div>
			</div>
			<?php if ( count( $gallery ) > 1 ) : ?>
			<div class="grid grid-cols-4 gap-space-sm">
				<?php foreach ( array_slice( $gallery, 0, 8 ) as $i => $g ) : ?>
				<button class="relative aspect-video rounded-xl overflow-hidden bg-surface-card shadow-sm p-1 transition-all <?php echo 0 === $i ? 'ring-2 ring-primary-container' : 'opacity-70 hover:opacity-100'; ?>" type="button" data-ls-thumb="<?php echo esc_url( ls_img_url( $g, 'large' ) ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d image index */ __( 'تصویر %d', 'larijani' ), $i + 1 ) ); ?>">
					<?php echo ls_img( $g, 'w-full h-full object-cover rounded-lg', '', 'ls-thumb' ); // phpcs:ignore ?>
				</button>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
			<?php if ( $d['trust'] ) : ?>
			<div class="grid grid-cols-1 sm:grid-cols-3 gap-space-sm pt-space-xs">
				<?php foreach ( $d['trust'] as $t ) : ?>
				<div class="bg-surface-card rounded-xl p-space-sm shadow-sm flex items-center gap-space-sm">
					<div class="w-10 h-10 rounded-lg bg-surface-container flex items-center justify-center text-primary-container shrink-0 text-lg"><?php echo ls_icon( $t['icon'] ?? 'bi bi-check2-circle' ); // phpcs:ignore ?></div>
					<div class="flex flex-col min-w-0"><span class="font-label-nav text-label-nav text-on-surface font-bold leading-tight"><?php echo esc_html( $t['title'] ?? '' ); ?></span><span class="font-body-sm text-body-sm text-on-surface-variant truncate"><?php echo esc_html( $t['text'] ?? '' ); ?></span></div>
				</div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>

		<div class="lg:col-span-6 flex flex-col gap-space-md">
			<div class="flex flex-wrap items-center gap-2">
				<?php if ( $d['stock_text'] ) : ?>
				<span class="px-3 py-1 rounded-full <?php echo $d['in_stock'] ? 'bg-accent-emerald/10 text-accent-emerald' : 'bg-error-container text-on-error-container'; ?> font-body-sm text-body-sm font-bold flex items-center gap-1.5"><i class="bi bi-box-seam-fill" aria-hidden="true"></i><?php echo esc_html( $d['stock_text'] ); ?></span>
				<?php endif; ?>
				<?php if ( $d['code'] ) : ?>
				<span class="px-3 py-1 rounded-full bg-surface-container text-on-surface-variant font-body-sm text-body-sm font-medium"><?php echo esc_html( $d['code_label'] ); ?> <strong class="text-on-surface font-mono" dir="ltr"><?php echo esc_html( $d['code'] ); ?></strong></span>
				<?php endif; ?>
				<?php if ( $d['hot_text'] ) : ?>
				<span class="px-2.5 py-1 rounded-full bg-accent-amber/15 text-accent-amber font-body-sm text-body-sm font-semibold flex items-center gap-1"><i class="bi bi-fire" aria-hidden="true"></i><?php echo esc_html( $d['hot_text'] ); ?></span>
				<?php endif; ?>
			</div>
			<div class="flex flex-col gap-1">
				<h1 class="font-headline-lg text-[22px] sm:text-[28px] text-on-surface font-black tracking-tight leading-snug"><?php echo esc_html( $d['title'] ); ?></h1>
				<?php if ( $d['subtitle'] ) : ?><p class="font-body-md text-body-md text-on-surface-variant"><?php echo esc_html( $d['subtitle'] ); ?></p><?php endif; ?>
			</div>
			<?php if ( $d['rating'] || $d['quality_note'] ) : ?>
			<div class="flex items-center flex-wrap gap-4 py-1">
				<?php if ( $d['rating'] ) : ?>
				<div class="flex items-center gap-1 text-accent-amber" aria-label="<?php echo esc_attr( sprintf( /* translators: %s rating */ __( 'امتیاز %s از ۵', 'larijani' ), ls_fa_num( $d['rating'] ) ) ); ?>">
					<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
					<i class="bi <?php echo $d['rating'] >= $i ? 'bi-star-fill' : ( $d['rating'] >= $i - 0.5 ? 'bi-star-half' : 'bi-star' ); ?> text-sm" aria-hidden="true"></i>
					<?php endfor; ?>
					<span class="font-label-nav text-label-nav text-on-surface font-black mr-1"><?php echo esc_html( ls_fa_num( round( $d['rating'], 1 ) ) ); ?></span>
				</div>
				<?php if ( $d['review_count'] ) : ?><a class="font-body-sm text-body-sm text-primary-container hover:underline" href="#ls-reviews" data-ls-tab-open="reviews"><?php echo esc_html( sprintf( /* translators: %s count */ __( '(%s تجربه ثبت‌شده کارگاه‌های تولیدی)', 'larijani' ), ls_fa_num( $d['review_count'] ) ) ); ?></a><?php endif; ?>
				<?php endif; ?>
				<?php if ( $d['quality_note'] ) : ?>
				<?php if ( $d['rating'] ) : ?><span class="text-on-surface-variant/40">•</span><?php endif; ?>
				<span class="font-body-sm text-body-sm text-on-surface-variant flex items-center gap-1"><i class="bi bi-patch-check-fill text-accent-emerald text-sm" aria-hidden="true"></i><?php echo esc_html( $d['quality_note'] ); ?></span>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<?php if ( $d['highlights'] ) : ?>
			<div class="grid grid-cols-2 sm:grid-cols-4 gap-2 bg-surface-card p-space-sm rounded-2xl shadow-sm">
				<?php foreach ( $d['highlights'] as $h ) : ?>
				<div class="flex flex-col p-2 bg-surface-canvas rounded-xl"><span class="font-body-sm text-body-sm text-on-surface-variant"><?php echo esc_html( $h['label'] ?? '' ); ?></span><span class="font-headline-sm text-[15px] sm:text-headline-sm text-on-surface font-extrabold mt-0.5"><?php echo esc_html( $h['value'] ?? '' ); ?></span></div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<div class="bg-surface-card rounded-2xl p-space-md shadow-sm flex flex-col gap-space-sm">
				<div class="flex items-end justify-between gap-3 flex-wrap">
					<div class="flex flex-col">
						<span class="font-body-sm text-body-sm text-on-surface-variant"><?php echo esc_html( $d['price_label'] ); ?></span>
						<div class="flex items-baseline gap-1 mt-1">
							<?php if ( $unit ) : ?>
							<span class="font-headline-lg text-headline-lg font-black text-on-surface" data-ls-unit-price><?php echo esc_html( $fmt( $price ) ); ?></span>
							<span class="font-body-sm text-body-sm text-on-surface-variant font-medium"><?php echo esc_html( $cur ); ?></span>
							<?php else : ?>
							<span class="font-headline-md text-headline-md font-black text-on-surface"><?php esc_html_e( 'استعلام قیمت تلفنی', 'larijani' ); ?></span>
							<?php endif; ?>
						</div>
					</div>
					<?php if ( $d['bulk_price'] && $d['bulk_min'] ) : ?>
					<div class="bg-primary/10 rounded-xl px-3 py-2 text-right">
						<span class="block font-body-sm text-body-sm text-primary-container font-bold"><?php echo esc_html( $d['bulk_title'] ); ?></span>
						<span class="block font-body-sm text-body-sm text-on-surface-variant"><?php echo esc_html( sprintf( /* translators: %s min qty */ __( 'بالای %s عدد:', 'larijani' ), ls_fa_num( $d['bulk_min'] ) ) ); ?> <strong class="text-primary-container"><?php echo esc_html( $fmt( $d['bulk_price'] ) ); ?></strong> <?php echo esc_html( $cur ); ?></span>
					</div>
					<?php endif; ?>
				</div>

				<?php
				$simple = $product && $product->is_type( 'simple' );
				$form_open = $simple && $product->is_purchasable() && $product->is_in_stock();
				?>
				<?php if ( $product && ! $simple ) : ?>
					<div class="ls-wc-add-to-cart [&_.single_add_to_cart_button]:bg-primary-container [&_.single_add_to_cart_button]:text-white [&_.single_add_to_cart_button]:rounded-full [&_.single_add_to_cart_button]:px-6 [&_.single_add_to_cart_button]:py-3 [&_select]:bg-surface-canvas [&_select]:rounded-xl [&_select]:py-2 [&_table]:w-full [&_td]:py-2"><?php woocommerce_template_single_add_to_cart(); ?></div>
				<?php else : ?>
				<form class="flex flex-col gap-space-sm" method="post" enctype="multipart/form-data" action="<?php echo esc_url( $product ? $product->get_permalink() : ( ! empty( $d['cart_link']['url'] ) ? $d['cart_link']['url'] : '#' ) ); ?>">
					<div class="pt-space-xs flex flex-col sm:flex-row items-center justify-between gap-space-sm bg-surface-canvas p-space-sm rounded-xl">
						<div class="flex items-center gap-space-sm w-full sm:w-auto">
							<span class="font-label-nav text-label-nav text-on-surface font-bold whitespace-nowrap"><?php echo esc_html( $d['qty_label'] ); ?></span>
							<div class="flex items-center bg-surface-card rounded-full shadow-sm p-1">
								<button aria-label="<?php esc_attr_e( 'افزایش', 'larijani' ); ?>" class="w-9 h-9 rounded-full bg-surface-canvas hover:bg-surface-container flex items-center justify-center text-on-surface transition-colors font-bold" type="button" data-ls-qty="1"><i class="bi bi-plus-lg" aria-hidden="true"></i></button>
								<input class="w-14 text-center font-headline-sm text-headline-sm font-black bg-transparent border-0 p-0 focus:ring-0 text-on-surface [appearance:textfield]" name="quantity" max="10000" min="1" type="number" value="<?php echo esc_attr( $qty ); ?>" data-ls-qty-input aria-label="<?php esc_attr_e( 'تعداد', 'larijani' ); ?>">
								<button aria-label="<?php esc_attr_e( 'کاهش', 'larijani' ); ?>" class="w-9 h-9 rounded-full bg-surface-canvas hover:bg-surface-container flex items-center justify-center text-on-surface transition-colors font-bold" type="button" data-ls-qty="-1"><i class="bi bi-dash-lg" aria-hidden="true"></i></button>
							</div>
						</div>
						<div class="flex items-center gap-4 text-on-surface-variant font-body-sm text-body-sm w-full sm:w-auto justify-between sm:justify-end flex-wrap">
							<?php if ( $d['area_per_unit'] ) : ?>
							<div><?php echo esc_html( $d['area_label'] ); ?> <strong class="text-on-surface font-bold" data-ls-area><?php echo esc_html( ls_fa_num( round( $qty * $d['area_per_unit'], 2 ) ) ); ?></strong> <?php echo esc_html( $d['area_unit'] ); ?></div>
							<?php endif; ?>
							<?php if ( $unit ) : ?>
							<div><?php echo esc_html( $d['total_label'] ); ?> <strong class="text-primary-container font-headline-sm text-headline-sm font-black" data-ls-total><?php echo esc_html( $fmt( $qty * $price ) ); ?></strong> <?php echo esc_html( $cur ); ?></div>
							<?php endif; ?>
						</div>
					</div>
					<div class="flex flex-col sm:flex-row items-stretch gap-space-sm pt-space-xs">
						<?php if ( $form_open ) : ?>
						<button class="flex-1 py-3.5 px-space-lg rounded-full bg-primary-container hover:bg-primary text-on-primary font-headline-sm text-headline-sm flex items-center justify-center gap-2 shadow-lg shadow-primary-container/20 transition-all hover:scale-[1.01] active:scale-[0.99]" type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>">
							<i class="bi bi-cart-plus-fill text-xl" aria-hidden="true"></i><span><?php echo esc_html( $d['cart_text'] ); ?></span>
						</button>
						<?php elseif ( ! $product ) : ?>
						<a class="flex-1 py-3.5 px-space-lg rounded-full bg-primary-container hover:bg-primary text-on-primary font-headline-sm text-headline-sm flex items-center justify-center gap-2 shadow-lg shadow-primary-container/20 transition-all" <?php echo ls_link_attrs( ! empty( $d['cart_link']['url'] ) ? $d['cart_link'] : ls_tel( ls_opt( 'phone_1' ) ) ); // phpcs:ignore ?>>
							<i class="bi bi-cart-plus-fill text-xl" aria-hidden="true"></i><span><?php echo esc_html( $d['cart_text'] ); ?></span>
						</a>
						<?php endif; ?>
						<a class="py-3.5 px-space-md rounded-full bg-surface-container hover:bg-surface-variant text-on-surface font-label-nav text-label-nav flex items-center justify-center gap-2 transition-colors" <?php echo ls_link_attrs( ! empty( $d['consult_link']['url'] ) ? $d['consult_link'] : ls_tel( ls_opt( 'phone_1' ) ) ); // phpcs:ignore ?>>
							<i class="bi bi-telephone-outbound-fill text-accent-emerald" aria-hidden="true"></i><span><?php echo esc_html( $d['consult_text'] ); ?></span>
						</a>
						<?php if ( 'yes' === $d['show_wishlist'] ) : ?>
						<button aria-label="<?php esc_attr_e( 'نشان کردن محصول', 'larijani' ); ?>" aria-pressed="false" class="w-12 h-12 rounded-full bg-surface-card hover:bg-surface-canvas text-on-surface flex items-center justify-center shadow-sm shrink-0 transition-colors self-center" type="button" data-ls-wishlist="<?php echo esc_attr( $product ? $product->get_id() : sanitize_title( $d['title'] ) ); ?>"><i class="bi bi-heart text-lg text-on-surface-variant" aria-hidden="true"></i></button>
						<?php endif; ?>
					</div>
				</form>
				<?php endif; ?>
			</div>

			<?php if ( $d['guarantees'] ) : ?>
			<div class="bg-surface-canvas rounded-2xl p-space-md space-y-2">
				<?php foreach ( $d['guarantees'] as $g ) : ?>
				<div class="flex items-start gap-space-sm">
					<?php echo ls_icon( ! empty( $g['icon'] ) ? $g['icon'] : 'bi bi-shield-fill-check', 'text-lg mt-0.5 shrink-0 ' . ls_tone( ! empty( $g['tone'] ) ? $g['tone'] : 'emerald', 'text' ) ); // phpcs:ignore ?>
					<p class="font-body-sm text-body-sm text-on-surface leading-relaxed"><strong><?php echo esc_html( $g['title'] ?? '' ); ?></strong> <?php echo esc_html( $g['text'] ?? '' ); ?></p>
				</div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Product tabs (specs / formulation / reviews).
 *
 * @param array $d Data.
 */
function ls_render_product_tabs( $d ) {
	$d = wp_parse_args(
		$d,
		array(
			'product'          => null,
			'tab1_label'       => __( 'مشخصات فنی و متریال', 'larijani' ),
			'tab1_icon'        => 'bi bi-sliders2-vertical',
			'spec_title'       => '',
			'spec_text'        => '',
			'spec_rows'        => array(),
			'highlight_icon'   => 'bi bi-shield-lock-fill',
			'highlight_title'  => '',
			'highlight_text'   => '',
			'datasheet_text'   => __( 'دانلود برگه مشخصات فنی PDF', 'larijani' ),
			'datasheet_link'   => '',
			'tab2_label'       => __( 'دستورالعمل و فرمولاسیون اختصاصی', 'larijani' ),
			'tab2_icon'        => 'bi bi-journal-code',
			'formula_title'    => '',
			'formula_subtitle' => '',
			'formula_items'    => array(),
			'formula_steps'    => array(),
			'formula_html'     => '',
			'tab3_label'       => __( 'دیدگاه‌ها و تجربیات کارگاه‌ها', 'larijani' ),
			'tab3_icon'        => 'bi bi-chat-square-quote',
			'rating'           => 0,
			'review_count'     => 0,
			'rating_bars'      => array(),
			'reviews'          => array(),
			'review_button'    => __( 'ثبت تجربه و عکس تولیدی شما', 'larijani' ),
		)
	);
	$product = $d['product'];
	$tabs    = array( 'specs' => array( $d['tab1_label'], $d['tab1_icon'] ) );
	if ( $d['formula_items'] || $d['formula_steps'] || $d['formula_html'] ) {
		$tabs['formula'] = array( $d['tab2_label'], $d['tab2_icon'] );
	}
	$review_label = $d['tab3_label'];
	if ( $product ) {
		$review_label .= ' (' . ls_fa_num( $product->get_review_count() ) . ')';
	} elseif ( $d['review_count'] ) {
		$review_label .= ' (' . ls_fa_num( $d['review_count'] ) . ')';
	}
	if ( ( $product && comments_open( $product->get_id() ) ) || $d['reviews'] ) {
		$tabs['reviews'] = array( $review_label, $d['tab3_icon'] );
	}
	$first = array_key_first( $tabs );
	?>
	<div data-ls-tabs>
		<div class="flex items-center gap-2 pb-space-md overflow-x-auto no-scrollbar" role="tablist">
			<?php foreach ( $tabs as $key => $t ) : ?>
			<button class="px-space-lg py-space-sm rounded-full font-label-nav text-label-nav transition-all whitespace-nowrap <?php echo $key === $first ? 'bg-primary-container text-on-primary shadow-sm' : 'bg-surface-canvas text-on-surface-variant hover:text-on-surface'; ?>" type="button" role="tab" aria-selected="<?php echo $key === $first ? 'true' : 'false'; ?>" data-ls-tab="<?php echo esc_attr( $key ); ?>" data-on="bg-primary-container text-on-primary shadow-sm" data-off="bg-surface-canvas text-on-surface-variant hover:text-on-surface"<?php echo 'reviews' === $key ? ' id="ls-reviews"' : ''; ?>>
				<?php echo ls_icon( $t[1], 'ml-1.5' ); // phpcs:ignore ?><?php echo esc_html( $t[0] ); ?>
			</button>
			<?php endforeach; ?>
		</div>

		<div class="flex flex-col gap-space-lg" role="tabpanel" data-ls-pane="specs">
			<div class="grid grid-cols-1 lg:grid-cols-3 gap-space-lg">
				<div class="<?php echo $d['highlight_title'] ? 'lg:col-span-2' : 'lg:col-span-3'; ?> flex flex-col gap-space-md">
					<?php if ( $d['spec_title'] ) : ?><h2 class="font-headline-md text-headline-md text-on-surface font-black"><?php echo esc_html( $d['spec_title'] ); ?></h2><?php endif; ?>
					<?php if ( $product ) : ?>
					<div class="ls-prose !text-[0.95rem]"><?php echo apply_filters( 'the_content', $product->get_description() ); // phpcs:ignore ?></div>
					<?php elseif ( $d['spec_text'] ) : ?>
					<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed"><?php echo esc_html( $d['spec_text'] ); ?></p>
					<?php endif; ?>
					<?php if ( $d['spec_rows'] ) : ?>
					<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm">
						<?php foreach ( $d['spec_rows'] as $r ) : ?>
						<div class="bg-surface-canvas p-space-md rounded-xl flex items-center justify-between gap-3"><span class="font-body-md text-body-md text-on-surface-variant"><?php echo esc_html( $r['label'] ?? '' ); ?></span><span class="font-headline-sm text-[15px] text-on-surface font-extrabold text-left"><?php echo esc_html( $r['value'] ?? '' ); ?></span></div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
				</div>
				<?php if ( $d['highlight_title'] ) : ?>
				<div class="bg-surface-canvas rounded-2xl p-space-lg flex flex-col justify-between">
					<div class="flex flex-col gap-space-sm">
						<div class="w-12 h-12 rounded-xl bg-primary-container text-on-primary flex items-center justify-center text-2xl"><?php echo ls_icon( $d['highlight_icon'] ); // phpcs:ignore ?></div>
						<h3 class="font-headline-sm text-headline-sm text-on-surface font-black"><?php echo esc_html( $d['highlight_title'] ); ?></h3>
						<p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed"><?php echo esc_html( $d['highlight_text'] ); ?></p>
					</div>
					<?php if ( $d['datasheet_text'] && ( ! empty( $d['datasheet_link']['url'] ) || ( is_string( $d['datasheet_link'] ) && $d['datasheet_link'] ) ) ) : ?>
					<div class="pt-space-md">
						<a class="w-full py-2.5 px-space-md rounded-full bg-surface-card hover:bg-surface-container text-on-surface font-label-nav text-label-nav flex items-center justify-center gap-2 shadow-sm transition-colors" <?php echo ls_link_attrs( $d['datasheet_link'] ); // phpcs:ignore ?>><i class="bi bi-file-earmark-pdf-fill text-error" aria-hidden="true"></i><span><?php echo esc_html( $d['datasheet_text'] ); ?></span></a>
					</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( isset( $tabs['formula'] ) ) : ?>
		<div class="hidden flex-col gap-space-lg" role="tabpanel" data-ls-pane="formula">
			<div class="bg-surface-canvas rounded-2xl p-space-lg">
				<?php if ( $d['formula_title'] ) : ?>
				<div class="flex items-center gap-space-sm pb-space-sm">
					<i class="bi bi-lightbulb-fill text-accent-amber text-2xl" aria-hidden="true"></i>
					<div><h3 class="font-headline-sm text-headline-sm text-on-surface font-black"><?php echo esc_html( $d['formula_title'] ); ?></h3><?php if ( $d['formula_subtitle'] ) : ?><p class="font-body-sm text-body-sm text-on-surface-variant"><?php echo esc_html( $d['formula_subtitle'] ); ?></p><?php endif; ?></div>
				</div>
				<?php endif; ?>
				<?php if ( $d['formula_items'] ) : ?>
				<div class="grid grid-cols-1 md:grid-cols-4 gap-space-sm pt-space-sm">
					<?php foreach ( $d['formula_items'] as $f ) : ?>
					<div class="bg-surface-card rounded-xl p-space-md shadow-sm">
						<span class="font-body-sm text-body-sm text-on-surface-variant"><?php echo esc_html( $f['label'] ?? '' ); ?></span>
						<span class="block font-headline-md text-headline-md font-black <?php echo esc_attr( ls_tone( $f['tone'] ?? 'dark', 'text' ) ); ?> mt-1"><?php echo esc_html( $f['value'] ?? '' ); ?></span>
						<span class="text-body-sm text-on-surface-variant font-body-sm"><?php echo esc_html( $f['note'] ?? '' ); ?></span>
					</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
				<?php if ( $d['formula_steps'] ) : ?>
				<div class="mt-space-lg grid grid-cols-1 md:grid-cols-3 gap-space-md">
					<?php foreach ( $d['formula_steps'] as $i => $st ) : ?>
					<div class="flex gap-space-sm items-start">
						<span class="w-8 h-8 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-bold text-sm shrink-0"><?php echo esc_html( ls_fa_num( $i + 1 ) ); ?></span>
						<div><h3 class="font-label-nav text-label-nav text-on-surface font-bold"><?php echo esc_html( $st['title'] ?? '' ); ?></h3><p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed"><?php echo esc_html( $st['text'] ?? '' ); ?></p></div>
					</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
				<?php if ( $d['formula_html'] ) : ?><div class="ls-prose mt-space-md"><?php echo wp_kses_post( $d['formula_html'] ); ?></div><?php endif; ?>
			</div>
		</div>
		<?php endif; ?>

		<?php if ( isset( $tabs['reviews'] ) ) : ?>
		<div class="hidden flex-col gap-space-lg" role="tabpanel" data-ls-pane="reviews">
			<?php if ( $product ) : ?>
				<div class="ls-comments ls-wc-reviews"><?php comments_template(); ?></div>
			<?php else : ?>
			<div class="grid grid-cols-1 lg:grid-cols-3 gap-space-lg">
				<div class="bg-surface-canvas rounded-2xl p-space-lg flex flex-col gap-space-md">
					<div class="text-center flex flex-col items-center">
						<span class="font-display-hero text-display-hero text-on-surface font-black leading-none"><?php echo esc_html( ls_fa_num( $d['rating'] ) ); ?></span>
						<div class="flex items-center gap-1 text-accent-amber mt-2 text-lg"><?php echo str_repeat( '<i class="bi bi-star-fill" aria-hidden="true"></i>', 5 ); // phpcs:ignore ?></div>
						<span class="font-body-sm text-body-sm text-on-surface-variant mt-1"><?php echo esc_html( sprintf( /* translators: %s count */ __( 'بر اساس %s نظر رسمی خریداران کارگاهی', 'larijani' ), ls_fa_num( $d['review_count'] ) ) ); ?></span>
					</div>
					<div class="space-y-2 pt-space-xs font-body-sm text-body-sm">
						<?php foreach ( $d['rating_bars'] as $bar ) : ?>
						<div class="flex items-center gap-2"><span class="w-12 text-left"><?php echo esc_html( $bar['label'] ?? '' ); ?></span><div class="flex-1 h-2 rounded-full bg-surface-container overflow-hidden"><div class="h-full bg-accent-amber rounded-full" style="width: <?php echo esc_attr( (int) ( $bar['percent'] ?? 0 ) ); ?>%"></div></div><span class="w-8 text-right font-semibold"><?php echo esc_html( ls_fa_num( (int) ( $bar['percent'] ?? 0 ) ) ); ?>٪</span></div>
						<?php endforeach; ?>
					</div>
					<?php if ( $d['review_button'] ) : ?><a class="mt-space-sm w-full py-2.5 px-space-md rounded-full bg-primary-container hover:bg-primary text-on-primary font-label-nav text-label-nav transition-colors text-center" href="<?php echo esc_url( ls_whatsapp_url() ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $d['review_button'] ); ?></a><?php endif; ?>
				</div>
				<div class="lg:col-span-2 flex flex-col gap-space-md">
					<?php foreach ( $d['reviews'] as $r ) : ?>
					<div class="bg-surface-canvas rounded-xl p-space-md flex flex-col gap-space-xs">
						<div class="flex items-center justify-between gap-3">
							<div class="flex items-center gap-space-sm">
								<div class="w-10 h-10 rounded-full bg-surface-card flex items-center justify-center font-bold text-primary-container shadow-sm shrink-0"><?php echo esc_html( ls_initials( $r['name'] ?? '' ) ); ?></div>
								<div><p class="font-label-nav text-label-nav text-on-surface font-bold"><?php echo esc_html( $r['name'] ?? '' ); ?></p><span class="font-body-sm text-body-sm text-on-surface-variant"><?php echo esc_html( $r['meta'] ?? '' ); ?></span></div>
							</div>
							<div class="flex items-center text-accent-amber text-xs shrink-0"><?php echo str_repeat( '<i class="bi bi-star-fill" aria-hidden="true"></i>', max( 1, min( 5, (int) ( $r['stars'] ?? 5 ) ) ) ); // phpcs:ignore ?></div>
						</div>
						<p class="font-body-md text-body-md text-on-surface mt-1 leading-relaxed"><?php echo esc_html( $r['text'] ?? '' ); ?></p>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
	<?php
}
