jQuery(document).ready(function() {	
	
	jQuery("form.user-leaderboard-filters").on("submit", function(e) {
		
		e.preventDefault();
		
		var data = {
				action : "user_leaderboard_filter",
				nonce : ub_frontend_data.ajax_nonce,
				"sort-by" :  jQuery(this).find("select[name=sort-by]").find("option:selected").val(),
				"from-date" : jQuery(this).find("input[name=from-date]").val(),
				"to-date" : jQuery(this).find("input[name=to-date]").val(),
		};
		
		jQuery.post(ub_frontend_data.ajax_url, data, function(response) {
			var jsonResponse = jQuery.parseJSON(response);
			jQuery(".user-leaderboard-table").replaceWith(jsonResponse.data.html);
		});
	});
	
	jQuery(".user-dashboard-assignments .ub-more-btn").on("click", function(e) {
		
		e.preventDefault();
		
		var data = {
				action : "user_dashboard_assignments_more",
				nonce : ub_frontend_data.ajax_nonce,
				"limit" : jQuery(this).parent().find("input[name=limit]").val(),
				"offset" : jQuery(this).parent().find("input[name=offset]").val(),
				"from-date" : jQuery(this).parent().find("input[name=from-date]").val(),
				"to-date" : jQuery(this).parent().find("input[name=to-date]").val(),
				"type" : jQuery(this).parent().find("input[name=type]").val(),
		};
		
		jQuery.post(ub_frontend_data.ajax_url, data, function(response) {
			var jsonResponse = jQuery.parseJSON(response);
			if ( jsonResponse.data.html.length > 0 ) {
				jQuery(".user-dashboard-assignments table").append(jsonResponse.data.html);
				jQuery(".user-dashboard-assignments input[name=offset]").val(jsonResponse.data.offset);
				var countAssignments = jQuery(".user-dashboard-assignments input[name=count-assignments]").val();
				
				if ( jsonResponse.data.offset >= countAssignments ) {
					jQuery(".user-dashboard-assignments .ub-more-btn").remove();
				}
			}
		});
	});

	
	if (Cookies.get("ub_new_assignment") != undefined) {
		
		var jsonNewAssignments = Cookies.get("ub_new_assignment");
		
		var newAssignments = jQuery.parseJSON(jsonNewAssignments);
		
		if ( ub_frontend_data.show_user_assignment_modal ) {
			jQuery.each(newAssignments, function(index, assignment) {
				alert(assignment.message.replace(/\+/g, ' '));
			});
		}
		
		// TODO converter
		//Cookies.withConverter(function (value) {
		//   return value.replace(/\+/g, ' ');
		//}).get('foo');		
		
		// https://github.com/js-cookie/js-cookie
		Cookies.remove("ub_new_assignment", { 'path' : ub_frontend_data.cookie_path, 'domain' : ub_frontend_data.cookie_domain });
		
	}
	
});