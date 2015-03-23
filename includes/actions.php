<?php

/**
 * When a post changes status, check if author is eligible for the published post count badge
 * 
 * @param unknown $new_status
 * @param unknown $old_status
 * @param unknown $post
 */
function ub_on_all_post_status_transitions( $new_status, $old_status, $post ) {
	
	// get post type
	$post_type = $post->post_type;
	
	if ( $post_type == 'post' ) {
		// get user id
		$user_id = $post->post_author;
		
		global $wpdb;
		
		$user_published_posts_count = $wpdb->get_var( $wpdb->prepare( "
				SELECT      COUNT(ID)
				FROM        $wpdb->posts
				WHERE       post_type = %s
							AND post_status = %s
							AND post_author = %d",
				$post_type, "publish", $user_id
		) ); 
		
		$user_has_published_post = false;
		
		if ( $user_published_post_count > 0 ) {
			$user_has_published_post = true;
		} else {
			// TODO
			// a) check if their badge is to be removed
			// b) exception for admin given badges
		}
		
		$user_has_published_post = apply_filters( 'ub_user_has_published_post', 
				$user_has_published_post, $user_id, $post_type );
		
		if ( $user_has_published_post == true ) {
			User_Badges::instance()->api->add_user_badge( __( 'User Published Post', 'user-badges' ), $user_id );
		}
	}
}
add_action(  'transition_post_status',  'ub_on_all_post_status_transitions', 10, 2 );


// TODO
// 1. if user has been active for 1 year (can we check if they've logged in)
// 2. if user has commented on a post, return true
// 3. How to handle admin awarded badges and ad-hoc badges for plugin add-ons