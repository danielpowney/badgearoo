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
									$conditions = User_Badges::instance()->api->get_conditions();
									
									if ( count( $conditions ) == 0 ) {
										$name = __( 'New Condition' );
										$condition = User_Badges::instance()->api->add_condition( $name );
										array_push( $conditions, $condition);
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
		<h3 class="hndle ui-sortable-handle"><span><?php echo esc_html( $condition->name ); ?></span></h3>
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
										<th scope="row"><?php _e( 'Badge', 'user-badges' ); ?></th>
										<td>
											<select name="badgeId">
												<option value=""><?php _e( 'No badge', 'user-badges' ); ?></option>
												<?php 
												
												$badges = User_Badges::instance()->api->get_badges();
												foreach ( $badges as $badge ) {
													?><option value="<?php echo $badge->id; ?>" <?php if ( $condition->badge_id == $badge->id ) echo 'selected'; ?>><?php echo esc_html( stripslashes( $badge->name ) ); ?></option><?php
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
function ub_step_meta_count( $step_id, $action  ) {
	if ( $action == UB_WP_LOGIN_ACTION || $action == UB_WP_PUBLISH_POST_ACTION || $action == UB_WP_SUBMIT_COMMENT_ACTION ) { 
		$count = User_Badges::instance()->api->get_step_meta_value( $step_id, 'count' );
		?>
		<span class="step-meta-value">
			<input name="count" type="number" value="<?php echo $count; ?>" class="small-text" />&nbsp;<?php _e( 'time(s)', 'user-badges' ); ?>
		</span>
		<?php
	}
}
add_action( 'ub_step_meta', 'ub_step_meta_count', 10, 2 );

/**
 * Shows a points step meta
 *
 * @param unknown $step_id
 * @param unknown $action
 */
function ub_step_meta_points( $step_id, $action  ) {
	if ( $action == UB_MIN_POINTS_ACTION ) {
		$points = User_Badges::instance()->api->get_step_meta_value( $step_id, 'points' );
		?>
		<span class="step-meta-value">
			<input name="points" type="number" value="<?php echo $points; ?>" class="small-text" />&nbsp;<?php _e( 'points', 'user-badges' ); ?>
		</span>
		<?php
	}
}
add_action( 'ub_step_meta', 'ub_step_meta_points', 10, 2 );

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
		
		$label = __( 'New Step' );
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
		
		$condition_id = intval( $_POST['conditionId'] );
		$name = $_POST['name'];
		$badge_id = intval( $_POST['badgeId'] );
		$points = intval( $_POST['points'] );
		
		$condition = new UB_Condition( $condition_id, $name, $badge_id, $points, null, '', false, false );
		
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
				'message' => __('Condition saved.'),
				'data' => array( 'name' => esc_html( $name ) )
		) );
	}
	
	die();
}