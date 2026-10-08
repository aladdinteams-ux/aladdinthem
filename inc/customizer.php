<?php
/**
 * Customizer: brand colours, contact info, header, footer, blog and forms.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Option field definitions shared by the Customizer and the dashboard
 * settings page (Appearance-independent: «لاریجانی استون › تنظیمات»).
 *
 * Each row: [ section, theme_mod key, label, type, (optional) choices ].
 *
 * @return array
 */
function larijani_option_fields() {
	return apply_filters(
		'ls_option_fields',
		array(
			// section, key, label, type.
			array( 'ls_brand', 'ls_brand_name', __( 'نام برند', 'larijani-stone' ), 'text' ),
			array( 'ls_brand', 'ls_brand_tagline', __( 'شعار کوتاه (هدر)', 'larijani-stone' ), 'text' ),
			array( 'ls_brand', 'ls_brand_tagline_alt', __( 'شعار فوتر', 'larijani-stone' ), 'text' ),
			array( 'ls_brand', 'ls_force_rtl', __( 'راست‌چین اجباری (حتی بدون بسته زبان فارسی)', 'larijani-stone' ), 'checkbox' ),
			array( 'ls_brand', 'ls_color_primary', __( 'رنگ اصلی (سبز سنگی)', 'larijani-stone' ), 'color' ),
			array( 'ls_brand', 'ls_color_primary_hover', __( 'رنگ اصلی – حالت هاور', 'larijani-stone' ), 'color' ),
			array( 'ls_brand', 'ls_color_secondary', __( 'رنگ ثانویه (سبز روشن)', 'larijani-stone' ), 'color' ),
			array( 'ls_brand', 'ls_color_dark', __( 'رنگ تیره (بازالت)', 'larijani-stone' ), 'color' ),
			array( 'ls_brand', 'ls_color_footer', __( 'پس‌زمینه فوتر', 'larijani-stone' ), 'color' ),
			array( 'ls_brand', 'ls_color_canvas', __( 'پس‌زمینه سایت (کرم سنگی)', 'larijani-stone' ), 'color' ),
			array( 'ls_brand', 'ls_color_border', __( 'رنگ خطوط و حاشیه‌ها', 'larijani-stone' ), 'color' ),
			array( 'ls_brand', 'ls_color_text', __( 'رنگ متن', 'larijani-stone' ), 'color' ),

			array( 'ls_contact', 'ls_phone_1', __( 'تلفن اول', 'larijani-stone' ), 'text' ),
			array( 'ls_contact', 'ls_phone_1_label', __( 'عنوان تلفن اول', 'larijani-stone' ), 'text' ),
			array( 'ls_contact', 'ls_phone_2', __( 'تلفن دوم', 'larijani-stone' ), 'text' ),
			array( 'ls_contact', 'ls_phone_2_label', __( 'عنوان تلفن دوم', 'larijani-stone' ), 'text' ),
			array( 'ls_contact', 'ls_whatsapp', __( 'شماره واتساپ (با کد کشور، مثل 98912...)', 'larijani-stone' ), 'text' ),
			array( 'ls_contact', 'ls_email', __( 'ایمیل', 'larijani-stone' ), 'email' ),
			array( 'ls_contact', 'ls_address', __( 'آدرس کامل', 'larijani-stone' ), 'textarea' ),
			array( 'ls_contact', 'ls_address_short', __( 'آدرس کوتاه (نوار بالا)', 'larijani-stone' ), 'text' ),
			array( 'ls_contact', 'ls_hours', __( 'ساعات کاری (کوتاه)', 'larijani-stone' ), 'text' ),
			array( 'ls_contact', 'ls_hours_full', __( 'ساعات کاری (کامل، فوتر)', 'larijani-stone' ), 'text' ),
			array( 'ls_contact', 'ls_instagram', __( 'اینستاگرام (لینک)', 'larijani-stone' ), 'url' ),
			array( 'ls_contact', 'ls_telegram', __( 'تلگرام (لینک)', 'larijani-stone' ), 'url' ),
			array( 'ls_contact', 'ls_eitaa', __( 'ایتا (لینک)', 'larijani-stone' ), 'url' ),
			array( 'ls_contact', 'ls_aparat', __( 'آپارات (لینک)', 'larijani-stone' ), 'url' ),
			array( 'ls_contact', 'ls_linkedin', __( 'لینکدین (لینک)', 'larijani-stone' ), 'url' ),

			array(
				'ls_header',
				'ls_header_style',
				__( 'طرح هدر', 'larijani-stone' ),
				'select',
				array(
					'dark'  => __( 'نوار بالای تیره (اکثر صفحات طرح)', 'larijani-stone' ),
					'light' => __( 'نوار بالای روشن + شبکه‌های اجتماعی (صفحه اصلی کلاسیک)', 'larijani-stone' ),
				),
			),
			array( 'ls_header', 'ls_header_topbar', __( 'نمایش نوار اطلاعات بالای هدر', 'larijani-stone' ), 'checkbox' ),
			array( 'ls_header', 'ls_header_sticky', __( 'هدر چسبان', 'larijani-stone' ), 'checkbox' ),
			array( 'ls_header', 'ls_header_search', __( 'دکمه جستجو', 'larijani-stone' ), 'checkbox' ),
			array( 'ls_header', 'ls_header_cta_text', __( 'متن دکمه اصلی', 'larijani-stone' ), 'text' ),
			array( 'ls_header', 'ls_header_cta_short', __( 'متن کوتاه دکمه (موبایل)', 'larijani-stone' ), 'text' ),
			array( 'ls_header', 'ls_header_cta_link', __( 'لینک دکمه اصلی (خالی = تماس تلفنی)', 'larijani-stone' ), 'url' ),
			array( 'ls_header', 'ls_header_whatsapp_label', __( 'متن لینک واتساپ نوار بالا', 'larijani-stone' ), 'text' ),

			array( 'ls_footer', 'ls_footer_about', __( 'متن درباره ما', 'larijani-stone' ), 'textarea' ),
			array( 'ls_footer', 'ls_footer_col1_title', __( 'عنوان ستون اول منو', 'larijani-stone' ), 'text' ),
			array( 'ls_footer', 'ls_footer_col2_title', __( 'عنوان ستون دوم منو', 'larijani-stone' ), 'text' ),
			array( 'ls_footer', 'ls_footer_col3_title', __( 'عنوان ستون تماس', 'larijani-stone' ), 'text' ),
			array( 'ls_footer', 'ls_footer_copyright', __( 'متن کپی‌رایت', 'larijani-stone' ), 'text' ),
			array( 'ls_footer', 'ls_footer_bg_image', __( 'تصویر پس‌زمینه فوتر', 'larijani-stone' ), 'image' ),
			array( 'ls_footer', 'ls_footer_enamad', __( 'کد نماد اعتماد / ساماندهی (HTML)', 'larijani-stone' ), 'textarea' ),

			array( 'ls_blog', 'ls_blog_sidebar', __( 'نمایش سایدبار در وبلاگ', 'larijani-stone' ), 'checkbox' ),
			array( 'ls_blog', 'ls_jalali_dates', __( 'نمایش تاریخ‌ها به شمسی (هجری خورشیدی)', 'larijani-stone' ), 'checkbox' ),
			array( 'ls_blog', 'ls_blog_show_views', __( 'نمایش تعداد بازدید', 'larijani-stone' ), 'checkbox' ),

			array( 'ls_forms', 'ls_leads_email', __( 'ایمیل دریافت درخواست‌ها (خالی = ایمیل مدیر)', 'larijani-stone' ), 'email' ),

			array(
				'ls_style',
				'ls_font_family',
				__( 'فونت سایت', 'larijani-stone' ),
				'select',
				array(
					'vazirmatn' => __( 'وزیرمتن (همراه قالب، طرح اصلی)', 'larijani-stone' ),
					'system'    => __( 'فونت سیستم (Tahoma / Segoe UI؛ بدون دانلود فونت)', 'larijani-stone' ),
					'custom'    => __( 'فونت دلخواه (فایل WOFF2 خودتان)', 'larijani-stone' ),
				),
			),
			array( 'ls_style', 'ls_font_custom_url', __( 'نشانی فایل فونت دلخواه (WOFF2 یا WOFF، بارگذاری‌شده در همین سایت)', 'larijani-stone' ), 'url' ),
			array( 'ls_style', 'ls_font_scale_heading', __( 'اندازه تیترها', 'larijani-stone' ), 'select', array(
					'85'  => '۸۵٪',
					'90'  => '۹۰٪',
					'95'  => '۹۵٪',
					'100' => __( '۱۰۰٪ (طرح اصلی)', 'larijani-stone' ),
					'105' => '۱۰۵٪',
					'110' => '۱۱۰٪',
					'120' => '۱۲۰٪',
				) ),
			array( 'ls_style', 'ls_font_scale_body', __( 'اندازه متن‌ها', 'larijani-stone' ), 'select', array(
					'85'  => '۸۵٪',
					'90'  => '۹۰٪',
					'95'  => '۹۵٪',
					'100' => __( '۱۰۰٪ (طرح اصلی)', 'larijani-stone' ),
					'105' => '۱۰۵٪',
					'110' => '۱۱۰٪',
					'120' => '۱۲۰٪',
				) ),
			array( 'ls_style', 'ls_font_scale_tablet', __( 'ضریب اندازه فونت در تبلت (۷۶۸ تا ۱۰۲۳ پیکسل)', 'larijani-stone' ), 'select', array(
					'85'  => '۸۵٪',
					'90'  => '۹۰٪',
					'95'  => '۹۵٪',
					'100' => __( '۱۰۰٪ (طرح اصلی)', 'larijani-stone' ),
					'105' => '۱۰۵٪',
					'110' => '۱۱۰٪',
					'120' => '۱۲۰٪',
				) ),
			array( 'ls_style', 'ls_font_scale_mobile', __( 'ضریب اندازه فونت در موبایل (کمتر از ۷۶۸ پیکسل)', 'larijani-stone' ), 'select', array(
					'85'  => '۸۵٪',
					'90'  => '۹۰٪',
					'95'  => '۹۵٪',
					'100' => __( '۱۰۰٪ (طرح اصلی)', 'larijani-stone' ),
					'105' => '۱۰۵٪',
					'110' => '۱۱۰٪',
					'120' => '۱۲۰٪',
				) ),
			array(
				'ls_style',
				'ls_container_width',
				__( 'عرض محتوای صفحات قالب', 'larijani-stone' ),
				'select',
				array(
					'1140' => '۱۱۴۰px',
					'1200' => '۱۲۰۰px',
					'1280' => __( '۱۲۸۰px (طرح اصلی)', 'larijani-stone' ),
					'1360' => '۱۳۶۰px',
					'1440' => '۱۴۴۰px',
				),
			),
			array(
				'ls_style',
				'ls_button_radius',
				__( 'گردی گوشه دکمه‌ها', 'larijani-stone' ),
				'select',
				array(
					''     => __( 'طرح اصلی', 'larijani-stone' ),
					'0'    => __( 'بدون گردی', 'larijani-stone' ),
					'6'    => __( 'کم (۶px)', 'larijani-stone' ),
					'12'   => __( 'متوسط (۱۲px)', 'larijani-stone' ),
					'9999' => __( 'کپسولی', 'larijani-stone' ),
				),
			),
		)
	);
}

