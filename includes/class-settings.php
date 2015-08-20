<?php

/**
 * Settings class
*
* @author dpowney
*/
class UB_Settings {
	
	public $actions_enabled = null;
	public $general_settings = array();
	
	/**
	 * Constructor
	 */
	function __construct() {
		
		if ( is_admin() ) {
			add_action( 'admin_init', array( &$this, 'default_settings' ) );
		}
		
		if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
			add_action('admin_init', array( &$this, 'register_settings' ) );
		}
	}
	
	/**
	 * Reisters settings
	 */
	function register_settings() {

		$this->register_general_settings();
		
	}
	
	function default_settings() {
		
		$this->actions_enabled = (array) get_option( 'ub_actions_enabled' );
		$this->general_settings = (array) get_option( 'ub_general_settings' );
		
		$this->actions_enabled = apply_filters( 'ub_default_actions_enabled', $this->actions_enabled );
		
		$assignment_moderator_notification_email_template = __( 'Hello,<br /><br />'
				. 'A new assignment #{assignment_id} for "{display_name}" requires moderation.<br /><br />'
				. '{assignment_details}<br />Date: {date}<br /><br />{assignment_moderation_link}<br /><br />'
				. 'Thank you.', 'user-badges' );
		
		$this->general_settings = array_merge( array(
				'ub_assignment_auto_approve'					=> true,
				'ub_assignment_notify_moderators'				=> false,
				'ub_assignment_moderators'						=> get_option( 'admin_email' ),
				'ub_assignment_moderation_notification_from'	=> get_option( 'blogname' ),
				'ub_assignment_moderation_notification_email'	=> get_option( 'admin_email' ),
				'ub_assignment_moderation_notification_subject' => __( 'New Assignment', 'user-badges' ),
				'ub_assignment_moderation_notification_heading'	=> __( 'New Assignment', 'user-badges' ),
				'ub_assignment_moderation_notification_template' => $assignment_moderator_notification_email_template,
				'ub_show_user_assignment_modal'					=> true
		), $this->general_settings );
		
		update_option( 'ub_actions_enabled', $this->actions_enabled );
		update_option( 'ub_general_settings', $this->general_settings);
	}
	
	/**
	 * Register general settings
	 */
	function register_general_settings() {
	
		register_setting( 'ub_general_settings', 'ub_general_settings', array( &$this, 'sanitize_general_settings' ) );
	
		add_settings_section( 'section_general', __( 'General', 'user-badges' ), array( &$this, 'section_general_desc' ), 'ub_general_settings' );
		add_settings_section( 'section_moderation', __( 'Moderation', 'user-badges' ), array( &$this, 'section_moderation_desc' ), 'ub_general_settings' );
		
		$setting_fields = array(
			'ub_show_user_assignment_modal' => array(
					'title' 	=> __( 'Show Assignment Modal', 'user-badges' ),
					'callback' 	=> 'field_checkbox',
					'page' 		=> 'ub_general_settings',
					'section' 	=> 'section_general',
					'args' => array(
							'option_name' 	=> 'ub_general_settings',
							'setting_id' 	=> 'ub_show_user_assignment_modal',
							'label' 		=> __( 'Do you want the user to see a popup message on page load upon assignment of badges and points?', 'user-badges' )
					)
			
			),
			'ub_assignment_auto_approve' => array( 
					'title' 	=> __( 'Auto Approve Assignments', 'user-badges' ),
					'callback' 	=> 'field_checkbox',
					'page' 		=> 'ub_general_settings',
					'section' 	=> 'section_moderation',
					'args' => array( 
							'option_name' 	=> 'ub_general_settings',
							'setting_id' 	=> 'ub_assignment_auto_approve',
							'label' 		=> __( 'Automatically approve user assignment of new badges and points.', 'user-badges' )
					)
					 
			),
			'ub_assignment_notify_moderators' => array(
					'title' 	=> __( 'Enable Assignment Moderator Notifications', 'user-badges' ),
					'callback' 	=> 'field_checkbox',
					'page' 		=> 'ub_general_settings',
					'section' 	=> 'section_moderation',
					'args' => array(
							'option_name' 	=> 'ub_general_settings',
							'setting_id' 	=> 'ub_assignment_notify_moderators',
							'label' 		=> __( 'Send email notifications for new assignments of badges or points which require moderation.', 'user-badges' )
					)
			),
			'ub_assignment_moderators' => array(
					'title' 	=> __( 'Moderators', 'user-badges' ),
					'callback' 	=> 'field_textarea',
					'page' 		=> 'ub_general_settings',
					'section' 	=> 'section_moderation',
					'args' => array(
							'option_name' 	=> 'ub_general_settings',
							'setting_id' 	=> 'ub_assignment_moderators',
							'label' 		=> __( 'Who should receive the assignment moderation notification?', 'user-badges' ),
							'footer' 		=> __('Enter the email address(es), one per line.', 'user-badges' )
					)
			),
			'ub_assignment_moderation_notification_from' => array(
				'title' 	=> __( 'From Name', 'user-badges' ),
					'callback' 	=> 'field_input',
					'page' 		=> 'ub_general_settings',
					'section' 	=> 'section_moderation',
					'args' => array(
							'option_name' 	=> 'ub_general_settings',
							'setting_id' 	=> 'ub_assignment_moderation_notification_from',
							'label' 		=> __( 'The name assignment moderation notifications are said to come from.', 'user-badges' )
				)
			),
			'ub_assignment_moderation_notification_email' => array(
					'title' 	=> __( 'From Email', 'user-badges' ),
					'callback' 	=> 'field_input',
					'page' 		=> 'ub_general_settings',
					'section' 	=> 'section_moderation',
					'args' => array(
							'option_name' 	=> 'ub_general_settings',
							'setting_id' 	=> 'ub_assignment_moderation_notification_email',
							'label' 		=> __( 'Email to send assignment moderation notifications from.', 'user-badges' )
					)
			),
			'ub_assignment_moderation_notification_subject' => array(
					'title' 	=> __( 'Email Subject', 'user-badges' ),
					'callback' 	=> 'field_input',
					'page' 		=> 'ub_general_settings',
					'section' 	=> 'section_moderation',
					'args' => array(
							'option_name' 	=> 'ub_general_settings',
							'setting_id' 	=> 'ub_assignment_moderation_notification_subject',
							'label' 		=> __( 'Enter the subject line for the assignment moderation notification email', 'user-badges' )
					)
			),
			'ub_assignment_moderation_notification_heading' => array(
					'title' 	=> __( 'Email Heading', 'user-badges' ),
					'callback' 	=> 'field_input',
					'page' 		=> 'ub_general_settings',
					'section' 	=> 'section_moderation',
					'args' => array(
							'option_name' 	=> 'ub_general_settings',
							'setting_id' 	=> 'ub_assignment_moderation_notification_heading',
							'label' 		=> __( 'Enter the heading for the assignment moderation notification email', 'user-badges' )
					)
			),
			'ub_assignment_moderation_notification_template' => array(
					'title' 	=> __( 'Email Template', 'user-badges' ),
					'callback' 	=> 'field_editor',
					'page' 		=> 'ub_general_settings',
					'section' 	=> 'section_moderation',
					'args' => array(
							'option_name' 	=> 'ub_general_settings',
							'setting_id' 	=> 'ub_assignment_moderation_notification_template',
							'footer' 		=> __( 'Enter the email that is sent to a moderator to notify them new assignment required approval. HTML is accepted. Available template tags:<br />'
									. '{display_name} - The user\'s display name<br />'
									. '{username} - The user\'s username on the site<br />'
									. '{user_email} - The user\'s email address<br />'
									. '{site_name} - Your site name<br />'
									. '{assignment_id} - The unique ID number for this assignment<br />'
									. '{date} - The date of the assignment<br />'
									. '{assignment_details} - A list of badges and points<br />'
									. '{assignment_moderation_link} - Link to assignments page', 'user-badges' ),
					)
			),
		);

		foreach ( $setting_fields as $setting_id => $setting_data ) {
			// $id, $title, $callback, $page, $section, $args
			add_settings_field( $setting_id, $setting_data['title'], array( &$this, $setting_data['callback'] ), $setting_data['page'], $setting_data['section'], $setting_data['args'] );
		}
	}
	
	/**
	 * General section desciption
	 */
	function section_moderation_desc() {
		?>
		<p><?php _e( 'Moderation settings for user assignment of badges and points.', 'user-badges' ); ?></p>
		<?php
	}
	
	/**
	 * Misc section desciption
	 */
	function section_general_desc() {
		?>
			<p><?php _e( 'Misc settings.', 'user-badges' ); ?></p>
			<?php
		}
	
	/**
	 * Checkbox setting
	 */
	function field_checkbox( $args ) {
		$settings = (array) get_option( $args['option_name' ] );
		?>
		<input type="checkbox" name="<?php echo $args['option_name']; ?>[<?php echo $args['setting_id']; ?>]" value="true" <?php checked( true, $settings[$args['setting_id']], true ); ?> />
		<label><?php echo $args['label']; ?></label>
		<?php 
	}
	
	/**
	 * Checkbox setting
	 */
	function field_input( $args ) {
		$settings = (array) get_option( $args['option_name' ] );
		?>
		<input class="regular-text" type="text" name="<?php echo $args['option_name']; ?>[<?php echo $args['setting_id']; ?>]" value="<?php echo $settings[$args['setting_id']]; ?>" />
		<label><?php echo $args['label']; ?></label>
		<?php 
	}
	
	/**
	 * 
	 */
	function field_textarea( $args ) {
		$settings = (array) get_option( $args['option_name' ] );
		?>
		<p><?php echo $args['label']; ?></p><br />
		<textarea name="<?php echo $args['option_name']; ?>[<?php echo $args['setting_id']; ?>]" rows="5" cols="75"><?php echo $settings[$args['setting_id']]; ?></textarea>
		<p><?php echo $args['footer']; ?></p><br />
		<?php
	}
	
	function field_editor( $args ) {
		
		$settings = (array) get_option( $args['option_name' ] );
		
		if ( ! empty( $args['label' ] ) ) {
			?>
			<p><?php echo $args['label']; ?></p><br />
			<?php
		}
		
		wp_editor( $settings[$args['setting_id']], $args['setting_id'], array(
				'textarea_name' => $args['setting_id'],
				'editor_class' => ''
		) );
		
		echo ( ! empty( $args['footer'] ) ) ? '<br/><p class="description">' . $args['footer'] . '</p>' : '';
	}
	
	/**
	 * Sanitize the general settings
	 *
	 * @param $input
	 * @return boolean
	 */
	function sanitize_general_settings( $input ) {
		
		if ( isset( $input['ub_show_user_assignment_modal'] ) && $input['ub_show_user_assignment_modal'] == 'true' ) {
			$input['ub_show_user_assignment_modal'] = true;
		} else {
			$input['ub_show_user_assignment_modal'] = false;
		}
		
		if ( isset( $input['ub_assignment_auto_approve'] ) && $input['ub_assignment_auto_approve'] == 'true' ) {
			$input['ub_assignment_auto_approve'] = true;
		} else {
			$input['ub_assignment_auto_approve'] = false;
		}
		
		if ( isset( $input['ub_assignment_notify_moderators'] ) && $input['ub_assignment_notify_moderators'] == 'true' ) {
			$input['ub_assignment_notify_moderators'] = true;
		} else {
			$input['ub_assignment_notify_moderators'] = false;
		}
		
		$assignment_moderators = preg_split( '/[\r\n,]+/', $input['ub_assignment_moderators'], -1, PREG_SPLIT_NO_EMPTY );
		foreach ( $assignment_moderators as $email ) {
			if (! is_email( $email ) ) {
				add_settings_error( 'ub_general_settings', 'invalid_assignment_moderators', sprintf( __( 'Moderator email  "%s" is invalid.', 'user-badges' ), $email ) );
				break;
			}
		}
		
		return $input;
	}
}