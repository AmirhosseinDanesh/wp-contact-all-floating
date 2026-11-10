<?php
/**
 * Options API / Settings API.
 *
 * @package ContactFloating
 */

namespace ContactFloating;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings manager.
 */
class Settings {

	/**
	 * Instance.
	 *
	 * @var Settings|null
	 */
	private static $instance = null;

	/**
	 * Cached options.
	 *
	 * @var array|null
	 */
	private $cache = null;

	/**
	 * Get instance.
	 *
	 * @return Settings
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
		add_action( 'admin_init', array( $this, 'register' ) );
	}

	/**
	 * Default options.
	 *
	 * @return array
	 */
	public static function defaults() {
		return array(
			'enabled'       => 1,
			'position'      => 'bottom-right',
			'devices'       => 'both',
			'show_labels'   => 1,
			'animation'     => 1,
			'offset_bottom'        => 24,
			'offset_bottom_mobile' => 88,
			'offset_side'          => 16,
			'offset_side_mobile'   => 12,
			'main_button'   => array(
				'label'            => __( 'راه‌های ارتباطی', 'contact-floating' ),
				'icon'             => 'chat',
				'svg'              => '',
				'svg_url'          => '',
				'background_color' => '#e74c3c',
				'icon_color'       => '#ffffff',
				'border_color'     => '#e74c3c',
				'border_width'     => 0,
				'size'             => 56,
			),
			'items'         => array(
				array(
					'id'      => 'sample-whatsapp',
					'title'   => 'WhatsApp',
					'url'     => 'https://wa.me/',
					'color'   => '#25d366',
					'icon'    => 'whatsapp',
					'svg'     => '',
					'svg_url' => '',
					'enabled' => 1,
				),
				array(
					'id'      => 'sample-telegram',
					'title'   => 'Telegram',
					'url'     => 'https://t.me/',
					'color'   => '#2aabee',
					'icon'    => 'telegram',
					'svg'     => '',
					'svg_url' => '',
					'enabled' => 1,
				),
				array(
					'id'      => 'sample-instagram',
					'title'   => 'Instagram',
					'url'     => 'https://instagram.com/',
					'color'   => '#e1306c',
					'icon'    => 'instagram',
					'svg'     => '',
					'svg_url' => '',
					'enabled' => 1,
				),
				array(
					'id'      => 'sample-facebook',
					'title'   => 'Facebook',
					'url'     => 'https://facebook.com/',
					'color'   => '#1877f2',
					'icon'    => 'facebook',
					'svg'     => '',
					'svg_url' => '',
					'enabled' => 1,
				),
			),
		);
	}

