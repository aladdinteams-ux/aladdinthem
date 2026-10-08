<?php
/**
 * Native Elementor layouts.
 *
 * Static design sections (section headings, icon cards, about + stats,
 * testimonials, call-to-action bands, process steps, page banner, contact
 * cards) are built from Elementor's own Container + core widgets (Heading,
 * Text Editor, Button, Icon, Image, Icon List, Star Rating) so every text,
 * icon, link and image is edited with Elementor's native controls.
 *
 * Styling: layout values (direction, gaps, grid columns, padding, widths) are
 * real Elementor settings; colours/typography come from Tailwind utility
 * classes in "CSS Classes" (scoped by .ls-root, specificity 0-2-0/0-3-0), so
 * anything changed in Elementor's Style tab (0-4-0) still wins.
 *
 * Data-driven or interactive parts (header/footer, product catalog, product
 * page, posts, forms that store leads, calculators, sliders, FAQ schema) stay
 * theme widgets — they are also fully editable in Elementor.
 *
 * Must stay free of WordPress calls (used by build/export-templates.php).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Low level builders.
 * ---------------------------------------------------------------------- */

/**
 * Icon setting from shorthand ("shield-check", "bi bi-x" or array).
 *
 * @param mixed $v Value.
 * @return array
 */
function larijani_n_icon( $v ) {
	if ( is_array( $v ) ) {
		return $v;
	}
	$v = trim( (string) $v );
	if ( '' === $v ) {
		return array( 'value' => '', 'library' => '' );
	}
	return array( 'value' => false === strpos( $v, ' ' ) ? 'bi bi-' . $v : $v, 'library' => 'bootstrap-icons' );
}

/**
 * Link setting from shorthand.
 *
 * @param mixed $v Value.
 * @return array
 */
function larijani_n_link( $v ) {
	$l = is_array( $v ) ? $v : array( 'url' => (string) $v );
	$l += array( 'url' => '', 'is_external' => '', 'nofollow' => '' );
	if ( '' === $l['is_external'] && preg_match( '#^https?://#', $l['url'] ) && false === strpos( $l['url'], '{{home}}' ) ) {
		$l['is_external'] = 'on';
	}
	return $l;
}

/**
 * Pixel dimensions (padding / margin).
 *
 * @param int      $t Top.
 * @param int|null $r Right (defaults to top).
 * @param int|null $b Bottom (defaults to top).
 * @param int|null $l Left (defaults to right).
 * @return array
 */
function larijani_n_box( $t, $r = null, $b = null, $l = null ) {
	$r = null === $r ? $t : $r;
	$b = null === $b ? $t : $b;
	$l = null === $l ? $r : $l;
	return array(
		'unit'     => 'px',
		'top'      => (string) $t,
		'right'    => (string) $r,
		'bottom'   => (string) $b,
		'left'     => (string) $l,
		'isLinked' => ( $t === $r && $r === $b && $b === $l ),
	);
}

/**
 * Set a (possibly responsive) setting: $v is a value or [desktop, tablet, mobile].
 *
 * @param array    $s   Settings (by reference).
 * @param string   $key Setting key.
 * @param mixed    $v   Value(s).
 * @param callable $fn  Converter for each value.
 */
function larijani_n_resp( &$s, $key, $v, $fn ) {
	$vals = is_array( $v ) && array_keys( $v ) === array_keys( array_values( $v ) ) && count( $v ) <= 3 && ! isset( $v['unit'] ) ? $v : array( $v );
	foreach ( array( '', '_tablet', '_mobile' ) as $i => $suffix ) {
		if ( array_key_exists( $i, $vals ) && null !== $vals[ $i ] ) {
			$s[ $key . $suffix ] = call_user_func( $fn, $vals[ $i ] );
		}
	}
}

/**
 * Width of a 12-column grid span in a flex row with the given gap (same box
 * as Tailwind's col-span-N with gap-*).
 *
 * @param int $span Columns (1–12).
 * @param int $gap  Gap in px.
 * @return string CSS width.
 */
function larijani_n_span( $span, $gap ) {
	return sprintf( 'calc(%s%% - %spx)', round( $span / 12 * 100, 3 ), round( ( 12 - $span ) * $gap / 12, 2 ) );
}

/**
 * Container element.
 *
 * Options: dir, gap (int|[col,row]) – both responsive [d,t,m]; pad (ls_n_box or
 * [d,t,m] of them); boxed (inner max width px); grid (columns, responsive);
 * align, justify, wrap, width (%, responsive), class, tag, id, grow.
 *
 * @param array $children Child elements.
 * @param array $o        Options.
 * @return array
 */
function larijani_n_c( $children, $o = array() ) {
	$o += array(
		'dir'     => 'column',
		'gap'     => 0,
		'pad'     => larijani_n_box( 0 ),
		'boxed'   => 0,
		'grid'    => null,
		'align'   => '',
		'justify' => '',
		'wrap'    => '',
		'width'   => null,
		'class'   => '',
		'tag'     => '',
		'id'      => '',
		'grow'    => false,
		'fixed'   => false,
		'link'    => '',
	);
	$s = array( 'content_width' => $o['boxed'] ? 'boxed' : 'full' );
	if ( $o['boxed'] ) {
		larijani_n_resp( $s, 'boxed_width', $o['boxed'], static function ( $v ) {
			return array( 'unit' => 'px', 'size' => (int) $v );
		} );
	}
	$gap = static function ( $v ) {
		$v = is_array( $v ) ? $v : array( $v, $v );
		return array( 'column' => (string) $v[0], 'row' => (string) $v[1], 'isLinked' => $v[0] === $v[1], 'unit' => 'px', 'size' => (int) $v[0] );
	};
	if ( $o['grid'] ) {
		$s['container_type'] = 'grid';
		larijani_n_resp( $s, 'grid_columns_grid', $o['grid'], static function ( $v ) {
			return array( 'unit' => 'fr', 'size' => (int) $v );
		} );
		$s['grid_rows_grid'] = array( 'unit' => 'custom', 'size' => 'auto' );
		larijani_n_resp( $s, 'grid_gaps', $o['gap'], static function ( $v ) {
			$v = is_array( $v ) ? $v : array( $v, $v );
			return array( 'column' => (string) $v[0], 'row' => (string) $v[1], 'isLinked' => $v[0] === $v[1], 'unit' => 'px' );
		} );
		if ( $o['align'] ) {
			$s['grid_align_items'] = $o['align'];
		}
	} else {
		larijani_n_resp( $s, 'flex_direction', $o['dir'], 'strval' );
		larijani_n_resp( $s, 'flex_gap', $o['gap'], $gap );
		if ( $o['align'] ) {
			larijani_n_resp( $s, 'flex_align_items', $o['align'], 'strval' );
		}
		if ( $o['justify'] ) {
			larijani_n_resp( $s, 'flex_justify_content', $o['justify'], 'strval' );
		}
		// Elementor wraps every flex container on mobile (--flex-wrap-mobile: wrap):
		// keep the designed direction unless wrapping is asked for.
		$wrap = $o['wrap'] ? $o['wrap'] : ( in_array( 'row', (array) $o['dir'], true ) ? 'nowrap' : '' );
		if ( $wrap ) {
			$s['flex_wrap']        = $wrap;
			$s['flex_wrap_tablet'] = $wrap;
			$s['flex_wrap_mobile'] = $wrap;
		}
	}
	larijani_n_resp( $s, 'padding', isset( $o['pad']['unit'] ) ? $o['pad'] : $o['pad'], static function ( $v ) {
		return $v;
	} );
	if ( null !== $o['width'] ) {
		// Mobile forces 100% on flex containers: repeat a single value on every device.
		$width = is_array( $o['width'] ) ? $o['width'] : array( $o['width'], $o['width'], $o['width'] );
		larijani_n_resp( $s, 'width', $width, static function ( $v ) {
			return is_string( $v ) ? array( 'unit' => 'custom', 'size' => $v ) : array( 'unit' => '%', 'size' => $v );
		} );
	}
	if ( $o['grow'] ) {
		$s['_flex_size']   = 'custom';
		$s['_flex_grow']   = 1;
		$s['_flex_shrink'] = 1;
	}
	if ( $o['fixed'] ) {
		$s['_flex_size'] = 'none';
	}
	if ( $o['class'] ) {
		$s['css_classes'] = $o['class'];
	}
	if ( $o['link'] ) {
		$o['tag']  = 'a';
		$s['link'] = larijani_n_link( $o['link'] );
	}
	if ( $o['tag'] ) {
		$s['html_tag'] = $o['tag'];
	}
	if ( $o['id'] ) {
		$s['_element_id'] = $o['id'];
	}
	return array(
		'elType'   => 'container',
		'settings' => $s,
		'elements' => array_values( array_filter( $children ) ),
	);
}

