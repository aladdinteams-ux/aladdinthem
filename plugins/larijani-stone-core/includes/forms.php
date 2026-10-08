<?php
/**
 * Secure public inquiry forms.
 *
 * Protection layers (none of them relies on a page-cached nonce):
 * 1. Signed form schema  – the server, not the browser, decides which fields
 *    exist, their labels, types, required flags and allowed options.
 * 2. Signed, single-use submission token fetched by JavaScript right before
 *    the visitor starts filling the form (cache-safe). It enforces a minimum
 *    fill-in time and expires after two hours; replaying it is impossible.
 * 3. Honeypot field.
 * 4. Rate limits per IP (attempts and successes), per phone and site-wide.
 * 5. Duplicate detection (same form + same answers within 15 minutes returns
 *    the original tracking code instead of creating a new lead).
 * 6. Strict server-side validation (phone, e-mail, number, options, length).
 * 7. Private file storage (see uploads.php).
 *
 * @package Larijani_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field types the schema may use.
 *
 * @return string[]
 */
function larijani_core_field_types() {
	return array( 'text', 'tel', 'email', 'number', 'select', 'checkboxes', 'textarea', 'file', 'consent', 'contact' );
}

/**
 * Tunable security settings.
 *
 * @return array
 */
function larijani_core_form_settings() {
	return apply_filters(
		'larijani_core_form_settings',
		array(
			'min_seconds'       => 3,
			'token_ttl'         => 2 * HOUR_IN_SECONDS,
			'ip_attempts'       => array( 10, 10 * MINUTE_IN_SECONDS ),
			'ip_success'        => array( 5, 10 * MINUTE_IN_SECONDS ),
			'ip_daily'          => array( 20, DAY_IN_SECONDS ),
			'phone_hourly'      => array( 3, HOUR_IN_SECONDS ),
			'global_hourly'     => array( 200, HOUR_IN_SECONDS ),
			'token_requests'    => array( 30, 10 * MINUTE_IN_SECONDS ),
			'duplicate_window'  => 15 * MINUTE_IN_SECONDS,
			'max_fields'        => 40,
			'max_text_length'   => 200,
			'max_textarea'      => 5000,
		)
	);
}

/**
 * HMAC key for tokens and schemas (derived from the site salts).
 *
 * @return string
 */
function larijani_core_secret() {
	return hash_hmac( 'sha256', 'larijani-stone-core/forms', wp_salt( 'auth' ) );
}

/**
 * URL-safe base64.
 *
 * @param string $data Data.
 * @return string
 */
