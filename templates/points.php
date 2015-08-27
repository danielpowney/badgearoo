<span class="<?php if ( isset( $class ) ) { echo esc_attr( $class ); } ?> ub-points"><?php 
	if ( $points > 0 ) {
		printf( __( '%s points', 'user-badges' ), number_format( $points ) );
	} else {
		_e( 'No points', 'user-badges' );
	}
?></span>