(function () {
	'use strict';

	function initRoot(root) {
		if (!root || root.getAttribute('data-cfloat-ready')) {
			return;
		}
		root.setAttribute('data-cfloat-ready', '1');

		var toggle = root.querySelector('[data-cfloat-toggle]');
		var list = root.querySelector('[data-cfloat-list]');
		if (!toggle || !list) {
			return;
		}

		var openInner = toggle.querySelector('.cfloat-toggle-open');
		var closeInner = toggle.querySelector('.cfloat-toggle-close');

		function isOpen() {
			return root.classList.contains('is-open');
		}

		function setOpen(open) {
			root.classList.toggle('is-open', open);
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');

			if (open) {
				list.removeAttribute('hidden');
			} else {
				list.setAttribute('hidden', 'hidden');
			}

			if (openInner) {
				if (open) {
					openInner.setAttribute('hidden', 'hidden');
				} else {
					openInner.removeAttribute('hidden');
				}
			}

			if (closeInner) {
				if (open) {
					closeInner.removeAttribute('hidden');
				} else {
					closeInner.setAttribute('hidden', 'hidden');
				}
			}
		}

		toggle.addEventListener('click', function (e) {
			e.preventDefault();
			e.stopPropagation();
			setOpen(!isOpen());
		});

		document.addEventListener('click', function (e) {
			if (!isOpen()) {
				return;
			}
			if (root.contains(e.target)) {
				return;
			}
			setOpen(false);
		});

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && isOpen()) {
				setOpen(false);
				toggle.focus();
			}
		});
	}

	function boot() {
		document.querySelectorAll('[data-cfloat]').forEach(initRoot);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
