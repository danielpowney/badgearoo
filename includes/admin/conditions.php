<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Conditions page
 */
// TODO sort conditions by last modified date

function broo_conditions_page() {
	?>
	<div class="wrap">
		<h2><?php _e( 'Conditions', 'badgearoo' ); ?>
			<a class="add-new-h2" href="#" id="add-condition"><?php _e('Add New', 'badgearoo' ); ?></a>
		</h2>
		
		<?php 
		
		global $wpdb;
		
		$query = 'SELECT enabled, COUNT(*) AS count FROM ' . $wpdb->prefix . BROO_CONDITION_TABLE_NAME . ' GROUP BY enabled';
		$results = $wpdb->get_results( $query );
		
		$enabled_count = 0;
		$disabled_count = 0;
		
		foreach ( $results as $row ) {
			if ( $row->enabled ) {
				$enabled_count = intval( $row->count );
			} else {
				$disabled_count = intval( $row->count );
			}
		}
		$total_count = $enabled_count + $disabled_count;
		
		$enabled = null;
		if ( isset( $_REQUEST['broo-enabled'] ) && $_REQUEST['broo-enabled'] == "1" ) {
			$enabled = true;
		} else if ( isset( $_REQUEST['broo-enabled'] ) && $_REQUEST['broo-enabled'] == "0" ) {
			$enabled = false;
		}
		?>
			
		<ul class="subsubsub">
			<li class="all"><a href="edit.php?post_type=badge&page=<?php echo Badgearoo::CONDITIONS_PAGE_SLUG; ?>" <?php if ( $enabled === null ) { echo 'class="current"'; } ?>><?php _e( 'All', 'badgearoo' ); ?>&nbsp;<span class="count">(<?php echo $total_count; ?>)</span></a> |</li>
			<li class="all"><a href="edit.php?post_type=badge&page=<?php echo Badgearoo::CONDITIONS_PAGE_SLUG; ?>&broo-enabled=1" <?php if ( $enabled === true ) { echo 'class="current"'; } ?>><?php _e( 'Enabled', 'badgearoo' ); ?>&nbsp;<span class="count">(<?php echo $enabled_count; ?>)</span></a> |</li>
			<li class="all"><a href="edit.php?post_type=badge&page=<?php echo Badgearoo::CONDITIONS_PAGE_SLUG; ?>&broo-enabled=0" <?php if ( $enabled === false ) { echo 'class="current"'; } ?>><?php _e( 'Disabled', 'badgearoo' ); ?>&nbsp;<span class="count">(<?php echo $disabled_count; ?>)</span></a></li>
		</ul>
	
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-1">
				<div id="postbox-container" class="postbox-container">
					<div id="poststuff">
						<div id="post-body" class="metabox-holder columns-1">
							<div id="postbox-container" class="postbox-container active">
								<div id="normal-sortables" class="meta-box-sortables ui-sortable">
									<?php 
									$conditions = Badgearoo::instance()->api->get_conditions( array( 'enabled' => $enabled ) );
									
									if ( count( $conditions ) == 0 ) {
										$name = __( 'New Condition' );
										$condition = Badgearoo::instance()->api->add_condition( $name );
										array_push( $conditions, $condition );
									}
									
									$is_closed = false;
									if ( count( $conditions ) > 1 ) {
										$is_closed = true;
									}
									
									foreach ( $conditions as $condition ) {
										broo_display_condition_meta_box( $condition, $is_closed );
										$is_closed = true;
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
function broo_display_condition_meta_box( $condition, $is_closed = false ) {
	?>
	<div <?php if ( isset( $condition->condition_id ) ) echo 'id="condition-' . $condition->condition_id . '"'; ?> class="postbox <?php if ( $is_closed ) echo 'closed'; ?>">
		<div class="handlediv" title="Click to toggle"><br /></div>
		<h3 class="hndle ui-sortable-handle">
			<span>
				<?php printf( __( 'Condition %d - %s', 'badgearoo' ), $condition->condition_id, esc_html( $condition->name ) ); ?>
			</span>
			<?php 
			$condition_status = broo_condition_status( $condition );
			
			$status_html = null;
			if ( $condition_status['incomplete'] == true ) {
				echo '- <span style="font-weight: 600; color: red;">' . __( 'Incomplete', 'badgearoo' ) . '</span>';
			} else if ( $condition->enabled == false ) {
				echo '- <span style="font-weight: 600; color: #f4a460;">' . __( 'Disabled', 'badgearoo' ) . '</span>';
			} else {
				echo '- <span style="font-weight: 600; color: green;">' . __( 'Enabled', 'badgearoo' ) . '</span>';
			}
			?>
		</h3>
		<div class="inside">
		
			<?php 
			if ( $condition_status['incomplete'] && count( $condition_status['messages'] ) > 0 ) {
				?>
				<div class="update-nag" style="margin: 10px 0 10px !important; display: block;">
					<?php 
					foreach ( $condition_status['messages'] as $message ) {
						?><p><?php echo $message; ?></p><?php
					}
					?>
				</div>
				<?php
			}
			?>
			
			<form method="post" class="condition">
				<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
	
				<table class="broo-condition">
					<tr>
						<td>
							<table class="form-table">
								<tbody>
									<tr>
										<th scope="row"><?php _e( 'Name', 'badgearoo' ); ?></th>
										<td><input type="text" name="name" value="<?php echo $condition->name; ?>" class="regular-text" /></td>
									</tr>
									<tr>
										<th scope="row"><?php _e( 'Enabled', 'badgearoo' ); ?></th>
										<td><input type="checkbox" name="enabled" value="true" <?php checked( $condition->enabled, true, true ); ?>/></td>
									</tr>
									<tr>
										<th scope="row"><?php _e( 'Badges', 'badgearoo' ); ?></th>
										<td>
											<?php 
											broo_dropdown_badges( array( 'name' => 'addBadge', 'echo' => true ) );
											?>
											<input type="button" class="button secondary addBadgeBtn" value="Add Badge" />
											<p class="description"><?php _e( 'Allocate badges to users'); ?></p>
											
											<div class="tagchecklist" style="max-width: 100%;">
												<?php if ( count( $condition->badges ) > 0 ) {
													foreach ( $condition->badges as $badge_id ) {
														$badge = Badgearoo::instance()->api->get_badge( $badge_id );
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
										<th scope="row"><?php _e( 'Points', 'badgearoo' ); ?></th>
										<td>
											<input type="number" class="medium-text" name="points" value="<?php echo $condition->points; ?>" />
											<p class="description"><?php _e( 'Allocate points to users.', 'badgearoo' ); ?></p>
										</td>
									</tr>
									<?php do_action( 'broo_condition_other_assignment', $condition ); ?>
									<tr>
										<th scope="row"><?php _e( 'Recurring', 'badgearoo' ); ?></th>
										<td>
											<input type="checkbox" name="recurring" value="true" <?php checked( $condition->recurring, true, true ); ?>/>
											<label for="recurring"><?php _e( 'Assignments can occur more than once.', 'badgearoo' ); ?></label>
											<p class="description"><?php _e( 'If turned on, assignments cannot be renewed.', 'badgearoo' ); ?>
										</td>
									</tr>
									<tr>
										<th scope="row"><?php _e( 'Expiry', 'badgearoo' ); ?></th>
										<td>
											<input type="number" name="expiry-value" class="broo-expiry-value" value="<?php if ( $condition->expiry_value != 0 ) { echo $condition->expiry_value; } ?>" />
											<select name="expiry-unit">
												<option value="day" <?php if ( $condition->expiry_unit == 'day' ) { echo 'selected="selected"'; }?>><?php _e( 'Day(s)', 'badgearoo' ); ?></option>
												<option value="week" <?php if ( $condition->expiry_unit == 'week' ) { echo 'selected="selected"'; }?>><?php _e( 'Week(s)', 'badgearoo' ); ?></option>
												<option value="month" <?php if ( $condition->expiry_unit == 'month' ) { echo 'selected="selected"'; }?>><?php _e( 'Month(s)', 'badgearoo' ); ?></option>
												<option value="year" <?php if ( $condition->expiry_unit == 'year' ) { echo 'selected="selected"'; }?>><?php _e( 'Year(s)', 'badgearoo' ); ?></option>
											</select>
											<p class="description"><?php _e( 'Leave empty if assignmemt has no expiration.', 'badgearoo' ); ?></p>
										</td>
										
									</tr>
								</tbody>
							</table>
						</td>
						<td class="broo-condition-steps">
							<ul class="broo-step-list">
								<?php
								if ( count( $condition->steps ) == 0 ) {
									$label = __( 'New Step', 'badgearoo' );
									
									$step = Badgearoo::instance()->api->add_step( $condition->condition_id, $label );
									
									array_push( $condition->steps, $step );
								}
								
								foreach ( $condition->steps as $step ) {
									broo_display_step( $step );
								}
								?>
							</ul>
							<p><input type="button" class="button add-step-btn" button" value="Add Step" /></p>
						</td>
					</tr>
				</table>
			
				<p>
					<input type="submit" class="button button-primary save-condition-btn" value="<?php _e( 'Save Changes', 'badgearoo' ); ?>" />
					<input type="button" class="button button-secondary delete-condition-btn" value="<?php _e( 'Delete', 'badgearoo' ); ?>" />
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
function broo_condition_status( $condition ) {
	
	$actions_enabled = (array) get_option( 'broo_actions_enabled' );

	$incomplete = false;
	$messages = array();
	
	if ( strlen( $condition->name ) == 0 ) {
		$incomplete = true;
		array_push( $messages, __( 'Name required.', 'badgearoo' ) );
	} 
	
	if ( count( $condition->badges ) == 0 && $condition->points == 0 ) {
		$incomplete = true;
		array_push( $messages, __( 'Badges or points assignment required.', 'badgearoo' ) );
	}
	
	if ( count( $condition->steps ) == 0 ) {
		$incomplete = true;
		array_push( $messages, __( 'Condition must have steps.', 'badgearoo' ) );
	}

	$action_not_found = false;
	$action_not_enabled = false;
	foreach ( $condition->steps as $step ) {
		if ( $step->action_name == null ) {
			$incomplete = true;
			$action_not_found = true;
			break;
		} else if ( ! isset( $actions_enabled[$step->action_name] ) 
				|| ( isset( $actions_enabled[$step->action_name] ) 
				&& $actions_enabled[$step->action_name] == false ) ) {
			$incomplete = true;
			$action_not_enabled = true;
		}
	}
	
	if ( $action_not_found ) {
		array_push( $messages, __( 'Each step must have an action.', 'badgearoo' ) );
	}
	
	if ( $action_not_enabled ) {
		// TODO add link to action settings tab
		array_push( $messages, __( 'Each step action must be enabled. Check plugin settings.', 'badgearoo' ) );
	}
	
	return array( 
			'incomplete' => $incomplete,
			'messages' => $messages
	);
}

/**
 * Displays a step
 * 
 * @param unknown $step_id
 * @return string
 */
function broo_display_step( $step ) {	
	?>
	<li id="step-<?php echo $step->step_id; ?>" class="broo-step ui-state-default">
		<label for="label"><?php _e( 'Label', 'badgearoo' ); ?></label>
		<input type="text" maxlength="50" name="label" value="<?php echo $step->label; ?>" class="regular-text" />
		
		<select name="action-name" class="action-name">
			<option value=""><?php _e( 'Please select an action.', 'badgearoo' ); ?>
			<?php
			
			$actions = Badgearoo::instance()->api->get_actions();
			
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
				do_action( 'broo_step_meta', $step->step_id, $step->action_name );
			}
			?>
		</div>
		
		<a href="#" class="delete-step"><?php _e( 'Delete', 'badgearoo' ); ?></a>
	</li>
	<?php 
}


/**
 * Returns HTML for a new condition
 */
function broo_add_condition() {
	
	$ajax_nonce = $_POST['nonce'];
	if ( wp_verify_nonce( $ajax_nonce, Badgearoo::ID.'-nonce' ) ) {

		ob_start();
	
		global $wpdb;	
		
		$name = __( 'New Condition' );
		$condition = Badgearoo::instance()->api->add_condition( $name );
		
		broo_display_condition_meta_box( $condition, false );
	
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
function broo_add_step() {
	
	$ajax_nonce = $_POST['nonce'];
	if ( wp_verify_nonce( $ajax_nonce, Badgearoo::ID.'-nonce' ) ) {

		ob_start();
		
		$label = __( 'New Step', 'badgearoo' );
		$condition_id = $_POST['conditionId'];
		
		$step = Badgearoo::instance()->api->add_step( $condition_id, $label );
	
		broo_display_step( $step );
	
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
function broo_delete_step() {

	$ajax_nonce = $_POST['nonce'];
	if ( wp_verify_nonce( $ajax_nonce, Badgearoo::ID.'-nonce' ) ) {
		$step_id = $_POST['stepId'];

		Badgearoo::instance()->api->delete_step( $step_id );

		echo json_encode( array(
				'success' => true
		) );
	}

	die();
}


/**
 * Deletes a condition
 */
function broo_delete_condition() {

	$ajax_nonce = $_POST['nonce'];
	if ( wp_verify_nonce( $ajax_nonce, Badgearoo::ID.'-nonce' ) ) {
		$condition_id = $_POST['conditionId'];

		Badgearoo::instance()->api->delete_condition( $condition_id );
		
		echo json_encode( array(
				'success' => true
		) );
	}

	die();
}


/**
 * Returns HTML for step meta
 */
function broo_step_meta() {
	
	$ajax_nonce = $_POST['nonce'];
	if ( wp_verify_nonce( $ajax_nonce, Badgearoo::ID.'-nonce' ) ) {
		
		$step_id = $_POST['stepId'];
		$action_name = $_POST['actionName'];
	
		ob_start();
		
		do_action( 'broo_step_meta', $step_id, $action_name );
		
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
function broo_save_condition() {
	
	$ajax_nonce = $_POST['nonce'];
	if ( wp_verify_nonce( $ajax_nonce, Badgearoo::ID.'-nonce' ) ) {
		
		$condition_id = ( isset( $_POST['conditionId'] ) && is_numeric( $_POST['conditionId'] ) ) ? intval( $_POST['conditionId'] ) : null;
		$name = ( isset( $_POST['name'] ) ) ? $_POST['name'] : '';
		$badges = ( isset( $_POST['badges'] ) && strlen( trim($_POST['badges'] ) ) > 0 ) ? preg_split( '/[\s,]+/', $_POST['badges'] ) : array();
		$points = ( isset( $_POST['points'] ) && is_numeric( $_POST['points'] ) ) ? intval( $_POST['points'] ) : 0;
		$enabled = ( isset( $_POST['enabled'] ) && $_POST['enabled'] == 'true' ) ? true : false;
		$expiry_value = ( isset( $_POST['expiryValue'] ) ) ? $_POST['expiryValue'] : 0;
		$expiry_unit = ( isset( $_POST['expiryUnit'] ) ) ? $_POST['expiryUnit'] : '';
		$recurring = ( isset( $_POST['recurring'] ) && $_POST['recurring'] == 'true' ) ? true : false;
		
		$steps = array();
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
					
				array_push( $steps, new BROO_Step( $step_id, $condition_id, $label, $action_name, null, $step_meta ) );
			}
		}
		
		$badges = array_filter( $badges ); // removes empty array elements if they are there
		
		// TODO sanitize input here
		
		if ( $condition_id != null ) {
			
			$condition = new BROO_Condition( $condition_id, $name, $badges, $points, null, $enabled, $expiry_unit, $expiry_value, $recurring );
			$condition->steps = $steps;
			
			Badgearoo::instance()->api->save_condition( $condition );
			
			$condition_status = broo_condition_status( $condition );
			
			$status_html = null;
			if ( $condition_status['incomplete'] == true ) {
				$status_html = '<span style="font-weight: 600; color: #555;"> - ' . __( 'Incomplete', 'badgearoo' ) . '</span>';
			}
			
			$messages_html = '<div class="updated" style="margin: 10px 0 10px !important; display: block;"><p>' . __( 'Condition saved.', 'badgearoo' ) . '</p></div>';
			
			if ( count( $condition_status['messages'] ) > 0 ) {
				$messages_html .= '<div class="update-nag" style="margin: 10px 0 10px !important; display: block;">';
				foreach ( $condition_status['messages'] as $message ) {
					$messages_html .= '<p>' . $message . '</p>';
				}
				$messages_html .= '</div>';
			}
			
			echo json_encode( array(
					'success' => true,
					'data' => array( 
							'name' => sprintf( __( 'Condition %d - %s', 'badgearoo' ), $condition->condition_id, esc_html( $condition->name ) ),
							'status_html' => $status_html,
							'messages_html' => $messages_html
			) ) );
			
		} else {
			echo json_encode( array(
					'success' => false,
					'message' => __( 'Unknown condition.', 'badgearoo' )
			) );
		}
	}
	
	die();
}