<?php
function ub_user_badges( $atts) {
	
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
	
	$badges = User_Badges::instance()->api->get_user_badges( $user_id );
	
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
	
	$html = '';
	
	if (count( $badges ) > 0 ) {
	
		foreach ( $badges as $badge ) {
				
			ob_start();
			ub_get_template_part( 'badge', null, true, array(
					'logo_type' => $badge->logo_type,
					'logo_html' => $badge->logo_html,
					'logo_image' => $badge->logo_image,
					'title' => $badge->title,
					'content'=> $badge->content,
					'excerpt'=> $badge->excerpt,
					'show_title' => true,
					'badge_count' => isset( $badge_count_lookup[$badge->id] ) ? $badge_count_lookup[$badge->id] : 1 
			) );
			$html .= ob_get_contents();
			ob_end_clean();
			
		}
	}
	
	return $html;
}
add_shortcode( 'ub_user_badges', 'ub_user_badges' );



function ub_user_points( $atts) {

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

	$points = User_Badges::instance()->api->get_user_points( $user_id );

	$html = '';
	ob_start();
	ub_get_template_part( 'points', null, true, array(
			'points' => $points
	) );
	$html .= ob_get_contents();
	ob_end_clean();
	
	return $html;
}
add_shortcode( 'ub_user_points', 'ub_user_points' );



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
function ub_badge( $atts ) {

	extract( shortcode_atts( array(
			'badge_id' => null,
			'show_description' => true,
			'before_name' => '',
			'after_name' => '',
			'show_users' => true,
			'show_users_count' => true
	), $atts ) );

	if ( $badge_id == null) {
		return __( 'Badge not found.', 'user-badges' );;
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
		return __( 'Badge not found.', 'user-badges' );
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
			'logo_type' => $badge->logo_type,
			'logo_html' => $badge->logo_html,
			'logo_image' => $badge->logo_image,
			'title' => $badge->title,
			'content'=> $badge->content,
			'excerpt'=> $badge->excerpt,
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
add_shortcode( 'ub_badge', 'ub_badge' );



/**
 * Displays a list of badges
 *
 * @param unknown $atts
 * @return unknown
 */
function ub_badge_list( $atts ) {

	extract( shortcode_atts( array(
			'badge_ids' => null,
			'show_description' => true,
			'before_name' => '',
			'after_name' => '',
			'show_users' => true,
			'show_users_count' => true
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
			
		if ( function_exists( 'icl_object_id' ) ) {
			$temp_badge_ids = array();
	
			foreach ( $badge_ids as $temp_badge_id ) {
				global $sitepress;
				array_push( $temp_badge_ids, icl_object_id( $temp_badge_id , get_post_type( $temp_badge_id ), true,
						$sitepress->get_default_language() ) );
			}
			
			$badge_ids = $temp_badge_ids;
		}
	} else {
		$badge_ids = null;
	}
	
	$badges =  User_Badges::instance()->api->get_badges( array( 'badge_ids' => $badge_ids ), ( $show_users || $show_users_count ) );

	$html = '';

	ob_start();
	ub_get_template_part( 'badge', 'list', true, array(
			'badges' => $badges,
			'show_users' => $show_users,
			'show_users_count' => $show_users_count,
			'show_description' => $show_description
				
	) );
	$html .= ob_get_contents();
	ob_end_clean();

	return $html;
}
add_shortcode( 'ub_badge_list', 'ub_badge_list' );



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
		return __( 'Condition not found.', 'user-badges' );
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
		return __( 'Condition not found.', 'user-badges' );
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
	
	$query .= ' GROUP BY user_id ORDER BY ' . $order_by . ' DESC';
	
	$rows = $wpdb->get_results( $query, ARRAY_A );
	
	return $rows;
	
}




/**
 * Displays the user dashboard
 *
 * @param unknown $atts
 * @return string
 */
function ub_user_dashboard($atts) {

	extract( shortcode_atts( array(
			'show_badges' => true,
			'show_points' => true,
			'show_assignments' => true,
			'limit' => 5,
			'offset' => 0,
			'type' => null,
			'show_filters' => true,
			'to_date' => null,
			'from_date' => null,
			'type' => null
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
	
	if ( $user_id == 0 ) {
		// TODO
	}
	
	$assignments = User_Badges::instance()->api->get_assignments( array( 
			'user_id' => $user_id,
			'limit' => $limit, 
			'offset' => $offset,
			'to_date' => $to_date,
			'from_date' => $from_date,
			'type' => $type
	), false );
	
	$count_assignments = User_Badges::instance()->api->get_assignments( array(
			'user_id' => $user_id, 
			'to_date' => $to_date,
			'from_date' => $from_date,
			'type' => $type
	), true );

	$points = User_Badges::instance()->api->get_user_points( $user_id, array( 'to_date' => $to_date, 'from_date' => $from_date ) );
	$badges = User_Badges::instance()->api->get_user_badges( $user_id, array( 'to_date' => $to_date, 'from_date' => $from_date ) );
	
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
	
	if ( ! is_array( $assignments ) ) {
		$assignments = array();
	}
	
	$offset = $limit; // next offset
	
	// TODO add condition progress

	$html = '';

	ob_start();
	ub_get_template_part( 'user-dashboard', null, true, array(
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
			'count_assignments' => $count_assignments
	) );
	$html .= ob_get_contents();
	ob_end_clean();

	return $html;
}
add_shortcode( 'ub_user_dashboard', 'ub_user_dashboard' );




/**
 * Loads more user dashboard assignments
 */
function ub_user_dashboard_assignments_more() {

	$ajax_nonce = $_POST['nonce'];
	if ( wp_verify_nonce($ajax_nonce, User_Badges::ID.'-nonce' ) ) {

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

		$assignments = User_Badges::instance()->api->get_assignments( array( 
			'user_id' => $user_id,
			'limit' => $limit, 
			'offset' => $offset,
			'to_date' => $to_date,
			'from_date' => $from_date,
			'type' => $type
		) );
		
		$offset += $limit; // next offset

		$html = '';

		ob_start();
		
		foreach ( $assignments as $assignment ) {
			ub_get_template_part( 'assignments-table-row', null, true, array(
					'assignment' => $assignment,
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