<?php
/**
 * Checks whether conditions have been met given a new action has been performed
 *
 * @param unknown $action_name
 * @param unknown $user_id
 */
function ub_check_conditions( $action_name, $user_id ) {

	global $wpdb;

	$query = 'SELECT cs.condition_id as condition_id FROM ' . $wpdb->prefix . UB_CONDITION_STEP_TABLE_NAME . ' cs, ' 
			. $wpdb->prefix . UB_CONDITION_TABLE_NAME . ' c WHERE cs.action_name = "' . esc_sql( $action_name ) 
			. '" AND c.condition_id = cs.condition_id AND c.enabled = 1 GROUP BY cs.condition_id';
	
	$conditions = $wpdb->get_col( $query );

	foreach ( $conditions as $condition_id ) {
		$condition = User_Badges::instance()->api->get_condition( $condition_id, false, true );
		$condition->check( $user_id );
	}

}
add_action( 'ub_check_conditions', 'ub_check_conditions', 10, 2 );
