<?php 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="<?php if ( isset( $class ) ) { echo esc_attr( $class ); } ?> broo-badge-summary">

	<div class="broo-badge-main">
		<h2><?php echo $title; ?></h2>
	</div>
	
	<?php if ( $show_description == true ) { ?>
		<div class="broo-badge-description"><?php echo $content; ?></div>
	<?php } ?>
	
	<?php if ( $show_users_count == true ) { ?>
		<div class="broo-users-count">
			<?php 
			if ( $users_count == 0 ) {
				_e( 'No users have earned this badge.', 'badgearoo' );
			} else if ( $users_count == 1 ) {
				_e( '1 user has earned this badge.', 'badgearoo' ); 
			} else {
				printf( __( '%d users have earned this badge.', 'badgearoo' ), $users_count );
			}
			?>
		</div>
	<?php } ?>
	
	<?php if ( $show_users == true ) { ?>
		<div class="broo-users">
			<?php
			foreach ( $users as $user ) {
				?>
				<div class="broo-user">
					<?php
					echo get_avatar( $user->ID );
					$user_permalink = apply_filters( 'broo_user_permalink', get_author_posts_url( $user->ID ), $user->ID );
					?>
					<br />
					<a href="<?php echo $user_permalink; ?>"><?php echo esc_html( $user->display_name ); ?></a>
				</div>
				<?php
			}
			?>
		</div>
	<?php } ?>
</div>