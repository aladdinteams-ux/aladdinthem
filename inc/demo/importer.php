<?php
/**
 * One-click setup: pages, menus, Theme Builder templates, sample content.
 * Appearance › راه‌اندازی قالب لاریجانی.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

require_once LS_DIR . '/inc/demo/pages.php';

/**
 * Random Elementor element id.
 *
 * @return string
 */
function ls_el_id() {
	return substr( md5( wp_rand() . microtime() ), 0, 7 );
}

/**
 * Normalise demo shorthand values (image keys, icon names, plain URLs) in widget settings.
 *
 * @param array $settings Settings.
 * @return array
 */
function ls_el_normalize( $settings ) {
	foreach ( $settings as $key => $val ) {
		if ( is_string( $val ) && 0 === strpos( $val, 'demo:' ) ) {
			$fn = 'ls_demo_' . substr( $val, 5 );
			$val = function_exists( $fn ) ? call_user_func( $fn ) : array();
		}
		if ( is_array( $val ) && isset( $val[0] ) && is_array( $val[0] ) ) {
			foreach ( $val as $i => $row ) {
				$val[ $i ] = ls_el_normalize( $row );
				$val[ $i ]['_id'] = ls_el_id();
			}
			$settings[ $key ] = $val;
			continue;
		}
		if ( is_string( $val ) ) {
			if ( 'image' === $key && '' !== $val ) {
				$settings[ $key ] = ls_demo_media( $val );
			} elseif ( preg_match( '/(^|_)icon$/', $key ) && '' !== $val && false === strpos( $val, ' ' ) ) {
				$settings[ $key ] = ls_bi( $val );
			} elseif ( preg_match( '/(^|_)link$/', $key ) ) {
				$settings[ $key ] = array( 'url' => $val );
			} else {
				$settings[ $key ] = $val;
			}
		} else {
			$settings[ $key ] = $val;
		}
	}
	return $settings;
}

/**
 * Widget element.
 *
 * @param array $w [ 'w' => type, 's' => settings ].
 * @return array
 */
function ls_el_widget( $w ) {
	return array(
		'id'         => ls_el_id(),
		'elType'     => 'widget',
		'widgetType' => $w['w'],
		'settings'   => ls_el_normalize( $w['s'] ?? array() ),
		'elements'   => array(),
	);
}

/**
 * Build Elementor data from layout rows.
 *
 * @param array $rows Rows.
 * @return array
 */
function ls_el_build( $rows ) {
	$zero = array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true );
	$out  = array();
	foreach ( $rows as $row ) {
		if ( isset( $row['cols'] ) ) {
			$cols = array();
			foreach ( $row['cols'] as $col ) {
				$cols[] = array(
					'id'       => ls_el_id(),
					'elType'   => 'column',
					'settings' => array(
						'_column_size' => (int) round( $col[0] ),
						'_inline_size' => $col[0],
						'padding'      => array( 'unit' => 'px', 'top' => '0', 'right' => '12', 'bottom' => '0', 'left' => '12', 'isLinked' => false ),
					),
					'elements' => array_map( 'ls_el_widget', $col[1] ),
				);
			}
			$out[] = array(
				'id'       => ls_el_id(),
				'elType'   => 'section',
				'settings' => array(
					'layout'          => 'boxed',
					'content_width'   => array( 'unit' => 'px', 'size' => 1280 ),
					'gap'             => 'no',
					'padding'         => array( 'unit' => 'px', 'top' => '40', 'right' => '20', 'bottom' => '40', 'left' => '20', 'isLinked' => false ),
					'padding_mobile'  => array( 'unit' => 'px', 'top' => '24', 'right' => '4', 'bottom' => '24', 'left' => '4', 'isLinked' => false ),
					'structure'       => '20',
				),
				'elements' => $cols,
			);
			continue;
		}
		$out[] = array(
			'id'       => ls_el_id(),
			'elType'   => 'section',
			'settings' => array(
				'layout'  => 'full_width',
				'gap'     => 'no',
				'padding' => $zero,
			),
			'elements' => array(
				array(
					'id'       => ls_el_id(),
					'elType'   => 'column',
					'settings' => array( '_column_size' => 100, '_inline_size' => null, 'padding' => $zero ),
					'elements' => array( ls_el_widget( $row ) ),
				),
			),
		);
	}
	return $out;
}

/**
 * Admin page (under the «لاریجانی استون» menu).
 */
function ls_setup_menu() {
	add_submenu_page( 'ls-settings', __( 'راه‌اندازی و درون‌ریزی', 'larijani' ), __( 'راه‌اندازی و درون‌ریزی', 'larijani' ), 'manage_options', 'ls-setup', 'ls_setup_page' );
}
add_action( 'admin_menu', 'ls_setup_menu', 20 );

/**
 * Automatic setup when the theme is activated: pages, Theme Builder
 * templates, menus, front page, sample content and Elementor global colours.
 * Runs once per site (re-run any time from the setup page). Demo images are
 * copied to the media library in the background (WP-Cron).
 *
 * Disable with: add_filter( 'ls_auto_setup_on_activation', '__return_false' );
 */
function ls_auto_setup() {
	if ( get_option( 'ls_auto_setup_done' ) || ! apply_filters( 'ls_auto_setup_on_activation', true ) ) {
		return;
	}
	if ( ! current_user_can( 'switch_themes' ) && ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		update_option( 'ls_auto_setup_pending', 1, false ); // Finish on the next admin visit.
		return;
	}
	delete_option( 'ls_auto_setup_pending' );
	$fresh = (bool) get_option( 'fresh_site' );
	$parts = array( 'pages', 'templates', 'assign', 'menus', 'projects', 'kit' );
	// Sample articles / products only where they cannot clutter real content.
	$posts = wp_count_posts( 'post' );
	if ( $fresh || (int) $posts->publish <= 1 ) {
		$parts[] = 'posts';
	}
	if ( ls_has_woo() && ! wc_get_products( array( 'limit' => 1, 'return' => 'ids' ) ) ) {
		$parts[] = 'products';
	}
	if ( $fresh ) {
		ls_cleanup_fresh_site();
	}
	$report = ls_run_import( $parts );
	update_option( 'ls_auto_setup_done', time(), false );
	update_option(
		'ls_setup_state',
		array(
			'elementor' => ls_has_elementor(),
			'pro'       => ls_has_elementor_pro(),
			'woo'       => ls_has_woo(),
		),
		false
	);
	ls_schedule_image_import();
	set_transient( 'ls_import_report', $report, HOUR_IN_SECONDS );
	set_transient( 'ls_show_setup_notice', 'done', DAY_IN_SECONDS );
}
add_action( 'after_switch_theme', 'ls_auto_setup' );

/**
 * Theme activated by a non-interactive request (e.g. WP-CLI): run the setup on the next admin visit.
 */
