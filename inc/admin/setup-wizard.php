<?php
/**
 * Setup wizard helpers: server requirements, plugin installation (only after
 * the administrator presses a button), and a journal of every demo import so
 * a failed or unwanted run can be undone without touching the owner's content.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Convert a php.ini size to bytes.
 *
 * @param string $val Value.
 * @return int
 */
function larijani_ini_bytes( $val ) {
	return function_exists( 'wp_convert_hr_to_bytes' ) ? (int) wp_convert_hr_to_bytes( (string) $val ) : (int) $val;
}

/**
 * Server requirement checks.
 *
 * @return array[] Each: label, value, status (ok|warn|fail), hint.
 */
function larijani_requirements() {
	global $wp_version;
	$rows   = array();
	$memory = larijani_ini_bytes( ini_get( 'memory_limit' ) );
	$time   = (int) ini_get( 'max_execution_time' );
	$upload = wp_max_upload_size();

	$rows[] = array( __( 'نسخه PHP', 'larijani-stone' ), PHP_VERSION, version_compare( PHP_VERSION, '7.4', '<' ) ? 'fail' : ( version_compare( PHP_VERSION, '8.1', '<' ) ? 'warn' : 'ok' ), __( 'حداقل ۷٫۴، پیشنهادی ۸٫۱ یا بالاتر', 'larijani-stone' ) );
	$rows[] = array( __( 'نسخه وردپرس', 'larijani-stone' ), $wp_version, version_compare( $wp_version, '6.5', '<' ) ? 'fail' : 'ok', __( 'حداقل ۶٫۵', 'larijani-stone' ) );
	$rows[] = array( __( 'حافظه PHP (memory_limit)', 'larijani-stone' ), ini_get( 'memory_limit' ), ( -1 === $memory || $memory >= 256 * MB_IN_BYTES ) ? 'ok' : ( $memory >= 128 * MB_IN_BYTES ? 'warn' : 'fail' ), __( 'حداقل 128M، پیشنهادی 256M برای المنتور', 'larijani-stone' ) );
	$rows[] = array( __( 'حداکثر زمان اجرا', 'larijani-stone' ), $time ? $time . 's' : __( 'نامحدود', 'larijani-stone' ), ( 0 === $time || $time >= 120 ) ? 'ok' : ( $time >= 60 ? 'warn' : 'fail' ), __( 'پیشنهادی ۱۲۰ ثانیه برای درون‌ریزی دمو', 'larijani-stone' ) );
	$rows[] = array( __( 'حداکثر حجم بارگذاری', 'larijani-stone' ), size_format( $upload ), $upload >= 32 * MB_IN_BYTES ? 'ok' : 'warn', __( 'پیشنهادی 32M برای فایل‌های پیوست فرم‌ها (DWG/ZIP)', 'larijani-stone' ) );
	foreach ( array(
		'mbstring' => __( 'لازم برای متن فارسی', 'larijani-stone' ),
		'dom'      => __( 'لازم برای پاک‌سازی SVG', 'larijani-stone' ),
		'fileinfo' => __( 'لازم برای بررسی نوع فایل‌های ارسالی', 'larijani-stone' ),
		'zip'      => __( 'لازم برای بررسی فایل‌های ZIP و نصب افزونه', 'larijani-stone' ),
		'curl'     => __( 'برای نصب افزونه‌ها و دانلود تصاویر', 'larijani-stone' ),
	) as $ext => $hint ) {
		$rows[] = array( sprintf( /* translators: %s: PHP extension */ __( 'افزونه PHP: %s', 'larijani-stone' ), $ext ), extension_loaded( $ext ) ? __( 'فعال', 'larijani-stone' ) : __( 'غیرفعال', 'larijani-stone' ), extension_loaded( $ext ) ? 'ok' : ( in_array( $ext, array( 'mbstring', 'fileinfo' ), true ) ? 'fail' : 'warn' ), $hint );
	}
	$gd     = extension_loaded( 'gd' ) || extension_loaded( 'imagick' );
	$rows[] = array( __( 'پردازش تصویر (GD یا Imagick)', 'larijani-stone' ), $gd ? __( 'فعال', 'larijani-stone' ) : __( 'غیرفعال', 'larijani-stone' ), $gd ? 'ok' : 'warn', __( 'برای ساخت اندازه‌های تصاویر', 'larijani-stone' ) );
	$rows[] = array( __( 'پیوند یکتا', 'larijani-stone' ), get_option( 'permalink_structure' ) ? __( 'فعال', 'larijani-stone' ) : __( 'ساده', 'larijani-stone' ), get_option( 'permalink_structure' ) ? 'ok' : 'warn', __( 'برای نشانی‌های فارسی و سئو', 'larijani-stone' ) );
	$writable = wp_is_writable( WP_CONTENT_DIR . '/plugins' ) || wp_is_writable( WP_PLUGIN_DIR );
	$rows[]   = array( __( 'امکان نصب افزونه', 'larijani-stone' ), $writable ? __( 'بله', 'larijani-stone' ) : __( 'خیر', 'larijani-stone' ), $writable ? 'ok' : 'warn', __( 'در غیر این صورت افزونه‌ها را دستی بارگذاری کنید', 'larijani-stone' ) );
	return $rows;
}

