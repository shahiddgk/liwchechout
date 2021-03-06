<?php
// phpcs:ignoreFile

namespace AutomateWoo;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class Variable_Product_Short_Description
 */
class Variable_Product_Short_Description extends Variable {


	function load_admin_details() {
		$this->description = __( "Displays the product's short description.", 'automatewoo');
	}


	/**
	 * @param $product \WC_Product
	 * @param $parameters
	 * @return string
	 */
	function get_value( $product, $parameters ) {
		return $product->get_short_description();
	}

}
