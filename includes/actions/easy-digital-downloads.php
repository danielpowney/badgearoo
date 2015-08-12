<?php 
/**
 * Easy Digital Downloads actions
 */

define( 'EDD_COMPLETE_DOWNLOAD_PURCHASE_ACTION', 'edd_complete_download_purchase' );

function ub_init_edd_actions( $ub_actions ) {

	$ub_actions[EDD_COMPLETE_DOWNLOAD_PURCHASE_ACTION] = array(
			'description' => __( 'User completed purchase.', 'user-badges' ),
			'source' =>	__( 'Easy Digital Downloads', 'user-badges' )
	);

	return $ub_actions;
}
add_filter( 'ub_init_actions', 'ub_init_edd_actions', 10, 1 );


/**
 * Adds WooCommerce actions
 *
 * @param actions
*/
function ub_add_edd_actions( $actions = array() ) {

	$actions_enabled = (array) get_option( 'ub_actions_enabled' );

	if ( isset( $actions[EDD_COMPLETE_DOWNLOAD_PURCHASE_ACTION] ) && $actions[EDD_COMPLETE_DOWNLOAD_PURCHASE_ACTION]['enabled'] == true ) {
		add_action( 'edd_complete_download_purchase',  'ub_edd_complete_download_purchase', 10, 3 );
		add_filter( 'ub_condition_step_check_edd_complete_download_purchase', 'ub_condition_step_check_count', 10, 4 );
	}

	add_filter('ub_step_meta_count_enabled', 'ub_edd_step_meta_count_enabled', 10, 2 );
}
add_action( 'ub_init_actions_complete', 'ub_add_edd_actions' );

/**
 * Sets whether step meta count is enabled
 *
 * @param unknown $enabled
 * @param unknown $action
 * @return boolean|unknown
*/
function ub_edd_step_meta_count_enabled( $enabled, $action ) {

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
function ub_default_edd_actions_enabled( $actions_enabled ) {

	return array_merge( array(
			EDD_COMPLETE_DOWNLOAD_PURCHASE_ACTION		=> false,
	), $actions_enabled );

}
add_filter( 'ub_default_actions_enabled', 'ub_default_edd_actions_enabled', 10, 1 );


/**
 * Whenever a user completes a download purchase
 *
 * @param unknown $order_id
 * @param unknown $posted
*/
function ub_edd_complete_download_purchase( $download_id, $payment_id, $download_type ) {

	// $download = edd_get_download( $download_id );

	$user_id = edd_get_payment_user_id( $payment_id );

	User_Badges::instance()->api->add_user_action( EDD_COMPLETE_DOWNLOAD_PURCHASE_ACTION, $user_id );
}