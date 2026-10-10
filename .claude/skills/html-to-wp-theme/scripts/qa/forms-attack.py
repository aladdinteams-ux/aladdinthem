"""Real HTTP tests for Larijani Stone Core forms. Usage: attack.py BASE ROUTE"""
import requests, re, json, base64, sys, html, time, io, zipfile, random
base, route = sys.argv[1], sys.argv[2]
ajax = base + '/wp-admin/admin-ajax.php'
results = []
def check(name, cond, info=''):
    results.append((name, bool(cond)))
    print(('PASS ' if cond else 'FAIL ') + name + ('  ' + str(info)[:200] if info else ''))

page = requests.get(base + route).text
form = re.search(r'<form[^>]*data-ls-form.*?</form>', page, re.S).group(0)
schema = html.unescape(re.search(r'name="ls_schema" value="([^"]+)"', form).group(1))
fname = html.unescape(re.search(r'name="ls_form_name" value="([^"]+)"', form).group(1))
opts = re.findall(r'<option value="([^"]*)"', form)
# Each test simulates a different visitor IP? Built-in server: all 127.0.0.1, so we
# space requests within the per-IP limits and reset counters between groups.
def token():
    r = requests.post(ajax, data={'action': 'larijani_form_token'}).json()
    return r['data']['token']
def phone():
    return '0912' + str(random.randint(1000000, 9999999))
def base_fields(p=None):
    d = {'action': 'larijani_lead', 'ls_form_name': fname, 'ls_schema': schema, 'ls_page': base + route,
         'fields[name]': 'آزمون ' + str(random.randint(1, 99999)), 'fields[phone]': p or phone(), 'fields[city]': 'تهران'}
    if 'fields[product]' in form:
        d['fields[product]'] = html.unescape(opts[0])
    return d
def post(d, files=None):
    r = requests.post(ajax, data=d, files=files, headers=HDR)
    try:
        return r.status_code, r.json()
    except Exception:
        return r.status_code, r.text[:200]

HDR = {'X-LS-QA': 'limits'} if 'files' in sys.argv else {}
mode = sys.argv[3] if len(sys.argv) > 3 else 'all'

if mode in ('all', 'token'):
    # 1. no token
    c, j = post(base_fields())
    check('no token rejected (403 expired)', c == 403 and j['data']['code'] == 'expired', (c, j))
    # 2. forged token
    d = base_fields(); d['ls_token'] = 'eyJpIjoxfQ.AAAA'
    c, j = post(d); check('forged token rejected', c == 403, (c, j))
    # 3. client-side ls_ts bypass of 1.3 no longer matters: immediate submit is too fast
    t = token(); d = base_fields(); d['ls_token'] = t; d['ls_ts'] = '1'
    c, j = post(d); check('submit < 3s rejected (425 too_fast)', c == 425 and j['data']['code'] == 'too_fast', (c, j))
    # 4. valid submit after wait
    time.sleep(3.2)
    c, j = post(d); ok = c == 200 and j.get('success')
    check('valid submission accepted', ok, (c, j))
    trk = j['data']['tracking'] if ok else ''
    check('tracking format LS-YYMMDD-XXXXXX', re.match(r'^LS-\d{6}-[A-HJ-KM-NP-Z2-9]{6}$', trk), trk)
    # 5. replay same token with different data
    d2 = dict(d); d2['fields[name]'] = 'replay'
    c, j = post(d2); check('token replay rejected (409)', c == 409, (c, j))
    # 6. duplicate content with fresh token -> same tracking, no new lead
    t2 = token(); time.sleep(3.2); d3 = dict(d); d3['ls_token'] = t2
    c, j = post(d3); check('duplicate content returns original tracking', c == 200 and j['data'].get('duplicate') and j['data']['tracking'] == trk, (c, j))
    # 7. tampered schema (drop required fields)
    t3 = token(); time.sleep(3.2)
    body, mac = schema.split('.')
    pad = body + '=' * (-len(body) % 4)
    sd = json.loads(base64.urlsafe_b64decode(pad)); 
    for v in sd['f'].values(): v['r'] = 0
    forged = base64.urlsafe_b64encode(json.dumps(sd).encode()).decode().rstrip('=') + '.' + mac
    d4 = base_fields(); d4['ls_token'] = t3; d4['ls_schema'] = forged
    c, j = post(d4); check('tampered schema rejected (400 invalid)', c == 400 and j['data']['code'] == 'invalid', (c, j))
    # 8. honeypot
    d5 = base_fields(); d5['ls_token'] = t3; d5['ls_hp'] = 'http://spam'
    c, j = post(d5); check('honeypot gets fake success', c == 200 and j.get('success'), (c, j))
    print('TRACKING', trk)

