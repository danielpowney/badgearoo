<?php 
/**
 * BuddyPress actions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

define ( 'BP_ACTIVITY_COMMENT_POSTED_ACTION', 'bp_activity_comment_posted' ); // works
define ( 'BP_ACTIVITY_ADD_USER_FAVORITE_ACTION', 'bp_activity_add_user_favorite' ); // works
define ( 'BP_ACTIVITY_POST_TYPE_PUBLISHED_ACTION', 'bp_activity_post_type_published' );
define ( 'BP_FRIENDS_FRIENDSHIP_ACCEPTED_ACTION', 'friends_friendship_accepted' ); // works
define ( 'BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION', 'friends_friendship_requested' ); // works
define ( 'BP_GROUPS_CREATE_GROUP_ACTION', 'groups_create_group' ); // works - may rename
define ( 'BP_GROUPS_JOIN_GROUP_ACTION', 'groups_join_group' ); // workds
// TODO Add the following actions
// * Invite Someone to Join a Group
// * Get Promoted to Group Moderator/Administrator
// * Promote another Group Member to Group Moderator/Administrator
// * Activated Account
// * Change Profile Avatar
// * Update Profile information
// * Send/reply to a Private Message

// TODO custom BuddyPress wp-admin settings
// * select which assignments can be displayed on user profiles and Activity Streams
// * enable adding assignments to activity streams

// Add an assignments tab

// TODO member e-mail settings to receieve e-mail notifications

// do_action( 'bp_activity_comment_posted', $comment_id, $r, $activity );
// do_action( 'bp_activity_add_user_favorite', $activity_id, $user_id );
// do_action( 'bp_activity_post_type_published', $activity_id, $post, $activity_args );
// do_action( 'friends_friendship_' . $action, $friendship->id, $friendship->initiator_user_id, $friendship->friend_user_id, $friendship );
// do_action( 'groups_create_group', $group->id, $member, $group );
// do_action( 'groups_join_group', $group_id, $user_id ); and groups_accept_invite

function broo_init_bp_actions( $broo_actions ) {
	
	$broo_actions[BP_ACTIVITY_COMMENT_POSTED_ACTION] = array(
			'description' => __( 'Comment on an activity.', 'badgearoo' ),
			'source' =>	__( 'BuddyPress', 'badgearoo' )
	);
	
	$broo_actions[BP_ACTIVITY_ADD_USER_FAVORITE_ACTION] = array(
			'description' => __( 'Add favorite.', 'badgearoo' ),
			'source' =>	__( 'BuddyPress', 'badgearoo' )
	);
	
	$broo_actions[BP_ACTIVITY_POST_TYPE_PUBLISHED_ACTION] = array(
			'description' => __( 'Post activity.', 'badgearoo' ),
			'source' =>	__( 'BuddyPress', 'badgearoo' )
	);
	
	$broo_actions[BP_FRIENDS_FRIENDSHIP_ACCEPTED_ACTION] = array(
			'description' => __( 'Accept a friend request.', 'badgearoo' ),
			'source' =>	__( 'BuddyPress', 'badgearoo' )
	);
	
	$broo_actions[BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION] = array(
			'description' => __( 'Request a friend.', 'badgearoo' ),
			'source' =>	__( 'BuddyPress', 'badgearoo' )
	);
	
	$broo_actions[BP_GROUPS_CREATE_GROUP_ACTION] = array(
			'description' => __( 'Create Group.', 'badgearoo' ),
			'source' =>	__( 'BuddyPress', 'badgearoo' )
	);
	
	$broo_actions[BP_GROUPS_JOIN_GROUP_ACTION] = array(
			'description' => __( 'Join Group.', 'badgearoo' ),
			'source' =>	__( 'BuddyPress', 'badgearoo' )
	);
	
	return $broo_actions;
}
add_filter( 'broo_init_actions', 'broo_init_bp_actions', 10, 1 );


/**
 * Adds WP Core actions
 *
 * @param actions
 */
