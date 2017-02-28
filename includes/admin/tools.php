<?php 

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Shows the tools screen
 */
function broo_tools_page() {
	?>
	<div class="wrap">
		<h2><?php _e( 'Tools', 'badgearoo' ); ?></h2>
		
		<?php 
		if ( current_user_can( 'manage_options' ) ) {
			?>
			
			<div class="metabox-holder">
				<div class="postbox">
					<h3><span><?php _e( 'Export', 'badgearoo' ); ?></span></h3>
					<div class="inside">
						<p><?php _e( 'Export assignments to a CSV file.', 'badgearoo' ); ?></p>
						
						<form method="post" id="export-assignments-form">
							<p>
								<input type="text" name="username1" class="" autocomplete="off" placeholder="Username">
								<input type="text" class="date-picker" autocomplete="off" name="from-date1" placeholder="From - yyyy-MM-dd">
								<input type="text" class="date-picker" autocomplete="off" name="to-date1" placeholder="To - yyyy-MM-dd">
								
								<select name="type1" id="type1">
									<option value=""><?php _e( 'All types', 'badgearoo' ); ?></option>
									<option value="badge"><?php _e( 'Badge', 'badgearoo' ); ?></option>
									<option value="points"><?php _e( 'Points', 'badgearoo' ); ?></option>
								</select>
																
								<?php
								broo_dropdown_badges( array( 'name' => 'badge-id1', 'show_option_all' => true, 'echo' => true ) );
								?>
								
								<input type="checkbox" name="expired1" /><label for="expired1"><?php _e( 'Include expired', 'badgearoo' ); ?></label>
								<input type="checkbox" name="approved1" /><label for="approved1"><?php _e( 'Approved only', 'badgearoo' ); ?></label>
								
								<?php
								submit_button( __( 'Export', 'badgearoo' ), 'secondary', 'export-assignments-btn', false, null );
								?>
							</p>
						</form>
					</div><!-- .inside -->
				</div>
			</div>
			
			<div class="metabox-holder">
				<div class="postbox">
					<h3><span><?php _e( 'Delete', 'badgearoo' ); ?></span></h3>
					
					<div class="inside">
						
						<p><?php _e( 'Delete badges and points assigned to users.', 'badgearoo' ); ?></p>
						
						<form method="post" id="delete-assignments-form">
							<p>
								<input type="text" name="username2" class="" autocomplete="off" placeholder="Username">
								<input type="text" class="date-picker" autocomplete="off" name="from-date2" placeholder="From - yyyy-MM-dd">
								<input type="text" class="date-picker" autocomplete="off" name="to-date2" placeholder="To - yyyy-MM-dd">
								
								<select name="type2" id="type2">
									<option value=""><?php _e( 'All types', 'badgearoo' ); ?></option>
									<option value="badge"><?php _e( 'Badge', 'badgearoo' ); ?></option>
									<option value="points"><?php _e( 'Points', 'badgearoo' ); ?></option>
								</select>
																
								<?php
								broo_dropdown_badges( array( 'name' => 'badge-id2', 'show_option_all' => true, 'echo' => true ) );
								?>
																
								<?php								
								submit_button( __( 'Delete', 'badgearoo' ), 'secondary', 'delete-assignments-btn', false, null );
								?>
							</p>
						</form>
					</div>
				</div>
			</div>	
		<?php } ?>
	</div>
	<?php
}

/**
 * Exports the rating results to a CSV file
 */
function broo_export_assignments() {

	$file_name = 'assignments-' . date( 'YmdHis' ) . '.csv';
		
	$username = isset( $_POST['username1'] ) ? $_POST['username1'] : null;
	$from_date = isset( $_POST['from-date1'] ) ? $_POST['from-date1'] : null;
	$to_date = isset( $_POST['to-date1'] ) ? $_POST['to-date1'] : null;
	$badge_id = isset( $_POST['badge-id1'] ) ? $_POST['badge-id1'] : null;
	$type = ( isset( $_POST['type1'] ) && strlen( $_POST['type1'] ) > 0 ) ? $_POST['type1'] : null;
		
	$filters = array();
	
	if ( isset( $_POST['expired1'] ) ) {
		$filters['expired'] = true;
	}
	
	if ( isset( $_POST['status1'] ) ) {
		$filters['status'] = 'approved';
	}
	
	if ( $type ) {
		$filters['type'] = $type;
	}
	
	$filters['user_id'] = null;
	if ( $username != null && strlen( $username ) > 0 ) {
		// get user id
		$user = get_user_by( 'login', $username );
		if ( $user && $user->ID ) {
			$filters['user_id'] = $user->ID;
		}
	}
	
	if ( $badge_id != null && strlen( $badge_id ) > 0 ) {
		$filters['badge_id'] = $badge_id;
	}
	
	if ( $from_date != null && strlen( $from_date ) > 0 ) {
		list( $year, $month, $day ) = explode( '-', $from_date ); // default yyyy-mm-dd format
			if ( checkdate( $month , $day , $year )) {
			$filters['from_date'] = $from_date;
		}
	}
	
	if ( $to_date != null && strlen($to_date) > 0 ) {
		list( $year, $month, $day ) = explode( '-', $to_date );// default yyyy-mm-dd format
			if ( checkdate( $month , $day , $year )) {
			$filters['to_date'] = $to_date;
		}
	}
	
	if ( broo_generate_assignments_csv( $file_name, $filters ) ) {
			
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="' . $file_name . '"');
		readfile( $file_name );
		
		// delete file
		unlink($file_name);
	}
		
	die();
}

