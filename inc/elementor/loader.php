<?php
/**
 * Elementor integration loader: widget category, widgets, icon library,
 * dynamic tags, global colours / typography (Site Settings) and editor tweaks.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Widget class map: file slug => class name.
 *
 * @return array
 */
function ls_elementor_widgets() {
	return apply_filters(
		'ls_elementor_widgets',
		array(
			// Site parts.
			'header'             => 'LS_Widget_Header',
			'footer'             => 'LS_Widget_Footer',
			'breadcrumb'         => 'LS_Widget_Breadcrumb',
			// Home.
			'hero'               => 'LS_Widget_Hero',
			'hero-classic'       => 'LS_Widget_Hero_Classic',
			'icon-cards'         => 'LS_Widget_Icon_Cards',
			'categories'         => 'LS_Widget_Categories',
			'products'           => 'LS_Widget_Products',
			'about'              => 'LS_Widget_About',
			'testimonials'       => 'LS_Widget_Testimonials',
			'posts'              => 'LS_Widget_Posts',
			'cta'                => 'LS_Widget_CTA',
			// Services.
			'page-hero'          => 'LS_Widget_Page_Hero',
			'steps'              => 'LS_Widget_Steps',
			'dark-feature'       => 'LS_Widget_Dark_Feature',
			'comparison'         => 'LS_Widget_Comparison',
			'lead-form'          => 'LS_Widget_Lead_Form',
			'faq'                => 'LS_Widget_FAQ',
			// Contact.
			'page-banner'        => 'LS_Widget_Page_Banner',
			'contact-cards'      => 'LS_Widget_Contact_Cards',
			'plant-card'         => 'LS_Widget_Plant_Card',
			'map'                => 'LS_Widget_Map',
			// Portfolio.
			'portfolio-hero'     => 'LS_Widget_Portfolio_Hero',
			'portfolio'          => 'LS_Widget_Portfolio',
			'performance'        => 'LS_Widget_Performance',
			'floating-cta'       => 'LS_Widget_Floating_CTA',
			// Shop.
			'shop-hero'          => 'LS_Widget_Shop_Hero',
			'catalog'            => 'LS_Widget_Catalog',
			'product-detail'     => 'LS_Widget_Product_Detail',
			'product-tabs'       => 'LS_Widget_Product_Tabs',
			// Blog.
			'blog-hero'          => 'LS_Widget_Blog_Hero',
			'featured-post'      => 'LS_Widget_Featured_Post',
			'posts-grid'         => 'LS_Widget_Posts_Grid',
			'sidebar-download'   => 'LS_Widget_Sidebar_Download',
			'sidebar-popular'    => 'LS_Widget_Sidebar_Popular',
			'sidebar-newsletter' => 'LS_Widget_Sidebar_Newsletter',
			'sidebar-tags'       => 'LS_Widget_Sidebar_Tags',
			'sidebar-cta'        => 'LS_Widget_Sidebar_CTA',
			'sidebar-promo'      => 'LS_Widget_Sidebar_Promo',
			'toc'                => 'LS_Widget_TOC',
			'calculator'         => 'LS_Widget_Calculator',
			// Single post (Theme Builder "Single" templates).
			'post-hero'          => 'LS_Widget_Post_Hero',
			'post-content'       => 'LS_Widget_Post_Content',
			'related-posts'      => 'LS_Widget_Related_Posts',
			'post-comments'      => 'LS_Widget_Post_Comments',
		)
	);
}

/**
 * Bootstrap once Elementor is loaded.
 */
function ls_elementor_init() {
	require_once LS_DIR . '/inc/elementor/class-widget-base.php';
	add_action( 'elementor/elements/categories_registered', 'ls_elementor_categories' );
	add_action( 'elementor/widgets/register', 'ls_elementor_register_widgets' );
	add_filter( 'elementor/icons_manager/additional_tabs', 'ls_elementor_icon_tabs' );
	add_action( 'elementor/dynamic_tags/register', 'ls_elementor_dynamic_tags' );
	add_action( 'elementor/frontend/after_enqueue_styles', 'ls_elementor_frontend_styles' );
	add_action( 'elementor/preview/enqueue_styles', 'ls_elementor_frontend_styles' );
}
// Elementor fires "elementor/loaded" while plugins load, i.e. before the theme.
if ( did_action( 'elementor/loaded' ) ) {
	ls_elementor_init();
} else {
	add_action( 'elementor/loaded', 'ls_elementor_init' );
}

