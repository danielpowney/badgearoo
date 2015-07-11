<div class="<?php echo $class; ?> ub-condition">

	<h2><?php echo $name; ?></h2>
	
	<?php
	if ( $enabled == false ) {
		?>
		<p><?php _e( 'This condition is not enabled.', 'user_badges' ); ?></p>
		<?php
	}
	
	if ( $show_steps && count( $steps ) > 0 ) { ?>
		<b><?php _e( 'Required steps:', 'user_badges' ); ?></b>
		
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
		<b><?php _e( 'Badges:', 'user_badges' ); ?></b>
		
		<ul class="ub-badges">
			<?php
			foreach ( $badges as $badge ) {
				?>
				<li class="ub-badge">
					<img src="<?php echo $badge->url; ?>" title="<?php echo $badge->description; ?>" /><?php echo $badge->name; ?>
				</li>
				<?php
			}
			?>
		</ul>
	<?php }
	
	if ( $show_points && $points > 0 ) {
		?>
		<div class="ub-points">
			<b><?php _e( 'Points: ', 'user_badges' ); ?></b>
			<?php echo $points; ?>
		</div>
		<?php
	} ?>
</div>