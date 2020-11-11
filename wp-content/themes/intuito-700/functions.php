<?php


add_action( 'after_setup_theme', 'woocommerce_support');
function woocommerce_support() {
	add_theme_support( 'woocommerce');
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}


function intuito_post_meta() { $a = 2 +4; }


if( function_exists('acf_add_options_page') ) {
	acf_add_options_page();	
}

//add_action('admin_init', 'liw_add_portfolio_page_attributes');
//function liw_add_portfolio_page_attributes(){
//    add_post_type_support( 'product', 'page-attributes' );
//}

// Add sidebar to WP widgets 
add_action( 'widgets_init', 'reg_sidebars' );
function reg_sidebars() {
	register_sidebar(array('name'=>'Products Sidebar',
	                       'id' => 'sidebar-in-category',
	));
	register_sidebar(array('name'=>'Our Seafood',
	));
}


// Thumbnails
if ( function_exists( 'add_theme_support' ) ) :
	add_theme_support( 'post-thumbnails' );
endif;


// Menu locations
add_action( 'init', 'custom_navigation_menus' );


function custom_navigation_menus() {
	register_nav_menus(array(
		'primary' => 'Primary Menu',
		'sidebar' => 'Sidebar Menu',
		'footer_one' => 'Footer One',
		'footer_two' => 'Footer Two',
		'footer_three' => 'Footer Three',
		'footer_four' => 'Footer Four',
		'footer_five' => 'Footer Five',
		'footer_six' => 'Footer Six',
        'dev' => 'Dev',
        'mobile' => 'Mobile'
	));
}

//do_shortcode('[product_table show_quantity="true"]');

// Customization of the admin ----------------------------------------------------------------------------------------- -->
add_action( 'woocommerce_after_shop_loop_item_title', 'wcs_stock_text_shop_page', 25 );
function wcs_stock_text_shop_page() {
    //returns an array with 2 items availability and class for CSS
    global $product;
    $availability = $product->get_availability();
    if ( $availability['availability'] == 'Out of stock') {
        echo apply_filters( 'woocommerce_stock_html', '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>', $availability['availability'] );
    }
}


// woocommerce_template_loop_product_link_open

add_action( 'woocommerce_after_shop_loop_item', 'as_category_items_add_to_cart', 10 );
function as_category_items_add_to_cart() {
    //returns an array with 2 items availability and class for CSS
    // global $product;
    // $availability = $product->get_availability();
    // if ( $availability['availability'] == 'Out of stock') {
    //     echo apply_filters( 'woocommerce_stock_html', '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>', $availability['availability'] );
	// }

	global $product;
	
		echo   '<a href="javascript:void(0);" onclick="as_add_product_to_cart('.$product->id.');" >Add to Cart</a>';

}



// Check if a club is open
function club_status($club_id) {
	$club_close_date = get_term_meta($club_id, 'clubs_close_date', true);
	$club_close_date = str_replace('-', '/', $club_close_date);
	$club_close_date = strtotime($club_close_date);
	$now = time();
	if ( $club_close_date > $now ) {
		return 'open';
	} else {
		return 'closed';
	}				
}


/**
 * Add a custom product data tab
 */
$this_user = get_current_user_id();
$user_club = get_user_meta($this_user, 'user_club', 'true');
if ( ( $user_club == '37' ) OR ( $user_club == '' ) OR ( $club_status == 'closed' ) ) {
add_filter( 'woocommerce_product_tabs', 'woo_new_product_tab' );
}

function woo_new_product_tab( $tabs ) {
	
	// Adds the new tab
	
	$tabs['test_tab'] = array(
		'title' 	=> __( 'Delivery FAQs', 'woocommerce' ),
		'priority' 	=> 50,
		'callback' 	=> 'woo_new_product_tab_content'
	);

	return $tabs;

}
function woo_new_product_tab_content() {

	// The new tab content

	echo '<h2>Delivery FAQs</h2>';
	the_field('shipping_faqs', 'option');
	
}

/**
 * Rename product data tabs
 */
add_filter( 'woocommerce_product_tabs', 'woo_rename_tabs', 98 );
function woo_rename_tabs( $tabs ) {

	$tabs['description']['title'] = __( 'Details' );		// Rename the description tab

	return $tabs;

}



add_shortcode('video','video_embed');
function video_embed($atts) {
    extract( shortcode_atts(  array(
        'code' => '',
        'height' => '',
        'width' => ''
        ), $atts ) );
        if ( $height == '' ) { $height = '315'; }
        if ( $width == '' ) { $width = '560'; }
    return '<div class="video-container"><iframe width="'.$width.'" height="'.$height.'" src="https://www.youtube.com/embed/'.$code.'?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe></div>';
}