	/**
	 * Register setting.
	 */
	public function register() {
		register_setting(
			'contact_floating_group',
			CFLOAT_OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => self::defaults(),
				'capability'        => 'manage_options',
				'show_in_rest'      => false,
			)
		);
	}

	/**
	 * Get all options (merged with defaults).
	 *
	 * @return array
	 */
	public function get() {
		if ( null !== $this->cache ) {
			return $this->cache;
		}

		$stored       = get_option( CFLOAT_OPTION_KEY, array() );
		$this->cache  = $this->merge_defaults( is_array( $stored ) ? $stored : array() );

		return $this->cache;
	}

	/**
	 * Clear request cache after save.
	 */
	public function clear_cache() {
		$this->cache = null;
	}

	/**
	 * Merge stored with defaults.
	 *
	 * @param array $stored Stored.
	 * @return array
	 */
	private function merge_defaults( $stored ) {
		$defaults = self::defaults();
		$out      = array_merge( $defaults, $stored );

		$out['main_button'] = array_merge(
			$defaults['main_button'],
			isset( $stored['main_button'] ) && is_array( $stored['main_button'] ) ? $stored['main_button'] : array()
		);

		if ( ! isset( $stored['items'] ) || ! is_array( $stored['items'] ) ) {
			$out['items'] = $defaults['items'];
		}

		return $out;
	}

	/**
	 * Sanitize options on save.
	 *
	 * @param mixed $input Raw input.
	 * @return array
	 */
	public function sanitize( $input ) {
		$defaults = self::defaults();
		$input    = is_array( $input ) ? $input : array();
		$output   = array();

		$output['enabled']     = empty( $input['enabled'] ) ? 0 : 1;
		$output['show_labels'] = empty( $input['show_labels'] ) ? 0 : 1;
		$output['animation']   = empty( $input['animation'] ) ? 0 : 1;

		$position = isset( $input['position'] ) ? sanitize_key( $input['position'] ) : 'bottom-right';
		$output['position'] = in_array( $position, array( 'bottom-right', 'bottom-left' ), true ) ? $position : 'bottom-right';

		$devices = isset( $input['devices'] ) ? sanitize_key( $input['devices'] ) : 'both';
		$output['devices'] = in_array( $devices, array( 'desktop', 'mobile', 'both' ), true ) ? $devices : 'both';

		$output['offset_bottom']        = Sanitizer::int_range( isset( $input['offset_bottom'] ) ? $input['offset_bottom'] : 24, 0, 240, 24 );
		$output['offset_bottom_mobile'] = Sanitizer::int_range( isset( $input['offset_bottom_mobile'] ) ? $input['offset_bottom_mobile'] : 88, 0, 240, 88 );
		$output['offset_side']          = Sanitizer::int_range( isset( $input['offset_side'] ) ? $input['offset_side'] : 16, 0, 200, 16 );
		$output['offset_side_mobile']   = Sanitizer::int_range( isset( $input['offset_side_mobile'] ) ? $input['offset_side_mobile'] : 12, 0, 200, 12 );

		$mb_in = isset( $input['main_button'] ) && is_array( $input['main_button'] ) ? $input['main_button'] : array();
		$mb    = $defaults['main_button'];

		$mb['label']            = isset( $mb_in['label'] ) ? sanitize_text_field( $mb_in['label'] ) : '';
		$mb['icon']             = isset( $mb_in['icon'] ) ? sanitize_key( $mb_in['icon'] ) : 'chat';
		$mb['svg']              = isset( $mb_in['svg'] ) ? Sanitizer::svg( $mb_in['svg'] ) : '';
		$mb['svg_url']          = isset( $mb_in['svg_url'] ) ? Sanitizer::svg_url( $mb_in['svg_url'] ) : '';
		$mb['background_color'] = Sanitizer::hex_color( isset( $mb_in['background_color'] ) ? $mb_in['background_color'] : '#e74c3c', '#e74c3c' );
		$mb['icon_color']       = Sanitizer::hex_color( isset( $mb_in['icon_color'] ) ? $mb_in['icon_color'] : '#ffffff', '#ffffff' );
		$mb['border_color']     = Sanitizer::hex_color( isset( $mb_in['border_color'] ) ? $mb_in['border_color'] : '#e74c3c', '#e74c3c' );
		$mb['border_width']     = Sanitizer::int_range( isset( $mb_in['border_width'] ) ? $mb_in['border_width'] : 0, 0, 12, 0 );
		$mb['size']             = Sanitizer::int_range( isset( $mb_in['size'] ) ? $mb_in['size'] : 56, 40, 96, 56 );

		$allowed_icons = array_merge( array( 'none' ), array_keys( Icons::labels() ) );
		if ( ! in_array( $mb['icon'], $allowed_icons, true ) ) {
			$mb['icon'] = 'chat';
		}

		$output['main_button'] = $mb;
		$output['items']       = $this->sanitize_items( isset( $input['items'] ) ? $input['items'] : array() );

		$this->clear_cache();

		return $output;
	}

	/**
	 * Sanitize contact items.
	 *
	 * @param mixed $items Raw items.
	 * @return array
	 */
	private function sanitize_items( $items ) {
		if ( ! is_array( $items ) ) {
			return array();
		}

		$allowed_icons = array_keys( Icons::labels() );
		$clean         = array();
		$count         = 0;

		foreach ( $items as $item ) {
			if ( $count >= CFLOAT_MAX_ITEMS ) {
				break;
			}

			if ( ! is_array( $item ) ) {
				continue;
			}

			$title = isset( $item['title'] ) ? sanitize_text_field( $item['title'] ) : '';
			$url   = isset( $item['url'] ) ? Sanitizer::url( $item['url'] ) : '';

			if ( '' === $title && '' === $url ) {
				continue;
			}

			$icon = isset( $item['icon'] ) ? sanitize_key( $item['icon'] ) : 'custom';
			if ( ! in_array( $icon, $allowed_icons, true ) ) {
				$icon = 'custom';
			}

			$id = isset( $item['id'] ) ? sanitize_key( $item['id'] ) : '';
			if ( '' === $id ) {
				$id = 'item-' . wp_generate_password( 8, false, false );
			}

			$clean[] = array(
				'id'      => $id,
				'title'   => $title,
				'url'     => $url,
				'color'   => Sanitizer::hex_color( isset( $item['color'] ) ? $item['color'] : '#333333', '#333333' ),
				'icon'    => $icon,
				'svg'     => isset( $item['svg'] ) ? Sanitizer::svg( $item['svg'] ) : '',
				'svg_url' => isset( $item['svg_url'] ) ? Sanitizer::svg_url( $item['svg_url'] ) : '',
				'enabled' => empty( $item['enabled'] ) ? 0 : 1,
			);

			++$count;
		}

		return $clean;
	}

	/**
	 * Active items only.
	 *
	 * @return array
	 */
	public function get_active_items() {
		$options = $this->get();
		$items   = isset( $options['items'] ) && is_array( $options['items'] ) ? $options['items'] : array();

		return array_values(
			array_filter(
				$items,
				static function ( $item ) {
					return ! empty( $item['enabled'] ) && ! empty( $item['url'] );
				}
			)
		);
	}
}
