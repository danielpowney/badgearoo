<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 
 * @param unknown $atts
 * @return string
 */
function broo_user_badges( $atts) {
	
	extract( shortcode_atts( array(
			'user_id' => 0,
			'username' => ''
	), $atts ) );
	
	if ( $user_id == 0 && strlen( $username ) > 0 ) {
		$user = get_user_by( 'login', $username );
		$user_id = $user->ID;
	} else if ( $user_id == 0 ) {
		global $authordata;
		$user_id = isset( $authordata->ID ) ? $authordata->ID : 0;
	}
	
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
	
	$html = '';
	
	ob_start();
	broo_get_template_part( 'user-badges', null, true, array(
			'badges' => $badges,
			'badge_theme' => $general_settings['broo_badge_theme'],
			'badge_count_lookup' => $badge_count_lookup,
			'enable_badge_permalink' => $general_settings['broo_enable_badge_permalink']
	) );
	$html .= ob_get_contents();
	ob_end_clean();
	
	return $html;
}
add_shortcode( 'broo_user_badges', 'broo_user_badges' );



function broo_user_points( $atts) {

	extract( shortcode_atts( array(
			'user_id' => 0,
			'username' => ''
	), $atts ) );

	if ( $user_id == 0 && strlen( $username ) > 0 ) {
		$user = get_user_by( 'login', $username );
		$user_id = $user->ID;
	} else if ( $user_id == 0 ) {
		global $authordata;
		$user_id = isset( $authordata->ID ) ? $authordata->ID : 0;
	}

	$points = Badgearoo::instance()->api->get_user_points( $user_id );

	$html = '';
	ob_start();
	broo_get_template_part( 'points', null, true, array(
			'points' => $points
	) );
	$html .= ob_get_contents();
	ob_end_clean();
	
	return $html;
}
add_shortcode( 'broo_user_points', 'broo_user_points' );



/**
 * Displays the user leaderboard for badges or points earned
 * 
 * @param unknown $atts
 * @return string
 */
function broo_leaderboard( $atts) {

	extract( shortcode_atts( array(
			'show_avatar' => true,
			'before_name' => '',
			'after_name' => '',
			'show_badges' => true,
			'show_points' => true,
			'sort_by' => 'points',
			'show_filters' => true,
			'from_date' => null,
			'to_date' => null,
			'limit' => null,
			'offset' => 0,
			'include_no_assignments' => false
	), $atts ) );
	
	if ( is_string( $show_avatar ) ) {
		$show_avatar = $show_avatar == 'true' ? true : false;
	}
	if ( is_string( $show_points ) ) {
		$show_points = $show_points == 'true' ? true : false;
	}
	if ( is_string( $show_badges ) ) {
		$show_badges = $show_badges == 'true' ? true : false;
	}
	if ( is_string( $show_filters ) ) {
		$show_filters = $show_filters == 'true' ? true : false;
	}
	if ( is_string( $include_no_assignments ) ) {
		$include_no_assignments = $include_no_assignments == 'true' ? true : false;
	}
	
	if ( $sort_by != 'badges' && $sort_by != 'points' ) {
		$sort_by = null;
	}
	
	if ( $from_date != null && strlen( $from_date ) > 0 ) {
		list( $year, $month, $day ) = explode( '-', $from_date ); // default yyyy-mm-dd format
		if ( ! checkdate( $month , $day , $year ) ) {
			$from_date = null;
		}
	}
	
	if ( $to_date != null && strlen($to_date) > 0 ) {
		list( $year, $month, $day ) = explode( '-', $to_date ); // default yyyy-mm-dd format
		if ( ! checkdate( $month , $day , $year ) ) {
			$to_date = null;
		}
	}
	
	$limit = isset( $limit ) && is_numeric( $limit ) && intval( $limit ) >= 0 ? intval( $limit ) : null;
	$offset = isset( $offset ) && is_numeric( $offset ) && intval( $offset ) >= 0 ? intval( $offset ) : 0;
		
	global $wpdb;
	
	$user_rows = broo_get_user_leaderboard( array(
			'sort_by' => $sort_by,
			'from_date' => $from_date,
			'to_date' => $to_date,
			'limit' => $limit,
			'offset' => $offset,
			'include_no_assignments' => $include_no_assignments
	) );
	
	$html = '';

	ob_start();
	broo_get_template_part( 'user-leaderboard', null, true, array(
			'user_rows'=> $user_rows,
			'show_avatar' => $show_avatar,
			'before_name' => $before_name,
			'after_name' => $after_name,
			'show_badges' => $show_badges,
			'show_points' => $show_points,
			'sort_by' => $sort_by,
			'show_filters' => $show_filters,
			'from_date' => $from_date,
			'to_date' => $to_date,
			'limit' => $limit,
			'offset' => $offset,
			'include_no_assignments' => $include_no_assignments
	) );
	$html .= ob_get_contents();
	ob_end_clean();

	return $html;
}
add_shortcode( 'broo_leaderboard', 'broo_leaderboard' );






