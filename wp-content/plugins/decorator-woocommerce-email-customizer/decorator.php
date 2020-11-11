<?php

/**
 * Plugin Name: Decorator - WooCommerce Email Customizer
 * Plugin URI: 
 * Description: Use native WordPress Customizer to make WooCommerce emails match your brand
 * Version: 1.0.8
 * Author: WebToffee
 * Author URI: https://www.webtoffee.com
 * Requires at least: 4.4
 * Tested up to: 5.5
 * WC tested up to: 4.5.2
 * Text Domain: decorator-woocommerce-email-customizer
 * Domain Path: /languages
 *
 * Copyright 2020 WebToffee (email: info@webtoffee.com)
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
 * @package Decorator
 * @category Core
 * @author WebToffee
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define Constants
define('RP_DECORATOR_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RP_DECORATOR_PLUGIN_URL', plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__)));
define('RP_DECORATOR_VERSION', '1.0.8');
define('RP_DECORATOR_SUPPORT_PHP', '5.3');
define('RP_DECORATOR_SUPPORT_WP', '4.4');
define('RP_DECORATOR_SUPPORT_WC', '2.4');

if (!class_exists('RP_Decorator')) {

/**
 * Main plugin class
 *
 * @package Decorator
 * @author WebToffee
 */
class RP_Decorator
{
    // Properties
    private static $admin_capability = null;

    // Singleton instance
    private static $instance = false;

    /**
     * Singleton control
     */
    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Class constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Load translation
        load_textdomain('decorator-woocommerce-email-customizer', WP_LANG_DIR . '/decorator-woocommerce-email-customizer/rp_decorator-' . apply_filters('plugin_locale', get_locale(), 'decorator-woocommerce-email-customizer') . '.mo');
        load_plugin_textdomain('decorator-woocommerce-email-customizer', false, dirname(plugin_basename(__FILE__)) . '/languages/');

