<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Actions class
 * 
 * @author dpowney
 *
 */
class BROO_Action {
	
	public $name;
	public $description;
	public $source;
	public $enabled;
	
	/**
	 * Constructor
	 */
	function __construct( $name, $description, $source, $enabled = true ) {
		$this->name = $name;
		$this->description = $description;
		$this->source = $source;
		$this->enabled = $enabled;
	}
}