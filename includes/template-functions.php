<?php 

/**
 * Template functions
 * 
 * Looks in the following directories in priority order for template files:
 * 1. wp-content/themes/CHILD_THEME/user-badges/{filename}
 * 2. wp-content/themes/PARENT_THEME/user-badges/{filename}
 * 3. wp-content/plugins/user-badges/templates/{filename}
 */

/**
 * Retrieves a template part
 *
 * @since v2.5
 *
 * Taken from bbPress
 *
 * @param string $slug
 * @param string $name Optional. Default null
 *
 * @uses  ub_locate_template()
 * @uses  load_template()
 * @uses  get_template_part()
 */
function ub_get_template_part( $slug, $name = null, $load = true, $template_vars ) {
	// Execute code for this part
	do_action( 'get_template_part_' . $slug, $slug, $name );
 
	// Setup possible parts
	$templates = array();
	if ( isset( $name ) )
		$templates[] = $slug . '-' . $name . '.php';
	$templates[] = $slug . '.php';
 
	// Allow template parts to be filtered
	$templates = apply_filters( 'ub_get_template_part', $templates, $slug, $name );
 
	// Return the part that is found
	ub_locate_template( $templates, $load, false, $template_vars );
}

/**
 * Retrieve the name of the highest priority template file that exists.
 *
 * Searches in the STYLESHEETPATH before TEMPLATEPATH so that themes which
 * inherit from a parent theme can just overload one file. If the template is
 * not found in either of those, it looks in the theme-compat folder last.
 *
 * Taken from bbPress
 *
 * @since v2.5
 *
 * @param string|array $template_names Template file(s) to search for, in order.
 * @param bool $load If true the template file will be loaded if it is found.
 * @param bool $require_once Whether to require_once or require. Default true.
 *                            Has no effect if $load is false.
 * @return string The template filename if one is located.
 */
function ub_locate_template( $template_names, $load = false, $require_once = true, $template_vars ) {
	// No file found yet
	$located = false;

	// Try to find a template file
	foreach ( (array) $template_names as $template_name ) {

		// Continue if template is empty
		if ( empty( $template_name ) )
			continue;

		// Trim off any slashes from the template name
		$template_name = ltrim( $template_name, '/' );

		// Check child theme first
		if ( file_exists( trailingslashit( get_stylesheet_directory() ) . 'user-badges/' . $template_name ) ) {
			$located = trailingslashit( get_stylesheet_directory() ) . 'user-badges/' . $template_name;
			break;

			// Check parent theme next
		} elseif ( file_exists( trailingslashit( get_template_directory() ) . 'user-badges/' . $template_name ) ) {
			$located = trailingslashit( get_template_directory() ) . 'user-badges/' . $template_name;
			break;

			// Check theme compatibility last
		} elseif ( file_exists( trailingslashit( ub_get_templates_dir() ) . $template_name ) ) {
			$located = trailingslashit( ub_get_templates_dir() ) . $template_name;
			break;
		}
	}

	if ( ( true == $load ) && ! empty( $located ) ) {
		ub_load_template( $located, $require_once, $template_vars );
	}

	return $located;
}

/**
 * 
 * Taken from WordPress template.php
 * 
 * Require the template file with WordPress environment and User Badges plugin params
 *
 * The globals are set up for the template file to ensure that the WordPress
 * environment is available from within the function. The query variables are
 * also available.
 *
 * @since 1.5.0
 *
 * @param string $_template_file Path to template file.
 * @param bool $require_once Whether to require_once or require. Default true.
 */
function ub_load_template( $_template_file, $require_once = true, $template_vars = array() ) {
	
	global $authordata, $user_ID;
	
	apply_filters( 'ub_load_template_params', $template_vars ); // in case you want to add your own global variables or common data
		
	if ( $template_vars ) {
		extract( $template_vars );
	}
		
	if ( $require_once )
		require_once( $_template_file );
	else
		require( $_template_file );
}

/**
 * Gets the relative path to the plugin directory
 * 
 * @return relative path to plugin directory
 */
function ub_get_templates_dir() {
	return plugin_dir_path( __FILE__ ) . '..' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;
}
?>