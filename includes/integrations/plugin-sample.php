<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Define a name for your custom action hook. It should be the same as the action hook name 
 * to make it simple.
 */ 
define( 'MY_CUSTOM_ACTION', 'my_custom_action' );

/**
 * Initialise any custom actions that you want to be registered. Once you do this, the action will 
 * show in the plugin settings and be available to select in a condition step.
 */
function init_my_actions( $broo_actions ) {

	$broo_actions[MY_CUSTOM_ACTION] = array(
			'description' => __( 'User performs my custom action.', 'my-plugin' ),
			'source' =>	__( 'My plugin', 'my-plugin' )
	);

	return $broo_actions;
}
add_filter( 'broo_init_actions', 'init_my_actions', 10, 1 );


/**
 * Now add the logic to record when your custom action is completed and also the check that is performed
 * to determine whether a step in a condition has been achieved.
 *
 * @param actions
*/
function add_my_actions( $actions = array() ) {

	if ( isset( $actions[MY_CUSTOM_ACTION] ) && $actions[MY_CUSTOM_ACTION]['enabled'] == true ) {
		/* 
		 * When the action hook is fired, call a function to record the action has been performed 
		 * including any step meta data that may be used
		 */
		add_action( 'my_custom_action', 'save_my_custom_action', 10, 2 ); // 2 variables $user_id and $some_other_data
		
		/*
		 * Sets up the function which checks whether a condition step has been met.
		 */
		add_filter( 'broo_condition_step_check_my_custom_action', 'check_condition_step_my_custom_action', 10, 4 );
	}
}
add_action( 'broo_init_actions_complete', 'add_my_actions' );


/**
 * Sets whether step meta is enabled. This is important otherwise the step meta will be enabled for all actions.
 *
 * @param unknown $enabled
 * @param unknown $action
 * @return boolean|unknown
*/
function my_some_other_data_step_meta_nabled( $enabled, $action ) {

	if ( $action == MY_CUSTOM_ACTION ) {
		return true;
	}

	return $enabled;
}



/**
 * Defaults actions enabled in settings. This will default the action to enabled or disabled 
 * in the plugin settings.
 *
 * @param array $actions_enabled
 * @return $actions_enabled:
 */
function default_my_actions_enabled( $actions_enabled ) {

	return array_merge( array(
			MY_CUSTOM_ACTION		=> true, // enabled by default
	), $actions_enabled );

}
add_filter( 'broo_default_actions_enabled', 'default_my_actions_enabled', 10, 1 );


/**
 * Saves my custom action and any step meta you want saved along with this action.
 *
 * @param unknown $user_id
 * @param unknown $some_other_data
*/
function save_my_custom_action( $user_id, $some_other_data ) {
	/* 
	 * If the action hook does not have the user_id, you will need to get it from somewhere 
	 * e.g. get_current_user_id().
	 * 
	 * Lets assume the action hook contains $user_id and we want to include $some_other_data in 
	 * the step meta which can be used as a part of checking whether a condition step has been 
	 * satisfied
	 */
	Badgearoo::instance()->api->add_user_action( MY_CUSTOM_ACTION, $user_id, array( 'some_other_data' => $some_other_data ) );
}



/**
 * Shows step meta HTML that is a part of the form in a condition step
 *
 * @param unknown $step_id
 * @param unknown $action
 */
function step_meta_some_other_data( $step_id, $action  ) {
	
	if ( $action != MY_CUSTOM_ACTION ) { // make sure it's only displayed for my custom action :)
		return;
	}

	$some_other_data = Badgearoo::instance()->api->get_step_meta_value( $step_id, 'some_other_data' ); // retrieve the step meta value
		
	if ( $some_other_data == null ) {
		$some_other_data = "test";
	}
	?>
	<span class="step-meta-value">
		<label for="some_other_data"><?php _e( 'Some other data', 'my-plugin' ); ?></label>
		<input name="some_other_data" type="text" value="<?php echo $some_other_data; ?>" class="small-text" />
	</span>
	<?php
}
add_action( 'broo_step_meta', 'step_meta_some_other_data', 10, 2 );



/**
 * Checks whether the condition step has been achieved.
 * 
 *  In this case, it checks at least one my custom action exists with step meta called "some_other_data" 
 *  that equals the value entered in the condition step.
 *
 * @param unknown $step_result
 * @param unknown $step
 * @param int $user_id
 * @param string $action_name
 * @return boolean
 */
function check_condition_step_my_custom_action( $step_result, $step, $user_id, $action_name ) {

	if ( $step_result == false ) { // no need to continue
		return $step_result;
	}
	
	$some_other_data = Badgearoo::instance()->api->get_step_meta_value( $step->step_id, 'some_other_data' );	

	global $wpdb;
	$query = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . BROO_USER_ACTION_TABLE_NAME
			. ' ua INNER JOIN ' . $wpdb->prefix . BROO_USER_ACTION_META_TABLE_NAME
			. ' uam ON uam.user_action_id = ua.id WHERE ua.action_name = "'
			. esc_sql( $action_name ) . '" AND ua.user_id = ' . $user_id
			. ' AND uam.meta_key = "some_other_data" AND uam.meta_value = "' 
			. esc_sql( $some_other_data ) . '"';

	$db_count = $wpdb->get_var( $query );

	if ( intval( $db_count ) == 0 ) {
		return false;
	}

	return $step_result;
}

/**
 * Demonstrates how to setup custom actions and also trigger assignment of points 
 * and badges. Uncomment what you need and change the badge ids.
 */
function test_my_plugin() {
	
	$user_id = get_current_user_id(); 
	
	/**
	 * My custom action hook executes. Make sure you setup a 
	 * condition using the new action.
	 * 
	 */
	//do_action( 'my_custom_action', $user_id, 'test' );
	
	$condition_id = null; // optional to have a condition otherwise null
	$expiry_dt = null; // date format "Y-m-d H:i:s" or null
	
	/**
	 * Shows you how to assign points to a user
	 */
	$points = 100; // points amount
	//Badgearoo::instance()->api->add_user_assignment( $condition_id, $user_id, 'points', $points, $expiry_dt );

	/**
	 * Shows how to assign a badge to a user
	 */
	$badge_id = 601; // badge post id
	//Badgearoo::instance()->api->add_user_assignment( $condition_id, $user_id, 'badge', $badge_id, $expiry_dt );
}
add_action( 'init', 'test_my_plugin' );