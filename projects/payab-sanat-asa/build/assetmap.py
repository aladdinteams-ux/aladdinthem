#!/usr/bin/env python3
"""Step 5: per-route asset families and script lists. Usage: python3 -I build/assetmap.py SRC_DIR THEME_DIR"""
import sys, re, json, os
sys.path.insert(0, os.path.dirname(__file__))
from pagelib import *
src, theme = sys.argv[1:3]
m = {}
for p in list_pages(src):
    h = open(p['file'], encoding='utf8').read()
    css = [re.sub(r'^(\.\./)+', '', x)[11:] for x in re.findall(r'<link[^>]*href="((?:\.\./)*assets/css/[^"]+)"', h)]
    js = [os.path.basename(x) for x in re.findall(r'<script[^>]*src="([^"]*assets/js/[^"]+)"', h)]
    fam = 'contact' if 'v32-contact-map.css' in css else 'product' if 'v35-product-description.css' in css else 'inner' if 'v30-content.css' in css else 'home'
    m.setdefault(p['group'], {})[p['lang']] = {'family': fam, 'js': js}
# check language-consistency
for g, v in m.items():
    fams = {x['family'] for x in v.values()}
    jss = {tuple(x['js']) for x in v.values()}
    if len(fams) > 1 or len(jss) > 1: print('lang mismatch', g, fams, jss)
json.dump({g: v['fa'] for g, v in m.items()}, open(os.path.join(theme, 'inc/data/asset-map.json'), 'w'), indent=1)
print({g: (v['fa']['family'], len(v['fa']['js'])) for g, v in m.items()})
