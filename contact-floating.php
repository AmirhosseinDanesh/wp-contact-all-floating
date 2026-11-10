<?php
/**
 * Plugin Name:       Contact Floating
 * Plugin URI:        https://github.com/YOUR_USER/wp-contact-all-floating
 * Description:       Floating contact widget for social links, phone, email and more. Simple admin, no Elementor/jQuery dependency.
 * Version:           1.0.4
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Contact Floating
 * Author URI:        https://github.com/YOUR_USER/wp-contact-all-floating
 * Text Domain:       contact-floating
 * Domain Path:       /languages
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package ContactFloating
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CFLOAT_VERSION', '1.0.4' );
define( 'CFLOAT_FILE', __FILE__ );
define( 'CFLOAT_PATH', plugin_dir_path( __FILE__ ) );
define( 'CFLOAT_URL', plugin_dir_url( __FILE__ ) );
define( 'CFLOAT_BASENAME', plugin_basename( __FILE__ ) );
define( 'CFLOAT_OPTION_KEY', 'contact_floating_options' );
define( 'CFLOAT_MAX_ITEMS', 20 );

require_once CFLOAT_PATH . 'includes/class-sanitizer.php';
require_once CFLOAT_PATH . 'includes/class-icons.php';
require_once CFLOAT_PATH . 'includes/class-settings.php';
require_once CFLOAT_PATH . 'includes/class-admin.php';
require_once CFLOAT_PATH . 'includes/class-frontend.php';
require_once CFLOAT_PATH . 'includes/class-plugin.php';

register_activation_hook( __FILE__, array( 'ContactFloating\\Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'ContactFloating\\Plugin', 'deactivate' ) );

ContactFloating\Plugin::instance();
