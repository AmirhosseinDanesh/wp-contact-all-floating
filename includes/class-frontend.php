<?php
/**
 * Frontend widget + shortcode.
 *
 * @package ContactFloating
 */

namespace ContactFloating;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Frontend.
 */
class Frontend {

	/**
	 * Instance.
	 *
	 * @var Frontend|null
	 */
	private static $instance = null;

	/**
	 * Whether widget markup was already printed.
	 *
	 * @var bool
	 */
	private $rendered = false;

	/**
	 * Whether assets were enqueued.
	 *
	 * @var bool
	 */
	private $assets_enqueued = false;

	/**
	 * Get instance.
	 *
	 * @return Frontend
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
		add_shortcode( 'contact_floating', array( $this, 'shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue' ) );
		add_action( 'wp_footer', array( $this, 'auto_render' ), 20 );
	}

	/**
	 * Options helper.
	 *
	 * @return array
	 */
	private function options() {
		return Settings::instance()->get();
	}

	/**
	 * Whether widget should appear on this request.
	 *
	 * @return bool
	 */
	private function should_display() {
		if ( is_admin() ) {
			return false;
		}

		$options = $this->options();

		if ( empty( $options['enabled'] ) ) {
			return false;
		}

		$items = Settings::instance()->get_active_items();

		return ! empty( $items );
	}

	/**
	 * Enqueue CSS/JS only when needed.
	 */
	public function maybe_enqueue() {
		if ( ! $this->should_display() ) {
			return;
		}

		$this->enqueue_assets();
	}

	/**
	 * Enqueue public assets once.
	 */
	private function enqueue_assets() {
		if ( $this->assets_enqueued ) {
			return;
		}

		$this->assets_enqueued = true;

		$options = $this->options();
		$mb      = $options['main_button'];

		wp_enqueue_style(
			'cfloat-frontend',
			CFLOAT_URL . 'public/css/frontend.css',
			array(),
			CFLOAT_VERSION
		);

		$size         = (int) $mb['size'];
		$size_tablet  = max( 40, $size - 6 );
		$size_mobile  = max( 40, $size - 10 );
		$offset_m     = (int) ( isset( $options['offset_bottom_mobile'] ) ? $options['offset_bottom_mobile'] : 88 );
		$side_m       = (int) ( isset( $options['offset_side_mobile'] ) ? $options['offset_side_mobile'] : 12 );

		$css_vars = sprintf(
			'.cfloat-root{--cfloat-offset-bottom:%dpx;--cfloat-offset-bottom-mobile:%dpx;--cfloat-offset-side:%dpx;--cfloat-offset-side-mobile:%dpx;--cfloat-main-size:%dpx;--cfloat-main-bg:%s;--cfloat-main-icon:%s;--cfloat-main-border:%s;--cfloat-main-border-w:%dpx;}@media (max-width:1024px){.cfloat-root{--cfloat-main-size:%dpx;}}@media (max-width:782px){.cfloat-root{--cfloat-main-size:%dpx;--cfloat-offset-bottom:%dpx;--cfloat-offset-side:%dpx;}}',
			(int) $options['offset_bottom'],
			$offset_m,
			(int) $options['offset_side'],
			$side_m,
			$size,
			esc_attr( $mb['background_color'] ),
			esc_attr( $mb['icon_color'] ),
			esc_attr( $mb['border_color'] ),
			(int) $mb['border_width'],
			$size_tablet,
			$size_mobile,
			$offset_m,
			$side_m
		);

		wp_add_inline_style( 'cfloat-frontend', $css_vars );

		wp_enqueue_script(
			'cfloat-frontend',
			CFLOAT_URL . 'public/js/frontend.js',
			array(),
			CFLOAT_VERSION,
			true
		);
	}

	/**
	 * Auto print in footer unless shortcode already rendered.
	 */
	public function auto_render() {
		if ( $this->rendered || ! $this->should_display() ) {
			return;
		}

		$this->enqueue_assets();
		echo $this->get_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in get_markup.
	}

	/**
	 * Shortcode callback.
	 *
	 * @return string
	 */
	public function shortcode() {
		if ( ! $this->should_display() ) {
			return '';
		}

		if ( $this->rendered ) {
			return '';
		}

		$this->enqueue_assets();

		return $this->get_markup();
	}

