<?php

namespace LW;

class CommerceConfig
{
    function __construct()
    {
    }

    public function applyConfig()
    {
        add_filter('woocommerce_locate_template', array($this,'lummiWooLocateTemplate'), 10, 3 );
        add_filter('woocommerce_prevent_admin_access', array($this, 'allowAdminForClubManagers'), 10, 1);
        add_filter('template_redirect', array($this, 'notProductsFromAnotherClub'));
        add_action('woocommerce_product_query', array($this, 'wooQueryByTaxonomyClubs'));
	    add_filter('woocommerce_product_get_price', array($this, 'wooClubProductsPrice'),10,2);
	    add_filter('woocommerce_product_variation_get_price', array($this, 'wooClubProductsPrice'),10,2);
        add_action('restrict_manage_posts', array($this, 'filterShopOrderByClub'));
        add_filter('pre_get_posts', array($this, 'filterShopOrderByClubWhere'), 10, 1);
        add_filter('manage_edit-shop_order_columns', array($this, 'registerClubColumnToOrderTable'), 20);
	    add_action('woocommerce_thankyou', array($this, 'addOrderCustomFiled'), 10, 1);
        add_action('manage_shop_order_posts_custom_column', array($this, 'rendingClubColumnToOrderTable'), 2, 2);
        add_action('woocommerce_thankyou_order_received_text', array($this, 'confirmationMessage'));
        add_action('woocommerce_checkout_before_customer_details', array($this, 'checkoutMessage'));
        add_filter('woocommerce_disable_admin_bar', array($this, 'allowAdminBarForClubManagers'), 10, 1);
        add_filter('woocommerce_email_order_details', array($this, 'addClubConfirmMessageToEmail'), 10, 2);
        add_action('woocommerce_after_order_notes', array($this, 'shippingDateField'));
        add_action('woocommerce_checkout_update_order_meta', array($this, 'shippingDateUpdateOrderMeta'));
        add_action('woocommerce_admin_order_data_after_shipping_address', array($this, 'dateShippingFieldInAdmin'), 10, 1);
        add_filter('woocommerce_email_order_meta_keys', array($this, 'sendShippingDateToUserEmail'));
        add_filter('woocommerce_available_variation', array($this, 'filterPriceForVariableProducts'), 10, 3);
        add_filter('woocommerce_variable_price_html', array($this, 'formatVariationPriceForProduct'), 10, 2);
        add_filter('woocommerce_variable_sale_price_html', array($this, 'formatVariationPriceForProduct'), 10, 3);
        add_action('woocommerce_before_main_content', array($this, 'loginMessage'), 10);
        add_action('woocommerce_account_page_endpoint', array($this, 'loginMessage'), 10);
        add_action('woocommerce_account_page_endpoint', array($this, 'inactiveMessageInAccount'), 20);
        add_action('woocommerce_products_widget_query_args', array($this, 'wooFeaturedWidgetProduct'));
        add_filter('woocommerce_related_products_args', array($this, 'wooChangeClubRelated'));
        add_filter('gettext', array($this, 'wooRenameSku'), 10, 3);
        add_action('wp_footer', array($this, 'wooHideVariationClubType'), 100);
        add_filter('woocommerce_product_data_tabs', array($this, 'wooAddPriceVariationTab'));
        add_action('woocommerce_product_data_panels', array($this, 'wooCustomAdminProductFields'));
        add_action('woocommerce_process_product_meta', array($this, 'wooSaveVariationsClubPrices'));
        /*use for dropdown*/
        add_filter('woocommerce_dropdown_variation_attribute_options_html', array($this, 'filter_variations'), 10, 2);
        add_action( 'woocommerce_after_cart_table' , array($this, 'custom_override_checkout_fields') );
        add_filter( 'woocommerce_after_checkout_form', array($this, 'add_meta_on_checkout_order_review_item') );
        add_action('woocommerce_before_shop_loop_item',array($this,'stockOutProducts'),10,1);
        add_filter( 'post_date_column_time' , array($this,'woo_custom_post_date_column_time') );
        add_shortcode( 'inactive_club_message', array($this,'inactiveMessageShortcode') );
        add_filter( 'woocommerce_get_order_item_totals', array($this,'custom_woocommerce_get_order_item_totals') );
        add_filter('woocommerce_checkout_fields',array($this,'removeShippingAddr'));
        add_filter('woocommerce_new_customer_data', array($this,'wooAssignClubMember'), 10, 1);
        add_action('woocommerce_created_customer',array($this,'wooClubForNewUser'),10,1);
        add_action( 'wp_ajax_chefs_save_product_options', array($this,'applyChefsFromProducts'),30);
        add_action( 'woocommerce_product_after_variable_attributes', array($this,'variationSettingsFields'), 99, 3 );
        //Save Variation Settings
        add_action( 'woocommerce_save_product_variation', array($this,'saveVariationSettingsFields'), 10, 2 );
        add_shortcode('club_close_time',array($this,'clubCloseDateFront'));

        // Remove comment, if you want to set default user destination shipping. This is only for non registered users
        // add_filter('woocommerce_cart_shipping_packages', array($this, 'shippingOverrite'));

//        add_filter('woocommerce_is_sold_individually', array($this, 'productQuantity'), 10, 2);
//        add_action('woocommerce_before_shop_loop', array($this, 'productCategory'));
    }

	/**
	 * Show products category sidebar in category page
	 */
    public function productCategory(){
        if( is_product_category()){
	        echo get_sidebar('sidebar-in-category');
        }
    }

	/**
     * Hide/Show Quantity
	 * @param $return
	 * @param $product
	 *
	 * @return bool
	 */
	public function productQuantity( $return, $product ) {
		$user = wp_get_current_user();
		$hide_quantity = get_field('hide_quantity');
		$hide_quantity_for_clubs = get_field('hide_quantity_for_clubs');

		if($hide_quantity != '' && !in_array( 'club-member', (array) $user->roles)){
            return true;
        }

		if($hide_quantity_for_clubs != '' && in_array( 'club-member', (array) $user->roles)){
			return true;
		}

		return $return;
	}

