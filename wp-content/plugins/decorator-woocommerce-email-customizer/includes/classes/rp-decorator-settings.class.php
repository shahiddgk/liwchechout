<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Customizer Settings
 *
 * @class RP_Decorator_Settings
 * @package Decorator
 * @author WebToffee
 */
if (!class_exists('RP_Decorator_Settings')) {

class RP_Decorator_Settings
{
    private static $panels          = null;
    private static $sections        = null;
    private static $settings        = null;
    private static $default_values = null;

    // Font family mapping
    public static $font_family_mapping = array(
        'arial'             => 'Arial, Helvetica, sans-serif',
        'arial_black'       => '"Arial Black", Gadget, sans-serif',
        'courier'           => '"Courier New", Courier, monospace',
        //'georgia'           => 'Georgia, serif',
        'helvetica'         => '"Helvetica Neue", Helvetica, Roboto, Arial, sans-serif',
        'impact'            => 'Impact, Charcoal, sans-serif',
        'lucida'            => '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
        'palatino'          => '"Palatino Linotype", "Book Antiqua", Palatino, serif',
        //'tahoma'            => 'Tahoma, Geneva, sans-serif',
        //'times'             => '"Times New Roman", Times, serif',
        //'verdana'           => 'Verdana, Geneva, sans-serif',
    );

    /**
     * Get panels
     *
     * @access public
     * @return array
     */
    public static function get_panels()
    {
        // Define panels
        if (self::$panels === null) {
            self::$panels = array(

                // Header
                'header' => array(
                    'title'     => __('Header', 'decorator-woocommerce-email-customizer'),
                    'priority'  => 20,
                ),

                // Footer
                'footer' => array(
                    'title'     => __('Footer', 'decorator-woocommerce-email-customizer'),
                    'priority'  => 30,
                ),

                // Content
                'content' => array(
                    'title'     => __('Content', 'decorator-woocommerce-email-customizer'),
                    'priority'  => 40,
                ),

                // Other
                'other' => array(
                    'title'     => __('Other', 'decorator-woocommerce-email-customizer'),
                    'priority'  => 50,
                ),
            );
        }

        // Return panels
        return self::$panels;
    }

    /**
     * Get sections
     *
     * @access public
     * @return array
     */
    public static function get_sections()
    {
        // Define sections
        if (self::$sections === null) {
            self::$sections = array(

                // Container
                'container' => array(
                    'title'     => __('Container', 'decorator-woocommerce-email-customizer'),
                    'priority'  => 10,
                ),

                // Header Style
                'header_style' => array(
                    'title'     => __('Header Style', 'decorator-woocommerce-email-customizer'),
                    'panel'     => 'header',
                    'priority'  => 20,
                ),

                // Header Image
                'header_image' => array(
                    'title'     => __('Header Image', 'decorator-woocommerce-email-customizer'),
                    'panel'     => 'header',
                    'priority'  => 20,
                ),

                // Heading
                'heading' => array(
                    'title'     => __('Heading', 'decorator-woocommerce-email-customizer'),
                    'panel'     => 'header',
                    'priority'  => 30,
                ),

                // Footer Style
                'footer_style' => array(
                    'title'     => __('Footer Style', 'decorator-woocommerce-email-customizer'),
                    'panel'     => 'footer',
                    'priority'  => 40,
                ),

                // Footer Content
                'footer_content' => array(
                    'title'     => __('Footer Content', 'decorator-woocommerce-email-customizer'),
                    'panel'     => 'footer',
                    'priority'  => 50,
                ),

                // Content Container
                'content_container' => array(
                    'title'     => __('Content Container', 'decorator-woocommerce-email-customizer'),
                    'panel'     => 'content',
                    'priority'  => 10,
                ),

                // Text Style
                'text_style' => array(
                    'title'     => __('Text Style', 'decorator-woocommerce-email-customizer'),
                    'panel'     => 'content',
                    'priority'  => 10,
                ),

                // Heading 1
                'h1' => array(
                    'title'     => __('Heading 1', 'decorator-woocommerce-email-customizer'),
                    'panel'     => 'other',
                    'priority'  => 20,
                ),

                // Heading 2
                'h2' => array(
                    'title'     => __('Heading 2', 'decorator-woocommerce-email-customizer'),
                    'panel'     => 'other',
                    'priority'  => 30,
                ),

                // Heading 3
                'h3' => array(
                    'title'     => __('Heading 3', 'decorator-woocommerce-email-customizer'),
                    'panel'     => 'other',
                    'priority'  => 40,
                ),

                // Heading 4
                'h4' => array(
                    'title'     => __('Heading 4', 'decorator-woocommerce-email-customizer'),
                    'panel'     => 'other',
                    'priority'  => 50,
                ),

                // Heading 5
                'h5' => array(
                    'title'     => __('Heading 5', 'decorator-woocommerce-email-customizer'),
                    'panel'     => 'other',
                    'priority'  => 60,
                ),

                // Heading 6
                'h6' => array(
                    'title'     => __('Heading 6', 'decorator-woocommerce-email-customizer'),
                    'panel'     => 'other',
                    'priority'  => 70,
                ),

                // Custom Styles
                'custom_styles' => array(
                    'title'     => __('Custom Styles', 'decorator-woocommerce-email-customizer'),
                    'panel'     => 'other',
                    'priority'  => 100,
                ),

                // Items Table
                'items_table' => array(
                    'title'     => __('Order Items', 'decorator-woocommerce-email-customizer'),
                    'panel'     => 'content',
                    'priority'  => 10,
                ),
            );
        }

        // Return sections
        return self::$sections;
    }

    /**
     * Get settings
     *
     * @access public
     * @return array
     */
    public static function get_settings()
    {
        // Define settings
        if (self::$settings === null) {
            self::$settings = array(

                // Background color
                'background_color' => array(
                    'title'         => __('Background color', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'container',
                    'control_type'  => 'color',
                    'default'       => RP_Decorator_Settings::get_default_value('background_color'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        'body'      => array('background-color'),
                        '#wrapper'  => array('background-color'),
                    ),
                ),

                // Email padding
                'email_padding' => array(
                    'title'         => __('Padding', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'container',
                    'default'       => RP_Decorator_Settings::get_default_value('email_padding'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#wrapper' => array('padding'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 0,
                        'max'   => 250,
                    ),
                ),

                // Email width
                'email_width' => array(
                    'title'         => __('Container width', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'container',
                    'default'       => RP_Decorator_Settings::get_default_value('email_width'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_container'   => array('width'),
                        '#template_header'      => array('width'),
                        '#template_body'        => array('width'),
                        '#template_footer'      => array('width'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 350,
                        'max'   => 1500,
                    ),
                ),

                // Email background color
                'email_background_color' => array(
                    'title'         => __('Background color', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'content_container',
                    'control_type'  => 'color',
                    'default'       => RP_Decorator_Settings::get_default_value('email_background_color'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_container'   => array('background-color'),
                        '#body_content'         => array('background-color'),
                    ),
                ),

                // Content padding
                'content_padding' => array(
                    'title'         => __('Padding', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'content_container',
                    'default'       => RP_Decorator_Settings::get_default_value('content_padding'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#body_content > table > tbody > tr > td' => array('padding'),
                        '#body_content > table > tr > td' => array('padding'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 0,
                        'max'   => 150,
                    ),
                ),

                // Font size
                'font_size' => array(
                    'title'         => __('Font size', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'text_style',
                    'default'       => RP_Decorator_Settings::get_default_value('font_size'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#body_content_inner'   => array('font-size'),
                        'img'                   => array('font-size'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 8,
                        'max'   => 30,
                    ),
                ),

                // Font family
                'font_family' => array(
                    'title'         => __('Font family', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'text_style',
                    'default'       => RP_Decorator_Settings::get_default_value('font_family'),
                    'live_method'   => 'css',
                    'type'          => 'select',
                    'choices'       => RP_Decorator_Settings::get_font_families(),
                    'selectors'     => array(
                        '#body_content_inner'   => array('font-family'),
                        '.td'                   => array('font-family'),
                        '.text'                 => array('font-family'),
                    ),
                ),

                // Text color
                'text_color' => array(
                    'title'         => __('Text color', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'text_style',
                    'control_type'  => 'color',
                    'default'       => RP_Decorator_Settings::get_default_value('text_color'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#body_content_inner'   => array('color'),
                        '.td'                   => array('color'),
                        '.text'                 => array('color'),
			'.address'                 => array('color'),
                    ),
                ),

                // Link color
                'link_color' => array(
                    'title'         => __('Link color', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'text_style',
                    'control_type'  => 'color',
                    'default'       => RP_Decorator_Settings::get_default_value('link_color'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        'a'     => array('color'),
                        '.link' => array('color'),
                    ),
                ),

                // Header background color
                'header_background_color' => array(
                    'title'         => __('Background color', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'header_style',
                    'control_type'  => 'color',
                    'default'       => RP_Decorator_Settings::get_default_value('header_background_color'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_header' => array('background-color'),
                    ),
                ),

                // Header Text align
                'header_text_align' => array(
                    'title'         => __('Text align', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'header_style',
                    'default'       => RP_Decorator_Settings::get_default_value('header_text_align'),
                    'live_method'   => 'css',
                    'type'          => 'select',
                    'choices'       => RP_Decorator_Settings::get_text_aligns(),
                    'selectors'     => array(
                        '#header_wrapper > h1' => array('text-align'),
                    ),
                ),

                // Header Padding top/bottom
                'header_padding_top_bottom' => array(
                    'title'         => __('Padding top/bottom', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'header_style',
                    'default'       => RP_Decorator_Settings::get_default_value('header_padding_top_bottom'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#header_wrapper' => array('padding-top', 'padding-bottom'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 0,
                        'max'   => 150,
                    ),
                ),

                // Header Padding left/right
                'header_padding_left_right' => array(
                    'title'         => __('Padding left/right', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'header_style',
                    'default'       => RP_Decorator_Settings::get_default_value('header_padding_left_right'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#header_wrapper' => array('padding-left', 'padding-right'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 0,
                        'max'   => 150,
                    ),
                ),

                // Header image
                'header_image' => array(
                    'title'         => __('Header image', 'decorator-woocommerce-email-customizer'),
                    'control_type'  => 'image',
                    'section'       => 'header_image',
                    'default'       => RP_Decorator_Settings::get_default_value('header_image'),
                    'original'      => '',
                    'live_method'   => 'replace',
                    'selectors'     => array(
                        '#template_header_image'
                    ),
                ),

                // Border radius
                'border_radius' => array(
                    'title'         => __('Border radius', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'container',
                    'default'       => RP_Decorator_Settings::get_default_value('border_radius'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_container'   => array('border-radius'),
                        '#template_header'      => array('border-radius'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 0,
                        'max'   => 100,
                    ),
                ),

                // Shadow
                'shadow' => array(
                    'title'         => __('Shadow', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'container',
                    'default'       => RP_Decorator_Settings::get_default_value('shadow'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_container' => array('box-shadow'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 0,
                        'max'   => 20,
                    ),
                ),

                // Heading Font size
                'heading_font_size' => array(
                    'title'         => __('Font size', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'heading',
                    'default'       => RP_Decorator_Settings::get_default_value('heading_font_size'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_header h1' => array('font-size'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 10,
                        'max'   => 75,
                    ),
                ),

                // Heading Font family
                'heading_font_family' => array(
                    'title'         => __('Font family', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'heading',
                    'default'       => RP_Decorator_Settings::get_default_value('heading_font_family'),
                    'live_method'   => 'css',
                    'type'          => 'select',
                    'choices'       => RP_Decorator_Settings::get_font_families(),
                    'selectors'     => array(
                        '#template_header h1' => array('font-family'),
                    ),
                ),

                // Heading Font weight
                'heading_font_weight' => array(
                    'title'         => __('Font weight', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'heading',
                    'default'       => RP_Decorator_Settings::get_default_value('heading_font_weight'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_header'      => array('font-weight'),
                        '#template_header h1'   => array('font-weight'),
                    ),
                    'input_attrs' => array(
                        'step'  => 100,
                        'min'   => 100,
                        'max'   => 900,
                    ),
                ),

                // Heading Color
                'heading_color' => array(
                    'title'         => __('Text color', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'heading',
                    'control_type'  => 'color',
                    'default'       => RP_Decorator_Settings::get_default_value('heading_color'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_header'      => array('color'),
                        '#template_header h1'   => array('color'),
                    ),
                ),

                // Footer Padding
                'footer_padding' => array(
                    'title'         => __('Padding', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'footer_style',
                    'default'       => RP_Decorator_Settings::get_default_value('footer_padding'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_footer #credit' => array('padding'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 0,
                        'max'   => 150,
                    ),
                ),

                // Footer Text align
                'footer_text_align' => array(
                    'title'         => __('Text align', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'footer_style',
                    'default'       => RP_Decorator_Settings::get_default_value('footer_text_align'),
                    'live_method'   => 'css',
                    'type'          => 'select',
                    'choices'       => RP_Decorator_Settings::get_text_aligns(),
                    'selectors'     => array(
                        '#template_footer #credit' => array('text-align'),
                    ),
                ),

                // Footer Font size
                'footer_font_size' => array(
                    'title'         => __('Font size', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'footer_style',
                    'default'       => RP_Decorator_Settings::get_default_value('footer_font_size'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_footer #credit' => array('font-size'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 8,
                        'max'   => 30,
                    ),
                ),

                // Footer Font family
                'footer_font_family' => array(
                    'title'         => __('Font family', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'footer_style',
                    'default'       => RP_Decorator_Settings::get_default_value('footer_font_family'),
                    'live_method'   => 'css',
                    'type'          => 'select',
                    'choices'       => RP_Decorator_Settings::get_font_families(),
                    'selectors'     => array(
                        '#template_footer #credit' => array('font-family'),
                    ),
                ),

                // Footer Font weight
                'footer_font_weight' => array(
                    'title'         => __('Font weight', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'footer_style',
                    'default'       => RP_Decorator_Settings::get_default_value('footer_font_weight'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_footer #credit' => array('font-weight'),
                    ),
                    'input_attrs' => array(
                        'step'  => 100,
                        'min'   => 100,
                        'max'   => 900,
                    ),
                ),

                // Footer Color
                'footer_color' => array(
                    'title'         => __('Text color', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'footer_style',
                    'control_type'  => 'color',
                    'default'       => RP_Decorator_Settings::get_default_value('footer_color'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_footer #credit' => array('color'),
                    ),
                ),

                // Footer Content Footer text
                'footer_content_text' => array(
                    'title'         => __('Footer text', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'textarea',
                    'section'       => 'footer_content',
                    'default'       => RP_Decorator_Settings::get_default_value('footer_content_text'),
                    'original'      => '',
                    'live_method'   => 'replace',
                    'selectors'     => array(
                        '#template_footer #credit'
                    ),
                ),

                // H1 Font size
                'h1_font_size' => array(
                    'title'         => __('Font size', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'h1',
                    'default'       => RP_Decorator_Settings::get_default_value('h1_font_size'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h1' => array('font-size'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 10,
                        'max'   => 50,
                    ),
                ),

                // H1 Font family
                'h1_font_family' => array(
                    'title'         => __('Font family', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h1',
                    'default'       => RP_Decorator_Settings::get_default_value('h1_font_family'),
                    'live_method'   => 'css',
                    'type'          => 'select',
                    'choices'       => RP_Decorator_Settings::get_font_families(),
                    'selectors'     => array(
                        '#template_body h1' => array('font-family'),
                    ),
                ),

                // H1 Font weight
                'h1_font_weight' => array(
                    'title'         => __('Font weight', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'h1',
                    'default'       => RP_Decorator_Settings::get_default_value('h1_font_weight'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h1' => array('font-weight'),
                    ),
                    'input_attrs' => array(
                        'step'  => 100,
                        'min'   => 100,
                        'max'   => 900,
                    ),
                ),

                // H1 Color
                'h1_color' => array(
                    'title'         => __('Text color', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h1',
                    'control_type'  => 'color',
                    'default'       => RP_Decorator_Settings::get_default_value('h1_color'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h1' => array('color'),
                    ),
                ),

                // H1 Separator
                'h1_separator_style' => array(
                    'title'         => __('Separator style', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h1',
                    'default'       => RP_Decorator_Settings::get_default_value('h1_separator_style'),
                    'live_method'   => 'css',
                    'type'          => 'select',
                    'choices'       => RP_Decorator_Settings::get_border_styles(),
                    'selectors'     => array(
                        '#template_body h1' => array('border-bottom-style'),
                    ),
                ),

                // H1 Separator width
                'h1_separator_width' => array(
                    'title'         => __('Separator width', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'h1',
                    'default'       => RP_Decorator_Settings::get_default_value('h1_separator_width'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h1' => array('border-bottom-width'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 0,
                        'max'   => 50,
                    ),
                ),

                // H1 Separator color
                'h1_separator_color' => array(
                    'title'         => __('Separator color', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h1',
                    'control_type'  => 'color',
                    'default'       => RP_Decorator_Settings::get_default_value('h1_separator_color'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h1' => array('border-bottom-color'),
                    ),
                ),

                // H2 Font size
                'h2_font_size' => array(
                    'title'         => __('Font size', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'h2',
                    'default'       => RP_Decorator_Settings::get_default_value('h2_font_size'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h2' => array('font-size'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 10,
                        'max'   => 50,
                    ),
                ),

                // H2 Font family
                'h2_font_family' => array(
                    'title'         => __('Font family', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h2',
                    'default'       => RP_Decorator_Settings::get_default_value('h2_font_family'),
                    'live_method'   => 'css',
                    'type'          => 'select',
                    'choices'       => RP_Decorator_Settings::get_font_families(),
                    'selectors'     => array(
                        '#template_body h2' => array('font-family'),
                    ),
                ),

                // H2 Font weight
                'h2_font_weight' => array(
                    'title'         => __('Font weight', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'h2',
                    'default'       => RP_Decorator_Settings::get_default_value('h2_font_weight'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h2' => array('font-weight'),
                    ),
                    'input_attrs' => array(
                        'step'  => 100,
                        'min'   => 100,
                        'max'   => 900,
                    ),
                ),

                // H2 Color
                'h2_color' => array(
                    'title'         => __('Text color', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h2',
                    'control_type'  => 'color',
                    'default'       => RP_Decorator_Settings::get_default_value('h2_color'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h2' => array('color'),
                    ),
                ),

                // H2 Separator
                'h2_separator_style' => array(
                    'title'         => __('Separator style', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h2',
                    'default'       => RP_Decorator_Settings::get_default_value('h2_separator_style'),
                    'live_method'   => 'css',
                    'type'          => 'select',
                    'choices'       => RP_Decorator_Settings::get_border_styles(),
                    'selectors'     => array(
                        '#template_body h2' => array('border-bottom-style'),
                    ),
                ),

                // H2 Separator width
                'h2_separator_width' => array(
                    'title'         => __('Separator width', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'h2',
                    'default'       => RP_Decorator_Settings::get_default_value('h2_separator_width'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h2' => array('border-bottom-width'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 0,
                        'max'   => 50,
                    ),
                ),

                // H2 Separator color
                'h2_separator_color' => array(
                    'title'         => __('Separator color', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h2',
                    'control_type'  => 'color',
                    'default'       => RP_Decorator_Settings::get_default_value('h2_separator_color'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h2' => array('border-bottom-color'),
                    ),
                ),

                // H3 Font size
                'h3_font_size' => array(
                    'title'         => __('Font size', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'h3',
                    'default'       => RP_Decorator_Settings::get_default_value('h3_font_size'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h3' => array('font-size'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 10,
                        'max'   => 50,
                    ),
                ),

                // H3 Font family
                'h3_font_family' => array(
                    'title'         => __('Font family', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h3',
                    'default'       => RP_Decorator_Settings::get_default_value('h3_font_family'),
                    'live_method'   => 'css',
                    'type'          => 'select',
                    'choices'       => RP_Decorator_Settings::get_font_families(),
                    'selectors'     => array(
                        '#template_body h3' => array('font-family'),
                    ),
                ),

                // H3 Font weight
                'h3_font_weight' => array(
                    'title'         => __('Font weight', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'h3',
                    'default'       => RP_Decorator_Settings::get_default_value('h3_font_weight'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h3' => array('font-weight'),
                    ),
                    'input_attrs' => array(
                        'step'  => 100,
                        'min'   => 100,
                        'max'   => 900,
                    ),
                ),

                // H3 Color
                'h3_color' => array(
                    'title'         => __('Text color', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h3',
                    'control_type'  => 'color',
                    'default'       => RP_Decorator_Settings::get_default_value('h3_color'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h3' => array('color'),
                    ),
                ),

                // H3 Separator
                'h3_separator_style' => array(
                    'title'         => __('Separator style', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h3',
                    'default'       => RP_Decorator_Settings::get_default_value('h3_separator_style'),
                    'live_method'   => 'css',
                    'type'          => 'select',
                    'choices'       => RP_Decorator_Settings::get_border_styles(),
                    'selectors'     => array(
                        '#template_body h3' => array('border-bottom-style'),
                    ),
                ),

                // H3 Separator width
                'h3_separator_width' => array(
                    'title'         => __('Separator width', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'h3',
                    'default'       => RP_Decorator_Settings::get_default_value('h3_separator_width'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h3' => array('border-bottom-width'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 0,
                        'max'   => 50,
                    ),
                ),

                // H3 Separator color
                'h3_separator_color' => array(
                    'title'         => __('Separator color', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h3',
                    'control_type'  => 'color',
                    'default'       => RP_Decorator_Settings::get_default_value('h3_separator_color'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h3' => array('border-bottom-color'),
                    ),
                ),

                // H4 Font size
                'h4_font_size' => array(
                    'title'         => __('Font size', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'h4',
                    'default'       => RP_Decorator_Settings::get_default_value('h4_font_size'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h4' => array('font-size'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 10,
                        'max'   => 50,
                    ),
                ),

                // H4 Font family
                'h4_font_family' => array(
                    'title'         => __('Font family', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h4',
                    'default'       => RP_Decorator_Settings::get_default_value('h4_font_family'),
                    'live_method'   => 'css',
                    'type'          => 'select',
                    'choices'       => RP_Decorator_Settings::get_font_families(),
                    'selectors'     => array(
                        '#template_body h4' => array('font-family'),
                    ),
                ),

                // H4 Font weight
                'h4_font_weight' => array(
                    'title'         => __('Font weight', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'h4',
                    'default'       => RP_Decorator_Settings::get_default_value('h4_font_weight'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h4' => array('font-weight'),
                    ),
                    'input_attrs' => array(
                        'step'  => 100,
                        'min'   => 100,
                        'max'   => 900,
                    ),
                ),

                // H4 Color
                'h4_color' => array(
                    'title'         => __('Text color', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h4',
                    'control_type'  => 'color',
                    'default'       => RP_Decorator_Settings::get_default_value('h4_color'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h4' => array('color'),
                    ),
                ),

                // H4 Separator
                'h4_separator_style' => array(
                    'title'         => __('Separator style', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h4',
                    'default'       => RP_Decorator_Settings::get_default_value('h4_separator_style'),
                    'live_method'   => 'css',
                    'type'          => 'select',
                    'choices'       => RP_Decorator_Settings::get_border_styles(),
                    'selectors'     => array(
                        '#template_body h4' => array('border-bottom-style'),
                    ),
                ),

                // H4 Separator width
                'h4_separator_width' => array(
                    'title'         => __('Separator width', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'h4',
                    'default'       => RP_Decorator_Settings::get_default_value('h4_separator_width'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h4' => array('border-bottom-width'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 0,
                        'max'   => 50,
                    ),
                ),

                // H4 Separator color
                'h4_separator_color' => array(
                    'title'         => __('Separator color', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h4',
                    'control_type'  => 'color',
                    'default'       => RP_Decorator_Settings::get_default_value('h4_separator_color'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h4' => array('border-bottom-color'),
                    ),
                ),

                // H5 Font size
                'h5_font_size' => array(
                    'title'         => __('Font size', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'h5',
                    'default'       => RP_Decorator_Settings::get_default_value('h5_font_size'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h5' => array('font-size'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 10,
                        'max'   => 50,
                    ),
                ),

                // H5 Font family
                'h5_font_family' => array(
                    'title'         => __('Font family', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h5',
                    'default'       => RP_Decorator_Settings::get_default_value('h5_font_family'),
                    'live_method'   => 'css',
                    'type'          => 'select',
                    'choices'       => RP_Decorator_Settings::get_font_families(),
                    'selectors'     => array(
                        '#template_body h5' => array('font-family'),
                    ),
                ),

                // H5 Font weight
                'h5_font_weight' => array(
                    'title'         => __('Font weight', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'h5',
                    'default'       => RP_Decorator_Settings::get_default_value('h5_font_weight'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h5' => array('font-weight'),
                    ),
                    'input_attrs' => array(
                        'step'  => 100,
                        'min'   => 100,
                        'max'   => 900,
                    ),
                ),

                // H5 Color
                'h5_color' => array(
                    'title'         => __('Text color', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h5',
                    'control_type'  => 'color',
                    'default'       => RP_Decorator_Settings::get_default_value('h5_color'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h5' => array('color'),
                    ),
                ),

                // H5 Separator
                'h5_separator_style' => array(
                    'title'         => __('Separator style', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h5',
                    'default'       => RP_Decorator_Settings::get_default_value('h5_separator_style'),
                    'live_method'   => 'css',
                    'type'          => 'select',
                    'choices'       => RP_Decorator_Settings::get_border_styles(),
                    'selectors'     => array(
                        '#template_body h5' => array('border-bottom-style'),
                    ),
                ),

                // H5 Separator width
                'h5_separator_width' => array(
                    'title'         => __('Separator width', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'h5',
                    'default'       => RP_Decorator_Settings::get_default_value('h5_separator_width'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h5' => array('border-bottom-width'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 0,
                        'max'   => 50,
                    ),
                ),

                // H5 Separator color
                'h5_separator_color' => array(
                    'title'         => __('Separator color', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h5',
                    'control_type'  => 'color',
                    'default'       => RP_Decorator_Settings::get_default_value('h5_separator_color'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h5' => array('border-bottom-color'),
                    ),
                ),

                // H6 Font size
                'h6_font_size' => array(
                    'title'         => __('Font size', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'h6',
                    'default'       => RP_Decorator_Settings::get_default_value('h6_font_size'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h6' => array('font-size'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 10,
                        'max'   => 50,
                    ),
                ),

                // H6 Font family
                'h6_font_family' => array(
                    'title'         => __('Font family', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h6',
                    'default'       => RP_Decorator_Settings::get_default_value('h6_font_family'),
                    'live_method'   => 'css',
                    'type'          => 'select',
                    'choices'       => RP_Decorator_Settings::get_font_families(),
                    'selectors'     => array(
                        '#template_body h6' => array('font-family'),
                    ),
                ),

                // H6 Font weight
                'h6_font_weight' => array(
                    'title'         => __('Font weight', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'h6',
                    'default'       => RP_Decorator_Settings::get_default_value('h6_font_weight'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h6' => array('font-weight'),
                    ),
                    'input_attrs' => array(
                        'step'  => 100,
                        'min'   => 100,
                        'max'   => 900,
                    ),
                ),

                // H6 Color
                'h6_color' => array(
                    'title'         => __('Text color', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h6',
                    'control_type'  => 'color',
                    'default'       => RP_Decorator_Settings::get_default_value('h6_color'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h6' => array('color'),
                    ),
                ),

                // H6 Separator
                'h6_separator_style' => array(
                    'title'         => __('Separator style', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h6',
                    'default'       => RP_Decorator_Settings::get_default_value('h6_separator_style'),
                    'live_method'   => 'css',
                    'type'          => 'select',
                    'choices'       => RP_Decorator_Settings::get_border_styles(),
                    'selectors'     => array(
                        '#template_body h6' => array('border-bottom-style'),
                    ),
                ),

                // H6 Separator width
                'h6_separator_width' => array(
                    'title'         => __('Separator width', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'h6',
                    'default'       => RP_Decorator_Settings::get_default_value('h6_separator_width'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h6' => array('border-bottom-width'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 0,
                        'max'   => 50,
                    ),
                ),

                // H6 Separator color
                'h6_separator_color' => array(
                    'title'         => __('Separator color', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'h6',
                    'control_type'  => 'color',
                    'default'       => RP_Decorator_Settings::get_default_value('h6_separator_color'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#template_body h6' => array('border-bottom-color'),
                    ),
                ),

                // Custom CSS
                'custom_css' => array(
                    'title'         => __('Custom CSS', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'custom_styles',
                    'default'       => '',
                    'type'          => 'textarea',
                    'live_method'   => 'replace',
                    'original'      => '',
                    'selectors'     => array(
                        'style#rp_decorator_custom_css'
                    ),
                ),

                // Items table Background color
                'items_table_background_color' => array(
                    'title'         => __('Background color', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'items_table',
                    'control_type'  => 'color',
                    'default'       => RP_Decorator_Settings::get_default_value('items_table_background_color'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#body_content_inner > table.td' => array('background-color'),
                        '#body_content_inner > div > table.td' => array('background-color'),
                    ),
                ),

                // Items table Padding
                'items_table_padding' => array(
                    'title'         => __('Padding', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'items_table',
                    'default'       => RP_Decorator_Settings::get_default_value('items_table_padding'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#body_content_inner > table.td th' => array('padding'),
                        '#body_content_inner > div > table.td th' => array('padding'),
                        '#body_content_inner > table.td td' => array('padding'),
                        '#body_content_inner > div > table.td td' => array('padding'),
			//'#body_content_inner > div#rp_wcec_email_content > table.td td' => array('padding'), - removed wcec_email_content div from all item table settings - woocommerce-email-center plugin compatiblity issues.			
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 0,
                        'max'   => 50,
                    ),
                ),

                // Items table Border width
                'items_table_border_width' => array(
                    'title'         => __('Border width', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'items_table',
                    'default'       => RP_Decorator_Settings::get_default_value('items_table_border_width'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#body_content_inner > table.td'        => array('border-width'),
                        '#body_content_inner > div > table.td'        => array('border-width'),
                        '#body_content_inner > table.td .td'    => array('border-width'),
                        '#body_content_inner > div > table.td .td'    => array('border-width'),
                        '.rp_decorator_order_refund_line .td'        => array('border-width'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 0,
                        'max'   => 10,
                    ),
                ),

                // Items table Border color
                'items_table_border_color' => array(
                    'title'         => __('Border color', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'items_table',
                    'control_type'  => 'color',
                    'default'       => RP_Decorator_Settings::get_default_value('items_table_border_color'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#body_content_inner > table.td'        => array('border-color'),
                        '#body_content_inner > div > table.td'        => array('border-color'),
                        '#body_content_inner > table.td td.td'    => array('border-color'),
                        '#body_content_inner > div > table.td td.td'    => array('border-color'),
                        '#body_content_inner > table.td .td'    => array('border-color'),
                        '#body_content_inner > div > table.td .td'    => array('border-color'),
                    ),
                ),

                // Items table Totals separator width
                'items_table_separator_width' => array(
                    'title'         => __('Totals separator width', 'decorator-woocommerce-email-customizer'),
                    'type'          => 'range',
                    'section'       => 'items_table',
                    'default'       => RP_Decorator_Settings::get_default_value('items_table_separator_width'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#body_content_inner > table.td > tbody > tr > td' => array('border-bottom-width'),
                        '#body_content_inner > div > table.td > tbody > tr:last-child > td' => array('border-bottom-width'),
                    ),
                    'input_attrs' => array(
                        'step'  => 1,
                        'min'   => 0,
                        'max'   => 20,
                    ),
                ),

                // Items table Totals separator color
                'items_table_separator_color' => array(
                    'title'         => __('Totals separator color', 'decorator-woocommerce-email-customizer'),
                    'section'       => 'items_table',
                    'control_type'  => 'color',
                    'default'       => RP_Decorator_Settings::get_default_value('items_table_separator_color'),
                    'live_method'   => 'css',
                    'selectors'     => array(
                        '#body_content_inner > table.td > tbody > tr > td' => array('border-bottom-color'),
                        '#body_content_inner > div > table.td > tbody > tr:last-child > td' => array('border-bottom-color'),
                    ),
                ),
            );
        }

        // Return settings
        return self::$settings;
    }

    /**
     * Get default values
     *
     * @access public
     * @return array
     */
    public static function get_default_values()
    {
        // Define default values
        if (self::$default_values === null) {
            self::$default_values = array(
                'background_color'                      => '#f5f5f5',
                'email_background_color'                => '#fdfdfd',
                'header_background_color'               => '#557da1',
                'header_text_align'                     => 'left',
                'header_padding_top_bottom'             => '36',
                'header_padding_left_right'             => '48',
                'text_color'                            => '#737373',
                'font_family'                           => 'helvetica',
                'font_size'                             => '14',
                'link_color'                            => '#557da1',
                'email_padding'                         => '70',
                'content_padding'                       => '48',
                'email_width'                           => '600',
                'border_radius'                         => '3',
                'shadow'                                => '4',
                'heading_font_size'                     => '30',
                'heading_font_family'                   => 'helvetica',
                'heading_color'                         => '#ffffff',
                'heading_font_weight'                   => '300',
                'footer_padding'                        => '48',
                'footer_text_align'                     => 'center',
                'footer_font_size'                      => '12',
                'footer_font_family'                    => 'helvetica',
                'footer_color'                          => '#99b1c7',
                'footer_font_weight'                    => '400',
                'h1_font_size'                          => '24',
                'h1_font_family'                        => 'helvetica',
                'h1_color'                              => '#557da1',
                'h1_font_weight'                        => '700',
                'h1_separator_style'                    => 'none',
                'h1_separator_width'                    => '1',
                'h1_separator_color'                    => '#e4e4e4',
                'h2_font_size'                          => '18',
                'h2_font_family'                        => 'helvetica',
                'h2_color'                              => '#557da1',
                'h2_font_weight'                        => '700',
                'h2_separator_style'                    => 'none',
                'h2_separator_width'                    => '1',
                'h2_separator_color'                    => '#e4e4e4',
                'h3_font_size'                          => '16',
                'h3_font_family'                        => 'helvetica',
                'h3_color'                              => '#557da1',
                'h3_font_weight'                        => '700',
                'h3_separator_style'                    => 'none',
                'h3_separator_width'                    => '1',
                'h3_separator_color'                    => '#e4e4e4',
                'h4_font_size'                          => '14',
                'h4_font_family'                        => 'helvetica',
                'h4_color'                              => '#557da1',
                'h4_font_weight'                        => '700',
                'h4_separator_style'                    => 'none',
                'h4_separator_width'                    => '1',
                'h4_separator_color'                    => '#e4e4e4',
                'h5_font_size'                          => '12',
                'h5_font_family'                        => 'helvetica',
                'h5_color'                              => '#557da1',
                'h5_font_weight'                        => '700',
                'h5_separator_style'                    => 'none',
                'h5_separator_width'                    => '1',
                'h5_separator_color'                    => '#e4e4e4',
                'h6_font_size'                          => '10',
                'h6_font_family'                        => 'helvetica',
                'h6_color'                              => '#557da1',
                'h6_font_weight'                        => '700',
                'h6_separator_style'                    => 'none',
                'h6_separator_width'                    => '1',
                'h6_separator_color'                    => '#e4e4e4',
                'items_table_border_width'              => '1',
                'items_table_border_color'              => '#e4e4e4',
                'items_table_separator_width'           => '4',
                'items_table_separator_color'          => '#e4e4e4',
                'items_table_background_color'          => '',
                'items_table_padding'                   => '12',
                'footer_content_text'                   => get_option('woocommerce_email_footer_text', ''),
            );
        }

        // Return default values
        return self::$default_values;
    }

    /**
     * Get default values
     *
     * @access public
     * @param string $key
     * @return string
     */
    public static function get_default_value($key)
    {
        // Get default values
        $default_values = RP_Decorator_Settings::get_default_values();

        // Check if such key exists and return default value
        return isset($default_values[$key]) ? $default_values[$key] : '';
    }

    /**
     * Get border styles
     *
     * @access public
     * @return array
     */
    public static function get_border_styles()
    {
        return array(
            'none'      => __('none', 'decorator-woocommerce-email-customizer'),
            'hidden'    => __('hidden', 'decorator-woocommerce-email-customizer'),
            'dotted'    => __('dotted', 'decorator-woocommerce-email-customizer'),
            'dashed'    => __('dashed', 'decorator-woocommerce-email-customizer'),
            'solid'     => __('solid', 'decorator-woocommerce-email-customizer'),
            'double'    => __('double', 'decorator-woocommerce-email-customizer'),
            'groove'    => __('groove', 'decorator-woocommerce-email-customizer'),
            'ridge'     => __('ridge', 'decorator-woocommerce-email-customizer'),
            'inset'     => __('inset', 'decorator-woocommerce-email-customizer'),
            'outset'    => __('outset', 'decorator-woocommerce-email-customizer'),
        );
    }

    /**
     * Get text align options
     *
     * @access public
     * @return array
     */
    public static function get_text_aligns()
    {
        return array(
            'left'      => __('left', 'decorator-woocommerce-email-customizer'),
            'center'    => __('center', 'decorator-woocommerce-email-customizer'),
            'right'     => __('right', 'decorator-woocommerce-email-customizer'),
            'justify'   => __('justify', 'decorator-woocommerce-email-customizer'),
        );
    }

    /**
     * Get font families
     *
     * @access public
     * @return array
     */
    public static function get_font_families()
    {
        return array(
            'arial'             => __('Arial', 'decorator-woocommerce-email-customizer'),
            'arial_black'       => __('Arial Black', 'decorator-woocommerce-email-customizer'),
            'courier'           => __('Courier New', 'decorator-woocommerce-email-customizer'),
            //'georgia'           => __('Georgia', 'decorator-woocommerce-email-customizer'),
            'helvetica'         => __('Helvetica', 'decorator-woocommerce-email-customizer'),
            'impact'            => __('Impact', 'decorator-woocommerce-email-customizer'),
            'lucida'            => __('Lucida', 'decorator-woocommerce-email-customizer'),
            'palatino'          => __('Palatino', 'decorator-woocommerce-email-customizer'),
            //'tahoma'            => __('Tahoma', 'decorator-woocommerce-email-customizer'),
            //'times'             => __('Times New Roman', 'decorator-woocommerce-email-customizer'),
            //'verdana'           => __('Verdana', 'decorator-woocommerce-email-customizer'),
        );
    }


}
}
