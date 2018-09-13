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
}