	/**
     * Set default user destination
     *
	 * @param $info
	 * @return mixed
	 */
    public function shippingOverrite($info){

        $user = \LW\Settings::currentUser();
        $user_def_club = $user["club_term_meta"]["default_club"];
        $is_closed = \LW\Settings::is_closed_club();

        if(!is_user_logged_in() && !$user_def_club && $is_closed){
	        foreach ($info as $k => $v){
		        $info[$k]["destination"]["country"] = 'US';
		        $info[$k]["destination"]["state"] = 'Washington';
		        $info[$k]["destination"]["postcode"] = '98001';
	        }
        }

	    return $info;
    }

	/**
     * Get daylight saving time
	 * @param $tzId
	 * @return bool
	 */
	function timezoneDoesDST($tzId) {
		$tz = new \DateTimeZone($tzId);
		return count( $tz->getTransitions( time() ) ) > 0;
	}

	/**
     * Close date club shortcode
	 * @param $attributes
	 * @param $content
	 *
	 * @return string
	 */
    public function clubCloseDateFront($attributes,$content){

        $user = \LW\Settings::currentUser();

        if( ! empty($user["user_info"]) && ! is_admin() ){

            $date = new \DateTime();
	        $_date = $date->createFromFormat('m-d-Y h:i a',$user["club_term_meta"]["clubs_close_date"]);

	        $origin_date = $_date->format('n/j/Y h:i:s A');

            $time_zone = $this->timezoneDoesDST('America/Los_Angeles');

	        wp_enqueue_script('moment',LW_PLUGIN_URL . 'additional/js/moment.js','', '', true);
	        wp_enqueue_script('time_script',LW_PLUGIN_URL . 'additional/js/time_script.js', array('jquery'), '', true);
            wp_localize_script('time_script','dt',array(
                    'origin_date' => $origin_date,
                    'la_time' => $time_zone
            ));
	        $attributes['data'] = shortcode_atts(
	                array(
                        'origin_date' => $origin_date
                    ),
                    $attributes
            );

	        $content = \LW\LW_Template::getTemplate('client_templates','club_close_time',$attributes,false);

	        return $content;

        }
    }
    
    public function variationSettingsFields($loop, $variation_data, $variation ){

        global $product;
        
        $master_chefs = get_post_custom($product->ID);
	    $is_club_chefs = null;
        
        foreach ($master_chefs as $k => $v){
            $e = explode('_',$k);
            if($e[0] === 'chefs'){
                $term = get_term($e[3]);
                $chef_percent = get_post_meta( $variation->ID, 'chef_var_percent_'.$term->term_id.'', true );
	            woocommerce_wp_text_input(
                    array(
                        'id'          => 'chef_var_percent_'.$term->term_id.'[' . $variation->ID . ']',
                        'class'       => 'chefs_variation_percent',
                        'label'       => __( 'Chefs percent for '.$term->name, 'woocommerce' ),
                        'placeholder' => ( ! $chef_percent ) ?  $v[0] : null,
                        'desc_tip'    => 'true',
                        'description' => __( 'Enter the percent here. Only numbers.', 'woocommerce' ),
                        'value'       => $chef_percent
                    )
                );
            }
        }
    }

    public function saveVariationSettingsFields($post_id){

        $master_chefs = get_post_custom($_POST["product_id"]);

        if( $master_chefs ){
            foreach ($master_chefs as $k => $v){
                $e = explode('_',$k);
                if( $e[0] === 'chefs' ){
                    $term_meta = get_term($e[3]);
                    $chef_percent = $_POST['chef_var_percent_'.$term_meta->term_id][ $post_id ];
                    update_post_meta( $post_id, 'chef_var_percent_'.$term_meta->term_id , esc_attr( $chef_percent ) );
                }
            }
        }
    }

    
    /**
    * Add default club to new user. Registered from checkout page
    */
    public function wooClubForNewUser($customer_id){

        $dclub = get_terms('clubs', array('fields' => 'all'));
            foreach ($dclub as $k => $cl) {
                if (get_term_meta($cl->term_id, 'default_club', true)) {
                    $default_club_id = $cl->term_id;
            }
        }
        if($default_club_id){
            update_user_meta( $customer_id, 'user_club', $default_club_id );
        }
    }
    /**
    * Add "club-member" user role. Registered from checkout page 
    */
    public function wooAssignClubMember($args) {

    $args['role'] = 'club-member';

    return $args;
    }
    
    /**
    * Remove shipping fields from private club users
    */
    public function removeShippingAddr($fields){

        $user = \LW\Settings::currentUser();
    
        $is_closed_club = \LW\Settings::is_closed_club();

        if( ! $is_closed_club && $user['user_info']['ID'] && ! $user["club_term_meta"]["default_club"] ){
            $fields["shipping"] = array();
        }

        return $fields;
    }

    /**
    * 
    */
    function custom_woocommerce_get_order_item_totals( $totals ) {
        $user = \LW\Settings::currentUser();

        $is_closed_club = \LW\Settings::is_closed_club();

        if(  ! $is_closed_club && $user['user_info']['ID'] && ! $user["club_term_meta"]["default_club"]  ){
            unset($totals['shipping']);
        }

        return $totals;
    }
    
    function woo_custom_post_date_column_time( $post ) {
        $h_time = get_post_time( __( 'm-d-Y', 'woocommerce' ), $post );
        return $h_time;
    }

    public function stockOutProducts(){

        global $post, $product;

        $user = \LW\Settings::currentUser();
        $is_closed_club = \LW\Settings::is_closed_club();

        $product = wc_get_product( $post->ID );

        if( $product->is_type( 'variable' )) {
	        $variables = $product->get_available_variations();

            if ( ! empty( $variables ) ){

                foreach( $variables as $k => $v ){
                    $arr[] = $v["attributes"]["attribute_pa_club-type"];
                }

                $defaul_club = (isset($user["club_term_meta"]["default_club"])) ? $user["club_term_meta"]["default_club"] : null;

                if( $is_closed_club && array_search('default-club',$arr) === FALSE ){
                    echo '<script type="text/javascript">
                                jQuery(document).ready(function($){
                                    $(".products .post-'.$post->ID.'").remove();
                                });
                              </script>';
                }
                if( ! is_user_logged_in() && array_search('default-club',$arr) === FALSE ){
                        echo '<script type="text/javascript">
                                jQuery(document).ready(function($){
                                    $(".products .post-'.$post->ID.'").remove();
                                });
                              </script>';
                }elseif(is_user_logged_in() && $defaul_club && array_search('default-club',$arr) === FALSE ){
                        echo '<script type="text/javascript">
                                jQuery(document).ready(function($){
                                    $(".products .post-'.$post->ID.'").remove();
                                });
                              </script>';
                }elseif(is_user_logged_in() && ! $defaul_club && array_search('private-club',$arr) === FALSE && ! $is_closed_club){
                        echo '<script type="text/javascript">
                                jQuery(document).ready(function($){
                                    $(".products .post-'.$post->ID.'").remove();
                                });
                              </script>';
                }
            }
        }
    }
    

