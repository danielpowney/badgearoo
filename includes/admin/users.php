<?php
add_action('manage_users_columns','ub_add_badges_column');
add_action('manage_users_custom_column','ub_user_badges_column',10,3);

function ub_add_badges_column( $column_headers ) {
    $column_headers['badges'] = __( 'Badges' , 'user-badges' ); 
    return $column_headers;
}

function ub_user_badges_column( $custom_column, $column_name, $user_id  ) {
		
	$column_content = '';
			
    if  ( $column_name == 'badges' ) {
        $badges = User_Badges::instance()->api->get_user_badges( $user_id );
        
        foreach ( $badges as $badge ) {
        	$attachment_img = wp_get_attachment_image_src( get_post_thumbnail_id( $badge->id ) );
        	$column_content .= '<img src="' . $attachment_img[0] . '" widht="' . $attachment_img[1] . '" height="' . $attachment_img . '" title="' . $badge->description . '" />';
        }	
    }
    return $column_content;
}