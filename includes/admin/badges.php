<?php

function ub_manage_badge_posts_columns( $posts_columns ) {
	$post_columns_new_order['cb'] = $posts_columns['cb'];
	$post_columns_new_order['id'] = __( 'ID', 'user-badges' );
	$post_columns_new_order['logo'] = __( 'Badge Logo', 'user-badges' );
	$post_columns_new_order['title'] = $posts_columns['title'];
	$post_columns_new_order['categories'] = $posts_columns['categories'];
	$post_columns_new_order['date'] = $posts_columns['date'];

	return $post_columns_new_order;
}
add_filter( 'manage_badge_posts_columns', 'ub_manage_badge_posts_columns', 5, 1  );

function ub_manage_badge_posts_custom_column( $column_name, $post_id ){

	switch ( $column_name ){

		case 'logo':
			
			$logo_type = get_post_meta( $post_id, 'ub_logo_type', true );
			$logo_image = get_post_meta( $post_id, 'ub_logo_image', true );
			$logo_html = get_post_meta( $post_id, 'ub_logo_html', true );
			
			if ( $logo_type == null && strlen( trim( $logo_type ) ) == 0 ) {
				$logo_type = 'image';
			}
			if ( $logo_image == null && strlen( trim( $logo_image ) ) == 0 ) {
				$logo_image = '';
			}
			if ( $logo_html == null && strlen( trim( $logo_html ) ) == 0 ) {
				$logo_html = '';
			}
			
			if ( $logo_type == 'image' ) {
				if ( strlen( $logo_image ) > 0 ) {
			
					?><img src="<?php echo $logo_image; ?>" /><?php
				}
			} else if ( $logo_type == 'html' ) {
				echo '<pre>' . $html . '</pre>';
			}
			
			break;

		case 'excerpt':
			$post = get_post( $post_id );
			echo $post->post_excerpt;
			break;
			
		case 'id':
			echo '<b>' . $post_id . '</b>';
			break;
	}
}
add_action( 'manage_badge_posts_custom_column', 'ub_manage_badge_posts_custom_column', 5, 2 );


/**
 * Displays the badhe logo meta box
 */
function display_badge_logo_meta_box( $post ) {
	
	wp_nonce_field( 'ub_badge_logo_meta_box_nonce', 'ub_badge_logo_meta_box_nonce_action' );
	
	$logo_type = get_post_meta( $post->ID, 'ub_logo_type', true );
	$logo_image = get_post_meta( $post->ID, 'ub_logo_image', true );
	$logo_html = get_post_meta( $post->ID, 'ub_logo_html', true );
	
	if ( $logo_type == null && strlen( trim( $logo_type ) ) == 0 ) {
		$logo_type = 'image';
	}
	if ( $logo_image == null && strlen( trim( $logo_image ) ) == 0 ) {
		$logo_image = '';
	}
	if ( $logo_html == null && strlen( trim( $logo_html ) ) == 0 ) {
		$logo_html = '';
	}
	
	?>
	<p><?php _e( 'Add a logo for your badge.', 'user-badges' ); ?>
	<div id="ub-logo-type-container">
		<input type="radio" name="ub-logo-type" value="none"<?php if ( $logo_type == 'none' ) echo ' checked'; ?>><?php _e( 'None', 'user-badges' ); ?></input>
		<input type="radio" name="ub-logo-type" value="image"<?php if ( $logo_type == 'image' ) echo ' checked'; ?>><?php _e( 'Image', 'user-badges' ); ?></input>
		<input type="radio" name="ub-logo-type" value="html"<?php if ( $logo_type == 'html' ) echo ' checked'; ?>><?php _e( 'HTML', 'user-badges' ); ?></input>
	</div>
				
	<div id="ub-logo-image-container"<?php if ( $logo_type != 'image' ) echo ' style="display: none;"'?>>
		<?php 
		if ( strlen( $logo_image ) > 0 ) {
			?><img id="ub-logo-image-preview" src="<?php echo $logo_image; ?>" \><?php
		} 
		?>
		<input type="hidden" name="ub-logo-image" id="ub-logo-image" value="<?php echo $logo_image; ?>" />
		<input type="submit" id="ub-logo-image-upload-btn" class="button" value="<?php _e( 'Upload Image', 'user-badges' ); ?>">
	</div>
	
	<div id="ub-logo-html-container"<?php if ( $logo_type != 'html' ) echo ' style="display: none;"'?>>
		<textarea name="ub-logo-html" rows="5" cols="40" class="widefat"><?php echo $logo_html; ?></textarea>
	</div>			
	<?php
}

/**
 * Adds the badge meta box
 */
function add_badge_meta_boxes( $post_type ) {

	if ( $post_type == 'badge' ) {
		add_meta_box( 'ub_badge_logo_meta_box', __( 'Badge Logo', 'user-badges' ), 'display_badge_logo_meta_box', 'badge', 'normal', 'high' );
	}

}
add_action( 'add_meta_boxes', 'add_badge_meta_boxes' );

/**
 * Saves badge post meta
 * @param unknown $post_id
 * @param $post
 * @param $update
 */
function save_badge_meta( $post_id, $post, $update ) {

	if ( ! isset( $_POST['ub_badge_logo_meta_box_nonce_action'] ) ) {
		return $post_id;
	}
	
	if ( ! wp_verify_nonce( $_POST['ub_badge_logo_meta_box_nonce_action'], 'ub_badge_logo_meta_box_nonce' ) ) {
		return $post_id;
	}
	
	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}
	
	// Check the user's permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}
	
	$logo_type = 'none';
	if ( isset( $_POST['ub-logo-type'] ) ) {
		$logo_type = $_POST['ub-logo-type'];
	}
	
	$logo_image = '';
	if ( isset( $_POST['ub-logo-image'] ) ) {
		$logo_image = $_POST['ub-logo-image'];
	}
	
	$logo_html = '';
	if ( isset( $_POST['ub-logo-html'] ) ) {
		$logo_html = $_POST['ub-logo-html'];
	}
	
	// Update the post meta fields
	update_post_meta( $post_id, 'ub_logo_type', $logo_type );
	update_post_meta( $post_id, 'ub_logo_image', $logo_image );
	update_post_meta( $post_id, 'ub_logo_html', $logo_html );
	
}
add_action( 'save_post', 'save_badge_meta', 10, 3 );