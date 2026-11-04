=== Payab Sanat Asa ===
Contributors: aladdintheme
Requires at least: 6.4
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.3.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tags: rtl-language-support, translation-ready, custom-colors, custom-logo, featured-images, accessibility-ready, e-commerce, one-column

Three-language (Persian, English, Arabic), RTL-ready theme for Payab Sanat Asa, a patient-transfer equipment maker. Works with the free Elementor plugin; Elementor Pro is not needed and not used.

== Description ==
* Shared chrome (top bar, floating glass header, products mega menu, mobile drawer, footer, mobile quick bar) with every text editable in the "Aladdin Payab" admin menu (companion plugin).
* Day / night mode, three languages (fa, en, ar) with correct direction and fonts.
* Page content is built from free Elementor containers and widgets; without Elementor the saved HTML still renders.
* Requires the companion plugin "Payab Sanat Asa Core" (languages, import, SEO, Elementor widget). Product catalog mode with WooCommerce (optional).

== Installation ==
1. Upload and activate the theme, then the companion plugin (Appearance > Install plugins, or upload payab-sanat-asa-core.zip).
2. the "Aladdin Payab" admin menu > Import / Undo > import pages and products (reversible).
3. Install the free Elementor plugin to edit pages visually (optional).
See docs/INSTALL-FA.md for the Persian guide.

== Frequently Asked Questions ==
= Is Elementor Pro needed? =
No. Only free Elementor is used; the theme never calls Pro code.

= Does activation change my existing site? =
No. Nothing is created or changed until you run the import yourself, and the import can be undone.

== Resources ==
* Vazirmatn font, OFL 1.1, https://github.com/rastikerdar/vazirmatn (assets/fonts/LICENSE-Vazirmatn.txt)
* Estedad font, OFL 1.1, Amin Abedi, https://github.com/aminabedi68/Estedad (assets/fonts/LICENSE-Estedad-OFL.txt)
* Manrope font, OFL 1.1, https://github.com/sharanda/manrope (assets/fonts/LICENSE-Manrope.txt)
* Cairo font, OFL 1.1, https://github.com/Gue3bara/Cairo (assets/fonts/LICENSE-Cairo.txt)
* Bootstrap Icons 1.11.3, MIT, https://icons.getbootstrap.com/ (assets/fonts/LICENSE-BootstrapIcons.txt)
* Images in assets/images are supplied by the client (Payab Sanat Asa). Several are labelled "conceptual image"; the client must confirm their licence/ownership before commercial release.
* Branding logos (companion plugin): Aladdin Theme (علاءالدین تم), author's trademark.

== Changelog ==
= 1.3.1 =
* Fix fatal error when the companion plugin ran while the theme was not active (plugin called theme functions). The plugin is now self-contained and shows a notice asking to activate the theme.

= 1.3.0 =
* Sites that had another theme: import tab lists the old front page and old pages on the same URLs; optional "Persian home on /" takeover and optional recoverable trashing of conflicting old pages (Undo restores both).

= 1.2.2 =
* Fix: header/footer menus could contain every link twice (setup run twice); duplicates are removed automatically, never created again, and never printed.

= 1.2.1 =
* Designed 404 page (fa/en/ar, light/dark, search, buttons, suggested links).

= 1.2.0 =
* Header navigation and both footer link lists are real WordPress menus (3 per language), created with the pages and editable in Appearance > Menus.
* Redesigned settings screen (overview, menus, texts, general, import).
* Dashboard keeps its own language/direction (theme no longer forces lang/dir in wp-admin).

= 1.1.0 =
* Each language has its own page post type (fa / en / ar); legacy 1.0.x pages are migrated automatically.
* Elementor base CSS is layered below the design CSS so Elementor resets cannot disturb the layout.
* Untranslated Persian leftovers in the English/Arabic pages fixed; Languages-and-pages status table with a "create missing" button.
* Automatic companion install and fresh-site import on theme activation; top-level admin menu.

= 1.0.0 =
* First release, converted from the approved HTML design v42.

== Copyright ==
Copyright (C) 2026 Aladdin Theme (علاءالدین تم).
Theme and plugin code are licensed GPLv2 or later. Third-party resources keep their own licences listed above.
