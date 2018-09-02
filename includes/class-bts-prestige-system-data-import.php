<?php
class Bts_Prestige_System_Data_Import
{
	private static $pdo = null;
	
	public static function import($pdo)
	{
		self::$pdo = $pdo;
		$domains = self::import_domains();
		$genres = self::import_genres();
		$venues = self::import_venues($genres, $domains);
		$users = self::import_users($domains);
		$officers = self::import_officers($venues, $domains, $users);
		/*$prestige_categories = self::import_prestige_categories();
		self::import_prestige($users, $officers, $prestige_categories);*/
	}
	
	private static function fetch_prestige_category_records()
	{
		$stmt = self::$pdo->prepare('
			SELECT
				pcaID		AS id,
				pcaTitle	AS name,
				pcaCap		AS monthly_cap
			FROM prestigecategories');
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	private static function import_prestige_categories()
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		$table = "{$prefix}prestige_categories";
		$category_records = self::fetch_prestige_category_records();
		$keyMap = [];
		foreach($category_records as $record)
		{
			$wpdb->insert($table, [
				'name'=>$record['name'],
				'monthly_cap'=>$record['monthly_cap']
			]);
			$keyMap[$record['id']] = $wpdb->insert_id;
		}
		return $keyMap;
	}
	
	private static function fetch_prestige_records()
	{
		$stmt = self::$pdo->prepare('SELECT * FROM prestigelog');
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	private static function import_prestige($users, $officers, $prestige_categories)
	{
		$prestige_records = self::fetch_prestige_records();
		
	}
	
	private static function fetch_officer_records()
	{
		$stmt = self::$pdo->prepare('SELECT
				p.posID					AS id,
				p.posTitle				AS title,
				p.posEmail				AS email,
				p.fk_mem_holder			AS old_member_id,
				p.fk_ent_belongsTo		AS old_entity_id,
				p.fk_pos_assistantTo	AS id_superior,
				e.fk_ett_type			AS old_entity_type
			FROM
				position p
				LEFT JOIN entity e ON (p.fk_ent_belongsTo = e.entID)
			ORDER BY
				id');
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$officer_records = ['domain'=>[], 'venue'=>[]];
		foreach($rows as $row)
		{
			$officer_records[$row['old_entity_type'] < 3 ? 'domain': 'venue'][] = $row;
		}
		return $officer_records;
	}
	
	private static function add_officer($officer_record, $domain_id, $venue_id, $user_id)
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		$table = "{$prefix}officers";
		$chain = strpos($officer_record['title'], 'tory') !== FALSE?'Storyteller':'Coordinator';
		$data = [
			'title'=>$officer_record['title'],
			'email'=>$officer_record['email'],
			'id_member'=>$user_id?$user_id:1,
			'chain'=>$chain
		];
		$keyMap = [];
		if($domain_id){ $data['id_domains'] = $domain_id;}
		if($venue_id){ $data['id_venues'] = $venue_id;}
		if($data['id_member'])
		{
			$wpdb->insert($table, $data);
			$keyMap[$officer_record['id']] = $wpdb->insert_id;
		}
		return $keyMap;
	}
	
	public static function update_officer_heirarchy($id_officer, $id_superior)
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		$table = "{$prefix}officers";
		$wpdb->update(
			$table,
			['id_superior'=>$id_superior],
			['id'=>$id_officer]
		);
	}
	
	private static function import_officers($venues, $domains, $users)
	{
		$officer_records = self::fetch_officer_records();
		$keyMap = [];
		
		foreach($officer_records['domain'] as $officer_record)
		{
			$domain_id = $domains[$officer_record['old_entity_id']];
			$user_id = $officer_record['old_member_id'] ? $users[$officer_record['old_member_id']]: null;
			$keyMap[$officer_record['id']] = self::add_officer($officer_record, $domain_id, null, $user_id);
		}
		foreach($officer_records['venue'] as $officer_record)
		{
			$venue_id = $venues[$officer_record['old_entity_id']];
			$user_id = $officer_record['old_member_id'] ? $users[$officer_record['old_member_id']]: null;
			$keyMap[$officer_record['id']] = self::add_officer($officer_record, null, $venue_id, $user_id);
		}
		
		
		
		return $keyMap;
	}
	
	private static function fetch_members()
	{
		$stmt = self::$pdo->prepare('
			SELECT
				m.memID			AS id,
				m.memFirstName	AS first_name,
				m.memLastName	AS last_name,
				m.memEmail		AS email,
				m.memNumber		AS membership_number,
				m.memExpiry		AS membership_renewal_date,
				m.memAddress	AS address_1,
				m.memCity		AS address_2,
				m.memState		AS state,
				m.memPostCode	AS zip,
				m.memDOB		AS date_of_birth,
				e.entTitle		AS domain
			FROM
				member m
				LEFT JOIN entity e ON (m.fk_ent_memberOf = e.entID)');
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	private static function map_user_meta_data($new_member_id, $member_record)
	{
		$fields_to_ignore = ['id', 'email'];
		foreach($member_record as $field=>$data)
		{
			if(!in_array($field, $fields_to_ignore))
			{
				update_user_meta($new_member_id, $field, $data);
			}
		}
	}
	
	private static function add_user_record($memberRecord)
	{
		$password = wp_generate_password();
		if($memberRecord['email'])
		{
			return wp_create_user($memberRecord['membership_number'], $password, $memberRecord['email']);
		}
		return wp_create_user($memberRecord['membership_number'], $password);		
	}
	
	private static function import_users()
	{
		$memberRecords = self::fetch_members();
		$keyMap = [];
		foreach($memberRecords as $memberRecord)
		{
			if($memberRecord['membership_number'])
			{
				$keyMap[$memberRecord['id']] = self::add_user_record($memberRecord);
				self::map_user_meta_data($user_id, $memberRecord);
			}
		}
		return $keyMap;
	}
	
	private static function import_genres()
	{
		global $wpdb;
		$stmt = self::$pdo->prepare('SELECT gnrID as old_id, gnrName as name FROM `genre`');
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		$table = "{$prefix}genres";
		$keyMap = [];
		foreach($rows as $row)
		{
			$wpdb->insert($table, ['name'=>$row['name']]);
			$keyMap[$row['old_id']] = $wpdb->insert_id;
		}
		return $keyMap;
	}
	
	private static function fetch_old_venue_rows()
	{
		$stmt = self::$pdo->prepare('
			SELECT 
				entID AS id,
				fk_ent_belongsTO AS old_id_domains,
				fk_ent_genreID AS old_id_genres,
				entTitle AS name,
				entNMCCode AS nmc_code,
				entActive AS active
			FROM `entity` 
			WHERE fk_ett_type = 3');
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}
	
	private static function add_venue($row)
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		$table = "{$prefix}venues";
		$rows_to_ignore = ['id', 'old_id_domains', 'old_id_genres'];
		$data = [];
		foreach($row as $field=>$value)
		{
			if(!in_array($field, $rows_to_ignore))
			{
				$data[$field] = $value;
			}
		}
		$wpdb->insert($table, $data);
		return $wpdb->insert_id;
	}
	
	private static function import_venues($genres, $domains)
	{
		$rows = self::fetch_old_venue_rows();
		$keyMap = [];
		foreach($rows as $row)
		{
			$row['id_domains'] = $domains[$row['old_id_domains']];
			$row['id_genres'] = $genres[$row['old_id_genres']];
			$keyMap[$row['id']] = self::add_venue($row);
		}
		return $keyMap;
	}
	
	private static function map_domain_structure($oldParentIds, $keyMap)
	{
		global $wpdb;
		foreach($oldParentIds as $oldId=>$oldParentId)
		{
			$newId = $keyMap[$oldId];
			$newParentId = $keyMap[$oldParentId];
			$wpdb->update('domains', ['parent_domain_id'=>$newParentId], ['id'=>$newId]);
		}
	}
	
	private static function fetch_old_domain_rows()
	{
		$stmt = self::$pdo->prepare
			('SELECT 
				entID AS id,
				fk_ent_belongsTO AS parent_domain_id,
				entTitle AS name,
				entNMCCode AS nmc_code,
				entNumber AS number,
				entLocation AS location,
				entActive AS active
			FROM `entity` 
			WHERE fk_ett_type IN (1, 2)');
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}
	
	private static function add_domain($row)
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		$table = "{$prefix}domains";
		$data = [
			'name'=>$row['name'],
			'nmc_code'=>$row['nmc_code'],
			'number'=>$row['number'],
			'location'=>$row['location'],
			'active'=>$row['active']
		];
		if($row['parent_domain_id'])
		{
			$data['parent_domain_id'] = $row['parent_domain_id'];
		}
			
		$wpdb->insert($table,$data);
		return $wpdb->insert_id;
	}
	
	/**
	 * Imports the domains from the old database
	 * returns a map of the new keys to the old keys in the form of
	 * $oldKey => $newKey
	 */
	private static function import_domains()
	{
		$rows = self::fetch_old_domain_rows();
		$keyMap = [];
		$oldParentIds = [];
		
		// perform the initial inserts
		foreach($rows as $row)
		{
			$keyMap[$row['id']] = self::add_domain($row);
		}
		self::map_domain_structure($oldParentIds, $keyMap);
		return $keyMap;
	}
	
}