function broo_add_bp_actions( $actions = array() ) {

	if ( isset( $actions[BP_ACTIVITY_COMMENT_POSTED_ACTION] ) && $actions[BP_ACTIVITY_COMMENT_POSTED_ACTION]['enabled'] == true ) {
		add_action( 'bp_activity_comment_posted', 'broo_bp_activity_comment_posted', 10, 3 );
		add_filter( 'broo_condition_step_check_bp_activity_comment_posted', 'broo_condition_step_check_bp_action_count', 10, 4 );
	}
	
	if ( isset( $actions[BP_ACTIVITY_ADD_USER_FAVORITE_ACTION] ) && $actions[BP_ACTIVITY_ADD_USER_FAVORITE_ACTION]['enabled'] == true ) {
		add_action( 'bp_activity_add_user_favorite', 'broo_bp_activity_add_user_favorite', 10, 2 );
		add_filter( 'broo_condition_step_check_bp_activity_add_user_favorite', 'broo_condition_step_check_bp_action_count', 10, 4 );
	}
	
	if ( isset( $actions[BP_ACTIVITY_POST_TYPE_PUBLISHED_ACTION] ) && $actions[BP_ACTIVITY_POST_TYPE_PUBLISHED_ACTION]['enabled'] == true ) {
		add_action( 'bp_activity_post_type_published', 'broo_bp_activity_post_type_published', 10, 2 );
		add_filter( 'broo_condition_step_check_bp_activity_post_type_published', 'broo_condition_step_check_bp_action_count', 10, 4 );
	}
	
	if ( isset( $actions[BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION] ) && $actions[BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION]['enabled'] == true ) {
		add_action( 'friends_friendship_requested', 'broo_friends_friendship_requested', 10, 4 );
		add_filter( 'broo_condition_step_check_friends_friendship_requested', 'broo_condition_step_check_bp_action_count', 10, 4 );
	}
	
	if ( isset( $actions[BP_FRIENDS_FRIENDSHIP_ACCEPTED_ACTION] ) && $actions[BP_FRIENDS_FRIENDSHIP_ACCEPTED_ACTION]['enabled'] == true ) {
		add_action( 'friends_friendship_accepted',  'broo_friends_friendship_accepted', 10, 4 );
		add_filter( 'broo_condition_step_check_friends_friendship_accepted', 'broo_condition_step_check_bp_action_count', 10, 4 );
	}
	
	if ( isset( $actions[BP_GROUPS_CREATE_GROUP_ACTION] ) && $actions[BP_GROUPS_CREATE_GROUP_ACTION]['enabled'] == true ) {
		add_action( 'groups_create_group', 'broo_groups_create_group', 10, 3 );
		add_filter( 'broo_condition_step_check_groups_create_group', 'broo_condition_step_check_bp_action_count', 10, 4 );
	}
	
	if ( isset( $actions[BP_GROUPS_JOIN_GROUP_ACTION] ) && $actions[BP_GROUPS_JOIN_GROUP_ACTION]['enabled'] == true ) {
		add_action( 'groups_join_group',  'broo_groups_join_group', 10, 2 );
		add_action( 'groups_accept_invite',  'broo_groups_join_group', 10, 2 );
		add_filter( 'broo_condition_step_check_groups_join_group', 'broo_condition_step_check_bp_action_count', 10, 4 );
	}

	add_filter('broo_step_meta_count_enabled', 'broo_step_meta_count_enabled_bp', 10, 2 );
	//add_filter('broo_step_meta_bp_activity_type_enabled', 'broo_step_meta_bp_activity_type_enabled', 10, 2 );

}
add_action( 'broo_init_actions_complete', 'broo_add_bp_actions' );



/**
 * Checks count for the BuddyPress actions action
 *
 * @param unknown $step_result
 * @param unknown $step
 * @param int $user_id
 * @param string $action_name
 * @return boolean
 */
