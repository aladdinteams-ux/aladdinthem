import requests, re, subprocess, sys, html
B = 'http://localhost:8085'
ck = {'Cookie': subprocess.check_output(['php', 'cookie-elz.php'], stderr=subprocess.DEVNULL).decode().strip().splitlines()[-1]}
def ok(n, c, i=''): print(('PASS ' if c else 'FAIL ') + n, str(i)[:200])
def page(): return requests.get(B + '/wp-admin/admin.php?page=ls-setup', headers=ck).text
p = page()
ok('wizard: requirements table', 'بررسی نیازمندی‌های سرور' in p and 'نسخه PHP' in p)
ok('wizard: plugins table', 'Larijani Stone Core' in p and 'WooCommerce' in p)
ok('wizard: last import card', 'آخرین درون‌ریزی' in p)
m = re.search(r'name="plugin" value="larijani-stone-core">\s*<input type="hidden" id="_wpnonce" name="_wpnonce" value="([^"]+)"', p)
if m:
    ok('core plugin install button', True)
    r = requests.post(B + '/wp-admin/admin-post.php', data={'action': 'larijani_install_plugin', 'plugin': 'larijani-stone-core', '_wpnonce': m.group(1)}, headers=ck, allow_redirects=False)
    p = page()
    ok('core plugin installed & activated from bundled ZIP', 'افزونه Larijani Stone Core نصب و فعال شد' in p, re.findall(r'<div class="notice[^"]*"><p>(.*?)</p>', p)[:2])
else:
    print('SKIP core plugin already active')
# CSRF: install without nonce
r = requests.post(B + '/wp-admin/admin-post.php', data={'action': 'larijani_install_plugin', 'plugin': 'woocommerce'}, headers=ck, allow_redirects=False)
ok('install without nonce refused', r.status_code == 403, r.status_code)
# run import of projects only
n = re.search(r'name="ls_import_nonce" value="([^"]+)"', p).group(1)
requests.post(B + '/wp-admin/admin.php?page=ls-setup', data={'ls_import_nonce': n, 'ls_parts[]': ['projects'], 'ls_do_import': '1'}, headers=ck, allow_redirects=False); r = requests.get(B + '/wp-admin/admin.php?page=ls-setup', headers=ck)
ok('projects import after plugin activation', re.search(r'6 نمونه\S*کار ساخته شد', r.text), re.findall(r'\d+ نمونه\S*کار ساخته شد', r.text))
requests.post(B + '/wp-admin/admin.php?page=ls-setup', data={'ls_import_nonce': n, 'ls_parts[]': ['projects'], 'ls_do_import': '1'}, headers=ck, allow_redirects=False); r2 = requests.get(B + '/wp-admin/admin.php?page=ls-setup', headers=ck)
ok('re-run creates no duplicates', re.search(r'0 نمونه\S*کار ساخته شد', r2.text), re.findall(r'\d+ نمونه\S*کار ساخته شد', r2.text))
if 'undo' in sys.argv:
    p = page()
    u = re.search(r'name="action" value="larijani_import_undo">\s*<input type="hidden" id="_wpnonce" name="_wpnonce" value="([^"]+)"', p)
    ok('undo button present', bool(u))
    requests.post(B + '/wp-admin/admin-post.php', data={'action': 'larijani_import_undo', '_wpnonce': u.group(1)}, headers=ck, allow_redirects=False); r = requests.get(B + '/wp-admin/admin.php?page=ls-setup', headers=ck)
    ok('undo report', 'درون‌ریزی قبلی برگردانده شد' in r.text, re.findall(r'درون‌ریزی قبلی برگردانده شد[^<]*', r.text))
