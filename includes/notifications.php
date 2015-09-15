<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Notifications
 */
function broo_assignment_moderation_notification( $assignment_id, $condition_id, $user_id, $type, $value, $created_dt, $status ) {
	
	$general_settings = (array) get_option( 'broo_general_settings' );
	$email_settings = (array) get_option( 'broo_email_settings' );
	
	if ( ! $general_settings['broo_assignment_auto_approve'] && $email_settings['broo_assignment_notify_moderators'] ) {
			
		$from_email = $email_settings['broo_assignment_moderation_notification_email'];
		$from_name = $email_settings['broo_assignment_moderation_notification_from'];
		$subject = $email_settings['broo_assignment_moderation_notification_subject'];
		$heading = $email_settings['broo_assignment_moderation_notification_heading'];
		$message = $email_settings['broo_assignment_moderation_notification_template'];
		
		$site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		/**
		 * Substitute the following template tags:
		 * {display_name} - The user's display name
		 * {username} - The user's username on the site
		 * {user_email} - The user's email address
		 * {site_name} - Your site name
		 * {assignment_id} - The unique ID number for this assignment
		 * {date} - The date of the assignment
		 * {assignment_details} - Badge name or points
		 * {assignment_moderation_link} - Link to assignments page
		 */
		$site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$display_name = get_the_author_meta( 'display_name', $user_id );
		$user_email = get_the_author_meta( 'user_email', $user_id );
		$username = get_the_author_meta( 'user_login', $user_id );
		$date = date( 'F j, Y, g:ia', strtotime( $created_dt ) );
		$assignment_moderation_link = admin_url() . 'edit.php?post_type=badge&page=broo_assignments&status=pending';
		
		$assignment_details = '';
		if ( $type == 'badge' ) {
			$badge = Badgearoo::instance()->api->get_badge( $value );
			$assignment_details = sprintf( __( 'Badge: %s', 'badgearoo' ), $badge->title ); 
		} else {
			$assignment_details = sprintf( __( 'Points: %d', 'badgearoo' ), $value );
		}
		
		$template_tags = array(
				'{display_name}' => trim( $display_name ),
				'{username}' => trim( $username ),
				'{user_email}' => trim( $user_email ),
				'{site_name}' => trim( $site_name ),
				'{assignment_id}' => $assignment_id,
				'{date}' => $date,
				'{assignment_details}' => $assignment_details,
				'{assignment_moderation_link}' => $assignment_moderation_link
		);
		
		foreach ( $template_tags as $string => $value ) {
			$message = str_replace( $string, $value, $message );
		}
		
		$message = str_replace( "\r\n", "<br />", $message );
			
		$emails = array();
		$moderators = preg_split( '/[\r\n,]+/', $email_settings['broo_assignment_moderators'], -1, PREG_SPLIT_NO_EMPTY );
		foreach ( $moderators as $moderator_email ) {
			if ( is_email( $moderator_email ) ) {
				array_push( $emails, $moderator_email );
			}
		}
	
		$headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';
		$headers[] = 'Content-Type: text/html';

		add_filter( 'wp_mail_content_type', 'broo_set_html_content_type' );
		foreach ( $emails as $moderator_email ) {
			@wp_mail( $moderator_email, wp_specialchars_decode( $subject ), $message, $message_headers );
		}
		remove_filter( 'wp_mail_content_type', 'broo_set_html_content_type' );
		
	}
}
add_action( 'broo_add_user_assignment', 'broo_assignment_moderation_notification', 10, 7 );

/**
 * 
 * @param unknown $content_type
 * @return string
 */
function broo_set_html_content_type( $content_type ) {
	return 'text/html';
}