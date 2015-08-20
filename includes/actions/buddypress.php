<?php 
/**
 * BuddyPress actions
 */

define ( 'BP_ACTIVITY_COMMENT_POSTED_ACTION', 'bp_activity_comment_posted' ); // works
define ( 'BP_ACTIVITY_ADD_USER_FAVORITE_ACTION', 'bp_activity_add_user_favorite' ); // works
define ( 'BP_ACTIVITY_POST_TYPE_PUBLISHED_ACTION', 'bp_activity_post_type_published' );
define ( 'BP_FRIENDS_FRIENDSHIP_ACCEPTED_ACTION', 'friends_friendship_accepted' ); // works
define ( 'BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION', 'friends_friendship_requested' ); // works
define ( 'BP_GROUPS_CREATE_GROUP_ACTION', 'groups_create_group' ); // works - may rename
define ( 'BP_GROUPS_JOIN_GROUP_ACTION', 'groups_join_group' ); // workds
// TODO invite members to join a group

// do_action( 'bp_activity_comment_posted', $comment_id, $r, $activity );
// do_action( 'bp_activity_add_user_favorite', $activity_id, $user_id );
// do_action( 'bp_activity_post_type_published', $activity_id, $post, $activity_args );
// do_action( 'friends_friendship_' . $action, $friendship->id, $friendship->initiator_user_id, $friendship->friend_user_id, $friendship );
// do_action( 'groups_create_group', $group->id, $member, $group );
// do_action( 'groups_join_group', $group_id, $user_id ); and groups_accept_invite

function ub_init_bp_actions( $ub_actions ) {
	
	$ub_actions[BP_ACTIVITY_COMMENT_POSTED_ACTION] = array(
			'description' => __( 'Comment on an activity.', 'user-badges' ),
			'source' =>	__( 'BuddyPress', 'user-badges' )
	);
	
	$ub_actions[BP_ACTIVITY_ADD_USER_FAVORITE_ACTION] = array(
			'description' => __( 'Add favorite.', 'user-badges' ),
			'source' =>	__( 'BuddyPress', 'user-badges' )
	);
	
	$ub_actions[BP_ACTIVITY_POST_TYPE_PUBLISHED_ACTION] = array(
			'description' => __( 'Post activity.', 'user-badges' ),
			'source' =>	__( 'BuddyPress', 'user-badges' )
	);
	
	$ub_actions[BP_FRIENDS_FRIENDSHIP_ACCEPTED_ACTION] = array(
			'description' => __( 'Accept a friend request.', 'user-badges' ),
			'source' =>	__( 'BuddyPress', 'user-badges' )
	);
	
	$ub_actions[BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION] = array(
			'description' => __( 'Request a friend.', 'user-badges' ),
			'source' =>	__( 'BuddyPress', 'user-badges' )
	);
	
	$ub_actions[BP_GROUPS_CREATE_GROUP_ACTION] = array(
			'description' => __( 'Create Group.', 'user-badges' ),
			'source' =>	__( 'BuddyPress', 'user-badges' )
	);
	
	$ub_actions[BP_GROUPS_JOIN_GROUP_ACTION] = array(
			'description' => __( 'Join Group.', 'user-badges' ),
			'source' =>	__( 'BuddyPress', 'user-badges' )
	);
	
	return $ub_actions;
}
add_filter( 'ub_init_actions', 'ub_init_bp_actions', 10, 1 );


/**
 * Adds WP Core actions
 *
 * @param actions
 */