/* -- Custom Logo ------------------------------------------------ --> */
add_action('login_head', 'login_css');

function login_css() {
	wp_enqueue_style( 'login_css', get_template_directory_uri() . '/style-admin.css' );
}

// Custom URL for the login logo

add_filter( 'login_headerurl', 'custom_loginlogo_url' );
function custom_loginlogo_url($url) {
    return 'http://www.intuitowebsites.com';
}

/* -- Custom admin css ------------------------------------------------ --> */
add_action('admin_print_styles', 'admin_css' );

function admin_css() {
	wp_enqueue_style( 'admin_css', get_template_directory_uri() . '/style-admin.css' );
}

/* -- Custom Footer------------------------------------------------ --> */
function remove_footer_admin () {
	echo '&copy; 2012 - Thank you for using <a href="http://www.intuitowebsites.com" target="_blank">Intuito Websites</a>';
}
add_filter('admin_footer_text', 'remove_footer_admin');

/* -- Custom dashboard widgets ------------------------------------------------ --> */
function wpc_dashboard_widget_function() {
	echo "<h2>Intuito Websites</h2>
			<p>Phone Support: 360-595-7287<br />
			email: support@intuitowebsites.com<br /></p>";
}
function wpc_add_dashboard_widgets() {
	wp_add_dashboard_widget('wp_dashboard_widget', 'Technical information', 'wpc_dashboard_widget_function');
}
add_action('wp_dashboard_setup', 'wpc_add_dashboard_widgets' );

/* -- Hide Dashboard Widgets ------------------------------------------------ --> */
add_action('wp_dashboard_setup', 'wpc_dashboard_widgets');
function wpc_dashboard_widgets() {
	global $wp_meta_boxes;
	// Today widget
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
	// Last comments
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
	// Incoming links
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
	// Plugins
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
}

//Delete [...] from excert
add_filter('excerpt_more', 'new_excerpt_more');
function new_excerpt_more( $more ) {
     return '';
}

// change the extract length
function custom_excerpt_length( $length ) {
     return 50;
}

add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

add_shortcode ('quote', 'intuito_quote');
function intuito_quote( $atts ) {
	extract( shortcode_atts( array(
        'text' => '',
        'source' => ''
        ), $atts ) );
		$q = '<div class=quote-container>';
		$q .= '<div class=quotation-mark><img src="' . get_bloginfo('template_directory') . '/images/quote.png"></div>';
		$q .= '<p class=the-quote>' . $text . '</p>';
		$q .= '<p class=the-source>— ' . $source . '</p>';
		$q .= '</div>';
	return $q;
}

/*
 * Register frontend javascripts
 */
if(!function_exists('intuito_register_frontend_scripts'))
{
    if(!is_admin()){
    	add_action('wp_enqueue_scripts', 'intuito_register_frontend_scripts');
    }

	function intuito_register_frontend_scripts()
	{
		$template_url = get_template_directory_uri();

        // custom functions
    	wp_register_script('intuito-jquery-functions', $template_url.'/js/jquery-functions.js', array('jquery'), 1, false);
    	//wp_register_script('cycle', $template_url.'/js/jquery.cycle2.min.js', array('jquery'), 1, false);

    	wp_enqueue_script('jquery');
    	wp_enqueue_script('intuito-jquery-functions');
    	//wp_enqueue_script('cycle');
    }
}

add_action('wp_enqueue_scripts','load_intuito_styles',9);
function load_intuito_styles(){
	wp_enqueue_style('intuito-style-temp',get_template_directory_uri().'/style-temp.css');
	wp_enqueue_style('intuito-style',get_template_directory_uri().'/style.css','','4.9.6');
	wp_enqueue_style('intuito-lightbox',get_template_directory_uri().'/css/lightbox.css');
	// cart_popup_style cart_popup_style
	wp_enqueue_style('cart_popup_style',get_template_directory_uri().'/css/cart_popup_style.css','',rand(99,9999));
	//wp_enqueue_style('intuito-font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
}


add_action('admin_enqueue_scripts', 'backend_js_customizations');
function backend_js_customizations() {
    wp_enqueue_script('admin_js', get_bloginfo('template_directory').'/js/admin-custom.js');
}


/* Woocommerce */

