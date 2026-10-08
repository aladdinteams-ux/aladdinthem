<?php
/**
 * Search form.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;
?>
<form role="search" method="get" class="search-form ls-root flex items-center gap-2 bg-white rounded-xl p-1.5 shadow-sm" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="flex-1 flex items-center gap-2 px-2">
		<span class="screen-reader-text"><?php esc_html_e( 'جستجو برای:', 'larijani-stone' ); ?></span>
		<i class="bi bi-search text-outline" aria-hidden="true"></i>
		<input type="search" class="w-full py-2 text-sm border-0 focus:ring-0" placeholder="<?php esc_attr_e( 'جستجو…', 'larijani-stone' ); ?>" value="<?php echo get_search_query(); ?>" name="s">
	</label>
	<button type="submit" class="px-4 py-2 rounded-lg bg-primary-container text-white text-sm font-bold"><?php esc_html_e( 'جستجو', 'larijani-stone' ); ?></button>
</form>
