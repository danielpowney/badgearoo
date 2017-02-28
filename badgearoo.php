<?php
/*
 Plugin Name: Badgearoo
 Plugin URI: http://wordpress.org/plugins/badgearoo/
 Description: Create your own badges and points system for WordPress users. You can configure automatic assignment or manually assign badges and points to users.
 Version: 1.0.14
 Author: Daniel Powney
 Author URI: http://danielpowney.com
 License: GPL2
 Text Domain: badgearoo
 Domain Path: languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'BROO_ACTION_TABLE_NAME', 'broo_action' ); // stores predefined actions e.g. publishes a post
define( 'BROO_USER_ACTION_TABLE_NAME', 'broo_user_action' ); // stores what actions a user has done
define( 'BROO_USER_ASSIGNMENT_TABLE_NAME', 'broo_user_assignment' ); // stores badges/points assigned to users
define( 'BROO_CONDITION_TABLE_NAME', 'broo_condition' );
define( 'BROO_CONDITION_STEP_META_TABLE_NAME', 'broo_condition_step_meta' );
define( 'BROO_CONDITION_STEP_TABLE_NAME', 'broo_condition_step' );
define( 'BROO_USER_ACTION_META_TABLE_NAME', 'broo_user_action_meta' );


/**
 * Badgearoo plugin class
 */
class Badgearoo {

	/** Singleton *************************************************************/

	/**
	 * @var Badgearoo The one true Badgearoo
	 */
	private static $instance;

	/**
	 * Settings instance variable
	 */
	public $settings = null;
	
	public $api = null;
	
	public $actions = array();
	
	/**
	 * Constants
	 */
	const
	VERSION = '1.0.14',
	ID = 'badgearoo',
	
	// options
	DO_ACTIVATION_REDIRECT_OPTION = 'broo_active_redirect',
	
	// slugs
	ABOUT_PAGE_SLUG = 'broo_about',
	BADGES_PAGE_SLUG = 'broo_badges',
	CONDITIONS_PAGE_SLUG = 'broo_conditions',
	SETTINGS_PAGE_SLUG = 'broo_settings',
	TOOLS_PAGE_SLUG = 'broo_tools',
	ASSIGNMENTS_PAGE_SLUG = 'broo_assignments';
	
	/**
	 *
	 * @return Multi_Rating
	 */
	public static function instance() {
	
		if ( ! isset( self::$instance )	&& ! ( self::$instance instanceof Badgearoo ) ) {
	
			self::$instance = new Badgearoo;
			
			self::$instance->includes();
			
			self::$instance->settings = new BROO_Settings();
			self::$instance->api = apply_filters( 'broo_api_instance', new BROO_API_Impl() );
			
			if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	
				add_action( 'admin_menu', array(self::$instance, 'add_admin_menus') );
				add_action( 'admin_enqueue_scripts', array( self::$instance, 'admin_assets' ) );
				add_action( 'admin_init', array( self::$instance, 'redirect_about_page' ) );
				
				add_action( 'delete_user', 'broo_delete_user', 11, 2 );
	
			} else {
				add_action( 'wp_enqueue_scripts', array( self::$instance, 'assets' ) );
			}
	
			add_action( 'wp_head', array( self::$instance, 'add_custom_css') );
			add_action( 'init', array( self::$instance, 'load_textdomain' ) );
			add_action( 'init', array( self::$instance, 'register_badge_post_type' ) );
			add_action( 'after_setup_theme', array( self::$instance, 'add_image_sizes') );
	
			self::$instance->add_ajax_callbacks();
			
			add_action( 'plugins_loaded', array( self::$instance, 'setup_actions' ) );
			
		}
	
