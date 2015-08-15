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

	if ( count( $badges ) > 0 ) {

		foreach ( $badges as $badge ) {
			
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

		}
	} else {
		_e( 'No badges', 'user-badges' );
	}
	
	echo '<br />';

	$points = User_Badges::instance()->api->get_user_points( $user_id );

	ub_get_template_part( 'points', null, true, array(
			'points' => $points
	) );
}
add_filter( 'get_the_author_badges', 'ub_get_the_author_badges', 10, 2);