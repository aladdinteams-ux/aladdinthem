<?php
/**
 * AJAX lead / inquiry forms: stored as "ls_lead" posts and e-mailed to the site owner.
 *
 * Every form rendered by the theme carries data-ls-form and posts here via
 * assets/js/theme.js. Anonymous forms are protected with a honeypot, a
 * minimum fill-in time and per-IP rate limiting (nonces are avoided on purpose
 * because full-page caches would make them expire).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Allowed upload types for lead attachments.
 *
 * @return array
 */
function larijani_lead_mimes() {
	return apply_filters(
		'ls_lead_mimes',
		array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'png'          => 'image/png',
			'webp'         => 'image/webp',
			'pdf'          => 'application/pdf',
			'dwg'          => 'application/acad',
			'zip'          => 'application/zip',
		)
	);
}

/**
 * Handle a submission.
 */
function larijani_handle_lead() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- public form, see file header.
	if ( ! empty( $_POST['ls_hp'] ) ) {
		wp_send_json_success( array( 'tracking' => 'LS-' . wp_rand( 10000, 99999 ) ) ); // Silently accept bots.
	}
	$ts = isset( $_POST['ls_ts'] ) ? (int) $_POST['ls_ts'] : 0;
	if ( $ts && ( time() - $ts ) < 2 ) {
		wp_send_json_error( array( 'message' => __( 'ارسال بسیار سریع بود؛ لطفاً دوباره تلاش کنید.', 'larijani-stone' ) ), 400 );
	}

	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0';
	$key = 'ls_lead_rl_' . md5( $ip );
	$hits = (int) get_transient( $key );
	if ( $hits >= 8 ) {
		wp_send_json_error( array( 'message' => __( 'تعداد درخواست‌ها زیاد است. لطفاً چند دقیقه دیگر تلاش کنید یا تماس بگیرید.', 'larijani-stone' ) ), 429 );
	}
	set_transient( $key, $hits + 1, 10 * MINUTE_IN_SECONDS );

	$form_name = isset( $_POST['ls_form_name'] ) ? sanitize_text_field( wp_unslash( $_POST['ls_form_name'] ) ) : __( 'فرم تماس', 'larijani-stone' );
	$page      = isset( $_POST['ls_page'] ) ? esc_url_raw( wp_unslash( $_POST['ls_page'] ) ) : '';
	$raw       = isset( $_POST['fields'] ) && is_array( $_POST['fields'] ) ? wp_unslash( $_POST['fields'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$labels    = isset( $_POST['labels'] ) && is_array( $_POST['labels'] ) ? wp_unslash( $_POST['labels'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$types     = isset( $_POST['types'] ) && is_array( $_POST['types'] ) ? wp_unslash( $_POST['types'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	// phpcs:enable

	$rows  = array();
	$phone = '';
	$name  = '';
	$raw   = array_slice( $raw, 0, (int) apply_filters( 'ls_lead_max_fields', 40 ), true ); // Bound the payload.
	foreach ( $raw as $k => $value ) {
		$k     = sanitize_key( $k );
		$label = isset( $labels[ $k ] ) ? sanitize_text_field( $labels[ $k ] ) : $k;
		$type  = isset( $types[ $k ] ) ? sanitize_key( $types[ $k ] ) : 'text';
		if ( is_array( $value ) ) {
			$value = implode( '، ', array_map( 'sanitize_text_field', $value ) );
		} elseif ( 'textarea' === $type ) {
			$value = sanitize_textarea_field( $value );
		} else {
			$value = sanitize_text_field( $value );
		}
		if ( 'tel' === $type && ! $phone ) {
			$phone = larijani_en_num( $value );
		}
		if ( 'text' === $type && ! $name ) {
			$name = $value;
		}
		$rows[] = array(
			'label' => mb_substr( $label, 0, 120 ),
			'value' => mb_substr( $value, 0, 5000 ),
		);
	}

	if ( ! $rows || ( '' === trim( implode( '', wp_list_pluck( $rows, 'value' ) ) ) ) ) {
		wp_send_json_error( array( 'message' => __( 'لطفاً فرم را تکمیل کنید.', 'larijani-stone' ) ), 400 );
	}
	if ( $phone && ! preg_match( '/^\+?\d[\d\s\-]{7,15}$/', $phone ) ) {
		wp_send_json_error( array( 'message' => __( 'شماره تماس معتبر نیست.', 'larijani-stone' ) ), 400 );
	}

	$tracking = 'LS-' . wp_rand( 10000, 99999 );
	$title    = trim( $form_name . ' – ' . ( $name ? $name : $phone ) );

	$lead_id = wp_insert_post(
		array(
			'post_type'   => 'ls_lead',
			'post_status' => 'private',
			'post_title'  => $title,
		),
		true
	);
	if ( is_wp_error( $lead_id ) ) {
		wp_send_json_error( array( 'message' => __( 'ذخیره درخواست ممکن نشد.', 'larijani-stone' ) ), 500 );
	}

	update_post_meta( $lead_id, '_ls_lead_fields', $rows );
	update_post_meta( $lead_id, '_ls_lead_form', $form_name );
	update_post_meta( $lead_id, '_ls_lead_page', $page );
	update_post_meta( $lead_id, '_ls_lead_phone', $phone );
	update_post_meta( $lead_id, '_ls_lead_tracking', $tracking );

	$attachments = larijani_handle_lead_files( $lead_id );
	if ( $attachments ) {
		update_post_meta( $lead_id, '_ls_lead_files', $attachments );
	}

	larijani_send_lead_email( $title, $rows, $tracking, $page, $attachments, $lead_id );

	do_action( 'ls_lead_submitted', $lead_id, $rows, $form_name );

	wp_send_json_success( array( 'tracking' => $tracking ) );
}
add_action( 'wp_ajax_ls_lead', 'larijani_handle_lead' );
add_action( 'wp_ajax_nopriv_ls_lead', 'larijani_handle_lead' );

/**
 * Store uploaded files (max 5, 20MB each).
 *
 * @param int $lead_id Lead id.
 * @return int[] Attachment ids.
 */
function larijani_handle_lead_files( $lead_id ) {
	if ( empty( $_FILES['ls_files']['name'] ) || ! is_array( $_FILES['ls_files']['name'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return array();
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$files = $_FILES['ls_files']; // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput
	$ids   = array();
	$max   = (int) apply_filters( 'ls_lead_max_upload', 20 * MB_IN_BYTES );
	$count = min( 5, count( $files['name'] ) );

	for ( $i = 0; $i < $count; $i++ ) {
		if ( UPLOAD_ERR_OK !== (int) $files['error'][ $i ] || (int) $files['size'][ $i ] > $max ) {
			continue;
		}
		$file = array(
			// Unguessable name: lead attachments are customer documents.
			'name'     => wp_unique_filename( wp_upload_dir()['path'], 'lead-' . strtolower( wp_generate_password( 16, false ) ) . '-' . sanitize_file_name( $files['name'][ $i ] ) ),
			'type'     => $files['type'][ $i ],
			'tmp_name' => $files['tmp_name'][ $i ],
			'error'    => $files['error'][ $i ],
			'size'     => $files['size'][ $i ],
		);
		$upload = wp_handle_upload(
			$file,
			array(
				'test_form' => false,
				'mimes'     => larijani_lead_mimes(),
			)
		);
		if ( empty( $upload['file'] ) || ! empty( $upload['error'] ) ) {
			continue;
		}
		$att_id = wp_insert_attachment(
			array(
				'post_mime_type' => $upload['type'],
				'post_title'     => sanitize_file_name( $files['name'][ $i ] ),
				'post_status'    => 'private',
				'post_parent'    => $lead_id,
			),
			$upload['file'],
			$lead_id
		);
		if ( ! is_wp_error( $att_id ) ) {
			if ( 0 === strpos( $upload['type'], 'image/' ) ) {
				wp_update_attachment_metadata( $att_id, wp_generate_attachment_metadata( $att_id, $upload['file'] ) );
			}
			$ids[] = $att_id;
		}
	}
	return $ids;
}

/**
 * Notify the owner by e-mail.
 *
 * @param string $title Title.
 * @param array  $rows Fields.
 * @param string $tracking Tracking code.
 * @param string $page Page URL.
 * @param array  $attachments Attachment ids.
 * @param int    $lead_id Lead id.
 */
function larijani_send_lead_email( $title, $rows, $tracking, $page, $attachments, $lead_id ) {
	$to = larijani_opt( 'leads_email' );
	if ( ! $to || ! is_email( $to ) ) {
		$to = get_option( 'admin_email' );
	}
	$html  = '<div dir="rtl" style="font-family:Tahoma,sans-serif;font-size:14px;line-height:1.9">';
	$html .= '<h2 style="margin:0 0 12px">' . esc_html( $title ) . '</h2>';
	$html .= '<table cellpadding="8" style="border-collapse:collapse;width:100%">';
	foreach ( $rows as $row ) {
		$html .= '<tr><th style="text-align:right;background:#F8F9F7;border:1px solid #E8ECE6;width:35%">' . esc_html( $row['label'] ) . '</th><td style="border:1px solid #E8ECE6">' . nl2br( esc_html( $row['value'] ) ) . '</td></tr>';
	}
	$html .= '</table>';
	$html .= '<p>' . esc_html__( 'کد پیگیری:', 'larijani-stone' ) . ' <b>' . esc_html( $tracking ) . '</b></p>';
	if ( $page ) {
		$html .= '<p>' . esc_html__( 'صفحه:', 'larijani-stone' ) . ' <a href="' . esc_url( $page ) . '">' . esc_html( $page ) . '</a></p>';
	}
	foreach ( (array) $attachments as $att ) {
		$html .= '<p><a href="' . esc_url( wp_get_attachment_url( $att ) ) . '">' . esc_html__( 'فایل پیوست', 'larijani-stone' ) . '</a></p>';
	}
	$html .= '<p><a href="' . esc_url( admin_url( 'post.php?post=' . $lead_id . '&action=edit' ) ) . '">' . esc_html__( 'مشاهده در پیشخوان', 'larijani-stone' ) . '</a></p></div>';

	wp_mail( $to, '[' . get_bloginfo( 'name' ) . '] ' . $title, $html, array( 'Content-Type: text/html; charset=UTF-8' ) );
}

/**
 * Common hidden inputs every theme form must include.
 *
 * @param string $form_name Human readable form name (stored with the lead).
 * @return string
 */
function larijani_form_hidden_fields( $form_name ) {
	return sprintf(
		'<input type="hidden" name="action" value="ls_lead"><input type="hidden" name="ls_form_name" value="%s"><input type="hidden" name="ls_ts" value="%d"><div aria-hidden="true" style="position:absolute;width:1px;height:1px;overflow:hidden;clip-path:inset(50%%);white-space:nowrap;opacity:0;pointer-events:none"><label>Leave empty<input type="text" name="ls_hp" tabindex="-1" autocomplete="off"></label></div>',
		esc_attr( $form_name ),
		time()
	);
}
