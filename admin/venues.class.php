<?php namespace BTS_Prestige\Admin;

class Venues
{
	public static function get_managed_venues_for_id_users($id_users)
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		
		return $wpdb->get_results($wpdb->prepare("
			SELECT
				v.id				AS id,
				v.name				AS name,
				v.nmc_code			AS nmc_code,
				g.name				AS genre,
                g.id				AS id_genres,
				d.name				AS domain_name,
                d.id				AS id_domains
			FROM
				{$prefix}venues v
				LEFT JOIN {$prefix}officers o	ON (v.id = o.id_venues)
				LEFT JOIN {$prefix}domains d	ON (v.id_domains = d.id)
				LEFT JOIN {$prefix}genres g		ON (v.id_genres = g.id)
			WHERE
					o.id_users = %d
				AND	v.id_domains IS NOT NULL",
			$id_users
		));
	}
	
	public static function get_all_active_venues()
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		return $wpdb->get_results("
			SELECT
				v.id				AS id,
				v.name				AS name,
				v.nmc_code			AS nmc_code,
				g.name				AS genre,
                g.id				AS id_genres,
				d.name				AS domain_name,
                d.id				AS id_domains
			FROM
				{$prefix}venues v
				LEFT JOIN {$prefix}domains d	ON (v.id_domains = d.id)
				LEFT JOIN {$prefix}genres g	ON (v.id_genres = g.id)
			WHERE
						v.id_domains IS NOT NULL
				AND		v.active = 1");
	}
}