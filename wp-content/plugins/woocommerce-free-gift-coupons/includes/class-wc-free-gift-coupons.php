<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( class_exists( 'WC_Free_Gift_Coupons' ) ) {
	return; // Exit if class exists.
}

/**
 * Main WC_Free_Gift_Coupons Class
 *
 * @class WC_Free_Gift_Coupons
 * @package Class
 * @author   Kathy Darling
 * @version	2.0.0
 */
class WC_Free_Gift_Coupons extends WC_Free_Gift_Coupons_Legacy {

	/**
	 * The plugin version
	 *
	 * @var string
	 */
	public static $version = '2.5.2';

	/**
	 * The required WooCommerce version
	 *
	 * @var string
	 */
	public static $required_woo = '3.1.0';

	/**
	 * Free Gift Coupons pseudo constructor
	 *
	 * @return WC_Free_Gift_Coupons
	 * @since 1.0
	 */
	public static function init() {

		self::includes();

		// Make translation-ready.
		add_action( 'init', array( __CLASS__, 'load_textdomain_files' ) );

		// Add the free_gift coupon type.
		add_filter( 'woocommerce_coupon_discount_types', array( __CLASS__, 'discount_types' ) );

		// Add the gift item when coupon is applied.
		add_action( 'woocommerce_applied_coupon', array( __CLASS__, 'apply_coupon' ) );

		// Change the price to ZERO/Free on gift item.
		add_filter( 'woocommerce_add_cart_item', array( __CLASS__, 'add_cart_item' ), 15 );
		add_filter( 'woocommerce_get_cart_item_from_session', array( __CLASS__, 'get_cart_item_from_session' ), 15, 2 );

		// Validate quantity on update_cart in case sneaky folks mess with the markup.
		add_filter( 'woocommerce_update_cart_validation', array( __CLASS__, 'update_cart_validation' ), 10, 4 );

		// Disable multiple quantities of free item.
		add_filter( 'woocommerce_cart_item_quantity', array( __CLASS__, 'cart_item_quantity' ), 5, 3 );

		// Remove Bonus item when coupon code is removed.
		add_action( 'woocommerce_removed_coupon', array( __CLASS__, 'remove_free_gift_from_cart' ) );

		// Remove Bonus item if coupon code conditions are no longer valid.
		add_action( 'woocommerce_check_cart_items', array( __CLASS__, 'check_cart_items' ) );

		// Free Gifts should not count one way or the other towards product validations.
		add_action( 'woocommerce_coupon_get_items_to_validate', array( __CLASS__, 'exclude_free_gifts_from_coupon_validation' ), 10, 2 );

		// Display as Free! in cart and in orders.
		add_filter( 'woocommerce_cart_item_price', array( __CLASS__, 'cart_item_price' ), 10, 2 );
		add_filter( 'woocommerce_cart_item_subtotal', array( __CLASS__, 'cart_item_price' ), 10, 2 );
		add_filter( 'woocommerce_order_formatted_line_subtotal', array( __CLASS__, 'cart_item_price' ), 10, 2 );

		// Remove free gifts from shipping calcs & enable free shipping if required.
		add_filter( 'woocommerce_cart_shipping_packages', array( __CLASS__, 'remove_free_shipping_items' ) );
		add_filter( 'woocommerce_shipping_free_shipping_is_available', array( __CLASS__, 'enable_free_shipping'), 20, 2 );
		add_filter( 'woocommerce_shipping_legacy_free_shipping_is_available', array( __CLASS__, 'enable_free_shipping'), 20, 2 );

		// Add order item meta.
		add_action( 'woocommerce_checkout_create_order_line_item', array( __CLASS__, 'add_order_item_meta' ), 10, 3 );

	}
	

	/**
	 * Includes.
	 * since 2.0.0
	 */
	public static function includes() {

		// Install.
		include_once  'updates/class-wc-free-gift-coupons-install.php' ;

		// Compatibility.
		include_once  'compatibility/class-wc-fgc-compatibility.php' ;

		// Admin includes.
		if ( is_admin() ) {
			self::admin_includes();
		}

	}


