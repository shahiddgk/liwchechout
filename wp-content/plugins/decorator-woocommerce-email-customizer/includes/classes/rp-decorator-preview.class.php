<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Preview for WordPress Customizer
 *
 * @class RP_Decorator_Preview
 * @package Decorator
 * @author WebToffee
 */
if (!class_exists('RP_Decorator_Preview')) {

class RP_Decorator_Preview
{
    // WooCommerce email classes
    public static $email_classes = array(
        'WC_Email_New_Order'                    => 'processing',
        'WC_Email_Cancelled_Order'              => 'cancelled',
        'WC_Email_Failed_Order'                 => 'failed',
        'WC_Email_Customer_On_Hold_Order'       => 'on-hold',
        'WC_Email_Customer_Processing_Order'    => 'processing',
        'WC_Email_Customer_Completed_Order'     => 'completed',
        'WC_Email_Customer_Refunded_Order'      => 'refunded',
        'WC_Email_Customer_Invoice'             => 'processing',
        'WC_Email_Customer_Note'                => 'processing',
        'WC_Email_Customer_Reset_Password'      => null,
        'WC_Email_Customer_New_Account'         => null,
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
        // Set up preview
        add_action('parse_request', array($this, 'set_up_preview'));
    }

    /**
     * Set up preview
     *
     * @access public
     * @return void
     */
    public function set_up_preview()
    {
        // Make sure this is own preview request
        if (!RP_Decorator::is_own_preview_request()) {
            return;
        }

        //make sure this user has the administrative capability
        if (!RP_Decorator::is_admin()) {
            return;
        }

        // Load main view
        include RP_DECORATOR_PLUGIN_PATH . 'includes/views/preview.php';

        // Do not load any further elements
        exit;
    }

    /**
     * Print preview email
     *
     * @access public
     * @return void
     */
    public static function print_preview_email()
    {
        // Load WooCommerce emails
        $wc_emails = WC_Emails::instance();
        $emails = $wc_emails->get_emails();

        // Reference email
        if (isset($emails['WC_Email_Customer_Processing_Order']) && is_object($emails['WC_Email_Customer_Processing_Order'])) {
            $email = $emails['WC_Email_Customer_Processing_Order'];
            $order_status = 'processing';
        }
        else {
            // Note: currently this is only used as a fallback
            foreach (RP_Decorator_Preview::$email_classes as $email_class => $order_status) {
                if (isset($emails[$email_class]) && is_object($emails[$email_class])) {
                    $email = $emails[$email_class];
                    break;
                }
            }
        }

// Note: need to set specific properties for specific emails when implementing full support for different emails

// NEW ACCOUNT
// $this->object             = new WP_User( $user_id );
// $this->user_pass          = $user_pass;
// $this->user_login         = stripslashes( $this->object->user_login );
// $this->user_email         = stripslashes( $this->object->user_email );
// $this->recipient          = $this->user_email;
// $this->password_generated = $password_generated;

// CUSTOMER NOTE
// $this->customer_note           = $customer_note;

// REFUNDED ORDER
// $this->partial_refund = $partial_refund;
// $this->set_email_strings( $partial_refund );    <-- run this after setting $partial_refund

// RESET PASSWORD
// $this->object     = get_user_by( 'login', $user_login );
// $this->user_login = $user_login;
// $this->reset_key  = $reset_key;
// $this->user_email = stripslashes( $this->object->user_email );
// $this->recipient  = $this->user_email;

        // Order object
        $email->object = RP_Decorator_Preview::get_wc_order_for_preview($order_status);

        // Macros
        $email->find['order-date']      = '{order_date}';
        $email->find['order-number']    = '{order_number}';
        $email->replace['order-date']   = method_exists($email->object, 'get_date_created') ? wc_format_datetime($email->object->get_date_created()) : date_i18n(wc_date_format(), strtotime($email->object->order_date));
        $email->replace['order-number'] = $email->object->get_order_number();

        // Other properties
        $email->recipient = method_exists($email->object, 'get_billing_email') ? $email->object->get_billing_email() : $email->object->billing_email;

        // Get email content and apply styles
        $content = $email->get_content_html();
        $content = $email->style_inline($content);

        // Print email content
        echo $content;

        // Print live preview scripts in footer
        add_action('wp_footer', array('RP_Decorator_Preview', 'print_live_preview_scripts'), 99);
    }

    /**
     * Get WooCommerce order for preview
     *
     * @access public
     * @param string $order_status
     * @return object
     */
    public static function get_wc_order_for_preview($order_status = null)
    {
        // Get last WooCommerce order
        $last_order_id = get_posts(array(
            'numberposts'   => 1,
            'orderby'       => 'ID',
            'post_type'     => 'shop_order',
            'post_status'   => array('wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed'),
            'fields'        => 'ids',
        ));

        // Use real order
        if (!empty($last_order_id)) {
            $last_order_id = array_pop($last_order_id);
            return wc_get_order($last_order_id);
        }
        // Use mockup order (WC 2.7+)
        else if (RP_Decorator::wc_version_gte('2.7')) {

            // Instantiate order object
            $order = new WC_Order();

            // Other order properties
            $order->set_props(array(
                'id'                    => 1,
                'status'                => ($order_status === null ? 'processing' : $order_status),
                'billing_first_name'    => 'Sherlock',
                'billing_last_name'     => 'Holmes',
                'billing_company'       => 'Detectives Ltd.',
                'billing_address_1'     => '221B Baker Street',
                'billing_city'          => 'London',
                'billing_postcode'      => 'NW1 6XE',
                'billing_country'       => 'GB',
                'billing_email'         => 'sherlock@holmes.co.uk',
                'billing_phone'         => '02079304832',
                'date_created'          => date('Y-m-d H:i:s'),
                'total'                 => 24.90,
            ));

            // Item #1
            $order_item = new WC_Order_Item_Product();
            $order_item->set_props(array(
                'name'      => 'A Study in Scarlet',
                'subtotal'  => '9.95',
            ));
            $order->add_item($order_item);

            // Item #2
            $order_item = new WC_Order_Item_Product();
            $order_item->set_props(array(
                'name'      => 'The Hound of the Baskervilles',
                'subtotal'  => '14.95',
            ));
            $order->add_item($order_item);

            // Return mockup order
            return $order;
        }
        // Use mockup order (pre WC 2.7)
        else {

            // Include mockup order class
            if (!class_exists('RP_Decorator_Mockup_Order')) {
                include RP_DECORATOR_PLUGIN_PATH . 'includes/classes/lazy/rp-decorator-mockup-order.class.php';
            }

            // Instantiate order object
            $order = new RP_Decorator_Mockup_Order();

            // Set order status
            if ($order_status) {
                $order->status = $order_status;
            }

            // Return mockup order
            return $order;
        }
    }

    /**
     * Print live preview scripts
     *
     * @access public
     * @return void
     */
    public static function print_live_preview_scripts()
    {
        // Open container
        $scripts = '<script type="text/javascript">jQuery(document).ready(function() {';

        // Font family mapping
        $scripts .= 'var font_family_mapping = ' . json_encode(RP_Decorator_Settings::$font_family_mapping) . ';';

        // Function to handle special cases
        $scripts .= "function prepare(value, key, selector) {
            if (key === 'border_radius' && selector === '#template_header') {
                value = value.replace('!important', '').trim();
                value = value + ' ' + value + ' 0 0 !important';
            }
            else if (key === 'email_padding' && selector === '#wrapper') {
                value = value + ' 0 ' + value + ' 0';
            }
            else if (key === 'footer_padding' && selector === '#template_footer #credit') {
                value = '0 ' + value + ' ' + value + ' ' + value;
            }
            else if (key === 'footer_content_text' && value !== '') {
                value = '<p>' + value + '</p>';
            }
            else if (key === 'shadow') {
                value = '0 ' + (value > 0 ? 1 : 0) + 'px ' + (value * 4) + 'px ' + value + 'px rgba(0,0,0,0.1) !important';
            }
            else if (key.match(/font_family$/)) {
                value = typeof font_family_mapping[value] !== 'undefined' ? font_family_mapping[value] : value;
            }
            else if (key === 'header_image') {
                value = '<p style=\"margin-top:0;\"><img src=\"' + value + '\" style=\"border: none; display: inline; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;\" /></p>';
            }
            return value;
        }";

        // Get CSS suffixes
        $scripts .= 'var suffixes = ' . json_encode(RP_Decorator_Customizer::get_css_suffix()) . ';';

        // Iterate over settings
        foreach (RP_Decorator_Settings::get_settings() as $setting_key => $setting) {

            // No live method
            if (!isset($setting['live_method'])) {
                continue;
            }

            // Iterate over selectors
            if (in_array($setting['live_method'], array('css', 'property')) && !empty($setting['selectors'])) {
                foreach ($setting['selectors'] as $selector => $properties) {

                    // Iterate over properties
                    foreach ($properties as $property) {

                        // CSS value change
                        if (!isset($setting['live_method']) || $setting['live_method'] === 'css') {
                            $scripts .= "wp.customize('rp_decorator[$setting_key]', function(value) {
                                value.bind(function(newval) {
                                    newval = newval + (typeof suffixes['$setting_key'] !== 'undefined' ? suffixes['$setting_key'] : '');
                                    newval = prepare(newval, '$setting_key', '$selector');
                                    jQuery('$selector').css('$property', '').attr('style', function(i, s) { return (s||'') + '$property: ' + newval + ';' });
                                });
                            });";
                        }

                        // DOM object property
                        if ($setting['live_method'] === 'property') {
                            $scripts .= "wp.customize('rp_decorator[$setting_key]', function(value) {
                                value.bind(function(newval) {
                                    newval = newval + (typeof suffixes['$setting_key'] !== 'undefined' ? suffixes['$setting_key'] : '');
                                    newval = prepare(newval, '$setting_key', '$selector');
                                    jQuery('$selector').prop('$property', newval);
                                });
                            });";
                        }
                    }
                }
            }

            // HTML Replace
            if ($setting['live_method'] === 'replace' && !empty($setting['selectors'])) {
                foreach ($setting['selectors'] as $selector) {
                    $original = json_encode($setting['original']);
                    $scripts .= "wp.customize('rp_decorator[$setting_key]', function(value) {
                        value.bind(function(newval) {
                            newval = (newval !== '' ? newval : $original);
                            newval = prepare(newval, '$setting_key', '$selector');
                            jQuery('$selector').html(newval);
                        });
                    });";
                }
            }
        }

        // Close container and return
        echo $scripts . '});</script>';
    }




}

RP_Decorator_Preview::get_instance();

}
