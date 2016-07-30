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
		$activity_supported_types = array( 'activity_update', 'bp_activity_share' );
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

		return apply_filters( 'bp_get_activity_share_link', wp_nonce_url( home_url( bp_get_activity_root_slug() . '/bp_activity_share/' . $activities_template->activity->id . '/' ), 'bp_share_activity' ) );

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

		$item_id = ( 0 === $activities_template->activity->item_id ) ? $activities_template->activity->id : $activities_template->activity->item_id;

		// Getting activity share count.
		$share_count = bp_activity_get_meta( $item_id, 'bp_share_activity_count', true );

		return (int) $share_count;

	}

	/**
	 * Share activity action
	 *
	 * @since	1.0.0
	 *
	 * @access	public
	 *
	 * @return bool
	 */
	public function bp_activity_action_share_activity() {

		if ( ! is_user_logged_in() || ! bp_is_activity_component() || ! bp_is_current_action( 'bp_activity_share' ) ) {
			return false;
		}

		// Check the nonce.
		check_admin_referer( 'bp_share_activity' );

		// Activity ID of activity being share.
		$current_activity_id = bp_action_variable( 0 );

		$current_user_id = bp_loggedin_user_id();

		// Getting activity using Activity ID.
		$current_activity = bp_activity_get_specific( array( 'activity_ids' => $current_activity_id ) );

		// Parent activity user's profile link.
		$parent_profile_link 	  = bp_core_get_userlink( $current_activity['activities'][0]->user_id );

		// Current user's profile link.
		$current_profile_link 	   = bp_core_get_userlink( $current_user_id );

		if ( $current_activity['activities'][0]->user_id === $current_user_id ) {
			$action = sprintf( esc_html__( '%1$s shared an update', 'bp-activity-share' ), $current_profile_link );
		} else {
			$action = sprintf( esc_html__( '%1$s shared %2$s\'s update', 'bp-activity-share' ), $current_profile_link, $parent_profile_link );
		}

		$item_id 		   = ( 0 === $current_activity['activities'][0]->item_id ) ? $current_activity['activities'][0]->id : $current_activity['activities'][0]->item_id;
		$secondary_item_id = ( 0 === $current_activity['activities'][0]->secondary_item_id ) ? $current_activity['activities'][0]->id : $current_activity_id;

		// Activity Component.
		$component = bp_current_component();

		// Prepare activity arguments.
		$activity_args = array(
			'user_id'           => $current_user_id,
			'action'            => $action,
			'component'			=> $component,
			'content'           => $current_activity['activities'][0]->content,
			'type'              => 'bp_activity_share',
			'primary_link'      => $current_profile_link,
			'secondary_item_id' => $secondary_item_id,
			'item_id' 			=> $item_id,
		);

		$activity_id = bp_activity_add( $activity_args );

		// Maintaining share activity count.
		$share_count = bp_activity_get_meta( $item_id, 'bp_share_activity_count', true );
		$share_count = ! empty( $share_count ) ? (int) $share_count + 1 : 1;

		bp_activity_update_meta( $item_id, 'bp_share_activity_count', $share_count );

		// Maintaining user's shared activity.
		$my_shared = bp_get_user_meta( $current_user_id, 'bp_shared_activities', true );

		if ( empty( $my_shared ) || ! is_array( $my_shared ) ) {
			$my_shared = array();
		}

		if ( ! in_array( $activity_id, $my_shared, true ) ) {
			$my_shared[] = $activity_id;
		}

		bp_update_user_meta( $current_user_id, 'bp_shared_activities', $my_shared );

	}
}
