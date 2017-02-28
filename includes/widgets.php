<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * UB User Details Widget
 */
class BROO_User_Details_Widget extends WP_Widget {

	/**
	 * Constructor
	 */

	function __construct( ) {

		$id_base = 'broo_user_badges';
		$name = __( 'Badgearoo User Badges', 'badgearoo' );
		$widget_opts = array(
				'classname' => 'broo-user-badges-widget',
				'description' => __( 'Shows the post author details including any badges and points they have.', 'badgearoo' )
		);
		$control_ops = array( 'width' => 400, 'height' => 350 );

		parent::__construct( $id_base, $name, $widget_opts, $control_ops );
	}

	/**
	 * (non-PHPdoc)
	 * @see WP_Widget::widget()
	 */
	function widget( $args, $instance ) {
		
		global $authordata;
		
		$post_id = url_to_postid( BROO_Utils::get_current_url() );
		
		if ( $post_id == 0 ) {
			return;
		}
		
		setup_postdata( get_post( $post_id ) );
		
		$post_type = get_post_type( $post_id );
		
		$can_show_user_badges_widget = 
				( is_author() || is_singular() ) // wrong page
				&& is_object( $authordata )       // wrong type
				&& isset ( $authordata->ID ) 
				&& $post_type != 'badge';
		
		if ( ! apply_filters( 'broo_can_show_user_badges_widget', $can_show_user_badges_widget, $post_id ) ) {
			return;
		}
			
		$general_settings = (array) get_option( 'broo_general_settings' );
		
		extract( $args );
		
		$user_id = isset( $authordata->ID ) ? $authordata->ID : 0;
		$user_id = apply_filters( 'broo_user_badges_user_id', $user_id, $post_id );
		
		$points = Badgearoo::instance()->api->get_user_points( $user_id );
		$badges = Badgearoo::instance()->api->get_user_badges( $user_id );
		
		// count badges by id
		$badge_count_lookup = array();
		foreach ( $badges as $index => $badge ) {
			if ( ! isset( $badge_count_lookup[$badge->id] ) ) {
				$badge_count_lookup[$badge->id] = 1;
			} else {
				$badge_count_lookup[$badge->id]++;
				unset( $badges[$index] );
			}
		}
		
		$general_settings = (array) get_option( 'broo_general_settings' );	

		$header = empty( $instance['header'] ) ? 'h3' : $instance['header'];
		
		$before_title = '<' . $header . ' class="widget-title">';
		$after_title = '</' . $header . '>';
		
		echo $before_widget;
		
		broo_get_template_part( 'user-badges-widget', null, true, array(
				'badge_theme' => $general_settings['broo_badge_theme'],
				'before_title' => $before_title,
				'after_title' => $after_title,
				'class' => 'broo-user-badges-widget',
				'enable_badge_permalink' => $general_settings['broo_enable_badge_permalink'],
				'user_id' => $user_id,
				'badges' => $badges,
				'points' => $points,
				'badge_count_lookup' => $badge_count_lookup,
		) );
		
		wp_reset_postdata();
		
		echo $after_widget;
	}

	/**
	 * (non-PHPdoc)
	 * @see WP_Widget::update()
	 */
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['header'] = $new_instance['header'];
		return $instance;
	}

	/**
	 * (non-PHPdoc)
	 * @see WP_Widget::form()
	 */
	function form( $instance ) {

		
		$instance = wp_parse_args( (array) $instance, array(
				'title' => '',
				'header' => 'h3'
		) );
		
		// TODO options to:
		// - show name
		// - show biography
		// - type: badges, points or both

		$header = $instance['header'];
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'header' ); ?>"><?php _e( 'Header', 'badgearoo' ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'header' ); ?>" id="<?php echo $this->get_field_id( 'header' ); ?>">
				<?php 
				$header_options = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
				
				foreach ( $header_options as $header_option ) {
					$selected = '';
					if ( $header_option == $header ) {
						$selected = ' selected="selected"';
					}
					echo '<option value="' . $header_option . '" ' . $selected . '>' . strtoupper( $header_option ) . '</option>';
				}
				?>
			</select>
		</p>		
		<?php	
	}
}



/**
 * UB Recent Assignments Widget
 */
class BROO_Recent_Assignments_Widget extends WP_Widget {

	/**
	 * Constructor
	 */

	function __construct( ) {

		$id_base = 'broo_recent_assignments';
		$name = __( 'Badgearoo Recent Assignments', 'badgearoo' );
		$widget_opts = array(
				'classname' => 'broo-recent-assignments-widget',
				'description' => __( 'Shows recent user assignments of badges and points.', 'badgearoo' )
		);
		$control_ops = array( 'width' => 400, 'height' => 350 );

		parent::__construct( $id_base, $name, $widget_opts, $control_ops );
	}

