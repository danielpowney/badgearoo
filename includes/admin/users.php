<?php
add_action('manage_users_columns','ub_add_user_badges_columns');
add_action('manage_users_custom_column','ub_manage_user_badges_columns',10,3);

function ub_add_user_badges_columns( $column_headers ) {
    $column_headers['badges'] = __( 'Badges' , 'user-badges' ); 
    $column_headers['points'] = __( 'Points' , 'user-badges' );
    $column_headers['view-assignments'] = __( 'Action' , 'user-badges' );
    return $column_headers;
}

function ub_manage_user_badges_columns( $custom_column, $column_name, $user_id  ) {
		
	$column_content = '';
			
    if  ( $column_name == 'badges' ) {
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
        
        $count = count( $badges );
        
        if ( $count == 0 ) {
        	$column_content .= __('None', 'user-badges' );
        } else {
        	
        	$index = 0;
	        foreach ( $badges as $badge ) {
	        	$attachment_img = wp_get_attachment_image_src( get_post_thumbnail_id( $badge->id ) );
	        	$column_content .= '<a href="' . get_edit_post_link( $badge->id ) . '">' . $badge->title;

	       		if ( $badge_count_lookup[$badge->id] && $badge_count_lookup[$badge->id] > 1 ) {
					$column_content .= ' (' . $badge_count_lookup[$badge->id] . ')';
				} 
				$column_content	.= '</a>';
	        	
	        	if ( $index < $count-1 ) {
	        		$column_content .= ', ';
	        	}
	        	$index++;
	        }
        }
    } 
    
    if ( $column_name == 'points' ) {
    			
		$points = User_Badges::instance()->api->get_user_points( $user_id );	
    	$column_content .= $points;
    	
    }
    
    if ( $column_name == 'view-assignments' ) {
    	 
    	$url = 'edit.php?post_type=badge&page=' . User_Badges::ASSIGNMENTS_PAGE_SLUG . '&user-id=' . $user_id;
    	$column_content .= '<a href="' . $url . '">' . __( 'View Assignments', 'user-badges' ) . '</a>';
    	 
    }
    
    return $column_content;
}


/**
 * 
 * @param unknown $user
 */
function ub_show_user_profile( $user ) {
	
	if ( current_user_can( 'manage_options') ) {
		
		$points = User_Badges::instance()->api->get_user_points( $user->ID );
		
		?>
		
		<h3><?php _e( 'User Badges', 'user-badges' ); ?></h3>
		<table class="form-table">
			<tr>
				<th>
					<label for="points"><?php _e('Points'); ?></label>
				</th>
				<td>
					<input type="number" name="points" value="<?php echo $points; ?>" class="small-text" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="badges"><?php _e('Badges'); ?></label>
				</th>
				<td>
					<?php 
					$badges = User_Badges::instance()->api->get_badges();
					
					$user_badges = User_Badges::instance()->api->get_user_badges( $user->ID );
					
					$selected = array();
					foreach ( $user_badges as $user_badge ) {
						array_push( $selected, $user_badge->id );
					}
								
					$index = 0;
					$count = count( $badges );
					foreach ( $badges as $badge ) {
						$is_selected = in_array( $badge->id, $selected );
						?>
						<input type="checkbox" name="badges[]" value="<?php echo $badge->id; ?>"<?php if ( $is_selected  == true) { echo 'checked'; } ?> />
						<label><a href="<?php echo get_edit_post_link( $badge->id ); ?>"><?php echo $badge->title; ?></a></label>
						<?php 
						if ( $index < ( $count-1) ) {
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
	
	if ( current_user_can( 'edit_user', $user_id ) ) {
		
		$points = isset( $_POST['points'] ) && is_numeric( $_POST['points'] ) ? intval( $_POST['points'] ) : 0;
		
		$total_points = User_Badges::instance()->api->get_user_points( $user_id );
			
		// Override all other points
		// Add points diff as a user assingment with no condition id (so we know it was by an admin)
		if ( $total_points > 0 ) {
			$diff_points = $points - $total_points;
			User_Badges::instance()->api->add_user_assignment( null, $user_id, 'points', $diff_points, null );
		}
		
		$badges = isset( $_POST['badges'] ) && is_array( $_POST['badges'] ) ? $_POST['badges'] : array();
		
		$current_badges = User_Badges::instance()->api->get_user_badges( $user_id );
		
		$temp_badges = array();
		foreach ( $current_badges as $current_badge ) {
			if ( ! in_array( $current_badge->id, $badges ) ) {
				User_Badges::instance()->api->delete_user_assignment( null, null, $user_id, $type = 'badge', $current_badge->id );
			}
			
			array_push( $temp_badges, $current_badge->id );
		}
		
		foreach ( $badges as $badge => $badge_id ) {
			if ( ! in_array( $badge_id, $temp_badges ) ) {
				User_Badges::instance()->api->add_user_assignment( null, $user_id, $type = 'badge', $badge_id );
			}
		}
	}
}
add_action( 'edit_user_profile_update', 'ub_update_user_profile', 1 );
add_action( 'personal_options_update', 'ub_update_user_profile', 1 );