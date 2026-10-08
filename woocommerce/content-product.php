<?php
/**
 * Product card inside WooCommerce loops (shortcodes, blocks, widgets).
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Larijani
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;
if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}
?>
<div <?php wc_product_class( 'ls-wc-loop-item grid', $product ); ?>>
	<?php echo larijani_product_card( larijani_wc_card_data( $product ), apply_filters( 'ls_wc_loop_card_style', 'catalog' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the card renderer. ?>
</div>