/**
 * Displays a summary of a badge
 * 
 * @param unknown $atts
 * @return unknown
 */
function broo_badge( $atts ) {

	extract( shortcode_atts( array(
			'badge_id' => null,
			'show_description' => true,
			'before_name' => '',
			'after_name' => '',
			'show_users' => true,
			'show_users_count' => true
	), $atts ) );

	if ( $badge_id == null) {
		return __( 'Badge not found.', 'badgearoo' );;
	}
	
	if ( is_string( $show_description ) ) {
		$show_description = $show_description == 'true' ? true : false;
	}
	if ( is_string( $show_users ) ) {
		$show_users = $show_users == 'true' ? true : false;
	}
	if ( is_string( $show_users_count ) ) {
		$show_users_count = $show_users_count == 'true' ? true : false;
	}
	
	$badge = Badgearoo::instance()->api->get_badge( $badge_id, ( $show_users || $show_users_count ) );
	
	if ( $badge == null) {
		return __( 'Badge not found.', 'badgearoo' );
	}

	$users = array();
	foreach ( $badge->users as $user_id ) {

		$user = get_userdata( intval( $user_id ) );
		
		if ( $user ) {
			array_push( $users, $user );
		}
	}
	
	$general_settings = (array) get_option( 'broo_general_settings' );
	
	$html = '';

	ob_start();
	broo_get_template_part( 'badge', 'summary', true, array(
			'badge_theme' => $general_settings['broo_badge_theme'],
			'badge_icon' => $badge->badge_icon,
			'badge_html' => $badge->badge_html,
			'badge_color' => $badge->badge_color,
			'title' => $badge->title,
			'content'=> $badge->content,
			'excerpt'=> $badge->excerpt,
			'users' => $users,
			'users_count' => count( $badge->users ),
			'show_users' => $show_users,
			'show_users_count' => $show_users_count,
			'show_description' => $show_description,
			'enable_badge_permalink' => $general_settings['broo_enable_badge_permalink']
			
	) );
	$html .= ob_get_contents();
	ob_end_clean();

	return $html;
}
add_shortcode( 'broo_badge', 'broo_badge' );



/**
 * Displays a list of badges
 *
 * @param unknown $atts
 * @return unknown
 */
function broo_badge_list( $atts ) {

	extract( shortcode_atts( array(
			'badge_ids' => null,
			'show_description' => true,
			'before_name' => '',
			'after_name' => '',
			'show_users' => true,
			'show_users_count' => true,
			'layout' => 'table',
			'taxonomy' => null,
			'terms' => null,
			'tax_operator' => 'IN'
	), $atts ) );

	if ( is_string( $show_description ) ) {
		$show_description = $show_description == 'true' ? true : false;
	}
	if ( is_string( $show_users ) ) {
		$show_users = $show_users == 'true' ? true : false;
	}
	if ( is_string( $show_users_count ) ) {
		$show_users_count = $show_users_count == 'true' ? true : false;
	}
	
	if ( isset( $badge_ids ) && strlen( trim( $badge_ids ) ) > 0 ) {
		$badge_ids = explode( ',', $badge_ids );
	} else {
		$badge_ids = null;
	}
	
	$tax_query = array();
	if ( isset( $taxonomy ) && is_string( $taxonomy ) ) {
		$tax_query = array( array(
					'taxonomy' => $taxonomy,
					'field' => 'slug',
					'operator' => $tax_operator,
					'terms' => isset( $terms ) && strlen( trim( $terms ) ) > 0 ? explode( ',', $terms ) : wp_list_pluck( get_terms( 'category' ), 'slug' )
		) );
	}
	
	$badges =  Badgearoo::instance()->api->get_badges( array( 
			'badge_ids' => $badge_ids, 
			'tax_query' => $tax_query ), 
			( $show_users || $show_users_count ) 
	);

	$general_settings = (array) get_option( 'broo_general_settings' );
	
	$html = '';

	ob_start();
	broo_get_template_part( 'badge', 'list', true, array(
			'layout' => $layout,
			'badges' => $badges,
			'show_users' => $show_users,
			'show_users_count' => $show_users_count,
			'show_description' => $show_description,
			'enable_badge_permalink' => $general_settings['broo_enable_badge_permalink'],
			'badge_theme' => $general_settings['broo_badge_theme']
	) );
	$html .= ob_get_contents();
	ob_end_clean();

	return $html;
}
add_shortcode( 'broo_badge_list', 'broo_badge_list' );



