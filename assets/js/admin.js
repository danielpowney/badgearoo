jQuery(document).ready(function($) {
	
	// Metabox toggle
	jQuery(".if-js-closed").removeClass("if-js-closed").addClass("closed");
	postboxes.add_postbox_toggles( 'user-badges');
	
	jQuery(".ub-step-list").sortable({
		items: '.ub-step',
		opacity: '0.6',
		cursor: 'move',
		axis: 'y',
		update : function() {
			var order = jQuery(this).sortable('serialize')
		},
		stop: function(event, ui) {
	        
	        var steps = new Array();
	        
	        jQuery(".ub-step-list li").each(function() {    

                //get the id
                var id  = jQuery(this).attr("id");
	        });

	    }
	});
	jQuery(".ub-step-list").disableSelection();
	
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
		jQuery.each( jQuery("#condition-" + conditionId + " li.ub-step"), function(index, value) {
			
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
				nonce : ub_admin_data.ajax_nonce,
				conditionId : conditionId,
				name : jQuery("#condition-" + conditionId + " input[name=name]").val(),
				enabled : jQuery("#condition-" + conditionId + " input[name=enabled]").is(':checked'),
				badges : jQuery("#condition-" + conditionId + " input[name=badges]").val(),
				points : jQuery("#condition-" + conditionId + " input[name=points]").val(),
				steps : steps
		};
	
		jQuery.post(ub_admin_data.ajax_url, data, function(response) {
			var jsonResponse = jQuery.parseJSON(response);
			
			// remove any previous message
			jQuery("div#condition-" + conditionId + " div.updated").remove();
			
			if (jsonResponse.success == true) {
				jQuery("<div class=\"updated\" style=\"margin: 10px 0 10px;\"><p>" + jsonResponse.message + "</p></div>").insertBefore("div#condition-" + conditionId + " form");
				jQuery("div#condition-" + conditionId + " h3 span").remove();
				var html = '<span>' + jsonResponse.data.name + '</span>';
				if ( jsonResponse.data.status ) {
					html += jsonResponse.data.status;
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
				nonce : ub_admin_data.ajax_nonce
		};
	
		jQuery.post(ub_admin_data.ajax_url, data, function(response) {
			var jsonResponse = jQuery.parseJSON(response);
			
			jQuery("#postbox-container #normal-sortables").append(jsonResponse.html);
			
			jQuery(".postbox .hndle, .postbox .handlediv , .postbox a.dismiss, .hide-postbox-tog").unbind("click.postboxes");
			postboxes.add_postbox_toggles('user-badges');
			
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
				nonce : ub_admin_data.ajax_nonce,
				conditionId : conditionId
		};
	
		jQuery.post(ub_admin_data.ajax_url, data, function(response) {
			var jsonResponse = jQuery.parseJSON(response);
			
			//var conditionId = jsonResponse.data.conditionId;
			jQuery("div#condition-" + conditionId + " .ub-step-list").append(jsonResponse.html);
			
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
				nonce : ub_admin_data.ajax_nonce,
				stepId : stepId
		};
	
		jQuery.post(ub_admin_data.ajax_url, data, function(response) {
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
				nonce : ub_admin_data.ajax_nonce,
				conditionId : conditionId
		};
	
		jQuery.post(ub_admin_data.ajax_url, data, function(response) {
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
				nonce : ub_admin_data.ajax_nonce,
				actionName : newActionName,
				stepId : stepId
		};
	
		jQuery.post(ub_admin_data.ajax_url, data, function(response) {
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
	        
	        jQuery("#ub-logo-image-preview").remove();
			
			jQuery("<img id=\"ub-logo-image-preview\" src=\"" + json.url + "\">").insertBefore("#ub-logo-image-upload-btn");
	        
	        // Store the image's information into the meta data fields
	        jQuery(field).val( json.url );
	    });
	 
	    // Now display the actual file_frame
	    file_frame.open();
	 
	}
	
	jQuery("#ub-logo-image-upload-btn").on("click", function(evt) {
        // Stop the anchor's default behavior
        evt.preventDefault();

        // Display the media uploader
        renderMediaUploader( '#ub-logo-image' );
    });
	
	jQuery("input[name=\"ub-logo-type\"]:radio").on("change", function(e) {
		var type = jQuery("input[name=\"ub-logo-type\"]:checked").val();
		
		if (type == 'image') {
			jQuery("#ub-logo-image-container").css('display', 'block');
			jQuery("#ub-logo-html-container").css('display', 'none');
		} else if (type == 'html'){
			jQuery("#ub-logo-html-container").css('display', 'block');
			jQuery("#ub-logo-image-container").css('display', 'none');
		} else {
			jQuery("#ub-logo-html-container").css('display', 'none');
			jQuery("#ub-logo-image-container").css('display', 'none');
		}
	});
	
	jQuery("#add-new-assignment-form select#type").on("change", function(e) {
		
		var data = {
				action : "change_assignment_type",
				nonce : ub_admin_data.ajax_nonce,
				type : jQuery("#add-new-assignment-form select#type").val()
		};
	
		jQuery.post(ub_admin_data.ajax_url, data, function(response) {
			var jsonResponse = jQuery.parseJSON(response);
			
			jQuery("#add-new-assignment-form #assignment").replaceWith(jsonResponse.data.html);
		});
	});

});