<?php
/**
 * Allows you to use get_the_author_meta( 'badges' ) or the_author_meta( 'badges' ) in your theme
 *
 * @param unknown $value
 * @param unknown $user_id
 * @return string
 */
function ub_get_the_author_badges( $value, $user_id = false ) {

	if ( ! $user_id ) {
		global $authordata;
		$user_id = isset( $authordata->ID ) ? $authordata->ID : 0;
	}

	$points = User_Badges::instance()->api->get_user_points( $user_id );
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
	
	$general_settings = (array) get_option( 'ub_general_settings' );
	
	ub_get_template_part( 'user-badges-summary', null, true, array(
			'badge_theme' => $general_settings['ub_badge_theme'],
			'badges' => $badges,
			'points' => $points,
			'badge_count_lookup' => $badge_count_lookup,
			'enable_badge_permalink' => $general_settings['ub_enable_badge_permalink']
	) );
	
}
add_filter( 'get_the_author_badges', 'ub_get_the_author_badges', 10, 2);