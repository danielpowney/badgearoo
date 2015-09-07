<?php 
/**
 * bbPress actions
*/

/* do_action( 'bbp_new_forum', array(
			'forum_id'           => $forum_id,
			'post_parent'        => $forum_parent_id,
			'forum_author'       => $forum_author,
			'last_topic_id'      => 0,
			'last_reply_id'      => 0,
			'last_active_id'     => 0,
			'last_active_time'   => 0,
			'last_active_status' => bbp_get_public_status_id()
		) ); 
do_action( 'bbp_new_reply', $reply_id, $topic_id, $forum_id, $anonymous_data, $reply_author, false, $reply_to );
do_action( 'bbp_new_topic', $topic_id, $forum_id, $anonymous_data, $topic_author );
do_action( 'bbp_closed_topic', $topic_id );
*/

define( 'BBP_NEW_FORUM_ACTION', 'bbp_new_forum' );
define( 'BBP_NEW_REPLY_ACTION', 'bbp_new_reply' );
define( 'BBP_NEW_TOPIC_ACTION', 'bbp_new_topic' );
define( 'BBP_CLOSED_TOPIC_ACTION', 'bbp_closed_topic' );


function broo_init_bbp_actions( $broo_actions ) {

	$broo_actions[BBP_NEW_FORUM_ACTION] = array(
			'description' => __( 'Creates a new forum (outside wp-admin).', 'badgearoo' ),
			'source' =>	__( 'bbPress', 'badgearoo' )
	);
	
	$broo_actions[BBP_NEW_REPLY_ACTION] = array(
			'description' => __( 'Replies to forum topic (outside wp-admin).', 'badgearoo' ),
			'source' =>	__( 'bbPress', 'badgearoo' )
	);
	
	$broo_actions[BBP_NEW_TOPIC_ACTION] = array(
			'description' => __( 'Adds a new forum topic (outside wp-admin).', 'badgearoo' ),
			'source' =>	__( 'bbPress', 'badgearoo' )
	);
	
	$broo_actions[BBP_CLOSED_TOPIC_ACTION] = array(
			'description' => __( 'Closes a forum topic.', 'badgearoo' ),
			'source' =>	__( 'bbPress', 'badgearoo' )
	);

	return $broo_actions;
}
add_filter( 'broo_init_actions', 'broo_init_bbp_actions', 10, 1 );


/**
 * Adds bbPress actions
 *
 * @param actions
*/
function broo_add_bbp_actions( $actions = array() ) {

	$actions_enabled = (array) get_option( 'broo_actions_enabled' );

	if ( isset( $actions[BBP_NEW_FORUM_ACTION] ) && $actions[BBP_NEW_FORUM_ACTION]['enabled'] == true ) {
		add_action( 'bbp_new_forum',  'broo_bbp_new_forum', 10, 1 );
		add_filter( 'broo_condition_step_check_bbp_new_forum', 'broo_condition_step_check_count', 10, 4 );
	}
	
	if ( isset( $actions[BBP_NEW_REPLY_ACTION] ) && $actions[BBP_NEW_REPLY_ACTION]['enabled'] == true ) {
		add_action( 'bbp_new_reply',  'broo_bbp_new_reply', 10, 7 );
		add_filter( 'broo_condition_step_check_bbp_new_reply', 'broo_condition_step_check_count', 10, 4 );
	}
	
	if ( isset( $actions[BBP_NEW_TOPIC_ACTION] ) && $actions[BBP_NEW_TOPIC_ACTION]['enabled'] == true ) {
		add_action( 'bbp_new_topic',  'broo_bbp_new_topic', 10, 4 );
		add_filter( 'broo_condition_step_check_bbp_new_topic', 'broo_condition_step_check_count', 10, 4 );
	}
	
	if ( isset( $actions[BBP_CLOSED_TOPIC_ACTION] ) && $actions[BBP_CLOSED_TOPIC_ACTION]['enabled'] == true ) {
		add_action( 'bbp_closed_topic',  'broo_bbp_closed_topic', 10, 1 );
		add_filter( 'broo_condition_step_check_bbp_closed_topic', 'broo_condition_step_check_count', 10, 4 );
	}

	add_filter('broo_step_meta_count_enabled', 'broo_bbp_step_meta_count_enabled', 10, 2 );
}


