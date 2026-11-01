"""Shared parsing helpers for the Payab build scripts (read-only on the untrusted source)."""
import re, os, glob, posixpath
from lxml import html as LH, etree

LANGS = ('fa', 'en', 'ar')

def route_of(path):
    """'en/product/x/index.html' -> ('en','product/x'); 'index.html' -> ('fa','')"""
    d = posixpath.dirname(path)
    parts = [p for p in d.split('/') if p]
    lang = 'fa'
    if parts and parts[0] in ('en', 'ar'):
        lang = parts[0]; parts = parts[1:]
    return lang, '/'.join(parts)

def list_pages(src):
    out = []
    for p in sorted(glob.glob(os.path.join(src, '**/index.html'), recursive=True)):
        rel = os.path.relpath(p, src)
        lang, group = route_of(rel)
        out.append(dict(path=rel, lang=lang, group=group, file=p))
    return out

def resolve_href(href, page_path):
    """Resolve a relative href from a page to ('lang','group') or None."""
    if not href or re.match(r'^(https?:|tel:|mailto:|#|javascript:|data:)', href):
        return None
    base = posixpath.dirname(page_path)
    full = posixpath.normpath(posixpath.join(base, href.split('#')[0].split('?')[0]))
    if full.endswith('index.html'): full = posixpath.dirname(full)
    parts = [p for p in full.split('/') if p and p != '.']
    lang = 'fa'
    if parts and parts[0] in ('en', 'ar'):
        lang = parts[0]; parts = parts[1:]
    return lang, '/'.join(parts)

ATTRS = ('aria-label', 'alt', 'placeholder', 'title', 'aria-labelledby-x')

def tokenise(fragment, page_path, home_abs=('https://payabsanatasa.ir/',)):
    """Return (template_with_{{N}}_tokens, strings). Strings are ('t', text) / ('a', value) / ('u', (lang,group)|raw) in order."""
    strings = []
    def add(kind, val):
        strings.append((kind, val)); return '{{%d}}' % (len(strings) - 1)
    def attr_sub(m):
        name, q, val = m.group(1), m.group(2), m.group(3)
        if name in ATTRS and val.strip():
            return '%s=%s%s%s' % (name, q, add('a', val), q)
        if name == 'href':
            r = resolve_href(val, page_path)
            if r is not None:
                return 'href=%s%s%s' % (q, add('u', r), q)
            if val.startswith('#') or val.startswith('tel:') or val.startswith('mailto:') or val.startswith('http'):
                if val.startswith('tel:') or val.startswith('mailto:'):
                    return 'href=%s%s%s' % (q, add('r', val), q)
        if name == 'action':
            return 'action=%s{{HOME}}%s' % (q, q)
        return m.group(0)
    def tag_sub(m):
        t = m.group(0)
        return re.sub(r'([a-zA-Z:-]+)=(["\'])(.*?)\2', attr_sub, t, flags=re.S)
    parts = re.split(r'(<[^>]*>)', fragment)
    out = []
    for p in parts:
        if p.startswith('<') and p.endswith('>') and not p.startswith('<!'):
            out.append(tag_sub(re.match(r'.*', p, re.S)))
        elif p.strip() and not p.startswith('<'):
            lead = p[:len(p) - len(p.lstrip())]; trail = p[len(p.rstrip()):]
            out.append(lead + add('t', p.strip()) + trail)
        else:
            out.append(p)
    # out items may be match objects for tags
    res = []
    for o in out:
        res.append(o.group(0) if hasattr(o, 'group') else o)
    return ''.join(res), strings
