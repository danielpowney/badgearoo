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

define( 'UB_BADGES_TABLE_NAME', 'ub_badges' );
define( 'UB_USER_BADGES_TABLE_NAME', 'ub_user_badges' );

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
	BADGES_PAGE_SLUG = 'ub_badges';
	
	/**
	 *
	 * @return Multi_Rating
	 */
	public static function instance() {
	
		if ( ! isset( self::$instance )
				&& ! ( self::$instance instanceof User_Badges ) ) {
	
					self::$instance = new User_Badges;
					
					self::$instance->includes();
					
					self::$instance->settings = new UB_Settings();
					self::$instance->api = new UB_API_Impl();
	
					add_action( 'admin_enqueue_scripts', array( self::$instance, 'assets' ) );
	
					if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	
						add_action( 'admin_menu', array(self::$instance, 'add_admin_menus') );
						add_action( 'admin_enqueue_scripts', array( self::$instance, 'admin_assets' ) );
						add_action( 'admin_init', array( self::$instance, 'redirect_about_page' ) );
	
					} else {
						add_action( 'wp_enqueue_scripts', array( self::$instance, 'assets' ) );
					}
	
					add_action( 'wp_head', array( self::$instance, 'add_custom_css') );
					add_action( 'init', array( self::$instance, 'load_textdomain' ) );
					
					add_image_size( 'user-badges', 64, 64, true );
	
					self::$instance->add_ajax_callbacks();
				}
	
				return User_Badges::$instance;
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
		
		require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR . 'badge.php';
		
		if ( is_admin() ) {
				
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'about.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'settings.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'badges.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-badges-table.php';
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'users.php';
		}
	}
	
	/**
	 * Activates the plugin
	 */
	public static function activate_plugin() {
	
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		$badges_query = 'CREATE TABLE ' . $wpdb->prefix . UB_BADGES_TABLE_NAME . ' (
				name varchar(100) NOT NULL,
				description varchar(400),
				url varchar(400),
				enabled tinyint(1) DEFAULT 1,
				created_dt datetime NOT NULL,
				PRIMARY KEY  (name)
		) ENGINE=InnoDB AUTO_INCREMENT=1;';
		
		dbDelta( $badges_query );
		
		$user_badges_query = 'CREATE TABLE ' . $wpdb->prefix . UB_USER_BADGES_TABLE_NAME . ' (
				badge_name varchar(100) NOT NULL,
				user_id bigint(20) NOT NULL,
				created_dt datetime NOT NULL,
				PRIMARY KEY  (badge_name,user_id)
		) ENGINE=InnoDB AUTO_INCREMENT=1;';
		
		dbDelta( $user_badges_query );
		
		$wpdb->replace(
				$wpdb->prefix . UB_BADGES_TABLE_NAME,
				array(
						'name' =>  __( 'User Published Post', 'user-badges' ),
						'description' => __( 'Has published posts.', 'user-badges' ),
						'url' =>  plugins_url( 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'trophy.png', __FILE__),
						'enabled' => true,
						'created_dt' => current_time( 'mysql' )
				),
				array( '%s', '%s', '%s', '%s', '%d', '%s' )
		);
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
		add_users_page( __( 'Badges', 'user-badges' ), __( 'Badges', 'user-badges' ), 'manage_options', User_Badges::BADGES_PAGE_SLUG, 'ub_badges_page' );
	}
	
	/**
	 * Javascript and CSS used by the plugin
	 *
	 * @since 0.1
	 */
	public function admin_assets() {
		
		wp_enqueue_script( 'jquery' );
		
		$config_array = array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( User_Badges::ID.'-nonce' )
		);
		
		wp_enqueue_script( 'ub-admin-script', plugins_url('assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'admin.js', __FILE__), array('jquery'), User_Badges::VERSION, true );
		wp_localize_script( 'ub-admin-script', 'ub_admin_data', $config_array );

		wp_enqueue_style( 'ub-admin-style', plugins_url( 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'admin.css', __FILE__ ) );
		
		
		wp_enqueue_media();
	}
	
	/**
	 * Javascript and CSS used by the plugin
	 *
	 * @since 0.1
	 */
	public function assets() {

		wp_enqueue_style( 'ub-frontend-style', plugins_url( 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'frontend.css', __FILE__ ) );
		
		
	}
	
	/**
	 * Register AJAX actions
	 */
	public function add_ajax_callbacks() {
		
	}
	
	function add_custom_css() {
		
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