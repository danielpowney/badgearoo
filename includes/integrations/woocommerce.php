<?php 
/**
 * WooCommerce actions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'WOOCOMMERCE_CHECKOUT_ORDER_PROCESSED_ACTION', 'woocommerce_checkout_order_processed' );


function broo_init_woocommerce_actions( $broo_actions = array() ) {

	$broo_actions[WOOCOMMERCE_CHECKOUT_ORDER_PROCESSED_ACTION] = array(
			'description' => __( 'Checkout order processed.', 'badgearoo' ),
			'source' =>	__( 'WooCommerce', 'badgearoo' )
	);

	return $broo_actions;
}
add_filter( 'broo_init_actions', 'broo_init_woocommerce_actions', 10, 1 );


/**
 * Adds WooCommerce actions
 *
 * @param actions
 */
function broo_add_woocommerce_actions( $actions = array() ) {

	if ( isset( $actions[WOOCOMMERCE_CHECKOUT_ORDER_PROCESSED_ACTION] ) && $actions[WOOCOMMERCE_CHECKOUT_ORDER_PROCESSED_ACTION]['enabled'] == true ) {
		add_action( 'woocommerce_checkout_order_processed',  'broo_woocommerce_checkout_order_processed', 10, 2 );
		add_filter( 'broo_condition_step_check_woocommerce_checkout_order_processed', 'broo_condition_step_check_count', 10, 4 );
	}

	add_filter('broo_step_meta_count_enabled', 'broo_woocommerce_step_meta_count_enabled', 10, 2 );
}
add_action( 'broo_init_actions_complete', 'broo_add_woocommerce_actions' );

/**
 * Sets whether step meta count is enabled
 *
 * @param unknown $enabled
 * @param unknown $action
 * @return boolean|unknown
 */
function broo_woocommerce_step_meta_count_enabled( $enabled, $action ) {

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
function broo_default_woocommerce_actions_enabled( $actions_enabled ) {

	return array_merge( array(
			WOOCOMMERCE_CHECKOUT_ORDER_PROCESSED_ACTION		=> true,
	), $actions_enabled );

}
add_filter( 'broo_default_actions_enabled', 'broo_default_woocommerce_actions_enabled', 10, 1 );


/**
 * Whenever a user performs a purchase
 * 
 * @param unknown $order_id
 * @param unknown $posted
 */
function broo_woocommerce_checkout_order_processed( $order_id, $posted ) {
	
	$order = new WC_Order( $order_id );
	$user_id = $order->get_user_id();
	
	// TODO total amount
	// TODO count items
	
	Badgearoo::instance()->api->add_user_action( WOOCOMMERCE_CHECKOUT_ORDER_PROCESSED_ACTION, $user_id, array(
			'order_id' => $order_id
	) );
}
