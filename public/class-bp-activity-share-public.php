<?php
/**
 * The file contains front-end functionality
 *
 * @since       1.0.0
 *
 * @package     BP_Activity_Share
 * @subpackage  BP_Activity_Share / public
 */

/**
 * The public class.
 *
 * This is used to define buddypress hooks for front-end.
 *
 * @since       1.0.0
 *
 * @package     BP_Activity_Share
 * @subpackage  BP_Activity_Share / public
 */
class BP_Activity_Share_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since	1.0.0
	 *
	 * @access  private
	 *
	 * @var     string	$plugin_name	The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 *
	 * @var     string  $version	The current version of this plugin.
	 */
	private $version;

	/**
	 * The suffix for css / js files.
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 *
	 * @var     string  $suffix    The suffix for css / js files.
	 */
	private $suffix;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 *
	 * @param   string	$plugin_name    The name of the plugin.
	 * @param   string 	$version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->suffix      = $this->bp_activity_share_check_script_debug();

	}

	/**
	 * Enqueueing js files
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	public function bp_activity_share_enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bp-activity-share-public' . $this->suffix . '.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Enqueueing css files
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	public function bp_activity_share_enqueue_style() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bp-activity-share-public' . $this->suffix . '.css', '', $this->version );

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

			// Getting share count.
			$share_count = $this->bp_activity_share_get_share_count();
			?>
			<a href="#" class="button bp-primary-action bpas-show-share-options" title="<?php esc_attr_e( 'Share this activity', 'bp-activity-share' ); ?>"><?php printf( esc_html__( 'Share', 'bp-activity-share' ) . ' <span>%s</span>', esc_html( $share_count ) ); ?></a>
			<div class="bpas-share-options-wrapper hide">
				<select class="bpas-share-options">
					<option value="bpas-sitewide-activity"><?php esc_html_e( 'Site-Wide Activity', 'bp-activity-share' ); ?></option>
					<option value="bpas-share-custom"><?php esc_html_e( 'Share with Custom Text', 'bp-activity-share' ); ?></option>
					<?php echo $this->bp_activity_share_get_users_groups(); ?>
				</select>
				<a href="<?php $this->bp_activity_share_link(); ?>" class="button bp-activity-share bp-primary-action" title="<?php esc_attr_e( 'Share this activity', 'bp-activity-share' ); ?>">
					<i class="dashicons dashicons-yes"></i>
				</a>
				<a href="#" class="button bpas-cancel bp-primary-action" title="<?php esc_attr_e( 'Cancel', 'bp-activity-share' ); ?>">
					<i class="dashicons dashicons-no-alt"></i>
				</a>
			</div>
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
	 * @return  bool	$can_share
	 */
	private function bp_activity_share_can_share() {

		$can_share = true;

		// Determine activity type name.
		$activity_type = bp_get_activity_type();

		// Get allowed activity types.
		$activity_supported_types = get_option( 'bpas-allowed-types', array( 'activity_update', 'bp_activity_share' ) );
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

		return apply_filters( 'bp_get_activity_share_link', wp_nonce_url( home_url( bp_get_activity_root_slug() . '/bp_activity_share/' . $activities_template->activity->id . '/' ), 'bpshare_activity' ) );

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
	 * @return 	int
	 */
	private function bp_activity_share_get_share_count() {

		global $activities_template;

		// Checking current activity's component.
		if ( 'activity' === $activities_template->activity->component ) {
			$item_id = ( 0 === $activities_template->activity->item_id ) ? $activities_template->activity->id : $activities_template->activity->item_id;
		} elseif ( in_array( $activities_template->activity->component, array( 'groups', 'profile' ), true ) ) {
			$item_id = bp_activity_get_meta( $activities_template->activity->id, 'bp_share_activity_main_parent_id', true );

			if ( empty( $item_id ) ) {
				$item_id = $activities_template->activity->id;
			}
		}

		// Getting activity share count.
		$share_count = bp_activity_get_meta( $item_id, 'bp_share_activity_count', true );

		return (int) $share_count;

	}

	/**
	 * Success / Error message for sharing an activity
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	public function bp_activity_share_render_custom_options() {

		if ( true === $this->bp_activity_share_can_share() ) :

			?>
			<div class="bp-activity-share-custom" style="display: none;">
				<textarea name="bpas-custom-text" class="bpas-custom-text"></textarea>
				<select class="bpas-custom-share-options">
					<option value="bpas-sitewide-activity"><?php esc_html_e( 'Site-Wide Activity', 'bp-activity-share' ); ?></option>
					<?php echo $this->bp_activity_share_get_users_groups(); ?>
				</select>
				<a href="<?php $this->bp_activity_share_link(); ?>" class="button bp-activity-share bp-primary-action" title="<?php esc_attr_e( 'Share this activity', 'bp-activity-share' ); ?>">
					<i class="dashicons dashicons-yes"></i>
				</a>
				<a href="#" class="button bpas-cancel bp-primary-action" title="<?php esc_attr_e( 'Cancel', 'bp-activity-share' ); ?>">
					<i class="dashicons dashicons-no-alt"></i>
				</a>
			</div>
			<div class="bp-activity-share-message"></div>
			<?php

		endif;
	}

	/**
	 * Checking if SCRIPT_DEBUG constant is defined or not and return $suffix
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 *
	 * @return  string
	 */
	public function bp_activity_share_check_script_debug() {

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && true === constant( 'SCRIPT_DEBUG' ) ) ? '' : '.min';

		return $suffix;

	}

	/**
	 * Update share count and item_id & secondary_item_id
	 *
	 * @since	1.2.0
	 *
	 * @access	public
	 *
	 * @param 	int		$activity_id	Activity ID which is being delete.
	 * @param 	int		$user_id		User ID of an activity.
	 */
	public function bp_activity_share_delete_activity( $activity_id, $user_id ) {

		global $wpdb;

		// Activity & Activity Meta table names.
		$activity_table = $wpdb->prefix . 'bp_activity';
		$activity_meta_table = $wpdb->prefix . 'bp_activity_meta';

		// Update item_id when activity is being delete.
		$data = array(
			'item_id' => 0,
		);
		$where = array(
			'item_id' => $activity_id,
			'type' 	  => 'bp_activity_share',
		);

		$wpdb->update( $activity_table, $data, $where );

		// Update secondary_item_id when activity is being delete.
		$data = array(
			'secondary_item_id' => 0,
		);
		$where = array(
			'secondary_item_id' => $activity_id,
			'type' 				=> 'bp_activity_share',
		);

		$wpdb->update( $activity_table, $data, $where );

		// Decrease activity share count by 1.
		$orig_parent_id = bp_activity_get_meta( $activity_id, 'bp_share_activity_main_parent_id', true );

		// Checking if an activity has any parent activity id.
		if ( ! empty( $orig_parent_id ) ) {
			$share_count = bp_activity_get_meta( $orig_parent_id, 'bp_share_activity_count', true );

			// Checking if share count is exists in meta.
			if ( ! empty( $share_count ) ) {
				$share_count = (int) $share_count - 1;

				if ( $share_count < 0 ) {
					$share_count = 0;
				}

				// Update activity share count.
				bp_activity_update_meta( $orig_parent_id, 'bp_share_activity_count', $share_count );
			}
		}

		// Remove original parent activity if main activity is being delete.
		$select_query = "SELECT * FROM {$activity_meta_table} WHERE meta_key='bp_share_activity_main_parent_id' AND meta_value=" . $activity_id;
		$results 	  = $wpdb->get_results( $select_query );

		// Checking if rows are exists.
		if ( ! empty( $results ) ) {
			foreach ( $results as $meta ) {
				// Delete `bp_share_activity_main_parent_id` meta.
				bp_activity_delete_meta( $meta->activity_id, 'bp_share_activity_main_parent_id' );
			}
		}

		// Maintaining user's shared activity.
		$user_shared = bp_get_user_meta( $user_id, 'bp_shared_activities', true );

		// Checking if $activity_id is already shared.
		if ( ! empty( $user_shared ) && in_array( $activity_id, $user_shared, true ) ) {
			foreach ( $user_shared as $key => $act_id ) {
				if ( (int) $activity_id === (int) $act_id ) {
					unset( $user_shared[ $key ] );
				}
			}
		}

		if ( ! empty( $user_shared ) ) {
			$user_shared = array_values( $user_shared );

			// Updating user's shared activity meta.
			bp_update_user_meta( $user_id, 'bp_shared_activities', $user_shared );
		} else {
			// Delete user's shared activity meta.
			bp_delete_user_meta( $user_id, 'bp_shared_activities' );
		}

	}


	/**
	 * Returns list of user's groups
	 *
	 * @since 1.4.0
	 *
	 * @return string
	 */
	private function bp_activity_share_get_users_groups() {

		$current_user_id = bp_loggedin_user_id();
		$html            = '';

		// Checking if group component is active.
		if ( bp_is_active( 'groups' ) ) {

			// Getting group ids of current user.
			$group_ids = BP_Groups_Member::get_group_ids( $current_user_id );

			// Checking if user belongs to any group.
			if ( ! empty( $group_ids ) && ! empty( $group_ids['groups'] ) ) {

				$html .= '<optgroup label="' . esc_attr__( 'Groups', 'bp-activity-share' ) . '">';

				foreach ( $group_ids['groups'] as $group_id ) {

					// Getting group info using group id.
					$group = groups_get_group( array( 'group_id' => $group_id ) );

					$html .= '<option value="group-' . esc_attr( $group_id ) . '">' . esc_html( $group->name ) . '</option>';
				}

				$html .= '</optgroup>';
			}
		}

		return $html;
	}


	/**
	 * Register BP Activity Share as an action of BuddyPress.
	 *
	 * @since 1.5.0
	 *
	 * @access public
	 */
	public function register_activity_action() {

		if ( class_exists( 'BuddyPress' ) && function_exists( 'bp_activity_set_action' ) ) {

			bp_activity_set_action(
				'bp_activity_share',
				'bp_activity_share',
				esc_html__( 'BP Activity Share', 'bp-activity-share' )
			);
		}
	}


	/**
	 * Add BP Activity Share option in BuddyPress activity filter
	 *
	 * @since 1.5.0
	 *
	 * @access public
	 *
	 * @param array  $filters BuddyPress activity filter array
	 * @param string $context BuddyPress context
	 *
	 * @return mixed
	 */
	public function bp_activity_share_add_filter_options( $filters, $context ) {

		$filters['bp_activity_share'] = esc_html__( 'Shared Updates', 'bp-activity-share' );

		return $filters;
	}


	/**
	 * Modify query string filter to get shared updates.
	 *
	 * @since 1.5.0
	 *
	 * @access public
	 *
	 * @param string $query_string
	 * @param string $object
	 *
	 * @return string
	 */
	public function activity_querystring_filter( $query_string = '', $object = '' ) {

		// Return if object is not activity.
		if ( $object != 'activity' ) {
			return $query_string;
		}

		// Manipulating the query string by transforming it into an array and merging arguments with these default ones.
		$args = wp_parse_args( $query_string, array(
			'action'  => false,
			'type'    => false,
			'user_id' => false,
			'page'    => 1
		) );

		if ( $args['type'] === 'bp_activity_share' ) {
			$query_string = 'type=bp_activity_share&action=bp_activity_share';
		}

		/**
		 * Filter query string for BP Activity Share
		 *
		 * @since 1.5.0
		 *
		 * @param string $query_string Query string.
		 * @param string $object       BuddyPress object.
		 */
		return apply_filters( 'bp_activity_share_activity_querystring_filter', $query_string, $object );
	}
}
