<?php
/**
 * Input sanitization helpers.
 *
 * @package ContactFloating
 */

namespace ContactFloating;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitizer.
 */
class Sanitizer {

	/**
	 * Sanitize hex color (#RGB or #RRGGBB).
	 *
	 * @param string $color Color value.
	 * @param string $fallback Fallback.
	 * @return string
	 */
	public static function hex_color( $color, $fallback = '#333333' ) {
		$color = trim( (string) $color );

		if ( preg_match( '/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $color ) ) {
			return strtolower( $color );
		}

		return $fallback;
	}

	/**
	 * Sanitize URL allowing common contact schemes.
	 *
	 * @param string $url URL.
	 * @return string
	 */
	public static function url( $url ) {
		$url = trim( (string) $url );

		if ( '' === $url ) {
			return '';
		}

		// Block obvious javascript/data payloads early.
		if ( preg_match( '/^\s*(javascript|data|vbscript)\s*:/i', $url ) ) {
			return '';
		}

		// Auto-prefix common contact values without a scheme.
		if ( preg_match( '/^[^\/:@\s]+@[^\/@\s]+\.[^\/@\s]+$/', $url ) ) {
			$url = 'mailto:' . $url;
		} elseif ( preg_match( '/^\+?[\d\s\-\(\)]{6,}$/u', $url ) ) {
			$url = 'tel:' . preg_replace( '/[^\d\+]/', '', $url );
		} elseif ( 0 === strpos( $url, '//' ) ) {
			$url = 'https:' . $url;
		} elseif ( preg_match( '/^[\w.-]+\.[a-z]{2,}([\/?#].*)?$/i', $url ) ) {
			$url = 'https://' . $url;
		}

		$allowed   = array( 'http', 'https', 'mailto', 'tel', 'sms', 'whatsapp', 'tg', 'viber' );
		$sanitized = esc_url_raw( $url, $allowed );

		return is_string( $sanitized ) ? $sanitized : '';
	}

	/**
	 * Sanitize SVG markup: allowlist-oriented cleanup for icon SVGs.
	 *
	 * @param string $svg SVG markup.
	 * @return string
	 */
	public static function svg( $svg ) {
		$svg = trim( (string) $svg );

		if ( '' === $svg ) {
			return '';
		}

		if ( false === stripos( $svg, '<svg' ) ) {
			return '';
		}

		// Size cap.
		if ( strlen( $svg ) > 50000 ) {
			return '';
		}

		// Decode a couple of common entity tricks before stripping.
		$svg = html_entity_decode( $svg, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

		// Drop XML/doctype and comments.
		$svg = preg_replace( '/<\?xml.*?\?>/is', '', $svg );
		$svg = preg_replace( '/<!DOCTYPE.*?>/is', '', $svg );
		$svg = preg_replace( '/<!--.*?-->/s', '', $svg );
		$svg = preg_replace( '/<!\[CDATA\[.*?\]\]>/is', '', $svg );

		// Remove dangerous elements entirely (with content when possible).
		$dangerous = 'script|style|foreignObject|iframe|embed|object|link|meta|base|form|input|button|textarea|select|option|a|use|image|animate|animateTransform|set|handler|listener';
		$svg       = preg_replace( '/<\s*(' . $dangerous . ')\b[^>]*>.*?<\s*\/\s*\1\s*>/is', '', $svg );
		$svg       = preg_replace( '/<\s*(' . $dangerous . ')\b[^>]*\/?\s*>/is', '', $svg );

		// Remove event handlers and javascript/data URLs in attributes.
		$svg = preg_replace( '/\son[a-z]+\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/is', '', $svg );
		$svg = preg_replace( '/\s(xlink:href|href|src)\s*=\s*([\'"])\s*(javascript|data|vbscript):[^\'"]*\2/is', '', $svg );
		$svg = preg_replace( '/\s(xlink:href|href|src)\s*=\s*(javascript|data|vbscript):[^\s>]*/is', '', $svg );

		// Disallow external references remaining on href-like attrs pointing outside fragment.
		$svg = preg_replace_callback(
			'/\s(xlink:href|href)\s*=\s*([\'"])(.*?)\2/is',
			static function ( $m ) {
				$val = trim( $m[3] );
				if ( '' === $val || '#' === substr( $val, 0, 1 ) ) {
					return $m[0];
				}
				return '';
			},
			$svg
		);

		// Keep only a single outer svg root if somehow nested weirdly.
		if ( ! preg_match( '/<svg\b[^>]*>.*<\/svg>/is', $svg, $match ) ) {
			return '';
		}
		$svg = $match[0];

		// Final reject if still looks executable.
		if ( preg_match( '/<\s*script\b/i', $svg ) || preg_match( '/javascript\s*:/i', $svg ) ) {
			return '';
		}

		return $svg;
	}

	/**
	 * Sanitize SVG URL (http/https only).
	 *
	 * @param string $url URL.
	 * @return string
	 */
	public static function svg_url( $url ) {
		$url = trim( (string) $url );

		if ( '' === $url ) {
			return '';
		}

		if ( preg_match( '/^\s*(javascript|data|vbscript)\s*:/i', $url ) ) {
			return '';
		}

		$sanitized = esc_url_raw( $url, array( 'http', 'https' ) );

		if ( ! $sanitized ) {
			return '';
		}

		$path = wp_parse_url( $sanitized, PHP_URL_PATH );
		if ( $path && ! preg_match( '/\.svg$/i', $path ) ) {
			// Allow media library URLs that may not end with .svg.
			if ( false === strpos( $sanitized, '/wp-content/' ) ) {
				return '';
			}
		}

		return $sanitized;
	}

	/**
	 * Positive integer with bounds.
	 *
	 * @param mixed $value Value.
	 * @param int   $min Min.
	 * @param int   $max Max.
	 * @param int   $fallback Fallback.
	 * @return int
	 */
	public static function int_range( $value, $min, $max, $fallback ) {
		if ( ! is_numeric( $value ) ) {
			return (int) $fallback;
		}

		$value = (int) $value;

		if ( $value < $min ) {
			return (int) $min;
		}

		if ( $value > $max ) {
			return (int) $max;
		}

		return $value;
	}
}
