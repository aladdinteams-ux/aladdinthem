<?php
/**
 * WooCommerce integration.
 *
 * - Shop / product category / single product templates in the Larijani design
 *   (overridable by Elementor Pro Theme Builder or the free theme builder).
 * - "اطلاعات لاریجانی" product data tab: badges, bulk price, area calculator,
 *   guarantees, formulation guide, datasheet …
 * - Automatic bulk (wholesale) price in the cart above a quantity threshold.
 * - Bootstrap icon field on product categories (used by the category chips).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme styles for cart / checkout / account.
 */
function larijani_wc_styles() {
	wp_enqueue_style( 'larijani-woocommerce', LARIJANI_URI . '/assets/css/woocommerce.css', array( 'woocommerce-general' ), larijani_asset_ver( 'assets/css/woocommerce.css' ) );
}
add_action( 'wp_enqueue_scripts', 'larijani_wc_styles', 25 );

/**
 * Products per page on the shop.
 *
 * @return int
 */
function larijani_wc_per_page() {
	return 12;
}
add_filter( 'loop_shop_per_page', 'larijani_wc_per_page' );

// Our templates print their own breadcrumb / wrappers.
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/**
 * Product data tab.
 *
 * @param array $tabs Tabs.
 * @return array
 */
function larijani_wc_product_tab( $tabs ) {
	$tabs['larijani'] = array(
		'label'    => __( 'اطلاعات لاریجانی', 'larijani-stone' ),
		'target'   => 'ls_product_data',
		'priority' => 65,
	);
	return $tabs;
}
add_filter( 'woocommerce_product_data_tabs', 'larijani_wc_product_tab' );

/**
 * Field definitions.
 *
 * @return array
 */
function larijani_wc_fields() {
	return array(
		'_ls_badge'           => array( 'text', __( 'برچسب روی کارت محصول', 'larijani-stone' ), '' ),
		'_ls_badge_tone'      => array( 'select', __( 'رنگ برچسب', 'larijani-stone' ), '', larijani_tone_options() ),
		'_ls_subtitle'        => array( 'text', __( 'ویژگی کوتاه کارت (سبک کلاسیک)', 'larijani-stone' ), '' ),
		'_ls_price_label'     => array( 'text', __( 'عنوان قیمت (مثلاً: قیمت هر عدد)', 'larijani-stone' ), '' ),
		'_ls_stock_text'      => array( 'text', __( 'متن موجودی (مثلاً: موجود در انبار آبیک – ارسال ۲۴ ساعته)', 'larijani-stone' ), '' ),
		'_ls_quality_note'    => array( 'text', __( 'تأییدیه کیفی کنار امتیاز', 'larijani-stone' ), '' ),
		'_ls_image_badges'    => array( 'textarea', __( 'برچسب‌های روی تصویر – هر خط: متن|رنگ(primary/amber)|آیکون', 'larijani-stone' ), 'ضمانت مادام‌العمر عدم شکستن ABS|primary|bi bi-shield-check' ),
		'_ls_trust'           => array( 'textarea', __( 'نشان‌های زیر گالری – هر خط: عنوان|متن|آیکون', 'larijani-stone' ), '۵۰۰+ سیکل بتن|ماندگاری فرم تضمینی|bi bi-arrow-repeat' ),
		'_ls_bulk_price'      => array( 'number', __( 'قیمت عمده (واحد)', 'larijani-stone' ), '' ),
		'_ls_bulk_threshold'  => array( 'number', __( 'حداقل تعداد برای قیمت عمده', 'larijani-stone' ), '' ),
		'_ls_area_per_unit'   => array( 'number', __( 'سطح تولید هر عدد (برای ماشین‌حساب)', 'larijani-stone' ), '0.15' ),
		'_ls_area_unit'       => array( 'text', __( 'واحد سطح', 'larijani-stone' ), 'مترمربع' ),
		'_ls_default_qty'     => array( 'number', __( 'تعداد پیش‌فرض', 'larijani-stone' ), '1' ),
		'_ls_guarantees'      => array( 'textarea', __( 'تضمین‌ها – هر خط: عنوان|متن|آیکون|رنگ(emerald/amber)', 'larijani-stone' ), 'تضمین تعویض بی‌قیدوشرط:|در صورت هرگونه تغییر فرم …|bi bi-shield-fill-check|emerald' ),
		'_ls_spec_title'      => array( 'text', __( 'عنوان تب مشخصات', 'larijani-stone' ), '' ),
		'_ls_highlight_title' => array( 'text', __( 'عنوان کارت ویژه تب مشخصات', 'larijani-stone' ), '' ),
		'_ls_highlight_text'  => array( 'textarea', __( 'متن کارت ویژه', 'larijani-stone' ), '' ),
		'_ls_datasheet'       => array( 'text', __( 'لینک فایل دیتاشیت PDF', 'larijani-stone' ), 'https://' ),
		'_ls_formulation'     => array( 'textarea', __( 'دستورالعمل / فرمولاسیون (HTML مجاز)', 'larijani-stone' ), '' ),
	);
}

/**
 * Product data panel.
 */
