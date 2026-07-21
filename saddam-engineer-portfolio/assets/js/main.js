/* Saddam Hossen WordPress Engineering 1.0.0 ,  dependency-free progressive enhancement. */
(function () {
	'use strict';

	document.documentElement.classList.add('js');
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* Mobile navigation. */
	var toggle = document.querySelector('.sp-nav-toggle');
	var nav = document.querySelector('.sp-nav');
	if (toggle && nav) {
		var setNav = function (open) {
			nav.classList.toggle('open', open);
			document.body.classList.toggle('sp-nav-open', open);
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			toggle.setAttribute('aria-label', open ? 'Close navigation menu' : 'Open navigation menu');
		};
		toggle.addEventListener('click', function () { setNav(!nav.classList.contains('open')); });
		nav.querySelectorAll('a').forEach(function (link) {
			link.addEventListener('click', function () { setNav(false); });
		});
		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && nav.classList.contains('open')) {
				setNav(false);
				toggle.focus();
			}
		});
	}

	/* Header state and reliable viewport sizing. */
	var header = document.querySelector('.sp-header');
	var announcement = document.querySelector('.sp-announcement');
	var syncChromeHeight = function () {
		var height = 0;
		if (header) { height += header.offsetHeight; }
		if (announcement) { height += announcement.offsetHeight; }
		document.documentElement.style.setProperty('--sp-chrome-height', Math.max(0, height) + 'px');
	};
	if (header) {
		var setHeaderState = function () { header.classList.toggle('is-scrolled', window.scrollY > 8); };
		setHeaderState();
		window.addEventListener('scroll', setHeaderState, { passive: true });
	}
	syncChromeHeight();
	window.addEventListener('load', syncChromeHeight);
	window.addEventListener('resize', syncChromeHeight, { passive: true });
	if ('ResizeObserver' in window) {
		var chromeObserver = new ResizeObserver(syncChromeHeight);
		if (header) { chromeObserver.observe(header); }
		if (announcement) { chromeObserver.observe(announcement); }
	}

	/* Mobile quick actions. */
	var mobileCta = document.getElementById('sp-mobile-cta');
	if (mobileCta) {
		var mobileQuery = window.matchMedia('(max-width:720px)');
		var syncMobileCta = function () {
			document.body.classList.toggle('has-mobile-cta', mobileQuery.matches);
			if (!mobileQuery.matches) { mobileCta.classList.remove('show'); }
		};
		syncMobileCta();
		if (mobileQuery.addEventListener) { mobileQuery.addEventListener('change', syncMobileCta); }
		window.addEventListener('scroll', function () {
			if (mobileQuery.matches) { mobileCta.classList.toggle('show', window.scrollY > 520); }
		}, { passive: true });
	}

	/* UTM and referrer capture for contact attribution. */
	(function captureAttribution() {
		try {
			var params = new URLSearchParams(window.location.search);
			['utm_source', 'utm_medium', 'utm_campaign'].forEach(function (key) {
				var current = params.get(key);
				if (current) { sessionStorage.setItem('sp_' + key, current); }
				var value = current || sessionStorage.getItem('sp_' + key) || '';
				document.querySelectorAll('[data-utm="' + key + '"]').forEach(function (field) { field.value = value; });
			});
			document.querySelectorAll('[data-referrer="1"]').forEach(function (field) { field.value = document.referrer || ''; });
		} catch (error) {
			// Storage is optional; the form remains functional.
		}
	}());

	/* AJAX contact submission with native form fallback. */
	var form = document.querySelector('.sp-form');
	if (form && window.SPTHEME && window.fetch) {
		form.addEventListener('submit', function (event) {
			event.preventDefault();
			var message = form.querySelector('.sp-form-msg');
			var button = form.querySelector('button[type="submit"]');
			var data = new FormData(form);
			data.set('action', 'spcore_contact');
			form.querySelectorAll('.sp-field.err').forEach(function (field) { field.classList.remove('err'); });
			if (message) { message.className = 'sp-form-msg'; message.textContent = ''; }
			if (button) {
				button.disabled = true;
				button.dataset.label = button.textContent;
				button.textContent = 'Sending…';
			}

			fetch(window.SPTHEME.ajax, { method: 'POST', body: data, credentials: 'same-origin' })
				.then(function (response) { return response.json(); })
				.then(function (response) {
					var payload = response.data || response;
					if (response.success) {
						form.reset();
						if (message) { message.className = 'sp-form-msg ok'; message.textContent = payload.message || 'Message sent.'; }
						if (window.spcoreTrack) { window.spcoreTrack('contact_submit', { label: 'career_form' }); }
						return;
					}
					if (message) { message.className = 'sp-form-msg err'; message.textContent = payload.message || 'Please check the form.'; }
					Object.keys(payload.errors || {}).forEach(function (key) {
						var input = form.querySelector('[name="' + key + '"]');
						if (input && input.closest('.sp-field')) { input.closest('.sp-field').classList.add('err'); }
					});
				})
				.catch(function () {
					if (message) { message.className = 'sp-form-msg err'; message.textContent = 'Network error. Please use the email link on this page.'; }
				})
				.finally(function () {
					if (button) { button.disabled = false; button.textContent = button.dataset.label || 'Send Role / Project Details'; }
				});
		});
	}

	/* Native counters and reveal effects. Content stays visible without JS. */
	var animateCounter = function (element) {
		if (element.dataset.counted) { return; }
		var target = Number(element.getAttribute('data-count-to'));
		if (!Number.isFinite(target)) { return; }
		element.dataset.counted = '1';
		if (reduceMotion) { element.textContent = String(target); return; }
		var start = performance.now();
		var duration = 750;
		var frame = function (now) {
			var progress = Math.min(1, (now - start) / duration);
			var eased = 1 - Math.pow(1 - progress, 3);
			element.textContent = String(Math.round(target * eased));
			if (progress < 1) { window.requestAnimationFrame(frame); }
		};
		window.requestAnimationFrame(frame);
	};

	var revealGroups = document.querySelectorAll('[data-anim="stagger"]');
	var counterElements = document.querySelectorAll('[data-count-to]');
	if (reduceMotion || !('IntersectionObserver' in window)) {
		counterElements.forEach(animateCounter);
	} else {
		var revealObserver = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) { return; }
				revealObserver.unobserve(entry.target);
				entry.target.querySelectorAll('.sp-pre').forEach(function (item, index) {
					window.setTimeout(function () { item.classList.add('sp-in'); }, Math.min(index * 55, 440));
				});
			});
		}, { rootMargin: '0px 0px -8% 0px' });
		revealGroups.forEach(function (group) {
			group.querySelectorAll(':scope > *, :scope > ul > li, :scope > ol > li').forEach(function (item) { item.classList.add('sp-pre'); });
			revealObserver.observe(group);
			window.setTimeout(function () { group.querySelectorAll('.sp-pre').forEach(function (item) { item.classList.add('sp-in'); }); }, 2500);
		});

		var counterObserver = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) { animateCounter(entry.target); counterObserver.unobserve(entry.target); }
			});
		}, { rootMargin: '0px 0px -10% 0px' });
		counterElements.forEach(function (element) { counterObserver.observe(element); });
	}


	/* Reading progress and active table-of-contents state. */
	(function articleProgress() {
		var article = document.getElementById('article-content');
		var bar = document.getElementById('sp-reading-progress-bar');
		if (!article || !bar) { return; }

		var tocLinks = Array.prototype.slice.call(document.querySelectorAll('.sp-toc a[href^="#"]'));
		var headings = tocLinks.map(function (link) {
			var id = decodeURIComponent(link.getAttribute('href').slice(1));
			return { link: link, heading: document.getElementById(id) };
		}).filter(function (item) { return item.heading; });

		var update = function () {
			var rect = article.getBoundingClientRect();
			var viewport = window.innerHeight || document.documentElement.clientHeight;
			var total = Math.max(1, article.offsetHeight - viewport * 0.42);
			var travelled = Math.min(total, Math.max(0, -rect.top + viewport * 0.18));
			bar.style.transform = 'scaleX(' + Math.min(1, travelled / total) + ')';

			var active = null;
			headings.forEach(function (item) {
				if (item.heading.getBoundingClientRect().top <= Math.max(150, viewport * 0.24)) { active = item; }
			});
			headings.forEach(function (item) {
				item.link.classList.toggle('is-active', item === active);
				if (item === active) { item.link.setAttribute('aria-current', 'location'); }
				else { item.link.removeAttribute('aria-current'); }
			});
		};
		update();
		window.addEventListener('scroll', update, { passive: true });
		window.addEventListener('resize', update, { passive: true });
	}());

	/* Back to top: unobtrusive, keyboard accessible and motion-aware. */
	(function backToTop() {
		var button = document.getElementById('sp-back-to-top');
		if (!button) { return; }
		var sync = function () {
			var show = window.scrollY > Math.max(620, window.innerHeight * 0.72);
			button.classList.toggle('is-visible', show);
			button.setAttribute('aria-hidden', show ? 'false' : 'true');
			button.tabIndex = show ? 0 : -1;
		};
		button.addEventListener('click', function () {
			window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
		});
		sync();
		window.addEventListener('scroll', sync, { passive: true });
		window.addEventListener('resize', sync, { passive: true });
	}());

	/* Optional hiring modal: disabled by default in plugin settings. */
	(function leadModal() {
		var modal = document.getElementById('sp-lead-modal');
		if (!modal) { return; }
		var key = 'sp_modal_dismissed_until';
		var opened = false;
		var previousFocus = null;
		var days = parseInt(modal.getAttribute('data-days') || '14', 10);
		try { if (parseInt(localStorage.getItem(key) || '0', 10) > Date.now()) { return; } } catch (error) {}

		var close = function () {
			modal.classList.remove('open');
			modal.setAttribute('aria-hidden', 'true');
			document.body.classList.remove('sp-modal-open');
			try { localStorage.setItem(key, String(Date.now() + days * 86400000)); } catch (error) {}
			if (previousFocus) { previousFocus.focus(); }
		};
		var open = function () {
			if (opened) { return; }
			opened = true;
			previousFocus = document.activeElement;
			modal.classList.add('open');
			modal.setAttribute('aria-hidden', 'false');
			document.body.classList.add('sp-modal-open');
			var first = modal.querySelector('a,button');
			if (first) { window.setTimeout(function () { first.focus(); }, 50); }
		};
		window.setTimeout(open, Math.max(3000, parseInt(modal.getAttribute('data-delay') || '12000', 10)));
		modal.querySelectorAll('[data-modal-close]').forEach(function (element) { element.addEventListener('click', close); });
		document.addEventListener('keydown', function (event) { if (event.key === 'Escape' && modal.classList.contains('open')) { close(); } });
	}());
}());
