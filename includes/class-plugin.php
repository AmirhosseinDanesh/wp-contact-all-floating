<?php
/**
 * Main plugin bootstrap.
 *
 * @package ContactFloating
 */

namespace ContactFloating;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin singleton.
 */
final class Plugin {

	/**
	 * Instance.
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'kses_allowed_protocols', array( $this, 'allowed_protocols' ) );
	}

	/**
	 * Allow contact-related URL schemes used by the widget.
	 *
	 * @param array $protocols Protocols.
	 * @return array
	 */
	public function allowed_protocols( $protocols ) {
		$extra = array( 'tel', 'sms', 'whatsapp', 'tg', 'viber' );

		foreach ( $extra as $protocol ) {
			if ( ! in_array( $protocol, $protocols, true ) ) {
				$protocols[] = $protocol;
			}
		}

		return $protocols;
	}

	/**
	 * Load translations.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'contact-floating', false, dirname( CFLOAT_BASENAME ) . '/languages' );
	}

	/**
	 * Boot modules.
	 */
	public function init() {
		Settings::instance();

		if ( is_admin() ) {
			Admin::instance();
		}

		Frontend::instance();
	}

	/**
	 * Activation: seed defaults once.
	 */
	public static function activate() {
		$existing = get_option( CFLOAT_OPTION_KEY, null );

		if ( null === $existing ) {
			add_option( CFLOAT_OPTION_KEY, Settings::defaults() );
		}

		flush_rewrite_rules();
	}

	/**
	 * Deactivation.
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}
}
