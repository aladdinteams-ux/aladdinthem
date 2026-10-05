<?php
/**
 * Product loop start.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Larijani
 * @version 3.3.0
 */

defined( 'ABSPATH' ) || exit;
$ls_cols = max( 1, min( 4, (int) wc_get_loop_prop( 'columns' ) ) );
$ls_map  = array( 1 => 'xl:grid-cols-1', 2 => 'xl:grid-cols-2', 3 => 'xl:grid-cols-3', 4 => 'xl:grid-cols-4' );
?>
<div class="ls-root products grid grid-cols-1 md:grid-cols-2 <?php echo esc_attr( $ls_map[ $ls_cols ] ); ?> gap-5">
