<?php
/**
 * Base class for every Larijani Stone Elementor widget.
 *
 * Provides compact helpers to declare controls, a shared "theme colours /
 * layout / typography" style section (CSS-variable driven, so a colour picked
 * in one widget recolours that widget only) and a scoped `.ls-root` wrapper.
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/*
 * With Elementor active the widgets extend \Elementor\Widget_Base. Without it
 * they extend a tiny compatible fallback (inc/elementor/fallback.php) so pages
 * created by the theme still render their saved layout.
 */
if ( class_exists( 'Elementor\\Widget_Base' ) ) {
	abstract class Larijani_Widget_Parent extends \Elementor\Widget_Base {} // phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound
} else {
	abstract class Larijani_Widget_Parent extends Larijani_Fallback_Widget {} // phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound
}

/**
 * Base widget.
 */
abstract class Larijani_Widget_Base extends Larijani_Widget_Parent {

	/**
	 * Elementor control-type / tab constant, or its documented string value without Elementor.
	 *
	 * @param string $name Constant name, e.g. TEXT.
	 * @return string
	 */
	protected static function cm( $name ) {
		$const = 'Elementor\\Controls_Manager::' . $name;
		if ( defined( $const ) ) {
			return constant( $const );
		}
		$map = array(
			'TAB_CONTENT' => 'content',
			'TAB_STYLE'   => 'style',
			'ICONS'       => 'icons',
			'SWITCHER'    => 'switcher',
		);
		return $map[ $name ] ?? strtolower( $name );
	}

	/**
	 * Category.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'larijani-stone' );
	}

	/**
	 * Keywords.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'larijani', 'لاریجانی', 'stone', 'سنگ' );
	}

	/**
	 * Styles.
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return array( 'larijani-bootstrap-icons', 'larijani-tailwind' );
	}

	/**
	 * Scripts.
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return array( 'larijani-theme' );
	}

	/**
	 * Custom help URL.
	 *
	 * @return string
	 */
	public function get_custom_help_url() {
		return admin_url( 'admin.php?page=ls-settings' );
	}

	/**
	 * Theme widgets render full-width sections themselves.
	 *
	 * @return bool
	 */
	public function has_widget_inner_wrapper(): bool {
		return true;
	}

	/* ---------------------------------------------------------------------
	 * Control helpers.
	 * ------------------------------------------------------------------ */

	/**
	 * Start a content section.
	 *
	 * @param string $id Id.
	 * @param string $label Label.
	 * @param string $tab content|style.
	 * @param array  $condition Condition.
	 */
	protected function section( $id, $label, $tab = 'content', $condition = array() ) {
		$args = array(
			'label' => $label,
			'tab'   => 'style' === $tab ? self::cm( 'TAB_STYLE' ) : self::cm( 'TAB_CONTENT' ),
		);
		if ( $condition ) {
			$args['condition'] = $condition;
		}
		$this->start_controls_section( $id, $args );
	}

	/**
	 * End a section.
	 */
	protected function end() {
		$this->end_controls_section();
	}

	/**
	 * Build a control definition from a compact spec.
	 *
	 * @param string $type   text|textarea|wysiwyg|url|media|icon|select|switch|number|color|slider|heading|code.
	 * @param string $label  Label.
	 * @param mixed  $default Default.
	 * @param array  $args   Extra args.
	 * @return array
	 */
	protected function spec( $type, $label, $default = '', $args = array() ) {
		$map = array(
			'text'     => self::cm( 'TEXT' ),
			'textarea' => self::cm( 'TEXTAREA' ),
			'wysiwyg'  => self::cm( 'WYSIWYG' ),
			'url'      => self::cm( 'URL' ),
			'media'    => self::cm( 'MEDIA' ),
			'icon'     => self::cm( 'ICONS' ),
			'select'   => self::cm( 'SELECT' ),
			'switch'   => self::cm( 'SWITCHER' ),
			'number'   => self::cm( 'NUMBER' ),
			'color'    => self::cm( 'COLOR' ),
			'slider'   => self::cm( 'SLIDER' ),
			'heading'  => self::cm( 'HEADING' ),
			'code'     => self::cm( 'CODE' ),
			'gallery'  => self::cm( 'GALLERY' ),
			'hidden'   => self::cm( 'HIDDEN' ),
		);
		$c = array(
			'label' => $label,
			'type'  => $map[ $type ] ?? self::cm( 'TEXT' ),
		);
		if ( 'heading' !== $type ) {
			$c['default'] = $default;
		}
		switch ( $type ) {
			case 'text':
			case 'textarea':
			case 'wysiwyg':
				$c['dynamic']     = array( 'active' => true );
				$c['label_block'] = true;
				if ( 'textarea' === $type ) {
					$c['rows'] = $args['rows'] ?? 3;
				}
				break;
			case 'url':
				$c['dynamic']     = array( 'active' => true );
				$c['label_block'] = true;
				$c['default']     = is_array( $default ) ? $default : array( 'url' => (string) $default );
				$c['placeholder'] = 'https://';
				break;
			case 'media':
				$c['dynamic'] = array( 'active' => true );
				$c['default'] = is_array( $default ) ? $default : ( $default ? larijani_demo_media( $default ) : array( 'url' => '' ) );
				break;
			case 'icon':
				$c['default'] = is_array( $default ) ? $default : ( $default ? larijani_bi( $default ) : array( 'value' => '', 'library' => '' ) );
				$c['skin']    = 'inline';
				$c['label_block'] = false;
				break;
			case 'switch':
				$c['label_on']     = __( 'بله', 'larijani-stone' );
				$c['label_off']    = __( 'خیر', 'larijani-stone' );
				$c['return_value'] = 'yes';
				break;
			case 'heading':
				$c['separator'] = 'before';
				break;
		}
		return array_merge( $c, $args );
	}

