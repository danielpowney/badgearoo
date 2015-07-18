<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * UB_Assignments_Table class
 * 
 * @author dpowney
 */
class UB_Assignments_Table extends WP_List_Table {

	/**
	 * Constructor
	 */
	function __construct() {
		
		parent::__construct( array(
				'singular'		=> __( 'Assignment', 'user-badges' ),
				'plural' 		=> __( 'Assignments', 'user-badges' ),
				'ajax'			=> false
		) );
	}

	/**
	 * (non-PHPdoc)
	 * @see WP_List_Table::extra_tablenav()
	 */
	function extra_tablenav( $which ) {
		
		if ( $which == 'top' ){
			
			$user_id = 0;
			if ( isset( $_REQUEST['user-id'] ) && strlen( trim( $_REQUEST['user-id'] ) ) > 0 ) {
				$user_id = $_REQUEST['user-id'];
			}
			$badge_id = 0;
			if ( isset( $_REQUEST['badge-id'] ) && strlen( trim( $_REQUEST['badge-id'] ) ) > 0 ) {
				$badge_id = $_REQUEST['badge-id'];
			}
			$type = null;
			if ( isset( $_REQUEST['type'] ) && strlen( trim( $_REQUEST['type'] ) ) > 0 ) {
				$type = $_REQUEST['type'];
			}
			
			?>
			<div class="alignleft filters">	
				<?php wp_dropdown_users( array(
						'name'  => 'user-id',
						'id' => 'user-id',
						'show_option_all' => __( 'All users', 'user-badges' ),
						'selected' => $user_id
				) ); ?>
				<?php ub_dropdown_badges( array(
						'name' => 'badge-id',
						'show_option_all' => true,
						'echo' => true,
						'selected' => $badge_id
				) ); ?>
				<label for="type"><?php _e( 'Type:', 'user-badges' ); ?></label>
				<select name="type" id="type">
					<option value=""<?php if ( $type == null ) echo ' selected'; ?>><?php _e( 'All types', 'user-badges' ); ?></option>
					<option value="badge"<?php if ( $type == 'badges' ) echo ' selected'; ?>><?php _e( 'Badge', 'user-badges' ); ?></option>
					<option value="points"<?php if ( $type == 'points' ) echo ' selected'; ?>><?php _e( 'Points', 'user-badges' ); ?></option>
				</select>
				<input type="submit" class="button" value="<?php _e( 'Filter', 'user-badges' ); ?>"/>
			</div>
			<?php
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
				'id'			=> '',
				'user_id'		=> __( 'User', 'user-badges'  ),
				'condition_id'	=> __( 'Condition', 'user-badges'  ),
				'type'			=> __( 'Type', 'user-badges'  ),
				'value'			=> __( 'Assignment', 'user-badges' ),
				'created_dt' 	=> __( 'Created Dt', 'user-badges' ),
				'expiry_dt' 	=> __( 'Expiry Dt', 'user-badges' )
		);
		
		return apply_filters( 'ub_assignments_table_columns', $columns );
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
		$hidden = array( 'id' );
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		
		$user_id = 0;
		if ( isset( $_REQUEST['user-id'] ) && strlen( trim( $_REQUEST['user-id'] ) ) > 0 ) {
			$user_id = intval( $_REQUEST['user-id'] );
		}
		$badge_id = 0;
		if ( isset( $_REQUEST['badge-id'] ) && strlen( trim( $_REQUEST['badge-id'] ) ) > 0 ) {
			$badge_id = intval( $_REQUEST['badge-id'] );
		}
		$type = null;
		if ( isset( $_REQUEST['type'] ) && strlen( trim( $_REQUEST['type'] ) ) > 0 ) {
			$type = $_REQUEST['type'];
		}

		$query = 'SELECT * FROM ' . $wpdb->prefix . UB_USER_ASSIGNMENT_TABLE_NAME;
		
		if ( $user_id != 0 || $badge_id != 0 || $type ) {
			$query .= ' WHERE';
			$added_to_query = false;
			
			if ( $user_id != 0 ) {
				$query .= ' user_id = ' . $user_id;
				$added_to_query = true;
			}
			
			if ( $badge_id != 0 ) {
				if ( $added_to_query ) {
					$query .= ' AND';
				}
				
				$query .= ' badge_id = ' . $badge_id;
				$added_to_query = true;
			}
			
			if ( $type ) {
				if ( $added_to_query ) {
					$query .= ' AND';
				}
			
				$query .= ' type = "' . esc_sql( $type ) . '"';
				$added_to_query = true;
			}
		}
		
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
			case 'value' :
				if ( $item['type'] == 'badge' ) {
					$badge_id = intval( $item[$column_name] );
					$post_link = esc_html( get_the_title( $badge_id ) );
					if ( current_user_can( 'edit_post', $badge_id ) ) {
						$post_link = "<a href='" . esc_url( get_edit_post_link( $badge_id ) ) . "'>";
						$post_link .= esc_html( get_the_title( $badge_id ) ) . '</a>';
					}
					echo $post_link;
				} else {
					echo $item[$column_name];
				}
				break;
			case 'user_id' :
				$user_id = intval( $item[$column_name] );
				$user = get_userdata( $user_id );
				
				if ( $user ) {
					?>
					<a href="<?php echo get_author_posts_url( $user_id ); ?>"><?php echo esc_html( $user->display_name ); ?></a>
					<?php
				}
				break;
			case 'condition_id' :
				$condition_id = intval( $item[$column_name] );
				$condition = User_Badges::instance()->api->get_condition( $condition_id );
				if ( $condition ) {
					echo $condition->name;
				}
				break;
			case 'type' :
				if ( $item[$column_name] == 'badge' ) {
					_e( 'Badge', 'user-badges' );
				} else {
					_e( 'Points', 'user-badges' );
				}
				break;
			case 'created_dt' :
			case 'expiry_dt' :
				if ( $item[$column_name] ) {
					echo date( 'F j, Y', strtotime( $item[$column_name] ) );
				}
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
		
		$id = $item['id'];
		
		return sprintf(
				'<input type="checkbox" name="cb[]" value="%s" />', $id
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
		
		global $wpdb;
		
		if ( $this->current_action() === 'delete' ) {
			
			$checked = ( is_array( $_REQUEST['cb'] ) ) ? $_REQUEST['cb'] : array( $_REQUEST['cb'] );
			
			foreach( $checked as $id ) {
				User_Badges::instance()->api->delete_user_assignment( $id );
			}
			
			echo '<div class="updated"><p>' . __( 'Assignment(s) deleted successfully.', 'user-badges' ) . '</p></div>';
		}
	}
}