<div class="<?php if ( isset( $class ) ) { echo esc_attr( $class ); } ?> ub-condition">

	<h2><?php echo $name; ?></h2>
	
	<?php
	if ( $enabled == false ) {
		?>
		<p><?php _e( 'This condition is not enabled.', 'user-badges' ); ?></p>
		<?php
	}
	
	if ( $show_steps && count( $steps ) > 0 ) { ?>
		<label class="ub-steps"><?php _e( 'Steps:', 'user-badges' ); ?></label>
		
		<ul class="ub-steps">
			<?php
			foreach ( $steps as $step ) {
				?>
				<li class="ub-step"><?php echo $step->label; ?></li>
				<?php
			}
			?>
		</ul>
	<?php }
	
	if ( $show_badges && count( $badges ) > 0 ) { ?>
		<label class="ub-assignments"><?php _e( 'Assignments:', 'user-badges' ); ?></label>
		<?php
		foreach ( $badges as $badge ) {

			ub_get_template_part( 'badge', null, true, array(
					'badge_id' => $badge->id,
					'show_title' => true,
					'badge_theme' => $badge_theme,
					'badge_icon' => $badge->badge_icon,
					'badge_html' => $badge->badge_html,
					'badge_color' => $badge->badge_color,
					'excerpt' => $badge->excerpt,
					'title' => $badge->title,
					'content' => $badge->content,
					'enable_badge_permalink' => $enable_badge_permalink
			) );
		}
	}
	
	if ( $show_points && $points > 0 ) {
		ub_get_template_part( 'points', null, true, array(
				'points' => $points
		) );
	} ?>
</div>