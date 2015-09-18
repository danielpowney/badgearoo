<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Badge class
 * 
 * @author dpowney
 *
 */
class BROO_Badge {
	
	public $id = 0;
	public $title = null;
	public $content = null;
	public $excerpt = null;
	public $created_dt = null;
	public $users = array();
	public $badge_html;
	public $badge_icon;
	public $badge_color;
	public $permalink;
	
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
		$this->badge_icon = get_post_meta( $id, 'broo_badge_icon', true );
		$this->badge_html = get_post_meta( $id, 'broo_badge_html', true );
		$this->badge_color = get_post_meta( $id, 'broo_badge_color', true );
		
		if ( $this->badge_color == null || $this->badge_color == '' ) {
			$this->badge_color = '#fc0'; // default to Gold
		}
		
		/**
		 * Template tags:
		 * {count}
		 * {title%
		 * %badge_id%
		 * %excerpt%
		 * %badge_icon%
		 * %content%
		 * $badge_color%
		 */
		$template_tags = array(
				'{title}' => trim( $this->title ),
				'{badge_id}' => trim( $this->id ),
				'{excerpt}' => trim( $this->excerpt ),
				'{content}' => $this->content,
				'{permalink}' => get_permalink( $this->id ),
				'{badge_icon}' => $this->badge_icon,
				'{badge_color}' => $this->badge_color
		);
		
		foreach ( $template_tags as $string => $value ) {
			$this->badge_html = str_replace( $string, $value, $this->badge_html );
		}

	}
}