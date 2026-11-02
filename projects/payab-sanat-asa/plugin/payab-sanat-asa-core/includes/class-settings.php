<?php
/**
 * Settings > Aladdin Payab: Overview, General, Texts (header/footer/mega menu strings), Import, Reserved.
 *
 * @package Aladdin\PayabSanatAsa
 */

namespace Aladdin\PayabSanatAsa;

defined( 'ABSPATH' ) || exit;

class Settings {

	const OPT = 'aladdin_payab_sanat_asa_settings';

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_post_aladdin_payab_save_general', array( __CLASS__, 'save_general' ) );
		add_action( 'admin_post_aladdin_payab_save_texts', array( __CLASS__, 'save_texts' ) );
		add_action( 'admin_post_aladdin_payab_reset_texts', array( __CLASS__, 'reset_texts' ) );
		add_filter( 'admin_footer_text', array( __CLASS__, 'footer' ) );
	}

	public static function menu() {
		add_menu_page(
			__( 'Aladdin Payab', 'payab-sanat-asa-core' ),
			__( 'Aladdin Payab', 'payab-sanat-asa-core' ),
			'manage_options',
			'aladdin-payab',
			array( __CLASS__, 'render' ),
			'dashicons-admin-generic',
			58
		);
	}

	private static function is_screen() {
		return isset( $_GET['page'] ) && 'aladdin-payab' === $_GET['page']; // phpcs:ignore WordPress.Security.NonceVerification
	}

	public static function footer( $text ) {
		if ( self::is_screen() ) {
			return esc_html__( 'Designed and developed by', 'payab-sanat-asa-core' ) . ' <strong>علاءالدین تم (Aladdin Theme)</strong>';
		}
		return $text;
	}

	public static function get( $key, $default = '' ) {
		$o = get_option( self::OPT, array() );
		return isset( $o[ $key ] ) ? $o[ $key ] : $default;
	}

	private static function guard( $action ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'payab-sanat-asa-core' ), 403 );
		}
		check_admin_referer( $action );
	}

	private static function back( $tab, $msg, $extra = array() ) {
		wp_safe_redirect( add_query_arg( array_merge( array( 'page' => 'aladdin-payab', 'tab' => $tab, 'aladdin_msg' => $msg ), $extra ), admin_url( 'admin.php' ) ) );
		exit;
	}

	public static function save_general() {
		self::guard( 'aladdin_payab_general' );
		$yn  = static function ( $k ) {
			return isset( $_POST[ $k ] ) && 'yes' === $_POST[ $k ] ? 'yes' : 'no'; // phpcs:ignore WordPress.Security.NonceVerification -- checked in guard().
		};
		$new = array(
			'catalog_mode'  => $yn( 'catalog_mode' ),
			'seo'           => $yn( 'seo' ),
			'header_tpl'    => array(),
			'footer_tpl'    => array(),
		);
		foreach ( Languages::codes() as $l ) {
			$new['header_tpl'][ $l ] = isset( $_POST['header_tpl'][ $l ] ) ? absint( $_POST['header_tpl'][ $l ] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
			$new['footer_tpl'][ $l ] = isset( $_POST['footer_tpl'][ $l ] ) ? absint( $_POST['footer_tpl'][ $l ] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
		}
		update_option( self::OPT, $new, false );
		update_option( 'aladdin_payab_sanat_asa_remove_data_on_uninstall', isset( $_POST['remove_data'] ) ? 1 : 0, false ); // phpcs:ignore WordPress.Security.NonceVerification
		self::back( 'general', 'saved' );
	}

	/** Defaults come from the theme (single source of the approved strings). */
	private static function defaults( $lang ) {
		return function_exists( 'aladdin_payab_sanat_asa_chrome_defaults' ) ? aladdin_payab_sanat_asa_chrome_defaults( $lang ) : array( 'pre' => array(), 'post' => array() );
	}

	public static function save_texts() {
		self::guard( 'aladdin_payab_texts' );
		$lang = isset( $_POST['lang'] ) && in_array( $_POST['lang'], Languages::codes(), true ) ? sanitize_key( wp_unslash( $_POST['lang'] ) ) : 'fa'; // phpcs:ignore WordPress.Security.NonceVerification
		$def  = self::defaults( $lang );
		$opt  = get_option( 'aladdin_payab_sanat_asa_chrome', array() );
		$new  = array( 'pre' => array(), 'post' => array(), 'mega' => array() );
		foreach ( array( 'pre', 'post' ) as $part ) {
			$posted = isset( $_POST['t'][ $part ] ) && is_array( $_POST['t'][ $part ] ) ? wp_unslash( $_POST['t'][ $part ] ) : array(); // phpcs:ignore WordPress.Security.NonceVerification,WordPress.Security.ValidatedSanitizedInput
			foreach ( (array) ( $def[ $part ] ?? array() ) as $i => $row ) {
				if ( ! isset( $posted[ $i ] ) ) {
					continue;
				}
				$v = trim( (string) $posted[ $i ] );
				if ( '' === $v || $v === $row[1] ) {
					continue;
				}
				if ( in_array( $row[0], array( 'u', 'r' ), true ) ) {
					$v = preg_match( '/^(fa|en|ar):[A-Za-z0-9\-_\/]*$/', $v ) ? $v : ( 0 === strpos( $v, 'tel:' ) || 0 === strpos( $v, 'mailto:' ) ? sanitize_text_field( $v ) : esc_url_raw( $v ) );
				} else {
					$v = sanitize_text_field( $v );
				}
				if ( '' !== $v ) {
					$new[ $part ][ $i ] = $v;
				}
			}
		}
		$posted = isset( $_POST['mega'] ) && is_array( $_POST['mega'] ) ? wp_unslash( $_POST['mega'] ) : array(); // phpcs:ignore WordPress.Security.NonceVerification,WordPress.Security.ValidatedSanitizedInput
		foreach ( $posted as $key => $vals ) {
			if ( ! in_array( $key, array( 'electric', 'manual', 'sling' ), true ) || ! is_array( $vals ) ) {
				continue;
			}
			foreach ( $vals as $i => $v ) {
				$v = sanitize_text_field( $v );
				if ( '' !== $v && in_array( (int) $i, array( 0, 1, 2, 3, 4 ), true ) ) {
					$new['mega'][ $key ][ (int) $i ] = $v;
				}
			}
		}
		$opt[ $lang ] = $new;
		update_option( 'aladdin_payab_sanat_asa_chrome', $opt, false );
		self::back( 'texts', 'saved', array( 'lang' => $lang ) );
	}

	public static function reset_texts() {
		self::guard( 'aladdin_payab_texts' );
		$lang = isset( $_POST['lang'] ) && in_array( $_POST['lang'], Languages::codes(), true ) ? sanitize_key( wp_unslash( $_POST['lang'] ) ) : 'fa'; // phpcs:ignore WordPress.Security.NonceVerification
		$opt  = get_option( 'aladdin_payab_sanat_asa_chrome', array() );
		if ( isset( $opt[ $lang ] ) ) {
			// Backup so the reset can be reverted.
			update_option( 'aladdin_payab_sanat_asa_chrome_backup', array( 'time' => time(), 'lang' => $lang, 'data' => $opt[ $lang ] ), false );
			unset( $opt[ $lang ] );
			update_option( 'aladdin_payab_sanat_asa_chrome', $opt, false );
		}
		self::back( 'texts', 'reset', array( 'lang' => $lang ) );
	}

	private static function tabs() {
		return array(
			'overview' => __( 'Overview', 'payab-sanat-asa-core' ),
			'general'  => __( 'General', 'payab-sanat-asa-core' ),
			'texts'    => __( 'Header & footer texts', 'payab-sanat-asa-core' ),
			'import'   => __( 'Import / Undo', 'payab-sanat-asa-core' ),
			'reserved' => __( 'Reserved', 'payab-sanat-asa-core' ),
		);
	}

	public static function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$tab = isset( $_GET['tab'] ) && isset( self::tabs()[ $_GET['tab'] ] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'overview'; // phpcs:ignore WordPress.Security.NonceVerification
		echo '<div class="wrap aladdin-payab-admin"><h1>' . esc_html__( 'Aladdin Payab — Payab Sanat Asa theme', 'payab-sanat-asa-core' ) . '</h1>';
		echo '<hr class="wp-header-end"/>';
		self::notices();
		echo '<nav class="nav-tab-wrapper">';
		foreach ( self::tabs() as $k => $label ) {
			printf( '<a class="nav-tab %s" href="%s">%s</a>', $k === $tab ? 'nav-tab-active' : '', esc_url( add_query_arg( array( 'page' => 'aladdin-payab', 'tab' => $k ), admin_url( 'admin.php' ) ) ), esc_html( $label ) );
		}
		echo '</nav><div style="margin-top:18px;max-width:1100px">';
		call_user_func( array( __CLASS__, 'tab_' . $tab ) );
		echo '</div></div>';
	}

	private static function notices() {
		$msg = isset( $_GET['aladdin_msg'] ) ? sanitize_key( wp_unslash( $_GET['aladdin_msg'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$map = array(
			'saved'    => array( 'success', __( 'Settings saved.', 'payab-sanat-asa-core' ) ),
			'reset'    => array( 'success', __( 'Texts of this language were reset to the approved design (a backup was kept).', 'payab-sanat-asa-core' ) ),
			'confirm'  => array( 'warning', __( 'Please tick the confirmation box first.', 'payab-sanat-asa-core' ) ),
			'imported' => array( 'success', '' ),
			'undone'   => array( 'success', '' ),
		);
		if ( ! isset( $map[ $msg ] ) ) {
			return;
		}
		$text = $map[ $msg ][1];
		if ( 'imported' === $msg ) {
			$r    = get_transient( 'aladdin_payab_import_result' );
			$text = $r ? sprintf( /* translators: 1: created, 2: skipped */ __( 'Import finished: %1$d created, %2$d already existed.', 'payab-sanat-asa-core' ), $r['created'], $r['skipped'] ) : __( 'Import finished.', 'payab-sanat-asa-core' );
			if ( $r && $r['errors'] ) {
				$text .= ' ' . implode( ' | ', array_map( 'esc_html', $r['errors'] ) );
			}
		}
		if ( 'undone' === $msg ) {
			/* translators: %d: number of items */
			$text = sprintf( __( 'Undo finished: %d items moved to the Trash.', 'payab-sanat-asa-core' ), (int) get_transient( 'aladdin_payab_undo_result' ) );
		}
		printf( '<div class="notice notice-%s is-dismissible"><p>%s</p></div>', esc_attr( $map[ $msg ][0] ), esc_html( $text ) );
	}

	private static function tab_overview() {
		$logo = ALADDIN_PAYAB_SANAT_ASA_CORE_URL . 'assets/branding/aladdin-theme-dark.png';
		$j    = Importer::journal();
		$theme_ok = function_exists( 'aladdin_payab_sanat_asa_lang' );
		echo '<div class="card" style="max-width:none"><h2>' . esc_html__( 'Status', 'payab-sanat-asa-core' ) . '</h2><table class="widefat striped" style="max-width:640px"><tbody>';
		$rows = array(
			__( 'Theme "Payab Sanat Asa" active', 'payab-sanat-asa-core' )         => $theme_ok ? '✔' : '✖',
			__( 'Elementor (free) active', 'payab-sanat-asa-core' )                 => did_action( 'elementor/loaded' ) ? '✔ ' . ( defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '' ) : '✖ (' . __( 'pages still show using the saved HTML', 'payab-sanat-asa-core' ) . ')',
			__( 'WooCommerce active (products)', 'payab-sanat-asa-core' )           => class_exists( 'WooCommerce' ) ? '✔' : '— (' . __( 'product pages are created as pages', 'payab-sanat-asa-core' ) . ')',
			__( 'Imported items', 'payab-sanat-asa-core' )                           => isset( $j['created'] ) ? count( $j['created'] ) : 0,
		);
		foreach ( $rows as $k => $v ) {
			printf( '<tr><td>%s</td><td>%s</td></tr>', esc_html( $k ), esc_html( (string) $v ) );
		}
		echo '</tbody></table></div>';
		echo '<div class="card" style="max-width:none;display:flex;gap:18px;align-items:center"><img src="' . esc_url( $logo ) . '" alt="Aladdin Theme" style="height:48px"/><div><h2 style="margin:0">علاءالدین تم (Aladdin Theme)</h2><p>' . esc_html__( 'Theme and companion plugin for Payab Sanat Asa. Version', 'payab-sanat-asa-core' ) . ' ' . esc_html( ALADDIN_PAYAB_SANAT_ASA_CORE_VERSION ) . '</p></div></div>';
	}

	private static function tab_general() {
		$o = get_option( self::OPT, array() );
		$q = get_posts( array( 'post_type' => 'elementor_library', 'post_status' => 'publish', 'posts_per_page' => 100, 'orderby' => 'title', 'order' => 'ASC', 'no_found_rows' => true ) );
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="aladdin_payab_save_general"/>';
		wp_nonce_field( 'aladdin_payab_general' );
		echo '<table class="form-table" role="presentation"><tbody>';
		printf( '<tr><th>%s</th><td><label><input type="checkbox" name="catalog_mode" value="yes" %s/> %s</label></td></tr>', esc_html__( 'Catalog mode', 'payab-sanat-asa-core' ), checked( ! isset( $o['catalog_mode'] ) || 'no' !== $o['catalog_mode'], true, false ), esc_html__( 'Hide prices and block purchasing/cart/checkout (WooCommerce).', 'payab-sanat-asa-core' ) );
		printf( '<tr><th>%s</th><td><label><input type="checkbox" name="seo" value="yes" %s/> %s</label><p class="description">%s</p></td></tr>', esc_html__( 'SEO tags', 'payab-sanat-asa-core' ), checked( ! isset( $o['seo'] ) || 'no' !== $o['seo'], true, false ), esc_html__( 'Print description, canonical, hreflang, Open Graph and Schema.org.', 'payab-sanat-asa-core' ), esc_html__( 'Automatically off when Yoast, Rank Math, SEOPress/TSF or AIOSEO is active.', 'payab-sanat-asa-core' ) );
		foreach ( array( 'header_tpl' => __( 'Header', 'payab-sanat-asa-core' ), 'footer_tpl' => __( 'Footer', 'payab-sanat-asa-core' ) ) as $key => $label ) {
			echo '<tr><th>' . esc_html( $label ) . ' (Elementor)</th><td>';
			foreach ( Languages::codes() as $l ) {
				printf( '<label style="display:inline-block;margin-inline-end:18px">%s <select name="%s[%s]"><option value="0">%s</option>', esc_html( strtoupper( $l ) ), esc_attr( $key ), esc_attr( $l ), esc_html__( 'Approved design (default)', 'payab-sanat-asa-core' ) );
				foreach ( $q as $t ) {
					printf( '<option value="%d" %s>%s</option>', (int) $t->ID, selected( isset( $o[ $key ][ $l ] ) ? (int) $o[ $key ][ $l ] : 0, $t->ID, false ), esc_html( $t->post_title ) );
				}
				echo '</select></label>';
			}
			echo '<p class="description">' . esc_html__( 'Optional: replace the shared chrome with an Elementor template (built with free Elementor, no Pro theme builder).', 'payab-sanat-asa-core' ) . '</p></td></tr>';
		}
		printf( '<tr><th>%s</th><td><label><input type="checkbox" name="remove_data" value="1" %s/> %s</label></td></tr>', esc_html__( 'On uninstall', 'payab-sanat-asa-core' ), checked( (bool) get_option( 'aladdin_payab_sanat_asa_remove_data_on_uninstall' ), true, false ), esc_html__( 'Delete this plugin\'s settings (pages and products are never deleted).', 'payab-sanat-asa-core' ) );
		echo '</tbody></table>';
		submit_button();
		echo '</form>';
	}

	private static function tab_texts() {
		$lang = isset( $_GET['lang'] ) && in_array( $_GET['lang'], Languages::codes(), true ) ? sanitize_key( wp_unslash( $_GET['lang'] ) ) : 'fa'; // phpcs:ignore WordPress.Security.NonceVerification
		$def  = self::defaults( $lang );
		$opt  = get_option( 'aladdin_payab_sanat_asa_chrome', array() );
		$cur  = isset( $opt[ $lang ] ) ? $opt[ $lang ] : array();
		echo '<p>';
		foreach ( Languages::codes() as $l ) {
			printf( '<a class="button %s" href="%s">%s</a> ', $l === $lang ? 'button-primary' : '', esc_url( add_query_arg( array( 'page' => 'aladdin-payab', 'tab' => 'texts', 'lang' => $l ), admin_url( 'admin.php' ) ) ), esc_html( strtoupper( $l ) ) );
		}
		echo '</p><p class="description">' . esc_html__( 'Every text, label and link of the top bar, header, mobile menu, footer and mobile bar. Leave a field empty to keep the approved design text. Links: use "fa:shop-2" style route keys or a full URL.', 'payab-sanat-asa-core' ) . '</p>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="aladdin_payab_save_texts"/><input type="hidden" name="lang" value="' . esc_attr( $lang ) . '"/>';
		wp_nonce_field( 'aladdin_payab_texts' );
		$parts = array( 'pre' => __( 'Top bar, header and mobile drawer', 'payab-sanat-asa-core' ), 'post' => __( 'Footer and mobile bar', 'payab-sanat-asa-core' ) );
		foreach ( $parts as $part => $label ) {
			echo '<h2>' . esc_html( $label ) . '</h2><table class="widefat striped"><thead><tr><th style="width:48px">#</th><th style="width:60px">' . esc_html__( 'Type', 'payab-sanat-asa-core' ) . '</th><th>' . esc_html__( 'Approved text', 'payab-sanat-asa-core' ) . '</th><th>' . esc_html__( 'Your text', 'payab-sanat-asa-core' ) . '</th></tr></thead><tbody>';
			foreach ( (array) ( $def[ $part ] ?? array() ) as $i => $row ) {
				$val = isset( $cur[ $part ][ $i ] ) ? $cur[ $part ][ $i ] : '';
				$kind = array( 't' => 'متن', 'a' => 'ویژگی', 'u' => 'لینک', 'r' => 'لینک' );
				printf( '<tr><td>%d</td><td>%s</td><td><code style="white-space:normal">%s</code></td><td><input class="large-text" type="text" name="t[%s][%d]" value="%s" dir="auto"/></td></tr>', (int) $i, esc_html( $kind[ $row[0] ] ?? $row[0] ), esc_html( $row[1] ), esc_attr( $part ), (int) $i, esc_attr( $val ) );
			}
			echo '</tbody></table>';
		}
		$sc = function_exists( 'aladdin_payab_sanat_asa_script_strings' ) ? json_decode( (string) file_get_contents( get_template_directory() . '/inc/data/script-strings.json' ), true ) : array(); // phpcs:ignore WordPress.WP_FileSystem.FileGet
		if ( isset( $sc[ $lang ]['mega'] ) ) {
			echo '<h2>' . esc_html__( 'Products mega menu (category panel)', 'payab-sanat-asa-core' ) . '</h2><table class="widefat striped"><tbody>';
			$labels = array( 'Tab title', 'Description', 'Button', 'Image alt', 'Caption' );
			foreach ( $sc[ $lang ]['mega'] as $key => $vals ) {
				foreach ( array_slice( $vals, 0, 5 ) as $i => $v ) {
					$cv = $cur['mega'][ $key ][ $i ] ?? '';
					printf( '<tr><td>%s · %s</td><td><code style="white-space:normal">%s</code></td><td><input class="large-text" type="text" name="mega[%s][%d]" value="%s" dir="auto"/></td></tr>', esc_html( $key ), esc_html( $labels[ $i ] ), esc_html( $v ), esc_attr( $key ), (int) $i, esc_attr( $cv ) );
				}
			}
			echo '</tbody></table>';
		}
		submit_button( __( 'Save texts', 'payab-sanat-asa-core' ) );
		echo '</form>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" onsubmit="return confirm(\'' . esc_js( __( 'Reset all texts of this language to the approved design? A backup is kept.', 'payab-sanat-asa-core' ) ) . '\')"><input type="hidden" name="action" value="aladdin_payab_reset_texts"/><input type="hidden" name="lang" value="' . esc_attr( $lang ) . '"/>';
		wp_nonce_field( 'aladdin_payab_texts' );
		submit_button( __( 'Reset this language to the approved design', 'payab-sanat-asa-core' ), 'secondary' );
		echo '</form>';
	}

	private static function tab_import() {
		$j = Importer::journal();
		echo '<p>' . esc_html__( 'Creates the 66 pages (22 routes × 3 languages) as Elementor pages with a plain-HTML fallback, and the 3 products × 3 languages. Existing pages are never overwritten; every created item is journaled and can be moved to the Trash with Undo. The Persian home becomes the static front page.', 'payab-sanat-asa-core' ) . '</p>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="aladdin_payab_import"/>';
		wp_nonce_field( 'aladdin_payab_import' );
		echo '<p><label><input type="checkbox" name="confirm" value="1"/> ' . esc_html__( 'I understand this creates pages and may set the front page.', 'payab-sanat-asa-core' ) . '</label></p>';
		submit_button( __( 'Import pages and products', 'payab-sanat-asa-core' ) );
		echo '</form>';
		if ( ! empty( $j['created'] ) ) {
			/* translators: 1: item count, 2: date */
			echo '<p>' . esc_html( sprintf( __( 'Last import: %1$d items on %2$s.', 'payab-sanat-asa-core' ), count( $j['created'] ), wp_date( 'Y-m-d H:i', (int) $j['time'] ) ) ) . '</p>';
			echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" onsubmit="return confirm(\'' . esc_js( __( 'Move all imported items to the Trash and restore changed options?', 'payab-sanat-asa-core' ) ) . '\')"><input type="hidden" name="action" value="aladdin_payab_undo"/>';
			wp_nonce_field( 'aladdin_payab_undo' );
			submit_button( __( 'Undo the import (move to Trash)', 'payab-sanat-asa-core' ), 'delete' );
			echo '</form>';
		}
	}

	private static function tab_reserved() {
		echo '<p>' . esc_html__( 'Shown for transparency: these options are planned but not active in this version.', 'payab-sanat-asa-core' ) . '</p><ul class="ul-disc">';
		foreach ( array(
			__( 'Colour and typography panel (the approved design uses fixed CSS variables)', 'payab-sanat-asa-core' ),
			__( 'Server-side consultation request storage and e-mail (the approved contact form only prepares text and copies it to the clipboard)', 'payab-sanat-asa-core' ),
			__( 'Third-party multilingual plugins (Polylang/WPML) compatibility', 'payab-sanat-asa-core' ),
		) as $r ) {
			echo '<li>' . esc_html( $r ) . ' — <em>' . esc_html__( 'Reserved', 'payab-sanat-asa-core' ) . '</em></li>';
		}
		echo '</ul>';
	}
}
