<?php namespace BTS_Prestige\Admin;

class Offices
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
	
	/**
	 * Strip out the fields that aren't necessary for the database
	 * @param type $fields
	 * @return type
	 */
	private static function prepare_office_fields($fields)
	{
		unset($fields['action'], $fields['genre_name'], $fields['first_name'], $fields['last_name'], $fields['membership_number']);
		foreach($fields as $key=>$val)
		{
			if(!$val)
			{
				unset($fields[$key]);
			}
		}
		return $fields;
	}
	
	/**
	 * When setting the user id of an office, it is not guaranteed that we remove
	 * all offices that manage domains from a user. They could either be a national officer
	 * with officer privileges or the domain coordinator of more than one domain.
	 * 
	 * Therefore, if an officer has more than one office, we don't remove their 
	 * privilege. But if they have only one, we remove their privilege.
	 */
	public static function remove_manage_club_structure_role($id_users)
	{
		$offices = Bts_Prestige_System_Domains::get_managed_domains_for_id_users($id_users);
		$office_count = count($offices);
		
		if($office_count == 1)
		{
			$user = new \WP_User($id_users);
			$user->remove_role(BTS_MANAGE_CLUB_STRUCTURE_ROLE);
			$user->remove_cap(BTS_MANAGE_CLUB_STRUCTURE_ROLE);
		}
		
		self::remove_prestige_management_role($id_users, $office_count - 1);
	}
	
	public static function remove_prestige_management_role($id_users, $domains_managed = 0)
	{
		if($domains_managed)
		{
			return;
		}
		
		$offices = Bts_Prestige_System_Venues::get_managed_venues_for_id_users($id_users);
		$office_count = count($offices);
		
		if($office_count == 1)
		{
			$user = new \WP_User($id_users);
			$user->remove_role(BTS_PRESTIGE_MANAGEMENT_ROLE);
			$user->remove_cap(BTS_PRESTIGE_MANAGEMENT_ROLE);
		}
	}
	
	public static function add_domain_coordinator_roles($id_users)
	{
		self::add_roles($id_users, [BTS_MANAGE_CLUB_STRUCTURE_ROLE, BTS_PRESTIGE_MANAGEMENT_ROLE]);
	}
	
	public static function add_officer_roles($id_users)
	{
		self::add_roles($id_users, [BTS_PRESTIGE_MANAGEMENT_ROLE]);
	}
	
	public static function add_national_office_role($id_users)
	{
		self::add_roles($id_users, [BTS_MANAGE_CLUB_STRUCTURE_ROLE, BTS_PRESTIGE_MANAGEMENT_ROLE, BTS_NATIONAL_OFFICE_ROLE]);
	}
	
	public static function add_roles($id_users, $roles)
	{
		$user = new \WP_User($id_users);
		foreach($roles as $role)
		{
			$user->add_role($role);
		}
	}
	
	public static function get_domain_coordinator_user_id($id_domains)
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		
		$sql_string = $wpdb->prepare("
			SELECT 
				id_users,
				id
			FROM {$prefix}officers 
			WHERE 
					id_domains = %d
				AND	id_venues IS NULL
				AND id_superior IS NULL
				AND chain = 'Coordinator'
			", 
			$id_domains);
		return $wpdb->get_row($sql_string);
	}
	
	public static function check_domain_coordinator_permissions($domain_coordinator, $id_users)
	{
		if($domain_coordinator->id_users !== $id_users)
		{
			self::remove_manage_club_structure_role($domain_coordinator->id_users);
			self::add_domain_coordinator_roles($id_users);
		}
	}
	
	public static function check_officer_roles($office, $id_users)
	{
		if($office->id_users !== $id_users)
		{
			self::remove_prestige_management_role($id_users);
			self::add_officer_roles($id_users);
		}
	}
	
	public static function add_office_position_fileds($officer)
	{
		$officer->isDC = $officer->chain == 'Coordinator' && $officer->id_superior == null && $officer->id_venues == null;
		$officer->isOfficer = $officer->id_superior == null;
	}
	
	public static function get_officer_by_id($id_officers)
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		$officer = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT id_domains, id_venues, id_superior, id_users, chain FROM {$prefix}officers WHERE id = %d",
				$id_officers
			)
		);
		self::add_office_position_fileds($officer);
		return $officer;
	}
	
	/**
	 * Check all of the roles that we add and remove to the user to make sure that they get updated
	 */
	public static function check_permissions($id_domains, $id_officers, $id_users)
	{
		if(!($id_domains && $id_officers && $id_users))
		{
			return;
		}
		$office = self::get_officer_by_id($id_officers);
		
		if($office->id_superior === null && $office->id_domains === 1)
		{
			self::add_national_office_role($id_users);
		}
		
		if($office->isDC)
		{
			self::check_domain_coordinator_permissions($office, $id_users);
		}
		else if($office->isOfficer)
		{
			self::check_officer_roles($office, $id_users);
		}
		
	}
	
	public static function update_office($id_domains, $id_officers, $fields)
	{
		global $wpdb;
		
		if(!Bts_Prestige_System_Domains::manages_domain($id_domains))
		{
			return ['success'=>false,'error'=>'domain not in user purview'];
		}
		self::check_permissions($id_domains, $id_officers, $fields['id_users']);
		
		$relevant_fields = self::prepare_office_fields($fields);
		$table = $wpdb->prefix.BTS_TABLE_PREFIX."officers";
		$wpdb->update($table,$relevant_fields,['id'=>$id_officers]);
		
		if($wpdb->last_error !== '')
		{
			return ['success'=>false, 'message'=>$wpdb->last_error];
		}
		
		return ['success'=>true];
	}
	
	/*
	 * This is distinct from all national positions.
	 * A top level position is an officer who does not answer directly to someone
	 * else in their specific chain's internal heirarchy
	 */
	public static function get_all_top_level_positions()
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		return $wpdb->get_results(
			"SELECT 
				o.id_superior, o.id, o.title, o.id_domains, o.id_venues, o.id_users, o.chain, v.name AS venue
			FROM 
							{$prefix}officers o
				LEFT JOIN	{$prefix}venues v ON(o.id_venues = v.id)
			WHERE 
				id_superior IS NULL 
				AND (v.id IS NULL OR v.active = 1)
				AND o.id_domains IS NOT NULL"
		);
	}
	
	public static function remove_all_office_permissions()
	{
		$roles = [BTS_MANAGE_CLUB_STRUCTURE_ROLE, BTS_PRESTIGE_MANAGEMENT_ROLE, BTS_NATIONAL_OFFICE_ROLE];
		$users_with_roles = get_users([
			'role__in'=>$roles
		]);
		
		foreach($users_with_roles as $user)
		{
			foreach($roles as $role)
			{
				$user->remove_role($role);
				$user->remove_cap($role);
			}
		}
	}
	
	public static function reset_office_roles()
	{
		$offices = self::get_all_top_level_positions();
		self::add_domain_coordinator_roles(1);
		foreach($offices as $office)
		{
			self::add_office_position_fileds($office);
			if(intval($office->id_domains) === 1)
			{
				self::add_national_office_role($office->id_users);
			}
			if($office->isDC)
			{
				self::add_domain_coordinator_roles($office->id_users);
			}
			else if($office->isOfficer)
			{
				self::add_officer_roles($office->id_users);
			}
		}
	}
	
	public static function update_office_roles()
	{
		$user = wp_get_current_user();
		if(!in_array(BTS_MANAGE_CLUB_STRUCTURE_ROLE, (array) $user->roles))
		{
			return ['success'=>false, 'reason'=>'Not permitted'];
		}
		
		self::remove_all_office_permissions();
		self::reset_office_roles();
		
		return ['success'=>true];
	}
	
	public static function get_all_active_offices()
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		$offices = $wpdb->get_results(
			"SELECT 
				o.id, o.title, o.id_domains, o.id_venues, o.id_users, o.chain, v.name AS venue
			FROM 
							{$prefix}officers o
				LEFT JOIN	{$prefix}venues v ON(o.id_venues = v.id)
			WHERE 
				id_superior IS NULL 
				AND (v.id IS NULL OR v.active = 1)
				AND o.id_domains IS NOT NULL");
		return $offices;
	}
	
	public static function get_id_offices_by_id_users($id_users)
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id FROM {$prefix}officers WHERE id_users = %d",
				$id_users
			)
		);
	}
	
}