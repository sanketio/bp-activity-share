<?php
/**
 * The file that defines the core plugin class
 *
 * @since       1.0.0
 *
 * @package     BP_Activity_Share
 * @subpackage  BP_Activity_Share/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since   1.0.0
 *
 * @package     BP_Activity_Share
 * @subpackage  BP_Activity_Share/includes
 */
class BP_Activity_Share {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
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
	 * @var     string  $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since   1.0.0
	 *
	 * @access  protected
	 *
	 * @var     string  $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	public function __construct() {

		$this->plugin_name = 'bp-activity-share';
		$this->version     = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Plugin_Name_Loader.    Orchestrates the hooks of the plugin.
	 * - Plugin_Name_i18n.      Defines internationalization functionality.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-activity-share-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-activity-share-i18n.php';

		$this->loader = new BP_Activity_Share_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the BP_Activity_Share_i18n class in order to set the domain and to register the hook
	 * with WordPress.
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
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
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