/**
* woocommerce_single_product_summary hook
*
* @hooked woocommerce_template_single_title - 5
* @hooked woocommerce_template_single_price - 10
* @hooked woocommerce_template_single_excerpt - 20
* @hooked woocommerce_template_single_add_to_cart - 30
* @hooked woocommerce_template_single_meta - 40
* @hooked woocommerce_template_single_sharing - 50
*/

//remove_action( 'my_action_title', 'my_custom_function', priority, number_of_accepted_arguments );
//add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 60 );

add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

function woo_remove_product_tabs( $tabs ) {

    //unset( $tabs['reviews'] ); 			// Remove the reviews tab
    unset( $tabs['additional_information'] );  	// Remove the additional information tab

    return $tabs;

}

/*
function wc_remove_related_products( $args ) {
	return array();
}
add_filter('woocommerce_related_products_args','wc_remove_related_products', 10);
*/


$user = wp_get_current_user();
if ( in_array( 'club-manager', (array) $user->roles ) ) {

	function dashboard_redirect(){
		wp_redirect(admin_url('admin.php?page=manage-club-home'));
	}
	add_action('load-index.php','dashboard_redirect');

	function custom_menu_page_removing() {
		remove_menu_page( 'index.php' );
		remove_menu_page( 'upload.php' );
	}
	add_action( 'admin_menu', 'custom_menu_page_removing' );


	add_action('admin_footer-profile.php', 'remove_profile_fields');
	function remove_profile_fields()
	{
		 ?>
			<script type="text/javascript">
				jQuery("h2:contains('Personal Options')").next('.form-table').remove();
				jQuery("h2:contains('Personal Options')").remove();
				jQuery("h2:contains('About Yourself')").next('.form-table').remove();
				jQuery("h2:contains('About Yourself')").remove();
				//jQuery(".user-nickname-wrap").remove();
				jQuery(".user-nickname-wrap").css('display','none');
			</script>
	<?php
	}

	add_action('admin_head', 'hide_shipping_price_for_manager');
	function hide_shipping_price_for_manager() {
		$output = "
			<style>
				.settings-club-ship-price-field {display:none;}
				.lw-orders-container tr td:last-child { width:40%; }
			</style>";
		echo $output;
	}

	add_filter( 'gettext', 'wpse6096_gettext', 10, 2 );
	function wpse6096_gettext( $translation, $original )
	{
		if ( 'Website' == $original ) {
			return 'Link your Club Facebook Page';
		}
		//if ( 'Biographical Info' == $original ) {
		//	return 'Resume';
		//}
		return $translation;
	}


}

$user = wp_get_current_user();
$user_id = get_current_user_id();
$user_club = get_user_meta($user_id, 'user_club', true);
if ( ( $user_club == '37' ) OR ( $user_club == '' ) OR ( club_status($user_club) == 'closed' ) ) {
	add_filter( 'woocommerce_checkout_fields' , 'wpi_additional_checkout_fields' );
	add_action( 'woocommerce_checkout_process', 'wpi_custom_fields_validation');
	add_action( 'woocommerce_checkout_update_order_meta', 'wpi_custom_checkout_field_update_order_meta' );
	add_action( 'woocommerce_admin_order_data_after_billing_address','wpi_custom_checkout_field_display_admin_order_meta', 10, 1 );
}

function wpi_additional_checkout_fields( $fields ) {

	$date_1 = get_field('date_1','option');
	$date_2 = get_field('date_2','option');
	$date_3 = get_field('date_3','option');
	$date_4 = get_field('date_4','option');

    $fields['billing']['selected_shipping'] = array(
    'label'     => __('Shipping Date', 'woocommerce'),
    'type' => 'select',
    'required'  => TRUE,
    'class'     => array('form-row-wide','address-field','update_totals_on_change'),
    'clear'     => true,
    'options' => array(
        '' => _('Please Select a Shipping Date...'),
        $date_1 => $date_1,
		$date_2 => $date_2,
		$date_3 => $date_3,
		$date_4 => $date_4
        )
    );
	return $fields;
}

function wpi_custom_fields_validation() {
        if ( ! $_POST['selected_shipping'] )
            wc_add_notice( __( 'Please select your shipping date.' ), 'error' );
    }

function wpi_custom_checkout_field_update_order_meta( $order_id ) {
    if ( !empty( $_POST['selected_shipping'] ) ) {
        update_post_meta( $order_id, 'selected_shipping_date', sanitize_text_field( $_POST['selected_shipping'] ) );
        }
    }

function wpi_custom_checkout_field_display_admin_order_meta($order){
    echo '<p><strong>'.__('Selected Shipping Date').':</strong> ' . get_post_meta( $order->id, 'selected_shipping_date', true ) . '</p>';
    }

