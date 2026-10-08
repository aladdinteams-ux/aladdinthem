<?php
/**
 * Inquiry (lead) screens: details meta box, list columns, CSV export, settings & status.
 *
 * @package Larijani_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the lead details meta box.
 */
function larijani_core_lead_meta_box() {
	add_meta_box( 'ls_lead_meta', __( 'اطلاعات ثبت‌شده', 'larijani-stone-core' ), 'larijani_core_lead_meta_box_html', 'ls_lead', 'normal', 'high' );
}
add_action( 'add_meta_boxes_ls_lead', 'larijani_core_lead_meta_box' );

/**
 * Lead details markup.
 *
 * @param WP_Post $post Post.
 */
function larijani_core_lead_meta_box_html( $post ) {
	$fields = get_post_meta( $post->ID, '_ls_lead_fields', true );
	$legacy = get_post_meta( $post->ID, '_ls_lead_files', true );
	$page   = get_post_meta( $post->ID, '_ls_lead_page', true );
	echo '<table class="widefat striped"><tbody>';
	printf( '<tr><th style="width:220px">%s</th><td>%s</td></tr>', esc_html__( 'فرم', 'larijani-stone-core' ), esc_html( get_post_meta( $post->ID, '_ls_lead_form', true ) ) );
	if ( $page ) {
		printf( '<tr><th>%1$s</th><td><a href="%2$s" target="_blank" rel="noopener noreferrer">%3$s</a></td></tr>', esc_html__( 'صفحه ارسال', 'larijani-stone-core' ), esc_url( $page ), esc_html( $page ) );
	}
	printf( '<tr><th>%s</th><td><code>%s</code></td></tr>', esc_html__( 'کد پیگیری', 'larijani-stone-core' ), esc_html( get_post_meta( $post->ID, '_ls_lead_tracking', true ) ) );
	if ( is_array( $fields ) ) {
		foreach ( $fields as $row ) {
			if ( isset( $row['label'], $row['value'] ) ) {
				printf( '<tr><th>%s</th><td>%s</td></tr>', esc_html( $row['label'] ), nl2br( esc_html( $row['value'] ) ) );
			}
		}
	}
	foreach ( larijani_core_lead_files( $post->ID ) as $rec ) {
		printf(
			'<tr><th>%1$s</th><td><a href="%2$s">%3$s</a> <span class="description">(%4$s، %5$s)</span></td></tr>',
			esc_html__( 'فایل پیوست (خصوصی)', 'larijani-stone-core' ),
			esc_url( larijani_core_file_url( $post->ID, $rec['id'] ) ),
			esc_html( $rec['name'] ),
			esc_html( size_format( (int) $rec['size'] ) ),
			esc_html( strtoupper( pathinfo( $rec['path'], PATHINFO_EXTENSION ) ) )
		);
	}
	if ( is_array( $legacy ) ) {
		foreach ( $legacy as $att_id ) {
			if ( get_post( $att_id ) ) {
				printf(
					'<tr><th>%1$s</th><td><a href="%2$s">%3$s</a> <span class="description" style="color:#b32d2e">%4$s</span></td></tr>',
					esc_html__( 'فایل پیوست (قدیمی)', 'larijani-stone-core' ),
					esc_url( wp_get_attachment_url( $att_id ) ),
					esc_html( get_the_title( $att_id ) ),
					esc_html__( 'این فایل هنوز در پوشه عمومی است؛ از «درخواست‌ها › تنظیمات و امنیت» آن را به فضای خصوصی منتقل کنید.', 'larijani-stone-core' )
				);
			}
		}
	}
	echo '</tbody></table>';
}

/**
 * Lead list columns.
 *
 * @param array $cols Columns.
 * @return array
 */
function larijani_core_lead_columns( $cols ) {
	return array(
		'cb'          => isset( $cols['cb'] ) ? $cols['cb'] : '',
		'title'       => __( 'عنوان', 'larijani-stone-core' ),
		'ls_phone'    => __( 'تلفن', 'larijani-stone-core' ),
		'ls_form'     => __( 'فرم', 'larijani-stone-core' ),
		'ls_tracking' => __( 'کد پیگیری', 'larijani-stone-core' ),
		'date'        => isset( $cols['date'] ) ? $cols['date'] : __( 'تاریخ', 'larijani-stone-core' ),
	);
}
add_filter( 'manage_ls_lead_posts_columns', 'larijani_core_lead_columns' );

