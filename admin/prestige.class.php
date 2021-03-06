<?php namespace BTS_Prestige\Admin;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Prestige
{
	protected static $memberClasses = [
		["title"=>"Associate",	"level"=>1,		"prestige"=>50],
		["title"=>"Journeyman",	"level"=>2,		"prestige"=>100],
		["title"=>"Artisan",	"level"=>3,		"prestige"=>300],
		["title"=>"Contributor","level"=>4,		"prestige"=>600],
		["title"=>"Sponsor",	"level"=>5,		"prestige"=>1000],
		["title"=>"Steward",	"level"=>6,		"prestige"=>1500],
		["title"=>"Benefactor",	"level"=>7,		"prestige"=>2100],
		["title"=>"Advocate",	"level"=>8,		"prestige"=>2700],
		["title"=>"Advisor",	"level"=>9,		"prestige"=>3400],
		["title"=>"Patron",		"level"=>10,	"prestige"=>4100],
		["title"=>"Mentor",		"level"=>11,	"prestige"=>4800],
		["title"=>"Luminary",	"level"=>12,	"prestige"=>5600],
		["title"=>"Executive",	"level"=>13,	"prestige"=>6400],
		["title"=>"Fellow",		"level"=>14,	"prestige"=>7200],
		["title"=>"Trustee",	"level"=>15,	"prestige"=>8000]
	];

	public static function add_prestige_claim($id_officers, $id_prestige_action, $reward_amount, $reward_type, $reason, $date)
	{
		$id_record = self::add_prestige_record(
				get_current_user_id(),
				null,
				$id_officers,
				$id_prestige_action,
				$date,
				$reward_amount,
				$reward_type,
				$reason
		);
		if($id_record)
		{
			return ['success'=>true, "id_record"=>$id_record, 'date'=>$date];
		}
		return ['success'=>false];
	}
	
	public static function add_prestige_reward($id_users, $id_officers, $id_prestige_action, $reward_amount, $reward_type, $reason, $status, $date)
	{
		$user = new \WP_User(get_current_user_id());
		if(!array_intersect(['admin', BTS_PRESTIGE_MANAGEMENT_ROLE], (array)$user->roles))
		{
			return ['success'=>false, "error"=>'Permission error'];
		}
		
		$id_record = self::add_prestige_record($id_users,get_current_user_id(),$id_officers,$id_prestige_action,$date,$reward_amount,$reward_type);
		
		if($id_record)
		{
			$id_note = self::add_record_note($id_record, get_current_user_id(), $reason, $date, $status, $id_officers);
			if($id_note)
			{
				return ['success'=>true, "id_record"=>$id_record, 'date'=>$date];
			}
			return ['success'=>false];
		}
		return ['success'=>false];
	}
	
	public static function fetch_user_prestige($id_users)
	{
		$logged_in_user_domains = Domains::get_managed_domain_ids(get_current_user_id());
		$user_id_domains = get_user_meta($id_users, 'id_domains', true);
		$editable = in_array($user_id_domains, $logged_in_user_domains);
		
		return ['success'=>true, 'log'=>self::get_prestige_for_user_by_id($id_users), 'editable'=>$editable];
	}
	
	public static function add_prestige_record($id_users, $id_member_approved, $id_officer_approved, $id_prestige_action, $date_claimed, $reward_amount, $reward_type, $reason = null)
	{
		global $wpdb;
		$table = $wpdb->prefix.BTS_TABLE_PREFIX."prestige_rewards";
		$wpdb->query('START TRANSACTION');
		$wpdb->insert(
			$table,[
				'id_member'=>$id_users,
				'id_member_approved'=>$id_member_approved,
				'id_officer_approved'=>$id_officer_approved,
				'id_prestige_action'=>$id_prestige_action,
				'date_claimed'=>$date_claimed,
				'reward_amount'=>$reward_amount,
				'reward_type'=>$reward_type
		]);
		$wpdb->query('COMMIT');
		$id_prestige_record = $wpdb->insert_id;
		
		if($reason)
		{
			self::add_record_note($id_prestige_record, $id_users, $reason, $date_claimed);
		}
		return $id_prestige_record;
	}
	
	public static function try_to_add_record_note($id_prestige_record, $note_text, $status, $id_officer)
	{
		$id_users = get_current_user_id();
		$now = date("Y-m-d H:i:s");
		if($status)
		{
			self::set_prestige_record_status($id_prestige_record, $status);
		}
		return self::add_record_note($id_prestige_record, $id_users, $note_text, $now, $status, $id_officer);
	}
	
	public static function set_prestige_record_status($id_prestige_record, $status)
	{
		global $wpdb;
		$table = $wpdb->prefix.BTS_TABLE_PREFIX."prestige_rewards";
		$wpdb->update(
			$table,
			["status"=>$status],
			["id"=>$id_prestige_record]
		);
		$error = $wpdb->last_error;
		if($error)
		{
			error_log($error);
		}
	}
	
	public static function add_record_note($id_prestige_record, $id_users, $note_text, $date_note_added, $status = "Submitted", $id_officer = null)
	{
		global $wpdb;
		$table = $wpdb->prefix.BTS_TABLE_PREFIX."prestige_reward_notes";
		//	id 	id_prestige_rewards 	id_users 	id_officer 	note 	approved 	date 
		$record = [
			'id_prestige_rewards'=>$id_prestige_record,
			'id_users'=>$id_users,
			'note'=>$note_text,
			'date'=>$date_note_added,
			'status'=>$status
		];
		if($id_officer)
		{
			$record['id_officer']=$id_officer;
		}
		$wpdb->insert($table,$record);
		if($wpdb->insert_id)
		{
			return ['success'=>true];
		}
		return ['success'=>false, 'message'=>$wpdb->last_error];
	}
	
	public static function edit_prestige_record($id_prestige_record, $id_officer_approved, $id_prestige_action, $reward_amount, $reward_type, $status, $date)
	{
		global $wpdb;
		$table = $wpdb->prefix.BTS_TABLE_PREFIX."prestige_rewards";
		
		$problems = self::get_edit_record_problems($id_prestige_record);
		if($problems)
		{
			return $problems;
		}
		
		$updates = ["id_officer_approved"=>$id_officer_approved,"id_prestige_action"=>$id_prestige_action,"date_claimed"=>$date,"reward_amount"=>$reward_amount,"reward_type"=>$reward_type];
		if($status)
		{
			$updates['status'] = $status;
		}
		
		$wpdb->update(
			$table,
			$updates,
			["id"=>$id_prestige_record]
		);
		
		return ['success'=>'true', "updates"=>$updates];
	}
	
	public static function get_edit_record_problems($id_prestige_record)
	{
		global $wpdb;
		$user = wp_get_current_user();
		$records_check = $wpdb->get_results($wpdb->prepare("select id_member, status FROM {$wpdb->prefix}".BTS_TABLE_PREFIX."prestige_rewards WHERE id = %d", $id_prestige_record))[0];
		if($records_check->status == "Audited")
		{
			return ["success"=>false, "reason"=>"Cannot update audited records"];
		}
		
		$user_can_update = $records_check->id_member == $user->ID;
		$results = ["id_record"=>$id_prestige_record,"id_record_user"=>$records_check->id_member,"user id"=>$user->ID, "record"=>$records_check];
		if(!$user_can_update)
		{
			$roles = (array)$user->roles;
			$user_can_update = boolval(array_intersect(['administrator', BTS_PRESTIGE_MANAGEMENT_ROLE, BTS_NATIONAL_OFFICE_ROLE], (array)$user->roles));
			$results['roles'] = $roles;
		}
		
		if(!$user_can_update)
		{
			return ['success'=>false, "reason"=>"Logged in user does not have permissions to update this record", "results"=>$results];
		}
		
		return false;
	}
	
	public static function get_audited_prestige($id_users)
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		$sql = $wpdb->prepare("
			SELECT
				SUM(reward_amount) AS prestige_total
			FROM 
				{$prefix}prestige_rewards
			WHERE	
				status = 'Audited' AND id_member = %d"
			, $id_users
		);
		return $wpdb->get_var($sql);
	}
	
	public static function cooerce_record_to_object($record)
	{
		$basic_fields = ["id", "officer_id_user", "member_id_user", "reward_amount", "reward_type", "date_claimed", "description", "id_prestige_actions", "id_prestige_categories", "category", "officer_title", "id_officers", "domain_name", "genre_name", "status", "id_officers"];
		$ordered_record = new \stdClass();
		$ordered_record->notes = [];
		foreach($basic_fields as $basic_field)
		{
			$ordered_record->$basic_field = $record->$basic_field;
		}
		return $ordered_record;
	}
	
	public static function cooerce_record_to_note_object($record)
	{
		$note_fields = ["note", "note_officer_title", "note_domain_name", "note_genre_name", "note_status", "note_date"];
		$note_object = new \stdClass();
		foreach($note_fields as $note_field)
		{
			$note_object->$note_field = $record->$note_field;
		}
		$note_object->status = $note_object->note_status;
		$raw_user_meta = get_user_meta($record->note_id_users);
		$note_object->member = "{$raw_user_meta['first_name'][0]} {$raw_user_meta['last_name'][0]}";
		return $note_object;
	}
	
	public static function get_prestige_for_user_by_id($id_users)
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		$qry = $wpdb->prepare("
			SELECT 
				pr.id					AS id,
				pr.id_member			AS member_id_user,
				pr.id_member_approved	AS officer_id_user,
				pr.reward_amount		AS reward_amount,
				pr.reward_type			AS reward_type,
				pr.date_claimed			AS date_claimed,
				pr.status				AS status,
				pa.description			AS description,
				pa.id					AS id_prestige_actions,
				pc.id					AS id_prestige_categories,
				pc.name					AS category,
				o.title					AS officer_title,
				o.id					AS id_officers,
				pr_off.short_form		AS office_short_form,
				d.name					AS domain_name,
				g.name					AS genre_name,
				pn.id					AS note_id,
				pn.note					AS note,
				pn.status				AS note_status,
				pn.date					AS note_date,
				n_o.title				AS note_officer_title,
				n_off.short_form		AS note_officer_short_form,
				nu.ID					AS note_id_users,
				dn.name					AS note_domain_name,
				dg.name					AS note_genre_name
			FROM
							{$prefix}prestige_rewards pr
				LEFT JOIN	{$prefix}prestige_actions pa		ON (pa.id		= pr.id_prestige_action)
				LEFT JOIN	{$prefix}prestige_categories pc		ON (pc.id		= pa.id_prestige_category)
				LEFT JOIN	{$prefix}prestige_reward_notes pn	ON (pr.id		= pn.id_prestige_rewards)
				LEFT JOIN	{$prefix}officers o					ON (o.id		= pr.id_officer_approved)
				LEFT JOIN	{$prefix}offices pr_off				ON (pr_off.id	= o.id_offices)
				LEFT JOIN	{$prefix}venues v					ON (v.id		= o.id_venues)
				LEFT JOIN	{$prefix}domains d					ON (d.id		= o.id_domains)
				LEFT JOIN	{$prefix}genres g					ON (g.id		= v.id_genres)
				LEFT JOIN	{$wpdb->prefix}users nu				ON (nu.ID		= pn.id_users)
				LEFT JOIN	{$prefix}officers n_o				ON (n_o.id		= pn.id_officer)
				LEFT JOIN	{$prefix}offices n_off				ON (n_off.id	= n_o.id_offices)
				LEFT JOIN	{$prefix}venues vn					ON (vn.id		= n_o.id_venues)
				LEFT JOIN	{$prefix}domains dn					ON (dn.id		= n_o.id_domains)
				LEFT JOIN	{$prefix}genres dg					ON (dg.id		= vn.id_genres)
			WHERE
				pr.id_member = %d
			ORDER BY 
				date_claimed	ASC,
				id				ASC,
				pn.id			ASC
			",
			$id_users);
		
		return self::get_prestige_records_from_qry($qry);
	}
	
	private static function get_prestige_records_from_qry($qry)
	{
		global $wpdb;
		$prestige_records = $wpdb->get_results($qry);
		$prestige_rewards = [];
		// these are the fields of the raw prestige reward request, the nature of hte join query is that you'll get a record per prestige note. This saves there being two
		// queries per entry at the expense of a little extra coding.
		foreach($prestige_records as $prestige_record)
		{
			if(!isset($prestige_rewards[$prestige_record->id]))
			{
				$prestige_rewards[$prestige_record->id] = self::cooerce_record_to_object($prestige_record);
			}
			$prestige_rewards[$prestige_record->id]->notes[] = self::cooerce_record_to_note_object($prestige_record);
		}
		return $prestige_rewards;
	}
	
	public static function get_prestige_categories()
	{
		global $wpdb;
		$table = $wpdb->prefix.BTS_TABLE_PREFIX."prestige_categories";
		return $wpdb->get_results("SELECT * FROM {$table} ORDER BY id");
	}
	
	public static function get_prestige_actions()
	{
		global $wpdb;
		$table = $wpdb->prefix.BTS_TABLE_PREFIX."prestige_actions";
		$sql = "SELECT * FROM {$table} ORDER BY id";
		return $wpdb->get_results($sql);
	}
	
	
	
	public static function get_mc($prestige)
	{
		$mc = ["title"=>"Associate",	"level"=>1,		"prestige"=>50];
		$index = 0; $searching = true;
		while($searching)
		{
			$memberClass = self::$memberClasses[$index];
			if($prestige >= $memberClass['prestige'])
			{
				$mc = $memberClass;
			}
			else
			{
				$searching = false;
			}
			$index++;
			if($index >= count(self::$memberClasses))
			{
				$searching = false;
			}
		}
		
		return $mc;
	}
	
	public static function get_prestige_to_next_mc($prestige)
	{
		$amount = 0; $index = 0; $searching = true;
		while($searching)
		{
			$memberClass = self::$memberClasses[$index];
			if($prestige < $memberClass['prestige'])
			{
				$amount = $memberClass['prestige'] - $prestige;
				$searching = false;
			}
			$index++;
			if($index >= count(self::$memberClasses))
			{
				$searching = false;
			}
		}
		return $amount;
	}
	
	public static function show_prestige_management_page()
	{
		$prestige_rewards		= self::get_prestige_for_user_by_id(get_current_user_id());
		$prestige_categories	= self::get_prestige_categories();
		$prestige_actions		= self::get_prestige_actions();
		$domains				= \BTS_Prestige\Admin\Domains::get_all_domains();
		$offices				= \BTS_Prestige\Admin\Offices::get_all_offices();
		$venues					= \BTS_Prestige\Admin\Venues::get_all_active_venues();
		$audited_prestige		= self::get_audited_prestige(get_current_user_id());
		
		$mc						= self::get_mc($audited_prestige);
		$prestigeLeft			= self::get_prestige_to_next_mc($audited_prestige);
		$viewing_own_log		= true;
		require_once (plugin_dir_path(__FILE__).'partials/prestige-management-page.php');
	}
	
	public static function view_prestige_page()
	{
		$managed_domains		= Domains::get_managed_domain_ids();
		$users					= Users::get_users_by_id_domains($managed_domains);
		$offices				= \BTS_Prestige\Admin\Offices::get_all_offices();
		$prestige_categories	= self::get_prestige_categories();
		$prestige_actions		= self::get_prestige_actions();
		$can_audit				= array_intersect(['administrator', BTS_NATIONAL_OFFICE_ROLE],  wp_get_current_user()->roles);
		
		require_once (plugin_dir_path(__FILE__).'partials/prestige-search-page.php');
	}
	
	public static function show_prestige_auditing_page()
	{
		$prestige_records	= self::fetch_prestige_entries_requiring_action();
		$prestige_categories	= self::get_prestige_categories();
		$prestige_actions		= self::get_prestige_actions();
		$can_audit				= array_intersect(['administrator', BTS_NATIONAL_OFFICE_ROLE],  wp_get_current_user()->roles);
		
		$domains				= \BTS_Prestige\Admin\Domains::get_all_domains();
		$offices				= \BTS_Prestige\Admin\Offices::get_all_offices();
		$venues					= \BTS_Prestige\Admin\Venues::get_all_active_venues();
		$usersMetaData			= \BTS_Prestige\Admin\Users::getUsermeta();
		
		require_once (plugin_dir_path(__FILE__).'partials/prestige-auditing.php');
	}
	
	public static function fetch_prestige_entries_requiring_action()
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		$sql = "SELECT 
				pr.id					AS id,
				pr.id_member			AS member_id_user,
				pr.id_member_approved	AS officer_id_user,
				pr.reward_amount		AS reward_amount,
				pr.reward_type			AS reward_type,
				pr.date_claimed			AS date_claimed,
				pr.status				AS status,
				pa.description			AS description,
				pa.id					AS id_prestige_actions,
				pc.id					AS id_prestige_categories,
				pc.name					AS category,
				o.title					AS officer_title,
				o.id					AS id_officers,
				pr_off.short_form		AS office_short_form,
				d.name					AS domain_name,
				g.name					AS genre_name,
				pn.id					AS note_id,
				pn.note					AS note,
				pn.status				AS note_status,
				pn.date					AS note_date,
				n_o.title				AS note_officer_title,
				n_off.short_form		AS note_officer_short_form,
				nu.ID					AS note_id_users,
				dn.name					AS note_domain_name,
				dg.name					AS note_genre_name
			FROM
							{$prefix}prestige_rewards pr
				LEFT JOIN	{$prefix}prestige_actions pa		ON (pa.id		= pr.id_prestige_action)
				LEFT JOIN	{$prefix}prestige_categories pc		ON (pc.id		= pa.id_prestige_category)
				LEFT JOIN	{$prefix}prestige_reward_notes pn	ON (pr.id		= pn.id_prestige_rewards)
				LEFT JOIN	{$prefix}officers o					ON (o.id		= pr.id_officer_approved)
				LEFT JOIN	{$prefix}offices pr_off				ON (pr_off.id	= o.id_offices)
				LEFT JOIN	{$prefix}venues v					ON (v.id		= o.id_venues)
				LEFT JOIN	{$prefix}domains d					ON (d.id		= o.id_domains)
				LEFT JOIN	{$prefix}genres g					ON (g.id		= v.id_genres)
				LEFT JOIN	{$wpdb->prefix}users nu				ON (nu.ID		= pn.id_users)
				LEFT JOIN	{$prefix}officers n_o				ON (n_o.id		= pn.id_officer)
				LEFT JOIN	{$prefix}offices n_off				ON (n_off.id	= n_o.id_offices	)
				LEFT JOIN	{$prefix}venues vn					ON (vn.id		= n_o.id_venues)
				LEFT JOIN	{$prefix}domains dn					ON (dn.id		= n_o.id_domains)
				LEFT JOIN	{$prefix}genres dg					ON (dg.id		= vn.id_genres)
			WHERE
				pr.status NOT IN ('Audited', 'Rejected')
				OR pr.status IS NULL";
		
		if(!array_intersect(['administrator', BTS_NATIONAL_OFFICE_ROLE],  wp_get_current_user()->roles))
		{
			$sql = $wpdb->prepare(
				$sql.' AND o.id_users = %d',
				get_current_user_id()
			);
		}
		
		$prestige_records = self::get_prestige_records_from_qry($sql);
		
		$users = [];
		
		foreach($prestige_records as $record)
		{
			if(!isset($users[$record->member_id_user]))
			{
				$raw_user_meta = get_user_meta($record->member_id_user);
				$users[$record->member_id_user] = [
						'first_name'=>$raw_user_meta['first_name'][0],
						'last_name'=>$raw_user_meta['last_name'][0],
						'number'=>$raw_user_meta['nickname'][0]
				];
			}
			$record->first_name	= $users[$record->member_id_user]['first_name'];
			$record->last_name	= $users[$record->member_id_user]['last_name'];
			$record->number		= $users[$record->member_id_user]['number'];
		}
		
		return $prestige_records;
	}
}