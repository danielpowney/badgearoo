<div class="ub-badge"><?php 
	if ( $logo_type == 'image' ) { ?>
		<img src="<?php echo $logo_image; ?>" title="<?php echo $excerpt; ?>" />
	<?php } else if ( $logo_type == 'html') { 
		echo $logo_html;
	}
	if ( $show_title || $logo_type == 'none' ) {
		echo $title;
	}
?></div>