<?php

/**
 * Plugin Name: Lummi Wild - Club Shipping Method
 * Plugin URI: http://www.intuitowebsites.com
 * Description: Individual delivery for clubs. ( Add-on for WooCommerce and Lummi Wild plugin )
 * Version: 1.0.0
 * Author: Rob Haskell, Intuito Websites
 * License: private
 * Text Domain: clubsshipping
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/*
 * Check if WooCommerce and Lummi Plugin is active
 */
if (
	in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) &&
	in_array('lummi/lummi_wild.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )
) {

	function clubs_shipping_method() {
		if ( ! class_exists( 'Clubs_Shipping_Method' ) ) {
			class Clubs_Shipping_Method extends WC_Shipping_Method {

				public function __construct() {
					$this->id                 = 'clubsshipping';
					$this->method_title       = __( 'Clubs', 'clubsshipping' );
					$this->method_description = __( 'Shipping Method for Clubs', 'clubsshipping' );

					// Availability & Countries
					$this->availability = 'including';
					$this->countries = array(
						'US'
					);

					$this->init();

					$this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
					$this->title = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'Clubs Shipping', 'clubsshipping' );
				}

				function init() {
					// Load the settings API
					$this->init_form_fields();
					$this->init_settings();

					// Save settings in admin if you have any defined
					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
				}

				function init_form_fields() {
					$this->form_fields = array(

						'enabled' => array(
							'title' => __( 'Enable', 'clubsshipping' ),
							'type' => 'checkbox',
							'description' => __( 'Enable this shipping.', 'clubsshipping' ),
							'default' => 'yes'
						),

						'title' => array(
							'title' => __( 'Title', 'clubs_shipping' ),
							'type' => 'text',
							'description' => __( 'Title to be displayed on the site', 'clubsshipping' ),
							'default' => __( 'Club Method', 'clubsshipping' )
						),

						'club_type' => array(
							'title' => __( 'Attribute key', 'clubs_shipping' ),
							'type' => 'text',
							'description' => __( 'Product attribute slug. Look <a target="_blank" rel="noopener noreferrer" href="/wp-admin/edit.php?post_type=product&page=product_attributes">here</a>', 'clubsshipping' ),
							'default' => 'club-type'
						),
						'club_type_value' => array(
							'title' => __( 'Attribute value', 'clubs_shipping' ),
							'type' => 'text',
							'description' => __( 'Attribute terms (slug).', 'clubsshipping' ),
							'default' => 'private-club'
						),

					);

				}

				public function calculate_shipping( $package = array()) {
					$cost = 0;
					$apply = false;
					$user = array();
					$_cost = 0;

					if( class_exists('\LW\Settings')){
						$user = \LW\Settings::currentUser();
						$is_closed = \LW\Settings::is_closed_club();
					}

					$country = $package["destination"]["country"];
					foreach ( $package['contents'] as $item_id => $values )
					{
						if(is_user_logged_in() && !$is_closed && !$user["club_term_meta"]["default_club"]){
							$apply = true;
							$_product = $values['data'];
							$_cost += $_product->get_weight() * $values['quantity'];
						}
					}

					if( $apply ) {
						$cost = $_cost * $user["club_term_meta"]["clubs_ship_price"];
					}

					$countryZones = array(
						'US' => 3
					);

					$zonePrices = array(
						3 => 0
					);

					$zoneFromCountry = $countryZones[ $country ];
					$priceFromZone = $zonePrices[ $zoneFromCountry ];

					$cost += $priceFromZone;

					$rate = array(
						'id' => $this->id,
						'label' => $this->title,
						'cost' => $cost
					);

					if( $apply ){
						$this->add_rate( $rate );
					}
				}
			}
		}
	}

	add_action( 'woocommerce_shipping_init', 'clubs_shipping_method',10 );

	function add_clubs_shipping_method( $methods ) {
		$methods[] = 'Clubs_Shipping_Method';
		return $methods;
	}

	add_filter( 'woocommerce_shipping_methods', 'add_clubs_shipping_method',10 );

	function required_fields()
	{
		echo '<script type="text/javascript">
					jQuery(document).ready(function($){
						var title = $("#woocommerce_clubsshipping_title");
						if( "undefined" !== title){
						    title.prop("required", true);   
						}
					});
			  </script>';
	}
	add_action('admin_footer', 'required_fields');

	function switch_shipping_method( $rates ) {

		$free = array();
//		$methods = array('clubsshipping');
		$rates = ( ! is_null($rates['clubsshipping'] ) ) ? array('clubsshipping' => $rates['clubsshipping']) + $rates : $rates;

		foreach ( $rates as $rate_id => $rate ) {

//			if ( in_array($rate->method_id,$methods) && ! is_null($rate) ) {
//				$free[ $rate_id ] = $rate;
//				break;
//			}
			if ( 'clubsshipping' == $rate->method_id ) {
				$free[ $rate_id ] = $rate;
				break;
			}
			if ( 'free_shipping' === $rate->method_id ) {
				$free[ $rate_id ] = $rate;
				break;
			}
		}
		if ( ! empty( $free ) ) {

			foreach ( $rates as $rate_id => $rate ) {

				if ( 'local_pickup' === $rate->method_id ) {
					$free[ $rate_id ] = $rate;
					break;
				}

			}
		}
		return ! empty( $free ) ? $free : $rates;
	}
	add_filter( 'woocommerce_package_rates', 'switch_shipping_method', 10 );
}