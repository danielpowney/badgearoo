<?php 

/*
 * bbPress
 * - create X number of topics
 * - has bbPress moderator user role
 * - closed X number of topic
 * - replied to a topic X times
 *
 * Show badges & points underneath user profile
 */

// do_action( 'bp_activity_comment_posted', $comment_id, $r, $activity );
// do_action( 'bp_activity_add_user_favorite', $activity_id, $user_id );
// do_action( 'bp_activity_post_type_published', $activity_id, $post, $activity_args ); 
// do_action( 'friends_friendship_' . $action, $friendship->id, $friendship->initiator_user_id, $friendship->friend_user_id, $friendship );
// do_action( 'groups_create_group', $group->id, $member, $group );
// do_action( 'groups_join_group', $group_id, $user_id );
// do_action_ref_array( 'messages_message_sent', array( &$message ) );




function ub_init_buddypress_actions( $ub_actions ) {
	
	$ub_actions['bp_activity_comment_posted'] = array(
			'description' => __( 'Comment on an activity.', 'user-badges' ),
			'source' =>	__( 'BuddyPress', 'user-badges' )
	);
	
	$ub_actions['bp_activity_add_user_favorite'] = array(
			'description' => __( 'Add favorite.', 'user-badges' ),
			'source' =>	__( 'BuddyPress', 'user-badges' )
	);
	
	$ub_actions['bp_activity_post_type_published'] = array(
			'description' => __( 'Post activity.', 'user-badges' ),
			'source' =>	__( 'BuddyPress', 'user-badges' )
	);
	
	$ub_actions['friends_friendship_accepted'] = array(
			'description' => __( 'Accept a friend request.', 'user-badges' ),
			'source' =>	__( 'BuddyPress', 'user-badges' )
	);
	
	$ub_actions['friends_friendship_requested'] = array(
			'description' => __( 'Request a friend.', 'user-badges' ),
			'source' =>	__( 'BuddyPress', 'user-badges' )
	);
	
	$ub_actions['groups_create_group'] = array(
			'description' => __( 'Create Group.', 'user-badges' ),
			'source' =>	__( 'BuddyPress', 'user-badges' )
	);
	
	$ub_actions['groups_join_group'] = array(
			'description' => __( 'Join Group.', 'user-badges' ),
			'source' =>	__( 'BuddyPress', 'user-badges' )
	);
	
	return $ub_actions;
}
add_filter( 'ub_init_actions', 'ub_init_buddypress_actions', 10, 1 );




/**
 * Adds WP Core actions
 *
 * @param actions
 */
