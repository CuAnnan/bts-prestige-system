<?php namespace BTS_Prestige\Admin;

class Admin
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
		wp_enqueue_style( $this->plugin_name.'admin', plugin_dir_url( __FILE__ ) . 'css/bts-prestige-system-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'autocomplete', plugin_dir_url( __FILE__ ) . 'css/easy-autocomplete.min.css', array(), $this->version, 'all');
		wp_enqueue_script( $this->plugin_name.'autocomplete', plugin_dir_url( __FILE__ ) . 'js/jquery.easy-autocomplete.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'admin', plugin_dir_url( __FILE__ ) . 'js/bts-prestige-system-admin.js', array( 'jquery' ), $this->version, false );
		
		Domains::show_domain_management_page();
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
		add_submenu_page(
			'bts_prestige_log',
			'Prestige Audit',
			'Prestige Audit',
			BTS_PRESTIGE_MANAGEMENT_ROLE,
			'bts_prestige_audit',
			array($this, 'audit_prestige_page')
		);
	}
	
	public function view_prestige()
	{
		add_submenu_page(
			'bts_prestige_log',
			'Prestige Log Search',
			'Prestige Log Search',
			BTS_PRESTIGE_MANAGEMENT_ROLE,
			'bts_prestige_view',
			array($this, 'view_prestige_page')
		);
	}
	
	public function audit_prestige_page()
	{
		wp_enqueue_style($this->plugin_name.'data_tables', 'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css', array(), $this->version, 'all');
		wp_enqueue_style( $this->plugin_name.'admin', plugin_dir_url( __FILE__ ) . 'css/easy-autocomplete.min.css', array(), $this->version, 'all');
		wp_enqueue_script( $this->plugin_name.'autocomplete', plugin_dir_url( __FILE__ ) . 'js/jquery.easy-autocomplete.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script($this->plugin_name.'data_tables', 'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name.'prestige_globals', plugin_dir_url(__FILE__).'js/Prestige.class.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name.'prestige_auditing', plugin_dir_url(__FILE__).'js/prestige_auditing.js', array('jquery'), $this->version, false);

		Prestige::show_prestige_auditing_page();
	}
	
	public function show_prestige_log()
	{
		wp_enqueue_style($this->plugin_name.'data_tables', 'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css', array(), $this->version, 'all');
		wp_enqueue_script($this->plugin_name.'data_tables', 'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name.'prestige_globals', plugin_dir_url(__FILE__).'js/Prestige.class.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name.'prestige_management', plugin_dir_url(__FILE__).'js/prestige_management.js', array('jquery'), $this->version, false);
		Prestige::show_prestige_management_page();
	}
	
	public function view_prestige_page()
	{
		wp_enqueue_style($this->plugin_name.'admin', plugin_dir_url( __FILE__ ) . 'css/easy-autocomplete.min.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name.'data_tables', 'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name.'view_prestige', plugin_dir_url(__FILE__).'css/prestige_log_search.css', array(), $this->version, 'all');
		wp_enqueue_script($this->plugin_name.'autocomplete', plugin_dir_url( __FILE__ ) . 'js/jquery.easy-autocomplete.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script($this->plugin_name.'data_tables', 'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name.'prestige_globals', plugin_dir_url(__FILE__).'js/Prestige.class.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name.'view_prestige', plugin_dir_url(__FILE__).'js/prestige_log_search.js', array('jquery'), $this->version, false);
		Prestige::view_prestige_page();
	}
	
	public function add_prestige_note()
	{
		$this->return_json(\BTS_Prestige\Admin\Prestige::try_to_add_record_note(
			filter_input(INPUT_POST, 'id_prestige_record'),
			filter_input(INPUT_POST, 'note_text'),
			filter_input(INPUT_POST, 'status'),
			filter_input(INPUT_POST, 'id_acting_officer')
		));
	}
	
	public function add_prestige_record()
	{
		$this->return_json(\BTS_Prestige\Admin\Prestige::add_prestige_claim(
			filter_input(INPUT_POST, 'id_officers'),
			filter_input(INPUT_POST, 'id_prestige_actions'),
			filter_input(INPUT_POST, 'prestige_amount'),
			filter_input(INPUT_POST, 'prestige_type'),
			filter_input(INPUT_POST, 'reason'),
			filter_input(INPUT_POST, 'date')
		));
	}
	
	public function add_prestige_reward()
	{
		$this->return_json(\BTS_Prestige\Admin\Prestige::add_prestige_reward(
			filter_input(INPUT_POST, 'id_users'),
			filter_input(INPUT_POST, 'id_officers'),
			filter_input(INPUT_POST, 'id_prestige_actions'),
			filter_input(INPUT_POST, 'prestige_amount'),
			filter_input(INPUT_POST, 'prestige_type'),
			filter_input(INPUT_POST, 'reason'),
			filter_input(INPUT_POST, 'status'),
			filter_input(INPUT_POST, 'date')
		));
	}
        
        public function add_domain()
        {
            $this->return_json(Domains::add_domain(
                filter_input(INPUT_POST, 'name'),
                filter_input(INPUT_POST, 'nmc_code'),
                filter_input(INPUT_POST, 'number'),
                filter_input(INPUT_POST, 'location'),
                filter_input(INPUT_POST, 'parent_id')
            ));
        }
        
        public function add_venue_to_domain()
        {
            $this->return_json(Venues::add_venue_to_domain(
                filter_input(INPUT_POST, 'name'),
                filter_input(INPUT_POST, 'id_domains'),
                filter_input(INPUT_POST, 'id_genres'),
                filter_input(INPUT_POST, 'nmc_code'),
                filter_input(INPUT_POST, 'active')
            ));
        }       
        public function add_genre()
        {
            $this->return_json(Genres::add_genre(
                filter_input(INPUT_POST, 'name'),
                filter_input(INPUT_POST, 'short_name')
            ));
        }
	
	public function edit_prestige_record()
	{
		$this->return_json(Prestige::edit_prestige_record(
			filter_input(INPUT_POST, 'id_prestige_record'),
			filter_input(INPUT_POST, 'id_officers'),
			filter_input(INPUT_POST, 'id_prestige_actions'),
			filter_input(INPUT_POST, 'reward_amount'),
			filter_input(INPUT_POST, 'reward_type'),
			filter_input(INPUT_POST, 'status'),
			filter_input(INPUT_POST, 'date_claimed')
		));
	}
	
	public function update_office()
	{
		$this->return_json(\BTS_Prestige\Admin\Offices::update_office(
			filter_input(INPUT_POST, 'id_domains'),
			filter_input(INPUT_POST, 'id'),
			$_POST
		));
	}
	
	public function reset_permissions()
	{
		$this->return_json(\BTS_Prestige\Admin\Offices::update_office_roles());
	}
	
	public function fetch_user_prestige()
	{
		$this->return_json(\BTS_Prestige\Admin\Prestige::fetch_user_prestige(
			filter_input(INPUT_POST, 'id_users')
		));
	}
	
	public function return_json($array)
	{
		header("Content-type: text/json");
		echo json_encode($array);
		exit();
	}
}