/**
 * Register Customizer settings.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function larijani_customize_register( $wp_customize ) {
	$d = larijani_option_defaults();

	$wp_customize->add_panel(
		'ls_panel',
		array(
			'title'    => __( 'تنظیمات قالب لاریجانی', 'larijani-stone' ),
			'priority' => 25,
		)
	);

	$sections = array(
		'ls_brand'   => __( 'برند و رنگ‌ها', 'larijani-stone' ),
		'ls_contact' => __( 'اطلاعات تماس و شبکه‌های اجتماعی', 'larijani-stone' ),
		'ls_header'  => __( 'هدر', 'larijani-stone' ),
		'ls_footer'  => __( 'فوتر', 'larijani-stone' ),
		'ls_blog'    => __( 'وبلاگ', 'larijani-stone' ),
		'ls_forms'   => __( 'فرم‌ها و درخواست‌ها', 'larijani-stone' ),
		'ls_style'   => __( 'تایپوگرافی و چیدمان', 'larijani-stone' ),
	);
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section( $id, array( 'title' => $title, 'panel' => 'ls_panel' ) );
	}

	$fields = larijani_option_fields();

	foreach ( $fields as $f ) {
		list( $section, $key, $label, $type ) = $f;
		$choices = $f[4] ?? array();
		$sanitize = 'sanitize_text_field';
		if ( 'color' === $type ) {
			$sanitize = 'sanitize_hex_color';
		} elseif ( 'checkbox' === $type ) {
			$sanitize = 'larijani_sanitize_checkbox';
		} elseif ( 'url' === $type || 'image' === $type ) {
			$sanitize = 'esc_url_raw';
		} elseif ( 'select' === $type ) {
			$sanitize = static function ( $v ) use ( $choices, $d, $key ) {
				return isset( $choices[ $v ] ) ? $v : ( $d[ $key ] ?? '' );
			};
		} elseif ( 'email' === $type ) {
			$sanitize = 'sanitize_email';
		} elseif ( 'textarea' === $type ) {
			$sanitize = 'larijani_sanitize_html';
		}
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => isset( $d[ $key ] ) ? $d[ $key ] : '',
				'sanitize_callback' => $sanitize,
				'transport'         => 'refresh',
			)
		);
		if ( 'color' === $type ) {
			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $key, array( 'label' => $label, 'section' => $section ) ) );
		} elseif ( 'image' === $type ) {
			$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $key, array( 'label' => $label, 'section' => $section ) ) );
		} elseif ( 'select' === $type ) {
			$wp_customize->add_control( $key, array( 'label' => $label, 'section' => $section, 'type' => 'select', 'choices' => $choices ) );
		} else {
			$wp_customize->add_control( $key, array( 'label' => $label, 'section' => $section, 'type' => $type ) );
		}
	}
}
add_action( 'customize_register', 'larijani_customize_register' );

/**
 * Checkbox sanitizer.
 *
 * @param mixed $v Value.
 * @return bool
 */
