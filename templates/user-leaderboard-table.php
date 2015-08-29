<?php 
/**
 * User leaderboard table template
 */
?>
<table class="user-leaderboard-table">
	<thead>
		<tr>
			<th><?php _e( 'Rank', 'badgearoo' ); ?></th>
			<th><?php _e( 'User', 'badgearoo' ); ?></th>
			<th><?php _e( 'Points', 'badgearoo' ); ?></th>
			<th><?php _e( 'Badges', 'badgearoo' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$rank = 1;
		foreach ( $user_rows as $user_row ) {
			?>
			<tr class="user-row">
				<td><span class="ub-rank"><?php echo $rank; ?></span></td>
				<td class="user-meta">
				
					<?php
					if ( $show_avatar == true ) {
						echo get_avatar( $user_row['user_id'] );
					}
					?>
					<span class="ub-name">
						<?php echo "$before_name"; ?>
						<a href="<?php echo get_author_posts_url( $user_row['user_id'] ); ?>"><?php echo esc_html( $user_row['display_name'] ); ?></a>
						<?php echo "$after_name"; ?>
					</span>
				</td>
				<?php if ( $show_points ) { ?>
					<td>
						<span class="ub-points"><?php echo $user_row['points']; ?></span>
					</td>
				<?php }
				if ( $show_badges == true) {
					?>
					<td class="ub-badges">
						<?php 
						echo $user_row['count_badges'];
						?>
					</td><?php
				} ?>
			</tr>
			<?php
			$rank++;
		}
		?>
	</tbody>
</table>