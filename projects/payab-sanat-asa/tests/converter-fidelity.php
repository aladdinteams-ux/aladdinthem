<?php
/**
 * Converter fidelity test (no Elementor needed): renders every converted page tree with a minimal emulation of
 * Elementor output (container -> its html_tag with classes/attributes, text-editor -> its markup, Payab Element -> real
 * widget render()) and compares the DOM with the approved <main> markup (normalised).
 * It proves the conversion is lossless in markup; it does NOT prove how real Elementor wraps it.
 *
 * Run: ALADDIN_WP_LOAD=/path/wp-load.php php tests/converter-fidelity.php SRC_DIR
 */
namespace Elementor {
	class Controls_Manager { const TEXT = 'text'; const SELECT = 'select'; const MEDIA = 'media'; const TEXTAREA = 'textarea'; const TAB_LAYOUT = 'layout'; const TAB_ADVANCED = 'adv'; const WYSIWYG = 'w'; }
	abstract class Widget_Base {
		public $s = array();
		public function __construct() {}
		public function get_settings_for_display() { return $this->s; }
		public function before_render() {}
		public function after_render() {}
		public function start_controls_section() {}
		public function end_controls_section() {}
		public function add_control() {}
	}
}
namespace {
	define( 'LS_TEST_USER', 1 );
	$_SERVER['HTTP_HOST'] = getenv( 'ALADDIN_HOST' ) ?: 'localhost:8091';
	require getenv( 'ALADDIN_WP_LOAD' );
	require_once ALADDIN_PAYAB_SANAT_ASA_CORE_DIR . 'includes/elementor/class-elementor.php';
	require_once ALADDIN_PAYAB_SANAT_ASA_CORE_DIR . 'includes/elementor/class-raw-widget.php';
	require_once ALADDIN_PAYAB_SANAT_ASA_CORE_DIR . 'includes/elementor/class-element-widget.php';
	require_once ALADDIN_PAYAB_SANAT_ASA_CORE_DIR . 'includes/elementor/class-text-widget.php';

	$src   = $argv[1];
	$pages = json_decode( file_get_contents( ALADDIN_PAYAB_SANAT_ASA_CORE_DIR . 'data/pages.json' ), true );

	function render_el( $e ) {
		if ( 'widget' === $e['elType'] ) {
			$cls  = 'aladdin_payab_sanat_asa_text' === $e['widgetType'] ? '\Aladdin\PayabSanatAsa\Text_Widget' : '\Aladdin\PayabSanatAsa\Element_Widget';
			$w    = new $cls();
			$w->s = $e['settings'];
			ob_start();
			$m = new ReflectionMethod( $w, 'render' );
			$m->setAccessible( true );
			$m->invoke( $w );
			return ob_get_clean();
		}
		$st  = $e['settings'];
		$tag = isset( $st['html_tag'] ) ? $st['html_tag'] : 'div';
		$at  = '';
		if ( ! empty( $st['_css_classes'] ) ) { $at .= ' class="' . esc_attr( $st['_css_classes'] ) . '"'; }
		if ( ! empty( $st['_element_id'] ) ) { $at .= ' id="' . esc_attr( $st['_element_id'] ) . '"'; }
		foreach ( \Aladdin\PayabSanatAsa\Elementor_Integration::parse_attrs( isset( $st['aladdin_attrs'] ) ? $st['aladdin_attrs'] : '' ) as $k => $v ) {
			$at .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
		}
		$in = '';
		foreach ( $e['elements'] as $c ) { $in .= render_el( $c ); }
		return "<$tag$at>$in</$tag>";
	}

	/** Canonical form: DOM -> sorted-attribute tree string, whitespace-collapsed text. */
	function canon( $html ) {
		$html = aladdin_payab_sanat_asa_resolve_tokens( $html );
		$d    = new DOMDocument();
		libxml_use_internal_errors( true );
		$d->loadHTML( '<?xml encoding="utf-8"?><div id="root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
		libxml_clear_errors();
		$out = array();
		$walk = function ( $n ) use ( &$walk, &$out ) {
			if ( XML_TEXT_NODE === $n->nodeType ) {
				$t = trim( preg_replace( '/\s+/u', ' ', $n->nodeValue ) );
				if ( '' !== $t ) { $out[] = 'T:' . $t; }
				return;
			}
			if ( XML_ELEMENT_NODE !== $n->nodeType ) { return; }
			$at = array();
			foreach ( $n->attributes as $a ) {
				$v = $a->value;
				if ( 'class' === $a->name ) { $v = implode( ' ', array_values( array_diff( preg_split( '/\s+/', trim( $v ) ), array( 'aladdin-pz' ) ) ) ); }
				if ( 'class' === $a->name && '' === $v ) { continue; }
				$at[ strtolower( $a->name ) ] = $v;
			}
			ksort( $at );
			$out[] = '<' . $n->nodeName . ' ' . json_encode( $at, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
			foreach ( $n->childNodes as $c ) { $walk( $c ); }
			$out[] = '>';
		};
		$walk( $d->documentElement );
		return $out;
	}

	$bad = 0; $n = 0;
	foreach ( $pages as $p ) {
		$n++;
		$tree = '';
		foreach ( $p['elementor'] as $e ) { $tree .= render_el( $e ); }
		$a = canon( $p['fallback'] );
		$b = canon( $tree );
		// Element widgets drop empty "class" and normalise boolean attrs; compare after removing that noise.
		$noise = function ( $arr ) { return array_values( array_filter( array_map( function ( $x ) { return preg_replace( '/,"(id|class)":""/', '', $x ); }, $arr ) ) ); };
		if ( $noise( $a ) !== $noise( $b ) ) {
			$bad++;
			$diff = array_values( array_diff_assoc( $noise( $a ), $noise( $b ) ) );
			echo 'MISMATCH ', $p['lang'], ':', $p['group'], ' first: ', substr( (string) ( $diff[0] ?? '' ), 0, 220 ), "\n";
			$diff2 = array_values( array_diff_assoc( $noise( $b ), $noise( $a ) ) );
			echo '    got: ', substr( (string) ( $diff2[0] ?? '' ), 0, 220 ), "\n";
		}
	}
	echo "pages=$n mismatches=$bad\n";
	exit( $bad ? 1 : 0 );
}
