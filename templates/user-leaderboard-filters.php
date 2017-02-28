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
	
	<!-- hidden fields -->
	<input type="hidden" name="offset" value="<?php if ( $offset ) { echo intval( $offset ); } ?>" />
	<input type="hidden" name="limit" value="<?php if ( $limit ) { echo intval( $limit ); } ?>" />
	<input type="hidden" name="show_avatar" value="<?php if ( $show_avatar ) { echo 'true'; } ?>" />
	<input type="hidden" name="before_name" value="<?php if ( $before_name ) { echo esc_attr( $before_name ); } ?>" />
	<input type="hidden" name="after_name" value="<?php if ( $after_name ) { echo esc_attr( $after_name ); } ?>" />
	<input type="hidden" name="show_badges" value="<?php if ( $show_badges ) { echo 'true'; } ?>" />
	<input type="hidden" name="show_points" value="<?php if ( $show_points ) { echo 'true'; } ?>" />
	<input type="hidden" name="include_no_assignments" value="<?php if ( $include_no_assignments ) { echo 'true'; } ?>" />
	
	<input type="submit" id="filter-btn" value="<?php _e( 'Filter', 'badgearoo' ); ?>" />
</form>