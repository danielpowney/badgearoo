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


function ub_leaderboard( $atts) {

	extract( shortcode_atts( array(
			'show_avatar' => true,
			'before_name' => '',
			'after_name' => '',
			'show_badges' => true,
			'show_points' => true,
			'sort_by' => 'points'
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
	
	global $wpdb;
	
	$results = $wpdb->get_results( 'SELECT user_id, meta_value AS points FROM ' . $wpdb->usermeta . ' WHERE meta_key = "ub_points"' );
	
	$user_rows = array();
	foreach ( $results as $row ) {
		$user = get_userdata( $row->user_id );
		$badges = User_Badges::instance()->api->get_user_badges( $row->user_id );
		array_push( $user_rows, array( 
				'display_name' => $user->display_name,
				'user_id' => $row->user_id,
				'points' => $row->points,
				'badges' => $badges 
		) );
	}
	
	if ( $sort_by == 'points' ) {
		uasort( $user_rows, 'ub_sort_most_points' );
	} else if ( $sort_by == 'badge_count' ) {
		uasort( $user_rows, 'ub_sort_badge_count' );
	}
	
	$html = '';

	ob_start();
	ub_get_template_part( 'user-leaderboard', null, true, array(
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

	return $html;
}
add_shortcode( 'ub_leaderboard', 'ub_leaderboard' );