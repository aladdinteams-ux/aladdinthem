#!/usr/bin/env python3
"""Write release/release-manifest.json from release/SHA256SUMS.txt. Usage: python3 -I build/manifest.py"""
import json, os, re, datetime
base = os.path.join(os.path.dirname(__file__), '..', 'release')
arts = []
for line in open(os.path.join(base, 'SHA256SUMS.txt')):
    m = re.match(r'(\w{64})\s+(\S+)\s+(\d+) files', line.strip())
    if m: arts.append({'file': m.group(2), 'sha256': m.group(1), 'files': int(m.group(3))})
man = {
 'name': 'Payab Sanat Asa', 'version': '1.1.0', 'date': datetime.date.today().isoformat(),
 'author': 'علاءالدین تم (Aladdin Theme)',
 'source': {'zip': 'payab-sanat-asa-v42-contact-tip-icons-3lang', 'sha256': '303e33c343cd4696e76dc676a186979f2775ae5dff93e6fac622e6755cce4766'},
 'requirements': {'wordpress': '6.4+', 'php': '7.4+', 'elementor': 'free (optional, not tested in this build)', 'woocommerce': 'optional (tested 10.3.5)'},
 'tested': {'wordpress': '6.8.3', 'php': '8.3.6', 'woocommerce': '10.3.5', 'elementor': None},
 'artifacts': arts,
 'not_executed': ['Elementor editor load/edit/save/Style-tab and editor-vs-site parity (blocked: Elementor could not be built in the sandbox)',
                  'PHPCS WordPress security sniffs and PHPCompatibility (blocked: composer/external code not permitted)',
                  'Plugin Check (blocked: needs WP-CLI + composer dependencies)',
                  'PHP 7.4 runtime, MySQL/MariaDB, Nginx/LiteSpeed, real page cache, Yoast/Rank Math coexistence',
                  'Lighthouse/PageSpeed, axe/screen reader, real devices, live site install'],
 'licences': {'fonts': 'OFL 1.1 (Vazirmatn, Estedad, Manrope, Cairo); Bootstrap Icons MIT', 'code': 'GPL-2.0-or-later',
              'images': 'supplied by client; licence/ownership of "conceptual" images not confirmed'},
 'release_ready': False,
 'blockers': ['Client must confirm licence/ownership of the product and hero images', 'Run the Elementor editor test on a real site before sale'],
}
json.dump(man, open(os.path.join(base, 'release-manifest.json'), 'w', encoding='utf8'), ensure_ascii=False, indent=1)
print('manifest ok', len(arts))
