<?php
/**
 * Dashboard settings: top-level «لاریجانی استون» admin menu.
 *
 * Tabs mirror the Customizer options (same theme_mods, so both stay in sync)
 * plus Theme Builder assignments. Saving goes through admin-post.php with a
 * nonce and the edit_theme_options capability.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Admin tabs: slug => [ label, dashicon ].
 *
 * @return array
 */
function larijani_settings_tabs() {
	return array(
		'overview' => array( __( 'پیشخوان قالب', 'larijani-stone' ), 'dashicons-dashboard' ),
		'ls_brand'   => array( __( 'برند و رنگ‌ها', 'larijani-stone' ), 'dashicons-art' ),
		'ls_contact' => array( __( 'تماس و شبکه‌های اجتماعی', 'larijani-stone' ), 'dashicons-phone' ),
		'ls_header'  => array( __( 'هدر', 'larijani-stone' ), 'dashicons-align-wide' ),
		'ls_footer'  => array( __( 'فوتر', 'larijani-stone' ), 'dashicons-editor-insertmore' ),
		'ls_blog'    => array( __( 'وبلاگ', 'larijani-stone' ), 'dashicons-welcome-write-blog' ),
		'ls_forms'   => array( __( 'فرم‌ها', 'larijani-stone' ), 'dashicons-email-alt' ),
		'ls_style'   => array( __( 'تایپوگرافی و چیدمان', 'larijani-stone' ), 'dashicons-editor-textcolor' ),
		'ls_builder' => array( __( 'تم‌بیلدر', 'larijani-stone' ), 'dashicons-layout' ),
	);
}

/**
 * Register the menu.
 */
function larijani_settings_menu() {
	add_menu_page(
		__( 'تنظیمات قالب لاریجانی استون', 'larijani-stone' ),
		__( 'لاریجانی استون', 'larijani-stone' ),
		'edit_theme_options',
		'ls-settings',
		'larijani_settings_page',
		'dashicons-building',
		59
	);
	add_submenu_page( 'ls-settings', __( 'تنظیمات قالب', 'larijani-stone' ), __( 'تنظیمات قالب', 'larijani-stone' ), 'edit_theme_options', 'ls-settings', 'larijani_settings_page' );
	add_submenu_page( 'ls-settings', __( 'منوها', 'larijani-stone' ), __( 'منوها', 'larijani-stone' ), 'edit_theme_options', 'nav-menus.php' );
	add_submenu_page( 'ls-settings', __( 'سفارشی‌سازی زنده', 'larijani-stone' ), __( 'سفارشی‌سازی زنده', 'larijani-stone' ), 'customize', 'customize.php?autofocus[panel]=ls_panel' );
}
add_action( 'admin_menu', 'larijani_settings_menu', 9 );

/**
 * Settings page assets (colour picker + media frame) – only on our screens.
 *
 * @param string $hook Hook suffix.
 */
function larijani_settings_assets( $hook ) {
	if ( false === strpos( $hook, 'ls-settings' ) && false === strpos( $hook, 'ls-setup' ) ) {
		return;
	}
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_media();
	$js = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || ! file_exists( LARIJANI_DIR . '/assets/js/admin.min.js' ) ? 'assets/js/admin.js' : 'assets/js/admin.min.js';
	wp_enqueue_script( 'larijani-admin', LARIJANI_URI . '/' . $js, array( 'jquery', 'wp-color-picker' ), LARIJANI_VERSION, true );
	wp_enqueue_style( 'larijani-admin', LARIJANI_URI . '/assets/css/admin.css', array(), LARIJANI_VERSION );
}
add_action( 'admin_enqueue_scripts', 'larijani_settings_assets' );

/**
 * Sanitize one option value by field type.
 *
 * @param string $type Field type.
 * @param mixed  $value Raw (unslashed) value.
 * @param array  $choices Select choices.
 * @param mixed  $default Default.
 * @return mixed
 */
