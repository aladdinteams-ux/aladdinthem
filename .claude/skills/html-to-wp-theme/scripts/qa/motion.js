const { chromium } = require('/opt/node22/lib/node_modules/playwright');
(async () => {
  const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' });
  for (const mode of ['reduce', 'no-preference']) {
    const ctx = await b.newContext({ reducedMotion: mode, viewport: { width: 1280, height: 900 } });
    const p = await ctx.newPage();
    await p.route(/googleusercontent|gravatar/, r => r.abort());
    await p.goto('http://localhost:' + process.env.PORT + '/', { waitUntil: 'load' });
    await p.waitForTimeout(500);
    const r = await p.evaluate(async () => {
      const c = document.querySelector('[data-ls-counter]');
      const before = c ? c.textContent : null;
      if (c) { c.scrollIntoView(); }
      await new Promise(res => setTimeout(res, 150));
      const mid = c ? c.textContent : null;
      const t = document.querySelector('.ls-root .transition-all, .ls-root .transition-colors');
      return { scroll: getComputedStyle(document.documentElement).scrollBehavior, counterStable: before === mid, transition: t ? getComputedStyle(t).transitionDuration : null };
    });
    console.log(mode, JSON.stringify(r));
    await ctx.close();
  }
  await b.close();
})();