add_action( 'broo_init_actions_complete', 'broo_add_bbp_actions' );

/**
 * Sets whether step meta count is enabled
 *
 * @param unknown $enabled
 * @param unknown $action
 * @return boolean|unknown
*/
function broo_bbp_step_meta_count_enabled( $enabled, $action ) {

	if ( $action == BBP_CLOSED_TOPIC_ACTION || $action == BBP_NEW_FORUM_ACTION 
			|| $action == BBP_NEW_REPLY_ACTION || $action == BBP_NEW_TOPIC_ACTION ) {
		return true;
	}

	return $enabled;
}


/**
 * Defaults actions enabled
 *
 * @param array $actions_enabled
 * @return $actions_enabled:
 */
function broo_default_bbp_actions_enabled( $actions_enabled ) {

	return array_merge( array(
			BBP_CLOSED_TOPIC_ACTION		=> false,
			BBP_NEW_FORUM_ACTION		=> false,
			BBP_NEW_REPLY_ACTION		=> false,
			BBP_NEW_TOPIC_ACTION		=> false
	), $actions_enabled );

}
add_filter( 'broo_default_actions_enabled', 'broo_default_bbp_actions_enabled', 10, 1 );


/**
 * Whenever a user creates a new forum
 * 
 * @param unknown $params
 */
function broo_bbp_new_forum( $params = array() ) {
	
	/* 
	 * array(
	 * 		'forum_id'           => $forum_id,
	 * 		'post_parent'        => $forum_parent_id,
	 *		'forum_author'       => $forum_author,
	 *		'last_topic_id'      => 0,
	 *		'last_reply_id'      => 0,
	 *		'last_active_id'     => 0,
	 *		'last_active_time'   => 0,
	 *		'last_active_status' => bbp_get_public_status_id()
	 * )
	 */

	$user_id = $params['forum_author'];

	Badgearoo::instance()->api->add_user_action( BBP_NEW_FORUM_ACTION, $user_id );
}

/**
 * Whenever a user replies to a forum topic
 * 
 * @param unknown $reply_id
 * @param unknown $topic_id
 * @param unknown $forum_id
 * @param unknown $anonymous_data
 * @param unknown $reply_author
 * @param unknown $is_edit
 * @param unknown $reply_to
 */
function broo_bbp_new_reply( $reply_id, $topic_id, $forum_id, $anonymous_data, $reply_author, $is_edit, $reply_to ) {

	$user_id = $reply_author;
	
	if ( $user_id != 0 ) {
		Badgearoo::instance()->api->add_user_action( BBP_NEW_REPLY_ACTION, $user_id );
	}
}


/**
 * Whenever a user creates a new topic
 * 
 * @param unknown $topic_id
 * @param unknown $forum_id
 * @param unknown $anonymous_data
 * @param unknown $topic_author
 */
function broo_bbp_new_topic( $topic_id = 0, $forum_id = 0, $anonymous_data = false, $topic_author = 0 ) {
	
	if ( $topic_author != 0 ) {
		Badgearoo::instance()->api->add_user_action( BBP_NEW_TOPIC_ACTION, $topic_author );
	}
	
}

/**
 * Whenever a user closes a topic
 * 
 * @param unknown $topic_id
 */
function broo_bbp_closed_topic( $topic_id ) {
	
	$user_id = get_current_user_id();
	
	if ( $user_id != 0 ) {
		Badgearoo::instance()->api->add_user_action( BBP_CLOSED_TOPIC_ACTION, $user_id );
	}
	
}