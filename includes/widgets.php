<?php
/**
 * UB User Details Widget
 */
class UB_User_Details_Widget extends WP_Widget {

	/**
	 * Constructor
	 */

	function __construct( ) {

		$id_base = 'ub_user_badges';
		$name = __( 'User Badges', 'user-badges' );
		$widget_opts = array(
				'classname' => 'ub-user-badges-widget',
				'description' => __( 'Shows the post author details including any badges and points they have.', 'user-badges' )
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
		
		$post_id = url_to_postid( UB_Utils::get_current_url() );
		
		setup_postdata( get_post( $post_id ) );
		
		if ( ! ( is_author() || is_singular() ) // wrong page
				|| ! is_object( $authordata )       // wrong type
				|| ! isset ( $authordata->ID ) ) {   // wrong object {
			return; // Nothing to do.
		}
		
		$general_settings = (array) get_option( 'ub_general_settings' );
		
		extract( $args );

		$header = empty( $instance['header'] ) ? 'h3' : $instance['header'];
		
		$before_title = '<' . $header . ' class="widget-title">';
		$after_title = '</' . $header . '>';
		
		echo $before_widget;
		
		ub_get_template_part( 'user-badges-widget', null, true, array(
				'badge_theme' => $general_settings['ub_badge_theme'],
				'before_title' => $before_title,
				'after_title' => $after_title,
				'class' => 'user-badges-widget',
				'enable_badge_permalink' => $general_settings['ub_enable_badge_permalink']
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

		$header = $instance['header'];
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'header' ); ?>"><?php _e( 'Header', 'user-badges' ); ?></label>
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
class UB_Recent_Assignments_Widget extends WP_Widget {

	/**
	 * Constructor
	 */

	function __construct( ) {

		$id_base = 'ub_recent_assignments';
		$name = __( 'Recent Assignments', 'user-badges' );
		$widget_opts = array(
				'classname' => 'ub-recent-assignments-widget',
				'description' => __( 'Shows recent user assignments of badges and points.', 'user-badges' )
		);
		$control_ops = array( 'width' => 400, 'height' => 350 );

		parent::__construct( $id_base, $name, $widget_opts, $control_ops );
	}

	/**
	 * (non-PHPdoc)
	 * @see WP_Widget::widget()
	 */
	function widget( $args, $instance ) {

		$general_settings = (array) get_option( 'ub_general_settings' );

		extract( $args );

		$header = empty( $instance['header'] ) ? 'h3' : $instance['header'];
		$type = empty( $instance['type'] ) ? '' : $instance['type'];
		$limit = empty( $instance['limit'] ) && ! is_numeric( $instance['limit'] ) ? 5 : intval( $instance['limit'] );
		$current_user = empty( $instance['current_user'] ) ? false : $instance['current_user'];
		
		$user_id = 0;
		if ( $current_user ) {
			$user_id = get_current_user_id();
		}
		
		$assignments = User_Badges::instance()->api->get_assignments( array(
				'user_id' => $user_id,
				'limit' => $limit,
				'type' => $type
		), false );
		
		if ( ! is_array( $assignments ) ) {
			$assignments = array();
		}

		$before_title = '<' . $header . ' class="widget-title">';
		$after_title = '</' . $header . '>';

		$general_settings = (array) get_option( 'ub_general_settings' );
		
		echo $before_widget;
		
		ub_get_template_part( 'recent-assignments-widget', null, true, array(
				'assignments' => $assignments,
				'type' => $type,
				'limit' => $limit,
				'before_title' => $before_title,
				'after_title' => $after_title,
				'class' => 'ub-recent-assignments-widget',
				'badge_theme' => $general_settings['ub_badge_theme'],
				'enable_badge_permalink' => $general_settings['ub_enable_badge_permalink']
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
		$type = $instance['type'];
		$limit = intval( $instance['limit'] );
		
		$current_user = empty( $instance['current_user'] ) ? false : boolval( $instance['current_user'] );

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
			<label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e( 'Type', 'user-badges' ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'type' ); ?>" id="<?php echo $this->get_field_id( 'type' ); ?>">
				<option value=""<?php if ( $type == null ) echo ' selected'; ?>><?php _e( 'All types', 'user-badges' ); ?></option>
				<option value="badge"<?php if ( $type == 'badges' ) echo ' selected'; ?>><?php _e( 'Badge', 'user-badges' ); ?></option>
				<option value="points"<?php if ( $type == 'points' ) echo ' selected'; ?>><?php _e( 'Points', 'user-badges' ); ?></option>
			</select>
		</p>	
		<p>
			<label for="<?php echo $this->get_field_id( 'header' ); ?>"><?php _e( 'Header', 'user-badges' ); ?></label>
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
function ub_register_widgets() {
	register_widget( 'UB_User_Details_Widget' );
	register_widget( 'UB_Recent_Assignments_Widget' );
}
add_action( 'widgets_init', 'ub_register_widgets' );