function ub_add_buddypress_actions( $actions = array() ) {
	
	$actions_enabled = (array) get_option( 'ub_actions_enabled' );

	if ( isset( $actions['bp_activity_comment_posted'] ) && $actions['bp_activity_comment_posted']['enabled'] == true ) {
		add_action( 'bp_activity_comment_posted',  'ub_bp_activity_comment_posted', 10, 3 );
		//add_filter( 'ub_condition_step_check_wp_publish_post', 'ub_condition_step_check_publish_post', 10, 4 );
	}
	
	if ( isset( $actions['bp_activity_add_user_favorite'] ) && $actions['bp_activity_add_user_favorite']['enabled'] == true ) {
		add_action( 'bp_activity_add_user_favorite',  'ub_bp_activity_add_user_favorite', 10, 2 );
		//add_filter( 'ub_condition_step_check_wp_publish_post', 'ub_condition_step_check_publish_post', 10, 4 );
	}
	
	if ( isset( $actions['bp_activity_post_type_published'] ) && $actions['bp_activity_post_type_published']['enabled'] == true ) {
		add_action( 'bp_activity_post_type_published',  'ub_bp_activity_post_type_published', 10, 2 );
		//add_filter( 'ub_condition_step_check_wp_publish_post', 'ub_condition_step_check_publish_post', 10, 4 );
	}
	
	if ( isset( $actions['friends_friendship_requested'] ) && $actions['friends_friendship_requested']['enabled'] == true ) {
		add_action( 'friends_friendship_requested',  'ub_friends_friendship_requested', 10, 4 );
		//add_filter( 'ub_condition_step_check_wp_publish_post', 'ub_condition_step_check_publish_post', 10, 4 );
	}
	
	if ( isset( $actions['friends_friendship_accepted'] ) && $actions['friends_friendship_accepted']['enabled'] == true ) {
		add_action( 'friends_friendship_accepted',  'ub_friends_friendship_accepted', 10, 4 );
		//add_filter( 'ub_condition_step_check_wp_publish_post', 'ub_condition_step_check_publish_post', 10, 4 );
	}
	
	if ( isset( $actions['groups_create_group'] ) && $actions['groups_create_group']['enabled'] == true ) {
		add_action( 'groups_create_group',  'ub_groups_create_group', 10, 3 );
		//add_filter( 'ub_condition_step_check_wp_publish_post', 'ub_condition_step_check_publish_post', 10, 4 );
	}
	
	if ( isset( $actions['groups_join_group'] ) && $actions['groups_join_group']['enabled'] == true ) {
		add_action( 'groups_join_group',  'ub_groups_join_group', 10, 2 );
		//add_filter( 'ub_condition_step_check_wp_publish_post', 'ub_condition_step_check_publish_post', 10, 4 );
	}

	//add_filter('ub_step_meta_count_enabled', 'ub_step_meta_count_enabled_wp_core', 10, 2 );
	//add_filter('ub_step_meta_post_type_enabled', 'ub_step_meta_post_type_enabled_wp_core', 10, 2 );

}
add_action( 'ub_init_actions_complete', 'ub_add_buddypress_actions' );


/**
 * Activity Comment Posted
 *
 * @param unknown $new_status
 * @param unknown $old_status
 * @param unknown $post
 */
function ub_bp_activity_comment_posted( $comment_id, $r, $activity ) {
	
	$user_id = $activity->user_id; // TODO
		
	User_Badges::instance()->api->add_user_action( 'bp_activity_comment_posted', $user_id, array(
			'activity_id' => $activity->id,
			'type' => $activity->type,
	) );
}


/**
 * Activity Add User Favorite
 * 
 * @param unknown $activity_id
 * @param unknown $user_id
 */
function ub_bp_activity_add_user_favorite( $activity_id, $user_id ) {
	
	User_Badges::instance()->api->add_user_action( 'bp_activity_comment_posted', $user_id, array(
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
	// TODO
}


/**
 * 
 * @param unknown $friendship_id
 * @param unknown $initiator_user_id
 * @param unknown $friend_user_id
 * @param unknown $friendship
 */
function ub_friends_friendship_accepted( $friendship_id, $initiator_user_id, $friend_user_id, $friendship ) {
	// TODO
}


/**
 * 
 * @param unknown $friendship_id
 * @param unknown $initiator_user_id
 * @param unknown $friend_user_id
 * @param unknown $friendship
 */
function ub_friends_friendship_requested( $friendship_id, $initiator_user_id, $friend_user_id, $friendship ) {
	// TODO
}

function ub_groups_create_group( $group_id, $member, $group ) {
	// TODO
}

/**
 * 
 * @param unknown $group_id
 * @param unknown $user_id
 */
function ub_groups_join_group( $group_id, $user_id ) {
	
	User_Badges::instance()->api->add_user_action( 'groups_join_group', $user_id, array(
			'group_id' => $group_id
	) );
}


/**
 * Defaults actions enabled
 *
 * @param array $actions_enabled
 * @return $actions_enabled:
 */
function ub_default_buddypress_actions_enabled( $actions_enabled ) {
	
	return array_merge( array(
			'bp_activity_comment_posted'			=> true,
			'bp_activity_add_user_favorite'			=> false,
			'bp_activity_post_type_published'		=> true,
			'friends_friendship_accepted'			=> false,
			'friends_friendship_requested'			=> false,
			'groups_create_group'					=> true,
			'groups_join_group'						=> true
	), $actions_enabled );

}
add_filter( 'ub_default_actions_enabled', 'ub_default_buddypress_actions_enabled', 10, 1 );

?>