/**
 * Required / recommended plugins.
 *
 * @return array slug => data
 */
function larijani_wizard_plugins() {
	return array(
		'larijani-stone-core' => array(
			'name'     => 'Larijani Stone Core',
			'file'     => 'larijani-stone-core/larijani-stone-core.php',
			'source'   => LARIJANI_DIR . '/bundled/larijani-stone-core.zip',
			'level'    => 'required',
			'desc'     => __( 'افزونه همراه قالب: نمونه‌کارها، درخواست‌ها، فرم‌های امن و نگهداری خصوصی فایل‌های مشتری. همراه قالب ارائه می‌شود.', 'larijani-stone' ),
			'external' => false,
		),
		'elementor'           => array(
			'name'     => 'Elementor',
			'file'     => 'elementor/elementor.php',
			'source'   => 'wordpress.org',
			'level'    => 'required',
			'desc'     => __( 'رایگان؛ برای ویرایش بصری برگه‌ها. بدون آن برگه‌ها با همان طرح نمایش داده می‌شوند.', 'larijani-stone' ),
			'external' => true,
		),
		'woocommerce'         => array(
			'name'     => 'WooCommerce',
			'file'     => 'woocommerce/woocommerce.php',
			'source'   => 'wordpress.org',
			'level'    => 'optional',
			'desc'     => __( 'اختیاری؛ فقط اگر فروشگاه آنلاین لازم دارید.', 'larijani-stone' ),
			'external' => true,
		),
	);
}

/**
 * Plugin state.
 *
 * @param array $p Plugin data.
 * @return string active|installed|missing
 */
function larijani_plugin_state( $p ) {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	if ( is_plugin_active( $p['file'] ) ) {
		return 'active';
	}
	return file_exists( WP_PLUGIN_DIR . '/' . $p['file'] ) ? 'installed' : 'missing';
}

/**
 * Install and/or activate one plugin after an explicit click.
 */
