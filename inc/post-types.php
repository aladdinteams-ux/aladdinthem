<?php
/**
 * Custom post types: portfolio projects (ls_project) and inquiries (ls_lead).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register post types & taxonomies.
 */
function ls_register_post_types() {
	register_post_type(
		'ls_project',
		array(
			'labels'       => array(
				'name'          => __( 'نمونه‌کارها', 'larijani' ),
				'singular_name' => __( 'نمونه‌کار', 'larijani' ),
				'add_new_item'  => __( 'افزودن پروژه جدید', 'larijani' ),
				'edit_item'     => __( 'ویرایش پروژه', 'larijani' ),
				'all_items'     => __( 'همه پروژه‌ها', 'larijani' ),
				'menu_name'     => __( 'نمونه‌کارها', 'larijani' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'menu_icon'    => 'dashicons-building',
			'menu_position' => 21,
			'rewrite'      => array( 'slug' => 'projects' ),
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'elementor', 'custom-fields' ),
			'show_in_rest' => true,
		)
	);

	register_taxonomy(
		'ls_project_cat',
		'ls_project',
		array(
			'labels'            => array(
				'name'          => __( 'دسته‌های پروژه', 'larijani' ),
				'singular_name' => __( 'دسته پروژه', 'larijani' ),
			),
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'project-category' ),
		)
	);

	register_post_type(
		'ls_lead',
		array(
			'labels'          => array(
				'name'          => __( 'درخواست‌ها و استعلام‌ها', 'larijani' ),
				'singular_name' => __( 'درخواست', 'larijani' ),
				'menu_name'     => __( 'درخواست‌ها', 'larijani' ),
				'edit_item'     => __( 'جزئیات درخواست', 'larijani' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'menu_icon'       => 'dashicons-email-alt',
			'menu_position'   => 22,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'ls_register_post_types' );

/**
 * Enable Elementor for projects by default.
 */
function ls_elementor_cpt_support() {
	$types = get_option( 'elementor_cpt_support', array( 'page', 'post' ) );
	if ( is_array( $types ) && ! in_array( 'ls_project', $types, true ) ) {
		$types[] = 'ls_project';
		update_option( 'elementor_cpt_support', $types );
	}
}
add_action( 'after_switch_theme', 'ls_elementor_cpt_support' );

/**
 * Project meta fields definition.
 *
 * @return array
 */
function ls_project_fields() {
	return array(
		'location'     => __( 'محل اجرا (مثلاً: تهران، برج اداری پارک‌وی)', 'larijani' ),
		'code'         => __( 'کد / ظرفیت (مثلاً: کد قالب: ۳D-904)', 'larijani' ),
		'badge_1'      => __( 'برچسب اول روی تصویر', 'larijani' ),
		'badge_2'      => __( 'برچسب دوم روی تصویر', 'larijani' ),
		'spec_1_label' => __( 'مشخصه ۱ – عنوان', 'larijani' ),
		'spec_1_value' => __( 'مشخصه ۱ – مقدار', 'larijani' ),
		'spec_2_label' => __( 'مشخصه ۲ – عنوان', 'larijani' ),
		'spec_2_value' => __( 'مشخصه ۲ – مقدار', 'larijani' ),
		'spec_3_label' => __( 'مشخصه ۳ – عنوان', 'larijani' ),
		'spec_3_value' => __( 'مشخصه ۳ – مقدار', 'larijani' ),
		'note'         => __( 'یادداشت پایین کارت (مثلاً: فاقد تغییر رنگ در تابش UV)', 'larijani' ),
		'note_icon'    => __( 'آیکون یادداشت (کلاس bootstrap، مثل bi bi-patch-check-fill)', 'larijani' ),
	);
}

/**
 * Project meta box.
 */
function ls_project_meta_box() {
	add_meta_box( 'ls_project_meta', __( 'مشخصات پروژه (کارت نمونه‌کار)', 'larijani' ), 'ls_project_meta_box_html', 'ls_project', 'normal', 'high' );
	add_meta_box( 'ls_lead_meta', __( 'اطلاعات ثبت‌شده', 'larijani' ), 'ls_lead_meta_box_html', 'ls_lead', 'normal', 'high' );
	add_meta_box( 'ls_post_meta', __( 'تنظیمات مقاله (قالب لاریجانی)', 'larijani' ), 'ls_post_meta_box_html', 'post', 'side', 'default' );
}
add_action( 'add_meta_boxes', 'ls_project_meta_box' );

/**
 * Project meta box markup.
 *
 * @param WP_Post $post Post.
 */
function ls_project_meta_box_html( $post ) {
	wp_nonce_field( 'ls_project_meta', 'ls_project_meta_nonce' );
	echo '<table class="form-table"><tbody>';
	foreach ( ls_project_fields() as $key => $label ) {
		printf(
			'<tr><th><label for="ls_%1$s">%2$s</label></th><td><input type="text" class="widefat" id="ls_%1$s" name="ls_project[%1$s]" value="%3$s"></td></tr>',
			esc_attr( $key ),
			esc_html( $label ),
			esc_attr( get_post_meta( $post->ID, '_ls_' . $key, true ) )
		);
	}
	echo '</tbody></table>';
}

/**
 * Save project meta.
 *
 * @param int $post_id Post id.
 */
function ls_save_project_meta( $post_id ) {
	if ( ! isset( $_POST['ls_project_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ls_project_meta_nonce'] ) ), 'ls_project_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$data = isset( $_POST['ls_project'] ) ? (array) wp_unslash( $_POST['ls_project'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	foreach ( array_keys( ls_project_fields() ) as $key ) {
		update_post_meta( $post_id, '_ls_' . $key, isset( $data[ $key ] ) ? sanitize_text_field( $data[ $key ] ) : '' );
	}
}
add_action( 'save_post_ls_project', 'ls_save_project_meta' );

/**
 * Post meta box: manual reading time + featured flag + image caption.
 *
 * @param WP_Post $post Post.
 */
function ls_post_meta_box_html( $post ) {
	wp_nonce_field( 'ls_post_meta', 'ls_post_meta_nonce' );
	printf(
		'<p><label><input type="checkbox" name="ls_featured" value="1" %s> %s</label></p>',
		checked( get_post_meta( $post->ID, '_ls_featured', true ), '1', false ),
		esc_html__( 'مقاله ویژه تحریریه (نمایش در بالای آرشیو)', 'larijani' )
	);
	printf(
		'<p><label>%s<br><input type="number" min="0" class="widefat" name="ls_reading_time" value="%s"></label></p>',
		esc_html__( 'زمان مطالعه (دقیقه، خالی = خودکار)', 'larijani' ),
		esc_attr( get_post_meta( $post->ID, '_ls_reading_time', true ) )
	);
	printf(
		'<p><label>%s<br><input type="text" class="widefat" name="ls_image_caption" value="%s"></label></p>',
		esc_html__( 'کپشن روی تصویر شاخص', 'larijani' ),
		esc_attr( get_post_meta( $post->ID, '_ls_image_caption', true ) )
	);
	printf(
		'<p><label>%s<br><input type="text" class="widefat" name="ls_image_badge" value="%s"></label></p>',
		esc_html__( 'برچسب گوشه تصویر شاخص (مثل کد استاندارد)', 'larijani' ),
		esc_attr( get_post_meta( $post->ID, '_ls_image_badge', true ) )
	);
	foreach ( array(
		'_ls_author_name' => __( 'نام نویسنده نمایشی (خالی = کاربر)', 'larijani' ),
		'_ls_author_role' => __( 'سمت نویسنده', 'larijani' ),
	) as $key => $label ) {
		printf( '<p><label>%s<br><input type="text" class="widefat" name="%s" value="%s"></label></p>', esc_html( $label ), esc_attr( ltrim( $key, '_' ) ), esc_attr( get_post_meta( $post->ID, $key, true ) ) );
	}
	printf(
		'<p><label>%s<br><textarea class="widefat" rows="3" name="ls_author_bio">%s</textarea></label></p>',
		esc_html__( 'معرفی کوتاه نویسنده', 'larijani' ),
		esc_textarea( get_post_meta( $post->ID, '_ls_author_bio', true ) )
	);
	printf(
		'<p><label>%s<br><textarea class="widefat" rows="5" dir="auto" name="ls_metrics" placeholder="%s">%s</textarea></label></p>',
		esc_html__( 'کارت‌های شاخص زیر تصویر (هر خط یک کارت)', 'larijani' ),
		esc_attr__( 'عنوان | مقدار | واحد | آیکون | رنگ | یادداشت | آیکون یادداشت', 'larijani' ),
		esc_textarea( get_post_meta( $post->ID, '_ls_metrics', true ) )
	);
}

/**
 * Save post meta.
 *
 * @param int $post_id Post id.
 */
function ls_save_post_meta( $post_id ) {
	if ( ! isset( $_POST['ls_post_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ls_post_meta_nonce'] ) ), 'ls_post_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	update_post_meta( $post_id, '_ls_featured', empty( $_POST['ls_featured'] ) ? '' : '1' );
	update_post_meta( $post_id, '_ls_reading_time', isset( $_POST['ls_reading_time'] ) ? absint( $_POST['ls_reading_time'] ) : '' );
	update_post_meta( $post_id, '_ls_image_caption', isset( $_POST['ls_image_caption'] ) ? sanitize_text_field( wp_unslash( $_POST['ls_image_caption'] ) ) : '' );
	update_post_meta( $post_id, '_ls_image_badge', isset( $_POST['ls_image_badge'] ) ? sanitize_text_field( wp_unslash( $_POST['ls_image_badge'] ) ) : '' );
	foreach ( array( 'ls_author_name', 'ls_author_role' ) as $key ) {
		update_post_meta( $post_id, '_' . $key, isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '' );
	}
	foreach ( array( 'ls_author_bio', 'ls_metrics' ) as $key ) {
		update_post_meta( $post_id, '_' . $key, isset( $_POST[ $key ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) : '' );
	}
}
add_action( 'save_post_post', 'ls_save_post_meta' );

/**
 * Lead details meta box.
 *
 * @param WP_Post $post Post.
 */
function ls_lead_meta_box_html( $post ) {
	$fields = get_post_meta( $post->ID, '_ls_lead_fields', true );
	$files  = get_post_meta( $post->ID, '_ls_lead_files', true );
	echo '<table class="widefat striped"><tbody>';
	printf( '<tr><th style="width:220px">%s</th><td>%s</td></tr>', esc_html__( 'فرم', 'larijani' ), esc_html( get_post_meta( $post->ID, '_ls_lead_form', true ) ) );
	printf( '<tr><th>%s</th><td><a href="%2$s" target="_blank">%2$s</a></td></tr>', esc_html__( 'صفحه ارسال', 'larijani' ), esc_url( get_post_meta( $post->ID, '_ls_lead_page', true ) ) );
	printf( '<tr><th>%s</th><td>%s</td></tr>', esc_html__( 'کد پیگیری', 'larijani' ), esc_html( get_post_meta( $post->ID, '_ls_lead_tracking', true ) ) );
	if ( is_array( $fields ) ) {
		foreach ( $fields as $row ) {
			printf( '<tr><th>%s</th><td>%s</td></tr>', esc_html( $row['label'] ), nl2br( esc_html( $row['value'] ) ) );
		}
	}
	if ( is_array( $files ) ) {
		foreach ( $files as $att_id ) {
			printf( '<tr><th>%s</th><td><a href="%2$s" target="_blank">%3$s</a></td></tr>', esc_html__( 'فایل پیوست', 'larijani' ), esc_url( wp_get_attachment_url( $att_id ) ), esc_html( get_the_title( $att_id ) ) );
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
function ls_lead_columns( $cols ) {
	return array(
		'cb'       => $cols['cb'],
		'title'    => __( 'عنوان', 'larijani' ),
		'ls_phone' => __( 'تلفن', 'larijani' ),
		'ls_form'  => __( 'فرم', 'larijani' ),
		'date'     => $cols['date'],
	);
}
add_filter( 'manage_ls_lead_posts_columns', 'ls_lead_columns' );

/**
 * Lead list column values.
 *
 * @param string $col Column.
 * @param int    $post_id Post id.
 */
function ls_lead_column_values( $col, $post_id ) {
	if ( 'ls_phone' === $col ) {
		$phone = get_post_meta( $post_id, '_ls_lead_phone', true );
		echo $phone ? '<a href="' . esc_url( ls_tel( $phone ) ) . '">' . esc_html( $phone ) . '</a>' : '—';
	} elseif ( 'ls_form' === $col ) {
		echo esc_html( get_post_meta( $post_id, '_ls_lead_form', true ) );
	}
}
add_action( 'manage_ls_lead_posts_custom_column', 'ls_lead_column_values', 10, 2 );

/**
 * Page meta box: per-page header style (design pages use different headers).
 */
function ls_page_meta_box() {
	add_meta_box( 'ls_page_meta', __( 'تنظیمات برگه (قالب لاریجانی)', 'larijani' ), 'ls_page_meta_box_html', array( 'page', 'post' ), 'side', 'default' );
}
add_action( 'add_meta_boxes', 'ls_page_meta_box' );

/**
 * Page meta box markup.
 *
 * @param WP_Post $post Post.
 */
function ls_page_meta_box_html( $post ) {
	wp_nonce_field( 'ls_page_meta', 'ls_page_meta_nonce' );
	$val     = get_post_meta( $post->ID, '_ls_header_style', true );
	$choices = array(
		''      => __( 'طبق تنظیمات قالب', 'larijani' ),
		'dark'  => __( 'هدر با نوار بالای تیره', 'larijani' ),
		'light' => __( 'هدر کلاسیک با نوار بالای روشن', 'larijani' ),
	);
	echo '<p><label for="ls_header_style"><strong>' . esc_html__( 'طرح هدر', 'larijani' ) . '</strong></label><br><select id="ls_header_style" name="ls_header_style" style="width:100%">';
	foreach ( $choices as $k => $l ) {
		echo '<option value="' . esc_attr( $k ) . '" ' . selected( $val, $k, false ) . '>' . esc_html( $l ) . '</option>';
	}
	echo '</select></p>';
}

/**
 * Save page meta.
 *
 * @param int $post_id Post id.
 */
function ls_save_page_meta( $post_id ) {
	if ( ! isset( $_POST['ls_page_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ls_page_meta_nonce'] ) ), 'ls_page_meta' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$val = isset( $_POST['ls_header_style'] ) ? sanitize_key( wp_unslash( $_POST['ls_header_style'] ) ) : '';
	if ( in_array( $val, array( 'dark', 'light' ), true ) ) {
		update_post_meta( $post_id, '_ls_header_style', $val );
	} else {
		delete_post_meta( $post_id, '_ls_header_style' );
	}
}
add_action( 'save_post', 'ls_save_page_meta' );
