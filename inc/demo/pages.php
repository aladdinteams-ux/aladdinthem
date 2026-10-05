<?php
/**
 * Page & template layouts for the demo importer and the JSON exporter.
 *
 * Every layout is a list of rows. A row is either:
 *   [ 'w' => 'ls-widget', 's' => [settings] ]                      – full width widget
 *   [ 'cols' => [ [ size, [ [ 'w' => ..., 's' => ... ], ... ] ], ... ], 'boxed' => true ]
 * Widgets keep their design defaults, so only differences are stored.
 *
 * This file must stay free of WordPress function calls (used by build/export-templates.php).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Layout definitions.
 *
 * @return array slug => [ title, kind, rows, extra ]
 */
function ls_demo_layouts() {
	$cta_dark = array(
		'w' => 'ls-cta',
		's' => array( 'variant' => 'dark-card' ),
	);

	return array(
		// ------------------------------------------------------------------ Pages.
		'home'            => array(
			'title' => 'صفحه اصلی',
			'kind'  => 'page',
			'rows'  => array(
				array( 'w' => 'ls-hero' ),
				array( 'w' => 'ls-icon-cards' ),
				array( 'w' => 'ls-products', 's' => array( '_element_id' => 'products' ) ),
				array( 'w' => 'ls-about', 's' => array( '_element_id' => 'about' ) ),
				array( 'w' => 'ls-testimonials' ),
				array( 'w' => 'ls-posts', 's' => array( '_element_id' => 'blog' ) ),
				$cta_dark,
			),
		),
		'home-classic'    => array(
			'title' => 'صفحه اصلی (نسخه کلاسیک)',
			'kind'  => 'page',
			'meta'  => array( '_ls_header_style' => 'light' ),
			'rows'  => array(
				array( 'w' => 'ls-hero-classic' ),
				array( 'w' => 'ls-categories' ),
				array(
					'w' => 'ls-products',
					's' => array(
						'heading_eyebrow'   => '',
						'heading_title'     => 'تجهیزات و قالب‌های برگزیده',
						'heading_desc'      => 'پرفروش‌ترین محصولات خط تولید با گارانتی رسمی شرکت',
						'heading_link_text' => 'مشاهده همه محصولات',
						'heading_link'      => '/shop/',
						'section_bg'        => 'white-bordered',
						'card_style'        => 'showcase',
						'items'             => array(
							array( 'image' => 'home1_product_1', 'badge' => 'ماشین‌آلات اصلی', 'title' => 'میز ویبره صنعتی دور متغیر', 'subtitle' => 'موتور اروپایی ضد شوک، شاسی صنعتی', 'spec1_label' => 'ابعاد میز', 'spec1_value' => '۲×۱ متر', 'spec2_label' => 'نوع ویبره', 'spec2_value' => '۲ موتوره', 'spec3_label' => 'گارانتی تعویض', 'spec3_value' => '۲۴ ماه', 'price_label' => 'قیمت محصول:', 'price' => '۴۸,۰۰۰,۰۰۰', 'price_raw' => 48000000, 'button_text' => 'سفارش سریع' ),
							array( 'image' => 'home1_product_2', 'badge' => 'قالب نشکن ABS', 'title' => 'قالب واش‌بتن شیاردار ۴۰×۴۰', 'subtitle' => 'مواد درجه یک تایوانی، عدم چسبندگی ملات', 'spec1_label' => 'سایز (cm)', 'spec1_value' => '۴۰×۴۰', 'spec2_label' => 'متریال', 'spec2_value' => 'ABS نشکن', 'spec3_label' => 'تیراژ کارکرد', 'spec3_value' => '+۵۰۰ بار', 'price_label' => 'قیمت هر عدد:', 'price' => '۱۱۰,۰۰۰', 'price_raw' => 110000, 'button_text' => 'خرید عمده' ),
							array( 'image' => 'home1_product_3', 'badge' => 'نما و صراحی', 'title' => 'قالب صراحی گرد رومی ۷۰ سانت', 'subtitle' => 'دو تکه با پین قفل‌کننده آسان‌بازشو', 'spec1_label' => 'ارتفاع', 'spec1_value' => '۷۰ cm', 'spec2_label' => 'نوع جنس', 'spec2_value' => 'فایبرگلاس', 'spec3_label' => 'کیفیت لعاب', 'spec3_value' => 'سطح آینه‌ای', 'price_label' => 'قیمت هر جفت:', 'price' => '۲۹۰,۰۰۰', 'price_raw' => 290000, 'button_text' => 'سفارش سریع' ),
							array( 'image' => 'home1_product_4', 'badge' => 'مواد شیمیایی', 'title' => 'روان‌کننده پلی‌کربوکسیلات گرید A', 'subtitle' => 'افزایش مقاومت فشاری تا ۴۵ درصد و خروج حباب', 'spec1_label' => 'بسته‌بندی گالن', 'spec1_value' => '۲۰ لیتر', 'spec2_label' => 'پایه رزین', 'spec2_value' => 'پلیمری', 'spec3_label' => 'دوز مصرفی', 'spec3_value' => '۰.۵ الی ۱٪', 'price_label' => 'قیمت گالن ۲۰ لیتری:', 'price' => '۱,۴۵۰,۰۰۰', 'price_raw' => 1450000, 'button_text' => 'سفارش تست' ),
						),
					),
				),
				array(
					'w' => 'ls-icon-cards',
					's' => array(
						'variant'       => 'horizontal',
						'section_bg'    => 'canvas',
						'heading_title' => 'چرا گروه صنعتی لاریجانی استون؟',
						'heading_desc'  => 'مزیت‌های رقابتی که ما را به اولین انتخاب تولیدکنندگان سنگ مصنوعی تبدیل کرده است',
						'heading_align' => 'center',
						'items'         => array(
							array( 'icon' => array( 'value' => 'bi bi-shield-check', 'library' => 'bootstrap-icons' ), 'title' => 'قالب‌های نشکن پلیمری', 'desc' => 'استفاده از گرانول درجه یک ABS بدون شکنندگی و تغییر فرم حرارتی.' ),
							array( 'icon' => array( 'value' => 'bi bi-currency-exchange', 'library' => 'bootstrap-icons' ), 'title' => 'بهترین قیمت مستقیم کارخانه', 'desc' => 'حذف تمامی واسطه‌ها و خرید مستقیم از کارخانه در شهرک صنعتی آبیک.' ),
							array( 'icon' => array( 'value' => 'bi bi-people', 'library' => 'bootstrap-icons' ), 'title' => 'مشاوره فرمولاسیون رایگان', 'desc' => 'ارائه رایگان جدیدترین فرمولاسیون سمنت‌پلاست به خریداران خط تولید.' ),
							array( 'icon' => array( 'value' => 'bi bi-file-earmark-text', 'library' => 'bootstrap-icons' ), 'title' => 'ارسال روزانه به سراسر کشور', 'desc' => 'بسته‌بندی صنعتی پالت‌بندی شده و ارسال سریع به تمامی استان‌ها.' ),
						),
					),
				),
				array(
					'w' => 'ls-testimonials',
					's' => array(
						'layout'          => 'slider',
						'card_style'      => 'classic',
						'rating_text'     => '',
						'heading_eyebrow' => '',
						'heading_title'   => 'نظرات تولیدکنندگان و همکاران',
						'heading_desc'    => 'تجربه فعالان صنعت سنگ مصنوعی از همکاری با مجموعه لاریجانی استون',
						'section_bg'      => 'canvas',
						'items'           => array(
							array( 'text' => 'ما خط تولید موزاییک پلیمری‌مون رو با دستگاه‌های مهندس لاریجانی تجهیز کردیم. کیفیت قالب‌ها بی‌نظیره؛ بعد از یک سال کار مداوم حتی یک مورد شکستگی یا تغییر زاویه نداشتیم.', 'name' => 'مهندس حسینی', 'role' => 'کارخانه موزاییک نگین اصفهان', 'avatar' => ls_demo_media( 'testimonial_1' ), 'stars' => 5 ),
							array( 'text' => 'میز ویبره ۲ موتوره لاریجانی استون ارتعاش کاملاً یکنواختی میده که باعث شده ملات بدون حباب و مثل شیشه دربیاد. ارسال قالب‌های جدیدشون هم ظرف ۴۸ ساعت به تبریز رسید.', 'name' => 'علیرضا رادپور', 'role' => 'مدیر تولید سنگ مدرن تبریز', 'avatar' => ls_demo_media( 'testimonial_2' ), 'stars' => 5 ),
							array( 'text' => 'فرمولاسیونی که برای رزین دادند مصرف سیمان کارگاه رو ۱۵٪ کاهش داد و همزمان مقاومت خمشی قطعات بالا رفت. تیم پشتیبانی همواره پاسخگوی سوالات فنی ما هستند.', 'name' => 'کامران مهدوی', 'role' => 'صنایع سنگ پارس - شیراز', 'avatar' => ls_demo_media( 'testimonial_3' ), 'stars' => 5 ),
						),
					),
				),
				array(
					'w' => 'ls-cta',
					's' => array(
						'variant'     => 'catalog-form',
						'badge'       => 'دانلود مستقیم کاتالوگ جامع ۱۴۰۴',
						'title'       => 'دریافت رایگان کاتالوگ جامع و لیست قیمت بهمن ۱۴۰۴',
						'desc'        => 'شماره تماس یا ایمیل خود را وارد کنید تا کاتالوگ ۱۲۰ صفحه‌ای شامل مشخصات فنی، ابعاد قالب‌ها و پیش‌فاکتور خطوط تولید برای شما ارسال شود.',
						'form_name'   => 'درخواست کاتالوگ',
						'form_placeholder' => 'شماره موبایل یا ایمیل شما...',
						'form_button' => 'ارسال کاتالوگ',
						'form_note'   => 'ارسال فایل PDF به تلگرام، ایتا یا واتساپ شما در کمتر از ۲ دقیقه.',
						'form_success' => 'درخواست شما ثبت شد؛ کاتالوگ به زودی ارسال می‌شود.',
					),
				),
			),
		),
		'services'        => array(
			'title' => 'خدمات و خطوط تولید',
			'kind'  => 'page',
			'rows'  => array(
				array( 'w' => 'ls-breadcrumb', 's' => array( 'right_style' => 'badge', 'bordered' => 'yes' ) ),
				array( 'w' => 'ls-page-hero' ),
				array(
					'w' => 'ls-icon-cards',
					's' => array(
						'variant'           => 'bento',
						'columns'           => '3',
						'columns_tablet'    => '1',
						'section_bg'        => 'surface',
						'heading_eyebrow'   => 'سه رکن اصلی توانمندی‌های کارخانه',
						'heading_title'     => 'محورهای خدمات تخصصی لاریجانی استون',
						'heading_desc'      => 'زنجیره تأمین صفر تا صد تولید قطعات بتنی دکوراتیو و سنگ سمنت‌پلاست، بر مبنای توان تولید ماشین‌آلات و قالب‌های استاندارد.',
						'heading_link_text' => 'دریافت لیست قیمت قالب‌ها و ماشین‌آلات',
						'heading_link'      => array( 'url' => '#consultation-form' ),
						'heading_align'     => 'split',
						'heading_style'     => 'token',
						'items'             => array(
							array( 'icon' => array( 'value' => 'bi bi-layers', 'library' => 'bootstrap-icons' ), 'tone' => 'sage', 'eyebrow' => 'تنوع بی‌رقیب', 'title' => 'فروش بیش از ۴۰۰ مدل قالب آماده نشکن', 'desc' => 'تولید و عرضه اختصاصی انواع قالب‌های تزریقی وکیوم‌فرمینگ از مواد مرغوب ABS کره‌ای نشکن؛ شامل انواع موزائیک پلیمری، سنگ نما و دکوراتیو، جدول خیابانی، صراحی، دورباغچه، دیوارپوش و انواع سرستون و تاج.', 'chips' => "قالب موزائیک و کفپوش\nقالب نما و صراحی\nدورباغچه و جدول\nتاج و سرستون", 'footer' => 'بیش از ۵۰۰ بار قالب‌ریزی بدون شکست' ),
							array( 'icon' => array( 'value' => 'bi bi-mortarboard', 'library' => 'bootstrap-icons' ), 'tone' => 'fixed', 'eyebrow' => 'انتقال تجربیات کارگاهی', 'title' => 'آموزش تخصصی تولید سنگ مصنوعی سمنت‌پلاست', 'desc' => 'آموزش جامع عملی متناسب با شرایط آب‌وهوایی منطقه شما: اعزام مهندس ناظر جهت آموزش حضوری در محل کارگاه شما و آموزش اپراتورها یا ارائه پکیج کامل ویدیوهای گام‌به‌گام با سرفصل‌های تخصصی شبیه‌سازی مرمر، رگه‌دار کردن و رفع زردگرایی.', 'chips' => "آموزش حضوری در کارگاه\nدوره‌های ویدیویی جامع\nفرمول ترکیب رنگ رگه‌ای", 'footer' => 'همراه با اعطای تاییدیه کیفی لاریجانی' ),
							array( 'icon' => array( 'value' => 'bi bi-gear-wide-connected', 'library' => 'bootstrap-icons' ), 'tone' => 'light', 'eyebrow' => 'تجهیزات صنعتی سنگین', 'title' => 'ساخت و سفارشی‌سازی دستگاه‌ها و ماشین‌آلات مدرن', 'desc' => 'طراحی و ساخت میزهای ویبره ۲ موتوره با فنربندی صنعتی و اینورتر تنظیم فرکانس ارتعاش جهت خروج کامل حباب‌های ریز، و میکسرهای بتن استاندارد طرح پان (Pan Mixer) مجهز به تیغه‌های ضدسایش هاردوکس برای اختلاط یکدست خمیر سنگ پلیمری.', 'chips' => "میز ویبره ۲ موتوره فرکانسی\nمیکسر طرح پان ضدسایش\nپایه‌های عایق لرزش", 'footer' => 'یک سال گارانتی کامل + ۵ سال خدمات' ),
						),
					),
				),
				array( 'w' => 'ls-steps' ),
				array( 'w' => 'ls-dark-feature' ),
				array( 'w' => 'ls-comparison' ),
				array( 'w' => 'ls-lead-form', 's' => array( '_element_id' => 'consultation-form' ) ),
				array( 'w' => 'ls-faq' ),
				array(
					'w' => 'ls-cta',
					's' => array(
						'variant'   => 'dark-strip',
						'icon'      => array( 'value' => 'bi bi-people', 'library' => 'bootstrap-icons' ),
						'title'     => 'آماده همکاری و احداث خط تولید اختصاصی شما هستیم',
						'desc'      => 'مشاوره تخصصی با مدیر فنی لاریجانی استون: ۰۹۱۲۲۳۰۲۶۸۵',
						'btn1_text' => 'تماس مستقیم تلفنی',
						'btn2_text' => '',
					),
				),
			),
		),
		'portfolio'       => array(
			'title' => 'نمونه کارها',
			'kind'  => 'page',
			'rows'  => array(
				array( 'w' => 'ls-portfolio-hero' ),
				array( 'w' => 'ls-portfolio' ),
				array( 'w' => 'ls-performance' ),
				array(
					'w' => 'ls-lead-form',
					's' => array(
						'layout'       => 'split-card',
						'card_style'   => 'canvas',
						'section_bg'   => 'canvas',
						'badge'        => 'باشگاه همکاران و تولیدکنندگان لاریجانی استون',
						'badge_icon'   => array( 'value' => 'bi bi-gift-fill', 'library' => 'bootstrap-icons' ),
						'title'        => 'پروژه خود را ارسال کنید؛ برای سفارش بعدی قالب، ۱۰٪ تخفیف هدیه بگیرید!',
						'desc'         => 'اگر شما هم با تجهیزات، قالب‌ها یا مواد اولیه کارخانه لاریجانی استون پروژه‌ای زیبا اجرا کرده‌اید، تصاویر و مشخصات آن را برای ما بفرستید تا با نام کارگاه خودتان در وب‌سایت و کاتالوگ کشوری معرفی شود.',
						'info_style'   => 'checks',
						'info_items'   => array(
							array( 'title' => 'درج اطلاعات تماس و برند کارگاه شما به عنوان مجری در صفحه اختصاصی' ),
							array( 'title' => 'دریافت بن تخفیف خرید قالب‌های تزریقی جدید و اکسید آهن' ),
							array( 'title' => 'مشاوره و عیب‌یابی رایگان خط تولید توسط مهندس لاریجانی' ),
						),
						'form_name'    => 'ارسال پروژه همکاران',
						'fields'       => array(
							array( 'label' => 'نام تولیدکننده یا کارگاه', 'type' => 'text', 'name' => 'name', 'placeholder' => 'مثال: کارگاه سنگ نوین', 'required' => 'yes' ),
							array( 'label' => 'شماره تماس همراه', 'type' => 'tel', 'name' => 'phone', 'placeholder' => '۰۹۱۲...', 'required' => 'yes', 'ltr' => 'yes' ),
							array( 'label' => 'شهر محل اجرا', 'type' => 'text', 'name' => 'city', 'placeholder' => 'مثال: اصفهان', 'required' => 'yes' ),
							array( 'label' => 'نوع قالب و محصول', 'type' => 'select', 'name' => 'mold', 'options' => "نمای سه‌بعدی و سنگ دکوراتیو\nواش‌بتن و کفپوش پلیمری\nجدول و دورباغچه\nستون، سرستون و نرده صراحی\nسایر قطعات مهندسی" ),
							array( 'label' => 'بارگذاری تصاویر پروژه (حداکثر ۵ تصویر)', 'type' => 'file', 'name' => 'files', 'placeholder' => 'انتخاب فایل‌های تصویر یا رها کردن اینجا', 'hint' => 'فرمت‌های JPG، PNG (حداکثر ۸ مگابایت)' ),
							array( 'label' => 'توضیح کوتاه درباره متراژ و نوع رزین', 'type' => 'textarea', 'name' => 'desc', 'placeholder' => 'توضیح دهید از چه متریال و در چه متراژی اجرا کردید...' ),
						),
						'submit_text'  => 'ثبت و دریافت کد تخفیف',
						'submit_icon'  => array( 'value' => 'bi bi-send-fill', 'library' => 'bootstrap-icons' ),
						'submit_full'  => '',
						'note'         => 'اطلاعات شما نزد لاریجانی استون محفوظ است',
						'success_title' => 'پروژه شما با موفقیت ثبت گردید!',
						'success_text' => 'کد پیگیری {tracking} – کارشناسان ما جهت بازبینی تصاویر و ارسال کد تخفیف ۱۰٪ با شما تماس خواهند گرفت.',
					),
				),
				array( 'w' => 'ls-floating-cta' ),
			),
		),
		'contact'         => array(
			'title' => 'تماس با ما',
			'kind'  => 'page',
			'rows'  => array(
				array( 'w' => 'ls-breadcrumb' ),
				array( 'w' => 'ls-page-banner' ),
				array( 'w' => 'ls-contact-cards' ),
				array(
					'boxed' => true,
					'cols'  => array(
						array(
							58.333,
							array(
								array(
									'w' => 'ls-lead-form',
									's' => array(
										'layout'      => 'form',
										'bare'        => 'yes',
										'form_kicker' => 'درخواست رسمی استعلام قیمت، کاتالوگ و فرمولاسیون',
										'form_title'  => 'فرم ثبت درخواست مشاوره و پیش‌فاکتور',
										'form_desc'   => 'مشخصات سفارش خود را درج نمایید تا مهندسان فروش ظرف حداکثر ۲ ساعت کاری پیش‌فاکتور رسمی و فایل‌های فنی را ارسال نمایند.',
										'form_name'   => 'استعلام قیمت و پیش‌فاکتور',
										'fields'      => array(
											array( 'label' => 'نام و نام خانوادگی / نام مجموعه صنعتی', 'type' => 'text', 'name' => 'name', 'placeholder' => 'مثال: مهندس رضوانی (سنگ بتن البرز)', 'required' => 'yes', 'icon' => array( 'value' => 'bi bi-person', 'library' => 'bootstrap-icons' ) ),
											array( 'label' => 'شماره تلفن همراه (جهت هماهنگی واتس‌اپ)', 'type' => 'tel', 'name' => 'phone', 'placeholder' => '۰۹۱۲XXXXXXX', 'required' => 'yes', 'ltr' => 'yes', 'icon' => array( 'value' => 'bi bi-phone', 'library' => 'bootstrap-icons' ) ),
											array( 'label' => 'نوع محصول مورد نیاز', 'type' => 'select', 'name' => 'product', 'required' => 'yes', 'options' => "راه‌اندازی کامل خط تولید و ماشین‌آلات\nقالب‌های ABS و سیلیکونی نشکن (بیش از ۴۰۰ طرح)\nمواد اولیه تخصصی (رزین، روان‌کننده، پیگمنت معدنی)\nمشاوره فرمولاسیون و آموزش حضوری\nقطعات یدکی و ارتقای میز ویبره موجود" ),
											array( 'label' => 'حجم تقریبی پروژه یا سرمایه کارگاه', 'type' => 'select', 'name' => 'scale', 'options' => "کارگاه نوپا و مقیاس متوسط (تا ۱۰۰ متر مربع در روز)\nخط تولید صنعتی نیمه‌اتوماتیک (۱۰۰ تا ۳۰۰ متر)\nخط تولید سنگین تمام‌اتوماتیک و شهرداری‌ها\nفقط خرید قالب (زیر ۵۰ متر مربع)\nخرید عمده قالب (بالای ۲۰۰ متر مربع)" ),
											array( 'label' => 'بارگذاری نقشه کارگاه، ابعاد طرح یا تصاویر درخواستی (اختیاری)', 'type' => 'file', 'name' => 'files', 'hint' => 'فرمت‌های مجاز: PDF, DWG, JPG, PNG (حداکثر ۲۰ مگابایت)' ),
											array( 'label' => 'توضیحات فنی، اقلام درخواستی یا شرایط جغرافیایی کارگاه', 'type' => 'textarea', 'name' => 'notes', 'placeholder' => 'مشخصات زمین، متراژ تولید روزانه مدنظر، برق تک‌فاز یا سه‌فاز، یا کدهای قالب کاتالوگ را بنویسید...' ),
											array( 'label' => 'ارسال نسخه کاتالوگ تصویری و پی‌دی‌اف قیمت‌ها به همراه پیامک وضعیت استعلام.', 'type' => 'consent', 'name' => 'catalog' ),
										),
										'submit_text'   => 'ثبت درخواست و دریافت مشاوره فوری',
										'submit_full'   => '',
										'note'          => 'تضمین محرمانگی اطلاعات پروژه‌ها و فرمولاسیون‌ها',
										'success_title' => 'درخواست شما با موفقیت به واحد مهندسی ارسال گردید.',
										'success_text'  => 'شماره پیگیری: {tracking}. همکاران فنی در کوتاهترین زمان با شما تماس خواهند گرفت.',
									),
								),
							),
						),
						array( 41.666, array( array( 'w' => 'ls-plant-card' ) ) ),
					),
				),
				array( 'w' => 'ls-map' ),
				array(
					'w' => 'ls-faq',
					's' => array(
						'heading_badge'   => 'پاسخ به ابهامات رایج تولیدکنندگان',
						'heading_eyebrow' => '',
						'heading_title'   => 'پرسش‌های متداول مشتریان و پیمانکاران',
						'heading_desc'    => 'نکات مهم در خصوص ارسال بار، گارانتی نشکن بودن قالب‌ها، آموزش‌های تخصصی فرمولاسیون و پشتیبانی دستگاه‌ها.',
						'heading_align'   => 'center',
						'icon_position'   => 'header',
						'narrow'          => 'yes',
						'section_bg'      => 'none',
						'items'           => array(
							array( 'icon' => array( 'value' => 'bi bi-truck', 'library' => 'bootstrap-icons' ), 'question' => 'نحوه ارسال قالب‌ها و مواد اولیه به سراسر کشور چگونه است؟', 'answer' => 'کلیه سفارش‌های قالب و افزودنی‌های شیمیایی از انبار آبیک از طریق شرکت‌های معتبر باربری (مانند وطن، پیشتاز، پیام‌شمس) بسته‌بندی پالت شده و ظرف ۲۴ الی ۴۸ ساعت به هر نقطه از ایران تحویل داده می‌شود. ماشین‌آلات نیز با بارنامه رسمی و بیمه‌نامه دولتی با خودروهای کفی ارسال می‌گردند.' ),
							array( 'icon' => array( 'value' => 'bi bi-shield-check', 'library' => 'bootstrap-icons' ), 'question' => 'شرایط ضمانت و تعویض قالب‌های ABS در صورت شکست چیست؟', 'answer' => 'قالب‌های لاریجانی استون با بهترین گرید ABS نشکن ضدسایش تولید می‌شوند. این قالب‌ها دارای ۲ سال ضمانت تعویض کتبی در برابر هرگونه ترک‌خوردگی ناشی از ویبره و فرآیند دپو هستند. در صورت بروز هرگونه عیب کیفی، قالب‌ها بدون قید و شرط تعویض می‌گردند.' ),
							array( 'icon' => array( 'value' => 'bi bi-mortarboard', 'library' => 'bootstrap-icons' ), 'question' => 'آیا آموزش فرمولاسیون و راه‌اندازی کارگاه شامل هزینه جداگانه است؟', 'answer' => 'برای خریداران خط تولید کامل و پکیج‌های قالب، آموزش کامل حضوری در محل کارخانه آبیک یا اعزام کارشناس فنی به کارگاه مشتری به همراه دفترچه فنی اختصاصی فرمولاسیون کاملاً رایگان انجام می‌پذیرد. همچنین پشتیبانی تلفنی به صورت مادام‌العمر در اختیار شماست.' ),
							array( 'icon' => array( 'value' => 'bi bi-gear-wide-connected', 'library' => 'bootstrap-icons' ), 'question' => 'آیا امکان تست دستگاه‌ها و دیدن خط تولید نمونه قبل از خرید وجود دارد؟', 'answer' => 'بله. ما در محوطه کارخانه آبیک یک خط کامل نمونه فعال داریم. مشتریان محترم می‌توانند با هماهنگی قبلی تشریف آورده و تست ارتعاش میز ویبره، کیفیت اختلاط میکسر بتن و دپوی قالب‌ها را به صورت عملی از نزدیک بررسی نمایند.' ),
						),
					),
				),
				array(
					'w' => 'ls-cta',
					's' => array(
						'variant'    => 'soft-card',
						'icon'       => array( 'value' => 'bi bi-hand-thumbs-up-fill', 'library' => 'bootstrap-icons' ),
						'title'      => 'نیاز به دریافت فوری لیست قیمت بهمن و پیش‌فاکتور رسمی دارید؟',
						'desc'       => 'فایل پی‌دی‌اف به‌روز شامل لیست ۴۰۰ قالب و کاتالوگ ماشین‌آلات را مستقیماً از طریق تلگرام یا ایتا نیز دریافت کنید.',
						'btn1_text'  => 'تماس با مدیر مهندسی',
						'btn1_icon'  => array( 'value' => 'bi bi-telephone', 'library' => 'bootstrap-icons' ),
						'btn2_text'  => 'ارتباط در پیام‌رسان',
						'btn2_icon'  => array( 'value' => 'bi bi-whatsapp', 'library' => 'bootstrap-icons' ),
						'section_bg' => 'none',
					),
				),
			),
		),
		'catalog'         => array(
			'title' => 'فروشگاه و کاتالوگ',
			'kind'  => 'page',
			'rows'  => ls_demo_shop_rows(),
		),
		'store'           => array(
			'title' => 'فروشگاه محصولات',
			'kind'  => 'page',
			'rows'  => array(
				array(
					'w' => 'ls-shop-hero',
					's' => array(
						'variant'    => 'bar',
						'badge'      => 'بزرگترین بانک قالب‌های ABS نشکن و ماشین‌آلات قطعات بتنی در کشور',
						'badge_icon' => 'patch-check-fill',
						'desc'       => 'تأمین بی‌واسطه بیش از ۴۰۰ مدل قالب تزریقی ضدسایش، میزهای ویبره با ارتعاش یکنواخت، میکسر ۵۰۰ کیلویی تخصصی و افزودنی‌های پلیمری فرموله‌شده برای کارخانجات و کارگاه‌های سراسر ایران.',
					),
				),
				array(
					'w' => 'ls-catalog',
					's' => array(
						'source'             => 'manual',
						'layout'             => 'store',
						'items'              => 'demo:store_items',
						'search_placeholder' => 'جستجوی قالب، ویبره، رزین یا ابعاد...',
						'chips'              => array(
							array( 'key' => 'molds', 'label' => 'قالب‌های ABS و کامپوزیت (۴)', 'icon' => '' ),
							array( 'key' => 'machinery', 'label' => 'ماشین‌آلات و میکسرها (۲)', 'icon' => '' ),
							array( 'key' => 'materials', 'label' => 'رزین و رنگدانه‌های صنعتی (۲)', 'icon' => '' ),
							array( 'key' => 'ready-stock', 'label' => 'تحویل فوری از انبار', 'icon' => 'bi bi-dot-pulse' ),
						),
						'footer_note'        => 'نمایش ۱ تا ۸ از بیش از ۴۰۰ مدل قالب و ماشین‌آلات موجود در انبار مرکزی',
					),
				),
				array(
					'w' => 'ls-cta',
					's' => array(
						'variant'    => 'consult-band',
						'badge'      => 'مشاوره تخصصی مهندسی و راه‌اندازی خط تولید کارگاهی',
						'badge_icon' => 'headset',
						'title'      => 'نمی‌دانید چه قالبی برای شرایط بازار منطقه شما پرسودتر است؟',
						'desc'       => 'مهندسین لاریجانی استون، از تیراژ روزانه تا محاسبه مقدار مصرف رزین پلی‌کربوکسیلات، درصد سیمان، دانه‌بندی سیلیس و انتخاب ابعاد میز ویبره را به صورت کاملاً رایگان برای کارگاه شما آنالیز می‌کنند.',
						'btn1_text'  => 'درخواست تماس فوری کارشناس',
						'btn1_icon'  => '',
						'btn1_link'  => 'tel:09122302685',
					),
				),
				array(
					'w' => 'ls-icon-cards',
					's' => array(
						'variant'         => 'guide',
						'columns'         => '3',
						'columns_tablet'  => '3',
						'section_bg'      => 'none',
						'heading_eyebrow' => 'راهنمای تخصصی کارگاهی',
						'heading_title'   => 'تفاوت قالب‌های ABS پلیمری لاریجانی با قالب‌های ارزان‌قیمت بازیافتی',
						'items'           => array(
							array( 'icon' => 'patch-check-fill', 'title' => 'ضدپوسته و عدم تغییر فرم', 'desc' => 'به دلیل استفاده از ورق‌های درجه یک با ضخامت واقعی ۴ و ۵ میلی‌متر، حرارت ناشی از هیدراتاسیون سیمان و ضربات ویبره موجب خمیدگی لبه‌ها و پریدگی گوشه سنگ تولیدی نمی‌شود.' ),
							array( 'icon' => 'magic', 'title' => 'جدایش بدون روغن نامرغوب', 'desc' => 'سطوح قالب‌ها در فرآیند پولیش CNC صیقل داده شده‌اند؛ بنابراین سنگ بدون ایجاد لکه چربی یا حباب‌های هوای ریز به آسانی با یک چرخش دست از قالب خارج می‌شود.' ),
							array( 'icon' => 'arrow-repeat', 'title' => 'توجیه اقتصادی تیراژ بالا', 'desc' => 'در حالی که قالب‌های نامرغوب پس از ۵۰ الی ۸۰ شات دچار شکنندگی می‌شوند، قالب‌های لاریجانی استون حداقل ۵۰۰ سیکل بدون افت براقیت سطح سنگ، بازدهی مداوم خواهند داشت.' ),
						),
					),
				),
			),
		),
		'product-sample'  => array(
			'title' => 'نمونه صفحه محصول',
			'kind'  => 'page',
			'rows'  => ls_demo_product_rows( 'manual' ),
		),

		// ------------------------------------------------------------------ Theme Builder templates.
		'tpl-header'          => array(
			'title' => 'لاریجانی – هدر',
			'kind'  => 'header',
			'rows'  => array( array( 'w' => 'ls-header' ) ),
		),
		'tpl-footer'          => array(
			'title' => 'لاریجانی – فوتر',
			'kind'  => 'footer',
			'rows'  => array( array( 'w' => 'ls-footer' ) ),
		),
		'tpl-single-post'     => array(
			'title' => 'لاریجانی – تک‌نوشته',
			'kind'  => 'single-post',
			'rows'  => array(
				array( 'w' => 'ls-post-hero' ),
				array( 'w' => 'ls-post-content' ),
				array(
					'w' => 'ls-cta',
					's' => array(
						'variant'    => 'dark-card',
						'badge'      => 'راه‌اندازی صفر تا صد خطوط مکانیزه',
						'badge_icon' => array( 'value' => 'bi bi-buildings', 'library' => 'bootstrap-icons' ),
						'title'      => 'آماده راه‌اندازی یا ارتقای خط تولید سنگ مصنوعی کارگاه خود هستید؟',
						'desc'       => 'تامین ماشین‌آلات سنگین شامل میکسرهای طرح آلمان، میزهای ویبره با ارتعاش کنترل‌شده و بیش از ۴۵۰ مدل قالب کامپوزیتی نشکن با پشتیبانی و گارانتی طلایی گروه صنعتی لاریجانی استون.',
						'btn1_text'  => 'استعلام قیمت و مشاوره رایگان',
						'btn2_text'  => 'مشاهده خطوط تولید',
						'btn2_link'  => array( 'url' => '/services/' ),
						'btn2_icon'  => array( 'value' => 'bi bi-arrow-left', 'library' => 'bootstrap-icons' ),
					),
				),
			),
		),
		'tpl-archive'         => array(
			'title' => 'لاریجانی – آرشیو وبلاگ',
			'kind'  => 'archive',
			'rows'  => array(
				array( 'w' => 'ls-blog-hero' ),
				array( 'w' => 'ls-featured-post' ),
				array( 'w' => 'ls-posts-grid' ),
				array(
					'w' => 'ls-cta',
					's' => array(
						'variant'    => 'dark-card',
						'badge'      => 'پشتیبانی فنی و مهندسی کارخانجات',
						'badge_icon' => array( 'value' => 'bi bi-headset', 'library' => 'bootstrap-icons' ),
						'title'      => 'نیاز به تنظیم فرمولاسیون اختصاصی یا رفع اشکال خط تولید دارید؟',
						'desc'       => 'تیم مهندسی لاریجانی استون آماده ارائه مشاوره مستقیم تلفنی، تست آزمایشگاهی نمونه سنگ‌های کارگاه شما و آموزش حضوری اپراتورهای خط تولید است.',
						'btn1_text'  => 'تماس مستقیم با مهندس لاریجانی',
						'btn2_style' => 'emerald',
						'btn2_icon'  => array( 'value' => 'bi bi-whatsapp', 'library' => 'bootstrap-icons' ),
						'center_mobile' => 'yes',
					),
				),
			),
		),
		'tpl-product'         => array(
			'title' => 'لاریجانی – تک‌محصول',
			'kind'  => 'product',
			'rows'  => ls_demo_product_rows( 'auto' ),
		),
		'tpl-product-archive' => array(
			'title' => 'لاریجانی – فروشگاه',
			'kind'  => 'product-archive',
			'rows'  => ls_demo_shop_rows( 'auto' ),
		),
		'tpl-404'             => array(
			'title' => 'لاریجانی – صفحه ۴۰۴',
			'kind'  => 'error-404',
			'rows'  => array(
				array(
					'w' => 'ls-page-banner',
					's' => array(
						'badge'      => 'خطای ۴۰۴',
						'title'      => 'صفحه مورد نظر پیدا نشد',
						'desc'       => 'ممکن است آدرس تغییر کرده باشد. از منوی بالا یا جستجو استفاده کنید.',
						'show_side'  => '',
					),
				),
				array( 'w' => 'ls-categories', 's' => array( 'heading_title' => 'شاید به دنبال این بخش‌ها باشید', 'heading_desc' => '' ) ),
			),
		),
	);
}

