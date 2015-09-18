<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * User Dashboard template
 */
?>

<div class="<?php if ( isset( $class ) ) { echo esc_attr( $class ); } ?> broo-user-dashboard">

	<h2><?php _e( 'User Dashboard', 'badgearoo' ); ?></h2>
	
	<?php 
	if ( $user_id == 0 ) {
		?>
		<p><?php _e( 'You must be logged in to view your user dashboard.', 'badgearoo' ); ?></p>
		<?php
	} else {
	
		if ( $show_filters == true ) { 
			broo_get_template_part( 'user-dashboard', 'filters', true, array( 
					'to_date' => $to_date, 
					'from_date' => $from_date, 
					'type' => $type
			) );
		}
		
		if ( $show_points || $show_badges ) { ?>
			<table class="broo-user-dashboard-summary">
				<tbody>
					<tr>
						<th scope="col"><?php _e( 'Assignments', 'badgearoo' ); ?></th>
						<td>
							<?php echo $count_assignments; ?>
						 </td>
					</tr>
					<?php if ( $show_points && $type != 'badge' ) { ?>
						<tr>
							<th scope="col"><?php _e( 'Points', 'badgearoo' ); ?></th>
							<td>
								<?php 
								broo_get_template_part( 'points', null, true, array(
										'points' => $points
								) );
							 	?>
							 </td>
						</tr>
					<?php }
					if ( $show_badges && $type != 'points' ) { ?>
						<tr>
							<th scope="col"><?php _e( 'Badges', 'badgearoo' ); ?></th>
							<td>
								<?php 
								if ( count( $badges ) > 0 ) {
									
									foreach ( $badges as $badge ) {
										
										broo_get_template_part( 'badge', null, true, array(
												'badge_id' => $badge->id,
												'show_title' => true,
												'badge_theme' => $badge_theme,
												'badge_icon' => $badge->badge_icon,
												'badge_html' => $badge->badge_html,
												'badge_color' => $badge->badge_color,
												'excerpt' => $badge->excerpt,
												'title' => $badge->title,
												'content' => $badge->content,
												'enable_badge_permalink' => $enable_badge_permalink,
												'badge_count' => isset( $badge_count_lookup[$badge->id] ) ? $badge_count_lookup[$badge->id] : 1,
										) );
									}
								} else {
									_e( 'No badges', 'badgearoo' );
								}
							 ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php }
		
		if ( $show_assignments ) {
			broo_get_template_part( 'user-dashboard', 'assignments', true, array(
					'assignments' => $assignments,
					'limit' => $limit,
					'offset' => $offset,
					'count_assignments' => $count_assignments,
					'badge_theme' => $badge_theme,
					'enable_badge_permalink' => $enable_badge_permalink
			) );
		}
	}
	?>
</div>