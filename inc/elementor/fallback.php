<?php
/**
 * Graceful fallback when Elementor is not active.
 *
 * Pages created by the theme store their layout as Elementor data
 * (`_elementor_data`). If Elementor is deactivated (or not installed yet) the
 * theme still renders that saved layout with its own widget classes, so the
 * site never shows empty pages. Editing still requires Elementor.
 *
 * Only theme widgets (ls-*) and a few basic core widgets are rendered here;
 * anything else is skipped silently.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Minimal stand-in for \Elementor\Repeater (collects field definitions only).
 */
class LS_Fallback_Repeater {

	/**
	 * Controls.
	 *
	 * @var array
	 */
	private $controls = array();

	/**
	 * Add a field.
	 *
	 * @param string $id Id.
	 * @param array  $args Args.
	 */
	public function add_control( $id, $args ) {
		$args['name']          = $id;
		$this->controls[ $id ] = $args;
	}

	/**
	 * Fields.
	 *
	 * @return array
	 */
	public function get_controls() {
		return $this->controls;
	}
}

/**
 * Minimal widget base used only when Elementor is unavailable. It records
 * control defaults so saved settings can be completed exactly like Elementor does.
 */
abstract class LS_Fallback_Widget {

	/**
	 * Element data.
	 *
	 * @var array
	 */
	protected $ls_data = array();

	/**
	 * Control definitions.
	 *
	 * @var array
	 */
	protected $ls_controls = array();

	/**
	 * Constructor.
	 *
	 * @param array $data Element data (id, settings).
	 * @param mixed $args Unused.
	 */
	public function __construct( $data = array(), $args = null ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		$this->ls_data = is_array( $data ) ? $data : array();
		$this->register_controls();
	}

	/**
	 * Register controls (implemented by widgets).
	 */
	abstract protected function register_controls();

	/**
	 * No-op section helpers.
	 */
	public function start_controls_section() {}
	public function end_controls_section() {} // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	public function start_controls_tabs() {} // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	public function end_controls_tabs() {} // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	public function start_controls_tab() {} // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	public function end_controls_tab() {} // phpcs:ignore Squiz.Commenting.FunctionComment.Missing
	public function add_group_control() {} // phpcs:ignore Squiz.Commenting.FunctionComment.Missing

	/**
	 * Record a control.
	 *
	 * @param string $id Id.
	 * @param array  $args Args.
	 */
	public function add_control( $id, $args ) {
		$this->ls_controls[ $id ] = $args;
	}

	/**
	 * Record a responsive control.
	 *
	 * @param string $id Id.
	 * @param array  $args Args.
	 */
	public function add_responsive_control( $id, $args ) {
		$this->ls_controls[ $id ] = $args;
	}

	/**
	 * Element id.
	 *
	 * @return string
	 */
	public function get_id() {
		return isset( $this->ls_data['id'] ) ? (string) $this->ls_data['id'] : substr( md5( spl_object_hash( $this ) ), 0, 7 );
	}

	/**
	 * Saved settings completed with control defaults.
	 *
	 * @return array
	 */
	public function get_settings_for_display() {
		$saved = isset( $this->ls_data['settings'] ) && is_array( $this->ls_data['settings'] ) ? $this->ls_data['settings'] : array();
		$out   = array();
		foreach ( $this->ls_controls as $id => $c ) {
			if ( array_key_exists( 'default', $c ) ) {
				$out[ $id ] = $c['default'];
			}
		}
		foreach ( $saved as $k => $v ) {
			if ( '__dynamic__' === $k || '__globals__' === $k ) {
				continue;
			}
			$out[ $k ] = $v;
		}
		// Like Elementor, complete repeater rows with their field defaults.
		foreach ( $this->ls_controls as $id => $c ) {
			if ( empty( $c['fields'] ) || empty( $out[ $id ] ) || ! is_array( $out[ $id ] ) ) {
				continue;
			}
			$defaults = array();
			foreach ( $c['fields'] as $fid => $field ) {
				$fid = $field['name'] ?? $fid;
				if ( array_key_exists( 'default', $field ) ) {
					$defaults[ $fid ] = $field['default'];
				}
			}
			foreach ( $out[ $id ] as $i => $row ) {
				$out[ $id ][ $i ] = is_array( $row ) ? array_merge( $defaults, $row ) : $row;
			}
		}
		return $out;
	}

