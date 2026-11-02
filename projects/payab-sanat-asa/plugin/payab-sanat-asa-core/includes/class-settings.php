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
		add_action( 'admin_post_aladdin_payab_repair_menus', array( __CLASS__, 'repair_menus' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
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
			'render_mode'   => isset( $_POST['render_mode'] ) && 'elementor' === $_POST['render_mode'] ? 'elementor' : 'html', // phpcs:ignore WordPress.Security.NonceVerification
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

	public static function assets( $hook ) {
		if ( 'toplevel_page_aladdin-payab' !== $hook ) {
			return;
		}
		wp_enqueue_style( 'aladdin-payab-admin', ALADDIN_PAYAB_SANAT_ASA_CORE_URL . 'assets/admin.css', array(), ALADDIN_PAYAB_SANAT_ASA_CORE_VERSION );
		wp_enqueue_script( 'aladdin-payab-admin', ALADDIN_PAYAB_SANAT_ASA_CORE_URL . 'assets/admin.js', array(), ALADDIN_PAYAB_SANAT_ASA_CORE_VERSION, array( 'in_footer' => true ) );
	}

	public static function repair_menus() {
		self::guard( 'aladdin_payab_menus' );
		$n = Menus::ensure();
		set_transient( 'aladdin_payab_menus_result', count( $n ), 120 );
		self::back( 'menus', 'menus' );
	}

	private static function tabs() {
		return array(
			'overview' => array( __( 'Overview', 'payab-sanat-asa-core' ), 'dashicons-dashboard' ),
			'menus'    => array( __( 'Menus', 'payab-sanat-asa-core' ), 'dashicons-menu-alt3' ),
			'texts'    => array( __( 'Header & footer texts', 'payab-sanat-asa-core' ), 'dashicons-editor-textcolor' ),
			'general'  => array( __( 'General', 'payab-sanat-asa-core' ), 'dashicons-admin-generic' ),
			'import'   => array( __( 'Import / Undo', 'payab-sanat-asa-core' ), 'dashicons-database-import' ),
			'reserved' => array( __( 'Reserved', 'payab-sanat-asa-core' ), 'dashicons-lock' ),
		);
	}

	private static function tab_url( $tab, $extra = array() ) {
		return add_query_arg( array_merge( array( 'page' => 'aladdin-payab', 'tab' => $tab ), $extra ), admin_url( 'admin.php' ) );
	}

	public static function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$tabs = self::tabs();
		$tab  = isset( $_GET['tab'] ) && isset( $tabs[ $_GET['tab'] ] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'overview'; // phpcs:ignore WordPress.Security.NonceVerification
		$logo = ALADDIN_PAYAB_SANAT_ASA_CORE_URL . 'assets/branding/aladdin-theme.png';
		echo '<div class="wrap ap-wrap"><h1 class="screen-reader-text">' . esc_html__( 'Aladdin Payab — Payab Sanat Asa theme', 'payab-sanat-asa-core' ) . '</h1><hr class="wp-header-end"/>';
		echo '<header class="ap-hero"><img class="ap-logo" src="' . esc_url( $logo ) . '" alt="Aladdin Theme" width="56" height="56"/><div class="ap-hero-text"><h2>' . esc_html__( 'Payab Sanat Asa', 'payab-sanat-asa-core' ) . '</h2><p>' . esc_html__( 'Theme settings · three languages · menus · import', 'payab-sanat-asa-core' ) . '</p></div><div class="ap-hero-meta"><span class="ap-pill">v' . esc_html( ALADDIN_PAYAB_SANAT_ASA_CORE_VERSION ) . '</span><a class="ap-pill ap-pill-link" href="' . esc_url( home_url( '/' ) ) . '" target="_blank" rel="noopener">' . esc_html__( 'View site', 'payab-sanat-asa-core' ) . ' ↗</a></div></header>';
		self::notices();
		echo '<nav class="ap-tabs" aria-label="' . esc_attr__( 'Settings sections', 'payab-sanat-asa-core' ) . '">';
		foreach ( $tabs as $k => $t ) {
			printf( '<a class="ap-tab%s" href="%s"%s><span class="dashicons %s" aria-hidden="true"></span>%s</a>', $k === $tab ? ' is-active' : '', esc_url( self::tab_url( $k ) ), $k === $tab ? ' aria-current="page"' : '', esc_attr( $t[1] ), esc_html( $t[0] ) );
		}
		echo '</nav><div class="ap-body">';
		call_user_func( array( __CLASS__, 'tab_' . $tab ) );
		echo '</div></div>';
	}

	private static function notices() {
		$msg = isset( $_GET['aladdin_msg'] ) ? sanitize_key( wp_unslash( $_GET['aladdin_msg'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$map = array(
			'saved'    => array( 'ok', __( 'Settings saved.', 'payab-sanat-asa-core' ) ),
			'reset'    => array( 'ok', __( 'Texts of this language were reset to the approved design (a backup was kept).', 'payab-sanat-asa-core' ) ),
			'confirm'  => array( 'warn', __( 'Please tick the confirmation box first.', 'payab-sanat-asa-core' ) ),
			'imported' => array( 'ok', '' ),
			'undone'   => array( 'ok', '' ),
			'menus'    => array( 'ok', '' ),
		);
		if ( ! isset( $map[ $msg ] ) ) {
			return;
		}
		$text = $map[ $msg ][1];
		if ( 'imported' === $msg ) {
			$r    = get_transient( 'aladdin_payab_import_result' );
			$text = $r ? sprintf( /* translators: 1: created, 2: skipped */ __( 'Import finished: %1$d created, %2$d already existed.', 'payab-sanat-asa-core' ), $r['created'], $r['skipped'] ) : __( 'Import finished.', 'payab-sanat-asa-core' );
			if ( $r && $r['errors'] ) {
				$text .= ' ' . implode( ' | ', $r['errors'] );
			}
		}
		if ( 'undone' === $msg ) {
			/* translators: %d: number of items */
			$text = sprintf( __( 'Undo finished: %d items moved to the Trash.', 'payab-sanat-asa-core' ), (int) get_transient( 'aladdin_payab_undo_result' ) );
		}
		if ( 'menus' === $msg ) {
			/* translators: %d: number of menus */
			$text = sprintf( __( 'Menus checked: %d created.', 'payab-sanat-asa-core' ), (int) get_transient( 'aladdin_payab_menus_result' ) );
		}
		printf( '<div class="ap-notice ap-notice-%s" role="status">%s</div>', esc_attr( $map[ $msg ][0] ), esc_html( $text ) );
	}

	private static function chip( $ok, $text ) {
		return sprintf( '<span class="ap-chip %s">%s</span>', $ok ? 'ok' : 'off', esc_html( $text ) );
	}

	private static function card_open( $title, $desc = '', $class = '' ) {
		echo '<section class="ap-card ' . esc_attr( $class ) . '"><div class="ap-card-head"><h3>' . esc_html( $title ) . '</h3>' . ( $desc ? '<p>' . esc_html( $desc ) . '</p>' : '' ) . '</div><div class="ap-card-body">';
	}

	private static function card_close() {
		echo '</div></section>';
	}

	private static function tab_overview() {
		$j      = Importer::journal();
		$m      = Importer::matrix();
		$totals = array( 'fa' => 0, 'en' => 0, 'ar' => 0 );
		foreach ( $m as $langs ) {
			foreach ( $langs as $l => $v ) {
				if ( $v['id'] ) {
					$totals[ $l ]++;
				}
			}
		}
		$count   = count( $m );
		$ms      = Menus::status();
		$menu_ok = 0;
		foreach ( $ms as $ks ) {
			foreach ( $ks as $v ) {
				$menu_ok += $v['items'] ? 1 : 0;
			}
		}
		echo '<div class="ap-grid ap-grid-3">';
		foreach ( Post_Types::types() as $l => $t ) {
			$pct = $count ? (int) round( 100 * $totals[ $l ] / $count ) : 0;
			printf(
				'<a class="ap-stat" href="%s"><span class="ap-stat-label">%s</span><span class="ap-stat-num">%d<small>/%d</small></span><span class="ap-bar"><i style="width:%d%%"></i></span><span class="ap-stat-foot">%s</span></a>',
				esc_url( admin_url( 'edit.php?post_type=' . $t['slug'] ) ),
				esc_html( $t['label'] ),
				(int) $totals[ $l ],
				(int) $count,
				(int) $pct,
				esc_html__( 'Manage pages', 'payab-sanat-asa-core' )
			);
		}
		echo '</div><div class="ap-grid ap-grid-2">';
		self::card_open( __( 'Status', 'payab-sanat-asa-core' ) );
		echo '<ul class="ap-status">';
		$rows = array(
			array( function_exists( 'aladdin_payab_sanat_asa_lang' ), __( 'Theme "Payab Sanat Asa" active', 'payab-sanat-asa-core' ) ),
			array( did_action( 'elementor/loaded' ), __( 'Elementor (free) active', 'payab-sanat-asa-core' ) . ( defined( 'ELEMENTOR_VERSION' ) ? ' ' . ELEMENTOR_VERSION : '' ) ),
			array( class_exists( 'WooCommerce' ), __( 'WooCommerce active (products)', 'payab-sanat-asa-core' ) ),
			array( 9 === $menu_ok, sprintf( /* translators: %d: count */ __( 'Menus ready: %d of 9', 'payab-sanat-asa-core' ), $menu_ok ) ),
			array( ! empty( $j['created'] ), sprintf( /* translators: %d: count */ __( 'Imported items: %d', 'payab-sanat-asa-core' ), isset( $j['created'] ) ? count( $j['created'] ) : 0 ) ),
		);
		foreach ( $rows as $r ) {
			echo '<li>' . self::chip( (bool) $r[0], $r[0] ? '✔' : '—' ) . '<span>' . esc_html( $r[1] ) . '</span></li>';
		}
		echo '</ul>';
		if ( ! did_action( 'elementor/loaded' ) ) {
			echo '<p class="ap-hint">' . esc_html__( 'Without Elementor the site still shows the exact design; install the free Elementor plugin to edit pages visually.', 'payab-sanat-asa-core' ) . '</p>';
		}
		self::card_close();
		self::card_open( __( 'Quick actions', 'payab-sanat-asa-core' ) );
		echo '<div class="ap-actions">';
		$acts = array(
			array( self::tab_url( 'texts' ), 'dashicons-editor-textcolor', __( 'Edit header & footer texts', 'payab-sanat-asa-core' ) ),
			array( admin_url( 'nav-menus.php' ), 'dashicons-menu-alt3', __( 'Edit menus (Appearance)', 'payab-sanat-asa-core' ) ),
			array( self::tab_url( 'import' ), 'dashicons-database-import', __( 'Import / Undo', 'payab-sanat-asa-core' ) ),
			array( self::tab_url( 'general' ), 'dashicons-admin-generic', __( 'General settings', 'payab-sanat-asa-core' ) ),
		);
		foreach ( $acts as $a ) {
			printf( '<a class="ap-action" href="%s"><span class="dashicons %s" aria-hidden="true"></span>%s</a>', esc_url( $a[0] ), esc_attr( $a[1] ), esc_html( $a[2] ) );
		}
		echo '</div>';
		self::card_close();
		echo '</div>';
		self::matrix_table();
	}

	/** Languages x routes: which language pages exist, with edit links, and a button that creates the missing ones. */
	private static function matrix_table() {
		$m       = Importer::matrix();
		$missing = 0;
		self::card_open( __( 'Languages and pages', 'payab-sanat-asa-core' ), __( 'Every route in every language, with a link to edit it.', 'payab-sanat-asa-core' ) );
		echo '<div class="ap-table-wrap"><table class="ap-table"><thead><tr><th>' . esc_html__( 'Route', 'payab-sanat-asa-core' ) . '</th>';
		foreach ( Post_Types::types() as $l => $t ) {
			printf( '<th>%s</th>', esc_html( $t['label'] ) );
		}
		echo '</tr></thead><tbody>';
		foreach ( $m as $group => $langs ) {
			echo '<tr><td><code>' . esc_html( '' === $group ? '/' : $group ) . '</code></td>';
			foreach ( array( 'fa', 'en', 'ar' ) as $l ) {
				$id = isset( $langs[ $l ]['id'] ) ? (int) $langs[ $l ]['id'] : 0;
				if ( $id ) {
					printf( '<td><span class="ap-dot ok"></span><a href="%s">%s</a></td>', esc_url( get_edit_post_link( $id ) ), esc_html( wp_trim_words( get_the_title( $id ), 5, '…' ) ) );
				} else {
					$missing++;
					echo '<td><span class="ap-dot off"></span><em>' . esc_html__( 'missing', 'payab-sanat-asa-core' ) . '</em></td>';
				}
			}
			echo '</tr>';
		}
		echo '</tbody></table></div>';
		if ( $missing ) {
			echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="ap-inline"><input type="hidden" name="action" value="aladdin_payab_import"/><input type="hidden" name="confirm" value="1"/>';
			wp_nonce_field( 'aladdin_payab_import' );
			/* translators: %d: number of missing pages */
			submit_button( sprintf( __( 'Create the %d missing pages', 'payab-sanat-asa-core' ), $missing ), 'primary', 'submit', false );
			echo '</form>';
		}
		self::card_close();
	}

	private static function tab_menus() {
		$ms = Menus::status();
		self::card_open( __( 'Header and footer links', 'payab-sanat-asa-core' ), __( 'Created automatically with the pages. They are real WordPress menus: edit, reorder or add links in Appearance > Menus; the site prints them in the header and footer.', 'payab-sanat-asa-core' ) );
		echo '<div class="ap-table-wrap"><table class="ap-table"><thead><tr><th>' . esc_html__( 'Language', 'payab-sanat-asa-core' ) . '</th><th>' . esc_html__( 'Menu', 'payab-sanat-asa-core' ) . '</th><th>' . esc_html__( 'Used in', 'payab-sanat-asa-core' ) . '</th><th>' . esc_html__( 'Links', 'payab-sanat-asa-core' ) . '</th><th></th></tr></thead><tbody>';
		$where = array( 'primary' => __( 'Header navigation', 'payab-sanat-asa-core' ), 'footer1' => __( 'Footer: quick links', 'payab-sanat-asa-core' ), 'footer2' => __( 'Footer: useful information', 'payab-sanat-asa-core' ) );
		$bad   = 0;
		foreach ( $ms as $l => $ks ) {
			foreach ( $ks as $k => $v ) {
				$ok = (bool) $v['items'];
				$bad += $ok ? 0 : 1;
				printf(
					'<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
					esc_html( strtoupper( $l ) ),
					esc_html( $v['name'] ),
					esc_html( $where[ $k ] ),
					self::chip( $ok, $ok ? (string) $v['items'] : __( 'missing', 'payab-sanat-asa-core' ) ), // phpcs:ignore WordPress.Security.EscapeOutput
					$v['id'] ? '<a href="' . esc_url( admin_url( 'nav-menus.php?action=edit&menu=' . $v['id'] ) ) . '">' . esc_html__( 'Edit', 'payab-sanat-asa-core' ) . '</a>' : ''
				); // phpcs:ignore WordPress.Security.EscapeOutput
			}
		}
		echo '</tbody></table></div><div class="ap-inline">';
		echo '<a class="button" href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Open Appearance > Menus', 'payab-sanat-asa-core' ) . '</a> ';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="display:inline"><input type="hidden" name="action" value="aladdin_payab_repair_menus"/>';
		wp_nonce_field( 'aladdin_payab_menus' );
		submit_button( $bad ? __( 'Create the missing menus', 'payab-sanat-asa-core' ) : __( 'Check menus', 'payab-sanat-asa-core' ), 'primary', 'submit', false );
		echo '</form></div>';
		echo '<p class="ap-hint">' . esc_html__( 'A location that already has links is never changed. The "Products" link of the header menu carries the class payab-mega (it opens the products mega menu).', 'payab-sanat-asa-core' ) . '</p>';
		self::card_close();
	}

	private static function tab_general() {
		$o  = get_option( self::OPT, array() );
		$q  = get_posts( array( 'post_type' => 'elementor_library', 'post_status' => 'publish', 'posts_per_page' => 100, 'orderby' => 'title', 'order' => 'ASC', 'no_found_rows' => true ) );
		$rm = isset( $o['render_mode'] ) && 'elementor' === $o['render_mode'] ? 'elementor' : 'html';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="aladdin_payab_save_general"/>';
		wp_nonce_field( 'aladdin_payab_general' );
		self::card_open( __( 'Page output', 'payab-sanat-asa-core' ), __( 'How the public site prints the design pages.', 'payab-sanat-asa-core' ) );
		echo '<div class="ap-choice">';
		printf( '<label class="ap-choice-item%s"><input type="radio" name="render_mode" value="html" %s/><strong>%s</strong><span>%s</span></label>', 'html' === $rm ? ' is-on' : '', checked( 'html', $rm, false ), esc_html__( 'Exact design', 'payab-sanat-asa-core' ), esc_html__( 'Recommended. The site shows the approved design markup. Elementor edits do not appear on the site.', 'payab-sanat-asa-core' ) );
		printf( '<label class="ap-choice-item%s"><input type="radio" name="render_mode" value="elementor" %s/><strong>%s</strong><span>%s</span></label>', 'elementor' === $rm ? ' is-on' : '', checked( 'elementor', $rm, false ), esc_html__( 'Elementor output', 'payab-sanat-asa-core' ), esc_html__( 'Edits made in Elementor appear on the site (requires Elementor; check the look after switching).', 'payab-sanat-asa-core' ) );
		echo '</div>';
		self::card_close();
		self::card_open( __( 'Shop and SEO', 'payab-sanat-asa-core' ) );
		printf( '<label class="ap-switch"><input type="checkbox" name="catalog_mode" value="yes" %s/><i></i><span><strong>%s</strong>%s</span></label>', checked( ! isset( $o['catalog_mode'] ) || 'no' !== $o['catalog_mode'], true, false ), esc_html__( 'Catalog mode', 'payab-sanat-asa-core' ), esc_html__( 'Hide prices and block purchasing/cart/checkout (WooCommerce).', 'payab-sanat-asa-core' ) );
		printf( '<label class="ap-switch"><input type="checkbox" name="seo" value="yes" %s/><i></i><span><strong>%s</strong>%s %s</span></label>', checked( ! isset( $o['seo'] ) || 'no' !== $o['seo'], true, false ), esc_html__( 'SEO tags', 'payab-sanat-asa-core' ), esc_html__( 'Print description, canonical, hreflang, Open Graph and Schema.org.', 'payab-sanat-asa-core' ), esc_html__( 'Automatically off when Yoast, Rank Math, SEOPress/TSF or AIOSEO is active.', 'payab-sanat-asa-core' ) );
		self::card_close();
		self::card_open( __( 'Elementor templates for header and footer', 'payab-sanat-asa-core' ), __( 'Optional: replace the shared chrome with an Elementor template (built with free Elementor, no Pro theme builder).', 'payab-sanat-asa-core' ) );
		foreach ( array( 'header_tpl' => __( 'Header', 'payab-sanat-asa-core' ), 'footer_tpl' => __( 'Footer', 'payab-sanat-asa-core' ) ) as $key => $label ) {
			echo '<div class="ap-field-row"><strong>' . esc_html( $label ) . '</strong>';
			foreach ( Languages::codes() as $l ) {
				printf( '<label>%s <select name="%s[%s]"><option value="0">%s</option>', esc_html( strtoupper( $l ) ), esc_attr( $key ), esc_attr( $l ), esc_html__( 'Approved design (default)', 'payab-sanat-asa-core' ) );
				foreach ( $q as $t ) {
					printf( '<option value="%d" %s>%s</option>', (int) $t->ID, selected( isset( $o[ $key ][ $l ] ) ? (int) $o[ $key ][ $l ] : 0, $t->ID, false ), esc_html( $t->post_title ) );
				}
				echo '</select></label>';
			}
			echo '</div>';
		}
		self::card_close();
		self::card_open( __( 'On uninstall', 'payab-sanat-asa-core' ) );
		printf( '<label class="ap-switch"><input type="checkbox" name="remove_data" value="1" %s/><i></i><span><strong>%s</strong>%s</span></label>', checked( (bool) get_option( 'aladdin_payab_sanat_asa_remove_data_on_uninstall' ), true, false ), esc_html__( 'Delete settings', 'payab-sanat-asa-core' ), esc_html__( 'Delete this plugin\'s settings (pages and products are never deleted).', 'payab-sanat-asa-core' ) );
		self::card_close();
		echo '<div class="ap-savebar">';
		submit_button( __( 'Save changes', 'payab-sanat-asa-core' ), 'primary', 'submit', false );
		echo '</div></form>';
	}

	private static function tab_texts() {
		$lang = isset( $_GET['lang'] ) && in_array( $_GET['lang'], Languages::codes(), true ) ? sanitize_key( wp_unslash( $_GET['lang'] ) ) : 'fa'; // phpcs:ignore WordPress.Security.NonceVerification
		$def  = self::defaults( $lang );
		$opt  = get_option( 'aladdin_payab_sanat_asa_chrome', array() );
		$cur  = isset( $opt[ $lang ] ) ? $opt[ $lang ] : array();
		echo '<div class="ap-seg" role="tablist">';
		foreach ( Post_Types::types() as $l => $t ) {
			printf( '<a class="%s" href="%s">%s</a>', $l === $lang ? 'is-active' : '', esc_url( self::tab_url( 'texts', array( 'lang' => $l ) ) ), esc_html( $t['label'] ) );
		}
		echo '</div><p class="ap-hint">' . esc_html__( 'Every text, label and link of the top bar, header, mobile menu, footer and mobile bar. Leave a field empty to keep the approved design text. Links: use "fa:shop-2" style route keys or a full URL. The header and footer link lists come from the Menus tab.', 'payab-sanat-asa-core' ) . '</p>';
		echo '<div class="ap-search"><span class="dashicons dashicons-search" aria-hidden="true"></span><input type="search" id="ap-filter" placeholder="' . esc_attr__( 'Filter texts…', 'payab-sanat-asa-core' ) . '" autocomplete="off"/></div>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="aladdin_payab_save_texts"/><input type="hidden" name="lang" value="' . esc_attr( $lang ) . '"/>';
		wp_nonce_field( 'aladdin_payab_texts' );
		$parts = array( 'pre' => __( 'Top bar, header and mobile drawer', 'payab-sanat-asa-core' ), 'post' => __( 'Footer and mobile bar', 'payab-sanat-asa-core' ) );
		foreach ( $parts as $part => $label ) {
			self::card_open( $label, '', 'ap-card-table' );
			echo '<div class="ap-table-wrap"><table class="ap-table ap-texts"><thead><tr><th style="width:48px">#</th><th style="width:70px">' . esc_html__( 'Type', 'payab-sanat-asa-core' ) . '</th><th>' . esc_html__( 'Approved text', 'payab-sanat-asa-core' ) . '</th><th>' . esc_html__( 'Your text', 'payab-sanat-asa-core' ) . '</th></tr></thead><tbody>';
			foreach ( (array) ( $def[ $part ] ?? array() ) as $i => $row ) {
				$val  = isset( $cur[ $part ][ $i ] ) ? $cur[ $part ][ $i ] : '';
				$kind = array( 't' => 'متن', 'a' => 'ویژگی', 'u' => 'لینک', 'r' => 'لینک' );
				printf( '<tr><td>%d</td><td><span class="ap-tag">%s</span></td><td><code>%s</code></td><td><input class="large-text" type="text" name="t[%s][%d]" value="%s" dir="auto"/></td></tr>', (int) $i, esc_html( $kind[ $row[0] ] ?? $row[0] ), esc_html( $row[1] ), esc_attr( $part ), (int) $i, esc_attr( $val ) );
			}
			echo '</tbody></table></div>';
			self::card_close();
		}
		$sc = function_exists( 'aladdin_payab_sanat_asa_script_strings' ) ? json_decode( (string) file_get_contents( get_template_directory() . '/inc/data/script-strings.json' ), true ) : array(); // phpcs:ignore WordPress.WP_FileSystem.FileGet
		if ( isset( $sc[ $lang ]['mega'] ) ) {
			self::card_open( __( 'Products mega menu (category panel)', 'payab-sanat-asa-core' ), '', 'ap-card-table' );
			echo '<div class="ap-table-wrap"><table class="ap-table ap-texts"><tbody>';
			$labels = array( 'Tab title', 'Description', 'Button', 'Image alt', 'Caption' );
			foreach ( $sc[ $lang ]['mega'] as $key => $vals ) {
				foreach ( array_slice( $vals, 0, 5 ) as $i => $v ) {
					$cv = $cur['mega'][ $key ][ $i ] ?? '';
					printf( '<tr><td>%s · %s</td><td><code>%s</code></td><td><input class="large-text" type="text" name="mega[%s][%d]" value="%s" dir="auto"/></td></tr>', esc_html( $key ), esc_html( $labels[ $i ] ), esc_html( $v ), esc_attr( $key ), (int) $i, esc_attr( $cv ) );
				}
			}
			echo '</tbody></table></div>';
			self::card_close();
		}
		echo '<div class="ap-savebar">';
		submit_button( __( 'Save texts', 'payab-sanat-asa-core' ), 'primary', 'submit', false );
		echo '</div></form>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="ap-inline" onsubmit="return confirm(\'' . esc_js( __( 'Reset all texts of this language to the approved design? A backup is kept.', 'payab-sanat-asa-core' ) ) . '\')"><input type="hidden" name="action" value="aladdin_payab_reset_texts"/><input type="hidden" name="lang" value="' . esc_attr( $lang ) . '"/>';
		wp_nonce_field( 'aladdin_payab_texts' );
		submit_button( __( 'Reset this language to the approved design', 'payab-sanat-asa-core' ), 'secondary', 'submit', false );
		echo '</form>';
	}

	private static function tab_import() {
		$j = Importer::journal();
		self::card_open( __( 'Import pages and products', 'payab-sanat-asa-core' ), __( 'Creates the 66 language pages (22 routes × 3 languages, a separate post type for each language) as Elementor pages with a plain-HTML fallback; products go to WooCommerce when it is active. Only missing pages are created. Existing pages are never overwritten; every created item is journaled and can be moved to the Trash with Undo. The Persian home takes the front page on a fresh site.', 'payab-sanat-asa-core' ) );
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="aladdin_payab_import"/>';
		wp_nonce_field( 'aladdin_payab_import' );
		echo '<label class="ap-switch"><input type="checkbox" name="confirm" value="1"/><i></i><span><strong>' . esc_html__( 'Confirm', 'payab-sanat-asa-core' ) . '</strong>' . esc_html__( 'I understand this creates pages and may set the front page.', 'payab-sanat-asa-core' ) . '</span></label>';
		submit_button( __( 'Import pages and products', 'payab-sanat-asa-core' ), 'primary', 'submit', false );
		echo '</form>';
		self::card_close();
		if ( ! empty( $j['created'] ) ) {
			self::card_open( __( 'Undo', 'payab-sanat-asa-core' ), '', 'ap-danger' );
			/* translators: 1: item count, 2: date */
			echo '<p>' . esc_html( sprintf( __( 'Last import: %1$d items on %2$s.', 'payab-sanat-asa-core' ), count( $j['created'] ), wp_date( 'Y-m-d H:i', (int) $j['time'] ) ) ) . '</p>';
			echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" onsubmit="return confirm(\'' . esc_js( __( 'Move all imported items to the Trash and restore changed options?', 'payab-sanat-asa-core' ) ) . '\')"><input type="hidden" name="action" value="aladdin_payab_undo"/>';
			wp_nonce_field( 'aladdin_payab_undo' );
			submit_button( __( 'Undo the import (move to Trash)', 'payab-sanat-asa-core' ), 'delete', 'submit', false );
			echo '</form>';
			self::card_close();
		}
	}

	private static function tab_reserved() {
		echo '<div class="ap-grid ap-grid-2">';
		foreach ( array(
			__( 'Colour and typography panel (the approved design uses fixed CSS variables)', 'payab-sanat-asa-core' ),
			__( 'Server-side consultation request storage and e-mail (the approved contact form only prepares text and copies it to the clipboard)', 'payab-sanat-asa-core' ),
			__( 'Third-party multilingual plugins (Polylang/WPML) compatibility', 'payab-sanat-asa-core' ),
		) as $r ) {
			echo '<section class="ap-card"><div class="ap-card-body"><span class="ap-chip off">' . esc_html__( 'Reserved', 'payab-sanat-asa-core' ) . '</span><p>' . esc_html( $r ) . '</p></div></section>';
		}
		echo '</div><p class="ap-hint">' . esc_html__( 'Shown for transparency: these options are planned but not active in this version.', 'payab-sanat-asa-core' ) . '</p>';
	}
}
