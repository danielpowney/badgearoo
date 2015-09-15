<?php 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>
<span class="<?php if ( isset( $class ) ) { echo esc_attr( $class ); } ?> broo-points"><?php 
	if ( $points > 0 ) {
		printf( __( '%s points', 'badgearoo' ), number_format( $points ) );
	} else {
		_e( 'No points.', 'badgearoo' );
	}
?></span>