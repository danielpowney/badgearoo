<?php
/*
 Plugin Name: User Badges
 Plugin URI: http://wordpress.org/plugins/user-badges/
 Description: Create your own badges for WordPress users. You can manually assign badges or configure automatic assignment of predefined badges to to users.
 Version: 1.0
 Author: Daniel Powney
 Author URI: http://danielpowney.com
 License: GPL2
 Text Domain: user-badges
 Domain Path: languages
 */

define( 'UB_ACTION_TABLE_NAME', 'ub_action' ); // stores predefined actions e.g. publishes a post
define( 'UB_USER_ACTION_TABLE_NAME', 'ub_user_action' ); // stores what actions a user has done
define( 'UB_USER_ASSIGNMENT_TABLE_NAME', 'ub_user_assignment' ); // stores badges/points assigned to users
define( 'UB_CONDITION_TABLE_NAME', 'ub_condition' );
define( 'UB_CONDITION_STEP_META_TABLE_NAME', 'ub_condition_step_meta' );
define( 'UB_CONDITION_STEP_TABLE_NAME', 'ub_condition_step' );
define( 'UB_USER_ACTION_META_TABLE_NAME', 'ub_user_action_meta' );


/**
 * User_Badges plugin class
 */
class User_Badges {

	/** Singleton *************************************************************/

	/**
	 * @var User_Badges The one true User_Badges
	 */
	private static $instance;

	/**
	 * Settings instance variable
	 */
	public $settings = null;
	
	public $api = null;
	
	public $actions = null;
	
	/**
	 * Constants
	 */
	const
	VERSION = '1.0',
	ID = 'user-badges',
	
	// options
	DO_ACTIVATION_REDIRECT_OPTION = 'ub_active_redirect',
	
	// slugs
	ABOUT_PAGE_SLUG = 'ub_about',
	BADGES_PAGE_SLUG = 'ub_badges',
	CONDITIONS_PAGE_SLUG = 'ub_conditions',
	SETTINGS_PAGE_SLUG = 'ub_settings',
	ASSIGNMENTS_PAGE_SLUG = 'ub_assignments';
	
	/**
	 *
	 * @return Multi_Rating
	 */
	public static function instance() {
	
		if ( ! isset( self::$instance )	&& ! ( self::$instance instanceof User_Badges ) ) {
	
			self::$instance = new User_Badges;
			
			self::$instance->includes();
			
			self::$instance->settings = new UB_Settings();
			self::$instance->api = new UB_API_Impl();

			//add_action( 'admin_enqueue_scripts', array( self::$instance, 'assets' ) );
			
			if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	
				add_action( 'admin_menu', array(self::$instance, 'add_admin_menus') );
				add_action( 'admin_enqueue_scripts', array( self::$instance, 'admin_assets' ) );
				add_action( 'admin_init', array( self::$instance, 'redirect_about_page' ) );
	
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
	
		return User_Badges::$instance;
	}
	
	function setup_actions() {
		
		self::$instance->actions = (array) apply_filters( 'ub_init_actions', self::$instance->actions );
		
		$actions_enabled = (array) get_option( 'ub_actions_enabled' );
		
		// Make sure all actions are stored in database
		if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
			
			global $wpdb;
			
			$query = 'SELECT DISTINCT name FROM ' . $wpdb->prefix . UB_ACTION_TABLE_NAME;
			
			$results = $wpdb->get_results( $query );
			
			$saved_actions= array();
			foreach ( $results as $row ) {
				array_push( $saved_actions, $row->name );
			}
			
			$missing_rows = array();
			foreach ( self::$instance->actions as $action_name => $action_data ) {
				
				if ( ! in_array( $action_name, $saved_actions ) ) {		
					array_push( $missing_rows, ' ( "' . esc_sql( $action_name ) . '", "' . esc_sql( $action_data['description'] ) 
							. '", "' . esc_sql( $action_data['source'] ) . '" )' );
				}
			}
			
			$count_missing = count( $missing_rows );
			if ( $count_missing > 0 ) {
				
				$query = 'INSERT INTO ' . $wpdb->prefix . UB_ACTION_TABLE_NAME . ' ( name, description, source ) VALUES';
			
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
		
		
		do_action( 'ub_init_actions_complete', self::$instance->actions );
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
		
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'actions' . DIRECTORY_SEPARATOR . 'common.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'actions' . DIRECTORY_SEPARATOR . 'buddypress.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'actions' . DIRECTORY_SEPARATOR . 'bbpress.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'actions' . DIRECTORY_SEPARATOR . 'woocommerce.php';
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'actions' . DIRECTORY_SEPARATOR . 'easy-digital-downloads.php';
		
		if ( is_admin() ) {
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'conditions.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'about.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'settings.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'badges.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-actions-table.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'users.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-assignments-table.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'assignments.php';
				
		}
		
	}
	
	/**
	 * Activates the plugin
	 */
	public static function activate_plugin() {
	
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		$action_query = 'CREATE TABLE ' . $wpdb->prefix . UB_ACTION_TABLE_NAME . ' (
				name varchar(50) NOT NULL,
				description varchar(50) NOT NULL,
				source varchar(100) NOT NULL,
				created_dt datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (name)
		) ENGINE=InnoDB AUTO_INCREMENT=1;';
		
		dbDelta( $action_query );
		
		$user_assignment_query = 'CREATE TABLE ' . $wpdb->prefix . UB_USER_ASSIGNMENT_TABLE_NAME . ' (
				id  bigint(20) NOT NULL AUTO_INCREMENT,
				user_id bigint(20) NOT NULL,
				condition_id bigint(20),
				type varchar(20) NOT NULL,
				value bigint(20) NOT NULL,
				created_dt datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				last_updated_dt datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				expiry_dt datetime DEFAULT NULL,
				status varchar(20) NOT NULL DEFAULT "approved",
				PRIMARY KEY  (id)
		) ENGINE=InnoDB AUTO_INCREMENT=1;';
		
		dbDelta( $user_assignment_query );
		
		$user_action_query = 'CREATE TABLE ' . $wpdb->prefix . UB_USER_ACTION_TABLE_NAME . ' (
				id  bigint(20) NOT NULL AUTO_INCREMENT,
				user_id bigint(20) NOT NULL,
				action_name varchar(50) NOT NULL,
				created_dt datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
		) ENGINE=InnoDB AUTO_INCREMENT=1;';
		