    public function custom_override_checkout_fields()
    {
        $user = \LW\Settings::currentUser();
        $capabilities = unserialize($user['user_meta']['wpsm_capabilities']);
        ?>

        <script type="text/javascript">

            /*add current user to window obj*/
            window.Lumi = {};
            window.Lumi.user = <?php echo json_encode($user); ?>;

            /*add unserialized capabilities to current user*/
            window.Lumi.user.capabilities = <?php echo json_encode($capabilities); ?>;

            /*
             * finding unique label identifier in this case " | "  and explode through it
             * check if user has some capabilities and show second exploded label
             * */
            jQuery(document).ready(function ($) {
                changeLabel();

                $( document ).on( 'updated_cart_totals', function(){
                    changeLabel();
                    jQuery('[data-title="Shipping"]:contains("Club Method:")').each(function(){jQuery(this).html(jQuery(this).html().split("Club Method:").join(""));});
                });

                function changeLabel(){
                    $("table.shop_table .cart_item dl.variation").each(function () {
                        $($($(this))[0]).find('dt').each(function(){
                            if($(this)[0].innerText.indexOf(' | ') !== -1){
                                var label = $(this)[0];
                                var explodeLabel = label.innerText.split(' | ');

                                if(window.Lumi.user.capabilities.hasOwnProperty('administrator') || window.Lumi.user.capabilities.hasOwnProperty('club-manager')){
                                    label.innerText = explodeLabel[1];
                                }
                                if(window.Lumi.user.club_term_meta){
                                    if(window.Lumi.user.club_term_meta.hasOwnProperty('default_club')){
                                        label.innerText = explodeLabel[0];
                                    }else{
                                        label.innerText = explodeLabel[1];
                                    }

                                }
                                if(window.Lumi.user.capabilities === false){
                                    label.innerText = explodeLabel[0];
                                }
                            }
                        });
                    });
                }

            });
        </script>

        <?php
    }

    public function add_meta_on_checkout_order_review_item()
    {
        $user = \LW\Settings::currentUser();
        $capabilities = unserialize($user['user_meta']['wpsm_capabilities']);
        ?>
        <script>
            /*add current user to window obj*/
            window.Lumi = {};
            window.Lumi.user = <?php echo json_encode($user); ?>;

            /*add unserialized capabilities to current user*/
            window.Lumi.user.capabilities = <?php echo json_encode($capabilities); ?>;

            jQuery(document).ready(function ($) {
                changeLabel();
                jQuery('[data-title="Shipping"]:contains("Club Method:")').each(function(){jQuery(this).html(jQuery(this).html().split("Club Method:").join(""));});
                $( document ).on( 'updated_checkout', function() {
                    changeLabel();
                    jQuery('[data-title="Shipping"]:contains("Club Method:")').each(function(){jQuery(this).html(jQuery(this).html().split("Club Method:").join(""));});
                });

                function changeLabel(){
                    $('tr.cart_item').each(function(){
                        $($(this)[0]).find('.variation').each(function(){
                            $($(this)[0]).find('dt').each(function(){
                                if($(this)[0].innerText.indexOf(' | ') !== -1){

                                    var label = $(this)[0];
                                    var explodeLabel = label.innerText.split(' | ');

                                    if(window.Lumi.user.capabilities.hasOwnProperty('administrator') || window.Lumi.user.capabilities.hasOwnProperty('club-manager')){
                                        $(this)[0].innerText = explodeLabel[1];
                                    }
                                    if(window.Lumi.user.club_term_meta){
                                        if(window.Lumi.user.club_term_meta.hasOwnProperty('default_club')){
                                            label.innerText = explodeLabel[0];
                                        }else{
                                            label.innerText = explodeLabel[1];
                                        }
                                    }
                                    if(window.Lumi.user.capabilities === false){
                                        label.innerText = explodeLabel[0];
                                    }
                                }
                            });
                        });
                    });
                }
            });
        </script>
        <?php
    }

    public function filter_variations($html, $args)
    {
        return $html;
    }


    /**
     * Save Custom fileds for variation price
     * @param $post_id
     */
    public function wooSaveVariationsClubPrices($post_id)
    {
        // Text field for DEFAULT club variable price
        $woocommerce_text_field_default = $_POST['_default_club_price'];

        if (!empty($woocommerce_text_field_default)) {
            update_post_meta($post_id, '_default_club_price', esc_attr($woocommerce_text_field_default));
        }

        // Text field for PRIVATE club variable price
        $woocommerce_text_field_private = $_POST['_private_club_price'];

        if (!empty($woocommerce_text_field_private)) {
            update_post_meta($post_id, '_private_club_price', esc_attr($woocommerce_text_field_private));
        }

	    $club_id = null;

	    foreach ($_POST as $k => $v){

		    $e = explode('_',$k);

		    if($e[0] === 'chefs'){
			    $club_id = (int)$e[3];
			    if( is_int($club_id) ){
				    update_post_meta($post_id, $k, $v);
			    }
		    }
	    }

    }

	/**
	 *  Update chefs club percent in product via Ajax
	 */
    public function applyChefsFromProducts(){
	    check_ajax_referer( 'chefs_secure_product', 'chefs_nonce_product');

	    parse_str($_POST["chefs_prc"], $chefs_post);

	    $product_id = ( $_POST["product_id"] ) ? $_POST["product_id"] : null;

	    if( ! empty($chefs_post) && is_array($chefs_post) && $product_id){
	        foreach ($chefs_post as $k => $v){
	            update_post_meta($product_id, $k, $v);
            }
        }else{
	        wp_send_json_error();
	        die();
        }

        wp_send_json_success();
        die();
    }

