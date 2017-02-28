<?php
/**
 * WordPress predefined actions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'BROO_WP_PUBLISH_POST_ACTION', 'wp_publish_post' );
define( 'BROO_WP_SUBMIT_COMMENT_ACTION', 'wp_submit_comment' );
define( 'BROO_WP_LOGIN_ACTION', 'wp_login' );
define( 'BROO_WP_USER_REGISTER_ACTION', 'user_register' );
define( 'BROO_WP_PROFILE_UPDATE_ACTION', 'wp_profile_update' );
define( 'BROO_VIEW_POST_ACTION', 'broo_view_post' );
define( 'BROO_EDIT_POST_ACTION', 'broo_edit_post' );

// Non WordPress
define( 'BROO_MIN_POINTS_ACTION', 'broo_min_points' );

function broo_init_common_actions( $broo_actions = array() ) {
	
	$broo_actions[BROO_WP_PUBLISH_POST_ACTION] = array(
			'description' => __( 'User publishes a post.', 'badgearoo' ),
			'source' =>	__( 'WordPress', 'badgearoo' )
	);
	
	$broo_actions[BROO_WP_SUBMIT_COMMENT_ACTION] = array(
			'description' => __( 'User submits a comment.', 'badgearoo' ),
			'source' =>	__( 'WordPress', 'badgearoo' )
	);
	
	$broo_actions[BROO_WP_LOGIN_ACTION] = array(
			'description' => __( 'User logs in.', 'badgearoo' ),
			'source' =>	__( 'WordPress', 'badgearoo' )
	);
	
	$broo_actions[BROO_WP_USER_REGISTER_ACTION] = array(
			'description' => __( 'Register user.', 'badgearoo' ),
			'source' =>	__( 'WordPress', 'badgearoo' )
	);
	
	$broo_actions[BROO_WP_PROFILE_UPDATE_ACTION] = array(
			'description' => __( 'User updates their profile.' ),
			'source' => __( 'WordPress')
	);
	
	$broo_actions[BROO_MIN_POINTS_ACTION] = array(
			'description' => __( 'Minimum points.', 'badgearoo' ),
			'source' =>	__( 'Custom', 'badgearoo' )
	);
	
	$broo_actions[BROO_VIEW_POST_ACTION] = array( 
			'description' => __( 'Views post.', 'badgearoo' ),
			'source' => __( 'Custom', 'badgearoo' )
	);
	
	$broo_actions[BROO_EDIT_POST_ACTION] = array(
			'description' => __( 'User edits a post.', 'badgearoo' ),
			'source' => __( 'WordPress', 'badgearoo' )
	);
	
	return $broo_actions;
}
add_filter( 'broo_init_actions', 'broo_init_common_actions', 10, 1 );


/**
 * Adds WP Core actions
 * 
 * @param actions
 */