/**
 * Widget element (class "ls-n" marks native widgets styled by the theme).
 *
 * @param string $type     Widget type.
 * @param array  $settings Settings.
 * @param string $class    Extra CSS classes.
 * @return array
 */
function larijani_n_w( $type, $settings, $class = '' ) {
	list( $class, $margins ) = larijani_n_margin_classes( $class );
	$settings                = array_merge( $settings, $margins );
	$settings['_css_classes'] = trim( 'ls-n ' . $class );
	return array(
		'elType'     => 'widget',
		'widgetType' => $type,
		'settings'   => $settings,
		'elements'   => array(),
	);
}

/**
 * Move vertical margin utilities (mt-*, mb-*, my-*, with sm:/md:/lg: prefixes)
 * from a widget's classes to Elementor's own Margin setting: Elementor resets
 * widget margins inside containers, and the native setting stays editable.
 *
 * @param string $class Classes.
 * @return array [ remaining classes, margin settings ]
 */
function larijani_n_margin_classes( $class ) {
	$space = array( 'space-xs' => 4, 'space-sm' => 8, 'space-md' => 16, 'space-lg' => 24, 'space-xl' => 40, 'space-2xl' => 64 );
	$vals  = array( 'base' => array(), 'sm' => array(), 'lg' => array() );
	$keep  = array();
	foreach ( preg_split( '/\s+/', trim( $class ) ) as $tok ) {
		if ( preg_match( '/^(?:(sm|md|lg):)?m([tby])-((?:\d+(?:\.5)?)|space-[a-z0-9]+)$/', $tok, $m ) ) {
			$px  = isset( $space[ $m[3] ] ) ? $space[ $m[3] ] : (float) $m[3] * 4;
			$bp  = '' === $m[1] ? 'base' : ( 'lg' === $m[1] ? 'lg' : 'sm' );
			$sides = 'y' === $m[2] ? array( 'top', 'bottom' ) : array( 't' === $m[2] ? 'top' : 'bottom' );
			foreach ( $sides as $side ) {
				$vals[ $bp ][ $side ] = $px;
			}
			continue;
		}
		if ( '' !== $tok ) {
			$keep[] = $tok;
		}
	}
	$out = array();
	if ( $vals['base'] || $vals['sm'] || $vals['lg'] ) {
		$mobile  = $vals['base'];
		$tablet  = array_merge( $vals['base'], $vals['sm'] );
		$desktop = array_merge( $tablet, $vals['lg'] );
		$box     = static function ( $v ) {
			return larijani_n_box( $v['top'] ?? 0, 0, $v['bottom'] ?? 0, 0 );
		};
		$out['_margin'] = $box( $desktop );
		if ( $tablet !== $desktop ) {
			$out['_margin_tablet'] = $box( $tablet );
		}
		if ( $mobile !== $tablet || isset( $out['_margin_tablet'] ) ) {
			$out['_margin_mobile'] = $box( $mobile );
		}
	}
	return array( implode( ' ', $keep ), $out );
}

/**
 * Heading widget.
 *
 * @param string $text  Text (inline HTML allowed).
 * @param string $tag   h1..h6|div|span|p.
 * @param string $class Classes.
 * @param mixed  $link  Optional link.
 * @return array|null
 */
function larijani_n_heading( $text, $tag, $class, $link = '' ) {
	if ( '' === trim( larijani_strip_tags( $text ) ) ) {
		return null;
	}
	$s = array( 'title' => $text, 'header_size' => $tag );
	if ( $link ) {
		$s['link'] = larijani_n_link( $link );
	}
	return larijani_n_w( 'heading', $s, $class );
}

/**
 * Text editor widget.
 *
 * @param string $html  HTML or plain text (wrapped in a paragraph).
 * @param string $class Classes.
 * @return array|null
 */
function larijani_n_text( $html, $class ) {
	$html = trim( (string) $html );
	if ( '' === $html ) {
		return null;
	}
	if ( '<' !== substr( $html, 0, 1 ) ) {
		$html = '<p>' . $html . '</p>';
	}
	return larijani_n_w( 'text-editor', array( 'editor' => $html ), $class );
}

/**
 * Button widget.
 *
 * @param string $text  Text.
 * @param mixed  $link  Link.
 * @param mixed  $icon  Icon.
 * @param string $class Classes (ls-n-btn ls-n-btn--{style}).
 * @param string $pos   Icon position: start|end.
 * @return array|null
 */
function larijani_n_button( $text, $link, $icon, $class, $pos = 'start' ) {
	if ( '' === trim( (string) $text ) ) {
		return null;
	}
	$s = array(
		'text' => $text,
		'link' => larijani_n_link( $link ),
	);
	$icon = larijani_n_icon( $icon );
	if ( '' !== $icon['value'] ) {
		$s['selected_icon'] = $icon;
		$s['icon_align']    = 'start' === $pos ? 'row' : 'row-reverse';
	}
	return larijani_n_w( 'button', $s, $class . ( 'end' === $pos ? ' ls-n-btn--arrow' : '' ) );
}

/**
 * Icon widget (box styling through classes on the widget).
 *
 * @param mixed  $icon  Icon.
 * @param string $class Classes.
 * @return array|null
 */
function larijani_n_iconw( $icon, $class ) {
	$icon = larijani_n_icon( $icon );
	if ( '' === $icon['value'] ) {
		return null;
	}
	return larijani_n_w( 'icon', array( 'selected_icon' => $icon ), 'ls-n-icon ' . $class );
}

/**
 * strip_tags that works without WordPress (exporter).
 *
 * @param string $s String.
 * @return string
 */
function larijani_strip_tags( $s ) {
	return trim( preg_replace( '/<[^>]*>/', '', (string) $s ) );
}

/**
 * Merge widget defaults (generated, see widget-defaults.php) with overrides.
 *
 * @param string $type      Widget type.
 * @param array  $overrides Overrides.
 * @return array
 */
function larijani_n_settings( $type, $overrides = array() ) {
	$all = larijani_widget_defaults();
	$def = isset( $all[ $type ] ) ? $all[ $type ] : array( 'defaults' => array(), 'fields' => array() );
	$s   = array_merge( $def['defaults'], $overrides );
	foreach ( $def['fields'] as $rep => $fields ) {
		if ( ! empty( $s[ $rep ] ) && is_array( $s[ $rep ] ) ) {
			foreach ( $s[ $rep ] as $i => $row ) {
				$s[ $rep ][ $i ] = array_merge( $fields, (array) $row );
			}
		}
	}
	return $s;
}

/**
 * Persian digits (no WordPress).
 *
 * @param int|string $n Number.
 * @return string
 */
function larijani_n_fa( $n ) {
	return strtr( (string) $n, array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' ) );
}

/**
 * Lines of a textarea value.
 *
 * @param string $s Value.
 * @return array
 */
function larijani_n_lines( $s ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $s ) ), 'strlen' ) );
}

/* -------------------------------------------------------------------------
 * Shared tokens (same classes as the theme widgets).
 * ---------------------------------------------------------------------- */

/**
 * Section background classes.
 *
 * @param string $bg Key.
 * @return string
 */
function larijani_n_bg( $bg ) {
	$map = array(
		'canvas'         => 'bg-surface-canvas',
		'white'          => 'bg-white',
		'surface'        => 'bg-surface',
		'low'            => 'bg-surface-container-low/50',
		'dark'           => 'bg-surface-dark',
		'none'           => '',
		'white-bordered' => 'bg-white border-y border-border-subtle',
	);
	return isset( $map[ $bg ] ) ? $map[ $bg ] : '';
}

/**
 * Soft tone classes (icon tiles).
 *
 * @param string $tone Tone.
 * @param string $kind soft|text.
 * @return string
 */