function larijani_wizard_install_plugin() {
	$slug    = isset( $_POST['plugin'] ) ? sanitize_key( wp_unslash( $_POST['plugin'] ) ) : '';
	$plugins = larijani_wizard_plugins();
	check_admin_referer( 'larijani_plugin_' . $slug );
	if ( ! isset( $plugins[ $slug ] ) || ! current_user_can( 'install_plugins' ) || ! current_user_can( 'activate_plugins' ) ) {
		wp_die( esc_html__( 'شما اجازه نصب افزونه را ندارید.', 'larijani-stone' ), '', array( 'response' => 403 ) );
	}
	$p      = $plugins[ $slug ];
	$result = true;
	if ( 'missing' === larijani_plugin_state( $p ) ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		$package = '';
		if ( $p['external'] ) {
			$api = plugins_api( 'plugin_information', array( 'slug' => $slug, 'fields' => array( 'sections' => false ) ) );
			$package = is_wp_error( $api ) ? '' : $api->download_link;
		} elseif ( file_exists( $p['source'] ) ) {
			$package = $p['source'];
		}
		if ( ! $package ) {
			$result = new WP_Error( 'no_package', __( 'بسته نصب در دسترس نیست (اتصال به wordpress.org یا فایل همراه قالب).', 'larijani-stone' ) );
		} else {
			$upgrader = new Plugin_Upgrader( new WP_Ajax_Upgrader_Skin() );
			$result   = $upgrader->install( $package );
			if ( ! is_wp_error( $result ) && ! $result ) {
				$errors = $upgrader->skin->get_errors();
				$result = is_wp_error( $errors ) && $errors->has_errors() ? $errors : new WP_Error( 'install_failed', __( 'نصب انجام نشد. ممکن است دسترسی نوشتن یا اطلاعات FTP لازم باشد.', 'larijani-stone' ) );
			}
		}
	}
	if ( ! is_wp_error( $result ) ) {
		$result = activate_plugin( $p['file'] );
	}
	$msg = is_wp_error( $result ) ? 'error:' . $result->get_error_message() : 'ok:' . $p['name'];
	set_transient( 'larijani_wizard_msg_' . get_current_user_id(), $msg, 5 * MINUTE_IN_SECONDS );
	wp_safe_redirect( admin_url( 'admin.php?page=ls-setup' ) );
	exit;
}
add_action( 'admin_post_larijani_install_plugin', 'larijani_wizard_install_plugin' );

/* -------------------------------------------------------------------------
 * Import journal & undo.
 * ---------------------------------------------------------------------- */

/**
 * Start recording what an import creates or changes.
 *
 * @param array $parts Parts.
 */
function larijani_journal_start( $parts ) {
	$watch = array( 'show_on_front', 'page_on_front', 'page_for_posts', 'woocommerce_shop_page_id', 'elementor_cpt_support', 'permalink_structure' );
	$prev  = array();
	foreach ( $watch as $opt ) {
		$prev[ $opt ] = get_option( $opt, null );
	}
	$mods = array();
	foreach ( array( 'nav_menu_locations', 'ls_tb_header', 'ls_tb_footer', 'ls_tb_single_post', 'ls_tb_archive', 'ls_tb_single_product', 'ls_tb_shop', 'ls_tb_404' ) as $mod ) {
		$mods[ $mod ] = get_theme_mod( $mod, null );
	}
	$kit_id                               = (int) get_option( 'elementor_active_kit' );
	$GLOBALS['larijani_journal_previous'] = get_option( 'larijani_import_journal' );
	$GLOBALS['larijani_journal']          = array(
		'id'      => wp_generate_password( 8, false, false ),
		'started' => time(),
		'status'  => 'running',
		'parts'   => array_values( $parts ),
		'user'    => get_current_user_id(),
		'posts'   => array(),
		'terms'   => array(),
		'options' => $prev,
		'mods'    => $mods,
		'kit'     => $kit_id ? array( 'id' => $kit_id, 'settings' => get_post_meta( $kit_id, '_elementor_page_settings', true ) ) : null,
		'error'   => '',
	);
	update_option( 'larijani_import_journal', $GLOBALS['larijani_journal'], false );
	add_action( 'wp_insert_post', 'larijani_journal_post', 10, 3 );
	add_action( 'created_term', 'larijani_journal_term', 10, 3 );
}

/**
 * Record a post created during the import.
 *
 * @param int     $post_id Post id.
 * @param WP_Post $post Post.
 * @param bool    $update Update.
 */
function larijani_journal_post( $post_id, $post, $update ) {
	if ( $update || empty( $GLOBALS['larijani_journal'] ) || 'revision' === $post->post_type || 'auto-draft' === $post->post_status ) {
		return;
	}
	$GLOBALS['larijani_journal']['posts'][] = (int) $post_id;
	update_option( 'larijani_import_journal', $GLOBALS['larijani_journal'], false ); // Persist as we go: survives a fatal error.
}