if mode in ('all', 'validate'):
    def submit(mod, files=None):
        t = token(); time.sleep(3.1)
        d = base_fields(); d['ls_token'] = t; d.update(mod)
        for k in [k for k, v in mod.items() if v is None]: d.pop(k)
        return post(d, files)
    c, j = submit({'fields[name]': None})
    check('missing required field -> field error', c == 400 and j['data'].get('field') == 'name', (c, j))
    c, j = submit({'fields[phone]': '12345'})
    check('invalid phone rejected', c == 400 and j['data'].get('field') == 'phone', (c, j))
    c, j = submit({'fields[phone]': '۰۹۱۲ ۳۴۵-۶۷۸۹'})
    check('Persian digits phone normalised & accepted', c == 200 and j.get('success'), (c, j))
    if 'fields[product]' in form:
        c, j = submit({'fields[product]': 'گزینه جعلی'})
        check('select value outside options rejected', c == 400 and j['data'].get('field') == 'product', (c, j))
    c, j = submit({'fields[name]': 'x' * 300})
    check('over-long text rejected', c == 400, (c, j))
    c, j = submit({'fields[evil]': '<script>alert(1)</script>', 'fields[name]': '<b>نام</b><script>x</script>'})
    check('unknown fields ignored, html stripped (accepted)', c == 200, (c, j))
    c, j = submit({'fields[name][]': 'arr', 'fields[name]': None})
    check('array in scalar field rejected', c == 400, (c, j))

if mode in ('all', 'files'):
    has_file = 'ls_files' in form
    def submit_files(files):
        t = token(); time.sleep(3.1)
        d = base_fields(); d['ls_token'] = t
        return post(d, files)
    if not has_file:
        c, j = submit_files([('ls_files[]', ('a.pdf', b'%PDF-1.4 test', 'application/pdf'))])
        check('files rejected on form without upload field', c == 400 and j['data']['code'] == 'invalid', (c, j))
    else:
        c, j = submit_files([('ls_files[]', ('نقشه.pdf', b'%PDF-1.4\n%%EOF\n', 'application/pdf'))])
        check('valid PDF accepted', c == 200 and j.get('success'), (c, j))
        print('PDF_TRACKING', j.get('data', {}).get('tracking'))
        c, j = submit_files([('ls_files[]', ('shell.pdf', b'<?php system($_GET[1]); ?>', 'application/pdf'))])
        check('PHP disguised as PDF rejected (bad_content)', c == 400 and j['data']['code'] == 'bad_content', (c, j))
        c, j = submit_files([('ls_files[]', ('x.php', b'<?php echo 1;', 'image/jpeg'))])
        check('.php extension rejected (bad_type)', c == 400 and j['data']['code'] == 'bad_type', (c, j))
        c, j = submit_files([('ls_files[]', ('x.php.jpg', b'\xff\xd8\xff\xe0<?php echo 1;', 'image/jpeg'))])
        check('fake JPEG (magic only) rejected by image check', c == 400 and j['data']['code'] == 'bad_content', (c, j))
        b = io.BytesIO(); z = zipfile.ZipFile(b, 'w'); z.writestr('docs/plan.txt', 'ok'); z.writestr('docs/shell.php', '<?php'); z.close()
        c, j = submit_files([('ls_files[]', ('p.zip', b.getvalue(), 'application/zip'))])
        check('ZIP containing .php rejected', c == 400 and j['data']['code'] == 'zip_rejected', (c, j))
        b = io.BytesIO(); z = zipfile.ZipFile(b, 'w'); z.writestr('../../evil.txt', 'x'); z.close()
        c, j = submit_files([('ls_files[]', ('t.zip', b.getvalue(), 'application/zip'))])
        check('ZIP path traversal rejected', c == 400 and j['data']['code'] == 'zip_rejected', (c, j))
        b = io.BytesIO(); z = zipfile.ZipFile(b, 'w', zipfile.ZIP_DEFLATED); z.writestr('plan.dwg', b'AC1027' + b'\0' * 1000); z.writestr('photo.txt', 'x'); z.close()
        c, j = submit_files([('ls_files[]', ('ok.zip', b.getvalue(), 'application/zip'))])
        check('clean ZIP accepted', c == 200 and j.get('success'), (c, j))
        c, j = submit_files([('ls_files[]', ('plan.dwg', b'AC1032' + b'\0' * 2000, 'application/octet-stream'))])
        check('DWG (AC10xx header) accepted', c == 200 and j.get('success'), (c, j))
        c, j = submit_files([('ls_files[]', ('fake.dwg', b'MZ\x90\x00' + b'\0' * 100, 'application/octet-stream'))])
        check('EXE renamed .dwg rejected', c == 400 and j['data']['code'] == 'bad_content', (c, j))
        c, j = submit_files([('ls_files[]', ('big.pdf', b'%PDF-' + b'0' * (21 * 1024 * 1024), 'application/pdf'))])
        check('PDF over 20MB rejected', c == 400 and j['data']['code'] in ('file_too_large',), (c, j))
        c, j = submit_files([('ls_files[]', ('a%d.pdf' % i, b'%PDF-1.4', 'application/pdf')) for i in range(6)])
        check('more than 5 files rejected', c == 400 and j['data']['code'] == 'too_many_files', (c, j))

if mode in ('all', 'rate'):
    p = phone(); codes = []
    for i in range(4):
        t = token(); time.sleep(3.1)
        d = base_fields(p); d['ls_token'] = t; d['fields[city]'] = 'شهر' + str(i)
        codes.append(post(d)[0])
    check('per-phone limit (3/h) -> 4th is 429', codes[:3] == [200, 200, 200] and codes[3] == 429, codes)

print('SUMMARY', sum(1 for _, ok in results if ok), '/', len(results))
