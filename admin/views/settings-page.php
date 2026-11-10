<?php
/**
 * Admin settings page.
 *
 * @package ContactFloating
 *
 * @var array $options
 * @var array $icon_labels
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mb    = $options['main_button'];
$items = isset( $options['items'] ) && is_array( $options['items'] ) ? $options['items'] : array();
?>
<div class="wrap cfloat-admin" dir="rtl">
	<h1><?php esc_html_e( 'Contact Floating', 'contact-floating' ); ?></h1>
	<p class="description"><?php esc_html_e( 'مدیریت دکمه شناور راه‌های ارتباطی سایت.', 'contact-floating' ); ?></p>

	<?php settings_errors(); ?>

	<form method="post" action="options.php" id="cfloat-settings-form">
		<?php settings_fields( 'contact_floating_group' ); ?>

		<div class="cfloat-admin-grid">
			<section class="cfloat-card">
				<h2><?php esc_html_e( 'تنظیمات عمومی', 'contact-floating' ); ?></h2>

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'وضعیت ویجت', 'contact-floating' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="contact_floating_options[enabled]" value="1" <?php checked( ! empty( $options['enabled'] ) ); ?> />
								<?php esc_html_e( 'فعال باشد', 'contact-floating' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'موقعیت', 'contact-floating' ); ?></th>
						<td>
							<select name="contact_floating_options[position]">
								<option value="bottom-right" <?php selected( $options['position'], 'bottom-right' ); ?>><?php esc_html_e( 'پایین راست', 'contact-floating' ); ?></option>
								<option value="bottom-left" <?php selected( $options['position'], 'bottom-left' ); ?>><?php esc_html_e( 'پایین چپ', 'contact-floating' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'نمایش در دستگاه‌ها', 'contact-floating' ); ?></th>
						<td>
							<select name="contact_floating_options[devices]">
								<option value="both" <?php selected( $options['devices'], 'both' ); ?>><?php esc_html_e( 'دسکتاپ و موبایل', 'contact-floating' ); ?></option>
								<option value="desktop" <?php selected( $options['devices'], 'desktop' ); ?>><?php esc_html_e( 'فقط دسکتاپ', 'contact-floating' ); ?></option>
								<option value="mobile" <?php selected( $options['devices'], 'mobile' ); ?>><?php esc_html_e( 'فقط موبایل', 'contact-floating' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'عنوان کنار آیکون', 'contact-floating' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="contact_floating_options[show_labels]" value="1" <?php checked( ! empty( $options['show_labels'] ) ); ?> />
								<?php esc_html_e( 'نمایش برچسب‌ها', 'contact-floating' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'انیمیشن', 'contact-floating' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="contact_floating_options[animation]" value="1" <?php checked( ! empty( $options['animation'] ) ); ?> />
								<?php esc_html_e( 'فعال', 'contact-floating' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'فاصله از پایین — دسکتاپ (px)', 'contact-floating' ); ?></th>
						<td>
							<input type="number" min="0" max="240" name="contact_floating_options[offset_bottom]" value="<?php echo esc_attr( (int) $options['offset_bottom'] ); ?>" class="small-text" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'فاصله از پایین — موبایل (px)', 'contact-floating' ); ?></th>
						<td>
							<input type="number" min="0" max="240" name="contact_floating_options[offset_bottom_mobile]" value="<?php echo esc_attr( (int) ( isset( $options['offset_bottom_mobile'] ) ? $options['offset_bottom_mobile'] : 88 ) ); ?>" class="small-text" />
							<p class="description"><?php esc_html_e( 'برای فاصله گرفتن از نوبار پایین موبایل (مثلاً ۸۰ تا ۱۰۰).', 'contact-floating' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'فاصله از کنار — دسکتاپ (px)', 'contact-floating' ); ?></th>
						<td>
							<input type="number" min="0" max="200" name="contact_floating_options[offset_side]" value="<?php echo esc_attr( (int) $options['offset_side'] ); ?>" class="small-text" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'فاصله از کنار — موبایل (px)', 'contact-floating' ); ?></th>
						<td>
							<input type="number" min="0" max="200" name="contact_floating_options[offset_side_mobile]" value="<?php echo esc_attr( (int) ( isset( $options['offset_side_mobile'] ) ? $options['offset_side_mobile'] : 12 ) ); ?>" class="small-text" />
						</td>
					</tr>
				</table>
			</section>

			<section class="cfloat-card">
				<h2><?php esc_html_e( 'دکمه اصلی', 'contact-floating' ); ?></h2>
				<p class="description"><?php esc_html_e( 'می‌توانید فقط نام، فقط آیکون، یا هر دو را نشان دهید.', 'contact-floating' ); ?></p>

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'متن دکمه', 'contact-floating' ); ?></th>
						<td>
							<input type="text" class="regular-text" name="contact_floating_options[main_button][label]" value="<?php echo esc_attr( $mb['label'] ); ?>" placeholder="<?php esc_attr_e( 'راه‌های ارتباطی', 'contact-floating' ); ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'آیکون', 'contact-floating' ); ?></th>
						<td>
							<select name="contact_floating_options[main_button][icon]" id="cfloat-main-icon">
								<option value="none" <?php selected( $mb['icon'], 'none' ); ?>><?php esc_html_e( 'بدون آیکون (فقط متن)', 'contact-floating' ); ?></option>
								<?php foreach ( $icon_labels as $key => $label ) : ?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $mb['icon'], $key ); ?>><?php echo esc_html( $label ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr class="cfloat-main-custom" <?php echo ( 'custom' === $mb['icon'] ) ? '' : 'hidden'; ?>>
						<th scope="row"><?php esc_html_e( 'SVG سفارشی', 'contact-floating' ); ?></th>
						<td>
							<textarea class="large-text code" rows="3" name="contact_floating_options[main_button][svg]" placeholder="<svg ...>...</svg>"><?php echo esc_textarea( $mb['svg'] ); ?></textarea>
						</td>
					</tr>
					<tr class="cfloat-main-custom" <?php echo ( 'custom' === $mb['icon'] ) ? '' : 'hidden'; ?>>
						<th scope="row"><?php esc_html_e( 'لینک SVG', 'contact-floating' ); ?></th>
						<td>
							<input type="url" class="regular-text" name="contact_floating_options[main_button][svg_url]" value="<?php echo esc_attr( $mb['svg_url'] ); ?>" placeholder="https://example.com/icon.svg" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'رنگ پس‌زمینه', 'contact-floating' ); ?></th>
						<td><input type="color" name="contact_floating_options[main_button][background_color]" value="<?php echo esc_attr( $mb['background_color'] ); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'رنگ آیکون / متن', 'contact-floating' ); ?></th>
						<td><input type="color" name="contact_floating_options[main_button][icon_color]" value="<?php echo esc_attr( $mb['icon_color'] ); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'رنگ بوردر', 'contact-floating' ); ?></th>
						<td><input type="color" name="contact_floating_options[main_button][border_color]" value="<?php echo esc_attr( $mb['border_color'] ); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'ضخامت بوردر (px)', 'contact-floating' ); ?></th>
						<td><input type="number" min="0" max="12" name="contact_floating_options[main_button][border_width]" value="<?php echo esc_attr( (int) $mb['border_width'] ); ?>" class="small-text" /></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'اندازه دکمه (px)', 'contact-floating' ); ?></th>
						<td><input type="number" min="40" max="96" name="contact_floating_options[main_button][size]" value="<?php echo esc_attr( (int) $mb['size'] ); ?>" class="small-text" /></td>
					</tr>
				</table>
			</section>
		</div>

		<section class="cfloat-card cfloat-card--items">
			<div class="cfloat-card__head">
				<h2><?php esc_html_e( 'راه‌های ارتباطی', 'contact-floating' ); ?></h2>
				<button type="button" class="button button-secondary" id="cfloat-add-item">
					<?php esc_html_e( '+ افزودن آیتم', 'contact-floating' ); ?>
				</button>
			</div>
			<p class="description"><?php esc_html_e( 'هر آیتم: نام، رنگ، آیکون (یا SVG/لینک SVG) و لینک. با درگ مرتب کنید. حداکثر ۲۰ مورد.', 'contact-floating' ); ?></p>

			<div id="cfloat-items" class="cfloat-items" data-cfloat-items>
				<?php
				foreach ( $items as $index => $item ) {
					$is_template = false;
					include CFLOAT_PATH . 'admin/views/partials-item-row.php';
				}
				?>
			</div>

		</section>

		<?php submit_button( __( 'ذخیره تنظیمات', 'contact-floating' ) ); ?>
	</form>

	<div class="cfloat-card cfloat-help">
		<h2><?php esc_html_e( 'نمایش در سایت', 'contact-floating' ); ?></h2>
		<p><?php esc_html_e( 'به‌صورت پیش‌فرض ویجت در فوتر همه صفحات نمایش داده می‌شود.', 'contact-floating' ); ?></p>
		<p><?php esc_html_e( 'برای نمایش دستی از شورت‌کد زیر استفاده کنید (نمایش تکراری خودکار غیرفعال می‌شود):', 'contact-floating' ); ?></p>
		<code>[contact_floating]</code>
	</div>
</div>
