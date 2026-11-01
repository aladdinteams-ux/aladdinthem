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
$front0 = get_option( 'page_on_front' ); $show0 = get_option( 'show_on_front' );

$before_all = wp_count_posts( 'page' )->publish;
$r = Importer::run();
ok( 'import creates 66 pages + 6 placeholders', 72 === $r['created'] && 0 === count( $r['errors'] ), json_encode( $r ) );
ok( '66 translated pages published', 66 === count_posts_mine() );
$j1 = get_option( Importer::JOURNAL );
ok( 'journal lists all created ids', 72 === count( $j1['created'] ) );
ok( 'front page = Persian home', (int) get_option( 'page_on_front' ) === \Aladdin\PayabSanatAsa\Languages::post_id( 'fa', '' ) && 'page' === get_option( 'show_on_front' ) );
ok( 'every page has Elementor data JSON + fallback content', (bool) array_reduce( get_posts( array( 'post_type' => 'page', 'posts_per_page' => -1, 'meta_key' => '_aladdin_payab_group', 'fields' => 'ids' ) ), function ( $c, $id ) { return $c && json_decode( get_post_meta( $id, '_elementor_data', true ), true ) && strlen( get_post_field( 'post_content', $id ) ) > 500; }, true ) );

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
ok( 'undo trashes all 72 items', 72 === $n, (string) $n );
ok( 'undo restores front page options', (string) get_option( 'page_on_front' ) === (string) $front0 && get_option( 'show_on_front' ) === $show0, "$front0/$show0" );
ok( 'journal removed after undo', false === get_option( Importer::JOURNAL ) );
ok( 'all journaled items are in Trash (recoverable), not deleted', 72 === count( array_filter( $ids_before, function ( $id ) { return 'trash' === get_post_status( $id ); } ) ) );
// re-import after undo (slugs of trashed pages must not block)
$r3 = Importer::run();
ok( 're-import after undo works', 66 <= $r3['created'] && 0 === count( $r3['errors'] ), json_encode( $r3 ) );
// post-import state is left in place for the HTTP tests
echo "lifecycle: pass=$pass fail=$fail\n";
exit( $fail ? 1 : 0 );
