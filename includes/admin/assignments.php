<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Assignments screen
 */
function broo_assignments_page() {
	?>
	<div class="wrap">
		<?php 
		if ( isset( $_REQUEST['add-new'] ) ) {
			?>
			<h2><?php _e( 'Add New Assignment', 'badgearoo' ); ?></h2>
			<form name="add-new-assignment-form" id="add-new-assignment-form" method="post" action="#">
				<table class="form-table">
					<tr class="form-field">
						<th scope="row"><label for="user-id"><?php _e( 'User', 'badgearoo' ); ?></label></td>
						<td><?php wp_dropdown_users( array(
								'name'  => 'user-id',
								'id' => 'user-id'
						) ); ?></td>
					</tr>
					<tr class="form-field">
						<th scope="row"><label for="type"><?php _e( 'Type', 'badgearoo' ); ?></label></td>
						<td>
							<select name="type" id="type">
								<option value="badge"><?php _e( 'Badge', 'badgearoo' ); ?></option>
								<option value="points"><?php _e( 'Points', 'badgearoo' ); ?></option>
							</select>
						</td>
					</tr>
					<tr class="form-field">
						<th scope="row"><label for="expiry_dt"><?php _e( 'Expiry Date', 'badgearoo' ); ?></label></td>
						<td>
							<input type="date" name="expiry-dt" id="expiry-dt" class="medium-text" />
							<label for="expiry_dt"><?php _e( 'Leave empty for no expiration.', 'badgearoo' ); ?></label>
						</td>
					</tr>
					<tr class="form-field">
						<th scope="row"><label for="value"><?php _e( 'Assignment', 'badgearoo' ); ?></label></td>
						<td>
							<div id="assignment">
								<?php broo_dropdown_badges( array( 'name' => 'value', 'echo' => true ) ); ?>
							</div>
						</td>
					</tr>
				</table>
				<?php 
				submit_button( __( 'Add New', 'badgearoo' ), 'primary', 'add-new-btn', true, null );
				?>
			</form>
			<?php 
		} else { ?>
			<h2><?php _e( 'Assignments', 'badgearoo' ); ?><a class="add-new-h2" href="edit.php?post_type=badge&page=<?php echo Badgearoo::ASSIGNMENTS_PAGE_SLUG; ?>&add-new=true"><?php _e( 'Add New', 'badgearoo' ); ?></a></h2>
			<form method="get" id="assignments-table-form" action="<?php echo admin_url( 'edit.php?post_type=badge&page=' . Badgearoo::ASSIGNMENTS_PAGE_SLUG ); ?>">
				<?php 
				$assignments_table = new BROO_Assignments_Table();
				$assignments_table->prepare_items();
				$assignments_table->views();
				$assignments_table->display();
				?>
				<input type="hidden" name="post_type" value="badge" />
				<input type="hidden" name="page" value="<?php echo Badgearoo::ASSIGNMENTS_PAGE_SLUG; ?>" />
			</form>
		<?php } ?>
	</div>
	<?php 
}

/**
 * Dropdown badges
 * 
 * @param string $name
 * @param string $echo
 * @return string
 */
function broo_dropdown_badges( $args = array() ) {
	
	$name = isset( $args['name'] ) ? $args['name'] : 'badge-id';
	$echo = isset( $args['echo'] ) ? $args['echo'] : false;
	$show_option_all = isset( $args['show_option_all'] ) ? $args['show_option_all'] : false;
	$selected_badge = isset( $args['selected'] ) ? $args['selected'] : 0;
	
	$badges = Badgearoo::instance()->api->get_badges();
	
	$html = '';
	$html .= '<select name="' . $name . '">';
	
	if ( $show_option_all ) {
		$selected = ( $selected_badge == 0 ) ? ' selected' : '';
		$html .= '<option value="0"' . $selected . '>' . __( 'All badges', 'badgearoo' ) . '</option>';
	}
	
	foreach ( $badges as $badge ) {
		$selected = ( $selected_badge == $badge->id ) ? ' selected' : '';
		$html .= '<option value="' . $badge->id . '"' . $selected . '>' . $badge->title . '</option>';
	}
	
	$html .= '</select>';

	if ( $echo ) {
		echo $html;
	}
	
	return $html;
}




/**
 * Changes assignment type
 */
function broo_change_assignment_type() {

	$ajax_nonce = $_POST['nonce'];
	if ( wp_verify_nonce($ajax_nonce, Badgearoo::ID.'-nonce' ) ) {

		$type = isset( $_POST['type'] ) ? $_POST['type'] : 'badge'; 

		$html = '';
		if ( $type == 'badge' ) {
			$html = broo_dropdown_badges( array( 'name' => 'value', 'echo' => false ) );
		} else {
			$html = '<input type="number" name="value" id="value" value="100" class="medium-text" style="width: auto;"></input>';
		}

		$data = array();
		$data['html'] = $html;

		echo json_encode( array(
				'status' => 'success',
				'data' => $data
		) );
	}

	die();

}


/**
 * Add a new assignment
 */
function broo_add_new_assignment() {
	
	// TODO check if has capability
	
	$user_id = isset( $_POST['user-id'] ) ? intval( $_POST['user-id'] ) : 0;
	$type = isset( $_POST['type'] ) ? $_POST['type'] : null;
	$expiry_dt = isset( $_POST['expiry-dt'] ) ? $_POST['expiry-dt'] : null;
	$value = isset( $_POST['value'] ) ? intval( $_POST['value'] ) : null;
	
	if ( $user_id == 0 | $type == null || $value == null ) {
		return;
	}
	
	// TODO sanitize input
	
	Badgearoo::instance()->api->add_user_assignment( null, $user_id, $type, $value, $expiry_dt );
	
	if ( $type == 'points' ) {
		broo_check_conditions( BROO_MIN_POINTS_ACTION, $user_id );
	}
	
	$url = 'edit.php?post_type=badge&page=' . Badgearoo::ASSIGNMENTS_PAGE_SLUG;
	wp_redirect($url);
	exit();
	
}

if ( isset( $_POST['add-new-btn'] ) ) {
	add_action( 'admin_init', 'broo_add_new_assignment' );
}