function ls_auto_setup_pending() {
	if ( get_option( 'ls_auto_setup_pending' ) && current_user_can( 'switch_themes' ) && ! wp_doing_ajax() ) {
		ls_auto_setup();
	}
}
add_action( 'admin_init', 'ls_auto_setup_pending', 5 );

/**
 * Remove WordPress' sample content on a brand-new site (Hello world!, Sample
 * Page, default widgets) and enable pretty permalinks.
 */
function ls_cleanup_fresh_site() {
	$hello = get_page_by_path( 'hello-world', OBJECT, 'post' );
	if ( $hello ) {
		wp_delete_post( $hello->ID, true );
	}
	$sample = get_page_by_path( 'sample-page', OBJECT, 'page' );
	if ( $sample ) {
		wp_delete_post( $sample->ID, true );
	}
	$default_cat = get_term( (int) get_option( 'default_category' ), 'category' );
	if ( $default_cat && ! is_wp_error( $default_cat ) && 'uncategorized' === $default_cat->slug ) {
		wp_update_term( $default_cat->term_id, 'category', array( 'name' => __( 'مقالات عمومی', 'larijani' ) ) );
	}
	$sidebars = get_option( 'sidebars_widgets', array() );
	foreach ( array( 'blog-sidebar', 'shop-sidebar', 'sidebar-1', 'sidebar-2' ) as $sb ) {
		if ( ! empty( $sidebars[ $sb ] ) ) {
			$sidebars['wp_inactive_widgets'] = array_merge( (array) ( $sidebars['wp_inactive_widgets'] ?? array() ), (array) $sidebars[ $sb ] );
			$sidebars[ $sb ]                 = array();
		}
	}
	update_option( 'sidebars_widgets', $sidebars );
	if ( '' === (string) get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
}

/**
 * Finish the setup when Elementor, Elementor Pro or WooCommerce is activated
 * after the theme (templates, conditions, products, shop page).
 */
function ls_maybe_complete_setup() {
	$state = get_option( 'ls_setup_state' );
	if ( ! is_array( $state ) || ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) {
		return;
	}
	$parts = array();
	if ( ls_has_elementor() && empty( $state['elementor'] ) ) {
		$parts = array_merge( $parts, array( 'templates', 'assign', 'kit' ) );
	}
	if ( ls_has_elementor_pro() && empty( $state['pro'] ) ) {
		$parts = array_merge( $parts, array( 'templates', 'assign' ) );
	}
	if ( ls_has_woo() && empty( $state['woo'] ) ) {
		$parts = array_merge( $parts, array( 'templates', 'assign', 'products' ) );
		$shop  = get_page_by_path( 'shop' );
		if ( $shop && get_post_meta( $shop->ID, '_ls_demo_page', true ) && (int) get_option( 'woocommerce_shop_page_id' ) !== $shop->ID ) {
			update_option( 'woocommerce_shop_page_id', $shop->ID );
		}
	}
	update_option(
		'ls_setup_state',
		array(
			'elementor' => ls_has_elementor(),
			'pro'       => ls_has_elementor_pro(),
			'woo'       => ls_has_woo(),
		),
		false
	);
	if ( $parts ) {
		ls_run_import( array_unique( $parts ) );
		ls_schedule_image_import();
	}
}
add_action( 'admin_init', 'ls_maybe_complete_setup', 20 );

/**
 * Queue the background copy of the design images into the media library.
 */
function ls_schedule_image_import() {
	if ( apply_filters( 'ls_import_demo_images', true ) && ! wp_next_scheduled( 'ls_import_images_event' ) ) {
		wp_schedule_single_event( time() + 20, 'ls_import_images_event' );
	}
}

/**
 * Cron: sideload images, set featured images and switch saved layouts to the local copies.
 */
function ls_import_images_cron() {
	ls_import_images();
	ls_attach_demo_thumbnails();
	ls_localize_demo_urls();
}
add_action( 'ls_import_images_event', 'ls_import_images_cron' );

/**
 * Give demo posts / projects / products their imported featured image.
 */
function ls_attach_demo_thumbnails() {
	$items = get_posts(
		array(
			'post_type'      => array( 'post', 'ls_project', 'product' ),
			'post_status'    => 'any',
			'posts_per_page' => 200,
			'meta_key'       => '_ls_demo_image', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'fields'         => 'ids',
		)
	);
	foreach ( $items as $id ) {
		if ( has_post_thumbnail( $id ) ) {
			continue;
		}
		$att = ls_demo_image_id( get_post_meta( $id, '_ls_demo_image', true ) );
		if ( $att ) {
			set_post_thumbnail( $id, $att );
		}
	}
}

/**
 * Replace remote design image URLs saved in the theme's pages/templates with
 * the media-library copies (keeps the site independent of the design CDN).
 */
function ls_localize_demo_urls() {
	$local = get_option( 'ls_demo_images_local', array() );
	if ( ! is_array( $local ) || ! $local ) {
		return;
	}
	$remote = ls_demo_images();
	$search = array();
	$repl   = array();
	foreach ( $local as $key => $url ) {
		if ( ! empty( $remote[ $key ] ) && $url ) {
			$search[] = str_replace( '/', '\\/', $remote[ $key ] );
			$repl[]   = str_replace( '/', '\\/', $url );
			$search[] = $remote[ $key ];
			$repl[]   = $url;
		}
	}
	$ids = get_posts(
		array(
			'post_type'      => array( 'page', 'elementor_library' ),
			'post_status'    => 'any',
			'posts_per_page' => 100,
			'meta_key'       => '_ls_demo_page', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'fields'         => 'ids',
		)
	);
	foreach ( $ids as $id ) {
		$data = get_post_meta( $id, '_elementor_data', true );
		if ( ! is_string( $data ) || '' === $data ) {
			continue;
		}
		$new = str_replace( $search, $repl, $data );
		if ( $new !== $data ) {
			update_post_meta( $id, '_elementor_data', wp_slash( $new ) );
		}
	}
	if ( ls_has_elementor() ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
}

/**
 * Setup notice.
 */
function ls_setup_notice() {
	$flag = get_transient( 'ls_show_setup_notice' );
	if ( ! $flag || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_GET['page'] ) && 'ls-setup' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	if ( 'done' === $flag ) {
		printf(
			'<div class="notice notice-success is-dismissible"><p><strong>%s</strong> %s <a class="button button-primary" href="%s">%s</a> <a class="button" href="%s" target="_blank">%s</a></p></div>',
			esc_html__( 'قالب لاریجانی استون فعال شد و برگه‌ها، منوها و صفحه اصلی به‌صورت خودکار ساخته شدند.', 'larijani' ),
			esc_html__( 'تنظیمات قالب در منوی «لاریجانی استون» پیشخوان است.', 'larijani' ),
			esc_url( admin_url( 'admin.php?page=ls-settings' ) ),
			esc_html__( 'تنظیمات قالب', 'larijani' ),
			esc_url( home_url( '/' ) ),
			esc_html__( 'مشاهده سایت', 'larijani' )
		);
		return;
	}
	printf(
		'<div class="notice notice-success is-dismissible"><p><strong>%s</strong> %s <a class="button button-primary" href="%s">%s</a></p></div>',
		esc_html__( 'قالب لاریجانی استون فعال شد.', 'larijani' ),
		esc_html__( 'برای ساخت خودکار صفحات، منوها و قالب‌های تم‌بیلدر:', 'larijani' ),
		esc_url( admin_url( 'admin.php?page=ls-setup' ) ),
		esc_html__( 'راه‌اندازی یک‌کلیکی', 'larijani' )
	);
}
add_action( 'admin_notices', 'ls_setup_notice' );

/**
 * Setup page markup.
 */
function ls_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$report = get_transient( 'ls_import_report' );
	delete_transient( 'ls_import_report' );
	$status = array(
		__( 'المنتور (رایگان)', 'larijani' )  => ls_has_elementor(),
		__( 'المنتور پرو (تم‌بیلدر)', 'larijani' ) => ls_has_elementor_pro(),
		__( 'ووکامرس', 'larijani' )          => ls_has_woo(),
	);
	?>
	<div class="wrap ls-admin" dir="rtl">
		<h1><?php esc_html_e( 'راه‌اندازی قالب لاریجانی استون', 'larijani' ); ?></h1>
		<?php if ( $report ) : ?>
		<div class="notice notice-success"><p><?php echo wp_kses_post( $report ); ?></p></div>
		<?php endif; ?>
		<div class="ls-card ls-card-wide">
			<h2><?php esc_html_e( 'وضعیت افزونه‌ها', 'larijani' ); ?></h2>
			<ul>
				<?php foreach ( $status as $label => $ok ) : ?>
				<li><?php echo $ok ? '✅' : '⚪'; ?> <?php echo esc_html( $label ); ?></li>
				<?php endforeach; ?>
			</ul>
			<p><?php esc_html_e( 'المنتور رایگان برای ویرایش بصری لازم است. با المنتور پرو، قالب‌های هدر، فوتر، تک‌نوشته، آرشیو، محصول و فروشگاه با شرط نمایش در تم‌بیلدر ثبت می‌شوند. بدون پرو، همین قالب‌ها از طریق «سفارشی‌سازی › تنظیمات قالب لاریجانی › تم‌بیلدر» به سایت متصل می‌شوند.', 'larijani' ); ?></p>
		</div>
		<form method="post" class="ls-card ls-card-wide">
			<?php wp_nonce_field( 'ls_import', 'ls_import_nonce' ); ?>
			<h2><?php esc_html_e( 'درون‌ریزی دمو', 'larijani' ); ?></h2>
			<p><label><input type="checkbox" name="ls_parts[]" value="pages" checked> <?php esc_html_e( 'برگه‌ها (اصلی، اصلی کلاسیک، خدمات، نمونه‌کارها، فروشگاه/کاتالوگ، تماس، وبلاگ، نمونه محصول)', 'larijani' ); ?></label></p>
			<p><label><input type="checkbox" name="ls_parts[]" value="templates" checked> <?php esc_html_e( 'قالب‌های تم‌بیلدر (هدر، فوتر، تک‌نوشته، آرشیو، محصول، فروشگاه، ۴۰۴)', 'larijani' ); ?></label></p>
			<p><label><input type="checkbox" name="ls_parts[]" value="assign" checked> <?php esc_html_e( 'اتصال قالب‌های تم‌بیلدر به سایت (شرط نمایش پرو یا تنظیمات سفارشی‌سازی)', 'larijani' ); ?></label></p>
			<p><label><input type="checkbox" name="ls_parts[]" value="menus" checked> <?php esc_html_e( 'منوها (اصلی، موبایل، فوتر)', 'larijani' ); ?></label></p>
			<p><label><input type="checkbox" name="ls_parts[]" value="posts" checked> <?php esc_html_e( 'مقالات نمونه وبلاگ', 'larijani' ); ?></label></p>
			<p><label><input type="checkbox" name="ls_parts[]" value="projects" checked> <?php esc_html_e( 'نمونه‌کارهای نمونه', 'larijani' ); ?></label></p>
			<?php if ( ls_has_woo() ) : ?>
			<p><label><input type="checkbox" name="ls_parts[]" value="products" checked> <?php esc_html_e( 'محصولات نمونه ووکامرس', 'larijani' ); ?></label></p>
			<?php endif; ?>
			<p><label><input type="checkbox" name="ls_parts[]" value="images" checked> <?php esc_html_e( 'کپی تصاویر طرح در کتابخانه رسانه (نیازمند دسترسی سرور به اینترنت)', 'larijani' ); ?></label></p>
			<p><label><input type="checkbox" name="ls_parts[]" value="kit" checked> <?php esc_html_e( 'رنگ‌ها و فونت سراسری المنتور (Site Settings)', 'larijani' ); ?></label></p>
			<?php submit_button( __( 'شروع راه‌اندازی', 'larijani' ), 'primary', 'ls_do_import' ); ?>
			<p class="description"><?php esc_html_e( 'برگه‌ها و قالب‌های موجود با همین نامک بازنویسی نمی‌شوند؛ اجرای دوباره امن است.', 'larijani' ); ?></p>
		</form>
		<div class="ls-card ls-card-wide">
			<h2><?php esc_html_e( 'فایل‌های قالب المنتور (JSON)', 'larijani' ); ?></h2>
			<p><?php esc_html_e( 'در پوشه elementor-templates قالب، فایل JSON همه صفحات و بخش‌ها قرار دارد. از «قالب‌ها › قالب‌های ذخیره‌شده › درون‌ریزی» می‌توانید آن‌ها را جداگانه وارد کنید.', 'larijani' ); ?></p>
		</div>
	</div>
	<?php
}

/**
 * Handle the import request.
 */
function ls_handle_import() {
	if ( empty( $_POST['ls_do_import'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	check_admin_referer( 'ls_import', 'ls_import_nonce' );
	$parts  = isset( $_POST['ls_parts'] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_POST['ls_parts'] ) ) : array();
	$report = ls_run_import( $parts );
	set_transient( 'ls_import_report', $report, MINUTE_IN_SECONDS * 5 );
	delete_transient( 'ls_show_setup_notice' );
	wp_safe_redirect( admin_url( 'admin.php?page=ls-setup' ) );
	exit;
}
add_action( 'admin_init', 'ls_handle_import' );

/**
 * Run the import (also usable from WP-CLI: wp eval 'ls_run_import();').
 *
 * @param array $parts Parts.
 * @return string Report HTML.
 */
function ls_run_import( $parts = array( 'pages', 'templates', 'assign', 'menus', 'posts', 'projects', 'products', 'images', 'kit' ) ) {
	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	}
	$log = array();

	if ( in_array( 'images', $parts, true ) ) {
		$log[] = sprintf( /* translators: %d count */ __( '%d تصویر در کتابخانه رسانه کپی شد.', 'larijani' ), ls_import_images() );
	}
	if ( in_array( 'kit', $parts, true ) && ls_has_elementor() ) {
		ls_setup_elementor_kit( true );
		$log[] = __( 'رنگ‌ها و فونت سراسری المنتور تنظیم شد.', 'larijani' );
	}

	$layouts = ls_demo_layouts();
	$pages   = array();
	if ( in_array( 'pages', $parts, true ) ) {
		$map = array(
			'home'           => 'home',
			'home-classic'   => 'home-classic',
			'services'       => 'services',
			'portfolio'      => 'portfolio',
			'contact'        => 'contact',
			'catalog'        => 'shop',
			'store'          => 'products',
			'product-sample' => 'product-sample',
		);
		foreach ( $map as $key => $slug ) {
			// With WooCommerce the real shop / product pages use the theme's templates instead.
			if ( in_array( $key, array( 'product-sample', 'catalog', 'store' ), true ) && ls_has_woo() ) {
				continue;
			}
			$pages[ $key ] = ls_import_page( $slug, $layouts[ $key ]['title'], $layouts[ $key ]['rows'] );
			if ( ! empty( $layouts[ $key ]['meta'] ) && $pages[ $key ] ) {
				foreach ( $layouts[ $key ]['meta'] as $mk => $mv ) {
					add_post_meta( $pages[ $key ], $mk, $mv, true );
				}
			}
		}
		$blog = get_page_by_path( 'blog' );
		$pages['blog'] = $blog ? $blog->ID : wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => __( 'وبلاگ تخصصی', 'larijani' ), 'post_name' => 'blog' ) );
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $pages['home'] );
		update_option( 'page_for_posts', $pages['blog'] );
		$log[] = sprintf( /* translators: %d count */ __( '%d برگه ساخته/به‌روز شد و صفحه اصلی و وبلاگ تنظیم شدند.', 'larijani' ), count( $pages ) );
	}

	if ( in_array( 'templates', $parts, true ) && ls_has_elementor() ) {
		$assign = in_array( 'assign', $parts, true );
		$n      = 0;
		foreach ( $layouts as $key => $layout ) {
			if ( 0 !== strpos( $key, 'tpl-' ) ) {
				continue;
			}
			if ( in_array( $layout['kind'], array( 'product', 'product-archive' ), true ) && ! ls_has_woo() ) {
				continue;
			}
			ls_import_template( $key, $layout, $assign );
			$n++;
		}
		if ( ls_has_elementor_pro() && $assign ) {
			ls_regenerate_pro_conditions();
		}
		$log[] = sprintf( /* translators: %d count */ __( '%d قالب تم‌بیلدر ساخته شد.', 'larijani' ), $n );
	}

	if ( in_array( 'posts', $parts, true ) ) {
		$log[] = sprintf( /* translators: %d count */ __( '%d مقاله نمونه ساخته شد.', 'larijani' ), ls_import_posts() );
	}
	if ( in_array( 'projects', $parts, true ) ) {
		$log[] = sprintf( /* translators: %d count */ __( '%d نمونه‌کار ساخته شد.', 'larijani' ), ls_import_projects() );
	}
	if ( in_array( 'products', $parts, true ) && ls_has_woo() ) {
		$log[] = sprintf( /* translators: %d count */ __( '%d محصول نمونه ساخته شد.', 'larijani' ), ls_import_products() );
	}
	if ( in_array( 'menus', $parts, true ) ) {
		ls_import_menus();
		$log[] = __( 'منوها ساخته و به جایگاه‌ها متصل شدند.', 'larijani' );
	}

	if ( ls_has_elementor() ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	flush_rewrite_rules();
	update_option( 'ls_demo_imported', time() );
	return implode( '<br>', array_map( 'esc_html', $log ) );
}

