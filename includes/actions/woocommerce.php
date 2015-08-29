<?php 
/**
 * WooCommerce actions
 */

define( 'WOOCOMMERCE_CHECKOUT_ORDER_PROCESSED_ACTION', 'woocommerce_checkout_order_processed' );


function ub_init_woocommerce_actions( $ub_actions ) {

	$ub_actions[WOOCOMMERCE_CHECKOUT_ORDER_PROCESSED_ACTION] = array(
			'description' => __( 'Checkout order processed.', 'user-badges' ),
			'source' =>	__( 'WooCommerce', 'user-badges' )
	);

	return $ub_actions;
}
add_filter( 'ub_init_actions', 'ub_init_woocommerce_actions', 10, 1 );


/**
 * Adds WooCommerce actions
 *
 * @param actions
 */
function ub_add_woocommerce_actions( $actions = array() ) {

	$actions_enabled = (array) get_option( 'ub_actions_enabled' );

	if ( isset( $actions[WOOCOMMERCE_CHECKOUT_ORDER_PROCESSED_ACTION] ) && $actions[WOOCOMMERCE_CHECKOUT_ORDER_PROCESSED_ACTION]['enabled'] == true ) {
		add_action( 'woocommerce_checkout_order_processed',  'ub_woocommerce_checkout_order_processed', 10, 2 );
		add_filter( 'ub_condition_step_check_woocommerce_checkout_order_processed', 'ub_condition_step_check_count', 10, 4 );
	}

	add_filter('ub_step_meta_count_enabled', 'ub_woocommerce_step_meta_count_enabled', 10, 2 );
}
add_action( 'ub_init_actions_complete', 'ub_add_woocommerce_actions' );

/**
 * Sets whether step meta count is enabled
 *
 * @param unknown $enabled
 * @param unknown $action
 * @return boolean|unknown
 */
function ub_woocommerce_step_meta_count_enabled( $enabled, $action ) {

	if ( $action == WOOCOMMERCE_CHECKOUT_ORDER_PROCESSED_ACTION ) {
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
function ub_default_woocommerce_actions_enabled( $actions_enabled ) {

	return array_merge( array(
			WOOCOMMERCE_CHECKOUT_ORDER_PROCESSED_ACTION		=> false,
	), $actions_enabled );

}
add_filter( 'ub_default_actions_enabled', 'ub_default_woocommerce_actions_enabled', 10, 1 );


/**
 * Whenever a user performs a purchase
 * 
 * @param unknown $order_id
 * @param unknown $posted
 */
function ub_woocommerce_checkout_order_processed( $order_id, $posted ) {
	
	$order = new WC_Order( $order_id );
	$user_id = $order->get_user_id();
	
	// TODO total amount
	// TODO count items
	
	User_Badges::instance()->api->add_user_action( WOOCOMMERCE_CHECKOUT_ORDER_PROCESSED_ACTION, $user_id );
}
