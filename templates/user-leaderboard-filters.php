<?php 
/**
 * User leaderboard filters template
 */
?>
<form class="user-leaderboard-filters">
	<label for="from-date"><?php _e( 'From: ', 'badgearoo' ); ?></label><input type="date" name="from-date"></input>
	<label for="to-date"><?php _e( 'To: ', 'badgearoo' ); ?></label><input type="date" name="to-date" ></input>
	<label for="sort-by"><?php _e( 'Sort: ', 'badgearoo' ); ?></label>
	<select name="sort-by">
		<option value="badges"><?php _e( 'Most badges', 'badgearoo' ); ?></option>
		<option value="points"><?php _e( 'Most points', 'badgearoo' ); ?></option>
	</select>
	<input type="submit" id="filter-btn" value="<?php _e( 'Filter', 'badgearoo' ); ?>" />
</form>