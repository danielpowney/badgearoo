<?php 

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * User leaderboard template
 */
?>

<div class="<?php if ( isset( $class ) ) { echo esc_attr( $class ); } ?> user-leaderboard">

	<h2><?php _e( 'Leaderboard', 'badgearoo' ); ?></h2>
	
	<?php
	if ( count( $user_rows ) == 0 ) {
		?>
		<p><?php _e( 'No assignments.', 'badgearoo' ); ?></p>
		<?php 
	} else {
		
		if ( $show_filters == true ) { 
			broo_get_template_part( 'user-leaderboard', 'filters', true, array( 
					'show_avatar' => $show_avatar,
					'before_name' => $before_name,
					'after_name' => $after_name,
					'show_badges' => $show_badges,
					'show_points' => $show_points,
					'sort_by' => $sort_by,
					'from_date' => $from_date,
					'to_date' => $to_date,
					'limit' => $limit,
					'offset' => $offset,
					'include_no_assignments' => $include_no_assignments
			 ) );
		}
		
		broo_get_template_part( 'user-leaderboard', 'table', true, array(
				'user_rows'=> $user_rows,
				'show_avatar' => $show_avatar,
				'before_name' => $before_name,
				'after_name' => $after_name,
				'show_badges' => $show_badges,
				'show_points' => $show_points,
				'sort_by' => $sort_by
		) );
	}		
?>
</div>