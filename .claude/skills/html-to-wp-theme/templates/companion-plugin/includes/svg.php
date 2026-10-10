<?php
/**
 * Safe SVG uploads for administrators (logos and icons).
 *
 * Theme 1.3.x allowed raw SVG uploads for administrators. SVG can carry
 * scripts, so every file is now rebuilt from an allow-list before it is saved.
 *
 * @package Larijani_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the current user may upload SVG.
 *
 * @return bool
 */
function larijani_core_svg_allowed() {
	return get_option( 'larijani_core_allow_svg', 1 ) && current_user_can( 'manage_options' ) && class_exists( 'DOMDocument' );
}

/**
 * Add the SVG mime type for administrators.
 *
 * @param array $mimes Mimes.
 * @return array
 */
function larijani_core_upload_mimes( $mimes ) {
	if ( larijani_core_svg_allowed() ) {
		$mimes['svg'] = 'image/svg+xml';
	}
	return $mimes;
}
add_filter( 'upload_mimes', 'larijani_core_upload_mimes' );

/**
 * Let WordPress accept sanitised SVG files whose detected type differs.
 *
 * @param array  $data File data.
 * @param string $file Path.
 * @param string $filename Name.
 * @return array
 */
function larijani_core_svg_filetype( $data, $file, $filename ) {
	if ( larijani_core_svg_allowed() && 'svg' === strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) ) ) {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}
	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'larijani_core_svg_filetype', 10, 3 );

/**
 * Sanitise SVG files before WordPress stores them.
 *
 * @param array $file Upload.
 * @return array
 */
function larijani_core_svg_prefilter( $file ) {
	if ( empty( $file['name'] ) || 'svg' !== strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) ) ) {
		return $file;
	}
	if ( ! larijani_core_svg_allowed() ) {
		$file['error'] = __( 'بارگذاری SVG مجاز نیست.', 'larijani-stone-core' );
		return $file;
	}
	$raw   = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$clean = false === $raw ? false : larijani_core_sanitize_svg( $raw );
	if ( false === $clean ) {
		$file['error'] = __( 'فایل SVG معتبر نیست یا شامل محتوای ناامن است.', 'larijani-stone-core' );
		return $file;
	}
	file_put_contents( $file['tmp_name'], $clean ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	$file['size'] = strlen( $clean );
	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'larijani_core_svg_prefilter' );

/**
 * Rebuild an SVG from an allow-list.
 *
 * @param string $svg Markup.
 * @return string|false
 */
function larijani_core_sanitize_svg( $svg ) {
	if ( strlen( $svg ) > 2 * MB_IN_BYTES || preg_match( '/<!ENTITY|<!DOCTYPE[^>]*\[/i', $svg ) ) {
		return false;
	}
	if ( 0 === strncmp( $svg, "\x1f\x8b", 2 ) ) {
		return false; // Compressed SVGZ is not accepted.
	}
	$elements = apply_filters( 'larijani_core_svg_elements', array( 'svg', 'g', 'path', 'rect', 'circle', 'ellipse', 'line', 'polyline', 'polygon', 'text', 'tspan', 'textpath', 'defs', 'lineargradient', 'radialgradient', 'stop', 'clippath', 'mask', 'pattern', 'symbol', 'use', 'title', 'desc', 'style', 'filter', 'fegaussianblur', 'feoffset', 'feblend', 'fecolormatrix', 'fecomposite', 'feflood', 'femerge', 'femergenode', 'image', 'marker' ) );
	$attrs    = apply_filters( 'larijani_core_svg_attributes', array( 'id', 'class', 'style', 'd', 'x', 'y', 'x1', 'x2', 'y1', 'y2', 'cx', 'cy', 'r', 'rx', 'ry', 'width', 'height', 'viewbox', 'preserveaspectratio', 'fill', 'fill-opacity', 'fill-rule', 'clip-rule', 'clip-path', 'mask', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-opacity', 'opacity', 'transform', 'points', 'offset', 'stop-color', 'stop-opacity', 'gradientunits', 'gradienttransform', 'patternunits', 'patterntransform', 'font-family', 'font-size', 'font-weight', 'text-anchor', 'dominant-baseline', 'letter-spacing', 'href', 'xlink:href', 'xmlns', 'xmlns:xlink', 'version', 'fx', 'fy', 'stddeviation', 'dx', 'dy', 'in', 'in2', 'result', 'mode', 'values', 'type', 'operator', 'flood-color', 'flood-opacity', 'filterunits', 'markerwidth', 'markerheight', 'refx', 'refy', 'orient', 'direction', 'display', 'visibility', 'xml:space', 'role', 'aria-hidden', 'aria-label', 'focusable' ) );

	$prev = libxml_use_internal_errors( true );
	$doc  = new DOMDocument();
	$ok   = $doc->loadXML( $svg, LIBXML_NONET | LIBXML_NOBLANKS );
	libxml_clear_errors();
	libxml_use_internal_errors( $prev );
	if ( ! $ok || ! $doc->documentElement || 'svg' !== strtolower( $doc->documentElement->localName ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		return false;
	}
	foreach ( iterator_to_array( $doc->childNodes ) as $node ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		if ( XML_DOCUMENT_TYPE_NODE === $node->nodeType || XML_PI_NODE === $node->nodeType ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$doc->removeChild( $node );
		}
	}
	$walk = function ( $node ) use ( &$walk, $elements, $attrs ) {
		foreach ( iterator_to_array( $node->childNodes ) as $child ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			if ( XML_ELEMENT_NODE === $child->nodeType ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				if ( ! in_array( strtolower( $child->localName ), $elements, true ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$node->removeChild( $child );
					continue;
				}
				foreach ( iterator_to_array( $child->attributes ) as $attr ) {
					$name  = strtolower( $attr->nodeName ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$value = preg_replace( '/[\x00-\x20]+/', '', strtolower( html_entity_decode( $attr->value, ENT_QUOTES ) ) );
					$bad   = ! in_array( $name, $attrs, true ) || 0 === strpos( $name, 'on' )
						|| ( in_array( $name, array( 'href', 'xlink:href' ), true ) && 0 !== strpos( $value, '#' ) && ! preg_match( '#^data:image/(png|jpe?g|gif|webp);base64,#', $value ) )
						|| preg_match( '/javascript:|vbscript:|data:text|expression\(|@import|url\((?!\s*[\'"]?#)/', $value );
					if ( $bad ) {
						$child->removeAttributeNode( $attr );
					}
				}
				if ( 'style' === strtolower( $child->localName ) && preg_match( '/@import|javascript:|expression\(|url\((?!\s*[\'"]?#)/i', $child->textContent ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$node->removeChild( $child );
					continue;
				}
				$walk( $child );
			} elseif ( in_array( $child->nodeType, array( XML_COMMENT_NODE, XML_PI_NODE, XML_ENTITY_REF_NODE ), true ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				$node->removeChild( $child );
			}
		}
	};
	$root = $doc->documentElement; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	foreach ( iterator_to_array( $root->attributes ) as $attr ) {
		if ( 0 === strpos( strtolower( $attr->nodeName ), 'on' ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$root->removeAttributeNode( $attr );
		}
	}
	$walk( $root );
	$out = $doc->saveXML( $root );
	return $out ? '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $out : false;
}
