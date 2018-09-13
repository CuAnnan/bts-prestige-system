<?php

/**
 * This is an import class. It runs a whole swathe of SQL queries against two databases.
 * As such it ignores best practices regarding method and class lenght.
 * While I could write the SQL statements to take up one line only, that would make them illegible.
 * While I could write them in a separate file each, that is both a more complex solution and a less legible one.
 * 
 * 
 * The basic way that each function works is it takes the old data and imports it into the new database
 * returning an array mapping old ids to new ids, so that the integrity of the imports can be guaranteed
 */

require_once(BTS_ABS_PATH.'admin/class-bts-prestige-system-offices.php');
require_once(BTS_ABS_PATH.'admin/class-bts-prestige-system-prestige.php');
class Bts_Prestige_System_Data_Import
{
	private static $pdo = null;
	
	public static function startTrans()
	{
		global $wpdb;
		wp_defer_term_counting( false );
		wp_defer_comment_counting( false );
		$wpdb->query( 'SET autocommit = 0;' );
	}
	
	public static function endTrans()
	{
		global $wpdb;
		wp_defer_term_counting( true );
		wp_defer_comment_counting( true );
		$wpdb->query( 'SET autocommit = 1;' );
		$wpdb->query( 'COMMIT;' );
	}
	
	public static function import($pdo)
	{
		
		self::$pdo = $pdo;
		error_log('Importing domains');
		$domainMap = self::import_domains();
		error_log('Domains imported');
		error_log('Importing genres');
		$genreMap = self::import_genres();
		error_log('Genres imported');
		error_log('Importing venues');
		list($venueMap, $venuesDomainMap) = self::import_venues($genreMap, $domainMap);
		error_log('Venues imported');
		error_log('Importing users');
		
		self::startTrans();
		$userMap = self::import_users($domainMap);
		self::endTrans();
		
		error_log('Users imported');
		error_log('Importing officers');
		$officerMap = self::import_officers($userMap, $venueMap, $domainMap, $venuesDomainMap);
		error_log('Officers imported');
		error_log('Importing prestige categories');
		$prestigeCategoryMap = self::import_prestige_categories();
		error_log('Prestige categories imported');
		error_log('Importing prestige actions');
		$prestigeActionMap = self::import_prestige_actions($prestigeCategoryMap);
		error_log('Prestige actions imported');
		error_log('Importing prestige records');
		self::startTrans();
		self::import_prestige($userMap, $officerMap, $prestigeActionMap);
		self::endTrans();
		error_log('Prestige records imported');
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
	
	private static function fetch_prestige_action_records()
	{
		$stmt = self::$pdo->prepare("SELECT * FROM prestigeactions");
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	
	private static function import_prestige_actions($prestigeCategoryMap)
	{
		global $wpdb;
		$table = $wpdb->prefix.BTS_TABLE_PREFIX."prestige_actions";
		$action_records = self::fetch_prestige_action_records();
		$keyMap = [];
		foreach($action_records as $record)
		{
			$wpdb->insert(
				$table,[
					'description'=>$record['pacDesc'],
					'active'=>$record['pacActive'],
					'value'=>$record['pacValue'],
					'id_prestige_category'=>$prestigeCategoryMap[$record['pacCategory']]
			]);
			$keyMap[$record['pacID']] = $wpdb->insert_id;
		}
		return $keyMap;
	}
	
	private static function fetch_prestige_records()
	{
		// We're only concerned with importing the approved prestige records
		// because we don't really know what the other prlAudited fields mean
		$stmt = self::$pdo->prepare('SELECT * FROM prestigelog WHERE prlAudited = 4');
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	private static function clean_prestige_amount($record)
	{
		if($record['prlOpen'])
		{
			return[$record['prlOpen'], 'Open'];
		}
		else if($record['prlRegional'])
		{
			return [$record['prlRegional'], 'Regional'];
		}
		else if($record['prlNational'])
		{
			return [$record['prlNational'], 'National'];
		}
	}
	
	private static function import_prestige($users, $officers, $prestige_actions)
	{
		$prestige_records = self::fetch_prestige_records();
		
		foreach($prestige_records as $record)
		{
			
			list($prestigeAmount, $prestigeType) = self::clean_prestige_amount($record);
			if($prestigeAmount)
			{
				$id_prestige_record = Bts_Prestige_System_Prestige::add_prestige_record(
					$users[$record['fk_mem_prlID']],
					$users[$record['prlAwardedBy']],
					$officers[$record['fk_prl_posAwarding']],
					$prestige_actions[$record['fk_prl_pacID']],
					$record['prlDate'],
					$prestigeAmount,
					$prestigeType,
					$record['prlReason']
				);
				/**
				 * The old system doesn't store either the id of the officer that audited the log or the id of the user that audited it
				 */
				Bts_Prestige_System_Prestige::add_record_note($id_prestige_record, null, $record['prlAuditNote'], $record['prlAuditDate'], true, null);
			}
		}
	}
	
	private static function fetch_officer_records()
	{
		$stmt = self::$pdo->prepare('SELECT
				p.posID					AS id,
				p.posTitle				AS title,
				p.posEmail				AS email,
				p.fk_mem_holder			AS old_member_id,
				p.fk_ent_belongsTo		AS old_entity_id,
				p.fk_pos_assistantTo	AS old_id_superior,
				p.posDateGained			AS date_appointed,
				e.fk_ett_type			AS old_entity_type
			FROM
				position p
				LEFT JOIN entity e ON (p.fk_ent_belongsTo = e.entID)
			ORDER BY
				id');
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	private static function add_officer($officer_record, $domain_id, $venue_id, $user_id)
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		$officers_table = "{$prefix}officers";
		$chain = strpos($officer_record['title'], 'tory') !== FALSE?'Storyteller':'Coordinator';
		$data = [
			'title'=>$officer_record['title'],
			'email'=>$officer_record['email'],
			'id_users'=>$user_id?$user_id:1,
			'chain'=>$chain,
			'date_appointed'=>$officer_record['date_appointed']
		];
		if($domain_id){ $data['id_domains'] = $domain_id;}
		if($venue_id){ $data['id_venues'] = $venue_id;}
		
		$idOfficer = null;
		if($data['id_users'])
		{
			$wpdb->insert($officers_table, $data);
			$idOfficer = $wpdb->insert_id;
		}
		return $idOfficer;
	}
	
	public static function update_officer_heirarchy($id_officer, $id_superior)
	{
		global $wpdb;
		$table = $wpdb->prefix.BTS_TABLE_PREFIX."officers";
		$wpdb->update(
			$table,
			['id_superior'=>$id_superior],
			['id'=>$id_officer]
		);
	}
	
	private static function update_officer_roles()
	{
		global $wpdb;
		$table = $wpdb->prefix.BTS_TABLE_PREFIX."officers";
		$domain_coordinator_records = $wpdb->get_results("SELECT id_users, id_domains FROM $table WHERE id_domains IS NOT NULL AND id_superior IS NULL AND chain = 'Coordinator'");
		foreach($domain_coordinator_records as $domain_coordinator_record)
		{
			Bts_Prestige_System_Offices::add_domain_coordinator_role($domain_coordinator_record->id_users);
		}
	}
	
	private static function import_officers($users, $venues, $domains, $venueDomainsMap)
	{
		$officer_records = self::fetch_officer_records();
		$keyMap = []; $subordinates = [];
		
		foreach($officer_records as $officer_record)
		{
			$venue_id  = $officer_record['old_entity_type'] < 3 ? null : $venues[$officer_record['old_entity_id']];
			$domain_id = $officer_record['old_entity_type'] < 3 ? $domains[$officer_record['old_entity_id']] :$venueDomainsMap[$venue_id];
			$user_id = $officer_record['old_member_id'] ? $users[$officer_record['old_member_id']]: null;
			$new_id = self::add_officer($officer_record, $domain_id, $venue_id, $user_id);
			$keyMap[$officer_record['id']] = $new_id;
			if($officer_record['old_id_superior'] != 0)
			{
				$subordinates[$new_id] = $officer_record['old_id_superior'];
			}
		}
		foreach($subordinates as $id_officer=>$old_id_superior)
		{
			self::update_officer_heirarchy($id_officer, $keyMap[$old_id_superior]);
		}
		self::update_officer_roles();
		return $keyMap;
	}
	
	private static function fetch_members()
	{
		$stmt = self::$pdo->prepare('
			SELECT
				m.memID				AS id,
				m.memFirstName		AS first_name,
				m.memLastName		AS last_name,
				m.memEmail			AS email,
				m.memNumber			AS membership_number,
				m.memExpiry			AS membership_renewal_date,
				m.memAddress		AS address_1,
				m.memCity			AS address_2,
				m.memState			AS state,
				m.memPostCode		AS zip,
				m.memDOB			AS date_of_birth,
				m.fk_ent_memberOf	AS old_id_domains,
				e.entTitle			AS domain
			FROM
				member m
				LEFT JOIN entity e ON (m.fk_ent_memberOf = e.entID)');
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	private static function map_user_meta_data($new_member_id, $member_record)
	{
		$fields_to_ignore = ['id', 'email', 'old_id_domains'];
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
	
	private static function import_users($domainMap)
	{
		$memberRecords = self::fetch_members();
		$keyMap = [];
		foreach($memberRecords as $memberRecord)
		{
			if($memberRecord['membership_number'])
			{
				$user_id = self::add_user_record($memberRecord);
				$memberRecord['id_domains'] = $domainMap[$memberRecord['old_id_domains']];
				$keyMap[$memberRecord['id']] = $user_id;
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
		$venuesDomainsMap = [];
		foreach($rows as $row)
		{
			$row['id_domains'] = $domains[$row['old_id_domains']];
			$row['id_genres'] = $genres[$row['old_id_genres']];
			$id_venues = self::add_venue($row);
			$keyMap[$row['id']] = $id_venues;
			$venuesDomainsMap[$id_venues] = $row['id_domains'];
		}
		return [$keyMap, $venuesDomainsMap];
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