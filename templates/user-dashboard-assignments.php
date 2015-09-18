<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * User Dashboard Assignments template
 */
?>

<div class="<?php if ( isset( $class ) ) { echo esc_attr( $class ); } ?> user-dashboard-assignments">

	<input type="hidden" name="limit" value="<?php if ( isset( $limit ) ) { echo $limit; } ?>" />
	<input type="hidden" name="offset" value="<?php if ( isset( $offset ) ) { echo $offset; } ?>" />
	<input type="hidden" name="to-date" value="<?php if ( isset( $_REQUEST['to-date'] ) ) { echo $_REQUEST['to-date']; } ?>" />
	<input type="hidden" name="offset" value="<?php if ( isset( $_REQUEST['from-date'] ) ) { echo $_REQUEST['from-date']; } ?>" />
	<input type="hidden" name="type" value="<?php if ( isset( $_REQUEST['type'] ) ) { echo $_REQUEST['type']; } ?>" />
	<input type="hidden" name="count-assignments" value="<?php echo $count_assignments; ?>" />
	
	<?php 
	if ( $count_assignments == 0 ) {
		_e( 'No assignments.', 'badgearoo' );
	} else {
		?>
		<label class="broo-recent-assignments"><?php _e( 'Recent Assignments', 'badgearoo')?></label>
		<table>
			<tr>
				<th><?php _e( 'Assignment', 'badgearoo' ); ?></th>
				<th><?php _e( 'Condition', 'badgearoo' ); ?></th>
				<th><?php _e( 'Created Dt', 'badgearoo' ); ?></th>
				<th><?php _e( 'Expiry Dt', 'badgearoo' ); ?></th>
			</tr>
			
			<?php 
			foreach ( $assignments as $assignment ) {
				broo_get_template_part( 'assignments-table-row', null, true, array(
						'assignment' => $assignment,
						'badge_theme' => $badge_theme,
						'enable_badge_permalink' => $enable_badge_permalink
				) );
			} ?>
		</table>
	<?php } ?>
	<?php 
	if ( $limit < $count_assignments ) { ?>
		<a href="#" class="broo-more-btn"><?php _e( 'Load more...', 'badgearoo' ); ?></a>
	<?php } ?>
</div>