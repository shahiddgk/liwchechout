<?php
/*
Plugin Name: WP Magnific Popup
Description: Add magnific popup lightbox script to your wordpress site
Version: 1.0
Author: Oleg Kosolapov
License: GPL2+
Text Domain: wp-magnific-popup
Domain Path: /lang
*/

class WPMagnificPopupPlugin
{
    public function __construct() {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('wp_head', array($this, 'wp_head'));
        add_action('wp_enqueue_scripts', array($this, 'front_scripts'));
        add_action( 'admin_init', array($this, 'register_settings') );
        add_action( 'plugins_loaded', array($this, 'load_textdomain') );
        add_filter( 'plugin_action_links', array($this, 'plugin_action_links'), 10, 2 );
        //add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
    }

    function load_admin_scripts()
    {
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
    }

    function admin_scripts()
    {
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-tabs');

        wp_enqueue_script('wpmp-admin', plugins_url('js/admin.js', __FILE__), array('jquery', 'jquery-ui-tabs'));
        wp_enqueue_style('wp-magnific-popup-jquery-ui-style', (is_ssl() ? 'https' : 'http').'://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/black-tie/jquery-ui.css');
    }

    function plugin_action_links($links, $file)
    {
        if( false === strpos( $file, basename(__FILE__) ) )
            return $links;

        $settings_link = '<a href="options-general.php?page=wp-magnific-popup">' . esc_html__( 'Settings', 'wp-magnific-popup' ) . '</a>';
        array_unshift( $links, $settings_link );

        return $links;
    }

    function load_textdomain()
    {
        load_plugin_textdomain( 'wp-magnific-popup', false, basename( dirname( __FILE__ ) ) . '/lang/' );
    }

    function front_scripts()
    {
        wp_enqueue_script('magnific-popup', plugins_url('mpopup/jquery.magnific-popup.min.js', __FILE__), array('jquery'));
        wp_enqueue_script('wpmp', plugins_url('js/wpmp.js', __FILE__), array('jquery'));
        wp_enqueue_style('magnific-popup', plugins_url('mpopup/magnific-popup.css', __FILE__));

        $w = intval($this->get_wpmp_option('iframe', 'width'));
        if ($w)
            wp_add_inline_style('magnific-popup', ".mfp-iframe-holder .mfp-content { max-width: ".$w."px; }");
    }

    function wp_head()
    {

        ?>
        <script type="text/javascript">
            var wpmp_options = {
                image: <?=json_encode(get_option('wpmp_image_settings'))?>,
                gallery: <?=json_encode(get_option('wpmp_gallery_settings'))?>,
                iframe: <?=json_encode(get_option('wpmp_iframe_settings'))?>,
                div: <?=json_encode(get_option('wpmp_div_settings'))?>,
                image_loading_text: '<?=__('Loading image #%curr%...', 'wp-magnific-popup')?>',
                image_error_text: '<?=__('<a href="%url%">The image #%curr%</a> could not be loaded.', 'wp-magnific-popup')?>'
            };
        </script>
    <?php
    }

    function admin_menu()
    {
        $pg = add_submenu_page('options-general.php', __('WP Magnific Popup', 'wp-magnific-popup'), __('WP Magnific Popup', 'wp-magnific-popup'), 'administrator', 'wp-magnific-popup', array($this, 'settings_page'));

        add_action('load-'.$pg, array($this, 'load_admin_scripts'));
    }