/**
 * Create (or reuse) a page built with Elementor.
 *
 * @param string $slug Slug.
 * @param string $title Title.
 * @param array  $rows Layout rows.
 * @return int Page id.
 */
function ls_import_page( $slug, $title, $rows ) {
	$existing = get_page_by_path( $slug );
	if ( $existing ) {
		return $existing->ID;
	}
	$id = wp_insert_post(
		array(
			'post_type'   => 'page',
			'post_status' => 'publish',
			'post_title'  => $title,
			'post_name'   => $slug,
		)
	);
	if ( is_wp_error( $id ) ) {
		return 0;
	}
	ls_save_elementor_data( $id, ls_el_build( $rows ), 'wp-page' );
	update_post_meta( $id, '_wp_page_template', ls_has_elementor() ? 'elementor_header_footer' : 'page-templates/full-width.php' );
	update_post_meta( $id, '_ls_demo_page', 1 );
	return $id;
}

/**
 * Save Elementor data on a post.
 *
 * @param int    $id Post id.
 * @param array  $data Elementor data.
 * @param string $type Template type.
 */
function ls_save_elementor_data( $id, $data, $type ) {
	update_post_meta( $id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $id, '_elementor_template_type', $type );
	update_post_meta( $id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '3.20.0' );
	update_post_meta( $id, '_elementor_data', wp_slash( wp_json_encode( $data, JSON_UNESCAPED_UNICODE ) ) );
	update_post_meta( $id, '_elementor_page_settings', array( 'hide_title' => 'yes' ) );
}

