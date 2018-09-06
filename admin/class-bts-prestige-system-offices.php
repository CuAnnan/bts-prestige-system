<?php
require_once plugin_dir_path(__FILE__).'class-bts-prestige-system-domains.php';

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
							{$prefix}officers		o
				LEFT JOIN	{$wpdb->prefix}users	u 		ON (o.id_users = u.ID)
                LEFT JOIN	{$wpdb->prefix}usermeta	um_fn 	ON (u.ID = um_fn.user_id)
                LEFT JOIN	{$wpdb->prefix}usermeta	um_sn 	ON (u.ID = um_sn.user_id)
                LEFT JOIN	{$wpdb->prefix}usermeta	um_cn	ON (u.ID = um_cn.user_id)
				LEFT JOIN	{$prefix}venues			v		ON (o.id_venues = v.id)
				LEFT JOIN	{$prefix}genres			g		ON (v.id_genres = g.id)
            WHERE
					um_fn.meta_key = 'first_name'
				AND	um_sn.meta_key = 'last_name'
				AND	um_cn.meta_key = 'membership_number'
				AND	o.id_domains IN (".join($place_holders, ', ').")
				AND	(o.id_venues IS NULL OR v.active = 1)
			ORDER BY
				`id_domains` ASC,
				id_genres ASC,
				chain ASC,
				id_officers ASC,
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
	
	private static function prepare_office_fields($fields)
	{
		unset($fields['action'], $fields['genre_name']);
		foreach($fields as $key=>$val)
		{
			if(!$val)
			{
				unset($fields[$key]);
			}
		}
		return $fields;
	}
	
	public static function update_office($id_domains, $id_officers, $fields)
	{
		global $wpdb;
		if(!Bts_Prestige_System_Domains::manages_domain($id_domains))
		{
			return ['success'=>false,'error'=>'domain not in user purview'];
		}
		$fields = self::prepare_office_fields($fields);
		
		$table = $wpdb->prefix.BTS_TABLE_PREFIX."officers";
		$wpdb->update($table,$fields,['id'=>$id_officers]);
		if($wpdb->last_error !== '')
		{
			return ['success'=>false, 'message'=>$wpdb->last_error];
		}
		
		return ['success'=>true];
	}
	
}