    /**
     * Register new tab for variation price field
     * @param $product_data_tabs
     * @return mixed
     */
    public function wooAddPriceVariationTab($product_data_tabs)
    {
        $product_data_tabs['individual_variation_price'] = array(
            'label' => __('Price Variations (Min - Max)', 'woocommerce'),
            'target' => 'individual_variation_price',
            'class' => array('show_if_variable'),
            'priority' => 80
        );

	    $product_data_tabs['chefs_club_option'] = array(
		    'label' => __('Chef\'s Clubs', 'woocommerce'),
		    'target' => 'chefs_club_option',
		    'priority' => 90
	    );

        return $product_data_tabs;
    }

    /**
     * Add custom field for club variation price
     */
    public function wooCustomAdminProductFields($variation)
    {
	global $post;

        echo '<div id="individual_variation_price" class="panel woocommerce_options_panel">';

        woocommerce_wp_text_input(array(
            'id' => '_default_club_price',
            'wrapper_class' => 'show_if_variable',
            'label' => __('Default Club Prices', 'woocommerce'),
            'description' => __('Low to High. Separated by a hyphen (only numbers without currency prefix)', 'woocommerce'),
            'default' => '0',
            'desc_tip' => false,
        ));
        woocommerce_wp_text_input(array(
            'id' => '_private_club_price',
            'wrapper_class' => 'show_if_variable',
            'label' => __('Private Club Prices', 'woocommerce'),
            'description' => __('Low to High. Separated by a hyphen (only numbers without currency prefix)', 'woocommerce'),
            'default' => '0',
            'desc_tip' => false,
        ));
        echo '</div>';

	    echo '<div id="chefs_club_option" class="panel woocommerce_options_panel"><h3 style="margin-left: 10px;">Chef\'s clubs</h3><hr/>';

        $master_chefs = get_post_custom($post->ID);
	    $is_club_chefs = null;

        foreach ($master_chefs as $k => $v){
            $e = explode('_',$k);
            if($e[0] === 'chefs'){

	            $term_meta = get_term($e[3]);

	            woocommerce_wp_text_input(array(
		            'name' => $k,
		            'class' => $k,
		            'id' => $k,
		            'value' => $v[0],
		            'label' => __($term_meta->name, 'woocommerce'),
		            'description' => __('percent discount', 'woocommerce'),
		            'default' => '0',
		            'desc_tip' => false,
	            ));

	            $is_club_chefs = true;
            }
        }

        if( ! $is_club_chefs ){
            echo '<h2>No clubs available</h2>';
        }else{
	        woocommerce_wp_hidden_input(array(
                'id' => 'chefs-post-id-' . $post->ID,
		        'value' => $post->ID,
                'class' => 'chefs-post-id-' . $post->ID,
            ));
            echo '<div class="chefs-btn-cont">
                        <button type="button" class="save_chefs_product button button-primary" disabled="disabled">Save changes</button>
                        <span class="load-image"></span>
                   <div>';
        }

	    echo '</div>';
    }

    /**
     * Remove type variation by club
     */
    public function wooHideVariationClubType()
    {

        $user = \LW\Settings::currentUser();
        $is_closed_club = \LW\Settings::is_closed_club();

        $capabilities = unserialize($user['user_meta']['wpsm_capabilities']);
        ?>

        <script type="text/javascript">

            /*add current user to window obj*/
            window.Lumi = {};
            window.Lumi.user = <?php echo json_encode($user); ?>;

            /*add unserialized capabilities to current user*/
            window.Lumi.user.capabilities = <?php echo json_encode($capabilities); ?>;

            /*
             * finding unique label identifier in this case " | "  and explode through it
             * check if user has some capabilities and show second exploded label
            * */
            jQuery(document).ready(function ($) {
                $("table.variations td.label label").each(function () {
                    if ($(this)[0].innerText.indexOf(' | ') !== -1) {

                        var label = $(this)[0];
                        var explodeLabel = label.innerText.split(' | ');

                        if(window.Lumi.user.capabilities.hasOwnProperty('administrator') || window.Lumi.user.capabilities.hasOwnProperty('club-manager')){
                            label.innerText = explodeLabel[1];
                        }
                        if(window.Lumi.user.club_term_meta){
                            if(window.Lumi.user.club_term_meta.hasOwnProperty('default_club')){
                                label.innerText = explodeLabel[0];
                            }else{
                                label.innerText = explodeLabel[1];
                            }
                        }
                        if(window.Lumi.user.capabilities === false){
                            label.innerText = explodeLabel[0];
                        }
                    }
                });
            });
        </script>

        <?php

        if (is_user_logged_in() && current_user_can('administrator')) {
            return false;
        }

        ?>
        <script type="text/javascript">
                jQuery(document).ready(function($) {
                    $('table.variations tr').has( '#pa_club-type' ).hide();
                    $(".reset_variations").remove();
                    $("dl.variation .variation-ClubType").remove();
                });
        </script>
        <style>
            .woocommerce td.product-name dl.variation .variation-ClubType{
                display: none !important;
            }
        </style>
    <?php }


    /**
     * Rename SKU to Lot Number
     */
    function wooRenameSku($translation, $text, $domain)
    {
        if ($domain == 'woocommerce') {
            switch ($text) {
                case 'SKU':
                    $translation = 'Lot Number';
                    break;
                case 'SKU:':
                    $translation = 'Lot Number:';
                    break;
            }
        }
        return $translation;
    }

