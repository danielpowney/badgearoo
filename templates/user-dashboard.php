<?php
/**
 * User Dashboard template
 */
?>

<div class="<?php if ( isset( $class ) ) { echo esc_attr( $class ); } ?> ub-user-dashboard">

	<h2><?php _e( 'User Dashboard', 'user-badges' ); ?></h2>
	
	<?php 
	if ( $show_filters == true ) { 
		ub_get_template_part( 'user-dashboard', 'filters', true, array( 
				'to_date' => $to_date, 
				'from_date' => $from_date, 
				'type' => $type
		) );
	}
	
	if ( $show_points || $show_badges ) { ?>
		<table class="ub-user-dashboard-summary">
			<tbody>
				<tr>
					<th scope="col"><?php _e( 'Assignments', 'user-badges' ); ?></th>
					<td>
						<?php echo $count_assignments; ?>
					 </td>
				</tr>
				<?php if ( $show_points && $type != 'badge' ) { ?>
					<tr>
						<th scope="col"><?php _e( 'Points', 'user-badges' ); ?></th>
						<td>
							<?php 
							ub_get_template_part( 'points', null, true, array(
									'points' => $points
							) );
						 	?>
						 </td>
					</tr>
				<?php }
				if ( $show_badges && $type != 'points' ) { ?>
					<tr>
						<th scope="col"><?php _e( 'Badges', 'user-badges' ); ?></th>
						<td><?php 
							if ( count( $badges ) > 0 ) {
								foreach ( $badges as $badge ) {
									?>
									<div class="ub-badge" title="<?php echo $badge->excerpt; ?>"><?php echo $badge->title; ?><?php 
									if ( $badge_count_lookup[$badge->id] && $badge_count_lookup[$badge->id] > 1 ) {
										printf( __( ' X %d', 'user-badges' ), $badge_count_lookup[$badge->id] );
									} 
									?></div>
									<?php
								}
							} else {
								_e( 'No badges', 'user-badges' );
							}
						 ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	<?php }
	
	if ( $show_assignments ) {
		ub_get_template_part( 'user-dashboard', 'assignments', true, array(
				'assignments' => $assignments,
				'limit' => $limit,
				'offset' => $offset,
				'count_assignments' => $count_assignments
		) );
	}
	?>
</div>