		return Badgearoo::$instance;
	}
	
	
	/**
	 * Setup actions
	 */
	function setup_actions() {
		
		self::$instance->actions = (array) apply_filters( 'broo_init_actions', self::$instance->actions );
		
		$actions_enabled = (array) get_option( 'broo_actions_enabled' );
		
		// Make sure all actions are stored in database
		if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
			
			global $wpdb;
			
			$query = 'SELECT DISTINCT name FROM ' . $wpdb->prefix . BROO_ACTION_TABLE_NAME;
			
			$results = $wpdb->get_results( $query );
			
			$saved_actions= array();
			foreach ( $results as $row ) {
				array_push( $saved_actions, $row->name );
			}
			
			$created_dt = current_time( 'mysql' );
			
			$missing_rows = array();
			foreach ( self::$instance->actions as $action_name => $action_data ) {
				
				if ( ! in_array( $action_name, $saved_actions ) ) {		
					array_push( $missing_rows, ' ( "' . esc_sql( $action_name ) . '", "' . esc_sql( $action_data['description'] ) 
							. '", "' . esc_sql( $action_data['source'] ) . '", "' . $created_dt . '" )' );
				}
			}
			
			$count_missing = count( $missing_rows );
			if ( $count_missing > 0 ) {
				
				$query = 'INSERT INTO ' . $wpdb->prefix . BROO_ACTION_TABLE_NAME . ' ( name, description, source, created_dt ) VALUES';
			
				$index = 0;
				foreach ( $missing_rows as $missing_row ) {
					
					$index++;
					$query .= $missing_row;
						
					if ( $index < $count_missing ) {
						$query .= ', ';
					}
				}
					
				$wpdb->query( $query );
			}			
		}
		
		foreach ( self::$instance->actions as $action_name => $action ) {
			
			// Check settings for enabled actions
			if ( $actions_enabled && isset( $actions_enabled[$action_name] ) 
					&& is_bool( $actions_enabled[$action_name] ) ) {
				self::$instance->actions[$action_name]['enabled'] = $actions_enabled[$action_name];
			} else {
				self::$instance->actions[$action_name]['enabled'] =  false;
			}
			
		}
		
		
		do_action( 'broo_init_actions_complete', self::$instance->actions );
	}
	
	/**
	 * Includes files
	 */
	function includes() {
	
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'shortcodes.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'widgets.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-utils.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-api.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-settings.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'actions.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'template-functions.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'misc-functions.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'notifications.php';
		
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-badge.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-step.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-action.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-condition.php';
		
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'integrations' . DIRECTORY_SEPARATOR . 'common.php';
		
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'integrations' . DIRECTORY_SEPARATOR . 'buddypress.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'integrations' . DIRECTORY_SEPARATOR . 'bbpress.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'integrations' . DIRECTORY_SEPARATOR . 'woocommerce.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'integrations' . DIRECTORY_SEPARATOR . 'easy-digital-downloads.php';
		
		if ( is_admin() ) {
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'conditions.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'about.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'tools.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'settings.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'badges.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'users.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-assignments-table.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'assignments.php';
				
		}
		
	}
	
	/**
	 * Activates the plugin
	 */
	public static function activate_plugin() {
	
		global $wpdb, $charset_collate;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		$action_query = 'CREATE TABLE ' . $wpdb->prefix . BROO_ACTION_TABLE_NAME . ' (
				name varchar(50) NOT NULL,
				description varchar(200) NOT NULL,
				source varchar(100) NOT NULL,
				created_dt datetime,
				PRIMARY KEY  (name)
		) ' . $charset_collate;
		
		dbDelta( $action_query );
		
		$user_assignment_query = 'CREATE TABLE ' . $wpdb->prefix . BROO_USER_ASSIGNMENT_TABLE_NAME . ' (
				id  bigint(20) NOT NULL AUTO_INCREMENT,
				user_id bigint(20) NOT NULL,
				condition_id bigint(20),
				type varchar(20) NOT NULL,
				value bigint(20) NOT NULL,
				created_dt datetime,
				last_updated_dt datetime,
				expiry_dt datetime DEFAULT NULL,
				status varchar(20) NOT NULL DEFAULT "approved",
				PRIMARY KEY  (id)
		) ' . $charset_collate;
		
		dbDelta( $user_assignment_query );
		
		$user_action_query = 'CREATE TABLE ' . $wpdb->prefix . BROO_USER_ACTION_TABLE_NAME . ' (
				id  bigint(20) NOT NULL AUTO_INCREMENT,
				user_id bigint(20) NOT NULL,
				action_name varchar(50) NOT NULL,
				created_dt datetime,
				PRIMARY KEY  (id)
		) ' . $charset_collate;
		
		dbDelta( $user_action_query );
			
		$condition_query = 'CREATE TABLE ' . $wpdb->prefix . BROO_CONDITION_TABLE_NAME . ' (
				condition_id  bigint(20) NOT NULL AUTO_INCREMENT,
				name varchar(255) NOT NULL,
				points bigint(20) DEFAULT 0,
				badges longtext,
				created_dt datetime,
				enabled tinyint(1) DEFAULT 1,
				expiry_value smallint(20) DEFAULT 0,
				expiry_unit varchar(20),
				recurring tinyint(1) DEFAULT 1,
				PRIMARY KEY  (condition_id)
		) ' . $charset_collate;
		
		dbDelta( $condition_query );
		
		$condition_step_query = 'CREATE TABLE ' . $wpdb->prefix . BROO_CONDITION_STEP_TABLE_NAME . ' (
				step_id  bigint(20) NOT NULL AUTO_INCREMENT,
				condition_id bigint(20) NOT NULL,
				label varchar(50),
				action_name varchar(50) NOT NULL,
				created_dt datetime,
				PRIMARY KEY  (step_id)
		) ' . $charset_collate;
		
		dbDelta( $condition_step_query );
		
		$condition_step_meta_query = 'CREATE TABLE ' . $wpdb->prefix . BROO_CONDITION_STEP_META_TABLE_NAME . ' (
				meta_id  bigint(20) NOT NULL AUTO_INCREMENT,
				step_id  bigint(20) NOT NULL,
				meta_key varchar(255),
				meta_value longtext,
				PRIMARY KEY  (meta_id)
		) ' . $charset_collate;
		
		dbDelta( $condition_step_meta_query );
		
		$user_actions_meta_query = 'CREATE TABLE ' . $wpdb->prefix . BROO_USER_ACTION_META_TABLE_NAME . ' (
				meta_id bigint(20) NOT NULL AUTO_INCREMENT,
				user_action_id bigint(20) NOT NULL,
				meta_key varchar(255),
				meta_value longtext,
				PRIMARY KEY  (meta_id)
		) ' . $charset_collate;
		
		dbDelta( $user_actions_meta_query );
	}
	
	/**
	 * Uninstalls the plugin
	 */
	public static function uninstall_plugin() {
	
	}
	
	/**
	 * Redirects to about page on activation
	 */
	function redirect_about_page() {
		if ( get_option( Badgearoo::DO_ACTIVATION_REDIRECT_OPTION, false ) ) {
			delete_option( Badgearoo::DO_ACTIVATION_REDIRECT_OPTION );
			wp_redirect( 'admin.php?page=' . Badgearoo::ABOUT_PAGE_SLUG );
		}
	}
	
	/**
	 * Loads plugin text domain
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'badgearoo', false, dirname( plugin_basename( __FILE__) ) . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR );
	}
	
	/**
	 * Adds admin menus
	 */	
	public function add_admin_menus() {
		
		add_dashboard_page( __( 'About Badgearoo', 'badgearoo' ), '', 'manage_options', Badgearoo::ABOUT_PAGE_SLUG, 'broo_about_page' );
		add_submenu_page( 'edit.php?post_type=badge', __( 'Conditions', 'badgearoo' ), __( 'Conditions', 'badgearoo' ), 'manage_options', Badgearoo::CONDITIONS_PAGE_SLUG, 'broo_conditions_page' );
		
		global $wpdb;
		
		$query = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . BROO_USER_ASSIGNMENT_TABLE_NAME . ' WHERE status = "pending"';
		$pending_count = intval( $wpdb->get_var( $query ) );
		
		$pending_assignments_counter = '';
		if ( $pending_count > 0 ) {
			$pending_assignments_counter = '<span class="awaiting-mod count-' . $pending_count . '"><span class="pending-count">' . $pending_count . '</span></span>';
		}
		
		add_submenu_page( 'edit.php?post_type=badge', __( 'Assignments', 'badgearoo' ), __( 'Assignments', 'badgearoo' ) . $pending_assignments_counter, 'manage_options', Badgearoo::ASSIGNMENTS_PAGE_SLUG, 'broo_assignments_page' );
		add_submenu_page( 'edit.php?post_type=badge', __( 'Settings', 'badgearoo' ), __( 'Settings', 'badgearoo' ), 'manage_options', Badgearoo::SETTINGS_PAGE_SLUG, 'broo_settings_page' );
		add_submenu_page( 'edit.php?post_type=badge', __( 'Tools', 'badgearoo' ), __( 'Tools', 'badgearoo' ), 'manage_options', Badgearoo::TOOLS_PAGE_SLUG, 'broo_tools_page' );
		add_submenu_page( 'edit.php?post_type=badge', __( 'About', 'badgearoo' ), __( 'About', 'badgearoo' ), 'manage_options', Badgearoo::ABOUT_PAGE_SLUG, 'broo_about_page' );
	}
	
	/**
	 * Javascript and CSS used by the plugin
	 *
	 * @since 0.1
	 */
	public function admin_assets() {
		
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		
		$config_array = array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( Badgearoo::ID.'-nonce' )
		);
		
		wp_enqueue_script( 'broo-admin-script', plugins_url('assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'admin.js', __FILE__), array('jquery'), Badgearoo::VERSION, true );
		wp_localize_script( 'broo-admin-script', 'broo_admin_data', $config_array );

		wp_enqueue_style( 'broo-admin-style', plugins_url( 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'admin.css', __FILE__ ) );
		
		wp_enqueue_script ( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );
		wp_enqueue_media();
		
		// color picker
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
	}
	
	/**
	 * Javascript and CSS used by the plugin
	 *
	 * @since 0.1
	 */
	public function assets() {
		
		$general_settings = (array) get_option( 'broo_general_settings' );
		
		$config_array = array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( Badgearoo::ID.'-nonce' ),
				'cookie_path' => COOKIEPATH,
				'cookie_domain' => COOKIE_DOMAIN,
				'show_user_assignment_modal' => $general_settings['broo_show_user_assignment_modal']
		);

		wp_enqueue_style( 'broo-frontend-style', plugins_url( 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'frontend.css', __FILE__ ) );
		
		wp_enqueue_script( 'js-cookie-script', plugins_url('assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'js.cookie.js', __FILE__), array(), Badgearoo::VERSION, true );
		wp_enqueue_script( 'broo-frontend-script', plugins_url('assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'frontend.js', __FILE__), array( 'jquery', 'js-cookie-script' ), Badgearoo::VERSION, true );
		wp_localize_script( 'broo-frontend-script', 'broo_frontend_data', $config_array );
		
	}
	
	/**
	 * Register AJAX actions
	 */
	public function add_ajax_callbacks() {
		
		if ( is_admin() ) {
			add_action( 'wp_ajax_add_condition', 'broo_add_condition' );
			add_action( 'wp_ajax_delete_condition', 'broo_delete_condition' );
			add_action( 'wp_ajax_add_step', 'broo_add_step' );
			add_action( 'wp_ajax_delete_step', 'broo_delete_step' );
			add_action( 'wp_ajax_step_meta', 'broo_step_meta' );
			add_action( 'wp_ajax_save_condition', 'broo_save_condition' );
			add_action( 'wp_ajax_change_assignment_type', 'broo_change_assignment_type' );
			add_action( 'wp_ajax_nopriv_update_user_assignment_status', 'broo_update_user_assignment_status' );
			add_action( 'wp_ajax_update_user_assignment_status', 'broo_update_user_assignment_status' );
				
		}
		
		add_action( 'wp_ajax_user_leaderboard_filter', 'broo_user_leaderboard_filter' );
		add_action( 'wp_ajax_nopriv_user_leaderboard_filter', 'broo_user_leaderboard_filter' );
		
		add_action( 'wp_ajax_user_dashboard_assignments_more', 'broo_user_dashboard_assignments_more' );
		add_action( 'wp_ajax_nopriv_user_dashboard_assignments_more', 'broo_user_dashboard_assignments_more' );
		
	}
	
	function add_custom_css() {
		
		$general_settings = (array) get_option( 'broo_general_settings' );
		
		if ( $general_settings['broo_show_badges_inline'] == true ) {
			?>
			<style type="text/css">
				.broo-badge-container {
					display: inline-block;
				}
			</style>
			<?php 
		}
	}
	
	/**
	 * Registers Badge post type
	 */
	public function register_badge_post_type() {
		
		$slug = get_theme_mod( 'badge_permalink' );
		
		register_post_type( 'badge', array(
				'label' => __( 'Badges', 'badgearoo' ),
				'labels' => array(
						'name' => __( 'Badges', 'badgearoo' ),
						'singular_name' => __( 'Badge', 'badgearoo' ),
						'add_new_item' => __( 'Add New Badge', 'badgearoo' ),
						'edit_item' => __( 'Edit Badge', 'badgearoo' ),
						'new_item' => __( 'New Badge', 'badgearoo' ),
						'view_item' => __( 'View Badge', 'badgearoo' ),
						'search_items' => __( 'Search Badges', 'badgearoo' ),
						'not_found' => __( 'No badge found.', 'badgearoo' ),
						'not_found_in_trash' => __( 'No badges found in trash.', 'badgearoo' ),
						'parent_item_colon' => __( 'Parent Badge', 'badgearoo' )
				),
				'description' => '',
				'public' => true,
				'exclude_from_search' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'menu_position' => 71, // below Users
				'menu_icon' => 'dashicons-awards',
				/* 'capability_type' => 'badge',
				'capabilities' => array(
						'publish_posts' => 'publish_badge',
						'edit_posts' => 'edit_badges',
						'edit_others_posts' => 'edit_others_badges',
						'delete_posts' => 'delete_badges',
						'delete_others_posts' => 'delete_others_badges',
						'read_private_posts' => 'read_private_badges',
						'edit_post' => 'edit_badge',
						'delete_post' => 'delete_badge',
						'read_post' => 'read_badge',
				),*/
				'hierarchical' => false,
				'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
				'slug' => ( empty( $slug ) ) ? 'badge' : $slug,
				'taxonomies' => array( 'category' )
		) );
	}
	
	/**
	 * Adds badge image sizes in theme
	 */
	public function add_image_sizes() {
		add_image_size( 'badge-small', 32, 32, true );
		add_image_size( 'badge-large', 128, 128, true );
	}
}	

/**
 * Activate plugin
 */
function broo_activate_plugin() {

	if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
		add_option(Badgearoo::DO_ACTIVATION_REDIRECT_OPTION, true);
		Badgearoo::activate_plugin();
	}

}
register_activation_hook( __FILE__, 'broo_activate_plugin' );

/**
 * Uninstall plugin
*/
function broo_uninstall_plugin() {

	if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
		Badgearoo::uninstall_plugin();
	}
}
register_uninstall_hook( __FILE__, 'broo_uninstall_plugin' );

/*
 * Instantiate plugin main class
 */
function broo_plugin_init() {
	return Badgearoo::instance();
}
broo_plugin_init();