// Pass custom data to shipstation
add_filter( 'woocommerce_shipstation_export_custom_field_2', 'shipstation_custom_field_2' );
function shipstation_custom_field_2() {
	return 'selected_shipping_date'; // Replace this with the key of your custom field
}

/* This is for custom field 3
add_filter( 'woocommerce_shipstation_export_custom_field_3', 'shipstation_custom_field_3' );
function shipstation_custom_field_3() {
	return '_meta_key_2'; // Replace this with the key of your custom field
}
*/




/**
 * Get manager data
 *
 * @param bool $all - if $all equal to true: functions return all manager data, else only manager url (Default: false)
 * @return false|WP_User
 */
function getManagerData($all = false){

    $user = wp_get_current_user();
    $user_terms = get_user_meta($user->ID);
    $the_club = get_term_meta($user_terms["user_club"][0]);
    $user_manager = get_userdata($the_club["clubs_manager_id"][0]);

    $content = $user_manager->data->user_url;
    if(!$all){
        return $content;
    }
    return $user_manager;
}

/*
$user = wp_get_current_user();
if ( in_array( 'club-member', (array)$user->roles ) ) {
    function myscript() {
        $content = getManagerData();
        echo '<script type="text/javascript">
                jQuery(document).ready(function($){
                    var content = "Club admin url: <a href='.$content.'>'.$content.'</a>";
                    $( ".woocommerce" ).append( "<p class=club-admin-url>"+ content +"</p>" );
                });
              </script>';
    }
    add_action('wp_footer', 'myscript');

}
*/

$user = wp_get_current_user();

if ( (in_array( 'club-manager', (array) $user->roles ) ) OR ( (in_array( 'club-member', (array) $user->roles ) ) ) ) {

	add_action('wp_head', 'hide_shipping_options_for_club_checkout');
	function hide_shipping_options_for_club_checkout() {

		$clubuser = \LW\Settings::currentUser();
		$is_default_club = get_term_meta($clubuser["user_meta"]["user_club"]);

		$output = '';

		if( ! $is_default_club["default_club"][0] ){
			$output = "<style>
						/*.woocommerce-shipping-fields,
						.shipping, 
						.addresses .col-2
						{display:none;} */
						.shipping .woocommerce-shipping-methods ul li:nth-child(2) { display:none; }
						#shipping_method_0_local_pickup5,
						#shipping_method_0_clubsshipping,
						#shipping_method_0_local_pickup5 ~ label {display:none!important;}
						.woocommerce ul#shipping_method li { margin-bottom:0; }
						.shop_table .fee { display:table-row; }
					</style>";
        }
        elseif( ! $is_default_club["default_club"][0] && $is_default_club["clubs_active"][0] === 'off' ){
	        $output = '<style>
						.cart_totals.calculated_shipping .shipping,.addresses .col-2{display: table-row !important}
						.club-member tr.shipping{display: table-row !important;}
					</style>';
        }
        else{
			$output = '<style>
						.cart_totals.calculated_shipping .shipping,.addresses .col-2{display: table-row !important}
						.club-member tr.shipping{display: table-row !important;}
					</style>';
        }

		echo $output;
	}

	/*
	add_filter( 'woocommerce_billing_fields', 'wc_npr_filter_phone', 10, 1 );
	function wc_npr_filter_phone( $address_fields ) {
		$address_fields['billing_phone']['required'] = false;
		$address_fields['billing_email']['required'] = false;
		return $address_fields;
	}
	*/

}

