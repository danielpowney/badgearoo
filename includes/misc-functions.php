<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Allows you to use get_the_author_meta( 'badges' ) or the_author_meta( 'badges' ) in your theme
 *
 * @param unknown $value
 * @param unknown $user_id
 * @return string
 */
function broo_get_the_author_badges( $value, $user_id = false ) {

	if ( ! $user_id ) {
		global $authordata;
		$user_id = isset( $authordata->ID ) ? $authordata->ID : 0;
	}

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
add_filter( 'get_the_author_badges', 'broo_get_the_author_badges', 10, 2);

/**
 * Adds a consistent user permalink
 * 
 * @param unknown $user_permalink
 * @param unknown $user_id
 */
function broo_user_permalink( $user_permalink, $user_id ) {
	$broo_general_settings = (array) get_option( 'broo_general_settings' );

	if ( $broo_general_settings['broo_user_permalinks'] == 'bp_core_get_userlink' && function_exists( 'bp_core_get_userlink' ) ) {
		return bp_core_get_userlink( $user_id, false, true );
	} else if ( $broo_general_settings['broo_user_permalinks'] == 'bbp_user_profile_url' && function_exists( 'bbp_user_profile_url' )) {
		return bbp_get_user_profile_url( $user_id );
	} else if ( $broo_general_settings['broo_user_permalinks'] == 'author_posts_url'  ) {
		return get_author_posts_url( $user_id );
	}
	
	return $user_permalink;
}
add_filter( 'broo_user_permalink', 'broo_user_permalink', 10, 2 );



/**
 * Delete all assignments by user id
 *
 * @param $user_id
 * @param $reassign user id
 */
function broo_delete_user( $user_id, $reassign ) {

	//if ( $reassign == null ) { // do not reassign assignments to anyone ...
		Badgearoo::instance()->api->delete_user_assignments( array( 'user_id' => $user_id ) );
	//} else {
		// TODO
	//}
}