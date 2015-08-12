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
							<div id="postbox-container" class="postbox-container active">
								<div id="normal-sortables" class="meta-box-sortables ui-sortable">
									<?php 
									$conditions = User_Badges::instance()->api->get_conditions();
									
									if ( count( $conditions ) == 0 ) {
										$name = __( 'New Condition' );
										$condition = User_Badges::instance()->api->add_condition( $name );
										array_push( $conditions, $condition );
									}
									
									foreach ( $conditions as $condition ) {
										ub_display_condition_meta_box( $condition );
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
		<h3 class="hndle ui-sortable-handle">
			<span>
				<?php printf( __( 'Condition %d - %s', 'user-badges' ), $condition->condition_id, esc_html( $condition->name ) ); ?>
			</span>
			<?php ub_condition_status( $condition ); ?>
		</h3>
		<div class="inside">
			<form method="post" class="condition">
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
										<th scope="row"><?php _e( 'Enabled', 'user-badges' ); ?></th>
										<td><input type="checkbox" name="enabled" value="true" <?php checked( $condition->enabled, true, true ); ?>/></td>
									</tr>
									<tr>
										<th scope="row"><?php _e( 'Badges', 'user-badges' ); ?></th>
										<td>
											<?php 
											ub_dropdown_badges( array( 'name' => 'addBadge', 'echo' => true ) );
											?>
											<input type="button" class="button secondary addBadgeBtn" value="Add Badge" />
											<p class="description"><?php _e( 'Allocate badges to users'); ?></p>
											
											<div class="tagchecklist" style="max-width: 100%;">
												<?php if ( count( $condition->badges ) > 0 ) {
													foreach ( $condition->badges as $badge_id ) {
														$badge = User_Badges::instance()->api->get_badge( $badge_id );
														?>
														<span><a name="badgeId-<?php echo $badge->id; ?>" class="ntdelbutton">X</a>&nbsp;<?php echo $badge->title; ?></span>
														<?php 
													} 
													?>
												<?php } ?>
											</div>
											<input type="hidden" name="badges" value="<?php echo implode( ',', $condition->badges ); ?>" />
										</td>
									</tr>
									<tr>
										<th scope="row"><?php _e( 'Points', 'user-badges' ); ?></th>
										<td>
											<input type="number" class="medium-text" name="points" value="<?php echo $condition->points; ?>" />
											<p class="description"><?php _e( 'Allocate points to users.', 'user-badges' ); ?></p>
										</td>
									</tr>
									<tr>
										<th scope="row"><?php _e( 'Recurring', 'user-badges' ); ?></th>
										<td>
											<input type="checkbox" name="recurring" value="true" <?php checked( $condition->recurring, true, true ); ?>/>
											<label for="recurring"><?php _e( 'Assignments can occur more than once.', 'user-badges' ); ?></label>
											<p class="description"><?php _e( 'If turned on, assignments cannot be renewed.', 'user-badges' ); ?>
										</td>
									</tr>
									<tr>
										<th scope="row"><?php _e( 'Expiry', 'user-badges' ); ?></th>
										<td>
											<input type="number" name="expiry-value" class="ub-expiry-value" value="<?php if ( $condition->expiry_value != 0 ) { echo $condition->expiry_value; } ?>" />
											<select name="expiry-unit">
												<option value="day" <?php if ( $condition->expiry_unit == 'day' ) { echo 'selected="selected"'; }?>><?php _e( 'Day(s)', 'user-badges' ); ?></option>
												<option value="week" <?php if ( $condition->expiry_unit == 'week' ) { echo 'selected="selected"'; }?>><?php _e( 'Week(s)', 'user-badges' ); ?></option>
												<option value="month" <?php if ( $condition->expiry_unit == 'month' ) { echo 'selected="selected"'; }?>><?php _e( 'Month(s)', 'user-badges' ); ?></option>
												<option value="year" <?php if ( $condition->expiry_unit == 'year' ) { echo 'selected="selected"'; }?>><?php _e( 'Year(s)', 'user-badges' ); ?></option>
											</select>
											<p class="description"><?php _e( 'Leave empty if assignmemt has no expiration.', 'user-badges' ); ?></p>
										</td>
										
									</tr>
								</tbody>
							</table>
						</td>
						<td class="ub-condition-steps">
							<ul class="ub-step-list">
								<?php
								if ( count( $condition->steps ) == 0 ) {
									$label = __( 'New Step', 'user-badges' );
									
									$step = User_Badges::instance()->api->add_step( $condition->condition_id, $label );
									
									array_push( $condition->steps, $step );
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
					<input type="submit" class="button button-primary save-condition-btn" value="<?php _e( 'Save Changes', 'user-badges' ); ?>" />
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
 * Returns status of condition
 * 
 * @param condition
 * @param echo
 */
function ub_condition_status( $condition, $echo = true ) {

	$incomplete = false;
	$messages = array();
	
	if ( strlen( $condition->name ) == 0 ) {
		$incomplete = true;
		array_push( $messages, __( 'Name required.', 'user-badges' ) );
	} 
	
	if ( count( $condition->badges ) == 0 && $condition->points == 0 ) {
		$incomplete = true;
		array_push( $messages, __( 'Badges or points assignment required.', 'user-badges' ) );
	}
	
	if ( count( $condition->steps ) == 0 ) {
		$incomplete = true;
		array_push( $messages, __( 'Condition must have steps.', 'user-badges' ) );
	}

	$action_not_found = false;
	foreach ( $condition->steps as $step ) {
		if ( $step->action_name == null ) {
			$incomplete = true;
			$action_not_found = true;
			break;
		}
	}
	
	if ( $action_not_found ) {
		array_push( $messages, __( 'Each step must have an action.', 'user-badges' ) );
	}
	
	$html = null;
	
	if ( $incomplete == true ) {
		$html = '<span style="font-weight: 600; color: #555;"> - ' . __( 'Incomplete', 'user-badges' ) . '</span>';
	}
	
	if ( $echo ) {
		echo $html;
	}
	
	return $html;
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
		
		<select name="action-name" class="action-name">
			<option value=""><?php _e( 'Please select an action.', 'user-badges' ); ?>
			<?php
			
			$actions = User_Badges::instance()->api->get_actions();
			
			foreach ( $actions as $group => $group_actions ) {
				?>
				<optgroup label="<?php echo $group; ?>">
					<?php 
					foreach ( $group_actions as $action ) {
						?><option value="<?php echo $action->name; ?>" <?php if ( $step->action_name == $action->name ) echo 'selected'; ?>><?php echo esc_html( stripslashes( $action->description ) ); ?></option><?php
					}
					?>
				</optgroup>
				<?php 
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
 * Returns HTML for a new condition
 */
function ub_add_condition() {
	
	$ajax_nonce = $_POST['nonce'];
	if ( wp_verify_nonce( $ajax_nonce, User_Badges::ID.'-nonce' ) ) {

		ob_start();
	
		global $wpdb;	
		
		$name = __( 'New Condition' );
		$condition = User_Badges::instance()->api->add_condition( $name );
		
		ub_display_condition_meta_box( $condition );
	
		$html = ob_get_contents();
		ob_end_clean();
	
		echo json_encode( array(
				'html' => $html,
				'data' => array( 'conditionId' => $condition->condition_id )
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
		
		$label = __( 'New Step', 'user-badges' );
		$condition_id = $_POST['conditionId'];
		
		$step = User_Badges::instance()->api->add_step( $condition_id, $label );
	
		ub_display_step( $step );
	
		$html = ob_get_contents();
		ob_end_clean();
	
		echo json_encode( array(
				'html' => $html,
				'data' => array( 'stepId' => $step->step_id )
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

		User_Badges::instance()->api->delete_step( $step_id );

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

		User_Badges::instance()->api->delete_condition( $condition_id );
		
		echo json_encode( array(
				'success' => true
		) );
	}

	die();
}


/**
 * Returns HTML for step meta
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

/**
 * Saves a condition including steps and step meta values
 */
function ub_save_condition() {
	
	$ajax_nonce = $_POST['nonce'];
	if ( wp_verify_nonce( $ajax_nonce, User_Badges::ID.'-nonce' ) ) {
		
		$condition_id = ( isset( $_POST['conditionId'] ) && is_numeric( $_POST['conditionId'] ) ) ? intval( $_POST['conditionId'] ) : null;
		$name = ( isset( $_POST['name'] ) ) ? $_POST['name'] : '';
		$badges = ( isset( $_POST['badges'] ) && strlen( trim($_POST['badges'] ) ) > 0 ) ? preg_split( '/[\s,]+/', $_POST['badges'] ) : array();
		$points = ( isset( $_POST['points'] ) && is_numeric( $_POST['points'] ) ) ? intval( $_POST['points'] ) : 0;
		$enabled = ( isset( $_POST['enabled'] ) && $_POST['enabled'] == 'true' ) ? true : false;
		$expiry_value = ( isset( $_POST['expiryValue'] ) ) ? $_POST['expiryValue'] : 0;
		$expiry_unit = ( isset( $_POST['expiryUnit'] ) ) ? $_POST['expiryUnit'] : '';
		$recurring = ( isset( $_POST['recurring'] ) && $_POST['recurring'] == 'true' ) ? true : false;
		
		$badges = array_filter( $badges ); // removes empty array elements if they are there
		
		// TODO sanitize input here
		
		if ( $condition_id != null ) {
			
			$condition = new UB_Condition( $condition_id, $name, $badges, $points, null, $enabled, $expiry_unit, $expiry_value, $recurring );
			
			if ( is_array( $_POST['steps'] ) ) {
				foreach ( $_POST['steps'] as $step ) {
					$step_id = intval( $step['stepId'] );
					$action_name = $step['actionName']; // do we need to check action_name is valid?
					$label = $step['label'];
					
					$step_meta = array();
					if ( is_array( $step['stepMeta'] )) {
						foreach ( $step['stepMeta'] as $meta ) {
							array_push( $step_meta, array( 'key' => $meta['key'], 'value' => $meta['value'] ) );
						}
					}
					array_push( $condition->steps, new UB_Step( $step_id, $condition_id, $label, $action_name, null, $step_meta ) );
				}
			}
			
			User_Badges::instance()->api->save_condition( $condition );
			
			echo json_encode( array(
					'success' => true,
					'message' => __( 'Condition saved.', 'user-badges' ),
					'data' => array( 
							'name' => sprintf( __( 'Condition %d - %s', 'user-badges' ), $condition->condition_id, esc_html( $condition->name ) ),
							'status' => ub_condition_status( $condition, false )
			) ) );
			
		} else {
			echo json_encode( array(
					'success' => false,
					'message' => __( 'Unknown condition.', 'user-badges' )
			) );
		}
	}
	
	die();
}