/**
 * Generates assignments in CSV format.
 *
 * @param $file_name the file_name to save
 * @param $filters used to filter the report e.g. from_date, to_date, user_id etc...
 * @returns true if report successfully generated and written to file
 */
function broo_generate_assignments_csv( $file_name, $filters ) {
	
	$header_row =
			'"' . __( 'Assignment Id', 'badgearoo' ) . '",' .
			'"' . __( 'Assignment Type', 'badgearoo' ) . '",' .
			'"' . __( 'User Id', 'badgearoo' ) . '",' .
			'"' . __( 'Username', 'badgearoo' ) . '",' .
			'"' . __( 'Condition Id', 'badgearoo' ) . '",' .
			'"' . __( 'Condition Name', 'user-bagdes' ) . '",' .
			'"' . __( 'Badge Id', 'badgearoo' ) . '",' .
			'"' . __( 'Badge Title', 'badgearoo' ) . '",' .
			'"' . __( 'Points', 'badgearoo' ) . '",' .
			'"' . __( 'Created Dt', 'badgearoo' ) . '",' .
			'"' . __( 'Expiry Dt', 'badgearoo' ) . '",' .
			'"' . __( 'Status', 'badgearoo' ) . '"';
	
	if ( ! isset( $filters['status'] ) ) {
		$filters['status'] = '';
	}
	$filters['limit'] = null;
	$filters['offset'] = null;
	
	$assignments = Badgearoo::instance()->api->get_user_assignments( $filters );
	
	$data_rows = array( $header_row );
	
	foreach ( $assignments as $assignment ) {
		
		$condition_id = '';
		$condition_name = '';
		$badge_id = '';
		$badge_title = '';
		
		if ( $assignment['condition'] ) {
			$condition = $assignment['condition'];
			$condition_id = $condition->condition_id;
			$condition_name = $condition->name;
		}
		
		if ( $assignment['badge'] ) {
			$badge = $assignment['badge'];
			$badge_id = $badge->id;
			$badge_title = $badge->title;
		}
	
		$current_row =
				$assignment['id'] .',' .
				'"' . $assignment['type'] . '",' .
				$assignment['user_id'] . ',' .
				'"' . $assignment['username'] . '",' .
			 	$condition_id . ',' .
				'"' . $condition_name . '",' .
				$badge_id . ',' .
				'"' . $badge_title. '",' .
				$assignment['points'] . ',' .
				'"' . $assignment['created_dt'] . '",' .
				'"' . $assignment['expiry_dt'] . '",' .
				'"' . $assignment['status'] . '"';

		array_push( $data_rows, $current_row );
	}

	$file = null;
	try {
		$file = fopen( $file_name, 'w' );
		foreach ( $data_rows as $row ) {
			fputs( $file, $row . "\r\n" );
		}
		fclose( $file );
	} catch ( Exception $e ) {
		return false;
	}

	return true;
}

/**
 * Deletes assignments from the database
 */
function broo_delete_user_assignments() {
	
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	
	$username = isset( $_POST['username2'] ) ? $_POST['username2'] : null;
	$from_date = isset( $_POST['from-date2'] ) ? $_POST['from-date2'] : null;
	$to_date = isset( $_POST['to-date2'] ) ? $_POST['to-date2'] : null;
	$badge_id = isset( $_POST['badge-id2'] ) ? $_POST['badge-id2'] : null;
	$type = ( isset( $_POST['type2'] ) && strlen( $_POST['type2'] ) > 0 ) ? $_POST['type1'] : null;
			
	$user_id = null;
	if ( $username ) {
		$user = get_user_by( 'login', $username );
		if ( $user && $user->ID ) {
			$user_id = $user->ID;
		} else {
			echo '<div class="error"><p>' . sprintf( __( 'Cannot find user with username "%s".', 'badgearoo' ), $username ) . '</p></div>';
			return;
		}
	}

	if ( $from_date != null && strlen( $from_date ) > 0 ) {
		list( $year, $month, $day ) = explode( '-', $from_date ); // default yyyy-mm-dd format
		if ( ! checkdate( $month , $day , $year ) ) {
			$from_date = null;
		}
	}
	
	if ( $to_date != null && strlen($to_date) > 0 ) {
		list( $year, $month, $day ) = explode( '-', $to_date );// default yyyy-mm-dd format
		if ( ! checkdate( $month , $day , $year ) ) {
			$to_date = null;
		}
	}
	
	$count = Badgearoo::instance()->api->delete_user_assignments( array( 
			'to_date' => $to_date,
			'from_date' => $from_date,
			'badge_id' => $badge_id,
			'type' => $type,
			'user_id' => $user_id
	) );
	
	if ( $count > 0 ) {
		echo '<div class="updated"><p>' . sprintf( __( '%d assignments deleted.', 'badgearoo' ), $count ) . '</p></div>';
	} else {
		echo '<div class="error"><p>' . __( 'No assignments found', 'badgearoo' ) . '</p></div>';
	}
}

if ( isset( $_POST['export-assignments-btn'] ) ) {
	add_action( 'admin_init', 'broo_export_assignments' );
}

if ( isset( $_POST['delete-assignments-btn'] ) ) {
	add_action( 'admin_init', 'broo_delete_user_assignments' );
}
?>