    function settings_page()
    {
        ?>

        <div class="wrap">
            <h2><?php _e('WP Magnific Popup Settings', 'wp-magnific-popup') ?>
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" style="display: inline-block; vertical-align: middle; float: right;">
                    <input type="hidden" name="cmd" value="_s-xclick" />
                    <input type="hidden" name="hosted_button_id" value="PQSF3JJPKKZHG" />
                    <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate" />
                    <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
                </form>
            </h2>
            <br>

            <form method="post" action="options.php">
                <?php settings_fields( 'wp-magnific-popup-settings-group' ); ?>
                <div id="wpmp_tabs">
                    <ul>
                        <li><a href="#wpmp_images"><?php _e('Images', 'wp-magnific-popup') ?></a></li>
                        <li><a href="#wpmp_galleries"><?php _e('Galleries', 'wp-magnific-popup') ?></a></li>
                        <li><a href="#wpmp_iframe"><?php _e('Iframes', 'wp-magnific-popup') ?></a></li>
                        <li><a href="#wpmp_dialog"><?php _e('Dialogs', 'wp-magnific-popup')?></a></li>
                    </ul>
                    <div id="wpmp_images">
                        <table class="form-table ps-form-table">
                            <tr>
                                <th><?php _e('CSS class for images', 'wp-magnific-popup') ?>:</th>
                                <td><input type="text" name="wpmp_image_settings[class]" value="<?=$this->get_wpmp_option('image', 'class')?>" /></td>
                            </tr>
                            <tr>
                                <th><?php _e('Vertical Fit', 'wp-magnific-popup') ?>:</th>
                                <td><input type="checkbox" name="wpmp_image_settings[vertical_fit]" value="1" <?php if ($this->get_wpmp_option('image', 'vertical_fit') == 1) echo 'checked="checked"';?> /></td>
                            </tr>
                            <tr>
                                <th><?php _e('Enable Zoom Animation', 'wp-magnific-popup') ?>:</th>
                                <td><input type="checkbox" name="wpmp_image_settings[zoom_enabled]" value="1" <?php if ($this->get_wpmp_option('image', 'zoom_enabled') == 1) echo 'checked="checked"';?> /></td>
                            </tr>
                            <tr>
                                <th><?php _e('Close Button Inside', 'wp-magnific-popup') ?>:</th>
                                <td><input type="checkbox" name="wpmp_image_settings[close_btn_inside]" value="1" <?php if ($this->get_wpmp_option('image', 'close_btn_inside') == 1) echo 'checked="checked"';?> /></td>
                            </tr>
                            <tr>
                                <th><?php _e('Close On Content Click', 'wp-magnific-popup') ?>:</th>
                                <td><input type="checkbox" name="wpmp_image_settings[close_on_content_click]" value="1" <?php if ($this->get_wpmp_option('image', 'close_on_content_click') == 1) echo 'checked="checked"';?> /></td>
                            </tr>
                            <tr>
                                <th><?php _e('Disable on screen width', 'wp-magnific-popup') ?>:</th>
                                <td>
                                    <input type="number" name="wpmp_image_settings[disable_on]" value="<?=$this->get_wpmp_option('image', 'disable_on')?>" min="0" max="10000" /> px
                                    <p class="description"><?php _e('If window width is less than the number in this option, lightbox will not be opened and the default behavior of the element will be triggered.', 'wp-magnific-popup') ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="wpmp_galleries">
                        <table class="form-table ps-form-table">
                            <tr>
                                <th><?php _e('CSS class for galleries', 'wp-magnific-popup') ?>:</th>
                                <td><input type="text" name="wpmp_gallery_settings[class]" value="<?=$this->get_wpmp_option('gallery', 'class')?>" /></td>
                            </tr>
                            <tr>
                                <th><?php _e('Navigate by image click', 'wp-magnific-popup') ?>:</th>
                                <td><input type="checkbox" name="wpmp_gallery_settings[navigate_by_img_click]" value="1" <?php if ($this->get_wpmp_option('gallery', 'navigate_by_img_click') == 1) echo 'checked="checked"';?> /></td>
                            </tr>
                            <tr>
                                <th><?php _e('Close Button Inside', 'wp-magnific-popup') ?>:</th>
                                <td><input type="checkbox" name="wpmp_gallery_settings[close_btn_inside]" value="1" <?php if ($this->get_wpmp_option('gallery', 'close_btn_inside') == 1) echo 'checked="checked"';?> /></td>
                            </tr>
                            <tr>
                                <th><?php _e('Disable on screen width', 'wp-magnific-popup') ?>:</th>
                                <td>
                                    <input type="number" name="wpmp_gallery_settings[disable_on]" value="<?=$this->get_wpmp_option('gallery', 'disable_on')?>" min="0" max="10000" /> px
                                    <p class="description"><?php _e('If window width is less than the number in this option, lightbox will not be opened and the default behavior of the element will be triggered.', 'wp-magnific-popup') ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="wpmp_iframe">
                        <table class="form-table ps-form-table">
                            <tr>
                                <th><?php _e('CSS class for iframes', 'wp-magnific-popup') ?>:</th>
                                <td><input type="text" name="wpmp_iframe_settings[class]" value="<?=$this->get_wpmp_option('iframe', 'class')?>" /></td>
                            </tr>
                            <tr>
                                <th><?php _e('Close Button Inside', 'wp-magnific-popup') ?>:</th>
                                <td><input type="checkbox" name="wpmp_iframe_settings[close_btn_inside]" value="1" <?php if ($this->get_wpmp_option('iframe', 'close_btn_inside') == 1) echo 'checked="checked"';?> /></td>
                            </tr>
                            <tr>
                                <th><?php _e('Iframe width', 'wp-magnific-popup') ?>:</th>
                                <td><input type="number" name="wpmp_iframe_settings[width]" min="0" max="10000" value="<?=$this->get_wpmp_option('iframe', 'width')?>" /> px</td>
                            </tr>
                            <tr>
                                <th><?php _e('Disable on screen width', 'wp-magnific-popup') ?>:</th>
                                <td>
                                    <input type="number" name="wpmp_iframe_settings[disable_on]" value="<?=$this->get_wpmp_option('iframe', 'disable_on')?>" min="0" max="10000" /> px
                                    <p class="description"><?php _e('If window width is less than the number in this option, lightbox will not be opened and the default behavior of the element will be triggered.', 'wp-magnific-popup') ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="wpmp_dialog">
                        <table class="form-table ps-form-table">
                            <tr>
                                <th><?php _e('CSS class for dialogs', 'wp-magnific-popup') ?>:</th>
                                <td><input type="text" name="wpmp_div_settings[class]" value="<?=$this->get_wpmp_option('div', 'class')?>" /></td>
                            </tr>
                            <tr>
                                <th><?php _e('Close Button Inside', 'wp-magnific-popup') ?>:</th>
                                <td><input type="checkbox" name="wpmp_div_settings[close_btn_inside]" value="1" <?php if ($this->get_wpmp_option('div', 'close_btn_inside') == 1) echo 'checked="checked"';?> /></td>
                            </tr>
                            <tr>
                                <th><?php _e('Disable on screen width', 'wp-magnific-popup') ?>:</th>
                                <td>
                                    <input type="number" name="wpmp_div_settings[disable_on]" value="<?=$this->get_wpmp_option('div', 'disable_on')?>" min="0" max="10000" /> px
                                    <p class="description"><?php _e('If window width is less than the number in this option, lightbox will not be opened and the default behavior of the element will be triggered.', 'wp-magnific-popup') ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                </p>
            </form>


        </div>
    <?php
    }