    /**
     * Filtered related products by clubs
     * @param $arg
     * @return mixed
     */
    public function wooChangeClubRelated($arg)
    {

        $data = \LW\Settings::currentUser();

        if (is_user_logged_in() && $data["club_term_meta"]["clubs_active"] == 'on' && self::clubsDateCloseOpenOrder() === 0) {

            unset($arg['post__in']);
            unset($arg['post__not_in']);

            $tax_query = array(
                'taxonomy' => $data['club_taxonomy']->taxonomy,
                'field' => 'slug',
                'terms' => $data['club_taxonomy']->slug
            );
            $arg['tax_query'][] = $tax_query;
        } elseif ((is_user_logged_in() && $data["club_term_meta"]["clubs_active"] == 'off') || (is_user_logged_in() && self::clubsDateCloseOpenOrder() === 1)) {
            unset($arg);
        } else {
            unset($arg['post__in']);
            unset($arg['post__not_in']);
            $dclub = get_terms('clubs', array('fields' => 'id=>slug'));
            foreach ($dclub as $k => $slug) {
                if (get_term_meta($k, 'default_club', true)) {
                    $default = $slug;
                }
            }
            $tax_query = array('taxonomy' => 'clubs',
                'field' => 'slug',
                'terms' => $default
            );
            $arg['tax_query'][] = $tax_query;
        }

        return $arg;
    }

    /**
     * Filter featured widget products
     * @param $arg
     * @return mixed
     */
    public function wooFeaturedWidgetProduct($arg)
    {

        $data = \LW\Settings::currentUser();

        if (is_user_logged_in() && $data["club_term_meta"]["clubs_active"] == 'on' && self::clubsDateCloseOpenOrder() === 0) {

            $tax_query = array(
                'taxonomy' => $data['club_taxonomy']->taxonomy,
                'field' => 'slug',
                'terms' => $data['club_taxonomy']->slug
            );
            $arg['tax_query'][] = $tax_query;
        } elseif ((is_user_logged_in() && $data["club_term_meta"]["clubs_active"] == 'off') || (is_user_logged_in() && self::clubsDateCloseOpenOrder() === 1)) {
            unset($arg);
        } else {
            $dclub = get_terms('clubs', array('fields' => 'id=>slug'));
            foreach ($dclub as $k => $slug) {
                if (get_term_meta($k, 'default_club', true)) {
                    $default = $slug;
                }
            }
            $tax_query = array('taxonomy' => 'clubs',
                'field' => 'slug',
                'terms' => $default
            );
            $arg['tax_query'][] = $tax_query;
        }

        return $arg;
    }

    /**
     * Login Message
     */
    public function loginMessage()
    {

        $data = \LW\Settings::currentUser();

        if (is_user_logged_in() && $data["club_term_meta"]["clubs_active"] == 'on' && self::clubsDateCloseOpenOrder() === 0) {
            echo '<div class="wellcome_message_container">
                    <p class="the_login_message">' . $data["club_term_meta"]["clubs_after_login_massage"] . '</p>
                  </div>';
        }
    }

    /**
     * Inactive message
     */
    public function inactiveMessageInAccount()
    {
        $data = \LW\Settings::currentUser();

        if ((is_user_logged_in() && $data["club_term_meta"]["clubs_active"] == 'off') || (is_user_logged_in() && self::clubsDateCloseOpenOrder() === 1)) {
            echo '<div class="wellcome_message_container">
                    <p class="the_login_message">' . $data["club_term_meta"]["clubs_inactive_massage"] . '</p>
                  </div>';
        }
    }

    /**
     * Inactive message shortcode
     */
    public function inactiveMessageShortcode(){
        $data = \LW\Settings::currentUser();
        return '<div class="wellcome_message_container">
                    <p class="the_login_message">' . $data["club_term_meta"]["clubs_inactive_massage"] . '</p>
                  </div>';
    }

    /**
     * Send shipping date to user if is club member
     * @param $keys
     * @return mixed
     */
    public function sendShippingDateToUserEmail($keys)
    {

        $shipping_date = \LW\Settings::currentUser();

        if ($shipping_date["club_term_meta"]["clubs_ship_date"]) {
            $keys['Shipping Date'] = 'club_order_shipping_date';
        }
        return $keys;
    }

    public function formatVariationPrice($avvar_ids,$chefs_percent,$club_id,$user)
    {
	    foreach($avvar_ids as $vids){
		    $var_type = get_post_meta($vids,'attribute_pa_club-type',true);
		    if ( $var_type === 'private-club'){
			    $regular_price = get_post_meta($vids,'_regular_price',true);
			    $chefs_variation_percent = get_post_meta($vids,'chef_var_percent_'.$club_id,true);
			    $percent_type = ( $chefs_variation_percent === '' ) ? $chefs_percent : $chefs_variation_percent;
			    $var_total[$vids]['total_price'] = $regular_price - ( ( $regular_price * ( $percent_type + $user["club_term_meta"]["clubs_discount_p"] ) ) / 100);
		    }
	    }
	    return $var_total;
    }

    /**
     * Format percent price for variation product
     * @param $price
     * @param $product
     * @return string
     */
    public function formatVariationPriceForProduct($price, $product)
    {

	    $cp = get_woocommerce_currency_symbol();
	    $user = \LW\Settings::currentUser();
	    $is_closed_club = \LW\Settings::is_closed_club();
	    $percent = null;

	    if( ! $is_closed_club && ! empty( $user["user_info"] ) ){
		    if( isset($user["club_term_meta"]["chefs_active"]) && $user["club_term_meta"]["chefs_active"] === '1' ){
			    $club_id = $user["user_meta"]["user_club"];
			    $chefs_percent = get_post_meta($product->id,'chefs_club_id_'.$club_id,true);
                if( $product->has_child() ){
                    $var_ids = $product->get_children();
	                $var_total = $this->formatVariationPrice($var_ids,$chefs_percent,$club_id,$user);

                }else{
                    $avvar = $product->get_available_variations();
	                $avvar_ids = @array_column($avvar,'variation_id');
	                $var_total = $this->formatVariationPrice($avvar_ids,$chefs_percent,$club_id,$user);
                }

				$the_price = @array_column($var_total, 'total_price');

                if(count($the_price) > 1 && min($the_price) !== max($the_price) ){
                    $min = min($the_price);
                    $max = max($the_price);
                    $price = $cp.number_format($min,2,".","") .' - '. $cp.number_format($max,2,".","");
                }else{
                    $price = $cp.number_format($the_price[0],2,".","");
                }

			    return $price;
		    }
	    }


	    if ($product->has_child())
	    {
		    if ( ( ! $is_closed_club && is_user_logged_in() && ! isset($user["club_term_meta"]["default_club"]) )
		         || ( ! $is_closed_club && is_user_logged_in() && current_user_can('administrator') ) )
		    {
			    $price_raw = get_post_meta(get_the_ID(), '_private_club_price', true);
			    $pr_exp = explode('-', $price_raw);
			    $final_min = trim($pr_exp[0]);
			    $final_max = trim($pr_exp[1]);
		    } else {
			    $price_raw = get_post_meta(get_the_ID(), '_default_club_price', true);
			    $pr_exp = explode('-', $price_raw);
			    $final_min = trim($pr_exp[0]);
			    $final_max = trim($pr_exp[1]);
		    }
	    }
	    // TODO
	    if( $is_closed_club ){
		    $percent = 0;
	    }
        elseif($user["club_term_meta"]["clubs_discount_p"]){
		    $percent = $user["club_term_meta"]["clubs_discount_p"];
	    }

	    if ( ! is_null($percent) ) {
		    $price = '';

		    if ($final_min) {
			    $price .= '<span class="from">' . _x('', 'min_price', 'woocommerce') . ' </span>';
			    $res = $final_min * $percent / 100;
			    $price .= wc_price($final_min - $res);
		    }
		    if ($final_max) {
			    $price .= '<span class="from">' . _x(' -', 'max_price', 'woocommerce') . ' </span>';
			    $res = $final_max * $percent / 100;
			    $price .= wc_price($final_max - $res);
		    }
	    }else{
		    $price = $cp . $final_min . ' - ' . $cp . $final_max;
	    }

	    return $price;

    }


