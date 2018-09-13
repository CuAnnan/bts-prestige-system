<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once plugin_dir_path(__FILE__).'class-bts-prestige-system-offices.php';
require_once plugin_dir_path(__FILE__).'class-bts-prestige-system-genres.php';

class Bts_Prestige_System_Domains
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
				{$prefix}domains d");
	}
	
	public static function get_managed_domains()
	{
		if(array_intersect(['administrator', 'national_membership_coordinator'],  wp_get_current_user()->roles))
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
		$officers	= Bts_Prestige_System_Offices::get_offices_by_id_domains($managed_domains);
		$genres		= Bts_Prestige_System_Genres::get_genres();
		require_once plugin_dir_path(__FILE__).'/partials/bts-prestige-system-management-page.php';
	}
}