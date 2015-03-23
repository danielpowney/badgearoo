<?php
echo get_avatar( get_current_user_id() );
		
$before_title = apply_filters( 'ub_user_badges_before_title', $before_title );
$after_title = apply_filters( 'ub_user_details_after_title', $after_title );

$title = apply_filters( 'widget_title', get_the_author_meta( 'display_name' ) );
		
echo "$before_title" . esc_html( $title ) . "$after_title";

do_action( 'ub_user_badges_widget_before_badges' );
?>

<div class="badges">
	<?php  echo get_the_author_meta( 'badges' ); ?>
</div>

<?php 
do_action( 'ub_user_badges_widget_after_badges' );