/**
 * Create a Theme Builder template.
 *
 * @param string $key Key.
 * @param array  $layout Layout.
 * @param bool   $assign Assign to the site.
 * @return int
 */
function ls_import_template( $key, $layout, $assign ) {
	$pro  = ls_has_elementor_pro();
	$type = $pro ? $layout['kind'] : ( in_array( $layout['kind'], array( 'header', 'footer' ), true ) ? 'section' : 'page' );
	$found = get_posts(
		array(
			'post_type'   => 'elementor_library',
			'name'        => $key,
			'numberposts' => 1,
			'post_status' => 'any',
		)
	);
	if ( $found ) {
		$id = $found[0]->ID;
		// Elementor Pro activated after the setup: turn the saved section/page into a real Theme Builder template.
		if ( $pro && get_post_meta( $id, '_elementor_template_type', true ) !== $type ) {
			update_post_meta( $id, '_elementor_template_type', $type );
			wp_set_object_terms( $id, $type, 'elementor_library_type' );
		}
	} else {
		$id = wp_insert_post(
			array(
				'post_type'   => 'elementor_library',
				'post_status' => 'publish',
				'post_title'  => $layout['title'],
				'post_name'   => $key,
			)
		);
		if ( is_wp_error( $id ) ) {
			return 0;
		}
		ls_save_elementor_data( $id, ls_el_build( $layout['rows'] ), $type );
		wp_set_object_terms( $id, $type, 'elementor_library_type' );
		update_post_meta( $id, '_ls_demo_page', 1 );
	}

	if ( ! $assign ) {
		return $id;
	}
	$conditions = array(
		'header'          => array( 'include/general' ),
		'footer'          => array( 'include/general' ),
		'single-post'     => array( 'include/singular/post' ),
		'archive'         => array( 'include/archive/posts_page', 'include/archive/category', 'include/archive/post_tag', 'include/archive/search' ),
		'product'         => array( 'include/product' ),
		'product-archive' => array( 'include/product_archive' ),
		'error-404'       => array( 'include/singular/not_found404' ),
	);
	if ( $pro ) {
		update_post_meta( $id, '_elementor_conditions', $conditions[ $layout['kind'] ] ?? array() );
	} else {
		$opt = array(
			'header'          => 'ls_tb_header',
			'footer'          => 'ls_tb_footer',
			'single-post'     => 'ls_tb_single_post',
			'archive'         => 'ls_tb_archive',
			'product'         => 'ls_tb_single_product',
			'product-archive' => 'ls_tb_shop',
			'error-404'       => 'ls_tb_404',
		);
		if ( isset( $opt[ $layout['kind'] ] ) ) {
			set_theme_mod( $opt[ $layout['kind'] ], $id );
		}
	}
	return $id;
}