	/**
	 * Build widget HTML.
	 *
	 * @return string
	 */
	private function get_markup() {
		$this->rendered = true;

		$options = $this->options();
		$items   = Settings::instance()->get_active_items();
		$mb      = $options['main_button'];

		$classes = array(
			'cfloat-root',
			'cfloat-pos-' . sanitize_html_class( $options['position'] ),
			'cfloat-devices-' . sanitize_html_class( $options['devices'] ),
		);

		if ( empty( $options['animation'] ) ) {
			$classes[] = 'cfloat-no-anim';
		}

		if ( empty( $options['show_labels'] ) ) {
			$classes[] = 'cfloat-hide-labels';
		}

		$main_label = isset( $mb['label'] ) ? $mb['label'] : '';
		$main_icon  = '';
		if ( empty( $mb['icon'] ) || 'none' !== $mb['icon'] ) {
			$main_icon = $this->render_icon(
				array(
					'icon'    => isset( $mb['icon'] ) ? $mb['icon'] : 'chat',
					'svg'     => isset( $mb['svg'] ) ? $mb['svg'] : '',
					'svg_url' => isset( $mb['svg_url'] ) ? $mb['svg_url'] : '',
				)
			);
		}
		// Fallback: always show something on the toggle.
		if ( '' === $main_icon && '' === $main_label ) {
			$main_icon = Icons::chat();
		}

		$close_icon   = Icons::close();
		$toggle_class = 'cfloat-toggle';
		if ( '' !== $main_label ) {
			$toggle_class .= ' cfloat-toggle--with-text';
		}

		$panel_id = 'cfloat-panel-' . uniqid( '', false );

		ob_start();
		?>
		<div
			class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
			data-cfloat
			dir="auto"
		>
			<ul id="<?php echo esc_attr( $panel_id ); ?>" class="cfloat-list" data-cfloat-list hidden>
				<?php foreach ( $items as $item ) : ?>
					<?php
					$item_url  = isset( $item['url'] ) ? $item['url'] : '';
					$is_web    = (bool) preg_match( '/^https?:\/\//i', $item_url );
					$link_atts = $is_web ? ' target="_blank" rel="noopener noreferrer"' : '';
					?>
					<li class="cfloat-item">
						<a
							class="cfloat-link"
							href="<?php echo esc_url( $item_url, array( 'http', 'https', 'mailto', 'tel', 'sms', 'whatsapp', 'tg', 'viber' ) ); ?>"
							<?php echo $link_atts; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static safe attributes. ?>
							aria-label="<?php echo esc_attr( $item['title'] ); ?>"
						>
							<?php if ( ! empty( $options['show_labels'] ) && '' !== $item['title'] ) : ?>
								<span class="cfloat-label"><?php echo esc_html( $item['title'] ); ?></span>
							<?php endif; ?>
							<span
								class="cfloat-icon"
								style="--cfloat-item-bg:<?php echo esc_attr( $item['color'] ); ?>"
							>
								<?php echo $this->render_icon( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized SVG. ?>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>

			<button
				type="button"
				class="<?php echo esc_attr( $toggle_class ); ?>"
				data-cfloat-toggle
				aria-expanded="false"
				aria-controls="<?php echo esc_attr( $panel_id ); ?>"
				aria-label="<?php echo esc_attr( $main_label ? $main_label : __( 'راه‌های ارتباطی', 'contact-floating' ) ); ?>"
			>
				<span class="cfloat-toggle-inner cfloat-toggle-open">
					<?php if ( $main_icon ) : ?>
						<span class="cfloat-toggle-svg"><?php echo $main_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php endif; ?>
					<?php if ( '' !== $main_label ) : ?>
						<span class="cfloat-toggle-text"><?php echo esc_html( $main_label ); ?></span>
					<?php endif; ?>
				</span>
				<span class="cfloat-toggle-inner cfloat-toggle-close" hidden>
					<span class="cfloat-toggle-svg"><?php echo $close_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				</span>
			</button>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Render icon markup or img for svg_url.
	 *
	 * @param array $item Item.
	 * @return string
	 */
	private function render_icon( $item ) {
		$icon    = isset( $item['icon'] ) ? sanitize_key( $item['icon'] ) : '';
		$svg     = isset( $item['svg'] ) ? $item['svg'] : '';
		$svg_url = isset( $item['svg_url'] ) ? Sanitizer::svg_url( $item['svg_url'] ) : '';

		if ( 'custom' === $icon || '' === $icon ) {
			$custom = Sanitizer::svg( $svg );
			if ( '' !== $custom ) {
				return $custom;
			}
			if ( '' !== $svg_url ) {
				return sprintf(
					'<img class="cfloat-icon-img" src="%s" alt="" width="24" height="24" loading="lazy" decoding="async" />',
					esc_url( $svg_url )
				);
			}
		}

		$markup = Icons::resolve_markup( $item );

		return '' !== $markup ? $markup : Icons::chat();
	}
}
