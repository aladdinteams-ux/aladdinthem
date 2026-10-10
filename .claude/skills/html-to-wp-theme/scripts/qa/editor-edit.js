const { chromium } = require('/opt/node22/lib/node_modules/playwright');
const B = 'http://localhost:' + (process.env.PORT || 8083);
const cookieStr = process.argv[2]; const pageId = process.argv[3];
(async () => {
  const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' });
  const ctx = await b.newContext({ viewport: { width: 1440, height: 900 } });
  await ctx.addCookies(cookieStr.split('; ').map(c => { const i = c.indexOf('='); return { name: c.slice(0, i), value: c.slice(i + 1), domain: 'localhost', path: '/' }; }));
  const p = await ctx.newPage();
  const errs = []; p.on('pageerror', e => errs.push(e.message.slice(0, 160)));
  await p.route(/googleusercontent|gravatar|elementor\.com|wordpress\.org|google/, r => r.abort());
  await p.goto(B + '/wp-admin/post.php?post=' + pageId + '&action=elementor', { waitUntil: 'domcontentloaded', timeout: 120000 });
  await p.waitForSelector('#elementor-panel', { timeout: 90000 });
  const frame = await (await p.waitForSelector('#elementor-preview-iframe')).contentFrame();
  await frame.waitForSelector('.elementor-widget-heading', { timeout: 60000 });
  await p.waitForTimeout(2000);
  const res = {};
  // 1) Native heading: select via the editor API and change its title through the control.
  res.headingTitleBefore = await frame.evaluate(() => { const h = [...document.querySelectorAll('.elementor-widget-heading .elementor-heading-title')].find(e => /پیشگام|ویرایش‌شده/.test(e.textContent)); return h ? h.textContent.trim() : null; });
  const target = frame.locator('.elementor-widget-heading', { hasText: /پیشگام|ویرایش‌شده/ }).first();
  await target.scrollIntoViewIfNeeded();
  await target.click({ position: { x: 20, y: 10 } });
  await p.waitForTimeout(1500);
  res.panelTitle = await p.evaluate(() => (document.querySelector('#elementor-panel-header-title') || {}).textContent || '');
  const ta = p.locator('.elementor-control-title textarea, .elementor-control-title input').first();
  res.titleControlFound = await ta.count() > 0;
  if (res.titleControlFound) {
    await ta.fill('عنوان ویرایش‌شده در المنتور ' + Date.now());
    await p.waitForTimeout(1200);
    res.previewUpdated = await frame.evaluate(() => !![...document.querySelectorAll('.elementor-heading-title')].find(e => /ویرایش‌شده در المنتور/.test(e.textContent)));
  }
  // 2) Style tab exists for the native heading (typography/colour controls).
  await p.locator('.elementor-tab-control-style a, [data-tab="style"]').first().click().catch(() => {});
  await p.waitForTimeout(800);
  res.styleControls = await p.locator('.elementor-control-title_color, .elementor-control-typography_typography').count();
  // 3) Save (publish).
  const saveResp = p.waitForResponse(r => /admin-ajax\.php/.test(r.url()) && r.request().method() === 'POST' && /save_builder/.test(r.request().postData() || ''), { timeout: 30000 }).catch(() => null);
  await p.evaluate(() => window.$e.run('document/save/publish'));
  const r = await saveResp;
  res.saveStatus = r ? r.status() : 'no-save-request';
  res.saveOk = r ? /"success":true/.test(await r.text()) : false;
  await p.waitForTimeout(2000);
  // 4) Container: select a native card container via its handle and check layout controls.
  const cont = await frame.evaluate(() => !!document.querySelector('.e-con.e-grid'));
  res.gridContainerPresent = cont;
  res.errors = errs;
  await p.screenshot({ path: __dirname + '/editor-edit.png' });
  await b.close();
  const html = await (await fetch(B + (process.env.FRONT || '/') + '?nocache=' + Date.now())).text();
  res.frontendHasEdit = /عنوان ویرایش‌شده در المنتور/.test(html);
  console.log(JSON.stringify(res, null, 1));
})();
