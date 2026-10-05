<?php
/**
 * Shop / product category archive in the Larijani design.
 * Elementor Pro "Product Archive" templates or the free theme builder override it.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Larijani
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

if ( ! ls_do_location( 'archive' ) ) {
	do_action( 'woocommerce_before_main_content' );
	echo '<div class="ls-root">';
	{
		$ls_title = is_product_taxonomy() ? single_term_title( '', false ) : ( is_search() ? sprintf( /* translators: %s query */ __( 'نتایج جستجو برای «%s»', 'larijani' ), get_search_query() ) : woocommerce_page_title( false ) );
		$ls_desc  = is_product_taxonomy() && term_description() ? wp_strip_all_tags( term_description() ) : wp_strip_all_tags( get_post_field( 'post_excerpt', wc_get_page_id( 'shop' ) ) );
		?>
		<section class="w-full bg-gradient-to-b from-surface-container-high/40 via-surface-canvas to-surface-canvas pt-space-lg pb-space-md">
			<div class="max-w-7xl mx-auto px-4 sm:px-gutter flex flex-col gap-3">
				<nav class="flex items-center flex-wrap gap-2 text-on-surface-variant font-body-sm text-body-sm"><?php echo ls_breadcrumb_html(); // phpcs:ignore ?></nav>
				<h1 class="font-headline-lg text-headline-lg text-on-surface"><?php echo esc_html( $ls_title ); ?></h1>
				<?php if ( $ls_desc ) : ?><p class="font-body-md text-body-md text-on-surface-variant max-w-3xl leading-relaxed"><?php echo esc_html( $ls_desc ); ?></p><?php endif; ?>
			</div>
		</section>
		<?php
	}
	echo '<section class="w-full py-space-lg"><div class="max-w-7xl mx-auto px-4 sm:px-gutter">';
	woocommerce_output_all_notices();
	ls_render_catalog( array( 'source' => 'woocommerce', 'use_main_query' => 'yes' ) );
	echo '</div></section></div>';
	do_action( 'woocommerce_after_main_content' );
}

get_footer( 'shop' );
