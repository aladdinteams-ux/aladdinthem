# Checkpoint — Payab Sanat Asa theme (Aladdin Theme)

Updated: 2026-11-01 · Mode: Build · Branch: claude/charming-mendel-99q4b1

## Source
- ZIP `payab-sanat-asa-v42-contact-tip-icons-3lang` SHA-256 `303e33c343cd4696e76dc676a186979f2775ae5dff93e6fac622e6755cce4766`
- 148 files, per-file checksums in `docs/source-inventory.sha256`
- 66 static HTML pages = 22 routes x 3 languages (fa at root, `en/`, `ar/`), ~40 layered CSS files, 8 JS files, 4 inline scripts, 17 images
- External dependencies: Google Fonts (Vazirmatn, Estedad, Manrope, Cairo), Bootstrap Icons CDN, neshan.org map link, instagram link
- Source pages carry `noindex,nofollow` and a "design preview" footer note (not a published site)

## Decisions (user, 2026-11-01)
- Multilingual: simple built-in multilingual in the companion plugin (no third-party plugin)
- Products: WooCommerce (catalog mode, no price/cart shown)
- Scope: full build (theme + plugin + child + tests + guide)
- Elementor Pro: not used
- Names: theme `payab-sanat-asa`, plugin `payab-sanat-asa-core`, PHP ns `Aladdin\PayabSanatAsa`, prefix `aladdin_payab_sanat_asa_`, CSS scope `aladdin-payab-sanat-asa`
- Fonts: Vazirmatn + Estedad (fa), Manrope (en), Cairo (ar) bundled locally (OFL); Bootstrap Icons bundled locally (MIT)

## Phases
- [x] P0 intake (inventory, decisions)
- [ ] P1 architecture / assets
- [ ] P2 Elementor content + widgets
- [ ] P3 chrome
- [ ] P4 forms/security
- [ ] P5 import
- [ ] P6 settings
- [ ] P7 quality
- [ ] P8 QA
- [ ] P9 release
