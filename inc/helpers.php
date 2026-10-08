<?php
/**
 * Generic helpers shared by templates, render functions and Elementor widgets.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default values of every theme option (Customizer). Keys are theme_mod names.
 *
 * @return array
 */
function larijani_option_defaults() {
	return array(
		// Brand.
		'ls_brand_name'        => 'لاریجانی استون',
		'ls_brand_tagline'     => 'تجهیزات و قالب‌های صنعتی بتنی',
		'ls_brand_tagline_alt' => 'مرکز تولید قالب و خطوط صنعتی سنگ',
		'ls_force_rtl'         => true,
		// Colours.
		'ls_color_primary'       => '#5C6754',
		'ls_color_primary_hover' => '#4B5545',
		'ls_color_dark'          => '#161D1A',
		'ls_color_footer'        => '#121715',
		'ls_color_canvas'        => '#F8F9F7',
		'ls_color_border'        => '#E8ECE6',
		'ls_color_secondary'     => '#8DA184',
		'ls_color_text'          => '#2D3748',

		'ls_font_family'         => 'vazirmatn',
		'ls_font_custom_url'     => '',
		'ls_font_scale_heading'  => '100',
		'ls_font_scale_body'     => '100',
		'ls_font_scale_tablet'   => '100',
		'ls_font_scale_mobile'   => '100',
		'ls_container_width'     => '1280',
		'ls_button_radius'       => '',
		'ls_block_remote_images' => false,
		// Contact.
		'ls_phone_1'         => '09122302685',
		'ls_phone_1_label'   => 'مشاوره خط تولید',
		'ls_phone_2'         => '09354431321',
		'ls_phone_2_label'   => 'فروش قالب و متریال',
		'ls_whatsapp'        => '989122302685',
		'ls_email'           => 'info@larijanistone.ir',
		'ls_address'         => 'قزوین، شهرک صنعتی آبیک، مجتمع پیروز، پلاک ۱۵',
		'ls_address_short'   => 'کارخانه و دفتر: قزوین، شهرک صنعتی آبیک، مجتمع پیروز',
		'ls_hours'           => 'شنبه تا چهارشنبه ۸:۰۰ الی ۱۸:۰۰',
		'ls_hours_full'      => 'ساعات کاری کارخانه: شنبه تا چهارشنبه ۸:۰۰ الی ۱۸:۰۰ | پنج‌شنبه‌ها تا ۱۴:۰۰',
		'ls_instagram'       => '',
		'ls_telegram'        => '',
		'ls_eitaa'           => '',
		'ls_aparat'          => '',
		'ls_linkedin'        => '',
		// Header.
		'ls_header_style'       => 'dark',
		'ls_header_topbar'      => true,
		'ls_header_sticky'      => true,
		'ls_header_search'      => true,
		'ls_header_cta_text'    => 'مشاوره و استعلام قیمت',
		'ls_header_cta_short'   => 'استعلام قیمت',
		'ls_header_cta_link'    => '',
		'ls_header_whatsapp_label' => 'پشتیبانی واتساپ',
		// Footer.
		'ls_footer_about'     => 'گروه صنعتی لاریجانی استون مرجع تخصصی طراحی و ساخت قالب‌های نشکن پلیمری، میزهای ویبره دور متغیر، ملات‌سازهای کارگاهی و تأمین‌کننده افزودنی‌های بتن و مواد اولیه در ایران.',
		'ls_footer_bg_image'  => '',
		'ls_footer_col1_title' => 'دسترسی سریع',
		'ls_footer_col2_title' => 'دسته‌بندی تجهیزات',
		'ls_footer_col3_title' => 'ارتباط مستقیم و آدرس',
		'ls_footer_copyright'  => '© ۱۴۰۳ تمامی حقوق مادی و معنوی متعلق به گروه صنعتی لاریجانی استون می‌باشد.',
		'ls_footer_enamad'     => '',
		// Blog.
		'ls_blog_sidebar'      => true,
		'ls_blog_show_views'   => true,
		'ls_jalali_dates'      => true,
		// Forms.
		'ls_leads_email'       => '',
		'ls_leads_sms_note'    => '',
		// Theme builder (free Elementor).
		'ls_tb_header'        => 0,
		'ls_tb_footer'        => 0,
		'ls_tb_single_post'   => 0,
		'ls_tb_archive'       => 0,
		'ls_tb_single_product' => 0,
		'ls_tb_shop'          => 0,
		'ls_tb_404'           => 0,
		'ls_tb_page'          => 0,
		'ls_tb_single_project' => 0,
	);
}

