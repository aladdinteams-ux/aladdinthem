<?php
/**
 * 404 page, designed with the site's own look (cards, buttons, brand symbol, light/dark mode, fa/en/ar).
 *
 * @package PayabSanatAsa
 */

defined( 'ABSPATH' ) || exit;

$aladdin_lang = aladdin_payab_sanat_asa_lang();
$aladdin_t    = apply_filters(
	'aladdin_payab_sanat_asa_404_texts',
	array(
		'fa' => array(
			'kicker' => 'خطای 404', 'title' => 'صفحه‌ای که دنبالش بودید پیدا نشد', 'text' => 'ممکن است نشانی تغییر کرده یا صفحه حذف شده باشد. از جست‌وجو استفاده کنید یا یکی از مسیرهای زیر را ادامه دهید.',
			'search' => 'جست‌وجوی بالابر و تجهیزات انتقال بیمار', 'go' => 'جست‌وجو', 'home' => 'بازگشت به صفحه اصلی', 'shop' => 'مشاهده محصولات', 'quick' => 'مسیرهای پیشنهادی',
			'links' => array( 'categories' => 'دسته‌بندی‌ها', 'selection-guide' => 'راهنمای انتخاب بالابر', 'videos' => 'آموزش و ویدیو', 'contact-us' => 'تماس با ما' ),
			'numeral' => '404',
		),
		'en' => array(
			'kicker' => 'Error 404', 'title' => 'We could not find that page', 'text' => 'The address may have changed or the page was removed. Try the search or continue with one of the paths below.',
			'search' => 'Search patient lifts and transfer equipment', 'go' => 'Search', 'home' => 'Back to home', 'shop' => 'View products', 'quick' => 'Suggested paths',
			'links' => array( 'categories' => 'Categories', 'selection-guide' => 'Lift selection guide', 'videos' => 'Guides & videos', 'contact-us' => 'Contact us' ),
			'numeral' => '404',
		),
		'ar' => array(
			'kicker' => 'خطأ 404', 'title' => 'لم نعثر على الصفحة المطلوبة', 'text' => 'ربما تغيّر العنوان أو حُذفت الصفحة. جرّب البحث أو تابع عبر أحد المسارات التالية.',
			'search' => 'البحث عن رافعات المرضى ومعدات النقل', 'go' => 'بحث', 'home' => 'العودة إلى الرئيسية', 'shop' => 'عرض المنتجات', 'quick' => 'مسارات مقترحة',
			'links' => array( 'categories' => 'الفئات', 'selection-guide' => 'دليل اختيار الرافعة', 'videos' => 'الأدلة والفيديوهات', 'contact-us' => 'اتصل بنا' ),
			'numeral' => '404',
		),
	)
);
$aladdin_t = isset( $aladdin_t[ $aladdin_lang ] ) ? $aladdin_t[ $aladdin_lang ] : $aladdin_t['fa'];

get_header();
?>
<section class="aladdin-404" aria-labelledby="aladdin-404-title">
	<div class="container aladdin-404-wrap">
		<div class="aladdin-404-card">
			<div class="aladdin-404-mark" aria-hidden="true">
				<span class="aladdin-404-num"><?php echo esc_html( $aladdin_t['numeral'] ); ?></span>
				<img src="<?php echo esc_url( ALADDIN_PAYAB_SANAT_ASA_URI . '/assets/images/payab-symbol-navy.png' ); ?>" alt="" width="54" height="48" loading="eager" decoding="async"/>
			</div>
			<span class="aladdin-404-kicker"><?php echo esc_html( $aladdin_t['kicker'] ); ?></span>
			<h1 id="aladdin-404-title"><?php echo esc_html( $aladdin_t['title'] ); ?></h1>
			<p class="aladdin-404-text"><?php echo esc_html( $aladdin_t['text'] ); ?></p>

			<form class="aladdin-404-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><circle cx="10.5" cy="10.5" r="6.5"></circle><path d="m16 16 5 5"></path></svg>
				<input type="search" name="s" placeholder="<?php echo esc_attr( $aladdin_t['search'] ); ?>" aria-label="<?php echo esc_attr( $aladdin_t['search'] ); ?>" autocomplete="off"/>
				<?php if ( 'fa' !== $aladdin_lang ) : ?>
					<input type="hidden" name="lang" value="<?php echo esc_attr( $aladdin_lang ); ?>"/>
				<?php endif; ?>
				<button type="submit"><?php echo esc_html( $aladdin_t['go'] ); ?></button>
			</form>

			<div class="aladdin-404-actions">
				<a class="btn btn-primary" href="<?php echo esc_url( aladdin_payab_sanat_asa_route_url( $aladdin_lang, '' ) ); ?>"><?php echo esc_html( $aladdin_t['home'] ); ?></a>
				<a class="btn btn-outline" href="<?php echo esc_url( aladdin_payab_sanat_asa_route_url( $aladdin_lang, 'shop-2' ) ); ?>"><?php echo esc_html( $aladdin_t['shop'] ); ?></a>
			</div>

			<div class="aladdin-404-quick">
				<span><?php echo esc_html( $aladdin_t['quick'] ); ?></span>
				<nav aria-label="<?php echo esc_attr( $aladdin_t['quick'] ); ?>">
					<?php foreach ( $aladdin_t['links'] as $aladdin_group => $aladdin_label ) : ?>
						<a href="<?php echo esc_url( aladdin_payab_sanat_asa_route_url( $aladdin_lang, $aladdin_group ) ); ?>"><?php echo esc_html( $aladdin_label ); ?></a>
					<?php endforeach; ?>
				</nav>
			</div>
		</div>
	</div>
</section>
<?php
get_footer();
