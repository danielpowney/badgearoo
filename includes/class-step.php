<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Step for a condition
 * 
 * @author dpowney
 *
 */
class BROO_Step {
	
	public $step_id;
	public $condition_id;
	public $label;
	public $action_name;
	public $created_dt;
	public $step_meta;
	
	/**
	 * Constructor
	 */
	function __construct( $step_id, $condition_id, $label, $action_name, $created_dt = null, $step_meta = array() ) {
		$this->step_id = intval( $step_id );
		$this->condition_id = intval( $condition_id );
		$this->label = $label;
		$this->action_name = $action_name;
		$this->created_dt = $created_dt;
		$this->step_meta = $step_meta;
	}
}