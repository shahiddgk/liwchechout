<?php

use AutomateWoo\Frontend_Endpoints\Login_Redirect;
use AutomateWoo\LegacyClassLoader;
use AutomateWoo\Options;
use AutomateWoo\Usage_Tracking\Initializer as UsageTrackingInitializer;
use AutomateWoo\Jobs\DeleteExpiredCoupons;
use AutomateWoo\Jobs\DeleteFailedQueuedWorkflows;

defined( 'ABSPATH' ) || exit;

/**
 * AutomateWoo plugin singleton.
 *
 * @class   AutomateWoo
 * @package AutomateWoo
 */
final class AutomateWoo extends AutomateWoo_Legacy {

	/**
	 * The plugin version.
	 *
	 * @var string
	 */
	public $version;

	/**
	 * The plugin slug 'automatewoo'.
	 *
	 * @var string
	 */
	public $plugin_slug;

	/**
	 * The plugin basename 'automatewoo/automatewoo.php'.
	 *
	 * @var string
	 */
	public $plugin_basename;

	/**
	 * The plugin website URL.
	 *
	 * @var string
	 */
	public $website_url = 'https://automatewoo.com/';

	/**
	 * Order helper class.
	 *
	 * @var AutomateWoo\Order_Helper
	 */
	public $order_helper;

	/**
	 * Options class.
	 *
	 * @var AutomateWoo\Options
	 */
	private $options;

