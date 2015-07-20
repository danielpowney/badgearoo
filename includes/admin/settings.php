<?php

/**
 * Shows the settings screen
*/
function ub_settings_page() {
	?>
	<div class="wrap">
	
		<h2><?php _e( 'Settings', 'user-badges' ); ?></h2>
		
		<?php
		global $wpdb;
		
		$actions_enabled = (array) get_option( 'ub_actions_enabled' );
		
		// if submit button clicked, update options
		if ( isset( $_POST['submit'] ) ) {
			
			$temp_actions_enabled = array();
			foreach (  $_POST['actions-enabled'] as $action_name ) {
				array_push( $temp_actions_enabled, $action_name );
			}
			
			foreach ( $actions_enabled as $action_name => $action_enabled ) {
				
				if ( in_array( $action_name, $temp_actions_enabled ) ) {
					$actions_enabled[$action_name] = true;
				} else {
					$actions_enabled[$action_name] = false;
				}
			}
			
			update_option( 'ub_actions_enabled', $actions_enabled );
			
			add_settings_error( 'general', 'settings_updated', __( 'Settings saved.', 'user-badges' ), 'updated');
		}
		
		$action_sources = $wpdb->get_col( 'SELECT DISTINCT(source) AS source FROM ' . $wpdb->prefix . UB_ACTION_TABLE_NAME );
		
		if ( count( $action_sources ) > 0 ) { ?>
			
			<form method="post" id="ub-actions-form">
				<table class="form-table">
					<tbody>
						<?php 
						foreach ( $action_sources as $source ) {?>
							<tr>
								<th scope="row"><?php echo $source; ?></th>
								<td>
									<?php
									$actions = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . UB_ACTION_TABLE_NAME . ' WHERE source = "' . $source . '"' );
									
									$index = 0;
									$count = count( $actions );
									foreach ( $actions as $action ) {
										
										$enabled = false;
										if ( is_array( $actions_enabled ) && in_array( $action->name, $actions_enabled )
												&& isset( $actions_enabled[$action->name] ) && $actions_enabled[$action->name] ) {
											$enabled = true;
										}
										
										?>
										<input type="checkbox" name="actions-enabled[]" value="<?php echo $action->name; ?>"<?php if ( $enabled ) { echo ' checked'; } ?> />
										<label for="actions-enabled[]"><?php echo $action->description; ?></label>
										<?php
										
										if ( $index < $count ) {
											echo '<br />';
										}
										$index++;
									}
								?>
								</td>
							<tr>
						<?php }?>
					</tbody>
				</table>
				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
				</p>
			</form>
		
		<?php } ?>
	</div>
	<?php 
}
?>