	/**
	 * Add one control.
	 *
	 * @param string $id Id.
	 * @param string $type Type.
	 * @param string $label Label.
	 * @param mixed  $default Default.
	 * @param array  $args Extra args.
	 */
	protected function ctl( $id, $type, $label, $default = '', $args = array() ) {
		$this->add_control( $id, $this->spec( $type, $label, $default, $args ) );
	}

	/**
	 * Add a repeater.
	 *
	 * @param string $id Id.
	 * @param string $label Label.
	 * @param array  $fields [ [name, type, label, default, args], ... ].
	 * @param array  $defaults Default rows.
	 * @param string $title_field Title field (e.g. "{{{ title }}}").
	 * @param array  $args Extra args.
	 */
	protected function rep( $id, $label, $fields, $defaults, $title_field = '{{{ title }}}', $args = array() ) {
		$r = class_exists( 'Elementor\\Repeater' ) ? new \Elementor\Repeater() : new Larijani_Fallback_Repeater();
		foreach ( $fields as $f ) {
			$r->add_control( $f[0], $this->spec( $f[1], $f[2], $f[3] ?? '', $f[4] ?? array() ) );
		}
		// Normalise media / icon defaults in rows.
		$ftypes = array();
		foreach ( $fields as $f ) {
			$ftypes[ $f[0] ] = $f[1];
		}
		foreach ( $defaults as &$row ) {
			foreach ( $row as $k => $v ) {
				if ( isset( $ftypes[ $k ] ) && 'media' === $ftypes[ $k ] && is_string( $v ) ) {
					$row[ $k ] = $v ? larijani_demo_media( $v ) : array( 'url' => '' );
				} elseif ( isset( $ftypes[ $k ] ) && 'icon' === $ftypes[ $k ] && is_string( $v ) ) {
					$row[ $k ] = $v ? larijani_bi( $v ) : array( 'value' => '', 'library' => '' );
				} elseif ( isset( $ftypes[ $k ] ) && 'url' === $ftypes[ $k ] && is_string( $v ) ) {
					$row[ $k ] = array( 'url' => $v );
				}
			}
		}
		unset( $row );
		$this->add_control(
			$id,
			array_merge(
				array(
					'label'       => $label,
					'type'        => self::cm( 'REPEATER' ),
					'fields'      => $r->get_controls(),
					'default'     => $defaults,
					'title_field' => $title_field,
				),
				$args
			)
		);
	}

