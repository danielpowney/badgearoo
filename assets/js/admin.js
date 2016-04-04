jQuery(document).ready(function($) {
	
	// Metabox toggle
	jQuery(".if-js-closed").removeClass("if-js-closed").addClass("closed");
	
	jQuery(".broo-step-list").sortable({
		items: '.broo-step',
		opacity: '0.6',
		cursor: 'move',
		axis: 'y',
		update : function() {
			var order = jQuery(this).sortable('serialize')
		},
		stop: function(event, ui) {
	        
	        var steps = new Array();
	        
	        jQuery(".broo-step-list li").each(function() {    

                //get the id
                var id  = jQuery(this).attr("id");
	        });

	    }
	});
	
	/**
	 * Click Save Changes
	 */
	jQuery("form.condition").submit(function(e) {
		
		e.preventDefault();
		
		var parts = jQuery(this).closest(".postbox")[0].id.split("-"); 
		var conditionId = parts[1]; // condition-X
		
		saveCondition(conditionId);
	});
	
	/**
	 * Saves a condition
	 */
	function saveCondition(conditionId) {
		
		var steps = [];
		jQuery.each( jQuery("#condition-" + conditionId + " li.broo-step"), function(index, value) {
			
			var parts = value.id.split("-")
			var stepId = parts[1];
			
			var stepMeta = [];
			jQuery.each( jQuery("li#step-" + stepId + " .step-meta input, li#step-" + stepId + " .step-meta select," +
					"li#step-" + stepId + " .step-meta textarea"), function(index, value) {
				
				var value = "";
				var name = jQuery(this).attr("name");
				if (jQuery(this).is(":checkbox")) {
					value = jQuery(this).is(':checked');
				} else { // input, select, textarea
					value = jQuery(this).val();
				}
				
				if ( value && value.length > 0 ) {
					stepMeta.push({
						key : name,
						value : value
					});
				}
			});
			
			var step = {
					stepId : stepId,
					label : jQuery("li#step-" + stepId + " input[name=label]").val(),
					actionName : jQuery("li#step-" + stepId + " select[name=action-name]").find("option:selected").val(),
					stepMeta : ( stepMeta.length > 0 ) ? stepMeta : null
			};
			
			steps.push(step);
		});
		
		var data = {
				action : "save_condition",
				nonce : broo_admin_data.ajax_nonce,
				conditionId : conditionId,
				name : jQuery("#condition-" + conditionId + " input[name=name]").val(),
				enabled : jQuery("#condition-" + conditionId + " input[name=enabled]").is(':checked'),
				badges : jQuery("#condition-" + conditionId + " input[name=badges]").val(),
				points : jQuery("#condition-" + conditionId + " input[name=points]").val(),
				recurring : jQuery("#condition-" + conditionId + " input[name=recurring]").is(':checked'),
				expiryValue : jQuery("#condition-" + conditionId + " input[name=expiry-value]").val(),
				expiryUnit : jQuery("#condition-" + conditionId + " select[name=expiry-unit]").find("option:selected").val(),
				steps : steps
		};
	
		jQuery.post(broo_admin_data.ajax_url, data, function(response) {
			var jsonResponse = jQuery.parseJSON(response);
			
			// remove any previous message
			jQuery("div#condition-" + conditionId + " div.updated, div#condition-" + conditionId 
					+ " div.error, div#condition-" + conditionId + " div.update-nag").remove();
			
			if (jsonResponse.success == true) {
				
				if ( jsonResponse.data.messages_html ) {
					jQuery(jsonResponse.data.messages_html).insertBefore("div#condition-" + conditionId + " form");
				}
				
				jQuery("div#condition-" + conditionId + " h3 span").remove();
				
				var html = '<span>' + jsonResponse.data.name + '</span>';
				if ( jsonResponse.data.status_html ) {
					html += jsonResponse.data.status_html;
				}
				jQuery("div#condition-" + conditionId + " h3").append(html);
			}
		});
	};
	
	/**
	 * Add condition
	 */
	jQuery("#add-condition").on("click", function(e) {
		
		var data = {
				action : "add_condition",
				nonce : broo_admin_data.ajax_nonce
		};
	
		jQuery.post(broo_admin_data.ajax_url, data, function(response) {
			var jsonResponse = jQuery.parseJSON(response);
			
			jQuery("#postbox-container #normal-sortables").prepend(jsonResponse.html);
			
			jQuery(".postbox .hndle, .postbox .handlediv , .postbox a.dismiss, .hide-postbox-tog").unbind("click.postboxes");
			postboxes.add_postbox_toggles('badgearoo');
			
			// delete condition
			jQuery("div#condition-" + jsonResponse.data.conditionId + " .delete-condition-btn").on("click", function(e) {
				var parts = jQuery(this).closest(".postbox")[0].id.split("-"); 
				var conditionId = parts[1]; // condition-X
				
				deleteCondition(conditionId);
			});
			
			// add step
			jQuery("div#condition-" + jsonResponse.data.conditionId + " .add-step-btn").on("click", function(e) {
				
				var parts = jQuery(this).closest(".postbox")[0].id.split("-"); 
				var conditionId = parts[1]; // condition-X
				
				addStep(conditionId);
			});
			
			/**
			 * Change step action name
			 */
			jQuery("div#condition-" + jsonResponse.data.conditionId + " select[name=action-name]").on("change", function(e) {
				var parts = jQuery(this).closest("li")[0].id.split("-"); 
				var stepId = parts[1]; // step-X
				
				changeStepAction(stepId);
			});
			
			jQuery("div#condition-" + jsonResponse.data.conditionId + " .addBadgeBtn").on("click", function(e) {
				var parts = jQuery(this).closest(".postbox")[0].id.split("-"); 
				var conditionId = parts[1]; // condition-X
				
				addBadge(conditionId);
			});
			
			jQuery("div#condition-" + jsonResponse.data.conditionId + " .ntdelbutton").on("click", function(e) {
				var parts = jQuery(this).closest(".postbox")[0].id.split("-"); 
				var conditionId = parts[1]; // condition-X
				
				parts = jQuery(this)[0].name.split("-");
				var badgeId = parts[1];
				
				jQuery(this).parent().remove();
				
				deleteBadge(conditionId, badgeId);
			});
			
			// delete step
			jQuery("div#condition-" + jsonResponse.data.conditionId + " a.delete-step").on("click", function(e) {
				var parts = jQuery(this).closest("li")[0].id.split("-"); 
				var stepId = parts[1]; // step-X
				
				deleteStep(stepId);
			});
			
			// save condition
			jQuery("div#condition-" + jsonResponse.data.conditionId + " form.condition").submit(function(e) {
				
				e.preventDefault();
				
				var parts = jQuery(this).closest(".postbox")[0].id.split("-"); 
				var conditionId = parts[1]; // condition-X
				
				saveCondition(conditionId);
			});
		});
	});
	
	/**
	 * Click add step
	 */
	jQuery("form.condition .add-step-btn").on("click", function(e) {
		
		var parts = jQuery(this).closest(".postbox")[0].id.split("-"); 
		var conditionId = parts[1]; // condition-X
		
		addStep(conditionId);
	});
	
	jQuery("form.condition .addBadgeBtn").on("click", function(e) {
		var parts = jQuery(this).closest(".postbox")[0].id.split("-"); 
		var conditionId = parts[1]; // condition-X
		
		addBadge(conditionId);
	});
	
	jQuery("form.condition .ntdelbutton").on("click", function(e) {
		var parts = jQuery(this).closest(".postbox")[0].id.split("-"); 
		var conditionId = parts[1]; // condition-X
		
		parts = jQuery(this)[0].name.split("-");
		var badgeId = parts[1];
		
		jQuery(this).parent().remove();
		
		deleteBadge(conditionId, badgeId);
	});
	
	/**
	 * Change step action name
	 */
	jQuery("form.condition select[name=action-name]").on("change", function(e) {
		var parts = jQuery(this).closest("li")[0].id.split("-"); 
		var stepId = parts[1]; // step-X
		
		changeStepAction(stepId);
	});
	
	/**
	 * Click delete step
	 */
	jQuery("form.condition a.delete-step").on("click", function(e) {
		var parts = jQuery(this).closest("li")[0].id.split("-"); 
		var stepId = parts[1]; // step-X
		
		deleteStep(stepId);
	});
	
	/**
	 * Click delete condition
	 */
	jQuery("form.condition .delete-condition-btn").on("click", function(e) {
		var parts = jQuery(this).closest(".postbox")[0].id.split("-"); 
		var conditionId = parts[1]; // condition-X
		
		deleteCondition(conditionId);
	});
	
	/**
	 * Adds a badge
	 */
	function addBadge(conditionId) {

		var selectedOption = jQuery("div#condition-" + conditionId + " select[name=addBadge]").find("option:selected");
		
		var badgeId = selectedOption.val();
		var badgeName = selectedOption.text();
		
		var html = "<span><a class=\"badge badgeId-" + badgeId + " ntdelbutton\">X</a>&nbsp;" + badgeName + "</span>";
		
		var badges = jQuery("div#condition-" + conditionId + " input[name=badges]").val().split();
		badges.push(badgeId);
		jQuery("div#condition-" + conditionId + " input[name=badges]").val(badges.join(","));
		
		jQuery("div#condition-" + conditionId + " .tagchecklist").append(html);
		
		jQuery("div#condition-" + conditionId + " .badgeId-" + badgeId).on("click", function(e) {
			var parts = jQuery(this).closest(".postbox")[0].id.split("-"); 
			var conditionId = parts[1]; // condition-X
			
			parts = jQuery(this)[0].name.split("-");
			var badgeId = parts[1];
			
			jQuery(this).parent().remove();
			
			deleteBadge(conditionId, badgeId);
		});
	}
	
	/**
	 * Deletes a badge
	 */
	function deleteBadge(conditionId, badgeId) {
		
		var badges = jQuery("div#condition-" + conditionId + " input[name=badges]").val().split(",");

		for (var index = 0; index< badges.length; index++) {
			if (badges[index] == badgeId) {
				if (badgeId == badges[index]) { // found, remove one
					badges.splice( index, 1 );
					break;
				}
			}
		}
		
		jQuery("div#condition-" + conditionId + " input[name=badges]").val(badges.join(","));	
	}
	
	/**
	 * Add step
	 */
	function addStep(conditionId) {
		
		var data = {
				action : "add_step",
				nonce : broo_admin_data.ajax_nonce,
				conditionId : conditionId
		};
	
		jQuery.post(broo_admin_data.ajax_url, data, function(response) {
			var jsonResponse = jQuery.parseJSON(response);
			
			//var conditionId = jsonResponse.data.conditionId;
			jQuery("div#condition-" + conditionId + " .broo-step-list").append(jsonResponse.html);
			
			// Change step action name
			jQuery("li#step-" + jsonResponse.data.stepId + " select[name=action-name]").on("change", function(e) {
				var parts = jQuery(this).closest("li")[0].id.split("-"); 
				var stepId = parts[1]; // step-X
				
				changeStepAction(stepId);
			});
			
			//Click delete step
			jQuery("li#step-" + jsonResponse.data.stepId + " a.delete-step").on("click", function(e) {
				var parts = jQuery(this).closest("li")[0].id.split("-"); 
				var stepId = parts[1]; // step-X
				
				deleteStep(stepId);
			});
		});
	}
	
	/**
	 * Deletes step and empties HTML if success
	 */
	function deleteStep(stepId) {
		
		var data = {
				action : "delete_step",
				nonce : broo_admin_data.ajax_nonce,
				stepId : stepId
		};
	
		jQuery.post(broo_admin_data.ajax_url, data, function(response) {
			var jsonResponse = jQuery.parseJSON(response);
			
			if (jsonResponse.success == true ) {
				jQuery("li#step-" + stepId).remove();
			}
		});
	}
	
	/**
	 * Deletes conditions and empties HTML if success
	 */
	function deleteCondition(conditionId) {
		
		var data = {
				action : "delete_condition",
				nonce : broo_admin_data.ajax_nonce,
				conditionId : conditionId
		};
	
		jQuery.post(broo_admin_data.ajax_url, data, function(response) {
			var jsonResponse = jQuery.parseJSON(response);
			
			if (jsonResponse.success == true ) {
				jQuery("div#condition-" + conditionId).remove();
			}
		});
	}
	
	/**
	 * Change step action
	 */
	function changeStepAction(stepId) {
		
		var newActionName = jQuery("li#step-" + stepId + " select[name=action-name]").find("option:selected").val();
		
		var data = {
				action : "step_meta",
				nonce : broo_admin_data.ajax_nonce,
				actionName : newActionName,
				stepId : stepId
		};
	
		jQuery.post(broo_admin_data.ajax_url, data, function(response) {
			var jsonResponse = jQuery.parseJSON(response);

			jQuery("li#step-" + stepId + " .step-meta").html(jsonResponse.html);
		});
	}
	
	/**
	 * Displays the media uploader for selecting an image.
	 * 
	 * @param starImage star image name for media uploader
	 */
	function renderMediaUploader(field) {
	 
	    var file_frame, image_data;
	 
	    /**
	     * If an instance of file_frame already exists, then we can open it
	     * rather than creating a new instance.
	     */
	    if ( undefined !== file_frame ) {
	        file_frame.open();
	        return;
	    }
	 
	    /**
	     * If we're this far, then an instance does not exist, so we need to
	     * create our own.
	     *
	     * Here, use the wp.media library to define the settings of the Media
	     * Uploader. We're opting to use the 'post' frame which is a template
	     * defined in WordPress core and are initializing the file frame
	     * with the 'insert' state.
	     *
	     * We're also not allowing the user to select more than one image.
	     */
	    file_frame = wp.media.frames.file_frame = wp.media({
	        frame:    "post",
	        state:    "insert",
	        multiple: false
	    });
	 
	    /**
	     * Setup an event handler for what to do when an image has been
	     * selected.
	     *
	     * Since we're using the 'view' state when initializing
	     * the file_frame, we need to make sure that the handler is attached
	     * to the insert event.
	     */
	    file_frame.on("insert", function() {
	 
	    	// Read the JSON data returned from the Media Uploader
	        var json = file_frame.state().get("selection").first().toJSON();
	 
	        // After that, set the properties of the image and display it
	        
	        jQuery("#broo-badge-icon-preview").remove();
			
			jQuery("<img id=\"broo-badge-icon-preview\" src=\"" + json.url + "\">").insertAfter("#broo-badge-icon-upload-btn");
	        
	        // Store the image's information into the meta data fields
	        jQuery(field).val( json.url );
	    });
	 
	    // Now display the actual file_frame
	    file_frame.open();
	 
	}
	
	jQuery("#broo-badge-icon-upload-btn").on("click", function(evt) {
        // Stop the anchor's default behavior
        evt.preventDefault();

        // Display the media uploader
        renderMediaUploader( '#broo-badge-icon' );
    });
	
	jQuery("#add-new-assignment-form select#type").on("change", function(e) {
		
		var data = {
				action : "change_assignment_type",
				nonce : broo_admin_data.ajax_nonce,
				type : jQuery("#add-new-assignment-form select#type").val()
		};
	
		jQuery.post(broo_admin_data.ajax_url, data, function(response) {
			var jsonResponse = jQuery.parseJSON(response);
			
			jQuery("#add-new-assignment-form #assignment").replaceWith(jsonResponse.data.html);
		});
	});
	
	
	var rowActions = jQuery("#assignments-table-form .row-actions > a");
	jQuery.each(rowActions, function(index, element) {
		jQuery(element).click(function(e) { 
			updateStatus(this);
		});
	});
	
	function updateStatus(e) {
		
		var anchorId = e.id; // e.g. broo-anchor-approve-82
		var parts = anchorId.split("-"); 
		var rowId = parts[3]; 
		
		var status = jQuery("#" + anchorId).hasClass("broo-approve") ? "approve" : "unapprove";
		
		
		var data =  { 
				action : "update_user_assignment_status",
				nonce : broo_admin_data.ajax_nonce,
				assignmentId : rowId,
				status : status
		};
				
		jQuery.post(broo_admin_data.ajax_url, data, function(response) {
			var jsonResponse = jQuery.parseJSON(response);
			
			var assignmentId = jsonResponse.assignment_id;
			var actionId = null;
			
			jQuery("#broo-row-actions-" + assignmentId).empty();
			if (jsonResponse.data.status != "approved") {
				actionId = "broo-anchor-approve-" + assignmentId;
				jQuery("#broo-row-actions-" + assignmentId).append("<a href='#' id='" + actionId + "' class='broo-approve'>" + jsonResponse.data.approve + "</a>");
				jQuery("#broo-text-approve-" + assignmentId).css('display', 'none');
				jQuery("#broo-text-pending-" + assignmentId).css('display', 'none');
				jQuery("#broo-text-unapprove-" + assignmentId).css('display', 'block');
			} else {
				actionId = "broo-anchor-unapprove-" + assignmentId;
				jQuery("#broo-row-actions-" + assignmentId).append("<a href='#' id='" + actionId + "' class='broo-unapprove'>" + jsonResponse.data.unapprove + "</a>");
				jQuery("#broo-text-approve-" + assignmentId).css('display', 'block');
				jQuery("#broo-text-pending-" + assignmentId).css('display', 'none');
				jQuery("#broo-text-unapprove-" + assignmentId).css('display', 'none');
			}
			
			jQuery("#" + actionId).click(function(e) { 
				updateStatus(this);
			});
		
		});
		
		// stop event
		event.preventDefault();
	}
	

	
	jQuery('.color-picker').wpColorPicker({
	    defaultColor: false,
	    change: function(event, ui){},
	    clear: function() {},
	    hide: true,
	    palettes: true
	});
	
});

jQuery(window).load(function() {
	jQuery(".postbox .hndle, .postbox .handlediv , .postbox a.dismiss, .hide-postbox-tog").unbind("click.postboxes");
	postboxes.add_postbox_toggles('badgearoo');

});