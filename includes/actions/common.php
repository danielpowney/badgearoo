<?php
/**
 * WordPress predefined actions
 */

define( 'UB_WP_PUBLISH_POST_ACTION', 'wp_publish_post' );
define( 'UB_WP_SUBMIT_COMMENT_ACTION', 'wp_submit_comment' );
define( 'UB_WP_LOGIN_ACTION', 'wp_login' );
define( 'UB_WP_REGISTER_ACTION', 'wp_register' );
define( 'UB_WP_PROFILE_UPDATE_ACTION', 'wp_profile_update' );

// Non WordPress
define( 'UB_MIN_POINTS_ACTION', 'ub_min_points' );

function ub_init_common_actions( $ub_actions ) {
	
	$ub_actions[UB_WP_PUBLISH_POST_ACTION] = array(
			'description' => __( 'User publishes a post.', 'user-badges' ),
			'source' =>	__( 'WordPress', 'user-badges' )
	);
	
	$ub_actions[UB_WP_SUBMIT_COMMENT_ACTION] = array(
			'description' => __( 'User submits a comment.', 'user-badges' ),
			'source' =>	__( 'WordPress', 'user-badges' )
	);
	
	$ub_actions[UB_WP_LOGIN_ACTION] = array(
			'description' => __( 'User logs in.', 'user-badges' ),
			'source' =>	__( 'WordPress', 'user-badges' )
	);
	
	$ub_actions[UB_WP_REGISTER_ACTION] = array(
			'description' => __( 'Register user.', 'user-badges' ),
			'source' =>	__( 'WordPress', 'user-badges' )
	);
	
	$ub_actions[UB_WP_PROFILE_UPDATE_ACTION] = array(
			'description' => __( 'User updates their profile.' ),
			'source' => __( 'WordPress')
	);
	
	$ub_actions[UB_MIN_POINTS_ACTION] = array(
			'description' => __( 'Minimum points.', 'user-badges' ),
			'source' =>	__( 'User Badges', 'user-badges' )
	);
	
	return $ub_actions;
}
add_filter( 'ub_init_actions', 'ub_init_common_actions', 10, 1 );


/**
 * Adds WP Core actions
 * 
 * @param actions
 */
function ub_add_common_actions( $actions = array() ) {
	
	$actions_enabled = (array) get_option( 'ub_actions_enabled' );
	
	if ( isset( $actions[UB_WP_PUBLISH_POST_ACTION] ) && $actions[UB_WP_PUBLISH_POST_ACTION]['enabled'] == true ) {
		add_action( 'transition_post_status',  'ub_transition_post_status', 10, 3 );
		add_filter( 'ub_condition_step_check_wp_publish_post', 'ub_condition_step_check_publish_post', 10, 4 );
	}
	
	if ( isset( $actions[UB_WP_SUBMIT_COMMENT_ACTION] ) && $actions[UB_WP_SUBMIT_COMMENT_ACTION]['enabled'] == true ) {
		add_action( 'comment_post', 'ub_submit_comment', 10, 2 );
		add_filter( 'ub_condition_step_check_wp_submit_comment', 'ub_condition_step_check_count', 10, 4 );
	}
	
	if ( isset( $actions[UB_WP_LOGIN_ACTION] ) && $actions[UB_WP_LOGIN_ACTION]['enabled'] == true ) {
		add_action( 'wp_login', 'ub_user_login', 10, 2 );
		add_filter( 'ub_condition_step_check_wp_login', 'ub_condition_step_check_count', 10, 4 );
	}
	
	if ( isset( $actions[UB_WP_REGISTER_ACTION] ) && $actions[UB_WP_REGISTER_ACTION]['enabled'] == true ) {
		add_action( 'wp_register', 'ub_wp_register', 10, 1 );
		add_filter( 'ub_condition_step_check_wp_register', 'ub_condition_step_check_count', 10, 4 );
	}
	
	if ( isset( $actions[UB_WP_PROFILE_UPDATE_ACTION] ) && $actions[UB_WP_PROFILE_UPDATE_ACTION]['enabled'] == true ) {
		add_action( 'profile_update', 'ub_wp_profile_update', 10, 2 );
		add_filter( 'ub_condition_step_check_wp_profile_update', 'ub_condition_step_check_count', 10, 4 );
	}
	
	if ( isset( $actions[UB_MIN_POINTS_ACTION] ) && $actions[UB_MIN_POINTS_ACTION]['enabled'] == true ) {
		add_filter( 'ub_condition_step_check_ub_min_points', 'ub_condition_step_check_points', 10, 4 );
	}
	
	
	add_filter('ub_step_meta_count_enabled', 'ub_step_meta_count_enabled', 10, 2 );
	add_filter('ub_step_meta_post_type_enabled', 'ub_step_meta_post_type_enabled', 10, 2 );
	add_filter('ub_step_meta_points_enabled', 'ub_step_meta_points_enabled', 10, 2 );
}
add_action( 'ub_init_actions_complete', 'ub_add_common_actions' );


