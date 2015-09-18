<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

echo get_avatar( $user_id );
		
$before_title = apply_filters( 'broo_user_badges_before_title', $before_title );
$after_title = apply_filters( 'broo_user_details_after_title', $after_title );

$title = apply_filters( 'widget_title', get_the_author_meta( 'display_name', $user_id ) );
		
echo "$before_title" . esc_html( $title ) . "$after_title";

$user_biography = get_the_author_meta( 'description', $user_id );

if ( $user_biography && strlen( $user_biography ) > 0 ) {
	?><p class="broo-user-biography"><?php echo $user_biography; ?></p><?php
}

do_action( 'broo_user_badges_widget_before_badges' );

broo_get_template_part( 'user-badges-summary', null, true, array(
		'badge_theme' => $badge_theme,
		'badges' => $badges,
		'points' => $points,
		'badge_count_lookup' => $badge_count_lookup,
		'enable_badge_permalink' => $enable_badge_permalink
) );

do_action( 'broo_user_badges_widget_after_badges' );