<?php namespace BTS_Prestige\Admin;

class Genres
{
	public static function get_genres()
	{
		global $wpdb;
		$prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
		return $wpdb->get_results("SELECT * FROM {$prefix}genres");
	}
        
        public static function add_genre($name, $short_name)
        {
            global $wpdb;
            $prefix = $wpdb->prefix.BTS_TABLE_PREFIX;
            $wpdb->insert(
                $prefix.'genres',
                [
                    'name'=>$name,
                    'short_name'=>$short_name
                ]
            );
            return $wpdb->insert_id;
        }
}