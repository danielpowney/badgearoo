<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * UB_Badges_Table class
 * 
 * @author dpowney
 */
class UB_Badges_Table extends WP_List_Table {

	/**
	 * Constructor
	 */
	function __construct() {
		
		parent::__construct( array(
				'singular'		=> __( 'Badge', 'user-badges' ),
				'plural' 		=> __( 'Badges', 'user-badges' ),
				'ajax'			=> false
		) );
	}

	/**
	 * (non-PHPdoc)
	 * @see WP_List_Table::extra_tablenav()
	 */
	function extra_tablenav( $which ) {
		
		if ( $which == 'top' ){
			echo "";
		}
		
		if ( $which == 'bottom' ){
			echo "";
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see WP_List_Table::get_columns()
	 */
	function get_columns() {
		
		$columns = array(
				'cb' 			=> '<input type="checkbox" />',
				'badge'			=> __( 'Badge' , 'user-badges' ),
				'name'			=> __( 'Name', 'user-badges'  ),
				'description'	=> __( 'Description', 'user-badges'  ),
				'enabled'		=> __( 'Enabled', 'user-badges'  ),
				'actions'		=> __( 'Actions', 'user-badges' )
		);
		
		return apply_filters( 'ub_badges_table_columns', $columns );
	}

	/**
	 * (non-PHPdoc)
	 * @see WP_List_Table::prepare_items()
	 */
	function prepare_items() {
		global $wpdb;
		
		// Process any bulk actions first
		$this->process_bulk_action();

		// Register the columns
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$query = 'SELECT * FROM ' . $wpdb->prefix . UB_BADGES_TABLE_NAME;
		
		$this->items = $wpdb->get_results( $query, ARRAY_A );
	}

	/**
	 * Default column
	 * @param unknown_type $item
	 * @param unknown_type $column_name
	 * @return unknown|mixed
	 */
	function column_default( $item, $column_name ) {
		
		switch( $column_name ) {
			case 'cb' :
				return $item[ $column_name ];
				break;
			case 'badge' :
				echo '<img src="' . $item[ 'url' ] . '" />';
				break;
			case 'name' : 
				echo $item[ 'name' ];
				break;
			case 'description' :
				echo $item[ 'description'];
				break;
			case 'enabled' :
				echo ( $item[ 'enabled' ] == 1 ) ? '<span class="dashicons dashicons-yes"></span>' . __( 'Yes', 'user-badges' ) : '<span class="dashicons dashicons-no"></span>';
				break;
			case 'actions' :
				echo '<a href="#" id="" class="">' . __( 'Edit', 'user-badges' ) . '</a>';
				break;
			default:
				echo $item[ $column_name ];
		}
	}
	
	/**
	 * checkbox column
	 * @param unknown_type $item
	 * @return string
	 */
	function column_cb( $item ) {
		
		$row_id = $item['name'];
		
		return sprintf(
				'<input type="checkbox" name="delete[]" value="%s" />', $row_id
		);
	}

	
	/**
	 * (non-PHPdoc)
	 * @see WP_List_Table::get_bulk_actions()
	 */
	function get_bulk_actions() {
		
		$bulk_actions = array(
				'delete'    => __( 'Delete', 'user-badges' )
		);
		
		return $bulk_actions;
	}

	/**
	 * Handles bulk actions
	 */
	function process_bulk_action() {
		
		if ( $this->current_action() === 'delete' ) {
			global $wpdb;

			$checked = ( is_array( $_REQUEST['delete'] ) ) ? $_REQUEST['delete'] : array( $_REQUEST['delete'] );
			
			foreach( $checked as $name ) {
				
				User_Badges::instance()->api->delete_badge( $name );
				
			}
			
			echo '<div class="updated"><p>' . __( 'Badge(s) deleted successfully.', 'user-badges' ) . '</p></div>';
		}
	}
}