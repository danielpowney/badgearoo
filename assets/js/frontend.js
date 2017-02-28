jQuery(document).ready(function() {	
	
	jQuery("form.user-leaderboard-filters").on("submit", function(e) {
		
		e.preventDefault();
		
		var data = {
				action : "user_leaderboard_filter",
				nonce : broo_frontend_data.ajax_nonce,
				"sort-by" :  jQuery(this).find("select[name=sort-by]").find("option:selected").val(),
				"from-date" : jQuery(this).find("input[name=from-date]").val(),
				"to-date" : jQuery(this).find("input[name=to-date]").val(),
				"limit" : jQuery(this).find("input[name=limit]").val(),
				"offset" : jQuery(this).find("input[name=offset]").val(),
				"show_avatar" : jQuery(this).find("input[name=show_avatar]").val(),
				"before_name" : jQuery(this).find("input[name=before_name]").val(),
				"after_name" : jQuery(this).find("input[name=after_name]").val(),
				"show_badges" : jQuery(this).find("input[name=show_badges]").val(),
				"show_points" : jQuery(this).find("input[name=show_points]").val(),
				"include_no_assignments" : jQuery(this).find("input[name=include_no_assignments]").val()
		};
		
		jQuery.post(broo_frontend_data.ajax_url, data, function(response) {
			var jsonResponse = jQuery.parseJSON(response);
			jQuery(".user-leaderboard-table").replaceWith(jsonResponse.data.html);
		});
	});
	
	jQuery(".user-dashboard-assignments .broo-more-btn").on("click", function(e) {
		
		e.preventDefault();
		
		var data = {
				action : "user_dashboard_assignments_more",
				nonce : broo_frontend_data.ajax_nonce,
				"limit" : jQuery(this).parent().find("input[name=limit]").val(),
				"offset" : jQuery(this).parent().find("input[name=offset]").val(),
				"from-date" : jQuery(this).parent().find("input[name=from-date]").val(),
				"to-date" : jQuery(this).parent().find("input[name=to-date]").val(),
				"type" : jQuery(this).parent().find("input[name=type]").val(),
		};
		
		jQuery.post(broo_frontend_data.ajax_url, data, function(response) {
			var jsonResponse = jQuery.parseJSON(response);
			if ( jsonResponse.data.html.length > 0 ) {
				jQuery(".user-dashboard-assignments table").append(jsonResponse.data.html);
				jQuery(".user-dashboard-assignments input[name=offset]").val(jsonResponse.data.offset);
				var countAssignments = jQuery(".user-dashboard-assignments input[name=count-assignments]").val();
				
				if ( jsonResponse.data.offset >= countAssignments ) {
					jQuery(".user-dashboard-assignments .broo-more-btn").remove();
				}
			}
		});
	});

	
	if (Cookies.get("broo_new_assignment") != undefined) {
		
		var jsonNewAssignments = Cookies.get("broo_new_assignment");
		
		var newAssignments = jQuery.parseJSON(jsonNewAssignments);
		
		if ( broo_frontend_data.show_user_assignment_modal ) {
			jQuery.each(newAssignments, function(index, assignment) {
				alert(assignment.message.replace(/\+/g, ' '));
			});
		}
		
		// TODO converter
		//Cookies.withConverter(function (value) {
		//   return value.replace(/\+/g, ' ');
		//}).get('foo');		
		
		// https://github.com/js-cookie/js-cookie
		Cookies.remove("broo_new_assignment", { 'path' : broo_frontend_data.cookie_path, 'domain' : broo_frontend_data.cookie_domain });
		
	}
	
});