function larijani_sanitize_checkbox( $v ) {
	return (bool) $v;
}

/**
 * Textarea sanitizer allowing safe HTML (used for footer trust-seal code too).
 *
 * @param string $v Value.
 * @return string
 */
function larijani_sanitize_html( $v ) {
	if ( current_user_can( 'unfiltered_html' ) ) {
		return $v;
	}
	return wp_kses_post( $v );
}

/**
 * Inline CSS variables from the Customizer.
 *
 * @return string
 */
function larijani_customizer_css() {
	$map = array(
		'--ls-c-primary'       => 'color_primary',
		'--ls-c-primary-hover' => 'color_primary_hover',
		'--ls-c-dark'          => 'color_dark',
		'--ls-c-footer'        => 'color_footer',
		'--ls-c-canvas'        => 'color_canvas',
		'--ls-c-border'        => 'color_border',
		'--ls-c-secondary'     => 'color_secondary',
		'--ls-c-text'          => 'color_text',
	);
	$css = '';
	foreach ( $map as $var => $key ) {
		$val = sanitize_hex_color( larijani_opt( $key ) );
		if ( $val ) {
			$css .= $var . ':' . $val . ';';
		}
	}
	// Typography & layout: only values that differ from the original design are printed.
	$font_face = '';
	$family    = larijani_opt( 'font_family' );
	if ( 'system' === $family ) {
		$css .= "--ls-font:Tahoma,'Segoe UI',system-ui,-apple-system,sans-serif;";
	} elseif ( 'custom' === $family && larijani_font_url() ) {
		$font_face = "@font-face{font-family:'LarijaniCustom';src:url('" . esc_url( larijani_font_url() ) . "');font-weight:100 900;font-display:swap}";
		$css      .= "--ls-font:'LarijaniCustom','Vazirmatn',Tahoma,system-ui,sans-serif;";
	}
	foreach ( array( '--ls-fs-h' => 'font_scale_heading', '--ls-fs-b' => 'font_scale_body' ) as $var => $key ) {
		$scale = absint( larijani_opt( $key ) );
		if ( $scale && 100 !== $scale ) {
			$css .= $var . ':' . ( $scale / 100 ) . ';';
		}
	}
	$width = absint( larijani_opt( 'container_width' ) );
	if ( $width && 1280 !== $width ) {
		$css .= '--ls-container:' . $width . 'px;';
	}
	$out = $font_face . ( $css ? ':root{' . $css . '}' : '' );
	foreach ( array( 'font_scale_tablet' => '(min-width:768px) and (max-width:1023.98px)', 'font_scale_mobile' => '(max-width:767.98px)' ) as $key => $media ) {
		$scale = absint( larijani_opt( $key ) );
		if ( $scale && 100 !== $scale ) {
			$out .= '@media ' . $media . '{:root{--ls-fs-r:' . ( $scale / 100 ) . '}}';
		}
	}
	$radius = larijani_opt( 'button_radius' );
	if ( '' !== (string) $radius && in_array( (string) $radius, array( '0', '6', '12', '9999' ), true ) ) {
		// Theme buttons only; Elementor's own Button widget keeps its per-widget Style settings.
		$out .= '.ls-root :is(button[type=submit],a[class~="bg-primary-container"],a[class~="bg-primary"],button[class~="bg-primary-container"]){border-radius:' . absint( $radius ) . 'px}';
	}
	return $out;
}

/**
 * Validated custom font URL (same site, WOFF2/WOFF only).
 *
 * @return string
 */
function larijani_font_url() {
	$url = (string) larijani_opt( 'font_custom_url' );
	if ( ! $url || ! preg_match( '/\.(woff2?)(\?.*)?$/i', wp_parse_url( $url, PHP_URL_PATH ) . '' ) || preg_match( '/[\s\'"()\\\\]/', $url ) ) {
		return '';
	}
	$host = wp_parse_url( $url, PHP_URL_HOST );
	return ( ! $host || wp_parse_url( home_url(), PHP_URL_HOST ) === $host ) ? $url : '';
}
