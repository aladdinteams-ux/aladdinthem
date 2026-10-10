const { chromium } = require('/opt/node22/lib/node_modules/playwright');
const routes = require(process.argv[2]); const widths = (process.argv[3] || '390,1440').split(',').map(Number);
(async () => {
  const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' });
  let bad = 0;
  for (const w of widths) {
    const p = await b.newPage({ viewport: { width: w, height: 900 } });
    const errs = []; p.on('pageerror', e => errs.push(e.message.slice(0, 120)));
    await p.route(/googleusercontent|gravatar/, r => r.fulfill({ status: 200, contentType: 'image/svg+xml', body: '<svg xmlns="http://www.w3.org/2000/svg"/>' }));
    for (const [n, u] of Object.entries(routes)) {
      errs.length = 0;
      const r = await p.goto('http://localhost:' + (process.env.PORT || 8083) + u, { waitUntil: 'load' });
      const m = await p.evaluate(() => ({ ovf: document.documentElement.scrollWidth > document.documentElement.clientWidth + 1, php: /Fatal error|Warning<\/b>|Notice<\/b>|Deprecated<\/b>/.test(document.body.innerHTML), h1: document.querySelectorAll('h1').length, lang: document.documentElement.lang, links404: 0 }));
      const ok = (r.status() === 200 || (n === '404' && r.status() === 404)) && !m.ovf && !m.php && m.h1 === 1 && errs.length === 0;
      if (!ok) bad++;
      console.log((ok ? 'PASS ' : 'FAIL ') + w + ' ' + n + ' status=' + r.status() + ' ovf=' + m.ovf + ' php=' + m.php + ' h1=' + m.h1 + (errs.length ? ' js=' + errs.join(';') : ''));
    }
    await p.close();
  }
  console.log('bad=' + bad);
  await b.close();
})();
