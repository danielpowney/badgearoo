<?php
/**
 * Conditions page
 */
function ub_conditions_page() {
	?>
	<div class="wrap">
		<h2><?php _e( 'Conditions', 'user-badges' ); ?>
			<a class="add-new-h2" href="#" id="add-condition"><?php _e('Add New', 'user-badges' ); ?></a>
		</h2>
	
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-1">
				<div id="postbox-container" class="postbox-container">
					<div id="poststuff">
						<div id="post-body" class="metabox-holder columns-1">
							<div id="postbox-container" class="postbox-container">
								<div id="normal-sortables" class="meta-box-sortables ui-sortable">
									<?php 
									
									global $wpdb;
									
									$results = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . UB_CONDITION_TABLE_NAME );
									
									if ( count( $results ) == 0 ) {
										$name = __( 'New Condition' );
										$created_dt = current_time('mysql');
										
										$wpdb->insert( $wpdb->prefix . UB_CONDITION_TABLE_NAME , array( 'name' => $name, 'created_dt' => $created_dt ), array( '%s', '%s' ) );
										$condition_id = $wpdb->insert_id;
										
										ub_display_condition_meta_box( new UB_Condition( $condition_id, $name, null, 0, $created_dt, null, false, false ) );
									} else {
										foreach ( $results as $row ) {
											ub_display_condition_meta_box( new UB_Condition( $row->id, $row->name, $row->badge_id, $row->points, $row->created_dt, $row->status, false, true  ) );
										}
									}
									?>
								</div>
							</div>
						</div>
					</div>	
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Displays a condition meta box
 * 
 * @param unknown $post
 * @param unknown $data
 */
function ub_display_condition_meta_box( $condition ) {
	?>
	<div <?php if ( isset( $condition->condition_id ) ) echo 'id="condition-' . $condition->condition_id . '"'; ?> class="postbox">
		<div class="handlediv" title="Click to toggle"><br /></div>
		<h3 class="hndle ui-sortable-handle"><span><?php echo esc_html( $condition->name ); ?></span></h3>
		<div class="inside">
			<form method="post" class="condition-form">
				<table class="ub-condition">
					<tr>
						<td>
							<table class="form-table">
								<tbody>
									<tr>
										<th scope="row"><?php _e( 'Name', 'user-badges' ); ?></th>
										<td><input type="text" name="name" value="<?php echo $condition->name; ?>" class="regular-text" /></td>
									</tr>
									<tr>
										<th scope="row"><?php _e( 'Badge', 'user-badges' ); ?></th>
										<td>
											<select name="badges">
												<option value=""><?php _e( 'No badge', 'user-badges' ); ?></option>
												<?php 
												global $wpdb;
												$badges = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->posts . ' WHERE post_type = "badge" AND post_status = "publish"' );
												foreach ( $badges as $badge ) {
													?><option value="<?php echo $badge->ID; ?>" <?php if ( $condition->badge_id == $badge-ID ) echo 'selected'; ?>><?php echo esc_html( stripslashes( $badge->post_title ) ); ?></option><?php
												}
												?>
											</select>
											<p class="description"><?php _e( 'Allocate badges to users.', 'user-badges' ); ?></p>
										</td>
									</tr>
									<tr>
										<th scope="row"><?php _e( 'Points', 'user-badges' ); ?></th>
										<td>
											<input type="number" class="medium-text" name="points" value="<?php echo $condition->points; ?>" />
											<p class="description"><?php _e( 'Allocate points to users.', 'user-badges' ); ?></p>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
						<td>
							<ul class="ub-step-list">
								<?php
								if ( count( $condition->steps ) == 0 ) {
									$created_dt = current_time('mysql');
									$label = __( 'New Step', 'user-badges' );
									
									$wpdb->insert( $wpdb->prefix . UB_CONDITION_STEP_TABLE_NAME , 
											array( 'condition_id' => $condition->condition_id, 'label' => $label, 'created_dt' => $created_dt ), 
											array( '%s', '%s', '%s')
									);
									$step_id = $wpdb->insert_id;
									
									ub_display_step( new UB_Step( $step_id, $condition->condition_id, $label, null, $created_dt ) );
								}
								foreach ( $condition->steps as $step ) {
									ub_display_step( $step );
								}
								?>
							</ul>
							<p><input type="button" class="button add-step-btn" button" value="Add Step" /></p>
						</td>
					</tr>
				</table>
			
				<p>
					<input type="button" class="button button-primary save-condition-btn" value="<?php _e( 'Save Changes', 'user-badges' ); ?>" />
					<input type="button" class="button button-secondary delete-condition-btn" value="<?php _e( 'Delete', 'user-badges' ); ?>" />
				</p>
				
				<?php 
				wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
				wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
				?>
			</form>
		</div>
	</div>
	<?php
}