/**
 * Shop page rows.
 *
 * @param string $source Catalog source.
 * @return array
 */
function ls_demo_shop_rows( $source = 'manual' ) {
	return array(
		array( 'w' => 'ls-shop-hero' ),
		array( 'w' => 'ls-catalog', 's' => array( 'source' => $source ) ),
		array(
			'w' => 'ls-icon-cards',
			's' => array(
				'variant'           => 'simple',
				'columns'           => '3',
				'columns_tablet'    => '1',
				'section_bg'        => 'low',
				'heading_eyebrow'   => 'استانداردهای قالب‌سازی لاریجانی استون',
				'heading_title'     => 'چرا قالب‌های نشکن ABS ما انتخاب اول کارگاه‌هاست؟',
				'heading_desc'      => 'استفاده از پودر بازیافتی و گرانول آسیابی در بازار متداول است؛ ما صرفاً از مواد خالص کره‌ای با برگ آزمایشگاهی استفاده می‌کنیم.',
				'heading_align'     => 'split-desc',
				'heading_style'     => 'token',
				'items'             => array(
					array( 'icon' => array( 'value' => 'bi bi-shield-slash', 'library' => 'bootstrap-icons' ), 'tone' => 'primary', 'title' => '۱۰۰٪ ضدپوسته و عدم تغییر فرم', 'desc' => 'در شرایط اقلیمی بسیار گرم تا مثبت ۵۰ درجه و در برابر لرزش‌های با فرکانس بالای میزهای ویبره صنعتی، زاویه‌ها و گونیای قالب ابداً تغییر فرم نمی‌دهد.' ),
					array( 'icon' => array( 'value' => 'bi bi-magic', 'library' => 'bootstrap-icons' ), 'tone' => 'emerald', 'title' => 'جدایش قطعه بدون روغن نامرغوب', 'desc' => 'پلیش و صیقل فوق‌العاده سطح قالب مانع از چسبیدن شیره سیمان می‌شود. بدون نیاز به اسیدشویی مخرب، قطعه تنها با یک ضربه آرام از قالب جدا می‌شود.' ),
					array( 'icon' => array( 'value' => 'bi bi-patch-check-fill', 'library' => 'bootstrap-icons' ), 'tone' => 'amber', 'title' => 'تضمین پتروشیمی نو کره جنوبی', 'desc' => 'استفاده انحصاری از ورق‌های درجه یک با ضخامت واقعی (نه اسمی). ضمانت کتبی عدم شکستگی در زمان خروج قطعه در شرایط کارگاهی نرمال.' ),
				),
			),
		),
		array(
			'w' => 'ls-cta',
			's' => array(
				'variant'    => 'dark-form',
				'badge'      => 'مشاوره استانی بر پایه آمایش مصرف مصالح ساختمانی',
				'badge_icon' => array( 'value' => 'bi bi-geo-alt-fill', 'library' => 'bootstrap-icons' ),
				'title'      => 'نمی‌دانید چه قالبی برای شرایط بازار منطقه شما پرسودتر است؟',
				'desc'       => 'سلیقه معماری در مناطق کوهستانی، کویری و مرطوب ساحلی متفاوت است. مهندسین فروش لاریجانی استون بر اساس سوابق فروش پروژه‌ای استان شما، پرفروش‌ترین ترکیب قالب نما، کف و جدول را همراه با فرمول دقیق عیار سیمان پیشنهاد می‌دهند.',
				'btn1_text'  => 'تماس با مدیر فنی: ۰۹۱۲ ۲۳۰ ۲۶۸۵',
				'btn1_icon'  => array( 'value' => 'bi bi-telephone-outbound-fill', 'library' => 'bootstrap-icons' ),
				'btn2_text'  => 'ارتباط در واتساپ: ۰۹۳۵ ۴۴۳ ۱۳۲۱',
				'btn2_link'  => array( 'url' => 'https://wa.me/989354431321', 'is_external' => 'on' ),
				'btn2_icon'  => array( 'value' => 'bi bi-whatsapp', 'library' => 'bootstrap-icons' ),
				'btn2_style' => 'emerald',
			),
		),
	);
}

