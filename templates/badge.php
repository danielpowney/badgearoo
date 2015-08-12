<div class="ub-badge" title="<?php echo $excerpt; ?>"><?php 
	if ( $logo_type == 'image' ) { ?>
		<img src="<?php echo $logo_image; ?>" />
	<?php } else if ( $logo_type == 'html') { 
		echo $logo_html;
	}
	if ( $show_title || $logo_type == 'none' ) {
		echo $title;
	}
?></div>