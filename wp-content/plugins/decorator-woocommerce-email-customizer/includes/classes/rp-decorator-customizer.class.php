<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Customizer Setup
 *
 * @class RP_Decorator_Customizer
 * @package Decorator
 * @author WebToffee
 */
if (!class_exists('RP_Decorator_Customizer')) {

class RP_Decorator_Customizer
{
    // Properties
    private static $panels_added    = array();
    private static $sections_added  = array();
    private static $css_suffixes    = null;
    public static $customizer_url  = null;

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
        // Add settings
        add_action('customize_register', array($this, 'add_settings'));

        // Maybe add custom styles to default WooCommerce styles
        add_filter('woocommerce_email_styles', array($this, 'maybe_add_custom_styles'), 9999);

        // Ajax handler
        add_action('wp_ajax_rp_decorator_reset', array($this, 'ajax_reset'));

        // Only proceed if this is own request
        if (!RP_Decorator::is_own_customizer_request() && !RP_Decorator::is_own_preview_request()) {
            return;
        }

        // Add controls, sections and panels
        add_action('customize_register', array($this, 'add_controls'));

        // Add user capability
        add_filter('user_has_cap', array($this, 'add_customize_capability'), 99);

        // Change site name
        add_filter('option_blogname', array($this, 'change_site_name'), 99);

        // Remove unrelated components
        add_filter('customize_loaded_components', array($this, 'remove_unrelated_components'), 99, 2);

        // Remove unrelated sections
        add_filter('customize_section_active', array($this, 'remove_unrelated_sections'), 99, 2);

        // Remove unrelated controls
        add_filter('customize_control_active', array($this, 'remove_unrelated_controls'), 99, 2);

        // Enqueue Customizer scripts
        add_filter('customize_controls_enqueue_scripts', array($this, 'enqueue_customizer_scripts'));
    }

    /**
     * Add customizer capability
     *
     * @access public
     * @param array $capabilities
     * @return array
     */
    public function add_customize_capability($capabilities)
    {
        // Remove filter (circular reference)
        remove_filter('user_has_cap', array($this, 'add_customize_capability'), 99);

        // Add customize capability for admin user if this is own customizer request
        if (RP_Decorator::is_admin() && RP_Decorator::is_own_customizer_request()) {
            $capabilities['customize'] = true;
        }

        // Add filter
        add_filter('user_has_cap', array($this, 'add_customize_capability'), 99);

        // Return capabilities
        return $capabilities;
    }

    /**
     * Get Customizer URL
     *
     * @access public
     * @return string
     */
    public static function get_customizer_url()
    {
        if (RP_Decorator_Customizer::$customizer_url === null) {
            RP_Decorator_Customizer::$customizer_url = add_query_arg(array(
                'rp-decorator-customize'  => '1',
                'url'                   => urlencode(add_query_arg(array('rp-decorator-preview' => '1'), site_url('/'))),
                'return'                => urlencode(RP_Decorator_WC::get_email_settings_page_url()),
            ), admin_url('customize.php'));
        }

        return RP_Decorator_Customizer::$customizer_url;
    }

    /**
     * Change site name
     *
     * @access public
     * @param string $name
     * @return string
     */
    public function change_site_name($name)
    {
        return __('WooCommerce Emails', 'decorator-woocommerce-email-customizer');
    }

    /**
     * Remove unrelated components
     *
     * @access public
     * @param array $components
     * @param object $wp_customize
     * @return array
     */
    public function remove_unrelated_components($components, $wp_customize)
    {
        // Iterate over components
        foreach ($components as $component_key => $component) {

            // Check if current component is own component
            if (!RP_Decorator_Customizer::is_own_component($component)) {
                unset($components[$component_key]);
            }
        }

        // Return remaining components
        return $components;
    }

    /**
     * Remove unrelated sections
     *
     * @access public
     * @param bool $active
     * @param object $section
     * @return bool
     */
    public function remove_unrelated_sections($active, $section)
    {
        // Check if current section is own section
        if (!RP_Decorator_Customizer::is_own_section($section->id)) {
            return false;
        }

        // We can override $active completely since this runs only on own Customizer requests
        return true;
    }

    /**
     * Remove unrelated controls
     *
     * @access public
     * @param bool $active
     * @param object $control
     * @return bool
     */
    public function remove_unrelated_controls($active, $control)
    {
        // Check if current control belongs to own section
        if (!RP_Decorator_Customizer::is_own_section($control->section)) {
            return false;
        }

        // We can override $active completely since this runs only on own Customizer requests
        return true;
    }

    /**
     * Check if current component is own component
     *
     * @access public
     * @param string $component
     * @return bool
     */
    public static function is_own_component($component)
    {
        return false;
    }

    /**
     * Check if current section is own section
     *
     * @access public
     * @param string $key
     * @return bool
     */
    public static function is_own_section($key)
    {
        // Iterate over own sections
        foreach (RP_Decorator_Settings::get_sections() as $section_key => $section) {
            if ($key === 'rp_decorator_' . $section_key) {
                return true;
            }
        }

        // Section not found
        return false;
    }

    /**
     * Enqueue Customizer scripts
     *
     * @access public
     * @return void
     */
    public function enqueue_customizer_scripts()
    {
        // Enqueue Customizer script
        wp_enqueue_script('rp-decorator-customizer-scripts', RP_DECORATOR_PLUGIN_URL . '/assets/js/customizer-scripts.js', array('jquery'), RP_DECORATOR_VERSION, true);

        // Send variables to Javascript
        wp_localize_script('rp-decorator-customizer-scripts', 'rp_decorator', array(
            'ajax_url'              => admin_url('admin-ajax.php'),
            'customizer_url'        => RP_Decorator_Customizer::get_customizer_url(),
            'labels'                => array(
                'reset'                 => __('Reset', 'decorator-woocommerce-email-customizer'),
                'reset_confirmation'    => __('Are you sure you want to reset all changes made to your WooCommerce emails?', 'decorator-woocommerce-email-customizer'),
                'description'           => __('<p>Use native WordPress Customizer to make WooCommerce emails match your brand.</p>', 'decorator-woocommerce-email-customizer') . '<p>' . sprintf(__('<a href="%s">Decorator</a> plugin by <a href="%s">WebToffee</a>.', 'decorator-woocommerce-email-customizer'), 'https://wordpress.org/plugins/decorator-woocommerce-email-customizer', 'https://www.webtoffee.com') . '</p>',
            ),
        ));
    }

    /**
     * Add settings
     *
     * @access public
     * @param object $wp_customize
     * @return void
     */
    public function add_settings($wp_customize)
    {
        // Iterate over settings
        foreach (RP_Decorator_Settings::get_settings() as $setting_key => $setting) {

            // Add setting
            $wp_customize->add_setting('rp_decorator[' . $setting_key . ']' , array(
                'type'          => 'option',
                'transport'     => 'postMessage',
                'capability'    => RP_Decorator::get_admin_capability(),
                'default'       => isset($setting['default']) ? $setting['default'] : '',
            ));
        }
    }

    /**
     * Add controls, sections and panels
     *
     * @access public
     * @param object $wp_customize
     * @return void
     */
    public function add_controls($wp_customize)
    {
        // Iterate over settings
        foreach (RP_Decorator_Settings::get_settings() as $setting_key => $setting) {

            // Maybe add section
            RP_Decorator_Customizer::maybe_add_section($wp_customize, $setting);

            // Maybe add panel
            RP_Decorator_Customizer::maybe_add_panel($wp_customize, $setting);

            // Get control class name (none, color, upload, image)
            $control_class = isset($setting['control_type']) ? ucfirst($setting['control_type']) . '_' : '';
            $control_class = 'WP_Customize_' . $control_class . 'Control';

            // Control configuration
            $control_config = array(
                'label'         => $setting['title'],
                'settings'      => 'rp_decorator[' . $setting_key . ']',
                'capability'    => RP_Decorator::get_admin_capability(),
                'priority'      => isset($setting['priority']) ? $setting['priority'] : 10,
            );

            // Description
            if (!empty($setting['description'])) {
                $control_config['description'] = $setting['description'];
            }

            // Add control to section
            if (!empty($setting['section'])) {
                $control_config['section'] = 'rp_decorator_' . $setting['section'];
            }

            // Add control to panel
            if (!empty($setting['panel'])) {
                $control_config['panel'] = 'rp_decorator_' . $setting['panel'];
            }

            // Add custom field type
            if (!empty($setting['type'])) {
                $control_config['type'] = $setting['type'];
            }

            // Add select field options
            if (!empty($setting['choices'])) {
                $control_config['choices'] = $setting['choices'];
            }

            // Input attributes
            if (!empty($setting['input_attrs'])) {
                $control_config['input_attrs'] = $setting['input_attrs'];
            }

            // Add control
            $wp_customize->add_control(new $control_class($wp_customize, 'rp_decorator_' . $setting_key, $control_config));
        }
    }

    /**
     * Maybe add section
     *
     * @access public
     * @param object $wp_customize
     * @param array $child
     * @return void
     */
    public static function maybe_add_section($wp_customize, $child)
    {
        // Get sections
        $sections = RP_Decorator_Settings::get_sections();

        // Check if section is set and exists
        if (!empty($child['section']) && isset($sections[$child['section']])) {

            // Reference current section key
            $section_key = $child['section'];

            // Check if section was not added yet
            if (!in_array($section_key, self::$sections_added, true)) {

                // Reference current section
                $section = $sections[$section_key];

                // Section config
                $section_config = array(
                    'title'     => $section['title'],
                    'priority'  => (isset($section['priority']) ? $section['priority'] : 10),
                );

                // Description
                if (!empty($section['description'])) {
                    $section_config['description'] = $section['description'];
                }

                // Maybe add panel
                RP_Decorator_Customizer::maybe_add_panel($wp_customize, $section);

                // Maybe add section to panel
                if (!empty($section['panel'])) {
                    $section_config['panel'] = 'rp_decorator_' . $section['panel'];
                }

                // Register section
                $wp_customize->add_section('rp_decorator_' . $section_key, $section_config);

                // Track which sections were added
                self::$sections_added[] = $section_key;
            }
        }
    }

    /**
     * Maybe add panel
     *
     * @access public
     * @param object $wp_customize
     * @param array $child
     * @return void
     */
    public static function maybe_add_panel($wp_customize, $child)
    {
        // Get panels
        $panels = RP_Decorator_Settings::get_panels();

        // Check if panel is set and exists
        if (!empty($child['panel']) && isset($panels[$child['panel']])) {

            // Reference current panel key
            $panel_key = $child['panel'];

            // Check if panel was not added yet
            if (!in_array($panel_key, self::$panels_added, true)) {

                // Reference current panel
                $panel = $panels[$panel_key];

                // Panel config
                $panel_config = array(
                    'title'         => $panel['title'],
                    'priority'      => (isset($panel['priority']) ? $panel['priority'] : 10),
                    'capability'    => RP_Decorator::get_admin_capability(),
                );

                // Panel description
                if (!empty($panel['description'])) {
                    $panel_config['description'] = $panel['description'];
                }

                // Register panel
                $wp_customize->add_panel('rp_decorator_' . $panel_key, $panel_config);

                // Track which panels were added
                self::$panels_added[] = $panel_key;
            }
        }
    }

    /**
     * Get styles string
     *
     * @access public
     * @param bool $add_custom_css
     * @return string
     */
    public static function get_styles_string($add_custom_css = true)
    {
        $styles_array = array();
        $styles = '';

        // Iterate over settings
        foreach (RP_Decorator_Settings::get_settings() as $setting_key => $setting) {

            // Only add CSS properties
            if (isset($setting['live_method']) && $setting['live_method'] === 'css') {

                // Iterate over selectors
                foreach ($setting['selectors'] as $selector => $properties) {

                    // Iterate over properties
                    foreach ($properties as $property) {

                        // Add value to styles array
                        $styles_array[$selector][$property] = RP_Decorator_Customizer::opt($setting_key, $selector);
                    }
                }
            }
        }

        // Join property names with values
        foreach ($styles_array as $selector => $properties) {

            // Open selector
            $styles .= $selector . '{';

            foreach ($properties as $property_key => $property_value) {

                // Add property
                $styles .= $property_key . ':' . $property_value . ';';
            }

            // Close selector
            $styles .= '}';
        }

        // Add custom CSS
        if ($add_custom_css) {
            $styles .= RP_Decorator_Customizer::opt('custom_css');
        }

        // Return styles string
        return $styles;
    }

    /**
     * Get value for use in templates
     *
     * @access public
     * @param string $key
     * @param string $selector
     * @return string
     */
    public static function opt($key, $selector = null)
    {
        // Get raw value
        $stored_value = RP_Decorator_Customizer::get_stored_value($key, RP_Decorator_Settings::get_default_value($key));

        // Prepare value
        $value = RP_Decorator_Customizer::prepare($key, $stored_value, $selector);

        // Allow developers to override
        return apply_filters('rp_decorator_option_value', $value, $key, $selector, $stored_value);
    }

    /**
     * Get value stored in database
     *
     * @access public
     * @param string $key
     * @param string $default
     * @return string
     */
    public static function get_stored_value($key, $default = '')
    {
        // Get all stored values
        $stored = (array) get_option('rp_decorator', array());

        // Check if value exists in stored values array
        if (!empty($stored) && isset($stored[$key])) {
            return $stored[$key];
        }

        // Stored value not found, use default value
        return $default;
    }

    /**
     * Prepare value for use in HTML
     *
     * @access public
     * @param string $key
     * @param string $value
     * @param string $selector
     * @return string
     */
    public static function prepare($key, $value, $selector = null)
    {
        // Append CSS suffix to value
        $value .= RP_Decorator_Customizer::get_css_suffix($key);

        // Special case for border_radius #template_header
        if ($key === 'border_radius' && $selector === '#template_header') {
            $value = trim(str_replace('!important', '', $value));
            $value = $value . ' ' . $value . ' 0 0 !important';
        }

        // Special case for email_padding #wrapper
        if ($key === 'email_padding' && $selector === '#wrapper') {
            // $value = trim(str_replace('!important', '', $value));
            $value = $value . ' 0 ' . $value . ' 0';
        }

        // Special case for footer_padding #template_footer #credit
        if ($key === 'footer_padding' && $selector === '#template_footer #credit') {
            $value = '0 ' . $value . ' ' . $value . ' ' . $value;
        }

        // Special case for shadow
        if ($key === 'shadow') {
            $value = '0 ' . ($value > 0 ? 1 : 0) . 'px ' . ($value * 4) . 'px ' . $value . 'px rgba(0,0,0,0.1) !important';
        }

        // Font family
        if (substr($key, -11) === 'font_family') {
            $value = isset(RP_Decorator_Settings::$font_family_mapping[$value]) ? RP_Decorator_Settings::$font_family_mapping[$value] : $value;
        }

        // Return prepared value
        return $value;
    }

    /**
     * Get CSS suffix by key or all CSS suffixes
     *
     * @access public
     * @param string $key
     * @return mixed
     */
    public static function get_css_suffix($key = null)
    {
        // Define CSS suffixes
        if (self::$css_suffixes === null) {
            self::$css_suffixes = array(
                'email_padding'                 => 'px',
                'content_padding'               => 'px',
                'email_width'                   => 'px',
                'border_width'                  => 'px',
                'border_radius'                 => 'px !important',
                'header_padding_top_bottom'     => 'px',
                'header_padding_left_right'     => 'px',
                'heading_font_size'             => 'px',
                'footer_padding'                => 'px',
                'footer_font_size'              => 'px',
                'h1_font_size'                  => 'px',
                'h2_font_size'                  => 'px',
                'h3_font_size'                  => 'px',
                'h4_font_size'                  => 'px',
                'h5_font_size'                  => 'px',
                'h6_font_size'                  => 'px',
                'h1_separator_width'            => 'px',
                'h2_separator_width'            => 'px',
                'h3_separator_width'            => 'px',
                'h4_separator_width'            => 'px',
                'h5_separator_width'            => 'px',
                'h6_separator_width'            => 'px',
                'font_size'                     => 'px',
                'items_table_border_width'      => 'px',
                'items_table_separator_width'   => 'px',
                'items_table_padding'           => 'px',
            );
        }

        // Return single suffix
        if (isset($key)) {
            return isset(self::$css_suffixes[$key]) ? self::$css_suffixes[$key] : '';
        }
        // Return all suffixes for use in Javascript
        else {
            return self::$css_suffixes;
        }
    }

    /**
     * Reset to default values via Ajax request
     *
     * @access public
     * @return void
     */
    public function ajax_reset()
    {
        // Check request
        if (empty($_REQUEST['wp_customize']) || $_REQUEST['wp_customize'] !== 'on' || empty($_REQUEST['action']) || $_REQUEST['action'] !== 'rp_decorator_reset') {
            exit;
        }

        // Check if user is allowed to reset values
        if (!RP_Decorator::is_admin()) {
            exit;
        }

        // Reset to default values
        RP_Decorator_Customizer::reset();

        exit;
    }

    /**
     * Reset to default values
     *
     * @access private
     * @return void
     */
    public static function reset()
    {
        update_option('rp_decorator', array());
    }

    /**
     * Maybe add custom styles to default WooCommerce styles
     *
     * @access public
     * @param string $styles
     * @return string
     */
    public function maybe_add_custom_styles($styles)
    {
        // Check if custom styles need to be applied
        if (RP_Decorator_WC::overwrite_options()) {

            // Add custom styles
            $styles .= RP_Decorator_Customizer::get_styles_string();

            // Static styles
            $styles .= RP_Decorator_Customizer::get_static_styles();
        }
        // Otherwise apply some fixes for Customizer Preview
        else if (RP_Decorator::is_own_preview_request()) {
            $styles .= 'body { background-color: ' . get_option('woocommerce_email_background_color') . '; }';
            $styles .= RP_Decorator_Customizer::get_static_styles();
        }

        // Return styles
        return $styles;
    }

    /**
     * Get static styles
     *
     * @access public
     * @return string
     */
    public static function get_static_styles()
    {
        return "
            #body_content_inner > table {
                border-collapse: collapse;
            }
            #body_content_inner > table.td > tbody {
                border-bottom-style: solid;
            }
        ";
    }





}

RP_Decorator_Customizer::get_instance();

}
