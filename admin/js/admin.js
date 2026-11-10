(function () {
	'use strict';

	var cfg = window.cfloatAdmin || {};
	var maxItems = cfg.maxItems || 20;
	var icons = cfg.icons || {};
	var i18n = cfg.i18n || {};

	var itemsWrap = document.getElementById('cfloat-items');
	var addBtn = document.getElementById('cfloat-add-item');
	var mainIcon = document.getElementById('cfloat-main-icon');

	if (!itemsWrap) {
		return;
	}

	function uid() {
		return 'item-' + Math.random().toString(36).slice(2, 10);
	}

	function escapeHtml(str) {
		return String(str)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#39;');
	}

	function escapeAttr(str) {
		return escapeHtml(str).replace(/`/g, '&#96;');
	}

	/**
	 * Lightweight SVG cleanup for admin preview only.
	 * Server-side Sanitizer remains the source of truth on save.
	 */
	function sanitizeSvgPreview(svg) {
		var out = String(svg || '');
		if (out.indexOf('<svg') === -1) {
			return '';
		}
		out = out.replace(/<\?xml[\s\S]*?\?>/gi, '');
		out = out.replace(/<!DOCTYPE[\s\S]*?>/gi, '');
		out = out.replace(/<!--[\s\S]*?-->/g, '');
		out = out.replace(/<!\[CDATA\[[\s\S]*?\]\]>/gi, '');
		out = out.replace(
			/<\s*(script|style|foreignObject|iframe|embed|object|link|meta|base|form|a|use|image|animate|set)\b[\s\S]*?<\/\s*\1\s*>/gi,
			''
		);
		out = out.replace(
			/<\s*(script|style|foreignObject|iframe|embed|object|link|meta|base|form|a|use|image|animate|set)\b[^>]*\/?\s*>/gi,
			''
		);
		out = out.replace(/\son[a-z]+\s*=\s*(".*?"|'.*?'|[^\s>]+)/gi, '');
		out = out.replace(
			/\s(xlink:href|href|src)\s*=\s*(['"])\s*(javascript|data|vbscript):[^'"]*\2/gi,
			''
		);
		if (/<\s*script\b/i.test(out) || /javascript\s*:/i.test(out)) {
			return '';
		}
		return out;
	}

	function reindex() {
		var rows = itemsWrap.querySelectorAll('[data-cfloat-item]');
		rows.forEach(function (row, index) {
			row.querySelectorAll('[name]').forEach(function (el) {
				el.name = el.name.replace(
					/contact_floating_options\[items\]\[(?:\d+|__INDEX__)\]/,
					'contact_floating_options[items][' + index + ']'
				);
			});
		});
	}

	function updatePreview(row) {
		var preview = row.querySelector('[data-cfloat-preview]');
		var colorInput = row.querySelector('[data-cfloat-color]');
		var iconSelect = row.querySelector('[data-cfloat-icon]');
		var svgArea = row.querySelector('[data-cfloat-svg]');
		var svgUrl = row.querySelector('[data-cfloat-svg-url]');

		if (!preview || !colorInput || !iconSelect) {
			return;
		}

		preview.style.background = colorInput.value || '#333';

		var key = iconSelect.value;
		if (key === 'custom') {
			var custom = sanitizeSvgPreview((svgArea && svgArea.value.trim()) || '');
			var url = (svgUrl && svgUrl.value.trim()) || '';
			if (custom) {
				preview.innerHTML = custom;
			} else if (/^https?:\/\//i.test(url)) {
				preview.innerHTML =
					'<img src="' + escapeAttr(url) + '" alt="" width="24" height="24" />';
			} else {
				preview.innerHTML = icons.chat || '';
			}
			return;
		}

		preview.innerHTML = icons[key] || icons.chat || '';
	}

	function toggleCustomFields(row) {
		var iconSelect = row.querySelector('[data-cfloat-icon]');
		var wraps = row.querySelectorAll('[data-cfloat-custom-wrap]');
		if (!iconSelect) {
			return;
		}
		var show = iconSelect.value === 'custom';
		wraps.forEach(function (el) {
			if (show) {
				el.removeAttribute('hidden');
			} else {
				el.setAttribute('hidden', 'hidden');
			}
		});
	}

	function bindRow(row) {
		var removeBtn = row.querySelector('[data-cfloat-remove]');
		var enabled = row.querySelector('[data-cfloat-enabled]');
		var iconSelect = row.querySelector('[data-cfloat-icon]');
		var colorInput = row.querySelector('[data-cfloat-color]');
		var svgArea = row.querySelector('[data-cfloat-svg]');
		var svgUrl = row.querySelector('[data-cfloat-svg-url]');

		if (removeBtn) {
			removeBtn.addEventListener('click', function () {
				if (!window.confirm(i18n.confirmDelete || 'Delete?')) {
					return;
				}
				row.remove();
				reindex();
			});
		}

		if (enabled) {
			enabled.addEventListener('change', function () {
				row.classList.toggle('is-disabled', !enabled.checked);
			});
		}

		if (iconSelect) {
			iconSelect.addEventListener('change', function () {
				toggleCustomFields(row);
				updatePreview(row);
			});
		}

		[colorInput, svgArea, svgUrl].forEach(function (el) {
			if (!el) {
				return;
			}
			el.addEventListener('input', function () {
				updatePreview(row);
			});
		});

		toggleCustomFields(row);
		updatePreview(row);
	}

	function createRow() {
		var count = itemsWrap.querySelectorAll('[data-cfloat-item]').length;
		if (count >= maxItems) {
			window.alert(i18n.maxReached || 'Max items reached');
			return;
		}

		var index = count;
		var id = uid();
		var optionsHtml = Object.keys(cfg.iconLabels || {})
			.map(function (k) {
				return (
					'<option value="' +
					escapeAttr(k) +
					'"' +
					(k === 'whatsapp' ? ' selected' : '') +
					'>' +
					escapeHtml(cfg.iconLabels[k] || k) +
					'</option>'
				);
			})
			.join('');

		var html =
			'<div class="cfloat-item-row" data-cfloat-item draggable="true">' +
			'<div class="cfloat-item-row__handle" aria-hidden="true"><span class="dashicons dashicons-menu"></span></div>' +
			'<div class="cfloat-item-row__preview" data-cfloat-preview style="background:#25d366"></div>' +
			'<div class="cfloat-item-row__fields">' +
			'<input type="hidden" name="contact_floating_options[items][' +
			index +
			'][id]" value="' +
			escapeAttr(id) +
			'" data-cfloat-id />' +
			'<label class="cfloat-field"><span>نام</span><input type="text" name="contact_floating_options[items][' +
			index +
			'][title]" value="' +
			escapeAttr(i18n.newItem || 'New item') +
			'" data-cfloat-title /></label>' +
			'<label class="cfloat-field"><span>لینک</span><input type="text" name="contact_floating_options[items][' +
			index +
			'][url]" value="" placeholder="https:// یا tel: یا mailto:" /></label>' +
			'<label class="cfloat-field cfloat-field--color"><span>رنگ</span><input type="color" name="contact_floating_options[items][' +
			index +
			'][color]" value="#25d366" data-cfloat-color /></label>' +
			'<label class="cfloat-field"><span>آیکون</span><select name="contact_floating_options[items][' +
			index +
			'][icon]" data-cfloat-icon>' +
			optionsHtml +
			'</select></label>' +
			'<label class="cfloat-field cfloat-field--wide cfloat-custom-svg" data-cfloat-custom-wrap hidden><span>کد SVG سفارشی</span><textarea name="contact_floating_options[items][' +
			index +
			'][svg]" rows="3" data-cfloat-svg></textarea></label>' +
			'<label class="cfloat-field cfloat-field--wide cfloat-custom-svg" data-cfloat-custom-wrap hidden><span>یا لینک فایل SVG</span><input type="url" name="contact_floating_options[items][' +
			index +
			'][svg_url]" value="" data-cfloat-svg-url /></label>' +
			'<label class="cfloat-field cfloat-field--toggle"><input type="checkbox" name="contact_floating_options[items][' +
			index +
			'][enabled]" value="1" checked data-cfloat-enabled /><span>فعال</span></label>' +
			'</div>' +
			'<button type="button" class="button-link-delete cfloat-item-row__remove" data-cfloat-remove><span class="dashicons dashicons-trash"></span></button>' +
			'</div>';

		itemsWrap.insertAdjacentHTML('beforeend', html);
		var row = itemsWrap.lastElementChild;
		bindRow(row);
		reindex();
	}

	/* Drag & drop */
	var dragEl = null;

	itemsWrap.addEventListener('dragstart', function (e) {
		var row = e.target.closest('[data-cfloat-item]');
		if (!row) {
			return;
		}
		dragEl = row;
		row.classList.add('is-dragging');
		e.dataTransfer.effectAllowed = 'move';
		try {
			e.dataTransfer.setData('text/plain', 'cfloat');
		} catch (err) {
			/* IE ignore */
		}
	});

	itemsWrap.addEventListener('dragend', function () {
		if (dragEl) {
			dragEl.classList.remove('is-dragging');
		}
		dragEl = null;
		reindex();
	});

	itemsWrap.addEventListener('dragover', function (e) {
		e.preventDefault();
		var row = e.target.closest('[data-cfloat-item]');
		if (!row || !dragEl || row === dragEl) {
			return;
		}
		var rect = row.getBoundingClientRect();
		var before = e.clientY < rect.top + rect.height / 2;
		itemsWrap.insertBefore(dragEl, before ? row : row.nextSibling);
	});

	itemsWrap.querySelectorAll('[data-cfloat-item]').forEach(bindRow);

	if (addBtn) {
		addBtn.addEventListener('click', createRow);
	}

	if (mainIcon) {
		mainIcon.addEventListener('change', function () {
			var show = mainIcon.value === 'custom';
			document.querySelectorAll('.cfloat-main-custom').forEach(function (row) {
				if (show) {
					row.removeAttribute('hidden');
				} else {
					row.setAttribute('hidden', 'hidden');
				}
			});
		});
	}
})();
