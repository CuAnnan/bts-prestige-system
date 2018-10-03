<?php

class Bts_Prestige_System_Users
{
	public static function getUsermeta()
	{
		$users = get_users(['fields'=>['id']]);
		$usersMetaData = [];
		foreach($users as $user_id)
		{
			$meta = get_user_meta($user_id->id);
			$meta['id'] = $user_id->id;
			$usersMetaData[] = $meta;
		}
		return $usersMetaData;
	}
}