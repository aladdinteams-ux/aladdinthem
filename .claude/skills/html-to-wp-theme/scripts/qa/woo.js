const { chromium } = require('/opt/node22/lib/node_modules/playwright');
const fs = require('fs');
const B = 'http://localhost:' + (process.env.PORT || 8081);
const PRODUCT = process.argv[2], CAT = process.argv[3];
const res = {}; const ok = (k, v, n) => { res[k] = { status: v ? 'PASS' : 'FAIL', note: n || '' }; console.log((v ? 'PASS ' : 'FAIL ') + k + (n ? ' — ' + n : '')); };
(async () => {
  const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' });
  for (const w of [390, 1280]) {
    const ctx = await b.newContext({ viewport: { width: w, height: 900 } });
    const p = await ctx.newPage();
    const errs = []; p.on('pageerror', e => errs.push(e.message)); p.on('console', m => { if (m.type() === 'error' && !/404|favicon/.test(m.text())) errs.push(m.text()); });
    await p.route(/googleusercontent|gravatar/, r => r.fulfill({ status: 200, contentType: 'image/svg+xml', body: '<svg xmlns="http://www.w3.org/2000/svg"/>' }));
    const check = async (name, url) => {
      const r = await p.goto(B + url, { waitUntil: 'networkidle' });
      const m = await p.evaluate(() => ({ ovf: document.documentElement.scrollWidth > document.documentElement.clientWidth + 1, fatal: /Fatal error|Warning<\/b>|Notice<\/b>/.test(document.body.innerHTML), h1: document.querySelectorAll('h1').length, cards: document.querySelectorAll('.ls-wc-loop-item, .product-item, [data-ls-product]').length }));
      ok(`woo.${name}.${w}`, r.status() === 200 && !m.ovf && !m.fatal, `status ${r.status()} overflow=${m.ovf} php-errors=${m.fatal} h1=${m.h1} items=${m.cards}`);
      return m;
    };
    await check('shop', '/shop/');
    await check('category', CAT);
    await check('search', '/?s=%D9%82%D8%A7%D9%84%D8%A8&post_type=product');
    await check('product', PRODUCT);
    // Add to cart from single product (theme detail form)
    const qty = p.locator('form.cart input[name=quantity], [data-ls-qty] input, input[name=quantity]').first();
    if (await qty.count()) { await qty.fill('2').catch(() => {}); }
    const btn = p.locator('form.cart button[type=submit], button[name=add-to-cart], button.single_add_to_cart_button').first();
    ok(`woo.add-to-cart-button.${w}`, await btn.count() > 0);
    if (await btn.count()) { await Promise.all([p.waitForLoadState('networkidle'), btn.click()]); await p.waitForTimeout(800); }
    const notice = await p.evaluate(() => /سبد|cart/i.test((document.querySelector('.woocommerce-message, .wc-block-components-notice-banner, .woocommerce-notices-wrapper') || {}).innerText || ''));
    ok(`woo.added-notice.${w}`, notice);
    await check('cart', '/cart/');
    const cartItems = await p.evaluate(() => document.querySelectorAll('.wc-block-cart-items__row, .cart_item').length);
    ok(`woo.cart-has-item.${w}`, cartItems > 0, cartItems + ' rows');
    await check('checkout', '/checkout/');
    const hasForm = await p.evaluate(() => !!document.querySelector('.wc-block-checkout, form.checkout'));
    ok(`woo.checkout-renders.${w}`, hasForm);
    if (w === 1280 && hasForm) {
      // Fill block checkout
      const fill = async (sel, v) => { const l = p.locator(sel).first(); if (await l.count()) { await l.fill(v); } };
      await fill('#email', 'qa@example.com');
      await fill('#billing-first_name, #shipping-first_name', 'تست');
      await fill('#billing-last_name, #shipping-last_name', 'کیو‌ای');
      await fill('#billing-address_1, #shipping-address_1', 'خیابان آزمایش ۱');
      await fill('#billing-city, #shipping-city', 'تهران');
      await fill('#billing-postcode, #shipping-postcode', '1234567890');
      await fill('#billing-phone, #shipping-phone', '09121234567');
      await p.waitForTimeout(1500);
      const place = p.locator('.wc-block-components-checkout-place-order-button').first();
      if (await place.count()) {
        await place.click();
        await p.waitForURL(/order-received/, { timeout: 30000 }).catch(() => {});
      }
      ok('woo.place-order', /order-received/.test(p.url()), p.url().replace(B, '').slice(0, 60));
      if (/order-received/.test(p.url())) { const m = await p.evaluate(() => ({ ovf: document.documentElement.scrollWidth > document.documentElement.clientWidth + 1 })); ok('woo.thankyou-no-overflow', !m.ovf); }
      await check('account', '/my-account/');
    }
    ok(`woo.js-errors.${w}`, errs.length === 0, errs.slice(0, 3).join(' | ').slice(0, 220));
    await ctx.close();
  }
  fs.writeFileSync(__dirname + '/woo.json', JSON.stringify(res, null, 1));
  await b.close();
})();
