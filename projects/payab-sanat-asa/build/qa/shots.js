// Screenshot source (static) vs WordPress at given widths. External fonts/icons are served from the theme's local copy.
// usage: PORT_SRC=8092 PORT_WP=8091 node shots.js OUT_DIR '{"home":["/","/"]}' 1440,390   (route pair: [srcPath, wpPath])
const { chromium } = require('/opt/node22/lib/node_modules/playwright');
const fs = require('fs');
const out = process.argv[2]; const routes = JSON.parse(process.argv[3]); const widths = (process.argv[4] || '1440,390').split(',').map(Number);
const THEME = '/home/user/aladdinthem/projects/payab-sanat-asa/theme/payab-sanat-asa/assets';
const dark = process.env.DARK === '1';
(async () => {
  const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium', args: ['--no-sandbox'] });
  for (const w of widths) {
    for (const side of ['src', 'wp']) {
      const ctx = await b.newContext({ viewport: { width: w, height: 900 } });
      const p = await ctx.newPage();
      if (dark) await p.addInitScript(() => localStorage.setItem('payab-theme', 'dark'));
      const errs = [];
      p.on('pageerror', e => errs.push(String(e).slice(0, 160)));
      await p.route(/fonts\.googleapis\.com|cdn\.jsdelivr\.net|fonts\.gstatic\.com|neshan\.org|instagram/, async r => {
        const u = r.request().url();
        if (/fonts\.googleapis/.test(u)) {
          const base = 'http://localhost:' + process.env.PORT_WP + '/wp-content/themes/payab-sanat-asa/assets/fonts/';
          const css = fs.readFileSync(THEME + '/css/fonts.css', 'utf8').replace(/\.\.\/fonts\//g, base);
          return r.fulfill({ status: 200, contentType: 'text/css', body: css });
        }
        if (/bootstrap-icons/.test(u)) {
          const base = 'http://localhost:' + process.env.PORT_WP + '/wp-content/themes/payab-sanat-asa/assets/fonts/';
          const css = fs.readFileSync(THEME + '/css/bootstrap-icons.css', 'utf8').replace(/\.\.\/fonts\//g, base);
          return r.fulfill({ status: 200, contentType: 'text/css', body: css });
        }
        return r.fulfill({ status: 200, contentType: 'text/html', body: '' });
      });
      for (const [n, pair] of Object.entries(routes)) {
        const url = 'http://localhost:' + (side === 'src' ? process.env.PORT_SRC : process.env.PORT_WP) + (side === 'src' ? pair[0] : pair[1]);
        await p.goto(url, { waitUntil: 'load' });
        await p.evaluate(() => document.fonts.ready); await p.waitForTimeout(900);
        await p.addStyleTag({ content: '*,*::before,*::after{transition:none!important;animation:none!important;scroll-behavior:auto!important}' });
        await p.waitForTimeout(250);
        const dir = `${out}/${side}`; fs.mkdirSync(dir, { recursive: true });
        await p.screenshot({ path: `${dir}/${n}-${w}.png`, fullPage: true });
        const m = await p.evaluate(() => ({ ovf: document.documentElement.scrollWidth > document.documentElement.clientWidth + 1, h: document.documentElement.scrollHeight }));
        console.log(side, n, w, JSON.stringify(m), errs.length ? 'JSERR ' + errs.join('|') : '');
      }
      await ctx.close();
    }
  }
  await b.close();
})();
