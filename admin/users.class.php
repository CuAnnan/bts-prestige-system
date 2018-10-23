<?php namespace BTS_Prestige\Admin;

class Users
{
	public static function getUsermeta()
	{
		$users = get_users(['fields'=>['id']]);
		return self::get_user_meta_array($users);
	}
	
	public static function get_users_by_id_domains($id_domains_array)
	{
		$users = get_users(
			[
				'fields'=>['id'],
				'meta_query'=>[
					array(
						'key'		=>'id_domains',
						'value'		=>$id_domains_array,
						'compare'	=>'IN'
					)
				]
			]
		);
		return self::get_user_meta_array($users);
	}
	
	
	public static function get_user_meta_array($users)
	{
		$usersMetaData = [];
		foreach($users as $user_id)
		{
			$meta = array_map( function( $a ){ return $a[0]; }, get_user_meta($user_id->id));
			$meta['id'] = $user_id->id;
			$usersMetaData[] = $meta;
		}
		return $usersMetaData;
	}

}