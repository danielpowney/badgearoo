<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


function broo_manage_badge_posts_columns( $posts_columns ) {
	$post_columns_new_order['cb'] = $posts_columns['cb'];
	$post_columns_new_order['id'] = __( 'ID', 'badgearoo' );
	$post_columns_new_order['title'] = $posts_columns['title'];
	$post_columns_new_order['categories'] = $posts_columns['categories'];
	$post_columns_new_order['date'] = $posts_columns['date'];

	return $post_columns_new_order;
}
add_filter( 'manage_badge_posts_columns', 'broo_manage_badge_posts_columns', 5, 1  );

function broo_manage_badge_posts_custom_column( $column_name, $post_id ){

	switch ( $column_name ){

		case 'excerpt':
			$post = get_post( $post_id );
			echo $post->post_excerpt;
			break;
			
		case 'id':
			echo '<b>' . $post_id . '</b>';
			break;
	}
}
add_action( 'manage_badge_posts_custom_column', 'broo_manage_badge_posts_custom_column', 5, 2 );


/**
 * Displays the badge style meta box
 */
function display_badge_theme_meta_box( $post ) {
	
	wp_nonce_field( 'broo_badge_theme_meta_box_nonce', 'broo_badge_theme_meta_box_nonce_action' );

	$general_settings = (array) get_option( 'broo_general_settings' );
	
	$badge_color = get_post_meta( $post->ID, 'broo_badge_color', true );
	if ( ! isset( $badge_color ) || $badge_color == '' ) {
		$badge_color = '#fc0';
	}
	
	$badge_theme_no_color = get_post_meta( $post->ID, 'broo_badge_theme_no_color', true );
		
	$badge_icon = get_post_meta( $post->ID, 'broo_badge_icon', true );
	$badge_html = get_post_meta( $post->ID, 'broo_badge_html', true );
		
	?>

	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><?php _e( 'Badge Color', 'badgearoo' ); ?></th>
				<td>
					<input type="text" class="color-picker" name="broo-badge-color" value="<?php echo $badge_color; ?>" />					
					
					<p><input type="checkbox" name="broo-badge-no-color" value="true" <?php checked( true, $badge_theme_no_color, true ); ?> />
					<label><?php _e( 'Ignore badge color.', 'badgearoo' ); ?></label></p>
					<p class="description"><?php _e( 'Used by light and dark badge themes to show a badge color. E.g. #c96 for Bronze, #c5c5c5 for Silver and #fc0 for Gold.', 'badgearoo' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Badge Icon', 'badgearoo' ); ?></th>
				<td>
					<input type="submit" id="broo-badge-icon-upload-btn" class="button" value="<?php _e( 'Upload', 'badgearoo' ); ?>">
					
					<?php 
					if ( strlen( $badge_icon ) > 0 ) {
						?>
						<img id="broo-badge-icon-preview" src="<?php echo $badge_icon; ?>" />
						<?php
					} 
					?>
					<p class="description"><?php _e( 'This is used by the icon and title theme. Upload a icon image for this badge. A badge icon is optional. Recommended icon size 32 pixels by 32 pixels.', 'badgearoo' ); ?></p>
					<input type="hidden" name="broo-badge-icon" id="broo-badge-icon" value="<?php echo $badge_icon; ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Custom HTML', 'badgearoo' ); ?></th>
				<td>
					<textarea name="broo-badge-html" rows="5" cols="40" class="widefat"><?php echo $badge_html; ?></textarea>
					
					<p class="description">
						<?php _e( 'This is used for the custom HTML theme. Available template tags: <br>'
								. '{title} - The badge title.<br>'
								. '{badge_id} - The unique ID of this badge.<br>'
								. '{excerpt} - The badge excerpt.<br>'
								. '{content} - The badge content.<br />'
								. '{badge_color} - The badge color.<br />'
								. '{badge_icon} - The badge icon.<br />'
								. '{permalink} - The badge permalink.', 'badgearoo' ); ?>
					</p>
				</td>
			</tr>
		</tbody>
	</table>
	
	<?php
}

/**
 * Adds the badge meta box
 */
function add_badge_meta_boxes( $post_type ) {

	if ( $post_type == 'badge' ) {
		add_meta_box( 'broo_badge_theme_meta_box', __( 'Badge Theme', 'badgearoo' ), 'display_badge_theme_meta_box', 'badge', 'normal', 'high' );
	}

}
add_action( 'add_meta_boxes', 'add_badge_meta_boxes' );

/**
 * Saves theme badge post meta
 * @param unknown $post_id
 * @param $post
 * @param $update
 */
function save_badge_meta( $post_id, $post, $update ) {

	if ( ! isset( $_POST['broo_badge_theme_meta_box_nonce_action'] ) ) {
		return $post_id;
	}
	
	if ( ! wp_verify_nonce( $_POST['broo_badge_theme_meta_box_nonce_action'], 'broo_badge_theme_meta_box_nonce' ) ) {
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
	
	$badge_icon = '';
	if ( isset( $_POST['broo-badge-icon'] ) ) {
		$badge_icon = $_POST['broo-badge-icon'];
	}
	
	$badge_html = '';
	if ( isset( $_POST['broo-badge-html'] ) ) {
		$badge_html = $_POST['broo-badge-html'];
	}
	
	$badge_color = '';
	if ( isset( $_POST['broo-badge-color'] ) ) {
		$badge_color = $_POST['broo-badge-color'];
	}
	
	// Update the post meta fields
	update_post_meta( $post_id, 'broo_badge_icon', $badge_icon );
	update_post_meta( $post_id, 'broo_badge_html', $badge_html );
	update_post_meta( $post_id, 'broo_badge_color', $badge_color );
	
}
add_action( 'save_post', 'save_badge_meta', 10, 3 );