    function get_wpmp_option($section, $option)
    {
        $arr = get_option('wpmp_'.$section.'_settings');

        if ($arr)
        {
            if (isset($arr[$option]))
                return $arr[$option];
        }
        return '';
    }

    function register_settings()
    {
        register_setting( 'wp-magnific-popup-settings-group', 'wpmp_image_settings' );
        register_setting( 'wp-magnific-popup-settings-group', 'wpmp_gallery_settings' );
        register_setting( 'wp-magnific-popup-settings-group', 'wpmp_iframe_settings' );
        register_setting( 'wp-magnific-popup-settings-group', 'wpmp_div_settings' );
    }

}

new WPMagnificPopupPlugin();

register_activation_hook(__FILE__, 'install_wp_magnific_popup');
function install_wp_magnific_popup()
{
    $o = get_option('wpmp_image_settings');
    if (!$o)
        update_option('wpmp_image_settings', array(
            'class' => 'mpopup',
            'vertical_fit' => 1,
            'zoom_enabled' => 0,
            'disable_on' => 400
        ));


    if (!get_option('wpmp_gallery_settings'))
        update_option('wpmp_gallery_settings', array(
            'class' => 'popup-gallery',
            'disable_on' => 400
        ));

    $o = get_option('wpmp_iframe_settings');
    if (!$o)
        update_option('wpmp_iframe_settings', array(
            'class' => 'mpopup_iframe',
            'width' => 900,
            'disable_on' => 400
        ));


    if (!get_option('wpmp_div_settings'))
        update_option('wpmp_div_settings', array(
            'class' => 'mpopup_div',
            'disable_on' => 400
        ));

}