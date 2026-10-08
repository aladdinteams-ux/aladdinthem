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
function larijani_elementor_widgets() {
	return apply_filters(
		'ls_elementor_widgets',
		array(
			// Site parts.
			'header'             => 'Larijani_Widget_Header',
			'footer'             => 'Larijani_Widget_Footer',
			'breadcrumb'         => 'Larijani_Widget_Breadcrumb',
			// Home.
			'hero'               => 'Larijani_Widget_Hero',
			'hero-classic'       => 'Larijani_Widget_Hero_Classic',
			'icon-cards'         => 'Larijani_Widget_Icon_Cards',
			'categories'         => 'Larijani_Widget_Categories',
			'products'           => 'Larijani_Widget_Products',
			'about'              => 'Larijani_Widget_About',
			'testimonials'       => 'Larijani_Widget_Testimonials',
			'posts'              => 'Larijani_Widget_Posts',
			'cta'                => 'Larijani_Widget_CTA',
			// Services.
			'page-hero'          => 'Larijani_Widget_Page_Hero',
			'steps'              => 'Larijani_Widget_Steps',
			'dark-feature'       => 'Larijani_Widget_Dark_Feature',
			'comparison'         => 'Larijani_Widget_Comparison',
			'lead-form'          => 'Larijani_Widget_Lead_Form',
			'faq'                => 'Larijani_Widget_FAQ',
			// Contact.
			'page-banner'        => 'Larijani_Widget_Page_Banner',
			'contact-cards'      => 'Larijani_Widget_Contact_Cards',
			'plant-card'         => 'Larijani_Widget_Plant_Card',
			'map'                => 'Larijani_Widget_Map',
			// Portfolio.
			'portfolio-hero'     => 'Larijani_Widget_Portfolio_Hero',
			'portfolio'          => 'Larijani_Widget_Portfolio',
			'performance'        => 'Larijani_Widget_Performance',
			'floating-cta'       => 'Larijani_Widget_Floating_CTA',
			// Shop.
			'shop-hero'          => 'Larijani_Widget_Shop_Hero',
			'catalog'            => 'Larijani_Widget_Catalog',
			'product-detail'     => 'Larijani_Widget_Product_Detail',
			'product-tabs'       => 'Larijani_Widget_Product_Tabs',
			// Blog.
			'blog-hero'          => 'Larijani_Widget_Blog_Hero',
			'featured-post'      => 'Larijani_Widget_Featured_Post',
			'posts-grid'         => 'Larijani_Widget_Posts_Grid',
			'sidebar-download'   => 'Larijani_Widget_Sidebar_Download',
			'sidebar-popular'    => 'Larijani_Widget_Sidebar_Popular',
			'sidebar-newsletter' => 'Larijani_Widget_Sidebar_Newsletter',
			'sidebar-tags'       => 'Larijani_Widget_Sidebar_Tags',
			'sidebar-cta'        => 'Larijani_Widget_Sidebar_CTA',
			'sidebar-promo'      => 'Larijani_Widget_Sidebar_Promo',
			'toc'                => 'Larijani_Widget_TOC',
			'calculator'         => 'Larijani_Widget_Calculator',
			// Single post (Theme Builder "Single" templates).
			'post-hero'          => 'Larijani_Widget_Post_Hero',
			'post-content'       => 'Larijani_Widget_Post_Content',
			'related-posts'      => 'Larijani_Widget_Related_Posts',
			'post-comments'      => 'Larijani_Widget_Post_Comments',
		)
	);
}

/**
 * Bootstrap once Elementor is loaded.
 */
function larijani_elementor_init() {
	add_action( 'elementor/elements/categories_registered', 'larijani_elementor_categories' );
	add_action( 'elementor/widgets/register', 'larijani_elementor_register_widgets' );
	add_filter( 'elementor/icons_manager/additional_tabs', 'larijani_elementor_icon_tabs' );
	add_action( 'elementor/dynamic_tags/register', 'larijani_elementor_dynamic_tags' );
	add_action( 'elementor/frontend/after_enqueue_styles', 'larijani_elementor_frontend_styles' );
	add_action( 'elementor/preview/enqueue_styles', 'larijani_elementor_frontend_styles' );
}
// Elementor fires "elementor/loaded" while plugins load, i.e. before the theme.
if ( did_action( 'elementor/loaded' ) ) {
	larijani_elementor_init();
} else {
	add_action( 'elementor/loaded', 'larijani_elementor_init' );
}