function broo_condition_step_check_bp_action_count( $step_result, $step, $user_id, $action_name ) {

	if ( $step_result == false ) { // no need to continue
		return $step_result;
	}
	
	$meta_count = Badgearoo::instance()->api->get_step_meta_value( $step->step_id, 'count' );	

	global $wpdb;
	$query = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . BROO_USER_ACTION_TABLE_NAME
			. ' ua WHERE ua.action_name = "' . esc_sql( $action_name ) . '" AND user_id = ' . intval( $user_id );

	$db_count = $wpdb->get_var( $query );
	
	if ( intval( $db_count ) < intval( $meta_count ) ) {
		return false;
	}

	return $step_result;
}

/**
 * Activity Comment Posted
 *
 * @param unknown $new_status
 * @param unknown $old_status
 * @param unknown $post
 */
function broo_bp_activity_comment_posted( $comment_id, $r, $activity ) {
	
	$user_id = $activity->user_id;
		
	Badgearoo::instance()->api->add_user_action( BP_ACTIVITY_COMMENT_POSTED_ACTION, $user_id, array(
			'activity_id' => $activity->id,
			'activity_type' => $activity->type,
	) );
}


/**
 * Activity Add User Favorite
 * 
 * @param unknown $activity_id
 * @param unknown $user_id
 */
function broo_bp_activity_add_user_favorite( $activity_id, $user_id ) {
	Badgearoo::instance()->api->add_user_action( BP_ACTIVITY_ADD_USER_FAVORITE_ACTION, $user_id, array(
			'activity_id' => $activity_id
	) );
}


/**
 * 
 * @param unknown $activity_id
 * @param unknown $post
 * @param unknown $activity_args
 */
function broo_bp_activity_post_type_published( $activity_id, $post, $activity_args ) {
	
	Badgearoo::instance()->api->add_user_action( BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION, $activity_args['user_id'], array() );
	
}


/**
 * 
 * @param unknown $friendship_id
 * @param unknown $initiator_user_id
 * @param unknown $friend_user_id
 * @param unknown $friendship
 */
function broo_friends_friendship_accepted( $friendship_id, $initiator_user_id, $friend_user_id, $friendship ) {
	
	Badgearoo::instance()->api->add_user_action( BP_FRIENDS_FRIENDSHIP_ACCEPTED_ACTION, $initiator_user_id, array() );
	
}


/**
 * 
 * @param unknown $friendship_id
 * @param unknown $initiator_user_id
 * @param unknown $friend_user_id
 * @param unknown $friendship
 */
function broo_friends_friendship_requested( $friendship_id, $initiator_user_id, $friend_user_id, $friendship ) {
	
	Badgearoo::instance()->api->add_user_action( BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION, $initiator_user_id, array() );
	
}


/**
 * 
 * @param unknown $group_id
 * @param unknown $member
 * @param unknown $group
 */
function broo_groups_create_group( $group_id, $member, $group ) {
	
	$user_id = $member->user_id;

	Badgearoo::instance()->api->add_user_action( BP_GROUPS_CREATE_GROUP_ACTION, $user_id, array(
			'group_id' => $group_id
	) );
}

/**
 * 
 * @param unknown $group_id
 * @param unknown $user_id
 */
function broo_groups_join_group( $group_id, $user_id ) {
	
	Badgearoo::instance()->api->add_user_action( BP_GROUPS_JOIN_GROUP_ACTION, $user_id, array(
			'group_id' => $group_id
	) );
}


/**
 * Defaults actions enabled
 *
 * @param array $actions_enabled
 * @return $actions_enabled:
 */