	/**
	 * (non-PHPdoc)
	 * @see WP_Widget::widget()
	 */
	function widget( $args, $instance ) {

		$general_settings = (array) get_option( 'broo_general_settings' );

		extract( $args );

		$header = empty( $instance['header'] ) ? 'h3' : $instance['header'];
		$type = empty( $instance['type'] ) ? '' : $instance['type'];
		$limit = empty( $instance['limit'] ) && ! is_numeric( $instance['limit'] ) ? 5 : intval( $instance['limit'] );
		$current_user = empty( $instance['current_user'] ) ? false : $instance['current_user'];
		
		$user_id = 0;
		if ( $current_user ) {
			$user_id = get_current_user_id();
		}
		
		$assignments = Badgearoo::instance()->api->get_user_assignments( array(
				'user_id' => $user_id,
				'limit' => $limit,
				'type' => $type
		), false );
		
		if ( ! is_array( $assignments ) ) {
			$assignments = array();
		}

		$before_title = '<' . $header . ' class="widget-title">';
		$after_title = '</' . $header . '>';

		$general_settings = (array) get_option( 'broo_general_settings' );
		
		echo $before_widget;
		
		broo_get_template_part( 'recent-assignments-widget', null, true, array(
				'assignments' => $assignments,
				'type' => $type,
				'limit' => $limit,
				'before_title' => $before_title,
				'after_title' => $after_title,
				'class' => 'broo-recent-assignments-widget',
				'badge_theme' => $general_settings['broo_badge_theme'],
				'enable_badge_permalink' => $general_settings['broo_enable_badge_permalink']
		) );

		echo $after_widget;
	}

	/**
	 * (non-PHPdoc)
	 * @see WP_Widget::update()
	 */
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['header'] = $new_instance['header'];
		$instance['limit'] = $new_instance['limit'];
		$instance['type'] = $new_instance['type'];
		
		$instance['current_user'] = false;
		if ( isset( $new_instance['current_user'] ) && ( $new_instance['current_user'] == 'true' ) ) {
			$instance['current_user'] = true;
		}
		
		return $instance;
	}

	/**
	 * (non-PHPdoc)
	 * @see WP_Widget::form()
	 */
	function form( $instance ) {
		
		$instance = wp_parse_args( (array) $instance, array(
				'header' => 'h3',
				'limit' => 5,
				'type' => '',
				'current_user' => false
		) );

		$header = $instance['header'];
		$limit = intval( $instance['limit'] );
		$type = $instance['type'];
		
		$current_user = empty( $instance['current_user'] ) ? false : ( $instance['current_user'] == 'true' );

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit'); ?>"><?php _e( 'Limit', 'multi-rating-pro' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" min="0" value="<?php echo esc_attr( $limit ); ?>" />
		</p>
		<p>
			<input id="<?php echo $this->get_field_id( 'current_user' ); ?>" name="<?php echo $this->get_field_name( 'current_user' ); ?>" type="checkbox" value="true" <?php checked( true, $current_user, true ); ?>/>
			<label for="<?php echo $this->get_field_id( 'current_user' ); ?>"><?php _e( 'Current Logged In User?', 'multi-rating-pro' ); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e( 'Type', 'badgearoo' ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'type' ); ?>" id="<?php echo $this->get_field_id( 'type' ); ?>">
				<option value=""<?php if ( $type == null ) echo ' selected'; ?>><?php _e( 'All types', 'badgearoo' ); ?></option>
				<option value="badge"<?php if ( $type == 'badge' ) echo ' selected'; ?>><?php _e( 'Badge', 'badgearoo' ); ?></option>
				<option value="points"<?php if ( $type == 'points' ) echo ' selected'; ?>><?php _e( 'Points', 'badgearoo' ); ?></option>
			</select>
		</p>	
		<p>
			<label for="<?php echo $this->get_field_id( 'header' ); ?>"><?php _e( 'Header', 'badgearoo' ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'header' ); ?>" id="<?php echo $this->get_field_id( 'header' ); ?>">
				<?php 
				$header_options = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
				
				foreach ( $header_options as $header_option ) {
					$selected = '';
					if ( $header_option == $header ) {
						$selected = ' selected="selected"';
					}
					echo '<option value="' . $header_option . '" ' . $selected . '>' . strtoupper( $header_option ) . '</option>';
				}
				?>
			</select>
		</p>		
		<?php	
	}
}

/**
 * Register widgets
 */
function broo_register_widgets() {
	register_widget( 'BROO_User_Details_Widget' );
	register_widget( 'BROO_Recent_Assignments_Widget' );
}
add_action( 'widgets_init', 'broo_register_widgets' );