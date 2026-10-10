# 06 — Performance, SEO, accessibility (without changing the look)

## Performance
- Hero/LCP image: eager + `fetchpriority="high"`; everything below the fold `loading="lazy" decoding="async"`; fixed heights/aspect to avoid CLS.
- Preload only the font in use; `font-display: swap`; no Google Fonts/CDN calls.
- One CSS + one JS file, minified, versioned by file mtime (`larijani_asset_ver`), JS `defer`; Woo CSS only on Woo pages.
- View counter: one atomic `UPDATE … meta_value = meta_value + 1` (add on first view), skip logged-in editors, bots, `Sec-Purpose/Purpose: prefetch`, HEAD; filter to disable.
- Cache-friendly forms (token by AJAX on interaction, no nonce in cached HTML). admin-ajax must not be cached.
- Images hotlinked from the design export (e.g. `lh3.googleusercontent.com`): external dependency + visitor privacy + unknown licence → copy to media library on request, add an opt-out switch, and report as a sale blocker until the client supplies licensed images.

## SEO
- Exactly one H1 per page (crawl checks it), semantic sections/headings, breadcrumbs.
- Theme meta description / OG / Organization JSON-LD only when no SEO plugin is active (Yoast, Rank Math, SEOPress, AIOSEO, TSF, Slim SEO) → verify no duplicate tags with a real SEO plugin.
- JSON-LD only from real data (Organization from settings, FAQPage from visible FAQ, escaped so `</script>` can't break out). Never ratings/reviews/fake counts.
- `noindex` on search results; sitemap link in footer menu.

## Accessibility
- Skip link, visible focus, keyboard menus with focus trap / Escape / restore focus, `aria-expanded`, `aria-current`, labels for every input (screen-reader label where the design hides it), alt text, AA contrast (fix low-contrast greys), progress bars with accessible names.
- `prefers-reduced-motion`: smooth scroll only under `no-preference`; global reduce rule inside `.ls-root` (animations/transitions ~0); JS skips counters/scroll animations and uses `behavior:'auto'`.
- RTL everywhere (logical properties, icons mirrored where needed), `lang`/`dir` attributes.
- Third-party content inside article/page content: `max-width:100%` for inputs/selects/textareas/iframes/video (e.g. Contact Form 7 `size="40"` overflowed on mobile).
- Core classes for Theme Check: `.bypostauthor`, `.gallery-caption` (harmless rules).
