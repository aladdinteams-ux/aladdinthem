<?php
use PHPUnit\Framework\TestCase;

/**
 * Larijani Stone Core: validation, signing and upload checks.
 */
class CoreFormsTest extends TestCase {

	public function test_plugin_loaded() {
		$this->assertTrue( function_exists( 'larijani_core_handle_lead' ) );
		$this->assertTrue( post_type_exists( 'ls_lead' ) );
	}

	/**
	 * @dataProvider phones
	 */
	public function test_phone_normalisation( $in, $out ) {
		$this->assertSame( $out, larijani_core_normalize_phone( $in ) );
	}

	public function phones() {
		return array(
			array( '09121234567', '09121234567' ),
			array( '۰۹۱۲ ۱۲۳-۴۵۶۷', '09121234567' ),
			array( '+989121234567', '09121234567' ),
			array( '00989121234567', '09121234567' ),
			array( '9121234567', '09121234567' ),
			array( '02144556677', '02144556677' ),
			array( '+4915112345678', '+4915112345678' ),
			array( '12345', false ),
			array( '0912123456', false ),
			array( '<script>', false ),
			array( '09121234567; DROP TABLE', false ),
		);
	}

	public function test_sign_and_verify() {
		$s = larijani_core_sign( array( 'a' => 1 ), 'schema' );
		$this->assertSame( array( 'a' => 1 ), larijani_core_verify( $s, 'schema' ) );
		$this->assertFalse( larijani_core_verify( $s, 'token' ), 'A schema must not be accepted as a token.' );
		list( $body, $mac ) = explode( '.', $s );
		$forged = larijani_core_b64( wp_json_encode( array( 'a' => 2 ) ) ) . '.' . $mac;
		$this->assertFalse( larijani_core_verify( $forged, 'schema' ) );
		$this->assertFalse( larijani_core_verify( 'garbage', 'schema' ) );
		$this->assertFalse( larijani_core_verify( str_repeat( 'a.', 20000 ), 'schema' ) );
	}

	private function schema() {
		return larijani_core_normalize_schema(
			'Test',
			array(
				'name'    => array( 'label' => 'Name', 'type' => 'text', 'required' => true ),
				'phone'   => array( 'label' => 'Phone', 'type' => 'tel', 'required' => true ),
				'mail'    => array( 'label' => 'Mail', 'type' => 'email' ),
				'size'    => array( 'label' => 'Size', 'type' => 'select', 'options' => array( 'S', 'L' ) ),
				'svc'     => array( 'label' => 'Svc', 'type' => 'checkboxes', 'options' => array( 'A', 'B' ) ),
				'qty'     => array( 'label' => 'Qty', 'type' => 'number' ),
				'contact' => array( 'label' => 'Contact', 'type' => 'contact' ),
				'evil'    => array( 'label' => '<b>x</b>', 'type' => 'php' ),
			)
		);
	}

	public function test_schema_normalisation() {
		$s = $this->schema();
		$this->assertSame( 'text', $s['f']['evil']['t'], 'Unknown types fall back to text.' );
		$this->assertSame( 'x', $s['f']['evil']['l'], 'Labels are stripped of HTML.' );
	}

	public function test_valid_submission() {
		$r = larijani_core_validate_fields( $this->schema(), array( 'name' => 'Ali', 'phone' => '۰۹۱۲۱۲۳۴۵۶۷', 'mail' => 'a@example.com', 'size' => 'L', 'svc' => array( 'A', 'B' ), 'qty' => '۱۲', 'contact' => 'b@example.com' ), false );
		$this->assertIsArray( $r );
		$this->assertSame( '09121234567', $r['phone'] );
		$this->assertSame( 'a@example.com', $r['email'] );
		$this->assertSame( 'Ali', $r['name'] );
	}

	/**
	 * @dataProvider invalid
	 */
	public function test_invalid_submissions( $data, $code, $field ) {
		$base = array( 'name' => 'Ali', 'phone' => '09121234567' );
		$r    = larijani_core_validate_fields( $this->schema(), array_merge( $base, $data ), false );
		$this->assertInstanceOf( WP_Error::class, $r );
		$this->assertSame( $code, $r->get_error_code() );
		$this->assertSame( $field, $r->get_error_data() );
	}

	public function invalid() {
		return array(
			'missing name'   => array( array( 'name' => '' ), 'required', 'name' ),
			'bad phone'      => array( array( 'phone' => '123' ), 'phone', 'phone' ),
			'bad mail'       => array( array( 'mail' => 'not-mail' ), 'email', 'mail' ),
			'bad option'     => array( array( 'size' => 'XL' ), 'option', 'size' ),
			'bad checkbox'   => array( array( 'svc' => array( 'A', 'Z' ) ), 'option', 'svc' ),
			'nan'            => array( array( 'qty' => 'abc' ), 'number', 'qty' ),
			'bad contact'    => array( array( 'contact' => 'hello' ), 'contact', 'contact' ),
			'array scalar'   => array( array( 'name' => array( 'x' ) ), 'invalid_value', 'name' ),
			'too long'       => array( array( 'name' => str_repeat( 'x', 201 ) ), 'too_long', 'name' ),
		);
	}

