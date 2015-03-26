<?php

class UB_Condition {
	
	public $func;
	public $action;
	
	/**
	 * Constructor
	 */
	function __construct( $func, $action ) {
		$this->func = $func;
		$this->action = $action;
	}
	/**
	 * Checks if condition is met
	 */
	public function check( $params ) {
		$result = apply_filters( 'ub_condition_' . $action . '_check', $func( $params ) );
		
		if ( $result ) {
			do_action( 'ub_condition_check_success', $action, $params );
		} else {
			do_action( 'ub_condition_check_fail', $action, $params );
		}
	}
}