function larijani_n_tone( $tone, $kind = 'soft' ) {
	$map = array(
		'soft' => array(
			'primary' => 'bg-primary/10 text-primary',
			'amber'   => 'bg-accent-amber/10 text-accent-amber',
			'emerald' => 'bg-accent-emerald/10 text-accent-emerald',
			'cobalt'  => 'bg-accent-cobalt/10 text-accent-cobalt',
			'dark'    => 'bg-surface-dark text-primary-fixed',
			'light'   => 'bg-surface-container text-on-surface',
			'sage'    => 'bg-secondary-container text-on-secondary-container',
			'fixed'   => 'bg-primary-fixed text-on-primary-fixed',
			'sfixed'  => 'bg-secondary-fixed text-on-secondary-fixed',
			'high'    => 'bg-surface-container-high text-on-surface',
		),
		'text' => array(
			'primary' => 'text-primary',
			'amber'   => 'text-accent-amber',
			'emerald' => 'text-accent-emerald',
			'cobalt'  => 'text-accent-cobalt',
			'dark'    => 'text-surface-dark',
			'light'   => 'text-outline',
			'sage'    => 'text-secondary',
			'error'   => 'text-error',
		),
	);
	$set = $map[ $kind ];
	return isset( $set[ $tone ] ) ? $set[ $tone ] : reset( $set );
}

/**
 * Outer section: full-width container with background + boxed inner area.
 * Matches "py-* + max-w-7xl mx-auto px-4 sm:px-6 lg:px-8".
 *
 * @param array $children Children.
 * @param array $o        bg, py [d,t,m], gap, class, id, max (inner px), px [d,t,m].
 * @return array
 */
function larijani_n_section( $children, $o = array() ) {
	$o  += array(
		'bg'    => '',
		'py'    => array( 64, 64, 48 ),
		'px'    => array( 32, 24, 16 ),
		'gap'   => 0,
		'class' => '',
		'id'    => '',
		'max'   => 1280,
		'dir'   => 'column',
		'align' => '',
		'justify' => '',
	);
	$pad = array();
	foreach ( array( 0, 1, 2 ) as $i ) {
		$py        = is_array( $o['py'] ) ? $o['py'][ $i ] : $o['py'];
		$px        = is_array( $o['px'] ) ? $o['px'][ $i ] : $o['px'];
		$pad[ $i ] = is_array( $py ) ? larijani_n_box( $py[0], $px, $py[1], $px ) : larijani_n_box( $py, $px, $py, $px );
	}
	$px0 = is_array( $o['px'] ) ? $o['px'][0] : $o['px'];
	// Outer wrapper carries "ls-root": theme utilities are scoped to it, so a
	// section keeps its design anywhere (saved templates, other page templates).
	return larijani_n_c(
		array(
			larijani_n_c(
				$children,
				array(
					'boxed'   => $o['max'] - 2 * $px0,
					'pad'     => $pad,
					'gap'     => $o['gap'],
					'class'   => trim( 'ls-n-section ' . $o['class'] ),
					'tag'     => 'section',
					'dir'     => $o['dir'],
					'align'   => $o['align'],
					'justify' => $o['justify'],
				)
			),
		),
		array( 'class' => 'ls-root ls-n-wrap', 'id' => $o['id'] )
	);
}

/**
 * Section heading (same variants as larijani_section_heading()).
 *
 * @param array $h eyebrow, title, desc, link_text, link, align (split|center|start|split-desc), style (classic|token), tag, dark, mb.
 * @return array|null
 */
function larijani_n_section_heading( $h ) {
	$h += array(
		'eyebrow'   => '',
		'title'     => '',
		'desc'      => '',
		'link_text' => '',
		'link'      => '',
		'align'     => 'split',
		'style'     => 'classic',
		'tag'       => 'h2',
		'dark'      => false,
		'mb'        => array( 40, 40, 32 ),
	);
	if ( ! $h['eyebrow'] && ! $h['title'] && ! $h['desc'] && ! $h['link_text'] ) {
		return null;
	}
	$token   = 'token' === $h['style'];
	$eyebrow = larijani_n_heading( $h['eyebrow'], 'span', ( $token ? 'font-label-badge text-label-badge' : 'text-xs font-bold uppercase' ) . ' text-primary-container tracking-wider' );
	$title   = larijani_n_heading( $h['title'], $h['tag'], ( $token ? 'font-headline-lg text-headline-lg' : 'text-xl sm:text-2xl lg:text-3xl font-black' ) . ' ' . ( $h['dark'] ? 'text-white' : 'text-surface-dark' ) . ' mt-1 leading-snug' );
	$desc    = larijani_n_text( $h['desc'], ( $token ? 'font-body-md text-body-md' : 'text-xs sm:text-sm' ) . ' ' . ( $h['dark'] ? 'text-slate-300' : ( $token ? 'text-on-surface-variant' : 'text-slate-500' ) ) . ' mt-1 leading-relaxed' );
	$link    = $h['link_text'] ? larijani_n_button( $h['link_text'], $h['link'], 'arrow-left', 'ls-n-btn ls-n-btn--link', 'end' ) : null;
	$mb      = array();
	foreach ( (array) $h['mb'] as $v ) {
		$mb[] = larijani_n_box( 0, 0, $v, 0 );
	}
	$mb = $mb ? $mb : array( larijani_n_box( 0 ) );

	if ( 'center' === $h['align'] ) {
		return larijani_n_c( array( $eyebrow, $title, $desc, $link ), array( 'align' => 'center', 'gap' => 4, 'pad' => $mb, 'class' => 'ls-n-head text-center max-w-3xl mx-auto w-full' ) );
	}
	if ( 'start' === $h['align'] ) {
		return larijani_n_c( array( $eyebrow, $title, $desc, $link ), array( 'gap' => 4, 'pad' => $mb, 'align' => 'flex-start', 'class' => 'ls-n-head' ) );
	}
	if ( 'split-desc' === $h['align'] ) {
		return larijani_n_c(
			array(
				larijani_n_c( array( $eyebrow, $title, $link ), array( 'gap' => 4, 'align' => 'flex-start', 'width' => array( 'auto', 'auto', 100 ) ) ),
				$desc ? larijani_n_c( array( $desc ), array( 'class' => 'max-w-md', 'width' => array( 'auto', 'auto', 100 ) ) ) : null,
			),
			array( 'dir' => array( 'row', 'row', 'column' ), 'justify' => 'space-between', 'align' => array( 'flex-end', 'flex-end', 'stretch' ), 'gap' => 16, 'pad' => $mb, 'class' => 'ls-n-head' )
		);
	}
	return larijani_n_c(
		array(
			larijani_n_c( array( $eyebrow, $title, $desc ), array( 'class' => 'max-w-2xl', 'width' => array( 'auto', 'auto', 100 ) ) ),
			$link,
		),
		array( 'dir' => array( 'row', 'row', 'column' ), 'justify' => 'space-between', 'align' => array( 'flex-end', 'flex-end', 'flex-start' ), 'gap' => 12, 'pad' => $mb, 'class' => 'ls-n-head' )
	);
}

/**
 * Heading options from widget settings (heading_* keys).
 *
 * @param array $s        Settings.
 * @param array $defaults Defaults.
 * @return array
 */
function larijani_n_heading_args( $s, $defaults = array() ) {
	$h = $defaults;
	foreach ( array( 'eyebrow', 'title', 'desc', 'link_text', 'link', 'align', 'style', 'tag' ) as $k ) {
		if ( isset( $s[ 'heading_' . $k ] ) && '' !== $s[ 'heading_' . $k ] && array() !== $s[ 'heading_' . $k ] ) {
			$h[ $k ] = $s[ 'heading_' . $k ];
		} elseif ( ! isset( $h[ $k ] ) ) {
			$h[ $k ] = '';
		}
	}
	if ( is_array( $h['link'] ) ) {
		$h['link'] = isset( $h['link']['url'] ) ? $h['link'] : '';
	}
	$h['align'] = $h['align'] ? $h['align'] : 'split';
	$h['style'] = $h['style'] ? $h['style'] : 'classic';
	$h['tag']   = $h['tag'] ? $h['tag'] : 'h2';
	return $h;
}

