<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Bts_Prestige_System_Genres
{
	public static function get_genres()
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		return $wpdb->get_results("SELECT * FROM {$prefix}genres");
	}
}