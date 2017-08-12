<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link        https://sanket09.wordpress.com/
 *
 * @since       1.3.0
 *
 * @package     BP_Activity_share
 * @subpackage  BP_Activity_share / admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version.
 *
 * @since       1.3.0
 *
 * @package     BP_Activity_share
 * @subpackage  BP_Activity_share / admin
 */
class BP_Activity_Share_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since   1.3.0
	 *
	 * @access  private
	 *
	 * @var     string  $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since   1.3.0
	 *
	 * @access  private
	 *
	 * @var     string  $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 *
	 * @param   string  $plugin_name    The name of this plugin.
	 * @param   string  $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		// Add link to settings page.
		add_filter( 'plugin_action_links',               array( $this, 'bp_activity_share_plugin_action_links' ), 11, 2 );
		add_filter( 'network_admin_plugin_action_links', array( $this, 'bp_activity_share_plugin_action_links' ), 11, 2 );

	}

	/**
	 * Register settings fields for BP Activity Share.
	 *
	 * @since	1.3.0
	 *
	 * @access	public
	 */
	public function bp_activity_share_activity_types() {

		// Checking if activity module is active or not.
		if ( function_exists( 'bp_is_active' ) && bp_is_active( 'activity' ) ) {
			// Add the main section.
			add_settings_section(
				'bp_activity_share_section',
				esc_html__( 'BP Activity Share Settings', 'bp-activity-share' ),
				array( $this, 'bp_activity_share_callback_section' ),
				'buddypress'
			);

			// Add settings fields.
			add_settings_field(
				'bpas-allowed-types',
				__( 'Sharable Activity types', 'bp-activity-share' ),
				array( $this, 'bp_activity_share_settings_field' ),
				'buddypress',
				'bp_activity_share_section'
			);

			// Register settings.
			register_setting(
				'buddypress',
				'bpas-allowed-types',
				array( $this, 'bp_activity_share_types_sanitize' )
			);
		}

	}

	/**
	 * Section callback function.
	 *
	 * @since	1.3.0
	 *
	 * @access	public
	 */
	public function bp_activity_share_callback_section() {

		echo '';

	}

	/**
	 * Settings fields callback.
	 *
	 * @since	1.3.0
	 *
	 * @access	public
	 */
	public function bp_activity_share_settings_field() {

		// Get all activity types.
		$activity_types = bp_activity_get_types();

		// Get allowed activity types.
		$allowed_share_types = get_option( 'bpas-allowed-types', array( 'activity_update', 'bp_activity_share' ) );
		?>
		<table>
			<tr>
				<?php
				$i = 1;

				foreach ( $activity_types as $type => $caption ) {
					?>
					<td>
						<input id="bpas-allowed-types-<?php echo esc_attr( $type ); ?>" name="bpas-allowed-types[<?php echo esc_attr( $type ); ?>]" type="checkbox" value="1" <?php checked( in_array( $type, $allowed_share_types, true ) ); ?>>&nbsp;
						<label for="bpas-allowed-types-<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $caption ) ?></label>
					</td>
					<?php
					if ( 0 === ( $i % 3 ) ) {
						?>
						</tr>
						<tr>
						<?php
					}

					$i++;
				}
				?>
			</tr>
		</table>
		<?php

	}

	/**
	 * Sanitize activity share options.
	 *
	 * @since	1.3.0
	 *
	 * @access	public
	 *
	 * @param 	bool 	$option
	 *
	 * @return 	array
	 */
	public function bp_activity_share_types_sanitize( $option = false ) {

		// Filter post array.
		$post_array = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

		// Checking if `bpas-allowed-types` is set in post array.
		if ( ! empty( $post_array['bpas-allowed-types'] ) && is_array( $post_array['bpas-allowed-types'] ) ) {
			$option = array_keys( $post_array['bpas-allowed-types'] );
		} else {
			$option = array();
		}

		return $option;

	}

	/**
	 * Add `bp_activity_share` in Activity list.
	 *
	 * @since	1.3.0
	 *
	 * @access	public
	 *
	 * @param 	array	$actions
	 *
	 * @return 	array
	 */
	public function bpas_add_share_type( $actions ) {

		// Store `bp_activity_share` in Activity Types list.
		$actions['bp_activity_share'] = esc_html__( 'BP Activity Share', 'bp-activity-share' );

		return $actions;

	}

	/**
	 * Add Settings link to plugins area.
	 *
	 * @since 1.4.0
	 *
	 * @param array $links Links array in which we would prepend our link.
	 * @param string $file Current plugin basename.
	 *
	 * @return array Processed links.
	 */
	public function bp_activity_share_plugin_action_links( $links, $file ) {

		// Return normal links if not BP Activity Share.
		if ( plugin_basename( 'bp-activity-share/bp-activity-share.php' ) !== $file ) {
			return $links;
		}

		if ( ! function_exists( 'bp_get_admin_url' ) ) {
			return $links;
		}

		// Add a few links to the existing links array.
		return array_merge( $links, array(
			'settings' => '<a href="' . esc_url( bp_get_admin_url( add_query_arg( array( 'page' => 'bp-settings' ), 'admin.php' ) ) ) . '">' . esc_html__( 'Settings', 'bp-activity-share' ) . '</a>',
		) );
	}


	/**
	 * Add admin notice if BuddyPress plugin is not active.
	 *
	 * @since 1.5.0
	 *
	 * @access public
	 */
	public function bp_activity_share_add_admin_notice() {

		if ( ! is_plugin_active( 'buddypress/bp-loader.php' ) ) {

			?>
			<div class="notice notice-error is-dismissible">
				<p>
					<?php
					/* translators: Placeholders: %1$s - <b>, %2$s - </b>, %3$s - <b>, %4$s - <a>, %5$s - </a>, %6$s - </b> */
					echo sprintf( __( '%1$sBP Activity Share%2$s requires %3$s%4$sBuddyPress%5$s%6$s plugin to be activated. Deactivating BP Activity Share plugin.', 'bp-activity-share' ), '<b>', '</b>', '<b>', '<a href="https://wordpress.org/plugins/buddypress/" target="_blank">', '</a>', '</b>' );
					?>
				</p>
			</div>
			<?php

			deactivate_plugins( 'bp-activity-share/bp-activity-share.php' );
		}
	}

}