    /**
     * Replace variable product price with club percent
     * @param $data
     * @param $product
     * @param $variation
     * @return mixed
     */
    public function filterPriceForVariableProducts($data, $product, $variation)
    {
	    $cp = get_woocommerce_currency_symbol();
        $is_closed_club = \LW\Settings::is_closed_club();
        $user = \LW\Settings::currentUser();

	    $data['price_html'] = '<p class="price"><span class="woocommerce-Price-amount amount">' . wc_price($data['display_regular_price']) . '</span></p>';

	    if( ! $is_closed_club && ! empty( $user["user_info"] ) ){
		    if( isset($user["club_term_meta"]["chefs_active"]) && $user["club_term_meta"]["chefs_active"] === '1' ){
			    $club_id = $user["user_meta"]["user_club"];
			    $chefs_percent = get_post_meta($product->get_id(),'chefs_club_id_'.$club_id,true);

                $var_total = $this->formatVariationPrice($product->get_children(),$chefs_percent,$club_id,$user);
                if( $var_total[$data['variation_id']]["total_price"] ){
	                $data['display_price'] = $var_total[$data['variation_id']]["total_price"];
	                $data['display_regular_price'] = $var_total[$data['variation_id']]["total_price"];
	                $data['price_html'] = '<p class="price"><span class="woocommerce-Price-amount amount">' . $cp.number_format($var_total[$data['variation_id']]["total_price"],2,".","") . '</span></p>';
                }

			    return $data;
		    }
	    }

        if( $is_closed_club ){
            $percents = 0;
        }
        elseif($user["club_term_meta"]["clubs_discount_p"]){
	        $percents =  $user["club_term_meta"]["clubs_discount_p"];
        }

        
        if (is_user_logged_in()) {
            if ($user["club_term_meta"]["clubs_discount_p"]) {
                // Amount of subtraction according percent
//                $data['display_price'] =  $data['display_price'] - ($data['display_price'] * $percents / 100);
                $data['display_regular_price'] = $data['display_regular_price'] - ($data['display_regular_price'] * $percents / 100);
                $data['price_html'] = '<p class="price"><span class="woocommerce-Price-amount amount">' . wc_price($data['display_regular_price']) . '</span></p>';
                return $data;
            }
        }

        return $data;
    }

    /**
     * Add shipping date fields to checkout form
     * @param $checkout
     */
    function shippingDateField($checkout)
    {
        $shipping_date = \LW\Settings::currentUser();

        if ($shipping_date["club_term_meta"]["clubs_ship_date"]) {
            $sh_date = $shipping_date["club_term_meta"]["clubs_ship_date"];
            echo '<div id="club_shipping_date"><p style="font-size: 18px; font-weight: bold; margin: 0;">' . __('Shipping Date') . '</p></div>';
            echo '<input type="text" name="club_shipping_date" value="' . $sh_date . '" readonly/>';
        }
    }

    /**
     * Add shipping date to post meta
     * @param $order_id
     */
    function shippingDateUpdateOrderMeta($order_id)
    {
        if( isset($_POST['club_shipping_date']) && !get_post_meta($order_id,'club_order_shipping_date',true)){
	        update_post_meta($order_id, 'club_order_shipping_date', $_POST['club_shipping_date']);
        }
    }

    /**
     * Add shipping date to admin
     * @param $order
     */
    public function dateShippingFieldInAdmin($order)
    {
        echo '<p><strong>' . __('Shipping Date') . ':</strong> ' . get_post_meta($order->id, 'club_order_shipping_date', true) . '</p>';
    }

    /**
     * @param $order
     * @param $sent_to_admin
     */
    public function addClubConfirmMessageToEmail($order, $sent_to_admin)
    {
        $is_closed_club = \LW\Settings::is_closed_club();
         $club_name = \LW\Settings::currentUser();
         $conf_message = $club_name["club_term_meta"]["clubs_confirmation_massage"];

        if($is_closed_club){
	        $default = \LW\Settings::getDefaultClub();
	        $conf_message = $default["clubs_confirmation_massage"][0];
        }

        if (!$sent_to_admin) {
                echo '<p style="font-size: 20px; margin-bottom: 5px;">' . $conf_message . '</p>';
                echo '<style>
                        #body_content_inner p:nth-child(1){display:none;}
                        </style>';
        }
    }

    public function confirmationMessage($order_id)
    {
	    $user = \LW\Settings::currentUser();
	    $default = \LW\Settings::getDefaultClub();
        $is_closed_club = \LW\Settings::is_closed_club();
	    $conf_message = $default["clubs_confirmation_massage"][0];

        if(!$is_closed_club && !isset($user["club_term_meta"]["default_club"] )){
            $conf_message = $user['club_term_meta']["clubs_confirmation_massage"];
        }

        return $conf_message;
    }

