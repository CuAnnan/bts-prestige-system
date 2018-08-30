<?php

/**
 * Fired during plugin activation
 *
 * @link       http://wing.so-4pt.net
 * @since      1.0.0
 *
 * @package    Bts_Prestige_System
 * @subpackage Bts_Prestige_System/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Bts_Prestige_System
 * @subpackage Bts_Prestige_System/includes
 * @author     wing <eamonn.kearns@so-4pt.net>
 */

class Bts_Prestige_System_Activator {

	private static $dbPrefix = 'bts_';
	/**
	 * Short Description. Build the databases.
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		self::buildDatabaseTables();
		require_once plugin_dir_path(__FILE__).'class-bts-prestige-system-data-import.php';
		Bts_Prestige_System_Data_Import::import(new PDO('mysql:host=localhost;dbname=camaus2', 'root'));
	}
	
	/**
	 * Short Description. A method to build the databases used by the system.
	 * @global type $wpdb	The Wordpress database object
	 */
	public static function buildDatabaseTables()
	{
		global $wpdb;
		$prefix = $wpdb->prefix.self::$dbPrefix;
		$charset_collate = $wpdb->get_charset_collate();
		$tables = self::getTables();
		require_once(ABSPATH.'wp-admin/includes/upgrade.php');
		
		foreach($tables as $tableName=>$lines)
		{
			$sql = "CREATE TABLE IF NOT EXISTS {$prefix}{$tableName}(\n".
					join(",\n\t", $lines).
				"\n) {$charset_collate}";
			dbDelta($sql);
			$error = $wpdb->last_error;
			if($error)
			{
				error_log($error);
			}
		}
	}
	
	public static function getTables()
	{
		global $wpdb;
		$prefix = $wpdb->prefix.self::$dbPrefix;
		
		return [
			'domains'=>[
				'id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT',
				'name varchar(255)',
				'nmc_code varchar(16)',
				'number varchar(16)', 
				'location varchar(64)',
				'parent_domain_id bigint(20) UNSIGNED',
				'active int(1) UNSIGNED NOT NULL DEFAULT 0',
				'PRIMARY KEY  (id)',
				"FOREIGN KEY (parent_domain_id) REFERENCES {$prefix}domains(id)"
			],
			'genres'=>[
				'id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT',
				'name varchar(255)',
				'PRIMARY KEY  (id)'
			],
			'venues'=>[
				'id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT',
				'name varchar(255)',
				'id_domains bigint(20) UNSIGNED NOT NULL',
				'id_genres bigint(20) UNSIGNED NOT NULL',
				'nmc_code varchar(10)',
				'active int(1) UNSIGNED NOT NULL DEFAULT 0',
				'PRIMARY KEY  (id)',
				"FOREIGN KEY (id_domains) REFERENCES {$prefix}domains(id)",
				"FOREIGN KEY (id_genres) REFERENCES {$prefix}genres(id)",
			],
			'officers'=>[
				'id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT',
				'id_venues bigint(20) UNSIGNED',
				'id_domains bigint(20) UNSIGNED',
				'id_member bigint(20) UNSIGNED',
				'position varchar(255)',
				'email varchar(255)',
				'PRIMARY KEY  (id)',
				"FOREIGN KEY (id_member) REFERENCES {$wpdb->prefix}users(ID)",
				"FOREIGN KEY (id_venues) REFERENCES {$prefix}venues(id)",
				"FOREIGN KEY (id_domains) REFERENCES {$prefix}domains(id)"
			],
			'prestige_categories'=>[
				'id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT',
				'name varchar(255)',
				'monthly_cap INT UNSIGNED',
				'PRIMARY KEY  (id)'
			],
			'prestige_rewards'=>[
				'id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT',
				'id_member bigint(20) UNSIGNED NOT NULL',
				'id_member_approved bigint(20) UNSIGNED',
				'id_officer_approved bigint(20) UNSIGNED',
				'id_prestige_category bigint(20) UNSIGNED NOT NULL',
				"date_claimed datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
				"date_added datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
				"reward_amount int UNSIGNED NOT NULL",
				'PRIMARY KEY  (id)',
				"FOREIGN KEY (id_member) REFERENCES {$wpdb->prefix}users(ID)",
				"FOREIGN KEY (id_member_approved) REFERENCES {$wpdb->prefix}users(ID)",
				"FOREIGN KEY (id_officer_approved) REFERENCES {$prefix}officers(ID)",
				"FOREIGN KEY (id_prestige_category) REFERENCES {$prefix}prestige_categories(id)",
			],
			'prestige_reward_notes'=>[
				'id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT',
				'id_prestige_rewards bigint(20) UNSIGNED NOT NULL',
				'id_users bigint(20) UNSIGNED NOT NULL',
				'id_officer bigint(20) UNSIGNED',
				'note varchar(255)',
				'approved boolean',
				"date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
				'PRIMARY KEY  (id)',
				"FOREIGN KEY (id_users) REFERENCES {$wpdb->prefix}users(ID)",
				"FOREIGN KEY (id_prestige_rewards) REFERENCES {$prefix}prestige_rewards(id)"
			]
		];
	}
	
	public static function deactivate()
	{
		global $wpdb;
		$prefix = $wpdb->prefix.self::$dbPrefix;
		$tableNames = array_reverse(array_keys(self::getTables()));
		foreach($tableNames as $table)
		{
			$wpdb->query("DROP TABLE IF EXISTS {$prefix}{$table}");
		}
		$wpdb->query("DELETE FROM {$wpdb->prefix}usermeta WHERE user_id > 1");
		$wpdb->query("DELETE FROM {$wpdb->prefix}users WHERE ID > 1");
		$wpdb->query("ALTER TABLE {$wpdb->prefix}users AUTO_INCREMENT = 2;");
	}

}