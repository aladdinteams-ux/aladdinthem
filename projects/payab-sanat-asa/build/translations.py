#!/usr/bin/env python3
"""Build fa_IR .po/.mo for the theme and the plugin from the POTs. Usage: python3 -I build/translations.py"""
import re, struct, os
FA = {
"Search results for: %s": "نتایج جست‌وجو برای: %s",
"Nothing found. Try different words.": "چیزی پیدا نشد. با کلمات دیگری جست‌وجو کنید.",
"Nothing found.": "چیزی پیدا نشد.",
"Page not found": "صفحه پیدا نشد",
"The page you are looking for does not exist or has moved.": "صفحه‌ای که دنبال آن هستید وجود ندارد یا جابه‌جا شده است.",
"Back to home": "بازگشت به صفحه اصلی",
"Payab setup": "راه‌اندازی پایاب", "Payab Sanat Asa setup": "راه‌اندازی پایاب صنعت آسا",
"The bundled plugin file was not found. Upload payab-sanat-asa-core.zip from Plugins > Add New.": "فایل افزونه همراه پیدا نشد. فایل payab-sanat-asa-core.zip را از افزونه‌ها ← افزودن بارگذاری کنید.",
"The companion plugin adds the settings page, languages and the page import. Click the button to install and activate it; on a fresh site the pages are created automatically.": "افزونه همراه صفحه تنظیمات، زبان‌ها و ایمپورت برگه‌ها را اضافه می‌کند. دکمه را بزنید تا نصب و فعال شود؛ روی سایت تازه برگه‌ها خودکار ساخته می‌شوند.",
"Languages and pages": "زبان‌ها و صفحه‌ها", "Route": "مسیر", "Total": "جمع", "missing": "ساخته نشده",
"Create the %d missing pages": "ساخت %d صفحهٔ ساخته‌نشده",
"Page output": "خروجی صفحه",
"Exact design (recommended): the site shows the approved design markup. Elementor edits do not appear on the site.": "طرح دقیق (پیشنهادی): سایت همان کد طرح تأییدشده را نشان می‌دهد. ویرایش‌های المنتور روی سایت دیده نمی‌شود.",
"Elementor output: edits made in Elementor appear on the site (requires Elementor; check the look after switching).": "خروجی المنتور: ویرایش‌های المنتور روی سایت دیده می‌شود (نیاز به المنتور؛ بعد از تغییر ظاهر را بررسی کنید).",
"Menus": "منوها", "Settings sections": "بخش‌های تنظیمات", "Payab Sanat Asa": "پایاب صنعت آسا",
"Theme settings · three languages · menus · import": "تنظیمات قالب · سه زبان · منوها · ایمپورت", "View site": "مشاهده سایت",
"Manage pages": "مدیریت صفحه‌ها", "Menus ready: %d of 9": "منوهای آماده: %d از ۹", "Imported items: %d": "موارد واردشده: %d",
"Without Elementor the site still shows the exact design; install the free Elementor plugin to edit pages visually.": "بدون المنتور سایت همچنان طرح دقیق را نشان می‌دهد؛ برای ویرایش بصری، افزونه رایگان المنتور را نصب کنید.",
"Quick actions": "دسترسی سریع", "Edit header & footer texts": "ویرایش متن‌های هدر و فوتر", "Edit menus (Appearance)": "ویرایش منوها (نمایش‌ها)", "General settings": "تنظیمات عمومی",
"Every route in every language, with a link to edit it.": "هر مسیر در هر سه زبان، با لینک ویرایش.",
"Header and footer links": "لینک‌های هدر و فوتر",
"Created automatically with the pages. They are real WordPress menus: edit, reorder or add links in Appearance > Menus; the site prints them in the header and footer.": "همراه صفحه‌ها خودکار ساخته می‌شوند. منوهای واقعی وردپرس‌اند: در نمایش‌ها ← فهرست‌ها لینک‌ها را ویرایش، جابه‌جا یا اضافه کنید؛ سایت آن‌ها را در هدر و فوتر چاپ می‌کند.",
"Language": "زبان", "Menu": "منو", "Used in": "محل استفاده", "Links": "لینک‌ها", "Edit": "ویرایش",
"Header navigation": "ناوبری هدر", "Footer: quick links": "فوتر: دسترسی سریع", "Footer: useful information": "فوتر: اطلاعات مفید",
"Open Appearance > Menus": "باز کردن نمایش‌ها ← فهرست‌ها", "Create the missing menus": "ساخت منوهای ساخته‌نشده", "Check menus": "بررسی منوها",
"A location that already has links is never changed. The \"Products\" link of the header menu carries the class payab-mega (it opens the products mega menu).": "جایگاهی که از قبل لینک دارد هرگز تغییر نمی‌کند. لینک «محصولات» منوی هدر کلاس payab-mega دارد (مگامنوی محصولات را باز می‌کند).",
"Menus checked: %d created.": "منوها بررسی شد: %d منو ساخته شد.",
"How the public site prints the design pages.": "سایت صفحه‌های طرح را چگونه چاپ می‌کند.",
"Exact design": "طرح دقیق", "Recommended. The site shows the approved design markup. Elementor edits do not appear on the site.": "پیشنهادی. سایت همان کد طرح تأییدشده را نشان می‌دهد. ویرایش‌های المنتور روی سایت دیده نمی‌شود.",
"Elementor output": "خروجی المنتور", "Edits made in Elementor appear on the site (requires Elementor; check the look after switching).": "ویرایش‌های المنتور روی سایت دیده می‌شود (نیاز به المنتور؛ پس از تغییر ظاهر را بررسی کنید).",
"Shop and SEO": "فروشگاه و سئو", "Elementor templates for header and footer": "قالب‌های المنتور برای هدر و فوتر", "Delete settings": "حذف تنظیمات", "Save changes": "ذخیره تغییرات",
"Filter texts…": "فیلتر متن‌ها…", "Import pages and products": "ایمپورت صفحه‌ها و محصولات", "Confirm": "تأیید", "Undo": "بازگردانی",
"Header menu — %s": "منوی هدر — %s",
"Every text, label and link of the top bar, header, mobile menu, footer and mobile bar. Leave a field empty to keep the approved design text. Links: use \"fa:shop-2\" style route keys or a full URL. The header and footer link lists come from the Menus tab.": "همه متن‌ها، برچسب‌ها و لینک‌های نوار بالا، هدر، منوی موبایل، فوتر و نوار موبایل. برای نگه داشتن متن طرح تأییدشده فیلد را خالی بگذارید. لینک‌ها: کلید مسیر مانند «fa:shop-2» یا نشانی کامل. فهرست لینک‌های هدر و فوتر از تب «منوها» می‌آید.",
"This site already has content from another theme": "این سایت از قبل محتوای قالب دیگری دارد",
"Current static front page: \"%s\". It stays untouched unless you tick the box below.": "صفحهٔ نخست ایستای فعلی: «%s». اگر کادر زیر را علامت نزنید دست‌نخورده می‌ماند.",
"These old pages use the same address as a design page. The design page is shown on that address; the old page is not deleted.": "این صفحه‌های قدیمی همان آدرس یک صفحهٔ طرح را دارند. روی آن آدرس صفحهٔ طرح نمایش داده می‌شود؛ صفحهٔ قدیمی حذف نمی‌شود.",
"Use the Persian design home as the front page (/)": "خانهٔ فارسی طرح، صفحهٔ نخست (/) باشد",
"Your previous front page setting is saved and restored by Undo.": "تنظیم قبلی صفحهٔ نخست ذخیره می‌شود و با «بازگردانی» برمی‌گردد.",
"Move the old pages listed above to the Trash": "صفحه‌های قدیمیِ بالا به زباله‌دان بروند",
"Optional and recoverable: Undo takes them out of the Trash again.": "اختیاری و قابل بازیابی: «بازگردانی» آن‌ها را از زباله‌دان بیرون می‌آورد.",
"Front page": "صفحهٔ نخست", "Persian home on the site root (/)": "خانهٔ فارسی روی ریشهٔ سایت (/)",
"The Persian home page of the design answers on /, even if another static front page was set before. Off: it lives at /payab-home/ and your own front page stays.": "خانهٔ فارسی طرح روی / پاسخ می‌دهد، حتی اگر صفحهٔ نخست ایستای دیگری تنظیم شده بود. خاموش: روی /payab-home/ می‌ماند و صفحهٔ نخست خودتان دست‌نخورده است.",
"Search": "جست‌وجو", "Search…": "جست‌وجو…",
"Primary (optional; header uses the Texts panel by default)": "منوی اصلی (اختیاری؛ هدر به‌صورت پیش‌فرض از پنل متن‌ها استفاده می‌کند)",
"Footer (optional)": "فوتر (اختیاری)",
"Payab Sanat Asa theme:": "قالب پایاب صنعت آسا:",
"the companion plugin is required for languages, import, SEO and the Elementor widget.": "افزونه همراه برای زبان‌ها، ایمپورت، سئو و ویجت المنتور لازم است.",
"Activate the companion plugin": "فعال‌سازی افزونه همراه",
"Install and activate the companion plugin": "نصب و فعال‌سازی افزونه همراه",
"Not allowed.": "دسترسی مجاز نیست.",
"Installing the bundled plugin failed. Upload payab-sanat-asa-core.zip manually.": "نصب افزونه همراه ناموفق بود. فایل payab-sanat-asa-core.zip را دستی بارگذاری کنید.",
"Language": "زبان", "Aladdin Payab": "علاءالدین پایاب",
"Designed and developed by": "طراحی و توسعه",
"Overview": "نمای کلی", "General": "عمومی", "Header & footer texts": "متن‌های هدر و فوتر", "Import / Undo": "ایمپورت / بازگردانی", "Reserved": "رزرو‌شده",
"Aladdin Payab — Payab Sanat Asa theme": "علاءالدین پایاب — قالب پایاب صنعت آسا",
"Settings saved.": "تنظیمات ذخیره شد.",
"Texts of this language were reset to the approved design (a backup was kept).": "متن‌های این زبان به طرح تأییدشده بازگردانده شد (نسخه پشتیبان نگه داشته شد).",
"Please tick the confirmation box first.": "ابتدا کادر تأیید را علامت بزنید.",
"Import finished: %1$d created, %2$d already existed.": "ایمپورت پایان یافت: %1$d مورد ساخته شد، %2$d مورد از قبل وجود داشت.",
"Import finished.": "ایمپورت پایان یافت.",
"Undo finished: %d items moved to the Trash.": "بازگردانی پایان یافت: %d مورد به زباله‌دان منتقل شد.",
"Status": "وضعیت", "Theme \"Payab Sanat Asa\" active": "قالب «پایاب صنعت آسا» فعال است",
"Elementor (free) active": "المنتور (رایگان) فعال است",
"pages still show using the saved HTML": "صفحات همچنان با HTML ذخیره‌شده نمایش داده می‌شوند",
"WooCommerce active (products)": "ووکامرس فعال است (محصولات)",
"product pages are created as pages": "صفحات محصول به‌صورت برگه ساخته می‌شوند",
"Imported items": "موارد واردشده",
"Theme and companion plugin for Payab Sanat Asa. Version": "قالب و افزونه همراه پایاب صنعت آسا. نسخه",
"Catalog mode": "حالت کاتالوگ",
"Hide prices and block purchasing/cart/checkout (WooCommerce).": "قیمت‌ها را پنهان و خرید، سبد و تسویه‌حساب را مسدود می‌کند (ووکامرس).",
"SEO tags": "برچسب‌های سئو",
"Print description, canonical, hreflang, Open Graph and Schema.org.": "توضیحات، canonical، hreflang، Open Graph و Schema.org را چاپ می‌کند.",
"Automatically off when Yoast, Rank Math, SEOPress/TSF or AIOSEO is active.": "با فعال بودن Yoast، Rank Math، SEOPress/TSF یا AIOSEO به‌طور خودکار خاموش می‌شود.",
"Header": "هدر", "Footer": "فوتر", "Approved design (default)": "طرح تأییدشده (پیش‌فرض)",
"Optional: replace the shared chrome with an Elementor template (built with free Elementor, no Pro theme builder).": "اختیاری: هدر/فوتر مشترک را با یک قالب المنتور جایگزین کنید (با المنتور رایگان، بدون Theme Builder پرو).",
"On uninstall": "هنگام حذف افزونه",
"Delete this plugin's settings (pages and products are never deleted).": "تنظیمات این افزونه حذف شود (برگه‌ها و محصولات هرگز حذف نمی‌شوند).",
"Every text, label and link of the top bar, header, mobile menu, footer and mobile bar. Leave a field empty to keep the approved design text. Links: use \"fa:shop-2\" style route keys or a full URL.": "همه متن‌ها، برچسب‌ها و لینک‌های نوار بالا، هدر، منوی موبایل، فوتر و نوار موبایل. برای نگه داشتن متن طرح تأییدشده، فیلد را خالی بگذارید. لینک‌ها: کلید مسیر مانند «fa:shop-2» یا نشانی کامل.",
"Top bar, header and mobile drawer": "نوار بالا، هدر و منوی موبایل", "Footer and mobile bar": "فوتر و نوار موبایل",
"Type": "نوع", "Approved text": "متن تأییدشده", "Your text": "متن شما",
"Products mega menu (category panel)": "مگامنوی محصولات (پنل دسته‌بندی)",
"Save texts": "ذخیره متن‌ها",
"Reset all texts of this language to the approved design? A backup is kept.": "همه متن‌های این زبان به طرح تأییدشده بازگردانده شود؟ نسخه پشتیبان نگه داشته می‌شود.",
"Reset this language to the approved design": "بازگردانی این زبان به طرح تأییدشده",
"Creates the 66 language pages (22 routes × 3 languages, a separate post type for each language) as Elementor pages with a plain-HTML fallback; products go to WooCommerce when it is active. Only missing pages are created. Existing pages are never overwritten; every created item is journaled and can be moved to the Trash with Undo. The Persian home takes the front page on a fresh site.": "۶۶ صفحه (۲۲ مسیر × ۳ زبان؛ برای هر زبان یک پست‌تایپ جدا) را به‌صورت صفحه المنتور با HTML جایگزین ساده می‌سازد؛ محصولات با ووکامرس فعال به ووکامرس می‌روند. فقط موارد ساخته‌نشده ساخته می‌شوند. صفحه‌های موجود هرگز بازنویسی نمی‌شوند؛ هر مورد ثبت می‌شود و با «بازگردانی» به زباله‌دان می‌رود. روی سایت تازه، صفحه اصلی فارسی صفحه نخست می‌شود.",
"I understand this creates pages and may set the front page.": "می‌دانم که این کار برگه می‌سازد و ممکن است صفحه نخست را تنظیم کند.",
"Import pages and products": "ایمپورت برگه‌ها و محصولات",
"Last import: %1$d items on %2$s.": "آخرین ایمپورت: %1$d مورد در %2$s.",
"Move all imported items to the Trash and restore changed options?": "همه موارد واردشده به زباله‌دان منتقل و تنظیمات تغییرکرده بازگردانده شود؟",
"Undo the import (move to Trash)": "بازگردانی ایمپورت (انتقال به زباله‌دان)",
"Shown for transparency: these options are planned but not active in this version.": "برای شفافیت نمایش داده می‌شود: این گزینه‌ها برنامه‌ریزی شده‌اند اما در این نسخه فعال نیستند.",
"Colour and typography panel (the approved design uses fixed CSS variables)": "پنل رنگ و تایپوگرافی (طرح تأییدشده از متغیرهای ثابت CSS استفاده می‌کند)",
"Server-side consultation request storage and e-mail (the approved contact form only prepares text and copies it to the clipboard)": "ذخیره درخواست مشاوره در سرور و ارسال ایمیل (فرم تأییدشده فقط متن را آماده و در کلیپ‌بورد کپی می‌کند)",
"Third-party multilingual plugins (Polylang/WPML) compatibility": "سازگاری با افزونه‌های چندزبانه (Polylang/WPML)",
"Payab Sanat Asa": "پایاب صنعت آسا",
"Payab Sanat Asa: install and activate the free Elementor plugin to edit pages visually. Without it the site still shows the saved page HTML.": "پایاب صنعت آسا: برای ویرایش بصری صفحات، افزونه رایگان المنتور را نصب و فعال کنید. بدون آن، سایت همچنان HTML ذخیره‌شده صفحه را نشان می‌دهد.",
"Payab attributes": "ویژگی‌های پایاب", "HTML attributes": "ویژگی‌های HTML",
"One per line: key|value (aria-*, data-*, role, title…). Needed for the approved design scripts.": "هر خط یک مورد: کلید|مقدار (aria-*، data-*، role، title…). برای اسکریپت‌های طرح تأییدشده لازم است.",
"Payab Element": "المان پایاب", "Payab Text": "متن پایاب", "Content": "محتوا", "HTML tag": "تگ HTML", "Text": "متن", "Link": "لینک",
"URL, #anchor, tel:… or a route token such as {{route:fa:shop-2}}.": "نشانی، #لنگر، tel:… یا توکن مسیر مانند {{route:fa:shop-2}}.",
"Image": "تصویر", "Image URL (when no media chosen)": "نشانی تصویر (اگر رسانه‌ای انتخاب نشده)", "Alt text": "متن جایگزین (alt)",
"Inner markup (icons, nested elements)": "کد داخلی (آیکون‌ها، عناصر تو در تو)",
"Used instead of Text when filled. Scripts and unsafe attributes are removed.": "در صورت پر بودن به‌جای «متن» استفاده می‌شود. اسکریپت و ویژگی‌های ناامن حذف می‌شوند.",
"Class & attributes": "کلاس و ویژگی‌ها", "CSS classes of the element": "کلاس‌های CSS عنصر", "Element ID": "شناسه عنصر",
"Attributes (key|value per line)": "ویژگی‌ها (کلید|مقدار در هر خط)",
}
def parse(pot):
    s = open(pot, encoding='utf8').read()
    return re.findall(r'msgid "((?:[^"\\]|\\.)*)"\n', s)
