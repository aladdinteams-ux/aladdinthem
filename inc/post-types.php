<?php
/**
 * Post/page presentation meta boxes. Projects and inquiries live in the
 * Larijani Stone Core plugin (see larijani_legacy_post_types() for upgrades).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the companion plugin (Larijani Stone Core) is active.
 *
 * Projects (ls_project / ls_project_cat) and inquiries (ls_lead) are content,
 * so since 1.4.0 they are registered by the plugin; deactivating or switching
 * the theme never hides them.
 *
 * @return bool
 */
function larijani_has_core() {
	return defined( 'LARIJANI_CORE_VERSION' );
}

/**
 * Safety net for sites updated from 1.3.x before the plugin is installed:
 * keep existing projects and inquiries reachable (same post types, same
 * URLs) until the plugin takes over. Fresh installs never use this path.
 */
function larijani_legacy_post_types() {
	if ( larijani_has_core() ) {
		return;
	}
	$legacy = get_option( 'larijani_legacy_content', null );
	if ( null === $legacy ) {
		// One-time check: does this site already hold projects or inquiries?
		global $wpdb;
		$legacy = (int) (bool) $wpdb->get_var( "SELECT ID FROM {$wpdb->posts} WHERE post_type IN ('ls_project','ls_lead') LIMIT 1" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		update_option( 'larijani_legacy_content', $legacy, true );
	}
	if ( ! $legacy ) {
		return;
	}
	register_post_type(
		'ls_project',
		array(
			'labels'        => array(
				'name'          => __( 'نمونه‌کارها', 'larijani-stone' ),
				'singular_name' => __( 'نمونه‌کار', 'larijani-stone' ),
			),
			'public'        => true,
			'has_archive'   => true,
			'menu_icon'     => 'dashicons-building',
			'menu_position' => 21,
			'rewrite'       => array( 'slug' => 'projects' ),
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'elementor', 'custom-fields' ),
			'show_in_rest'  => true,
		)
	);
	register_taxonomy(
		'ls_project_cat',
		'ls_project',
		array(
			'labels'            => array( 'name' => __( 'دسته‌های پروژه', 'larijani-stone' ) ),
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'project-category' ),
		)
	);
	register_post_type(
		'ls_lead',
		array(
			'labels'          => array( 'name' => __( 'درخواست‌ها و استعلام‌ها', 'larijani-stone' ) ),
			'public'          => false,
			'show_ui'         => true,
			'show_in_rest'    => false,
			'menu_icon'       => 'dashicons-email-alt',
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'capabilities'    => array(
				'create_posts'       => 'do_not_allow',
				'edit_posts'         => 'edit_others_posts',
				'edit_others_posts'  => 'edit_others_posts',
				'edit_private_posts' => 'edit_others_posts',
				'read_private_posts' => 'edit_others_posts',
				'delete_posts'       => 'edit_others_posts',
			),
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'larijani_legacy_post_types' );

/**
 * Article meta box (presentation settings stay in the theme).
 */
function larijani_post_meta_box() {
	add_meta_box( 'ls_post_meta', __( 'تنظیمات مقاله (قالب لاریجانی)', 'larijani-stone' ), 'larijani_post_meta_box_html', 'post', 'side', 'default' );
}
add_action( 'add_meta_boxes', 'larijani_post_meta_box' );

/**
 * Post meta box: manual reading time + featured flag + image caption.
 *
 * @param WP_Post $post Post.
 */
function larijani_post_meta_box_html( $post ) {
	wp_nonce_field( 'ls_post_meta', 'ls_post_meta_nonce' );
	printf(
		'<p><label><input type="checkbox" name="ls_featured" value="1" %s> %s</label></p>',
		checked( get_post_meta( $post->ID, '_ls_featured', true ), '1', false ),
		esc_html__( 'مقاله ویژه تحریریه (نمایش در بالای آرشیو)', 'larijani-stone' )
	);
	printf(
		'<p><label>%s<br><input type="number" min="0" class="widefat" name="ls_reading_time" value="%s"></label></p>',
		esc_html__( 'زمان مطالعه (دقیقه، خالی = خودکار)', 'larijani-stone' ),
		esc_attr( get_post_meta( $post->ID, '_ls_reading_time', true ) )
	);
	printf(
		'<p><label>%s<br><input type="text" class="widefat" name="ls_image_caption" value="%s"></label></p>',
		esc_html__( 'کپشن روی تصویر شاخص', 'larijani-stone' ),
		esc_attr( get_post_meta( $post->ID, '_ls_image_caption', true ) )
	);
	printf(
		'<p><label>%s<br><input type="text" class="widefat" name="ls_image_badge" value="%s"></label></p>',
		esc_html__( 'برچسب گوشه تصویر شاخص (مثل کد استاندارد)', 'larijani-stone' ),
		esc_attr( get_post_meta( $post->ID, '_ls_image_badge', true ) )
	);
	foreach ( array(
		'_ls_author_name' => __( 'نام نویسنده نمایشی (خالی = کاربر)', 'larijani-stone' ),
		'_ls_author_role' => __( 'سمت نویسنده', 'larijani-stone' ),
	) as $key => $label ) {
		printf( '<p><label>%s<br><input type="text" class="widefat" name="%s" value="%s"></label></p>', esc_html( $label ), esc_attr( ltrim( $key, '_' ) ), esc_attr( get_post_meta( $post->ID, $key, true ) ) );
	}
	printf(
		'<p><label>%s<br><textarea class="widefat" rows="3" name="ls_author_bio">%s</textarea></label></p>',
		esc_html__( 'معرفی کوتاه نویسنده', 'larijani-stone' ),
		esc_textarea( get_post_meta( $post->ID, '_ls_author_bio', true ) )
	);
	printf(
		'<p><label>%s<br><textarea class="widefat" rows="5" dir="auto" name="ls_metrics" placeholder="%s">%s</textarea></label></p>',
		esc_html__( 'کارت‌های شاخص زیر تصویر (هر خط یک کارت)', 'larijani-stone' ),
		esc_attr__( 'عنوان | مقدار | واحد | آیکون | رنگ | یادداشت | آیکون یادداشت', 'larijani-stone' ),
		esc_textarea( get_post_meta( $post->ID, '_ls_metrics', true ) )
	);
}

/**
 * Save post meta.
 *
 * @param int $post_id Post id.
 */
function larijani_save_post_meta( $post_id ) {
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
add_action( 'save_post_post', 'larijani_save_post_meta' );

/**
 * Page meta box: per-page header style (design pages use different headers).
 */
function larijani_page_meta_box() {
	add_meta_box( 'ls_page_meta', __( 'تنظیمات برگه (قالب لاریجانی)', 'larijani-stone' ), 'larijani_page_meta_box_html', array( 'page', 'post' ), 'side', 'default' );
}
add_action( 'add_meta_boxes', 'larijani_page_meta_box' );

/**
 * Page meta box markup.
 *
 * @param WP_Post $post Post.
 */
function larijani_page_meta_box_html( $post ) {
	wp_nonce_field( 'ls_page_meta', 'ls_page_meta_nonce' );
	$val     = get_post_meta( $post->ID, '_ls_header_style', true );
	$choices = array(
		''      => __( 'طبق تنظیمات قالب', 'larijani-stone' ),
		'dark'  => __( 'هدر با نوار بالای تیره', 'larijani-stone' ),
		'light' => __( 'هدر کلاسیک با نوار بالای روشن', 'larijani-stone' ),
	);
	echo '<p><label for="ls_header_style"><strong>' . esc_html__( 'طرح هدر', 'larijani-stone' ) . '</strong></label><br><select id="ls_header_style" name="ls_header_style" style="width:100%">';
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
function larijani_save_page_meta( $post_id ) {
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
add_action( 'save_post', 'larijani_save_page_meta' );
