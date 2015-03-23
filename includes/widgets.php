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
				'classname' => 'user-badges-widget',
				'description' => __('Shows the post author details including any badges they have.', 'user-badges' )
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
		
		extract( $args );

		$header = empty( $instance['header'] ) ? 'h3' : $instance['header'];
		
		$before_title = '<' . $header . ' class="widget-title">';
		$after_title = '</' . $header . '>';
		
		echo $before_widget;
		
		ub_get_template_part( 'user-badges-widget', null, true, array(
				'before_title' => $before_title,
				'after_title' => $after_title,
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
 * Register widgets
 */
function ub_register_widgets() {
	register_widget( 'UB_User_Details_Widget' );
}
add_action( 'widgets_init', 'ub_register_widgets' );