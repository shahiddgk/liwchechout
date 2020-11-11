<?php
/**
 * Plugin Name: AutomateWoo - Refer A Friend Add-on
 * Plugin URI: https://automatewoo.com/addons/refer-a-friend/
 * Description: Refer A Friend add-on for AutomateWoo.
 * Version: 2.5.6
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0
 * Text Domain: automatewoo-referrals
 * Domain Path: /languages/
 *
 * WC requires at least: 3.0
 * WC tested up to: 4.1
 * Woo: 4871154:3fd134b42d7c710d96a6e6abd38718bc
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package AutomateWoo/Referrals
 */

defined( 'ABSPATH' ) || exit;


/**
 * @class AW_Referrals_Plugin_Data
 */
class AW_Referrals_Plugin_Data {

	function __construct() {
		$this->id                      = 'automatewoo-referrals';
		$this->name                    = __( 'AutomateWoo - Refer A Friend', 'automatewoo-referrals' );
		$this->version                 = '2.5.6';
		$this->file                    = __FILE__;
		$this->min_php_version         = '5.4';
		$this->min_automatewoo_version = '4.6.0';
		$this->min_woocommerce_version = '3.0.0';
	}
}



/**
 * @class AW_Referrals_Loader
 */
class AW_Referrals_Loader {

	/** @var AW_Referrals_Plugin_Data */
	static $data;

	static $errors = array();


	/**
	 * @param AW_Referrals_Plugin_Data $data
	 */
	static function init( $data ) {
		self::$data = $data;

		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
		add_action( 'plugins_loaded', array( __CLASS__, 'load' ) );
		add_action( 'plugins_loaded', array( __CLASS__, 'load_textdomain' ) );
	}


	static function load() {
		self::check();
		if ( empty( self::$errors ) ) {
			include 'includes/automatewoo-referrals.php';
		}
	}


	static function load_textdomain() {
		load_plugin_textdomain( 'automatewoo-referrals', false, "automatewoo-referrals/languages" );
	}


	static function check() {

		$inactive_text = '<strong>' . sprintf( __( '%s is inactive.', 'automatewoo-referrals' ), self::$data->name ) . '</strong>';

		if ( version_compare( phpversion(), self::$data->min_php_version, '<' ) ) {
			self::$errors[] = sprintf( __( '%1$s The plugin requires PHP version %2$s or newer.', 'automatewoo-referrals' ), $inactive_text, self::$data->min_php_version );
		}

		if ( ! self::is_automatewoo_active() ) {
			self::$errors[] = sprintf( __( '%s The plugin requires AutomateWoo to be installed and activated.', 'automatewoo-referrals' ), $inactive_text );
		} elseif ( ! self::is_automatewoo_version_ok() ) {
			self::$errors[] = sprintf( __( '%1$s The plugin requires AutomateWoo version %2$s or newer.', 'automatewoo-referrals' ), $inactive_text, self::$data->min_automatewoo_version );
		} elseif ( ! self::is_automatewoo_directory_name_ok() ) {
			self::$errors[] = sprintf( __( '%s AutomateWoo plugin directory name is not correct.', 'automatewoo-referrals' ), $inactive_text );
		}

		if ( ! self::is_woocommerce_version_ok() ) {
			self::$errors[] = sprintf( __( '%1$s The plugin requires WooCommerce version %2$s or newer.', 'automatewoo-referrals' ), $inactive_text, self::$data->min_woocommerce_version );
		}
	}


	/**
	 * @return bool
	 */
	static function is_automatewoo_active() {
		return function_exists( 'AW' );
	}


	/**
	 * @return bool
	 */
	static function is_automatewoo_version_ok() {
		if ( ! function_exists( 'AW' ) ) return false;
		return version_compare( AW()->version, self::$data->min_automatewoo_version, '>=' );
	}


	/**
	 * @return bool
	 */
	static function is_woocommerce_version_ok() {
		if ( ! function_exists( 'WC' ) ) return false;
		if ( ! self::$data->min_woocommerce_version ) return true;
		return version_compare( WC()->version, self::$data->min_woocommerce_version, '>=' );
	}


	/**
	 * @return bool
	 */
	static function is_automatewoo_directory_name_ok() {
		$active_plugins = (array) get_option( 'active_plugins', [] );
		return in_array( 'automatewoo/automatewoo.php', $active_plugins ) || array_key_exists( 'automatewoo/automatewoo.php', $active_plugins );
	}


	static function admin_notices() {
		if ( empty( self::$errors ) ) return;
		echo '<div class="notice notice-error"><p>';
		echo wp_kses_post( implode( '<br>', self::$errors ) );
		echo '</p></div>';
	}


}

AW_Referrals_Loader::init( new AW_Referrals_Plugin_Data() );
