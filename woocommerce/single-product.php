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
	if ( ls_do_location( 'single' ) ) {
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
				<nav class="flex items-center flex-wrap gap-space-xs text-on-surface-variant font-body-sm text-body-sm" aria-label="<?php esc_attr_e( 'مسیر صفحه', 'larijani' ); ?>"><?php echo ls_breadcrumb_html(); // phpcs:ignore ?></nav>
			</div>
		</section>
		<section class="w-full py-space-md lg:py-space-xl">
			<div class="max-w-7xl mx-auto px-4 sm:px-gutter"><?php ls_render_product_detail( ls_wc_detail_data( $product ) ); ?></div>
		</section>

		<section class="w-full py-space-xl bg-surface-card shadow-sm">
			<div class="max-w-7xl mx-auto px-4 sm:px-gutter">
				<?php
				$ls_rows = array();
				foreach ( $product->get_attributes() as $ls_attr ) {
					if ( $ls_attr->get_visible() ) {
						$ls_vals   = $ls_attr->is_taxonomy() ? wc_get_product_terms( $product->get_id(), $ls_attr->get_name(), array( 'fields' => 'names' ) ) : $ls_attr->get_options();
						$ls_rows[] = array( 'label' => wc_attribute_label( $ls_attr->get_name() ), 'value' => implode( '، ', $ls_vals ) );
					}
				}
				$ls_sheet   = get_post_meta( $product->get_id(), '_ls_datasheet', true );
				$ls_formula = get_post_meta( $product->get_id(), '_ls_formulation', true );
				ls_render_product_tabs(
					array(
						'product'         => $product,
						'spec_title'      => get_post_meta( $product->get_id(), '_ls_spec_title', true ),
						'spec_rows'       => $ls_rows,
						'highlight_title' => get_post_meta( $product->get_id(), '_ls_highlight_title', true ),
						'highlight_text'  => get_post_meta( $product->get_id(), '_ls_highlight_text', true ),
						'datasheet_link'  => $ls_sheet ? array( 'url' => $ls_sheet, 'is_external' => true ) : '',
						'formula_title'   => $ls_formula ? __( 'دستورالعمل و فرمولاسیون اختصاصی', 'larijani' ) : '',
						'formula_html'    => $ls_formula,
					)
				);
				?>
			</div>
		</section>
		<?php
		$ls_related = wc_get_related_products( $product->get_id(), 4 );
		$ls_upsells = $product->get_upsell_ids();
		$ls_ids     = $ls_upsells ? array_slice( $ls_upsells, 0, 4 ) : $ls_related;
		if ( $ls_ids ) :
			?>
		<section class="w-full py-space-2xl bg-surface-canvas">
			<div class="max-w-7xl mx-auto px-4 sm:px-gutter flex flex-col gap-space-xl">
				<?php
				echo ls_section_heading( // phpcs:ignore
					array(
						'eyebrow'   => __( 'تجهیزات و مواد مکمل خط تولید', 'larijani' ),
						'title'     => __( 'مواد اولیه و ماشین‌آلات متناسب با این محصول', 'larijani' ),
						'link_text' => __( 'مشاهده کل کاتالوگ فروشگاه', 'larijani' ),
						'link'      => get_permalink( wc_get_page_id( 'shop' ) ),
						'style'     => 'token',
						'mb'        => '',
					)
				);
				?>
				<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-md">
					<?php
					foreach ( $ls_ids as $ls_id ) {
						$ls_prod = wc_get_product( $ls_id );
						if ( $ls_prod ) {
							echo ls_product_card( ls_wc_card_data( $ls_prod ), 'compact' ); // phpcs:ignore
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
