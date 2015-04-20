<?php

/**
 * Step for a condition
 * 
 * @author dpowney
 *
 */
class UB_Step {
	
	public $step_id;
	public $condition_id;
	public $label;
	public $action_name;
	public $created_dt;
	
	/**
	 * Constructor
	 */
	function __construct( $step_id, $condition_id, $label, $action_name, $created_dt ) {
		$this->step_id = intval( $step_id );
		$this->condition_id = intval( $condition_id );
		$this->label = $label;
		$this->action_name = $action_name;
		$this->created_dt = $created_dt;
	}
}