/**
 * Rebuild Elementor Pro's conditions cache.
 */
function ls_regenerate_pro_conditions() {
	if ( ! class_exists( '\ElementorPro\Modules\ThemeBuilder\Module' ) ) {
		return;
	}
	$module = \ElementorPro\Modules\ThemeBuilder\Module::instance();
	if ( method_exists( $module, 'get_conditions_manager' ) ) {
		$manager = $module->get_conditions_manager();
		if ( method_exists( $manager, 'get_cache' ) ) {
			$manager->get_cache()->regenerate();
		}
	}
}

/**
 * Sideload the design's demo images into the media library.
 *
 * @return int Count.
 */
function ls_import_images() {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$local = get_option( 'ls_demo_images_local', array() );
	$local = is_array( $local ) ? $local : array();
	$ids   = get_option( 'ls_demo_images_ids', array() );
	$ids   = is_array( $ids ) ? $ids : array();
	$count = 0;
	foreach ( ls_demo_images() as $key => $url ) {
		if ( ! empty( $local[ $key ] ) ) {
			continue;
		}
		$tmp = download_url( $url, 20 );
		if ( is_wp_error( $tmp ) ) {
			continue;
		}
		$file = array(
			'name'     => 'larijani-' . sanitize_file_name( $key ) . ( 'logo' === $key || 'logo_alt' === $key ? '.png' : '.jpg' ),
			'tmp_name' => $tmp,
		);
		$att = media_handle_sideload( $file, 0, 'Larijani – ' . $key );
		if ( is_wp_error( $att ) ) {
			wp_delete_file( $tmp );
			continue;
		}
		$local[ $key ] = wp_get_attachment_url( $att );
		$ids[ $key ]   = $att;
		$count++;
	}
	update_option( 'ls_demo_images_local', $local, false );
	update_option( 'ls_demo_images_ids', $ids, false );
	return $count;
}

/**
 * Attachment id of an imported demo image.
 *
 * @param string $key Key.
 * @return int
 */
function ls_demo_image_id( $key ) {
	$ids = get_option( 'ls_demo_images_ids', array() );
	return is_array( $ids ) && ! empty( $ids[ $key ] ) ? (int) $ids[ $key ] : 0;
}

/**
 * Find a post by exact title (replacement for the deprecated get_page_by_title()).
 *
 * @param string $title Title.
 * @param string $type Post type.
 * @return int Post id or 0.
 */
function ls_find_post_by_title( $title, $type ) {
	$q = new WP_Query(
		array(
			'post_type'              => $type,
			'title'                  => $title,
			'post_status'            => 'any',
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
		)
	);
	return $q->posts ? (int) $q->posts[0] : 0;
}

/**
 * Sample blog posts (from the blog archive design).
 *
 * @return int
 */
