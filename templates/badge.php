<div class="<?php if ( isset( $class ) ) { echo esc_attr( $class ); } ?> ub-badge" title="<?php echo $excerpt; ?>"><?php 
	if ( $logo_type == 'image' ) { ?>
		<img src="<?php echo $logo_image; ?>" />
	<?php } else if ( $logo_type == 'html') { 
		echo $logo_html;
	}
	if ( $show_title || $logo_type == 'none' ) {
		echo $title;
	}
	
	if ( isset( $badge_count ) && $badge_count > 1 ) {
		printf( __( ' X %d', 'user-badges' ), $badge_count );
	}
?></div>