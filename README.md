# WP Contact All Floating

Lightweight WordPress floating contact widget. Manage social links, phone, email and more from **Settings → Contact Floating**.

No Elementor. No jQuery. Vanilla JS + plain CSS + inline SVG.

## Features

- Enable / disable the widget
- Bottom-right or bottom-left position
- Desktop / mobile / both
- Optional labels next to icons
- Optional open/close animation
- Main button: label and/or icon, colors, border, size
- Contact items: title, URL, color, preset icon or custom SVG / SVG URL
- Drag & drop ordering (max 20 items)
- Separate mobile offsets (for bottom nav bars)
- Shortcode: `[contact_floating]`
- RTL / Persian friendly
- Assets load only when the widget is active

## Requirements

- WordPress 5.8+
- PHP 7.4+

## Installation

1. Download the repository ZIP or a [Release](../../releases).
2. In WordPress: **Plugins → Add New → Upload Plugin**.
3. Activate **Contact Floating**.
4. Open **Settings → Contact Floating**.

### From source

```bash
git clone https://github.com/YOUR_USER/wp-contact-all-floating.git
# copy folder into wp-content/plugins/wp-contact-all-floating
```

## Shortcode

```text
[contact_floating]
```

If the shortcode renders the widget, the automatic footer output is skipped to avoid duplicates.

## Security notes

- Admin screen requires `manage_options`
- Settings saved via WordPress Settings API + nonce
- Inputs sanitized (URLs, hex colors, SVG markup)
- Outputs escaped (`esc_html`, `esc_attr`, `esc_url`)
- Custom SVG is stripped of scripts, event handlers, and dangerous elements before save/output

## Development

Plugin slug / text domain: `contact-floating`  
Option key: `contact_floating_options`  
CSS prefix: `cfloat-`

## Changelog

See [readme.txt](readme.txt).

## License

GPL-2.0-or-later. See [LICENSE](LICENSE).
