<?php
/**
 * Payab Sanat Asa theme bootstrap.
 *
 * @package PayabSanatAsa
 */

defined( 'ABSPATH' ) || exit;

define( 'ALADDIN_PAYAB_SANAT_ASA_VERSION', '1.2.1' );
define( 'ALADDIN_PAYAB_SANAT_ASA_DIR', get_template_directory() );
define( 'ALADDIN_PAYAB_SANAT_ASA_URI', get_template_directory_uri() );

require_once ALADDIN_PAYAB_SANAT_ASA_DIR . '/inc/routes.php';
require_once ALADDIN_PAYAB_SANAT_ASA_DIR . '/inc/setup.php';
require_once ALADDIN_PAYAB_SANAT_ASA_DIR . '/inc/chrome.php';
require_once ALADDIN_PAYAB_SANAT_ASA_DIR . '/inc/assets.php';
require_once ALADDIN_PAYAB_SANAT_ASA_DIR . '/inc/content-filters.php';
require_once ALADDIN_PAYAB_SANAT_ASA_DIR . '/inc/companion.php';
