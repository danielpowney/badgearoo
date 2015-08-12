<div class="<?php echo $class; ?> ub-condition">

	<h2><?php echo $name; ?></h2>
	
	<?php
	if ( $enabled == false ) {
		?>
		<p><?php _e( 'This condition is not enabled.', 'user-badges' ); ?></p>
		<?php
	}
	
	if ( $show_steps && count( $steps ) > 0 ) { ?>
		<label><?php _e( 'Required steps:', 'user-badges' ); ?></label>
		
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
		<label><?php _e( 'Badges:', 'user-badges' ); ?></label>
		
		<ul class="ub-badges">
			<?php
			foreach ( $badges as $badge ) {
				?>
				<li class="ub-badge-list">
					<?php 
					ub_get_template_part( 'badge', null, true, array(
							'show_title' => true,
							'logo_type' => $badge->logo_type,
							'logo_image' => $badge->logo_image,
							'logo_html' => $badge->logo_html,
							'excerpt' => $badge->excerpt,
							'title' => $badge->title,
							'content' => $badge->content
					) );
					?>
				</li>
				<?php
			}
			?>
		</ul>
	<?php }
	
	if ( $show_points && $points > 0 ) {
		ub_get_template_part( 'points', null, true, array(
				'points' => $points
		) );
	} ?>
</div>