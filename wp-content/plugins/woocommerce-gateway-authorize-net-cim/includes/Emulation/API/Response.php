<?php
/**
 * WooCommerce Authorize.Net Gateway
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Authorize.Net Gateway to newer
 * versions in the future. If you wish to customize WooCommerce Authorize.Net Gateway for your
 * needs please refer to http://docs.woocommerce.com/document/authorize-net-cim/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Authorize_Net\Emulation\API;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_8_1 as Framework;

/**
 * Authorize.Net Emulation Response Class
 *
 * Parses XML received from the Authorize.Net NVP API
 *
 * @link http://www.authorize.net/support/AIM_guide.pdf
 *
 * @since 3.0.0
 */
class Response implements Framework\SV_WC_Payment_Gateway_API_Response, Framework\SV_WC_Payment_Gateway_API_Authorization_Response {


	/** approved transaction response code */
	const TRANSACTION_APPROVED = '1';

	/** held for review transaction response code */
	const TRANSACTION_HELD = '4';

	/** CSC match code */
	const CSC_MATCH = 'M';

	/** @var string raw response string */
	protected $raw_response;

	/** @var \stdClass parsed response object */
	protected $response;


	/**
	 * Constructs the class.
	 *
	 * @since 3.0.0
	 *
	 * @param string $raw_response raw response data
	 * @throws Framework\SV_WC_Payment_Gateway_Exception
	 */
	public function __construct( $raw_response ) {

		$this->raw_response = $raw_response;

		$this->parse_response();
	}


	/**
	 * Parses the response string and set the parsed response object.
	 *
	 * @since 3.0.0
	 *
	 * @throws Framework\SV_WC_Payment_Gateway_Exception
	 */
	protected function parse_response() {

		// adjust response based on our hybrid delimiter :|: (delimiter = | encapsulation = :)
		// remove the leading encap character and add a trailing delimiter/encap character
		// so explode works correctly -- response string starts and ends with an encapsulation
		// character)
		$this->raw_response = ltrim( $this->raw_response, ':' ) . '|:';

		// parse response
		$response = explode( ':|:', $this->raw_response );

		if ( empty( $response ) ) {
			throw new Framework\SV_WC_Payment_Gateway_Exception( __( 'Could not parse direct response.', 'woocommerce-gateway-authorize-net-cim' ) );
		}

		// offset array by 1 to match Authorize.Net's order, mainly for readability
		array_unshift( $response, null );

		$this->response = new \stdClass();

		// response fields are URL encoded, but we currently do not use any fields
		// (e.g. billing/shipping details) that would be affected by that
		$response_fields = array(
			'response_code'        => 1,
			'response_subcode'     => 2,
			'response_reason_code' => 3,
			'response_reason_text' => 4,
			'authorization_code'   => 5,
			'avs_response'         => 6,
			'transaction_id'       => 7,
			'amount'               => 10,
			'account_type'         => 11,
			'transaction_type'     => 12,
			'csc_response'         => 39,
			'cavv_response'        => 40,
			'account_last_four'    => 51,
			'card_type'            => 52,
		);

		foreach ( $response_fields as $field => $order ) {

			$this->response->$field = ( isset( $response[ $order ] ) ) ? $response[ $order ] : '';
		}
	}


	/**
	 * Determines if the transaction was successful.
	 *
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::transaction_approved()
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function transaction_approved() {

		return self::TRANSACTION_APPROVED === $this->get_status_code();
	}


	/**
	 * Determines if the transaction was held.
	 *
	 * This indicates that the transaction was successful, but did not pass a
	 * fraud check and should be reviewed, for instance due to AVS/CSC Fraud
	 * Settings.
	 *
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::transaction_held()
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function transaction_held() {

		return self::TRANSACTION_HELD === $this->get_status_code();
	}


	/**
	 * Gets the response transaction ID.
	 *
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::get_transaction_id()
	 *
	 * @since 3.0.0
	 *
	 * @return string|null
	 */
	public function get_transaction_id() {

		return $this->response->transaction_id;
	}


	/**
	 * Gets the transaction status message.
	 *
	 * This will be API error message if there was an API error, otherwise the
	 * transaction status message.
	 *
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::get_status_message()
	 *
	 * @since 3.0.0
	 *
	 * @return string|null
	 */
	public function get_status_message() {

		return $this->response->response_reason_text;
	}


	/**
	 * Gets the transaction status code.
	 *
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::get_status_code()
	 *
	 * @since 3.0.0
	 *
	 * @return string|null
	 */
	public function get_status_code() {

		return $this->response->response_code;
	}


	/**
	 * Gets the transaction authorization code.
	 *
	 * This is returned from the credit card processor to indicate that the
	 * charge will be paid by the card issuer.
	 *
	 * @see Framework\SV_WC_Payment_Gateway_API_Authorization_Response::get_authorization_code()
	 *
	 * @since 3.0.0
	 *
	 * @return string|null
	 */
	public function get_authorization_code() {

		return $this->response->authorization_code;
	}


	/**
	 * Gets the result of the AVS check.
	 *
	 * @see page 49 of the AIM XML developer documentation for explanations
	 * @see Framework\SV_WC_Payment_Gateway_API_Authorization_Response::get_avs_result()
	 *
	 * @since 3.0.0
	 *
	 * @return string|null
	 */
	public function get_avs_result() {

		return $this->response->avs_response;
	}


	/**
	 * Gets the result of the CSC check.
	 *
	 * @see Framework\SV_WC_Payment_Gateway_API_Authorization_Response::get_csc_result()
	 *
	 * @since 3.0.0
	 *
	 * @return string|null
	 */
	public function get_csc_result() {

		return $this->response->csc_response;
	}


	/**
	 * Determines if the CSC check was successful.
	 *
	 * @see page 50 of the AIM XML developer documentation for CSC response code explanations
	 * @see Framework\SV_WC_Payment_Gateway_API_Authorization_Response::csc_match()
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function csc_match() {

		return self::CSC_MATCH === $this->get_csc_result();
	}


	/**
	 * Gets the result of the CAVV (Cardholder authentication verification) check.
	 *
	 * @see Framework\SV_WC_Payment_Gateway_API_Authorization_Response::get_csc_result()
	 *
	 * @since 3.0.0
	 *
	 * @return string|null
	 */
	public function get_cavv_result() {

		return $this->response->cavv_response;
	}


	/**
	 * Gets a message appropriate for a frontend user.
	 *
	 * @see Framework\SV_WC_Payment_Gateway_API_Response_Message_Helper
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::get_user_message()
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_user_message() {

		return '';
	}


	/**
	 * Gets the payment type: 'credit-card', 'echeck', etc
	 *
	 * @since 3.0.0
	 *
	 * @return string payment type or null if not available
	 */
	public function get_payment_type() {

		return 'credit-card';
	}


	/**
	 * Gets the string representation of the response
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function to_string() {

		return $this->raw_response;
	}


	/**
	 * Gets the string representation of the response.
	 *
	 * Stripped of any sensitive or confidential information to make it suitable for logging.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function to_string_safe() {

		// no sensitive data to mask in response
		return $this->raw_response;
	}


}