	/**
	 * Instance of singleton.
	 *
	 * @var AutomateWoo
	 */
	private static $instance = null;

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->version         = AUTOMATEWOO_VERSION;
		$this->plugin_basename = plugin_basename( AUTOMATEWOO_FILE );
		$this->plugin_slug     = AUTOMATEWOO_SLUG;
		require_once $this->path() . '/includes/helpers.php';
		add_action( 'woocommerce_init', [ $this, 'init' ], 20 );
	}

	/**
	 * Init
	 */
	public function init() {

		( new LegacyClassLoader() )->register();

		$this->includes();

		AutomateWoo\Constants::init();
		AutomateWoo\Post_Types::init();
		AutomateWoo\Ajax::init();

		if ( $this->is_installed() ) {
			AutomateWoo\Cron::init();
			( new AutomateWoo\Rest_Api() )->init();
			AutomateWoo\Session_Tracker::init();
			AutomateWoo\Customers::init();
		}

		// legacy access to session tracker class
		$this->session_tracker = new AutomateWoo\Session_Tracker();
		$this->order_helper    = new AutomateWoo\Order_Helper();

		do_action( 'automatewoo_init_addons' );

		// Init all triggers
		// Actions don't load until required by admin interface or when a workflow runs
		AutomateWoo\Triggers::init();

		if ( is_admin() ) {
			$this->admin = new AutomateWoo\Admin();
			AutomateWoo\Admin::init();
			AutomateWoo\Updater::init();
			AutomateWoo\UpdateNoticeManager::init();
			AutomateWoo\Installer::init();
		}

		do_action( 'automatewoo_init' );

		AutomateWoo\Event_Helpers\User_Registration::init();
		AutomateWoo\Event_Helpers\Products_On_Sale::init();
		AutomateWoo\Event_Helpers\Review_Posted::init();

		if ( AutomateWoo\Integrations::is_subscriptions_active() ) {
			AutomateWoo\Event_Helpers\Subscription_Status_Changed::init();
		}

		AutomateWoo\Active_Triggers_Cache::init();
		AutomateWoo\Async_Events::init_required_events();

		/**
		 * Check if Points and Rewards is active
		 *
		 * @since 4.5.0
		 */
		if ( AutomateWoo\Integrations::is_points_rewards_active() ) {
			new \AutomateWoo\Points_Rewards_Integration();
		}

		if ( $this->is_request( 'ajax' ) || $this->is_request( 'cron' ) ) {
			// Load all background processes
			AutomateWoo\Background_Processes::get_all();
			// Load async request
			AutomateWoo\Events::get_event_runner_async_request();

			// Init jobs
			( new DeleteFailedQueuedWorkflows() )->init();
			if ( AW()->options()->clean_expired_coupons ) {
				( new DeleteExpiredCoupons() )->init();
			}
		}

		if ( $this->is_request( 'frontend' ) ) {
			( new Login_Redirect() )->init();
		}

		// Load the usage tracker.
		UsageTrackingInitializer::init();

		if ( AutomateWoo\Options::abandoned_cart_enabled() ) {
			AutomateWoo\Carts::init();
		}

		AutomateWoo\Communication_Account_Tab::init();
		AutomateWoo\Workflows::init();
		AutomateWoo\Marketplace_License_Transition_Helper::init();

		if ( $this->is_installed() ) {
			AutomateWoo\Hooks::init();
		}

		if ( version_compare( WC()->version, '3.4', '>=' ) ) {
			new AutomateWoo\Privacy();
		}

		do_action( 'automatewoo_loaded' );
	}

	/**
	 * File includes.
	 */
	public function includes() {
		require_once $this->path() . '/includes/customer-functions.php';
		require_once $this->path() . '/includes/product-functions.php';

		if ( ! class_exists( 'Easy_User_Tags' ) ) {
			new AutomateWoo\User_Tags();
		}
	}

	/**
	 * Plugin options.
	 *
	 * @return AutomateWoo\Options
	 */
	public function options() {
		if ( ! isset( $this->options ) ) {
			$this->options = new AutomateWoo\Options();
		}
		return $this->options;
	}

	/**
	 * What type of request is this?
	 *
	 * @param string $type Ajax, frontend or admin.
	 *
	 * @return bool
	 */
	public function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! $this->is_rest_api_request();
		}
		return false;
	}


	/**
	 * Returns true if the request is a non-legacy REST API request.
	 *
	 * Legacy REST requests should still run some extra code for backwards compatibility.
	 *
	 * Copy of \WooCommerce::is_rest_api_request() which was added around 3.6
	 *
	 * @since 4.5.4
	 *
	 * @todo: replace this function once core WP function is available: https://core.trac.wordpress.org/ticket/42061.
	 *
	 * @return bool
	 */
	public function is_rest_api_request() {
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		$rest_prefix         = trailingslashit( rest_get_url_prefix() );
		$is_rest_api_request = ( false !== strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) ); // phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		return apply_filters( 'woocommerce_is_rest_api_request', $is_rest_api_request );
	}

	/**
	 * Get the URL to something in the plugin dir.
	 *
	 * @param string $end End of the URL.
	 *
	 * @return string
	 */
	public function url( $end = '' ) {
		return untrailingslashit( plugin_dir_url( $this->plugin_basename ) ) . $end;
	}

	/**
	 * Get the URL to something in the plugin admin assets dir.
	 *
	 * @param string $end End of the URL.
	 *
	 * @return string
	 */
	public function admin_assets_url( $end = '' ) {
		return $this->url( '/admin/assets' . $end );
	}

	/**
	 * Get the path to something in the plugin dir.
	 *
	 * @param string $end End of the path.
	 *
	 * @return string
	 */
	public function path( $end = '' ) {
		return untrailingslashit( dirname( AUTOMATEWOO_FILE ) ) . $end;
	}

	/**
	 * Get the path to something in the plugin admin dir.
	 *
	 * @param string $end End of the path.
	 *
	 * @return string
	 */
	public function admin_path( $end = '' ) {
		return $this->path( '/admin' . $end );
	}

	/**
	 * Get the path to something in the plugin library dir.
	 *
	 * @param string $end End of the path.
	 *
	 * @return string
	 */
	public function lib_path( $end = '' ) {
		return $this->path( '/libraries' . $end );
	}

	/**
	 * Return true if AutomateWoo is installed.
	 *
	 * @since 4.9.2
	 *
	 * @return bool
	 */
	public function is_installed() {
		// The database version is only added after install is complete
		return (bool) Options::database_version();
	}

	/**
	 * Return the singleton instance.
	 *
	 * @return AutomateWoo
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

}

// phpcs:disable WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid

/**
 * For backwards compatible.
 *
 * @deprecated
 *
 * @return AutomateWoo
 */
function AutomateWoo() {
	return AW();
}

/**
 * Access the plugin singleton with this.
 *
 * @return AutomateWoo
 */
function AW() {
	return AutomateWoo::instance();
}