function larijani_core_b64( $data ) {
	return rtrim( strtr( base64_encode( $data ), '+/', '-_' ), '=' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- signed transport encoding.
}

/**
 * Decode URL-safe base64.
 *
 * @param string $data Data.
 * @return string|false
 */
function larijani_core_unb64( $data ) {
	return base64_decode( strtr( $data, '-_', '+/' ), true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- signed transport encoding.
}

/**
 * Sign an array.
 *
 * @param array  $data Data.
 * @param string $purpose Context, so a token can never be used as a schema.
 * @return string
 */
function larijani_core_sign( $data, $purpose ) {
	$body = larijani_core_b64( wp_json_encode( $data ) );
	return $body . '.' . larijani_core_b64( hash_hmac( 'sha256', $purpose . '|' . $body, larijani_core_secret(), true ) );
}

/**
 * Verify and decode a signed value.
 *
 * @param string $signed Signed string.
 * @param string $purpose Context.
 * @return array|false
 */
function larijani_core_verify( $signed, $purpose ) {
	if ( ! is_string( $signed ) || strlen( $signed ) > 20000 || 1 !== substr_count( $signed, '.' ) ) {
		return false;
	}
	list( $body, $mac ) = explode( '.', $signed );
	$expected = larijani_core_b64( hash_hmac( 'sha256', $purpose . '|' . $body, larijani_core_secret(), true ) );
	if ( ! hash_equals( $expected, $mac ) ) {
		return false;
	}
	$json = larijani_core_unb64( $body );
	$data = $json ? json_decode( $json, true ) : null;
	return is_array( $data ) ? $data : false;
}

/**
 * Normalise a schema definition coming from a theme renderer.
 *
 * @param string $form_name Form name.
 * @param array  $fields key => array( label, type, required, options ).
 * @return array
 */
function larijani_core_normalize_schema( $form_name, $fields ) {
	$out   = array();
	$types = larijani_core_field_types();
	foreach ( (array) $fields as $key => $f ) {
		$key = sanitize_key( $key );
		if ( '' === $key || ! is_array( $f ) ) {
			continue;
		}
		$type = isset( $f['type'] ) && in_array( $f['type'], $types, true ) ? $f['type'] : 'text';
		$row  = array(
			'l' => mb_substr( wp_strip_all_tags( isset( $f['label'] ) ? (string) $f['label'] : $key ), 0, 120 ),
			't' => $type,
			'r' => ! empty( $f['required'] ) ? 1 : 0,
		);
		if ( in_array( $type, array( 'select', 'checkboxes' ), true ) && ! empty( $f['options'] ) ) {
			$row['o'] = array_values( array_map( 'sanitize_text_field', array_map( 'strval', (array) $f['options'] ) ) );
		}
		$out[ $key ] = $row;
	}
	return array(
		'v' => 1,
		'n' => mb_substr( sanitize_text_field( $form_name ), 0, 120 ),
		'f' => $out,
	);
}

/**
 * Hidden inputs for a theme form (public API used by the theme).
 *
 * @param string $form_name Human readable form name.
 * @param array  $fields Field schema, see larijani_core_normalize_schema().
 * @return string
 */
function larijani_core_form_fields( $form_name, $fields ) {
	$schema = larijani_core_normalize_schema( $form_name, $fields );
	return sprintf(
		'<input type="hidden" name="action" value="larijani_lead"><input type="hidden" name="ls_form_name" value="%1$s"><input type="hidden" name="ls_schema" value="%2$s"><input type="hidden" name="ls_token" value="" data-ls-token><div aria-hidden="true" style="position:absolute;width:1px;height:1px;overflow:hidden;clip-path:inset(50%%);white-space:nowrap;opacity:0;pointer-events:none"><label>%3$s<input type="text" name="ls_hp" tabindex="-1" autocomplete="off"></label></div>',
		esc_attr( $schema['n'] ),
		esc_attr( larijani_core_sign( $schema, 'schema' ) ),
		esc_html__( 'این فیلد را خالی بگذارید', 'larijani-stone-core' )
	);
}

/**
 * Client IP. Behind a trusted reverse proxy hook "larijani_core_client_ip".
 *
 * @return string
 */
function larijani_core_client_ip() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$ip = (string) apply_filters( 'larijani_core_client_ip', $ip );
	return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '0.0.0.0';
}

/**
 * Fixed-window counter.
 *
 * @param string $bucket Bucket name.
 * @param array  $rule array( limit, seconds ).
 * @param bool   $increment Count this hit.
 * @return bool True when the limit is already reached.
 */
function larijani_core_limited( $bucket, $rule, $increment = true ) {
	$key  = 'larijani_rl_' . md5( $bucket );
	$data = get_transient( $key );
	$now  = time();
	if ( ! is_array( $data ) || empty( $data['e'] ) || $data['e'] <= $now ) {
		$data = array(
			'c' => 0,
			'e' => $now + (int) $rule[1],
		);
	}
	if ( $data['c'] >= (int) $rule[0] ) {
		return true;
	}
	if ( $increment ) {
		$data['c']++;
		set_transient( $key, $data, max( 1, $data['e'] - $now ) );
	}
	return false;
}

/**
 * Convert Persian/Arabic digits to Latin.
 *
 * @param string $value Value.
 * @return string
 */
function larijani_core_latin_digits( $value ) {
	return strtr(
		(string) $value,
		array(
			'۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9', // phpcs:ignore WordPress.Arrays.ArrayDeclarationSpacing.ArrayItemNoNewLine
			'٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9', // phpcs:ignore WordPress.Arrays.ArrayDeclarationSpacing.ArrayItemNoNewLine
		)
	);
}

/**
 * Validate and normalise a phone number (Iranian mobile/landline or international).
 *
 * @param string $value Raw value.
 * @return string|false Normalised number or false.
 */
function larijani_core_normalize_phone( $value ) {
	$v = preg_replace( '/[\s\-\.\(\)]/', '', larijani_core_latin_digits( $value ) );
	if ( preg_match( '/^(?:\+98|0098|98)?0?(9\d{9})$/', $v, $m ) ) {
		$out = '0' . $m[1];
	} elseif ( preg_match( '/^0[1-8]\d{9}$/', $v ) ) {
		$out = $v;
	} elseif ( preg_match( '/^(?:\+|00)([1-9]\d{7,14})$/', $v, $m ) ) {
		$out = '+' . $m[1];
	} else {
		$out = false;
	}
	return apply_filters( 'larijani_core_normalize_phone', $out, $value );
}

/**
 * Random code in the tracking format (unambiguous alphabet, CSPRNG).
 *
 * @return string
 */
function larijani_core_random_code() {
	$alphabet = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
	$code     = '';
	for ( $i = 0; $i < 6; $i++ ) {
		$code .= $alphabet[ random_int( 0, strlen( $alphabet ) - 1 ) ];
	}
	return 'LS-' . gmdate( 'ymd' ) . '-' . $code;
}

/**
 * Unique, unguessable tracking code: LS-YYMMDD-XXXXXX.
 *
 * @return string
 */
function larijani_core_tracking_code() {
	for ( $attempt = 0; $attempt < 5; $attempt++ ) {
		$code = larijani_core_random_code();
		$used = get_posts(
			array(
				'post_type'      => 'ls_lead',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => '_ls_lead_tracking', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => $code, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);
		if ( ! $used ) {
			return $code;
		}
	}
	return 'LS-' . gmdate( 'ymd' ) . '-' . strtoupper( wp_generate_password( 10, false, false ) );
}

/**
 * Error messages shown to visitors (never contain internals).
 *
 * @param string $code Code.
 * @return string
 */
function larijani_core_error_message( $code ) {
	$messages = array(
		'expired'        => __( 'اعتبار فرم به پایان رسیده است. لطفاً صفحه را تازه کنید و دوباره ارسال کنید.', 'larijani-stone-core' ),
		'too_fast'       => __( 'ارسال بسیار سریع بود؛ لطفاً چند لحظه صبر کنید و دوباره ارسال کنید.', 'larijani-stone-core' ),
		'rate'           => __( 'تعداد درخواست‌ها زیاد است. لطفاً کمی بعد تلاش کنید یا تماس بگیرید.', 'larijani-stone-core' ),
		'invalid'        => __( 'فرم معتبر نیست. لطفاً صفحه را تازه کنید.', 'larijani-stone-core' ),
		'empty'          => __( 'لطفاً فرم را تکمیل کنید.', 'larijani-stone-core' ),
		'server'         => __( 'ثبت درخواست در حال حاضر ممکن نشد. لطفاً دوباره تلاش کنید یا تماس بگیرید.', 'larijani-stone-core' ),
		'too_many_files' => __( 'تعداد فایل‌ها بیش از حد مجاز است.', 'larijani-stone-core' ),
		'file_too_large' => __( 'حجم فایل بیش از حد مجاز است.', 'larijani-stone-core' ),
		'bad_type'       => __( 'نوع فایل مجاز نیست. فرمت‌های مجاز: JPG، PNG، WEBP، PDF، DWG و ZIP.', 'larijani-stone-core' ),
		'bad_content'    => __( 'محتوای فایل با پسوند آن مطابقت ندارد یا فایل معیوب است.', 'larijani-stone-core' ),
		'zip_rejected'   => __( 'فایل ZIP شامل موارد غیرمجاز است یا بیش از حد بزرگ است.', 'larijani-stone-core' ),
		'zip_unsupported' => __( 'ارسال فایل ZIP روی این سرور پشتیبانی نمی‌شود؛ لطفاً فایل‌ها را جداگانه ارسال کنید.', 'larijani-stone-core' ),
		'upload_failed'  => __( 'بارگذاری فایل ناموفق بود.', 'larijani-stone-core' ),
	);
	return isset( $messages[ $code ] ) ? $messages[ $code ] : $messages['server'];
}

/**
 * Send a JSON error and stop.
 *
 * @param string $code Code.
 * @param int    $status HTTP status.
 * @param array  $extra Extra data.
 */
function larijani_core_fail( $code, $status = 400, $extra = array() ) {
	wp_send_json_error( array_merge( array( 'code' => $code, 'message' => larijani_core_error_message( $code ) ), $extra ), $status );
}

/**
 * AJAX: issue a signed single-use submission token.
 */
function larijani_core_issue_token() {
	$settings = larijani_core_form_settings();
	if ( larijani_core_limited( 'tok|' . larijani_core_client_ip(), $settings['token_requests'] ) ) {
		larijani_core_fail( 'rate', 429 );
	}
	nocache_headers();
	wp_send_json_success(
		array(
			'token' => larijani_core_sign(
				array(
					'i' => time(),
					'n' => wp_generate_password( 20, false, false ),
				),
				'token'
			),
			'wait'  => (int) $settings['min_seconds'],
		)
	);
}
add_action( 'wp_ajax_larijani_form_token', 'larijani_core_issue_token' );
add_action( 'wp_ajax_nopriv_larijani_form_token', 'larijani_core_issue_token' );

/**
 * Atomically claim a token nonce (prevents replay and double submission).
 *
 * @param string $nonce Token nonce.
 * @return bool
 */
function larijani_core_claim_token( $nonce ) {
	global $wpdb;
	$name = 'larijani_tok_' . md5( $nonce );
	$rows = $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$wpdb->options} (option_name, option_value, autoload) VALUES (%s, %s, 'no')", $name, (string) time() ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	return 1 === (int) $rows;
}

/**
 * Release a claimed token (the lead could not be stored).
 *
 * @param string $nonce Token nonce.
 */
function larijani_core_release_token( $nonce ) {
	global $wpdb;
	$wpdb->delete( $wpdb->options, array( 'option_name' => 'larijani_tok_' . md5( $nonce ) ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
}

/**
 * Daily cleanup of expired token claims.
 */
function larijani_core_cleanup_tokens() {
	global $wpdb;
	$limit = time() - (int) larijani_core_form_settings()['token_ttl'] - HOUR_IN_SECONDS;
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s AND CAST(option_value AS UNSIGNED) < %d", $wpdb->esc_like( 'larijani_tok_' ) . '%', $limit ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
}
add_action( 'larijani_core_cleanup', 'larijani_core_cleanup_tokens' );

/**
 * Schedule cleanup.
 */
function larijani_core_schedule_cleanup() {
	if ( ! wp_next_scheduled( 'larijani_core_cleanup' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'larijani_core_cleanup' );
	}
}
add_action( 'init', 'larijani_core_schedule_cleanup' );

/**
 * Validate submitted values against the signed schema.
 *
 * @param array $schema Verified schema.
 * @param array $raw Raw submitted values.
 * @param bool  $has_files Whether files were uploaded.
 * @return array|WP_Error array( rows, phone, email, name, has_file_field )
 */
function larijani_core_validate_fields( $schema, $raw, $has_files ) {
	$settings = larijani_core_form_settings();
	$rows     = array();
	$phone    = '';
	$email    = '';
	$name     = '';
	$filled   = false;
	$file_ok  = false;
	foreach ( array_slice( $schema['f'], 0, (int) $settings['max_fields'], true ) as $key => $f ) {
		$type  = $f['t'];
		$label = $f['l'];
		$value = isset( $raw[ $key ] ) ? $raw[ $key ] : '';
		if ( 'file' === $type ) {
			$file_ok = true;
			if ( $f['r'] && ! $has_files ) {
				return new WP_Error( 'required', $label, $key );
			}
			continue;
		}
		if ( 'checkboxes' === $type ) {
			$value = array_values( array_filter( array_map( 'sanitize_text_field', array_map( 'strval', is_array( $value ) ? $value : array( $value ) ) ), 'strlen' ) );
			if ( isset( $f['o'] ) && array_diff( $value, $f['o'] ) ) {
				return new WP_Error( 'option', $label, $key );
			}
			$value = implode( '، ', $value );
		} elseif ( is_array( $value ) ) {
			return new WP_Error( 'invalid_value', $label, $key );
		} elseif ( 'textarea' === $type ) {
			$value = sanitize_textarea_field( (string) $value );
			if ( mb_strlen( $value ) > $settings['max_textarea'] ) {
				return new WP_Error( 'too_long', $label, $key );
			}
		} else {
			$value = sanitize_text_field( (string) $value );
			if ( mb_strlen( $value ) > $settings['max_text_length'] ) {
				return new WP_Error( 'too_long', $label, $key );
			}
		}

		if ( '' === $value ) {
			if ( $f['r'] ) {
				return new WP_Error( 'required', $label, $key );
			}
			$rows[] = array( 'label' => $label, 'value' => '' );
			continue;
		}

		switch ( $type ) {
			case 'tel':
				$norm = larijani_core_normalize_phone( $value );
				if ( ! $norm ) {
					return new WP_Error( 'phone', $label, $key );
				}
				$value = $norm;
				$phone = $phone ? $phone : $norm;
				break;
			case 'email':
				if ( ! is_email( $value ) ) {
					return new WP_Error( 'email', $label, $key );
				}
				$value = sanitize_email( $value );
				$email = $email ? $email : $value;
				break;
			case 'contact':
				$norm = larijani_core_normalize_phone( $value );
				if ( $norm ) {
					$value = $norm;
					$phone = $phone ? $phone : $norm;
				} elseif ( is_email( $value ) ) {
					$value = sanitize_email( $value );
					$email = $email ? $email : $value;
				} else {
					return new WP_Error( 'contact', $label, $key );
				}
				break;
			case 'number':
				$value = larijani_core_latin_digits( $value );
				if ( ! is_numeric( $value ) ) {
					return new WP_Error( 'number', $label, $key );
				}
				break;
			case 'select':
				if ( isset( $f['o'] ) && ! in_array( $value, $f['o'], true ) ) {
					return new WP_Error( 'option', $label, $key );
				}
				break;
			case 'text':
				$name = $name ? $name : $value;
				break;
		}
		$filled = true;
		$rows[] = array( 'label' => $label, 'value' => $value );
	}
	if ( ! $filled && ! $has_files ) {
		return new WP_Error( 'empty', '' );
	}
	if ( $has_files && ! $file_ok ) {
		return new WP_Error( 'invalid_files', '' ); // The form never offered an upload.
	}
	return compact( 'rows', 'phone', 'email', 'name' );
}

/**
 * Human message for a field error.
 *
 * @param WP_Error $error Error.
 * @return string
 */
function larijani_core_field_error_message( $error ) {
	$label = $error->get_error_message();
	switch ( $error->get_error_code() ) {
		case 'required':
			/* translators: %s: field label */
			return sprintf( __( 'لطفاً «%s» را تکمیل کنید.', 'larijani-stone-core' ), $label );
		case 'phone':
			/* translators: %s: field label */
			return sprintf( __( '«%s» معتبر نیست. نمونه صحیح: ۰۹۱۲۳۴۵۶۷۸۹', 'larijani-stone-core' ), $label );
		case 'email':
			/* translators: %s: field label */
			return sprintf( __( '«%s» یک نشانی ایمیل معتبر نیست.', 'larijani-stone-core' ), $label );
		case 'contact':
			/* translators: %s: field label */
			return sprintf( __( 'لطفاً در «%s» یک شماره تماس یا ایمیل معتبر وارد کنید.', 'larijani-stone-core' ), $label );
		case 'number':
			/* translators: %s: field label */
			return sprintf( __( '«%s» باید عدد باشد.', 'larijani-stone-core' ), $label );
		case 'too_long':
			/* translators: %s: field label */
			return sprintf( __( 'متن «%s» بیش از حد طولانی است.', 'larijani-stone-core' ), $label );
		case 'option':
		case 'invalid_value':
			/* translators: %s: field label */
			return sprintf( __( 'مقدار انتخاب‌شده برای «%s» معتبر نیست.', 'larijani-stone-core' ), $label );
		case 'empty':
			return larijani_core_error_message( 'empty' );
	}
	return larijani_core_error_message( 'invalid' );
}

/**
 * AJAX: handle a submission (new action and the 1.3.x "ls_lead" action).
 */
function larijani_core_handle_lead() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- public form; protected by a signed single-use token, see file header.
	if ( ! isset( $_SERVER['REQUEST_METHOD'] ) || 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
		larijani_core_fail( 'invalid', 405 );
	}
	nocache_headers();
	$settings = larijani_core_form_settings();
	$ip       = larijani_core_client_ip();

	if ( ! empty( $_POST['ls_hp'] ) ) {
		// Bots get a plausible answer and nothing is stored.
		wp_send_json_success( array( 'tracking' => larijani_core_random_code() ) );
	}
	if ( larijani_core_limited( 'try|' . $ip, $settings['ip_attempts'] ) ) {
		larijani_core_fail( 'rate', 429 );
	}

	$token = larijani_core_verify( isset( $_POST['ls_token'] ) ? sanitize_text_field( wp_unslash( $_POST['ls_token'] ) ) : '', 'token' );
	if ( ! $token || empty( $token['i'] ) || empty( $token['n'] ) ) {
		larijani_core_fail( 'expired', 403 );
	}
	$age = time() - (int) $token['i'];
	if ( $age > (int) $settings['token_ttl'] || $age < -60 ) {
		larijani_core_fail( 'expired', 403 );
	}
	if ( $age < (int) $settings['min_seconds'] ) {
		larijani_core_fail( 'too_fast', 425, array( 'retry' => (int) $settings['min_seconds'] - $age ) );
	}

	$schema = larijani_core_verify( isset( $_POST['ls_schema'] ) ? sanitize_text_field( wp_unslash( $_POST['ls_schema'] ) ) : '', 'schema' );
	if ( ! $schema || empty( $schema['f'] ) || ! is_array( $schema['f'] ) ) {
		larijani_core_fail( 'invalid', 400 );
	}

	$raw   = isset( $_POST['fields'] ) && is_array( $_POST['fields'] ) ? wp_unslash( $_POST['fields'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- validated per field against the schema.
	$page  = isset( $_POST['ls_page'] ) ? esc_url_raw( wp_unslash( $_POST['ls_page'] ) ) : '';
	$files = isset( $_FILES['ls_files'] ) ? larijani_core_normalize_files( $_FILES['ls_files'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- validated in larijani_core_validate_uploads().
	// phpcs:enable

	if ( $page && wp_parse_url( $page, PHP_URL_HOST ) !== wp_parse_url( home_url(), PHP_URL_HOST ) ) {
		$page = '';
	}

	$data = larijani_core_validate_fields( $schema, $raw, (bool) $files );
	if ( is_wp_error( $data ) ) {
		$code = $data->get_error_code();
		wp_send_json_error(
			array(
				'code'    => 'invalid_files' === $code ? 'invalid' : 'field',
				'field'   => (string) $data->get_error_data(),
				'message' => 'invalid_files' === $code ? larijani_core_error_message( 'invalid' ) : larijani_core_field_error_message( $data ),
			),
			400
		);
	}
	if ( $files ) {
		$check = larijani_core_validate_uploads( $files );
		if ( true !== $check ) {
			larijani_core_fail( $check, 400 );
		}
	}

	$form_name = $schema['n'] ? $schema['n'] : __( 'فرم تماس', 'larijani-stone-core' );
	$dup_key   = 'larijani_dup_' . md5( $form_name . '|' . $ip . '|' . wp_json_encode( $data['rows'] ) . '|' . count( $files ) );
	$previous  = get_transient( $dup_key );
	if ( $previous ) {
		wp_send_json_success( array( 'tracking' => $previous, 'duplicate' => true ) );
	}

	if ( larijani_core_limited( 'ok|' . $ip, $settings['ip_success'], false )
		|| larijani_core_limited( 'day|' . $ip, $settings['ip_daily'], false )
		|| ( $data['phone'] && larijani_core_limited( 'tel|' . $data['phone'], $settings['phone_hourly'], false ) )
		|| larijani_core_limited( 'all', $settings['global_hourly'], false ) ) {
		larijani_core_fail( 'rate', 429 );
	}

	$spam = apply_filters( 'larijani_core_is_spam', false, $data, $schema );
	if ( $spam ) {
		wp_send_json_success( array( 'tracking' => larijani_core_random_code() ) );
	}

	if ( ! larijani_core_claim_token( $token['n'] ) ) {
		larijani_core_fail( 'expired', 409 );
	}

	$tracking = larijani_core_tracking_code();
	$who      = $data['name'] ? $data['name'] : ( $data['phone'] ? $data['phone'] : $data['email'] );
	$title    = trim( $form_name . ( $who ? ' – ' . $who : '' ) );

	$lead_id = wp_insert_post(
		array(
			'post_type'   => 'ls_lead',
			'post_status' => 'private',
			'post_title'  => $title,
		),
		true
	);
	if ( is_wp_error( $lead_id ) || ! $lead_id ) {
		larijani_core_release_token( $token['n'] );
		larijani_core_fail( 'server', 500 );
	}

	$stored = array();
	if ( $files ) {
		$stored = larijani_core_store_uploads( $files, $lead_id );
		if ( false === $stored ) {
			wp_delete_post( $lead_id, true ); // Never keep a half-saved lead.
			larijani_core_release_token( $token['n'] );
			larijani_core_fail( 'upload_failed', 500 );
		}
	}

	update_post_meta( $lead_id, '_ls_lead_fields', $data['rows'] );
	update_post_meta( $lead_id, '_ls_lead_form', $form_name );
	update_post_meta( $lead_id, '_ls_lead_page', $page );
	update_post_meta( $lead_id, '_ls_lead_phone', $data['phone'] );
	update_post_meta( $lead_id, '_ls_lead_email', $data['email'] );
	update_post_meta( $lead_id, '_ls_lead_tracking', $tracking );
	update_post_meta( $lead_id, '_ls_lead_ip_hash', hash_hmac( 'sha256', $ip, larijani_core_secret() ) );
	if ( $stored ) {
		update_post_meta( $lead_id, '_ls_lead_private_files', $stored );
	}

	larijani_core_limited( 'ok|' . $ip, $settings['ip_success'] );
	larijani_core_limited( 'day|' . $ip, $settings['ip_daily'] );
	if ( $data['phone'] ) {
		larijani_core_limited( 'tel|' . $data['phone'], $settings['phone_hourly'] );
	}
	larijani_core_limited( 'all', $settings['global_hourly'] );
	set_transient( $dup_key, $tracking, (int) $settings['duplicate_window'] );

	larijani_core_send_lead_email( $lead_id, $title, $data, $tracking, $page, $stored );

	/** Backward-compatible hook from theme 1.3.x. */
	do_action( 'ls_lead_submitted', $lead_id, $data['rows'], $form_name );
	do_action( 'larijani_core_lead_submitted', $lead_id, $data, $form_name, $tracking );

	wp_send_json_success( array( 'tracking' => $tracking ) );
}
add_action( 'wp_ajax_larijani_lead', 'larijani_core_handle_lead' );
add_action( 'wp_ajax_nopriv_larijani_lead', 'larijani_core_handle_lead' );
// Pages cached before the update still post "ls_lead"; they have no token and get a "refresh" message.
add_action( 'wp_ajax_ls_lead', 'larijani_core_handle_lead' );
add_action( 'wp_ajax_nopriv_ls_lead', 'larijani_core_handle_lead' );

/**
 * Notify the owner. Files are never linked publicly: the mail points to the dashboard.
 *
 * @param int    $lead_id Lead id.
 * @param string $title Title.
 * @param array  $data Validated data.
 * @param string $tracking Tracking code.
 * @param string $page Page URL.
 * @param array  $stored Stored file records.
 */
function larijani_core_send_lead_email( $lead_id, $title, $data, $tracking, $page, $stored ) {
	$to = get_option( 'larijani_core_leads_email' );
	if ( ! $to && function_exists( 'larijani_opt' ) ) {
		$to = larijani_opt( 'leads_email' );
	}
	if ( ! $to || ! is_email( $to ) ) {
		$to = get_option( 'admin_email' );
	}
	$to = apply_filters( 'larijani_core_lead_email_to', $to, $lead_id );
	if ( ! $to ) {
		return;
	}
	$html  = '<div dir="rtl" style="font-family:Tahoma,sans-serif;font-size:14px;line-height:1.9">';
	$html .= '<h2 style="margin:0 0 12px">' . esc_html( $title ) . '</h2>';
	$html .= '<table cellpadding="8" style="border-collapse:collapse;width:100%">';
	foreach ( $data['rows'] as $row ) {
		$html .= '<tr><th style="text-align:right;background:#F8F9F7;border:1px solid #E8ECE6;width:35%">' . esc_html( $row['label'] ) . '</th><td style="border:1px solid #E8ECE6">' . nl2br( esc_html( $row['value'] ) ) . '</td></tr>';
	}
	$html .= '</table>';
	$html .= '<p>' . esc_html__( 'کد پیگیری:', 'larijani-stone-core' ) . ' <b>' . esc_html( $tracking ) . '</b></p>';
	if ( $page ) {
		$html .= '<p>' . esc_html__( 'صفحه:', 'larijani-stone-core' ) . ' <a href="' . esc_url( $page ) . '">' . esc_html( $page ) . '</a></p>';
	}
	if ( $stored ) {
		/* translators: %d: number of files */
		$html .= '<p>' . esc_html( sprintf( _n( '%d فایل پیوست به‌صورت خصوصی ذخیره شد و فقط از پیشخوان قابل دریافت است.', '%d فایل پیوست به‌صورت خصوصی ذخیره شد و فقط از پیشخوان قابل دریافت است.', count( $stored ), 'larijani-stone-core' ), count( $stored ) ) ) . '</p>';
	}
	$html .= '<p><a href="' . esc_url( admin_url( 'post.php?post=' . (int) $lead_id . '&action=edit' ) ) . '">' . esc_html__( 'مشاهده در پیشخوان', 'larijani-stone-core' ) . '</a></p></div>';

	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	if ( $data['email'] && is_email( $data['email'] ) ) {
		$headers[] = 'Reply-To: ' . $data['email'];
	}
	wp_mail( $to, '[' . wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) . '] ' . $title, $html, $headers );
}