function larijani_wc_product_panel() {
	echo '<div id="ls_product_data" class="panel woocommerce_options_panel hidden"><div class="options_group">';
	foreach ( larijani_wc_fields() as $key => $f ) {
		$args = array(
			'id'          => $key,
			'label'       => $f[1],
			'placeholder' => $f[2],
		);
		if ( 'textarea' === $f[0] ) {
			woocommerce_wp_textarea_input( $args );
		} elseif ( 'select' === $f[0] ) {
			$args['options'] = array_merge( array( '' => '—' ), $f[3] );
			woocommerce_wp_select( $args );
		} else {
			$args['type'] = 'number' === $f[0] ? 'number' : 'text';
			if ( 'number' === $f[0] ) {
				$args['custom_attributes'] = array( 'step' => 'any', 'min' => '0' );
			}
			woocommerce_wp_text_input( $args );
		}
	}
	echo '</div></div>';
}
add_action( 'woocommerce_product_data_panels', 'larijani_wc_product_panel' );

/**
 * Save product fields.
 *
 * @param WC_Product $product Product.
 */
function larijani_wc_save_product( $product ) {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- WooCommerce verifies the product save nonce.
	foreach ( larijani_wc_fields() as $key => $f ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$raw = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( '_ls_formulation' === $key ) {
			$val = wp_kses_post( $raw );
		} elseif ( 'textarea' === $f[0] ) {
			$val = sanitize_textarea_field( $raw );
		} elseif ( 'number' === $f[0] ) {
			$val = '' === $raw ? '' : (float) $raw;
		} elseif ( '_ls_datasheet' === $key ) {
			$val = esc_url_raw( $raw );
		} else {
			$val = sanitize_text_field( $raw );
		}
		$product->update_meta_data( $key, $val );
	}
	// phpcs:enable
}
add_action( 'woocommerce_admin_process_product_object', 'larijani_wc_save_product' );

/**
 * Bulk pricing in the cart.
 *
 * @param WC_Cart $cart Cart.
 */
function larijani_wc_bulk_pricing( $cart ) {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return;
	}
	foreach ( $cart->get_cart() as $item ) {
		$product = $item['data'];
		$pid     = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
		$bulk    = (float) get_post_meta( $pid, '_ls_bulk_price', true );
		$min     = (int) get_post_meta( $pid, '_ls_bulk_threshold', true );
		if ( $bulk > 0 && $min > 0 && (int) $item['quantity'] >= $min ) {
			$product->set_price( $bulk );
		}
	}
}
add_action( 'woocommerce_before_calculate_totals', 'larijani_wc_bulk_pricing', 20 );

/**
 * Icon field on product categories.
 *
 * @param WP_Term|string $term Term (edit) or taxonomy (add).
 */
function larijani_wc_cat_icon_field( $term ) {
	$val = is_object( $term ) ? get_term_meta( $term->term_id, 'ls_icon', true ) : '';
	$label = esc_html__( 'آیکون (کلاس Bootstrap Icons، مثل bi bi-grid-3x3-gap)', 'larijani-stone' );
	if ( is_object( $term ) ) {
		printf( '<tr class="form-field"><th><label for="ls_icon">%s</label></th><td><input type="text" id="ls_icon" name="ls_icon" value="%s"></td></tr>', $label, esc_attr( $val ) ); // phpcs:ignore
	} else {
		printf( '<div class="form-field"><label for="ls_icon">%s</label><input type="text" id="ls_icon" name="ls_icon" value=""></div>', $label ); // phpcs:ignore
	}
	wp_nonce_field( 'ls_cat_icon', 'ls_cat_icon_nonce' );
}
add_action( 'product_cat_add_form_fields', 'larijani_wc_cat_icon_field' );
add_action( 'product_cat_edit_form_fields', 'larijani_wc_cat_icon_field' );

/**
 * Save category icon.
 *
 * @param int $term_id Term id.
 */
function larijani_wc_save_cat_icon( $term_id ) {
	if ( ! isset( $_POST['ls_cat_icon_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ls_cat_icon_nonce'] ) ), 'ls_cat_icon' ) ) {
		return;
	}
	if ( isset( $_POST['ls_icon'] ) ) {
		update_term_meta( $term_id, 'ls_icon', sanitize_text_field( wp_unslash( $_POST['ls_icon'] ) ) );
	}
}
add_action( 'created_product_cat', 'larijani_wc_save_cat_icon' );
add_action( 'edited_product_cat', 'larijani_wc_save_cat_icon' );

/**
 * Cart count fragment for AJAX add to cart (header badge, if used).
 *
 * @param array $fragments Fragments.
 * @return array
 */
function larijani_wc_cart_fragment( $fragments ) {
	$fragments['span.ls-cart-count'] = '<span class="ls-cart-count">' . esc_html( larijani_fa_num( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ) ) . '</span>';
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'larijani_wc_cart_fragment' );

/**
 * WooCommerce collects Product/Offer structured data on
 * "woocommerce_single_product_summary", which the theme's product layouts
 * (PHP template, Elementor widgets, theme-builder templates) do not fire.
 * Collect it just before WooCommerce prints its JSON-LD in the footer.
 * Real reviews/ratings only — nothing is generated by the theme.
 */
function larijani_wc_product_structured_data() {
	if ( ! is_product() || did_action( 'woocommerce_single_product_summary' ) || ! isset( WC()->structured_data ) ) {
		return;
	}
	if ( ! apply_filters( 'ls_wc_product_structured_data', true ) ) {
		return;
	}
	$product = wc_get_product( get_queried_object_id() );
	if ( $product ) {
		WC()->structured_data->generate_product_data( $product );
	}
}
add_action( 'wp_footer', 'larijani_wc_product_structured_data', 9 );
