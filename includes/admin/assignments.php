<?php
/**
 * Assignments screen
 */
function ub_assignments_page() {
	?>
	<div class="wrap">
		<?php 
		if ( isset( $_REQUEST['add-new'] ) ) {
			?>
			<h2><?php _e( 'Add New Assignment', 'user-badges' ); ?></h2>
			<form name="add-new-assignment-form" id="add-new-assignment-form" method="post" action="#">
				<table class="form-table">
					<tr class="form-field">
						<th scope="row"><label for="user-id"><?php _e( 'User', 'user-badges' ); ?></label></td>
						<td><?php wp_dropdown_users( array(
								'name'  => 'user-id',
								'id' => 'user-id'
						) ); ?></td>
					</tr>
					<tr class="form-field">
						<th scope="row"><label for="type"><?php _e( 'Type', 'user-badges' ); ?></label></td>
						<td>
							<select name="type" id="type">
								<option value="badge"><?php _e( 'Badge', 'user-badges' ); ?></option>
								<option value="points"><?php _e( 'Points', 'user-badges' ); ?></option>
							</select>
						</td>
					</tr>
					<tr class="form-field">
						<th scope="row"><label for="expiry_dt"><?php _e( 'Expiry Date', 'user-badges' ); ?></label></td>
						<td><input type="date" name="expiry-dt" id="expiry-dt" class="medium-text" /></td>
					</tr>
					<tr class="form-field">
						<th scope="row"><label for="value"><?php _e( 'Assignment', 'user-badges' ); ?></label></td>
						<td>
							<div id="assignment">
								<?php ub_dropdown_badges( array( 'name' => 'value', 'echo' => true ) ); ?>
							</div>
						</td>
					</tr>
				</table>
				<?php 
				submit_button( __( 'Add New', 'user-badges' ), 'primary', 'add-new-btn', true, null );
				?>
			</form>
			<?php 
		} else { ?>
			<h2><?php _e( 'Assignments', 'user-badges' ); ?><a class="add-new-h2" href="edit.php?post_type=badge&page=<?php echo User_Badges::ASSIGNMENTS_PAGE_SLUG; ?>&add-new=true"><?php _e( 'Add New', 'user-badges' ); ?></a></h2>
			<form method="post" id="assignments-table-form">
				<?php 
				$assignments_table = new UB_Assignments_Table();
				$assignments_table->prepare_items();
				$assignments_table->display();
				?>
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
function ub_dropdown_badges( $args = array() ) {
	
	$name = isset( $args['name'] ) ? $args['name'] : 'badge-id';
	$echo = isset( $args['echo'] ) ? $args['echo'] : false;
	$show_option_all = isset( $args['show_option_all'] ) ? $args['show_option_all'] : false;
	$selected_badge = isset( $args['selected'] ) ? $args['selected'] : 0;
	
	$badges = User_Badges::instance()->api->get_badges();
	
	$html = '';
	$html .= '<select name="' . $name . '">';
	
	if ( $show_option_all ) {
		$selected = ( $selected_badge == 0 ) ? ' selected' : '';
		$html .= '<option value="0"' . $selected . '>' . __( 'All badges', 'user-badges' ) . '</option>';
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
function ub_change_assignment_type() {

	$ajax_nonce = $_POST['nonce'];
	if ( wp_verify_nonce($ajax_nonce, User_Badges::ID.'-nonce' ) ) {

		$type = isset( $_POST['type'] ) ? $_POST['type'] : 'badge'; 

		$html = '';
		if ( $type == 'badge' ) {
			$html = ub_dropdown_badges( array( 'name' => 'value', 'echo' => false ) );
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



function ub_add_new_assignment() {
	
	$user_id = isset( $_POST['user-id'] ) ? intval( $_POST['user-id'] ) : 0;
	$type = isset( $_POST['type'] ) ? $_POST['type'] : null;
	$expiry_dt = isset( $_POST['expiry-dt'] ) ? $_POST['expiry-dt'] : null;
	$value = isset( $_POST['value'] ) ? intval( $_POST['value'] ) : null;
	
	if ( $user_id == 0 | $type == null || $value == null ) {
		return;
	}
	
	User_Badges::instance()->api->add_user_assignment( null, $user_id, $type, $value, $expiry_dt );
	
	$url = 'edit.php?post_type=badge&page=' . User_Badges::ASSIGNMENTS_PAGE_SLUG;
	wp_redirect($url);
	exit();
	
}

if ( isset( $_POST['add-new-btn'] ) ) {
	add_action( 'admin_init', 'ub_add_new_assignment' );
}