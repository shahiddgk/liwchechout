<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Integration with WooCommerce
 *
 * @class RP_Decorator_WC
 * @package Decorator
 * @author WebToffee
 */
if (!class_exists('RP_Decorator_WC')) {

class RP_Decorator_WC
{
    // Properties
    private static $overwrite_options = null;

    // Settings to overwrite
    public static $default_setting_replacement = array(
        'woocommerce_email_header_image'            => 'header_image',
        'woocommerce_email_footer_text'             => 'footer_content_text',
        'woocommerce_email_base_color'              => null,
        'woocommerce_email_background_color'        => 'background_color',
        'woocommerce_email_body_background_color'   => 'email_background_color',
        'woocommerce_email_text_color'              => 'text_color',
    );

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
        // Replace default WooCommerce email style settings with Open Decorator button
        add_filter('woocommerce_email_settings', array($this, 'replace_woocommerce_email_settings'));
        add_action('woocommerce_admin_field_rp_decorator_open_customizer_button', array($this, 'print_open_customizer_button'));

        // Overwrite default email settings
        if (RP_Decorator_WC::overwrite_options()) {
            foreach (RP_Decorator_WC::$default_setting_replacement as $option => $replacement) {
                add_filter('pre_option_' . $option, array($this, 'overwrite_default_email_settings'), 99, 2);
            }
        }
    }

    /**
     * Check if WooCommerce settings need to be overwritten and custom styles applied
     * This is true when plugin is active and at least one custom option is stored in the database
     *
     * @access public
     * @return bool
     */
    public static function overwrite_options()
    {
        // Check if any settings were saved
        if (self::$overwrite_options === null) {
            $option = get_option('rp_decorator', array());
            self::$overwrite_options = !empty($option);
        }

        // Return result
        return self::$overwrite_options;
    }

    /**
     * Overwrite default email settings
     *
     * @access public
     * @param mixed $value
     * @param string $option
     * @return mixed
     */
    public function overwrite_default_email_settings($value, $option)
    {
        // Check if we know what the replacement is
        if (isset(RP_Decorator_WC::$default_setting_replacement[$option])) {

            // Get value from a set of custom values and return it
            return RP_Decorator_Customizer::get_stored_value(RP_Decorator_WC::$default_setting_replacement[$option], $value);
        }

        // Return original value
        return $value;
    }

    /**
     * Replace default WooCommerce email style settings with Open Decorator button
     *
     * @access public
     * @param array $settings
     * @return array
     */
    public function replace_woocommerce_email_settings($settings)
    {
        // Define options that need to be replaced
        $replace = array_merge(array_keys(RP_Decorator_WC::$default_setting_replacement), array('email_template_options'));

        // Iterate over settings and unset those that are available in Customizer
        foreach ($settings as $setting_key => $setting) {
            if (isset($setting['id']) && in_array($setting['id'], $replace, true)) {
                unset($settings[$setting_key]);
            }
        }

        // Open section
        $settings[] = array(
            'id'    => 'rp_decorator',
            'type'  => 'title',
            'title' => __('Decorator', 'decorator-woocommerce-email-customizer'),
        );

        // Add Open Decorator button
        $settings[] = array(
            'id'    => 'rp_decorator_open_customizer_button',
            'type'  => 'rp_decorator_open_customizer_button',
        );

        // Close section
        $settings[] = array(
            'id'    => 'rp_decorator',
            'type'  => 'sectionend',
        );

        // Return remaining settings
        return $settings;
    }

    /**
     * Print Open Decorator button
     *
     * @access public
     * @param array $options
     * @return void
     */
    public function print_open_customizer_button($options)
    {
        ?><tr valign="top">
            <th scope="row" class="titledesc">
                <?php _e('Customize WooCommerce Emails', 'decorator-woocommerce-email-customizer'); ?>
            </th>
            <td class="forminp forminp-<?php echo sanitize_title($options['type']); ?>">
                <a href="<?php echo RP_Decorator_Customizer::get_customizer_url(); ?>">
                    <button type="button" class="button button-secondary" value="<?php _e('Open Decorator', 'decorator-woocommerce-email-customizer'); ?>">
                        <?php _e('Open Decorator', 'decorator-woocommerce-email-customizer'); ?>
                    </button>
                </a>
                <p class="description"><?php printf(__('Make WooCommerce emails match your brand. <a href="%s">Decorator</a> plugin by <a href="%s">WebToffee</a>.', 'decorator-woocommerce-email-customizer'), 'https://wordpress.org/plugins/decorator-woocommerce-email-customizer', 'https://www.webtoffee.com'); ?></p>
            </td>
        </tr><?php
    }

    /**
     * Get WooCommerce email settings page URL
     *
     * @access public
     * @return string
     */
    public static function get_email_settings_page_url()
    {
        return admin_url('admin.php?page=wc-settings&tab=email');
    }





}

RP_Decorator_WC::get_instance();

}
