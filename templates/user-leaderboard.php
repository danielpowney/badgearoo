<?php 
/**
 * User leaderboard template
 */
?>

<div class="user-leaderboard <?php if ( isset( $class ) ) { echo esc_attr( $class ); } ?>">

	<h3><?php _e( 'Leaderboard', 'user-badges' ); ?></h3>
	
	<?php
	if ( count( $user_rows ) == 0 ) {
		?>
		<p><?php _e( 'None', 'user-badges' ); ?></p>
		<?php 
	} else {
		
		if ( $show_filters == true ) { 
			ub_get_template_part( 'user-leaderboard', 'filters', true, array() );
		}
		
		ub_get_template_part( 'user-leaderboard', 'table', true, array(
				'user_rows'=> $user_rows,
				'show_avatar' => $show_avatar,
				'before_name' => $before_name,
				'after_name' => $after_name,
				'show_badges' => $show_badges,
				'show_points' => $show_points,
				'sort_by' => $sort_by
		) );
	}		
?>
</div>