/**
 * Record a term created during the import.
 *
 * @param int    $term_id Term id.
 * @param int    $tt_id Term taxonomy id.
 * @param string $taxonomy Taxonomy.
 */
function larijani_journal_term( $term_id, $tt_id, $taxonomy ) {
	if ( empty( $GLOBALS['larijani_journal'] ) ) {
		return;
	}
	$GLOBALS['larijani_journal']['terms'][] = array( (int) $term_id, $taxonomy );
	update_option( 'larijani_import_journal', $GLOBALS['larijani_journal'], false );
}

/**
 * Finish the journal.
 *
 * @param string $status done|failed.
 * @param string $error Error message.
 */
function larijani_journal_end( $status, $error = '' ) {
	remove_action( 'wp_insert_post', 'larijani_journal_post', 10 );
	remove_action( 'created_term', 'larijani_journal_term', 10 );
	if ( empty( $GLOBALS['larijani_journal'] ) ) {
		return;
	}
	$j             = $GLOBALS['larijani_journal'];
	$j['status']   = $status;
	$j['error']    = $error;
	$j['finished'] = time();
	$changed       = $j['posts'] || $j['terms'] || 'done' !== $status;
	foreach ( $j['options'] as $opt => $val ) {
		$changed = $changed || get_option( $opt, null ) !== $val;
	}
	foreach ( $j['mods'] as $mod => $val ) {
		$changed = $changed || get_theme_mod( $mod, null ) !== $val;
	}
	$previous = $GLOBALS['larijani_journal_previous'] ?? null;
	if ( ! $changed && is_array( $previous ) ) {
		// Nothing new was created (safe re-run): keep the previous run undoable.
		update_option( 'larijani_import_journal', $previous, false );
	} else {
		update_option( 'larijani_import_journal', $j, false );
	}
	unset( $GLOBALS['larijani_journal'], $GLOBALS['larijani_journal_previous'] );
}

/**
 * Undo the last import: delete only what it created (and has not been edited
 * since), restore the options it changed. Content that existed before is never touched.
 */
function larijani_journal_undo() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'دسترسی غیرمجاز.', 'larijani-stone' ), '', array( 'response' => 403 ) );
	}
	check_admin_referer( 'larijani_import_undo' );
	$j = get_option( 'larijani_import_journal' );
	if ( ! is_array( $j ) || 'undone' === $j['status'] ) {
		wp_safe_redirect( admin_url( 'admin.php?page=ls-setup' ) );
		exit;
	}
	$removed = 0;
	$kept    = 0;
	foreach ( array_reverse( array_unique( $j['posts'] ) ) as $id ) {
		$post = get_post( $id );
		if ( ! $post ) {
			continue;
		}
		// Edited by a person after the import finished: keep it.
		$end = ! empty( $j['finished'] ) ? (int) $j['finished'] : (int) $j['started'] + 10 * MINUTE_IN_SECONDS;
		if ( strtotime( $post->post_modified_gmt . ' UTC' ) > $end + 60 && 'nav_menu_item' !== $post->post_type && 'attachment' !== $post->post_type ) {
			++$kept;
			continue;
		}
		if ( 'attachment' === $post->post_type ) {
			wp_delete_attachment( $id, true );
		} else {
			wp_delete_post( $id, true );
		}
		++$removed;
	}
	foreach ( array_reverse( $j['terms'] ) as $t ) {
		$term = get_term( $t[0], $t[1] );
		if ( $term && ! is_wp_error( $term ) && 0 === (int) $term->count ) {
			wp_delete_term( $t[0], $t[1] );
		} elseif ( $term && ! is_wp_error( $term ) && 'nav_menu' === $t[1] ) {
			wp_delete_nav_menu( $t[0] );
		}
	}
	foreach ( $j['options'] as $opt => $val ) {
		if ( null === $val ) {
			delete_option( $opt );
		} else {
			update_option( $opt, $val );
		}
	}
	foreach ( $j['mods'] as $mod => $val ) {
		if ( null === $val ) {
			remove_theme_mod( $mod );
		} else {
			set_theme_mod( $mod, $val );
		}
	}
	if ( ! empty( $j['kit']['id'] ) && get_post( $j['kit']['id'] ) ) {
		update_post_meta( $j['kit']['id'], '_elementor_page_settings', $j['kit']['settings'] );
	}
	if ( larijani_has_elementor() && class_exists( '\Elementor\Plugin' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	flush_rewrite_rules();
	$j['status'] = 'undone';
	update_option( 'larijani_import_journal', $j, false );
	set_transient(
		'ls_import_report',
		esc_html(
			sprintf(
				/* translators: 1: removed items, 2: kept items */
				__( 'درون‌ریزی قبلی برگردانده شد: %1$d مورد ساخته‌شده حذف شد، تنظیمات قبلی بازگردانی شد. %2$d مورد که پس از درون‌ریزی ویرایش شده بود حفظ شد.', 'larijani-stone' ),
				$removed,
				$kept
			)
		),
		5 * MINUTE_IN_SECONDS
	);
	wp_safe_redirect( admin_url( 'admin.php?page=ls-setup' ) );
	exit;
}
add_action( 'admin_post_larijani_import_undo', 'larijani_journal_undo' );

/**
 * Does the site already hold the owner's own content?
 *
 * @return bool
 */
function larijani_site_has_content() {
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => 5,
			'fields'         => 'ids',
			'meta_query'     => array( array( 'key' => '_ls_demo_page', 'compare' => 'NOT EXISTS' ) ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		)
	);
	$pages = array_diff( $pages, array( (int) get_option( 'wp_page_for_privacy_policy' ) ) );
	foreach ( $pages as $i => $id ) {
		if ( 'sample-page' === get_post_field( 'post_name', $id ) ) {
			unset( $pages[ $i ] );
		}
	}
	$posts = wp_count_posts( 'post' );
	return (bool) $pages || (int) $posts->publish > 1;
}

