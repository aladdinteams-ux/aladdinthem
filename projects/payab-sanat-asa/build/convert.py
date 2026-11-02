#!/usr/bin/env python3
"""Step 4: convert each page <main> into Elementor container/widget JSON (free Elementor only) + a plain-HTML fallback.
Usage: python3 -I build/convert.py SRC_DIR THEME_DIR PLUGIN_DIR"""
import sys, re, json, os, html, posixpath, urllib.parse
sys.path.insert(0, os.path.dirname(__file__))
from pagelib import *
from lxml import html as LH, etree

BLOCK = {'div', 'section', 'article', 'aside', 'nav', 'header', 'footer', 'main', 'figure', 'figcaption', 'form', 'details',
         'table', 'ul', 'ol', 'dl', 'dialog', 'iframe', 'select', 'textarea', 'blockquote', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'li'}
STRUCT_TAGS = {'div', 'section', 'article', 'aside', 'nav', 'header', 'footer', 'figure'}
TEXT_TAGS = {'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'ul', 'ol', 'blockquote', 'dl'}
OPAQUE = {'table', 'form', 'details', 'dialog', 'iframe', 'select', 'textarea', 'svg'}   # always one leaf (raw inner markup control)
INLINE_OK = {'a', 'span', 'em', 'strong', 'b', 'i', 'small', 'br', 'svg', 'img', 'button', 'sup', 'sub', 'mark', 'time', 'abbr', 'label', 'input', 'g', 'path'}

FA_ONLY = re.compile(r'[\u067e\u0686\u0698\u06af\u06a9\u06cc\u06f0-\u06f9]')
TOPICS = {   # Persian topic shown after "Quick answer:" in the en/ar pages -> translation
    'en': {'پایاب صنعت آسا': 'Payab Sanat Asa', 'آموزش تجهیزات توانبخشی': 'rehabilitation equipment guides', 'بالابر بیمار برقی': 'electric patient lifts',
           'انواع بالابر بیمار': 'types of patient lifts', 'بالابر بیمار دستی هیدرولیک': 'manual hydraulic patient lifts', 'جلیقه انتقال بیمار': 'patient transfer slings',
           'تماس با پایاب صنعت آسا': 'contacting Payab Sanat Asa', 'عکس بالابر بیمار': 'patient lift photos', 'مدارک و گواهی پایاب صنعت': 'Payab Sanat documents and certificates',
           'خرید بالابر بیمار برقی': 'buying an electric patient lift', 'خرید بالابر بیمار دستی': 'buying a manual patient lift', 'راهنمای خرید بالابر بیمار': 'patient lift buying guide',
           'تجهیزات انتقال بیمار': 'patient transfer equipment', 'آموزش کار با بالابر بیمار': 'patient lift training'},
    'ar': {'پایاب صنعت آسا': 'باياب صنعت آسا', 'آموزش تجهیزات توانبخشی': 'أدلة معدات التأهيل', 'بالابر بیمار برقی': 'رافعة المريض الكهربائية',
           'انواع بالابر بیمار': 'أنواع رافعات المرضى', 'بالابر بیمار دستی هیدرولیک': 'رافعة المريض الهيدروليكية اليدوية', 'جلیقه انتقال بیمار': 'حمالة نقل المريض',
           'تماس با پایاب صنعت آسا': 'التواصل مع باياب صنعت آسا', 'عکس بالابر بیمار': 'صور رافعة المريض', 'مدارک و گواهی پایاب صنعت': 'وثائق وشهادات باياب صنعت',
           'خرید بالابر بیمار برقی': 'شراء رافعة مريض كهربائية', 'خرید بالابر بیمار دستی': 'شراء رافعة مريض يدوية', 'راهنمای خرید بالابر بیمار': 'دليل شراء رافعة المريض',
           'تجهیزات انتقال بیمار': 'معدات نقل المرضى', 'آموزش کار با بالابر بیمار': 'تدريب على استخدام رافعة المريض'},
}
SUFFIX = {'en': {' — نمای مفهومی محصول': ' — conceptual product view'}, 'ar': {' — نمای مفهومی محصول': ' — عرض مفاهيمي للمنتج'}}
LEFT = []      # strings still containing Persian-only letters after localisation (reported by the build)

def localize(lang, text):
    """Fix source leftovers: Persian words inside en/ar pages (see report P-06)."""
    if lang == 'fa' or not text or not FA_ONLY.search(text):
        return text
    for k, v in SUFFIX[lang].items():
        text = text.replace(k, v)
    m = re.match(r'^(.*?:\s*)(.+)$', text)
    if m and m.group(2).strip() in TOPICS[lang]:
        text = m.group(1) + TOPICS[lang][m.group(2).strip()]
    if lang == 'ar':   # Persian letters/digits typed inside Arabic text -> Arabic letters, Western digits
        text = text.translate({0x06cc: 0x064a, 0x06a9: 0x0643, **{0x06f0 + i: 0x30 + i for i in range(10)}})
    if FA_ONLY.search(text):
        LEFT.append((lang, text[:80]))
    return text

def localize_tree(el, lang):
    for e in el.iter():
        if not isinstance(e.tag, str):
            continue
        if e.text: e.text = localize(lang, e.text)
        if e.tail: e.tail = localize(lang, e.tail)
        for a in ('alt', 'aria-label', 'title', 'placeholder'):
            if a in e.attrib: e.set(a, localize(lang, e.get(a)))

class Ctx:
    def __init__(self, page, urlmap):
        self.page = page; self.urlmap = urlmap; self.n = 0; self.ids = set()
    def eid(self):
        self.n += 1
        return '%07x' % (0xA1D0000 + self.n + (hash(self.page['lang'] + self.page['group']) & 0xFFFF) * 0x1000)

def norm_abs(u):
    return urllib.parse.unquote(u).lower().rstrip('/')

PRODUCT_MAP = {
    norm_abs('https://payabsanatasa.ir/product/بالابر-بیمار-برقی-مخصوص-منزل-با-برق-شه/'): 'product/electric-lift',
    norm_abs('https://payabsanatasa.ir/product/بالابر-بیمار-دستی-هیدرولیک-سایز-بزرگ-ب/'): 'product/manual-lift',
    norm_abs('https://payabsanatasa.ir/product/جلیقه-ایستادن/'): 'product/transfer-sling',
}

def fix_url(val, ctx, attr):
    v = val.strip()
    if not v: return val
    if v.startswith(('#', 'tel:', 'mailto:', 'data:', 'javascript:')): return val
    if v.startswith('http'):
        k = norm_abs(v.split('#')[0])
        if k in PRODUCT_MAP:
            return '{{route:%s:%s}}' % (ctx.page['lang'], PRODUCT_MAP[k])
        if k in ctx.urlmap:
            lang, group = ctx.urlmap[k]
            return '{{route:%s:%s}}' % (lang, group) + (('#' + v.split('#')[1]) if '#' in v else '')
        return val
    base = posixpath.dirname(ctx.page['path'])
    full = posixpath.normpath(posixpath.join(base, v.split('#')[0].split('?')[0]))
    if full.startswith('assets/'):
        return '{{theme}}/' + full
    r = resolve_href(v, ctx.page['path'])
    if r:
        return '{{route:%s:%s}}' % r + (('#' + v.split('#')[1]) if '#' in v else '')
    return val

def fix_tree(el, ctx):
    for e in el.iter():
        if not isinstance(e.tag, str): continue
        for a in ('href', 'src', 'poster', 'data-image', 'action'):
            if a in e.attrib:
                e.set(a, fix_url(e.get(a), ctx, a))
        if 'style' in e.attrib:
            e.set('style', re.sub(r'url\((["\']?)(\.\./)*assets/', r'url(\1{{theme}}/assets/', e.get('style')))
        if e.get('action') and 'payabsanatasa.ir' in e.get('action'):
            e.set('action', '{{search}}')

def inner(el):
    s = (el.text or '')
    for c in el:
        s += etree.tostring(c, encoding='unicode', method='html', with_tail=True)
    return s

def outer(el):
    return etree.tostring(el, encoding='unicode', method='html', with_tail=False)

def has_block(el):
    return any(isinstance(d.tag, str) and d.tag in BLOCK for d in el.iterdescendants())

def is_leaf(el):
    t = el.tag
    if t in OPAQUE or t in TEXT_TAGS or t == 'figure': return True       # figure is not an Elementor container tag
    if t in STRUCT_TAGS or t in ('main',):
        if len(el) == 0 and not (el.text or '').strip(): return False      # empty decorative block -> empty container
        if any(d.tag == 'img' for d in el.iterdescendants() if isinstance(d.tag, str)) and el.xpath('count(.//a//img)=0 or true()'):
            return False                                                      # images get their own media widget
        return not has_block(el) and not any(isinstance(c.tag, str) and c.tag in STRUCT_TAGS for c in el)
    return True

def container_settings(el, ctx):
    s = {'content_width': 'full', 'flex_direction': 'column', 'flex_gap': {'column': '0', 'row': '0', 'isLinked': True, 'unit': 'px', 'size': 0},
         'padding': {'unit': 'px', 'top': '0', 'right': '0', 'bottom': '0', 'left': '0', 'isLinked': True},
         'margin': {'unit': 'px', 'top': '0', 'right': '0', 'bottom': '0', 'left': '0', 'isLinked': True}}
    # `aladdin-pz` = "plain block": the approved CSS controls this container (see elementor-bridge.css); remove it to use Elementor layout controls.
    s['_css_classes'] = ((el.get('class') or '') + ' aladdin-pz').strip()
    if el.get('id'): s['_element_id'] = el.get('id')
    if el.tag in ('header', 'footer', 'article', 'aside', 'section', 'nav', 'main', 'div'): s['html_tag'] = el.tag
    attrs = []
    for k, v in el.attrib.items():
        if k in ('class', 'id'): continue
        attrs.append('%s|%s' % (k, v))
    if attrs: s['aladdin_attrs'] = '\n'.join(attrs)
    return s

def widget(wt, settings, ctx):
    return {'id': ctx.eid(), 'elType': 'widget', 'widgetType': wt, 'settings': settings, 'elements': []}

def leaf(el, ctx):
    t = el.tag
    if t in TEXT_TAGS:
        return widget('aladdin_payab_sanat_asa_text', {'editor': outer(el)}, ctx)
    # generic element widget (plugin): exact tag/class/attrs, link, image, inner markup
    cls = el.get('class', '')
    attrs = [(k, v) for k, v in el.attrib.items() if k not in ('class', 'id', 'href', 'alt') and not (k == 'src' and t == 'img')]
    s = {'tag': t, 'css_classes': cls, 'element_id': el.get('id', ''),
         'attrs': '\n'.join('%s|%s' % (k, v) for k, v in attrs)}
    if t == 'img':
        s['src'] = el.get('src', ''); s['alt'] = el.get('alt', '')
    else:
        if el.get('href') is not None: s['href'] = el.get('href')
        inn = inner(el)
        if re.search(r'<[a-zA-Z]', inn): s['inner_html'] = inn
        else: s['text'] = html.unescape(inn.strip()) if inn.strip() else ''
    return widget('aladdin_payab_sanat_asa_element', s, ctx)

def convert(el, ctx):
    """returns list of elementor elements for element el"""
    if not isinstance(el.tag, str): return []
    if is_leaf(el):
        return [leaf(el, ctx)]
    kids = []
    # mixed content: bare text between block children becomes a text-editor
    if (el.text or '').strip():
        kids.append(widget('aladdin_payab_sanat_asa_text', {'editor': '<span>%s</span>' % html.escape(el.text.strip())}, ctx))
    for c in el:
        kids.extend(convert(c, ctx))
        if (c.tail or '').strip():
            kids.append(widget('aladdin_payab_sanat_asa_text', {'editor': '<span>%s</span>' % html.escape(c.tail.strip())}, ctx))
    return [{'id': ctx.eid(), 'elType': 'container', 'isInner': False, 'settings': container_settings(el, ctx), 'elements': kids}]

def build_urlmap(src, pages):
    m = {}
    for p in pages:
        h = open(p['file'], encoding='utf8').read()
        c = re.search(r'<link[^>]*rel="canonical"[^>]*>', h)
        if c:
            u = re.search(r'href="([^"]+)"', c.group(0)).group(1)
            m[norm_abs(u)] = (p['lang'], p['group'])
    return m

if __name__ == '__main__':
    src, theme, plugin = sys.argv[1:4]
    pages = list_pages(src)
    urlmap = build_urlmap(src, pages)
    print('urlmap', len(urlmap))
    out = []
    for p in pages:
        h = open(p['file'], encoding='utf8').read()
        doc = LH.fromstring(h)
        main = doc.xpath('//main')[0]
        ctx = Ctx(p, urlmap)
        fix_tree(main, ctx)
        localize_tree(main, p['lang'])
        elements = []
        for c in main:
            elements.extend(convert(c, ctx))
        head = doc.xpath('//head')[0]
        title = localize(p['lang'], (head.findtext('title') or '').strip())
        desc = localize(p['lang'], ''.join(head.xpath('./meta[@name="description"]/@content')))
        ld = [etree.tostring(s, encoding='unicode', method='text') for s in head.xpath('.//script[@type="application/ld+json"]')]
        out.append({'lang': p['lang'], 'group': p['group'], 'path': p['path'], 'title': title, 'description': desc,
                    'h1': ''.join(main.xpath('.//h1[1]//text()')).strip(), 'jsonld': ld,
                    'elementor': elements, 'fallback': inner(main), 'main_attrs': dict(main.attrib),
                    'widgets': ctx.n})
    print('Persian-only letters left in en/ar content:', len(LEFT), LEFT[:5])
    d = os.path.join(plugin, 'data'); os.makedirs(d, exist_ok=True)
    json.dump(out, open(os.path.join(d, 'pages.json'), 'w', encoding='utf8'), ensure_ascii=False)
    print(len(out), 'pages', sum(o['widgets'] for o in out), 'elements', os.path.getsize(os.path.join(d, 'pages.json')) // 1024, 'KB')