        // Execute other code when all plugins are loaded
        add_action('plugins_loaded', array($this, 'on_plugins_loaded'), 1);
    }

    /**
     * Code executed when all plugins are loaded
     *
     * @access public
     * @return void
     */
    public function on_plugins_loaded()
    {
        // Check environment
        if (!RP_Decorator::check_environment()) {
            return;
        }

        // Includes
        foreach (glob(RP_DECORATOR_PLUGIN_PATH . 'includes/classes/*.class.php') as $filename) {
            include $filename;
        }
        include RP_DECORATOR_PLUGIN_PATH . 'includes/rp-decorator-uninstall-feedback.php';

        // Plugins page links
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'plugins_page_links'));
    }

    /**
     * Check if environment meets requirements
     *
     * @access public
     * @return bool
     */
    public static function check_environment()
    {
        $is_ok = true;

        // Check PHP version
        if (!RP_Decorator::php_version_gte(RP_DECORATOR_SUPPORT_PHP)) {
            add_action('admin_notices', array('RP_Decorator', 'php_version_notice'));
        }

        // Check WordPress version
        if (!RP_Decorator::wp_version_gte(RP_DECORATOR_SUPPORT_WP)) {
            add_action('admin_notices', array('RP_Decorator', 'wp_version_notice'));
            $is_ok = false;
        }

        // Check if WooCommerce is enabled
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array('RP_Decorator', 'wc_disabled_notice'));
            $is_ok = false;
        }
        else if (!RP_Decorator::wc_version_gte(RP_DECORATOR_SUPPORT_WC)) {
            add_action('admin_notices', array('RP_Decorator', 'wc_version_notice'));
            $is_ok = false;
        }

        return $is_ok;
    }

    /**
     * Check PHP version
     *
     * @access public
     * @param string $version
     * @return bool
     */
    public static function php_version_gte($version)
    {
        return version_compare(PHP_VERSION, $version, '>=');
    }

    /**
     * Check WordPress version
     *
     * @access public
     * @param string $version
     * @return bool
     */
    public static function wp_version_gte($version)
    {
        $wp_version = get_bloginfo('version');

        // Treat release candidate strings
        $wp_version = preg_replace('/-RC.+/i', '', $wp_version);

        if ($wp_version) {
            return version_compare($wp_version, $version, '>=');
        }

        return false;
    }

    /**
     * Check WooCommerce version
     *
     * @access public
     * @param string $version
     * @return bool
     */
    public static function wc_version_gte($version)
    {
        if (defined('WC_VERSION') && WC_VERSION) {
            return version_compare(WC_VERSION, $version, '>=');
        }
        else if (defined('WOOCOMMERCE_VERSION') && WOOCOMMERCE_VERSION) {
            return version_compare(WOOCOMMERCE_VERSION, $version, '>=');
        }
        else {
            return false;
        }
    }

    /**
     * Display PHP version notice
     *
     * @access public
     * @return void
     */
    public static function php_version_notice()
    {
        echo '<div class="error"><p>' . sprintf(__('<strong>Decorator</strong> requires PHP %s or later. Please update PHP on your server to use this plugin.', 'decorator-woocommerce-email-customizer'), RP_DECORATOR_SUPPORT_PHP) . '</p></div>';
    }

    /**
     * Display WP version notice
     *
     * @access public
     * @return void
     */
    public static function wp_version_notice()
    {
        echo '<div class="error"><p>' . sprintf(__('<strong>Decorator</strong> requires WordPress version %s or later. Please update WordPress to use this plugin.', 'decorator-woocommerce-email-customizer'), RP_DECORATOR_SUPPORT_WP) . '</p></div>';
    }

    /**
     * Display WC disabled notice
     *
     * @access public
     * @return void
     */
    public static function wc_disabled_notice()
    {
        echo '<div class="error"><p>' . sprintf(__('<strong>Decorator</strong> requires WooCommerce to be active. You can download WooCommerce %s.', 'decorator-woocommerce-email-customizer'), '<a href="https://wordpress.org/plugins/woocommerce">' . __('here', 'decorator-woocommerce-email-customizer') . '</a>') . '</p></div>';
    }

    /**
     * Display WC version notice
     *
     * @access public
     * @return void
     */
    public static function wc_version_notice()
    {
        echo '<div class="error"><p>' . sprintf(__('<strong>Decorator</strong> requires WooCommerce version %s or later. Please update WooCommerce to use this plugin.', 'decorator-woocommerce-email-customizer'), RP_DECORATOR_SUPPORT_WC) . '</p></div>';
    }

    /**
     * Plugins page links
     *
     * @access public
     * @param array $links
     * @return array
     */
    public function plugins_page_links($links)
    {

	    $plugin_links = array( '<a href="' . RP_Decorator_Customizer::get_customizer_url() . '"  target="_blank">' . __( 'Open Decorator', 'decorator-woocommerce-email-customizer' ) . '</a>',
        '<a href="https://wordpress.org/support/plugin/decorator-woocommerce-email-customizer/"  target="_blank">' . __( 'Support', 'decorator-woocommerce-email-customizer' ) . '</a>' );
        
        if (array_key_exists('deactivate', $links)) {
            $links['deactivate'] = str_replace('<a', '<a class="decorator-deactivate-link"', $links['deactivate']);
        }
	     return array_merge($plugin_links, $links);
    }

    /**
     * Check if current user has administrative capability
     *
     * @access public
     * @return bool
     */
    public static function is_admin()
    {
        return current_user_can(RP_Decorator::get_admin_capability());
    }

    /**
     * Get admininistrative capability
     *
     * @access public
     * @return string
     */
    public static function get_admin_capability()
    {
        // Get capability
        if (self::$admin_capability === null) {
            self::$admin_capability = apply_filters('rp_decorator_capability', 'manage_woocommerce');
        }

        // Return capability
        return self::$admin_capability;
    }

    /**
     * Check if current request is own Customizer request
     *
     * @access public
     * @return bool
     */
    public static function is_own_customizer_request()
    {
        return isset($_REQUEST['rp-decorator-customize']) && $_REQUEST['rp-decorator-customize'] === '1';
    }

    /**
     * Check if current request is preview request
     *
     * @access public
     * @return bool
     */
    public static function is_own_preview_request()
    {
        return isset($_REQUEST['rp-decorator-preview']) && $_REQUEST['rp-decorator-preview'] === '1';
    }



}

RP_Decorator::get_instance();

}
