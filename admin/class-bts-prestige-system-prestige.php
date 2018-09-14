<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Bts_Prestige_System_Prestige
{
	public static function add_prestige_record($id_users, $id_member_approved, $id_officer_approved, $id_prestige_action, $date_claimed, $reward_amount, $reward_type, $reason = null)
	{
		global $wpdb;
		$table = $wpdb->prefix.BTS_TABLE_PREFIX."prestige_rewards";
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
		
		$id_prestige_record = $wpdb->insert_id;
		
		if($reason)
		{
			self::add_record_note($id_prestige_record, $id_users, $reason, $date_claimed);
		}
		return $id_prestige_record;
	}
	
	public static function add_record_note($id_prestige_record, $id_users, $note_text, $date_note_added, $approved = false, $id_officer = null)
	{
		global $wpdb;
		$table = $wpdb->prefix.BTS_TABLE_PREFIX."prestige_reward_notes";
		//	id 	id_prestige_rewards 	id_users 	id_officer 	note 	approved 	date 
		$record = [
			'id_prestige_rewards'=>$id_prestige_record,
			'id_users'=>$id_users,
			'note'=>$note_text,
			'date'=>$date_note_added,
			'approved'=>$approved
		];
		if($id_officer)
		{
			$record['id_officer']=$id_officer;
		}
		$wpdb->insert($table,$record);
	}
	
	
	public static function cooerce_record_to_object($record)
	{
		$basic_fields = ["id", "officer_id_user", "reward_amount", "reward_type", "date_claimed", "description", "category", "officer_title", "domain_name", "genre_name"];
		$ordered_record = new stdClass();
		$ordered_record->notes = [];
		foreach($basic_fields as $basic_field)
		{
			$ordered_record->$basic_field = $record->$basic_field;
		}
		return $ordered_record;
	}
	
	public static function cooerce_record_to_note_object($record)
	{
		$note_fields = ["note", "note_officer_title", "note_domain_name", "note_genre_name"];
		$note_object = new stdClass();
		foreach($note_fields as $note_field)
		{
			$note_object->$note_field = $record->$note_field;
		}
		return $note_object;
	}
	
	public static function get_prestige_for_user_by_id($id_users)
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		$qry = $wpdb->prepare("
			SELECT 
				pr.id					AS id,
				pr.id_member_approved	AS officer_id_user,
				pr.reward_amount		AS reward_amount,
				pr.reward_type			AS reward_type,
				pr.date_claimed			AS date_claimed,
				pa.description			AS description,
				pc.name					AS category,
				o.title					AS officer_title,
				d.name					AS domain_name,
				g.name					AS genre_name,
				pn.id					AS note_id,
				pn.note					AS note,
				pn.approved				AS approved,
				n_o.title				AS note_officer_title,
				dn.name					AS note_domain_name,
				dg.name					AS note_genre_name
			FROM
							{$prefix}prestige_rewards pr
				LEFT JOIN	{$prefix}prestige_actions pa		ON (pa.id	= pr.id_prestige_action)
				LEFT JOIN	{$prefix}prestige_categories pc		ON (pc.id	= pa.id_prestige_category)
				LEFT JOIN	{$prefix}prestige_reward_notes pn	ON (pr.id	= pn.id_prestige_rewards)
				LEFT JOIN	{$prefix}officers o					ON (o.id	= pr.id_officer_approved)
				LEFT JOIN	{$prefix}venues v					ON (v.id	= o.id_venues)
				LEFT JOIN	{$prefix}domains d					ON (d.id	= o.id_domains)
				LEFT JOIN	{$prefix}genres g					ON (g.id	= v.id_genres)
				LEFT JOIN	{$wpdb->prefix}users nu				ON (nu.ID	= pn.id_users)
				LEFT JOIN	{$prefix}officers n_o				ON (n_o.id	= pn.id_officer)
				LEFT JOIN	{$prefix}venues vn					ON (vn.id	= n_o.id_venues)
				LEFT JOIN	{$prefix}domains dn					ON (dn.id	= n_o.id_domains)
				LEFT JOIN	{$prefix}genres dg					ON (dg.id	= vn.id_genres)

			WHERE
				pr.id_member = %d
			ORDER BY 
				date_claimed	ASC,
				id				ASC,
				pn.id			ASC
			",
			$id_users);
			//191);
		
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
			$prestige_rewards[$prestige_record->id]->approved = $prestige_record->approved?'Approved':'Not approved';
			$prestige_rewards[$prestige_record->id]->notes[] = self::cooerce_record_to_note_object($prestige_record);
		}
		return $prestige_rewards;
	}
	
	public static function show_prestige_management_page()
	{
		$prestige_rewards = self::get_prestige_for_user_by_id(get_current_user_id());
		require_once (plugin_dir_path(__FILE__).'partials/bts-prestige-system-prestige-management-page.php');
	}
}