function show_wccart() {
	global $woocommerce;
	if ( sizeof( $woocommerce->cart->cart_contents) > 0 ) :
		echo '
		<div class=checkout-link>
			<a href="' . $woocommerce->cart->get_cart_url() . '" title="' . __( 'Cart' ) . '"><i style=color:white class="fa fa-shopping-cart" aria-hidden="true"></i> ';
				echo sprintf ( _n( '%d item', '%d items', WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() );
				echo ' - ' . WC()->cart->get_cart_total() . '
			</a>
		</div>';
	endif;
}


add_action('wp_login', 'clear_wccart', 10, 2);
function clear_wccart($user_login, $user) {
	$user = wp_get_current_user();
	if (in_array( 'club-member', (array) $user->roles )) {
		global $woocommerce;
		$woocommerce->cart->empty_cart();
	}
}


add_action( 'wp_logout', 'kia_destroy_persistent_cart' );
function kia_destroy_persistent_cart(){
    if(function_exists('wc_empty_cart')){
        wc_empty_cart();
    }
}

add_action( 'init', 'create_programs_post_type' );
function create_programs_post_type() {
	register_post_type( 'programs',
		array(
			'labels' => array(
			'name' => 'Programs',
			'singular_name' => 'Program',
			'add_new' => 'Add Program',
			'add_new_item' => 'Add Program',
			'edit_item' => 'Edit Program',
			'new_item' => 'New Program',
			'all_items' => 'All Programs',
			'view_item' => 'View Program',
			'search_items' => 'Search Programs',
			'not_found' => 'No Programs found',
			'not_found_in_trash' => 'No Programs found in Trash',
			'parent_item_colon' => '',
			'menu_name' => 'Programs'
		),
		'public' => true,
		'has_archive' => true,
		'supports' => array( 'title', 'editor', 'thumbnail' )
		)
	);
}

function get_user_role( $user = null ) {
	$user = $user ? new WP_User( $user ) : wp_get_current_user();
	return $user->roles ? $user->roles[0] : false;
}

function woo_in_cart($product_id) {
    global $woocommerce;

    foreach($woocommerce->cart->get_cart() as $key => $val ) {
        $_product = $val['data'];

        if($product_id == $_product->id ) {
            return true;
        }
    }

    return false;
}


add_filter( 'woocommerce_gallery_thumbnail_size', function( $size ) {
return array( '160', '160' );
} );


add_filter('woocommerce_package_rates', 'wf_hide_shipping_method_based_on_shipping_class', 10, 2);
function wf_hide_shipping_method_based_on_shipping_class($available_shipping_methods, $package)
{
    
    $hide_when_shipping_class_exist = array(
        158 => array(
            'fedex:GROUND_HOME_DELIVERY'
        )
    );
    
    /*
    $hide_when_shipping_class_not_exist = array(
        158 => array(
            'fedex:GROUND_HOME_DELIVERY'
        )
    );
    */
    
    $shipping_class_in_cart = array();
    foreach(WC()->cart->cart_contents as $key => $values) {
       $shipping_class_in_cart[] = $values['data']->get_shipping_class_id();
    }

    foreach($hide_when_shipping_class_exist as $class_id => $methods) {
        if(in_array($class_id, $shipping_class_in_cart)){
            foreach($methods as & $current_method) {
                unset($available_shipping_methods[$current_method]);
            }
        }
    }
/*
    foreach($hide_when_shipping_class_not_exist as $class_id => $methods) {
        if(!in_array($class_id, $shipping_class_in_cart)){
            foreach($methods as & $current_method) {
                unset($available_shipping_methods[$current_method]);
            }
        }
    }
*/
    return $available_shipping_methods;
}

add_filter( 'loop_shop_per_page', 'new_loop_shop_per_page', 20 );
function new_loop_shop_per_page( $cols ) {
// $cols contains the current number of products per page based on the value stored on Options -> Reading
// Return the number of products you wanna show per page.
$cols = 50;
return $cols;
}

add_filter( 'automatewoo_email_templates', 'my_automatewoo_email_templates' );
function my_automatewoo_email_templates( $templates ) {
    
	// SIMPLE
	// register a template by adding a slug and name to the $templates array
	$templates['liw'] = 'LIW Template';
	// ADVANCED
	// you can also create a template with a unique from name and from email by passing using the following array format 
	/*
	$templates['custom-2'] = array(
		'template_name' => 'Custom Template #2',
		'from_name' => 'AutomateWoo Custom',
		'from_email' => 'custom@automatewoo.com'
	);
	*/
	return $templates;
}

add_action( 'admin_bar_menu', 'remove_admin_items', 999 );
function remove_admin_items( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'wp-admin-bar-abovethefold' );
}

add_action( 'woocommerce_thankyou', 'custom_thankyou_text', 1, 0);
function custom_thankyou_text(){
    echo '<p class="thankyou-custom-text">Your order was successfull! Thank you for your purchase.</p>';
}