function ub_add_bp_actions( $actions = array() ) {
	
	$actions_enabled = (array) get_option( 'ub_actions_enabled' );

	if ( isset( $actions[BP_ACTIVITY_COMMENT_POSTED_ACTION] ) && $actions[BP_ACTIVITY_COMMENT_POSTED_ACTION]['enabled'] == true ) {
		add_action( 'bp_activity_comment_posted',  'ub_bp_activity_comment_posted', 10, 3 );
		add_filter( 'ub_condition_step_check_bp_activity_comment_posted', 'ub_condition_step_check_bp_action_count', 10, 4 );
	}
	
	if ( isset( $actions[BP_ACTIVITY_ADD_USER_FAVORITE_ACTION] ) && $actions[BP_ACTIVITY_ADD_USER_FAVORITE_ACTION]['enabled'] == true ) {
		add_action( 'bp_activity_add_user_favorite',  'ub_bp_activity_add_user_favorite', 10, 2 );
		add_filter( 'ub_condition_step_check_bp_activity_add_user_favorite', 'ub_condition_step_check_bp_action_count', 10, 4 );
	}
	
	if ( isset( $actions[BP_ACTIVITY_POST_TYPE_PUBLISHED_ACTION] ) && $actions[BP_ACTIVITY_POST_TYPE_PUBLISHED_ACTION]['enabled'] == true ) {
		add_action( 'bp_activity_post_type_published',  'ub_bp_activity_post_type_published', 10, 2 );
		add_filter( 'ub_condition_step_check_bp_activity_post_type_published', 'ub_condition_step_check_bp_action_count', 10, 4 );
	}
	
	if ( isset( $actions[BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION] ) && $actions[BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION]['enabled'] == true ) {
		add_action( 'friends_friendship_requested',  'ub_friends_friendship_requested', 10, 4 );
		add_filter( 'ub_condition_step_check_friends_friendship_requested', 'ub_condition_step_check_bp_action_count', 10, 4 );
	}
	
	if ( isset( $actions[BP_FRIENDS_FRIENDSHIP_ACCEPTED_ACTION] ) && $actions[BP_FRIENDS_FRIENDSHIP_ACCEPTED_ACTION]['enabled'] == true ) {
		add_action( 'friends_friendship_accepted',  'ub_friends_friendship_accepted', 10, 4 );
		add_filter( 'ub_condition_step_check_friends_friendship_accepted', 'ub_condition_step_check_bp_action_count', 10, 4 );
	}
	
	if ( isset( $actions[BP_GROUPS_CREATE_GROUP_ACTION] ) && $actions[BP_GROUPS_CREATE_GROUP_ACTION]['enabled'] == true ) {
		add_action( 'groups_create_group',  'ub_groups_create_group', 10, 3 );
		add_filter( 'ub_condition_step_check_groups_create_group', 'ub_condition_step_check_bp_action_count', 10, 4 );
	}
	
	if ( isset( $actions[BP_GROUPS_JOIN_GROUP_ACTION] ) && $actions[BP_GROUPS_JOIN_GROUP_ACTION]['enabled'] == true ) {
		add_action( 'groups_join_group',  'ub_groups_join_group', 10, 2 );
		add_action( 'groups_accept_invite',  'ub_groups_join_group', 10, 2 );
		add_filter( 'ub_condition_step_check_groups_join_group', 'ub_condition_step_check_bp_action_count', 10, 4 );
	}

	add_filter('ub_step_meta_count_enabled', 'ub_step_meta_count_enabled_bp', 10, 2 );
	//add_filter('ub_step_meta_bp_activity_type_enabled', 'ub_step_meta_bp_activity_type_enabled', 10, 2 );

}
add_action( 'ub_init_actions_complete', 'ub_add_bp_actions' );



/**
 * Checks count for the BuddyPress actions action
 *
 * @param unknown $step_result
 * @param unknown $step
 * @param int $user_id
 * @param string $action_name
 * @return boolean
 */
function ub_condition_step_check_bp_action_count( $step_result, $step, $user_id, $action_name ) {

	if ( $step_result == false ) { // no need to continue
		return $step_result;
	}
	
	$meta_count = User_Badges::instance()->api->get_step_meta_value( $step->step_id, 'count' );	

	global $wpdb;
	$query = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . UB_USER_ACTION_TABLE_NAME
			. ' ua WHERE ua.action_name = "' . esc_sql( $action_name ) . '"';

	$db_count = $wpdb->get_var( $query );

	if ( intval( $db_count ) < intval( $meta_count ) ) {
		return false;
	}

	return $step_result;
}

/**
 * Activity Comment Posted
 *
 * @param unknown $new_status
 * @param unknown $old_status
 * @param unknown $post
 */
function ub_bp_activity_comment_posted( $comment_id, $r, $activity ) {
	
	$user_id = $activity->user_id;
		
	User_Badges::instance()->api->add_user_action( BP_ACTIVITY_COMMENT_POSTED_ACTION, $user_id, array(
			'activity_id' => $activity->id,
			'activity_type' => $activity->type,
	) );
}


/**
 * Activity Add User Favorite
 * 
 * @param unknown $activity_id
 * @param unknown $user_id
 */
function ub_bp_activity_add_user_favorite( $activity_id, $user_id ) {
	
	User_Badges::instance()->api->add_user_action( BP_ACTIVITY_ADD_USER_FAVORITE_ACTION, $user_id, array(
			'activity_id' => $activity_id
	) );
}


/**
 * 
 * @param unknown $activity_id
 * @param unknown $post
 * @param unknown $activity_args
 */
function ub_bp_activity_post_type_published( $activity_id, $post, $activity_args ) {
	
	User_Badges::instance()->api->add_user_action( BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION, $activity_args['user_id'], array() );
	
}


/**
 * 
 * @param unknown $friendship_id
 * @param unknown $initiator_user_id
 * @param unknown $friend_user_id
 * @param unknown $friendship
 */
function ub_friends_friendship_accepted( $friendship_id, $initiator_user_id, $friend_user_id, $friendship ) {
	
	User_Badges::instance()->api->add_user_action( BP_FRIENDS_FRIENDSHIP_ACCEPTED_ACTION, $initiator_user_id, array() );
	
}


/**
 * 
 * @param unknown $friendship_id
 * @param unknown $initiator_user_id
 * @param unknown $friend_user_id
 * @param unknown $friendship
 */
