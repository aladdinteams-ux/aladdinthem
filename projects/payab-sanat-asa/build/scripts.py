#!/usr/bin/env python3
"""Step 3: merge the inline scripts into one theme JS file; language data goes to inc/data/script-strings.json.
Usage: python3 -I build/scripts.py SRC_DIR THEME_DIR"""
import sys, re, json, os
src, theme = sys.argv[1:3]
def scripts(p):
    h = open(os.path.join(src, p), encoding='utf8').read()
    return [m.group(2) for m in re.finditer(r'<script(?![^>]*src)(?![^>]*ld\+json)([^>]*)>(.*?)</script>', h, re.S)]
S = {l: scripts(p) for l, p in (('fa', 'index.html'), ('en', 'en/index.html'), ('ar', 'ar/index.html'))}
strings = {}
for l, sc in S.items():
    m = re.search(r'const data=(\{.*?\});\n', sc[4], re.S)
    mega = json.loads(m.group(1))
    fav = re.search(r"isOn\?(\"[^\"]*\"):(\"[^\"]*\")", sc[3])
    strings[l] = {'mega': mega, 'favAdd': json.loads(fav.group(1)), 'favRemove': json.loads(fav.group(2))}
fa = S['fa']
mega = re.sub(r'const data=\{.*?\};\n', 'const data=(window.PayabAsa&&PayabAsa.mega)||{};\n', fa[4], count=1, flags=re.S)
mega = mega.replace("const assetBase='assets/';", "const assetBase=((window.PayabAsa&&PayabAsa.assets)||'assets')+'/';")
fav = re.sub(r"isOn\?\"[^\"]*\":\"[^\"]*\"", "isOn?((window.PayabAsa&&PayabAsa.favAdd)||''):((window.PayabAsa&&PayabAsa.favRemove)||'')", fa[3])
out = ['/* Aladdin Payab Sanat Asa - merged from the approved HTML inline scripts. Language data comes from window.PayabAsa. */']
for chunk in (fa[1], fa[2], fav, mega, fa[5]):
    out.append(chunk.strip())
js = '\n'.join(out) + '\n'
os.makedirs(os.path.join(theme, 'assets/js'), exist_ok=True)
open(os.path.join(theme, 'assets/js/payab-site.js'), 'w', encoding='utf8').write(js)
json.dump(strings, open(os.path.join(theme, 'inc/data/script-strings.json'), 'w', encoding='utf8'), ensure_ascii=False, indent=0)
print(len(js), list(strings['en']['mega']))
