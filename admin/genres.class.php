<?php namespace BTS_Prestige\Admin;

class Genres
{
	public static function get_genres()
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		return $wpdb->get_results("SELECT * FROM {$prefix}genres");
	}
}