	public function test_files_need_file_field() {
		$r = larijani_core_validate_fields( $this->schema(), array( 'name' => 'A', 'phone' => '09121234567' ), true );
		$this->assertSame( 'invalid_files', $r->get_error_code() );
	}

	private function tmp( $bytes ) {
		$f = wp_tempnam( 'lstest' );
		file_put_contents( $f, $bytes );
		return $f;
	}

	public function test_content_checks() {
		$png = $this->tmp( base64_decode( 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==' ) );
		$this->assertTrue( larijani_core_check_content( $png, 'png' ) );
		$this->assertSame( 'bad_content', larijani_core_check_content( $png, 'pdf' ) );
		$this->assertSame( 'bad_content', larijani_core_check_content( $this->tmp( '<?php echo 1;' ), 'jpg' ) );
		$this->assertSame( 'bad_content', larijani_core_check_content( $this->tmp( "\xFF\xD8\xFF<?php" ), 'jpg' ) );
		$this->assertTrue( larijani_core_check_content( $this->tmp( '%PDF-1.7 x' ), 'pdf' ) );
		$this->assertTrue( larijani_core_check_content( $this->tmp( 'AC1032' . str_repeat( "\0", 64 ) ), 'dwg' ) );
		$this->assertSame( 'bad_content', larijani_core_check_content( $this->tmp( 'MZ' . str_repeat( "\0", 64 ) ), 'dwg' ) );
	}

	public function test_zip_checks() {
		$make = function ( $entries ) {
			$f = wp_tempnam( 'lszip' );
			$z = new ZipArchive();
			$z->open( $f, ZipArchive::OVERWRITE );
			foreach ( $entries as $n => $c ) {
				$z->addFromString( $n, $c );
			}
			$z->close();
			return $f;
		};
		$this->assertTrue( larijani_core_check_zip( $make( array( 'a/plan.dwg' => 'AC1032', 'b.pdf' => '%PDF' ) ) ) );
		$this->assertSame( 'zip_rejected', larijani_core_check_zip( $make( array( 'x/shell.php' => '<?php' ) ) ) );
		$this->assertSame( 'zip_rejected', larijani_core_check_zip( $make( array( 'x/shell.php.jpg' => '<?php' ) ) ) );
		$this->assertSame( 'zip_rejected', larijani_core_check_zip( $make( array( '.htaccess' => 'x' ) ) ) );
		$this->assertSame( 'zip_rejected', larijani_core_check_zip( $make( array( '../evil.txt' => 'x' ) ) ) );
	}

	public function test_tracking_code_format() {
		for ( $i = 0; $i < 20; $i++ ) {
			$this->assertMatchesRegularExpression( '/^LS-\d{6}-[ABCDEFGHJKMNPQRSTUVWXYZ23456789]{6}$/', larijani_core_tracking_code() );
		}
	}

	public function test_rate_limiter_window() {
		$bucket = 'unit|' . wp_generate_password( 8, false );
		$rule   = array( 2, 60 );
		$this->assertFalse( larijani_core_limited( $bucket, $rule ) );
		$this->assertFalse( larijani_core_limited( $bucket, $rule ) );
		$this->assertTrue( larijani_core_limited( $bucket, $rule ) );
	}

	public function test_csv_injection_escaped() {
		$this->assertSame( "'=HYPERLINK(1)", larijani_core_csv_cell( '=HYPERLINK(1)' ) );
		$this->assertSame( "'+1", larijani_core_csv_cell( '+1' ) );
		$this->assertSame( '09121234567', larijani_core_csv_cell( '09121234567' ) );
	}

	public function test_svg_sanitiser() {
		$out = larijani_core_sanitize_svg( '<svg xmlns="http://www.w3.org/2000/svg" onload="x()"><script>x</script><path d="M0 0" onclick="y()"/></svg>' );
		$this->assertStringContainsString( '<path', $out );
		$this->assertStringNotContainsString( 'script', $out );
		$this->assertStringNotContainsString( 'onload', $out );
		$this->assertStringNotContainsString( 'onclick', $out );
		$this->assertFalse( larijani_core_sanitize_svg( '<!DOCTYPE svg [<!ENTITY x SYSTEM "file:///etc/passwd">]><svg/>' ) );
	}
}
