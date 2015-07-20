<?php

/**
 * Settings class
*
* @author dpowney
*/
class UB_Settings {
	
	public $actions_enabled = null;
	
	/**
	 * Constructor
	 */
	function __construct() {
		
		if ( is_admin() ) {
			add_action( 'admin_init', array( &$this, 'default_settings' ) );
		}
	}
	
	function default_settings() {
		
		$this->actions_enabled = (array) get_option( 'ub_actions_enabled' );
		
		$this->actions_enabled = apply_filters( 'ub_default_actions_enabled', $this->actions_enabled );
		
		update_option( 'ub_actions_enabled', $this->actions_enabled );
	}
	
}