    public function checkoutMessage()
    {
        $user = \LW\Settings::currentUser();
        $default = \LW\Settings::getDefaultClub();
        $is_closed = \LW\Settings::is_closed_club();

        if(!$is_closed && !isset($user["club_term_meta"]["default_club"])){
	        echo '<p class="lwu-checkout-message">' . $user['club_term_meta']["clubs_checkout_massage"] . '</p>';
        }else{
	        echo '<p class="lwu-checkout-message">' . $default["clubs_checkout_massage"][0] . '</p>';
        }
    }

    /**
     * Register columns for display club name in orders table
     * @param $columns
     * @return mixed
     */
    public function registerClubColumnToOrderTable($columns)
    {
        $offset = 15;
        $updated_columns = array_slice($columns, 0, $offset, true) +
            array('club_field' => esc_html__('Club', 'woocommerce')) +
            array_slice($columns, $offset, NULL, true);
        return $updated_columns;
    }

    /**
     * Rending clubs name to columns orders table
     * @param $column
     * @param $order_id
     */
    function rendingClubColumnToOrderTable($column, $order_id)
    {
        if ($order_id) {
            $club_order = get_post_meta($order_id, 'clubs-name', true);
            if ($column == 'club_field' && $club_order != '') {
                echo $club_order;
            }
        }
    }

    /**
     * If the current date is greater than the date of close
     */
    public static function clubsDateCloseOpenOrder()
    {
        $base = \LW\Settings::currentUser();
        $status = 0;
        
        if ( is_user_logged_in() && ! current_user_can('administrator') ) {
            
            $date_now = \LW\Settings::getDateFormat('timestamp_now');
            
            $close_date = \LW\Settings::getDateFormat('this_timestamp', array(
                'date' => $base["club_term_meta"]["clubs_close_date"]
            ));
            
            $open_date = \LW\Settings::getDateFormat('this_timestamp',  array(
                'date' => $base["club_term_meta"]["clubs_open_date"]
            ));
            
            if ( $date_now > $close_date || $date_now < $open_date ){
                $status = 1;
            }
            
        }
        
        return $status;
    }

    /**
     * Get clubs to filter
     */
    public function filterShopOrderByClub()
    {
        global $typenow, $wp_query;
        if (in_array($typenow, wc_get_order_types('order-meta-boxes'))) :
            $slug = '';

            $order = get_terms(array('taxonomy' => 'clubs'), array('hide_empty' => false));

            if (!empty($_GET['clubs-slug'])) {
                $slug = sanitize_text_field($_GET['clubs-slug']);
            }
            // Display drop down
            ?><select name='clubs-slug'>
            <option value=''><?php _e('Filter by Club', 'woocommerce'); ?></option><?php
            foreach ($order as $key => $value) :
                ?>
                <option <?php selected($slug, $value->slug); ?>
                value='<?php echo $value->slug; ?>'><?php echo $value->name; ?></option><?php
            endforeach;
            ?></select><?php
        endif;
    }

    /**
     * Filter the clubs orders
     */
    public function filterShopOrderByClubWhere($query)
    {
        if (!$query->is_main_query() || !isset($_GET['clubs-slug'])) {
            return;
        }

        $query->set('meta_query', array(
            array(
                'key' => 'clubs-slug',
                'value' => sanitize_text_field($_GET["clubs-slug"]),
            )
        ));
        if (empty($ids)) {
            $query->set('posts_per_page', 0);
        }
    }

    /**
     * Set Order custom fields - clubs-name, clubs-slug
     * Set default club for correct work
     * @param $order_id
     */
    public function addOrderCustomFiled($order_id)
    {
        if( !$order_id )
        {
            return;
        }

        $is_closed_club = \LW\Settings::is_closed_club();

        $liw_order = wc_get_order($order_id);
        $liw_items = $liw_order->get_items();

	    $gd = 0;
        $userID = get_current_user_id();
        $club = \LW\Settings::currentUser();

        if($is_closed_club){

            $dclub = get_terms(array('taxonomy' => 'clubs'));

            foreach ($dclub as $val) {
                if ( get_term_meta($val->term_id, 'default_club', true) )
                {
                    $def_club_name = $val->name;
                    $def_club_slug = $val->slug;

                }
            }

        }

        foreach ($liw_items as $liw_item) {

	        $chefs_id = $club["user_meta"]["user_club"];
	        if( $club["club_term_meta"]["chefs_active"] === '1' &&  ! $is_closed_club ) {

	            $chefs_individual_percent = get_post_meta( $liw_item["variation_id"], 'chef_var_percent_' . $chefs_id, true );
		        $chef_default_percent = get_post_meta( $liw_item["product_id"], 'chefs_club_id_' . $chefs_id, true );

		        if ( $liw_item["variation_id"] !== '' ) {
		            $prc = ($chefs_individual_percent !== '' ) ? $chefs_individual_percent : $chef_default_percent;
			        update_post_meta( $order_id, 'order_variation_chef_prc_' . $liw_item["variation_id"], $prc );
		        } else {
			        update_post_meta( $order_id, 'order_product_chef_prc_' . $liw_item["product_id"], $chef_default_percent );
		        }
	        }

            if ($gd !== 1) {

                $club_name = ($def_club_name) ? $def_club_name : $club['club_taxonomy']->name;
                $club_slug = ($def_club_slug) ? $def_club_slug : $club['club_taxonomy']->slug;

                if (!get_post_meta($order_id, 'clubs-name', true)) {
                    update_post_meta($order_id, 'clubs-name', $club_name, true);
                }
                if (!get_post_meta($order_id, 'clubs-slug', true)) {
                    update_post_meta($order_id, 'clubs-slug', $club_slug, true);
                }
                if (!get_post_meta($order_id, 'club-manager', true)) {
                    update_post_meta($order_id, 'club-manager', $userID, true);
                }
            }
            $gd = $gd + 1;

        }

    }

