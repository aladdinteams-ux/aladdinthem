# Changelog

## 1.0.0 — 2026-11-01
### Added
- Theme `payab-sanat-asa`: shared chrome from the approved HTML v42 (fa/en/ar), local fonts, bundled CSS split into core / family / final bundles.
- Companion plugin `payab-sanat-asa-core`: multilingual layer, SEO, products (WooCommerce catalog mode), reversible importer, settings, "Payab Element" widget.
- Child theme `payab-sanat-asa-child`.
- Build pipeline (`build/*.py`) converting the source HTML into theme parts and 66 Elementor page trees.
### Notes
- Source inconsistency kept as delivered: the Persian pages link two extra CSS files (v24, v25) that the en/ar pages do not.
- Source pages carried `noindex,nofollow` (design preview); the theme does not output a robots meta of its own.