function get_product_category_comments($category_id) {

	$pages = get_posts(array(
		'post_type' => 'product',
		'numberposts' => -1,
		'tax_query' => 
			array(
				array(
					'taxonomy' => 'product_cat',
					'field' => 'id',
					'terms' => $category_id 
				)
			)
		)						  
	);

	$ids = array();
	foreach ( $pages as $page ) {
		$ids[] = $page->ID;
	}

	echo "<div class='category-comments-container'>";
	$counter = 1;
	foreach ( $ids as $pid ) {
		$args = array( 
			'number'      => -1, 
			'status'      => 'approve', 
			'post_status' => 'publish', 
			'post_type'   => 'product',
			'post_id'     => $pid			
		);

		$comments = get_comments( $args );
		foreach ( $comments as $comment ) {
			if ( $counter < 4 ) {
				if ( $comment->comment_content != '' ) {		
					$phrase = $comment->comment_content;
					$max_words = 25;
					$phrase_array = explode(' ',$phrase);
					if(count($phrase_array) > $max_words && $max_words > 0)
						$phrase = implode(' ',array_slice($phrase_array, 0, $max_words)).'...';
					$url = get_bloginfo('template_directory');
					echo "<div class='single-comment'>" . $phrase . " ~ <strong>" . $comment->comment_author . "</strong><img src='".$url."/images/five-stars.png' /></div>";
				}
			}
			$counter++;
		}
	}
	echo "</div>";
}


add_filter( 'woocommerce_product_review_list_args', 'reverse_review_order', 999 );
function reverse_review_order( $x ){ $x['reverse_top_level'] = true; return $x; }



/**
 * Show a message at the cart/checkout displaying
 * how much to go for free shipping.
 */
function my_custom_wc_free_shipping_notice($user_club) {

	if ( ! is_cart() && ! is_checkout() ) { // Remove partial if you don't want to show it on cart/checkout
		return;
	}
	$order_received = is_wc_endpoint_url( 'order-received' );
	$packages = WC()->cart->get_shipping_packages();
	$package = reset( $packages );
	$zone = wc_get_shipping_zone( $package );
	//user_club = get_user_meta($user->ID, 'user_club', 'true');

	$cart_total = WC()->cart->get_displayed_subtotal();
	if ( WC()->cart->display_prices_including_tax() ) {
		$cart_total = round( $cart_total - ( WC()->cart->get_discount_total() + WC()->cart->get_discount_tax() ), wc_get_price_decimals() );
	} else {
		$cart_total = round( $cart_total - WC()->cart->get_discount_total(), wc_get_price_decimals() );
	}
	foreach ( $zone->get_shipping_methods( true ) as $k => $method ) {
		$min_amount = $method->get_option( 'min_amount' );

		if ( $method->id == 'free_shipping' && ! empty( $min_amount ) && $cart_total < $min_amount ) {
			$remaining = $min_amount - $cart_total;
			if ( ($user_club == '' ) OR ($user_club == '37' ) ) { // Only show to public club or non-logged in users ($user_club == '37') OR ($user_club == '') 
				if ( (!$order_received) AND ( $remaining > 1 ) ) { // Don't show on the order received page and don't show if the cart has more than free shipping threshhold
					wc_add_notice( sprintf( '<span id="free-shipping-diff">Want free shipping? Add %s to your cart.</span>', wc_price( $remaining ) ), 'notice' );
				}
			}
		}
	}

}
//add_action( 'wp', 'my_custom_wc_free_shipping_notice' );

/**
 * @snippet       Disable Free Shipping if Cart has Shipping Class (WooCommerce 2.6+)
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @testedwith    WooCommerce 4.0
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */
  //https://businessbloomer.com/woocommerce-disable-free-shipping-if-cart-has-shipping-class/
add_filter( 'woocommerce_package_rates', 'hide_ground_on_perishable', 10, 2 );
   
function hide_ground_on_perishable( $rates, $package ) {
   $shipping_class_target = 158; // shipping class ID (to find it, see screenshot below)
   $in_cart = false;
   foreach ( WC()->cart->get_cart_contents() as $key => $values ) {
      if ( $values[ 'data' ]->get_shipping_class_id() == $shipping_class_target ) {
         $in_cart = true;
         break;
      } 
   }
   if ( $in_cart ) {
      unset( $rates['fedex:2:GROUND_HOME_DELIVERY'] ); // shipping method with ID (to find it, see screenshot below)
   }
   return $rates;
}





// rename the "Have a Coupon?" message on the checkout page
function woocommerce_rename_coupon_message_on_checkout() {

	return 'Have a Coupon Code?' . ' <a href="#" class="showcoupon">' . __( 'Add', 'woocommerce' ) . '</a>';
}
add_filter( 'woocommerce_checkout_coupon_message', 'woocommerce_rename_coupon_message_on_checkout' );

