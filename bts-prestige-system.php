<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://wing.so-4pt.net
 * @since             1.0.0
 * @package           Bts_Prestige_System
 *
 * @wordpress-plugin
 * Plugin Name:       BTS Prestige System
 * Plugin URI:        http://wing.so-4pt.net/BTS-Prestige-System
 * Description:       A plugin to manage prestige for all users in the wordpress instance.
 * Version:           1.0.0
 * Author:            wing
 * Author URI:        http://wing.so-4pt.net
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bts-prestige-system
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('PLUGIN_NAME_VERSION', '1.0.0');

define('BTS_TABLE_PREFIX', 'bts_');
define('BTS_MANAGE_CLUB_STRUCTURE_ROLE', 'club_manager');
define('BTS_MANAGE_CLUB_STRUCTURE_PERM', 'club_management');
define('BTS_ABS_PATH', plugin_dir_path(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bts-prestige-system-activator.php
 */
function activate_bts_prestige_system() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bts-prestige-system-activator.php';
	Bts_Prestige_System_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bts-prestige-system-activator.php
 */
function deactivate_bts_prestige_system() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bts-prestige-system-activator.php';
	Bts_Prestige_System_Activator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bts_prestige_system' );
register_deactivation_hook( __FILE__, 'deactivate_bts_prestige_system' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bts-prestige-system.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bts_prestige_system() {

	$plugin = new Bts_Prestige_System();
	$plugin->run();

}
run_bts_prestige_system();
