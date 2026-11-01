<?php
/**
 * Styles and scripts. The approved CSS is bundled as core + one family bundle per page type.
 *
 * @package PayabSanatAsa
 */

defined( 'ABSPATH' ) || exit;

/** Page family + script list for the current route (inc/data/asset-map.json). */
function aladdin_payab_sanat_asa_page_assets() {
	static $map = null;
	if ( null === $map ) {
		$file = ALADDIN_PAYAB_SANAT_ASA_DIR . '/inc/data/asset-map.json';
		$map  = is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : array(); // phpcs:ignore WordPress.WP_FileSystem.FileGet
	}
	$group = is_singular() ? (string) get_post_meta( get_queried_object_id(), '_aladdin_payab_group', true ) : '__other';
	if ( isset( $map[ $group ] ) ) {
		return $map[ $group ];
	}
	return array( 'family' => 'inner', 'js' => $map['about-us-2']['js'] ?? array() );
}

add_action(
	'wp_enqueue_scripts',
	static function () {
		$ver = ALADDIN_PAYAB_SANAT_ASA_VERSION;
		$u   = ALADDIN_PAYAB_SANAT_ASA_URI . '/assets/';
		$dep = wp_style_is( 'elementor-frontend', 'registered' ) ? array( 'elementor-frontend' ) : array();
		$set = aladdin_payab_sanat_asa_page_assets();

		wp_enqueue_style( 'aladdin-payab-sanat-asa-fonts', $u . 'css/fonts.css', array(), $ver );
		wp_enqueue_style( 'aladdin-payab-sanat-asa-icons', $u . 'css/bootstrap-icons.css', array(), $ver );
		wp_enqueue_style( 'aladdin-payab-sanat-asa-core', $u . 'css/site-core.css', array_merge( $dep, array( 'aladdin-payab-sanat-asa-fonts' ) ), $ver );
		$prev = 'aladdin-payab-sanat-asa-core';
		if ( 'home' !== $set['family'] ) {
			wp_enqueue_style( 'aladdin-payab-sanat-asa-family', $u . 'css/site-' . $set['family'] . '.css', array( $prev ), $ver );
			$prev = 'aladdin-payab-sanat-asa-family';
		}
		wp_enqueue_style( 'aladdin-payab-sanat-asa-final', $u . 'css/site-final.css', array( $prev ), $ver );
		wp_enqueue_style( 'aladdin-payab-sanat-asa-elementor-bridge', $u . 'css/elementor-bridge.css', array( 'aladdin-payab-sanat-asa-final' ), $ver );

		$lang = aladdin_payab_sanat_asa_lang();
		wp_enqueue_script( 'aladdin-payab-sanat-asa-site', $u . 'js/payab-site.js', array(), $ver, array( 'in_footer' => true, 'strategy' => 'defer' ) );
		$sc = aladdin_payab_sanat_asa_script_strings( $lang );
		wp_add_inline_script(
			'aladdin-payab-sanat-asa-site',
			'window.PayabAsa=' . wp_json_encode(
				array(
					'assets'    => untrailingslashit( $u ),
					'mega'      => $sc['mega'] ?? new stdClass(),
					'favAdd'    => $sc['favAdd'] ?? '',
					'favRemove' => $sc['favRemove'] ?? '',
					'lang'      => $lang,
				)
			) . ';',
			'before'
		);
		foreach ( $set['js'] as $js ) {
			wp_enqueue_script( 'aladdin-payab-sanat-asa-' . sanitize_key( basename( $js, '.js' ) ), $u . 'js/' . $js, array(), $ver, array( 'in_footer' => true, 'strategy' => 'defer' ) );
		}
	},
	20
);

/** Dark-mode bootstrap (same one-liner as the approved HTML) in <head>, before paint. */
add_action(
	'wp_head',
	static function () {
		echo "<script>try{if(localStorage.getItem('payab-theme')==='dark')document.documentElement.dataset.theme='dark'}catch(e){}</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<meta name="theme-color" content="#0E2E4C"/>' . "\n";
	},
	1
);

/** Preload the LCP font of the active language. */
add_action(
	'wp_head',
	static function () {
		$f = 'en' === aladdin_payab_sanat_asa_lang() ? 'manrope-latin-600-normal.woff2' : ( 'ar' === aladdin_payab_sanat_asa_lang() ? 'cairo-arabic-600-normal.woff2' : 'Vazirmatn-var.woff2' );
		printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin/>' . "\n", esc_url( ALADDIN_PAYAB_SANAT_ASA_URI . '/assets/fonts/' . $f ) );
	},
	2
);