function ub_friends_friendship_requested( $friendship_id, $initiator_user_id, $friend_user_id, $friendship ) {
	
	User_Badges::instance()->api->add_user_action( BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION, $initiator_user_id, array() );
	
}


/**
 * 
 * @param unknown $group_id
 * @param unknown $member
 * @param unknown $group
 */
function ub_groups_create_group( $group_id, $member, $group ) {
	
	$user_id = $member->user_id;

	User_Badges::instance()->api->add_user_action( BP_GROUPS_CREATE_GROUP_ACTION, $user_id, array(
			'group_id' => $group_id
	) );
}

/**
 * 
 * @param unknown $group_id
 * @param unknown $user_id
 */
function ub_groups_join_group( $group_id, $user_id ) {
	
	User_Badges::instance()->api->add_user_action( BP_GROUPS_JOIN_GROUP_ACTION, $user_id, array(
			'group_id' => $group_id
	) );
}


/**
 * Defaults actions enabled
 *
 * @param array $actions_enabled
 * @return $actions_enabled:
 */
function ub_default_bp_actions_enabled( $actions_enabled ) {
	
	return array_merge( array(
			BP_ACTIVITY_COMMENT_POSTED_ACTION			=> true,
			BP_ACTIVITY_ADD_USER_FAVORITE_ACTION		=> false,
			BP_ACTIVITY_POST_TYPE_PUBLISHED_ACTION		=> true,
			BP_FRIENDS_FRIENDSHIP_ACCEPTED_ACTION		=> false,
			BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION		=> false,
			BP_GROUPS_CREATE_GROUP_ACTION				=> true,
			BP_GROUPS_JOIN_GROUP_ACTION					=> true
	), $actions_enabled );

}
add_filter( 'ub_default_actions_enabled', 'ub_default_bp_actions_enabled', 10, 1 );


/**
 * Displays badges before BuddPress member header meta
 */
function ub_bp_before_member_header_meta() {
	
	$user_id = bp_displayed_user_id();
	
	$points = User_Badges::instance()->api->get_user_points( $user_id );
	$badges = User_Badges::instance()->api->get_user_badges( $user_id );
	
	// count badges by id
	$badge_count_lookup = array();
	foreach ( $badges as $index => $badge ) {
		if ( ! isset( $badge_count_lookup[$badge->id] ) ) {
			$badge_count_lookup[$badge->id] = 1;
		} else {
			$badge_count_lookup[$badge->id]++;
			unset( $badges[$index] );
		}
	}
	
	ub_get_template_part( 'user-badges-summary', null, true, array(
			'badges' => $badges,
			'points' => $points,
			'badge_count_lookup' => $badge_count_lookup
	) );
	
}
add_action( 'bp_before_member_header_meta', 'ub_bp_before_member_header_meta' );


/**
 * Shows a points step meta
 *
 * @param unknown $step_id
 * @param unknown $action
 */
function ub_step_meta_bp_activity_type( $step_id, $action  ) {

	$step_meta_enabled = apply_filters( 'ub_step_meta_bp_activity_type_enabled', false, $action );

	if ( $step_meta_enabled ) {
		$value = User_Badges::instance()->api->get_step_meta_value( $step_id, 'activity_type' );
		?>
		<span class="step-meta-value">
			<label for="activity_type"><?php _e( 'Activity Type', 'user-badges' ); ?></label>
			<select name="activity_type">
				<option value="" <?php selected( ! $value ); ?>><?php _e( 'All activity types', 'user-badges' ); ?></option>
				<option value="activity_comment"><?php _e( 'Replied to a status update', 'user-badges' ); ?></option>
				<option value="activity_update"><?php _e( 'Posted a status update', 'user-badges' ); ?></option>
			</select>
		</span>
		<?php
	}
}
//add_action( 'ub_step_meta', 'ub_step_meta_bp_activity_type', 10, 2 );

/**
 * Sets whether step meta post type is enabled for BuddyPress activity types
 *
 * @param unknown $enabled
 * @param unknown $action
 * @return boolean|unknown
 */
function ub_step_meta_bp_activity_type_enabled( $enabled, $action ) {

	if ( $action == BP_ACTIVITY_COMMENT_POSTED_ACTION ) {
		return true;
	}

	return $enabled;
}

function ub_step_meta_count_enabled_bp( $enabled, $action ) {

	if ( $action == BP_ACTIVITY_COMMENT_POSTED_ACTION || $action == BP_ACTIVITY_ADD_USER_FAVORITE_ACTION
			|| $action == BP_ACTIVITY_POST_TYPE_PUBLISHED_ACTION || $action == BP_FRIENDS_FRIENDSHIP_ACCEPTED_ACTION
			|| $action == BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION || $action == BP_GROUPS_CREATE_GROUP_ACTION
			|| $action == BP_GROUPS_JOIN_GROUP_ACTION ) {
		return true;
	}

	return $enabled;
}

?>