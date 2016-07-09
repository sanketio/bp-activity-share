<?php
/**
 * The file contains front-end functionality
 *
 * @since       1.0.0
 *
 * @package     BP_Activity_Share
 * @subpackage  BP_Activity_Share/public
 */

/**
 * The public class.
 *
 * This is used to define buddypress hooks for front-end.
 *
 * @since       1.0.0
 *
 * @package     BP_Activity_Share
 * @subpackage  BP_Activity_Share/public
 */
class BP_Activity_Share_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 *
	 * @var     string  $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since   1.0.0
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
	 * @param   string $plugin_name    The name of the plugin.
	 * @param   string $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Rendering share button in activity
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	public function bp_activity_share_button_render() {

		if ( true === $this->bp_activity_share_can_share() ) {
			?>
			<a href="<?php $this->bp_activity_share_link(); ?>" class="button bp-activity-share bp-primary-action" title="<?php esc_attr_e( 'Share this activity', 'buddypress' ); ?>"><?php printf( esc_html__( 'Share', 'buddypress' ) . '<span>%s</span>', esc_html__( $this->bp_activity_share_get_share_count() ) ); ?></a>
			<?php
		}

	}

	/**
	 * Determine if an activity is sharable.
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 *
	 * @global  object  $activities_template
	 *
	 * @return  bool    $can_share
	 */
	private function bp_activity_share_can_share() {

		global $activities_template;

		$bp = buddypress();

		$can_share = true;

		// Determine activity type name.
		$activity_type = bp_get_activity_type();

		// Supported activity types array.
		$activity_supported_types = array( 'activity_update' );
		$activity_supported_types = apply_filters( 'bp_activity_share_supported_types', $activity_supported_types );

		// Checking if activity is supported for share.
		if ( ! in_array( $activity_type, $activity_supported_types, true ) ) {
			$can_share = false;
		}

		$can_share = apply_filters( 'bp_activity_share_can_share', $can_share, $activity_type, $activity_supported_types );

		return $can_share;

	}

	/**
	 * Output the activity share link.
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 */
	private function bp_activity_share_link() {

		$args = array(
			'a'    => array(
				'href'  => array(),
				'class' => array(),
				'title' => array(),
			),
			'span' => array(),
		);

		echo wp_kses( $this->bp_get_activity_share_link(), $args );

	}

	/**
	 * Return the activity share link.
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 *
	 * @global  object  $activities_template
	 *
	 * @return  string  The activity share link
	 */
	private function bp_get_activity_share_link() {

		global $activities_template;

		return apply_filters( 'bp_get_activity_share_link', wp_nonce_url( home_url( bp_get_activity_root_slug() . '/share/' . $activities_template->activity->id . '/' ), 'share' ) );

	}

	/**
	 * Return the share count of an activity item.
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 *
	 * @global  object  $activities_template
	 *
	 * @return int
	 */
	private function bp_activity_share_get_share_count() {

		global $activities_template;

		$count = 0;

		return (int) $count;

	}
}
