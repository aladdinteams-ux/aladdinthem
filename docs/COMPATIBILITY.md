# گزارش سازگاری – Larijani Stone 1.2.0

تاریخ بررسی: ۱۳ مهر ۱۴۰۵ (۵ اکتبر ۲۰۲۶) · وضعیت‌ها فقط بر اساس شواهد اجرایی: PASS / WARN / FAIL / UNKNOWN / NOT EXECUTED

## نسخه‌ها

| مورد | حداقل اعلام‌شده | آزمایش اجرایی (Runtime) | وضعیت |
|---|---|---|---|
| وردپرس | 6.5 | 6.5.5 و 7.1.2 (SQLite) – نصب، راه‌اندازی خودکار، همه مسیرها | PASS |
| PHP | 7.4 | اجرا روی 8.3.6؛ سازگاری 7.4 فقط به‌صورت ایستا (PHPCompatibilityWP) | PASS (8.3) / UNKNOWN (7.4 runtime) |
| Elementor (رایگان) | 3.20 | **اجرا نشد** – دانلود افزونه واقعی در محیط ساخت ممکن نبود (403/404). رندر ویجت‌ها با شبیه‌ساز API (stub سخت‌گیر با type-hint واقعی `Widgets_Manager::register(Widget_Base)`) و رندر جایگزین بدون المنتور آزموده شد | NOT EXECUTED |
| Elementor Pro (Theme Builder) | اختیاری | اجرا نشد | NOT EXECUTED |
| Elementor 4 (Atomic) | — | اجرا نشد | UNKNOWN |
| WooCommerce | اختیاری | 11.1.2 با HPOS و بلاک‌های سبد/تسویه – ۲۵ سناریو تا ثبت سفارش | PASS |
| افزونه‌های سئو (Yoast/Rank Math/…) | اختیاری | فقط بررسی کد (تشخیص و واگذاری خروجی) | NOT EXECUTED |
| مرورگرها | — | Chromium (Playwright) | PASS (Chromium) / NOT EXECUTED (Safari, Firefox) |
| پایگاه‌داده | — | SQLite (افزونه رسمی) – MySQL/MariaDB اجرا نشد | UNKNOWN (MySQL) |
| Multisite | — | اجرا نشد | NOT EXECUTED |

> مهم: چون المنتور واقعی در محیط ساخت در دسترس نبود، قبل از انتشار روی سایت اصلی، قالب را روی یک **سایت آزمایشی (staging)** با آخرین Elementor (و در صورت استفاده Elementor Pro) نصب کنید و ویرایشگر را باز کنید. خطای مهلک گزارش‌شده توسط شما (`Widgets_Manager::register(): Argument #1 … LS_Widget_Header given`) با شبیه‌ساز سخت‌گیر بازتولید و رفع شد (نسخه قبلی: Fatal، نسخه جدید: HTTP 200).

## نکات Elementor 4 (Atomic Editor)

طبق اعلام رسمی المنتور، ویجت‌های کلاسیک (`\Elementor\Widget_Base`) در نسخه 4 در کنار عناصر Atomic پشتیبانی می‌شوند. کلاس‌ها و Variables مختص عناصر Atomic هستند؛ رنگ‌های سراسری قالب (LS – …) در Global Colors ثبت شده‌اند و از طریق همگام‌سازی Variables با Global Colors در نسخه 4 قابل استفاده‌اند.

## API‌های استفاده‌شده (فقط عمومی و مستند)

- ثبت ویجت: `elementor/widgets/register` + `Widget_Base` · دسته: `elementor/elements/categories_registered` · آیکون: `elementor/icons_manager/additional_tabs` · تگ داینامیک: `elementor/dynamic_tags/register` · تم‌بیلدر پرو: `elementor/theme/register_locations` با `register_all_core_location()` و `elementor_theme_do_location()` (با تشخیص وجود).
- رفع اشکال مهم در این نسخه: المنتور رویداد `elementor/loaded` را هنگام بارگذاری افزونه‌ها (قبل از پوسته) اجرا می‌کند؛ قالب اکنون با `did_action()` این حالت را تشخیص می‌دهد.
- وردپرس: Settings/Options (theme_mods)، Customizer، `after_switch_theme`، WP-Cron، `admin-post.php`، Media sideload، Comments API، theme.json نسخه 2.

## امنیت

- ذخیره تنظیمات: `edit_theme_options` + nonce؛ راه‌اندازی/درون‌ریزی: `manage_options` + nonce؛ متاباکس‌ها: `edit_post` + nonce.
- همه ورودی‌ها بر اساس نوع پاکسازی (`sanitize_hex_color`, `esc_url_raw`, `sanitize_email`, `wp_kses_post` …) و خروجی‌ها escape می‌شوند.
- فرم‌های عمومی (AJAX): honeypot + حداقل زمان پر کردن + محدودیت نرخ IP (بدون nonce عمدی به خاطر کش صفحه)؛ آپلود فقط با MIMEهای مجاز و `wp_handle_upload`.
- محتوای نمونه مقاله (شامل SVG نمودار) فقط در هنگام درون‌ریزی توسط مدیر با غیرفعال‌سازی موقت kses ذخیره می‌شود.

## رفتار بدون افزونه‌ها

- **بدون Elementor:** برگه‌های ساخته‌شده توسط رندر جایگزین قالب (`inc/elementor/fallback.php`) با همان طرح نمایش داده می‌شوند؛ هدر، فوتر، وبلاگ، نمونه‌کار و ۴۰۴ تمپلیت PHP دارند. ویرایش بصری نیازمند Elementor است.
- **بدون Elementor Pro:** تب «تم‌بیلدر» در تنظیمات قالب، قالب‌های ذخیره‌شده را به لوکیشن‌ها وصل می‌کند.
- **بدون WooCommerce:** کاتالوگ و فروشگاه با کارت‌های دستی (فیلتر/جستجو/مرتب‌سازی/محدوده قیمت سمت کاربر) کار می‌کنند.

## محدودیت‌های شناخته‌شده

- معماری «پوسته + افزونه همراه» پیشنهادی در راهنما اجرا نشده است؛ چون خواسته شما ساخت خودکار برگه‌ها با نصب **قالب** بود، همه امکانات در یک بسته است. داده‌ها (درخواست‌ها، نمونه‌کارها، تنظیمات) در پایگاه‌داده می‌مانند و با تعویض قالب پاک نمی‌شوند، اما ویجت‌ها و فرم‌ها با غیرفعال کردن قالب از کار می‌افتند.
- فوترهای متفاوت سه صفحه طرح (`_1`، `_7`، `_10`) به یک فوتر واحد سایت تبدیل شده‌اند؛ هدر کلاسیک روشن (`_1`) به صورت تنظیم برگه/سراسری موجود است. منوی اصلی سراسری است (برچسب‌های منو در صفحات طرح کمی متفاوت بودند).
- تصاویر طرح در صورت نبود دسترسی سرور به `lh3.googleusercontent.com` از همان آدرس نمایش داده می‌شوند؛ تصاویر واقعی خود را جایگزین کنید.
- تاریخ‌ها با تبدیل داخلی به شمسی نمایش داده می‌شوند (قابل خاموش کردن در تب وبلاگ).

## منابع رسمی بررسی‌شده

- WordPress 7.0 – حداقل PHP 7.4: https://make.wordpress.org/hosting/handbook/compatibility/version/7-0/
- Elementor 4 و ویجت‌های کلاسیک: https://elementor.com/products/website-builder/v4-faq/ · https://developers.elementor.com/elementor-editor-4-0-developers-update/
- تغییرات Elementor Pro: https://elementor.com/pro/changelog/