	/**
	 * Print the widget (wraps the protected render()).
	 */
	public function ls_print() {
		$this->render();
	}

	/**
	 * Render (implemented by LS_Widget_Base).
	 */
	abstract protected function render();
}

/**
 * Should the fallback renderer be used?
 *
 * @return bool
 */
function ls_fallback_active() {
	return ! ls_has_elementor() && ! defined( 'ELEMENTOR_VERSION' ) && (bool) apply_filters( 'ls_fallback_render', true );
}

/**
 * Widget type (e.g. "ls-hero") => class name, loading the class files on demand.
 *
 * @param string $type Widget type.
 * @return string Class name or ''.
 */
function ls_fallback_widget_class( $type ) {
	if ( 0 !== strpos( $type, 'ls-' ) ) {
		return '';
	}
	$slug = substr( $type, 3 );
	$map  = ls_elementor_widgets();
	if ( empty( $map[ $slug ] ) ) {
		return '';
	}
	require_once LS_DIR . '/inc/elementor/class-widget-base.php';
	$file = LS_DIR . '/inc/elementor/widgets/' . $slug . '.php';
	if ( file_exists( $file ) ) {
		require_once $file;
	}
	return class_exists( $map[ $slug ] ) ? $map[ $slug ] : '';
}

/**
 * Pixel value of an Elementor dimensions setting side.
 *
 * @param array  $dim Dimensions.
 * @param string $side top|right|bottom|left.
 * @return string CSS value or ''.
 */
function ls_fallback_dim( $dim, $side ) {
	if ( ! is_array( $dim ) || ! isset( $dim[ $side ] ) || '' === $dim[ $side ] ) {
		return '';
	}
	return (float) $dim[ $side ] . ( $dim['unit'] ?? 'px' );
}

/**
 * Render a list of Elementor elements.
 *
 * @param array $elements Elements.
 */
function ls_fallback_render_elements( $elements ) {
	foreach ( (array) $elements as $el ) {
		if ( ! is_array( $el ) || empty( $el['elType'] ) ) {
			continue;
		}
		$s = isset( $el['settings'] ) && is_array( $el['settings'] ) ? $el['settings'] : array();
		switch ( $el['elType'] ) {
			case 'container':
				ls_fallback_render_container( $el );
				break;
			case 'section':
				$boxed = 'boxed' === ( $s['layout'] ?? '' ) || 'boxed' === ( $s['content_width'] ?? '' );
				$style = '';
				foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
					$v = ls_fallback_dim( $s['padding'] ?? array(), $side );
					if ( '' !== $v ) {
						$style .= 'padding-' . $side . ':' . $v . ';';
					}
				}
				if ( ! empty( $s['background_color'] ) ) {
					$style .= 'background-color:' . $s['background_color'] . ';';
				}
				echo '<div class="ls-fb-section"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>';
				$max = $boxed && ! empty( $s['content_width']['size'] ) ? (int) $s['content_width']['size'] : 0;
				echo '<div class="ls-fb-row"' . ( $max ? ' style="max-width:' . esc_attr( $max ) . 'px;margin-inline:auto"' : '' ) . '>';
				ls_fallback_render_elements( $el['elements'] ?? array() );
				echo '</div></div>';
				break;
			case 'column':
				$w     = ! empty( $s['_inline_size'] ) ? (float) $s['_inline_size'] : (float) ( $s['_column_size'] ?? 100 );
				$style = '--ls-fb-w:' . $w . '%;';
				foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
					$v = ls_fallback_dim( $s['padding'] ?? array(), $side );
					if ( '' !== $v ) {
						$style .= 'padding-' . $side . ':' . $v . ';';
					}
				}
				echo '<div class="ls-fb-col" style="' . esc_attr( $style ) . '">';
				ls_fallback_render_elements( $el['elements'] ?? array() );
				echo '</div>';
				break;
			case 'widget':
				ls_fallback_render_widget( $el );
				break;
		}
	}
}

/**
 * Render one widget.
 *
 * @param array $el Element.
 */