/* -------------------------------------------------------------------------
 * Section recipes (one per converted theme widget).
 * ---------------------------------------------------------------------- */

/**
 * Icon cards (ls-icon-cards): badge | horizontal | simple | bento | guide.
 *
 * @param array $o Widget settings (overrides).
 * @return array
 */
function larijani_nr_icon_cards( $o = array() ) {
	$s     = larijani_n_settings( 'ls-icon-cards', $o );
	$v     = $s['variant'];
	$cols  = array( (int) $s['columns'], (int) $s['columns_tablet'], (int) $s['columns_mobile'] );
	$cards = array();

	foreach ( $s['items'] as $it ) {
		$link = ! empty( $it['link']['url'] ) || ( is_string( $it['link'] ) && '' !== $it['link'] ) ? $it['link'] : '';
		$tone = $it['tone'] ? $it['tone'] : 'primary';
		if ( 'badge' === $v ) {
			$text_tone = array( 'primary' => 'text-primary', 'emerald' => 'text-accent-emerald', 'amber' => 'text-accent-amber', 'cobalt' => 'text-accent-cobalt' );
			$cards[]   = larijani_n_c(
				array(
					larijani_n_iconw( $it['icon'], 'w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-white border border-slate-200 shadow-sm text-xl shrink-0 ' . ( $text_tone[ $tone ] ?? 'text-primary' ) ),
					larijani_n_c(
						array(
							larijani_n_heading( $it['title'], 'p', 'text-xs sm:text-sm font-black text-surface-dark', $link ),
							larijani_n_text( $it['desc'], 'text-[11px] sm:text-xs text-slate-500 mt-0.5' ),
						),
						array( 'grow' => true, 'width' => 'auto' )
					),
				),
				array( 'dir' => 'row', 'align' => 'center', 'gap' => 14, 'pad' => array( larijani_n_box( 16 ), larijani_n_box( 16 ), larijani_n_box( 14 ) ), 'class' => 'ls-n-card rounded-2xl bg-surface-canvas border border-border-subtle transition-transform hover:-translate-y-1' )
			);
		} elseif ( 'horizontal' === $v ) {
			$cards[] = larijani_n_c(
				array(
					larijani_n_iconw( $it['icon'], 'w-11 h-11 sm:w-12 sm:h-12 rounded-xl text-xl shrink-0 ' . larijani_n_tone( $tone ) ),
					larijani_n_c(
						array(
							larijani_n_heading( $it['title'], 'h3', 'font-bold text-gray-900 text-sm sm:text-base', $link ),
							larijani_n_text( $it['desc'], 'text-xs text-gray-500 mt-1 leading-relaxed' ),
						),
						array( 'grow' => true, 'width' => 'auto' )
					),
				),
				array( 'dir' => 'row', 'align' => 'flex-start', 'gap' => 16, 'pad' => array( larijani_n_box( 24 ), larijani_n_box( 24 ), larijani_n_box( 20 ) ), 'class' => 'ls-n-card bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md transition-shadow' )
			);
		} elseif ( 'simple' === $v ) {
			$cards[] = larijani_n_c(
				array(
					larijani_n_iconw( $it['icon'], 'w-12 h-12 rounded-xl text-2xl ' . larijani_n_tone( $tone ) ),
					larijani_n_heading( $it['title'], 'h3', 'font-title-card text-title-card text-on-surface', $link ),
					larijani_n_text( $it['desc'], 'font-body-md text-body-md text-on-surface-variant leading-relaxed' ),
				),
				array( 'gap' => 8, 'pad' => larijani_n_box( 24 ), 'class' => 'ls-n-card rounded-2xl bg-surface-card shadow-sm hover:shadow-md transition-shadow' )
			);
		} elseif ( 'guide' === $v ) {
			$cards[] = larijani_n_c(
				array(
					larijani_n_c(
						array(
							larijani_n_iconw( $it['icon'], 'text-primary text-[20px]' ),
							larijani_n_heading( $it['title'], 'h3', 'font-headline-sm text-headline-sm text-on-surface', $link ),
						),
						array( 'dir' => 'row', 'align' => 'center', 'gap' => 4 )
					),
					larijani_n_text( $it['desc'], 'font-body-sm text-body-sm leading-relaxed text-on-surface-variant' ),
				),
				array( 'gap' => 4, 'pad' => larijani_n_box( 16 ), 'class' => 'ls-n-card bg-surface-canvas rounded-xl' )
			);
		} else {
			$chips = array();
			foreach ( larijani_n_lines( $it['chips'] ) as $chip ) {
				$chips[] = array( 'text' => $chip, 'selected_icon' => array( 'value' => '', 'library' => '' ) );
			}
			$cards[] = larijani_n_c(
				array(
					larijani_n_c(
						array(
							larijani_n_iconw( $it['icon'], 'w-14 h-14 rounded-xl text-[28px] ' . larijani_n_tone( $tone ) ),
							larijani_n_c(
								array(
									larijani_n_heading( $it['eyebrow'], 'span', 'font-label-badge text-label-badge text-outline' ),
									larijani_n_heading( $it['title'], 'h3', 'font-headline-sm text-headline-sm text-surface-dark', $link ),
								),
								array( 'gap' => 4 )
							),
							larijani_n_text( $it['desc'], 'font-body-md text-body-md text-on-surface-variant leading-relaxed' ),
							$chips ? larijani_n_w( 'icon-list', array( 'view' => 'inline', 'icon_list' => $chips ), 'ls-n-chips pt-space-xs' ) : null,
						),
						array( 'gap' => 16 )
					),
					$it['footer'] ? larijani_n_button( $it['footer'], $link, 'arrow-left', 'ls-n-btn ls-n-btn--card-footer', 'end' ) : null,
				),
				array( 'gap' => 16, 'justify' => 'space-between', 'pad' => larijani_n_box( 24 ), 'class' => 'ls-n-card bg-surface-card rounded-2xl shadow-sm hover:shadow-md transition-shadow' )
			);
		}
	}

	if ( 'guide' === $v ) {
		$h = larijani_n_heading_args( $s );
		return larijani_n_section(
			array(
				larijani_n_c(
					array(
						$h['eyebrow'] ? larijani_n_c(
							array(
								larijani_n_iconw( 'journal-bookmark', 'text-primary text-[20px]' ),
								larijani_n_heading( $h['eyebrow'], 'span', 'font-label-nav text-label-nav text-primary' ),
							),
							array( 'dir' => 'row', 'align' => 'center', 'gap' => 4, 'pad' => larijani_n_box( 0, 0, 4, 0 ) )
						) : null,
						larijani_n_heading( $h['title'], 'h2', 'font-headline-md text-headline-md text-on-surface' ),
						larijani_n_c( $cards, array( 'grid' => $cols, 'gap' => 24 ) ),
					),
					array( 'gap' => 16, 'pad' => larijani_n_box( 40 ), 'class' => 'bg-surface-card rounded-2xl shadow-sm' )
				),
			),
			array( 'bg' => $s['section_bg'], 'class' => larijani_n_bg( $s['section_bg'] ), 'py' => 40, 'px' => array( 32, 16, 16 ) )
		);
	}

	$token = in_array( $v, array( 'simple', 'bento' ), true );
	$h     = larijani_n_heading_args( $s, array( 'style' => $token ? 'token' : 'classic' ) );
	if ( ! empty( $s['heading_style'] ) ) {
		$h['style'] = $s['heading_style'];
	}
	$gap = 'badge' === $v ? array( 24, 16, 12 ) : array( 24, 24, 16 );
	return larijani_n_section(
		array(
			larijani_n_section_heading( $h ),
			larijani_n_c( $cards, array( 'grid' => $cols, 'gap' => $gap ) ),
		),
		array(
			'class' => larijani_n_bg( $s['section_bg'] ),
			'py'    => 'badge' === $v ? array( 32, 32, 24 ) : array( 64, 64, 48 ),
		)
	);
}

/**
 * About + stats (ls-about).
 *
 * @param array $o Overrides.
 * @return array
 */
