<?php
/**
 * Plugin Name:       Payab Sanat Asa Core
 * Description:       افزونه همراه قالب «پایاب صنعت آسا»: چندزبانگی سه‌زبانه (فا/EN/عربی)، ویجت‌های المنتور رایگان، ایمپورت برگشت‌پذیر ۶۶ صفحه (هر زبان یک پست‌تایپ جدا) و محصولات، حالت کاتالوگ ووکامرس، سئو (hreflang/Schema) و پنل ویرایش متن‌های هدر و فوتر.
 * Version:           1.3.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            علاءالدین تم (Aladdin Theme)
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       payab-sanat-asa-core
 * Domain Path:       /languages
 *
 * Copyright (C) 2026 Aladdin Theme (علاءالدین تم)
 *
 * @package Aladdin\PayabSanatAsa
 */

defined( 'ABSPATH' ) || exit;

define( 'ALADDIN_PAYAB_SANAT_ASA_CORE_VERSION', '1.3.0' );
define( 'ALADDIN_PAYAB_SANAT_ASA_CORE_FILE', __FILE__ );
define( 'ALADDIN_PAYAB_SANAT_ASA_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'ALADDIN_PAYAB_SANAT_ASA_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once __DIR__ . '/includes/class-post-types.php';
require_once __DIR__ . '/includes/class-languages.php';
require_once __DIR__ . '/includes/class-migrations.php';
require_once __DIR__ . '/includes/class-seo.php';
require_once __DIR__ . '/includes/class-render-mode.php';
require_once __DIR__ . '/includes/class-menus.php';
require_once __DIR__ . '/includes/class-products.php';
require_once __DIR__ . '/includes/class-importer.php';
require_once __DIR__ . '/includes/class-settings.php';
require_once __DIR__ . '/includes/class-theme-builder.php';
require_once __DIR__ . '/includes/elementor/class-elementor.php';

add_action(
	'plugins_loaded',
	static function () {
		load_plugin_textdomain( 'payab-sanat-asa-core', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		\Aladdin\PayabSanatAsa\Post_Types::init();
		\Aladdin\PayabSanatAsa\Languages::init();
		\Aladdin\PayabSanatAsa\Migrations::init();
		\Aladdin\PayabSanatAsa\Seo::init();
		\Aladdin\PayabSanatAsa\Render_Mode::init();
		\Aladdin\PayabSanatAsa\Products::init();
		\Aladdin\PayabSanatAsa\Settings::init();
		\Aladdin\PayabSanatAsa\Importer::init();
		\Aladdin\PayabSanatAsa\Theme_Builder::init();
		\Aladdin\PayabSanatAsa\Elementor_Integration::init();
	}
);

register_activation_hook(
	__FILE__,
	static function () {
		// Never change an existing site on activation: only remember the version and flush rewrites.
		update_option( 'aladdin_payab_sanat_asa_core_version', ALADDIN_PAYAB_SANAT_ASA_CORE_VERSION, false );
		set_transient( 'aladdin_payab_sanat_asa_flush', 1, 60 );
		// Fresh sites get the pages automatically on the next admin request; existing sites are sent to the Import tab.
		update_option( 'aladdin_payab_sanat_asa_autoimport', 1, false );
	}
);
