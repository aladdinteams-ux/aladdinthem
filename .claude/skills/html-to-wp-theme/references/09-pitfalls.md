# 09 — Pitfalls already paid for (read before starting)

Contents: 1 Larijani Stone (technical) · 2 Navar Khatar (process) · 3 Never repeat

These are experiences, not guarantees. IDs, counts and versions below belong to those projects only.

## 1. Larijani Stone — technical traps
| Symptom | Cause | Fix |
|---|---|---|
| Edited/re-imported Elementor page still shows old markup | `_elementor_element_cache` / `_elementor_css` / page assets | delete them after every `_elementor_data` save + clear files cache |
| Mobile rows of containers wrap or squash | default flex wrap/grow | `flex_wrap: nowrap` + responsive widths, explicit grow/shrink |
| Fallback (no Elementor) colours wrong | CSS variable inheritance from wrappers | reset variables on fallback wrappers |
| Theme CSS beats the Elementor Style tab | selector specificity / `!important` | scope class, specificity ≤ 0-3-0, margins as `_margin` |
| Blog menu item active on shop/product/projects | WordPress `current_page_parent` on the posts page | own active-state function (references/05) |
| Hero search "does nothing" | tab values ≠ real category slugs; other params ignored; unknown `product_cat` → 404 | real slugs + aliases + filter params + request normalisation |
| Custom dropdown invisible | copied the select's classes after adding the hide class | copy classes first |
| Native `<select>` list can't be rounded/glass | the browser draws the popup | combobox + listbox enhancement (references/10) |
| Admin notices inside a custom page header | WordPress inserts after the first h1 | `<hr class="wp-header-end">` |
| Forms break behind page cache | nonce in cached HTML | signed token fetched on interaction |
| Time-trap bypass | trusted client timestamp / skipped when missing | server-signed issue time |
| Customer files public | `wp_handle_upload` into the media library | private store + admin download handler |
| Re-running import made the real import un-undoable | no-op run overwrote the journal | keep the previous journal when nothing changed |
| Activation changed an existing site | auto-setup on any site | only fresh/empty sites |
| Third-party form overflowed on mobile | `size="40"` inputs | `max-width: 100%` for content form fields |
| POT empty for the plugin | exclusion regex matched the absolute path | apply excludes to the path relative to the root |
| Composer/PHAR downloads fail | proxy blocks dist/phar hosts | Composer `--prefer-source` / git clone; if policy blocks external code, report «مسدود» |
| Bundled library cache never refreshes | wrong `?ver=` string | read the real version from the file header |

## 2. Navar Khatar — process lessons
| Observed | Transferable action |
|---|---|
| Elementor preview differed from the site | extra wrappers and inherited default styles change width, colour, spacing; compare both render paths at the same level (references/11) |
| Complex header/footer | a dedicated editable widget kept the approved structure; normal page content stays editable piece by piece |
| Cramped desktop menu | rebalance brand / menu / CTA and move secondary items into the mega menu per width and language |
| Header slid under the top bar | two independent sticky rows; make top bar + nav one sticky unit (references/10) |
| Old off-canvas panel still opened | new panel CSS alone was not enough; control the hamburger directly, remove old listeners, bust bundle caches |
| Unreadable text in dark mode | buttons and labels were missed; include the specs button and dynamic text in the contrast matrix |
| Transient colour differences | hover and transitions skewed comparisons; compare the same state after transitions end |
| `select` without rounded corners | OS-drawn popup; custom UI that keeps value and accessibility |
| Wide table | scroll inside its wrapper, sensible column widths and cell padding; the page never scrolls horizontally |
| Field glued to its button | explicit gap; don't turn one screenshot's number into a rule for every form |
| Reference theme with 92 options | some were reserved; "92 options" ≠ "92 working features" |
| Migration backup bug | found only by a real test; verify exact data match, custom text, re-run and restore |
| Local test vs real Pro | locally the old path was bypassed, but published old Pro templates still rendered on the real site |
| Old header/footer on the live site | published Pro templates with "entire site" conditions (and post/archive templates) had to be moved to Trash with permission; new output verified |
| Page cleanup | with permission, old pages/drafts moved to Trash; new pages kept — counts are project-specific |
| URL `contact-us-2` | an old slug existed; build links from IDs and real permalinks |
| WordPress "critical error" | the generic message gave no cause; fixing header/footer did not prove all PHP errors were fixed |
| Transient DB connection error | happened once and recovered; without logs it is environment, not theme |
| Per-version tests | some versions had language/menu/migration tests but no real Pro or full PageSpeed; report each version's evidence separately |
| Admin login | "form submitted" ≠ logged in; confirm by seeing the dashboard; never store credentials |

## 3. Never repeat
- Declaring independence from the previous header before a real Pro test.
- Classifying content as old by date alone — check ownership, content, history, links and the replacement list within the permitted scope.
- Publishing previous-version evidence as a new test.
- Putting a whole page in one HTML widget for speed.
- Treating every colour difference as a CSS bug (check hover, transition, font loading first).
- Deleting unrelated data or plugins to fix a display problem.
- Clearing caches instead of fixing an active old template.
- Assuming the root error is fixed because the dashboard opens again.
- Weakening test criteria after a failure.
- Treating the number of settings, pages or tests as a quality measure — real behaviour and recoverability are the measure.