function larijani_nr_about( $o = array() ) {
	$s     = larijani_n_settings( 'ls-about', $o );
	$stats = array();
	foreach ( $s['stats'] as $st ) {
		$stats[] = larijani_n_c(
			array(
				larijani_n_heading( $st['value'], 'div', 'text-2xl sm:text-3xl font-black ' . ( 'yes' === $st['accent'] ? 'text-primary-container' : 'text-surface-dark' ) ),
				larijani_n_heading( $st['label'], 'div', 'text-[11px] sm:text-xs font-semibold text-slate-500 mt-1' ),
			),
			array( 'pad' => array( larijani_n_box( 20 ), larijani_n_box( 20 ), larijani_n_box( 16 ) ), 'class' => 'ls-n-card bg-surface-canvas rounded-2xl border border-border-subtle text-right' )
		);
	}
	return larijani_n_section(
		array(
			larijani_n_c(
				array(
					larijani_n_heading( $s['eyebrow'], 'span', 'text-xs font-bold text-primary-container uppercase tracking-wider' ),
					larijani_n_heading( $s['title'], 'h2', 'text-xl sm:text-2xl lg:text-3xl font-black text-surface-dark mt-2 mb-3 sm:mb-4 leading-snug' ),
					larijani_n_text( $s['content'], 'ls-n-rich text-slate-600 text-xs sm:text-sm leading-relaxed mb-6 [&_strong]:text-surface-dark' ),
					larijani_n_button( $s['button_text'], $s['button_link'], 'arrow-left', 'ls-n-btn ls-n-btn--primary', 'end' ),
				),
				array( 'width' => array( larijani_n_span( 5, 40 ), 100, 100 ), 'align' => 'flex-start', 'class' => 'text-right' )
			),
			larijani_n_c(
				array( larijani_n_w( 'image', array( 'image' => $s['image'], 'image_size' => 'large' ), 'ls-n-img-cover h-64 sm:h-80 lg:h-96' ) ),
				array( 'width' => array( larijani_n_span( 4, 40 ), 100, 100 ), 'class' => 'rounded-2xl sm:rounded-3xl overflow-hidden shadow-lg border border-slate-200' )
			),
			larijani_n_c( $stats, array( 'grid' => array( 1, 2, 2 ), 'gap' => array( 16, 16, 12 ), 'width' => array( larijani_n_span( 3, 40 ), 100, 100 ) ) ),
		),
		array(
			'class' => larijani_n_bg( $s['section_bg'] ),
			'id'    => $o['_element_id'] ?? '',
			'gap'   => array( 40, 32, 32 ),
			'dir'   => array( 'row', 'column', 'column' ),
			'align' => array( 'center', 'stretch', 'stretch' ),
		)
	);
}

/**
 * Testimonials grid (ls-testimonials, grid layout).
 *
 * @param array $o Overrides.
 * @return array
 */
function larijani_nr_testimonials( $o = array() ) {
	$s       = larijani_n_settings( 'ls-testimonials', $o );
	$classic = 'classic' === ( $s['card_style'] ?? '' );
	$cards   = array();
	foreach ( $s['items'] as $it ) {
		$stars  = min( 5, (int) $it['stars'] );
		$avatar = ! empty( $it['avatar']['url'] )
			? larijani_n_w( 'image', array( 'image' => $it['avatar'], 'image_size' => 'thumbnail' ), 'ls-n-avatar w-10 h-10' )
			: larijani_n_heading( larijani_n_initials( $it['name'] ), 'div', 'ls-n-initials w-10 h-10 rounded-full bg-slate-200 font-bold text-xs text-slate-700' );
		$rating = $stars > 0 ? larijani_n_w( 'star-rating', array( 'rating_scale' => '5', 'rating' => $stars, 'star_style' => 'star_unicode', 'unmarked_star_style' => 'outline' ), 'ls-n-stars ' . ( $classic ? 'text-amber-400' : 'text-amber-500' ) . ' text-xs' ) : null;
		$person = larijani_n_c(
			array(
				larijani_n_heading( $it['name'], 'p', $classic ? 'font-bold text-gray-900 text-xs sm:text-sm' : 'text-xs font-black text-surface-dark' ),
				larijani_n_heading( $it['role'], 'div', $classic ? 'text-[10px] sm:text-[11px] text-gray-500' : 'text-[11px] text-slate-500' ),
			),
			array( 'width' => 'auto' )
		);
		if ( $classic ) {
			$cards[] = larijani_n_c(
				array(
					larijani_n_c(
						array(
							larijani_n_heading( '“', 'span', 'text-3xl sm:text-4xl text-primary-container/40 font-serif leading-none mb-2' ),
							larijani_n_text( $it['text'], 'text-gray-700 text-xs sm:text-sm leading-relaxed' ),
						)
					),
					larijani_n_c(
						array(
							larijani_n_c( array( $avatar, $person ), array( 'dir' => 'row', 'align' => 'center', 'gap' => 12, 'width' => 'auto' ) ),
							$rating,
						),
						array( 'dir' => 'row', 'align' => 'center', 'justify' => 'space-between', 'pad' => larijani_n_box( 16, 0, 0, 0 ), 'class' => 'mt-5 border-t border-gray-100' )
					),
				),
				array( 'justify' => 'space-between', 'pad' => array( larijani_n_box( 24 ), larijani_n_box( 24 ), larijani_n_box( 20 ) ), 'class' => 'ls-n-card bg-white rounded-2xl border border-gray-200/90 shadow-sm' )
			);
			continue;
		}
		$cards[] = larijani_n_c(
			array(
				larijani_n_c(
					array(
						larijani_n_c(
							array(
								larijani_n_heading( '“', 'div', 'text-3xl font-serif text-primary-container mb-2 leading-none' ),
								$rating,
							),
							array( 'dir' => 'row', 'align' => 'center', 'justify' => 'space-between' )
						),
						larijani_n_text( $it['text'], 'text-slate-600 text-xs sm:text-sm leading-relaxed mb-6' ),
					)
				),
				larijani_n_c( array( $avatar, $person ), array( 'dir' => 'row', 'align' => 'center', 'gap' => 12, 'pad' => larijani_n_box( 16, 0, 0, 0 ), 'class' => 'border-t border-slate-100' ) ),
			),
			array( 'justify' => 'space-between', 'pad' => array( larijani_n_box( 24 ), larijani_n_box( 24 ), larijani_n_box( 20 ) ), 'class' => 'ls-n-card bg-white rounded-2xl border border-border-subtle card-shadow' )
		);
	}
	$h      = larijani_n_heading_args( $s );
	$rating = $s['rating_text'] ? larijani_n_c(
		array(
			larijani_n_iconw( 'star-fill', 'text-amber-500' ),
			larijani_n_heading( $s['rating_text'], 'span', 'text-xs sm:text-sm font-bold text-slate-700' ),
		),
		array( 'dir' => 'row', 'align' => 'center', 'gap' => 6, 'width' => 'auto' )
	) : null;
	$head   = larijani_n_c(
		array(
			larijani_n_c(
				array(
					larijani_n_heading( $h['eyebrow'], 'span', 'text-xs font-bold text-primary-container uppercase tracking-wider' ),
					larijani_n_heading( $h['title'], 'h2', 'text-xl sm:text-2xl lg:text-3xl font-black text-surface-dark mt-1' ),
					larijani_n_text( $h['desc'], 'text-xs sm:text-sm text-slate-500 mt-1' ),
				),
				array( 'width' => array( 'auto', 'auto', 100 ) )
			),
			$rating,
		),
		array( 'dir' => array( 'row', 'row', 'column' ), 'justify' => 'space-between', 'align' => array( 'center', 'center', 'flex-start' ), 'gap' => 8, 'pad' => array( larijani_n_box( 0, 0, 40, 0 ), larijani_n_box( 0, 0, 40, 0 ), larijani_n_box( 0, 0, 32, 0 ) ) )
	);
	return larijani_n_section(
		array(
			$head,
			larijani_n_c( $cards, array( 'grid' => array( (int) $s['columns'], (int) $s['columns_tablet'], (int) $s['columns_mobile'] ), 'gap' => array( 24, 24, 20 ) ) ),
		),
		array( 'class' => larijani_n_bg( $s['section_bg'] ) )
	);
}

/**
 * Initials avatar text (no WordPress).
 *
 * @param string $name Name.
 * @return string
 */
