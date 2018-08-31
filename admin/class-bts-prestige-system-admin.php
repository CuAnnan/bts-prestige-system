<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://wing.so-4pt.net
 * @since      1.0.0
 *
 * @package    Bts_Prestige_System
 * @subpackage Bts_Prestige_System/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bts_Prestige_System
 * @subpackage Bts_Prestige_System/admin
 * @author     wing <eamonn.kearns@so-4pt.net>
 */
class Bts_Prestige_System_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bts_Prestige_System_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bts_Prestige_System_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bts-prestige-system-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bts_Prestige_System_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bts_Prestige_System_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bts-prestige-system-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	public function manage_club_structure()
	{
		if(!current_user_can('manage_club_structure') && $this->is_admin())
		{
			return;
		}
		add_menu_page(
			'Club Structure',
			'Manage Club Structure',
			'manage_club_structure',
			'bts_manage_club_structure',
			array($this, 'manage_club_structure_page')
		);
	}
	
	private function is_admin()
	{
		return in_array('administrator',  wp_get_current_user()->roles);
	}
	
	private function holds_venue_office($loggedInUserId, $positionType, $venue_name, $domain_name)
	{
		
	}
	
	private function holds_domain_office($loggedInUserId, $positionType, $domain_name)
	{
		global $wpdb;
		require_once('');
		// this should be 1 or zero
		$countOfficers = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}{BTS_TABLE_PREFIX}officers WHERE id_members = %d AND positioin = %s", [$loggedInUserId, $position]));
	}
	
	/**
	 * Determine what permissions the currently logged in user has based on 
	 */
	public function manage_club_structure_page()
	{
		
		if(!$this->is_admin() && !$this->holds_office('national_membership_coordinator'))
		{
			wp_die('This is not permitted for your account');
		}
		echo '<div class="wrap">';
		
		echo '</div>';
	}
	
	
}
