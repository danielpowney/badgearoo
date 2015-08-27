<?php 
/**
 * Recent assignments widget
 */
?>

<div class="<?php if ( isset( $class ) ) { echo esc_attr( $class ); } ?> ub-recent-assignments">

	<?php echo "$before_title" . __( 'Recent Assignments', 'user-badges' ) . "$after_title"; ?>
	<table>
		<?php 
		foreach ( $assignments as $assignment ) {
			?>
			<div class="ub-assignment">
				<?php
				if ( $assignment['type'] == 'badge' && $assignment['badge'] ) {
						
					ub_get_template_part( 'badge', null, true, array(
							'badge_id' => $assignment['badge']->id,
							'show_title' => true,
							'badge_theme' => $badge_theme,
							'badge_icon' => $assignment['badge']->badge_icon,
							'badge_html' => $assignment['badge']->badge_html,
							'badge_color' => $assignment['badge']->badge_color,
							'excerpt' => $assignment['badge']->excerpt,
							'title' => $assignment['badge']->title,
							'content' => $assignment['badge']->content,
							'enable_badge_permalink' => $enable_badge_permalink
					) );
				
				} else if ( $assignment['points'] ) {
					ub_get_template_part( 'points', null, true, array(
							'points' => $assignment['points']
					) );
				}

				$user = get_user_by( 'id', $assignment['user_id'] );
				?>
				<a href="<?php echo get_author_posts_url( $assignment['user_id'] ); ?>"><?php echo esc_html( $user->display_name ); ?></a>
				<span class="ub-time-diff"><?php printf( __( '%s ago', 'user-badges' ), human_time_diff( strtotime( $assignment['created_dt'] ), strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s' ) ) ) ) ); ?></span>
			</div>
			<?php
		} ?>
	</table>
</div>