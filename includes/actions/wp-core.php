<?php

// WordPress predefined actions
define( 'UB_WP_PUBLISH_POST_ACTION', 'wp_publish_post' );
define( 'UB_WP_SUBMIT_COMMENT_ACTION', 'wp_submit_comment' );
define( 'UB_WP_LOGIN_ACTION', 'wp_login' );
define( 'UB_WP_REGISTER_ACTION', 'wp_register' );

function ub_init_wp_core_actions( $ub_actions ) {
	
	$ub_actions[UB_WP_PUBLISH_POST_ACTION] = array(
			'description' => __( 'User publishes a post.', 'user-badges' ),
			'source' =>	__( 'Wordpress', 'user-badges' )
	);
	
	$ub_actions[UB_WP_SUBMIT_COMMENT_ACTION] = array(
			'description' => __( 'User submits a comment.', 'user-badges' ),
			'source' =>	__( 'Wordpress', 'user-badges' )
	);
	
	$ub_actions[UB_WP_LOGIN_ACTION] = array(
			'description' => __( 'User logs in.', 'user-badges' ),
			'source' =>	__( 'Wordpress', 'user-badges' )
	);
	
	$ub_actions[UB_WP_REGISTER_ACTION] = array(
			'description' => __( 'Register user.', 'user-badges' ),
			'source' =>	__( 'Wordpress', 'user-badges' )
	);
	
	return $ub_actions;
}
add_filter( 'ub_init_actions', 'ub_init_wp_core_actions', 10, 1 );

/**
 * Adds WP Core actions
 * 
 * @param actions
 */
function ub_add_wp_core_actions( $actions = array() ) {
	
	$actions_enabled = (array) get_option( 'ub_actions_enabled' );
	
	if ( isset( $actions[UB_WP_PUBLISH_POST_ACTION] ) && $actions[UB_WP_PUBLISH_POST_ACTION]['enabled'] == true ) {
		add_action( 'transition_post_status',  'ub_transition_post_status', 10, 3 );
		add_filter( 'ub_condition_step_check_wp_publish_post', 'ub_condition_step_check_publish_post', 10, 4 );
	}
	
	if ( isset( $actions[UB_WP_SUBMIT_COMMENT_ACTION] ) && $actions[UB_WP_SUBMIT_COMMENT_ACTION]['enabled'] == true ) {
		add_action( 'comment_post', 'ub_submit_comment', 2 );
		add_filter( 'ub_condition_step_check_wp_submit_comment', 'ub_condition_step_check_count', 10, 4 );
	}
	
	if ( isset( $actions[UB_WP_LOGIN_ACTION] ) && $actions[UB_WP_LOGIN_ACTION]['enabled'] == true ) {
		add_action( 'wp_login', 'ub_user_login', 2 );
		add_filter( 'ub_condition_step_check_wp_login', 'ub_condition_step_check_count', 10, 4 );
	}
	
	if ( isset( $actions[UB_WP_REGISTER_ACTION] ) && $actions[UB_WP_REGISTER_ACTION]['enabled'] == true ) {
		add_action( 'user_register', 'ub_user_register', 1 );
		add_filter( 'ub_condition_step_check_wp_register', 'ub_condition_step_check_count', 10, 4 );
	}
	
	add_filter('ub_step_meta_count_enabled', 'ub_step_meta_count_enabled_wp_core', 10, 2 );
	add_filter('ub_step_meta_post_type_enabled', 'ub_step_meta_post_type_enabled_wp_core', 10, 2 );
	
}
add_action( 'ub_init_actions_complete', 'ub_add_wp_core_actions' );


/**
 * Sets whether step meta count is enabled for WP core actions
 * 
 * @param unknown $enabled
 * @param unknown $action
 * @return boolean|unknown
 */
function ub_step_meta_count_enabled_wp_core( $enabled, $action ) {
	
	if ( $action == UB_WP_LOGIN_ACTION || $action == UB_WP_PUBLISH_POST_ACTION || $action == UB_WP_SUBMIT_COMMENT_ACTION ) {
		return true;
	}
	
	return $enabled;
}

/**
 * Sets whether step meta post type is enabled for WP core actions
 *
 * @param unknown $enabled
 * @param unknown $action
 * @return boolean|unknown
 */
function ub_step_meta_post_type_enabled_wp_core( $enabled, $action ) {
	
	if ( $action == UB_WP_PUBLISH_POST_ACTION ) {
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
function ub_default_wp_core_actions_enabled( $actions_enabled ) {
	
	return array_merge( array(
			UB_WP_PUBLISH_POST_ACTION			=> true,
			UB_WP_SUBMIT_COMMENT_ACTION			=> true,
			UB_WP_LOGIN_ACTION					=> false,
			UB_WP_REGISTER_ACTION				=> false,
	), $actions_enabled );

}
add_filter( 'ub_default_actions_enabled', 'ub_default_wp_core_actions_enabled', 10, 1 );

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


/**
 * Whenever a user logs in
 *
 * @param unknown $user_login
 * @param unknown $user
 */
function ub_user_login( $user_login, $user ) {
	User_Badges::instance()->api->add_user_action( UB_WP_REGISTER_ACTION, $user->ID );
}


/**
 * Whenever a user registers
 * @param unknown $user_id
 */
function ub_user_register( $user_id ) {
	User_Badges::instance()->api->add_user_action( UB_WP_REGISTER_ACTION, $user_id );
}


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
			. ' AND uam.meta_key = "post_type"';

	if ( $meta_post_type && strlen( trim( $meta_post_type ) ) > 0 ) {
		$query .= ' AND uam.meta_value = "' . esc_sql( $meta_post_type ) . '"';
	}

	$db_count = $wpdb->get_var( $query );

	if ( intval( $db_count ) < intval( $meta_count ) ) {
		return false;
	}

	return $step_result;
}