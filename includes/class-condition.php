<?php

class UB_Condition {
	
	public $condition_id;
	public $steps;
	public $name;
	public $badge;
	public $badge_id;
	public $points;
	public $status;
	public $created_dt;
	
	/**
	 * Constructor
	 */
	function __construct( $condition_id, $name, $badge_id, $points, $created_dt, $status, $load_badge = true, $load_steps = true ) {
		$this->condition_id = $condition_id;
		$this->name = $name;
		$this->badge_id = $badge_id;
		
		// get badge
		if ( $load_badge == true ) {
			$this->badge = User_Badges::instance()->api->get_badge( $badge_id, false );
		}
		
		$this->steps = array();
		
		// get steps
		if ( $load_steps == true ) {
			
			global $wpdb;
			$results = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . UB_CONDITION_STEP_TABLE_NAME . ' WHERE condition_id = ' . $condition_id );
			foreach ( $results as $row ) {
				array_push( $this->steps, new UB_Step( $row->id, $row->condition_id, $row->label, $row->action_name, $row->created_dt ) );
			}
		}
		
		$this->points = intval( $points );
		$this->created_dt = $created_dt;
		$this->status = $status;
	}
	/**
	 * Checks if condition is met
	 */
	public function check() {
	}
}