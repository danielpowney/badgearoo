jQuery(document).ready(function() {	
	
	jQuery("form#user-leaderboard-filters").on("submit", function(e) {
		
		e.preventDefault();
		
		var data = {
				action : "user_leaderboard_filter",
				nonce : ub_frontend_data.ajax_nonce,
				"sort-by" : jQuery("#sort-by").val(),
				"from-date" : jQuery("#from-date").val(),
				"to-date" : jQuery("#to-date").val()
		};
		
		jQuery.post(ub_frontend_data.ajax_url, data, function(response) {
			var jsonResponse = jQuery.parseJSON(response);
			jQuery("#user-leaderboard-table").replaceWith(jsonResponse.data.html);
		});
	});
	
});