<?php
/**
 * The file that defines the core plugin class
 *
 * @since       1.0.0
 *
 * @package     BP_Activity_Share
 * @subpackage  BP_Activity_Share / includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, and public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current version of the plugin.
 *
 * @since       1.0.0
 *
 * @package     BP_Activity_Share
 * @subpackage  BP_Activity_Share / includes
 */
class BP_Activity_Share {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power the plugin.
	 *
	 * @since   1.0.0
	 *
	 * @access  protected
	 *
	 * @var     BP_Activity_Share_Loader    $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since   1.0.0
	 *
	 * @access  protected
	 *
	 * @var     string      $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since   1.0.0
	 *
	 * @access  protected
	 *
	 * @var     string      $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the public-facing side of the site.
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	public function __construct() {

		$this->plugin_name = 'bp-activity-share';
		$this->version     = '1.5.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_public_ajax_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - BP_Activity_Share_Loader.      Orchestrates the hooks of the plugin.
	 * - BP_Activity_Share_i18n.        Defines internationalization functionality.
	 * - BP_Activity_Share_Public.      Defines all hooks for the public side of the site.
	 * - BP_Activity_Share_Public_Ajax. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks with WordPress.
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-activity-share-loader.php';

		/**
		 * The class responsible for defining internationalization functionality of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-activity-share-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bp-activity-share-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bp-activity-share-public.php';

		/**
		 * The class responsible for defining all ajax actions that occur in the public-facing side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bp-activity-share-public-ajax.php';

		$this->loader = new BP_Activity_Share_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the BP_Activity_Share_i18n class in order to set the domain and to register the hook with WordPress.
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 */
	private function set_locale() {

		$plugin_i18n = new BP_Activity_Share_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality of the plugin.
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 */
	private function define_admin_hooks() {

		$bp_activity_share_admin = new BP_Activity_Share_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'bp_register_admin_settings', $bp_activity_share_admin, 'bp_activity_share_activity_types', 11 );
		$this->loader->add_action( 'admin_notices',              $bp_activity_share_admin, 'bp_activity_share_add_admin_notice' );

		$this->loader->add_filter( 'bp_activity_get_types', $bp_activity_share_admin, 'bpas_add_share_type' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality of the plugin.
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 */
	private function define_public_hooks() {

		$bp_activity_share_public = new BP_Activity_Share_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts',                        $bp_activity_share_public, 'bp_activity_share_enqueue_scripts' );
		$this->loader->add_action( 'wp_enqueue_scripts',                        $bp_activity_share_public, 'bp_activity_share_enqueue_style' );
		$this->loader->add_action( 'bp_activity_entry_meta',                    $bp_activity_share_public, 'bp_activity_share_button_render' );
		$this->loader->add_action( 'bp_before_activity_entry_comments',         $bp_activity_share_public, 'bp_activity_share_render_custom_options' );
		$this->loader->add_action( 'bp_activity_before_action_delete_activity', $bp_activity_share_public, 'bp_activity_share_delete_activity', 10, 2 );
		$this->loader->add_action( 'admin_init',                                $bp_activity_share_public, 'register_activity_action' );

		$this->loader->add_filter( 'bp_get_activity_show_filters_options', $bp_activity_share_public, 'bp_activity_share_add_filter_options', 10, 2 );
		$this->loader->add_filter( 'bp_ajax_querystring',                  $bp_activity_share_public, 'activity_querystring_filter',          12, 2 );
	}

	/**
	 * Register all of the ajax actions related to the public-facing functionality of the plugin.
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 */
	private function define_public_ajax_hooks() {

		$bp_activity_share_public_ajax = new BP_Activity_Share_Public_Ajax();

		$this->loader->add_action( 'wp_ajax_bp_share_activity',        $bp_activity_share_public_ajax, 'bp_activity_action_bp_share_activity' );
		$this->loader->add_action( 'wp_ajax_nopriv_bp_share_activity', $bp_activity_share_public_ajax, 'bp_activity_action_bp_share_activity' );
		$this->loader->add_action( 'bp_activity_allowed_tags',         $bp_activity_share_public_ajax, 'bp_activity_share_override_allowed_tags' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	public function run() {

		$this->loader->run();

	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of WordPress and to define internationalization functionality.
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 *
	 * @return  string  The name of the plugin.
	 */
	public function get_plugin_name() {

		return $this->plugin_name;

	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 *
	 * @return  BP_Activity_Share_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {

		return $this->loader;

	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 *
	 * @return  string  The version number of the plugin.
	 */
	public function get_version() {

		return $this->version;

	}
}
