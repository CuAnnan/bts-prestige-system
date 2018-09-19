<?php

class Bts_Prestige_System_Venues
{
	public static function get_managed_venues_for_id_users($id_users)
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		
		return $wpdb->get_results($wpdb->prepare("SELECT
				v.id				AS id,
				v.name				AS name,
				v.nmc_code			AS nmc_code,
				g.name				AS genre,
				d.name				AS domain_name
			FROM
				{$prefix}venues v
				LEFT JOIN {$prefix}domains d	ON (o.id_domains = d.id)
				LEFT JOIN {$prefix}genres g		ON (o.id_genres = g.id)
			WHERE
					o.id_users = %d
				AND o.id_venues	IS NOT NULL	
				AND	o.id_superior IS NULL",
			$id_users
		));
	}
}