/**
 * Read a theme option with its default.
 *
 * @param string $key Option key (with or without the ls_ prefix).
 * @return mixed
 */
function larijani_opt( $key ) {
	if ( 0 !== strpos( $key, 'ls_' ) ) {
		$key = 'ls_' . $key;
	}
	$defaults = larijani_option_defaults();
	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * Pick the first non-empty value.
 *
 * @param mixed ...$values Candidates.
 * @return mixed
 */
function larijani_first( ...$values ) {
	foreach ( $values as $v ) {
		if ( is_array( $v ) ) {
			if ( ! empty( $v['url'] ) || ! empty( $v['value'] ) || ! empty( $v['id'] ) ) {
				return $v;
			}
			continue;
		}
		if ( null !== $v && '' !== $v && false !== $v ) {
			return $v;
		}
	}
	return '';
}

/**
 * Convert Latin digits to Persian digits.
 *
 * @param string|int $str Input.
 * @return string
 */
function larijani_fa_num( $str ) {
	return strtr( (string) $str, array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' ) );
}

/**
 * Convert Persian/Arabic digits to Latin digits.
 *
 * @param string $str Input.
 * @return string
 */
function larijani_en_num( $str ) {
	return strtr( (string) $str, array( '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9', '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9' ) );
}

/**
 * Pretty print a phone number: 09122302685 → ۰۹۱۲ ۲۳۰ ۲۶۸۵.
 *
 * @param string $phone Phone.
 * @param bool   $spaced Insert spaces.
 * @return string
 */
function larijani_phone_display( $phone, $spaced = true ) {
	$p = preg_replace( '/\D+/', '', larijani_en_num( $phone ) );
	if ( $spaced && 11 === strlen( $p ) ) {
		$p = substr( $p, 0, 4 ) . ' ' . substr( $p, 4, 3 ) . ' ' . substr( $p, 7 );
	}
	return larijani_fa_num( $p );
}

/**
 * tel: link for a phone number.
 *
 * @param string $phone Phone.
 * @return string
 */
function larijani_tel( $phone ) {
	return 'tel:' . preg_replace( '/[^\d+]/', '', larijani_en_num( $phone ) );
}

/**
 * WhatsApp link.
 *
 * @param string $number International number without +, e.g. 98912...
 * @param string $text   Optional prefilled text.
 * @return string
 */
function larijani_whatsapp_url( $number = '', $text = '' ) {
	$number = preg_replace( '/\D+/', '', larijani_en_num( $number ? $number : larijani_opt( 'whatsapp' ) ) );
	if ( 0 === strpos( $number, '0' ) ) {
		$number = '98' . substr( $number, 1 );
	}
	$url = 'https://wa.me/' . $number;
	if ( $text ) {
		$url .= '?text=' . rawurlencode( $text );
	}
	return $url;
}

/**
 * Render an icon. Accepts an Elementor ICONS control value, a plain class
 * string ("bi bi-truck") or an empty value.
 *
 * @param array|string $icon  Icon.
 * @param string       $class Extra classes.
 * @param array        $attrs Extra attributes.
 * @return string
 */
function larijani_icon( $icon, $class = '', $attrs = array() ) {
	if ( empty( $icon ) ) {
		return '';
	}
	if ( is_array( $icon ) ) {
		if ( empty( $icon['value'] ) ) {
			return '';
		}
		if ( 'svg' === ( $icon['library'] ?? '' ) && class_exists( '\Elementor\Icons_Manager' ) ) {
			ob_start();
			\Elementor\Icons_Manager::render_icon( $icon, array_merge( array( 'class' => $class . ' inline-block w-[1em] h-[1em] fill-current', 'aria-hidden' => 'true' ), $attrs ) );
			return ob_get_clean();
		}
		if ( 'svg' === ( $icon['library'] ?? '' ) ) {
			$url = is_array( $icon['value'] ) ? ( $icon['value']['url'] ?? '' ) : '';
			return $url ? '<img src="' . esc_url( $url ) . '" class="' . esc_attr( $class ) . ' inline-block w-[1em] h-[1em]" alt="" aria-hidden="true">' : '';
		}
		$icon = $icon['value'];
	}
	if ( ! is_string( $icon ) ) {
		return '';
	}
	$extra = '';
	foreach ( $attrs as $k => $v ) {
		$extra .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
	}
	return '<i class="' . esc_attr( trim( $icon . ' ' . $class ) ) . '" aria-hidden="true"' . $extra . '></i>';
}

/**
 * Icon control default value for a Bootstrap icon.
 *
 * @param string $name Icon name without the "bi-" prefix.
 * @return array
 */
function larijani_bi( $name ) {
	return array(
		'value'   => 'bi bi-' . $name,
		'library' => 'bootstrap-icons',
	);
}

/**
 * Resolve an image from an Elementor MEDIA control value, an attachment id or URL.
 *
 * @param mixed  $media Media.
 * @param string $size  Image size.
 * @return string URL.
 */
function larijani_img_url( $media, $size = 'large' ) {
	if ( is_array( $media ) ) {
		if ( ! empty( $media['id'] ) ) {
			$src = wp_get_attachment_image_url( (int) $media['id'], $size );
			if ( $src ) {
				return $src;
			}
		}
		return isset( $media['url'] ) ? $media['url'] : '';
	}
	if ( is_numeric( $media ) ) {
		return (string) wp_get_attachment_image_url( (int) $media, $size );
	}
	return (string) $media;
}

/**
 * Output an <img> for a media value.
 *
 * @param mixed  $media Media value.
 * @param string $class Classes.
 * @param string $alt   Alt text.
 * @param string $size  Size.
 * @param bool|string $lazy Lazy load; false = eager, 'high' = eager + fetchpriority (hero / LCP images).
 * @return string
 */
function larijani_img( $media, $class = '', $alt = '', $size = 'large', $lazy = true ) {
	$url = larijani_img_url( $media, $size );
	if ( ! $url ) {
		$url = LARIJANI_URI . '/assets/images/placeholder.svg';
	}
	if ( ! $alt && is_array( $media ) && ! empty( $media['alt'] ) ) {
		$alt = $media['alt'];
	}
	if ( ! $alt && is_array( $media ) && ! empty( $media['id'] ) ) {
		$alt = get_post_meta( (int) $media['id'], '_wp_attachment_image_alt', true );
	}
	return sprintf(
		'<img src="%s" class="%s" alt="%s"%s decoding="async">',
		esc_url( $url ),
		esc_attr( $class ),
		esc_attr( $alt ),
		'high' === $lazy ? ' fetchpriority="high"' : ( $lazy ? ' loading="lazy"' : '' )
	);
}

/**
 * Build link attributes from an Elementor URL control value or a plain string.
 *
 * @param array|string $link URL value.
 * @return string
 */
function larijani_link_attrs( $link ) {
	if ( is_string( $link ) ) {
		return 'href="' . esc_url( $link ? $link : '#' ) . '"';
	}
	$url  = ! empty( $link['url'] ) ? $link['url'] : '#';
	$out  = 'href="' . esc_url( $url ) . '"';
	$rel  = array();
	if ( ! empty( $link['is_external'] ) ) {
		$out  .= ' target="_blank"';
		$rel[] = 'noopener';
	}
	if ( ! empty( $link['nofollow'] ) ) {
		$rel[] = 'nofollow';
	}
	if ( $rel ) {
		$out .= ' rel="' . esc_attr( implode( ' ', $rel ) ) . '"';
	}
	if ( ! empty( $link['custom_attributes'] ) ) {
		foreach ( explode( ',', $link['custom_attributes'] ) as $pair ) {
			$parts = array_map( 'trim', explode( '|', $pair, 2 ) );
			if ( ! empty( $parts[0] ) && preg_match( '/^[a-z0-9_\-]+$/i', $parts[0] ) && 'href' !== strtolower( $parts[0] ) && 0 !== stripos( $parts[0], 'on' ) ) {
				$out .= ' ' . esc_attr( $parts[0] ) . '="' . esc_attr( $parts[1] ?? '' ) . '"';
			}
		}
	}
	return $out;
}

/**
 * Text with limited HTML (used for headings that may contain <br> / <span> / <strong>).
 *
 * @param string $text Text.
 * @return string
 */
function larijani_kses( $text ) {
	return wp_kses(
		(string) $text,
		array(
			'br'     => array( 'class' => true ),
			'span'   => array( 'class' => true, 'dir' => true ),
			'strong' => array( 'class' => true ),
			'b'      => array( 'class' => true ),
			'em'     => array( 'class' => true ),
			'i'      => array( 'class' => true ),
			'a'      => array( 'href' => true, 'class' => true, 'target' => true, 'rel' => true ),
			'small'  => array( 'class' => true ),
			'mark'   => array( 'class' => true ),
		)
	);
}

/**
 * Split a textarea into non-empty lines.
 *
 * @param string $text Text.
 * @return array
 */
function larijani_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ), 'strlen' ) );
}

