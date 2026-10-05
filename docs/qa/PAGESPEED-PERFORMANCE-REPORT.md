# PageSpeed / Performance Report — Larijani Stone 1.2.0

**Type of data: LAB only (synthetic).** Lighthouse 12.8.2, headless Chromium, default simulated throttling (mobile: Moto G Power / slow 4G; desktop preset). Server: PHP 8.3 built-in development server on localhost — **no gzip/brotli, no HTTP caching, no CDN**, SQLite. Demo images are hot-linked from `lh3.googleusercontent.com`, which is blocked in the sandbox (so image weight is under-reported and LCP is the H1 text).
**Field data (CrUX / real-user Core Web Vitals): NOT EXECUTED** — the site is not public.

## Results (after fixes, 2026-10-05)

| Page | Perf | A11y | Best Pr. | SEO | FCP | LCP | TBT | CLS | Speed Index | Transfer |
|---|---|---|---|---|---|---|---|---|---|---|
| contact-desktop | 99 | 100 | 96 | 100 | 0.6 s | 0.7 s | 0 ms | 0 | 0.6 s | 551 KiB |
| contact-mobile | 84 | 100 | 96 | 100 | 2.9 s | 3.7 s | 0 ms | 0 | 2.9 s | 551 KiB |
| home-desktop | 100 | 100 | 96 | 100 | 0.6 s | 0.7 s | 0 ms | 0 | 0.6 s | 552 KiB |
| home-mobile | 84 | 100 | 96 | 100 | 2.9 s | 3.8 s | 0 ms | 0 | 2.9 s | 552 KiB |
| post-desktop | 98 | 100 | 78 | 100 | 0.8 s | 0.9 s | 0 ms | 0 | 0.8 s | 586 KiB |
| post-mobile | 78 | 100 | 79 | 100 | 3.5 s | 4.2 s | 0 ms | 0.001 | 3.5 s | 586 KiB |
| product-desktop | 100 | 100 | 96 | 100 | 0.6 s | 0.7 s | 0 ms | 0 | 0.6 s | 554 KiB |
| product-mobile | 84 | 100 | 96 | 100 | 2.9 s | 3.8 s | 0 ms | 0 | 2.9 s | 554 KiB |
| store-desktop | 100 | 100 | 96 | 100 | 0.6 s | 0.7 s | 0 ms | 0 | 0.6 s | 557 KiB |
| store-mobile | 84 | 100 | 96 | 100 | 2.9 s | 3.7 s | 0 ms | 0.001 | 2.9 s | 557 KiB |

Best-practices < 100 is caused by the sandbox: blocked external demo images (console network errors) and Gravatar over `http` on the http-only sandbox (post page).

## Interpretation
- **CLS ≈ 0 and TBT = 0 ms on every page** → layout stability and main-thread work: PASS.
- **Mobile LCP 3.7–4.2 s (lab) is above the 2.5 s "good" threshold** → WARN. Root cause in the lab: render-blocking CSS sent **uncompressed** by the dev server (Tailwind 85 KB + Bootstrap Icons 87 KB) plus fonts (Vazirmatn 111 KB, icons 134 KB). With gzip/brotli the CSS shrinks by roughly 75–85 % on typical hosts; this was not measured here.

## Optimisations in 1.2.0
- `theme.min.js` (15 KB) / `admin.min.js`, loaded with `defer` in the footer.
- Block-library / global-styles CSS dequeued on Elementor-built pages without blocks (`ls_dequeue_block_styles` filter).
- Tailwind purged & minified (85 KB raw); Vazirmatn is a single variable WOFF2 with `font-display: swap`; fonts/icons self-hosted (no third-party CDN).
- Images use `loading="lazy"` except above-the-fold hero images.

## Remaining WARN / recommendations
1. Enable gzip/brotli + long cache headers on the server (Lighthouse `uses-text-compression`, `uses-long-cache-ttl`) — hosting config.
2. Bootstrap Icons: the full icon CSS (87 KB) and font (134 KB, `font-display: block`) load on every page. A subset build would cut ~200 KB → WARN (architecture).
3. Replace hot-linked demo images with optimised local WebP/AVIF (the setup's WP-Cron import does this when the server can reach Google's image host).
4. Use a page cache plugin; re-measure with PageSpeed Insights on the live domain for field data.

**Performance Status: WARN** (desktop lab 98–100, CLS/TBT excellent; mobile lab LCP above target on an uncompressed dev server; field data not available).
