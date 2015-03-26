<?php

/**
 * When a post changes status, check if author is eligible for the published post count badge
 * 
 * @param unknown $new_status
 * @param unknown $old_status
 * @param unknown $post
 */
function ub_has_published_post( $new_status, $old_status, $post = null ) {
	
	if ( $post == null ) {
		return;
	}
	
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
		
		return apply_filters( 'ub_user_has_published_post', 
				$user_has_published_post, $user_id, $post_type );
	}
}

function ub_handle_condition_check_success( $action, $params ) {
	
	switch ( $action ) {
		case UB_WP_PUBLISH_POST_ACTION :
			User_Badges::instance()->api->add_user_badge( 1, 1 );
		break;
	}
}