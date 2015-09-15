<?php 

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Shows a summary of a user's badges and points
 */

?>
<div class="<?php if ( isset( $class ) ) { echo esc_attr( $class ); } ?> broo-user-badges-summary">

	<?php 
	
	if ( count( $badges ) == 0) {
		?><span class="broo-badges"><?php _e( 'No badges.', 'badgearoo' ); ?></span><?php
	} else {
		foreach ( $badges as $badge ) {
			
			broo_get_template_part( 'badge', null, true, array(
					'badge_id' => $badge->id,
					'show_title' => true,
					'badge_theme' => $badge_theme,
					'badge_icon' => $badge->badge_icon,
					'badge_html' => $badge->badge_html,
					'badge_color' => $badge->badge_color,
					'excerpt' => $badge->excerpt,
					'title' => $badge->title,
					'content' => $badge->content,
					'badge_count' => isset( $badge_count_lookup[$badge->id] ) ? $badge_count_lookup[$badge->id] : 1,
					'enable_badge_permalink' => $enable_badge_permalink
			) );
		}
	} ?>
	
	<?php
	
	broo_get_template_part( 'points', null, true, array(
			'points' => $points
	) );
	
	?>
</div>