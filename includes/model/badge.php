<?php
/**
 * Badge class
 * 
 * @author dpowney
 *
 */
class Badge {
	
	public $name = null;
	public $description = null;
	public $url = null;
	public $enabled = null;
	public $created_dt = null;
	public $users = array();
	
	/**
	 * Constructor
	 */
	function __construct( $name, $description, $url, $enabled = true, $created_dt, $users = array() ) {
		$this->name = $name;
		$this->description = $description;
		$this->url = $url;
		$this->enabled = $enabled;
		$this->created_dt = $created_dt;
		$this->users = $users;
	}
}