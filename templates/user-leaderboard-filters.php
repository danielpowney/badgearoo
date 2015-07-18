<?php 
/**
 * User leaderboard filters template
 */
?>
<form id="user-leaderboard-filters">
	<label for="from-date"><?php _e( 'From: ', 'user-badges' ); ?></label><input type="date" name="from-date" id="from-date"/></input>
	<label for="to-date"><?php _e( 'To: ', 'user-badges' ); ?></label><input type="date" name="to-date" id="to-date"></input>
	<label for="sort-by"><?php _e( 'Sort: ', 'user-badges' ); ?></label>
	<select name="sort-by" id="sort-by">
		<option value="badges"><?php _e( 'Most badges', 'user-badges' ); ?></option>
		<option value="points"><?php _e( 'Most points', 'user-badges' ); ?></option>
	</select>
	<input type="submit" id="filter-btn" value="<?php _e( 'Filter', 'user-badges' ); ?>" />
</form>