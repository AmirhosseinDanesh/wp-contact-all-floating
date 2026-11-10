<?php
/**
 * Single contact item row (admin).
 *
 * @package ContactFloating
 *
 * @var array $item
 * @var int|string $index
 * @var array $icon_labels
 * @var bool $is_template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_template = ! empty( $is_template );
$name_prefix = 'contact_floating_options[items][' . $index . ']';
$enabled     = ! empty( $item['enabled'] );
$icon_key    = isset( $item['icon'] ) ? $item['icon'] : 'whatsapp';
$preview_svg = \ContactFloating\Icons::resolve_markup( $item );
if ( '' === $preview_svg ) {
	$preview_svg = \ContactFloating\Icons::chat();
}
?>
<div class="cfloat-item-row<?php echo $enabled ? '' : ' is-disabled'; ?>" data-cfloat-item draggable="<?php echo $is_template ? 'false' : 'true'; ?>">
	<div class="cfloat-item-row__handle" title="<?php esc_attr_e( 'جابجایی', 'contact-floating' ); ?>" aria-hidden="true">
		<span class="dashicons dashicons-menu"></span>
	</div>

	<div class="cfloat-item-row__preview" data-cfloat-preview style="background:<?php echo esc_attr( $item['color'] ); ?>">
		<?php echo $preview_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>

	<div class="cfloat-item-row__fields">
		<input type="hidden" name="<?php echo esc_attr( $name_prefix ); ?>[id]" value="<?php echo esc_attr( $item['id'] ); ?>" data-cfloat-id />

		<label class="cfloat-field">
			<span><?php esc_html_e( 'نام', 'contact-floating' ); ?></span>
			<input type="text" name="<?php echo esc_attr( $name_prefix ); ?>[title]" value="<?php echo esc_attr( $item['title'] ); ?>" data-cfloat-title placeholder="<?php esc_attr_e( 'مثلاً WhatsApp', 'contact-floating' ); ?>" />
		</label>

		<label class="cfloat-field">
			<span><?php esc_html_e( 'لینک', 'contact-floating' ); ?></span>
			<input type="text" name="<?php echo esc_attr( $name_prefix ); ?>[url]" value="<?php echo esc_attr( $item['url'] ); ?>" placeholder="https:// یا tel: یا mailto:" />
		</label>

		<label class="cfloat-field cfloat-field--color">
			<span><?php esc_html_e( 'رنگ', 'contact-floating' ); ?></span>
			<input type="color" name="<?php echo esc_attr( $name_prefix ); ?>[color]" value="<?php echo esc_attr( $item['color'] ); ?>" data-cfloat-color />
		</label>

		<label class="cfloat-field">
			<span><?php esc_html_e( 'آیکون', 'contact-floating' ); ?></span>
			<select name="<?php echo esc_attr( $name_prefix ); ?>[icon]" data-cfloat-icon>
				<?php foreach ( $icon_labels as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $icon_key, $key ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>

		<label class="cfloat-field cfloat-field--wide cfloat-custom-svg" data-cfloat-custom-wrap <?php echo ( 'custom' === $icon_key ) ? '' : 'hidden'; ?>>
			<span><?php esc_html_e( 'کد SVG سفارشی', 'contact-floating' ); ?></span>
			<textarea name="<?php echo esc_attr( $name_prefix ); ?>[svg]" rows="3" data-cfloat-svg placeholder="<svg ...>...</svg>"><?php echo esc_textarea( isset( $item['svg'] ) ? $item['svg'] : '' ); ?></textarea>
		</label>

		<label class="cfloat-field cfloat-field--wide cfloat-custom-svg" data-cfloat-custom-wrap <?php echo ( 'custom' === $icon_key ) ? '' : 'hidden'; ?>>
			<span><?php esc_html_e( 'یا لینک فایل SVG', 'contact-floating' ); ?></span>
			<input type="url" name="<?php echo esc_attr( $name_prefix ); ?>[svg_url]" value="<?php echo esc_attr( isset( $item['svg_url'] ) ? $item['svg_url'] : '' ); ?>" data-cfloat-svg-url placeholder="https://example.com/icon.svg" />
		</label>

		<label class="cfloat-field cfloat-field--toggle">
			<input type="checkbox" name="<?php echo esc_attr( $name_prefix ); ?>[enabled]" value="1" <?php checked( $enabled ); ?> data-cfloat-enabled />
			<span><?php esc_html_e( 'فعال', 'contact-floating' ); ?></span>
		</label>
	</div>

	<button type="button" class="button-link-delete cfloat-item-row__remove" data-cfloat-remove aria-label="<?php esc_attr_e( 'حذف', 'contact-floating' ); ?>">
		<span class="dashicons dashicons-trash"></span>
	</button>
</div>