function ls_fallback_render_widget( $el ) {
	$type  = (string) ( $el['widgetType'] ?? '' );
	$s     = isset( $el['settings'] ) && is_array( $el['settings'] ) ? $el['settings'] : array();
	$class = ls_fallback_widget_class( $type );
	if ( ! $class ) {
		// Core widgets: Elementor's own markup so the theme CSS applies.
		if ( ls_fallback_native_widget( $el ) ) {
			return;
		}
		if ( 'shortcode' === $type ) {
			echo '<div class="ls-fb-widget">' . do_shortcode( (string) ( $s['shortcode'] ?? '' ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} elseif ( 'html' === $type ) {
			echo '<div class="ls-fb-widget">' . wp_kses_post( $s['html'] ?? '' ) . '</div>';
		}
		return;
	}
	echo '<div class="ls-fb-widget">';
	$widget = new $class( $el );
	$widget->ls_print();
	echo '</div>';
}

/**
 * Responsive values of a setting (desktop, tablet ≤1024px, mobile ≤767px).
 *
 * @param array  $s   Settings.
 * @param string $key Key.
 * @return array [ '' => v, '_tablet' => v, '_mobile' => v ] (only set ones).
 */
function ls_fallback_resp( $s, $key ) {
	$out = array();
	foreach ( array( '', '_tablet', '_mobile' ) as $sfx ) {
		if ( isset( $s[ $key . $sfx ] ) && '' !== $s[ $key . $sfx ] && array() !== $s[ $key . $sfx ] ) {
			$out[ $sfx ] = $s[ $key . $sfx ];
		}
	}
	return $out;
}

/**
 * Render a container the way Elementor does (classes + CSS variables).
 *
 * @param array $el Element.
 */
function ls_fallback_render_container( $el ) {
	$s     = isset( $el['settings'] ) && is_array( $el['settings'] ) ? $el['settings'] : array();
	$id    = preg_replace( '/[^a-z0-9]/i', '', (string) ( $el['id'] ?? wp_rand() ) );
	$grid  = 'grid' === ( $s['container_type'] ?? '' );
	$boxed = 'boxed' === ( $s['content_width'] ?? 'boxed' );
	$css   = array( '' => array(), '_tablet' => array(), '_mobile' => array() );
	$num   = static function ( $v, $unit = 'px' ) {
		return is_numeric( $v ) ? (float) $v . $unit : '';
	};
	$css['']['--display'] = $grid ? 'grid' : 'flex';
	foreach ( ls_fallback_resp( $s, 'padding' ) as $sfx => $p ) {
		foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
			if ( isset( $p[ $side ] ) && '' !== $p[ $side ] ) {
				$css[ $sfx ][ '--padding-' . $side ] = $num( $p[ $side ], $p['unit'] ?? 'px' );
			}
		}
	}
	foreach ( ls_fallback_resp( $s, $grid ? 'grid_gaps' : 'flex_gap' ) as $sfx => $g ) {
		$col = $num( $g['column'] ?? ( $g['size'] ?? '' ), $g['unit'] ?? 'px' );
		$row = $num( $g['row'] ?? ( $g['size'] ?? '' ), $g['unit'] ?? 'px' );
		if ( '' !== $col ) {
			$css[ $sfx ]['--column-gap'] = $col;
			$css[ $sfx ]['--row-gap']    = '' !== $row ? $row : $col;
		}
	}
	if ( $grid ) {
		foreach ( ls_fallback_resp( $s, 'grid_columns_grid' ) as $sfx => $c ) {
			if ( isset( $c['size'] ) && is_numeric( $c['size'] ) ) {
				$css[ $sfx ]['--e-con-grid-template-columns'] = 'repeat(' . (int) $c['size'] . ', minmax(0, 1fr))';
			}
		}
		if ( ! isset( $s['grid_columns_grid_mobile'] ) ) {
			$css['_mobile']['--e-con-grid-template-columns'] = 'repeat(1, minmax(0, 1fr))';
		}
		if ( ! empty( $s['grid_align_items'] ) ) {
			$css['']['--align-items'] = $s['grid_align_items'];
		}
	} else {
		foreach ( array( 'flex_direction' => '--flex-direction', 'flex_align_items' => '--align-items', 'flex_justify_content' => '--justify-content' ) as $key => $var ) {
			foreach ( ls_fallback_resp( $s, $key ) as $sfx => $d ) {
				$css[ $sfx ][ $var ] = (string) $d;
			}
		}
		if ( ! empty( $s['flex_wrap'] ) ) {
			$css['']['--flex-wrap'] = $s['flex_wrap'];
		}
	}
	foreach ( ls_fallback_resp( $s, 'boxed_width' ) as $sfx => $w ) {
		$css[ $sfx ]['--content-width'] = 'min(100%, ' . $num( $w['size'] ?? '', $w['unit'] ?? 'px' ) . ')';
	}
	if ( ! $boxed ) {
		foreach ( ls_fallback_resp( $s, 'width' ) as $sfx => $w ) {
			$css[ $sfx ]['--width'] = 'custom' === ( $w['unit'] ?? '' ) ? (string) $w['size'] : $num( $w['size'] ?? '', $w['unit'] ?? '%' );
		}
	}
	if ( 'none' === ( $s['_flex_size'] ?? '' ) ) {
		$css['']['flex-shrink'] = '0';
	}
	if ( 'grow' === ( $s['_flex_size'] ?? '' ) || ( 'custom' === ( $s['_flex_size'] ?? '' ) && ! empty( $s['_flex_grow'] ) ) ) {
		$css['']['flex-grow']   = '1';
		$css['']['flex-shrink'] = 'custom' === $s['_flex_size'] ? (string) (int) ( $s['_flex_shrink'] ?? 1 ) : '0';
	}
	$rules = '';
	$media = array( '' => '', '_tablet' => '@media (max-width:1024px)', '_mobile' => '@media (max-width:767px)' );
	foreach ( $css as $sfx => $decl ) {
		$decl = array_filter( $decl, 'strlen' );
		if ( ! $decl ) {
			continue;
		}
		$body = '.ls-fb-content .elementor-element-' . $id . '{';
		foreach ( $decl as $k => $v ) {
			$body .= $k . ':' . preg_replace( '/[^a-z0-9%.,() _-]/i', '', $v ) . ';';
		}
		$body  .= '}';
		$rules .= $media[ $sfx ] ? $media[ $sfx ] . '{' . $body . '}' : $body;
	}
	$classes = array( 'elementor-element', 'elementor-element-' . $id, 'e-con', $grid ? 'e-grid' : 'e-flex', $boxed ? 'e-con-boxed' : 'e-con-full' );
	if ( ! empty( $s['css_classes'] ) ) {
		$classes[] = $s['css_classes'];
	}
	$tag   = in_array( $s['html_tag'] ?? '', array( 'section', 'article', 'aside', 'header', 'footer', 'nav', 'main', 'a' ), true ) ? $s['html_tag'] : 'div';
	$attrs = 'a' === $tag ? ls_fallback_link_attrs( $s['link'] ?? '' ) : '';
	$tag   = 'a' === $tag && ! $attrs ? 'div' : $tag;
	echo '<style>' . $rules . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- values sanitised above.
	echo '<' . $tag . ' class="' . esc_attr( implode( ' ', $classes ) ) . '"' . ( ! empty( $s['_element_id'] ) ? ' id="' . esc_attr( $s['_element_id'] ) . '"' : '' ) . $attrs . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tag is whitelisted, $attrs escaped.
	if ( $boxed ) {
		echo '<div class="e-con-inner">';
	}
	ls_fallback_render_elements( $el['elements'] ?? array() );
	if ( $boxed ) {
		echo '</div>';
	}
	echo '</' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Icon markup for a core icon setting.
 *
 * @param mixed $icon Icon setting.
 * @return string
 */
function ls_fallback_icon( $icon ) {
	$v = is_array( $icon ) ? ( $icon['value'] ?? '' ) : (string) $icon;
	if ( is_array( $v ) ) {
		return ! empty( $v['url'] ) ? '<img src="' . esc_url( $v['url'] ) . '" alt="" width="16" height="16">' : '';
	}
	return '' !== $v ? '<i aria-hidden="true" class="' . esc_attr( $v ) . '"></i>' : '';
}

/**
 * Link attributes of a core link setting.
 *
 * @param mixed $link Link.
 * @return string Attributes (escaped) or '' when there is no URL.
 */
function ls_fallback_link_attrs( $link ) {
	$url = is_array( $link ) ? ( $link['url'] ?? '' ) : (string) $link;
	if ( '' === $url ) {
		return '';
	}
	$ext   = is_array( $link ) && ! empty( $link['is_external'] );
	$rel   = array_filter( array( $ext ? 'noopener' : '', is_array( $link ) && ! empty( $link['nofollow'] ) ? 'nofollow' : '' ) );
	$attrs = ' href="' . esc_url( $url ) . '"' . ( $ext ? ' target="_blank"' : '' );
	return $attrs . ( $rel ? ' rel="' . esc_attr( implode( ' ', $rel ) ) . '"' : '' );
}

/**
 * Render a core Elementor widget with Elementor's markup (no Elementor needed).
 *
 * @param array $el Element.
 * @return bool True when handled.
 */
function ls_fallback_native_widget( $el ) {
	$type = (string) ( $el['widgetType'] ?? '' );
	$s    = isset( $el['settings'] ) && is_array( $el['settings'] ) ? $el['settings'] : array();
	$id   = preg_replace( '/[^a-z0-9]/i', '', (string) ( $el['id'] ?? '' ) );
	$html = '';
	switch ( $type ) {
		case 'heading':
			$tag   = in_array( $s['header_size'] ?? 'h2', array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ), true ) ? $s['header_size'] : 'h2';
			$title = wp_kses_post( $s['title'] ?? '' );
			$attrs = ls_fallback_link_attrs( $s['link'] ?? '' );
			$html  = '<' . $tag . ' class="elementor-heading-title elementor-size-default">' . ( $attrs ? '<a' . $attrs . '>' . $title . '</a>' : $title ) . '</' . $tag . '>';
			break;
		case 'text-editor':
			$html = wp_kses_post( wpautop( $s['editor'] ?? '' ) );
			break;
		case 'image':
			if ( ! empty( $s['image']['url'] ) ) {
				$img   = ! empty( $s['image']['id'] ) ? wp_get_attachment_image( (int) $s['image']['id'], $s['image_size'] ?? 'large', false, array( 'loading' => 'lazy' ) ) : '';
				$html  = $img ? $img : '<img src="' . esc_url( $s['image']['url'] ) . '" alt="' . esc_attr( $s['image']['alt'] ?? '' ) . '" loading="lazy" decoding="async">';
				$attrs = 'custom' === ( $s['link_to'] ?? '' ) ? ls_fallback_link_attrs( $s['link'] ?? '' ) : '';
				$html  = $attrs ? '<a' . $attrs . '>' . $html . '</a>' : $html;
			}
			break;
		case 'button':
			if ( '' === (string) ( $s['text'] ?? '' ) ) {
				return true;
			}
			$attrs = ls_fallback_link_attrs( $s['link'] ?? '' );
			$icon  = ls_fallback_icon( $s['selected_icon'] ?? '' );
			$dir   = 'row' === ( $s['icon_align'] ?? ( is_rtl() ? 'row-reverse' : 'row' ) ) ? 'row' : 'row-reverse';
			$inner = '<span class="elementor-button-content-wrapper" style="flex-direction:' . $dir . '">' . ( $icon ? '<span class="elementor-button-icon">' . $icon . '</span>' : '' ) . '<span class="elementor-button-text">' . esc_html( $s['text'] ) . '</span></span>';
			$html  = $attrs ? '<a class="elementor-button elementor-button-link elementor-size-sm"' . $attrs . '>' . $inner . '</a>' : '<span class="elementor-button elementor-size-sm">' . $inner . '</span>';
			break;
		case 'icon':
			$icon  = ls_fallback_icon( $s['selected_icon'] ?? '' );
			$attrs = ls_fallback_link_attrs( $s['link'] ?? '' );
			$html  = '<div class="elementor-icon-wrapper">' . ( $attrs ? '<a class="elementor-icon"' . $attrs . '>' . $icon . '</a>' : '<div class="elementor-icon">' . $icon . '</div>' ) . '</div>';
			break;
		case 'icon-list':
			$items = '';
			foreach ( (array) ( $s['icon_list'] ?? array() ) as $it ) {
				$icon   = ls_fallback_icon( $it['selected_icon'] ?? '' );
				$inner  = ( $icon ? '<span class="elementor-icon-list-icon">' . $icon . '</span>' : '' ) . '<span class="elementor-icon-list-text">' . esc_html( $it['text'] ?? '' ) . '</span>';
				$attrs  = ls_fallback_link_attrs( $it['link'] ?? '' );
				$items .= '<li class="elementor-icon-list-item">' . ( $attrs ? '<a' . $attrs . '>' . $inner . '</a>' : $inner ) . '</li>';
			}
			$html = '<ul class="elementor-icon-list-items' . ( 'inline' === ( $s['view'] ?? '' ) ? ' elementor-inline-items' : '' ) . '">' . $items . '</ul>';
			break;
		case 'star-rating':
			$rating = max( 0, min( 5, (float) ( $s['rating'] ?? 5 ) ) );
			$stars  = '';
			for ( $i = 1; $i <= 5; $i++ ) {
				$stars .= $i <= $rating ? '<i class="elementor-star-full">&#9733;</i>' : '<i class="elementor-star-empty">&#9734;</i>';
			}
			/* translators: %s: rating out of 5. */
			$html = '<div class="elementor-star-rating" title="' . esc_attr( sprintf( __( 'امتیاز %s از ۵', 'larijani' ), $rating ) ) . '">' . $stars . '</div>';
			break;
		case 'progress':
			$pct  = max( 0, min( 100, (int) ( $s['percent']['size'] ?? 0 ) ) );
			$html = '<div class="elementor-progress-wrapper" role="progressbar" aria-label="' . esc_attr( $s['title'] ?? '' ) . '" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' . $pct . '"><div class="elementor-progress-bar" style="width:' . $pct . '%"></div></div>';
			break;
		default:
			return false;
	}
	$rules = '';
	$media = array( '' => '', '_tablet' => '@media (max-width:1024px)', '_mobile' => '@media (max-width:767px)' );
	foreach ( ls_fallback_resp( $s, '_margin' ) as $sfx => $m ) {
		$vals = array();
		foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
			$vals[] = is_numeric( $m[ $side ] ?? '' ) ? (float) $m[ $side ] . ( $m['unit'] ?? 'px' ) : '0';
		}
		$body   = '.ls-fb-content .elementor-element-' . $id . '{margin:' . preg_replace( '/[^a-z0-9%. -]/i', '', implode( ' ', $vals ) ) . '}';
		$rules .= $media[ $sfx ] ? $media[ $sfx ] . '{' . $body . '}' : $body;
	}
	if ( $rules ) {
		echo '<style>' . $rules . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitised numbers.
	}
	$classes = 'elementor-element elementor-element-' . $id . ' elementor-widget elementor-widget-' . $type . ( ! empty( $s['_css_classes'] ) ? ' ' . $s['_css_classes'] : '' );
	echo '<div class="' . esc_attr( $classes ) . '"' . ( ! empty( $s['_element_id'] ) ? ' id="' . esc_attr( $s['_element_id'] ) . '"' : '' ) . '>' . $html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts.
	return true;
}

