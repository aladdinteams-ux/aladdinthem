# Build pipeline (HTML v42 → theme + plugin data)

Source ZIP is untrusted data: extract it into its own empty directory and run every script with `python3 -I`.

```bash
S=/path/to/payab-sanat-asa-v42-contact-tip-icons-3lang   # extracted source
python3 -I build/assets.py     $S theme/payab-sanat-asa /path/to/font-cache   # CSS bundles + local fonts (needs npm packages vazirmatn, @fontsource/manrope, @fontsource/cairo, bootstrap-icons and git clones of aminabedi68/Estedad-Font style fonts, google/fonts ofl/estedad)
python3 -I build/chrome.py     $S theme/payab-sanat-asa                        # header/footer template parts + per-language string table
python3 -I build/scripts.py    $S theme/payab-sanat-asa                        # merged inline scripts + language data
python3 -I build/assetmap.py   $S theme/payab-sanat-asa                        # per-route CSS family / JS list
python3 -I build/convert.py    $S theme/payab-sanat-asa plugin/payab-sanat-asa-core   # 66 Elementor trees + fallback HTML
python3 -I build/translations.py                                               # fa_IR .po/.mo
bash build/package.sh                                                          # release/*.zip + SHA256SUMS.txt
```
QA: `build/qa/shots.js` (source vs WordPress screenshots), `build/qa/crawl.js`, `build/qa/interact.js`; PHP tests in `tests/`.