function ls_import_posts() {
	$posts = array(
		array( 'راهنمای جامع فرمولاسیون سنگ مصنوعی با رزین پلی‌کربوکسیلات LS-500 و روش‌های حذف حباب‌های میکروسکوپی', 'دانشنامه فرمولاسیون', 'archive_featured', 1, 'بررسی اثر پیوند زنجیره‌های اتر جانبی بر روانی دوغاب سیمانی، زمان‌بندی دقیق ویبراسیون جهت خروج حباب‌های به تله افتاده پشت قالب‌های ABS، و روش‌های بهینه‌سازی نسبت آب به سیمان تا زیر ۰.۲۸ با حفظ اسلامپ استاندارد.' ),
		array( 'کالیبراسیون و راهنمای انتخاب میز ویبره صنعتی دور متغیر و اثر زاویه لنگ بر تراکم بتن', 'ماشین‌آلات صنعتی', 'archive_1', 0, 'تنظیم دقیق بسامد و دامنه نوسان در میزهای دو موتوره ضد ارتعاش جهت جلوگیری از پدیده آب‌انداختگی و دوفاز شدن مصالح ریزدانه.' ),
		array( 'ترفندهای افزایش طول عمر قالب‌های نشکن ABS تا بیش از ۱۰۰۰ بار قالب‌گیری مداوم', 'قالب‌های نشکن ABS', 'archive_2', 0, 'نحوه چینش پالت‌ها، کنترل شوک حرارتی در زمان دمولدینگ و انتخاب روان‌کننده‌های قالب با پایه ارگانیک بدون فرسایش پلیمری.' ),
		array( 'فرمول استاندارد تولید سنگ پلیمری با رزین پلی‌کربوکسیلات LS-500 لاریجانی', 'شیمی ساختمان و رزین LS', 'archive_3', 0, 'جدول عیار سیمان تیپ ۲، درصد وزنی رزین مایع بر پایه ماده خشک و تعیین زمان بهینه اختلاط در میکسرهای بشقابی صنعتی.' ),
		array( 'تکنیک‌های حرفه‌ای رگه‌دار کردن و ایجاد بافت مرمر طبیعی با پیگمنت‌های معدنی اکسید آهن', 'تکنیک‌های رنگ و ماربلینگ', 'archive_4', 0, 'آموزش ریختن لایه‌ای ملات‌های چند رنگ با ویسکوزیته متفاوت داخل قالب، کنترل زمان استپ رنگ و ماندگاری در برابر پرتو UV خورشید.' ),
		array( 'چرا نباید از اسید کلریدریک و گازوئیل برای تمیزکاری قالب‌های پلیمری استفاده کرد؟', 'قالب‌های نشکن ABS', 'archive_5', 0, 'بررسی تخریب زنجیره پلیمری و ایجاد ترک‌های میکروسکوپی در اثر شوینده‌های نفتی و معرفی جایگزین‌های زیست‌تخریب‌پذیر استاندارد.' ),
		array( 'تحلیل اقتصادی و طرح توجیهی راه‌اندازی کارگاه تولید سنگ پلیمری و جدول در سال ۱۴۰۴', 'طرح‌های توجیهی کارگاهی', 'archive_6', 0, 'برآورد سرمایه اولیه خط تولید، هزینه‌های استهلاک قالب، حاشیه سود هر متر مربع کفپوش و بازگشت سرمایه در بازه زمانی ۶ ماهه.' ),
	);
	$body = '<p>رزین‌های پایه نفتالینی و ملامینی نسل قدیم تنها از طریق ایجاد بار الکتریکی منفی سطحی عمل می‌کردند؛ رزین پلی‌کربوکسیلات اتر اختصاصی <strong>LS-500</strong> مجهز به زنجیره‌های جانبی پلیمری آب‌دوست با طول شاخه بهینه‌شده است.</p>'
		. '<h2>مکانیزم ممانعت فضایی و تغییر رفتار ذرات هیدراتاسیون</h2><p>این زنجیره‌ها مانع از آگلومره شدن دانه‌های ریز سیمان تیپ ۲ و پودرهای سیلیسی می‌شوند و فضایی الاستیک میان لایه‌های دوگانه آب ایجاد می‌کنند؛ در نتیجه روانی ملات برای ۴۵ دقیقه حفظ می‌شود.</p>'
		. '<h2>جدول طرح اختلاط پیشنهادی برای ۱ متر مکعب</h2><table><thead><tr><th>ماده اولیه</th><th>مشخصه فنی</th><th>وزن (Kg/m³)</th></tr></thead><tbody><tr><td>سیمان پرتلند تیپ ۲</td><td>بلین بیش از 3100</td><td>۴۵۰</td></tr><tr><td>ماسه سیلیسی شکسته</td><td>دانه‌بندی ۰ تا ۳ میلی‌متر</td><td>۱,۱۵۰</td></tr><tr><td>فوق روان‌کننده LS-500</td><td>غلظت ۵۰٪</td><td>۳.۶ الی ۴.۵</td></tr></tbody></table>'
		. '<blockquote>هرگز رزین LS-500 را روی مصالح خشک نریزید؛ همواره ابتدا ۶۰ درصد آب را با مصالح خشک مخلوط کنید.</blockquote>'
		. '<h2>علل پیدایش حباب‌های سوزنی و کنترل نوسان میز ویبره</h2><ul><li>تنظیم زاویه لنگ موتور ویبره دور متغیر</li><li>روغن‌کاری صحیح قالب‌های ABS با روغن پایه گیاهی</li><li>کنترل ویسکوزیته دینامیکی و دوزینگ حباب‌زدا</li></ul>';

	$tag_names = array( 'سمنت پلاست', 'رزین پلی‌کربوکسیلات', 'میز ویبره دو موتوره', 'الیاف PP بتن', 'اکسید آهن', 'قالب نشکن ABS', 'نسبت آب به سیمان' );
	$count     = 0;
	foreach ( $posts as $i => $p ) {
		if ( ls_find_post_by_title( $p[0], 'post' ) ) {
			continue;
		}
		$cat = term_exists( $p[1], 'category' );
		if ( ! $cat ) {
			$cat = wp_insert_term( $p[1], 'category' );
		}
		$content = $p[3] ? ls_demo_article_html() : $body;
		kses_remove_filters(); // Demo article contains the design's inline SVG diagram.
		$id = wp_insert_post(
			array(
				'post_type'     => 'post',
				'post_status'   => 'publish',
				'post_title'    => $p[0],
				'post_excerpt'  => $p[4],
				'post_content'  => $content,
				'post_category' => is_array( $cat ) ? array( (int) $cat['term_id'] ) : array(),
				'post_date'     => gmdate( 'Y-m-d H:i:s', time() - $i * DAY_IN_SECONDS * 4 ),
				'tags_input'    => $p[3] ? array( 'رزین پلی‌کربوکسیلات', 'سنگ مصنوعی', 'فرمولاسیون بتن', 'قالب نشکن ABS', 'میز ویبره صنعتی' ) : array_slice( $tag_names, $i % 3, 4 ),
			)
		);
		kses_init_filters();
		if ( is_wp_error( $id ) ) {
			continue;
		}
		if ( $p[3] ) {
			update_post_meta( $id, '_ls_author_name', 'مهندس مسعود لاریجانی' );
			update_post_meta( $id, '_ls_author_role', 'مدیر ارشد فنی و مهندسی مواد' );
			update_post_meta( $id, '_ls_author_bio', 'بنیان‌گذار و مدیر ارشد فنی گروه صنعتی لاریجانی استون؛ با بیش از ۱۶ سال سابقه در طراحی خطوط تولید پیوسته سنگ‌های پلیمری، طراحی بیش از ۴۵۰ دست قالب کامپوزیتی ضدسایش و مشاوره فنی به بیش از ۳۰۰ کارگاه و کارخانه در سراسر کشور و کشورهای همسایه.' );
			update_post_meta( $id, '_ls_reading_time', 12 );
			update_post_meta(
				$id,
				'_ls_metrics',
				"مقاومت فشاری ۲۸ روزه | > ۷۵ | مگاپاسکال (MPa) | bi bi-speedometer2 | primary | ۱۱۰٪ فراتر از بتن معمولی | bi bi-graph-up-arrow\n"
				. "کاهش نسبت آب به سیمان | ۰.۲۸ | W/C Ratio | bi bi-droplet | cobalt | کاهش ۳۸ درصدی آب مصرفی | bi bi-check2\n"
				. "پایه شیمیایی فوق روان‌کننده | PCE اتر نسل ۳ |  | bi bi-diagram-3 | amber | سنتز ویژه سمنت پلاست صنعتی | bi bi-patch-check\n"
				. "افزایش دوام و چگالی ظاهری | +۶۵٪ | نفوذناپذیری | bi bi-shield-check | emerald | مقاوم در برابر ۳۰۰ سیکل یخبندان | bi bi-snow"
			);
			ls_demo_comments( $id );
			update_post_meta( $id, '_ls_featured', '1' );
			update_post_meta( $id, '_ls_image_caption', 'آزمایشگاه سنجش مقاومت هیدرولیکی لاریجانی استون - واحد کنترل کیفیت پایلوت' );
			update_post_meta( $id, '_ls_image_badge', 'کد استاندارد: ASTM C109 / ISIRI 755' );
		}
		update_post_meta( $id, 'ls_views', wp_rand( 900, 4500 ) );
		update_post_meta( $id, '_ls_demo_image', $p[2] );
		$att = ls_demo_image_id( $p[2] );
		if ( $att ) {
			set_post_thumbnail( $id, $att );
		}
		$count++;
	}
	return $count;
}

/**
 * The design's long-form article (inc/demo/article.html) with demo image URLs.
 *
 * @return string
 */
