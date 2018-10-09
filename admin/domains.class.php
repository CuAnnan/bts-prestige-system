<?php namespace BTS_Prestige\Admin;

class Domains
{
	public static function get_managed_domains_for_id_users($id_users)
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		
		return $wpdb->get_results($wpdb->prepare("SELECT
				d.id				AS id,
				d.name				AS name,
				d.nmc_code			AS nmc_code,
				d.number			AS number,
				d.location			AS location
			FROM
				{$prefix}officers o
				LEFT JOIN {$prefix}domains d ON (o.id_domains = d.id)
			WHERE
					o.id_users = %d
				AND o.id_venues	IS NULL	
				AND	o.id_superior IS NULL",
			$id_users
		));
	}
	
	public static function get_managed_domains_for_logged_in_user()
	{
		return self::get_managed_domains_for_id_users(get_current_user_id());
	}
	
	public static function get_all_domains()
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		
		return $wpdb->get_results("SELECT
				d.id				AS id,
				d.name				AS name,
				d.nmc_code			AS nmc_code,
				d.number			AS number,
				d.location			AS location
			FROM
				{$prefix}domains d
			ORDER 
				BY id");
	}
	
	public static function get_managed_domains()
	{
		if(array_intersect(['administrator', BTS_NATIONAL_OFFICE_ROLE],  wp_get_current_user()->roles))
		{
			return self::get_all_domains();
		}
		else
		{
			return self::get_managed_domains_for_logged_in_user();
		}
	}
	
	public static function get_managed_domain_ids()
	{
		$managed_domains = self::get_managed_domains();
		$ids = [];
		foreach($managed_domains as $domain)
		{
			$ids[] = $domain->id;
		}
		return $ids;
	}
	
	public static function manages_domain($id_domains)
	{
		$domains = self::get_managed_domain_ids();
		
		return (array_search($id_domains, $domains) !== false);
	}
	
	public static function show_domain_management_page()
	{
		$managed_domains = self::get_managed_domains();
		$officers		= \BTS_Prestige\Admin\Offices::get_offices_by_id_domains($managed_domains);
		$genres			= \BTS_Prestige\Admin\Genres::get_genres();
		$usersMetaData	= \BTS_Prestige\Admin\Users::getUsermeta();
		require_once plugin_dir_path(__FILE__).'/partials/bts-prestige-system-management-page.php';
	}
}