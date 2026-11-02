<?php
/**
 * Lifecycle test (run inside a sandbox WordPress): import, re-run, owner edits preserved, texts override + reset,
 * undo (trash + options restored), re-import, uninstall data rules.
 * Run: ALADDIN_WP_LOAD=/path/wp-load.php php tests/lifecycle.php
 */
define( 'LS_TEST_USER', 1 );
$_SERVER['HTTP_HOST'] = getenv( 'ALADDIN_HOST' ) ?: 'localhost:8091';
require getenv( 'ALADDIN_WP_LOAD' );
use Aladdin\PayabSanatAsa\Importer;
$pass = 0; $fail = 0;
function ok( $name, $cond, $detail = '' ) { global $pass, $fail; $cond ? $pass++ : $fail++; echo ( $cond ? 'PASS ' : 'FAIL ' ), $name, $detail ? " — $detail" : '', "\n"; }
function count_posts_mine() { global $wpdb; return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->postmeta} m JOIN {$wpdb->posts} p ON p.ID=m.post_id WHERE m.meta_key='_aladdin_payab_group' AND p.post_status IN ('publish','draft')" ); }

// start clean: undo any previous journal
Importer::undo();
ok( 'clean state: no imported pages', 0 === count_posts_mine() );
delete_option( 'aladdin_payab_sanat_asa_claim_home' );
$front0 = get_option( 'page_on_front' ); $show0 = get_option( 'show_on_front' );

$r = Importer::run();
ok( 'import creates 66 language pages', 66 === $r['created'] && 0 === count( $r['errors'] ), json_encode( $r ) );
ok( '66 translated pages published', 66 === count_posts_mine() );
$j1 = get_option( Importer::JOURNAL );
ok( 'journal lists all created ids', 66 === count( $j1['created'] ) );
ok( 'Persian home claims / (claim option) and resolves', 'yes' === get_option( 'aladdin_payab_sanat_asa_claim_home' ) && home_url( '/' ) === get_permalink( \Aladdin\PayabSanatAsa\Languages::post_id( 'fa', '' ) ) );
wp_cache_flush();
ok( 'one post type per language, 22 pages each', 22 === (int) wp_count_posts( 'aladdin_page_fa' )->publish && 22 === (int) wp_count_posts( 'aladdin_page_en' )->publish && 22 === (int) wp_count_posts( 'aladdin_page_ar' )->publish, wp_count_posts( 'aladdin_page_fa' )->publish . '/' . wp_count_posts( 'aladdin_page_en' )->publish . '/' . wp_count_posts( 'aladdin_page_ar' )->publish );
ok( 'language pages are enabled for free Elementor', 3 === count( array_intersect( (array) get_option( 'elementor_cpt_support' ), \Aladdin\PayabSanatAsa\Post_Types::slugs() ) ) );
// every language is complete and really translated
$fa_only = '/[\x{067e}\x{0686}\x{0698}\x{06af}\x{06a9}\x{06cc}\x{06f0}-\x{06f9}]/u';
$miss = array(); $wrong = array();
foreach ( json_decode( file_get_contents( ALADDIN_PAYAB_SANAT_ASA_CORE_DIR . 'data/pages.json' ), true ) as $pg ) {
	$id = \Aladdin\PayabSanatAsa\Languages::post_id( $pg['lang'], $pg['group'], true );
	if ( ! $id ) { $miss[] = $pg['lang'] . ':' . $pg['group']; continue; }
	if ( get_post_type( $id ) !== 'aladdin_page_' . $pg['lang'] || get_post_meta( $id, '_aladdin_payab_lang', true ) !== $pg['lang'] ) { $wrong[] = 'type ' . $pg['lang'] . ':' . $pg['group']; }
	$text = wp_strip_all_tags( get_post_field( 'post_content', $id ) );
	if ( 'fa' !== $pg['lang'] && preg_match( $fa_only, $text . ' ' . get_post_meta( $id, '_aladdin_payab_seo_title', true ) . ' ' . get_post_meta( $id, '_aladdin_payab_seo_description', true ) ) ) { $wrong[] = 'persian text in ' . $pg['lang'] . ':' . $pg['group']; }
	if ( 'fa' === $pg['lang'] && ! preg_match( $fa_only, $text ) ) { $wrong[] = 'no persian in fa:' . $pg['group']; }
	if ( 'en' === $pg['lang'] && ! preg_match( '/[A-Za-z]{4}/', $text ) ) { $wrong[] = 'no latin in en:' . $pg['group']; }
	if ( 'ar' === $pg['lang'] && ! preg_match( '/[\x{0621}-\x{064a}]{4}/u', $text ) ) { $wrong[] = 'no arabic in ar:' . $pg['group']; }
}
ok( 'all 66 routes exist in the right post type', ! $miss && ! $wrong, implode( ',', array_merge( $miss, $wrong ) ) );
ok( 'every page has Elementor data JSON + fallback content', (bool) array_reduce( get_posts( array( 'post_type' => \Aladdin\PayabSanatAsa\Post_Types::slugs(), 'posts_per_page' => -1, 'meta_key' => '_aladdin_payab_group', 'fields' => 'ids' ) ), function ( $c, $id ) { return $c && json_decode( get_post_meta( $id, '_elementor_data', true ), true ) && strlen( get_post_field( 'post_content', $id ) ) > 500; }, true ) );

