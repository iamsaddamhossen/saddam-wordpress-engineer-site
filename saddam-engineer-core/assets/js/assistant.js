/* Saddam Engineer Core assistant 1.0.0. */
(function () {
	'use strict';
	var root = document.getElementById('sp-assistant');
	if (!root || !window.SPCORE_ASSISTANT) { return; }
	document.body.classList.add('spcore-assistant-visible');

	var launcher = root.querySelector('.sp-assistant-launcher');
	var panel = root.querySelector('.sp-assistant-panel');
	var close = root.querySelector('.sp-assistant-close');
	var log = root.querySelector('.sp-assistant-log');
	var form = root.querySelector('.sp-assistant-form');
	var input = root.querySelector('#sp-assistant-input');
	var send = form ? form.querySelector('button[type="submit"]') : null;
	var lastFocus = null;
	var mode = window.SPCORE_ASSISTANT.mode || 'guided';

	var links = {
		engineering: { text: 'The Engineering page explains how content models, registered metadata, REST routes, a dynamic Gutenberg block, WP-CLI, privacy, caching and quality checks work together.', label: 'Review engineering evidence', url: '/engineering/' },
		work: { text: 'The Work section explains production problems, constraints, technical decisions and observable outcomes without exposing confidential client details.', label: 'View case studies', url: '/case-studies/' },
		availability: { text: 'Saddam is focused on full-time remote WordPress engineering roles and selected long-term technical collaborations. Share the team, product context, responsibilities and technical priorities.', label: 'Discuss a role', url: '/contact/' },
		writing: { text: 'The technical writing section covers maintainable plugins, REST API design, production debugging, WordPress SEO/AEO and practical engineering workflows.', label: 'Read technical writing', url: '/blog/' },
		tools: { text: 'The Recommended Tools page documents a practical WordPress stack. Affiliate relationships are disclosed and do not change the price paid by the reader.', label: 'Review recommended tools', url: '/recommended-tools/' }
	};

	var absolute = function (path) {
		try { return new URL(path, window.SPCORE_ASSISTANT.contactUrl).href; } catch (error) { return path; }
	};
	var scrollLog = function () { log.scrollTop = log.scrollHeight; };
	var message = function (text, who, action) {
		var item = document.createElement('div');
		item.className = 'sp-assistant-message ' + (who === 'user' ? 'is-user' : 'is-assistant');
		var paragraph = document.createElement('p');
		paragraph.textContent = text;
		item.appendChild(paragraph);
		if (action && action.url) {
			var link = document.createElement('a');
			link.href = absolute(action.url);
			link.textContent = action.label + ' ';
			item.appendChild(link);
		}
		log.appendChild(item);
		scrollLog();
		return item;
	};
	var setOpen = function (open) {
		root.classList.toggle('is-open', open);
		launcher.setAttribute('aria-expanded', open ? 'true' : 'false');
		panel.setAttribute('aria-hidden', open ? 'false' : 'true');
		if (open) {
			lastFocus = document.activeElement;
			window.setTimeout(function () { input.focus(); }, 80);
		} else if (lastFocus) { lastFocus.focus(); }
	};
	launcher.addEventListener('click', function () { setOpen(!root.classList.contains('is-open')); });
	close.addEventListener('click', function () { setOpen(false); });
	document.addEventListener('keydown', function (event) { if (event.key === 'Escape' && root.classList.contains('is-open')) { setOpen(false); } });

	var guided = function (query) {
		var value = String(query || '').toLowerCase();
		if (/engineer|code|plugin|api|gutenberg|cli|architecture/.test(value)) { return links.engineering; }
		if (/work|project|case|client|production/.test(value)) { return links.work; }
		if (/available|hire|role|job|remote|contact|salary/.test(value)) { return links.availability; }
		if (/blog|article|writing|learn|seo|aeo|search/.test(value)) { return links.writing; }
		if (/tool|hosting|plugin recommendation|affiliate|stack/.test(value)) { return links.tools; }
		return { text: 'I can guide you to WordPress engineering details, production work, technical writing, recommended tools or the contact page. Choose a topic below or ask about one of those areas.', label: 'Explore engineering work', url: '/engineering/' };
	};

	root.querySelectorAll('[data-prompt]').forEach(function (button) {
		button.addEventListener('click', function () {
			var key = button.getAttribute('data-prompt');
			var item = links[key] || guided(key);
			message(button.textContent.trim(), 'user');
			window.setTimeout(function () { message(item.text, 'assistant', item); }, 120);
		});
	});

	var aiAnswer = function (value) {
		var pending = message(window.SPCORE_ASSISTANT.i18n.working, 'assistant');
		pending.classList.add('is-pending');
		var data = new FormData();
		data.set('action', 'spcore_assistant');
		data.set('nonce', window.SPCORE_ASSISTANT.nonce);
		data.set('message', value);
		return fetch(window.SPCORE_ASSISTANT.ajax, { method: 'POST', body: data, credentials: 'same-origin' })
			.then(function (response) { return response.json(); })
			.then(function (response) {
				pending.remove();
				if (!response.success) { throw new Error((response.data && response.data.message) || window.SPCORE_ASSISTANT.i18n.error); }
				message(response.data.answer, 'assistant');
			})
			.catch(function (error) {
				pending.remove();
				message(error.message || window.SPCORE_ASSISTANT.i18n.error, 'assistant', links.availability);
			});
	};

	form.addEventListener('submit', function (event) {
		event.preventDefault();
		var value = input.value.trim();
		if (!value) { return; }
		message(value, 'user');
		input.value = '';
		input.disabled = true;
		if (send) { send.disabled = true; }
		var request = mode === 'ai' ? aiAnswer(value) : new Promise(function (resolve) {
			window.setTimeout(function () { var item = guided(value); message(item.text, 'assistant', item); resolve(); }, 140);
		});
		request.finally(function () { input.disabled = false; if (send) { send.disabled = false; } input.focus(); });
	});
}());
