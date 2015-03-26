<?php

function ub_manage_badge_posts_columns( $posts_columns ) {
	$post_columns_new_order['badge'] = __( 'Badge Image', 'user-badges' );
	$post_columns_new_order['title'] = $posts_columns['title'];
	$post_columns_new_order['categories'] = $posts_columns['categories'];
	$post_columns_new_order['description'] = __( 'Description', 'user-badges' );
	$post_columns_new_order['date'] = $posts_columns['date'];

	return $post_columns_new_order;
}
add_filter( 'manage_badge_posts_columns', 'ub_manage_badge_posts_columns', 5, 1  );

function ub_manage_badge_posts_custom_column( $column_name, $post_id ){

	switch ( $column_name ){

		case 'badge':
			$attachment_img = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ) );
			echo '<img src="' . $attachment_img[0] . '" widht="' . $attachment_img[1] . '" height="' . $attachment_img[2] . '" />';
			break;

		case 'description':
			$post = get_post( $post_id );
			echo $post->post_excerpt;
			break;
	}
}
add_action( 'manage_badge_posts_custom_column', 'ub_manage_badge_posts_custom_column', 5, 2 );


function ub_badge_posts_table_sorting( $columns ) {
	$columns['description'] = 'description';
	return $columns;
}
add_filter( 'manage_edit-badge_sortable_columns', 'ub_badge_posts_table_sorting' );

function ub_badge_posts_column_orderby( $vars ) {
	if ( isset( $vars['orderby'] ) && ( 'type' == $vars['orderby'] || 'description' == $vars['orderby'] ) ) {
		$vars = array_merge( $vars, array(
				'meta_key' => 'ub_badge_type',
				'orderby' => 'meta_value'
		) );
	}

	return $vars;
}
add_filter( 'request', 'ub_badge_posts_column_orderby' );

// See here http://www.smashingmagazine.com/2013/12/05/modifying-admin-post-lists-in-wordpress/