/**
 * Lead list column values.
 *
 * @param string $col Column.
 * @param int    $post_id Post id.
 */
function larijani_core_lead_column_values( $col, $post_id ) {
	if ( 'ls_phone' === $col ) {
		$phone = get_post_meta( $post_id, '_ls_lead_phone', true );
		echo $phone ? '<a href="' . esc_url( 'tel:' . preg_replace( '/[^\d+]/', '', larijani_core_latin_digits( $phone ) ) ) . '" dir="ltr">' . esc_html( $phone ) . '</a>' : '—';
	} elseif ( 'ls_form' === $col ) {
		echo esc_html( get_post_meta( $post_id, '_ls_lead_form', true ) );
	} elseif ( 'ls_tracking' === $col ) {
		echo '<code>' . esc_html( get_post_meta( $post_id, '_ls_lead_tracking', true ) ) . '</code>';
	}
}
add_action( 'manage_ls_lead_posts_custom_column', 'larijani_core_lead_column_values', 10, 2 );

/**
 * Search leads by tracking code / phone too.
 *
 * @param WP_Query $query Query.
 */
function larijani_core_lead_search( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || 'ls_lead' !== $query->get( 'post_type' ) ) {
		return;
	}
	$term = (string) $query->get( 's' );
	if ( preg_match( '/^LS-[\w-]+$/i', $term ) || preg_match( '/^[\d+\s۰-۹]{6,}$/u', $term ) ) {
		$query->set( 's', '' );
		$query->set(
			'meta_query', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				'relation' => 'OR',
				array(
					'key'   => '_ls_lead_tracking',
					'value' => strtoupper( $term ),
				),
				array(
					'key'     => '_ls_lead_phone',
					'value'   => larijani_core_latin_digits( $term ),
					'compare' => 'LIKE',
				),
			)
		);
	}
}
add_action( 'pre_get_posts', 'larijani_core_lead_search' );

/**
 * Submenu: settings, status and CSV export.
 */
function larijani_core_admin_menu() {
	add_submenu_page( 'edit.php?post_type=ls_lead', __( 'تنظیمات و امنیت درخواست‌ها', 'larijani-stone-core' ), __( 'تنظیمات و امنیت', 'larijani-stone-core' ), 'manage_options', 'larijani-core', 'larijani_core_settings_page' );
}
add_action( 'admin_menu', 'larijani_core_admin_menu' );

/**
 * Save settings.
 */
function larijani_core_save_settings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'دسترسی غیرمجاز.', 'larijani-stone-core' ), '', array( 'response' => 403 ) );
	}
	check_admin_referer( 'larijani_core_settings' );
	$email = isset( $_POST['larijani_core_leads_email'] ) ? sanitize_email( wp_unslash( $_POST['larijani_core_leads_email'] ) ) : '';
	update_option( 'larijani_core_leads_email', is_email( $email ) ? $email : '', false );
	update_option( 'larijani_core_delete_on_uninstall', empty( $_POST['larijani_core_delete_on_uninstall'] ) ? 0 : 1, false );
	update_option( 'larijani_core_allow_svg', empty( $_POST['larijani_core_allow_svg'] ) ? 0 : 1 );
	wp_safe_redirect(
		add_query_arg(
			array(
				'post_type' => 'ls_lead',
				'page'      => 'larijani-core',
				'updated'   => 1,
			),
			admin_url( 'edit.php' )
		)
	);
	exit;
}
add_action( 'admin_post_larijani_core_settings', 'larijani_core_save_settings' );

/**
 * Settings & status page.
 */
