<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Settings class
*
* @author dpowney
*/
class BROO_Settings {
	
	public $actions_enabled = null;
	public $general_settings = array();
	public $email_settings = array();
	public $bp_settings = array();
	
	/**
	 * Constructor
	 */
	function __construct() {
		
		if ( is_admin() ) {
			add_action( 'admin_init', array( &$this, 'default_settings' ), 10, 0 );
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
		$this->register_email_settings();
		$this->register_bp_settings();
		
	}
	
	function default_settings() {
		
		$this->actions_enabled = (array) get_option( 'broo_actions_enabled' );
		$this->general_settings = (array) get_option( 'broo_general_settings' );
		$this->email_settings = (array) get_option( 'broo_email_settings' );
		$this->bp_settings = (array) get_option( 'broo_bp_settings' );
		
		$this->actions_enabled = apply_filters( 'broo_default_actions_enabled', $this->actions_enabled );
		
		$assignment_moderator_notification_email_template = 
				__( "Hello,", 'badgearoo' ) . "\r\n\r\n"
				. __( "A new assignment #{assignment_id} for \"{display_name}\" requires moderation.", 'badgearoo' ) . "\r\n\r\n"
				. __( "{assignment_details}", 'badgearoo' ) . "\r\n" 
				. __( "Date: {date}", 'badgearoo' ) . "\r\n\r\n" 
				. __( "{assignment_moderation_link}", 'badgearoo' ) . "\r\n\r\n"
				. __( "Thank you.", 'badgearoo' );
		
		$this->email_settings = array_merge( array(
				'broo_assignment_notify_moderators'					=> false,
				'broo_assignment_moderators'						=> get_option( 'admin_email' ),
				'broo_assignment_moderation_notification_from'		=> get_option( 'blogname' ),
				'broo_assignment_moderation_notification_email'		=> get_option( 'admin_email' ),
				'broo_assignment_moderation_notification_subject' 	=> __( 'New Assignment', 'badgearoo' ),
				'broo_assignment_moderation_notification_heading'	=> __( 'New Assignment', 'badgearoo' ),
				'broo_assignment_moderation_notification_template' 	=> $assignment_moderator_notification_email_template,
		), $this->email_settings );
		
		$this->general_settings = array_merge( array(
				'broo_assignment_auto_approve'						=> true,
				'broo_show_user_assignment_modal'					=> true,
				'broo_enable_badge_permalink'						=> true,
				'broo_badge_theme'									=> 'light',
				'broo_user_permalinks'								=> 'author_posts_url',
				'broo_show_badges_inline'							=> false
		), $this->general_settings );
		
		$this->bp_settings = array_merge( array(
				'broo_bp_assignment_summary_placement'				=> 'tab',
				'broo_bp_assignments_activity_stream'				=> true,
				'broo_bp_directory_members_item_recent_assignments'	=> false
		), $this->bp_settings );
		
		update_option( 'broo_actions_enabled', $this->actions_enabled );
		update_option( 'broo_general_settings', $this->general_settings);
		update_option( 'broo_email_settings', $this->email_settings);
		update_option( 'broo_bp_settings', $this->bp_settings);
	}
	
	/**
	 * Register general settings
	 */
	function register_general_settings() {
	
		register_setting( 'broo_general_settings', 'broo_general_settings', array( &$this, 'sanitize_general_settings' ) );
	
		add_settings_section( 'section_general', null, array( &$this, 'section_general_desc' ), 'broo_general_settings' );
		
		$user_permalinks_options = apply_filters( 'broo_user_permalinks_options', array(
				'author_posts_url'		=> __( 'Author Posts', 'badgearoo' )
		) );
		
		$setting_fields = array(
				'broo_badge_theme' => array(
						'title' 	=> __( 'Badge Theme', 'badgearoo' ),
						'callback' 	=> 'field_select',
						'page' 		=> 'broo_general_settings',
						'section' 	=> 'section_general',
						'args' => array(
								'option_name' 	=> 'broo_general_settings',
								'setting_id' 	=> 'broo_badge_theme',
								'label' 		=> __( 'Choose a theme to apply to badges.', 'badgearoo' ),
								'select_options' => array(
										'icon'	=> __( 'Icon', 'badgearoo' ),
										'light' 		=> __( 'Light', 'badgearoo' ),
										'dark' 			=> __( 'Dark', 'badgearoo' ),
										'html' 			=> __( 'Custom HTML', 'badgearoo' )
								)
						)
				),
				'broo_assignment_auto_approve' => array(
						'title' 	=> __( 'Auto Approve Assignments', 'badgearoo' ),
						'callback' 	=> 'field_checkbox',
						'page' 		=> 'broo_general_settings',
						'section' 	=> 'section_general',
						'args' => array(
								'option_name' 	=> 'broo_general_settings',
								'setting_id' 	=> 'broo_assignment_auto_approve',
								'label' 		=> __( 'Check this box if you want new user assignments of badges and points to be approved automatically?', 'badgearoo' )
						)
				
				),
				'broo_show_user_assignment_modal' => array(
						'title' 	=> __( 'New Assignments Popup', 'badgearoo' ),
						'callback' 	=> 'field_checkbox',
						'page' 		=> 'broo_general_settings',
						'section' 	=> 'section_general',
						'args' => array(
								'option_name' 	=> 'broo_general_settings',
								'setting_id' 	=> 'broo_show_user_assignment_modal',
								'label' 		=> __( 'Check this box if you want to show a popup message when users are assigned new badges and points?', 'badgearoo' )
						)
				
				),
				'broo_enable_badge_permalink' => array(
						'title' 	=> __( 'Badge Permalinks', 'badgearoo' ),
						'callback' 	=> 'field_checkbox',
						'page' 		=> 'broo_general_settings',
						'section' 	=> 'section_general',
						'args' => array(
								'option_name' 	=> 'broo_general_settings',
								'setting_id' 	=> 'broo_enable_badge_permalink',
								'label' 		=> __( 'Check this box if you want to enabled badge permalinks?', 'badgearoo' )
						)			
				),
				'broo_user_permalinks' => array(
						'title' 	=> __( 'User Permalinks', 'badgearoo' ),
						'callback' 	=> 'field_select',
						'page' 		=> 'broo_general_settings',
						'section' 	=> 'section_general',
						'args' => array(
								'option_name' 	=> 'broo_general_settings',
								'setting_id' 	=> 'broo_user_permalinks',
								'label' 		=> __( 'Choose a user permalink.', 'badgearoo' ),
								'select_options' => $user_permalinks_options
						)
				),
				'broo_show_badges_inline' => array(
						'title' 	=> __( 'Show Badges Inline', 'badgearoo' ),
						'callback' 	=> 'field_checkbox',
						'page' 		=> 'broo_general_settings',
						'section' 	=> 'section_general',
						'args' => array(
								'option_name' 	=> 'broo_general_settings',
								'setting_id' 	=> 'broo_show_badges_inline',
								'label' 		=> __( 'Check this box if you want to show badges inline i.e. horizontal', 'badgearoo' )
						)	
				),
			
		);

		foreach ( $setting_fields as $setting_id => $setting_data ) {
			// $id, $title, $callback, $page, $section, $args
			add_settings_field( $setting_id, $setting_data['title'], array( &$this, $setting_data['callback'] ), $setting_data['page'], $setting_data['section'], $setting_data['args'] );
		}
	}
	
	/**
	 * Register general settings
	 */
	function register_email_settings() {
	
		register_setting( 'broo_email_settings', 'broo_email_settings', array( &$this, 'sanitize_email_settings' ) );
	
		add_settings_section( 'section_moderation', __( 'Assignment Moderation', 'badgearoo' ), array( &$this, 'section_moderation_desc' ), 'broo_email_settings' );
			
		$setting_fields = array(
				'broo_assignment_notify_moderators' => array(
						'title' 	=> __( 'Enable Assignment Moderator Notifications', 'badgearoo' ),
						'callback' 	=> 'field_checkbox',
						'page' 		=> 'broo_email_settings',
						'section' 	=> 'section_moderation',
						'args' => array(
								'option_name' 	=> 'broo_email_settings',
								'setting_id' 	=> 'broo_assignment_notify_moderators',
								'label' 		=> __( 'Check this box if you want to send email notifications for new assignments of badges or points which require moderation.', 'badgearoo' )
						)
				),
				'broo_assignment_moderators' => array(
						'title' 	=> __( 'Moderators', 'badgearoo' ),
						'callback' 	=> 'field_textarea',
						'page' 		=> 'broo_email_settings',
						'section' 	=> 'section_moderation',
						'args' => array(
								'option_name' 	=> 'broo_email_settings',
								'setting_id' 	=> 'broo_assignment_moderators',
								'label' 		=> __( 'Who should receive the assignment moderation notification?', 'badgearoo' ),
								'footer' 		=> __('Enter the email address(es), one per line.', 'badgearoo' )
						)
				),
				'broo_assignment_moderation_notification_from' => array(
						'title' 	=> __( 'From Name', 'badgearoo' ),
						'callback' 	=> 'field_input',
						'page' 		=> 'broo_email_settings',
						'section' 	=> 'section_moderation',
						'args' => array(
								'option_name' 	=> 'broo_email_settings',
								'setting_id' 	=> 'broo_assignment_moderation_notification_from',
								'label' 		=> __( 'The name assignment moderation notifications are said to come from.', 'badgearoo' )
						)
				),
				'broo_assignment_moderation_notification_email' => array(
						'title' 	=> __( 'From Email', 'badgearoo' ),
						'callback' 	=> 'field_input',
						'page' 		=> 'broo_email_settings',
						'section' 	=> 'section_moderation',
						'args' => array(
								'option_name' 	=> 'broo_email_settings',
								'setting_id' 	=> 'broo_assignment_moderation_notification_email',
								'label' 		=> __( 'Email to send assignment moderation notifications from.', 'badgearoo' )
						)
				),
				'broo_assignment_moderation_notification_subject' => array(
						'title' 	=> __( 'Email Subject', 'badgearoo' ),
						'callback' 	=> 'field_input',
						'page' 		=> 'broo_email_settings',
						'section' 	=> 'section_moderation',
						'args' => array(
								'option_name' 	=> 'broo_email_settings',
								'setting_id' 	=> 'broo_assignment_moderation_notification_subject',
								'label' 		=> __( 'Enter the subject line for the assignment moderation notification email', 'badgearoo' )
						)
				),
				'broo_assignment_moderation_notification_heading' => array(
						'title' 	=> __( 'Email Heading', 'badgearoo' ),
						'callback' 	=> 'field_input',
						'page' 		=> 'broo_email_settings',
						'section' 	=> 'section_moderation',
						'args' => array(
								'option_name' 	=> 'broo_email_settings',
								'setting_id' 	=> 'broo_assignment_moderation_notification_heading',
								'label' 		=> __( 'Enter the heading for the assignment moderation notification email', 'badgearoo' )
						)
				),
				'broo_assignment_moderation_notification_template' => array(
						'title' 	=> __( 'Email Template', 'badgearoo' ),
						'callback' 	=> 'field_editor',
						'page' 		=> 'broo_email_settings',
						'section' 	=> 'section_moderation',
						'args' => array(
								'option_name' 	=> 'broo_email_settings',
								'setting_id' 	=> 'broo_assignment_moderation_notification_template',
								'footer' 		=> __( 'Enter the email that is sent to a moderator to notify them new assignment required approval. HTML is accepted. Available template tags:<br />'
										. '{display_name} - The user\'s display name<br />'
										. '{username} - The user\'s username on the site<br />'
										. '{user_email} - The user\'s email address<br />'
										. '{site_name} - Your site name<br />'
										. '{assignment_id} - The unique ID number for this assignment<br />'
										. '{date} - The date of the assignment<br />'
										. '{assignment_details} - A list of badges and points<br />'
										. '{assignment_moderation_link} - Link to assignments page', 'badgearoo' ),
						)
				),
		);
	
		foreach ( $setting_fields as $setting_id => $setting_data ) {
			// $id, $title, $callback, $page, $section, $args
			add_settings_field( $setting_id, $setting_data['title'], array( &$this, $setting_data['callback'] ), $setting_data['page'], $setting_data['section'], $setting_data['args'] );
		}
	}
	
	/**
	 * Register general settings
	 */
	function register_bp_settings() {
	
		register_setting( 'broo_bp_settings', 'broo_bp_settings', array( &$this, 'sanitize_bp_settings' ) );
	
		add_settings_section( 'section_bp', null, array( &$this, 'section_bp_desc' ), 'broo_bp_settings' );
		
		$setting_fields = array(
				'broo_bp_assignment_summary_placement' => array(
						'title' 	=> __( 'Assignment Summary Placement', 'badgearoo' ),
						'callback' 	=> 'field_select',
						'page' 		=> 'broo_bp_settings',
						'section' 	=> 'section_bp',
						'args' => array(
								'option_name' 	=> 'broo_bp_settings',
								'setting_id' 	=> 'broo_bp_assignment_summary_placement',
								'label' 		=> __( 'Where do you want to display a summary of badges & points assigned to members?', 'badgearoo' ),
								'select_options' => array(
										'tab' 				=> __( 'New Member Tab', 'badgearoo' ),
										'header'	=> __( 'Member Header', 'badgearoo' ),
										'none' 				=> __( 'Do not show', 'badgearoo' )
								)
						)
				),
				'broo_bp_assignments_activity_stream' => array(
						'title' 	=> __( 'Assignments Activity Stream', 'badgearoo' ),
						'callback' 	=> 'field_checkbox',
						'page' 		=> 'broo_bp_settings',
						'section' 	=> 'section_bp',
						'args' => array(
								'option_name' 	=> 'broo_bp_settings',
								'setting_id' 	=> 'broo_bp_assignments_activity_stream',
								'label' 		=> __( 'Check this box if you want to display new assignments in member activity streams?', 'badgearoo' )
						)
				),
				'broo_bp_directory_members_item_recent_assignments' => array(
						'title' 	=> __( 'Members Directory Recent Assignments', 'badgearoo' ),
						'callback' 	=> 'field_checkbox',
						'page' 		=> 'broo_bp_settings',
						'section' 	=> 'section_bp',
						'args' => array(
								'option_name' 	=> 'broo_bp_settings',
								'setting_id' 	=> 'broo_bp_directory_members_item_recent_assignments',
								'label' 		=> __( 'Check this box if you want to show each member\'s 3 most recent assignments in the member directory list?', 'badgearoo' )
						)
				),
				
		);
	
		foreach ( $setting_fields as $setting_id => $setting_data ) {
			// $id, $title, $callback, $page, $section, $args
			add_settings_field( $setting_id, $setting_data['title'], array( &$this, $setting_data['callback'] ), $setting_data['page'], $setting_data['section'], $setting_data['args'] );
		}
	}
	
	/**
	 * Email section desciption
	 */
	function section_moderation_desc() {	
	}
	
	/**
	 * General section desciption
	 */
	function section_general_desc() {
	}
	
	/**
	 * BuddyPress section desciption
	 */
	function section_bp_desc() {
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
	
	/**
	 * Editor field
	 * 
	 * @param unknown $args
	 */
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
	 * Color picker field
	 * 
	 * @param unknown $args
	 */
	function field_color_picker( $args ) {
		$settings = (array) get_option( $args['option_name' ] );
		?>
		<input type="text" class="color-picker" name="<?php echo $args['option_name']; ?>[<?php echo $args['setting_id']; ?>]" value="<?php echo $settings[$args['setting_id']]; ?>" />
		<?php 
	}
	
	/**
	 * Color picker field
	 *
	 * @param unknown $args
	 */
	function field_select( $args ) {
		$settings = (array) get_option( $args['option_name' ] );
		$value = $settings[$args['setting_id']];
		?>
		<select name="<?php echo $args['option_name']; ?>[<?php echo $args['setting_id']; ?>]">
			<?php 
			foreach ( $args['select_options'] as $option_value => $option_label ) {
				$selected = '';
				if ( $value == $option_value ) {
					$selected = 'selected="selected"';
				}
				echo '<option value="' . $option_value . '" ' . $selected . '>' . $option_label . '</option>';
			}
			?>
		</select>
		<label><?php echo $args['label']; ?></label>
		<?php 
	}
	
	/**
	 * Sanitize the general settings
	 *
	 * @param $input
	 * @return boolean
	 */
	function sanitize_general_settings( $input ) {
		
		if ( isset( $input['broo_show_user_assignment_modal'] ) && $input['broo_show_user_assignment_modal'] == 'true' ) {
			$input['broo_show_user_assignment_modal'] = true;
		} else {
			$input['broo_show_user_assignment_modal'] = false;
		}
		
		if ( isset( $input['broo_enable_badge_permalink'] ) && $input['broo_enable_badge_permalink'] == 'true' ) {
			$input['broo_enable_badge_permalink'] = true;
		} else {
			$input['broo_enable_badge_permalink'] = false;
		}
		
		if ( isset( $input['broo_assignment_auto_approve'] ) && $input['broo_assignment_auto_approve'] == 'true' ) {
			$input['broo_assignment_auto_approve'] = true;
		} else {
			$input['broo_assignment_auto_approve'] = false;
		}
		
		if ( isset( $input['broo_show_badges_inline'] ) && $input['broo_show_badges_inline'] == 'true' ) {
			$input['broo_show_badges_inline'] = true;
		} else {
			$input['broo_show_badges_inline'] = false;
		}
		
		return $input;
	}
	
	/**
	 * Sanitize the email settings
	 *
	 * @param $input
	 * @return boolean
	 */
	function sanitize_email_settings( $input ) {
	
		if ( isset( $input['broo_assignment_notify_moderators'] ) && $input['broo_assignment_notify_moderators'] == 'true' ) {
			$input['broo_assignment_notify_moderators'] = true;
		} else {
			$input['broo_assignment_notify_moderators'] = false;
		}
	
		$assignment_moderators = preg_split( '/[\r\n,]+/', $input['broo_assignment_moderators'], -1, PREG_SPLIT_NO_EMPTY );
		foreach ( $assignment_moderators as $email ) {
			if (! is_email( $email ) ) {
				add_settings_error( 'broo_email_settings', 'invalid_assignment_moderators', sprintf( __( 'Moderator email  "%s" is invalid.', 'badgearoo' ), $email ) );
				break;
			}
		}
	
		return $input;
	}
	
	/**
	 * Sanitize the BuddyPress settings
	 *
	 * @param $input
	 * @return boolean
	 */
	function sanitize_bp_settings( $input ) {
		
		if ( isset( $input['broo_bp_assignments_activity_stream'] ) && $input['broo_bp_assignments_activity_stream'] == 'true' ) {
			$input['broo_bp_assignments_activity_stream'] = true;
		} else {
			$input['broo_bp_assignments_activity_stream'] = false;
		}
		
		if ( isset( $input['broo_bp_directory_members_item_recent_assignments'] ) && $input['broo_bp_directory_members_item_recent_assignments'] == 'true' ) {
			$input['broo_bp_directory_members_item_recent_assignments'] = true;
		} else {
			$input['broo_bp_directory_members_item_recent_assignments'] = false;
		}
	
		return $input;
	}
}