/**
 * Single product rows.
 *
 * @param string $source auto|manual.
 * @return array
 */
function ls_demo_product_rows( $source = 'auto' ) {
	return array(
		array( 'w' => 'ls-product-detail', 's' => array( 'source' => $source ) ),
		array( 'w' => 'ls-product-tabs', 's' => array( 'source' => $source ) ),
		array(
			'w' => 'ls-products',
			's' => array(
				'source'            => 'auto' === $source ? 'woocommerce' : 'manual',
				'wc_query'          => 'related',
				'card_style'        => 'compact',
				'section_bg'        => 'canvas',
				'heading_eyebrow'   => 'تجهیزات و مواد مکمل خط تولید',
				'heading_title'     => 'مواد اولیه و ماشین‌آلات متناسب با این قالب',
				'heading_desc'      => '',
				'heading_link_text' => 'مشاهده کل کاتالوگ فروشگاه',
				'heading_style'     => 'token',
				'items'             => 'demo:cross_sell',
			),
		),
		array(
			'w' => 'ls-cta',
			's' => array(
				'variant'    => 'dark-card',
				'badge'      => 'پشتیبانی مهندسی خط تولید سنگ مصنوعی',
				'badge_icon' => array( 'value' => 'bi bi-gear-wide-connected', 'library' => 'bootstrap-icons' ),
				'title'      => 'قصد راه‌اندازی کارگاه یا تولید انبوه با این طرح را دارید؟',
				'desc'       => 'از مشاوره فنی در زمینه چیدمان میزهای ویبره، انتخاب میکسر و نسبت‌های دقیق رزین پلیمری توسط مهندس لاریجانی بهره‌مند شوید. قالب‌ها به همراه آموزش ویدئویی و پشتیبانی فنی کارگاهی ارسال می‌گردند.',
				'btn1_text'  => '۰۹۱۲ ۲۳۰ ۲۶۸۵',
				'btn1_sub'   => 'تماس مستقیم با مدیریت فنی',
				'btn2_style' => 'darker',
				'btn2_icon'  => array( 'value' => 'bi bi-whatsapp', 'library' => 'bootstrap-icons' ),
				'center_mobile' => 'yes',
			),
		),
	);
}
