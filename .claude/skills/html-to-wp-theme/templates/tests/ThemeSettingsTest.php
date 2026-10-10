<?php
use PHPUnit\Framework\TestCase;

/**
 * Theme: settings CSS, font URL validation, image helper.
 */
class ThemeSettingsTest extends TestCase {

	private $keys = array( 'ls_font_family', 'ls_font_custom_url', 'ls_container_width', 'ls_button_radius', 'ls_font_scale_heading', 'ls_font_scale_mobile' );

	protected function tearDown(): void {
		foreach ( $this->keys as $k ) {
			remove_theme_mod( $k );
		}
	}

	public function test_defaults_print_only_colours() {
		$css = larijani_customizer_css();
		$this->assertStringNotContainsString( '--ls-font', $css );
		$this->assertStringNotContainsString( '--ls-container', $css );
		$this->assertStringNotContainsString( 'border-radius', $css );
		$this->assertStringNotContainsString( '@media', $css );
	}

	public function test_custom_values() {
		set_theme_mod( 'ls_font_family', 'system' );
		set_theme_mod( 'ls_container_width', '1200' );
		set_theme_mod( 'ls_button_radius', '6' );
		set_theme_mod( 'ls_font_scale_mobile', '90' );
		$css = larijani_customizer_css();
		$this->assertStringContainsString( '--ls-font:Tahoma', $css );
		$this->assertStringContainsString( '--ls-container:1200px', $css );
		$this->assertStringContainsString( 'border-radius:6px', $css );
		$this->assertStringContainsString( '@media (max-width:767.98px){:root{--ls-fs-r:0.9}}', $css );
	}

	/**
	 * @dataProvider font_urls
	 */
	public function test_font_url_validation( $url, $ok ) {
		set_theme_mod( 'ls_font_custom_url', $url );
		$this->assertSame( $ok, '' !== larijani_font_url() );
	}

	public function font_urls() {
		$home = home_url( '/wp-content/uploads/f.woff2' );
		return array(
			array( $home, true ),
			array( '/wp-content/uploads/f.woff', true ),
			array( 'https://evil.example/f.woff2', false ),
			array( $home . "');}body{display:none", false ),
			array( home_url( '/f.ttf' ), false ),
			array( 'javascript:alert(1)//.woff2', false ),
			array( 'javascript:x//.woff2', false ),
			array( '//evil.example/f.woff2', false ),
			array( 'data:font/woff2;base64,AAAA.woff2', false ),
		);
	}

	public function test_lcp_image_attributes() {
		$this->assertStringContainsString( 'fetchpriority="high"', larijani_img( 'https://example.com/a.jpg', '', '', 'large', 'high' ) );
		$this->assertStringNotContainsString( 'loading="lazy"', larijani_img( 'https://example.com/a.jpg', '', '', 'large', 'high' ) );
		$this->assertStringContainsString( 'loading="lazy"', larijani_img( 'https://example.com/a.jpg' ) );
	}
}
