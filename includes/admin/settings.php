<?php

/**
 * Shows the settings screen
*/
function ub_settings_screen() {
	?>
	<div class="wrap">
	
		<h2><?php _e( 'Settings', 'user-badges' ); ?></h2>
		
		<?php 
		
		settings_errors();
		
		if ( isset( $_GET['updated'] ) && isset( $_GET['page'] ) ) {
			add_settings_error('general', 'settings_updated', __( 'Settings saved.', 'user-badges' ), 'updated');
		}
		
		// TODO forms
		?>
		
		
			
	</div>
	<?php 
}

?>