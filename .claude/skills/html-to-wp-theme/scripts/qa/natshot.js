const { chromium } = require('/opt/node22/lib/node_modules/playwright');
const fs = require('fs');
const out = process.argv[2]; const routes = JSON.parse(process.argv[3]); const widths = (process.argv[4] || '390,1440').split(',').map(Number);
(async () => {
  const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' });
  for (const w of widths) {
    const p = await b.newPage({ viewport: { width: w, height: 900 } });
    await p.route(/googleusercontent|gravatar/, r => r.fulfill({ status: 200, contentType: 'image/svg+xml', body: '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"><rect width="10" height="10" fill="#9aa39a"/></svg>' }));
    for (const [n, u] of Object.entries(routes)) {
      await p.goto('http://localhost:' + (process.env.PORT || 8083) + u, { waitUntil: 'load' }); await p.waitForTimeout(1200);
      await p.addStyleTag({ content: '*,*::before,*::after{transition:none!important;animation:none!important}' });
      await p.evaluate(() => document.querySelectorAll('[data-to-value]').forEach(e => e.textContent = e.getAttribute('data-to-value')));
      await p.waitForTimeout(300);
      await p.screenshot({ path: `${out}/${n}-${w}.png`, fullPage: true });
      const m = await p.evaluate(() => ({ ovf: document.documentElement.scrollWidth > document.documentElement.clientWidth + 1, h: document.documentElement.scrollHeight }));
      console.log(n, w, JSON.stringify(m));
    }
    await p.close();
  }
  await b.close();
})();
