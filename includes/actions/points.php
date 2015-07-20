<?php 

// points actions
define( 'UB_MIN_POINTS_ACTION', 'ub_min_points' );

function ub_init_points_action( $ub_actions ) {

	$ub_actions[UB_MIN_POINTS_ACTION] = array(
			'description' => __( 'Minimum points.', 'user-badges' ),
			'source' =>	__( 'User Badges', 'user-badges' )
	);

	return $ub_actions;
}
add_filter( 'ub_init_actions', 'ub_init_points_action', 10, 1 );

/**
 * Adds points action
 *
 * @param actions
 */
function ub_add_points_action( $actions = array() ) {

	$actions_enabled = (array) get_option( 'ub_actions_enabled' );

	if ( isset( $actions[UB_MIN_POINTS_ACTION] ) && $actions[UB_MIN_POINTS_ACTION]['enabled'] == true ) {
		add_filter( 'ub_condition_step_check_ub_min_points', 'ub_condition_step_check_points', 10, 4 );
	}

	add_filter('ub_step_meta_points_enabled', 'ub_step_meta_points_enabled', 10, 2 );

}
add_action( 'ub_init_actions_complete', 'ub_add_points_action' );

/**
 * Sets whether step meta points is enabled for WP core actions
 *
 * @param unknown $enabled
 * @param unknown $action
 * @return boolean
*/
function ub_step_meta_points_enabled( $enabled, $action ) {

	if ( $action == UB_MIN_POINTS_ACTION  ) {
		return true;
	}

	return $enabled;
}

/**
 * Checks points for user
 *
 * @param unknown $step_result
 * @param unknown $step
 * @param int $user_id
 * @param string $action_name
 * @return unknown
 */
function ub_condition_step_check_points( $step_result, $step, $user_id, $action_name ) {

	if ( $step_result == false ) { // no need to continue
		return $step_result;
	}

	$value = User_Badges::instance()->api->get_step_meta_value( $step->step_id, 'points' );

	global $wpdb;

	$points = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $wpdb->prefix . UB_USER_ACTION_TABLE_NAME . ' WHERE action_name = "' . esc_sql( $action ) . '" AND user_id = ' . $user_id );

	if ( intval( $points ) < intval( $value ) ) {
		return false;
	}

	return $step_result;
}


/**
 * Defaults actions enabled
 *
 * @param array $actions_enabled
 * @return $actions_enabled:
 */
function ub_default_points_enabled( $actions_enabled ) {

	return array_merge( array(
			UB_MIN_POINTS_ACTION			=> true
	), $actions_enabled );

}
add_filter( 'ub_default_actions_enabled', 'ub_default_points_enabled', 10, 1 );

?>