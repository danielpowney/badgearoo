<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class BROO_Condition {
	
	public $condition_id;
	public $steps;
	public $name;
	public $badges;
	public $points;
	public $created_dt;
	public $enabled;
	public $expiry_unit;
	public $expiry_value;
	public $recurring;
	
	/**
	 * Constructor
	 */
	function __construct( $condition_id, $name, $badges = array(), $points = 0, $created_dt, $enabled = true, $expiry_unit = null, $expiry_value = null, $recurring = false ) {
		
		$this->condition_id = $condition_id;
		$this->name = $name;
		$this->badges = ( ! is_array( $badges ) ) ? array() : $badges;
		$this->points = intval( $points );
		$this->enabled = ( $enabled == true || $enabled == "true" ) ? true : false;
		$this->steps = array();
		global $wpdb;
		$results = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . BROO_CONDITION_STEP_TABLE_NAME . ' WHERE condition_id = ' . $condition_id );
		foreach ( $results as $row ) {
			array_push( $this->steps, new BROO_Step( 
					$row->step_id,
					$row->condition_id,
					apply_filters( 'wpml_translate_single_string', $row->label, 'badgearoo', sprintf( __( 'Condition %d Step %d Label', 'badgearoo' ), $row->condition_id, $row->step_id ) ),
					$row->action_name, 
					$row->created_dt
			) );
		}
		$this->created_dt = $created_dt;
		$this->expiry_unit = $expiry_unit;
		$this->expiry_value = $expiry_value;
		$this->recurring = $recurring;
	}
	
	/**
	 * Checks if condition is met
	 */
	public function check( $user_id ) {
		
		$condition_result = true;
		
		/*
		// AND
		if ( $steps_and ) {
			$condition_result = apply_filters( 'broo_condition_check_and', $condition_result, $this, $user_id );
		} else { // OR
		}
		*/
		
		if ( $condition_result ) {
			foreach ( $this->steps as $step ) {
					
				if ( $step->action_name == null ) {
					$condition_result = false; // not setup correctly
					break;
				}
				
				$step_result = apply_filters( 'broo_condition_step_check_' . $step->action_name, true, $step, $user_id, $step->action_name );
				
				if ( $step_result == false ) { // if any step is false, condition is not met
					$condition_result = false;
					break;
				}
			}
		}
		
		// work out expiry_dt based on expiry_unit and expiry_value e.g. 1 month
		$expiry_dt = null;
		
		if ( is_numeric( $this->expiry_value ) && intval( $this->expiry_value ) != 0 && strlen( $this->expiry_unit ) > 0 ) {
		
			$time_format = '+' . $this->expiry_value . ' '.  $this->expiry_unit;
			if ( $this->expiry_value > 1 ) {
				$time_format .= 's';
			}
		
			$expiry_dt = date( 'Y-m-d H:i:s', strtotime( $time_format ) );
		}
				
		// if you get this far, condition has been met
		if ( $condition_result == true && count( $this->steps ) > 0 ) {
			
			foreach ( $this->badges as $badge_id ) {
				Badgearoo::instance()->api->add_user_assignment( $this->condition_id, $user_id, 'badge', $badge_id, $expiry_dt );
			}
			
			if ( $this->points > 0 ) {
				Badgearoo::instance()->api->add_user_assignment( $this->condition_id, $user_id, 'points', $this->points, $expiry_dt );
			}
		}
	}
}