/**
 * Displays a step
 * 
 * @param unknown $step_id
 * @return string
 */
function ub_display_step( $step ) {	
	?>
	<li id="step-<?php echo $step->step_id; ?>" class="ub-step ui-state-default">
		<label for="label"><?php _e( 'Label', 'user-badges' ); ?></label>
		<input type="text" maxlength="50" name="label" value="<?php echo $step->label; ?>" class="regular-text" />
		
		<select name="action-name-<?php echo $step->step_id; ?>" class="action-name">
			<option value=""><?php _e( 'Please select an action.', 'user-badges' ); ?>
			<?php
			global $wpdb;
			$actions = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . UB_ACTION_TABLE_NAME . ' WHERE enabled = 1' );
			foreach ( $actions as $action ) {
				?><option value="<?php echo $action->name; ?>" <?php if ( $step->action_name == $action->name ) echo 'selected'; ?>><?php echo esc_html( stripslashes( $action->description ) ); ?></option><?php
			}
			?>
		</select>
			
		<div class="step-meta">			
			<?php 
			if ( isset( $step->action_name ) ) {
				do_action( 'ub_step_meta', $step->step_id, $step->action_name );
			}
			?>
		</div>
		
		<a href="#" class="delete-step"><?php _e( 'Delete', 'user-badges' ); ?></a>
	</li>
	<?php 
}

/**
 * Shows a count step meta
 * 
 * @param unknown $step_id
 * @param unknown $action
 */
function ub_step_meta_count_action( $step_id, $action  ) {
	if ( $action == UB_WP_LOGIN_ACTION || $action == UB_WP_PUBLISH_POST_ACTION || $action == UB_WP_SUBMIT_COMMENT_ACTION ) { 
		?>
		<span class="step-meta-value">
			<input name="count" type="number" value="" class="small-text" />&nbsp;<?php _e( 'time(s)', 'user-badges' ); ?>
		</span>
		<?php
	}
}
add_action( 'ub_step_meta', 'ub_step_meta_count_action', 10, 2 );

/**
 * Shows a points step meta
 *
 * @param unknown $step_id
 * @param unknown $action
 */
function ub_step_meta_points_action( $step_id, $action  ) {
	if ( $action == UB_MIN_POINTS_ACTION ) {
		?>
		<span class="step-meta-value">
			<input name="points" type="number" value="" class="small-text" />&nbsp;<?php _e( 'points', 'user-badges' ); ?>
		</span>
		<?php
	}
}
add_action( 'ub_step_meta', 'ub_step_meta_points_action', 10, 2 );

/**
 * Returns HTML for a new condition
 */
