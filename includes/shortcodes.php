<?php
function ub_user_badges( $atts) {
	
	extract( shortcode_atts( array(
			'user_id' => 0,
			'username' => ''
	), $atts ) );
	
	if ( $user_id == 0 && strlen( $username ) > 0 ) {
		$user = get_user_by( 'login', $username );
		$user_id = $user->ID;
	} else {
		global $authordata;
		$user_id = isset( $authordata->ID ) ? $authordata->ID : 0;
	}
	
	$badges = User_Badges::instance()->api->get_user_badges( $user_id );
	
	$html = '';
	
	if (count( $badges ) > 0 ) {
	
		foreach ( $badges as $badge ) {
				
			ob_start();
			ub_get_template_part( 'badge', null, true, array(
					'url' => $badge->url,
					'description'=> $badge->description
			) );
			$html .= ob_get_contents();
			ob_end_clean();
			
		}
	}
	
	return $html;
}
add_shortcode( 'ub_user_badges', 'ub_user_badges' );

/**
 * Helper to sort user points by most points
 *
 * @param unknown_type $a
 * @param unknown_type $b
 */
function ub_sort_most_points( $a, $b ) {
	return ( $a['points'] > $b['points'] ) ? -1 : 1;
}

/**
 * Helper to sort user points by most points
 *
 * @param unknown_type $a
 * @param unknown_type $b
 */
function ub_badge_count( $a, $b ) {
	return ( count( $a['badges'] ) > count( $b['badges'] ) ) ? -1 : 1;
}




/**
 * Displays the user leaderboard for badges or points earned
 * 
 * @param unknown $atts
 * @return string
 */
function ub_leaderboard( $atts) {

	extract( shortcode_atts( array(
			'show_avatar' => false,
			'before_name' => '',
			'after_name' => '',
			'show_badges' => true,
			'show_points' => true,
			'sort_by' => 'points',
			'show_filters' => true
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
	
	global $wpdb;
	
	$results = $wpdb->get_results( 'SELECT user_id, meta_value AS points FROM ' . $wpdb->usermeta . ' WHERE meta_key = "ub_points"' );
	
	$user_rows = ub_get_user_leaderboard( );
	
	$html = '';

	ob_start();
	ub_get_template_part( 'user-leaderboard', null, true, array(
			'user_rows'=> $user_rows,
			'show_avatar' => $show_avatar,
			'before_name' => $before_name,
			'after_name' => $after_name,
			'show_badges' => $show_badges,
			'show_points' => $show_points,
			'sort_by' => $sort_by,
			'show_filters' => $show_filters
	) );
	$html .= ob_get_contents();
	ob_end_clean();

	return $html;
}
add_shortcode( 'ub_leaderboard', 'ub_leaderboard' );






/**
 * Displays a summary of a badge
 * 
 * @param unknown $atts
 * @return unknown
 */
function ub_badge_summary( $atts ) {

	extract( shortcode_atts( array(
			'badge_id' => null,
			'show_description' => true,
			'before_name' => '',
			'after_name' => '',
			'show_users' => true,
			'show_users_count' => true
	), $atts ) );

	if ( $badge_id == null) {
		return __( 'Badge not found.', 'user_badges' );;
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
	
	$badge = User_Badges::instance()->api->get_badge( $badge_id, ( $show_users || $show_users_count ) );
	
	if ( $badge == null) {
		return __( 'Badge not found.', 'user_badges' );
	}

	$users = array();
	foreach ( $badge->users as $user_id ) {
		$user = get_userdata( intval( $user_id ) );
		
		if ( $user ) {
			array_push( $users, $user );
		}
	}
	
	$html = '';

	ob_start();
	ub_get_template_part( 'badge', 'summary', true, array(
			'name' => $badge->name,
			'url' => $badge->url,
			'description'=> $badge->description,
			'users' => $users,
			'users_count' => count( $badge->users ),
			'show_users' => $show_users,
			'show_users_count' => $show_users_count,
			'show_description' => $show_description
			
	) );
	$html .= ob_get_contents();
	ob_end_clean();

	return $html;
}
add_shortcode( 'ub_badge_summary', 'ub_badge_summary' );



/**
 * Displays a condition including steps, badges and points
 * 
 * @param unknown $atts
 * @return unknown
 */
function ub_condition( $atts ) {

	extract( shortcode_atts( array(
			'condition_id' => null,
			'show_steps' => true,
			'show_badges' => true,
			'show_points' => true
	), $atts ) );

	if ( $condition_id == null) {
		return __( 'Condition not found.', 'user_badges' );
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

	$condition = User_Badges::instance()->api->get_condition( $condition_id );

	if ( $condition == null) {
		return __( 'Condition not found.', 'user_badges' );
	}
	
	$badges = array();
	if ( $show_badges ) {
		foreach ( $condition->badges as $badge_id ) {
			$badge = User_Badges::instance()->api->get_badge( $badge_id );
			
			if ( $badge ) {
				array_push( $badges, $badge );
			}
		}
	}

	$html = '';

	ob_start();
	ub_get_template_part( 'condition', null, true, array(
			'name' => $condition->name,
			'steps' => $condition->steps,
			'badges' => $badges,
			'points' => $condition->points,
			'show_steps' => $show_steps,
			'show_badges' => $show_badges,
			'show_points' => $show_points,
			'enabled' => $condition->enabled
	) );
	$html .= ob_get_contents();
	ob_end_clean();

	return $html;
}
add_shortcode( 'ub_condition', 'ub_condition' );



/**
 * Filters the user leaderboard
 */
function ub_user_leaderboard_filter() {
	
	$ajax_nonce = $_POST['nonce'];
	if ( wp_verify_nonce($ajax_nonce, User_Badges::ID.'-nonce' ) ) {
		
		$filters = array();
		
		$show_avatar = false;
		$before_name = '';
		$after_name = '';
		$show_badges = true;
		$show_points = true;
		
		$sort_by = isset( $_POST['sort-by'] ) ? $_POST['sort-by'] : null;
		$from_date = isset( $_POST['from-date'] ) ? $_POST['from-date'] : null;
		$to_date = isset( $_POST['to-date'] ) ? $_POST['to-date'] : null;
		
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
		
		$user_rows = ub_get_user_leaderboard( array(
				'sort_by' => $sort_by,
				'from_date' => $from_date,
				'to_date' => $to_date
		) );
		
		$html = '';
		
		ob_start();
		ub_get_template_part( 'user-leaderboard-table', null, true, array(
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

function ub_get_user_leaderboard( $filters = array() ) {
	
	$sort_by = isset( $filters['sort_by'] ) ? $filters['sort_by'] : 'points';
	$from_date = isset( $filters['from_date'] ) ? $filters['from_date'] : null;
	$to_date = isset( $filters['to_date'] ) ? $filters['to_date'] : null;
	
	global $wpdb;
	
	$order_by = 'points';
	if ( $sort_by == 'badges' ) {
		$order_by = 'count_badges';
	}
	
	$query = 'SELECT user_id, display_name, SUM(CASE WHEN type = "badge" THEN 1 ELSE 0 END) AS count_badges, '
			. 'SUM(CASE WHEN type = "points" THEN value ELSE 0 END) AS points FROM wp_ub_user_assignment, ' . $wpdb->users
			. ' u WHERE user_id = u.ID';
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
	
	$query .= ' GROUP BY user_id ORDER BY ' . $order_by;
	
	
	$rows = $wpdb->get_results( $query, ARRAY_A );
	
	return $rows;
	
}