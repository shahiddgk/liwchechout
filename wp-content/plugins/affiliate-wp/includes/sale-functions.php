<?php
/**
 * Sales functions
 *
 * @since 2.5
 */

/**
 * Retrieves a sales record.
 *
 * @since 2.5
 *
 * @param int|AffWP\Referral\Sale $referral referral ID or object.
 * @return \AffWP\Referral\Sale|false Commission object if it exists, otherwise false.
 */
function affwp_get_sale( $referral = null ) {

	if ( is_object( $referral ) && isset( $referral->referral_id ) ) {
		$referral_id = $referral->referral_id;
	} elseif ( is_numeric( $referral ) ) {
		$referral_id = absint( $referral );
	} else {
		return false;
	}

	return affiliate_wp()->referrals->sales->get_object( $referral_id );
}

/**
 * Gets the cache key for sync totals.
 *
 * @since 2.5
 *
 * @param string $context Context (integration) to retrieve the sales referrals count cache key.
 * @return string The sync totals cache key if `$context` is not empty, otherwise an empty string.
 */
function affwp_get_sales_referrals_counts_cache_key( $context ) {
	$key = '';

	if ( ! empty( $context ) ) {
		$key = "{$context}_sales_referrals_counts";
	}

	return $key;
}