/**
 * Sets whether step meta count is enabled
 * 
 * @param unknown $enabled
 * @param unknown $action
 * @return boolean|unknown
 */
function ub_step_meta_count_enabled( $enabled, $action ) {
	
	if ( $action == UB_WP_LOGIN_ACTION || $action == UB_WP_PUBLISH_POST_ACTION 
			|| $action == UB_WP_SUBMIT_COMMENT_ACTION || $action == UB_WP_PROFILE_UPDATE_ACTION ) {
		return true;
	}
	
	return $enabled;
}


/**
 * Sets whether step meta post type is enabled
 *
 * @param unknown $enabled
 * @param unknown $action
 * @return boolean|unknown
 */
function ub_step_meta_post_type_enabled( $enabled, $action ) {
	
	if ( $action == UB_WP_PUBLISH_POST_ACTION ) {
		return true;
	}
	
	return $enabled;
}


/**
 * Sets whether step meta points is enabled
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
 * Shows a points step meta
 *
 * @param unknown $step_id
 * @param unknown $action
 */
function ub_step_meta_points( $step_id, $action  ) {

	$step_meta_enabled = apply_filters( 'ub_step_meta_points_enabled', false, $action );

	if ( $step_meta_enabled ) {
		$points = User_Badges::instance()->api->get_step_meta_value( $step_id, 'points' );
		?>
		<span class="step-meta-value">
			<input name="points" type="number" value="<?php echo $points; ?>" class="small-text" />&nbsp;<?php _e( 'points', 'user-badges' ); ?>
		</span>
		<?php
	}
}
add_action( 'ub_step_meta', 'ub_step_meta_points', 10, 2 );


/**
 * Shows a count step meta
 *
 * @param unknown $step_id
 * @param unknown $action
 */
function ub_step_meta_count( $step_id, $action  ) {

	$step_meta_enabled = apply_filters( 'ub_step_meta_count_enabled', false, $action );

	if ( $step_meta_enabled ) {
		$count = User_Badges::instance()->api->get_step_meta_value( $step_id, 'count' );
		?>
		<span class="step-meta-value">
			<input name="count" type="number" value="<?php echo $count; ?>" class="small-text" />&nbsp;<?php _e( 'time(s)', 'user-badges' ); ?>
		</span>
		<?php
	}
}
add_action( 'ub_step_meta', 'ub_step_meta_count', 10, 2 );


/**
 * Shows a points step meta
 *
 * @param unknown $step_id
 * @param unknown $action
 */
function ub_step_meta_post_type( $step_id, $action  ) {

	$step_meta_enabled = apply_filters( 'ub_step_meta_post_type_enabled', false, $action );

	if ( $step_meta_enabled ) {
		$value = User_Badges::instance()->api->get_step_meta_value( $step_id, 'post_type' );
		?>
		<span class="step-meta-value">
			<label for="post_type"><?php _e( 'Post Type', 'user-badges' ); ?></label>
			<select name="post_type">
				<option value=""><?php _e( 'All', 'user-badges'); ?></option>
				<?php 
				$post_types = get_post_types( array( 'public' => true ), 'objects' );
				foreach ( $post_types as $post_type ) {
					?><option value="<?php echo $post_type->name; ?>" <?php if ( $post_type->name == $value ) { echo 'selected'; } ?>><?php echo $post_type->labels->name; ?></option><?php
				} ?>
			</select>
		</span>
		<?php
	}
}
add_action( 'ub_step_meta', 'ub_step_meta_post_type', 10, 2 );


/**
 * Defaults actions enabled
 * 
 * @param array $actions_enabled
 * @return $actions_enabled:
 */
function ub_default_common_actions_enabled( $actions_enabled ) {
	
	return array_merge( array(
			UB_WP_PUBLISH_POST_ACTION			=> true,
			UB_WP_SUBMIT_COMMENT_ACTION			=> true,
			UB_WP_LOGIN_ACTION					=> false,
			UB_WP_REGISTER_ACTION				=> false,
			UB_MIN_POINTS_ACTION				=> true,
			UB_WP_PROFILE_UPDATE_ACTION			=> false
	), $actions_enabled );

}
add_filter( 'ub_default_actions_enabled', 'ub_default_common_actions_enabled', 10, 1 );

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
function ub_wp_register( $user_id ) {
	User_Badges::instance()->api->add_user_action( UB_WP_REGISTER_ACTION, $user_id );
}



/**
 * Whenever a user updates their profile
 * 
 * @param unknown $meta_id
 * @param unknown $user_id
 * @param unknown $meta_key
 * @param unknown $meta_value
 */
function ub_wp_profile_update( $user_id, $old_user_data ) {
	User_Badges::instance()->api->add_user_action( UB_WP_PROFILE_UPDATE_ACTION, $user_id, array() );
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