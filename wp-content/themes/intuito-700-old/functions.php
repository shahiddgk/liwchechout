<?php

function intuito_post_meta() { $a = 2 +4; }

// Sidebars
if ( function_exists('register_sidebar') ) :
	register_sidebar(array('name'=>'Products Sidebar',
	));
	register_sidebar(array('name'=>'Our Seafood',
	));
endif;

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
		'footer_six' => 'Footer Six'
	));
}

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
     return 10;
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

    unset( $tabs['reviews'] ); 			// Remove the reviews tab
    unset( $tabs['additional_information'] );  	// Remove the additional information tab

    return $tabs;

}

/*
function wc_remove_related_products( $args ) {
	return array();
}
add_filter('woocommerce_related_products_args','wc_remove_related_products', 10);
*/
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}


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
if ( ( !in_array( 'club-member', (array)$user->roles ) ) AND ( ( !in_array( 'club-manager', (array)$user->roles ) ) ) ) {
	add_filter( 'woocommerce_checkout_fields' , 'wpi_additional_checkout_fields' );
	add_action( 'woocommerce_checkout_process', 'wpi_custom_fields_validation');
	add_action( 'woocommerce_checkout_update_order_meta', 'wpi_custom_checkout_field_update_order_meta' );
	add_action( 'woocommerce_admin_order_data_after_billing_address','wpi_custom_checkout_field_display_admin_order_meta', 10, 1 );
}

function wpi_additional_checkout_fields( $fields ) {

	//$t = strtotime('next tuesday');
	//$next = date('l F j',$t);
	//$t = strtotime('next tuesday +1 week');
	//$week_from_next = date('l F j',$t);
	//$t = strtotime('next tuesday +2 weeks');
	//$two_weeks_from_next = date('l F j',$t);
	/*
	$dates = array(
		'' => _('Please Select a Shipping Date...'),
	);

	if ( have_rows('public_club_shipping_dates','option') ) :
		while ( have_rows('public_club_shipping_dates', 'option') ) : the_row();
			$date = get_field('date', false, false);
			$date = new DateTime($date);
			$this_date = $date->format('l F j');
			$dates[$this_date] = $this_date;
		endwhile;
	endif;
	*/

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
		$output = "<style>
						.woocommerce-shipping-fields,
						.shipping,
						.addresses .col-2
						{display:none;}
						.shop_table .fee { display:table-row; }
					</style>";
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