		dbDelta( $user_action_query );
			
		$condition_query = 'CREATE TABLE ' . $wpdb->prefix . UB_CONDITION_TABLE_NAME . ' (
				condition_id  bigint(20) NOT NULL AUTO_INCREMENT,
				name varchar(255) NOT NULL,
				points bigint(20) DEFAULT 0,
				badges longtext,
				created_dt datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				enabled tinyint(1) DEFAULT 1,
				expiry_value smallint(20) DEFAULT 0,
				expiry_unit varchar(20),
				recurring tinyint(1) DEFAULT 1,
				PRIMARY KEY  (condition_id)
		) ENGINE=InnoDB AUTO_INCREMENT=1;';
		
		// TODO expiry? e.g. 1 year, 1 month
		
		dbDelta( $condition_query );
		
		$condition_step_query = 'CREATE TABLE ' . $wpdb->prefix . UB_CONDITION_STEP_TABLE_NAME . ' (
				step_id  bigint(20) NOT NULL AUTO_INCREMENT,
				condition_id bigint(20) NOT NULL,
				label varchar(50),
				action_name varchar(50) NOT NULL,
				created_dt datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (step_id)
		) ENGINE=InnoDB AUTO_INCREMENT=1;';
		
		dbDelta( $condition_step_query );
		
		$condition_step_meta_query = 'CREATE TABLE ' . $wpdb->prefix . UB_CONDITION_STEP_META_TABLE_NAME . ' (
				meta_id  bigint(20) NOT NULL AUTO_INCREMENT,
				step_id  bigint(20) NOT NULL,
				meta_key varchar(255),
				meta_value longtext,
				PRIMARY KEY  (meta_id)
		) ENGINE=InnoDB AUTO_INCREMENT=1;';
		
		dbDelta( $condition_step_meta_query );
		
		$user_actions_meta_query = 'CREATE TABLE ' . $wpdb->prefix . UB_USER_ACTION_META_TABLE_NAME . ' (
				meta_id bigint(20) NOT NULL AUTO_INCREMENT,
				user_action_id bigint(20) NOT NULL,
				meta_key varchar(255),
				meta_value longtext,
				PRIMARY KEY  (meta_id)
		) ENGINE=InnoDB AUTO_INCREMENT=1;';
		
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
		if ( get_option( User_Badges::DO_ACTIVATION_REDIRECT_OPTION, false ) ) {
			delete_option( User_Badges::DO_ACTIVATION_REDIRECT_OPTION );
			wp_redirect( 'admin.php?page=' . User_Badges::ABOUT_PAGE_SLUG );
		}
	}
	
	/**
	 * Loads plugin text domain
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'user-badges', false, dirname( plugin_basename( __FILE__) ) . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR );
	}
	
	/**
	 * Adds admin menus
	 */	
	public function add_admin_menus() {
		
		add_dashboard_page( __( 'About User Badges', 'user-badges' ), '', 'manage_options', User_Badges::ABOUT_PAGE_SLUG, 'ub_about_page' );
		add_submenu_page( 'edit.php?post_type=badge', __( 'Conditions', 'user-badges' ), __( 'Conditions', 'user-badges' ), 'manage_options', User_Badges::CONDITIONS_PAGE_SLUG, 'ub_conditions_page' );
		
		global $wpdb;
		
		$query = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . UB_USER_ASSIGNMENT_TABLE_NAME . ' WHERE status = "pending"';
		$pending_count = intval( $wpdb->get_var( $query ) );
		
		$pending_assignments_counter = '';
		if ( $pending_count > 0 ) {
			$pending_assignments_counter = '<span class="awaiting-mod count-' . $pending_count . '"><span class="pending-count">' . $pending_count . '</span></span>';
		}
		
		add_submenu_page( 'edit.php?post_type=badge', __( 'Assignments', 'user-badges' ), __( 'Assignments', 'user-badges' ) . $pending_assignments_counter, 'manage_options', User_Badges::ASSIGNMENTS_PAGE_SLUG, 'ub_assignments_page' );
		add_submenu_page( 'edit.php?post_type=badge', __( 'Settings', 'user-badges' ), __( 'Settings', 'user-badges' ), 'manage_options', User_Badges::SETTINGS_PAGE_SLUG, 'ub_settings_page' );
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
				'ajax_nonce' => wp_create_nonce( User_Badges::ID.'-nonce' )
		);
		
		wp_enqueue_script( 'ub-admin-script', plugins_url('assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'admin.js', __FILE__), array('jquery'), User_Badges::VERSION, true );
		wp_localize_script( 'ub-admin-script', 'ub_admin_data', $config_array );

		wp_enqueue_style( 'ub-admin-style', plugins_url( 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'admin.css', __FILE__ ) );
		
		wp_enqueue_script ( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );
		wp_enqueue_media();
	}
	
	/**
	 * Javascript and CSS used by the plugin
	 *
	 * @since 0.1
	 */
	public function assets() {
		
		$config_array = array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( User_Badges::ID.'-nonce' )
		);

		wp_enqueue_style( 'ub-frontend-style', plugins_url( 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'frontend.css', __FILE__ ) );
		
		wp_enqueue_script( 'ub-frontend-script', plugins_url('assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'frontend.js', __FILE__), array('jquery'), User_Badges::VERSION, true );
		wp_localize_script( 'ub-frontend-script', 'ub_frontend_data', $config_array );
		
		
	}
	
	/**
	 * Register AJAX actions
	 */
	public function add_ajax_callbacks() {
		
		if ( is_admin() ) {
			add_action( 'wp_ajax_add_condition', 'ub_add_condition' );
			add_action( 'wp_ajax_delete_condition', 'ub_delete_condition' );
			add_action( 'wp_ajax_add_step', 'ub_add_step' );
			add_action( 'wp_ajax_delete_step', 'ub_delete_step' );
			add_action( 'wp_ajax_step_meta', 'ub_step_meta' );
			add_action( 'wp_ajax_save_condition', 'ub_save_condition' );
			add_action( 'wp_ajax_change_assignment_type', 'ub_change_assignment_type' );
			add_action( 'wp_ajax_nopriv_update_user_assignment_status', 'ub_update_user_assignment_status' );
			add_action( 'wp_ajax_update_user_assignment_status', 'ub_update_user_assignment_status' );
				
		}
		
		add_action( 'wp_ajax_user_leaderboard_filter', 'ub_user_leaderboard_filter' );
		add_action( 'wp_ajax_nopriv_user_leaderboard_filter', 'ub_user_leaderboard_filter' );
		
	}
	
	function add_custom_css() {
		
	}
	
	/**
	 * Registers Badge post type
	 */
	public function register_badge_post_type() {
		
		$slug = get_theme_mod( 'badge_permalink' );
		
		register_post_type( 'badge', array(
				'label' => 'Badges',
				'labels' => array(
						'name' => __( 'Badges', 'user-badges' ),
						'singular_name' => __( 'Badge', 'user-badges' ),
						'add_new_item' => __( 'Add New Badge', 'user-badges' ),
						'edit_item' => __( 'Edit Badge', 'user-badges' ),
						'new_item' => __( 'New Badge', 'user-badges' ),
						'view_item' => __( 'View Badge', 'user-badges' ),
						'search_items' => __( 'Search Badges', 'user-badges' ),
						'not_found' => __( 'No badge found.', 'user-badges' ),
						'not_found_in_trash' => __( 'No badges found in trash.', 'user-badges' ),
						'parent_item_colon' => __( 'Parent Badge', 'user-badges' )
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
				'supports' => array( 'title', 'editor', 'excerpt' ),
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
function ub_activate_plugin() {

	if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
		add_option(User_Badges::DO_ACTIVATION_REDIRECT_OPTION, true);
		User_Badges::activate_plugin();
	}

}
register_activation_hook( __FILE__, 'ub_activate_plugin' );

/**
 * Uninstall plugin
*/
function ub_uninstall_plugin() {

	if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
		User_Badges::uninstall_plugin();
	}
}
register_uninstall_hook( __FILE__, 'ub_uninstall_plugin' );

/*
 * Instantiate plugin main class
 */
function ub_plugin_init() {
	return User_Badges::instance();
}
ub_plugin_init();