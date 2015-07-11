<div class="<?php echo $class; ?> ub-badge-summary">

	<div class="ub-badge-main">
		<img src="<?php echo $url; ?>" title="<?php echo $name; ?>" />
		<h2><?php echo $name; ?></h2>
	</div>
	
	<div class="ub-badge-description"><?php echo $description; ?></div>
	
	<div class="ub-users-count"><?php printf( __( '%d users have earned this badge.', 'user_badges' ), $users_count ); ?></div>
	
	<div class="ub-users">
		<?php
		foreach ( $users as $user ) {
			?>
			<div class="ub-user">
				<?php
				echo get_avatar( $user->ID );
				?>
				<br />
				<a href="<?php echo get_author_posts_url( $user->ID ); ?>"><?php echo esc_html( $user->display_name ); ?></a>
			</div>
			<?php
		}
		?>
	</div>
</div>