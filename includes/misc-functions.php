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

	$value = '';

	if ( count( $badges ) > 0 ) {

		foreach ( $badges as $badge ) {
			
			ub_get_template_part( 'badge', null, true, array(
					'logo_type' => $badge->logo_type,
					'logo_html' => $badge->logo_html,
					'logo_image' => $badge->logo_image,
					'title' => $badge->title,
					'content'=> $badge->content,
					'excerpt'=> $badge->excerpt,
					'show_title' => true
			) );

		}
	}

	return $value;
}
add_filter( 'get_the_author_badges', 'ub_get_the_author_badges', 10, 2);