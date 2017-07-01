<?php

/**
 * The ajax actions for public-facing of the plugin.
 *
 * @since       1.0.0
 *
 * @package     BP_Activity_Share
 * @subpackage  BP_Activity_Share / public
 */
/**
 * The ajax actions for public-facing of the plugin.
 *
 * @since       1.0.0
 *
 * @package     BP_Activity_Share
 * @subpackage  BP_Activity_Share / public
 */
class BP_Activity_Share_Public_Ajax {

	/**
	 * BP_Profile_Status_Public_Ajax constructor.
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	public function __construct() {

	}

	/**
	 * Share activity action
	 *
	 * @since	1.0.0
	 *
	 * @access	public
	 *
	 * @return 	bool
	 */
	public function bp_activity_action_bp_share_activity() {

		if ( ! is_user_logged_in() || ! bp_is_activity_component() ) {
			return false;
		}

		// Check the nonce.
		check_admin_referer( 'bpshare_activity', '_wpnonce' );

		// Activity ID of activity being share.
		$current_activity_id = filter_input( INPUT_POST, 'act_id', FILTER_VALIDATE_INT );

		// Where to share an activity.
		$share_to = filter_input( INPUT_POST, 'share_to', FILTER_SANITIZE_STRING );

		// custom text if any.
		$custom_text = filter_input( INPUT_POST, 'custom_text', FILTER_SANITIZE_STRING );

		// Current logged in User's ID.
		$current_user_id = bp_loggedin_user_id();

		// Getting activity using Activity ID.
		$current_activity = bp_activity_get_specific( array( 'activity_ids' => $current_activity_id ) );

		// Current user's profile link.
		$current_profile_link = bp_core_get_userlink( $current_user_id );

		// Checking if sharing is in site-wide activity or in the group.
		if ( 'bpas-sitewide-activity' === $share_to ) {
			// If current activity's component is activity.
			if ( 'activity' === $current_activity['activities'][0]->component ) {
				if ( 0 === $current_activity['activities'][0]->item_id || 0 === $current_activity['activities'][0]->secondary_item_id || null === $current_activity['activities'][0]->secondary_item_id ) {
					// User ID as a current activity's User ID
					$user_id = $current_activity['activities'][0]->user_id;

					// Item id as an activity ID.
					$item_id = $current_activity['activities'][0]->id;
				} else {
					$activity_id = $current_activity['activities'][0]->item_id;

					// Getting parent activity using Item ID.
					$parent_activity = bp_activity_get_specific( array( 'activity_ids' => $activity_id ) );

					// User ID as a parent activity's User ID.
					$user_id = $parent_activity['activities'][0]->user_id;

					// Item id as an item ID.
					$item_id = $current_activity['activities'][0]->item_id;
				}
			} elseif ( in_array( $current_activity['activities'][0]->component, array( 'groups', 'profile' ), true ) ) {
				// Getting main parent activity id.
				$main_parent_id = bp_activity_get_meta( $current_activity['activities'][0]->id, 'bp_share_activity_main_parent_id', true );

				if ( ! empty( $main_parent_id ) ) {
					// Getting parent activity using Item ID.
					$parent_activity = bp_activity_get_specific( array( 'activity_ids' => $main_parent_id ) );

					// User ID as a parent activity's User ID.
					$user_id = $parent_activity['activities'][0]->user_id;

					// Storing main parent id as an item id.
					$item_id = $main_parent_id;
				} else {
					// User ID as a current activity's User ID
					$user_id = $current_activity['activities'][0]->user_id;

					// Storing current activity id as an item id.
					$item_id = $current_activity['activities'][0]->id;
				}
			}

			// Activity ID.
			$activity_id = $item_id;

			// Parent activity user's profile link.
			$parent_profile_link = bp_core_get_userlink( $user_id );
		} else {
			$group_id       = explode( '-', $share_to );
			$group          = groups_get_group( array( 'group_id' => $group_id[1] ) );
			$group_link     = '<a href="' . esc_attr( bp_get_group_permalink( $group ) ) . '">' . esc_attr( $group->name ) . '</a>';
			$main_parent_id = bp_activity_get_meta( $current_activity['activities'][0]->id, 'bp_share_activity_main_parent_id', true );

			if ( ! empty( $main_parent_id ) ) {
				// Getting parent activity using Item ID.
				$parent_activity = bp_activity_get_specific( array( 'activity_ids' => $main_parent_id ) );

				// User ID as a parent activity's User ID.
				$user_id = $parent_activity['activities'][0]->user_id;

				// Activity ID.
				$activity_id = $main_parent_id;
			} else {
				// User ID as a current activity's User ID
				$user_id = $current_activity['activities'][0]->user_id;

				// Activity ID.
				$activity_id = $current_activity['activities'][0]->id;
			}

			// Item id as an item ID.
			$item_id = $group_id[1];

			// Parent activity user's profile link.
			$parent_profile_link = bp_core_get_userlink( $user_id );
		}

		/**
		 * Detect plugin. For use on Front End only.
		 */
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		// Checking if RTMedia plugin is active & component is profile
		if ( ( 'profile' === $current_activity['activities'][0]->component ) && is_plugin_active( 'buddypress-media/index.php' ) ) {
			global $rtmedia;

			$media_type = '';

			// Get media using activity id.
			$rtm_model = new RTMediaModel();
			$media     = $rtm_model->get_media( array(
				'activity_id' => $activity_id,
			) );

			$media_type_array = array();

			// Store media type into array.
			foreach ( $media as $single_media ) {
				array_push( $media_type_array, $single_media->media_type );
			}

			// Checking if all media are of same media type.
			if ( 1 === count( array_unique( $media_type_array ) ) ) {
				$media_type = $media_type_array[0];
			}

			// Check if allowed type is set or not and set label & plural label.
			if ( ! empty( $rtmedia->allowed_types[ $media_type ] ) ) {
				$label        = $rtmedia->allowed_types[ $media_type ]['label'];
				$plural_label = $rtmedia->allowed_types[ $media_type ]['plural_label'];
			} else {
				if ( 'photo' === $media_type ) {
					$label        = esc_html__( 'Photo', 'bp-activity-share' );
					$plural_label = esc_html__( 'Photos', 'bp-activity-share' );
				} elseif ( 'video' === $media_type ) {
					$label        = esc_html__( 'Video', 'bp-activity-share' );
					$plural_label = esc_html__( 'Videos', 'bp-activity-share' );
				} elseif ( 'music' === $media_type ) {
					$label        = esc_html__( 'Music', 'bp-activity-share' );
					$plural_label = esc_html__( 'Music', 'bp-activity-share' );
				} else {
					$label = RTMEDIA_MEDIA_SLUG;
					$plural_label = RTMEDIA_MEDIA_SLUG;
				}
			}

			// Checking if sharing is in site-wide activity or in the group.
			if ( 'bpas-sitewide-activity' === $share_to ) {
				if ( 1 === count( $media ) ) {
					// If user is sharing his/her own activity.
					if ( $current_user_id === $user_id ) {
						$action = sprintf( esc_html__( '%1$s shared a %2$s', 'bp-activity-share' ), $current_profile_link, $label );
					} else {
						$action = sprintf( esc_html__( '%1$s shared %2$s\'s %3$s', 'bp-activity-share' ), $current_profile_link, $parent_profile_link, $label );
					}
				} else {
					// If user is sharing his/her own activity.
					if ( $current_user_id === $user_id ) {
						$action = sprintf( esc_html__( '%1$s shared %2$d %3$s', 'bp-activity-share' ), $current_profile_link, count( $media ), $plural_label );
					} else {
						$action = sprintf( esc_html__( '%1$s shared %2$s\'s %3$d %4$s', 'bp-activity-share' ), $current_profile_link, $parent_profile_link, count( $media ), $plural_label );
					}
				}
			} else {
				if ( 1 === count( $media ) ) {
					// If user is sharing his/her own activity.
					if ( $current_user_id === $user_id ) {
						$action = sprintf( esc_html__( '%1$s shared a %2$s in the group %3$s', 'bp-activity-share' ), $current_profile_link, $label, $group_link );
					} else {
						$action = sprintf( esc_html__( '%1$s shared %2$s\'s %3$s in the group %4$s', 'bp-activity-share' ), $current_profile_link, $parent_profile_link, $label, $group_link );
					}
				} else {
					// If user is sharing his/her own activity.
					if ( $current_user_id === $user_id ) {
						$action = sprintf( esc_html__( '%1$s shared %2$d %3$s in the group %4$s', 'bp-activity-share' ), $current_profile_link, count( $media ), $plural_label, $group_link );
					} else {
						$action = sprintf( esc_html__( '%1$s shared %2$s\'s %3$d %4$s in the group %5$s', 'bp-activity-share' ), $current_profile_link, $parent_profile_link, count( $media ), $plural_label, $group_link );
					}
				}
			}

			$component = 'profile';
		} else {
			// Checking if sharing is in site-wide activity or in the group.
			if ( 'bpas-sitewide-activity' === $share_to ) {
				// If user is sharing his/her own activity.
				if ( $current_user_id === $user_id ) {
					$action = sprintf( esc_html__( '%1$s shared an update', 'bp-activity-share' ), $current_profile_link );
				} else {
					$action = sprintf( esc_html__( '%1$s shared %2$s\'s update', 'bp-activity-share' ), $current_profile_link, $parent_profile_link );
				}

				$component = 'activity';
			} else {
				// If user is sharing his/her own activity.
				if ( $current_user_id === $user_id ) {
					$action = sprintf( esc_html__( '%1$s shared an update in the group %2$s', 'bp-activity-share' ), $current_profile_link, $group_link );
				} else {
					$action = sprintf( esc_html__( '%1$s shared %2$s\'s update in the group %3$s', 'bp-activity-share' ), $current_profile_link, $parent_profile_link, $group_link );
				}

				$component = 'groups';
			}
		}

		$secondary_item_id = ( 0 === $current_activity['activities'][0]->secondary_item_id ) ? $current_activity['activities'][0]->id : $current_activity_id;

		$content = $current_activity['activities'][0]->content;

		// Checking if the activity is already a shared activity with custom text
		$shared_content = explode( 'bpas-shared-content', $content );

		if ( ! empty( $shared_content[1] ) ) {

			$content = substr( $shared_content[1], 2, -6 );
		}

		// Checking if user shared an activity with custom text
		if ( ! empty( $custom_text ) ) {

			$content = $custom_text . '<div class="bpas-shared-content">' . $content . '</div>';
		}

		// Prepare activity arguments.
		$activity_args = array(
			'user_id'           => $current_user_id,
			'action'            => $action,
			'component'         => $component,
			'content'           => $content,
			'type'              => 'bp_activity_share',
			'primary_link'      => $current_profile_link,
			'secondary_item_id' => $secondary_item_id,
			'item_id'           => $item_id,
		);

		$activity_id = bp_activity_add( $activity_args );

		if ( ! empty( $activity_id ) ) {
			// Maintaining share activity count.
			if ( 'bpas-sitewide-activity' === $share_to ) {
				$share_count = bp_activity_get_meta( $item_id, 'bp_share_activity_count', true );
				$share_count = ! empty( $share_count ) ? (int) $share_count + 1 : 1;

				bp_activity_update_meta( $item_id, 'bp_share_activity_count', $share_count );
				bp_activity_update_meta( $activity_id, 'bp_share_activity_main_parent_id', $item_id );
			} else {
				$main_parent_id = bp_activity_get_meta( $current_activity['activities'][0]->id, 'bp_share_activity_main_parent_id', true );

				if ( ! empty( $main_parent_id ) ) {
					$parent_act_id = $main_parent_id;
				} else {
					$parent_act_id = $current_activity['activities'][0]->id;
				}

				$share_count = bp_activity_get_meta( $parent_act_id, 'bp_share_activity_count', true );
				$share_count = ! empty( $share_count ) ? (int) $share_count + 1 : 1;

				bp_activity_update_meta( $parent_act_id, 'bp_share_activity_count', $share_count );
				bp_activity_update_meta( $activity_id, 'bp_share_activity_main_parent_id', $parent_act_id );
			}

			// Maintaining user's shared activity.
			$my_shared = bp_get_user_meta( $current_user_id, 'bp_shared_activities', true );

			// Checking if $mu_shared is exist or not.
			if ( empty( $my_shared ) || ! is_array( $my_shared ) ) {
				$my_shared = array();
			}

			// Checking if $activity_id is already shared.
			if ( ! in_array( $activity_id, $my_shared, true ) ) {
				$my_shared[] = $activity_id;
			}

			// Updating user's shared activity meta.
			bp_update_user_meta( $current_user_id, 'bp_shared_activities', $my_shared );

			// Success message.
			$success_msg = __( 'An update shared successfully.', 'bp-activity-share' );
			$success_msg = apply_filters( 'bpas_success_message', $success_msg );

			$message = array(
				'type'    => 'success',
				'message' => esc_html( $success_msg ),
			);
		} else {
			// Error message.
			$error_msg = __( 'There is an error when sharing this update. Please refresh page and try again.', 'bp-activity-share' );
			$error_msg = apply_filters( 'bpas_error_message', $error_msg );

			$message = array(
				'type'    => 'error',
				'message' => esc_html( $error_msg ),
			);
		}

		bp_core_add_message( $message['message'], $message['type'] );

		bp_core_redirect( wp_get_referer() );

	}


	/**
	 * Overriding allowed tags in the BuddyPress activity.
	 *
	 * @since 1.4.0
	 *
	 * @param array $activity_allowed_tags allowed tags array in BuddyPress activity
	 *
	 * @return array
	 */
	public function bp_activity_share_override_allowed_tags( $activity_allowed_tags ) {

		if ( ! isset( $activity_allowed_tags['div'] ) ) {

			$activity_allowed_tags['div'] = array();
		}

		$activity_allowed_tags['div']['id']    = array();
		$activity_allowed_tags['div']['class'] = array();

		return $activity_allowed_tags;
	}

}
