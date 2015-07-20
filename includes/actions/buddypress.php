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



function ub_init_buddypress_actions( $ub_actions = array() ) {
	
	return $ub_actions;
	
/*	$ub_actions = array_merge( $ub_actions, array( 
			'bp_activity_comment_posted' => array(
					'description' => __( 'Activity Comment Posted.', 'user-badges' ),
					'source' =>	__( 'BuddyPress', 'user-badges' ),
					'enabled' => null
			)
	) );
			
	return $ub_actions;*/
}
//apply_filter( 'ub_actions_init', 'ub_init_buddypress_actions', 10, 1 );

//$ub_actions = User_Badges::instance()->actions;

/**
 * TODO
 *
 * @param unknown $new_status
 * @param unknown $old_status
 * @param unknown $post
 */
function ub_bp_activity_comment_posted( $comment_id, $r, $activity ) {

	$check = false;
	
	if ( $check ) {

		// get user id
		$user_id = 0; // TODO
		$meta = array(); // TODO
		
		User_Badges::instance()->api->add_user_action( 'bp_activity_comment_posted', $user_id, $meta );
	}
}
//if ( isset( $ub_actions['bp_activity_comment_posted'] ) && $ub_actions['bp_activity_comment_posted']['enabled'] == true ) {
	//add_action( 'bp_activity_comment_posted',  'ub_bp_activity_comment_posted', 10, 3 );
//}
?>