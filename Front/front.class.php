<?php namespace BTS_Prestige\Front;

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
class Front
{
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
		
	}
	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		
	}
	
	/**
	 * The method to show the user fields for the org
	 * @param type $user
	 */
	public function extra_user_profile_fields($user)
	{
		require(plugin_dir_path(__FILE__).'partials/custom-user-fields.php');
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