<?php
/**
 * Control defaults of the theme widgets whose content is converted to native
 * Elementor widgets by inc/demo/native.php. GENERATED from the widget classes
 * (control "default" values and repeater field defaults) — regenerate after
 * changing a widget's defaults. Keep free of WordPress calls (used by the
 * standalone JSON exporter).
 *
 * @package Larijani
 */

defined( 'ABSPATH' ) || exit;

/**
 * Defaults per widget type.
 *
 * @return array
 */
function larijani_widget_defaults() {
	return array(
		'ls-icon-cards' => array(
			'defaults' => array(
				'variant' => 'badge',
				'columns' => '4',
				'columns_tablet' => '2',
				'columns_mobile' => '1',
				'section_bg' => 'white-bordered',
				'heading_eyebrow' => '',
				'heading_title' => '',
				'heading_desc' => '',
				'heading_link_text' => '',
				'heading_link' => array(
					'url' => '#',
				),
				'heading_align' => 'center',
				'heading_style' => 'classic',
				'heading_tag' => 'h2',
				'items' => array(
					array(
						'icon' => array(
							'value' => 'bi bi-shield-check',
							'library' => 'bootstrap-icons',
						),
						'title' => 'قالب‌های نشکن پلیمری',
						'desc' => 'مواد درجه یک ABS بدون تغییر شکل',
					),
					array(
						'icon' => array(
							'value' => 'bi bi-award',
							'library' => 'bootstrap-icons',
						),
						'title' => '۲ سال ضمانت ماشین‌آلات',
						'desc' => 'موتورهای ویبره با سیم‌پیچی مس',
					),
					array(
						'icon' => array(
							'value' => 'bi bi-headset',
							'library' => 'bootstrap-icons',
						),
						'title' => 'مشاوره رایگان تولید',
						'desc' => 'فرمولاسیون اختصاصی سنگ و بتن',
					),
					array(
						'icon' => array(
							'value' => 'bi bi-truck',
							'library' => 'bootstrap-icons',
						),
						'title' => 'ارسال سریع به کل کشور',
						'desc' => 'بارگیری روزانه از انبار مرکزی',
					),
				),
			),
			'fields' => array(
				'items' => array(
					'icon' => array(
						'value' => 'bi bi-shield-check',
						'library' => 'bootstrap-icons',
					),
					'tone' => 'primary',
					'eyebrow' => '',
					'title' => '',
					'desc' => '',
					'chips' => '',
					'footer' => '',
					'link' => array(
						'url' => '',
					),
				),
			),
		),
		'ls-about' => array(
			'defaults' => array(
				'eyebrow' => 'درباره مجموعه صنعتی ما',
				'title' => 'پیشگام در صنعت قالب‌سازی و ماشین‌آلات سنگ مصنوعی',
				'content' => '<p>مجموعه <strong>لاریجانی استون</strong> با بیش از ۱۵ سال تجربه تخصصی و مستمر، با در اختیار داشتن خط تولید مجهز تزریق پلاستیک، ورق‌کاری صنعتی و آزمایشگاه کنترل کیفیت بتن، صفر تا صد ملزومات تولیدکنندگان سنگ‌های پلیمری و بتنی را با بالاترین استانداردهای روز دنیا تأمین می‌کند.</p><p>هدف ما ارتقای بهره‌وری کارگاه‌ها با تجهیزاتی با دوام، خروج آسان قطعه از قالب، سطح نهایی صیقلی و پشتیبانی فنی دائمی است.</p>',
				'button_text' => 'اطلاعات بیشتر و درخواست نمایندگی',
				'button_link' => array(
					'url' => '#contact',
				),
				'image' => 'about',
				'section_bg' => 'white-bordered',
				'stats' => array(
					array(
						'value' => '+۱۵',
						'label' => 'سال سابقه تخصصی درخشان',
						'accent' => 'yes',
					),
					array(
						'value' => '+۲,۵۰۰',
						'label' => 'پروژه موفق تجهیز کارگاه',
					),
					array(
						'value' => '+۱,۲۰۰',
						'label' => 'تنوع قالب‌های انحصاری',
					),
					array(
						'value' => '+۲۵',
						'label' => 'مهندس و کارشناس تولید',
						'accent' => 'yes',
					),
				),
				'counter' => 'yes',
			),
			'fields' => array(
				'stats' => array(
					'value' => '',
					'label' => '',
					'accent' => '',
				),
			),
		),
		'ls-testimonials' => array(
			'defaults' => array(
				'heading_eyebrow' => 'رضایت همکاران صنعتی',
				'heading_title' => 'نظرات تولیدکنندگان قطعات بتنی',
				'heading_desc' => '',
				'heading_link_text' => '',
				'heading_link' => array(
					'url' => '#',
				),
				'heading_align' => 'split',
				'heading_style' => 'classic',
				'heading_tag' => 'h2',
				'rating_text' => '۴.۹ از ۵ رضایت مشتری',
				'items' => array(
					array(
						'text' => 'قالب‌های واش‌بتن لاریجانی استون واقعاً نشکن هستند؛ ما بیش از ۵۰۰ مرتبه قالب‌ریزی انجام دادیم بدون حتی یک مورد شکستگی یا انحراف لبه. خروج سنگ هم بدون نیاز به اسید راحت انجام می‌شه.',
						'name' => 'مهندس حسینی',
						'role' => 'کارخانه موزاییک اصفهان',
					),
					array(
						'text' => 'میز ویبره ۲ متری که پارسال خریداری کردیم بدون کوچک‌ترین لرزش معکوس به بدنه، ویبره یکدست و بی‌نقصی به ملات میده. تراکم قطعات نهایی با فرمول رزین خودشون فوق‌العاده بالاست.',
						'name' => 'علیرضا رادپور',
						'role' => 'مدیر تولید سنگ مدرن تبریز',
					),
					array(
						'text' => 'پشتیبانی فنی و آموزش ترکیب رزین‌ها نقطه تمایز لاریجانی استون هست. از صفر کارگاه راه‌اندازی کردیم و در کمتر از یک هفته به راندمان تولید روزانه مطلوب رسیدیم.',
						'name' => 'کامران مهدوی',
						'role' => 'صنایع سنگ مصنوعی شیراز',
					),
				),
				'layout' => 'grid',
				'card_style' => 'default',
				'columns' => '3',
				'columns_tablet' => '3',
				'columns_mobile' => '1',
				'section_bg' => 'none',
			),
			'fields' => array(
				'items' => array(
					'text' => '',
					'name' => '',
					'role' => '',
					'avatar' => array(
						'url' => '',
					),
					'stars' => 0,
				),
			),
		),
		'ls-cta' => array(
			'defaults' => array(
				'variant' => 'dark-card',
				'icon' => array(
					'value' => 'bi bi-people',
					'library' => 'bootstrap-icons',
				),
				'badge' => '',
				'badge_icon' => array(
					'value' => '',
					'library' => '',
				),
				'title' => 'آماده راه‌اندازی خط تولید سنگ مصنوعی و قطعات بتنی هستید؟',
				'desc' => 'همین حالا با مهندسین و مشاورین ارشد لاریجانی استون تماس بگیرید و لیست قیمت و فرمولاسیون جامع را به صورت رایگان دریافت کنید.',
				'center_mobile' => '',
				'checks' => 'تست قالب‌ها قبل از بارگیری
ارائه طرح اختلاط اختصاصی
آموزش ترکیب رنگ رگه‌ای و گرانیتی',
				'phone1_label' => 'ارتباط مستقیم با مدیر فنی (مهندس لاریجانی):',
				'phone1' => '09122302685',
				'phone2_label' => 'مسئول واحد فروش و توزیع قالب و رزین:',
				'phone2' => '09354431321',
				'btn1_text' => 'تماس مستقیم: ۰۹۱۲۲۳۰۲۶۸۵',
				'btn1_sub' => '',
				'btn1_link' => array(
					'url' => 'tel:09122302685',
				),
				'btn1_icon' => array(
					'value' => 'bi bi-telephone-fill',
					'library' => 'bootstrap-icons',
				),
				'btn2_text' => 'ارسال پیام در واتساپ',
				'btn2_link' => array(
					'url' => 'https://wa.me/989122302685',
					'is_external' => true,
				),
				'btn2_icon' => array(
					'value' => '',
					'library' => '',
				),
				'btn2_style' => 'glass',
				'form_name' => 'درخواست تماس فوری',
				'form_title' => 'درخواست مشاوره اختصاصی',
				'form_desc' => 'شماره همراه خود را وارد کنید تا کارشناس خط تولید حداکثر ظرف ۱۵ دقیقه با شما تماس بگیرد.',
				'form_placeholder' => '0912...',
				'form_button' => 'ثبت درخواست تماس فوری',
				'form_note' => '',
				'form_success' => 'درخواست شما ثبت شد؛ به زودی تماس خواهیم گرفت.',
				'section_bg' => 'none',
			),
			'fields' => array(),
		),
		'ls-steps' => array(
			'defaults' => array(
				'heading_eyebrow' => 'مراحل گام‌به‌گام راه‌اندازی',
				'heading_title' => 'از سوله خالی تا اولین تولید تجاری سنگ پلیمری',
				'heading_desc' => 'نقشه راه روشن و استاندارد لاریجانی استون برای ورود امن سرمایه‌گذاران و تولیدکنندگان به بازار پرسود مصالح نوین ساختمانی.',
				'heading_link_text' => '',
				'heading_link' => array(
					'url' => '#',
				),
				'heading_align' => 'center',
				'heading_style' => 'token',
				'heading_tag' => 'h2',
				'steps' => array(
					array(
						'icon' => array(
							'value' => 'bi bi-bar-chart-line',
							'library' => 'bootstrap-icons',
						),
						'tone' => 'dark',
						'title' => 'مشاوره و امکان‌سنجی اولیه',
						'desc' => 'بررسی متراژ فضا (برق ۳ فاز یا تک‌فاز، انبارش قالب‌ها) متناسب با بودجه اولیه و پتانسیل بازار منطقه شما.',
						'note' => 'تحلیل ظرفیت تولید',
					),
					array(
						'icon' => array(
							'value' => 'bi bi-tools',
							'library' => 'bootstrap-icons',
						),
						'title' => 'انتخاب قالب و ساخت ماشین‌آلات',
						'desc' => 'انتخاب پرفروش‌ترین قالب‌های منطقه و ساخت سفارشی میز ویبره و میکسر صنعتی با بالاترین متریال کارخانه‌ای.',
						'note' => 'استاندارد صادراتی',
					),
					array(
						'icon' => array(
							'value' => 'bi bi-eyedropper',
							'library' => 'bootstrap-icons',
						),
						'title' => 'تأمین رزین و پیگمنت مرغوب',
						'desc' => 'ارسال مستقیم رزین‌های پلی‌کربوکسیلات آلمانی، رنگدانه‌های معدنی اکسید آهن بایر و روان‌کننده‌های فوق زودگیر.',
						'note' => 'مواد اولیه تضمین‌شده',
					),
					array(
						'icon' => array(
							'value' => 'bi bi-book',
							'library' => 'bootstrap-icons',
						),
						'title' => 'ارسال فرمولاسیون و آموزش',
						'desc' => 'انتقال فرمول سمنت‌پلاست بدون حباب با مقاومت در برابر سرما و گرما به همراه تست عملی اولین بچ تولیدی کارگاه.',
						'note' => 'تست مقاومت فشاری',
					),
					array(
						'icon' => array(
							'value' => 'bi bi-headset',
							'library' => 'bootstrap-icons',
						),
						'tone' => 'sage',
						'title' => 'پشتیبانی دائم و توسعه قالب‌ها',
						'desc' => 'پشتیبانی فنی بی‌پایان، رفع ایرادات خط، معرفی پروژه‌های منطقه‌ای و ارائه تخفیف‌های ویژه بر روی قالب‌های جدید.',
						'note' => 'توسعه مستمر خط',
					),
				),
				'columns' => '5',
				'columns_tablet' => '2',
				'columns_mobile' => '1',
				'gallery' => array(
					array(
						'image' => 'services_gallery_1',
						'eyebrow' => 'تست کارگاهی',
						'title' => 'تست تراکم ارتعاشی در کارخانه آبیک',
						'desc' => 'تمام میزهای ویبره پیش از تحویل با بارگذاری کامل قالب‌ها تحت سنجش شتاب‌سنج دیجیتال کالیبره می‌شوند.',
					),
					array(
						'image' => 'services_gallery_2',
						'eyebrow' => 'انبارش استاندارد',
						'title' => 'دسترسی دائم به قطعات یدکی و قالب‌ها',
						'desc' => 'بزرگ‌ترین بانک قالب کشور با تحویل سریع ۴۸ ساعته جهت جلوگیری از هرگونه توقف در خط تولید شما.',
					),
				),
				'section_bg' => 'canvas',
			),
			'fields' => array(
				'steps' => array(
					'icon' => array(
						'value' => 'bi bi-bar-chart-line',
						'library' => 'bootstrap-icons',
					),
					'title' => '',
					'desc' => '',
					'note' => '',
					'tone' => 'primary',
				),
				'gallery' => array(
					'image' => array(
						'url' => '',
					),
					'eyebrow' => '',
					'title' => '',
					'desc' => '',
				),
			),
		),
		'ls-page-banner' => array(
			'defaults' => array(
				'badge' => 'پاسخگویی فنی کارخانجات و خطوط صنعتی سنگ مصنوعی',
				'badge_icon' => array(
					'value' => 'bi bi-gear-wide-connected',
					'library' => 'bootstrap-icons',
				),
				'title' => 'تماس با گروه صنعتی لاریجانی استون',
				'desc' => 'ارتباط مستقیم با مدیریت مهندسی، کارشناسان متالورژی و قالب‌های نشکن بتن، تأمین‌کنندگان رزین‌های پلی‌کربوکسیلاتی و واحد پشتیبانی ماشین‌آلات سنگین مستقر در مجتمع صنعتی پیروز آبیک.',
				'show_side' => 'yes',
				'side_label' => 'زمان پاسخگویی میانگین',
				'side_value' => 'کمتر از ۱۵ دقیقه',
				'side_percent' => 94,
				'side_note_1' => 'ترافیک استعلام: متراکم',
				'side_note_2' => '۹۴٪ رضایت مشتریان صنعتی',
			),
			'fields' => array(),
		),
		'ls-contact-cards' => array(
			'defaults' => array(
				'items' => array(
					array(
						'icon' => array(
							'value' => 'bi bi-gear-wide-connected',
							'library' => 'bootstrap-icons',
						),
						'icon_tone' => 'sage',
						'badge' => 'خط تولید و میز ویبره',
						'title' => 'مشاوره راه‌اندازی و ماشین‌آلات',
						'desc' => 'مشاوره فنی راه‌اندازی خطوط سنگ پلیمری، میکسر بتن، میزهای ویبره با ضربه افقی و عمودی، و محاسبات تناژ کارگاهی.',
						'mode' => 'phone',
						'phone' => '09122302685',
						'phone_icon' => array(
							'value' => 'bi bi-telephone-fill',
							'library' => 'bootstrap-icons',
						),
						'btn1_text' => 'تماس مستقیم',
						'btn2_text' => 'واتس‌اپ واحد',
						'whatsapp' => '989122302685',
					),
					array(
						'icon' => array(
							'value' => 'bi bi-layers-fill',
							'library' => 'bootstrap-icons',
						),
						'icon_tone' => 'sfixed',
						'badge' => 'قالب نشکن و رنگ',
						'badge_tone' => 'sage',
						'title' => 'واحد فروش قالب، رنگ و رزین',
						'desc' => 'ارسال کاتالوگ بیش از ۴۰۰ طرح قالب ABS و سیلیکونی، پیگمنت‌های معدنی بایر، و روان‌سازهای پلی‌کربوکسیلاتی.',
						'mode' => 'phone',
						'phone' => '09354431321',
						'phone_icon' => array(
							'value' => 'bi bi-cart3',
							'library' => 'bootstrap-icons',
						),
						'btn1_text' => 'تماس فروش',
						'btn2_text' => 'دریافت کاتالوگ',
						'btn2_dark' => 'yes',
						'whatsapp' => '989354431321',
					),
					array(
						'icon' => array(
							'value' => 'bi bi-buildings',
							'library' => 'bootstrap-icons',
						),
						'icon_tone' => 'high',
						'badge' => 'کارخانه و انبار مرکزی',
						'badge_tone' => 'amber',
						'title' => 'آدرس و تقویم کاری کارخانه',
						'desc' => 'قزوین، آبیک، بلوار خلیج فارس، مجتمع صنعتی پیروز، پلاک ۱۵',
						'mode' => 'hours',
						'hours' => 'شنبه تا چهارشنبه:|۰۸:۰۰ الی ۱۸:۰۰|emerald
پنج‌شنبه‌ها:|۰۸:۰۰ الی ۱۴:۰۰|amber
جمعه و ایام تعطیل رسمی:|تعطیل (انبار تحویل هماهنگ)|muted',
					),
				),
				'columns' => '3',
				'columns_tablet' => '1',
				'columns_mobile' => '1',
			),
			'fields' => array(
				'items' => array(
					'icon' => array(
						'value' => 'bi bi-gear-wide-connected',
						'library' => 'bootstrap-icons',
					),
					'icon_tone' => 'sage',
					'badge' => '',
					'badge_tone' => 'light',
					'title' => '',
					'desc' => '',
					'mode' => 'phone',
					'phone' => '',
					'phone_icon' => array(
						'value' => 'bi bi-telephone-fill',
						'library' => 'bootstrap-icons',
					),
					'btn1_text' => 'تماس مستقیم',
					'btn2_text' => 'واتس‌اپ واحد',
					'btn2_dark' => '',
					'whatsapp' => '',
					'hours' => '',
				),
			),
		),
	);
}