def unesc(x): return x.replace('\\"', '"').replace('\\n', '\n').replace('\\\\', '\\')
def esc(x): return x.replace('\\', '\\\\').replace('"', '\\"').replace('\n', '\\n')
def mo(entries, path):
    keys = sorted(entries); ids = b''; strs = b''; offs = []
    for k in keys:
        kb = k.encode('utf8'); vb = entries[k].encode('utf8')
        offs.append((len(ids), len(kb), len(strs), len(vb))); ids += kb + b'\0'; strs += vb + b'\0'
    n = len(keys); ko = 7 * 4 + 16 * n; vo = ko + len(ids)
    out = struct.pack('Iiiiiii', 0x950412de, 0, n, 28, 28 + 8 * n, 0, 0)
    out += b''.join(struct.pack('ii', l, ko + o) for o, l, _, _ in offs)
    out += b''.join(struct.pack('ii', l, vo + o) for _, _, o, l in offs)
    open(path, 'wb').write(out + ids + strs)
base = os.path.join(os.path.dirname(__file__), '..')
missing = []
for pot, dom, d in (('theme/payab-sanat-asa/languages/payab-sanat-asa.pot', 'payab-sanat-asa', 'theme/payab-sanat-asa/languages'),
                    ('plugin/payab-sanat-asa-core/languages/payab-sanat-asa-core.pot', 'payab-sanat-asa-core', 'plugin/payab-sanat-asa-core/languages')):
    ids = [unesc(i) for i in parse(os.path.join(base, pot)) if i]
    ent = {}; po = ['# Persian (fa_IR) — Aladdin Theme', 'msgid ""', 'msgstr ""', '"Project-Id-Version: %s 1.0.0\\n"' % dom, '"Language: fa_IR\\n"', '"MIME-Version: 1.0\\n"', '"Content-Type: text/plain; charset=UTF-8\\n"', '"Plural-Forms: nplurals=2; plural=(n > 1);\\n"', '']
    for i in ids:
        if i in FA: ent[i] = FA[i]; po += ['msgid "%s"' % esc(i), 'msgstr "%s"' % esc(FA[i]), '']
        else: missing.append(i)
    fn = 'fa_IR' if dom == 'payab-sanat-asa' else dom + '-fa_IR'   # themes load {locale}.mo, plugins {domain}-{locale}.mo
    open(os.path.join(base, d, fn + '.po'), 'w', encoding='utf8').write('\n'.join(po))
    mo({'': 'Project-Id-Version: %s\nLanguage: fa_IR\nMIME-Version: 1.0\nContent-Type: text/plain; charset=UTF-8\nPlural-Forms: nplurals=2; plural=(n > 1);\n' % dom, **ent}, os.path.join(base, d, fn + '.mo'))
    print(dom, len(ent), 'translated of', len(ids))
print('missing:', missing)
