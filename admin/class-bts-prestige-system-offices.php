<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Bts_Prestige_System_Offices
{
	/**
	 * 
	 * @global type $wpdb
	 * @param type $domains
	 * @return type
	 */
	public static function get_offices_by_id_domains($domains)
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		$place_holders = [];
		$id_domains = [];
		foreach($domains as $domain)
		{
			$place_holders[] = "%d";
			$id_domains[] = $domain->id;
		}
		$qry_string = $wpdb->prepare("
			SELECT 
				o.id				AS id_officers,
				o.id_venues			AS id_venues,
				o.id_domains		AS id_domains,
				o.id_users			AS id_users,
				o.id_superior		AS id_superior,
				o.title				AS title,
				o.email				AS email,
				o.chain				AS chain,
				o.id_superior		AS id_superior,
				o.date_appointed	AS date_appointed,
				um_fn.meta_value	AS first_name,
                um_sn.meta_value	AS last_name,
                um_cn.meta_value	AS membership_number,
				g.id				AS id_genres,
				g.name				AS genre_name
			FROM 
							wp_bts_officers o
				LEFT JOIN	wp_users 	u 		ON (o.id_users = u.ID)
                LEFT JOIN	wp_usermeta um_fn 	ON (u.ID = um_fn.user_id)
                LEFT JOIN	wp_usermeta um_sn 	ON (u.ID = um_sn.user_id)
                LEFT JOIN	wp_usermeta um_cn	ON (u.ID = um_cn.user_id)
				LEFT JOIN	wp_bts_venues v		ON (o.id_venues = v.id)
				LEFT JOIN	wp_bts_genres g		ON (v.id_genres = g.id)
            WHERE
				um_fn.meta_key = 'first_name'
				AND um_sn.meta_key = 'last_name'
				AND um_cn.meta_key = 'membership_number'
				AND o.id_domains IN (".join($place_holders, ', ').")
				AND (o.id_venues IS NULL OR v.active = 1)
			ORDER BY
				`id_domains` ASC,
				id_genres ASC,
				chain ASC,
				id_superior ASC
			",
			$id_domains
		);
		$results = $wpdb->get_results($qry_string);	
		$offices = [];
		foreach($results as $result)
		{
			if(!isset($offices[$result->id_domains]))
			{
				$offices[$result->id_domains] = [];
			}
			$offices[$result->id_domains][] = $result;
		}
		return $offices;
	}
}