<?php 

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Recent assignments widget
 */
?>

<div class="<?php if ( isset( $class ) ) { echo esc_attr( $class ); } ?> broo-recent-assignments">

	<?php echo "$before_title" . __( 'Recent Assignments', 'badgearoo' ) . "$after_title"; ?>
	<table>
		<?php 
		
		if ( count( $assignments ) == 0 ) {
			_e( 'No assignments.', 'badgearoo' );
		} else { 
			foreach ( $assignments as $assignment ) {
				?>
				<div class="broo-assignment">
					<?php
					if ( $assignment['type'] == 'badge' && $assignment['badge'] ) {
							
						broo_get_template_part( 'badge', null, true, array(
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
						broo_get_template_part( 'points', null, true, array(
								'points' => $assignment['points']
						) );
					}
	
					$user = get_user_by( 'id', $assignment['user_id'] );
					
					$temp = get_author_posts_url( $assignment['user_id'] );
					$user_permalink = apply_filters( 'broo_user_permalink', get_author_posts_url( $assignment['user_id'] ), $assignment['user_id'] );
					
					?>&nbsp;<?php _e('by'); ?>&nbsp;<a href="<?php echo $user_permalink; ?>"><?php echo esc_html( $user->display_name ); ?></a>
					<span class="broo-time-diff"><?php printf( __( '%s ago', 'badgearoo' ), human_time_diff( strtotime( $assignment['created_dt'] ), strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s' ) ) ) ) ); ?></span>
				</div>
				<?php
			}
		} ?>
	</table>
</div>