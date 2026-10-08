/**
 * Larijani Stone – front-end behaviour (vanilla JS, no dependencies).
 * Works on normal pages and inside the Elementor editor preview.
 */
(function () {
	'use strict';

	var CFG = window.LarijaniTheme || { ajaxUrl: '/wp-admin/admin-ajax.php', i18n: {} };
	var T = CFG.i18n || {};
	var FA = '۰۱۲۳۴۵۶۷۸۹';

	function toFa(str) {
		return String(str).replace(/[0-9]/g, function (d) { return FA[d]; });
	}
	function toEn(str) {
		return String(str).replace(/[۰-۹]/g, function (d) { return FA.indexOf(d); }).replace(/[٠-٩]/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'.indexOf(d); });
	}
	function faNumber(n, decimals) {
		var v = Number(n);
		if (!isFinite(v)) { return ''; }
		var s = decimals ? v.toFixed(decimals).replace(/\.?0+$/, '') : Math.round(v).toString();
		var parts = s.split('.');
		parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
		return toFa(parts.join('.'));
	}
	function reducedMotion() {
		return !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
	}
	function $all(root, sel) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); }
	function once(el, key) {
		var k = 'lsInit' + key;
		if (el.dataset[k]) { return false; }
		el.dataset[k] = '1';
		return true;
	}
	function swapClasses(el, on) {
		var add = (on ? el.getAttribute('data-on') : el.getAttribute('data-off')) || '';
		var rem = (on ? el.getAttribute('data-off') : el.getAttribute('data-on')) || '';
		rem.split(/\s+/).forEach(function (c) { if (c) { el.classList.remove(c); } });
		add.split(/\s+/).forEach(function (c) { if (c) { el.classList.add(c); } });
	}

	/* ------------------------------------------------------------------
	 * Drawers, search modal.
	 * ---------------------------------------------------------------- */
	var openId = null;
	var lastOpener = null;
	var FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
	function openPanel(id, opener) {
		var panel = document.getElementById(id);
		if (!panel) { return; }
		closePanel(true);
		lastOpener = opener || document.activeElement;
		panel.setAttribute('data-open', 'true');
		$all(document, '[data-ls-backdrop="' + id + '"]').forEach(function (b) { b.setAttribute('data-open', 'true'); });
		$all(document, '[data-ls-open="' + id + '"]').forEach(function (b) { b.setAttribute('aria-expanded', 'true'); });
		document.body.classList.add('ls-no-scroll');
		openId = id;
		var focus = panel.querySelector('[data-ls-autofocus]') || panel.querySelector('a, button, input');
		if (focus) { setTimeout(function () { focus.focus(); }, 250); }
	}
	function closePanel(silent) {
		if (!openId) { return; }
		var panel = document.getElementById(openId);
		if (panel) { panel.setAttribute('data-open', 'false'); }
		$all(document, '[data-ls-backdrop="' + openId + '"]').forEach(function (b) { b.setAttribute('data-open', 'false'); });
		$all(document, '[data-ls-open="' + openId + '"]').forEach(function (b) { b.setAttribute('aria-expanded', 'false'); });
		document.body.classList.remove('ls-no-scroll');
		openId = null;
		// Return focus to the control that opened the panel (WCAG 2.4.3).
		if (!silent && lastOpener && typeof lastOpener.focus === 'function' && document.contains(lastOpener)) { lastOpener.focus(); }
		lastOpener = null;
	}
	document.addEventListener('click', function (e) {
		var opener = e.target.closest('[data-ls-open]');
		if (opener) { e.preventDefault(); openPanel(opener.getAttribute('data-ls-open'), opener); return; }
		if (e.target.closest('[data-ls-close]') && openId) {
			closePanel();
			return;
		}
		if (e.target.closest('[data-ls-backdrop]')) { closePanel(); }
	});
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') { closePanel(); return; }
		// Keep keyboard focus inside an open drawer / dialog.
		if (e.key === 'Tab' && openId) {
			var panel = document.getElementById(openId);
			if (!panel) { return; }
			var items = $all(panel, FOCUSABLE).filter(function (el) { return el.offsetWidth || el.offsetHeight || el.getClientRects().length; });
			if (!items.length) { return; }
			var first = items[0], last = items[items.length - 1];
			if (!panel.contains(document.activeElement)) { e.preventDefault(); first.focus(); }
			else if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
			else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
		}
	});

	/* ------------------------------------------------------------------
	 * AJAX lead forms.
	 * ---------------------------------------------------------------- */
	// Signed single-use token (Larijani Stone Core). Fetched on the first
	// interaction, so full-page caches never serve a stale one.
	function fetchToken(form) {
		var input = form.querySelector('[data-ls-token]');
		if (!input) { return Promise.resolve(); }
		if (input.value) { return Promise.resolve(); }
		if (form._lsTokenReq) { return form._lsTokenReq; }
		var body = new FormData();
		body.set('action', 'larijani_form_token');
		form._lsTokenReq = fetch(CFG.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' })
			.then(function (r) { return r.json(); })
			.then(function (res) { if (res && res.success && res.data) { input.value = res.data.token; } })
			.catch(function () {})
			.then(function () { form._lsTokenReq = null; });
		return form._lsTokenReq;
	}
	function initForms(root) {
		$all(root, 'form[data-ls-form]').forEach(function (form) {
			if (!once(form, 'Form')) { return; }
			$all(form, '[data-ls-file]').forEach(function (input) {
				input.addEventListener('change', function () {
					var out = input.parentNode.querySelector('[data-ls-file-names]');
					if (out) { out.textContent = Array.prototype.map.call(input.files, function (f) { return f.name; }).join('، '); }
				});
			});
			['focusin', 'pointerdown', 'touchstart'].forEach(function (ev) {
				form.addEventListener(ev, function () { fetchToken(form); }, { passive: true });
			});
			var err = form.querySelector('[data-ls-error]');
			var ok = form.querySelector('[data-ls-success]');
			var btn = form.querySelector('[type="submit"]');
			function showError(msg) {
				if (err) { err.textContent = msg || T.error || 'Error'; err.classList.remove('hidden'); }
			}
			function done() {
				form.classList.remove('is-loading');
				form.removeAttribute('aria-busy');
				form._lsBusy = false;
				if (btn) { btn.disabled = false; }
			}
			function send(retries) {
				var data = new FormData(form);
				if (!data.get('action')) { data.set('action', 'larijani_lead'); }
				data.set('ls_page', window.location.href);
				$all(form, 'input[type="tel"]').forEach(function (f) { if (f.name) { data.set(f.name, toEn(f.value)); } });
				fetch(CFG.ajaxUrl, { method: 'POST', body: data, credentials: 'same-origin' })
					.then(function (r) { return r.json().catch(function () { return { success: false }; }); })
					.then(function (res) {
						var info = (res && res.data) || {};
						var tokenEl = form.querySelector('[data-ls-token]');
						if (!res || !res.success) {
							// Minimum fill-in time not reached yet: wait and resend once.
							if (info.code === 'too_fast' && retries > 0) {
								setTimeout(function () { send(retries - 1); }, (Math.max(1, info.retry || 1) * 1000) + 250);
								return;
							}
							// Token expired (e.g. tab left open for hours): get a new one and resend.
							if (info.code === 'expired' && tokenEl && retries > 0) {
								tokenEl.value = '';
								fetchToken(form).then(function () { send(retries - 1); });
								return;
							}
							done();
							showError(info.message);
							if (info.field) {
								var bad = form.querySelector('[name="fields[' + info.field + ']"], [name="fields[' + info.field + '][]"]');
								if (bad) { bad.focus(); }
							}
							return;
						}
						done();
						if (tokenEl) { tokenEl.value = ''; } // Single use: the next interaction fetches a new one.
						var tracking = info.tracking || '';
						if (ok) {
							$all(ok, '[data-ls-success-text]').forEach(function (s) {
								s.textContent = s.getAttribute('data-ls-success-text').replace('{tracking}', tracking);
							});
							ok.classList.remove('hidden');
							if (ok.classList.contains('items-start') || ok.classList.contains('gap-space-sm')) { ok.classList.add('flex'); }
							ok.scrollIntoView({ behavior: reducedMotion() ? 'auto' : 'smooth', block: 'nearest' });
						}
						form.reset();
						$all(form, '[data-ls-file-names]').forEach(function (n) { n.textContent = ''; });
						if (btn && form.querySelectorAll('input:not([type=hidden])').length === 1) { btn.disabled = true; }
						document.dispatchEvent(new CustomEvent('ls:lead', { detail: { form: form, tracking: tracking } }));
					})
					.catch(function () { done(); showError(); });
			}
			form.addEventListener('submit', function (e) {
				e.preventDefault();
				if (form._lsBusy) { return; } // Prevent double submission.
				if (err) { err.classList.add('hidden'); err.textContent = ''; }
				var missing = $all(form, '[required]').filter(function (f) {
					if (f.type === 'checkbox') { return !f.checked; }
					return !String(f.value || '').trim();
				});
				if (missing.length) {
					missing[0].focus();
					showError(T.required || 'Required');
					return;
				}
				form._lsBusy = true;
				form.classList.add('is-loading');
				form.setAttribute('aria-busy', 'true');
				if (btn) { btn.disabled = true; }
				fetchToken(form).then(function () { send(2); });
			});
		});
	}

	/* ------------------------------------------------------------------
	 * Client-side filtering (portfolio, manual catalog).
	 * ---------------------------------------------------------------- */
	function applyFilters(scope) {
		var activeBtn = scope.querySelector('[data-ls-filter][aria-pressed="true"]');
		var cat = activeBtn ? activeBtn.getAttribute('data-ls-filter') : 'all';
		var searchEl = scope.querySelector('[data-ls-search-input]');
		var q = searchEl && scope.getAttribute('data-ls-catalog') === 'client' ? searchEl.value.trim().toLowerCase() : '';
		var checked = $all(scope, '[data-ls-check-filter]:checked').map(function (c) { return c.value; }).filter(Boolean);
		var range = scope.querySelector('[data-ls-price-range]');
		var maxPrice = range && range.dataset.applied ? parseFloat(range.dataset.applied) : Infinity;
		var count = 0;
		$all(scope, '.ls-filter-item').forEach(function (item) {
			var cats = (item.getAttribute('data-category') || '').split(/\s+/);
			var title = (item.getAttribute('data-title') || item.textContent || '').toLowerCase();
			var ok = (cat === 'all' || cats.indexOf(cat) !== -1) &&
				(!q || title.indexOf(q) !== -1) &&
				(!checked.length || checked.some(function (c) { return cats.indexOf(c) !== -1; })) &&
				(parseFloat(item.getAttribute('data-price') || '0') <= maxPrice);
			item.style.display = ok ? '' : 'none';
			if (ok) { count++; }
		});
		var counter = scope.querySelector('[data-ls-filter-count]');
		if (counter) { counter.textContent = toFa(count) + ' ' + (scope.getAttribute('data-ls-count-suffix') || T.projects || ''); }
	}
	function initAutoSubmit(root) {
		$all(root, '[data-ls-autosubmit]').forEach(function (el) {
			if (!once(el, 'AutoSubmit')) { return; }
			el.addEventListener('change', function () { if (el.form) { el.form.submit(); } });
		});
	}
	function initFilters(root) {
		$all(root, '[data-ls-filter-scope], [data-ls-catalog="client"]').forEach(function (scope) {
			if (!once(scope, 'Filter')) { return; }
			$all(scope, '[data-ls-filter]').forEach(function (btn) {
				btn.addEventListener('click', function () {
					$all(scope, '[data-ls-filter]').forEach(function (b) { b.setAttribute('aria-pressed', 'false'); swapClasses(b, false); });
					btn.setAttribute('aria-pressed', 'true');
					swapClasses(btn, true);
					applyFilters(scope);
				});
			});
			$all(scope, '[data-ls-check-filter]').forEach(function (c) { c.addEventListener('change', function () { applyFilters(scope); }); });
			var range = scope.querySelector('[data-ls-price-range]');
			if (range) {
				var label = scope.querySelector('[data-ls-price-max]');
				range.addEventListener('input', function () {
					if (label) { label.textContent = toFa(Number(range.value).toLocaleString('en-US')); }
				});
				var apply = scope.querySelector('[data-ls-price-apply]');
				if (apply) {
					apply.addEventListener('click', function () {
						range.dataset.applied = range.value >= parseFloat(range.max) ? '' : range.value;
						applyFilters(scope);
					});
				}
			}
			if (scope.getAttribute('data-ls-catalog') === 'client') {
				var input = scope.querySelector('[data-ls-search-input]');
				var form = scope.querySelector('[data-ls-catalog-search]');
				if (form) { form.addEventListener('submit', function (e) { e.preventDefault(); applyFilters(scope); }); }
				if (input) { input.addEventListener('input', function () { applyFilters(scope); }); }
				var grid = scope.querySelector('[data-ls-catalog-grid]');
				var original = grid ? $all(grid, '.ls-filter-item') : [];
				var sortItems = function (mode) {
					var items = original.slice();
					if (mode === 'price') { items.sort(function (a, b) { return a.getAttribute('data-price') - b.getAttribute('data-price'); }); }
					if (mode === 'price-desc') { items.sort(function (a, b) { return b.getAttribute('data-price') - a.getAttribute('data-price'); }); }
					if (mode === 'title') { items.sort(function (a, b) { return (a.getAttribute('data-title') || '').localeCompare(b.getAttribute('data-title') || '', 'fa'); }); }
					if (mode === 'date') { items.reverse(); }
					items.forEach(function (i) { grid.appendChild(i); });
				};
				$all(scope, '[data-ls-sort]').forEach(function (btn) {
					btn.setAttribute('data-on', 'bg-primary-container text-on-primary');
					btn.setAttribute('data-off', 'text-on-surface-variant hover:text-on-surface');
					btn.addEventListener('click', function () {
						$all(scope, '[data-ls-sort]').forEach(function (b) { swapClasses(b, false); });
						swapClasses(btn, true);
						sortItems(btn.getAttribute('data-ls-sort'));
					});
				});
				$all(scope, '[data-ls-sort-select]').forEach(function (sel) {
					sel.addEventListener('change', function () { sortItems(sel.value); });
				});
			}
			// Deep link from menus: ?ls_cat=<filter key> pre-selects the category.
			var wanted = '';
			try { wanted = new URLSearchParams(window.location.search).get('ls_cat') || ''; } catch (e) { wanted = ''; }
			if (wanted) {
				var target = $all(scope, '[data-ls-filter]').filter(function (b) { return b.getAttribute('data-ls-filter') === wanted; })[0];
				var box = $all(scope, '[data-ls-check-filter]').filter(function (c) { return c.value === wanted; })[0];
				if (target) { target.click(); } else if (box) { box.checked = true; applyFilters(scope); }
			}
		});
	}

	/* ------------------------------------------------------------------
	 * Tabs, accordions, hero search tabs.
	 * ---------------------------------------------------------------- */
	function activateTab(wrap, key) {
		$all(wrap, '[data-ls-tab]').forEach(function (b) {
			var on = b.getAttribute('data-ls-tab') === key;
			b.setAttribute('aria-selected', on ? 'true' : 'false');
			swapClasses(b, on);
		});
		$all(wrap, '[data-ls-pane]').forEach(function (p) {
			var on = p.getAttribute('data-ls-pane') === key;
			p.classList.toggle('hidden', !on);
			p.classList.toggle('flex', on);
		});
	}
	function initTabs(root) {
		$all(root, '[data-ls-tabs]').forEach(function (wrap) {
			if (!once(wrap, 'Tabs')) { return; }
			$all(wrap, '[data-ls-tab]').forEach(function (b) {
				b.addEventListener('click', function () { activateTab(wrap, b.getAttribute('data-ls-tab')); });
			});
		});
		$all(root, '[data-ls-tab-open]').forEach(function (a) {
			if (!once(a, 'TabOpen')) { return; }
			a.addEventListener('click', function () {
				var wrap = document.querySelector('[data-ls-tabs]');
				if (wrap) { activateTab(wrap, a.getAttribute('data-ls-tab-open')); }
			});
		});
		$all(root, '[data-ls-acc]').forEach(function (btn) {
			if (!once(btn, 'Acc')) { return; }
			btn.addEventListener('click', function () {
				var item = btn.closest('.ls-acc-item');
				var open = item.getAttribute('data-open') === 'true';
				item.setAttribute('data-open', open ? 'false' : 'true');
				btn.setAttribute('aria-expanded', open ? 'false' : 'true');
			});
		});
		$all(root, '[data-ls-hero-search]').forEach(function (form) {
			if (!once(form, 'Hero')) { return; }
			var hidden = form.querySelector('[data-ls-tab-input]');
			$all(form, '[data-ls-search-tab]').forEach(function (b) {
				b.addEventListener('click', function () {
					$all(form, '[data-ls-search-tab]').forEach(function (x) { swapClasses(x, false); x.setAttribute('aria-pressed', 'false'); });
					swapClasses(b, true);
					b.setAttribute('aria-pressed', 'true');
					if (hidden) { hidden.value = b.getAttribute('data-ls-search-tab'); }
				});
			});
		});
	}

	/* ------------------------------------------------------------------
	 * Product page: gallery, quantity calculator, wishlist.
	 * ---------------------------------------------------------------- */
	function initProduct(root) {
		$all(root, '[data-ls-product]').forEach(function (wrap) {
			if (!once(wrap, 'Product')) { return; }
			var unit = parseFloat(wrap.getAttribute('data-unit')) || 0;
			var bulk = parseFloat(wrap.getAttribute('data-bulk')) || 0;
			var bulkMin = parseInt(wrap.getAttribute('data-bulk-min'), 10) || 0;
			var area = parseFloat(wrap.getAttribute('data-area')) || 0;
			var input = wrap.querySelector('[data-ls-qty-input]');
			function recalc() {
				if (!input) { return; }
				var qty = Math.max(1, parseInt(toEn(input.value), 10) || 1);
				input.value = qty;
				var price = bulk && bulkMin && qty >= bulkMin ? bulk : unit;
				var u = wrap.querySelector('[data-ls-unit-price]');
				var t = wrap.querySelector('[data-ls-total]');
				var a = wrap.querySelector('[data-ls-area]');
				if (u && price) { u.textContent = faNumber(price); }
				if (t) { t.textContent = faNumber(qty * price); }
				if (a) { a.textContent = faNumber(qty * area, 2); }
			}
			$all(wrap, '[data-ls-qty]').forEach(function (b) {
				b.addEventListener('click', function () {
					if (!input) { return; }
					input.value = Math.max(1, (parseInt(toEn(input.value), 10) || 1) + parseInt(b.getAttribute('data-ls-qty'), 10));
					recalc();
				});
			});
			if (input) { input.addEventListener('input', recalc); input.addEventListener('change', recalc); }

			var main = wrap.querySelector('[data-ls-gallery] .aspect-\\[4\\/3\\] img');
			var zoom = wrap.querySelector('[data-ls-zoom]');
			$all(wrap, '[data-ls-thumb]').forEach(function (thumb) {
				thumb.addEventListener('click', function () {
					$all(wrap, '[data-ls-thumb]').forEach(function (t) { t.classList.remove('ring-2', 'ring-primary-container'); t.classList.add('opacity-70'); });
					thumb.classList.add('ring-2', 'ring-primary-container');
					thumb.classList.remove('opacity-70');
					if (main) { main.src = thumb.getAttribute('data-ls-thumb'); main.removeAttribute('srcset'); }
					if (zoom) { zoom.href = thumb.getAttribute('data-ls-thumb'); }
				});
			});
			$all(wrap, '[data-ls-wishlist]').forEach(function (btn) {
				var id = btn.getAttribute('data-ls-wishlist');
				var list = [];
				try { list = JSON.parse(localStorage.getItem('lsWishlist') || '[]'); } catch (e) { list = []; }
				function paint(on) {
					var i = btn.querySelector('i');
					btn.setAttribute('aria-pressed', on ? 'true' : 'false');
					if (i) { i.className = on ? 'bi bi-heart-fill text-lg text-error' : 'bi bi-heart text-lg text-on-surface-variant'; }
				}
				paint(list.indexOf(id) !== -1);
				btn.addEventListener('click', function () {
					try { list = JSON.parse(localStorage.getItem('lsWishlist') || '[]'); } catch (e) { list = []; }
					var idx = list.indexOf(id);
					if (idx === -1) { list.push(id); } else { list.splice(idx, 1); }
					try { localStorage.setItem('lsWishlist', JSON.stringify(list)); } catch (e) { /* storage unavailable */ }
					paint(idx === -1);
				});
			});
		});
	}

	/* ------------------------------------------------------------------
	 * Calculators, copy link, table of contents, sliders.
	 * ---------------------------------------------------------------- */
	function initMisc(root) {
		$all(root, '[data-ls-calc]').forEach(function (box) {
			if (!once(box, 'Calc')) { return; }
			var input = box.querySelector('[data-calc-input]');
			var f1 = parseFloat(box.getAttribute('data-f1')) || 0;
			var f2 = parseFloat(box.getAttribute('data-f2')) || 0;
			function update() {
				var v = parseFloat(input.value) || 0;
				box.querySelector('[data-calc-label]').textContent = faNumber(v) + ' ' + (box.getAttribute('data-unit') || '');
				box.querySelector('[data-calc-out1]').textContent = faNumber(v * f1, 2);
				box.querySelector('[data-calc-out2]').textContent = faNumber(v * f2, 1);
			}
			if (input) { input.addEventListener('input', update); update(); }
		});

		$all(root, '[data-ls-copy]').forEach(function (btn) {
			if (!once(btn, 'Copy')) { return; }
			btn.addEventListener('click', function () {
				var url = btn.getAttribute('data-ls-copy');
				var done = function () {
					var span = btn.querySelector('span');
					if (span) { var old = span.textContent; span.textContent = T.copied || 'Copied'; setTimeout(function () { span.textContent = old; }, 1800); }
				};
				if (navigator.clipboard) { navigator.clipboard.writeText(url).then(done, done); } else { done(); }
			});
		});

		$all(root, '[data-ls-toc]').forEach(function (box) {
			if (!once(box, 'Toc')) { return; }
			var list = box.querySelector('[data-ls-toc-list]');
			if (!list || list.children.length) { return; }
			var heads = $all(document, box.getAttribute('data-ls-toc') || '.ls-article h2');
			if (!heads.length) { box.style.display = 'none'; return; }
			heads.forEach(function (h, i) {
				if (!h.id) { h.id = 'ls-toc-' + (i + 1); }
				var a = document.createElement('a');
				a.href = '#' + h.id;
				a.className = 'flex items-center gap-2 p-2 rounded-xl text-on-surface-variant hover:text-surface-dark hover:bg-surface-canvas transition-colors';
				a.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-primary-container shrink-0"></span><span></span>';
				a.lastChild.textContent = h.textContent.trim();
				list.appendChild(a);
			});
		});

		$all(root, '[data-ls-slider]').forEach(function (s) {
			if (!once(s, 'Slider')) { return; }
			var track = s.querySelector('[data-ls-track]');
			$all(s, '[data-ls-slide]').forEach(function (b) {
				b.addEventListener('click', function () {
					if (!track || !track.firstElementChild) { return; }
					var step = track.firstElementChild.getBoundingClientRect().width + 24;
					// RTL: "next" scrolls toward the left (negative scrollLeft in modern browsers).
					track.scrollBy({ left: b.getAttribute('data-ls-slide') === 'next' ? -step : step, behavior: reducedMotion() ? 'auto' : 'smooth' });
				});
			});
		});
	}

	/* ------------------------------------------------------------------
	 * Animated counters, progress rings & bars (on first view).
	 * ---------------------------------------------------------------- */
	function animateCounter(el) {
		var text = el.textContent;
		var m = toEn(text).match(/[\d,]+(\.\d+)?/);
		if (!m) { return; }
		var target = parseFloat(m[0].replace(/,/g, ''));
		var decimals = m[1] ? m[1].length - 1 : 0;
		var start = null;
		var dur = 1200;
		var hasComma = m[0].indexOf(',') !== -1;
		function frame(ts) {
			if (!start) { start = ts; }
			var p = Math.min(1, (ts - start) / dur);
			var val = target * (1 - Math.pow(1 - p, 3));
			var num = decimals ? val.toFixed(decimals) : Math.round(val).toString();
			if (hasComma) { num = num.replace(/\B(?=(\d{3})+(?!\d))/g, ','); }
			el.textContent = toEn(text).replace(m[0], num).replace(/[0-9]/g, function (d) { return /[۰-۹]/.test(text) ? FA[d] : d; });
			if (p < 1) { requestAnimationFrame(frame); } else { el.textContent = text; }
		}
		requestAnimationFrame(frame);
	}
	function initReveal(root) {
		var els = $all(root, '[data-ls-counter], [data-ls-ring], [data-ls-bar]').filter(function (el) { return once(el, 'Reveal'); });
		if (!els.length) { return; }
		var inEditor = document.body.classList.contains('elementor-editor-active');
		// Reduced motion: keep the final values, skip the animation.
		if (reducedMotion()) { return; }
		els.forEach(function (el) {
			if (el.hasAttribute('data-ls-ring')) { el.style.strokeDashoffset = el.getAttribute('stroke-dasharray'); }
			if (el.hasAttribute('data-ls-bar')) { el.dataset.w = el.style.width; el.style.width = '0%'; }
		});
		function show(el) {
			if (el.hasAttribute('data-ls-counter') && !inEditor) { animateCounter(el); }
			if (el.hasAttribute('data-ls-ring')) { el.style.strokeDashoffset = el.getAttribute('data-ls-ring'); }
			if (el.hasAttribute('data-ls-bar')) { el.style.width = el.dataset.w; }
		}
		if (!('IntersectionObserver' in window)) { els.forEach(show); return; }
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) {
				if (en.isIntersecting) { show(en.target); io.unobserve(en.target); }
			});
		}, { threshold: 0.3 });
		els.forEach(function (el) { io.observe(el); });
	}

	function init(root) {
		root = root || document;
		initForms(root);
		initFilters(root);
		initAutoSubmit(root);
		initTabs(root);
		initProduct(root);
		initMisc(root);
		initReveal(root);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () { init(document); });
	} else {
		init(document);
	}

	// Elementor: re-run for widgets rendered/re-rendered in the editor or loaded later.
	// Elementor triggers "elementor/frontend/init" through jQuery (and natively in newer versions);
	// listen to both and also hook immediately if Elementor is already initialised.
	var elementorHooked = false;
	function hookElementor() {
		if (elementorHooked || !window.elementorFrontend || !window.elementorFrontend.hooks) { return; }
		elementorHooked = true;
		window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
			init($scope && $scope[0] ? $scope[0] : document);
		});
	}
	window.addEventListener('elementor/frontend/init', hookElementor);
	if (window.jQuery) { window.jQuery(window).on('elementor/frontend/init', hookElementor); }
	hookElementor();

	window.LarijaniThemeInit = init;
})();
