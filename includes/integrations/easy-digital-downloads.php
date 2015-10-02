<?php 
/**
 * Easy Digital Downloads actions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'EDD_COMPLETE_DOWNLOAD_PURCHASE_ACTION', 'edd_complete_download_purchase' );

function broo_init_edd_actions( $broo_actions = array() ) {

	$broo_actions[EDD_COMPLETE_DOWNLOAD_PURCHASE_ACTION] = array(
			'description' => __( 'User completed purchase.', 'badgearoo' ),
			'source' =>	__( 'Easy Digital Downloads', 'badgearoo' )
	);

	return $broo_actions;
}
add_filter( 'broo_init_actions', 'broo_init_edd_actions', 10, 1 );


/**
 * Adds WooCommerce actions
 *
 * @param actions
*/
function broo_add_edd_actions( $actions = array() ) {

	if ( isset( $actions[EDD_COMPLETE_DOWNLOAD_PURCHASE_ACTION] ) && $actions[EDD_COMPLETE_DOWNLOAD_PURCHASE_ACTION]['enabled'] == true ) {
		add_action( 'edd_complete_download_purchase',  'broo_edd_complete_download_purchase', 10, 3 );
		add_filter( 'broo_condition_step_check_edd_complete_download_purchase', 'broo_condition_step_check_count', 10, 4 );
	}

	add_filter('broo_step_meta_count_enabled', 'broo_edd_step_meta_count_enabled', 10, 2 );
}
add_action( 'broo_init_actions_complete', 'broo_add_edd_actions' );

/**
 * Sets whether step meta count is enabled
 *
 * @param unknown $enabled
 * @param unknown $action
 * @return boolean|unknown
*/
function broo_edd_step_meta_count_enabled( $enabled, $action ) {

	if ( $action == EDD_COMPLETE_DOWNLOAD_PURCHASE_ACTION ) {
		return true;
	}

	return $enabled;
}


/**
 * Defaults actions enabled
 *
 * @param array $actions_enabled
 * @return $actions_enabled:
 */
function broo_default_edd_actions_enabled( $actions_enabled ) {

	return array_merge( array(
			EDD_COMPLETE_DOWNLOAD_PURCHASE_ACTION		=> true,
	), $actions_enabled );

}
add_filter( 'broo_default_actions_enabled', 'broo_default_edd_actions_enabled', 10, 1 );


/**
 * Whenever a user completes a download purchase
 *
 * @param unknown $order_id
 * @param unknown $posted
*/
function broo_edd_complete_download_purchase( $download_id, $payment_id, $download_type ) {

	// $download = edd_get_download( $download_id );

	$user_id = edd_get_payment_user_id( $payment_id );

	Badgearoo::instance()->api->add_user_action( EDD_COMPLETE_DOWNLOAD_PURCHASE_ACTION, $user_id );
}