<?php 

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Badge theme: light
 */
?>
<div class="<?php if ( isset( $class ) ) { echo esc_attr( $class ); } ?> broo-badge">
	
	<img class="broo-badge-icon" src="<?php echo $badge_icon; ?>" />
	
	<?php
	if ( isset( $enable_badge_permalink ) && $enable_badge_permalink ) {
		?>
		<a href="<?php echo get_the_permalink( $badge_id ); ?>" title="<?php echo $excerpt; ?>" class="broo-badge-title"><?php echo $title; ?></span></a>
		<?php
	} else {
		?>
		<span class="broo-badge-title" title="<?php echo $excerpt; ?>"><?php echo $title; ?></span>
		<?php
	}
	?>
	
</div>