/**
 * Tailwind classes for a "tone" select used by badges and icon boxes.
 *
 * @param string $tone Tone key.
 * @param string $kind badge|soft|icon|text.
 * @return string
 */
function larijani_tone( $tone, $kind = 'badge' ) {
	$map = array(
		'badge' => array(
			'primary' => 'bg-primary-container text-white',
			'amber'   => 'bg-amber-700 text-white',
			'emerald' => 'bg-emerald-700 text-white',
			'cobalt'  => 'bg-blue-800 text-white',
			'dark'    => 'bg-surface-dark/80 backdrop-blur text-white',
			'light'   => 'bg-white/90 backdrop-blur-sm text-on-surface',
			'sage'    => 'bg-secondary-container text-on-secondary-container',
		),
		'soft'  => array(
			'primary' => 'bg-primary/10 text-primary',
			'amber'   => 'bg-accent-amber/10 text-accent-amber',
			'emerald' => 'bg-accent-emerald/10 text-accent-emerald',
			'cobalt'  => 'bg-accent-cobalt/10 text-accent-cobalt',
			'dark'    => 'bg-surface-dark text-primary-fixed',
			'light'   => 'bg-surface-container text-on-surface',
			'sage'    => 'bg-secondary-container text-on-secondary-container',
			'fixed'   => 'bg-primary-fixed text-on-primary-fixed',
			'sfixed'  => 'bg-secondary-fixed text-on-secondary-fixed',
			'high'    => 'bg-surface-container-high text-on-surface',
		),
		'text'  => array(
			'primary' => 'text-primary',
			'amber'   => 'text-accent-amber',
			'emerald' => 'text-accent-emerald',
			'cobalt'  => 'text-accent-cobalt',
			'dark'    => 'text-surface-dark',
			'light'   => 'text-outline',
			'sage'    => 'text-secondary',
			'error'   => 'text-error',
		),
	);
	$set = isset( $map[ $kind ] ) ? $map[ $kind ] : $map['badge'];
	return isset( $set[ $tone ] ) ? $set[ $tone ] : reset( $set );
}