function larijani_n_initials( $name ) {
	$parts = array_values(
		array_filter(
			preg_split( '/\s+/u', trim( larijani_strip_tags( $name ) ) ),
			static function ( $p ) {
				return '' !== $p && ! in_array( $p, array( 'مهندس', 'دکتر', 'حاج', 'آقای', 'خانم', 'جناب' ), true );
			}
		)
	);
	if ( ! $parts ) {
		return '';
	}
	$first = mb_substr( $parts[0], 0, 1 );
	$last  = isset( $parts[1] ) ? mb_substr( $parts[1], 0, 1 ) : '';
	return $last ? $first . '.' . $last : $first;
}

/**
 * Call to action (ls-cta): dark-card | dark-strip | soft-card | consult-band.
 * Form variants stay theme widgets (they store leads).
 *
 * @param array $o Overrides.
 * @return array
 */
function larijani_nr_cta( $o = array() ) {
	$s    = larijani_n_settings( 'ls-cta', $o );
	$v    = $s['variant'];
	$btn  = static function ( $n, $class, $pos = 'start' ) use ( $s ) {
		return larijani_n_button( $s[ "btn{$n}_text" ], $s[ "btn{$n}_link" ], $s[ "btn{$n}_icon" ], $class, $pos );
	};
	$badge_icon = larijani_n_icon( $s['badge_icon'] );

	if ( 'consult-band' === $v ) {
		$checks = array();
		foreach ( larijani_n_lines( $s['checks'] ?? '' ) as $c ) {
			$checks[] = array( 'text' => $c, 'selected_icon' => larijani_n_icon( 'check-lg' ) );
		}
		$phone = static function ( $label, $num, $icon, $class ) {
			if ( '' === trim( (string) $num ) ) {
				return null;
			}
			return larijani_n_c(
				array(
					larijani_n_heading( $label, 'span', 'text-body-sm font-body-sm text-outline-variant' ),
					larijani_n_button( larijani_n_phone( $num ), 'tel:' . preg_replace( '/[^0-9+]/', '', $num ), $icon, 'ls-n-btn ls-n-btn--phone ' . $class, 'end' ),
				),
				array( 'gap' => 4 )
			);
		};
		return larijani_n_section(
			array(
				larijani_n_c(
					array(
						$s['badge'] ? larijani_n_c(
							array(
								larijani_n_iconw( $badge_icon, 'text-[16px]' ),
								larijani_n_heading( $s['badge'], 'span', '' ),
							),
							array( 'dir' => 'row', 'align' => 'center', 'gap' => 4, 'width' => 'auto', 'pad' => larijani_n_box( 4, 16 ), 'class' => 'ls-n-pill text-primary-fixed font-label-badge text-label-badge bg-primary-container/40 rounded-full' )
						) : null,
						larijani_n_heading( $s['title'], 'h2', 'font-headline-lg text-headline-lg text-on-tertiary' ),
						larijani_n_text( $s['desc'], 'font-body-lg text-body-lg text-on-tertiary-container leading-relaxed' ),
						$checks ? larijani_n_w( 'icon-list', array( 'view' => 'inline', 'icon_list' => $checks ), 'ls-n-checks pt-space-xs font-body-sm text-body-sm text-outline-variant' ) : null,
					),
					array( 'gap' => 16, 'width' => array( larijani_n_span( 8, 40 ), 100, 100 ), 'align' => 'flex-start' )
				),
				larijani_n_c(
					array(
						$phone( $s['phone1_label'], $s['phone1'], 'telephone', 'ls-n-btn--phone-lg' ),
						$phone( $s['phone2_label'], $s['phone2'], 'phone', '' ),
						$btn( 1, 'ls-n-btn ls-n-btn--primary ls-n-btn--block' ),
					),
					array( 'gap' => 16, 'pad' => larijani_n_box( 24 ), 'width' => array( larijani_n_span( 4, 40 ), 100, 100 ), 'class' => 'bg-surface-footer/80 rounded-2xl' )
				),
			),
			array( 'class' => 'bg-surface-dark text-on-tertiary my-space-lg overflow-hidden', 'py' => 64, 'px' => array( 32, 16, 16 ), 'gap' => 40, 'dir' => array( 'row', 'column', 'column' ), 'align' => array( 'center', 'stretch', 'stretch' ) )
		);
	}

	if ( 'dark-strip' === $v ) {
		return larijani_n_section(
			array(
				larijani_n_c(
					array(
						larijani_n_iconw( $s['icon'], 'w-12 h-12 rounded-xl bg-primary-container text-on-primary shrink-0 text-2xl' ),
						larijani_n_c(
							array(
								larijani_n_heading( $s['title'], 'span', 'font-headline-sm text-headline-sm text-on-tertiary' ),
								larijani_n_text( $s['desc'], 'font-body-sm text-body-sm text-tertiary-fixed-dim' ),
							),
							array( 'width' => 'auto' )
						),
					),
					array( 'dir' => 'row', 'align' => 'center', 'gap' => 16, 'width' => array( 'auto', 'auto', 100 ) )
				),
				larijani_n_c(
					array(
						$btn( 1, 'ls-n-btn ls-n-btn--light' ),
						$btn( 2, 'ls-n-btn ls-n-btn--ghost' ),
					),
					array( 'dir' => 'row', 'align' => 'center', 'gap' => 8, 'wrap' => 'wrap', 'width' => 'auto' )
				),
			),
			array( 'class' => 'bg-surface-dark text-on-tertiary', 'py' => 40, 'px' => array( 32, 16, 16 ), 'gap' => 16, 'dir' => array( 'row', 'row', 'column' ), 'align' => 'center', 'justify' => 'space-between' )
		);
	}

	if ( 'soft-card' === $v ) {
		return larijani_n_section(
			array(
				larijani_n_c(
					array(
						larijani_n_c(
							array(
								larijani_n_iconw( $s['icon'], 'w-14 h-14 rounded-2xl bg-surface-card text-primary shrink-0 shadow-sm text-[28px]' ),
								larijani_n_c(
									array(
										$s['badge'] ? larijani_n_c(
											array( larijani_n_iconw( $badge_icon, 'text-accent-emerald' ), larijani_n_heading( $s['badge'], 'span', '' ) ),
											array( 'dir' => 'row', 'align' => 'center', 'gap' => 8, 'width' => 'auto', 'pad' => larijani_n_box( 4, 12 ), 'class' => 'ls-n-pill bg-white text-primary rounded-full font-label-badge text-label-badge mb-3' )
										) : null,
										larijani_n_heading( $s['title'], 'span', 'font-headline-sm text-headline-sm text-on-secondary-container' ),
										larijani_n_text( $s['desc'], 'text-body-md font-body-md text-on-secondary-fixed-variant' ),
									),
									array( 'width' => 'auto', 'align' => 'flex-start' )
								),
							),
							array( 'dir' => 'row', 'align' => 'center', 'gap' => 16, 'width' => array( 'auto', 'auto', 100 ) )
						),
						larijani_n_c(
							array(
								$btn( 1, 'ls-n-btn ls-n-btn--primary-dark' ),
								$btn( 2, 'ls-n-btn ls-n-btn--white' ),
							),
							array( 'dir' => 'row', 'align' => 'center', 'gap' => 8, 'wrap' => 'wrap', 'width' => 'auto' )
						),
					),
					array( 'dir' => array( 'row', 'row', 'column' ), 'align' => 'center', 'justify' => 'space-between', 'gap' => 24, 'pad' => array( larijani_n_box( 40 ), larijani_n_box( 24 ), larijani_n_box( 24 ) ), 'class' => 'bg-secondary-container/60 rounded-3xl' )
				),
			),
			array( 'class' => larijani_n_bg( $s['section_bg'] ), 'py' => 40, 'px' => array( 32, 16, 16 ) )
		);
	}

	// dark-card.
	$btn2 = array(
		'glass'   => 'ls-n-btn--glass',
		'emerald' => 'ls-n-btn--emerald',
		'darker'  => 'ls-n-btn--darker',
		'white'   => 'ls-n-btn--white',
	);
	$sub  = $s['btn1_sub'] ? larijani_n_c(
		array(
			larijani_n_iconw( $s['btn1_icon'], 'text-lg' ),
			larijani_n_c(
				array(
					larijani_n_heading( $s['btn1_sub'], 'span', 'text-xs opacity-80 leading-none' ),
					larijani_n_heading( $s['btn1_text'], 'span', 'font-black mt-1 ls-n-ltr' ),
				),
				array( 'width' => 'auto', 'class' => 'text-right' )
			),
		),
		array( 'dir' => 'row', 'align' => 'center', 'justify' => 'center', 'gap' => 8, 'width' => 'auto', 'fixed' => true, 'pad' => larijani_n_box( 14, 24 ), 'link' => $s['btn1_link'], 'class' => 'ls-n-btn-sub bg-primary-container hover:bg-primary text-white font-bold text-xs sm:text-sm rounded-full shadow-lg transition-all' )
	) : $btn( 1, 'ls-n-btn ls-n-btn--primary ls-n-btn--lg' );
	$center = 'yes' === $s['center_mobile'] ? ' text-center lg:text-right' : ' text-right';
	return larijani_n_section(
		array(
			larijani_n_c(
				array(
					larijani_n_c(
						array(
							$s['badge'] ? larijani_n_c(
								array( larijani_n_iconw( $badge_icon, 'text-accent-emerald' ), larijani_n_heading( $s['badge'], 'span', '' ) ),
								array( 'dir' => 'row', 'align' => 'center', 'gap' => 8, 'width' => 'auto', 'pad' => larijani_n_box( 4, 12 ), 'class' => 'ls-n-pill bg-white/10 text-secondary-fixed rounded-full font-label-badge text-label-badge mb-3' )
							) : null,
							larijani_n_heading( $s['title'], 'h3', 'font-black tracking-tight mb-2 text-lg sm:text-xl lg:text-2xl leading-snug text-white' ),
							larijani_n_text( $s['desc'], 'text-slate-300 text-xs sm:text-sm max-w-xl leading-relaxed' ),
						),
						array( 'class' => 'max-w-2xl' . $center, 'align' => 'flex-start', 'width' => array( 'auto', 100, 100 ) )
					),
					larijani_n_c(
						array( $sub, $btn( 2, 'ls-n-btn ' . ( $btn2[ $s['btn2_style'] ] ?? 'ls-n-btn--glass' ) . ' ls-n-btn--lg ls-n-nowrap' ) ),
						array( 'dir' => array( 'row', 'row', 'column' ), 'align' => array( 'center', 'center', 'stretch' ), 'gap' => 12, 'width' => array( 'auto', 100, 100 ), 'fixed' => true )
					),
				),
				array( 'dir' => array( 'row', 'column', 'column' ), 'align' => array( 'center', 'stretch', 'stretch' ), 'justify' => 'space-between', 'gap' => 24, 'pad' => array( larijani_n_box( 48 ), larijani_n_box( 40 ), larijani_n_box( 24 ) ), 'class' => 'ls-n-glow bg-surface-dark rounded-2xl sm:rounded-3xl overflow-hidden text-white shadow-xl' )
			),
		),
		array( 'class' => larijani_n_bg( $s['section_bg'] ), 'py' => array( 56, 56, 40 ) )
	);
}

