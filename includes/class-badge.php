<?php
/**
 * Badge class
 * 
 * @author dpowney
 *
 */
class UB_Badge {
	
	public $id = 0;
	public $title = null;
	public $content = null;
	public $excerpt = null;
	public $created_dt = null;
	public $users = array();
	public $logo_type;
	public $logo_html;
	public $logo_image;
	
	/**
	 * Constructor
	 */
	function __construct( $id, $title, $content, $excerpt, $created_dt, $users = array() ) {
		$this->id = $id;
		$this->title = $title;
		$this->content = $content;
		$this->excerpt = $excerpt;
		$this->created_dt = $created_dt;
		$this->users = $users;
		$this->logo_type = get_post_meta( $id, 'ub_logo_type', true );
		$this->logo_image = get_post_meta( $id, 'ub_logo_image', true );
		$this->logo_html = get_post_meta( $id, 'ub_logo_html', true );
		
		if ( $this->logo_type == '' ) {
			$this->logo_type = 'none';
		}
	}
}