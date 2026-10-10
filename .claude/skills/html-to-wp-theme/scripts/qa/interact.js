const { chromium } = require('/opt/node22/lib/node_modules/playwright');
const fs = require('fs');
const res = {};
const ok = (k, v, note) => { res[k] = { status: v ? 'PASS' : 'FAIL', note: note || '' }; console.log((v ? 'PASS ' : 'FAIL ') + k + (note ? ' — ' + note : '')); };
(async () => {
  const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' });
  const p = await b.newPage({ viewport: { width: 390, height: 800 } });
  const errs = []; p.on('pageerror', e => errs.push(e.message));
  await p.goto('http://localhost:' + (process.env.PORT || 8080) + '/', { waitUntil: 'networkidle' });
  // Mobile drawer via keyboard
  const burger = p.locator('header button[data-ls-open$="-drawer"]').first();
  await burger.focus(); await p.keyboard.press('Enter'); await p.waitForTimeout(400);
  const drawerId = await burger.getAttribute('data-ls-open');
  ok('menu.open.keyboard', await p.evaluate(id => document.getElementById(id).getAttribute('data-open') === 'true', drawerId));
  ok('menu.aria-expanded', (await burger.getAttribute('aria-expanded')) === 'true');
  ok('menu.dialog-role', (await p.locator('#' + drawerId).getAttribute('role')) === 'dialog');
  ok('menu.focus-moved-inside', await p.evaluate(id => document.getElementById(id).contains(document.activeElement), drawerId));
  for (let i = 0; i < 40; i++) await p.keyboard.press('Tab');
  ok('menu.focus-trapped', await p.evaluate(id => document.getElementById(id).contains(document.activeElement), drawerId));
  await p.keyboard.press('Escape'); await p.waitForTimeout(400);
  ok('menu.escape-closes', await p.evaluate(id => document.getElementById(id).getAttribute('data-open') !== 'true', drawerId));
  ok('menu.focus-restored', await p.evaluate(id => document.activeElement && document.activeElement.getAttribute('data-ls-open') === id, drawerId));
  ok('menu.scroll-unlocked', await p.evaluate(() => !document.body.classList.contains('ls-no-scroll')));
  // Repeated open/close
  for (let i = 0; i < 5; i++) { await burger.click(); await p.waitForTimeout(150); await p.keyboard.press('Escape'); await p.waitForTimeout(150); }
  ok('menu.repeat-open-close', await p.evaluate(id => document.getElementById(id).getAttribute('data-open') !== 'true' && !document.body.classList.contains('ls-no-scroll'), drawerId));
  // Backdrop click closes
  await burger.click(); await p.waitForTimeout(350);
  await p.mouse.click(20, 400); await p.waitForTimeout(350);
  ok('menu.backdrop-closes', await p.evaluate(id => document.getElementById(id).getAttribute('data-open') !== 'true', drawerId));
  // Search modal
  const sbtn = p.locator('header button[data-ls-open$="-search"]').first();
  await sbtn.click(); await p.waitForTimeout(400);
  const sid = await sbtn.getAttribute('data-ls-open');
  ok('search.open', await p.evaluate(id => document.getElementById(id).getAttribute('data-open') === 'true', sid));
  ok('search.input-focused', await p.evaluate(() => document.activeElement && document.activeElement.name === 's'));
  await p.keyboard.type('قالب'); await Promise.all([p.waitForNavigation({ timeout: 15000 }).catch(() => {}), p.keyboard.press('Enter')]);
  ok('search.submits', /[?&]s=/.test(p.url()), p.url().slice(-30));
  // Desktop: dropdown + skip link
  await p.setViewportSize({ width: 1280, height: 900 });
  await p.goto('http://localhost:' + (process.env.PORT || 8080) + '/', { waitUntil: 'networkidle' });
  await p.keyboard.press('Tab');
  ok('skiplink.first-focus', await p.evaluate(() => document.activeElement && /skip-link/.test(document.activeElement.className)));
  ok('skiplink.target-exists', await p.evaluate(() => !!document.getElementById('ls-main')));
  const focusVisible = await p.evaluate(() => { const el = document.querySelector('header nav a'); el.focus(); const cs = getComputedStyle(el); return cs.outlineStyle !== 'none' || cs.boxShadow !== 'none'; });
  ok('focus.visible-nav', focusVisible);
  // Lead form: validation + submit (contact page)
  await p.goto('http://localhost:' + (process.env.PORT || 8080) + '/contact/', { waitUntil: 'networkidle' });
  const form = p.locator('form[data-ls-form]').first();
  await form.locator('button[type=submit]').click(); await p.waitForTimeout(500);
  const invalid = await form.evaluate(f => [...f.querySelectorAll('[required]')].filter(i => !i.checkValidity()).length);
  ok('form.required-validation', invalid > 0, invalid + ' invalid required fields blocked');
  // Fill immediately (under the 3 s minimum): JS must wait and resend by itself.
  const fields = form.locator('input[required]:not([type=checkbox]), textarea[required]');
  const n = await fields.count();
  for (let i = 0; i < n; i++) { const t = await fields.nth(i).getAttribute('type'); await fields.nth(i).fill(t === 'tel' ? '09121234567' : 'تست خودکار QA ' + Date.now()); }
  const sels = form.locator('select[required]'); for (let i = 0; i < await sels.count(); i++) { await sels.nth(i).selectOption({ index: 1 }); }
  const leadResponses = [];
  p.on('response', async r => { if (r.url().includes('admin-ajax.php') && (r.request().postData() || '').includes('larijani_lead')) { leadResponses.push(r.status()); } });
  const okResp = p.waitForResponse(r => r.url().includes('admin-ajax.php') && r.status() === 200 && (r.request().postData() || '').includes('larijani_lead'), { timeout: 15000 });
  await form.locator('button[type=submit]').click();
  await form.locator('button[type=submit]').click({ force: true, timeout: 1000 }).catch(() => {}); // double click
  const resp = await okResp;
  const body = await resp.json().catch(() => ({}));
  ok('form.ajax-submit', body.success === true && /^LS-\d{6}-[A-Z0-9]{6}$/.test(body.data.tracking), 'tracking ' + (body.data && body.data.tracking));
  await p.waitForTimeout(600);
  ok('form.auto-retry-after-min-time', leadResponses.includes(425) || leadResponses[0] === 200, 'statuses ' + leadResponses.join(','));
  ok('form.single-submission', leadResponses.filter(s => s === 200).length === 1, 'statuses ' + leadResponses.join(','));
  ok('form.success-visible', await form.evaluate(f => { const s = f.querySelector('[data-ls-success]'); return !!s && !s.classList.contains('hidden') && getComputedStyle(s).display !== 'none' && /LS-\d{6}-/.test(s.textContent); }));
  ok('form.button-reenabled', await form.evaluate(f => !f.querySelector('[type=submit]').disabled));
  // Request without the signed token (e.g. a bot replaying the 1.3 payload) is rejected.
  const r2 = await p.evaluate(async () => { const fd = new FormData(); fd.append('action', 'ls_lead'); fd.append('ls_ts', '1'); fd.append('fields[name]', 'x'); const r = await fetch('/wp-admin/admin-ajax.php', { method: 'POST', body: fd }); return r.status; });
  ok('form.tokenless-rejected', r2 === 403, 'status ' + r2);
  ok('js.no-pageerrors', errs.length === 0, errs.join(' | ').slice(0, 200));
  fs.writeFileSync(__dirname + '/interact.json', JSON.stringify(res, null, 1));
  await b.close();
})();
