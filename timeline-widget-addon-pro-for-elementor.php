<?php
/**
 * Plugin Name: Timeline Widget Pro For Elementor
 * Description: Best timeline widget for Elementor page builder to showcase your personal or business stories in beautiful vertical or horizontal timeline layouts with many preset styles. <strong>[Elementor Addon]</strong>
 * Plugin URI:  https://cooltimeline.com
 * Version:     2.1.2
 * Author:      Cool Plugins
 * Author URI:  https://coolplugins.net
 * Text Domain: twae
 * Elementor tested up to: 3.23.1
 * Elementor Pro tested up to: 3.23.0
 */



if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'TWAE_PRO_VERSION' ) ) {
	return;
}

define( 'TWAE_PRO_VERSION', '2.1.2' );
define( 'TWAE_PRO_FILE', __FILE__ );
define( 'TWAE_PRO_PATH', plugin_dir_path( TWAE_PRO_FILE ) );
define( 'TWAE_PRO_URL', plugin_dir_url( TWAE_PRO_FILE ) );

if ( ! defined( 'TWAE_DEMO_URL' ) ) {
	define( 'TWAE_PREFIX', 'twae' );
	define( 'TWAE_DEMO_URL', 'https://cooltimeline.com/demo/elementor-timeline/?utm_source=twae-plugin&utm_medium=inside&utm_campaign=twae-pro-dashboard' );
}

register_activation_hook( TWAE_PRO_FILE, array( 'Timeline_Widget_Pro_Addon', 'twae_pro_activate' ) );
register_deactivation_hook( TWAE_PRO_FILE, array( 'Timeline_Widget_Pro_Addon', 'twae_pro_deactivate' ) );

/**
 * Class Timeline_Widget_Pro_Addon
 */
final class Timeline_Widget_Pro_Addon {


	/**
	 * Plugin instance.
	 *
	 * @var Timeline_Widget_Pro_Addon
	 * @access private
	 */
	private static $instance = null;

	/**
	 * Get plugin instance.
	 *
	 * @return Timeline_Widget_Pro_Addon
	 * @static
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @access private
	 */
	private function __construct() {
		// $this->twae_load_dependency();
		 // Load the plugin after Elementor (and other plugins) are loaded.
		add_action( 'plugins_loaded', array( $this, 'twae_pro_is_plugin_loaded' ) );

		add_action( 'plugins_loaded', array( $this, 'twae_load_addon' ) );

		add_action( 'plugins_loaded', array( $this, 'twae_pro_load_dependency' ) );
		if ( is_admin() ) {
			// Only one plugin must be active at a time
			add_action( 'admin_init', array( $this, 'twae_pro_is_free_version_active' ) );
			add_action( 'admin_init', array( $this, 'twae_show_upgrade_notice' ) );
		}

	}

	/**
	 * Load the addon.
	 */
	function twae_load_addon() {
		// Load plugin file
		require_once TWAE_PRO_PATH . '/includes/class-twae-pro-main.php';
		// Run the plugin
		TWAE_PRO_Main::instance();

	}

	/**
	 * Load essential file(s) required for the plugin in any/all cases.
	 */
	function twae_pro_load_dependency() {
		if ( is_admin() ) {
			require_once TWAE_PRO_PATH . 'admin/registration-settings.php';
			require_once TWAE_PRO_PATH . '/admin/init-api.php';
			require_once TWAE_PRO_PATH . '/admin/admin-notices.php';
			require_once TWAE_PRO_PATH . '/admin/timeline-addon-page/timeline-addon-page.php';
			cool_plugins_timeline_addons_settings_page( 'timeline', 'cool-plugins-timeline-addon', 'Timeline Addons', 'Timeline Addons', esc_url( TWAE_PRO_URL . 'assets/images/timeline-icon-222.png' ) ); // Escape URL
		}
	}

	/**
	 * Deactivate the free version if the pro version is activated.
	 *
	 * @access public
	 */
	public function twae_pro_is_free_version_active() {
		if ( is_plugin_active( 'timeline-widget-addon-for-elementor/timeline-widget-addon-for-elementor.php' ) ) {
			twae_pro_create_admin_notice(
				array(
					'id'      => 'twae-free-deactivate',
					'message' => __(
						'<strong>Timeline Widget</strong> free version has been <strong>deactivated</strong> as you are using <strong>Timeline Widget Pro for Elementor</strong>.',
						'twae'
					),
				)
			);
			deactivate_plugins( 'timeline-widget-addon-for-elementor/timeline-widget-addon-for-elementor.php' );
		}
	}


