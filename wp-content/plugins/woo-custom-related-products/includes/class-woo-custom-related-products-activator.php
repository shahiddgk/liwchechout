<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Woo_Custom_Related_Products
 * @subpackage Woo_Custom_Related_Products/includes
 * @author     WPCodelibrary <support@wpcodelibrary.com>
 */
class Woo_Custom_Related_Products_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		
		set_transient( '_wcrp_screen_activation_redirect', true, 30 );
         add_option( 'wcrp_version', Woo_Custom_Related_Products::VERSION);

	}

}