/**
 * Widget category.
 *
 * @param \Elementor\Elements_Manager $manager Manager.
 */
function larijani_elementor_categories( $manager ) {
	$manager->add_category(
		'larijani-stone',
		array(
			'title' => __( 'لاریجانی استون', 'larijani-stone' ),
			'icon'  => 'eicon-site-identity',
		)
	);
	$manager->add_category(
		'larijani-stone-single',
		array(
			'title' => __( 'لاریجانی – نوشته و آرشیو (تم‌بیلدر)', 'larijani-stone' ),
			'icon'  => 'eicon-post',
		)
	);
}

/**
 * Register widgets.
 *
 * @param \Elementor\Widgets_Manager $manager Manager.
 */
function larijani_elementor_register_widgets( $manager ) {
	// Loaded here (not earlier) so the widgets extend Elementor's own Widget_Base,
	// which is guaranteed to be available when Elementor registers widgets.
	require_once LARIJANI_DIR . '/inc/elementor/class-widget-base.php';
	if ( ! is_subclass_of( 'Larijani_Widget_Base', 'Elementor\\Widget_Base' ) ) {
		return; // Base was already bound to the no-Elementor fallback in this request.
	}
	foreach ( larijani_elementor_widgets() as $slug => $class ) {
		$file = LARIJANI_DIR . '/inc/elementor/widgets/' . $slug . '.php';
		if ( ! file_exists( $file ) ) {
			continue;
		}
		require_once $file;
		if ( class_exists( $class ) && is_subclass_of( $class, 'Elementor\\Widget_Base' ) ) {
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
function larijani_elementor_icon_tabs( $tabs ) {
	$tabs['bootstrap-icons'] = array(
		'name'          => 'bootstrap-icons',
		'label'         => __( 'Bootstrap Icons', 'larijani-stone' ),
		'url'           => LARIJANI_URI . '/assets/vendor/bootstrap-icons/bootstrap-icons.min.css',
		'enqueue'       => array( LARIJANI_URI . '/assets/vendor/bootstrap-icons/bootstrap-icons.min.css' ),
		'prefix'        => 'bi-',
		'displayPrefix' => 'bi',
		'labelIcon'     => 'bi bi-bootstrap-fill',
		'ver'           => '1.11.3',
		'fetchJson'     => LARIJANI_URI . '/assets/vendor/bootstrap-icons/elementor-icons.json',
		'native'        => false,
	);
	return $tabs;
}

/**
 * Theme styles inside Elementor pages / preview.
 */
function larijani_elementor_frontend_styles() {
	wp_enqueue_style( 'larijani-tailwind' );
	wp_enqueue_script( 'larijani-theme' );
}

/**
 * Dynamic tags (site contact info) – work in Elementor free & Pro.
 *
 * @param \Elementor\Core\DynamicTags\Manager $manager Manager.
 */
function larijani_elementor_dynamic_tags( $manager ) {
	require_once LARIJANI_DIR . '/inc/elementor/dynamic-tags.php';
	$manager->register_group(
		'larijani',
		array( 'title' => __( 'لاریجانی استون', 'larijani-stone' ) )
	);
	$manager->register( new Larijani_Tag_Contact_Text() );
	$manager->register( new Larijani_Tag_Contact_URL() );
	$manager->register( new Larijani_Tag_Post_Meta_Text() );
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
function larijani_kit_colors() {
	return array(
		array( '_id' => 'lsprimary', 'title' => 'LS – سبز برند (اصلی)', 'color' => larijani_opt( 'color_primary' ) ),
		array( '_id' => 'lsprimaryhover', 'title' => 'LS – سبز برند (هاور)', 'color' => larijani_opt( 'color_primary_hover' ) ),
		array( '_id' => 'lssecondary', 'title' => 'LS – سبز روشن', 'color' => larijani_opt( 'color_secondary' ) ),
		array( '_id' => 'lsdark', 'title' => 'LS – بازالت تیره', 'color' => larijani_opt( 'color_dark' ) ),
		array( '_id' => 'lsfooter', 'title' => 'LS – فوتر', 'color' => larijani_opt( 'color_footer' ) ),
		array( '_id' => 'lscanvas', 'title' => 'LS – زمینه کرم سنگی', 'color' => larijani_opt( 'color_canvas' ) ),
		array( '_id' => 'lsborder', 'title' => 'LS – خطوط', 'color' => larijani_opt( 'color_border' ) ),
		array( '_id' => 'lstext', 'title' => 'LS – متن', 'color' => larijani_opt( 'color_text' ) ),
	);
}

/**
 * Add the theme colours & fonts to the active Elementor kit (once, idempotent).
 *
 * @param bool $force  Re-sync the theme's own "LS –" colours.
 * @param bool $system Also replace Elementor's system colours/typography/layout (fresh sites only).
 */
function larijani_setup_elementor_kit( $force = false, $system = false ) {
	if ( ! larijani_has_elementor() ) {
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
	foreach ( larijani_kit_colors() as $c ) {
		$pos = array_search( $c['_id'], $ids, true );
		if ( false === $pos ) {
			$custom[] = $c;
		} elseif ( $force ) {
			$custom[ $pos ] = $c;
		}
	}
	$settings['custom_colors'] = $custom;

	// Elementor's own Site Settings (system colours/fonts, layout) belong to the user:
	// only filled when empty, or replaced when $system is explicitly requested.
	if ( $system || empty( $settings['system_colors'] ) ) {
		$settings['system_colors'] = array(
			array( '_id' => 'primary', 'title' => 'Primary', 'color' => '#5C6754' ),
			array( '_id' => 'secondary', 'title' => 'Secondary', 'color' => '#8DA184' ),
			array( '_id' => 'text', 'title' => 'Text', 'color' => '#2D3748' ),
			array( '_id' => 'accent', 'title' => 'Accent', 'color' => '#161D1A' ),
		);
	}
	if ( $system || empty( $settings['system_typography'] ) ) {
		$font = array( 'typography_typography' => 'custom', 'typography_font_family' => 'Vazirmatn' );
		$settings['system_typography'] = array(
			array_merge( array( '_id' => 'primary', 'title' => 'Primary' ), $font, array( 'typography_font_weight' => '900' ) ),
			array_merge( array( '_id' => 'secondary', 'title' => 'Secondary' ), $font, array( 'typography_font_weight' => '700' ) ),
			array_merge( array( '_id' => 'text', 'title' => 'Text' ), $font, array( 'typography_font_weight' => '400' ) ),
			array_merge( array( '_id' => 'accent', 'title' => 'Accent' ), $font, array( 'typography_font_weight' => '600' ) ),
		);
	}
	if ( $system || ! isset( $settings['space_between_widgets'] ) ) {
		$settings['space_between_widgets'] = array( 'column' => '0', 'row' => '0', 'isLinked' => true, 'unit' => 'px', 'size' => 0 );
	}
	if ( $system || empty( $settings['container_width'] ) ) {
		$settings['container_width'] = array( 'unit' => 'px', 'size' => 1280, 'sizes' => array() );
	}
	if ( $system || empty( $settings['body_typography_font_family'] ) ) {
		$settings['body_typography_typography']  = 'custom';
		$settings['body_typography_font_family'] = 'Vazirmatn';
	}

	update_post_meta( $kit_id, '_elementor_page_settings', $settings );
	if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	update_option( 'ls_kit_version', LARIJANI_VERSION );
}

/**
 * Run the kit setup after theme activation / Elementor activation / version change.
 */
function larijani_maybe_setup_kit() {
	if ( larijani_has_elementor() && get_option( 'ls_kit_version' ) !== LARIJANI_VERSION && current_user_can( 'manage_options' ) ) {
		larijani_setup_elementor_kit();
	}
}
add_action( 'admin_init', 'larijani_maybe_setup_kit' );

/**
 * Vazirmatn is bundled with the theme: register it as a custom font in Elementor's font list.
 *
 * @param array $fonts Fonts.
 * @return array
 */
function larijani_elementor_fonts( $fonts ) {
	$fonts['Vazirmatn'] = 'system';
	return $fonts;
}
add_filter( 'elementor/fonts/additional_fonts', 'larijani_elementor_fonts' );

/**
 * Disable Elementor's default colours & fonts (the theme provides its own) on activation.
 */
function larijani_elementor_defaults_on_switch() {
	update_option( 'elementor_disable_color_schemes', 'yes' );
	update_option( 'elementor_disable_typography_schemes', 'yes' );
	delete_option( 'ls_kit_version' );
}
add_action( 'after_switch_theme', 'larijani_elementor_defaults_on_switch' );