/**
 * Displays a condition including steps, badges and points
 * 
 * @param unknown $atts
 * @return unknown
 */
function broo_condition( $atts ) {

	extract( shortcode_atts( array(
				'condition_id' => null,
				'show_steps' => true,
				'show_badges' => true,
				'show_points' => true
	), $atts ) );

	if ( $condition_id == null) {
		return __( 'Condition not found.', 'badgearoo' );
	}
	
	if ( is_string( $show_steps ) ) {
		$show_steps = $show_steps == 'true' ? true : false;
	}
	if ( is_string( $show_badges ) ) {
		$show_badges = $show_badges == 'true' ? true : false;
	}
	if ( is_string( $show_points ) ) {
		$show_points = $show_points == 'true' ? true : false;
	}

	$condition = Badgearoo::instance()->api->get_condition( $condition_id );

	if ( $condition == null) {
		return __( 'Condition not found.', 'badgearoo' );
	}
	
	$badges = array();
	if ( $show_badges ) {
		foreach ( $condition->badges as $badge_id ) {
			$badge = Badgearoo::instance()->api->get_badge( $badge_id );
			
			if ( $badge ) {
				array_push( $badges, $badge );
			}
		}
	}
	
	$general_settings = (array) get_option( 'broo_general_settings' );

	$html = '';

	ob_start();
	broo_get_template_part( 'condition', null, true, array(
			'badge_theme' => $general_settings['broo_badge_theme'],
			'name' => $condition->name,
			'steps' => $condition->steps,
			'badges' => $badges,
			'points' => $condition->points,
			'show_steps' => $show_steps,
			'show_badges' => $show_badges,
			'show_points' => $show_points,
			'enabled' => $condition->enabled,
			'enable_badge_permalink' => $general_settings['broo_enable_badge_permalink']
	) );
	$html .= ob_get_contents();
	ob_end_clean();

	return $html;
}
add_shortcode( 'broo_condition', 'broo_condition' );



/**
 * Filters the user leaderboard
 */
