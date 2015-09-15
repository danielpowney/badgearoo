<?php 

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

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
				<td><span class="broo-rank"><?php echo $rank; ?></span></td>
				<td class="user-meta">
				
					<?php
					if ( $show_avatar == true ) {
						echo get_avatar( $user_row['user_id'] );
					}
					?>
					<span class="broo-name">
						<?php 
						echo "$before_name"; 
						$user_permalink = apply_filters( 'broo_user_permalink', get_author_posts_url( $user_row['user_id'] ), $user_row['user_id'] );
						?>
						<a href="<?php echo $user_permalink; ?>"><?php echo esc_html( $user_row['display_name'] ); ?></a>
						<?php echo "$after_name"; ?>
					</span>
				</td>
				<?php if ( $show_points ) { ?>
					<td>
						<span class="broo-points"><?php echo $user_row['points']; ?></span>
					</td>
				<?php }
				if ( $show_badges == true) {
					?>
					<td class="broo-badges">
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