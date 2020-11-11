<?php
// phpcs:ignoreFile

namespace AutomateWoo\Rules;

use AutomateWoo\RuleQuickFilters\Clauses\ClauseInterface;
use AutomateWoo\RuleQuickFilters\Clauses\NoOpClause;
use AutomateWoo\Rules\Interfaces\NonPrimaryDataTypeQuickFilterable;
use AutomateWoo\Rules\Utilities\DataTypeConditions;
use AutomateWoo\Rules\Utilities\StringQuickFilter;
use Exception;

defined( 'ABSPATH' ) || exit;

/**
 * @class Customer_Postcode
 */
class Customer_Postcode extends Abstract_String implements NonPrimaryDataTypeQuickFilterable {

	use StringQuickFilter;
	use DataTypeConditions;

	public $data_item = 'customer';


	function init() {
		$this->title = __( 'Customer - Postcode', 'automatewoo' );
	}


	/**
	 * @param $customer \AutomateWoo\Customer
	 * @param $compare
	 * @param $value
	 * @return bool
	 */
	function validate( $customer, $compare, $value ) {
		return $this->validate_string( $this->data_layer()->get_customer_postcode(), $compare, $value );
	}

	/**
	 * Get any non-primary data type quick filter clauses for this rule.
	 *
	 * @since 5.0.0
	 *
	 * @param string $data_type    The data type that is being filtered.
	 * @param string $compare_type The rule's compare type.
	 * @param mixed  $value        The rule's expected value.
	 *
	 * @return ClauseInterface
	 *
	 * @throws Exception When there is an error.
	 */
	public function get_non_primary_quick_filter_clause( $data_type, $compare_type, $value ) {
		// Return clauses for order and subscription queries
		if ( $this->is_data_type_order_or_subscription( $data_type ) ) {
			return $this->generate_string_quick_filter_clause( 'billing_postcode', $compare_type, $value );
		}

		return new NoOpClause();
	}

}