function ub_add_condition() {
	
	$ajax_nonce = $_POST['nonce'];
	if ( wp_verify_nonce( $ajax_nonce, User_Badges::ID.'-nonce' ) ) {

		ob_start();
	
		global $wpdb;	
		
		$name = __( 'New Condition' );
		$created_dt = current_time('mysql');
		
		$wpdb->insert( $wpdb->prefix . UB_CONDITION_TABLE_NAME , array( 'name' => $name, 'created_dt' => $created_dt ), array( '%s', '%s' ) );
		$condition_id = $wpdb->insert_id;
		
		$condition = new UB_Condition( $condition_id, $name, null, 0, $created_dt, null, false, false  );
		
		ub_display_condition_meta_box( $condition );
	
		$html = ob_get_contents();
		ob_end_clean();
	
		echo json_encode( array(
				'html' => $html,
				'data' => array( 'conditionId' => $condition_id )
		) );
	}
	
	die();
}

/**
 * Returns HTML for a new step
 */
function ub_add_step() {
	
	$ajax_nonce = $_POST['nonce'];
	if ( wp_verify_nonce( $ajax_nonce, User_Badges::ID.'-nonce' ) ) {

		ob_start();
		
		$label = __( 'New Step' );
		$created_dt = current_time('mysql');
		$condition_id = $_POST['conditionId'];
		
		global $wpdb;
		
		$wpdb->insert( $wpdb->prefix . UB_CONDITION_STEP_TABLE_NAME , 
				array( 'label' => $label, 'condition_id' => $condition_id, 'created_dt' => $created_dt ), 
				array( '%s', '%d', '%s' )
		);
		$step_id = $wpdb->insert_id;
		
		$step = new UB_Step( $step_id, $condition_id, $label, null, $created_dt );
	
		ub_display_step( $step );
	
		$html = ob_get_contents();
		ob_end_clean();
	
		echo json_encode( array(
				'html' => $html,
				'data' => array( 'stepId' => $step_id )
		) );
	}
	
	die();
}

/**
 * Deletes a step
 */
function ub_delete_step() {

	$ajax_nonce = $_POST['nonce'];
	if ( wp_verify_nonce( $ajax_nonce, User_Badges::ID.'-nonce' ) ) {
		$step_id = $_POST['stepId'];

		global $wpdb;

		$wpdb->delete( $wpdb->prefix . UB_CONDITION_STEP_TABLE_NAME ,
				array( 'id' => $step_id ),
				array( '%d' )
		);

		echo json_encode( array(
				'success' => true
		) );
	}

	die();
}

/**
 * Deletes a condition
 */
function ub_delete_condition() {

	$ajax_nonce = $_POST['nonce'];
	if ( wp_verify_nonce( $ajax_nonce, User_Badges::ID.'-nonce' ) ) {
		$condition_id = $_POST['conditionId'];

		global $wpdb;

		$wpdb->delete( $wpdb->prefix . UB_CONDITION_TABLE_NAME ,
				array( 'id' => $condition_id ),
				array( '%d' )
		);
		
		$steps = $wpdb->get_col( $wpdb->prepare( 'SELECT id FROM ' . $wpdb->prefix . UB_CONDITION_STEP_TABLE_NAME . ' WHERE condition_id = %d', $condition_id ) );
		
		$wpdb->delete( $wpdb->prefix . UB_CONDITION_STEP_TABLE_NAME ,
				array( 'condition_id' => $condition_id ),
				array( '%d' )
		);
		
		$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . UB_CONDITION_STEP_META_TABLE_NAME . ' WHERE step_id IN ( ' . implode(',', $steps ) . ')' );

		echo json_encode( array(
				'success' => true
		) );
	}

	die();
}


/**
 * Returns HTML for step meta
 * 
 * @param unknown $step_id
 * @param unknown $action_name
 */
function ub_step_meta() {
	
	$ajax_nonce = $_POST['nonce'];
	if ( wp_verify_nonce( $ajax_nonce, User_Badges::ID.'-nonce' ) ) {
		
		$step_id = $_POST['stepId'];
		$action_name = $_POST['actionName'];
	
		ob_start();
		
		do_action( 'ub_step_meta', $step_id, $action_name );
		
		$html = ob_get_contents();
		ob_end_clean();
		
		echo json_encode( array(
				'html' => $html
		) );
	}
	
	die();
}