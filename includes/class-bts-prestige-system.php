<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://wing.so-4pt.net
 * @since      1.0.0
 *
 * @package    Bts_Prestige_System
 * @subpackage Bts_Prestige_System/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Bts_Prestige_System
 * @subpackage Bts_Prestige_System/includes
 * @author     wing <eamonn.kearns@so-4pt.net>
 */
class Bts_Prestige_System {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Bts_Prestige_System_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'bts-prestige-system';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Bts_Prestige_System_Loader. Orchestrates the hooks of the plugin.
	 * - Bts_Prestige_System_i18n. Defines internationalization functionality.
	 * - Bts_Prestige_System_Admin. Defines all hooks for the admin area.
	 * - Bts_Prestige_System_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bts-prestige-system-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bts-prestige-system-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bts-prestige-system-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bts-prestige-system-public.php';

		$this->loader = new Bts_Prestige_System_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Bts_Prestige_System_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Bts_Prestige_System_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Bts_Prestige_System_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts',		$plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts',		$plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu',				$plugin_admin, 'manage_club_structure');
		$this->loader->add_action( 'admin_menu',				$plugin_admin, 'manage_prestige');
		$this->loader->add_action( 'admin_menu',				$plugin_admin, 'audit_prestige');
		$this->loader->add_action( 'wp_ajax_update_office',		$plugin_admin, 'update_office');
		$this->loader->add_action( 'wp_ajax_add_prestige_note', $plugin_admin, 'add_prestige_note');
		$this->loader->add_action( 'wp_ajax_reset_permissions',	$plugin_admin, 'reset_permissions');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Bts_Prestige_System_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts',		$plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts',		$plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'user_register',				$plugin_public, 'post_register_action' );
		$this->add_profile_functions($plugin_public);
	}
	
	/**
	 * Add the profile action hooks
	 * @param	type $plugin_public	The plugin class reference
	 * @since	1.0.0
	 * @access	private
	 */
	private function add_profile_functions($plugin_public)
	{
		$this->loader->add_action('show_user_profile',			$plugin_public, 'extra_user_profile_fields');
		$this->loader->add_action('edit_user_profile',			$plugin_public, 'extra_user_profile_fields');
		$this->loader->add_action('personal_options_update',	$plugin_public, 'save_extra_user_profile_fields');
		$this->loader->add_action('edit_user_profile_update',	$plugin_public, 'save_extra_user_profile_fields');
	}
	
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Bts_Prestige_System_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