function broo_default_bp_actions_enabled( $actions_enabled ) {
	
	return array_merge( array(
			BP_ACTIVITY_COMMENT_POSTED_ACTION			=> true,
			BP_ACTIVITY_ADD_USER_FAVORITE_ACTION		=> true,
			BP_ACTIVITY_POST_TYPE_PUBLISHED_ACTION		=> true,
			BP_FRIENDS_FRIENDSHIP_ACCEPTED_ACTION		=> true,
			BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION		=> true,
			BP_GROUPS_CREATE_GROUP_ACTION				=> true,
			BP_GROUPS_JOIN_GROUP_ACTION					=> true
	), $actions_enabled );

}
add_filter( 'broo_default_actions_enabled', 'broo_default_bp_actions_enabled', 10, 1 );


/**
 * Displays badges before BuddPress member header meta
 */
function broo_bp_before_member_header_meta() {
	
	$user_id = bp_displayed_user_id();
	
	$points = Badgearoo::instance()->api->get_user_points( $user_id );
	$badges = Badgearoo::instance()->api->get_user_badges( $user_id );
	
	// count badges by id
	$badge_count_lookup = array();
	foreach ( $badges as $index => $badge ) {
		if ( ! isset( $badge_count_lookup[$badge->id] ) ) {
			$badge_count_lookup[$badge->id] = 1;
		} else {
			$badge_count_lookup[$badge->id]++;
			unset( $badges[$index] );
		}
	}
	
	$general_settings = (array) get_option( 'broo_general_settings' );
	
	broo_get_template_part( 'user-badges-summary', null, true, array(
			'badge_theme' => $general_settings['broo_badge_theme'],
			'badges' => $badges,
			'points' => $points,
			'badge_count_lookup' => $badge_count_lookup,
			'enable_badge_permalink' => $general_settings['broo_enable_badge_permalink']
	) );
	
}


/**
 * Shows a points step meta
 *
 * @param unknown $step_id
 * @param unknown $action
 */
function broo_step_meta_bp_activity_type( $step_id, $action  ) {

	$step_meta_enabled = apply_filters( 'broo_step_meta_bp_activity_type_enabled', false, $action );

	if ( $step_meta_enabled ) {
		$value = Badgearoo::instance()->api->get_step_meta_value( $step_id, 'activity_type' );
		?>
		<span class="step-meta-value">
			<label for="activity_type"><?php _e( 'Activity Type', 'badgearoo' ); ?></label>
			<select name="activity_type">
				<option value="" <?php selected( ! $value ); ?>><?php _e( 'All activity types', 'badgearoo' ); ?></option>
				<option value="activity_comment"><?php _e( 'Replied to a status update', 'badgearoo' ); ?></option>
				<option value="activity_update"><?php _e( 'Posted a status update', 'badgearoo' ); ?></option>
			</select>
		</span>
		<?php
	}
}
//add_action( 'broo_step_meta', 'broo_step_meta_bp_activity_type', 10, 2 );

/**
 * Sets whether step meta post type is enabled for BuddyPress activity types
 *
 * @param unknown $enabled
 * @param unknown $action
 * @return boolean|unknown
 */
function broo_step_meta_bp_activity_type_enabled( $enabled, $action ) {

	if ( $action == BP_ACTIVITY_COMMENT_POSTED_ACTION ) {
		return true;
	}

	return $enabled;
}

function broo_step_meta_count_enabled_bp( $enabled, $action ) {

	if ( $action == BP_ACTIVITY_COMMENT_POSTED_ACTION || $action == BP_ACTIVITY_ADD_USER_FAVORITE_ACTION
			|| $action == BP_ACTIVITY_POST_TYPE_PUBLISHED_ACTION || $action == BP_FRIENDS_FRIENDSHIP_ACCEPTED_ACTION
			|| $action == BP_FRIENDS_FRIENDSHIP_REQUESTED_ACTION || $action == BP_GROUPS_CREATE_GROUP_ACTION
			|| $action == BP_GROUPS_JOIN_GROUP_ACTION ) {
		return true;
	}

	return $enabled;
}

/**
 * Adds assignments BuddyPress tab
 */