/**
 * Wizard sections printed on the setup page (requirements, plugins, last import).
 */
function larijani_wizard_sections() {
	$msg = get_transient( 'larijani_wizard_msg_' . get_current_user_id() );
	delete_transient( 'larijani_wizard_msg_' . get_current_user_id() );
	if ( $msg ) {
		$is_ok = 0 === strpos( $msg, 'ok:' );
		printf(
			'<div class="notice %1$s"><p>%2$s</p></div>',
			$is_ok ? 'notice-success' : 'notice-error',
			esc_html( $is_ok ? sprintf( /* translators: %s plugin */ __( 'افزونه %s نصب و فعال شد.', 'larijani-stone' ), substr( $msg, 3 ) ) : sprintf( /* translators: %s error */ __( 'نصب افزونه ناموفق بود: %s', 'larijani-stone' ), substr( $msg, 6 ) ) )
		);
	}
	$icons = array( 'ok' => '✅', 'warn' => '⚠️', 'fail' => '❌' );
	?>
	<div class="ls-card ls-card-wide">
		<h2><?php esc_html_e( '۱. بررسی نیازمندی‌های سرور', 'larijani-stone' ); ?></h2>
		<table class="widefat striped"><tbody>
		<?php foreach ( larijani_requirements() as $r ) : ?>
			<tr><td style="width:32px"><?php echo esc_html( $icons[ $r[2] ] ); ?></td><th scope="row"><?php echo esc_html( $r[0] ); ?></th><td dir="ltr" style="text-align:right"><?php echo esc_html( $r[1] ); ?></td><td class="description"><?php echo esc_html( $r[3] ); ?></td></tr>
		<?php endforeach; ?>
		</tbody></table>
	</div>
	<div class="ls-card ls-card-wide">
		<h2><?php esc_html_e( '۲. افزونه‌ها', 'larijani-stone' ); ?></h2>
		<p class="description"><?php esc_html_e( 'هیچ افزونه‌ای بدون کلیک شما نصب یا فعال نمی‌شود. المنتور پرو لازم نیست؛ اگر نسخه قانونی آن را دارید، قالب از تم‌بیلدر آن هم پشتیبانی می‌کند.', 'larijani-stone' ); ?></p>
		<table class="widefat striped"><tbody>
		<?php
		foreach ( larijani_wizard_plugins() as $slug => $p ) :
			$state = larijani_plugin_state( $p );
			?>
			<tr>
				<th scope="row"><?php echo esc_html( $p['name'] ); ?> <span class="description">(<?php echo 'required' === $p['level'] ? esc_html__( 'لازم', 'larijani-stone' ) : esc_html__( 'اختیاری', 'larijani-stone' ); ?>)</span></th>
				<td><?php echo esc_html( $p['desc'] ); ?></td>
				<td style="width:200px">
				<?php if ( 'active' === $state ) : ?>
					✅ <?php esc_html_e( 'فعال', 'larijani-stone' ); ?>
				<?php elseif ( current_user_can( 'install_plugins' ) ) : ?>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="larijani_install_plugin">
						<input type="hidden" name="plugin" value="<?php echo esc_attr( $slug ); ?>">
						<?php wp_nonce_field( 'larijani_plugin_' . $slug ); ?>
						<button type="submit" class="button <?php echo 'required' === $p['level'] ? 'button-primary' : ''; ?>"><?php echo 'installed' === $state ? esc_html__( 'فعال‌سازی', 'larijani-stone' ) : esc_html__( 'نصب و فعال‌سازی', 'larijani-stone' ); ?></button>
					</form>
				<?php else : ?>
					<?php esc_html_e( 'نیازمند دسترسی مدیر کل', 'larijani-stone' ); ?>
				<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody></table>
	</div>
	<?php
}