function broo_user_leaderboard_filter() {
	
	$ajax_nonce = $_POST['nonce'];
	if ( wp_verify_nonce($ajax_nonce, Badgearoo::ID.'-nonce' ) ) {
		
		$filters = array();
		
		$sort_by = isset( $_POST['sort-by'] ) ? $_POST['sort-by'] : null;
		$from_date = isset( $_POST['from-date'] ) ? $_POST['from-date'] : null;
		$to_date = isset( $_POST['to-date'] ) ? $_POST['to-date'] : null;
		$limit = isset( $_POST['limit'] ) ? intval( $_POST['limit'] ) : null;
		$offset = isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : 0;
		
		$show_avatar = isset( $_POST['show_avatar'] ) && $_POST['show_avatar'] == 'true' ? true : false;
		$show_badges = isset( $_POST['show_badges'] ) && $_POST['show_badges'] == 'true' ? true : false;
		$show_points = isset( $_POST['show_points'] ) && $_POST['show_points'] == 'true' ? true : false;
		$before_name = isset( $_POST['before_name'] ) ? $before_name : '';
		$after_name = isset( $_POST['before_name'] ) ? $after_name : '';
		$include_no_assignments = isset( $_POST['include_no_assignments'] ) && $_POST['include_no_assignments'] == 'true' ? true : false;
		
		if ( $sort_by != 'badges' && $sort_by != 'points' ) {
			$sort_by = null;
		}
		
		if ( $from_date != null && strlen( $from_date ) > 0 ) {
			list( $year, $month, $day ) = explode( '-', $from_date ); // default yyyy-mm-dd format
			if ( ! checkdate( $month , $day , $year ) ) {
				$from_date = null;
			}
		}
		
		if ( $to_date != null && strlen($to_date) > 0 ) {
			list( $year, $month, $day ) = explode( '-', $to_date ); // default yyyy-mm-dd format
			if ( ! checkdate( $month , $day , $year ) ) {
				$to_date = null;
			}
		}
		
		$user_rows = broo_get_user_leaderboard( array(
				'sort_by' => $sort_by,
				'from_date' => $from_date,
				'to_date' => $to_date,
				'offset' => $offset,
				'limit' => $limit,
				'include_no_assignments' => $include_no_assignments
		) );
		
		$html = '';
		
		ob_start();
		broo_get_template_part( 'user-leaderboard-table', null, true, array(
				'user_rows'=> $user_rows,
				'show_avatar' => $show_avatar,
				'before_name' => $before_name,
				'after_name' => $after_name,
				'show_badges' => $show_badges,
				'show_points' => $show_points,
				'sort_by' => $sort_by
		) );
		$html .= ob_get_contents();
		ob_end_clean();
		
		$data = array();
		$data['html'] = $html;
		
		echo json_encode( array(
				'status' => 'success',
				'data' => $data
		) );
		
	}
	
	die();
	
}

/**
 * Gets user leaderboard data
 * 
 * @param unknown $filters
 * @return unknown
 */
function broo_get_user_leaderboard( $filters = array() ) {
	
	$sort_by = isset( $filters['sort_by'] ) ? $filters['sort_by'] : 'points';
	$from_date = isset( $filters['from_date'] ) ? $filters['from_date'] : null;
	$to_date = isset( $filters['to_date'] ) ? $filters['to_date'] : null;
	$limit = isset( $filters['limit'] ) && is_numeric( $filters['limit'] ) && intval( $filters['limit'] ) >= 0 ? intval( $filters['limit'] ) : null;
	$offset = isset( $filters['offset'] ) && is_numeric( $filters['offset'] ) && intval( $filters['offset'] ) >= 0 ? intval( $filters['offset'] ) : 0;
	$include_no_assignments = isset( $filters['include_no_assignments'] ) && $filters['include_no_assignments'] == true ? true : false;
	
	global $wpdb;
	
	$order_by = 'points';
	if ( $sort_by == 'badges' ) {
		$order_by = 'count_badges';
	}
	
	if ( ! $include_no_assignments ) { // only shows users who have assignments: badges and points
	
		$query = 'SELECT a.user_id, u.display_name, SUM(CASE WHEN a.type = "badge" THEN 1 ELSE 0 END) AS count_badges, '
				. 'SUM(CASE WHEN a.type = "points" THEN a.value ELSE 0 END) AS points FROM '
				. $wpdb->prefix . BROO_USER_ASSIGNMENT_TABLE_NAME . ' a LEFT JOIN ' . $wpdb->posts . ' p'
				. ' ON ( a.type = "badge" AND a.value = p.ID AND p.post_status = "publish" ) INNER JOIN '
				. $wpdb->users . ' u ON a.user_id = u.ID WHERE ( ( a.type = "badge" AND p.post_status = "publish" )'
				. ' OR ( a.type = "points" ) ) AND a.status = "approved" AND ( NOW() <= expiry_dt OR expiry_dt IS NULL )';
		
		$added_to_query = true;
		if ( $to_date ) {
			if ( $added_to_query ) {
				$query .= ' AND';
			}
		
			$query .= ' created_dt <= "' . esc_sql( $to_date ) . '"';
			$added_to_query = true;
		}
		
		if ( $from_date ) {
			if ( $added_to_query ) {
				$query .= ' AND';
			}
		
			$query .= ' created_dt >= "' . esc_sql( $from_date ) . '"';
			$added_to_query = true;
		}
		
		$query .= ' GROUP BY user_id ORDER BY ' . $order_by . ' DESC';
		
	} else { // show all users, even if they have not earned any badges or points
		
		$query = 'SELECT u.ID as user_id, u.display_name, SUM(CASE WHEN a.type = "badge" THEN 1 ELSE 0 END) AS count_badges, '
				. 'SUM(CASE WHEN a.type = "points" THEN a.value ELSE 0 END) AS points FROM ' . $wpdb->users . ' u'
				. ' LEFT JOIN ' . $wpdb->prefix . BROO_USER_ASSIGNMENT_TABLE_NAME . ' a ON u.ID = a.user_id'
				. ' AND a.status = "approved" AND( NOW() <= a.expiry_dt OR a.expiry_dt IS NULL )';

		$added_to_query = true;
		if ( $to_date ) {
			if ( $added_to_query ) {
				$query .= ' AND';
			}
		
			$query .= ' created_dt <= "' . esc_sql( $to_date ) . '"';
			$added_to_query = true;
		}
		
		if ( $from_date ) {
			if ( $added_to_query ) {
				$query .= ' AND';
			}
		
			$query .= ' created_dt >= "' . esc_sql( $from_date ) . '"';
			$added_to_query = true;
		}
				 
		$query .= ' LEFT JOIN ' . $wpdb->posts . ' p ON ( a.type = "badge" AND a.value = p.ID AND p.post_status = "publish"'
				. ' AND ( ( a.type = "badge" AND p.post_status = "publish" ) OR ( a.type = "points" ) ) )';
		
		$query .= ' GROUP BY u.ID ORDER BY ' . $order_by . ' DESC';
	}
	
	if ( $limit && $limit > 0 ) {
		$query .= ' LIMIT ' . $offset . ', ' . $limit;
	}
	
	$rows = $wpdb->get_results( $query, ARRAY_A );
	
	return $rows;
	
}




