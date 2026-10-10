<?php
/**
 * Theme side of the inquiry forms.
 *
 * The theme only renders forms. Receiving, validating and storing submissions
 * (signed field schema, single-use tokens, rate limits, private file storage)
 * is done by the companion plugin "Larijani Stone Core", so customer data
 * never depends on the active theme.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Hidden inputs every theme form must include.
 *
 * @param string $form_name Human readable form name (stored with the lead).
 * @param array  $fields    Field schema: key => array( label, type, required, options ).
 *                          The plugin signs it so the server enforces it.
 * @return string
 */
function larijani_form_hidden_fields( $form_name, $fields = array() ) {
	if ( function_exists( 'larijani_core_form_fields' ) ) {
		return larijani_core_form_fields( $form_name, $fields );
	}
	// Plugin missing: keep the markup identical; submissions get a clear message.
	return sprintf(
		'<input type="hidden" name="action" value="larijani_lead"><input type="hidden" name="ls_form_name" value="%s">',
		esc_attr( $form_name )
	);
}

/**
 * Without the plugin, answer form posts with a helpful message instead of "0".
 */
function larijani_forms_unavailable() {
	wp_send_json_error(
		array(
			'code'    => 'unavailable',
			'message' => __( 'ارسال فرم در حال حاضر فعال نیست. لطفاً از طریق تلفن با ما تماس بگیرید.', 'larijani-stone' ),
		),
		503
	);
}

/**
 * Register the fallback only when the plugin is not active.
 */
function larijani_forms_fallback() {
	if ( larijani_has_core() ) {
		return;
	}
	foreach ( array( 'larijani_lead', 'ls_lead' ) as $action ) {
		add_action( 'wp_ajax_' . $action, 'larijani_forms_unavailable' );
		add_action( 'wp_ajax_nopriv_' . $action, 'larijani_forms_unavailable' );
	}
}
add_action( 'init', 'larijani_forms_fallback' );