	/**
	 * Load the Elementor test addon.
	 *
	 * This function loads the Elementor test addon by requiring the plugin file and running the plugin.
	 */
	public function elementor_test_addon() {
		// Load plugin file
		require_once __DIR__ . '/includes/plugin.php';

		// Run the plugin
		\Timeline_Widget_Addon\Plugin::instance();

	}

	/**
	 * Run when all other plugins are loaded.
	 *
	 * This function checks if Elementor is active and displays a notice if it is not.
	 */
	public function twae_pro_is_plugin_loaded() {
		if ( is_admin() ) {

			// Display notice if Elementor is not active.
			if ( ! did_action( 'elementor/loaded' ) ) {
				add_action( 'admin_notices', array( $this, 'twae_fail_to_load' ) );
				return;
			}

			/*** Plugin review notice file */
			require_once TWAE_PRO_PATH . '/admin/admin-notices.php';
			twae_pro_create_admin_notice(
				array(
					'id'              => 'twae_pro_review_box',  // Unique identifier for the review notice.
					'slug'            => 'twae',      // Slug for the review notice.
					'review'          => true,     // Set to true to display the review notice.
					'review_url'      => 'https://wordpress.org/support/plugin/timeline-widget-addon-for-elementor/reviews/#new-post', // URL for the review notice.
					'plugin_name'     => 'Timeline Widget Pro For Elementor',    // Name of the plugin for the review notice.
					'logo'            => TWAE_PRO_URL . 'assets/images/twae-logo.png',   // Optional: URL for the logo to display.
					'review_interval' => 3,                    // Optional: Number of days before displaying the review notice after the installation_time (default is 3).
				)
			); // End of twae_pro_create_admin_notice;

		}

		load_plugin_textdomain( 'twae', false, basename( dirname( __FILE__ ) ) . '/languages/' );

		// Require the main plugin file.
		require_once TWAE_PRO_PATH . '/includes/twae-functions.php';
		// require_once TWAE_PRO_PATH . '/admin/twae-copy-paste/ixporter.php'.
		require_once TWAE_PRO_PATH . '/includes/class-twae-load-more-handler.php';

	}   // end of ctla_loaded()


	/**
	 * Display a notice if Elementor is not active.
	 */
	public function twae_fail_to_load() {
		// Check if Elementor plugin is active
		if ( ! is_plugin_active( 'elementor/elementor.php' ) ) : ?>
			<div class="notice notice-warning is-dismissible">
				<p><?php echo sprintf( __( 'You must install and activate <a href="%s" target="_blank" >Elementor Website Builder</a> to use "<strong>Timeline Widget Pro For Elementor</strong>".' ), esc_url( 'https://wordpress.org/plugins/elementor/' ) ); ?></p> <!-- Escape URL -->
			</div>
			<?php
			deactivate_plugins( 'timeline-widget-addon-for-elementor-pro/timeline-widget-addon-pro-for-elementor.php' );
		endif;
	}

	/**
	 * Display an upgrade notice if the 'twae-v' option is set.
	 */
	public function twae_show_upgrade_notice() {
		if ( get_option( 'twae-v' ) != false ) {
			twae_pro_create_admin_notice(
				array(
					'id'      => 'twae-upgrade-noticesdfdsf',
					'message' => '<strong>Major Update Notice!</strong> Please update your timeline widget settings if you face any style issue after an update of <strong>Timeline Widget Pro for Elementor</strong>.',
				)
			);
		}
	}

	/**
	 * Run when the plugin is activated.
	 *
	 * Update plugin options on activation.
	 */
	public static function twae_pro_activate() {
		update_option( 'twae-type', 'PRO' );  // Set plugin type to 'PRO'.
		update_option( 'twae_activation_time', gmdate( 'Y-m-d h:i:s' ) );  // Set activation time to current UTC time.
		update_option( 'twae-pro-v', TWAE_PRO_VERSION );  // Set plugin version option.
	}

	/**
	 * Run when the plugin is deactivated.
	 */
	public static function twae_pro_deactivate() {
	}
}

/**
 * Get the instance of the Timeline Widget Pro Addon.
 *
 * @return Timeline_Widget_Pro_Addon The instance of the Timeline Widget Pro Addon.
 */
function timeline_widget_pro_addon() {
	return Timeline_Widget_Pro_Addon::get_instance();
}

$GLOBALS['Timeline_Widget_Pro_Addon'] = Timeline_Widget_Pro_Addon();