/**
 * Render a post's saved Elementor layout.
 *
 * @param int $post_id Post id.
 * @return string HTML ('' when the post has no layout).
 */
function ls_fallback_render_post( $post_id ) {
	static $stack = array();
	if ( isset( $stack[ $post_id ] ) ) {
		return '';
	}
	$raw  = get_post_meta( $post_id, '_elementor_data', true );
	$data = is_array( $raw ) ? $raw : json_decode( (string) $raw, true );
	if ( ! is_array( $data ) || ! $data ) {
		return '';
	}
	$stack[ $post_id ] = true;
	ob_start();
	echo '<div class="ls-fb-content">';
	ls_fallback_render_elements( $data );
	echo '</div>';
	unset( $stack[ $post_id ] );
	return (string) ob_get_clean();
}

/**
 * Replace the content of builder pages with the fallback rendering.
 *
 * @param string $content Content.
 * @return string
 */
function ls_fallback_the_content( $content ) {
	if ( ! ls_fallback_active() ) {
		return $content;
	}
	$id = get_the_ID();
	if ( ! $id || 'builder' !== get_post_meta( $id, '_elementor_edit_mode', true ) ) {
		return $content;
	}
	$html = ls_fallback_render_post( $id );
	return '' !== $html ? $html : $content;
}
add_filter( 'the_content', 'ls_fallback_the_content', 9999 );