function broo_add_common_actions( $actions = array() ) {
		
	if ( isset( $actions[BROO_WP_PUBLISH_POST_ACTION] ) && $actions[BROO_WP_PUBLISH_POST_ACTION]['enabled'] == true ) {
		add_action( 'transition_post_status',  'broo_transition_post_status', 10, 3 );
		add_filter( 'broo_condition_step_check_wp_publish_post', 'broo_condition_step_check_publish_post', 10, 4 );
	}
	
	if ( isset( $actions[BROO_WP_SUBMIT_COMMENT_ACTION] ) && $actions[BROO_WP_SUBMIT_COMMENT_ACTION]['enabled'] == true ) {
		add_action( 'comment_post', 'broo_submit_comment', 10, 2 );
		add_filter( 'broo_condition_step_check_wp_submit_comment', 'broo_condition_step_check_count', 10, 4 );
	}
	
	if ( isset( $actions[BROO_WP_LOGIN_ACTION] ) && $actions[BROO_WP_LOGIN_ACTION]['enabled'] == true ) {
		add_action( 'wp_login', 'broo_user_login', 10, 2 );
		add_filter( 'broo_condition_step_check_wp_login', 'broo_condition_step_check_count', 10, 4 );
	}
	
	if ( isset( $actions[BROO_WP_USER_REGISTER_ACTION] ) && $actions[BROO_WP_USER_REGISTER_ACTION]['enabled'] == true ) {
		add_action( 'user_register', 'broo_user_register', 10, 1 );
		add_filter( 'broo_condition_step_check_user_register', 'broo_condition_step_check_once', 10, 4 );
	}
	
	if ( isset( $actions[BROO_WP_PROFILE_UPDATE_ACTION] ) && $actions[BROO_WP_PROFILE_UPDATE_ACTION]['enabled'] == true ) {
		add_action( 'profile_update', 'broo_wp_profile_update', 10, 2 );
		add_filter( 'broo_condition_step_check_wp_profile_update', 'broo_condition_step_check_count', 10, 4 );
	}
	
	if ( isset( $actions[BROO_MIN_POINTS_ACTION] ) && $actions[BROO_MIN_POINTS_ACTION]['enabled'] == true ) {
		add_filter( 'broo_condition_step_check_broo_min_points', 'broo_condition_step_check_points', 10, 4 );
	}
	
	if ( isset( $actions[BROO_VIEW_POST_ACTION] ) && $actions[BROO_VIEW_POST_ACTION]['enabled'] == true ) {
		add_action( 'wp_head', 'broo_view_post', 10, 0 );
		add_filter( 'broo_condition_step_check_broo_view_post', 'broo_condition_step_check_count', 10, 4 );
		
		// TODO add step meta: unique, post type, post id...
	}
	
	if ( isset( $actions[BROO_EDIT_POST_ACTION] ) && $actions[BROO_EDIT_POST_ACTION]['enabled'] == true ) {
		add_action( 'edit_post', 'broo_edit_post', 10, 3 );
		add_filter( 'broo_condition_step_check_edit_post', 'broo_condition_step_check_count', 10, 4 );
	}
	
	
	add_filter('broo_step_meta_count_enabled', 'broo_step_meta_count_enabled', 10, 2 );
	add_filter('broo_step_meta_post_type_enabled', 'broo_step_meta_post_type_enabled', 10, 2 );
	add_filter('broo_step_meta_points_enabled', 'broo_step_meta_points_enabled', 10, 2 );
}
add_action( 'broo_init_actions_complete', 'broo_add_common_actions' );


/**
 * Sets whether step meta count is enabled
 * 
 * @param unknown $enabled
 * @param unknown $action
 * @return boolean|unknown
 */