/**
 * Phone display (0912 230 2685) without WordPress.
 *
 * @param string $num Number.
 * @return string
 */
function larijani_n_phone( $num ) {
	$d = preg_replace( '/\D+/', '', strtr( (string) $num, array( '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9' ) ) );
	if ( 11 === strlen( $d ) ) {
		$d = substr( $d, 0, 4 ) . ' ' . substr( $d, 4, 3 ) . ' ' . substr( $d, 7 );
	}
	return larijani_n_fa( $d );
}

/**
 * Process steps + image cards (ls-steps).
 *
 * @param array $o Overrides.
 * @return array
 */
function larijani_nr_steps( $o = array() ) {
	$s     = larijani_n_settings( 'ls-steps', $o );
	$tones = array(
		'dark'    => 'bg-surface-dark text-on-tertiary',
		'primary' => 'bg-primary-container text-on-primary',
		'sage'    => 'bg-secondary text-on-secondary',
	);
	$steps = array();
	foreach ( $s['steps'] as $i => $st ) {
		$steps[] = larijani_n_c(
			array(
				larijani_n_c(
					array(
						larijani_n_heading( larijani_n_fa( $i + 1 ), 'span', 'ls-n-num w-9 h-9 rounded-full font-headline-sm text-headline-sm ' . ( $tones[ $st['tone'] ] ?? $tones['primary'] ) ),
						larijani_n_iconw( $st['icon'], 'text-[22px] text-outline' ),
					),
					array( 'dir' => 'row', 'align' => 'center', 'justify' => 'space-between' )
				),
				larijani_n_c(
					array(
						larijani_n_heading( $st['title'], 'h3', 'font-title-card text-title-card text-surface-dark' ),
						larijani_n_text( $st['desc'], 'font-body-sm text-body-sm text-on-surface-variant leading-relaxed' ),
					),
					array( 'gap' => 4 )
				),
				larijani_n_heading( $st['note'], 'span', 'font-body-sm text-body-sm text-secondary font-medium' ),
			),
			array( 'gap' => 16, 'justify' => 'space-between', 'pad' => larijani_n_box( 16 ), 'class' => 'ls-n-card bg-surface-card rounded-2xl shadow-sm overflow-hidden' )
		);
	}
	$gallery = array();
	foreach ( (array) $s['gallery'] as $g ) {
		$gallery[] = larijani_n_c(
			array(
				larijani_n_w( 'image', array( 'image' => is_array( $g['image'] ) ? $g['image'] : $g['image'], 'image_size' => 'medium_large' ), 'ls-n-img-cover w-full sm:w-44 h-36 rounded-xl overflow-hidden shrink-0' ),
				larijani_n_c(
					array(
						larijani_n_heading( $g['eyebrow'], 'span', 'font-label-badge text-label-badge text-outline' ),
						larijani_n_heading( $g['title'], 'h3', 'font-headline-sm text-headline-sm text-surface-dark' ),
						larijani_n_text( $g['desc'], 'font-body-sm text-body-sm text-on-surface-variant leading-relaxed' ),
					),
					array( 'gap' => 4, 'width' => array( 'auto', 'auto', 100 ), 'grow' => true )
				),
			),
			array( 'dir' => array( 'row', 'row', 'column' ), 'align' => 'center', 'gap' => 16, 'pad' => larijani_n_box( 24 ), 'class' => 'ls-n-card bg-surface-card rounded-2xl shadow-sm' )
		);
	}
	$h = larijani_n_heading_args( $s, array( 'align' => 'center', 'style' => 'token' ) );
	$h['mb'] = array( 0 );
	return larijani_n_section(
		array(
			larijani_n_section_heading( $h ),
			larijani_n_c( $steps, array( 'grid' => array( (int) $s['columns'], (int) $s['columns_tablet'], (int) $s['columns_mobile'] ), 'gap' => 16 ) ),
			$gallery ? larijani_n_c( $gallery, array( 'grid' => array( 2, 2, 1 ), 'gap' => 24 ) ) : null,
		),
		array( 'class' => larijani_n_bg( $s['section_bg'] ?? '' ), 'py' => 64, 'px' => array( 32, 16, 16 ), 'gap' => 64 )
	);
}

/**
 * Page banner with response-time box (ls-page-banner).
 *
 * @param array $o Overrides.
 * @return array
 */
