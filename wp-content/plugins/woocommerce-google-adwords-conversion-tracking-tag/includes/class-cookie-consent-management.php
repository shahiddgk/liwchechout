<?php

/**
 * Name: Cookie Consent Management
 * Version:  1.0
 */

// TODO implement CCM https://wordpress.org/plugins/uk-cookie-consent/ (200k) -> doesn't allow cookies to be disabled
// TODO impelemnt CCM https://wordpress.org/plugins/cookiebot/ (20k) -> no cookie or filter based third party tracking opt out
// TODO impelemnt CCM https://wordpress.org/plugins/responsive-cookie-consent/ (3k)
// TODO impelemnt CCM https://wordpress.org/plugins/easy-wp-cookie-popup/ (2k)
// TODO impelemnt CCM https://wordpress.org/plugins/surbma-gdpr-proof-google-analytics/ (1k)
// TODO impelemnt CCM https://wordpress.org/plugins/eu-cookie-law/ (100k) -> doesn't set a non tracking cookie. bad programming overall
// TODO impelemnt CCM https://wordpress.org/plugins/gdpr/ (30k) -> not possible to implement since users can choose their own cookie names
// TODO impelemnt CCM https://wordpress.org/plugins/wf-cookie-consent/ (20k)


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WgactCookieConsentManagement {

	public static $pluginPrefix;

	// check if third party cookie prevention is active
	public static function is_cookie_prevention_active() {

		$cookiePrevention = false;

		// use filter to set default to activate prevention
		// add_filter( 'wgact_cookie_prevention', '__return_true' );
		// later, turn it off in order to allow cookies in case they have been actively approved
		$cookiePrevention = apply_filters( self::$pluginPrefix . 'cookie_prevention', $cookiePrevention );

		// check if the Moove third party cookie prevention is on
		if ( self::is_moove_cookie_prevention_active() ) {
			$cookiePrevention = true;
		}

		// check if the Cooke Notice Plugin third party cookie prevention is on
		if ( self::is_cookie_notice_plugin_cookie_prevention_active() ) {
			$cookiePrevention = true;
		}

		// check if the Cooke Law Info third party cookie prevention is on
		if ( self::is_cookie_law_info_cookie_prevention_active() ) {
			$cookiePrevention = true;
		}

		// check if marketing cookies have been approved by Borlabs
		if ( self::checkBorlabsGaveMarketingConsent() ){
			$cookiePrevention = false;
		}

		return $cookiePrevention;
	}

	public static function checkBorlabsGaveMarketingConsent(){

		// check if Borlabs is running
		if (function_exists('BorlabsCookieHelper')){

			// check if Borlabs minimum version is installed
			$borlabs_info = get_file_data( ABSPATH . 'wp-content/plugins/' . 'borlabs-cookie/borlabs-cookie.php', [
				'Version' => 'Version'
			] );

			// the minimum version I know of that supports gaveConsent('marketing') is 2.2.4
			if(version_compare('2.1.0', $borlabs_info['Version'], '<=')){

				if (BorlabsCookieHelper()->gaveConsent('google-ads')){
					return true;
				}
			}
		}

		return false;
	}

	public static function setPluginPrefix( $name ) {
		self::$pluginPrefix = $name;
	}

	// return the cookie contents, if the cookie is set
	public static function getCookie( $cookie_name ) {

		if ( isset( $_COOKIE[ $cookie_name ] ) ) {
			return $_COOKIE[ $cookie_name ];
		} else {
			return null;
		}
	}

	// check if the Cookie Law Info plugin prevents third party cookies
	// https://wordpress.org/plugins/cookie-law-info/
	public static function is_cookie_law_info_cookie_prevention_active() {

		$cookieConsentManagementcookie = self::getCookie( 'viewed_cookie_policy' );

		if ( $cookieConsentManagementcookie == 'no' ) {
			return true;
		} else {
			return false;
		}
	}

	// check if the Cookie Notice Plugin prevents third party cookies
	// https://wordpress.org/plugins/cookie-notice/
	public static function is_cookie_notice_plugin_cookie_prevention_active() {

		$cookieConsentManagementcookie = self::getCookie( 'cookie_notice_accepted' );

		if ( $cookieConsentManagementcookie == 'false' ) {
			return true;
		} else {
			return false;
		}
	}

	// check if the Moove GDPR Cookie Compliance prevents third party cookies
	// https://wordpress.org/plugins/gdpr-cookie-compliance/
	public static function is_moove_cookie_prevention_active() {
		if ( isset( $_COOKIE['moove_gdpr_popup'] ) ) {

			$cookieConsentManagementcookie = $_COOKIE['moove_gdpr_popup'];
			$cookieConsentManagementcookie = json_decode( stripslashes( $cookieConsentManagementcookie ), true );

			if ( $cookieConsentManagementcookie['thirdparty'] == 0 ) {
				// print_r( $cookieConsentManagementcookie );
				return true;
			} else {
				return false;
			}

		} else {
			return false;
		}
	}
}