/**
 * Displays the user dashboard
 *
 * @param unknown $atts
 * @return string
 */
function broo_user_dashboard($atts) {

	extract( shortcode_atts( array(
			'show_badges' => true,
			'show_points' => true,
			'show_assignments' => true,
			'limit' => 5,
			'offset' => 0,
			'type' => null,
			'show_filters' => true,
			'from_date' => null,
			'to_date' => null
	), $atts ) );
	
	
	if ( is_string( $show_badges ) ) {
		$show_badges = $show_badges == 'true' ? true : false;
	}
	if ( is_string( $show_points ) ) {
		$show_points = $show_points == 'true' ? true : false;
	}
	if ( is_string( $show_assignments ) ) {
		$show_assignments = $show_assignments == 'true' ? true : false;
	}
	if ( is_string( $show_filters ) ) {
		$show_filters = $show_filters == 'true' ? true : false;
	}
	
	$to_date = isset( $_REQUEST['to-date'] ) ? $_REQUEST['to-date'] : $to_date;
	$from_date = isset( $_REQUEST['from-date'] ) ? $_REQUEST['from-date'] : $from_date;
	$type = isset( $_REQUEST['type'] ) ? $_REQUEST['type'] : $type;
	
	if ( $from_date != null && strlen( $from_date ) > 0 ) {
		list( $year, $month, $day ) = explode( '-', $from_date ); // default yyyy-mm-dd format
		if ( ! checkdate( $month , $day , $year ) ) {
			$from_date = null;
		}
	}
		
	if ( $to_date != null && strlen($to_date) > 0 ) {
		list( $year, $month, $day ) = explode( '-', $to_date );// default yyyy-mm-dd format
		if ( ! checkdate( $month , $day , $year ) ) {
			$to_date = null;
		}
	}
	
	$user_id = get_current_user_id();
	
	$assignments = null;
	$count_assignments = null;
	$points = null;
	$badges = array();
	$badge_count_lookup = array();
	
	if ( $user_id != 0 ) {
			
		$assignments = Badgearoo::instance()->api->get_user_assignments( array( 
				'user_id' => $user_id,
				'limit' => $limit, 
				'offset' => $offset,
				'to_date' => $to_date,
				'from_date' => $from_date,
				'type' => $type
		), false );
		
		$count_assignments = Badgearoo::instance()->api->get_user_assignments( array(
				'user_id' => $user_id, 
				'to_date' => $to_date,
				'from_date' => $from_date,
				'type' => $type
		), true );
	
		$points = Badgearoo::instance()->api->get_user_points( $user_id, array( 'to_date' => $to_date, 'from_date' => $from_date ) );
		$badges = Badgearoo::instance()->api->get_user_badges( $user_id, array( 'to_date' => $to_date, 'from_date' => $from_date ) );
		
		// count badges by id
		foreach ( $badges as $index => $badge ) {
			if ( ! isset( $badge_count_lookup[$badge->id] ) ) {
				$badge_count_lookup[$badge->id] = 1;
			} else {
				$badge_count_lookup[$badge->id]++;
				unset( $badges[$index] );
			}
		}
	}
	
	if ( ! is_array( $assignments ) ) {
		$assignments = array();
	}
	
	$offset = $limit; // next offset
	
	// TODO add condition progress

	$html = '';
	
	$general_settings = (array) get_option( 'broo_general_settings' );

	ob_start();
	broo_get_template_part( 'user-dashboard', null, true, array(
			'assignments' => $assignments,
			'points' => $points,
			'badges' => $badges,
			'show_badges' => $show_badges,
			'show_points' => $show_points,
			'show_assignments' => $show_assignments,
			'badge_count_lookup' => $badge_count_lookup,
			'show_filters' => $show_filters,
			'type' => $type,
			'to_date' => $to_date,
			'from_date' => $from_date,
			'limit' => $limit,
			'offset' => $offset,
			'count_assignments' => $count_assignments,
			'badge_theme' => $general_settings['broo_badge_theme'],
			'enable_badge_permalink' => $general_settings['broo_enable_badge_permalink'],
			'user_id' => $user_id
	) );
	$html .= ob_get_contents();
	ob_end_clean();

	return $html;
}
add_shortcode( 'broo_user_dashboard', 'broo_user_dashboard' );




