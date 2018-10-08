<?php
require_once plugin_dir_path( __FILE__ ).'class-bts-prestige-system-domains.php';
require_once plugin_dir_path(__FILE__).'class-bts-prestige-system-offices.php';
require_once plugin_dir_path(__FILE__).'class-bts-prestige-system-prestige.php';

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
		wp_register_style('prefix_bootstrap', '//stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css');
		wp_enqueue_style('prefix_bootstrap');
		
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
		
		wp_register_script('prefix_bootstrap', '//stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js');
		wp_enqueue_script('prefix_bootstrap');
	}
	
	public function manage_club_structure()
	{
		add_menu_page(
			'Club Structure',
			'Manage Club Structure',
			BTS_MANAGE_CLUB_STRUCTURE_ROLE,
			'bts_manage_club_structure',
			array($this, 'manage_club_structure_page')
		);
	}
	
	private function user_is_admin()
	{
		return in_array('administrator',  wp_get_current_user()->roles);
	}
	
	/**
	 * Determine what permissions the currently logged in user has based on 
	 */
	public function manage_club_structure_page()
	{
		wp_enqueue_style( $this->plugin_name.'autocomplete', plugin_dir_url( __FILE__ ) . 'css/bts-prestige-system-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'admin', plugin_dir_url( __FILE__ ) . 'css/easy-autocomplete.min.css', array(), $this->version, 'all');
		wp_enqueue_script( $this->plugin_name.'autocomplete', plugin_dir_url( __FILE__ ) . 'js/jquery.easy-autocomplete.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'admin', plugin_dir_url( __FILE__ ) . 'js/bts-prestige-system-admin.js', array( 'jquery' ), $this->version, false );
		
		Bts_Prestige_System_Domains::show_domain_management_page();
	}
	
	public function manage_prestige()
	{
		add_menu_page(
			'Prestige',
			'Prestige',
			'read',
			'bts_prestige_log',
			array($this, 'show_prestige_log')
		);
	}
	
	public function audit_prestige()
	{
		add_menu_page(
			'Prestige Audit',
			'Prestige Audit',
			BTS_PRESTIGE_MANAGEMENT_ROLE,
			'bts_prestige_audit',
			array($this, 'audit_prestige_page')
		);
	}
	
	public function audit_prestige_page()
	{
		wp_enqueue_style($this->plugin_name.'data_tables', 'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css', array(), $this->version, 'all');
		wp_enqueue_style( $this->plugin_name.'admin', plugin_dir_url( __FILE__ ) . 'css/easy-autocomplete.min.css', array(), $this->version, 'all');
		wp_enqueue_script( $this->plugin_name.'autocomplete', plugin_dir_url( __FILE__ ) . 'js/jquery.easy-autocomplete.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script($this->plugin_name.'data_tables', 'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name.'prestige_auditing', plugin_dir_url(__FILE__).'js/prestige_auditing.js', array('jquery'), $this->version, false);

		Bts_Prestige_System_Prestige::show_prestige_auditing_page();
	}
	
	public function show_prestige_log()
	{
		wp_enqueue_style($this->plugin_name.'data_tables', 'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css', array(), $this->version, 'all');
		wp_enqueue_script($this->plugin_name.'data_tables', 'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name.'prestige_management', plugin_dir_url(__FILE__).'js/prestige_management.js', array('jquery'), $this->version, false);
		Bts_Prestige_System_Prestige::show_prestige_management_page();
	}
	
	public function add_prestige_note()
	{
		header("Content-type: text/json");
		echo json_encode(Bts_Prestige_System_Prestige::try_to_add_record_note(
			filter_input(INPUT_POST, 'id_prestige_record'),
			filter_input(INPUT_POST, 'note_text'),
			filter_input(INPUT_POST, 'status'),
			filter_input(INPUT_POST, 'id_acting_officer')
		));
		exit();
	}
	
	public function add_prestige_record()
	{
		header("Content-type: text/json");
		// signature of the method
		
		echo json_encode(Bts_Prestige_System_Prestige::add_prestige_claim(
			filter_input(INPUT_POST, 'id_officers'),
			filter_input(INPUT_POST, 'id_prestige_actions'),
			filter_input(INPUT_POST, 'prestige_amount'),
			filter_input(INPUT_POST, 'prestige_type'),
			filter_input(INPUT_POST, 'reason'),
			filter_input(INPUT_POST, 'date')
		));
		exit();
	}
	
	public function add_prestige_reward()
	{
		header('Content type: text/json');
		
		echo json_encode(Bts_Prestige_System_Prestige::add_prestige_reward(
			filter_input(INPUT_POST, 'id_users'),
			filter_input(INPUT_POST, 'id_officers'),
			filter_input(INPUT_POST, 'id_prestige_actions'),
			filter_input(INPUT_POST, 'prestige_amount'),
			filter_input(INPUT_POST, 'prestige_type'),
			filter_input(INPUT_POST, 'reason'),
			filter_input(INPUT_POST, 'date')
		));
		
		exit();
	}
	
	public function update_office()
	{
		header("Content-type: text/json");
		echo json_encode(Bts_Prestige_System_Offices::update_office(
			filter_input(INPUT_POST, 'id_domains'),
			filter_input(INPUT_POST, 'id'),
			$_POST
		));
		exit();
	}
	
	public function reset_permissions()
	{
		header("Content-type: text/json");
		echo json_encode(Bts_Prestige_System_Offices::update_office_roles());
		exit();
	}
	
}