    /**
     * Filterd price by club
     * Apply in cart and after switch variation
     * @param $price
     * @param $product
     * @return mixed
     */
    public function wooClubProductsPrice($price,$product)
    {

        $is_closed_club = \LW\Settings::is_closed_club();
        $user = \LW\Settings::currentUser();

	    if( ! $is_closed_club && ! empty( $user["user_info"] ) ){
		    if( isset($user["club_term_meta"]["chefs_active"]) && $user["club_term_meta"]["chefs_active"] === '1' ){
			    $club_id = $user["user_meta"]["user_club"];
			    $chefs_percent = get_post_meta($product->id,'chefs_club_id_'.$club_id,true);
			    if($product->variation_id){
				    $chefs_variation_percent = get_post_meta($product->variation_id,'chef_var_percent_'.$club_id,true);
			    }else{
				    $chefs_variation_percent = get_post_meta($product->id,'chef_var_percent_'.$club_id,true);
			    }
			    $percent_type = ( $chefs_variation_percent === '' ) ? $chefs_percent : $chefs_variation_percent;
			    $price = $price - ( ( $price * ( $percent_type + $user["club_term_meta"]["clubs_discount_p"] ) ) / 100);

			    return $price;
		    }
	    }

        if (is_user_logged_in()) {

            if( $is_closed_club ){

//                $dclub = get_terms('clubs', array('fields' => 'all'));
//
//                foreach ($dclub as $k => $cl) {
//                    if (get_term_meta($cl->term_id, 'default_club', true)) {
//                        $default = get_term_meta($cl->term_id);
//                    }
//                }
//                $percent = $default["clubs_discount_p"][0];
                $percent = 0;
            }
            elseif($user["club_term_meta"]["clubs_discount_p"]){
	            $percent = $user["club_term_meta"]["clubs_discount_p"];
            }

            if ( $user["club_term_meta"]["clubs_discount_p"] ) {

                // Amount of subtraction according percent
                $result = $price * $percent / 100;

                // Deducts the discount from the base price
                $price = $price - $result;

                return $price;
            }
        }

        return $price;
    }

    /**
     * Not allow the current user access to products from other clubs
     *
     */
    public function notProductsFromAnotherClub()
    {
        $is_closed_club = \LW\Settings::is_closed_club();

        if($is_closed_club){
            return;
        }

        if (!current_user_can('administrator') && is_user_logged_in() && !is_admin() && !is_page()) {

            $post_id = get_the_ID();

            $terms = wp_get_post_terms($post_id, 'clubs');

            foreach ($terms as $vterm) {
                $post_terms[] = $vterm->term_id;
            }

            if ($post_terms) {
                $term_id = \LW\Settings::currentUser();
                $in_the_club = in_array($term_id['club_taxonomy']->term_id, $post_terms);
                if (!$in_the_club && !is_admin()) {
                    $shop_page_id = get_option('woocommerce_shop_page_id');
                    $post = get_post($shop_page_id);
                    $slug = $post->post_name;
                    wp_redirect(home_url($slug));
                    exit;
                }
            }
        }
    }

    /**
     * Add club-manager to woocommerce role for access to admin dashboard
     *
     * @param $prevent_access
     * @return bool
     */
    public function allowAdminForClubManagers($prevent_access)
    {
        if (current_user_can('club_manager_options')) {
            $prevent_access = false;
        }

        return $prevent_access;
    }

    /**
     * Force woocommerce to allow access for manager to admin bar
     * @param $prevent_admin_bar
     * @return bool
     */
    function allowAdminBarForClubManagers($prevent_admin_bar)
    {
        if (current_user_can('club_manager_options')) {
            $prevent_admin_bar = false;
        }
        return $prevent_admin_bar;
    }

    /**
     * Show product in shop by user role
     * @param $q
     * @return mixed
     */
    public function wooQueryByTaxonomyClubs($q)
    {
        $taxonomy = \LW\Settings::currentUser();

        if (current_user_can('administrator')) {
            return $q;
        }

        $dclub = get_terms('clubs', array('fields' => 'id=>slug'));
        foreach ($dclub as $k => $slug) {
            if (get_term_meta($k, 'default_club', true)) {
                $default = $slug;
            }
        }

        if ( ! is_user_logged_in() ) {
            $q->set('tax_query', array(
                    array('taxonomy' => 'clubs',
                        'field' => 'slug',
                        'terms' => $default
                    )
                )
            );
            return $q;
        }

        if ( $taxonomy["club_term_meta"]["clubs_active"] == 'on' &&  self::clubsDateCloseOpenOrder() === 0 ) {
            if (current_user_can('club_manager_options')) {
                $tax_query = $q->get('tax_query');
                $tax_query[] = array(
                    'taxonomy' => $taxonomy['club_taxonomy']->taxonomy,
                    'field' => 'slug',
                    'terms' => $taxonomy['club_taxonomy']->slug
                );

                $q->set('tax_query', $tax_query);
                return $q;
            }
            if (!current_user_can('club_manager_options') && is_user_logged_in() && !$taxonomy["club_term_meta"]["default_club"]) {

                $tax_query = $q->get('tax_query');
                $tax_query[] = array(
                    'taxonomy' => $taxonomy['club_taxonomy']->taxonomy,
                    'field' => 'slug',
                    'terms' => $taxonomy['club_taxonomy']->slug
                );

                $q->set('tax_query', $tax_query);
            } elseif ($taxonomy["club_term_meta"]["default_club"]) {
                $q->set('tax_query', array(
                        array('taxonomy' => 'clubs',
                            'field' => 'slug',
                            'terms' => $taxonomy['club_taxonomy']->slug
                        )
                    )
                );
            }
        } else {
            $q->set('tax_query', array(
                array('taxonomy' => 'clubs',
                    'field' => 'slug',
                    'terms' => $default,
                )));
        }
    }

    /**
     * Override original woocommerce template
     *
     * @param $template
     * @param $template_name
     * @param $template_path
     * @return string
     */
    public function lummiWooLocateTemplate( $template, $template_name, $template_path ) {
        global $woocommerce;

        $_template = $template;

        if ( ! $template_path ) $template_path = $woocommerce->template_url;

        $plugin_path  = LW_PLUGIN_DIR . '/woocommerce/lummi_woo_template/';

        $template = locate_template(

            array(
                $template_path . $template_name,
                $template_name
            )
        );
        if ( ! $template && file_exists( $plugin_path . $template_name ) )

            $template = $plugin_path . $template_name;

        if ( ! $template )

            $template = $_template;

        return $template;
    }

}