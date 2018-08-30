<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://wing.so-4pt.net
 * @since      1.0.0
 *
 * @package    Bts_Prestige_System
 * @subpackage Bts_Prestige_System/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Bts_Prestige_System
 * @subpackage Bts_Prestige_System/public
 * @author     wing <eamonn.kearns@so-4pt.net>
 */
class Bts_Prestige_System_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bts-prestige-system-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bts-prestige-system-public.js', array( 'jquery' ), $this->version, false );

	}
	
	/**
	 * The method to show the user fields for the org
	 * @param type $user
	 */
	public function extra_user_profile_fields($user)
	{
		require(plugin_dir_path(__FILE__).'partials/bts-prestige-system-custom-user-fields.php');
	}
	
	/**
	 * Save the user fields the org needs
	 * @param type $user_id The id of the user to update
	 * @return boolean Whether or not the user fields were updated
	 */
	public function save_extra_user_profile_fields($user_id)
	{
		if ( !current_user_can( 'edit_user', $user_id ) )
		{
			return false;
		}
		$fields = ['address1', 'address2', 'zip', 'city', 'state', 'country', 'phone'];
		foreach($fields as $field)
		{
			update_user_meta($user_id, $field, $_POST[$field]);
		}
		return true;
	}
	
	public function post_register_action($user_id)
	{
		
	}

}