	/**
	 * Standard section-heading controls (eyebrow, title, description, link, alignment).
	 *
	 * @param array $d Defaults.
	 */
	protected function heading_controls( $d = array() ) {
		$d = wp_parse_args( $d, array( 'eyebrow' => '', 'title' => '', 'desc' => '', 'link_text' => '', 'link' => '#', 'align' => 'split', 'tag' => 'h2' ) );
		$this->ctl( 'heading_eyebrow', 'text', __( 'متن بالای عنوان', 'larijani-stone' ), $d['eyebrow'] );
		$this->ctl( 'heading_title', 'textarea', __( 'عنوان بخش', 'larijani-stone' ), $d['title'], array( 'rows' => 2, 'description' => __( 'می‌توانید از &lt;br&gt; و &lt;span class="text-primary-container"&gt; استفاده کنید.', 'larijani-stone' ) ) );
		$this->ctl( 'heading_desc', 'textarea', __( 'توضیح بخش', 'larijani-stone' ), $d['desc'] );
		$this->ctl( 'heading_link_text', 'text', __( 'متن لینک', 'larijani-stone' ), $d['link_text'] );
		$this->ctl( 'heading_link', 'url', __( 'لینک', 'larijani-stone' ), $d['link'], array( 'condition' => array( 'heading_link_text!' => '' ) ) );
		$this->ctl(
			'heading_align',
			'select',
			__( 'چیدمان عنوان', 'larijani-stone' ),
			$d['align'],
			array(
				'options' => array(
					'split'  => __( 'عنوان راست / لینک چپ', 'larijani-stone' ),
					'split-desc' => __( 'عنوان راست / توضیح چپ', 'larijani-stone' ),
					'center' => __( 'وسط‌چین', 'larijani-stone' ),
					'start'  => __( 'راست‌چین ستونی', 'larijani-stone' ),
				),
			)
		);
		$this->ctl(
			'heading_style',
			'select',
			__( 'سبک عنوان', 'larijani-stone' ),
			$d['style'] ?? 'classic',
			array(
				'options' => array(
					'classic' => __( 'صفحه اصلی (درشت و فشرده)', 'larijani-stone' ),
					'token'   => __( 'صفحات داخلی (سیستم طراحی)', 'larijani-stone' ),
				),
			)
		);
		$this->ctl(
			'heading_tag',
			'select',
			__( 'تگ HTML عنوان', 'larijani-stone' ),
			$d['tag'],
			array( 'options' => array( 'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'div' => 'div' ) )
		);
	}

	/**
	 * Section background select.
	 *
	 * @param string $default Default key.
	 */
	protected function bg_control( $default = 'none' ) {
		$this->ctl( 'section_bg', 'select', __( 'پس‌زمینه بخش', 'larijani-stone' ), $default, array( 'options' => larijani_section_bg_options() ) );
	}

	/**
	 * Columns controls.
	 *
	 * @param int $desktop Desktop.
	 * @param int $tablet Tablet.
	 * @param int $mobile Mobile.
	 * @param int $max Max columns.
	 */
	protected function columns_controls( $desktop = 4, $tablet = 2, $mobile = 1, $max = 6 ) {
		$opts = array();
		for ( $i = 1; $i <= $max; $i++ ) {
			$opts[ (string) $i ] = larijani_fa_num( $i );
		}
		$small = array_slice( $opts, 0, 4, true );
		$this->ctl( 'columns', 'select', __( 'ستون‌ها – دسکتاپ', 'larijani-stone' ), (string) $desktop, array( 'options' => $opts ) );
		$this->ctl( 'columns_tablet', 'select', __( 'ستون‌ها – تبلت', 'larijani-stone' ), (string) $tablet, array( 'options' => $small ) );
		$this->ctl( 'columns_mobile', 'select', __( 'ستون‌ها – موبایل', 'larijani-stone' ), (string) $mobile, array( 'options' => array_slice( $opts, 0, 2, true ) ) );
	}

	/**
	 * Shared style section: theme colour overrides, container width, section padding, typography.
	 *
	 * @param array $o Options { typography: bool }.
	 */
	protected function style_controls( $o = array() ) {
		$o = wp_parse_args( $o, array( 'typography' => true, 'layout' => true ) );

		$this->start_controls_section(
			'ls_style_colors',
			array(
				'label' => __( 'رنگ‌های قالب (فقط این ویجت)', 'larijani-stone' ),
				'tab'   => self::cm( 'TAB_STYLE' ),
			)
		);
		$vars = array(
			'primary'       => __( 'رنگ اصلی (سبز برند)', 'larijani-stone' ),
			'primary-hover' => __( 'رنگ اصلی – هاور / تیره‌تر', 'larijani-stone' ),
			'secondary'     => __( 'سبز روشن', 'larijani-stone' ),
			'dark'          => __( 'رنگ تیره (بازالت)', 'larijani-stone' ),
			'canvas'        => __( 'زمینه کرم سنگی', 'larijani-stone' ),
			'border'        => __( 'خطوط و حاشیه', 'larijani-stone' ),
		);
		foreach ( $vars as $var => $label ) {
			$this->add_control(
				'ls_var_' . str_replace( '-', '_', $var ),
				array(
					'label'     => $label,
					'type'      => self::cm( 'COLOR' ),
					'selectors' => array( '{{WRAPPER}}' => '--ls-' . $var . ': {{VALUE}};' ),
				)
			);
		}
		$this->end_controls_section();

		if ( $o['layout'] ) {
			$this->start_controls_section(
				'ls_style_layout',
				array(
					'label' => __( 'چیدمان بخش', 'larijani-stone' ),
					'tab'   => self::cm( 'TAB_STYLE' ),
				)
			);
			$this->add_responsive_control(
				'ls_container_width',
				array(
					'label'      => __( 'حداکثر عرض محتوا', 'larijani-stone' ),
					'type'       => self::cm( 'SLIDER' ),
					'size_units' => array( 'px', '%', 'vw' ),
					'range'      => array( 'px' => array( 'min' => 600, 'max' => 1920 ) ),
					'selectors'  => array( '{{WRAPPER}} .max-w-7xl, {{WRAPPER}} .max-w-\[80rem\]' => 'max-width: {{SIZE}}{{UNIT}};' ),
				)
			);
			$this->add_responsive_control(
				'ls_section_padding',
				array(
					'label'      => __( 'فاصله داخلی بخش (بالا/پایین)', 'larijani-stone' ),
					'type'       => self::cm( 'DIMENSIONS' ),
					'size_units' => array( 'px', 'rem', 'em' ),
					'allowed_dimensions' => 'vertical',
					'selectors'  => array( '{{WRAPPER}} .ls-root > :first-child' => 'padding-top: {{TOP}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};' ),
				)
			);
			$this->add_control(
				'ls_section_bg_color',
				array(
					'label'     => __( 'رنگ پس‌زمینه سفارشی بخش', 'larijani-stone' ),
					'type'      => self::cm( 'COLOR' ),
					'selectors' => array( '{{WRAPPER}} .ls-root > :first-child' => 'background-color: {{VALUE}};' ),
				)
			);
			$this->end_controls_section();
		}

		if ( $o['typography'] ) {
			$this->start_controls_section(
				'ls_style_typo',
				array(
					'label' => __( 'تایپوگرافی', 'larijani-stone' ),
					'tab'   => self::cm( 'TAB_STYLE' ),
				)
			);
			$this->add_control( 'ls_title_color', array( 'label' => __( 'رنگ عناوین', 'larijani-stone' ), 'type' => self::cm( 'COLOR' ), 'selectors' => array( '{{WRAPPER}} :is(h1,h2,h3)' => 'color: {{VALUE}};' ) ) );
			if ( class_exists( 'Elementor\\Group_Control_Typography' ) ) {
				$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array( 'name' => 'ls_title_typo', 'label' => __( 'عناوین اصلی', 'larijani-stone' ), 'selector' => '{{WRAPPER}} :is(h1,h2)' ) );
				$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array( 'name' => 'ls_card_title_typo', 'label' => __( 'عنوان کارت‌ها', 'larijani-stone' ), 'selector' => '{{WRAPPER}} :is(h3,h4,h5)' ) );
			}
			$this->add_control( 'ls_text_color', array( 'label' => __( 'رنگ متن‌ها', 'larijani-stone' ), 'type' => self::cm( 'COLOR' ), 'selectors' => array( '{{WRAPPER}} p' => 'color: {{VALUE}};' ) ) );
			if ( class_exists( 'Elementor\\Group_Control_Typography' ) ) {
				$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array( 'name' => 'ls_text_typo', 'label' => __( 'متن‌ها', 'larijani-stone' ), 'selector' => '{{WRAPPER}} p' ) );
			}
			$this->end_controls_section();
		}
	}

	/* ---------------------------------------------------------------------
	 * Rendering.
	 * ------------------------------------------------------------------ */

	/**
	 * Render wrapper.
	 */
	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<div class="ls-root">';
		$this->render_widget( $s );
		echo '</div>';
	}

	/**
	 * Widget markup.
	 *
	 * @param array $s Settings.
	 */
	abstract protected function render_widget( $s );

	/**
	 * Section heading from settings.
	 *
	 * @param array $s Settings.
	 * @param array $extra Overrides.
	 * @return string
	 */
	protected function heading( $s, $extra = array() ) {
		// Never print a dead "#" link: fall back to the widget's natural destination.
		$url = is_array( $s['heading_link'] ?? null ) ? ( $s['heading_link']['url'] ?? '' ) : (string) ( $s['heading_link'] ?? '' );
		if ( ( '' === $url || '#' === $url ) && ! empty( $s['heading_link_text'] ) ) {
			$fallback = $this->heading_fallback_url();
			if ( $fallback ) {
				$s['heading_link'] = array( 'url' => $fallback );
			} else {
				$s['heading_link_text'] = '';
			}
		}
		return larijani_section_heading( larijani_heading_from_settings( $s, $extra ) );
	}

	/**
	 * Destination used when the section-heading link was left empty.
	 *
	 * @return string
	 */
	protected function heading_fallback_url() {
		return '';
	}

	/**
	 * Read a text field (allowing basic inline HTML).
	 *
	 * @param array  $s Settings.
	 * @param string $key Key.
	 * @return string
	 */
	protected function t( $s, $key ) {
		return isset( $s[ $key ] ) ? larijani_kses( $s[ $key ] ) : '';
	}

	/**
	 * Is a switcher on?
	 *
	 * @param array  $s Settings.
	 * @param string $key Key.
	 * @return bool
	 */
	protected function on( $s, $key ) {
		return isset( $s[ $key ] ) && 'yes' === $s[ $key ];
	}
}
