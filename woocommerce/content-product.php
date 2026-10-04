<?php
/**
 * Product card inside WooCommerce loops (shortcodes, blocks, widgets).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

global $product;
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
echo ls_product_card( ls_wc_card_data( $product ), apply_filters( 'ls_wc_loop_card_style', 'catalog' ) ); // phpcs:ignore