/**
 * Widget category.
 *
 * @param \Elementor\Elements_Manager $manager Manager.
 */
function ls_elementor_categories( $manager ) {
	$manager->add_category(
		'larijani-stone',
		array(
			'title' => __( 'لاریجانی استون', 'larijani' ),
			'icon'  => 'eicon-site-identity',
		)
	);
	$manager->add_category(
		'larijani-stone-single',
		array(
			'title' => __( 'لاریجانی – نوشته و آرشیو (تم‌بیلدر)', 'larijani' ),
			'icon'  => 'eicon-post',
		)
	);
}

/**
 * Register widgets.
 *
 * @param \Elementor\Widgets_Manager $manager Manager.
 */
function ls_elementor_register_widgets( $manager ) {
	foreach ( ls_elementor_widgets() as $slug => $class ) {
		$file = LS_DIR . '/inc/elementor/widgets/' . $slug . '.php';
		if ( ! file_exists( $file ) ) {
			continue;
		}
		require_once $file;
		if ( class_exists( $class ) ) {
			$manager->register( new $class() );
		}
	}
}

/**
 * Bootstrap Icons as an Elementor icon library tab.
 *
 * @param array $tabs Tabs.
 * @return array
 */
function ls_elementor_icon_tabs( $tabs ) {
	$tabs['bootstrap-icons'] = array(
		'name'          => 'bootstrap-icons',
		'label'         => __( 'Bootstrap Icons', 'larijani' ),
		'url'           => LS_URI . '/assets/vendor/bootstrap-icons/bootstrap-icons.min.css',
		'enqueue'       => array( LS_URI . '/assets/vendor/bootstrap-icons/bootstrap-icons.min.css' ),
		'prefix'        => 'bi-',
		'displayPrefix' => 'bi',
		'labelIcon'     => 'bi bi-bootstrap-fill',
		'ver'           => '1.11.3',
		'fetchJson'     => LS_URI . '/assets/vendor/bootstrap-icons/elementor-icons.json',
		'native'        => false,
	);
	return $tabs;
}

/**
 * Theme styles inside Elementor pages / preview.
 */
function ls_elementor_frontend_styles() {
	wp_enqueue_style( 'larijani-tailwind' );
	wp_enqueue_script( 'larijani-theme' );
}

/**
 * Dynamic tags (site contact info) – work in Elementor free & Pro.
 *
 * @param \Elementor\Core\DynamicTags\Manager $manager Manager.
 */
function ls_elementor_dynamic_tags( $manager ) {
	require_once LS_DIR . '/inc/elementor/dynamic-tags.php';
	$manager->register_group(
		'larijani',
		array( 'title' => __( 'لاریجانی استون', 'larijani' ) )
	);
	$manager->register( new LS_Tag_Contact_Text() );
	$manager->register( new LS_Tag_Contact_URL() );
	$manager->register( new LS_Tag_Post_Meta_Text() );
}

/* -------------------------------------------------------------------------
 * Elementor Site Settings (Kit): global colours + typography.
 * ---------------------------------------------------------------------- */

/**
 * Global colours registered in the active Elementor kit. The theme CSS reads
 * them as --e-global-color-{id}, so changing a colour in
 * Site Settings › Global Colors restyles every theme widget.
 *
 * @return array
 */
function ls_kit_colors() {
	return array(
		array( '_id' => 'lsprimary', 'title' => 'LS – سبز برند (اصلی)', 'color' => ls_opt( 'color_primary' ) ),
		array( '_id' => 'lsprimaryhover', 'title' => 'LS – سبز برند (هاور)', 'color' => ls_opt( 'color_primary_hover' ) ),
		array( '_id' => 'lssecondary', 'title' => 'LS – سبز روشن', 'color' => ls_opt( 'color_secondary' ) ),
		array( '_id' => 'lsdark', 'title' => 'LS – بازالت تیره', 'color' => ls_opt( 'color_dark' ) ),
		array( '_id' => 'lsfooter', 'title' => 'LS – فوتر', 'color' => ls_opt( 'color_footer' ) ),
		array( '_id' => 'lscanvas', 'title' => 'LS – زمینه کرم سنگی', 'color' => ls_opt( 'color_canvas' ) ),
		array( '_id' => 'lsborder', 'title' => 'LS – خطوط', 'color' => ls_opt( 'color_border' ) ),
		array( '_id' => 'lstext', 'title' => 'LS – متن', 'color' => ls_opt( 'color_text' ) ),
	);
}