/**
 * Tone options for SELECT controls.
 *
 * @return array
 */
function larijani_tone_options() {
	return array(
		'primary' => __( 'سبز برند', 'larijani-stone' ),
		'amber'   => __( 'کهربایی', 'larijani-stone' ),
		'emerald' => __( 'زمردی', 'larijani-stone' ),
		'cobalt'  => __( 'آبی کبالت', 'larijani-stone' ),
		'dark'    => __( 'تیره', 'larijani-stone' ),
		'light'   => __( 'روشن', 'larijani-stone' ),
		'sage'    => __( 'سبز ملایم', 'larijani-stone' ),
	);
}

/**
 * Background classes for a section background select.
 *
 * @param string $bg Key.
 * @return string
 */
function larijani_section_bg( $bg ) {
	$map = array(
		'canvas'    => 'bg-surface-canvas',
		'white'     => 'bg-white',
		'surface'   => 'bg-surface',
		'low'       => 'bg-surface-container-low/50',
		'dark'      => 'bg-surface-dark',
		'none'      => '',
		'white-bordered' => 'bg-white border-y border-border-subtle',
	);
	return isset( $map[ $bg ] ) ? $map[ $bg ] : '';
}

/**
 * Section background options.
 *
 * @return array
 */
function larijani_section_bg_options() {
	return array(
		'none'           => __( 'بدون پس‌زمینه', 'larijani-stone' ),
		'canvas'         => __( 'کرم سنگی (Canvas)', 'larijani-stone' ),
		'white'          => __( 'سفید', 'larijani-stone' ),
		'white-bordered' => __( 'سفید با خط مرزی', 'larijani-stone' ),
		'surface'        => __( 'Surface', 'larijani-stone' ),
		'low'            => __( 'آبی-خاکستری ملایم', 'larijani-stone' ),
		'dark'           => __( 'تیره', 'larijani-stone' ),
	);
}

/**
 * Estimated reading time in minutes for a post.
 *
 * @param int|WP_Post|null $post Post.
 * @return int
 */
function larijani_reading_time( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return 1;
	}
	$manual = (int) get_post_meta( $post->ID, '_ls_reading_time', true );
	if ( $manual ) {
		return $manual;
	}
	$words = count( preg_split( '/\s+/u', wp_strip_all_tags( $post->post_content ), -1, PREG_SPLIT_NO_EMPTY ) );
	return max( 1, (int) ceil( $words / 200 ) );
}

/**
 * Post view count.
 *
 * @param int|null $post_id Post id.
 * @return int
 */
function larijani_post_views( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	return (int) get_post_meta( $post_id, 'ls_views', true );
}

