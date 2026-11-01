// Interaction checks in the WordPress site. usage: PORT=8091 node interact.js
const { chromium } = require('/opt/node22/lib/node_modules/playwright');
const B = 'http://localhost:' + process.env.PORT;
const res = []; const t = (n, ok, d) => { res.push(ok); console.log((ok ? 'PASS ' : 'FAIL ') + n + (d ? ' — ' + d : '')); };
(async () => {
  const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium', args: ['--no-sandbox'] });
  const mk = async (w) => { const c = await b.newContext({ viewport: { width: w, height: 900 } }); const p = await c.newPage(); p.errs = []; p.on('pageerror', e => p.errs.push(e.message)); await p.route(/^https?:\/\/(?!localhost)/, r => r.fulfill({ status: 200, body: '' })); return [c, p]; };

  let [c, p] = await mk(390);
  await p.goto(B + '/'); await p.waitForTimeout(500);
  await p.click('#menu-open'); await p.waitForTimeout(400);
  t('mobile drawer opens', await p.evaluate(() => document.getElementById('mobile-panel').classList.contains('open') && document.getElementById('menu-open').getAttribute('aria-expanded') === 'true'));
  t('drawer moves focus into panel', await p.evaluate(() => document.getElementById('mobile-panel').contains(document.activeElement)));
  await p.keyboard.press('Escape'); await p.waitForTimeout(300);
  t('Escape closes drawer', await p.evaluate(() => !document.getElementById('mobile-panel').classList.contains('open') && document.getElementById('mobile-panel').getAttribute('aria-hidden') === 'true'));
  await p.click('#menu-open'); await p.click('#payab-drawer-theme'); await p.waitForTimeout(200);
  t('drawer theme button toggles dark', await p.evaluate(() => document.documentElement.dataset.theme === 'dark'));
  await c.close();

  [c, p] = await mk(1440);
  await p.goto(B + '/'); await p.waitForTimeout(500);
  await p.click('#theme-toggle'); await p.waitForTimeout(200);
  t('day/night toggle sets dark', await p.evaluate(() => document.documentElement.dataset.theme === 'dark'));
  t('dark persisted in localStorage', await p.evaluate(() => localStorage.getItem('payab-theme') === 'dark'));
  await p.reload(); t('dark survives reload before paint', await p.evaluate(() => document.documentElement.dataset.theme === 'dark'));
  await p.click('#theme-toggle');
  t('toggle back to light', await p.evaluate(() => document.documentElement.dataset.theme !== 'dark'));

  // mega menu
  await p.hover('[data-mega-parent="products"]'); await p.waitForTimeout(400);
  t('mega menu opens on hover', await p.evaluate(() => document.querySelector('.payab-header').classList.contains('mega-is-open') && document.getElementById('payab-products-mega').getAttribute('aria-hidden') === 'false'));
  await p.click('[data-mega-category="manual"]'); await p.waitForTimeout(300);
  const mm = await p.evaluate(() => ({ h: document.querySelector('.m9-feature-copy h3').textContent, src: document.querySelector('.m9-feature-row .m9-img img').src }));
  t('mega tab switches content', /دستی/.test(mm.h), mm.h);
  const ir = await p.request.get(mm.src, { maxRedirects: 0 }); t('mega image url resolves to a real image', ir.status() === 200 && /image\//.test(ir.headers()['content-type'] || ''), mm.src.split('/').slice(-3).join('/'));
  await p.mouse.move(700, 600); await p.keyboard.press('Escape');

  // product filter + favourite
  await p.goto(B + '/'); await p.waitForTimeout(500);
  await p.click('[data-filter="manual"]'); await p.waitForTimeout(200);
  const vis = await p.evaluate(() => [...document.querySelectorAll('[data-type]')].filter(e => !e.hidden).map(e => e.dataset.type));
  t('product filter shows only manual', vis.length >= 1 && vis.every(x => x === 'manual'), vis.join(','));
  await p.click('[data-filter="all"]');
  const fav = p.locator('.asa-fav').first(); await fav.scrollIntoViewIfNeeded(); await fav.click();
  t('favourite toggles + persists', await p.evaluate(() => document.querySelector('.asa-fav').getAttribute('aria-pressed') === 'true' && localStorage.getItem('payab-favorite-electric') === '1'));

  // language switch from shop page
  await p.goto(B + '/shop-2/'); await p.click('.language-switch a[lang="en"]'); await p.waitForLoadState('load');
  t('language switch fa->en keeps page', p.url() === B + '/en/shop-2/' && await p.evaluate(() => document.documentElement.lang === 'en' && document.documentElement.dir === 'ltr'), p.url());
  await p.click('.language-switch a[lang="ar"]'); await p.waitForLoadState('load');
  t('language switch en->ar', p.url() === B + '/ar/shop-2/' && await p.evaluate(() => document.documentElement.dir === 'rtl'), p.url());

  // search
  await p.goto(B + '/en/'); await p.click('.header-search-form', { position: { x: 20, y: 20 } }); await p.fill('.header-search-form input[name=s]', 'lift'); await p.press('.header-search-form input[name=s]', 'Enter'); await p.waitForLoadState('load');
  t('English search keeps language and returns results', /s=lift/.test(p.url()) && /lang=en/.test(p.url()) && await p.evaluate(() => document.documentElement.lang === 'en' && document.querySelectorAll('.aladdin-listing article').length > 0), p.url());
  await p.goto(B + '/?s=zzzznotfound'); t('empty search page renders', await p.evaluate(() => !!document.querySelector('.aladdin-listing')));

  // FAQ + keyboard
  await p.goto(B + '/'); const d = p.locator('.faq-section details').nth(1); await d.locator('summary').scrollIntoViewIfNeeded(); await d.locator('summary').click();
  t('FAQ details opens', await d.evaluate(e => e.open));
  await p.goto(B + '/'); await p.keyboard.press('Tab');
  t('first Tab stop is the skip link', await p.evaluate(() => document.activeElement.classList.contains('sr-only') && document.activeElement.getAttribute('href') === '#content'));
  t('no JS errors in desktop session', p.errs.length === 0, p.errs.join('|'));
  await c.close();

  // 404 + placeholder redirect
  [c, p] = await mk(1440);
  const r404 = await p.goto(B + '/no-such-page-xyz/'); t('404 status + template', r404.status() === 404 && await p.evaluate(() => !!document.querySelector('.aladdin-404')));
  const r2 = await p.goto(B + '/en/articles/'); t('placeholder /en/articles/ redirects to /en/blog-2/', p.url() === B + '/en/blog-2/', p.url());
  await c.close(); await b.close();
  console.log(`interact: ${res.filter(Boolean).length}/${res.length} passed`);
})();
