<?php
add_action('manage_users_columns','ub_add_badges_column');
add_action('manage_users_custom_column','ub_user_badges_column',10,3);

function ub_add_badges_column( $column_headers ) {
    $column_headers['badges'] = __( 'Badges' , 'user-badges' ); 
    $column_headers['points'] = __( 'Points' , 'user-badges' );
    return $column_headers;
}

function ub_user_badges_column( $custom_column, $column_name, $user_id  ) {
		
	$column_content = '';
			
    if  ( $column_name == 'badges' ) {
        $badges = User_Badges::instance()->api->get_user_badges( $user_id );
        
        $count = count( $badges );
        if ( $count == 0 ) {
        	$column_content .= __('None', 'user-badges' );
        } else {
        	$index = 0;
	        foreach ( $badges as $badge ) {
	        	$attachment_img = wp_get_attachment_image_src( get_post_thumbnail_id( $badge->id ) );
	        	//$column_content .= '<div style="display: inline-block; text-align: center; margin-right: 10px;"><img src="' . $attachment_img[0] . '" widht="' . $attachment_img[1] . '" height="' . $attachment_img . '" title="' . $badge->description . '" /><br />';
	        	$column_content .= '<a href="' . get_edit_post_link( $badge->id ) . '">' . $badge->name . '</a>';
	        	
	        	if ( $index < $count-1 ) {
	        		$column_content .= ', ';
	        	}
	        	$index++;
	        }
        }
    } 
    if ( $column_name == 'points' ) {

    	$points = get_user_meta( $user_id, 'ub_points', true );
    	
    	if ( strlen( $points ) > 0 ) {
    		$column_content .= $points;
    	} else {
    		$column_content .= '0';
    	}
    }
    
    return $column_content;
}


/**
 * 
 * @param unknown $user
 */
function ub_show_user_profile( $user ) {
	
	if ( current_user_can( 'manage_options') ) {
		
		$points = get_user_meta( $user->ID, 'ub_points', true );
		
		if ( strlen( $points ) == 0 || ! is_numeric( $points ) ) {
			$points = 0;
		}
		?>
		
		<h3><?php _e( 'User Badges', 'user-badges' ); ?></h3>
		<table class="form-table">
			<tr>
				<th>
					<label for="points"><?php _e('Points'); ?></label>
				</th>
				<td>
					<input type="number" name="points" value="<?php echo $points; ?>" class="small-text" />
					TODO show how points are assigned
				</td>
			</tr>
			<tr>
				<th>
					<label for="badges"><?php _e('Badges'); ?></label>
				</th>
				<td>
					<?php 
					global $wpdb;
	
					$query = 'SELECT * FROM ' . $wpdb->posts . ' WHERE post_type = "badge"';
					$results = $wpdb->get_results( $query );
					
					$selected = $wpdb->get_col( 'SELECT badge_id FROM ' . $wpdb->prefix . UB_USER_ASSIGNMENT_TABLE_NAME . ' WHERE type = "badge" AND user_id = ' . $user->ID );
					
					$index = 0;
					$count = count( $results );
					foreach ( $results as $row ) {
						$is_selected = in_array( $row->ID, $selected );
						?>
						<input type="checkbox" name="badges[]" value="<?php echo $row->ID; ?>"<?php if ( $is_selected  == true) { echo 'checked'; } ?> />
						<label><a href="<?php echo get_edit_post_link( $row->ID ); ?>"><?php echo $row->post_title; ?></a></label>
						<?php 
						if ( $index < $count-1 ) {
							echo '<br />';
						}
						$index++;
					} ?>
				</td>
			</tr>
		</table>
		<?php
	}
}
add_action( 'show_user_profile', 'ub_show_user_profile' );
add_action( 'edit_user_profile', 'ub_show_user_profile' );

/**
 * Allow admins to manually change assigned points & badges
 * @param unknown $user_id
 */
function ub_update_user_profile( $user_id ) {
	
	$points = isset( $_POST['points'] ) && is_numeric( $_POST['points'] ) ? intval( $_POST['points'] ) : 0;
	
	$total_points = get_user_meta( $user_id, 'ub_points', true );
	if ( strlen( $total_points ) == 0 || ! is_numeric( $total_points ) ) {
		$total_points = 0;
	} else {
		$total_points = intval( $total_points );
	}
		
	$total_points += $points;
	update_user_meta( $user_id, 'ub_points', $total_points );
}
add_action( 'edit_user_profile_update', 'ub_update_user_profile' );