/**
 * Format a number with Persian digits and thousands separators.
 *
 * @param float|int $n Number.
 * @param int       $decimals Decimals.
 * @return string
 */
function larijani_fa_number_format( $n, $decimals = 0 ) {
	return larijani_fa_num( number_format( (float) $n, $decimals, '.', ',' ) );
}

/**
 * Initials for avatar placeholders (e.g. "م.ح").
 *
 * @param string $name Name.
 * @return string
 */
function larijani_initials( $name ) {
	$parts = preg_split( '/\s+/u', trim( wp_strip_all_tags( $name ) ) );
	$parts = array_values( array_filter( $parts, static function ( $p ) {
		return ! in_array( $p, array( 'مهندس', 'دکتر', 'حاج', 'آقای', 'خانم', 'جناب' ), true );
	} ) );
	if ( ! $parts ) {
		return '';
	}
	$first = mb_substr( $parts[0], 0, 1 );
	$last  = isset( $parts[1] ) ? mb_substr( $parts[1], 0, 1 ) : '';
	return $last ? $first . '.' . $last : $first;
}

/**
 * Is Elementor (free) active?
 *
 * @return bool
 */
function larijani_has_elementor() {
	return did_action( 'elementor/loaded' ) > 0;
}

/**
 * Is Elementor Pro active?
 *
 * @return bool
 */
function larijani_has_elementor_pro() {
	return defined( 'ELEMENTOR_PRO_VERSION' );
}

/**
 * Is WooCommerce active?
 *
 * @return bool
 */
function larijani_has_woo() {
	return class_exists( 'WooCommerce' );
}

/**
 * Are we inside the Elementor editor / preview?
 *
 * @return bool
 */
function larijani_is_elementor_editor() {
	if ( ! larijani_has_elementor() ) {
		return false;
	}
	$plugin = \Elementor\Plugin::$instance;
	return ( $plugin->editor && $plugin->editor->is_edit_mode() ) || ( $plugin->preview && $plugin->preview->is_preview_mode() );
}

/**
 * Featured image URL of a post, falling back to the demo image recorded at
 * setup (`_ls_demo_image`) and finally to the placeholder.
 *
 * @param int|WP_Post $post Post.
 * @param string      $size Image size.
 * @return string
 */
function larijani_post_image_url( $post, $size = 'large' ) {
	$url = get_the_post_thumbnail_url( $post, $size );
	if ( $url ) {
		return $url;
	}
	$post = get_post( $post );
	$key  = $post ? get_post_meta( $post->ID, '_ls_demo_image', true ) : '';
	return $key ? larijani_demo_image( $key ) : '';
}

/**
 * Convert a Gregorian date to the Jalali (Persian) calendar.
 *
 * @param int $gy Year.
 * @param int $gm Month.
 * @param int $gd Day.
 * @return int[] [ year, month, day ]
 */
function larijani_gregorian_to_jalali( $gy, $gm, $gd ) {
	$g_d_m = array( 0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334 );
	$gy2   = ( $gm > 2 ) ? ( $gy + 1 ) : $gy;
	$days  = 355666 + ( 365 * $gy ) + intdiv( $gy2 + 3, 4 ) - intdiv( $gy2 + 99, 100 ) + intdiv( $gy2 + 399, 400 ) + $gd + $g_d_m[ $gm - 1 ];
	$jy    = -1595 + ( 33 * intdiv( $days, 12053 ) );
	$days %= 12053;
	$jy   += 4 * intdiv( $days, 1461 );
	$days %= 1461;
	if ( $days > 365 ) {
		$jy  += intdiv( $days - 1, 365 );
		$days = ( $days - 1 ) % 365;
	}
	if ( $days < 186 ) {
		$jm = 1 + intdiv( $days, 31 );
		$jd = 1 + ( $days % 31 );
	} else {
		$jm = 7 + intdiv( $days - 186, 30 );
		$jd = 1 + ( ( $days - 186 ) % 30 );
	}
	return array( $jy, $jm, $jd );
}

/**
 * Persian (Jalali) date such as «۱۴ اسفند ۱۴۰۳».
 *
 * @param int $timestamp Unix timestamp (site time zone is applied).
 * @return string
 */
function larijani_jalali_date( $timestamp ) {
	$months = array( 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند' );
	$parts  = explode( '-', wp_date( 'Y-n-j', $timestamp ) );
	list( $jy, $jm, $jd ) = larijani_gregorian_to_jalali( (int) $parts[0], (int) $parts[1], (int) $parts[2] );
	return larijani_fa_num( $jd . ' ' . $months[ $jm - 1 ] . ' ' . $jy );
}
