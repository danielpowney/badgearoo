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