<div class="<?php if ( isset( $class ) ) { echo esc_attr( $class ); } ?> ub-badge-summary">

	<div class="ub-badge-main">
		<h2><?php echo $title; ?></h2>
	</div>
	
	<div class="ub-badge-description"><?php echo $content; ?></div>
	
	<div class="ub-users-count">
		<?php 
		if ( $users_count == 0 ) {
			_e( 'No users have earned this badge.', 'user-badges' );
		} else if ( $users_count == 1 ) {
			_e( '1 user has earned this badge.', 'user-badges' ); 
		} else {
			printf( __( '%d users have earned this badge.', 'user-badges' ), $users_count );
		}
		?>
	</div>
	
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