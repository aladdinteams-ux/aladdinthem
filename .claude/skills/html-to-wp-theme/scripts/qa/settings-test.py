import json, subprocess, requests, re, sys
B = 'http://localhost:8083'
d = json.loads(subprocess.check_output(['php', 'qa/forms/roles.php', 'wpel'], stderr=subprocess.DEVNULL).decode().strip().splitlines()[-1])
ck = {'Cookie': d['users']['administrator']['cookie']}
def ok(n, c, i=''): print(('PASS ' if c else 'FAIL ') + n, str(i)[:160])
pg = requests.get(B + '/wp-admin/admin.php?page=ls-settings&tab=ls_style', headers=ck).text
ok('style tab renders', 'ls[ls_font_family]' in pg and 'ls[ls_container_width]' in pg)
nonce = re.search(r'name="ls_settings_nonce" value="([^"]+)"', pg).group(1)
data = {'action': 'ls_save_settings', 'ls_tab': 'ls_style', 'ls_settings_nonce': nonce, 'ls[ls_font_family]': 'system', 'ls[ls_font_custom_url]': '', 'ls[ls_font_scale_heading]': '110', 'ls[ls_font_scale_body]': '100', 'ls[ls_font_scale_tablet]': '100', 'ls[ls_font_scale_mobile]': '90', 'ls[ls_container_width]': '1140', 'ls[ls_button_radius]': '9999'}
r = requests.post(B + '/wp-admin/admin-post.php', data=data, headers=ck, allow_redirects=False); ok('save', r.status_code == 302, r.headers.get('location'))
h = requests.get(B + '/').text
css = re.search(r"<style id=.larijani-tailwind-inline-css.>(.*?)</style>", h, re.S); css = css.group(1) if css else ''
ok('font var printed', "--ls-font:Tahoma" in css, css[:300])
ok('heading scale', '--ls-fs-h:1.1' in css); ok('container', '--ls-container:1140px' in css); ok('mobile scale', '--ls-fs-r:0.9' in css); ok('button radius', 'border-radius:9999px' in css)
ok('vazirmatn not preloaded with system font', 'Vazirmatn-Variable.woff2" as="font"' not in h)
bad = dict(data); bad['ls[ls_container_width]'] = '99999'; bad['ls[ls_font_custom_url]'] = "https://evil.example/x.woff2');}body{display:none"; bad['ls[ls_font_family]'] = 'custom'
pg = requests.get(B + '/wp-admin/admin.php?page=ls-settings&tab=ls_style', headers=ck).text
bad['ls_settings_nonce'] = re.search(r'name="ls_settings_nonce" value="([^"]+)"', pg).group(1)
requests.post(B + '/wp-admin/admin-post.php', data=bad, headers=ck, allow_redirects=False)
h = requests.get(B + '/').text
css = re.search(r"<style id=.larijani-tailwind-inline-css.>(.*?)</style>", h, re.S); css = css.group(1) if css else ''
ok('invalid width falls back to default', '99999' not in css)
ok('foreign/injected font URL ignored', 'evil' not in css and 'display:none' not in css, css[:200])
# editor role cannot save theme settings
r = requests.post(B + '/wp-admin/admin-post.php', data=data, headers={'Cookie': d['users']['editor']['cookie']}, allow_redirects=False)
ok('editor cannot save settings', r.status_code == 403, r.status_code)
if sys.argv[-1] == 'keep': sys.exit()
ov = requests.get(B + '/wp-admin/admin.php?page=ls-settings', headers=ck).text
n = re.search(r'name="action" value="ls_reset_all">\s*<input type="hidden" id="_wpnonce" name="_wpnonce" value="([^"]+)"', ov).group(1)
requests.post(B + '/wp-admin/admin-post.php', data={'action': 'ls_reset_all', '_wpnonce': n}, headers=ck, allow_redirects=False)
h = requests.get(B + '/').text
css = re.search(r"<style id=.larijani-tailwind-inline-css.>(.*?)</style>", h, re.S); css = css.group(1) if css else ''
ok('reset all -> no custom vars', '--ls-font' not in css and '--ls-container' not in css, css[:200])
ok('reset all -> brand defaults kept', 'Vazirmatn-Variable.woff2" as="font"' in h)
ov = requests.get(B + '/wp-admin/admin.php?page=ls-settings', headers=ck).text
m = re.search(r'name="action" value="ls_restore_settings">\s*<input type="hidden" id="_wpnonce" name="_wpnonce" value="([^"]+)"', ov)
ok('restore button shown', bool(m))
requests.post(B + '/wp-admin/admin-post.php', data={'action': 'ls_restore_settings', '_wpnonce': m.group(1)}, headers=ck, allow_redirects=False)
h = requests.get(B + '/').text
css = re.search(r"<style id=.larijani-tailwind-inline-css.>(.*?)</style>", h, re.S); css = css.group(1) if css else ''
ok('restore brings settings back', '--ls-fs-h:1.1' in css and '--ls-fs-r:0.9' in css, css[:120])
