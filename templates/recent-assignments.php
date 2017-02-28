<?php 

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Recent assignments widget
 */
?>

<div class="<?php if ( isset( $class ) ) { echo esc_attr( $class ); } ?> broo-recent-assignments">

	<table>
		<?php 
		
		if ( count( $assignments ) == 0 ) {
			?><span class="activity"><?php _e( 'No assignments.', 'badgearoo' ); ?></span><?php
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
	
					?>
					<span class="broo-time-diff activity"><?php printf( __( '%s ago', 'badgearoo' ), human_time_diff( strtotime( $assignment['created_dt'] ), strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s' ) ) ) ) ); ?></span>
				</div>
				<?php
			}
		} ?>
	</table>
</div>