function larijani_sanitize_option_value( $type, $value, $choices = array(), $default = '' ) {
	switch ( $type ) {
		case 'checkbox':
			return (bool) $value;
		case 'color':
			$c = sanitize_hex_color( $value );
			return $c ? $c : $default;
		case 'url':
		case 'image':
			return esc_url_raw( $value );
		case 'email':
			return sanitize_email( $value );
		case 'textarea':
			return larijani_sanitize_html( $value );
		case 'select':
			return isset( $choices[ $value ] ) ? $value : $default;
		case 'template':
			return absint( $value );
		default:
			return sanitize_text_field( $value );
	}
}

/**
 * Theme Builder template fields (free Elementor mini theme builder).
 *
 * @return array
 */
function larijani_builder_fields() {
	$labels = array(
		'ls_tb_header'         => __( 'هدر', 'larijani-stone' ),
		'ls_tb_footer'         => __( 'فوتر', 'larijani-stone' ),
		'ls_tb_single_post'    => __( 'تک‌نوشته (مقاله)', 'larijani-stone' ),
		'ls_tb_archive'        => __( 'آرشیو وبلاگ / جستجو', 'larijani-stone' ),
		'ls_tb_single_product' => __( 'تک‌محصول ووکامرس', 'larijani-stone' ),
		'ls_tb_shop'           => __( 'فروشگاه و دسته‌های محصول', 'larijani-stone' ),
		'ls_tb_single_project' => __( 'تک‌پروژه (نمونه‌کار)', 'larijani-stone' ),
		'ls_tb_page'           => __( 'برگه‌های بدون المنتور', 'larijani-stone' ),
		'ls_tb_404'            => __( 'صفحه ۴۰۴', 'larijani-stone' ),
	);
	$out = array();
	foreach ( $labels as $key => $label ) {
		$out[] = array( 'ls_builder', $key, $label, 'template' );
	}
	return $out;
}

/**
 * Saved Elementor templates for the theme builder selects.
 *
 * @return array id => title
 */
function larijani_template_choices() {
	$choices = array( 0 => __( '— قالب پیش‌فرض پوسته —', 'larijani-stone' ) );
	$posts   = get_posts(
		array(
			'post_type'      => 'elementor_library',
			'post_status'    => 'publish',
			'posts_per_page' => 200,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);
	foreach ( $posts as $p ) {
		$choices[ $p->ID ] = $p->post_title;
	}
	return $choices;
}

/**
 * Save handler.
 */
function larijani_settings_save() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'شما اجازه تغییر تنظیمات قالب را ندارید.', 'larijani-stone' ), 403 );
	}
	check_admin_referer( 'ls_save_settings', 'ls_settings_nonce' );

	$tab      = isset( $_POST['ls_tab'] ) ? sanitize_key( wp_unslash( $_POST['ls_tab'] ) ) : 'ls_brand';
	$defaults = larijani_option_defaults();
	$fields   = 'ls_builder' === $tab ? larijani_builder_fields() : larijani_option_fields();
	$values   = isset( $_POST['ls'] ) && is_array( $_POST['ls'] ) ? wp_unslash( $_POST['ls'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field below.
	$reset    = ! empty( $_POST['ls_reset'] );

	if ( $reset ) {
		larijani_settings_backup( wp_list_pluck( wp_list_filter( $fields, array( 0 => $tab ) ), 1 ) );
	}
	foreach ( $fields as $f ) {
		list( $section, $key, , $type ) = $f;
		if ( $section !== $tab ) {
			continue;
		}
		if ( $reset ) {
			remove_theme_mod( $key );
			continue;
		}
		$raw = $values[ $key ] ?? ( 'checkbox' === $type ? '' : null );
		if ( null === $raw ) {
			continue;
		}
		set_theme_mod( $key, larijani_sanitize_option_value( $type, $raw, $f[4] ?? array(), $defaults[ $key ] ?? '' ) );
	}

	if ( 'ls_brand' === $tab && larijani_has_elementor() && function_exists( 'larijani_setup_elementor_kit' ) ) {
		larijani_setup_elementor_kit( true ); // Keep Elementor global colours in sync.
	}
	if ( 'ls_style' === $tab && function_exists( 'larijani_sync_kit_font' ) ) {
		larijani_sync_kit_font();
	}

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'    => 'ls-settings',
				'tab'     => $tab,
				'updated' => $reset ? 'reset' : '1',
			),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
add_action( 'admin_post_ls_save_settings', 'larijani_settings_save' );

/**
 * Keep a copy of the current values before a reset, so it can be undone.
 *
 * @param string[] $keys Theme mod keys.
 */
function larijani_settings_backup( $keys ) {
	$mods = array();
	foreach ( $keys as $key ) {
		$val = get_theme_mod( $key, null );
		if ( null !== $val ) {
			$mods[ $key ] = $val;
		}
	}
	update_option(
		'larijani_settings_backup',
		array(
			'time' => time(),
			'keys' => array_values( $keys ),
			'mods' => $mods,
		),
		false
	);
}

/**
 * Reset every theme setting (colours, contact, header, footer, typography, builder) to the defaults.
 */
function larijani_settings_reset_all() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'شما اجازه تغییر تنظیمات قالب را ندارید.', 'larijani-stone' ), 403 );
	}
	check_admin_referer( 'ls_reset_all' );
	$keys = array_merge( wp_list_pluck( larijani_option_fields(), 1 ), wp_list_pluck( larijani_builder_fields(), 1 ) );
	larijani_settings_backup( $keys );
	foreach ( $keys as $key ) {
		remove_theme_mod( $key );
	}
	if ( function_exists( 'larijani_sync_kit_font' ) ) {
		larijani_sync_kit_font();
	}
	wp_safe_redirect( admin_url( 'admin.php?page=ls-settings&updated=reset' ) );
	exit;
}
add_action( 'admin_post_ls_reset_all', 'larijani_settings_reset_all' );

