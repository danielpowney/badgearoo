<?php

/**
 * Shows the settings screen
*/
function ub_settings_page() {
	?>
	<div class="wrap">
		
		<h2 class="nav-tab-wrapper">
			<?php
			$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'ub_action_settings';
			$page = User_Badges::SETTINGS_PAGE_SLUG;
			$tabs = array (
					'ub_action_settings' 		=> __( 'Actions', 'user-badges' ),
					'ub_general_settings'		=> __( 'General', 'user-badges' )
			);
			
			$tabs = apply_filters( 'ub_settings_tabs', $tabs );
			
			foreach ( $tabs as $tab_key => $tab_caption ) {
				$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
				echo '<a class="nav-tab ' . $active . '" href="edit.php?post_type=badge&page=' . $page . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
			}
			?>
		</h2>
		
		<?php 
			
		if ( $current_tab == 'ub_general_settings' && isset( $_POST['submit'] ) ) {

			global $wpdb;
			$actions_enabled = (array) get_option( 'ub_actions_enabled' );
		
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
		}
		
		if ( isset( $_GET['updated'] ) && isset( $_GET['page'] ) ) {
			add_settings_error( 'general', 'settings_updated', __('Settings saved.', 'user-badges' ), 'updated' );
		}

		settings_errors();
		
		if ( $current_tab == 'ub_general_settings' ) {
			?>
			<form method="post" name="ub_general_settings" action="options.php">
				<?php
				wp_nonce_field( 'update-options' );
				settings_fields( 'ub_general_settings' );
				do_settings_sections( 'ub_general_settings' );
				submit_button(null, 'primary', 'submit', true, null);
				?>
			</form>
			<?php
		} else {
			
			global $wpdb;
			$actions_enabled = (array) get_option( 'ub_actions_enabled' );
			
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
			
			<?php
			} 
		} ?>
	</div>
	<?php
}
?>