	/**
	 * Admin & AJAX functions and hooks.
	 */
	public static function admin_includes() {

		// Admin notices handling.
		include_once  'admin/class-wc-free-gift-coupons-admin-notices.php' ;

		// Admin functions and hooks.
		include_once  'admin/class-wc-free-gift-coupons-admin.php' ;
	}

	/**
	 * Load localisation files
	 *
	 * Preferred language file location is: /wp-content/languages/plugins/wc_free_gift_coupons-$locale.mo
	 *
	 * @return void
	 * @since 1.0
	 */
	public static function load_textdomain_files() {
		$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'wc_free_gift_coupons' );

		load_textdomain( 'wc_free_gift_coupons', WP_LANG_DIR . '/wc_free_gift_coupons/wc_free_gift_coupons-' . $locale . '.mo' );
		load_plugin_textdomain( 'wc_free_gift_coupons', false, 'woocommerce-free-gift-coupons/languages' );
	}


	/**
	 * Displays a warning message if version check fails.
	 *
	 * @return string
	 */
	public static function admin_notice() {
		wc_deprecated_function( 'WC_Free_Gift_Coupons::admin_notice()', '1.6.0', 'Function is no longer used.' );
		/* translators: %s: Required version of WooCommerce */
		echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Free Gift Coupons requires at least WooCommerce %s in order to function. Please upgrade WooCommerce.', 'wc_free_gift_coupons' ), self::$required_woo ) . '</p></div>';
	}


	/**
	 * Add a new coupon type
	 *
	 * @param array $types - available coupon types
	 * @return array
	 * @since 1.0
	 */
	public static function discount_types( $types ) {
		$types['free_gift'] = __( 'Free Gift', 'wc_free_gift_coupons' );
		return $types;
	}


	/**
	 * Add the gift item to the cart when coupon is applied
	 *
	 * @param string $coupon_code
	 * @return void
	 * @since 1.0
	 */
	public static function apply_coupon( $coupon_code ) { 

		// Get the Gift IDs.
		$gift_data = self::get_gift_data( $coupon_code );

		if ( ! empty ( $gift_data ) ) {
			
			foreach ( $gift_data as $gift_id => $data ) {
						
				$data = apply_filters( 'woocommerce_free_gift_coupon_apply_coupon_data', $data, $coupon_code );

				if ( $data['product_id'] > 0 && isset( $data['data'] ) && $data['data'] instanceof WC_Product && $data['data']->is_purchasable() ) { 
					$key = WC()->cart->add_to_cart( 
						$data['product_id'],
						$data['quantity'],
						$data['variation_id'],
						$data['variation'], 
						array(
							'free_gift'    => $coupon_code,
							'fgc_quantity' => isset( $data['quantity'] ) && $data['quantity'] > 0 ? intval( $data['quantity'] ) : 1,
							'fgc_type'     => $data['data'] instanceof WC_Product_Variable ? 'variable' : 'not-variable',
						)
					);
				}
			}

			do_action( 'woocommerce_free_gift_coupon_applied', $coupon_code );

		}

	}


	/**
	 * Prevent Subscriptions validating free gift coupons
	 *
	 * @param bool $validate
	 * @param obj $coupon
	 * @return bool
	 * @since 1.0.7
	 * @deprecated 1.2.1 - Moved to separate compatibility class.
	 */
	public static function ignore_free_gift( $validate, $coupon ) {
		wc_deprecated_function( 'WC_Free_Gift_Coupons::ignore_free_gift', '2.1.2', 'WC_FGC_Subscriptions_Compatibility::ignore_free_gift' );
		return WC_FGC_Subscriptions_Compatibility::ignore_free_gift( $validate, $coupon );
	}


	/**
	 * Change the price on the gift item to be zero
	 *
	 * @param array $cart_item
	 * @return array
	 * @since 1.0
	 */
	public static function add_cart_item( $cart_item ) {

		// Adjust price in cart if bonus item.
		if ( ! empty ( $cart_item['free_gift'] ) ) {
			$cart_item['data']->set_price( 0 );
			$cart_item['data']->set_regular_price( 0 );
			$cart_item['data']->set_sale_price( 0 );
		}

		// Strictly enforce original quantity.
		if ( ! empty ( $cart_item['fgc_quantity'] ) ) {
			$cart_item['quantity'] = $cart_item['fgc_quantity'];
		}

		return $cart_item;
	}

	/**
	 * Adjust session values on the gift item
	 *
	 * @param array $cart_item
	 * @param array $values
	 * @return array
	 * @since 1.0
	 */
	public static function get_cart_item_from_session( $cart_item, $values ) {

		if ( ! empty( $values['free_gift'] ) ) {
			$cart_item['free_gift'] = $values['free_gift'];

			if ( ! empty ( $values['fgc_quantity'] ) ) {
				$cart_item['fgc_quantity'] = $values['fgc_quantity'];
			}

			if ( ! empty ( $values['fgc_type'] ) ) {
				$cart_item['fgc_type'] = $values['fgc_type'];
			}
			$cart_item = self::add_cart_item( $cart_item );
		}

		return $cart_item;

	}

	/**
	 * Update cart validation.
	 * Malicious users can change the quantity input in the source markup.
	 * 
	 * @param bool $passed_validation Whether or not this product is valid.
	 * @param string $cart_item_key The unique key in the cart array.
	 * @param array $values The cart item data values.
	 * @param int $quantity the cart quantity.
	 * @return bool
	 * @since 2.4.0
	 */
	public static function update_cart_validation( $passed_validation, $cart_item_key, $values, $quantity ) {

		if ( ! empty( $values['free_gift'] ) ) {
			
			// Has an initial FGC quantity.
			if ( ! empty ( $values['fgc_quantity'] ) && $quantity !== $values['fgc_quantity'] ) {

				/* Translators: %s Product title. */
				wc_add_notice( sprintf( __( 'You are not allowed to modify the quantity of your %s gift.', 'wc_free_gift_coupons' ), $values['data']->get_name() ), 'error' );
				$passed_validation = false;
			}

		}

		return $passed_validation;

	}			

	/**
	 * Disable quantity inputs in cart
	 *
	 * @param string $product_quantity
	 * @param string $cart_item_key
	 * @param array $cart_item
	 * @return string
	 * @since 1.0
	 */
	public static function cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {

		if ( ! empty ( $cart_item['free_gift'] ) ) {
			$product_quantity = sprintf( '%1$s <input type="hidden" name="cart[%2$s][qty]" value="%1$s" />', $cart_item['quantity'], $cart_item_key );
		}

		return $product_quantity;
	}


	/**
	 * Removes gift item from cart when coupon is removed
	 *
	 * @param string $coupon
	 * @return void
	 * @since 1.2.0
	 */
	public static function remove_free_gift_from_cart( $coupon ) {

		// If the coupon still applies to the initial cart, the free product can remain. WC Subscriptions removes coupons from recurring cart objects. This condition bypasses that.
		if ( WC()->cart->has_discount( $coupon ) ) {
			return;
		}

		foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {

			if ( isset( $values['free_gift'] ) && $values['free_gift'] === $coupon ) {

				WC()->cart->set_quantity( $cart_item_key, 0 );

			}
		}

	}


	/**
	 * Removes gift item from cart if coupon is invalidated
	 *
	 * @return void
	 * @since 1.0
	 */
	public static function check_cart_items() {

		$cart_coupons = (array) WC()->cart->get_applied_coupons();

		foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {

			if ( isset( $values['free_gift'] ) && ! in_array( $values['free_gift'], $cart_coupons, true ) ) {

				WC()->cart->set_quantity( $cart_item_key, 0 );

				wc_add_notice( __( 'A gift item which is no longer available was removed from your cart.', 'wc_free_gift_coupons' ), 'error' );

			}
		}

	}

	/**
	 * Removes gift item from cart if coupon is invalidated
	 * 
	 * @param  object[] $items | $item properties:  key, object (cart item or order item), product, quantity, price
	 * @return object[]
	 * @since 2.0.0
	 */
	public static function exclude_free_gifts_from_coupon_validation( $items, $discount ) {
		return array_filter( $items, array( __CLASS__, 'exclude_free_gifts' ) );
	}


	/**
	 * Array_filter callback
	 * 
	 * @param  object $item | properties:  key, object (cart item or order item), product, quantity, price
	 * @return object[]
	 * @since 2.0.0
	 */
	public static function exclude_free_gifts( $item ) {
		return ! ( is_array( $item->object ) && isset( $item->object[ 'free_gift' ] ) ) || ( is_a( $item->object, 'WC_Order_Item' ) && $item->object->get_meta( '_free_gift' ) );
	}


	/**
	 * Instead of $0, show Free! in the cart/order summary
	 *
	 * @param string $price
	 * @param mixed array|WC_Order_Item $cart_item
	 * @return string
	 * @since 1.0
	 */
	public static function cart_item_price( $price, $cart_item ) {

		// WC 2.7 passes a $cart_item object to order item subtotal.
		if ( ( is_array( $cart_item ) && isset( $cart_item['free_gift' ] ) ) || ( is_object( $cart_item ) && $cart_item->get_meta( '_free_gift' ) ) ) {
			$price = __( 'Free!', 'wc_free_gift_coupons' );
		}

		return $price;
	}


	/**
	 * Unset the free items from the packages needing shipping calculations
	 *
	 * @param array $packages
	 * @return array
	 * @since 1.0.7
	 */
	public static function remove_free_shipping_items( $packages ) {

		if ( $packages ) {
			foreach ( $packages as $i => $package ) { 

				$free_shipping_count = 0;
				$remove_items        = array();
				$total_count         = count( $package['contents'] );

				foreach ( $package['contents'] as $key => $item ) {
				
					// If the item is a free gift item get free shipping status.
					if ( isset( $item['free_gift'] ) ) {

						if ( self::has_free_shipping( $item['free_gift'] ) ) {
							$remove_items[$key] = $item;
							$free_shipping_count++;
						} 

					} 

					// If the free gift with free shipping is the only item then switch 
					// shipping to free shipping. otherwise delete free gift from package calcs.
					if ( $total_count === $free_shipping_count ) {
						$packages[$i]['ship_via'] = array( 'free_shipping' );					
					} else {
						$remaining_packages       = array_diff_key( $packages[$i]['contents'], $remove_items );
						$packages[$i]['contents'] = $remaining_packages;
					}

				}

			}
		}

		return $packages;
	}


	/**
	 * If the free gift w/ free shipping is the only item in the cart, enable free shipping
	 *
	 * @param array $packages
	 * @return array
	 * @since 1.0.7
	 */
	public static function enable_free_shipping( $is_available, $package ) { 

		if ( count( $package['contents'] ) === 1 && self::check_for_free_gift_with_free_shipping( $package ) ) {
			$is_available = true;
		}
	 
		return $is_available;
	}


	/**
	 * Check shipping package for a free gift with free shipping
	 *
	 * @param array $package
	 * @return boolean
	 * @since 1.1.0
	 */
	public static function check_for_free_gift_with_free_shipping( $package ) { 

		$has_free_gift_with_free_shipping = false;

		// Loop through the items looking for one in the eligible array.
		foreach ( $package['contents'] as $item ) {

			// if the item is a free gift item get free shipping status
			if ( isset( $item['free_gift'] ) ) { 

				if ( self::has_free_shipping( $item['free_gift'] ) ) {
					$has_free_gift_with_free_shipping = true;
					break;
				} 

			} 

		}
	 
		return $has_free_gift_with_free_shipping;
	}

	/**
	 * When a new order is inserted, add item meta noting this item was a free gift
	 *
	 * @param WC_Order_Item $item
	 * @param str $cart_item_key
	 * @param array $values
	 * @return void
	 * @since 1.1.1
	 */
	public static function add_order_item_meta( $item, $cart_item_key, $values ) {
		if ( isset( $values['free_gift'] ) ) { 	
			$item->add_meta_data( '_free_gift', $values['free_gift'], true );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Check the installed version of WooCommerce is greater than $version argument
	 *
	 * @param   $version
	 * @return	boolean
	 * @since   1.1.0
	 */
	public static function wc_is_version( $version = '2.6' ) {
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, $version ) >= 0 ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get Free Gift Data from a coupon's ID.
	 *
	 * @param   mixed $code int coupon ID  | str coupon code
	 * @param   bool $add_titles add product titles - Deprecated 2.5.0
	 * @return	array
	 * @since   2.0.0
	 */
	public static function get_gift_data( $code, $add_titles = false ) {

		$gift_data = array();

		// Sanitize coupon code.
		$code = wc_format_coupon_code( $code );

		// Get the coupon object.
		$coupon = new WC_Coupon( $code );
		
		if ( ! is_wp_error( $coupon ) && self::is_supported_gift_coupon_type( $coupon->get_discount_type() ) ) {
			
			$coupon_meta = $coupon->get_meta( '_wc_free_gift_coupon_data' );

			// Only return meta if it is an array, since coupon meta can be null, which results in an empty model in the JS collection.
			$gift_data = is_array( $coupon_meta ) ? $coupon_meta : array();

			foreach ( $gift_data as $gift_id => $gift ) {

				$gift_product = wc_get_product( $gift_id );

				$defaults = array (
					'product_id'   => 0,
					'quantity'     => 1,
					'variation_id' => 0,
					'variation'    => array(),
					'data'         => $gift_product, // The product object is always passed now.
					'title'        => '',
				);

				$gift_data[$gift_id] = wp_parse_args( $gift, $defaults );
		

				if ( $gift_product instanceof WC_Product ) {

					// Add variation attributes.
					if ( $gift_product->get_parent_id() > 0 && is_callable( array( $gift_product, 'get_variation_attributes' ) ) ) { 
						$gift_data[$gift_id]['variation'] = $gift_product->get_variation_attributes();
					}

					// Get the title of each product.
					$gift_data[$gift_id]['title'] = $gift_product->get_formatted_name();

				}
			}
		}

		return apply_filters( 'woocommerce_free_gift_coupon_data', $gift_data, $code );

	}

	/**
	 * Supported coupon types.
	 *
	 * @return	array
	 * @since   3.0.0
	 */
	public static function get_gift_coupon_types() {
		return apply_filters( 'woocommerce_free_gift_coupon_types', array( 'free_gift', 'percent', 'fixed_cart', 'fixed_product' ) );
	}

	/**
	 * Is Supported coupon types.
	 *
	 * @param   string
	 * @return	bool
	 * @since   3.0.0
	 */
	public static function is_supported_gift_coupon_type( $type ) {
		return in_array( $type, self::get_gift_coupon_types(), true );
	}

	/**
	 * Is free shipping enabled for free gift?
	 *
	 * @param   mixed $code int coupon ID  | str coupon code
	 * @return	bool
	 * @since   1.1.1
	 */
	public static function has_free_shipping( $code ) {

		$has_free_shipping = false;

		// Sanitize coupon code.
		$code = wc_format_coupon_code( $code );

		// Get the coupon object.
		$coupon = new WC_Coupon( $code );

		$gift_ids = array();

		if ( ! is_wp_error( $coupon ) && self::is_supported_gift_coupon_type( $coupon->get_discount_type() ) ) {
			$has_free_shipping = wc_string_to_bool( $coupon->get_meta( '_wc_free_gift_coupon_free_shipping' ) );
		}

		return $has_free_shipping;

	}

} // End class.

