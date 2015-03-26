<?php

/**
 * Shows the settings screen
*/
function ub_settings_page() {
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
		
		<p>Show list of actions. Turn on or off.</p>
		<p>How do we assign predefined actions to badges? Needs to support: select an action, how many times it has to occur, add other actions. Set any order. Assign badges from drop down.
			
			







// TODO
// 1. if user has been active for 1 year (can we check if they've logged in)

// 2. if user has commented on a post, return true

// 3. How to handle admin awarded badges and ad-hoc badges for plugin add-ons

// 4. Steps. Add number of steps that needs to occur before a  badge is awarded. 
// This could be implemented tieing the event of actions hooks e.g. add_action( 'some_plugin_event_a' ) 
// and the storing this event has occured for the user in db. Would need to register all steps first 
// so admins can construct their own steps to get a badge.

// 5. filter users table by badge. Delete badge from user?
	</div>
	<?php 
}

?>