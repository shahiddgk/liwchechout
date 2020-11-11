<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Custom_Related_Products
 * @subpackage Woo_Custom_Related_Products/admin
 * @author     WPCodelibrary <support@wpcodelibrary.com>
 */
class Woo_Custom_Related_Products_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-custom-related-products-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-custom-related-products-admin.js', array( 'jquery' ), $this->version, false );

	}
	 /**
         * Add relatd products selector to edit product section
         */
        function wcrp_select_related_products() {
            global $post, $woocommerce;
            $product_ids = array_filter(array_map('absint', (array) get_post_meta($post->ID, '_wcrp_related_ids', true)));
            ?>
            <div class="options_group">
                <?php if ($woocommerce->version >= '2.3' && $woocommerce->version < '3.0') : ?>
                    <p class="form-field"><label for="related_ids"><?php _e('Custom Related Products', 'woocommerce'); ?></label>
                        <input type="hidden" class="wc-product-search" style="width: 50%;" id="wcrp_related_ids" name="wcrp_related_ids" data-placeholder="<?php _e('Search for a product&hellip;', 'woocommerce'); ?>" data-action="woocommerce_json_search_products" data-multiple="true" data-selected="<?php
                        $json_ids = array();
                        foreach ($product_ids as $product_id) {
                            $product = wc_get_product($product_id);
                            if (is_object($product) && is_callable(array($product, 'get_formatted_name'))) {
                                $json_ids[$product_id] = wp_kses_post($product->get_formatted_name());
                            }
                        }
                        echo esc_attr(json_encode($json_ids));
                        ?>" value="<?php echo implode(',', array_keys($json_ids)); ?>" /> <img class="help_tip" data-tip='<?php _e('Related products are displayed on the product detail page.', 'woocommerce') ?>' src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
                    </p>
                <?php else: ?>
                    <p class="form-field"><label for="related_ids"><?php _e('Custom Related Products', 'woocommerce'); ?></label>
                         <select id="wcrp_related_ids" 
                        class="wc-product-search" 
                        name="wcrp_related_ids[]" 
                        multiple="multiple" 
                        style="width: 400px;" 
                       data-placeholder="<?php _e('Search for a product&hellip;', 'woocommerce'); ?>" 
                       data-action="woocommerce_json_search_products_and_variations">
                            <?php
                            foreach ($product_ids as $product_id) {
                                $product = wc_get_product($product_id);
                                if ( is_object( $product ) )
                                    echo '<option value="' . esc_attr($product_id) . '" selected="selected">' . wp_kses_post($product->get_formatted_name()) . '</option>';
                            }
                            ?>
                        </select> <img class="help_tip" data-tip='<?php _e('Related products are displayed on the product detail page.', 'woocommerce') ?>' src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
                    </p>
                <?php endif; ?>
            </div>
            <?php
        }
	
	 
        /**
         * Save related products on product edit screen
         */
        function wcrp_save_related_products($post_id, $post) {
            global $woocommerce;
            if (isset($_POST['wcrp_related_ids'])) {
                 if ($woocommerce->version >= '2.3' && $woocommerce->version < '3.0') {
                    $related = isset($_POST['wcrp_related_ids']) ? array_filter(array_map('intval', explode(',', $_POST['wcrp_related_ids']))) : array();
                } else {
                    $related = array();
                    $ids = $_POST['wcrp_related_ids'];
                    foreach ($ids as $id) {
                        if ($id && $id > 0) {
                            $related[] = $id;
                        }
                    }
                }
                update_post_meta($post_id, '_wcrp_related_ids', $related);
            } else {
                delete_post_meta($post_id, '_wcrp_related_ids');
            }
        }
		
		public function wcrp_do_activation_redirect() {
        // Bail if no activation redirect
        if (!get_transient('_wcrp_screen_activation_redirect')) {
            return;
        }

        // Delete the redirect transient
        delete_transient('_wcrp_screen_activation_redirect');

        // Bail if activating from network, or bulk
        if (is_network_admin() || isset($_GET['activate-multi'])) {
            return;
        }

        // Redirect to bbPress about page
        wp_safe_redirect(add_query_arg(array('page' => 'wcrp-about'), admin_url('index.php')));
    }

    public function wcrp_screen_pages() {
        add_dashboard_page(
                'Welcome To WooCommerce Custom Related Products', 'Welcome To WooCommerce Custom Related Products', 'read', 'wcrp-about', array($this, 'wcrp_screen_content'));
    }

    public function wcrp_screen_content() {
        ?>
        <div class="wrap wcrp_welcome_wrap">
            <fieldset>
                <h2>Welcome to WooCommerce Custom Related Products <?php echo Woo_Custom_Related_Products::VERSION; ?></h2>
                <div class="wcrp_welcom_div">
                    <div class="wcrp_lite">
                        <div>Thank you for installing WooCommerce Custom Related Products <?php echo Woo_Custom_Related_Products::VERSION; ?></div>
                        <div> WooCommerce Custom Related Products allows you to choose related products for the particular product.</div>
                        
                        <div class="block-content"><h4>How to Setup :</h4>

                            <ul>
                                <li>Step-1: Go to edit product section in product data section go to Linked Products you will find 'Custom Related Product'.</li>
                                <li>Step-2: Select products which you want to set related products for that product.</li>
                                <li>Step-3: Save product.</li>
                            </ul></div>
                        

                    </div>
                    <p class="wcrp_pro">
                        <a href="https://codecanyon.net/item/woocommerce-custom-related-products-pro/17893664" target="_blank"><h3>Try Our WooCommerce Custom Related Products Pro</h3></a>

                    <h4><strong> Key Features of WooCommerce Custom Related Products Pro</strong></h4>
                    <ul>
                        <li>
                            <strong>Choose custom related products </strong>
                            - For example you want to setup your own choice products as a related product for a particular product then please select products in custom related products option in edit product section
                        </li>
                        <li><strong>Choose related products from specific categories </strong>
                            - For example you want to display products from specific categories as a related product for a particular product then please select categories from ‘Select Categories’ options from Linked Products in product edit section .
                        </li>
                        <li><strong>Choose related products from specific tags </strong>
                            - For example you want to display products from specific tags as a related product for a particular product then please select tags from ‘Select Tags’ options from Linked Products in product edit section .
                        </li>
                        <li><strong>Set number of related products to display </strong>
                            - For example you want to display total 5 products in related product  then please enter the number of products to display in related product list

                    </ul>
                    <a href="https://codecanyon.net/item/woocommerce-custom-related-products-pro/17893664" target="_blank"><h4> Download WooCommerce Custom Related Products Pro Plugin</h4></a>
            </fieldset>
        </div>

        </div>


        <?php
    }

    public function wcrp_screen_remove_menus() {
        remove_submenu_page('index.php', 'wcrp-about');
    }


}