const { chromium } = require('/opt/node22/lib/node_modules/playwright');
const B = 'http://localhost:' + process.env.PORT;
(async () => {
  const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' });
  const p = await b.newPage({ viewport: { width: Number(process.env.W || 1280), height: 900 } });
  const errs = []; p.on('pageerror', e => errs.push(e.message));
  await p.route(/googleusercontent|gravatar/, r => r.fulfill({ status: 200, contentType: 'image/svg+xml', body: '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"><rect width="10" height="10" fill="#9aa39a"/></svg>' }));
  await p.goto(B + '/', { waitUntil: 'load' });
  const form = p.locator('form[data-ls-hero-search]');
  const btns = form.locator('.ls-dd-btn');
  console.log('dropdowns', await btns.count(), 'native hidden', await form.locator('select.ls-dd-native').count());
  // Tab 2 (machinery) then open first dropdown
  await form.locator('[data-ls-search-tab]').nth(Number(process.env.TAB || 0)).click();
  await btns.nth(0).click();
  await p.waitForTimeout(300);
  const opts = await form.locator('.ls-dd-list:not([hidden]) [role=option]').allTextContents();
  console.log('options', JSON.stringify(opts.map(t => t.trim())));
  await form.locator('.ls-dd').nth(0).screenshot({ path: process.argv[2] + '-closed.png' }).catch(()=>{});
  const box = await form.boundingBox();
  await p.screenshot({ path: process.argv[2] + '-open.png', clip: { x: Math.max(0, box.x - 20), y: box.y - 10, width: Math.min(box.width + 40, 1280), height: box.height + 320 } });
  // keyboard: arrow down + enter selects next
  await p.keyboard.press('ArrowDown'); await p.keyboard.press('Enter');
  console.log('aria-expanded after enter', await btns.nth(0).getAttribute('aria-expanded'), 'value', await form.locator('select').nth(0).inputValue());
  const pick = process.env.PICK ? JSON.parse(process.env.PICK) : null;
  if (pick) { for (const [i, v] of pick) { await form.locator('select').nth(i).selectOption(v, { force: true }); } }
  await Promise.all([p.waitForNavigation(), form.locator('button[type=submit]').click()]);
  const url = decodeURIComponent(p.url());
  const res = await p.evaluate(() => ({
    title: document.title,
    cards: document.querySelectorAll('[data-ls-catalog-grid] > *').length,
    visible: [...document.querySelectorAll('.ls-filter-item')].filter(e => e.style.display !== 'none').length,
    chips: [...document.querySelectorAll('[data-ls-active-filters] a')].map(a => a.textContent.trim()),
    count: (document.querySelector('[data-ls-filter-count]') || {}).textContent,
    empty: !!document.querySelector('[data-ls-empty].flex'),
    names: [...document.querySelectorAll('[data-ls-catalog-grid] > *')].filter(e => e.style.display !== 'none').map(e => (e.querySelector('h3,h2,h4')||e).textContent.trim().slice(0,40)),
  }));
  console.log('url', url); console.log(JSON.stringify(res, null, 0)); console.log('errors', errs);
  await b.close();
})();
