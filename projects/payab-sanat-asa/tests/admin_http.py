"""Admin HTTP checks (sandbox): settings page, nonce enforcement, capability enforcement, texts save + reset, import/undo via the UI.
usage: BASE=http://localhost:8091 ADMIN_COOKIE=... SUB_COOKIE=... python3 -I tests/admin_http.py"""
import os, re, sys, html, urllib.request, urllib.parse, urllib.error, http.cookiejar
B = os.environ['BASE']
class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, *a, **k): return None
op = urllib.request.build_opener(NoRedirect)
def req(path, cookie, data=None):
    r = urllib.request.Request(B + path, data=urllib.parse.urlencode(data).encode() if data else None, headers={'Cookie': cookie})
    try:
        x = op.open(r); return x.status, x.read().decode('utf8', 'replace'), x.headers
    except urllib.error.HTTPError as e:
        return e.code, e.read().decode('utf8', 'replace'), e.headers
P = F = 0
def t(n, c, d=''):
    global P, F
    P += bool(c); F += (not c); print(('PASS ' if c else 'FAIL ') + n + (' — ' + str(d) if d else ''))
A = os.environ['ADMIN_COOKIE']; S = os.environ['SUB_COOKIE']
st, body, _ = req('/wp-admin/options-general.php?page=aladdin-payab&tab=texts&lang=en', A)
t('admin settings (texts tab) renders', st == 200 and 'name="t[pre]' in body, st)
t('admin footer credit shown on own screen', 'Aladdin Theme' in body)
t('branding header is present', 'aladdin-theme' in body or 'علاءالدین تم' in body)
t('hr.wp-header-end present', 'wp-header-end' in body)
nonce = re.search(r'name="_wpnonce" value="([^"]+)"', body).group(1)
# find index of English brand input
m = re.search(r'<code[^>]*>PAYAB SANAT ASA</code></td><td><input[^>]*name="t\[pre\]\[(\d+)\]"', body)
t('texts table lists approved English strings', bool(m))
idx = m.group(1)
st, _, h = req('/wp-admin/admin-post.php', A, {'action': 'aladdin_payab_save_texts', 'lang': 'en', 't[pre][%s]' % idx: 'PAYAB TEST'})
t('save without nonce is rejected', st in (403, 400), st)
st, _, h = req('/wp-admin/admin-post.php', S, {'action': 'aladdin_payab_save_texts', '_wpnonce': nonce, 'lang': 'en', 't[pre][%s]' % idx: 'HACK'})
t('subscriber cannot save settings', st in (403, 302, 400), st)
st, pg, _ = req('/en/', '')
t('rejected saves changed nothing', 'HACK' not in pg and 'PAYAB TEST' not in pg)
st, _, h = req('/wp-admin/admin-post.php', A, {'action': 'aladdin_payab_save_texts', '_wpnonce': nonce, 'lang': 'en', 't[pre][%s]' % idx: '<b>PAYAB NEW</b>'})
t('admin save with nonce redirects back', st == 302 and 'aladdin_msg=saved' in h.get('Location', ''), st)
st, pg, _ = req('/en/', '')
t('new brand text visible on the public English page (escaped)', 'PAYAB NEW' in pg and '<b>PAYAB NEW</b>' not in pg)
st, fa, _ = req('/', '')
t('Persian page unaffected', 'PAYAB NEW' not in fa)
st, body, _ = req('/wp-admin/options-general.php?page=aladdin-payab&tab=texts&lang=en', A)
nonce = re.search(r'name="_wpnonce" value="([^"]+)"', body).group(1)
st, _, h = req('/wp-admin/admin-post.php', A, {'action': 'aladdin_payab_reset_texts', '_wpnonce': nonce, 'lang': 'en'})
st, pg, _ = req('/en/', '')
t('reset restores the approved English text', 'PAYAB NEW' not in pg and 'PAYAB SANAT ASA' in pg)
# import tab + subscriber access
st, body, _ = req('/wp-admin/options-general.php?page=aladdin-payab&tab=import', S)
t('subscriber cannot open settings page', st in (403, 302) or 'Aladdin Payab' not in body, st)
st, body, _ = req('/wp-admin/options-general.php?page=aladdin-payab&tab=import', A)
t('import tab shows journal + undo', 'aladdin_payab_undo' in body)
inon = re.search(r'name="_wpnonce" value="([^"]+)"', body).group(1)
st, _, h = req('/wp-admin/admin-post.php', A, {'action': 'aladdin_payab_import', '_wpnonce': inon})
t('import without the confirmation box is refused', 'aladdin_msg=confirm' in h.get('Location', ''), h.get('Location', ''))
st, body, _ = req('/wp-admin/options-general.php?page=aladdin-payab&tab=general', A)
t('general tab renders', st == 200 and 'catalog_mode' in body)
st, body, _ = req('/wp-admin/options-general.php?page=aladdin-payab&tab=reserved', A)
t('reserved tab labels unimplemented options', 'Reserved' in body)
print('admin_http: pass=%d fail=%d' % (P, F)); sys.exit(1 if F else 0)
