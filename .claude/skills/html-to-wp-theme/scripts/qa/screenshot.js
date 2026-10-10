const { chromium } = require('/opt/node22/lib/node_modules/playwright');
(async () => {
  const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' });
  const p = await b.newPage({ viewport: { width: 1200, height: 900 }, deviceScaleFactor: 1 });
  await p.route(/googleusercontent|gravatar/, r => r.fulfill({ status: 200, contentType: 'image/svg+xml', body: '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"><rect width="10" height="10" fill="#9aa39a"/></svg>' }));
  await p.goto('http://localhost:8083/', { waitUntil: 'load' }); await p.waitForTimeout(1500);
  await p.addStyleTag({ content: '#wpadminbar{display:none!important}*{transition:none!important;animation:none!important}' });
  await p.screenshot({ path: process.argv[2], clip: { x: 0, y: 0, width: 1200, height: 900 } });
  await b.close();
})();
