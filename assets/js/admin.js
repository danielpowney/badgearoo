
jQuery(document).ready(function($) {

	$("#add-new-badge-header").click(function() {
		$("#add-new-badge-btn").show();
		$("#add-edit-badge-form").show("slow", function() {} );
	});
	
	$("#add-new-badge-btn").click(function() {
		$("#form-submitted").val("true");
	});
	
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
	        //jQuery("#preview").attr("src", json.url ).show().parent().removeClass("hidden");
	        
	        // Store the image's information into the meta data fields
	        jQuery(field).val( json.url );
	    });
	 
	    // Now display the actual file_frame
	    file_frame.open();
	 
	}
	
	jQuery("#upload-btn").on("click", function(evt) {
        // Stop the anchor's default behavior
        evt.preventDefault();

        // Display the media uploader
        renderMediaUploader( '#url' );
    });

});