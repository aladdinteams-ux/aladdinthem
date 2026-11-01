<?php
/**
 * Header: <head> + shared chrome (top bar, header, mega menu, mobile drawer).
 *
 * @package PayabSanatAsa
 */

defined( 'ABSPATH' ) || exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?> data-header-style="03-floating-glass" data-theme="light">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php aladdin_payab_sanat_asa_chrome( 'pre' ); ?>
<main id="content">
