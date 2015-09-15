<?php 

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * User dashboard filters template
 */
?>
<form class="user-dashboard-filters">
			<label for="to-date"><?php _e( 'From: ', 'badgearoo' ); ?></label><input type="date" name="from-date" value="<?php echo $from_date; ?>"></input>
	<label for="to-date"><?php _e( 'To: ', 'badgearoo' ); ?></label><input type="date" name="to-date" value="<?php echo $to_date; ?>"></input>
	<select name="type">
		<option value="" <?php if ( $type != 'points' && $type != 'badge' ) { echo 'selected="selected"'; } ?>><?php _e( 'Badges and points', 'badgearoo' ); ?></option>
		<option value="badge" <?php if ( $type == 'badge' ) { echo 'selected="selected"'; } ?>><?php _e( 'Badges', 'badgearoo' ); ?></option>
		<option value="points" <?php if ( $type == 'points' ) { echo 'selected="selected"'; } ?>><?php _e( 'Points', 'badgearoo' ); ?></option>
	</select>
	<input type="submit" id="filter-btn" value="<?php _e( 'Filter', 'badgearoo' ); ?>" />
</form>