/**
 * Add the theme colours & fonts to the active Elementor kit (once, idempotent).
 *
 * @param bool $force Overwrite existing values.
 */
function ls_setup_elementor_kit( $force = false ) {
	if ( ! ls_has_elementor() ) {
		return;
	}
	$kit_id = (int) get_option( 'elementor_active_kit' );
	if ( ! $kit_id ) {
		return;
	}
	$settings = get_post_meta( $kit_id, '_elementor_page_settings', true );
	$settings = is_array( $settings ) ? $settings : array();

	$custom = isset( $settings['custom_colors'] ) && is_array( $settings['custom_colors'] ) ? $settings['custom_colors'] : array();
	$ids    = wp_list_pluck( $custom, '_id' );
	foreach ( ls_kit_colors() as $c ) {
		$pos = array_search( $c['_id'], $ids, true );
		if ( false === $pos ) {
			$custom[] = $c;
		} elseif ( $force ) {
			$custom[ $pos ] = $c;
		}
	}
	$settings['custom_colors'] = $custom;

	if ( $force || empty( $settings['system_colors'] ) ) {
		$settings['system_colors'] = array(
			array( '_id' => 'primary', 'title' => 'Primary', 'color' => '#5C6754' ),
			array( '_id' => 'secondary', 'title' => 'Secondary', 'color' => '#8DA184' ),
			array( '_id' => 'text', 'title' => 'Text', 'color' => '#2D3748' ),
			array( '_id' => 'accent', 'title' => 'Accent', 'color' => '#161D1A' ),
		);
	}
	if ( $force || empty( $settings['system_typography'] ) ) {
		$font = array( 'typography_typography' => 'custom', 'typography_font_family' => 'Vazirmatn' );
		$settings['system_typography'] = array(
			array_merge( array( '_id' => 'primary', 'title' => 'Primary' ), $font, array( 'typography_font_weight' => '900' ) ),
			array_merge( array( '_id' => 'secondary', 'title' => 'Secondary' ), $font, array( 'typography_font_weight' => '700' ) ),
			array_merge( array( '_id' => 'text', 'title' => 'Text' ), $font, array( 'typography_font_weight' => '400' ) ),
			array_merge( array( '_id' => 'accent', 'title' => 'Accent' ), $font, array( 'typography_font_weight' => '600' ) ),
		);
	}
	if ( $force || ! isset( $settings['space_between_widgets'] ) ) {
		$settings['space_between_widgets'] = array( 'column' => '0', 'row' => '0', 'isLinked' => true, 'unit' => 'px', 'size' => 0 );
	}
	if ( $force || empty( $settings['container_width'] ) ) {
		$settings['container_width'] = array( 'unit' => 'px', 'size' => 1280, 'sizes' => array() );
	}
	$settings['body_typography_typography']  = 'custom';
	$settings['body_typography_font_family'] = 'Vazirmatn';

	update_post_meta( $kit_id, '_elementor_page_settings', $settings );
	if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	update_option( 'ls_kit_version', LS_VERSION );
}

/**
 * Run the kit setup after theme activation / Elementor activation / version change.
 */
function ls_maybe_setup_kit() {
	if ( ls_has_elementor() && get_option( 'ls_kit_version' ) !== LS_VERSION && current_user_can( 'manage_options' ) ) {
		ls_setup_elementor_kit();
	}
}
add_action( 'admin_init', 'ls_maybe_setup_kit' );

/**
 * Vazirmatn is bundled with the theme: register it as a custom font in Elementor's font list.
 *
 * @param array $fonts Fonts.
 * @return array
 */
function ls_elementor_fonts( $fonts ) {
	$fonts['Vazirmatn'] = 'system';
	return $fonts;
}
add_filter( 'elementor/fonts/additional_fonts', 'ls_elementor_fonts' );

/**
 * Disable Elementor's default colours & fonts (the theme provides its own) on activation.
 */
function ls_elementor_defaults_on_switch() {
	update_option( 'elementor_disable_color_schemes', 'yes' );
	update_option( 'elementor_disable_typography_schemes', 'yes' );
	delete_option( 'ls_kit_version' );
}
add_action( 'after_switch_theme', 'ls_elementor_defaults_on_switch' );
