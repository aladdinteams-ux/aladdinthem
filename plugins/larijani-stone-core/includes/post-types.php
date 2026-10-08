<?php
/**
 * Portfolio projects (ls_project + ls_project_cat) and inquiries (ls_lead).
 *
 * Post type, taxonomy and meta keys are identical to Larijani Stone theme
 * 1.3.x so existing content keeps working without any data conversion.
 *
 * @package Larijani_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register post types & taxonomies.
 */
function larijani_core_register_post_types() {
	register_post_type(
		'ls_project',
		array(
			'labels'        => array(
				'name'          => __( 'نمونه‌کارها', 'larijani-stone-core' ),
				'singular_name' => __( 'نمونه‌کار', 'larijani-stone-core' ),
				'add_new_item'  => __( 'افزودن پروژه جدید', 'larijani-stone-core' ),
				'edit_item'     => __( 'ویرایش پروژه', 'larijani-stone-core' ),
				'all_items'     => __( 'همه پروژه‌ها', 'larijani-stone-core' ),
				'menu_name'     => __( 'نمونه‌کارها', 'larijani-stone-core' ),
			),
			'public'        => true,
			'has_archive'   => true,
			'menu_icon'     => 'dashicons-building',
			'menu_position' => 21,
			'rewrite'       => array( 'slug' => apply_filters( 'larijani_core_project_slug', 'projects' ) ),
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'elementor', 'custom-fields' ),
			'show_in_rest'  => true,
		)
	);

	register_taxonomy(
		'ls_project_cat',
		'ls_project',
		array(
			'labels'            => array(
				'name'          => __( 'دسته‌های پروژه', 'larijani-stone-core' ),
				'singular_name' => __( 'دسته پروژه', 'larijani-stone-core' ),
			),
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => apply_filters( 'larijani_core_project_cat_slug', 'project-category' ) ),
		)
	);

	register_post_type(
		'ls_lead',
		array(
			'labels'              => array(
				'name'          => __( 'درخواست‌ها و استعلام‌ها', 'larijani-stone-core' ),
				'singular_name' => __( 'درخواست', 'larijani-stone-core' ),
				'menu_name'     => __( 'درخواست‌ها', 'larijani-stone-core' ),
				'edit_item'     => __( 'جزئیات درخواست', 'larijani-stone-core' ),
				'search_items'  => __( 'جستجوی درخواست‌ها', 'larijani-stone-core' ),
				'not_found'     => __( 'درخواستی یافت نشد.', 'larijani-stone-core' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_rest'        => false, // Personal data never goes through the public REST API.
			'menu_icon'           => 'dashicons-email-alt',
			'menu_position'       => 22,
			'supports'            => array( 'title' ),
			'capability_type'     => 'post',
			// Leads contain personal data: editors and administrators only.
			'capabilities'        => array(
				'create_posts'       => 'do_not_allow',
				'edit_posts'         => 'edit_others_posts',
				'edit_others_posts'  => 'edit_others_posts',
				'edit_private_posts' => 'edit_others_posts',
				'read_private_posts' => 'edit_others_posts',
				'delete_posts'       => 'edit_others_posts',
			),
			'map_meta_cap'        => true,
		)
	);

	foreach ( array_keys( larijani_core_project_fields() ) as $key ) {
		register_post_meta(
			'ls_project',
			'_ls_' . $key,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => false,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function ( $allowed, $meta_key, $post_id ) {
					return current_user_can( 'edit_post', $post_id );
				},
			)
		);
	}
}
add_action( 'init', 'larijani_core_register_post_types' );

/**
 * Project meta fields definition.
 *
 * @return array
 */
function larijani_core_project_fields() {
	return apply_filters(
		'larijani_core_project_fields',
		array(
			'location'     => __( 'محل اجرا (مثلاً: تهران، برج اداری پارک‌وی)', 'larijani-stone-core' ),
			'code'         => __( 'کد / ظرفیت (مثلاً: کد قالب: ۳D-904)', 'larijani-stone-core' ),
			'badge_1'      => __( 'برچسب اول روی تصویر', 'larijani-stone-core' ),
			'badge_2'      => __( 'برچسب دوم روی تصویر', 'larijani-stone-core' ),
			'spec_1_label' => __( 'مشخصه ۱ – عنوان', 'larijani-stone-core' ),
			'spec_1_value' => __( 'مشخصه ۱ – مقدار', 'larijani-stone-core' ),
			'spec_2_label' => __( 'مشخصه ۲ – عنوان', 'larijani-stone-core' ),
			'spec_2_value' => __( 'مشخصه ۲ – مقدار', 'larijani-stone-core' ),
			'spec_3_label' => __( 'مشخصه ۳ – عنوان', 'larijani-stone-core' ),
			'spec_3_value' => __( 'مشخصه ۳ – مقدار', 'larijani-stone-core' ),
			'note'         => __( 'یادداشت پایین کارت (مثلاً: فاقد تغییر رنگ در تابش UV)', 'larijani-stone-core' ),
			'note_icon'    => __( 'آیکون یادداشت (کلاس bootstrap، مثل bi bi-patch-check-fill)', 'larijani-stone-core' ),
		)
	);
}

/**
 * Project meta box.
 */
function larijani_core_project_meta_box() {
	add_meta_box( 'ls_project_meta', __( 'مشخصات پروژه (کارت نمونه‌کار)', 'larijani-stone-core' ), 'larijani_core_project_meta_box_html', 'ls_project', 'normal', 'high' );
}
add_action( 'add_meta_boxes_ls_project', 'larijani_core_project_meta_box' );

/**
 * Project meta box markup.
 *
 * @param WP_Post $post Post.
 */
function larijani_core_project_meta_box_html( $post ) {
	wp_nonce_field( 'ls_project_meta', 'ls_project_meta_nonce' );
	echo '<table class="form-table"><tbody>';
	foreach ( larijani_core_project_fields() as $key => $label ) {
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
function larijani_core_save_project_meta( $post_id ) {
	if ( ! isset( $_POST['ls_project_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ls_project_meta_nonce'] ) ), 'ls_project_meta' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$data = isset( $_POST['ls_project'] ) && is_array( $_POST['ls_project'] ) ? wp_unslash( $_POST['ls_project'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per key below.
	foreach ( array_keys( larijani_core_project_fields() ) as $key ) {
		$value = isset( $data[ $key ] ) && is_scalar( $data[ $key ] ) ? sanitize_text_field( (string) $data[ $key ] ) : '';
		update_post_meta( $post_id, '_ls_' . $key, $value );
	}
}
add_action( 'save_post_ls_project', 'larijani_core_save_project_meta' );
