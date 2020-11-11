<?php
/**
 * Plugin Name:       Woo Custom Related Products
 * Plugin URI:        http://www.wpcodelibrary.com/
 * Description:       Woo Custom Related Products for WooCommerce allows you to choose related products for the particular product.
 * Version:           1.3.2
 * Author:            WPCodelibrary
 * Author URI:        http://www.wpcodelibrary.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-custom-related-products
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if (!defined('WCRP_PLUGIN_URL'))
    define('WCRP_PLUGIN_URL', plugin_dir_url(__FILE__));

if (!defined('WCRP_PLUGIN_DIR')) {
    define('WCRP_PLUGIN_DIR', dirname(__FILE__));
}
if (!defined('WCRP_PLUGIN_DIR_PATH')) {
    define('WCRP_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
}



/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woo-custom-related-products-activator.php
 */
function activate_woo_custom_related_products() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-custom-related-products-activator.php';
	Woo_Custom_Related_Products_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woo-custom-related-products-deactivator.php
 */
function deactivate_woo_custom_related_products() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-custom-related-products-deactivator.php';
	Woo_Custom_Related_Products_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woo_custom_related_products' );
register_deactivation_hook( __FILE__, 'deactivate_woo_custom_related_products' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woo-custom-related-products.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woo_custom_related_products() {

	$plugin = new Woo_Custom_Related_Products();
	$plugin->run();

}
run_woo_custom_related_products();
