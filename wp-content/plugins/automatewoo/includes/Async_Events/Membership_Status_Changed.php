<?php

namespace AutomateWoo\Async_Events;

use AutomateWoo\Events;

defined( 'ABSPATH' ) || exit;

/**
 * Class Membership_Status_Changed
 *
 * @since 4.8.0
 * @package AutomateWoo
 */
class Membership_Status_Changed extends Abstract_Async_Event {

	/**
	 * Init the event.
	 */
	public function init() {
		add_action( 'wc_memberships_user_membership_status_changed', [ $this, 'schedule_event' ], 30, 3 );
	}

	/**
	 * Schedule event.
	 *
	 * @param \WC_Memberships_User_Membership $membership The membership
	 * @param string                          $old_status Old status, without the wcm- prefix
	 * @param string                          $new_status New status, without the wcm- prefix
	 */
	public function schedule_event( $membership, $old_status, $new_status ) {
		Events::schedule_async_event(
			'automatewoo/membership_status_changed_async',
			[
				$membership->get_id(),
				$old_status,
				$new_status,
			]
		);
	}

}
