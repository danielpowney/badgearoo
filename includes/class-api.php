<?php

/**
 * API
 * 
 * @author dpowney
 *
 */
interface UB_API {
	
	/**
	 * Adds a badge to a user if they do not have it already
	 * 
	 * @param unknown $badge_id
	 * @param unknown $user_id
	 */
	public function add_user_badge( $badge_id, $user_id );
	
	/**
	 * Gets badges by user id
	 * 
	 * @param unknown $user_id
	 */
	public function get_user_badges( $user_id );
	
	/**
	 * 
	 * Gets a badge and optionally loads an array of users who have the badge
	 * 
	 * @param unknown $badge_id
	 * @param unknown $load_users
	 */
	public function get_badge( $badge_id, $load_users );
	
	/**
	 * Deletes a badge associated to a specific user
	 * 
	 * @param unknown $badge_id
	 * @param unknown $user_id
	 */
	public function delete_user_badge( $badge_id, $user_id );
}

/**
 * API implementation
 * 
 * @author dpowney
 *
 */
class UB_API_Impl implements UB_API {
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::add_user_badge()
	 */
	public function add_user_badge( $badge_id, $user_id ) {

		// TODO check if user already has badge, if they do, do nothing. If they don't, add action 
		// 'ub_new_user_badge'. This could be used for e-mail comms
		
		global $wpdb;
		
		$wpdb->replace(
				$wpdb->prefix . UB_USER_BADGES_TABLE_NAME,
				array(
						'badge_id' => $badge_id,
						'user_id' => $user_id,
						'created_dt' => current_time( 'mysql' )
				),
				array( '%d', '%d', '%s' )
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::get_user_badges()
	 */
	public function get_user_badges( $user_id ) {
		
		$badges_list = array();
		
		global $wpdb;
		
		$user_badges_results = $wpdb->get_results( $wpdb->prepare( "
				SELECT      *
				FROM        " . $wpdb->prefix . UB_USER_BADGES_TABLE_NAME . "
				WHERE       user_id = %d",
				$user_id
		) );
		
		foreach ( $user_badges_results as $row ) {
			
			$badge = $this->get_badge( $row->badge_id );
			
			if ( $badge != null ) {
				array_push( $badges_list, $badge );
			}
		}
		
		return $badges_list;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::get_badge()
	 */
	public function get_badge( $badge_id = 0, $load_users = false ) {
		if ( $badge_id == 0 ) {
			return null;
		}
		
		$post = get_post( $badge_id );
		
		if ( $post != null ) {
			
			$users = array();
			
			if ( $load_users ) {
				
				$users = $wpdb->get_results( $wpdb->prepare( "
						SELECT      user_id
						FROM        " . $wpdb->prefix . UB_USER_BADGES_TABLE_NAME . "
						WHERE       badge_id = %d",
						$badge_id
				), ARRAY_N );
			}
		
			return new Badge( 
					$badge_id,
					$post->post_title,
					$post->post_excerpt,
					$post->post_date,
					$users
			);
		}
		
		return null;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::delete_user_badge()
	 */
	public function delete_user_badge( $badge_id, $user_id ) {

		global $wpdb;
		
		$wpdb->delete( $wpdb->prefix . UB_USER_BADGES_TABLE_NAME, 
				array( 'badge_id' => $badge_id, 'user_id' => $user_id ), 
				array( '%d', '%d' )
		);
	}
}