<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * UB_Actions_Table class
 * 
 * @author dpowney
 */
class UB_Actions_Table extends WP_List_Table {

	/**
	 * Constructor
	 */
	function __construct() {
		
		parent::__construct( array(
				'singular'		=> __( 'Action', 'user-badges' ),
				'plural' 		=> __( 'Actions', 'user-badges' ),
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
				'name'			=> __( 'Name', 'user-badges'  ),
				'description'	=> __( 'Description', 'user-badges'  ),
				'source'		=> __( 'Source', 'user-badges'  ),
				'enabled'		=> __( 'Enabled', 'user-badges'  )
		);
		
		return apply_filters( 'ub_actions_table_columns', $columns );
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

		$query = 'SELECT * FROM ' . $wpdb->prefix . UB_ACTION_TABLE_NAME;
		
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
			case 'description' :
			case 'source' :
			case 'name' : 
				echo $item[ $column_name ];
				break;
			case 'enabled' :
				echo ( $item[ 'enabled' ] == 1 ) ? '<span class="dashicons dashicons-yes"></span>' . __( 'Yes', 'user-badges' ) : '<span class="dashicons dashicons-no"></span>';
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
				'<input type="checkbox" name="cb[]" value="%s" />', $row_id
		);
	}

	
	/**
	 * (non-PHPdoc)
	 * @see WP_List_Table::get_bulk_actions()
	 */
	function get_bulk_actions() {
		
		$bulk_actions = array(
				'enable'    => __( 'Enable', 'user-badges' ),
				'disable'    => __( 'Disable', 'user-badges' )
		);
		
		return $bulk_actions;
	}

	/**
	 * Handles bulk actions
	 */
	function process_bulk_action() {
		
		global $wpdb;
		
		if ( $this->current_action() === 'enable' ) {
			
			$checked = ( is_array( $_REQUEST['cb'] ) ) ? $_REQUEST['cb'] : array( $_REQUEST['cb'] );
			
			foreach( $checked as $id ) {
				
				// TODO check API result
				User_Badges::instance()->api->enable_actions( $id );
			}
			
			echo '<div class="updated"><p>' . __( 'Action(s) enabled successfully.', 'user-badges' ) . '</p></div>';
		} else if ( $this->current_action() === 'disable' ) {
			
			$checked = ( is_array( $_REQUEST['cb'] ) ) ? $_REQUEST['cb'] : array( $_REQUEST['cb'] );
			
			foreach( $checked as $id ) {
			
				// TODO check API result
				User_Badges::instance()->api->disable_action( $id );
			}
				
			echo '<div class="updated"><p>' . __( 'Action(s) disabled successfully.', 'user-badges' ) . '</p></div>';
		}
	}
}