function ls_demo_article_html() {
	$html = (string) file_get_contents( LS_DIR . '/inc/demo/article.html' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file.
	return preg_replace_callback(
		'/\{\{img:([a-z0-9_]+)\}\}/',
		static function ( $m ) {
			return esc_url( ls_demo_image( $m[1] ) );
		},
		$html
	);
}

/**
 * Sample answered workshop questions for the featured article.
 *
 * @param int $post_id Post id.
 */
function ls_demo_comments( $post_id ) {
	$threads = array(
		array( 'حاج رضا صادقی (سنگ آریا، اصفهان)', 'کارگاه فعال سنگ سمنت پلاست', 'سلام مهندس لاریجانی عزیز. ما در هوای سرد فعلی اصفهان (حدود ۶ درجه) وقتی از LS-500 استفاده می‌کنیم زمان گیرش اولیه تا ۴ ساعت طول می‌کشد. آیا می‌توانیم بدون افت مقاومت نهایی از زودگیر کلسیم کلراید در کنار آن استفاده کنیم؟', DAY_IN_SECONDS, 'مهندس مسعود لاریجانی', 'درود بر شما جناب صادقی. به هیچ عنوان کلراید اضافه نکنید زیرا به شدت روی براقیت سطح اثر سوء گذاشته و شوره سفیدک ایجاد می‌کند. در دمای زیر ۱۰ درجه، آب اختلاط را تا ۴۰ درجه سانتی‌گراد گرم کنید و دوز LS-500 را از ۱ درصد به ۰.۸۵ درصد برسانید تا گیرش در زمان استاندارد ۹۰ دقیقه کامل شود.', 20 * HOUR_IN_SECONDS ),
		array( 'مهندس کمالی (پروژه ویلایی دماوند)', 'مهندس ناظر سازه', 'آیا این فرمولاسیون برای تولید موزاییک پلیمری در شرایط آب و هوایی مناطق مرطوب شمال کشور هم مانع رشد خزه در درزها می‌شود یا خیر؟', 3 * DAY_IN_SECONDS, 'واحد تحقیق و توسعه لاریجانی استون', 'بله جناب مهندس. به دلیل کاهش نسبت آب به سیمان به زیر ۰.۳۰ و تشکیل ریزساختار پیوسته، جذب آب به کمتر از ۲ درصد کاهش می‌یابد که عملاً محیط زیستی برای ریشه‌دوانی خزه و باکتری باقی نمی‌گذارد.', 2 * DAY_IN_SECONDS ),
	);
	foreach ( $threads as $t ) {
		$parent = wp_insert_comment(
			array(
				'comment_post_ID'  => $post_id,
				'comment_author'   => $t[0],
				'comment_content'  => $t[2],
				'comment_approved' => 1,
				'comment_date'     => wp_date( 'Y-m-d H:i:s', time() - $t[3] ),
				'comment_date_gmt' => gmdate( 'Y-m-d H:i:s', time() - $t[3] ),
				'comment_meta'     => array( '_ls_badge' => $t[1] ),
			)
		);
		if ( $parent ) {
			wp_insert_comment(
				array(
					'comment_post_ID'  => $post_id,
					'comment_parent'   => $parent,
					'comment_author'   => $t[4],
					'comment_content'  => $t[5],
					'comment_approved' => 1,
					'comment_date'     => wp_date( 'Y-m-d H:i:s', time() - $t[6] ),
					'comment_date_gmt' => gmdate( 'Y-m-d H:i:s', time() - $t[6] ),
					'comment_meta'     => array( '_ls_staff' => 1 ),
				)
			);
		}
	}
}

/**
 * Sample portfolio projects.
 *
 * @return int
 */
function ls_import_projects() {
	$cats  = array(
		'facade'     => 'نمای مدرن و سنگ سه‌بعدی',
		'paving'     => 'موزاییک و واش‌بتن',
		'landscape'  => 'جدول، دورباغچه و ویلایی',
		'industrial' => 'خطوط تولید و کارخانجات',
	);
	$count = 0;
	foreach ( ls_demo_projects() as $p ) {
		if ( ls_find_post_by_title( $p['title'], 'ls_project' ) ) {
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_type'    => 'ls_project',
				'post_status'  => 'publish',
				'post_title'   => $p['title'],
				'post_excerpt' => $p['desc'],
				'post_content' => '<p>' . $p['desc'] . '</p>',
			)
		);
		if ( is_wp_error( $id ) ) {
			continue;
		}
		$term = term_exists( $p['filter'], 'ls_project_cat' );
		if ( ! $term ) {
			$term = wp_insert_term( $cats[ $p['filter'] ], 'ls_project_cat', array( 'slug' => $p['filter'] ) );
		}
		if ( is_array( $term ) ) {
			wp_set_object_terms( $id, (int) $term['term_id'], 'ls_project_cat' );
		}
		foreach ( array( 'location', 'code', 'badge_1', 'badge_2', 'note' ) as $k ) {
			update_post_meta( $id, '_ls_' . $k, $p[ $k ] );
		}
		update_post_meta( $id, '_ls_note_icon', 'bi bi-' . $p['note_icon'] );
		update_post_meta( $id, '_ls_demo_image', $p['image'] );
		for ( $i = 1; $i <= 3; $i++ ) {
			update_post_meta( $id, "_ls_spec_{$i}_label", $p[ "spec{$i}_label" ] );
			update_post_meta( $id, "_ls_spec_{$i}_value", $p[ "spec{$i}_value" ] );
		}
		$att = ls_demo_image_id( $p['image'] );
		if ( $att ) {
			set_post_thumbnail( $id, $att );
		}
		$count++;
	}
	return $count;
}

/**
 * Sample WooCommerce products.
 *
 * @return int
 */
