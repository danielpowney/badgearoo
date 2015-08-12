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

	public $total_count = 0;
	public $approved_count = 0;
	public $pending_count = 0;
	public $unapproved_count = 0;
	
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
	 * Retrieve the view types
	 *
	 * @access public
	 * @since 2.1.7
	 * @return array $views All the views available
	 */
	public function get_views() {
		$current        = isset( $_GET['status'] ) ? $_GET['status'] : '';
	
		$total_count    = '&nbsp;<span class="count">(' . $this->total_count    . ')</span>';
		$approved_count = '&nbsp;<span class="count">(' . $this->approved_count . ')</span>';
		$pending_count  = '&nbsp;<span class="count">(' . $this->pending_count  . ')</span>';
		$unapproved_count = '&nbsp;<span class="count">(' . $this->unapproved_count . ')</span>';
		
		$views = array(
				'all'		=> sprintf( '<a href="%s"%s>%s</a>', remove_query_arg( array( 'status', 'paged' ) ), $current === 'all' || $current == '' ? ' class="current"' : '', __( 'All', 'user-badges' ) . $total_count ),
				'approved'	=> sprintf( '<a href="%s"%s>%s</a>', add_query_arg( array( 'status' => 'approved', 'paged' => FALSE ) ), $current === 'approved' ? ' class="current"' : '', __( 'Approved', 'user-badges' ) . $approved_count ),
				'pending'	=> sprintf( '<a href="%s"%s>%s</a>', add_query_arg( array( 'status' => 'pending', 'paged' => FALSE ) ), $current === 'pending' ? ' class="current"' : '', __( 'Pending', 'user-badges' ) . $pending_count ),
				'unapproved'	=> sprintf( '<a href="%s"%s>%s</a>', add_query_arg( array( 'status' => 'unapproved', 'paged' => FALSE ) ), $current === 'unapproved' ? ' class="current"' : '', __( 'Unapproved', 'user-badges' ) . $unapproved_count )
		);
	
		return apply_filters( 'ub_user_assignment_views', $views );
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
				'expiry_dt' 	=> __( 'Expiry Dt', 'user-badges' ),
				'status'		=> __( 'Status', 'user-bades' )
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
		$status = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : null;

		$query = 'SELECT * FROM ' . $wpdb->prefix . UB_USER_ASSIGNMENT_TABLE_NAME;
		
		if ( $user_id != 0 || $badge_id != 0 || $type || $status ) {
			
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
			
			if ( $status ) {
				if ( $added_to_query ) {
					$query .= ' AND';
				}
					
				$query .= ' status = "' . esc_sql( $status ) . '"';
				$added_to_query = true;
			}
			
		}
		
		// get counts of each status
		$this->set_view_counts( array(
				'user_id' => $user_id,
				'badge_id' => $badge_id,
				'type' => $type
		) );
		
		$this->items = $wpdb->get_results( $query, ARRAY_A );
	}
	
	/**
	 * Sets view counts
	 * 
	 * @param unknown $query
	 */
	function set_view_counts( $params = array() ) {
		
		global $wpdb;
		$query = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . UB_USER_ASSIGNMENT_TABLE_NAME;
		
		$added_to_query = false;
		if ( ( isset ( $params['user_id'] ) && $params['user_id'] != 0 ) 
				|| ( isset ( $params['badge_id'] ) && $params['badge_id'] != 0 ) 
				|| isset ( $params['type'] ) ) {
			
			$query .= ' WHERE';
				
			if ( isset ( $params['user_id'] ) && $params['user_id'] != 0 ) {
				$query .= ' user_id = ' . intval( $params['user_id'] );
				$added_to_query = true;
			}
				
			if ( isset ( $params['badge_id'] ) && $params['badge_id'] != 0 ) {
				if ( $added_to_query ) {
					$query .= ' AND';
				}
		
				$query .= ' badge_id = ' . intval( $params['badge_id'] );
				$added_to_query = true;
			}
				
			if ( isset( $params['type'] ) ) {
				if ( $added_to_query ) {
					$query .= ' AND';
				}
					
				$query .= ' type = "' . esc_sql( $params['type'] ) . '"';
				$added_to_query = true;
			}
				
		}
		
		$this->total_count = intval( $wpdb->get_var( $query ) );
		
		if ( ! $added_to_query ) {
			$query .= ' WHERE';
		} else {
			$query .= ' AND';
		}
		
		$this->approved_count = intval( $wpdb->get_var( $query . ' status = "approved"' ) );
		$this->pending_count = intval( $wpdb->get_var( $query . ' status = "pending"' ) );
		$this->unapproved_count = intval( $wpdb->get_var( $query . ' status = "unapproved"' ) );
	
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
					echo date( 'F j, Y, g:ia', strtotime( $item[$column_name] ) );
				}
				break;
			case 'status' :
				$row_id = $item['id'];
				?>
				<div id="ub-status-text-<?php echo $row_id ?>">
					<span id="ub-text-approve-<?php echo $row_id; ?>"<?php if ( $item[$column_name] != 'approved' ) { echo ' style="display: none"'; } ?>><?php _e( 'Approved', 'user-badges' ); ?></span>
					<span id="ub-text-pending-<?php echo $row_id; ?>"<?php if ( $item[$column_name] != 'pending' ) { echo ' style="display: none"'; } ?>><?php _e( 'Pending', 'user-badges' ); ?></span>
					<span id="ub-text-unapprove-<?php echo $row_id; ?>"<?php if ( $item[$column_name] != 'unapproved' ) { echo ' style="display: none"'; } ?>><?php _e( 'Unapproved', 'user-badges' ); ?></span>
				</div>
				<div id="ub-row-actions-<?php echo $row_id; ?>" class="row-actions">
					<?php 
					if ( $item[$column_name] == 'approved' ) {
						?>
						<a href="#" id="ub-anchor-unapprove-<?php echo $row_id; ?>" class="ub-unapprove"><?php _e( 'Unapprove', 'user-badges' ); ?></a>
						<?php
					} else {
						?>
						<a href="#" id="ub-anchor-unapprove-<?php echo $row_id; ?>" class="ub-approve"><?php _e( 'Approve', 'user-badges' ); ?></a>
						<?php
					}
					?>
		
				</div>
				<?php 
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
				'delete'    => __( 'Delete', 'user-badges' ),
				'approve'	=> __( 'Approve', 'user-badges' ),
				'unapprove'	=> __( 'Unapprove', 'user-badges' )
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

function ub_update_user_assignment_status() {
	
	$ajax_nonce = $_POST['nonce'];
	
	if ( wp_verify_nonce( $ajax_nonce, User_Badges::ID.'-nonce' ) ) {
	
		$assignment_id = ( isset( $_POST['assignmentId'] ) && is_numeric( $_POST['assignmentId'] ) ) ? intval( $_POST['assignmentId'] ) : null;
		$status = ( $_POST['status'] == 'approve' ) ? 'approved' : 'unapproved';
			
		if ( ! $assignment_id ) {
			die();
		}
			
		global $wpdb;
			
		$wpdb->update( $wpdb->prefix . UB_USER_ASSIGNMENT_TABLE_NAME, array( 'status' => $status ), array( 'id' => $assignment_id ) );
			
		do_action( 'ub_user_assignment_approved', $assignment_id );
			
		echo json_encode( array (
				'data' => array( 
						'status' => $status,
						'approve'	=> __( 'Approve', 'user-badges' ),
						'unapprove'	=> __( 'Unapprove', 'user-badges' )
				),
				'assignment_id' => $assignment_id
		) );
	}
	
	die();
	
}