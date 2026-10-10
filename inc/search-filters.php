<?php
/**
 * Product search panel (hero widget) → real filtering of the shop.
 *
 * The hero form sends:
 *   - the active tab as a product category (WooCommerce: product_cat, without
 *     WooCommerce: ls_cat for the theme catalog page);
 *   - one GET parameter per select field; its value is either the slug of a
 *     product tag / attribute term, or search words separated by commas;
 *   - ls_hs = comma-separated list of the parameters that must filter
 *     (informational fields such as "order size" are not listed).
 *
 * Values saved by older versions (molds, paving, abs …) are mapped through
 * larijani_search_aliases() so existing pages keep working.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Old default values → current meaning. 'info' = not a filter.
 *
 * @return array
 */
function larijani_search_aliases() {
	return apply_filters(
		'ls_search_aliases',
		array(
			// Tabs / categories.
			'molds'     => 'mold',
			'chemicals' => 'chemical',
			'materials' => 'chemical',
			// Product type.
			'paving'    => 'کفپوش,واش',
			'facade'    => 'نما,سه‌بعدی,صخره',
			'curb'      => 'جدول,دورباغچه',
			'stairs'    => 'پله',
			'machinery' => 'میز ویبره,میکسر',
			// Material.
			'abs'       => 'ABS',
			'polymer'   => 'پلیمر,کامپوزیت',
			'rubber'    => 'لاستیک',
			'alloy'     => 'فولاد,ST52,ST-37',
			// Order size: informational only.
			'single'    => 'info',
			'medium'    => 'info',
			'bulk'      => 'info',
		)
	);
}

/**
 * Parse one hero option line: "Label|value|tab1,tab2".
 *
 * @param string $line Line.
 * @return array { label, value, tabs[] }
 */
function larijani_search_option( $line ) {
	$p = array_map( 'trim', explode( '|', $line ) );
	return array(
		'label' => $p[0],
		'value' => isset( $p[1] ) ? $p[1] : $p[0],
		'tabs'  => isset( $p[2] ) && '' !== $p[2] ? array_map( 'trim', explode( ',', $p[2] ) ) : array(),
	);
}

/**
 * Active search filters of the current request.
 *
 * @return array[] Each: param, value, label (for display), terms (array) or words (array).
 */
function larijani_active_search_filters() {
	static $cache = null;
	if ( null === $cache ) {
		$cache = larijani_active_search_filters_uncached();
	}
	return $cache;
}

/**
 * Compute the active filters from $_GET (see larijani_active_search_filters()).
 *
 * @return array[]
 */
function larijani_active_search_filters_uncached() {
	$cache = array();
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only GET filters.
	if ( empty( $_GET['ls_hs'] ) ) {
		return $cache;
	}
	$params  = array_slice( array_filter( array_map( 'sanitize_key', explode( ',', sanitize_text_field( wp_unslash( $_GET['ls_hs'] ) ) ) ) ), 0, 6 );
	$aliases = larijani_search_aliases();
	foreach ( $params as $param ) {
		if ( in_array( $param, array( 's', 'post_type', 'product_cat', 'ls_cat', 'paged', 'orderby', 'ls_hs' ), true ) || empty( $_GET[ $param ] ) || ! is_string( $_GET[ $param ] ) ) {
			continue;
		}
		$value = mb_substr( sanitize_text_field( wp_unslash( $_GET[ $param ] ) ), 0, 120 );
		$key   = strtolower( $value );
		if ( isset( $aliases[ $key ] ) ) {
			if ( 'info' === $aliases[ $key ] ) {
				continue;
			}
			$value = $aliases[ $key ];
		}
		$filter = array(
			'param' => $param,
			'value' => $value,
			'terms' => array(),
			'words' => array(),
		);
		// A product tag or attribute term with this slug wins over word matching.
		if ( larijani_has_woo() ) {
			$taxes = array_merge( array( 'product_tag' ), function_exists( 'wc_get_attribute_taxonomy_names' ) ? wc_get_attribute_taxonomy_names() : array() );
			foreach ( $taxes as $tax ) {
				$term = get_term_by( 'slug', sanitize_title( $value ), $tax );
				if ( $term && ! is_wp_error( $term ) ) {
					$filter['terms'] = array( $tax, (int) $term->term_id );
					$filter['label'] = $term->name;
					break;
				}
			}
		}
		if ( ! $filter['terms'] ) {
			$filter['words'] = array_slice( array_filter( array_map( 'trim', preg_split( '/[,،]+/u', $value ) ), 'strlen' ), 0, 8 );
			$filter['label'] = implode( '، ', $filter['words'] );
		}
		if ( $filter['terms'] || $filter['words'] ) {
			$cache[] = $filter;
		}
	}
	// phpcs:enable
	return $cache;
}

/**
 * Normalise ?product_cat= from the hero tabs (old values / unknown slugs never cause a 404).
 *
 * @param array $vars Query vars.
 * @return array
 */
