<?php
/**
 * Single product in the Larijani design.
 * Elementor Pro "Single Product" templates or the free theme builder override it.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Larijani
 * @version 1.6.4
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

while ( have_posts() ) :
	the_post();
	if ( larijani_do_location( 'single' ) ) {
		continue;
	}
	global $product;
	$product = wc_get_product( get_the_ID() );
	if ( post_password_required() ) {
		echo get_the_password_form(); // phpcs:ignore
		continue;
	}
	do_action( 'woocommerce_before_single_product' );
	?>
	<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'ls-root bg-surface-canvas', $product ); ?>>
		<section class="w-full bg-surface-canvas py-space-sm">
			<div class="max-w-7xl mx-auto px-4 sm:px-gutter">
				<?php woocommerce_output_all_notices(); ?>
				<nav class="flex items-center flex-wrap gap-space-xs text-on-surface-variant font-body-sm text-body-sm" aria-label="<?php esc_attr_e( 'مسیر صفحه', 'larijani-stone' ); ?>"><?php echo larijani_breadcrumb_html(); // phpcs:ignore ?></nav>
			</div>
		</section>
		<section class="w-full py-space-md lg:py-space-xl">
			<div class="max-w-7xl mx-auto px-4 sm:px-gutter"><?php larijani_render_product_detail( larijani_wc_detail_data( $product ) ); ?></div>
		</section>

		<section class="w-full py-space-xl bg-surface-card shadow-sm">
			<div class="max-w-7xl mx-auto px-4 sm:px-gutter">
				<?php
				$larijani_rows = array();
				foreach ( $product->get_attributes() as $larijani_attr ) {
					if ( $larijani_attr->get_visible() ) {
						$larijani_vals   = $larijani_attr->is_taxonomy() ? wc_get_product_terms( $product->get_id(), $larijani_attr->get_name(), array( 'fields' => 'names' ) ) : $larijani_attr->get_options();
						$larijani_rows[] = array( 'label' => wc_attribute_label( $larijani_attr->get_name() ), 'value' => implode( '، ', $larijani_vals ) );
					}
				}
				$larijani_sheet   = get_post_meta( $product->get_id(), '_ls_datasheet', true );
				$larijani_formula = get_post_meta( $product->get_id(), '_ls_formulation', true );
				larijani_render_product_tabs(
					array(
						'product'         => $product,
						'spec_title'      => get_post_meta( $product->get_id(), '_ls_spec_title', true ),
						'spec_rows'       => $larijani_rows,
						'highlight_title' => get_post_meta( $product->get_id(), '_ls_highlight_title', true ),
						'highlight_text'  => get_post_meta( $product->get_id(), '_ls_highlight_text', true ),
						'datasheet_link'  => $larijani_sheet ? array( 'url' => $larijani_sheet, 'is_external' => true ) : '',
						'formula_title'   => $larijani_formula ? __( 'دستورالعمل و فرمولاسیون اختصاصی', 'larijani-stone' ) : '',
						'formula_html'    => $larijani_formula,
					)
				);
				?>
			</div>
		</section>
		<?php
		$larijani_related = wc_get_related_products( $product->get_id(), 4 );
		$larijani_upsells = $product->get_upsell_ids();
		$larijani_ids     = $larijani_upsells ? array_slice( $larijani_upsells, 0, 4 ) : $larijani_related;
		if ( $larijani_ids ) :
			?>
		<section class="w-full py-space-2xl bg-surface-canvas">
			<div class="max-w-7xl mx-auto px-4 sm:px-gutter flex flex-col gap-space-xl">
				<?php
				echo larijani_section_heading( // phpcs:ignore
					array(
						'eyebrow'   => __( 'تجهیزات و مواد مکمل خط تولید', 'larijani-stone' ),
						'title'     => __( 'مواد اولیه و ماشین‌آلات متناسب با این محصول', 'larijani-stone' ),
						'link_text' => __( 'مشاهده کل کاتالوگ فروشگاه', 'larijani-stone' ),
						'link'      => get_permalink( wc_get_page_id( 'shop' ) ),
						'style'     => 'token',
						'mb'        => '',
					)
				);
				?>
				<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-md">
					<?php
					foreach ( $larijani_ids as $larijani_id ) {
						$larijani_prod = wc_get_product( $larijani_id );
						if ( $larijani_prod ) {
							echo larijani_product_card( larijani_wc_card_data( $larijani_prod ), 'compact' ); // phpcs:ignore
						}
					}
					?>
				</div>
			</div>
		</section>
		<?php endif; ?>
	</div>
	<?php
	do_action( 'woocommerce_after_single_product' );
endwhile;

get_footer( 'shop' );
