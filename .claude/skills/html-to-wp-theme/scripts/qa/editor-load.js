const { chromium } = require('/opt/node22/lib/node_modules/playwright');
const B = 'http://localhost:' + (process.env.PORT || 8083);
const cookieStr = process.argv[2]; const pageId = process.argv[3]; const shot = process.argv[4] || 'editor';
(async () => {
  const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' });
  const ctx = await b.newContext({ viewport: { width: 1440, height: 900 } });
  await ctx.addCookies(cookieStr.split('; ').map(c => { const i = c.indexOf('='); return { name: c.slice(0, i), value: c.slice(i + 1), domain: 'localhost', path: '/' }; }));
  const p = await ctx.newPage();
  const errs = [];
  p.on('pageerror', e => errs.push('pageerror: ' + e.message.slice(0, 200)));
  p.on('console', m => { if (m.type() === 'error') errs.push('console: ' + m.text().slice(0, 200)); });
  await p.route(/googleusercontent|gravatar|elementor\.com|wordpress\.org|google/, r => r.abort());
  const t0 = Date.now();
  await p.goto(B + '/wp-admin/post.php?post=' + pageId + '&action=elementor', { waitUntil: 'domcontentloaded', timeout: 120000 });
  let ok = false;
  try {
    const guest = p.getByText('Continue as a guest');
    await guest.waitFor({ timeout: 15000 });
    await guest.click();
    await p.waitForLoadState('domcontentloaded');
  } catch (e) { /* no onboarding screen */ }
  if (!/action=elementor/.test(p.url())) { await p.goto(B + '/wp-admin/post.php?post=' + pageId + '&action=elementor', { waitUntil: 'domcontentloaded', timeout: 120000 }); }
  try { await p.waitForSelector('#elementor-panel', { timeout: 90000 }); ok = true; } catch (e) { errs.push('panel not found: ' + e.message.slice(0, 100)); }
  let previewOk = false, widgetsInPreview = 0, lsWidgets = 0, catFound = false;
  try {
    const frame = await (await p.waitForSelector('#elementor-preview-iframe', { timeout: 60000 })).contentFrame();
    await frame.waitForSelector('.elementor-element', { timeout: 60000 });
    await p.waitForTimeout(3000);
    widgetsInPreview = await frame.locator('.elementor-widget').count();
    previewOk = widgetsInPreview > 0;
  } catch (e) { errs.push('preview: ' + e.message.slice(0, 120)); }
  try {
    await p.waitForSelector('#elementor-panel-elements-search-input, .elementor-panel-category', { timeout: 30000 });
    await p.waitForTimeout(2000);
    const info = await p.evaluate(() => {
      const cats = [...document.querySelectorAll('.elementor-panel-category')].map(c => ({ id: c.id, title: (c.querySelector('.elementor-panel-category-title') || {}).textContent, n: c.querySelectorAll('.elementor-element').length }));
      const wt = window.elementor && elementor.widgetsCache ? Object.keys(elementor.widgetsCache).filter(k => k.startsWith('ls-')).length : -1;
      return { cats, wt };
    });
    lsWidgets = info.wt;
    catFound = info.cats.filter(c => /larijani|لاریجانی/i.test((c.id || '') + (c.title || '')));
  } catch (e) { errs.push('panel widgets: ' + e.message.slice(0, 120)); }
  await p.screenshot({ path: __dirname + '/' + shot + '.png' });
  console.log(JSON.stringify({ panel: ok, previewOk, widgetsInPreview, panelWidgetTitles: lsWidgets, larijaniCategory: catFound, seconds: (Date.now() - t0) / 1000, errors: errs.slice(0, 15) }, null, 1));
  await b.close();
})();
