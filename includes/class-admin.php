<?php
/**
 * Admin settings page.
 *
 * @package ContactFloating
 */

namespace ContactFloating;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin UI.
 */
class Admin {

	/**
	 * Instance.
	 *
	 * @var Admin|null
	 */
	private static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return Admin
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
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_filter( 'plugin_action_links_' . CFLOAT_BASENAME, array( $this, 'action_links' ) );
	}

	/**
	 * Settings link on plugins list.
	 *
	 * @param array $links Links.
	 * @return array
	 */
	public function action_links( $links ) {
		$url = admin_url( 'options-general.php?page=contact-floating' );

		array_unshift(
			$links,
			sprintf(
				'<a href="%s">%s</a>',
				esc_url( $url ),
				esc_html__( 'Settings', 'contact-floating' )
			)
		);

		return $links;
	}

	/**
	 * Register submenu under Settings.
	 */
	public function add_menu() {
		add_options_page(
			__( 'Contact Floating', 'contact-floating' ),
			__( 'Contact Floating', 'contact-floating' ),
			'manage_options',
			'contact-floating',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Hook suffix.
	 */
	public function enqueue( $hook ) {
		if ( 'settings_page_contact-floating' !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'cfloat-admin',
			CFLOAT_URL . 'admin/css/admin.css',
			array(),
			CFLOAT_VERSION
		);

		wp_enqueue_script(
			'cfloat-admin',
			CFLOAT_URL . 'admin/js/admin.js',
			array(),
			CFLOAT_VERSION,
			true
		);

		wp_localize_script(
			'cfloat-admin',
			'cfloatAdmin',
			array(
				'maxItems'    => CFLOAT_MAX_ITEMS,
				'icons'       => Icons::all(),
				'iconLabels'  => Icons::labels(),
				'i18n'        => array(
					'confirmDelete' => __( 'حذف این آیتم؟', 'contact-floating' ),
					'maxReached'    => __( 'حداکثر ۲۰ آیتم مجاز است.', 'contact-floating' ),
					'newItem'       => __( 'آیتم جدید', 'contact-floating' ),
					'untitled'      => __( 'بدون عنوان', 'contact-floating' ),
				),
			)
		);
	}

	/**
	 * Render settings page.
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$options     = Settings::instance()->get();
		$icon_labels = Icons::labels();

		include CFLOAT_PATH . 'admin/views/settings-page.php';
	}
}
