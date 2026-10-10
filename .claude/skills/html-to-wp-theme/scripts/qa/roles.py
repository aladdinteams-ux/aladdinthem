import json, subprocess, requests, sys, html
S = sys.argv[1]
d = json.loads(subprocess.check_output(['php', S + '/qa/forms/roles.php', S + '/wpel'], stderr=subprocess.DEVNULL).decode().strip().splitlines()[-1])
print('lead', d['lead'], 'meta', json.dumps(d['meta'], ensure_ascii=False)[:300])
print('media attachments for new lead:', d['media_library_attachments_for_lead'], 'PASS' if d['media_library_attachments_for_lead'] == 0 else 'FAIL')
res = []
for role, u in d['users'].items():
    r = requests.get(html.unescape(u['url']), headers={'Cookie': u['cookie']}, allow_redirects=False)
    exp_ok = role in ('administrator', 'editor')
    ok = (r.status_code == 200 and r.headers.get('Content-Disposition', '').startswith('attachment') and r.headers.get('X-Content-Type-Options') == 'nosniff') if exp_ok else r.status_code in (403,)
    print(role, r.status_code, r.headers.get('Content-Type'), r.headers.get('Content-Disposition', '')[:60], 'PASS' if ok else 'FAIL')
# Admin URL used by an anonymous visitor (link leaked by e-mail forward)
u = d['users']['administrator']
r = requests.get(html.unescape(u['url']), allow_redirects=False)
print('anonymous with admin link', r.status_code, 'PASS' if r.status_code != 200 else 'FAIL')
# Editor using the administrator's nonce
r = requests.get(html.unescape(u['url']), headers={'Cookie': d['users']['editor']['cookie']}, allow_redirects=False)
print('editor with admin nonce', r.status_code, 'PASS' if r.status_code != 200 else 'FAIL')
r = requests.get(d['direct_url'], allow_redirects=False)
print('direct URL to private file (PHP built-in server ignores .htaccess):', r.status_code)
