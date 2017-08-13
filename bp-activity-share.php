<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since           1.0.0
 *
 * @package         BP_Activity_Share
 *
 * @wordpress-plugin
 * Plugin Name:     BP Activity Share
 * Plugin URI:      https://github.com/sanketio/bp-activity-share
 * Description:     This plugin shares BuddyPress Activity locally (i.e., in site only like we share Facebook posts).
 * Version:         1.5.0
 * Author:          Pranali, Sanket
 * Author URI:      https://github.com/sanketio/bp-activity-share
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     bp-activity-share
 * Domain Path:     /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class that is used to define internationalization, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-bp-activity-share.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since   1.0.0
 */
function run_bp_activity_share() {

	$bp_activity_share = new BP_Activity_Share();
	$bp_activity_share->run();

}

run_bp_activity_share();