// rename the coupon field on the checkout page
function woocommerce_rename_coupon_field_on_checkout( $translated_text, $text, $text_domain ) {

	// bail if not modifying frontend woocommerce text
	if ( is_admin() || 'woocommerce' !== $text_domain ) {
		return $translated_text;
	}

	if ( 'Coupon code' === $text ) {
		$translated_text = 'Promo Code';
	
	} elseif ( 'Apply Coupon' === $text ) {
		$translated_text = 'Apply Promo Code';
	}

	return $translated_text;
}
add_filter( 'gettext', 'woocommerce_rename_coupon_field_on_checkout', 10, 3 );
/*
* Reduce the strength requirement for woocommerce registration password.
* Strength Settings:
* 0 = Nothing = Anything
* 1 = Weak
* 2 = Medium
* 3 = Strong (default)
*/

add_filter( 'woocommerce_min_password_strength', 'wpglorify_woocommerce_password_filter', 10 );
function wpglorify_woocommerce_password_filter() {
return 2; }

// Hide out of stock products from the main product category query
// We do this in stead of using the WC control under settings - Products - Inventory
// inorder to have two lists on the category page and also use the waitlist

add_action( 'pre_get_posts', 'iconic_hide_out_of_stock_products' );
function iconic_hide_out_of_stock_products( $q ) {
    if ( ! $q->is_main_query() || is_admin() ) {
        return;
    }
    if ( $outofstock_term = get_term_by( 'name', 'outofstock', 'product_visibility' ) ) {
        $tax_query = (array) $q->get('tax_query');
        $tax_query[] = array(
            'taxonomy' => 'product_visibility',
            'field' => 'term_taxonomy_id',
            'terms' => array( $outofstock_term->term_taxonomy_id ),
            'operator' => 'NOT IN'
        );
        $q->set( 'tax_query', $tax_query );
    }
    remove_action( 'pre_get_posts', 'iconic_hide_out_of_stock_products' );
}



///---------------	 Rated Work ----------------------------

///---------------	 Get Cart Items ----------------------------
function as_get_cart_data(){
	global $woocommerce;
	$items 			=	$woocommerce->cart->get_cart();
	$resArr 		= [];

	$perishable_Subtotal = 0;
	$non_perishable_Subtotal = 0;

		foreach ( $items as $cart_item ) {
			$row['key'] = $cart_item['key'];
			$row['price'] 			= $cart_item['data']->get_price();
			$row['quantity'] 		= $cart_item['quantity'];
			$row['line_total'] 		= $cart_item['line_total'];
			$row['title'] 			= $cart_item['data']->get_title();
			$row['product_detail'] 	= $cart_item;
			$row['image'] 			= $cart_item['data']->get_image('thumbnail');
			$row['others']				= wc_get_product($cart_item['product_id']);
			$perishable_Subtotal 		+= $row['price'];
			$non_perishable_Subtotal  	+= $row['price'];
			$row['key'] 			= $cart_item['key'];
			$row['product_id'] 		= $cart_item['product_id'];

			$resArr[] = $row;
		} 

		$perishable_Subtotal = WC()->cart->cart_contents_total;
		$non_perishable_Subtotal = WC()->cart->cart_contents_total;

	$total = array('perishable_Subtotal' => $perishable_Subtotal, 'non_perishable_Subtotal' => $non_perishable_Subtotal, 'total_cart' => $non_perishable_Subtotal);
	return array('total' => $total, 'items' => $resArr, 'items_quan_count' => WC()->cart->get_cart_contents_count() );
}


///---------------	 Add to cart ----------------------------
add_action( 'wp_ajax_as_add_to_cart', 'prefix_ajax_as_add_to_cart' );
add_action( 'wp_ajax_nopriv_as_add_to_cart', 'prefix_ajax_as_add_to_cart' );

function prefix_ajax_as_add_to_cart() {

	if(!isset($_POST['product_id'])){
		wp_send_json(array('status' => false, 'message'=> 'Product not found!'));
	}

	$product_id  = intval( $_POST['product_id'] );

	global $woocommerce;
	$woocommerce->cart->add_to_cart($product_id);

	$data = as_get_cart_data();
	wp_send_json(array('status' => true, 'message'=> 'Done', 'data' => $data ));

}





///---------------	 Add to cart ----------------------------
add_action( 'wp_ajax_as_update_cart_quantity', 'prefix_ajax_as_update_cart_quantity' );
add_action( 'wp_ajax_nopriv_as_update_cart_quantity', 'prefix_ajax_as_update_cart_quantity' );