/**
 * Loads more user dashboard assignments
 */
function broo_user_dashboard_assignments_more() {

	$ajax_nonce = $_POST['nonce'];
	if ( wp_verify_nonce($ajax_nonce, Badgearoo::ID.'-nonce' ) ) {

		$from_date = isset( $_POST['from-date'] ) ? $_POST['from-date'] : null;
		$to_date = isset( $_POST['to-date'] ) ? $_POST['to-date'] : null;
		$type = isset( $_POST['type'] ) ? $_POST['type'] : null;
		$limit = isset( $_POST['limit'] ) ? intval( $_POST['limit'] ) : 5;
		$offset = isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : 0;
		$user_id = get_current_user_id();
		
		if ( $user_id == 0 ) {
			echo json_encode( array(
					'status' => 'error'
			) );
			die();
		}

		if ( $from_date != null && strlen( $from_date ) > 0 ) {
			list( $year, $month, $day ) = explode( '-', $from_date ); // default yyyy-mm-dd format
			if ( ! checkdate( $month , $day , $year ) ) {
				$from_date = null;
			}
		}

		if ( $to_date != null && strlen($to_date) > 0 ) {
			list( $year, $month, $day ) = explode( '-', $to_date ); // default yyyy-mm-dd format
			if ( ! checkdate( $month , $day , $year ) ) {
				$to_date = null;
			}
		}

		$assignments = Badgearoo::instance()->api->get_user_assignments( array( 
			'user_id' => $user_id,
			'limit' => $limit, 
			'offset' => $offset,
			'to_date' => $to_date,
			'from_date' => $from_date,
			'type' => $type
		) );
		
		$offset += $limit; // next offset
		
		$general_settings = (array) get_option( 'broo_general_settings' );

		$html = '';

		ob_start();
		
		foreach ( $assignments as $assignment ) {
			broo_get_template_part( 'assignments-table-row', null, true, array(
					'assignment' => $assignment,
					'badge_theme' => $general_settings['broo_badge_theme'],
					'enable_badge_permalink' => $general_settings['broo_enable_badge_permalink']
			) );
		}
		
		$html .= ob_get_contents();
		ob_end_clean();

		$data = array();
		$data['html'] = $html;
		$data['offset'] = $offset;

		echo json_encode( array(
				'status' => 'success',
				'data' => $data
		) );

	}

	die();

}