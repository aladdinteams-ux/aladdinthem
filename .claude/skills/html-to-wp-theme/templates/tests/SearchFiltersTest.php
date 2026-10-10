<?php
use PHPUnit\Framework\TestCase;

/**
 * Hero search: option parsing, aliases, active filters.
 */
class SearchFiltersTest extends TestCase {

	protected function tearDown(): void {
		$_GET = array();
	}

	public function test_option_parsing() {
		$this->assertSame( array( 'label' => 'همه', 'value' => '', 'tabs' => array() ), larijani_search_option( 'همه|' ) );
		$this->assertSame( array( 'label' => 'میز', 'value' => 'ویبره', 'tabs' => array( 'machinery' ) ), larijani_search_option( 'میز|ویبره|machinery' ) );
		$this->assertSame( 'قدیمی', larijani_search_option( 'قدیمی' )['value'] );
	}

	public function test_no_marker_no_filters() {
		$_GET = array( 'type' => 'کفپوش' );
		$this->assertSame( array(), larijani_active_search_filters_uncached() );
	}

	public function test_words_aliases_and_info_fields() {
		$_GET = array( 'ls_hs' => 'type,material,scale', 'type' => 'کفپوش,واش', 'material' => 'abs', 'scale' => 'bulk' );
		$f    = larijani_active_search_filters_uncached();
		$this->assertCount( 2, $f, 'Order size is informational.' );
		$this->assertSame( array( 'کفپوش', 'واش' ), $f[0]['words'] );
		$this->assertSame( array( 'ABS' ), $f[1]['words'], 'Old value "abs" maps to the word ABS.' );
	}

	public function test_reserved_params_ignored() {
		$_GET = array( 'ls_hs' => 's,post_type,product_cat,type', 's' => 'x', 'post_type' => 'page', 'product_cat' => 'mold', 'type' => '<b>کفپوش</b>' );
		$f    = larijani_active_search_filters_uncached();
		$this->assertCount( 1, $f );
		$this->assertSame( 'type', $f[0]['param'] );
		$this->assertStringNotContainsString( '<', implode( '', $f[0]['words'] ) );
	}
}
