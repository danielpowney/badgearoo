<?php

/**
 * API
 * 
 * @author dpowney
 *
 */
interface UB_API {
	
	/**
	 * Adds assignment (e.g. badge, points) to a user if they do not have it already
	 * 
	 * @param int condition_id
	 * @param int $user_id
	 * @param string $type
	 * @param int $value
	 */
	public function add_user_assignment( $condition_id, $user_id = 0, $type = 'badge', $value = 0 );
	
	/**
	 * Deletes assignment (e.g. badge, points) from a user
	 *
	 * @param int condition_id
	 * @param int $user_id
	 * @param string $type
	 * @param int $value
	 */
	public function delete_user_assignment( $condition_id = null, $user_id = 0, $type = 'badge', $value = 0 );
	
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
	 * Records a user action
	 * 
	 * @param unknown $action
	 * @param unknown $user_id
	 */
	public function add_user_action( $action_name, $user_id, $meta = array() );
	
	/**
	 * Adds a step
	 * 
	 * @param unknown $condition_id
	 * @param unknown $label
	 * @return step
	 */
	public function add_step( $condition_id, $label );
	
	/**
	 * Deletes a step
	 * @param unknown $step_id
	 */
	public function delete_step( $step_id );
	
	/**
	 * Adds a condition
	 * 
	 * @param unknown $name
	 * @return condition
	 */
	public function add_condition( $name );
	
	/**
	 * Deletes a condition
	 * 
	 * @param unknown $condition_id
	 */
	public function delete_condition( $condition_id );
	
	/**
	 * Gets conditions
	 * 
	 * @param unknown $filters
	 * @return conditions
	 */
	public function get_conditions( $filters = array() );
	
	/**
	 * Get a condition by id
	 * 
	 * @param unknown $condition_id
	 * @return condition
	 */
	public function get_condition( $condition_id );
	
	/**
	 * Gets badges
	 * 
	 * @param unknown $filters
	 * @param string $load_users
	 * @return badges
	 */
	public function get_badges( $filters = array( 'status' => 'publish' ), $load_users = false );
	
	/**
	 * Gets actions
	 * @param unknown $filters
	 * @return actions
	 */
	public function get_actions( $filters = array( 'enabled' => true ) );
	
	/**
	 * Saves a condition
	 * 
	 * @param unknown $condition
	 */
	public function save_condition( $condition );
	
	/**
	 * Saves a step
	 * 
	 * @param unknown $step
	 */
	public function save_step( $step );
	
	/**
	 * Saves step meta
	 * 
	 * @param int $step_id
	 * @param string $meta_key
	 * @param string $meta_value
	 */
	public function save_step_meta( $step_id, $meta_key, $meta_value = '' );
	
	/**
	 * Get step meta value
	 * 
	 * @param unknown $step_id
	 * @param unknown $meta_key
	 * @return value
	 */
	public function get_step_meta_value( $step_id, $meta_key );

