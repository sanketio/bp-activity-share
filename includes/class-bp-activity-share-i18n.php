<?php
/**
 * Define the internationalization functionality
 *
 * @since       1.0.0
 *
 * @package     BP_Activity_Share
 * @subpackage  BP_Activity_Share / includes
 */

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin so that it is ready for translation.
 *
 * @since       1.0.0
 *
 * @package     BP_Activity_Share
 * @subpackage  BP_Activity_Share / includes
 */
class BP_Activity_Share_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain( 'bp-activity-share', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );

	}
}