function prefix_ajax_as_update_cart_quantity() {

	if(!isset($_POST['product_id'])){
		wp_send_json(array('status' => false, 'message'=> 'Product not found!'));
	}

	if(!isset($_POST['key'])){
		wp_send_json(array('status' => false, 'message'=> 'Product Key not found!'));
	}

	if(!isset($_POST['quan'])){
		wp_send_json(array('status' => false, 'message'=> 'Quantity not found!'));
	}

	$product_id   = intval( $_POST['product_id'] );
	$product_key  = $_POST['key'];
	$quan  		  = intval( $_POST['quan'] );

	global $woocommerce;
	$woocommerce->cart->set_quantity($product_key, $quan);

	$data = as_get_cart_data();
	wp_send_json(array('status' => true, 'message'=> 'Done', 'data' => $data ));

}

add_action( 'wp_ajax_as_get_cart_items', 'prefix_ajax_as_get_cart_items' );
add_action( 'wp_ajax_nopriv_as_get_cart_items', 'prefix_ajax_as_get_cart_items' );

function prefix_ajax_as_get_cart_items() {
	$data = as_get_cart_data();
	wp_send_json(array('status' => true, 'message'=> 'Done', 'data' => $data ));
}

///---------------------------------------------------------------

// add_action('template_redirect', 'woocommerce_custom_redirections');
// function woocommerce_custom_redirections() {
//     // Case1: Non logged user on checkout page (cart empty or not empty)
// 	if ( !is_user_logged_in() && is_checkout() )
// //	echo get_permalink( get_option('woocommerce_myaccount_page_id') ); exit;
//         wp_redirect( get_permalink( get_option('woocommerce_myaccount_page_id') ) );

//     // Case2: Logged user on my account page with something in cart
//     // if( is_user_logged_in() && ! WC()->cart->is_empty() && is_account_page() )
//     //     wp_redirect( get_permalink( get_option('woocommerce_checkout_page_id') ) );
// }


//----------------------------------------------------------------------------

/**
     * Rendering login form by template and set attributes
     *
     * @param $attributes
     * @param null $content
     * @return string|void
     */
    // public function renderLoginForm( $attributes, $content = null ) {

    //     $default_attributes = array( 'show_title' => false );
    //     $attributes = shortcode_atts( $default_attributes, $attributes );
    //     $show_title = $attributes['show_title'];

    //     if ( is_user_logged_in() ) {
    //         return __( 'You are already signed in.', 'lummi-wild-se' );
    //     }

    //     $attributes['redirect'] = '';
    //     if ( isset( $_REQUEST['redirect_to'] ) ) {
    //         $attributes['redirect'] = wp_validate_redirect( $_REQUEST['redirect_to'], $attributes['redirect'] );
    //     }

    //     // Error messages
    //     $errors = array();
    //     if ( isset( $_REQUEST['login'] ) ) {
    //         $error_codes = explode( ',', $_REQUEST['login'] );

    //         foreach ( $error_codes as $code ) {
    //             $errors[] = $this->getErrorMessage( $code );
    //         }
    //     }

    //     $attributes['errors'] = $errors;
    //     $attributes['logged_out'] = isset( $_REQUEST['logged_out'] ) && $_REQUEST['logged_out'] == true;
    //     $attributes['registered'] = isset( $_REQUEST['registered'] );
    //     $attributes['lost_password_sent'] = isset( $_REQUEST['checkemail'] ) && $_REQUEST['checkemail'] == 'confirm';
    //     $attributes['password_updated'] = isset( $_REQUEST['password'] ) && $_REQUEST['password'] == 'changed';

    //     return LW_Template::getTemplate( 'client_templates','login_form', $attributes, false);
    // }




	add_shortcode('login_for_checkout_page_s_code', 'login_for_checkout_page');
	function login_for_checkout_page( $atts = array() ) {
  //return '';
  
 
  // set up default parameters
		// extract(shortcode_atts(array(
		//  'rating' => '5'
		// ), $atts));
		
		// return "<img src=\"http://dayoftheindie.com/wp-content/uploads/$rating-star.png\" 
		// alt=\"doti-rating\" width=\"130\" height=\"188\" class=\"left-align\" />";

		// if ( ! $attributes ) {
        //     $attributes = array();
        // }

       // ob_start();
        
        // do_action( 'lummi_wild_before_' . $template_name );

        // require( LW_PLUGIN_DIR. $template_folder. DIRECTORY_SEPARATOR . $template_name . '.php');


		// require('./2020_work/login_form.php');


        // // do_action( 'lummi_wild_after_' . $template_name );

        //  $html = ob_get_contents();
		//  ob_end_clean();
		



		ob_start();
		require('./2020_work/login_form.php');
	return ob_get_clean();
	
	// ob_start();
    // get_template_part('my_form_template');
    // return ob_get_clean(); 


		//  return $html;

        // if($echo === false){
        //     return $html;
        // }
        // echo $html;





	}