function ls_import_products() {
	$cats  = array(
		'mold'      => array( 'قالب‌های ABS و کامپوزیت', 'bi bi-bounding-box-circles' ),
		'machinery' => array( 'ماشین‌آلات و میکسرها', 'bi bi-cpu-fill' ),
		'chemical'  => array( 'رزین و رنگدانه‌های صنعتی', 'bi bi-droplet-half' ),
	);
	$count = 0;
	foreach ( ls_demo_catalog() as $i => $p ) {
		if ( ls_find_post_by_title( $p['title'], 'product' ) ) {
			continue;
		}
		$term = term_exists( $p['filter'], 'product_cat' );
		if ( ! $term ) {
			$term = wp_insert_term( $cats[ $p['filter'] ][0], 'product_cat', array( 'slug' => $p['filter'] ) );
			if ( is_array( $term ) ) {
				update_term_meta( (int) $term['term_id'], 'ls_icon', $cats[ $p['filter'] ][1] );
			}
		}
		$product = new WC_Product_Simple();
		$product->set_name( $p['title'] );
		$product->set_status( 'publish' );
		$product->set_regular_price( (string) $p['price_raw'] );
		$product->set_short_description( $p['desc'] );
		$product->set_description( '<p>' . $p['desc'] . '</p>' );
		$product->set_sku( str_replace( 'کد: ', '', $p['code'] ) );
		$product->set_featured( 0 === $i % 3 );
		if ( is_array( $term ) ) {
			$product->set_category_ids( array( (int) $term['term_id'] ) );
		}
		$attrs = array();
		for ( $k = 1; $k <= 3; $k++ ) {
			$a = new WC_Product_Attribute();
			$a->set_name( $p[ "spec{$k}_label" ] );
			$a->set_options( array( $p[ "spec{$k}_value" ] ) );
			$a->set_visible( true );
			$a->set_position( $k );
			$attrs[] = $a;
		}
		$product->set_attributes( $attrs );
		$att = ls_demo_image_id( $p['image'] );
		if ( $att ) {
			$product->set_image_id( $att );
		}
		$product->update_meta_data( '_ls_demo_image', $p['image'] );
		$product->update_meta_data( '_ls_badge', $p['badge'] );
		$product->update_meta_data( '_ls_badge_tone', $p['badge_tone'] );
		$product->update_meta_data( '_ls_price_label', $p['price_label'] );
		$product->update_meta_data( '_ls_stock_text', 'موجود در انبار مرکزی آبیک (ارسال فوری ۲۴ ساعته)' );
		$product->update_meta_data( '_ls_quality_note', 'تأییدیه کنترل کیفی آزمایشگاه بتن لاریجانی' );
		if ( 'mold' === $p['filter'] ) {
			$product->update_meta_data( '_ls_bulk_price', round( $p['price_raw'] * 0.92 ) );
			$product->update_meta_data( '_ls_bulk_threshold', 50 );
			$product->update_meta_data( '_ls_area_per_unit', 0.15 );
			$product->update_meta_data( '_ls_default_qty', 10 );
			$product->update_meta_data( '_ls_image_badges', "ضمانت مادام‌العمر عدم شکستن ABS|primary|bi bi-shield-check\nگرید صادراتی A++|amber|bi bi-award-fill" );
		}
		$product->update_meta_data( '_ls_trust', "۵۰۰+ سیکل بتن|ماندگاری فرم تضمینی|bi bi-arrow-repeat\nبدون نیاز به اسید|صیقلی و ضدرسوب|bi bi-moisture\nارسال روزانه|از انبار کارخانه آبیک|bi bi-truck" );
		$product->update_meta_data( '_ls_guarantees', "تضمین تعویض بی‌قیدوشرط:|در صورت هرگونه تغییر فرم، ترکیدگی در ارتعاش ویبره یا دفرمه شدن در ۶ ماه اول، محصول فوراً مرجوع و تعویض می‌گردد.|bi bi-shield-fill-check|emerald\nفرمولاسیون رایگان همراه فاکتور:|جدول دقیق نسبت‌های اختلاط رزین، پودر سنگ سیلیسی، پیگمنت و سیمان همراه بار ارسال می‌شود.|bi bi-journal-bookmark-fill|amber" );
		$product->save();
		$count++;
	}
	return $count;
}

/**
 * Menus.
 */
function ls_import_menus() {
	$url = static function ( $slug ) {
		$p = get_page_by_path( $slug );
		return $p ? get_permalink( $p ) : home_url( '/' . $slug . '/' );
	};
	$shop = ls_has_woo() ? get_permalink( wc_get_page_id( 'shop' ) ) : $url( 'shop' );
	$menus = array(
		'primary'           => array(
			__( 'منوی اصلی', 'larijani' ),
			array(
				array( __( 'صفحه اصلی', 'larijani' ), home_url( '/' ) ),
				array( __( 'فروشگاه و کاتالوگ', 'larijani' ), $shop ),
				array( __( 'خدمات و خطوط تولید', 'larijani' ), $url( 'services' ) ),
				array( __( 'نمونه کارها', 'larijani' ), $url( 'portfolio' ) ),
				array( __( 'وبلاگ تخصصی', 'larijani' ), $url( 'blog' ) ),
				array( __( 'تماس با ما', 'larijani' ), $url( 'contact' ) ),
			),
		),
		'drawer_categories' => array(
			__( 'دسته‌بندی‌های موبایل', 'larijani' ),
			array(
				array( __( 'قالب کفپوش', 'larijani' ), $shop ),
				array( __( 'میز ویبره سنگین', 'larijani' ), $shop ),
				array( __( 'قالب صراحی و نما', 'larijani' ), $shop ),
				array( __( 'رزین روان‌کننده', 'larijani' ), $shop ),
			),
		),
		'footer_quick'      => array(
			__( 'فوتر – دسترسی سریع', 'larijani' ),
			array(
				array( __( 'صفحه اصلی', 'larijani' ), home_url( '/' ) ),
				array( __( 'فروشگاه قالب‌های نشکن', 'larijani' ), $shop ),
				array( __( 'خدمات و خطوط تولید', 'larijani' ), $url( 'services' ) ),
				array( __( 'نمونه کارها', 'larijani' ), $url( 'portfolio' ) ),
				array( __( 'فرمولاسیون و مقالات', 'larijani' ), $url( 'blog' ) ),
				array( __( 'تماس با واحد فروش', 'larijani' ), $url( 'contact' ) ),
			),
		),
		'footer_categories' => array(
			__( 'فوتر – دسته‌بندی تجهیزات', 'larijani' ),
			array(
				array( __( 'قالب کفپوش و سنگفرش', 'larijani' ), $shop ),
				array( __( 'قالب جدول و دورباغچه', 'larijani' ), $shop ),
				array( __( 'قالب نما و صراحی رومی', 'larijani' ), $shop ),
				array( __( 'رزین روان‌کننده بتن', 'larijani' ), $shop ),
				array( __( 'رنگدانه‌های معدنی اکسید آهن', 'larijani' ), $shop ),
				array( __( 'روغن قالب پایه گیاهی', 'larijani' ), $shop ),
			),
		),
		'footer_bottom'     => array(
			__( 'فوتر – لینک‌های پایین', 'larijani' ),
			array(
				array( __( 'قوانین و ضمانت محصولات', 'larijani' ), $url( 'contact' ) ),
				array( __( 'حریم خصوصی', 'larijani' ), get_privacy_policy_url() ? get_privacy_policy_url() : home_url( '/' ) ),
				array( __( 'نقشه سایت', 'larijani' ), home_url( '/wp-sitemap.xml' ) ),
			),
		),
	);
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	foreach ( $menus as $location => $menu ) {
		if ( ! empty( $locations[ $location ] ) && wp_get_nav_menu_object( $locations[ $location ] ) ) {
			continue;
		}
		$existing = wp_get_nav_menu_object( $menu[0] );
		$menu_id  = $existing ? $existing->term_id : wp_create_nav_menu( $menu[0] );
		if ( is_wp_error( $menu_id ) ) {
			continue;
		}
		if ( ! $existing ) {
			foreach ( $menu[1] as $item ) {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'  => $item[0],
						'menu-item-url'    => $item[1],
						'menu-item-status' => 'publish',
						'menu-item-type'   => 'custom',
					)
				);
			}
		}
		$locations[ $location ] = $menu_id;
	}
	set_theme_mod( 'nav_menu_locations', $locations );
}