function larijani_search_request( $vars ) {
	if ( empty( $vars['product_cat'] ) || ! is_string( $vars['product_cat'] ) || ! larijani_has_woo() || ! isset( $_GET['product_cat'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- only ?product_cat= from search forms, never pretty category URLs.
		return $vars;
	}
	$slug    = sanitize_title( $vars['product_cat'] );
	$aliases = larijani_search_aliases();
	if ( term_exists( $slug, 'product_cat' ) ) {
		return $vars;
	}
	if ( isset( $aliases[ $slug ] ) && term_exists( $aliases[ $slug ], 'product_cat' ) ) {
		$vars['product_cat'] = $aliases[ $slug ];
	} else {
		unset( $vars['product_cat'] ); // Unknown category: show the whole shop rather than an error page.
		if ( empty( $vars['post_type'] ) ) {
			$vars['post_type'] = 'product';
		}
	}
	return $vars;
}
add_filter( 'request', 'larijani_search_request' );

/**
 * Apply the filters to the main shop query.
 *
 * @param WP_Query $q Query.
 */
function larijani_search_pre_get_posts( $q ) {
	if ( is_admin() || ! $q->is_main_query() || ! larijani_has_woo() ) {
		return;
	}
	$filters = larijani_active_search_filters();
	if ( ! $filters ) {
		return;
	}
	$tax = (array) $q->get( 'tax_query' );
	foreach ( $filters as $f ) {
		if ( $f['terms'] ) {
			$tax[] = array(
				'taxonomy' => $f['terms'][0],
				'field'    => 'term_id',
				'terms'    => array( $f['terms'][1] ),
			);
		}
	}
	if ( count( $tax ) > 1 && ! isset( $tax['relation'] ) ) {
		$tax['relation'] = 'AND';
	}
	$q->set( 'tax_query', array_filter( $tax ) );
	foreach ( $filters as $f ) {
		if ( $f['words'] ) {
			$q->set( 'larijani_words', true );
		}
	}
}
add_action( 'pre_get_posts', 'larijani_search_pre_get_posts', 20 );

/**
 * Word filters: every field must match one of its words in the product
 * title, short description, description or SKU.
 *
 * @param string   $where WHERE clause.
 * @param WP_Query $q Query.
 * @return string
 */
function larijani_search_posts_where( $where, $q ) {
	if ( ! $q->get( 'larijani_words' ) ) {
		return $where;
	}
	global $wpdb;
	foreach ( larijani_active_search_filters() as $f ) {
		if ( ! $f['words'] ) {
			continue;
		}
		$or = array();
		foreach ( $f['words'] as $w ) {
			$like = '%' . $wpdb->esc_like( $w ) . '%';
			$or[] = $wpdb->prepare(
				"({$wpdb->posts}.post_title LIKE %s OR {$wpdb->posts}.post_excerpt LIKE %s OR {$wpdb->posts}.post_content LIKE %s OR EXISTS (SELECT 1 FROM {$wpdb->postmeta} lsm WHERE lsm.post_id = {$wpdb->posts}.ID AND lsm.meta_key = '_sku' AND lsm.meta_value LIKE %s))",
				$like,
				$like,
				$like,
				$like
			);
		}
		$where .= ' AND (' . implode( ' OR ', $or ) . ')';
	}
	return $where;
}
add_filter( 'posts_where', 'larijani_search_posts_where', 10, 2 );

/**
 * Bar above the results: active filters with remove links, and an order-size note.
 *
 * @return string
 */
function larijani_search_filter_bar() {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	if ( empty( $_GET['ls_hs'] ) ) {
		return '';
	}
	$filters = larijani_active_search_filters();
	$scale   = isset( $_GET['scale'] ) && is_string( $_GET['scale'] ) ? sanitize_key( wp_unslash( $_GET['scale'] ) ) : '';
	// phpcs:enable
	if ( ! $filters && ! in_array( $scale, array( 'medium', 'bulk' ), true ) ) {
		return '';
	}
	$out = '<div class="flex flex-wrap items-center gap-2 rounded-2xl bg-surface-card shadow-sm px-4 py-3 text-body-sm font-body-sm" data-ls-active-filters>';
	if ( $filters ) {
		$out .= '<span class="text-on-surface-variant">' . esc_html__( 'فیلترهای جستجو:', 'larijani-stone' ) . '</span>';
		foreach ( $filters as $f ) {
			$out .= '<a class="inline-flex items-center gap-1.5 rounded-full bg-surface-canvas hover:bg-surface-container px-3 py-1 font-semibold text-on-surface transition-colors" href="' . esc_url( remove_query_arg( array( $f['param'], 'paged' ) ) ) . '" aria-label="' . esc_attr( sprintf( /* translators: %s filter */ __( 'حذف فیلتر %s', 'larijani-stone' ), $f['label'] ) ) . '">' . esc_html( $f['label'] ) . '<i class="bi bi-x-lg text-[11px]" aria-hidden="true"></i></a>';
		}
		$out .= '<a class="text-primary font-semibold hover:underline ms-auto" href="' . esc_url( remove_query_arg( array_merge( wp_list_pluck( $filters, 'param' ), array( 'ls_hs', 'scale', 'paged' ) ) ) ) . '">' . esc_html__( 'حذف همه فیلترها', 'larijani-stone' ) . '</a>';
	}
	if ( in_array( $scale, array( 'medium', 'bulk' ), true ) ) {
		$out .= '<p class="basis-full flex items-center gap-2 text-on-surface-variant pt-1"><i class="bi bi-box-seam text-primary" aria-hidden="true"></i>' . esc_html__( 'برای سفارش تیراژ بالا و پروژه‌ای، قیمت عمده و زمان تحویل اختصاصی اعلام می‌شود.', 'larijani-stone' ) . ' <a class="text-primary font-bold hover:underline" href="' . esc_url( larijani_tel( larijani_opt( 'phone_1' ) ) ) . '">' . esc_html__( 'استعلام قیمت عمده', 'larijani-stone' ) . '</a></p>';
	}
	return $out . '</div>';
}
