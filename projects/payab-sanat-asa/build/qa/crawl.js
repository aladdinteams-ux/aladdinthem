// Crawl every route in WordPress (and the source for baseline): status, overflow, PHP errors, H1, html lang/dir, JS errors,
// broken images, internal links, language-switch targets, hreflang.   usage: PORT=8091 PORT_SRC=8092 node crawl.js
const { chromium } = require('/opt/node22/lib/node_modules/playwright');
const groups = ['', 'shop-2', 'about-us-2', 'contact-us', 'blog-2', 'videos', 'gallery', 'selection-guide', 'categories', 'categories/electric', 'categories/manual', 'categories/sling', 'industrial-certifications-and-certificates', 'product/electric-lift', 'product/manual-lift', 'product/transfer-sling', 'articles/cane-types', 'articles/mattress-guide', 'articles/patient-lift-guide', 'articles/pillow-types', 'articles/rollator-guide', 'articles/wheelchair-guide'];
const langs = ['fa', 'en', 'ar'];
(async () => {
  const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium', args: ['--no-sandbox'] });
  let bad = 0, total = 0; const linkSet = new Set();
  for (const w of [390, 1440]) {
    const ctx = await b.newContext({ viewport: { width: w, height: 900 } });
    const p = await ctx.newPage();
    const errs = [];
    p.on('pageerror', e => errs.push(e.message.slice(0, 100)));
    await p.route(/^https?:\/\/(?!localhost)/, r => r.fulfill({ status: 200, contentType: 'text/html', body: '' }));
    for (const l of langs) for (const g of groups) {
      errs.length = 0; total++;
      const path = '/' + (l === 'fa' ? '' : l + '/') + (g ? g + '/' : '');
      const r = await p.goto('http://localhost:' + process.env.PORT + path, { waitUntil: 'load' });
      await p.evaluate(() => document.fonts.ready);
      const m = await p.evaluate(() => {
        const imgs = [...document.images].filter(i => i.complete && i.naturalWidth === 0 && i.currentSrc && !i.currentSrc.startsWith('data:'));
        const links = [...document.querySelectorAll('a[href]')].map(a => a.href).filter(h => h.startsWith(location.origin));
        return {
          ovf: document.documentElement.scrollWidth > document.documentElement.clientWidth + 1,
          php: /Fatal error|Warning<\/b>|Notice<\/b>|Deprecated<\/b>|Stack trace/.test(document.body.innerHTML),
          h1: document.querySelectorAll('h1').length, lang: document.documentElement.lang, dir: document.documentElement.dir,
          broken: imgs.length, links, hreflang: document.querySelectorAll('link[rel=alternate][hreflang]').length,
          canonical: document.querySelectorAll('link[rel=canonical]').length, desc: document.querySelectorAll('meta[name=description]').length,
          langLinks: [...document.querySelectorAll('.language-switch a')].map(a => a.getAttribute('lang') + '>' + new URL(a.href).pathname),
          main: !!document.querySelector('main#content'), title: document.title.length > 3,
        };
      });
      const dir = l === 'en' ? 'ltr' : 'rtl';
      const ok = r.status() === 200 && !m.ovf && !m.php && m.h1 === 1 && m.lang === l && m.dir === dir && errs.length === 0 && m.broken === 0 && m.hreflang >= 4 && m.canonical === 1 && m.main && m.title;
      m.links.forEach(h => linkSet.add(h.split('#')[0]));
      if (!ok) { bad++; }
      if (!ok || process.env.VERBOSE) console.log((ok ? 'PASS ' : 'FAIL ') + w + ' ' + path + ' st=' + r.status() + ' ovf=' + m.ovf + ' php=' + m.php + ' h1=' + m.h1 + ' lang=' + m.lang + '/' + m.dir + ' broken=' + m.broken + ' hreflang=' + m.hreflang + ' canon=' + m.canonical + (errs.length ? ' js=' + errs.join(';') : ''));
      if (w === 1440 && g === 'shop-2') console.log('  langLinks', path, m.langLinks.join(' '));
    }
    await ctx.close();
  }
  // internal links
  const ctx = await b.newContext(); const p = await ctx.newPage(); let deadLinks = 0;
  for (const u of linkSet) { const r = await p.request.get(u, { maxRedirects: 5 }); if (r.status() >= 400) { deadLinks++; console.log('DEADLINK', r.status(), u); } }
  console.log(`routes_checked=${total * 1} (x2 widths) bad=${bad} unique_internal_links=${linkSet.size} dead=${deadLinks}`);
  await b.close();
})();
