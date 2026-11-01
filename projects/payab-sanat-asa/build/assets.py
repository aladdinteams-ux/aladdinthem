#!/usr/bin/env python3
"""Step 1: copy source assets into the theme, bundle CSS per page family, bundle local fonts.
Usage: python3 -I build/assets.py SRC_DIR THEME_DIR FONT_CACHE_DIR"""
import sys, re, shutil, os, glob
src, theme, fc = sys.argv[1:4]
A = os.path.join(theme, 'assets')
for d in ('css', 'js', 'images', 'fonts'):
    os.makedirs(os.path.join(A, d), exist_ok=True)
for f in glob.glob(os.path.join(src, 'assets/images/*')):
    shutil.copy(f, os.path.join(A, 'images'))

def links(path):
    h = open(path, encoding='utf8').read()
    out = []
    for m in re.finditer(r'<link[^>]*href="((?:\.\./)*assets/css/[^"]+)"', h):
        out.append(re.sub(r'^(\.\./)+', '', m.group(1))[len('assets/css/'):])
    return out

fam = {
    'home': ['index.html', 'en/index.html', 'ar/index.html'],
    'inner': ['about-us-2/index.html', 'blog-2/index.html', 'ar/about-us-2/index.html', 'en/blog-2/index.html'],
    'product': ['product/electric-lift/index.html', 'en/product/electric-lift/index.html'],
    'contact': ['contact-us/index.html', 'en/contact-us/index.html'],
}
# common prefix (core) = CSS present in every family's first-seen order
lists = {k: links(os.path.join(src, v[0])) for k, v in fam.items()}
late = ['v36-accordion-icons.css', 'v37-frameless-arrow.css']
en_home = links(os.path.join(src, 'en/index.html'))
fa_only = [c for c in lists['home'] if c not in en_home]      # CSS the Persian pages link but the en/ar pages do not (v24/v25)
core = [c for c in lists['home'] if all(c in l for l in lists.values()) and c not in late and c not in fa_only]
print('fa-only css', fa_only)
cut = lists['home'].index(fa_only[0]) if fa_only else len(core)
before = [c for c in core if lists['home'].index(c) < cut]
after = [c for c in core if lists['home'].index(c) >= cut]
print('core', len(core))
def read(c): return open(os.path.join(src, 'assets/css', c), encoding='utf8').read()
def bundle(name, files):
    with open(os.path.join(A, 'css', name), 'w', encoding='utf8') as o:
        for c in files:
            o.write('/* == %s == */\n%s\n' % (c, read(c)))
    print(name, len(files))
bundle('site-core-a.css', before)
bundle('site-fa-compat.css', fa_only)
bundle('site-core-b.css', after)
bundle('site-final.css', late)
for k in ('home', 'inner', 'product', 'contact'):
    extra = [c for c in lists[k] if c not in core and c not in late and c not in fa_only]
    bundle('site-%s.css' % k, extra)
# union-order sanity: home-only extras (not in other families)
for c in glob.glob(os.path.join(src, 'assets/js/*.js')):
    shutil.copy(c, os.path.join(A, 'js'))

# ---- fonts
F = os.path.join(A, 'fonts')
def cp(a, b): shutil.copy(a, os.path.join(F, b))
css = ['/* Local fonts: Vazirmatn (OFL), Estedad (OFL), Manrope (OFL), Cairo (OFL), Bootstrap Icons (MIT). */']
cp(fc + '/vazirmatn/package/fonts/webfonts/Vazirmatn[wght].woff2', 'Vazirmatn-var.woff2')
css.append("@font-face{font-family:'Vazirmatn';src:url('../fonts/Vazirmatn-var.woff2') format('woff2');font-weight:100 900;font-display:swap}")
cp(fc + '/gf/ofl/estedad/Estedad[wght].ttf', 'Estedad-var.ttf')   # same version Google Fonts serves
css.append("@font-face{font-family:'Estedad';src:url('../fonts/Estedad-var.ttf') format('truetype');font-weight:100 900;font-display:swap}")
for w in (400, 500, 600, 700, 800):
    for sub in ('latin', 'latin-ext'):
        n = 'manrope-%s-%d-normal.woff2' % (sub, w)
        cp(fc + '/manrope/package/files/' + n, n)
        rng = 'U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD' if sub == 'latin' else 'U+0100-02AF,U+0304,U+0308,U+0329,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20C0,U+2113,U+2C60-2C7F,U+A720-A7FF'
        css.append("@font-face{font-family:'Manrope';src:url('../fonts/%s') format('woff2');font-weight:%d;font-display:swap;unicode-range:%s}" % (n, w, rng))
    for sub in ('arabic', 'latin'):
        n = 'cairo-%s-%d-normal.woff2' % (sub, w)
        cp(fc + '/cairo/package/files/' + n, n)
        rng = 'U+0600-06FF,U+0750-077F,U+0870-088E,U+0890-0891,U+0898-08E1,U+08E3-08FF,U+200C-200E,U+2010-2011,U+204F,U+2E41,U+FB50-FDFF,U+FE70-FE74,U+FE76-FEFC' if sub == 'arabic' else 'U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD'
        css.append("@font-face{font-family:'Cairo';src:url('../fonts/%s') format('woff2');font-weight:%d;font-display:swap;unicode-range:%s}" % (n, w, rng))
cp(fc + '/bootstrap-icons/package/font/fonts/bootstrap-icons.woff2', 'bootstrap-icons.woff2')
bi = open(fc + '/bootstrap-icons/package/font/bootstrap-icons.min.css', encoding='utf8').read()
bi = re.sub(r'@font-face\{.*?\}', "@font-face{font-display:swap;font-family:bootstrap-icons;src:url('../fonts/bootstrap-icons.woff2') format('woff2')}", bi, count=1, flags=re.S)
open(os.path.join(A, 'css/fonts.css'), 'w', encoding='utf8').write('\n'.join(css) + '\n')
open(os.path.join(A, 'css/bootstrap-icons.css'), 'w', encoding='utf8').write(bi)
shutil.copy(fc + '/gf/ofl/estedad/OFL.txt', os.path.join(F, 'LICENSE-Estedad-OFL.txt'))
for d, n in (('vazirmatn', 'Vazirmatn'), ('manrope', 'Manrope'), ('cairo', 'Cairo'), ('bootstrap-icons', 'BootstrapIcons')):
    p = glob.glob(fc + '/%s/package/LICENSE*' % d)
    if p: shutil.copy(p[0], os.path.join(F, 'LICENSE-%s.txt' % n))
print('fonts ok', len(os.listdir(F)))
