<?php
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
	
	<label class="ub-recent-assignments"><?php _e( 'Recent Assignments', 'user-badges')?></label>
	<table>
		<tr>
			<th><?php _e( 'Assignment', 'user-badges' ); ?></th>
			<th><?php _e( 'Condition', 'user-badges' ); ?></th>
			<th><?php _e( 'Created Dt', 'user-badges' ); ?></th>
			<th><?php _e( 'Expiry Dt', 'user-badges' ); ?></th>
		</tr>
		
		<?php 
		foreach ( $assignments as $assignment ) {
			ub_get_template_part( 'assignments-table-row', null, true, array(
					'assignment' => $assignment,
			) );
		} ?>
	</table>
	
	<?php 
	if ( $limit < $count_assignments ) { ?>
		<a href="#" class="ub-more-btn"><?php _e( 'Load more...', 'user-badges' ); ?></a>
	<?php } ?>
</div>