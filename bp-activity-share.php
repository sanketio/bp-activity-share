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
 * Plugin URI:      https://bitbucket.org/sanketio/bp-activity-share
 * Description:     This plugin shares BuddyPress Activity locally (i.e. in site only like we share Facebook posts).
 * Version:         1.0.0
 * Author:          wp3sixty
 * Author URI:      http://wp3sixty.com/
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     bp-activity-share
 * Domain Path:     /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
