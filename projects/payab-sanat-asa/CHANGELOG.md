# Changelog

## 1.1.1 — 2026-11-02
- Setting "Page output" (Settings > General): `html` (default) prints the approved design markup exactly, `elementor` prints Elementor output. Fixes remaining layout differences reported on a live site while Elementor output could not be tested with real Elementor.

## 1.1.0 — 2026-11-02
### Changed
- Pages now live in one post type per language (`aladdin_page_fa`, `aladdin_page_en`, `aladdin_page_ar`); URLs unchanged. Pages imported by 1.0.x are migrated automatically (structural parent pages are trashed, the Persian home takes "/" again).
- Elementor's base stylesheet is loaded in a cascade layer below the design CSS (fixes broken UI risk from `.elementor img/a` resets); widget wrappers do not add boxes on the public site.
### Fixed
- Persian text left in English/Arabic pages ("Quick answer: …", image alt suffix, Arabic address letters, mobile drawer label).
### Added
- "Languages and pages" matrix in the admin menu with a button that creates only the missing pages; auto setup on theme activation; top-level admin menu.

## 1.0.0 — 2026-11-01
### Added
- Theme `payab-sanat-asa`: shared chrome from the approved HTML v42 (fa/en/ar), local fonts, bundled CSS split into core / family / final bundles.
- Companion plugin `payab-sanat-asa-core`: multilingual layer, SEO, products (WooCommerce catalog mode), reversible importer, settings, "Payab Element" widget.
- Child theme `payab-sanat-asa-child`.
- Build pipeline (`build/*.py`) converting the source HTML into theme parts and 66 Elementor page trees.
### Notes
- Source inconsistency kept as delivered: the Persian pages link two extra CSS files (v24, v25) that the en/ar pages do not.
- Source pages carried `noindex,nofollow` (design preview); the theme does not output a robots meta of its own.
