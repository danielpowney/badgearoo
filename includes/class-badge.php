<?php
/**
 * Badge class
 * 
 * @author dpowney
 *
 */
class UB_Badge {
	
	public $id = 0;
	public $name = null;
	public $description = null;
	public $created_dt = null;
	public $users = array();
	public $url = null;
	
	/**
	 * Constructor
	 */
	function __construct( $id, $name, $description, $created_dt, $users = array() ) {
		$this->id = $id;
		$this->name = $name;
		$this->description = $description;
		$this->created_dt = $created_dt;
		$this->users = $users;
		
		$this->url = wp_get_attachment_url( get_post_thumbnail_id( $id ) );
	}
}