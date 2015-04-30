<?php 
/**
 * Rating result reviews template
 */
?>

<div class="user-leaderboard <?php if ( isset( $class ) ) { echo esc_attr( $class ); } ?>">

	<h3><?php _e( 'Leaderboard', 'user-badges' ); ?></h3>
	TODO: filters: sort by badge_count or points, date range
	<?php
	if ( count( $user_rows ) == 0 ) {
		?>
		<p><?php _e( 'None', 'user-badges' ); ?></p>
		<?php 
	} else {
		?>
		<table>
		<?php
		
			foreach ( $user_rows as $user_row ) {
				?>
				<tr class="user-row">
					<td class="user-meta">
					
						<?php
						if ( $show_avatar == true ) {
							echo get_avatar( $user_row['user_id'] );
						}
						?>
						<span class="name">
							<?php echo "$before_name" . esc_html( $user_row['display_name'] ) . "$after_name"; ?>
						</span>
					</td>
					<?php if ( $show_points ) { ?>
						<td class="points">
							<?php echo $user_row['points']; ?>
						</td>
					<?php }
					if ( $show_badges == true) {
						?>
						<td>
							<?php
							foreach ( $user_row['badges'] as $badge ) {
								ub_get_template_part( 'badge', null, true, array(
										'url'=> $badge->url,
										'description' => $badge->description
								) );
							}
							?>
						</td><?php
					} ?>
				</tr>
				<?php
			}
			?>
		</table>	
		<?php
	}		
?>
</div>