function larijani_core_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$status = null;
	if ( isset( $_GET['check'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'larijani_core_check' ) ) {
		$status = larijani_core_private_dir_status();
	}
	$legacy = larijani_core_legacy_file_count();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'تنظیمات و امنیت درخواست‌ها', 'larijani-stone-core' ); ?></h1>
		<?php if ( isset( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
		<div class="notice notice-success"><p><?php esc_html_e( 'ذخیره شد.', 'larijani-stone-core' ); ?></p></div>
		<?php endif; ?>
		<?php if ( isset( $_GET['migrated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
		<div class="notice notice-success"><p>
			<?php
			/* translators: 1: moved files, 2: failed files */
			echo esc_html( sprintf( __( '%1$d فایل به فضای خصوصی منتقل شد؛ %2$d مورد ناموفق.', 'larijani-stone-core' ), absint( $_GET['migrated'] ), isset( $_GET['failed'] ) ? absint( $_GET['failed'] ) : 0 ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			?>
		</p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="larijani_core_settings">
			<?php wp_nonce_field( 'larijani_core_settings' ); ?>
			<table class="form-table" role="presentation"><tbody>
				<tr>
					<th scope="row"><label for="larijani_core_leads_email"><?php esc_html_e( 'ایمیل دریافت درخواست‌ها', 'larijani-stone-core' ); ?></label></th>
					<td><input type="email" class="regular-text" dir="ltr" id="larijani_core_leads_email" name="larijani_core_leads_email" value="<?php echo esc_attr( get_option( 'larijani_core_leads_email' ) ); ?>">
					<p class="description"><?php esc_html_e( 'خالی = ایمیل تنظیم‌شده در قالب یا ایمیل مدیر سایت.', 'larijani-stone-core' ); ?></p></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'بارگذاری SVG', 'larijani-stone-core' ); ?></th>
					<td><label><input type="checkbox" name="larijani_core_allow_svg" value="1" <?php checked( get_option( 'larijani_core_allow_svg', 1 ) ); ?>> <?php esc_html_e( 'اجازه بارگذاری SVG فقط برای مدیران کل (فایل پیش از ذخیره پاک‌سازی می‌شود).', 'larijani-stone-core' ); ?></label></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'حذف افزونه', 'larijani-stone-core' ); ?></th>
					<td><label><input type="checkbox" name="larijani_core_delete_on_uninstall" value="1" <?php checked( get_option( 'larijani_core_delete_on_uninstall' ) ); ?>> <?php esc_html_e( 'هنگام حذف کامل افزونه، درخواست‌ها و فایل‌های خصوصی هم حذف شوند (پیش‌فرض: خاموش؛ نمونه‌کارها هرگز حذف نمی‌شوند).', 'larijani-stone-core' ); ?></label></td>
				</tr>
			</tbody></table>
			<?php submit_button(); ?>
		</form>

		<h2><?php esc_html_e( 'وضعیت فضای خصوصی فایل‌ها', 'larijani-stone-core' ); ?></h2>
		<p><?php esc_html_e( 'فایل‌های ارسالی مشتریان در پوشه‌ای با نام تصادفی ذخیره می‌شوند که با .htaccess و web.config محافظت شده است. روی سرور Nginx این فایل‌ها خوانده نمی‌شوند؛ با دکمه زیر بررسی کنید و در صورت نیاز ثابت LARIJANI_CORE_PRIVATE_DIR را به مسیری بیرون از پوشه عمومی در wp-config.php تعریف کنید.', 'larijani-stone-core' ); ?></p>
		<?php if ( 'protected' === $status ) : ?>
			<div class="notice notice-success inline"><p><?php esc_html_e( 'محافظت‌شده: فایل آزمایشی از طریق اینترنت قابل دریافت نبود.', 'larijani-stone-core' ); ?></p></div>
		<?php elseif ( 'exposed' === $status ) : ?>
			<div class="notice notice-error inline"><p><?php esc_html_e( 'هشدار: فایل آزمایشی از طریق اینترنت قابل دریافت بود. سرور شما .htaccess را اجرا نمی‌کند؛ قانون deny برای این پوشه در Nginx اضافه کنید یا LARIJANI_CORE_PRIVATE_DIR را تعریف کنید.', 'larijani-stone-core' ); ?></p></div>
		<?php elseif ( 'unknown' === $status ) : ?>
			<div class="notice notice-warning inline"><p><?php esc_html_e( 'بررسی ممکن نشد (سرور نتوانست به خودش درخواست بدهد).', 'larijani-stone-core' ); ?></p></div>
		<?php endif; ?>
		<p><a class="button" href="
		<?php
		echo esc_url(
			wp_nonce_url(
				add_query_arg(
					array(
						'post_type' => 'ls_lead',
						'page'      => 'larijani-core',
						'check'     => 1,
					),
					admin_url( 'edit.php' )
				),
				'larijani_core_check'
			)
		);
		?>
									"><?php esc_html_e( 'بررسی دسترسی عمومی', 'larijani-stone-core' ); ?></a></p>

		<h2><?php esc_html_e( 'فایل‌های قدیمی در پوشه عمومی', 'larijani-stone-core' ); ?></h2>
		<?php if ( $legacy ) : ?>
			<p>
			<?php
			/* translators: %d: number of files */
			echo esc_html( sprintf( __( '%d فایل پیوست از نسخه‌های قبلی قالب در کتابخانه رسانه (با لینک عمومی) وجود دارد. انتقال، هر فایل را به فضای خصوصی کپی و پس از تطبیق هش، نسخه عمومی را حذف می‌کند.', 'larijani-stone-core' ), $legacy ) );
			?>
			</p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="larijani_core_migrate_files">
				<?php wp_nonce_field( 'larijani_core_migrate_files' ); ?>
				<?php submit_button( __( 'انتقال امن فایل‌ها به فضای خصوصی', 'larijani-stone-core' ), 'primary', 'submit', false ); ?>
			</form>
		<?php else : ?>
			<p><?php esc_html_e( 'فایل قدیمی عمومی وجود ندارد.', 'larijani-stone-core' ); ?></p>
		<?php endif; ?>

		<h2><?php esc_html_e( 'خروجی', 'larijani-stone-core' ); ?></h2>
		<p><a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=larijani_core_export' ), 'larijani_core_export' ) ); ?>"><?php esc_html_e( 'دریافت فایل CSV درخواست‌ها', 'larijani-stone-core' ); ?></a></p>
	</div>
	<?php
}

/**
 * Neutralise spreadsheet formulas (CSV injection).
 *
 * @param string $value Value.
 * @return string
 */
function larijani_core_csv_cell( $value ) {
	$value = (string) $value;
	return preg_match( '/^[=+\-@\t\r]/', $value ) ? "'" . $value : $value;
}

/**
 * CSV export (editors and administrators).
 */
function larijani_core_export_csv() {
	if ( ! current_user_can( 'edit_others_posts' ) ) {
		wp_die( esc_html__( 'دسترسی غیرمجاز.', 'larijani-stone-core' ), '', array( 'response' => 403 ) );
	}
	check_admin_referer( 'larijani_core_export' );
	nocache_headers();
	header( 'Content-Type: text/csv; charset=UTF-8' );
	header( 'Content-Disposition: attachment; filename="leads-' . gmdate( 'Y-m-d' ) . '.csv"' );
	header( 'X-Content-Type-Options: nosniff' );
	$out = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
	fwrite( $out, "\xEF\xBB\xBF" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- UTF-8 BOM for Excel.
	fputcsv( $out, array( __( 'تاریخ', 'larijani-stone-core' ), __( 'کد پیگیری', 'larijani-stone-core' ), __( 'فرم', 'larijani-stone-core' ), __( 'تلفن', 'larijani-stone-core' ), __( 'ایمیل', 'larijani-stone-core' ), __( 'جزئیات', 'larijani-stone-core' ) ) );
	$page = 1;
	do {
		$ids = get_posts(
			array(
				'post_type'      => 'ls_lead',
				'post_status'    => 'any',
				'posts_per_page' => 200,
				'paged'          => $page,
				'fields'         => 'ids',
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);
		foreach ( $ids as $id ) {
			$details = array();
			foreach ( (array) get_post_meta( $id, '_ls_lead_fields', true ) as $row ) {
				if ( isset( $row['label'], $row['value'] ) && '' !== $row['value'] ) {
					$details[] = $row['label'] . ': ' . $row['value'];
				}
			}
			fputcsv(
				$out,
				array_map(
					'larijani_core_csv_cell',
					array(
						get_the_date( 'Y-m-d H:i', $id ),
						get_post_meta( $id, '_ls_lead_tracking', true ),
						get_post_meta( $id, '_ls_lead_form', true ),
						get_post_meta( $id, '_ls_lead_phone', true ),
						get_post_meta( $id, '_ls_lead_email', true ),
						implode( ' | ', $details ),
					)
				)
			);
		}
		++$page;
	} while ( count( $ids ) === 200 );
	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	exit;
}
add_action( 'admin_post_larijani_core_export', 'larijani_core_export_csv' );
