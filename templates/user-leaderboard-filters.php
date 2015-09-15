<?php 

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * User leaderboard filters template
 */
?>
<form class="user-leaderboard-filters">
	<label for="from-date"><?php _e( 'From: ', 'badgearoo' ); ?></label><input type="date" name="from-date" value="<?php echo $from_date; ?>"></input>
	<label for="to-date"><?php _e( 'To: ', 'badgearoo' ); ?></label><input type="date" name="to-date" "<?php echo $to_date; ?>"></input>
	<label for="sort-by"><?php _e( 'Sort: ', 'badgearoo' ); ?></label>
	<select name="sort-by">
		<option value="badges" <?php if ( isset( $sort_by ) && $sort_by == 'badges' ) { echo 'selected="selected"'; } ?>><?php _e( 'Most badges', 'badgearoo' ); ?></option>
		<option value="points" <?php if ( isset( $sort_by ) && $sort_by == 'points' ) { echo 'selected="selected"'; } ?>><?php _e( 'Most points', 'badgearoo' ); ?></option>
	</select>
	<input type="submit" id="filter-btn" value="<?php _e( 'Filter', 'badgearoo' ); ?>" />
</form>