/**
 * Undo the last reset.
 */
function larijani_settings_restore() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'شما اجازه تغییر تنظیمات قالب را ندارید.', 'larijani-stone' ), 403 );
	}
	check_admin_referer( 'ls_restore_settings' );
	$backup  = get_option( 'larijani_settings_backup' );
	$allowed = array_merge( wp_list_pluck( larijani_option_fields(), 1 ), wp_list_pluck( larijani_builder_fields(), 1 ) );
	if ( is_array( $backup ) && ! empty( $backup['mods'] ) ) {
		foreach ( $backup['mods'] as $key => $val ) {
			if ( in_array( $key, $allowed, true ) ) {
				set_theme_mod( $key, $val );
			}
		}
		delete_option( 'larijani_settings_backup' );
		if ( function_exists( 'larijani_sync_kit_font' ) ) {
			larijani_sync_kit_font();
		}
	}
	wp_safe_redirect( admin_url( 'admin.php?page=ls-settings&updated=restored' ) );
	exit;
}
add_action( 'admin_post_ls_restore_settings', 'larijani_settings_restore' );

/**
 * Reset / restore card on the overview tab.
 */
function larijani_settings_reset_card() {
	$backup = get_option( 'larijani_settings_backup' );
	?>
	<div class="ls-card">
		<h2><span class="dashicons dashicons-image-rotate"></span> <?php esc_html_e( 'بازنشانی تنظیمات', 'larijani-stone' ); ?></h2>
		<p><?php esc_html_e( 'همه تنظیمات قالب (رنگ‌ها، تماس، هدر، فوتر، تایپوگرافی و تم‌بیلدر) به حالت اولیه طرح برمی‌گردد. برگه‌ها، منوها و محتوای سایت تغییر نمی‌کنند و یک نسخه پشتیبان از مقادیر فعلی نگه داشته می‌شود.', 'larijani-stone' ); ?></p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="ls_reset_all">
			<?php wp_nonce_field( 'ls_reset_all' ); ?>
			<button type="submit" class="button button-link-delete" data-ls-confirm="<?php esc_attr_e( 'همه تنظیمات قالب به پیش‌فرض برگردد؟ (قابل بازگردانی است)', 'larijani-stone' ); ?>"><?php esc_html_e( 'بازنشانی همه تنظیمات', 'larijani-stone' ); ?></button>
		</form>
		<?php if ( is_array( $backup ) && ! empty( $backup['mods'] ) ) : ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:8px">
			<input type="hidden" name="action" value="ls_restore_settings">
			<?php wp_nonce_field( 'ls_restore_settings' ); ?>
			<button type="submit" class="button"><?php echo esc_html( sprintf( /* translators: %s: date */ __( 'بازگردانی تنظیمات قبل از بازنشانی (%s)', 'larijani-stone' ), larijani_jalali_date( (int) $backup['time'] ) ) ); ?></button>
		</form>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Render one field.
 *
 * @param array $f Field.
 */
function larijani_settings_field( $f ) {
	list( , $key, $label, $type ) = $f;
	$choices = $f[4] ?? array();
	$value   = larijani_opt( $key );
	$name    = 'ls[' . $key . ']';
	$id      = 'ls-field-' . $key;
	echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label></th><td>';
	switch ( $type ) {
		case 'checkbox':
			echo '<input type="hidden" name="' . esc_attr( $name ) . '" value="">';
			echo '<label class="ls-switch"><input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="1" ' . checked( (bool) $value, true, false ) . '><span></span></label>';
			break;
		case 'color':
			echo '<input type="text" class="ls-color" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( larijani_option_defaults()[ $key ] ?? '' ) . '">';
			break;
		case 'textarea':
			echo '<textarea class="large-text" rows="4" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '">' . esc_textarea( $value ) . '</textarea>';
			break;
		case 'select':
		case 'template':
			if ( 'template' === $type ) {
				$choices = larijani_template_choices();
			}
			echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '">';
			foreach ( $choices as $k => $l ) {
				echo '<option value="' . esc_attr( $k ) . '" ' . selected( (string) $value, (string) $k, false ) . '>' . esc_html( $l ) . '</option>';
			}
			echo '</select>';
			if ( 'template' === $type && $value ) {
				echo ' <a class="button button-small" href="' . esc_url( admin_url( 'post.php?post=' . absint( $value ) . '&action=elementor' ) ) . '">' . esc_html__( 'ویرایش با المنتور', 'larijani-stone' ) . '</a>';
			}
			break;
		case 'image':
			echo '<div class="ls-media"><input type="url" class="regular-text" dir="ltr" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '"> <button type="button" class="button ls-media-pick">' . esc_html__( 'انتخاب تصویر', 'larijani-stone' ) . '</button>';
			echo '<div class="ls-media-preview">' . ( $value ? '<img src="' . esc_url( $value ) . '" alt="">' : '' ) . '</div></div>';
			break;
		default:
			$input = in_array( $type, array( 'email', 'url' ), true ) ? $type : 'text';
			$dir   = in_array( $type, array( 'email', 'url' ), true ) || preg_match( '/phone|whatsapp/', $key ) ? ' dir="ltr"' : '';
			echo '<input type="' . esc_attr( $input ) . '" class="regular-text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '"' . $dir . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attribute.
	}
	echo '</td></tr>';
}

/**
 * Overview tab: status, created pages, quick links.
 */
function larijani_settings_overview() {
	$plugins = array(
		array( __( 'المنتور', 'larijani-stone' ), larijani_has_elementor(), __( 'لازم برای ویرایش بصری صفحات (بدون آن، صفحات با همان طرح نمایش داده می‌شوند ولی قابل ویرایش بصری نیستند).', 'larijani-stone' ), 'elementor' ),
		array( __( 'المنتور پرو', 'larijani-stone' ), larijani_has_elementor_pro(), __( 'اختیاری؛ برای شرط‌های نمایش تم‌بیلدر. بدون آن تب «تم‌بیلدر» همین کار را انجام می‌دهد.', 'larijani-stone' ), '' ),
		array( __( 'ووکامرس', 'larijani-stone' ), larijani_has_woo(), __( 'اختیاری؛ برای فروشگاه، سبد خرید و قیمت پلکانی.', 'larijani-stone' ), 'woocommerce' ),
	);
	$pages = get_posts(
		array(
			'post_type'      => array( 'page', 'elementor_library' ),
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'meta_key'       => '_ls_demo_page', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
		)
	);
	$leads = post_type_exists( 'ls_lead' ) ? wp_count_posts( 'ls_lead' ) : null;
	?>
	<div class="ls-cards">
		<div class="ls-card">
			<h2><span class="dashicons dashicons-admin-plugins"></span> <?php esc_html_e( 'افزونه‌های مرتبط', 'larijani-stone' ); ?></h2>
			<ul class="ls-status">
				<?php foreach ( $plugins as $p ) : ?>
				<li>
					<span class="ls-dot <?php echo $p[1] ? 'is-on' : ''; ?>"></span>
					<strong><?php echo esc_html( $p[0] ); ?></strong> — <?php echo $p[1] ? esc_html__( 'فعال', 'larijani-stone' ) : esc_html__( 'غیرفعال', 'larijani-stone' ); ?>
					<p class="description"><?php echo esc_html( $p[2] ); ?></p>
					<?php if ( ! $p[1] && $p[3] && current_user_can( 'install_plugins' ) ) : ?>
					<a class="button button-small" href="<?php echo esc_url( admin_url( 'plugin-install.php?s=' . $p[3] . '&tab=search&type=term' ) ); ?>"><?php esc_html_e( 'نصب / فعال‌سازی', 'larijani-stone' ); ?></a>
					<?php endif; ?>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="ls-card">
			<h2><span class="dashicons dashicons-email-alt"></span> <?php esc_html_e( 'درخواست‌های مشتریان', 'larijani-stone' ); ?></h2>
			<?php if ( $leads ) : ?>
			<p class="ls-big"><?php echo esc_html( larijani_fa_num( (int) ( $leads->private ?? 0 ) ) ); ?></p>
			<p><?php esc_html_e( 'فرم‌های تماس، مشاوره و استعلام قیمت در این بخش ذخیره و به ایمیل شما ارسال می‌شوند.', 'larijani-stone' ); ?></p>
			<a class="button button-primary" href="<?php echo esc_url( admin_url( 'edit.php?post_type=ls_lead' ) ); ?>"><?php esc_html_e( 'مشاهده درخواست‌ها', 'larijani-stone' ); ?></a>
			<?php else : ?>
			<p><?php esc_html_e( 'برای ذخیره امن درخواست‌ها و فایل‌های مشتریان، افزونه همراه «Larijani Stone Core» را نصب و فعال کنید.', 'larijani-stone' ); ?></p>
			<?php endif; ?>
		</div>
		<div class="ls-card">
			<h2><span class="dashicons dashicons-admin-tools"></span> <?php esc_html_e( 'راه‌اندازی خودکار', 'larijani-stone' ); ?></h2>
			<?php if ( get_option( 'ls_demo_imported' ) ) : ?>
			<p><?php echo esc_html( sprintf( /* translators: %s date */ __( 'برگه‌ها و منوهای قالب در %s ساخته شدند.', 'larijani-stone' ), larijani_jalali_date( (int) get_option( 'ls_demo_imported' ) ) ) ); ?></p>
			<?php else : ?>
			<p><?php esc_html_e( 'هنوز برگه‌های قالب ساخته نشده‌اند.', 'larijani-stone' ); ?></p>
			<?php endif; ?>
			<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=ls-setup' ) ); ?>"><?php esc_html_e( 'راه‌اندازی / ساخت مجدد برگه‌ها', 'larijani-stone' ); ?></a>
		</div>
	</div>
	<?php if ( $pages ) : ?>
	<div class="ls-card ls-card-wide">
		<h2><span class="dashicons dashicons-admin-page"></span> <?php esc_html_e( 'برگه‌ها و قالب‌های ساخته‌شده توسط پوسته', 'larijani-stone' ); ?></h2>
		<table class="widefat striped">
			<thead><tr><th><?php esc_html_e( 'عنوان', 'larijani-stone' ); ?></th><th><?php esc_html_e( 'نوع', 'larijani-stone' ); ?></th><th><?php esc_html_e( 'عملیات', 'larijani-stone' ); ?></th></tr></thead>
			<tbody>
			<?php foreach ( $pages as $p ) : ?>
				<tr>
					<td><strong><?php echo esc_html( $p->post_title ); ?></strong><?php echo (int) get_option( 'page_on_front' ) === $p->ID ? ' — <em>' . esc_html__( 'صفحه اصلی', 'larijani-stone' ) . '</em>' : ''; ?></td>
					<td><?php echo 'page' === $p->post_type ? esc_html__( 'برگه', 'larijani-stone' ) : esc_html__( 'قالب المنتور', 'larijani-stone' ); ?></td>
					<td>
						<?php if ( larijani_has_elementor() ) : ?>
						<a class="button button-small button-primary" href="<?php echo esc_url( admin_url( 'post.php?post=' . $p->ID . '&action=elementor' ) ); ?>"><?php esc_html_e( 'ویرایش با المنتور', 'larijani-stone' ); ?></a>
						<?php endif; ?>
						<?php if ( 'page' === $p->post_type ) : ?>
						<a class="button button-small" href="<?php echo esc_url( get_permalink( $p ) ); ?>" target="_blank"><?php esc_html_e( 'مشاهده', 'larijani-stone' ); ?></a>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php endif; ?>
	<?php
}

/**
 * Page markup.
 */
function larijani_settings_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$tabs = larijani_settings_tabs();
	$tab  = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'overview'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! isset( $tabs[ $tab ] ) ) {
		$tab = 'overview';
	}
	$updated = isset( $_GET['updated'] ) ? sanitize_key( wp_unslash( $_GET['updated'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	?>
	<div class="wrap ls-admin" dir="rtl">
		<div class="ls-admin-head">
			<img src="<?php echo esc_url( LARIJANI_URI . '/assets/images/logo.svg' ); ?>" alt="" width="44" height="44">
			<div>
				<h1><?php esc_html_e( 'تنظیمات قالب لاریجانی استون', 'larijani-stone' ); ?></h1>
				<p><?php esc_html_e( 'همه تنظیمات اینجا با «نمایش › سفارشی‌سازی» هماهنگ است و بلافاصله روی سایت و ویجت‌های المنتور اعمال می‌شود.', 'larijani-stone' ); ?></p>
			</div>
		</div>
		<?php if ( $updated ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php echo 'reset' === $updated ? esc_html__( 'تنظیمات به حالت پیش‌فرض برگشت. نسخه قبلی از «پیشخوان قالب › بازنشانی تنظیمات» قابل بازگردانی است.', 'larijani-stone' ) : ( 'restored' === $updated ? esc_html__( 'تنظیمات قبلی بازگردانی شد.', 'larijani-stone' ) : esc_html__( 'تنظیمات ذخیره شد.', 'larijani-stone' ) ); ?></p></div>
		<?php endif; ?>
		<nav class="nav-tab-wrapper ls-tabs">
			<?php foreach ( $tabs as $slug => $t ) : ?>
			<a class="nav-tab <?php echo $slug === $tab ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=ls-settings&tab=' . $slug ) ); ?>"><span class="dashicons <?php echo esc_attr( $t[1] ); ?>"></span> <?php echo esc_html( $t[0] ); ?></a>
			<?php endforeach; ?>
		</nav>
		<?php
		if ( 'overview' === $tab ) {
			larijani_settings_overview();
			echo '<div class="ls-cards">';
			larijani_settings_reset_card();
			echo '</div>';
			echo '</div>';
			return;
		}
		$fields = 'ls_builder' === $tab ? larijani_builder_fields() : larijani_option_fields();
		?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="ls-card ls-card-wide">
			<input type="hidden" name="action" value="ls_save_settings">
			<input type="hidden" name="ls_tab" value="<?php echo esc_attr( $tab ); ?>">
			<?php wp_nonce_field( 'ls_save_settings', 'ls_settings_nonce' ); ?>
			<?php if ( 'ls_builder' === $tab ) : ?>
			<p class="description">
				<?php
				echo larijani_has_elementor_pro()
					? esc_html__( 'المنتور پرو فعال است: شرط‌های نمایش قالب‌ها را از «قالب‌ها › تم‌بیلدر» مدیریت کنید. انتخاب‌های زیر فقط وقتی استفاده می‌شوند که برای آن بخش شرط پرو تعریف نشده باشد.', 'larijani-stone' )
					: esc_html__( 'بدون المنتور پرو هم می‌توانید برای هدر، فوتر، نوشته‌ها، آرشیو، محصولات و ۴۰۴ یک قالب ذخیره‌شده المنتور انتخاب کنید.', 'larijani-stone' );
				?>
			</p>
			<?php endif; ?>
			<?php if ( 'ls_brand' === $tab ) : ?>
			<p class="description">
				<?php esc_html_e( 'لوگو و آیکون سایت (فاوآیکن) از تنظیمات استاندارد وردپرس خوانده می‌شوند:', 'larijani-stone' ); ?>
				<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[control]=custom_logo' ) ); ?>"><?php esc_html_e( 'تغییر لوگو', 'larijani-stone' ); ?></a> |
				<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[control]=site_icon' ) ); ?>"><?php esc_html_e( 'تغییر آیکون سایت', 'larijani-stone' ); ?></a>
			</p>
			<?php endif; ?>
			<?php if ( 'ls_style' === $tab ) : ?>
			<p class="description"><?php esc_html_e( 'مقادیر «طرح اصلی» ظاهر فعلی قالب را دقیقاً حفظ می‌کنند. این تنظیمات روی بخش‌های قالب و ویجت‌های لاریجانی اعمال می‌شود؛ تنظیماتی که در تب Style المنتور برای یک ویجت مشخص کرده‌اید همیشه اولویت دارند. عرض بخش‌های کانتینری المنتور از تنظیمات هر بخش در المنتور تعیین می‌شود.', 'larijani-stone' ); ?></p>
			<?php endif; ?>
			<table class="form-table" role="presentation">
				<?php
				foreach ( $fields as $f ) {
					if ( $f[0] === $tab ) {
						larijani_settings_field( $f );
					}
				}
				?>
			</table>
			<p class="submit">
				<?php submit_button( __( 'ذخیره تنظیمات', 'larijani-stone' ), 'primary', 'submit', false ); ?>
				<button type="submit" name="ls_reset" value="1" class="button button-link-delete" data-ls-confirm="<?php esc_attr_e( 'تنظیمات این بخش به پیش‌فرض برگردد؟', 'larijani-stone' ); ?>"><?php esc_html_e( 'بازگشت به پیش‌فرض', 'larijani-stone' ); ?></button>
			</p>
		</form>
	</div>
	<?php
}

/**
 * "Settings" link on the Themes screen is not available for themes, so add a
 * quick link in the admin bar for administrators.
 *
 * @param WP_Admin_Bar $bar Admin bar.
 */
function larijani_admin_bar_link( $bar ) {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$bar->add_node(
		array(
			'id'    => 'ls-settings',
			'title' => '<span class="ab-icon dashicons dashicons-building" style="top:2px"></span>' . esc_html__( 'تنظیمات لاریجانی', 'larijani-stone' ),
			'href'  => admin_url( 'admin.php?page=ls-settings' ),
		)
	);
}
add_action( 'admin_bar_menu', 'larijani_admin_bar_link', 80 );
