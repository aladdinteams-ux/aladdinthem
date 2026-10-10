<?php
/**
 * WordPress privacy tools: export / erase inquiry data by e-mail or phone.
 *
 * Tools › Export/Erase Personal Data accept an e-mail address; leads are matched
 * by the stored e-mail and, if the request identifier is a phone number, by phone.
 *
 * @package Larijani_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Find leads belonging to an identifier.
 *
 * @param string $identifier E-mail or phone.
 * @param int    $page Page.
 * @return int[]
 */
function larijani_core_find_leads( $identifier, $page = 1 ) {
	$meta  = array( 'relation' => 'OR' );
	$email = is_email( $identifier ) ? sanitize_email( $identifier ) : '';
	$phone = larijani_core_normalize_phone( $identifier );
	if ( $email ) {
		$meta[] = array(
			'key'   => '_ls_lead_email',
			'value' => $email,
		);
		$meta[] = array(
			'key'     => '_ls_lead_fields',
			'value'   => $email,
			'compare' => 'LIKE',
		); // Leads saved by theme 1.3.x.
	}
	if ( $phone ) {
		$meta[] = array(
			'key'   => '_ls_lead_phone',
			'value' => $phone,
		);
	}
	if ( count( $meta ) < 2 ) {
		return array();
	}
	return get_posts(
		array(
			'post_type'      => 'ls_lead',
			'post_status'    => 'any',
			'posts_per_page' => 50,
			'paged'          => $page,
			'fields'         => 'ids',
			'meta_query'     => $meta, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		)
	);
}

/**
 * Exporter.
 *
 * @param string $identifier E-mail.
 * @param int    $page Page.
 * @return array
 */
function larijani_core_privacy_export( $identifier, $page = 1 ) {
	$items = array();
	$ids   = larijani_core_find_leads( $identifier, $page );
	foreach ( $ids as $id ) {
		$data = array(
			array(
				'name'  => __( 'کد پیگیری', 'larijani-stone-core' ),
				'value' => get_post_meta( $id, '_ls_lead_tracking', true ),
			),
			array(
				'name'  => __( 'فرم', 'larijani-stone-core' ),
				'value' => get_post_meta( $id, '_ls_lead_form', true ),
			),
			array(
				'name'  => __( 'تاریخ', 'larijani-stone-core' ),
				'value' => get_the_date( 'Y-m-d H:i', $id ),
			),
		);
		foreach ( (array) get_post_meta( $id, '_ls_lead_fields', true ) as $row ) {
			if ( isset( $row['label'], $row['value'] ) ) {
				$data[] = array(
					'name'  => $row['label'],
					'value' => $row['value'],
				);
			}
		}
		foreach ( larijani_core_lead_files( $id ) as $rec ) {
			$data[] = array(
				'name'  => __( 'فایل پیوست', 'larijani-stone-core' ),
				'value' => $rec['name'],
			);
		}
		$items[] = array(
			'group_id'    => 'larijani-leads',
			'group_label' => __( 'درخواست‌ها و استعلام‌ها', 'larijani-stone-core' ),
			'item_id'     => 'lead-' . $id,
			'data'        => $data,
		);
	}
	return array(
		'data' => $items,
		'done' => count( $ids ) < 50,
	);
}

/**
 * Eraser: deletes the matching leads and their private files.
 *
 * @param string $identifier E-mail.
 * @param int    $page Page.
 * @return array
 */
function larijani_core_privacy_erase( $identifier, $page = 1 ) {
	$ids     = larijani_core_find_leads( $identifier, 1 ); // Always page 1: matches are removed as we go.
	$removed = 0;
	foreach ( $ids as $id ) {
		if ( wp_delete_post( $id, true ) ) { // Private files are removed by before_delete_post.
			++$removed;
		}
	}
	return array(
		'items_removed'  => $removed > 0,
		'items_retained' => false,
		'messages'       => array(),
		'done'           => count( $ids ) < 50,
	);
}

/**
 * Register exporter.
 *
 * @param array $exporters Exporters.
 * @return array
 */
function larijani_core_register_exporter( $exporters ) {
	$exporters['larijani-stone-core'] = array(
		'exporter_friendly_name' => __( 'درخواست‌های لاریجانی استون', 'larijani-stone-core' ),
		'callback'               => 'larijani_core_privacy_export',
	);
	return $exporters;
}
add_filter( 'wp_privacy_personal_data_exporters', 'larijani_core_register_exporter' );

/**
 * Register eraser.
 *
 * @param array $erasers Erasers.
 * @return array
 */
function larijani_core_register_eraser( $erasers ) {
	$erasers['larijani-stone-core'] = array(
		'eraser_friendly_name' => __( 'درخواست‌های لاریجانی استون', 'larijani-stone-core' ),
		'callback'             => 'larijani_core_privacy_erase',
	);
	return $erasers;
}
add_filter( 'wp_privacy_personal_data_erasers', 'larijani_core_register_eraser' );

/**
 * Suggested privacy-policy text.
 */
function larijani_core_privacy_policy() {
	if ( function_exists( 'wp_add_privacy_policy_content' ) ) {
		wp_add_privacy_policy_content(
			__( 'لاریجانی استون – فرم‌ها', 'larijani-stone-core' ),
			wp_kses_post( wpautop( __( 'اطلاعاتی که در فرم‌های درخواست و استعلام وارد می‌کنید (مانند نام، شماره تماس، ایمیل، توضیحات و فایل‌های پیوست) برای پاسخ‌گویی به درخواست شما ذخیره و به ایمیل مدیر سایت ارسال می‌شود. فایل‌های پیوست در فضای خصوصی سرور نگهداری می‌شوند و لینک عمومی ندارند. برای جلوگیری از هرزنامه، نشانی IP شما به‌صورت هش‌شده (غیرقابل بازگشت) نگهداری می‌شود. برای دریافت یا حذف اطلاعات خود با مدیر سایت تماس بگیرید.', 'larijani-stone-core' ) ) )
		);
	}
}
add_action( 'admin_init', 'larijani_core_privacy_policy' );