function broo_step_meta_count_enabled( $enabled, $action ) {
	
	if ( $action == BROO_WP_LOGIN_ACTION || $action == BROO_WP_PUBLISH_POST_ACTION 
			|| $action == BROO_WP_SUBMIT_COMMENT_ACTION || $action == BROO_WP_PROFILE_UPDATE_ACTION 
			|| $action == BROO_VIEW_POST_ACTION || $action == BROO_EDIT_POST_ACTION ) {
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
function broo_step_meta_post_type_enabled( $enabled, $action ) {
	
	if ( $action == BROO_WP_PUBLISH_POST_ACTION || $action == BROO_EDIT_POST_ACTION ) {
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
function broo_step_meta_points_enabled( $enabled, $action ) {

	if ( $action == BROO_MIN_POINTS_ACTION  ) {
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
function broo_step_meta_points( $step_id, $action  ) {

	$step_meta_enabled = apply_filters( 'broo_step_meta_points_enabled', false, $action );

	if ( $step_meta_enabled ) {
		$points = Badgearoo::instance()->api->get_step_meta_value( $step_id, 'points' );
		?>
		<span class="step-meta-value">
			<input name="points" type="number" value="<?php echo $points; ?>" class="small-text" />&nbsp;<?php _e( 'points', 'badgearoo' ); ?>
		</span>
		<?php
	}
}
add_action( 'broo_step_meta', 'broo_step_meta_points', 10, 2 );


/**
 * Shows a count step meta
 *
 * @param unknown $step_id
 * @param unknown $action
 */
function broo_step_meta_count( $step_id, $action  ) {

	$step_meta_enabled = apply_filters( 'broo_step_meta_count_enabled', false, $action );

	if ( $step_meta_enabled ) {
		$count = Badgearoo::instance()->api->get_step_meta_value( $step_id, 'count' );
		
		if ( $count == null || ! is_numeric( $count ) ) {
			$count = 1;
		}
		?>
		<span class="step-meta-value">
			<input name="count" type="number" value="<?php echo $count; ?>" class="small-text" />&nbsp;<?php _e( 'time(s)', 'badgearoo' ); ?>
		</span>
		<?php
	}
}
add_action( 'broo_step_meta', 'broo_step_meta_count', 10, 2 );


/**
 * Shows a points step meta
 *
 * @param unknown $step_id
 * @param unknown $action
 */
function broo_step_meta_post_type( $step_id, $action  ) {

	$step_meta_enabled = apply_filters( 'broo_step_meta_post_type_enabled', false, $action );

	if ( $step_meta_enabled ) {
		$value = Badgearoo::instance()->api->get_step_meta_value( $step_id, 'post_type' );
		?>
		<span class="step-meta-value">
			<label for="post_type"><?php _e( 'Post Type', 'badgearoo' ); ?></label>
			<select name="post_type">
				<option value=""><?php _e( 'All', 'badgearoo'); ?></option>
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
add_action( 'broo_step_meta', 'broo_step_meta_post_type', 10, 2 );


/**
 * Defaults actions enabled
 * 
 * @param array $actions_enabled
 * @return $actions_enabled:
 */
function broo_default_common_actions_enabled( $actions_enabled ) {
	
	return array_merge( array(
			BROO_WP_PUBLISH_POST_ACTION				=> true,
			BROO_WP_SUBMIT_COMMENT_ACTION			=> true,
			BROO_WP_LOGIN_ACTION					=> true,
			BROO_WP_USER_REGISTER_ACTION			=> true,
			BROO_MIN_POINTS_ACTION					=> true,
			BROO_WP_PROFILE_UPDATE_ACTION			=> true,
			BROO_VIEW_POST_ACTION					=> true,
			BROO_EDIT_POST_ACTION					=> true
	), $actions_enabled );

}
add_filter( 'broo_default_actions_enabled', 'broo_default_common_actions_enabled', 10, 1 );

/**
 * When a post changes status, check if it's just been published
 *
 * @param unknown $new_status
 * @param unknown $old_status
 * @param unknown $post
 */
function broo_transition_post_status( $new_status, $old_status, $post = null ) {

	if ( $post == null ) {
		return;
	}

	$post_type = $post->post_type;
	$post_types = get_post_types( array( 'public' => true ), 'names' );

	if ( in_array( $post_type, $post_types ) && $old_status != 'publish' && $new_status == 'publish' ) {

		// get user id
		$user_id = $post->post_author;

		Badgearoo::instance()->api->add_user_action( BROO_WP_PUBLISH_POST_ACTION, $user_id, array(
				'post_type' => $post_type ,
				'post_id' => $post->ID
		) );
		
	}
}


/**
 * When a post updates
 *
 * @param unknown $post_ID
 * @param unknown $post
 */
function broo_edit_post( $post_ID, $post ) {
	
	if ( $post == null ) {
		return;
	}
	
	// Autosave, do nothing
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// AJAX? Not used here
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
 		return;
	}
	// Check user permissions
	if ( ! current_user_can( 'edit_post', $post_ID ) ) {
		return;
	}
	// Return if it's a post revision
	if ( false !== wp_is_post_revision( $post_ID ) ) {
		return;
	}

	$post_type = $post->post_type;
	$post_types = get_post_types( array( 'public' => true ), 'names' );

	if ( in_array( $post_type, $post_types ) ) {

		$user_id = get_current_user_id();

		Badgearoo::instance()->api->add_user_action( BROO_EDIT_POST_ACTION, $user_id, array(
				'post_type' => $post_type ,
				'post_id' => $post_ID
		) );

	}
}


/**
 * When a comment is posted
 *
 * @param unknown $comment_id
 * @param unknown $comment_approved
 */
function broo_submit_comment( $comment_id, $comment_approved = null) {
	$comment = get_comment( $comment_id );

	$user_id = $comment->user_id;

	if ( $user_id != 0 ) {
		Badgearoo::instance()->api->add_user_action( BROO_WP_SUBMIT_COMMENT_ACTION, $user_id, array( 'comment_id' => $comment_id ) );
	}
}


/**
 * Whenever a user logs in
 *
 * @param unknown $user_login
 * @param unknown $user
 */
function broo_user_login( $user_login, $user ) {
	Badgearoo::instance()->api->add_user_action( BROO_WP_LOGIN_ACTION, $user->ID );
}

/**
 * Saves post view
 */
function broo_view_post() {

	$post_id = url_to_postid( BROO_Utils::get_current_url() );
	$user_id = get_current_user_id();

	if ( $post_id == 0 || $user_id == 0 ) {
		return;
	}
	
	$post_type = get_post_type( $post_id );

	Badgearoo::instance()->api->add_user_action( BROO_VIEW_POST_ACTION, $user_id, array(
			'post_id' => $post_id,
			'post_type' => $post_type
	) );

}


/**
 * Whenever a user registers
 * @param unknown $user_id
 */
function broo_user_register( $user_id ) {
	Badgearoo::instance()->api->add_user_action( BROO_WP_USER_REGISTER_ACTION, $user_id );
}



/**
 * Whenever a user updates their profile
 * 
 * @param unknown $meta_id
 * @param unknown $user_id
 * @param unknown $meta_key
 * @param unknown $meta_value
 */
function broo_wp_profile_update( $user_id, $old_user_data ) {
	Badgearoo::instance()->api->add_user_action( BROO_WP_PROFILE_UPDATE_ACTION, $user_id, array() );
}


/**
 * Checks user action has been done once
 *
 * @param unknown $step_result
 * @param unknown $step
 * @param int $user_id
 * @param string $action_name
 * @return boolean
 */
function broo_condition_step_check_once( $step_result, $step, $user_id, $action_name ) {

	if ( $step_result == false ) { // no need to continue
		return $step_result;
	}

	global $wpdb;
	$query = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . BROO_USER_ACTION_TABLE_NAME . ' WHERE action_name = "'
			. esc_sql( $step->action_name ) . '" and user_id = ' . $user_id;

	$db_count = $wpdb->get_var( $query );

	if ( intval( $db_count ) == 0 ) {
		return false;
	}

	return $step_result;
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
function broo_condition_step_check_count( $step_result, $step, $user_id, $action_name ) {

	if ( $step_result == false ) { // no need to continue
		return $step_result;
	}

	$meta_count = Badgearoo::instance()->api->get_step_meta_value( $step->step_id, 'count' );
	
	// in case empty count is saved...
	if ( $meta_count == null || ( is_string( $meta_count ) && strlen( trim( $meta_count ) == 0 ) ) ) {
		$meta_count = 1;
	}

	global $wpdb;
	$query = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . BROO_USER_ACTION_TABLE_NAME . ' WHERE action_name = "'
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
function broo_condition_step_check_publish_post( $step_result, $step, $user_id, $action_name ) {

	if ( $step_result == false ) { // no need to continue
		return $step_result;
	}

	$meta_count = Badgearoo::instance()->api->get_step_meta_value( $step->step_id, 'count' );
	$meta_post_type = Badgearoo::instance()->api->get_step_meta_value( $step->step_id, 'post_type' );

	global $wpdb;
	$query = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . BROO_USER_ACTION_TABLE_NAME
			. ' ua INNER JOIN ' . $wpdb->prefix . BROO_USER_ACTION_META_TABLE_NAME
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
function broo_condition_step_check_points( $step_result, $step, $user_id, $action_name ) {
	
	if ( $step_result == false ) { // no need to continue
		return $step_result;
	}
	
	$value = Badgearoo::instance()->api->get_step_meta_value( $step->step_id, 'points' );

	$points = Badgearoo::instance()->api->get_user_points( $user_id );
	
	if ( intval( $points ) < intval( $value ) ) {
		return false;
	}

	return $step_result;
}