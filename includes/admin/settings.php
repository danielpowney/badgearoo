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
		
		$actions_enabled = isset( $_POST['actions-enabled'] ) ?  $_POST['actions-enabled'] : null;
		
		if ( $actions_enabled ) {
		
			$results = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . UB_ACTION_TABLE_NAME );
			foreach ( $results as $row ) {
				$enabled = false;
					
				if ( in_array( $row->name, $actions_enabled ) ) {
					$enabled = true;
				}
					
				$wpdb->update( $wpdb->prefix . UB_ACTION_TABLE_NAME, array( 'enabled' => $enabled), array( 'name' => $row->name ), array( '%d' ), array( '%s' ) );
			}
			
			add_settings_error('general', 'settings_updated', __( 'Settings saved.', 'user-badges' ), 'updated');
		}
		
		$action_sources = $wpdb->get_col( 'SELECT source FROM ' . $wpdb->prefix . UB_ACTION_TABLE_NAME . ' GROUP BY source' );
		
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
										?>
										<input type="checkbox" name="actions-enabled[]" value="<?php echo $action->name; ?>" <?php checked( 1, $action->enabled, true ); ?> />
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