// re-run is a no-op and keeps the journal
$r2 = Importer::run();
$j2 = get_option( Importer::JOURNAL );
ok( 're-run creates nothing (66 skipped)', 0 === $r2['created'] && 66 === $r2['skipped'], json_encode( $r2 ) );
ok( 're-run keeps the original journal untouched', $j1 === $j2 );

// owner edits are preserved
$shop = \Aladdin\PayabSanatAsa\Languages::post_id( 'fa', 'shop-2' );
wp_update_post( array( 'ID' => $shop, 'post_content' => 'OWNER EDIT' ) );
Importer::run();
ok( 'owner edit survives a re-import', 'OWNER EDIT' === get_post_field( 'post_content', $shop ) );
// restore content for later steps
$pages = json_decode( file_get_contents( ALADDIN_PAYAB_SANAT_ASA_CORE_DIR . 'data/pages.json' ), true );
foreach ( $pages as $p ) { if ( 'fa' === $p['lang'] && 'shop-2' === $p['group'] ) { wp_update_post( array( 'ID' => $shop, 'post_content' => wp_slash( $p['fallback'] ) ) ); } }

// URL structure
ok( 'URLs: fa/en/ar + nested + product', 
	get_permalink( \Aladdin\PayabSanatAsa\Languages::post_id( 'en', 'categories/electric' ) ) === home_url( '/en/categories/electric/' )
	&& get_permalink( \Aladdin\PayabSanatAsa\Languages::post_id( 'ar', 'product/manual-lift' ) ) === home_url( '/ar/product/manual-lift/' )
	&& get_permalink( \Aladdin\PayabSanatAsa\Languages::post_id( 'fa', 'articles/cane-types' ) ) === home_url( '/articles/cane-types/' )
	&& home_url( '/' ) === get_permalink( \Aladdin\PayabSanatAsa\Languages::post_id( 'fa', '' ) ) );

// texts override: change the brand name of the Persian header; check rendered chrome; reset
$defs = aladdin_payab_sanat_asa_chrome_defaults( 'fa' );
$idx = null; foreach ( $defs['pre'] as $i => $row ) { if ( 't' === $row[0] && 'پایاب صنعت آسا' === $row[1] ) { $idx = $i; break; } }
update_option( 'aladdin_payab_sanat_asa_chrome', array( 'fa' => array( 'pre' => array( $idx => 'برند تست' ), 'post' => array(), 'mega' => array( 'electric' => array( 0 => 'عنوان مگا تست' ) ) ) ) );
$html = aladdin_payab_sanat_asa_chrome_html( 'pre', 'fa' );
ok( 'text override appears in header markup', false !== strpos( $html, 'برند تست' ) );
$sc = aladdin_payab_sanat_asa_script_strings( 'fa' );
ok( 'mega override applies', 'عنوان مگا تست' === $sc['mega']['electric'][0] && 'مدل‌های برقی برای کمک به جابه‌جایی بیمار؛ انتخاب نهایی به فضای استفاده و وضعیت بیمار بستگی دارد.' === $sc['mega']['electric'][1] );
ok( 'other language unaffected', false === strpos( aladdin_payab_sanat_asa_chrome_html( 'pre', 'en' ), 'برند تست' ) );
$xss = '<script>alert(1)</script>';
update_option( 'aladdin_payab_sanat_asa_chrome', array( 'fa' => array( 'pre' => array( $idx => $xss ), 'post' => array() ) ) );
ok( 'override is escaped on output', false === strpos( aladdin_payab_sanat_asa_chrome_html( 'pre', 'fa' ), '<script>alert' ) );
delete_option( 'aladdin_payab_sanat_asa_chrome' );
ok( 'reset restores approved text', false !== strpos( aladdin_payab_sanat_asa_chrome_html( 'pre', 'fa' ), 'پایاب صنعت آسا' ) );

// undo
$ids_before = $j1['created'];
$n = Importer::undo();
ok( 'undo trashes all 66 items', 66 === $n, (string) $n );
ok( 'undo restores options (claim flag removed)', false === get_option( 'aladdin_payab_sanat_asa_claim_home', false ) && (string) get_option( 'page_on_front' ) === (string) $front0 && get_option( 'show_on_front' ) === $show0, "$front0/$show0" );
ok( 'journal removed after undo', false === get_option( Importer::JOURNAL ) );
ok( 'all journaled items are in Trash (recoverable), not deleted', 66 === count( array_filter( $ids_before, function ( $id ) { return 'trash' === get_post_status( $id ); } ) ) );
// re-import after undo (slugs of trashed pages must not block)
$r3 = Importer::run();
ok( 're-import after undo works', 66 === $r3['created'] && 0 === count( $r3['errors'] ), json_encode( $r3 ) );
// post-import state is left in place for the HTTP tests
echo "lifecycle: pass=$pass fail=$fail\n";
exit( $fail ? 1 : 0 );
