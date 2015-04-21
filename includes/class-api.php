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
	
	/**
	 * Records a user action
	 * 
	 * @param unknown $action
	 * @param unknown $user_id
	 */
	public function add_user_action( $action, $user_id );
	
	/**
	 * Adds a step
	 * 
	 * @param unknown $condition_id
	 * @param unknown $label
	 * @param unknown $created_dt
	 * @return step
	 */
	public function add_step( $condition_id, $label, $created_dt );
	
	public function delete_step();
	
	/**
	 * Adds a condition
	 * 
	 * @param unknown $name
	 * @return condition
	 */
	public function add_condition( $name );
	
	public function delete_condition();
	
	public function add_step_meta();
	
	/**
	 * Gets conditions
	 * 
	 * @param unknown $filters
	 * @param string $load_badge
	 * @param string $load_steps
	 * @return conditions
	 */
	public function get_conditions( $filters = array(), $load_badge = false, $load_steps = true );
	
	public function get_badges( $filters, $load_users = false );
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
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::add_user_action()
	 */
	public function add_user_action( $action, $user_id ) {
		
		global $wpdb;
		
		$wpdb->insert( $wpdb->prefix . UB_USER_ACTIONS_TABLE_NAME, array( 'user_id' => $user_id, 'action' => $action ), array( '%d', '%s') );	
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::add_step()
	 */
	public function add_step( $condition_id, $label, $created_dt ) {
		
		global $wpdb;
		
		$wpdb->insert( $wpdb->prefix . UB_CONDITION_STEP_TABLE_NAME ,
				array( 'condition_id' => $condition_id, 'label' => $label, 'created_dt' => $created_dt ),
				array( '%s', '%s', '%s')
		);
		$step_id = $wpdb->insert_id;
		
		return new UB_Step( $step_id, $condition->condition_id, $label, null, $created_dt );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::add_condition()
	 */
	public function add_condition( $name ) {
		global $wpdb;
		
		$created_dt = current_time('mysql');
		
		$wpdb->insert( $wpdb->prefix . UB_CONDITION_TABLE_NAME , array( 'name' => $name, 'created_dt' => $created_dt ), array( '%s', '%s' ) );
		$condition_id = $wpdb->insert_id;
		
		return new UB_Condition( $condition_id, $name, null, 0, $created_dt, null, false, false );
	}
	
	public function get_conditions( $filters = array(), $load_badge = false, $load_steps = true ) {
		global $wpdb;
		
		$results = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . UB_CONDITION_TABLE_NAME );
		
		$conditions = array();
		foreach ( $results as $row ) {
			array_push( $conditions, new UB_Condition( $row->id, $row->name, $row->badge_id, $row->points, $row->created_dt, $row->status, $load_badge, $load_steps ) );
		}
		
		return $conditions;
	}
	
	public function get_badges( $filters = array( 'status' => 'publish' ), $load_users = false ) {
		global $wpdb;
				
		$query = 'SELECT * FROM ' . $wpdb->posts . ' WHERE post_type = "badge"';
				
		if ( isset( $filters['status'] ) ) {
			$query .= ' AND post_status = "' . esc_sql( $filters['status'] ) . '"';
		}
		
		$results = $wpdb->get_results( $query );
		
		$badges = array();
		foreach ( $results as $row ) {
			
			$users = null;
			if ( $load_users == true ) {
				$users = $wpdb->get_results( $wpdb->prepare( "
							SELECT      user_id
							FROM        " . $wpdb->prefix . UB_USER_BADGES_TABLE_NAME . "
							WHERE       badge_id = %d",
						$row->ID
				), ARRAY_N );
			}
			
			$badge = new Badge(
					$row->ID,
					$row->post_title,
					$row->post_excerpt,
					$row->post_date,
					$users
			);
			
			array_push( $badges, $badge );
		}
		
		return $badges;
														}
	}
}