const { chromium } = require('/opt/node22/lib/node_modules/playwright');
(async () => {
  const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' });
  const ctx = await b.newContext({ viewport: { width: 1400, height: 1000 } });
  await ctx.addCookies(process.argv[2].split('; ').map(c => { const i = c.indexOf('='); return { name: c.slice(0, i), value: c.slice(i + 1), domain: 'localhost', path: '/' }; }));
  const p = await ctx.newPage();
  await p.goto('http://localhost:8083/wp-admin/admin.php?page=ls-settings', { waitUntil: 'load' });
  await p.screenshot({ path: process.argv[3], fullPage: true });
  console.log(await p.locator('#footer-left').textContent());
  await p.goto('http://localhost:8083/wp-admin/themes.php?theme=larijani-stone', { waitUntil: 'load' });
  await p.waitForTimeout(800);
  console.log((await p.locator('.theme-author').first().textContent().catch(() => 'n/a')).trim());
  await b.close();
})();
