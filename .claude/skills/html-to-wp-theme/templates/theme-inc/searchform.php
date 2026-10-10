<?php
/**
 * Search forms. Every search box of the theme is rendered here through
 * get_search_form() so plugins can filter it; "ls_variant" picks the design.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

$larijani_args    = isset( $args ) && is_array( $args ) ? $args : array();
$larijani_variant = isset( $larijani_args['ls_variant'] ) ? $larijani_args['ls_variant'] : '';
$larijani_action  = ! empty( $larijani_args['ls_action'] ) ? $larijani_args['ls_action'] : home_url( '/' );
$larijani_ph      = isset( $larijani_args['ls_placeholder'] ) ? $larijani_args['ls_placeholder'] : '';
$larijani_label   = __( 'جستجو', 'larijani-stone' );

if ( '404' === $larijani_variant ) : ?>
<form role="search" method="get" action="<?php echo esc_url( $larijani_action ); ?>" class="w-full max-w-xl bg-surface-card rounded-2xl shadow-sm p-2 flex items-center gap-2">
	<i class="bi bi-search text-outline text-lg px-3" aria-hidden="true"></i>
	<label class="screen-reader-text" for="ls-s-404"><?php echo esc_html( $larijani_label ); ?></label>
	<input id="ls-s-404" class="flex-1 py-3 border-0 focus:ring-0 text-on-surface placeholder:text-outline" type="search" name="s" placeholder="<?php esc_attr_e( 'جستجو در سایت…', 'larijani-stone' ); ?>">
	<button class="px-5 py-3 rounded-xl bg-primary-container hover:bg-primary text-white font-bold text-sm" type="submit"><?php esc_html_e( 'جستجو', 'larijani-stone' ); ?></button>
</form>
<?php elseif ( 'blog' === $larijani_variant ) : ?>
<form role="search" method="get" action="<?php echo esc_url( $larijani_action ); ?>" class="w-full max-w-2xl bg-surface-card rounded-2xl shadow-sm p-2 flex flex-col sm:flex-row items-center gap-2 mb-8">
	<input type="hidden" name="post_type" value="post">
	<label class="flex items-center gap-3 w-full px-3 py-2 flex-1">
		<i class="bi bi-search text-outline text-lg" aria-hidden="true"></i>
		<span class="screen-reader-text"><?php echo esc_html( $larijani_label ); ?></span>
		<input class="w-full bg-transparent border-0 p-0 focus:ring-0 text-on-surface placeholder:text-outline font-body-md text-body-md" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php echo esc_attr( $larijani_ph ); ?>" type="search">
	</label>
	<button class="w-full sm:w-auto px-6 py-3 rounded-xl bg-primary-container text-on-primary font-headline-sm text-headline-sm hover:bg-primary transition-all flex items-center justify-center gap-2 shadow-sm flex-shrink-0" type="submit">
		<span><?php echo esc_html( isset( $larijani_args['ls_button'] ) ? $larijani_args['ls_button'] : $larijani_label ); ?></span><i class="bi bi-arrow-left" aria-hidden="true"></i>
	</button>
</form>
<?php elseif ( 'header' === $larijani_variant ) : ?>
<form role="search" method="get" action="<?php echo esc_url( $larijani_action ); ?>" class="bg-white rounded-2xl shadow-2xl p-2 flex items-center gap-2">
	<i class="bi bi-search text-outline text-lg px-3" aria-hidden="true"></i>
	<label class="screen-reader-text" for="<?php echo esc_attr( $larijani_args['ls_id'] ?? 'ls-s-header' ); ?>"><?php echo esc_html( $larijani_label ); ?></label>
	<input id="<?php echo esc_attr( $larijani_args['ls_id'] ?? 'ls-s-header' ); ?>" class="flex-1 py-3 border-0 focus:ring-0 text-on-surface text-base placeholder:text-outline" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'جستجو در محصولات، قالب‌ها و مقالات…', 'larijani-stone' ); ?>" data-ls-autofocus>
	<?php if ( larijani_has_woo() ) : ?>
	<select name="post_type" class="hidden sm:block bg-surface-canvas rounded-xl py-2.5 pr-3 text-sm text-on-surface-variant" aria-label="<?php esc_attr_e( 'محدوده جستجو', 'larijani-stone' ); ?>">
		<option value=""><?php esc_html_e( 'همه', 'larijani-stone' ); ?></option>
		<option value="product"><?php esc_html_e( 'محصولات', 'larijani-stone' ); ?></option>
		<option value="post"><?php esc_html_e( 'مقالات', 'larijani-stone' ); ?></option>
	</select>
	<?php endif; ?>
	<button type="submit" class="px-5 py-3 rounded-xl bg-primary-container hover:bg-primary text-white font-bold text-sm transition-colors"><?php esc_html_e( 'جستجو', 'larijani-stone' ); ?></button>
	<button type="button" class="w-11 h-11 rounded-xl text-slate-500 hover:text-slate-800 flex items-center justify-center" data-ls-close aria-label="<?php esc_attr_e( 'بستن', 'larijani-stone' ); ?>"><i class="bi bi-x-lg" aria-hidden="true"></i></button>
</form>
<?php elseif ( 'catalog' === $larijani_variant || 'store' === $larijani_variant ) : ?>
<form class="relative w-full md:w-96" role="search" method="get" action="<?php echo esc_url( $larijani_args['ls_action'] ?? '' ); ?>" data-ls-catalog-search>
	<?php if ( ! empty( $larijani_args['ls_woo'] ) ) : ?><input type="hidden" name="post_type" value="product"><?php endif; ?>
	<label class="screen-reader-text" for="ls-catalog-s"><?php echo esc_html( $larijani_label ); ?></label>
	<?php if ( 'catalog' === $larijani_variant ) : ?>
	<i class="bi bi-search absolute right-3 top-1/2 -translate-y-1/2 text-outline" aria-hidden="true"></i>
	<input id="ls-catalog-s" class="w-full bg-surface-canvas text-on-surface pr-10 pl-4 py-2.5 rounded-xl font-body-md text-body-md placeholder:text-outline focus:outline-none focus:bg-surface-container-high transition-all" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php echo esc_attr( $larijani_ph ); ?>" type="search" data-ls-search-input>
	<?php else : ?>
	<input id="ls-catalog-s" class="w-full pr-11 pl-4 py-2.5 rounded-xl bg-surface-canvas text-on-surface font-body-md text-body-md focus:bg-surface-card transition-all placeholder:text-outline" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php echo esc_attr( $larijani_ph ); ?>" type="search" data-ls-search-input>
	<i class="bi bi-search absolute right-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant text-base" aria-hidden="true"></i>
	<?php endif; ?>
</form>
<?php else : ?>
<form role="search" method="get" class="search-form ls-root flex items-center gap-2 bg-white rounded-xl p-1.5 shadow-sm" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="flex-1 flex items-center gap-2 px-2">
		<span class="screen-reader-text"><?php esc_html_e( 'جستجو برای:', 'larijani-stone' ); ?></span>
		<i class="bi bi-search text-outline" aria-hidden="true"></i>
		<input type="search" class="w-full py-2 text-sm border-0 focus:ring-0" placeholder="<?php esc_attr_e( 'جستجو…', 'larijani-stone' ); ?>" value="<?php echo get_search_query(); ?>" name="s">
	</label>
	<button type="submit" class="px-4 py-2 rounded-lg bg-primary-container text-white text-sm font-bold"><?php esc_html_e( 'جستجو', 'larijani-stone' ); ?></button>
</form>
<?php
endif;
