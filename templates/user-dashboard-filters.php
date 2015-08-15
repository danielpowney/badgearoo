<?php 
/**
 * User dashboard filters template
 */
?>
<form class="user-dashboard-filters">
			<label for="to-date"><?php _e( 'From: ', 'user-badges' ); ?></label><input type="date" name="from-date" value="<?php echo $from_date; ?>"></input>
	<label for="to-date"><?php _e( 'To: ', 'user-badges' ); ?></label><input type="date" name="to-date" value="<?php echo $to_date; ?>"></input>
	<select name="type">
		<option value="" <?php if ( $type != 'points' && $type != 'badge' ) { echo 'selected="selected"'; } ?>><?php _e( 'Badges and points', 'user-badges' ); ?></option>
		<option value="badge" <?php if ( $type == 'badge' ) { echo 'selected="selected"'; } ?>><?php _e( 'Badges', 'user-badges' ); ?></option>
		<option value="points" <?php if ( $type == 'points' ) { echo 'selected="selected"'; } ?>><?php _e( 'Points', 'user-badges' ); ?></option>
	</select>
	<input type="submit" id="filter-btn" value="<?php _e( 'Filter', 'user-badges' ); ?>" />
</form>