	/**
	 * Adds user action meta
	 * 
	 * @param unknown $action_name
	 * @param unknown $meta_key
	 * @param unknown $meta_value
	 */
	public function add_user_action_meta( $action_name, $meta_key, $meta_value = '' );
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
	 * @see UB_API::add_user_assignment()
	 */
	public function add_user_assignment( $condition_id = null, $user_id = 0, $type = 'badge', $value = 0 ) {
		
		if ( $user_id == 0 || ( $type != 'points' && $type != 'badge' ) || $value == 0 ) {
			return;
		}
		
		global $wpdb;
		
		$row = null;
		if ( $condition_id ) {
			$query = 'SELECT DISTINCT(value) FROM ' . $wpdb->prefix . UB_USER_ASSIGNMENT_TABLE_NAME . ' WHERE condition_id = ' . esc_sql( $condition_id ) . ' AND type = "' . esc_sql( $type ) . '"';
			
			if ( $type == 'badge' ) {
				$query .= ' AND value = ' . $value;
			}
			$row = $wpdb->get_row( $query );
		}
		
		if ( $row ) { // > 0
			
			$where = array( 'condition_id' => $condition_id, 'type' => $type );
			$where_format = array( '%d', '%s' );
			
			if ( $type = 'badge' ) {
				$where['value'] = $value;
				array_push( $where_format, '%d' );
			}
						
			$result = $wpdb->update( $wpdb->prefix . UB_USER_ASSIGNMENT_TABLE_NAME,
					array( 'created_dt' => current_time( 'mysql' ), 'value' => $value ),
					$where,
					array( '%s', '%d' ),
					$where_format
			);
			
			if ( $type == 'points' && $row->value != $value ) { // subtract previous points from usermeta and the add new points
				
				$total_points = get_user_meta( $user_id, 'ub_points', true );
				if ( strlen( $total_points ) == 0 || ! is_numeric( $total_points ) ) {
					$total_points = 0;
				} else {
					$total_points = intval( $total_points );
				}
				
				$total_points = $total_points - intval( $row->value ) + intval( $value );
				update_user_meta( $user_id, 'ub_points', $total_points );
			}
			
			do_action( 'ub_update_user_assignment', $condition_id, $user_id, $type, $value );
			
		} else {
			$wpdb->insert( $wpdb->prefix . UB_USER_ASSIGNMENT_TABLE_NAME , 
					array(
						'condition_id' => $condition_id,
						'user_id' => $user_id,
						'created_dt' => current_time( 'mysql' ),
						'type' => $type,
						'value' => $value
					), 
					array( '%d', '%d', '%s', '%s', '%d' )
			);
			
			if ( $type == 'points' ) {
				
				$total_points = get_user_meta( $user_id, 'ub_points', true );
				if ( strlen( $total_points ) == 0 || ! is_numeric( $total_points ) ) {
					$total_points = 0;
				} else {
					$total_points = intval( $total_points );
				}
				
				$total_points += intval( $value );
				update_user_meta( $user_id, 'ub_points', $total_points );
			}
			
			do_action( 'ub_add_user_assignment', $condition_id, $user_id, $type, $value );
			
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::delete_user_assignment()
	 */
	public function delete_user_assignment( $condition_id = null, $user_id = 0, $type = 'badge', $value = 0 ) {
		
		global $wpdb;
		
		$where = array( 'user_id' => $user_id, 'type' => $type );
		$where_format = array( '%d', '%s' );
		
		if ( $type = 'badge' ) {
			$where['value'] = $value;
			array_push( $where_format, '%d' );
		}
		
		if ( isset( $condition_id ) ) {
			$where['condition_id'] = $value;
			array_push( $where_format, '%d' );
		}
	
		$result = $wpdb->delete( $wpdb->prefix . UB_USER_ASSIGNMENT_TABLE_NAME, $where, $where_format );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::get_user_badges()
	 */
	public function get_user_badges( $user_id, $filters = array() ) {
		
		
		extract( wp_parse_args( $filters, array(
				'to_date' => null,
				'from_date' => null
		) ) );
		
		global $wpdb;
		
		$query = 'SELECT	value AS badge_id
				FROM        ' . $wpdb->prefix . UB_USER_ASSIGNMENT_TABLE_NAME . '
				WHERE       user_id = %d AND type = "badge"';
		$added_to_query = true;
		
		if ( $to_date ) {
			if ( $added_to_query ) {
				$query .= ' AND';
			}
		
			$query .= ' created_dt <= "' . esc_sql( $to_date ) . '"';
			$added_to_query = true;
		}
		
		if ( $from_date ) {
			if ( $added_to_query ) {
				$query .= ' AND';
			}
		
			$query .= ' created_dt >= "' . esc_sql( $from_date ) . '"';
			$added_to_query = true;
		}

		$user_badges_results = $wpdb->get_results( $wpdb->prepare( $query, $user_id ) );
		
		$badges_list = array();
		
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
				
				global $wpdb;
				
				$users = $wpdb->get_results( $wpdb->prepare( '
						SELECT      user_id
						FROM        ' . $wpdb->prefix . UB_USER_ASSIGNMENT_TABLE_NAME . '
						WHERE       value = %d AND type = "badge"',
						$badge_id
				), ARRAY_N );
				
				$wpdb->show_errors();
			}
		
			return new UB_Badge( 
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
	 * @see UB_API::add_user_action()
	 */
	public function add_user_action( $action_name, $user_id, $meta = array() ) {
		
		global $wpdb;
		
		$wpdb->insert( $wpdb->prefix . UB_USER_ACTION_TABLE_NAME, array( 'user_id' => $user_id, 'action_name' => $action_name ), array( '%d', '%s') );	
		$user_action_id = $wpdb->insert_id;
		
		foreach ( $meta as $meta_key => $meta_value )  {
			
			$wpdb->insert( $wpdb->prefix . UB_USER_ACTION_META_TABLE_NAME, 
					array( 'user_action_id' => $user_action_id, 'meta_key' => $meta_key, 'meta_value' => $meta_value ), 
					array( '%d', '%s', '%s')
			);
				
		}
		
		ub_check_conditions( $action_name, $user_id );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::add_step()
	 */
	public function add_step( $condition_id, $label ) {
		
		$created_dt = current_time('mysql');
		
		global $wpdb;
		
		$wpdb->insert( $wpdb->prefix . UB_CONDITION_STEP_TABLE_NAME ,
				array( 'condition_id' => $condition_id, 'label' => $label, 'created_dt' => $created_dt ),
				array( '%s', '%s', '%s')
		);
		$step_id = $wpdb->insert_id;
		
		return new UB_Step( $step_id, $condition_id, $label, null, $created_dt );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::add_condition()
	 */
	public function add_condition( $name ) {
		global $wpdb;
		
		$created_dt = current_time('mysql');
		
		$wpdb->insert( $wpdb->prefix . UB_CONDITION_TABLE_NAME , array( 'name' => $name, 'created_dt' => $created_dt, 'enabled' => true ), array( '%s', '%s', '%d' ) );
		$condition_id = $wpdb->insert_id;
		
		return new UB_Condition( $condition_id, $name, array(), 0, $created_dt, true, null );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::get_conditions()
	 */
	public function get_conditions( $filters = array() ) {
		global $wpdb;
		
		$query = 'SELECT * FROM ' . $wpdb->prefix . UB_CONDITION_TABLE_NAME;
		
		$results = $wpdb->get_results( $query );
		
		$conditions = array();
		foreach ( $results as $row ) {
			$badges = ( strlen( trim ( $row->badges ) ) == 0 ) ? array() : preg_split( '/[\s,]+/', $row->badges );
			array_push( $conditions, new UB_Condition( $row->condition_id, $row->name, $badges, $row->points, $row->created_dt, $row->enabled, $row->assignment_expiry ) );
		}
		
		return $conditions;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::get_conditions()
	 */
	public function get_condition( $condition_id ) {
		global $wpdb;
	
		$query = 'SELECT * FROM ' . $wpdb->prefix . UB_CONDITION_TABLE_NAME . ' WHERE condition_id = ' . intval( $condition_id );
	
		$row = $wpdb->get_row( $query );
	
		if ( $row != null ) {
			$badges = ( strlen( trim ( $row->badges ) ) == 0 ) ? array() : preg_split( '/[\s,]+/', $row->badges );
			return new UB_Condition( $row->condition_id, $row->name, $badges, $row->points, $row->created_dt, $row->enabled, $row->assignment_expiry );
		}
		
		return null;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::get_badges()
	 */
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
				$users = $wpdb->get_results( $wpdb->prepare( '
							SELECT      user_id
							FROM        ' . $wpdb->prefix . UB_USER_ASSIGNMENT_TABLE_NAME . '
							WHERE       value = %d AND type = "badge"',
						$row->ID
				), ARRAY_N );
			}
			
			$badge = new UB_Badge(
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
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::get_actions()
	 */
	public function get_actions( $filters = array( 'enabled' => true ) ) {
		global $wpdb;
		
		$query = 'SELECT * FROM ' . $wpdb->prefix . UB_ACTION_TABLE_NAME;
		
		if ( isset( $filters['enabled'] ) && $filters['enabled'] == true ) {
			$query .= ' WHERE enabled = 1';			
		}
		
		$results = $wpdb->get_results( $query );
		
		$actions = array();
		foreach ( $results as $row ) {
			array_push( $actions, new UB_Action($row->name, $row->description, $row->source ) );
		}
		
		return $actions;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::delete_step()
	 */
	public function delete_step( $step_id ) {
		global $wpdb;
		
		$wpdb->delete( $wpdb->prefix . UB_CONDITION_STEP_TABLE_NAME ,
				array( 'step_id' => $step_id ),
				array( '%d' )
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::delete_condition()
	 */
	public function delete_condition( $condition_id ) {
		global $wpdb;
		
		$wpdb->delete( $wpdb->prefix . UB_CONDITION_TABLE_NAME ,
				array( 'condition_id' => $condition_id ),
				array( '%d' )
		);
		
		$steps = $wpdb->get_col( $wpdb->prepare( 'SELECT step_id FROM ' . $wpdb->prefix . UB_CONDITION_STEP_TABLE_NAME . ' WHERE condition_id = %d', $condition_id ) );
		
		$wpdb->delete( $wpdb->prefix . UB_CONDITION_STEP_TABLE_NAME ,
				array( 'condition_id' => $condition_id ),
				array( '%d' )
		);
		
		$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . UB_CONDITION_STEP_META_TABLE_NAME . ' WHERE step_id IN ( ' . implode(',', $steps ) . ')' );

		$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . UB_USER_ASSIGNMENT_TABLE_NAME . ' WHERE condition_id = ' . $condition_id );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::save_condition()
	 */
	public function save_condition( $condition ) {
		
		global $wpdb;
		
		// TODO serialize badges?
		
		$result = $wpdb->update( $wpdb->prefix . UB_CONDITION_TABLE_NAME , 
				array( 
						'name' => $condition->name,
						'badges' => implode(',', $condition->badges ),
						'points' => $condition->points,
						'enabled' => $condition->enabled,
						'assignment_expiry' => $condition->assignment_expiry
				),
				array( 'condition_id' => $condition->condition_id ),
				array( '%s', '%s', '%d', '%d', '%s' ),
				array( '%d' ) 
		);
		
		// TODO recalculate points usermeta in case condition points has been updated
		
		foreach ( $condition->steps as $step ) {
			$this->save_step( $step );
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::save_step()
	 */
	public function save_step( $step ) {
		
		global $wpdb;
		
		$wpdb->update( $wpdb->prefix . UB_CONDITION_STEP_TABLE_NAME ,
				array( 'label' => $step->label, 'action_name' => $step->action_name ),
				array( 'step_id' => $step->step_id ),
				array( '%s', '%s' ),
				array( '%d' )
		);
		
		foreach ( $step->step_meta as $meta ) {
			$this->save_step_meta( $step->step_id, $meta['key'], $meta['value'] );
		}
		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::save_step_meta()
	 */
	public function save_step_meta( $step_id, $meta_key, $meta_value = '' ) {
			
		global $wpdb;
		
		$exists = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $wpdb->prefix . UB_CONDITION_STEP_META_TABLE_NAME . ' WHERE step_id = ' . esc_sql( $step_id ) . ' AND meta_key = "' . esc_sql( $meta_key ) . '"' );
		
		if ( $exists ) { // > 0
			$result = $wpdb->update( $wpdb->prefix . UB_CONDITION_STEP_META_TABLE_NAME,
					array( 'meta_value' => $meta_value ),
					array( 'step_id' => $step_id, 'meta_key' => $meta_key ),
					array( '%s' ),
					array( '%d', '%s' )
			);
		} else {
			$wpdb->insert( $wpdb->prefix . UB_CONDITION_STEP_META_TABLE_NAME , array( 'step_id' => $step_id, 'meta_key' => $meta_key, 'meta_value' => $meta_value ), array( '%d', '%s', '%s' ) );
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::get_step_meta_value()
	 */
	public function get_step_meta_value( $step_id, $meta_key ) {
		global $wpdb;
		
		$value = $wpdb->get_var( 'SELECT meta_value FROM ' . $wpdb->prefix . UB_CONDITION_STEP_META_TABLE_NAME . ' WHERE step_id = ' . esc_sql( $step_id ) . ' AND meta_key = "' . esc_sql( $meta_key ) . '"' );
	
		if ( $value == null ) {
			$value = '';
		}
		
		return $value;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see UB_API::add_user_action_meta($action_name, $meta_key, $meta_value)
	 */
	public function add_user_action_meta( $user_action_id, $meta_key, $meta_value = '' ) {
		global $wpdb;
		
		// TODO replace if id exists
		
		$wpdb->insert( $wpdb->prefix . UB_USER_ACTION_META_TABLE_NAME ,
				array( 'user_action_id' => $user_action_id, 'meta_key' => $meta_key, 'meta_value' => $meta_value ),
				array( '%s', '%s', '%s')
		);
	}
}