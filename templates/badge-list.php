<?php 
/**
 * Badge list template
 */
?>

<div class="<?php if ( isset( $class ) ) { echo esc_attr( $class ); } ?> ub-badge-list">
	
	<?php
	if ( count( $badges ) == 0 ) {
		_e( 'No badges', 'user-badges' );
	} else {

		foreach ( $badges as $badge ) {
			
			$users = array();
			foreach ( $badge->users as $user_id ) {
			
				$user = get_userdata( intval( $user_id ) );
			
				if ( $user ) {
					array_push( $users, $user );
				}
			}
		
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
		}
	}
	?>
</div>