function larijani_nr_page_banner( $o = array() ) {
	$s    = larijani_n_settings( 'ls-page-banner', $o );
	$side = 'yes' === $s['show_side'];
	$pct  = max( 0, min( 100, (int) $s['side_percent'] ) );
	return larijani_n_section(
		array(
			larijani_n_c(
				array(
					larijani_n_c(
						array(
							$s['badge'] ? larijani_n_c(
								array( larijani_n_iconw( $s['badge_icon'], 'text-[16px]' ), larijani_n_heading( $s['badge'], 'span', 'font-label-badge text-label-badge font-bold' ) ),
								array( 'dir' => 'row', 'align' => 'center', 'gap' => 8, 'width' => 'auto', 'pad' => larijani_n_box( 6, 14 ), 'class' => 'ls-n-pill bg-white/10 backdrop-blur-md rounded-full max-w-full text-primary-fixed' )
							) : null,
							larijani_n_heading( $s['title'], 'h1', 'font-headline-lg text-headline-lg lg:font-display-hero lg:text-display-hero text-on-tertiary tracking-tight leading-snug' ),
							larijani_n_text( $s['desc'], 'font-body-lg text-body-lg text-outline-variant max-w-2xl leading-relaxed' ),
						),
						array( 'gap' => 16, 'align' => 'flex-start', 'width' => $side ? array( larijani_n_span( 8, 40 ), 100, 100 ) : 100 )
					),
					$side ? larijani_n_c(
						array(
							larijani_n_c(
								array(
									larijani_n_heading( $s['side_label'], 'span', 'font-label-badge text-label-badge text-secondary-fixed' ),
									larijani_n_heading( $s['side_value'], 'span', 'font-headline-sm text-headline-sm text-primary-fixed' ),
								),
								array( 'dir' => 'row', 'align' => 'center', 'justify' => 'space-between', 'gap' => 12, 'pad' => larijani_n_box( 0, 0, 4, 0 ) )
							),
							larijani_n_w(
								'progress',
								array(
									'title'        => larijani_strip_tags( $s['side_label'] . ' ' . $s['side_note_2'] ),
									'percent'      => array( 'unit' => '%', 'size' => $pct ),
									'display_percentage' => 'hide',
									'inner_text'   => '',
									'progress_type' => '',
								),
								'ls-n-progress'
							),
							larijani_n_c(
								array(
									larijani_n_heading( $s['side_note_1'], 'span', '' ),
									larijani_n_heading( $s['side_note_2'], 'span', 'text-on-tertiary' ),
								),
								array( 'dir' => 'row', 'align' => 'center', 'justify' => 'space-between', 'gap' => 8, 'pad' => larijani_n_box( 4, 0, 0, 0 ), 'class' => 'text-body-sm font-body-sm text-outline-variant' )
							),
						),
						array( 'gap' => 8, 'pad' => larijani_n_box( 16 ), 'width' => array( larijani_n_span( 4, 40 ), 100, 100 ), 'class' => 'bg-white/5 backdrop-blur-sm rounded-2xl' )
					) : null,
				),
				array( 'dir' => array( 'row', 'column', 'column' ), 'align' => array( 'flex-end', 'stretch', 'stretch' ), 'gap' => 40, 'pad' => array( larijani_n_box( 64 ), larijani_n_box( 24 ), larijani_n_box( 24 ) ), 'class' => 'ls-n-glow ls-n-glow--banner bg-surface-dark text-on-tertiary rounded-3xl overflow-hidden shadow-xl' )
			),
		),
		array( 'py' => array( array( 0, 24 ), array( 0, 24 ), array( 0, 24 ) ), 'px' => array( 32, 16, 16 ) )
	);
}

/**
 * Contact cards (ls-contact-cards).
 *
 * @param array $o Overrides.
 * @return array
 */
function larijani_nr_contact_cards( $o = array() ) {
	$s           = larijani_n_settings( 'ls-contact-cards', $o );
	$badge_tones = array(
		'light' => 'bg-surface-container-high text-on-surface-variant',
		'sage'  => 'bg-secondary-fixed-dim/30 text-on-secondary-container',
		'amber' => 'bg-accent-amber/10 text-accent-amber',
	);
	$cards       = array();
	foreach ( $s['items'] as $it ) {
		if ( 'hours' === $it['mode'] ) {
			$rows = array();
			foreach ( larijani_n_lines( $it['hours'] ) as $line ) {
				$p      = array_map( 'trim', explode( '|', $line ) );
				$muted  = 'muted' === ( $p[2] ?? '' );
				$color  = 'amber' === ( $p[2] ?? '' ) ? 'text-accent-amber' : ( 'emerald' === ( $p[2] ?? '' ) ? 'text-accent-emerald' : '' );
				$rows[] = larijani_n_c(
					array(
						larijani_n_c(
							array( larijani_n_iconw( $muted ? 'clock-history' : 'clock', 'text-[14px] ' . $color ), larijani_n_heading( $p[0] ?? '', 'span', '' ) ),
							array( 'dir' => 'row', 'align' => 'center', 'gap' => 4, 'width' => 'auto', 'class' => $muted ? '' : 'text-on-surface-variant' )
						),
						larijani_n_heading( $p[1] ?? '', 'span', $muted ? '' : 'font-bold' ),
					),
					array( 'dir' => 'row', 'align' => 'center', 'justify' => 'space-between', 'gap' => 8, 'pad' => larijani_n_box( 4, 0 ), 'class' => $muted ? 'text-outline' : 'text-on-surface' )
				);
			}
			$bottom = larijani_n_c( $rows, array( 'gap' => 4, 'pad' => larijani_n_box( 8 ), 'class' => 'mt-space-lg text-body-sm font-body-sm bg-surface-canvas rounded-xl' ) );
		} else {
			$tel    = 'tel:' . preg_replace( '/[^0-9+]/', '', strtr( (string) $it['phone'], array( '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9' ) ) );
			$wa_num = preg_replace( '/\D+/', '', (string) ( $it['whatsapp'] ? $it['whatsapp'] : $it['phone'] ) );
			$wa_num = 0 === strpos( $wa_num, '0' ) ? '98' . substr( $wa_num, 1 ) : $wa_num;
			$bottom = larijani_n_c(
				array(
					larijani_n_button( larijani_n_phone( $it['phone'] ), $tel, $it['phone_icon'], 'ls-n-btn ls-n-btn--phone-row', 'end' ),
					larijani_n_c(
						array(
							larijani_n_button( $it['btn1_text'], $tel, 'telephone', 'ls-n-btn ls-n-btn--primary ls-n-btn--block ls-n-btn--sm' ),
							larijani_n_button( $it['btn2_text'], 'https://wa.me/' . $wa_num, 'whatsapp', 'ls-n-btn ' . ( 'yes' === $it['btn2_dark'] ? 'ls-n-btn--dark' : 'ls-n-btn--emerald' ) . ' ls-n-btn--block ls-n-btn--sm' ),
						),
						array( 'grid' => array( 2, 2, 2 ), 'gap' => 4 )
					),
				),
				array( 'gap' => 8, 'pad' => larijani_n_box( 24, 0, 0, 0 ) )
			);
		}
		$cards[] = larijani_n_c(
			array(
				larijani_n_c(
					array(
						larijani_n_c(
							array(
								larijani_n_iconw( $it['icon'], 'w-14 h-14 rounded-2xl shadow-inner text-[28px] ' . larijani_n_tone( $it['icon_tone'] ) ),
								larijani_n_heading( $it['badge'], 'span', 'ls-n-badge font-label-badge text-label-badge px-2.5 py-1 rounded-full ' . ( $badge_tones[ $it['badge_tone'] ] ?? $badge_tones['light'] ) ),
							),
							array( 'dir' => 'row', 'align' => 'center', 'justify' => 'space-between', 'gap' => 8 )
						),
						larijani_n_c(
							array(
								larijani_n_heading( $it['title'], 'h2', 'font-headline-sm text-headline-sm text-on-surface' ),
								larijani_n_text( $it['desc'], 'font-body-md text-body-md text-on-surface-variant leading-relaxed' ),
							),
							array( 'gap' => 4 )
						),
					),
					array( 'gap' => 16 )
				),
				$bottom,
			),
			array( 'justify' => 'space-between', 'pad' => larijani_n_box( 24 ), 'class' => 'ls-n-card bg-surface-card rounded-2xl shadow-sm hover:shadow-md transition-shadow' )
		);
	}
	return larijani_n_section(
		array( larijani_n_c( $cards, array( 'grid' => array( (int) $s['columns'], (int) $s['columns_tablet'], (int) $s['columns_mobile'] ), 'gap' => 24 ) ) ),
		array( 'py' => 24, 'px' => array( 32, 16, 16 ) )
	);
}

/**
 * Wrap a theme widget row in a zero-padding full-width container (used by
 * larijani_el_build() for rows that are not native).
 *
 * @param array $widget Widget element.
 * @return array
 */
function larijani_n_widget_row( $widget ) {
	return larijani_n_c( array( $widget ), array( 'class' => 'ls-n-wrap' ) );
}
