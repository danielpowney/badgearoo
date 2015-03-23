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
	 * @param unknown $badge_name
	 * @param unknown $user_id
	 */
	public function add_user_badge( $badge_name, $user_id );
	
	/**
	 * Gets badges by user id
	 * 
	 * @param unknown $user_id
	 */
	public function get_user_badges( $user_id );
	
	
	/**
	 * Deletes a badge by name
	 * 
	 * @param unknown $name
	 */
	public function delete_badge( $name );
	
	/**
	 * 
	 * Gets a badge and optionally loads an array of users who have the badge
	 * 
	 * @param unknown $name
	 * @param unknown $load_users
	 */
	public function get_badge( $name, $load_users );
	
	/**
	 * Deletes a badge associated to a specific user
	 * 
	 * @param unknown $badge_name
	 * @param unknown $user_id
	 */
	public function delete_user_badge( $badge_name, $user_id );
	
	/**
	 * Adds a new badge
	 * 
	 * @param unknown $name
	 * @param unknown $description
	 * @param unknown $url
	 * @param string $enabled
	 */
	public function add_new_badge( $name, $description, $url, $enabled = true );

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
	public function add_user_badge( $badge_name, $user_id ) {

		// TODO check if user already has badge, if they do, do nothing. If they don't, add action 
		// 'ub_new_user_badge'. This could be used for e-mail comms
		
		global $wpdb;
		
		$wpdb->replace(
				$wpdb->prefix . UB_USER_BADGES_TABLE_NAME,
				array(
						'badge_name' => $badge_name,
						'user_id' => $user_id,
						'created_dt' => current_time( 'mysql' )
				),
				array( '%s', '%d', '%s' )
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
			
			$badge = $this->get_badge( $row->badge_name );
			
			if ( $badge != null ) {
				array_push( $badges_list, $badge );
			}
		}
		
		return $badges_list;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::delete_badge()
	 */
	public function delete_badge( $name ) {
		
		global $wpdb;
		
		$wpdb->delete( $wpdb->prefix . UB_BADGES_TABLE_NAME, array( 'name' => $name ), array( '%s' ) );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::get_badge()
	 */
	public function get_badge( $name = null, $load_users = false ) {
		if ( $name == null) {
			return null;
		}
		
		global $wpdb;
		
		$badge_row = $wpdb->get_row( $wpdb->prepare( "
				SELECT      *
				FROM        " . $wpdb->prefix . UB_BADGES_TABLE_NAME . "
				WHERE       name = %s",
				$name
		) );
		
		if ( $badge_row != null ) {
			
			$users = array();
			
			if ( $load_users ) {
				
				$users = $wpdb->get_results( $wpdb->prepare( "
						SELECT      user_id
						FROM        " . $wpdb->prefix . UB_USER_BADGES_TABLE_NAME . "
						WHERE       badge_name = %s",
						$name
				), ARRAY_N );
			}
		
			return new Badge( $name, $badge_row->description, $badge_row->url, $badge_row->enabled, $badge_row->created_dt, $users );
		}
		
		return null;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::delete_user_badge()
	 */
	public function delete_user_badge( $badge_name, $user_id ) {

		global $wpdb;
		
		$wpdb->delete( $wpdb->prefix . UB_USER_BADGES_TABLE_NAME, 
				array( 'badge_name' => $badge_name, 'user_id' => $user_id ), 
				array( '%s', '%d' )
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::add_new_badge()
	 */
	public function add_new_badge( $name, $description, $url, $enabled = true ) {
		$url = esc_url_raw($url);
		
		global $wpdb;
		
		$wpdb->insert(
				$wpdb->prefix . UB_BADGES_TABLE_NAME,
				array(
						'name' => $name,
						'description' => $description,
						'url' => $url,
						'enabled' => $enabled
				),
				array( '%s', '%s', '%s', '%d' )
		);
	}
}