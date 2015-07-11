<?php
global $ub_actions;

/**
 * When a post changes status, check if it's just been published
 * 
 * @param unknown $new_status
 * @param unknown $old_status
 * @param unknown $post
 */
function ub_transition_post_status( $new_status, $old_status, $post = null ) {
	
	if ( $post == null ) {
		return;
	}
	
	$post_type = $post->post_type;
	$post_types = get_post_types( array( 'public' => true ), 'names' );
	
	if ( in_array($post_type, $post_types) && $old_status != 'publish' && $new_status == 'publish' ) {
		
		// get user id
		$user_id = $post->post_author;
		
		User_Badges::instance()->api->add_user_action( UB_WP_PUBLISH_POST_ACTION, $user_id, array( 
				'post_type' => $post_type ,
				'post_id' => $post->ID 
		) );
	}
}
if ( isset( $ub_actions[UB_WP_PUBLISH_POST_ACTION] ) && $ub_actions[UB_WP_PUBLISH_POST_ACTION]['enabled'] == true ) {
	add_action( 'transition_post_status',  'ub_transition_post_status', 10, 3 );
}

/**
 * When a comment is posted
 * 
 * @param unknown $comment_id
 * @param unknown $comment_approved
 */
function ub_submit_comment( $comment_id, $comment_approved = null) {
	$comment = get_comment( $comment_id );
	
	$user_id = $comment->user_id;
	
	if ( $user_id != 0 ) {
		User_Badges::instance()->api->add_user_action( UB_WP_SUBMIT_COMMENT_ACTION, $user_id, array( 'comment_id' => $comment_id ) );
		// is the plugin initiated?
	}
}
if ( isset( $ub_actions[UB_WP_SUBMIT_COMMENT_ACTION] ) && $ub_actions[UB_WP_SUBMIT_COMMENT_ACTION]['enabled'] == true ) {
	add_action( 'comment_post', 'ub_submit_comment', 2 );
}

/**
 * Whenever a user logs in
 * 
 * @param unknown $user_login
 * @param unknown $user
 */
function ub_user_login( $user_login, $user ) {
	User_Badges::instance()->api->add_user_action( UB_WP_REGISTER_ACTION, $user->ID );
}
if ( isset( $ub_actions[UB_WP_LOGIN_ACTION] ) && $ub_actions[UB_WP_LOGIN_ACTION]['enabled'] == true ) {
	add_action( 'wp_login', 'ub_user_login', 2 );
}

/**
 * Whenever a user registers
 * @param unknown $user_id
 */
function ub_user_register( $user_id ) {
	User_Badges::instance()->api->add_user_action( UB_WP_REGISTER_ACTION, $user_id );
}
if ( isset( $ub_actions[UB_WP_REGISTER_ACTION] ) && $ub_actions[UB_WP_REGISTER_ACTION]['enabled'] == true ) {
	add_action( 'user_register', 'ub_user_register', 1 );
}



/*
 * bbPress
 * - create X number of topics
 * - has bbPress moderator user role
 * - closed X number of topic
 * - replied to a topic X times
 * 
 * Show badges & points underneath user profile
 */

/**
 * Checks count for user actions
 *
 * @param unknown $step_result
 * @param unknown $step
 * @param int $user_id
 * @param string $action_name
 * @return boolean
 */
function ub_condition_step_check_count( $step_result, $step, $user_id, $action_name ) {

	if ( $step_result == false ) { // no need to continue
		return $step_result;
	}

	$meta_count = User_Badges::instance()->api->get_step_meta_value( $step->step_id, 'count' );
	
	global $wpdb;
	$query = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . UB_USER_ACTION_TABLE_NAME . ' WHERE action_name = "' 
			. esc_sql( $step->action_name ) . '" and user_id = ' . $user_id;
	
	$db_count = $wpdb->get_var( $query );

	if ( intval( $db_count ) < intval( $meta_count ) ) {
		return false;
	}

	return $step_result;
}

/**
 * Checks count for the WP publish post user action
 *
 * @param unknown $step_result
 * @param unknown $step
 * @param int $user_id
 * @param string $action_name
 * @return boolean
 */
function ub_condition_step_check_publish_post( $step_result, $step, $user_id, $action_name ) {
	
	if ( $step_result == false ) { // no need to continue
		return $step_result;
	}
	
	$meta_count = User_Badges::instance()->api->get_step_meta_value( $step->step_id, 'count' );
	$meta_post_type = User_Badges::instance()->api->get_step_meta_value( $step->step_id, 'post_type' );
	
	global $wpdb;
	$query = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . UB_USER_ACTION_TABLE_NAME 
			. ' ua INNER JOIN ' . $wpdb->prefix . UB_USER_ACTION_META_TABLE_NAME 
			. ' uam ON uam.user_action_id = ua.id WHERE ua.action_name = "'
			. esc_sql( $action_name ) . '" AND ua.user_id = ' . $user_id 
			. ' AND uam.meta_key = "post_type" AND uam.meta_value = "' . esc_sql( $meta_post_type ) . '"';
	
	$db_count = $wpdb->get_var( $query );
	
	if ( intval( $db_count ) < intval( $meta_count ) ) {
		return false;
	}
	
	return $step_result;
}

add_filter( 'ub_condition_step_check_wp_submit_comment', 'ub_condition_step_check_count', 10, 4 );
add_filter( 'ub_condition_step_check_wp_login', 'ub_condition_step_check_count', 10, 4 );
add_filter( 'ub_condition_step_check_wp_publish_post', 'ub_condition_step_check_publish_post', 10, 4 );
add_filter( 'ub_condition_step_check_wp_register', 'ub_condition_step_check_count', 10, 4 );

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
add_filter( 'ub_condition_step_check_ub_min_points', 'ub_condition_step_check_points', 10, 4 );


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
