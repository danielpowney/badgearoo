<?php 
/**
 * Badge theme: light
 */


if ( isset( $enable_badge_permalink ) && $enable_badge_permalink ) {
	?><a href="<?php echo get_the_permalink( $badge_id ); ?>"<?php
} else {
	?><div<?php
}
?> class="<?php if ( isset( $class ) ) { echo esc_attr( $class ); } ?> ub-badge" title="<?php echo $excerpt; ?>"><?php

?>
<span class="ub-theme-color" style="background: <?php echo $badge_color; ?>;"></span>
<?php
echo $title;
		
if ( isset( $enable_badge_permalink ) && $enable_badge_permalink ) {
	?></a><?php
} else {
	?></div><?php
}