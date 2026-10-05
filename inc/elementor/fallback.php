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
	return ! ls_has_elementor() && (bool) apply_filters( 'ls_fallback_render', true );
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
			case 'section':
			case 'container':
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
	echo '<div class="ls-fb-widget">';
	if ( $class ) {
		$widget = new $class( $el );
		$widget->ls_print();
	} else {
		switch ( $type ) {
			case 'heading':
				$tag = in_array( $s['header_size'] ?? 'h2', array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'p' ), true ) ? $s['header_size'] : 'h2';
				echo '<' . $tag . ' class="ls-fb-heading">' . wp_kses_post( $s['title'] ?? '' ) . '</' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $tag is whitelisted.
				break;
			case 'text-editor':
				echo '<div class="ls-prose">' . wp_kses_post( wpautop( $s['editor'] ?? '' ) ) . '</div>';
				break;
			case 'image':
				if ( ! empty( $s['image']['url'] ) ) {
					echo '<img src="' . esc_url( $s['image']['url'] ) . '" alt="" loading="lazy">';
				}
				break;
			case 'shortcode':
				echo do_shortcode( (string) ( $s['shortcode'] ?? '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;
			case 'html':
				echo wp_kses_post( $s['html'] ?? '' );
				break;
			case 'button':
				if ( ! empty( $s['text'] ) ) {
					echo '<a class="ls-fb-button" href="' . esc_url( $s['link']['url'] ?? '#' ) . '">' . esc_html( $s['text'] ) . '</a>';
				}
				break;
		}
	}
	echo '</div>';
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
