<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Shows the settings screen
*/
function broo_settings_page() {
	?>
	<div class="wrap">
		
		<h2 class="nav-tab-wrapper">
			<?php
			$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'broo_general_settings';
			$page = Badgearoo::SETTINGS_PAGE_SLUG;
			$tabs = array (
					'broo_general_settings'		=> __( 'General', 'badgearoo' ),
					'broo_email_settings'		=> __( 'Emails', 'badgearoo' ),
					'broo_bp_settings'			=> __( 'BuddyPress', 'badgearoo' ),
					'broo_action_settings' 		=> __( 'Actions', 'badgearoo' )
			);
			
			$tabs = apply_filters( 'broo_settings_tabs', $tabs );
			
			foreach ( $tabs as $tab_key => $tab_caption ) {
				$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
				echo '<a class="nav-tab ' . $active . '" href="edit.php?post_type=badge&page=' . $page . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
			}
			?>
		</h2>
		
		<?php 

		if ( $current_tab == 'broo_action_settings' && isset( $_POST['submit'] ) ) {

			global $wpdb;
			$actions_enabled = (array) get_option( 'broo_actions_enabled' );
		
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
		
			update_option( 'broo_actions_enabled', $actions_enabled );
		}
		
		if ( isset( $_GET['updated'] ) && isset( $_GET['page'] ) ) {
			add_settings_error( 'general', 'settings_updated', __('Settings saved.', 'badgearoo' ), 'updated' );
		}

		settings_errors();
		
		if ( $current_tab == 'broo_general_settings' ) {
			?>
			<form method="post" name="broo_general_settings" action="options.php">
				<?php
				wp_nonce_field( 'update-options' );
				settings_fields( 'broo_general_settings' );
				do_settings_sections( 'broo_general_settings' );
				submit_button(null, 'primary', 'submit', true, null);
				?>
			</form>
			<?php
		} else if ( $current_tab == 'broo_email_settings' ) {
			?>
			<form method="post" name="broo_email_settings" action="options.php">
				<?php
				wp_nonce_field( 'update-options' );
				settings_fields( 'broo_email_settings' );
				do_settings_sections( 'broo_email_settings' );
				submit_button(null, 'primary', 'submit', true, null);
				?>
			</form>
			<?php
		} else if ( $current_tab == 'broo_bp_settings' ) {
			?>
			<form method="post" name="broo_bp_settings" action="options.php">
				<?php
				wp_nonce_field( 'update-options' );
				settings_fields( 'broo_bp_settings' );
				do_settings_sections( 'broo_bp_settings' );
				submit_button(null, 'primary', 'submit', true, null);
				?>
			</form>
			<?php
		} else {
			
			global $wpdb;
			$actions_enabled = (array) get_option( 'broo_actions_enabled' );
			
			$action_sources = $wpdb->get_col( 'SELECT DISTINCT(source) AS source FROM ' . $wpdb->prefix . BROO_ACTION_TABLE_NAME );
			
			if ( count( $action_sources ) > 0 ) { ?>
				
				<form method="post" id="broo-actions-form">
					<table class="form-table">
						<tbody>
							<?php 
							foreach ( $action_sources as $source ) {
								
								$disabled = false;
								
								if ( ( ! class_exists( 'BuddyPress' ) && $source == 'BuddyPress' ) 
										|| ( ! class_exists( 'bbPress' ) && $source == 'bbPress' )
										|| ( ! class_exists( 'Easy_Digital_Downloads' ) && $source == 'Easy Digital Downloads' )
										|| ( ! class_exists( 'WooCommerce' ) && $source == 'WooCommerce' ) ) {
									$disabled = true;
								}
								?>
								<tr>
									<th scope="row">
										<?php 
										echo $source; 
										if ( $disabled ) {
											?>&nbsp;<span style="color: grey; font-weight: normal;">(<?php _e( 'inactive', 'badgearoo' ); ?>)</span><?php
										} ?>
									</th>
									<td>
										<?php
										$actions = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . BROO_ACTION_TABLE_NAME . ' WHERE source = "' . $source . '"' );
										
										$index = 0;
										$count = count( $actions );
										foreach ( $actions as $action ) {
											
											$enabled = false;
											if ( is_array( $actions_enabled ) && in_array( $action->name, $actions_enabled )
													&& isset( $actions_enabled[$action->name] ) && $actions_enabled[$action->name] ) {
												$enabled = true;
											}
											
											?>
											<input <?php if ( $disabled ) echo 'disabled'; ?> type="checkbox" name="actions-enabled[]" value="<?php echo $action->name; ?>"<?php if ( $enabled ) { echo ' checked'; } ?> />
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