/**
 * Last import status + undo button.
 */
function larijani_wizard_journal_card() {
	$j = get_option( 'larijani_import_journal' );
	if ( ! is_array( $j ) ) {
		return;
	}
	$stale = 'running' === $j['status'] && time() - (int) $j['started'] > 10 * MINUTE_IN_SECONDS;
	?>
	<div class="ls-card ls-card-wide">
		<h2><?php esc_html_e( 'آخرین درون‌ریزی', 'larijani-stone' ); ?></h2>
		<p>
		<?php
		if ( 'done' === $j['status'] ) {
			esc_html_e( 'با موفقیت انجام شد.', 'larijani-stone' );
		} elseif ( 'failed' === $j['status'] || $stale ) {
			echo '<strong style="color:#b32d2e">' . esc_html__( 'کامل نشد.', 'larijani-stone' ) . '</strong> ' . esc_html( $j['error'] ? $j['error'] : __( 'اجرا قطع شد (احتمالاً محدودیت زمان یا حافظه سرور).', 'larijani-stone' ) ) . ' ' . esc_html__( 'می‌توانید موارد ساخته‌شده را برگردانید و دوباره اجرا کنید.', 'larijani-stone' );
		} elseif ( 'undone' === $j['status'] ) {
			esc_html_e( 'برگردانده شده است.', 'larijani-stone' );
		} else {
			esc_html_e( 'در حال اجرا…', 'larijani-stone' );
		}
		echo ' ' . esc_html( larijani_jalali_date( (int) $j['started'] ) );
		?>
		</p>
		<?php if ( 'undone' !== $j['status'] && ( count( $j['posts'] ) || count( $j['terms'] ) ) ) : ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="larijani_import_undo">
			<?php wp_nonce_field( 'larijani_import_undo' ); ?>
			<button type="submit" class="button" data-ls-confirm="<?php esc_attr_e( 'موارد ساخته‌شده در آخرین درون‌ریزی حذف و تنظیمات قبلی بازگردانی شود؟ محتوای قبلی شما و مواردی که بعد از درون‌ریزی ویرایش کرده‌اید حفظ می‌شوند.', 'larijani-stone' ); ?>">
				<?php echo esc_html( sprintf( /* translators: %d: items */ __( 'برگرداندن آخرین درون‌ریزی (%d مورد)', 'larijani-stone' ), count( array_unique( $j['posts'] ) ) ) ); ?>
			</button>
		</form>
		<?php endif; ?>
	</div>
	<?php
}
