<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * BROO_Assignments_Table class
 * 
 * @author dpowney
 */
class BROO_Assignments_Table extends WP_List_Table {

	public $total_count = 0;
	public $approved_count = 0;
	public $pending_count = 0;
	public $unapproved_count = 0;
	
	/**
	 * Constructor
	 */
	function __construct() {
		
		parent::__construct( array(
				'singular'		=> __( 'Assignment', 'badgearoo' ),
				'plural' 		=> __( 'Assignments', 'badgearoo' ),
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
				'all'		=> sprintf( '<a href="%s"%s>%s</a>', remove_query_arg( array( 'status', 'paged' ) ), $current === 'all' || $current == '' ? ' class="current"' : '', __( 'All', 'badgearoo' ) . $total_count ),
				'approved'	=> sprintf( '<a href="%s"%s>%s</a>', add_query_arg( array( 'status' => 'approved', 'paged' => FALSE ) ), $current === 'approved' ? ' class="current"' : '', __( 'Approved', 'badgearoo' ) . $approved_count ),
				'pending'	=> sprintf( '<a href="%s"%s>%s</a>', add_query_arg( array( 'status' => 'pending', 'paged' => FALSE ) ), $current === 'pending' ? ' class="current"' : '', __( 'Pending', 'badgearoo' ) . $pending_count ),
				'unapproved'	=> sprintf( '<a href="%s"%s>%s</a>', add_query_arg( array( 'status' => 'unapproved', 'paged' => FALSE ) ), $current === 'unapproved' ? ' class="current"' : '', __( 'Unapproved', 'badgearoo' ) . $unapproved_count )
		);
	
		return apply_filters( 'broo_user_assignment_views', $views );
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
			$order_by = 'created_dt';
			if ( isset( $_REQUEST['order-by'] ) && strlen( trim( $_REQUEST['order-by'] ) ) > 0 ) {
				$order_by = $_REQUEST['order-by'];
			}
			$expired = false;
			if ( isset( $_REQUEST['expired'] ) ) {
				$expired = true;
			}
			
			?>
			<div class="alignleft filters">	
				<?php wp_dropdown_users( array(
						'name'  => 'user-id',
						'id' => 'user-id',
						'show_option_all' => __( 'All users', 'badgearoo' ),
						'selected' => $user_id
				) ); ?>
				<?php broo_dropdown_badges( array(
						'name' => 'badge-id',
						'show_option_all' => true,
						'echo' => true,
						'selected' => $badge_id
				) ); ?>
				<label for="type"><?php _e( 'Type:', 'badgearoo' ); ?></label>
				<select name="type" id="type">
					<option value=""<?php if ( $type == null ) echo ' selected'; ?>><?php _e( 'All types', 'badgearoo' ); ?></option>
					<option value="badge"<?php if ( $type == 'badges' ) echo ' selected'; ?>><?php _e( 'Badge', 'badgearoo' ); ?></option>
					<option value="points"<?php if ( $type == 'points' ) echo ' selected'; ?>><?php _e( 'Points', 'badgearoo' ); ?></option>
				</select>
				<select name="order-by" id="order-by">
					<option value="newest"<?php if ( $order_by == 'newest' ) echo ' selected'; ?>><?php _e( 'Newest', 'badgearoo' ); ?></option>
					<option value="oldest"<?php if ( $order_by == 'oldest' ) echo ' selected'; ?>><?php _e( 'Oldest', 'badgearoo' ); ?></option>
					<option value="expires"<?php if ( $order_by == 'expires' ) echo ' selected'; ?>><?php _e( 'Expires', 'badgearoo' ); ?></option>
				</select>
				<input name="expired" id="expired" type="checkbox" <?php checked( true, $expired, true )?>><label for="expired"><?php _e( 'Include expired', 'badgearoo' ); ?></label>
				<input type="submit" class="button" value="<?php _e( 'Filter', 'badgearoo' ); ?>"/>
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
				'user_id'		=> __( 'User', 'badgearoo'  ),
				'condition_id'	=> __( 'Condition', 'badgearoo'  ),
				'type'			=> __( 'Type', 'badgearoo'  ),
				'value'			=> __( 'Assignment', 'badgearoo' ),
				'created_dt' 	=> __( 'Created Dt', 'badgearoo' ),
				'expiry_dt' 	=> __( 'Expiry Dt', 'badgearoo' ),
				'status'		=> __( 'Status', 'user-bades' )
		);
		
		return apply_filters( 'broo_assignments_table_columns', $columns );
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
		$expired = isset( $_REQUEST['expired'] ) ? true : false;
		$order_by = 'created_dt';

		$query = 'SELECT * FROM ' . $wpdb->prefix . BROO_USER_ASSIGNMENT_TABLE_NAME . ' a LEFT JOIN ' . $wpdb->posts . ' p'
				. ' ON ( a.type = "badge" AND a.value = p.ID AND p.post_status = "publish" )'
				. ' WHERE ( ( a.type = "badge" AND p.post_status = "publish" ) OR ( a.type = "points" ) )';
		
		$added_to_query = true;
			
		if ( $user_id != 0 ) {
			if ( $added_to_query ) {
				$query .= ' AND';
			}
			
			$query .= ' a.user_id = ' . $user_id;
			$added_to_query = true;
		}
		
		if ( $badge_id != 0 ) {
			if ( $added_to_query ) {
				$query .= ' AND';
			}
			
			$query .= ' a.type = "badge" && a.value = ' . $badge_id;
			$added_to_query = true;
		}
		
		if ( $type ) {
			if ( $added_to_query ) {
				$query .= ' AND';
			}
		
			$query .= ' a.type = "' . esc_sql( $type ) . '"';
			$added_to_query = true;
		}
		
		if ( $status ) {
			if ( $added_to_query ) {
				$query .= ' AND';
			}
				
		$query .= ' a.status = "' . esc_sql( $status ) . '"';
			$added_to_query = true;
		}
		
		if ( ! $expired ) {
			if ( $added_to_query ) {
				$query .= ' AND';
			}
				
			$query .= ' ( a.expiry_dt >= NOW() OR a.expiry_dt IS NULL )';
			$added_to_query = true;
		}
			
		if ( isset( $_REQUEST['order-by'] ) && strlen( trim( $_REQUEST['order-by'] ) ) > 0 ) {
			$order_by = $_REQUEST['order-by'];
		}
			
		if ( $order_by == 'oldest' ) {
			$query .= ' ORDER BY a.created_dt ASC';
		} else if ( $order_by == 'expires' ) {
			$query .= ' ORDER BY a.expiry_dt ASC';
		} else {
			$query .= ' ORDER BY a.created_dt DESC';
		}
		
		// pagination
		$items_per_page = 25;
		$page_num = ! empty( $_GET['paged'] ) ? $_GET['paged'] : '';
		if ( empty( $page_num ) || ! is_numeric( $page_num ) || $page_num <= 0 ) {
			$page_num = 1;
		}
		$offset = 0;
		if ( ! empty( $page_num ) && ! empty( $items_per_page ) ) {
			$offset = ( $page_num -1 ) * $items_per_page;
		}
		
		$query .= ' LIMIT ' . intval( $offset ) . ', ' . $items_per_page;
		
		// get counts of each status
		$this->set_view_counts( array(
				'user_id' => $user_id,
				'badge_id' => $badge_id,
				'type' => $type,
				'status' => $status,
				'expired' => $expired
		) );
		
		$total_items = intval( $this->total_count );
		if ( $status == 'approved' ) {
			$total_items = intval( $this->approved_count );
		} else if ( $status == 'pending' ) {
			$total_items = intval( $this->pending_count );
		} else if ( $status == 'unapproved' ) {
			$total_items = intval( $this->unapproved_count );
		}
		
		$total_pages = ceil( $total_items / $items_per_page );
		
		$this->set_pagination_args( array(
				'total_items' => $total_items,
				'total_pages' => $total_pages,
				'per_page' => $items_per_page
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
		$query = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . BROO_USER_ASSIGNMENT_TABLE_NAME . ' a LEFT JOIN ' . $wpdb->posts . ' p'
				. ' ON ( a.type = p.post_type AND a.value = p.ID AND p.post_status = "publish" )'
				. ' WHERE ( ( a.type = "badge" AND p.post_status = "publish" ) OR ( a.type = "points" ) )';
		
		$added_to_query = true;
			
		if ( isset ( $params['user_id'] ) && $params['user_id'] != 0 ) {
			if ( $added_to_query ) {
				$query .= ' AND';
			}
			
			$query .= ' a.user_id = ' . intval( $params['user_id'] );
			$added_to_query = true;
		}
			
		if ( isset ( $params['badge_id'] ) && $params['badge_id'] != 0 ) {
			if ( $added_to_query ) {
				$query .= ' AND';
			}
	
			$query .= ' a.id = ' . intval( $params['badge_id'] );
			$added_to_query = true;
		}
			
		if ( isset( $params['type'] ) ) {
			if ( $added_to_query ) {
				$query .= ' AND';
			}
				
			$query .= ' a.type = "' . esc_sql( $params['type'] ) . '"';
			$added_to_query = true;
		}
		
		if ( isset( $params['expired'] ) && ! $params['expired'] ) {
			if ( $added_to_query ) {
				$query .= ' AND';
			}
				
			$query .= ' ( a.expiry_dt >= NOW() OR a.expiry_dt IS NULL )';
			$added_to_query = true;
		}
		
		$this->total_count = intval( $wpdb->get_var( $query ) );
		
		if ( ! $added_to_query ) {
			$query .= ' WHERE';
		} else {
			$query .= ' AND';
		}
		
		$this->approved_count = intval( $wpdb->get_var( $query . ' a.status = "approved"' ) );
		$this->pending_count = intval( $wpdb->get_var( $query . ' a.status = "pending"' ) );
		$this->unapproved_count = intval( $wpdb->get_var( $query . ' a.status = "unapproved"' ) );
	
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
					$user_permalink = apply_filters( 'broo_user_permalink', get_author_posts_url( $user_id ), $user_id );
					?>
					<a href="<?php echo $user_permalink; ?>"><?php echo esc_html( $user->display_name ); ?></a>
					<?php
				}
				break;
			case 'condition_id' :
				$condition_id = intval( $item[$column_name] );
				$condition = Badgearoo::instance()->api->get_condition( $condition_id );
				if ( $condition ) {
					echo $condition->name;
				} else if ( $condition_id != 0 ) {
					printf( __( 'Condition %d has been deleted.', 'badgearoo' ), $condition_id );
				}
				break;
			case 'type' :
				if ( $item[$column_name] == 'badge' ) {
					_e( 'Badge', 'badgearoo' );
				} else {
					_e( 'Points', 'badgearoo' );
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
				<div id="broo-status-text-<?php echo $row_id ?>">
					<span id="broo-text-approve-<?php echo $row_id; ?>"<?php if ( $item[$column_name] != 'approved' ) { echo ' style="display: none"'; } ?>><?php _e( 'Approved', 'badgearoo' ); ?></span>
					<span id="broo-text-pending-<?php echo $row_id; ?>"<?php if ( $item[$column_name] != 'pending' ) { echo ' style="display: none"'; } ?>><?php _e( 'Pending', 'badgearoo' ); ?></span>
					<span id="broo-text-unapprove-<?php echo $row_id; ?>"<?php if ( $item[$column_name] != 'unapproved' ) { echo ' style="display: none"'; } ?>><?php _e( 'Unapproved', 'badgearoo' ); ?></span>
				</div>
				<div id="broo-row-actions-<?php echo $row_id; ?>" class="row-actions">
					<?php 
					if ( $item[$column_name] == 'approved' ) {
						?>
						<a href="#" id="broo-anchor-unapprove-<?php echo $row_id; ?>" class="broo-unapprove"><?php _e( 'Unapprove', 'badgearoo' ); ?></a>
						<?php
					} else {
						?>
						<a href="#" id="broo-anchor-unapprove-<?php echo $row_id; ?>" class="broo-approve"><?php _e( 'Approve', 'badgearoo' ); ?></a>
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
				'delete'    => __( 'Delete', 'badgearoo' ),
				'approve'	=> __( 'Approve', 'badgearoo' ),
				'unapprove'	=> __( 'Unapprove', 'badgearoo' )
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
				Badgearoo::instance()->api->delete_user_assignment( $id );
			}
			
			echo '<div class="updated"><p>' . __( 'Assignment(s) deleted successfully.', 'badgearoo' ) . '</p></div>';
			
		} else if ( $this->current_action() === 'unapprove' ) {
				
			$checked = ( is_array( $_REQUEST['cb'] ) ) ? $_REQUEST['cb'] : array( $_REQUEST['cb'] );
				
			global $wpdb;
			
			foreach( $checked as $id ) {
				$wpdb->update( $wpdb->prefix . BROO_USER_ASSIGNMENT_TABLE_NAME, array( 'status' => 'unapproved' ), array( 'id' => $id ) );
				do_action( 'broo_user_assignment_unapproved', $id );
			}
				
			echo '<div class="updated"><p>' . __( 'Assignment(s) deleted successfully.', 'badgearoo' ) . '</p></div>';
			
		} else if ( $this->current_action() === 'approve' ) {
				
			$checked = ( is_array( $_REQUEST['cb'] ) ) ? $_REQUEST['cb'] : array( $_REQUEST['cb'] );
				
			global $wpdb;
			
			foreach( $checked as $id ) {
				$wpdb->update( $wpdb->prefix . BROO_USER_ASSIGNMENT_TABLE_NAME, array( 'status' => 'approved' ), array( 'id' => $id ) );
				do_action( 'broo_user_assignment_approved', $id );
			}
				
			echo '<div class="updated"><p>' . __( 'Assignment(s) deleted successfully.', 'badgearoo' ) . '</p></div>';
		}
	}
}

function broo_update_user_assignment_status() {
	
	$ajax_nonce = $_POST['nonce'];
	
	if ( wp_verify_nonce( $ajax_nonce, Badgearoo::ID.'-nonce' ) ) {
	
		$assignment_id = ( isset( $_POST['assignmentId'] ) && is_numeric( $_POST['assignmentId'] ) ) ? intval( $_POST['assignmentId'] ) : null;
		$status = ( $_POST['status'] == 'approve' ) ? 'approved' : 'unapproved';
			
		if ( ! $assignment_id ) {
			die();
		}
			
		global $wpdb;
			
		$wpdb->update( $wpdb->prefix . BROO_USER_ASSIGNMENT_TABLE_NAME, array( 'status' => $status ), array( 'id' => $assignment_id ) );
			
		do_action( 'broo_user_assignment_approved', $assignment_id );
			
		echo json_encode( array (
				'data' => array( 
						'status' => $status,
						'approve'	=> __( 'Approve', 'badgearoo' ),
						'unapprove'	=> __( 'Unapprove', 'badgearoo' )
				),
				'assignment_id' => $assignment_id
		) );
	}
	
	die();
	
}