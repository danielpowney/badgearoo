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
	
	// get post type
	$post_type = $post->post_type;
	
	if ( $post_type == 'post' && $old_status != 'publish'  &&  $new_status == 'publish' ) {
		
		// get user id
		$user_id = $post->post_author;
		
		User_Badges::instance()->api->add_user_action( UB_WP_PUBLISH_POST_ACTION, $user_id );
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
function ub_submit_comment( $comment_id, $comment_approved ) {
	$comment = get_comment( $comment_id );
	
	$user_id = $comment->user_id;
	
	if ( $user_id != 0 ) {
		User_Badges::instance()->api->add_user_action( UB_WP_SUBMIT_COMMENT_ACTION, $user_id );
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
