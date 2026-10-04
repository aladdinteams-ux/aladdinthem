<?php
/**
 * Theme Builder integration.
 *
 * 1) Elementor Pro: every core location (header, footer, single, archive, 404,
 *    WooCommerce single product / product archive) is registered, so Pro's
 *    Theme Builder conditions work everywhere in this theme.
 * 2) Elementor (free): a built-in "mini theme builder" lets you pick any saved
 *    Elementor template (Templates › Saved Templates) for the header, footer,
 *    single post, archive, single product, shop, project and 404 locations
 *    from Customizer › Theme Builder.
 * 3) No Elementor: native PHP templates render the same design.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register Elementor Pro theme locations.
 *
 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $manager Manager.
 */
function ls_register_elementor_locations( $manager ) {
	$manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'ls_register_elementor_locations' );

/**
 * Saved Elementor templates for the Customizer selects.
 *
 * @return array
 */
function ls_elementor_template_choices() {
	$choices = array( 0 => __( '— پیش‌فرض قالب —', 'larijani' ) );
	if ( ! post_type_exists( 'elementor_library' ) ) {
		return $choices;
	}
	$posts = get_posts(
		array(
			'post_type'      => 'elementor_library',
			'posts_per_page' => 200,
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);
	foreach ( $posts as $p ) {
		$type                = get_post_meta( $p->ID, '_elementor_template_type', true );
		$choices[ $p->ID ] = $p->post_title . ( $type ? ' (' . $type . ')' : '' );
	}
	return $choices;
}

/**
 * Customizer controls for the free theme builder.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function ls_theme_builder_customizer( $wp_customize ) {
	$wp_customize->add_section(
		'ls_theme_builder',
		array(
			'title'       => __( 'تم‌بیلدر (قالب‌های المنتور)', 'larijani' ),
			'panel'       => 'ls_panel',
			'description' => __( 'هر قالب ذخیره‌شده المنتور را برای بخش‌های سایت انتخاب کنید. اگر المنتور پرو فعال باشد و برای یک بخش شرط نمایش تعیین کرده باشید، تنظیمات المنتور پرو اولویت دارد.', 'larijani' ),
		)
	);
	$locations = array(
		'ls_tb_header'          => __( 'هدر', 'larijani' ),
		'ls_tb_footer'          => __( 'فوتر', 'larijani' ),
		'ls_tb_single_post'     => __( 'تک‌نوشته (مقاله)', 'larijani' ),
		'ls_tb_archive'         => __( 'آرشیو وبلاگ / جستجو', 'larijani' ),
		'ls_tb_single_product'  => __( 'تک‌محصول ووکامرس', 'larijani' ),
		'ls_tb_shop'            => __( 'فروشگاه و دسته‌های محصول', 'larijani' ),
		'ls_tb_single_project'  => __( 'تک‌پروژه (نمونه‌کار)', 'larijani' ),
		'ls_tb_page'            => __( 'برگه‌های بدون المنتور', 'larijani' ),
		'ls_tb_404'             => __( 'صفحه ۴۰۴', 'larijani' ),
	);
	$choices = ls_elementor_template_choices();
	foreach ( $locations as $key => $label ) {
		$wp_customize->add_setting( $key, array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
		$wp_customize->add_control(
			$key,
			array(
				'label'   => $label,
				'section' => 'ls_theme_builder',
				'type'    => 'select',
				'choices' => $choices,
			)
		);
	}
}
add_action( 'customize_register', 'ls_theme_builder_customizer', 20 );

/**
 * Which free-theme-builder template applies to a location in the current context.
 *
 * @param string $location header|footer|single|archive|404.
 * @return int
 */
function ls_tb_template_for( $location ) {
	$id = 0;
	switch ( $location ) {
		case 'header':
			$id = ls_opt( 'tb_header' );
			break;
		case 'footer':
			$id = ls_opt( 'tb_footer' );
			break;
		case 'single':
			if ( is_singular( 'product' ) ) {
				$id = ls_opt( 'tb_single_product' );
			} elseif ( is_singular( 'ls_project' ) ) {
				$id = ls_opt( 'tb_single_project' );
			} elseif ( is_singular( 'post' ) ) {
				$id = ls_opt( 'tb_single_post' );
			} elseif ( is_page() && ! ls_is_built_with_elementor( get_the_ID() ) ) {
				$id = ls_opt( 'tb_page' );
			}
			break;
		case 'archive':
			if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ) {
				$id = ls_opt( 'tb_shop' );
			} else {
				$id = ls_opt( 'tb_archive' );
			}
			break;
		case '404':
			$id = ls_opt( 'tb_404' );
			break;
	}
	return (int) apply_filters( 'ls_tb_template_for', (int) $id, $location );
}

/**
 * Is a post built with Elementor?
 *
 * @param int $post_id Post id.
 * @return bool
 */
function ls_is_built_with_elementor( $post_id ) {
	return ls_has_elementor() && 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true );
}

/**
 * Render a saved Elementor template.
 *
 * @param int $template_id Template id.
 * @return bool Rendered?
 */
function ls_render_elementor_template( $template_id ) {
	if ( ! $template_id || ! ls_has_elementor() || 'publish' !== get_post_status( $template_id ) ) {
		return false;
	}
	$html = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_id, true );
	if ( '' === trim( (string) $html ) ) {
		return false;
	}
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor output.
	return true;
}

/**
 * Render a location: Elementor Pro → free theme builder → false (caller renders fallback).
 *
 * @param string $location Location.
 * @return bool
 */
function ls_do_location( $location ) {
	if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( $location ) ) {
		return true;
	}
	return ls_render_elementor_template( ls_tb_template_for( $location ) );
}

/**
 * Make sure Elementor's frontend CSS for theme-builder templates is enqueued early
 * (free Elementor renders inline CSS otherwise, which is fine but slower).
 */
function ls_tb_enqueue_template_css() {
	if ( ! ls_has_elementor() || ! class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
		return;
	}
	foreach ( array( 'header', 'footer', 'single', 'archive', '404' ) as $loc ) {
		$id = ls_tb_template_for( $loc );
		if ( $id ) {
			$css = \Elementor\Core\Files\CSS\Post::create( $id );
			$css->enqueue();
		}
	}
}
add_action( 'wp_enqueue_scripts', 'ls_tb_enqueue_template_css', 30 );