function broo_bp_add_assignments_tab() {
	global $bp;
	bp_core_new_nav_item(
			array(
					'name'                => __( 'Assignments', 'buddypress' ),
					'slug'                => 'broo-bp-assignments',
					'position'            => 75,
					'screen_function'     => 'broo_bp_assignments_tab',
					'default_subnav_slug' => 'broo-bp-assignments',
					'parent_url'          => $bp->loggedin_user->domain . $bp->slug . '/',
					'parent_slug'         => $bp->slug
			) );
}
function broo_bp_assignments_tab() {
	//add title and content here - last is to call the members plugin.php template
	add_action( 'bp_template_title', 'broo_bp_assignments_tab_title' );
	add_action( 'bp_template_content', 'broo_bp_assignments_tab_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}
function broo_bp_assignments_tab_title() {
	//_e( 'Assignments', 'badgearoo' );
}
function broo_bp_assignments_tab_content() {
	
	global $bp;
	$user_id = $bp->displayed_user->id;
	
	if ( $user_id != 0 ) {
	
		$points = Badgearoo::instance()->api->get_user_points( $user_id );
		$badges = Badgearoo::instance()->api->get_user_badges( $user_id );
		
		// count badges by id
		foreach ( $badges as $index => $badge ) {
			if ( ! isset( $badge_count_lookup[$badge->id] ) ) {
				$badge_count_lookup[$badge->id] = 1;
			} else {
				$badge_count_lookup[$badge->id]++;
				unset( $badges[$index] );
			}
		}
		
		$general_settings = (array) get_option( 'broo_general_settings' );
		
		broo_get_template_part( 'user-badges-summary', null, true, array(
				'badge_theme' => $general_settings['broo_badge_theme'],
				'badges' => $badges,
				'points' => $points,
				'badge_count_lookup' => $badge_count_lookup,
				'enable_badge_permalink' => $general_settings['broo_enable_badge_permalink']
		) );
	} else {
		_e( 'Invalid member profile.', 'badgearoo' );
	}

}


/**
 * Adds user assignments to Buddypress member activity stream
 * 
 * @param unknown $assignment_id
 * @param unknown $condition_id
 * @param unknown $user_id
 * @param unknown $type
 * @param unknown $value
 * @param unknown $created_dt
 * @param unknown $status
 */
function broo_bp_add_activity( $assignment_id, $condition_id, $user_id, $type, $value, $created_dt, $status ) {
	
	if ( $status == 'approved' ) {
		
		$user = get_user_by( 'id', $user_id );
		$user_permalink = apply_filters( 'broo_user_permalink', get_author_posts_url( $user_id ), $user_id );
		
		$activity_action = '';
		$activity_content = '';
		
		if ( $type == 'badge' ) {
			
			$badge = Badgearoo::instance()->api->get_badge( $value );
			
			$activity_action = sprintf( __( '<a href="%s">%s</a> has been assigned badge %s.', 'badgearoo' ), $user_permalink, $user->display_name, $badge->title );
			
			$general_settings = (array) get_option( 'broo_general_settings' );
			
			ob_start();
			broo_get_template_part( 'badge', null, true, array(
					'badge_id' => $badge->id,
					'show_title' => true,
					'badge_theme' => $general_settings['broo_badge_theme'],
					'badge_icon' => $badge->badge_icon,
					'badge_html' => $badge->badge_html,
					'badge_color' => $badge->badge_color,
					'excerpt' => $badge->excerpt,
					'title' => $badge->title,
					'content' => $badge->content,
					'enable_badge_permalink' => $general_settings['broo_enable_badge_permalink']
			) );
			$activity_content .= ob_get_contents();
			ob_end_clean();
			
			$activity_content = str_replace( array( "\r", "\n" ), '', $activity_content );
			
		} else {
			$activity_action = sprintf( __( '<a href="%s">%s</a> has been assigned %d points.', 'badgearoo' ), $user_permalink, $user->display_name, $value );
		}
		
		$activity_id = bp_activity_add( array(
				'id' => null, // not updating
				'action' => $activity_action,
				'content' => $activity_content,
				'component' => 'broo',
				'type' => $type,
				'primary_link' => null,
				'user_id' => $user_id,
				'item_id' => $assignment_id,
				'secondary_item_id' => null, // if badge, points null,
				
		) );
	}
}

/**
 * Allows activity content tags
 * 
 * @param unknown $activity_allowedtags
 * @return multitype:
 */
function broo_bp_activity_allowed_tags( $activity_allowedtags ) {
	
	$activity_allowedtags['span']          = array();
    $activity_allowedtags['span']['class'] = array();
    $activity_allowedtags['span']['style'] = array();
    $activity_allowedtags['div']           = array();
    $activity_allowedtags['div']['class']  = array();
    $activity_allowedtags['div']['id']     = array();
    $activity_allowedtags['div']['style'] = array();
	
	return $activity_allowedtags;
}


/**
 * Regiters BuddyPress badge and points assigment actions
 */
function broo_bp_register_activity_actions() {
    bp_activity_set_action( 'broo', 'badge', __( 'Badge assignment', 'broo' ), false, __( 'Badges', 'broo' ), array( 'member' ), 0 );
    bp_activity_set_action( 'broo', 'points', __( 'Points assignment', 'broo' ), false, __( 'Points', 'broo' ), array( 'member' ), 0 );
    
}

function broo_bp_init() {
	if ( class_exists( 'BuddyPress' ) ) {
		
		$broo_bp_settings = (array) get_option( 'broo_bp_settings' );
		
		add_filter( 'broo_can_show_user_badges_widget', 'broo_bp_can_show_user_badges_widget', 10, 2 );
		add_filter( 'broo_user_badges_user_id', 'broo_bp_user_badges_user_id', 10, 2);
		add_filter( 'bp_activity_allowed_tags', 'broo_bp_activity_allowed_tags' );
		
		add_action( 'bp_register_activity_actions', 'broo_bp_register_activity_actions' );
		
		if ( $broo_bp_settings['broo_bp_assignments_activity_stream'] ) {
			add_action( 'broo_add_user_assignment', 'broo_bp_add_activity', 10, 7 );
		}
		
		if ( $broo_bp_settings['broo_bp_assignment_summary_placement'] == 'tab' ) {
			add_action( 'bp_setup_nav', 'broo_bp_add_assignments_tab', 50 );
		}
		
		if ( $broo_bp_settings['broo_bp_assignment_summary_placement'] == 'header' ) {
			add_action( 'bp_before_member_header_meta', 'broo_bp_before_member_header_meta' );
		}
		
		if ( is_admin() ) {
			add_filter( 'broo_user_permalinks_options', 'broo_bbp_user_permalinks_options', 10, 1 );
		}
	}
}
add_action( 'init', 'broo_bp_init' );

/**
 * Checks whether user badges widget can be shown
 * 
 * @param unknown $can_show_user_badges_widget
 * @param unknown $post_id
 * @return unknown
 */
function broo_bp_can_show_user_badges_widget( $can_show_user_badges_widget, $post_id ) {
	
	if ( is_buddypress() ) {
		return ( ( bp_is_user() || bp_is_members_component() ) && bp_displayed_user_id() != 0 );
	}
	
	return $can_show_user_badges_widget;
}

/**
 * Sets BuddyPress user id for user badges widget
 * 
 * @param unknown $user_id
 * @param unknown $post_id
 * @return unknown
 */
function broo_bp_user_badges_user_id( $user_id, $post_id ) {
	
	if ( is_buddypress() && bp_displayed_user_id() != 0 ) {	
		return bp_displayed_user_id();
	}
	
	return $user_id;
	
}

function broo_bbp_user_permalinks_options( $user_permalinks_options = array() ) {

	if ( ! is_array( $user_permalinks_options ) ) {
		$user_permalinks_options = array();
	}

	$user_permalinks_options['bp_core_get_userlink'] = __( 'BuddyPress User Link', 'badgearoo' );

	return $user_permalinks_options;
}