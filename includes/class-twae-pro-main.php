<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Plugin class.
 *
 * The main class that initiates and runs the addon.
 *
 * @since 1.0.0
 */
final class TWAE_PRO_Main {


	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 * @var string Minimum PHP version required to run the addon.
	 */
	const MINIMUM_PHP_VERSION = '5.6';

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 * @var \Elementor_Test_Addon\Plugin The single instance of the class.
	 */
	private static $instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @return \Elementor_Test_Addon\Plugin An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;

	}

	/**
	 * Constructor
	 *
	 * Perform some compatibility checks to make sure basic requirements are meet.
	 * If all compatibility checks pass, initialize the functionality.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		if ( $this->is_compatible() ) {
			add_action( 'elementor/init', array( $this, 'init' ) ); // Initialize the addon after Elementor is initialized.
			// Add a custom category for panel widgets
			add_action( 'elementor/init', array( $this, 'register_timeline_category' ) ); // Register a custom category for panel widgets.
			add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'twae_plugin_editor_styles' ) ); // Enqueue styles for the editor.
			// This code is used for the editor side (paste design)
			add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'twae_copy_paste_enqueue' ) ); // Enqueue scripts for the editor side (paste design).
		}

	}

	/**
	 * Enqueue scripts for the editor side (paste design).
	 */
	public function twae_copy_paste_enqueue() {
		$src          = TWAE_PRO_URL . 'admin/twae-copy-paste/twae-paste-js.js';
		$dependencies = array( 'elementor-editor' );
		wp_enqueue_script(
			'twae-paste-js',
			$src,
			$dependencies,
			TWAE_PRO_VERSION,
			true
		); // Enqueue the paste script for the editor.
		wp_localize_script(
			'twae-paste-js',
			'twaepastejs',
			array(
				'storageKey' => md5( 'Twae LICENSE KEY' ),
				'ajaxURL'    => admin_url( 'admin-ajax.php' ),
				'nonce'      => wp_create_nonce( 'twae_process_ixport' ),
			)
		); // Localize the paste script for the editor.

	}

	/**
	 * Register a custom category for panel widgets.
	 */
	public function register_timeline_category() {

			\Elementor\Plugin::$instance->elements_manager->add_category(
				'twae',              // The name of the category
				array(
					'title' => esc_html__( 'Timeline Widgets', 'twae' ),
					'icon'  => 'fa fa-header', // Default icon
				),
				1 // Position
			); // Register a custom category for timeline widgets.
	}

	/**
	 * Enqueue styles for the editor.
	 */
	function twae_plugin_editor_styles() {
		wp_enqueue_style( 'twae-disabled-widget', TWAE_PRO_URL . 'admin/preset/disabled-widget.css', array() ); // Enqueue styles for the disabled widget.
	}

	/**
	 * Compatibility Checks
	 *
	 * Checks whether the site meets the addon requirement.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_compatible() {

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) ); // Add an admin notice for the minimum PHP version requirement.
			return false;
		}

		return true;

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'twae' ),
			'<strong>' . esc_html__( 'Timeline Widget Pro For Elementor', 'twae' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'twae' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message ); // Display an admin notice for the minimum PHP version requirement.

	}

	/**
	 * Initialize
	 *
	 * Load the addons functionality only after Elementor is initialized.
	 *
	 * Fired by `elementor/init` action hook.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {
		add_action( 'elementor/controls/register', array( $this, 'register_twae_preset_control' ) ); // Register the addon's preset control.
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) ); // Register the addon's widgets.

		if ( function_exists( 'is_plugin_active' ) ) {
			if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
				require_once TWAE_PRO_PATH . 'includes/class-twae-wpml-translation.php'; // Include the WPML translation class if WPML plugin is active.
				add_filter( 'wpml_elementor_widgets_to_translate', array( $this, 'timeline_widgets_to_translate_filter' ) ); // Add a filter for WPML translation of timeline widgets.
			};
		}

	}

	/**
	 * Include the addon's controls file and register the addon's preset control.
	 *
	 * @param \Elementor\Controls_Manager $controls_manager Elementor controls manager.
	 */
	public function register_twae_preset_control( $controls_manager ) {
		require_once TWAE_PRO_PATH . 'controls/twae-controls.php'; // Include the addon's controls file.
		$controls_manager->register( new Twae_Presets_Control() ); // Register the addon's preset control.
	}

	/**
	 * Register Widgets
	 *
	 * Load widgets files and register new Elementor widgets.
	 *
	 * Fired by `elementor/widgets/register` action hook.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_widgets( $widgets_manager ) {

		require_once TWAE_PRO_PATH . 'widgets/story-timeline/twae-widget.php'; // Include the story timeline widget file.
		require_once TWAE_PRO_PATH . 'widgets/content-timeline/twae-content-widget.php'; // Include the content timeline widget file.
		require_once TWAE_PRO_PATH . 'widgets/process-steps/process-steps-widget.php'; // Include the process steps widget file.

		$widgets_manager->register( new steps_process_widget() ); // Register the process steps widget.

		$widgets_manager->register( new TWAE_PRO_Widget() ); // Register the main addon widget.
		$widgets_manager->register( new TWAE_PRO_Post_Widget() ); // Register the addon's post widget.

	}

	/**
	 * Add wpml dependency.
	 *
	 * @param array $widget all elementor widgets.
	 */
	public function timeline_widgets_to_translate_filter( $widget ) {
		$widget['timeline-widget-addon']     = array(
			'conditions'        => array( 'widgetType' => 'timeline-widget-addon' ),
			'fields'            => array(),
			'integration-class' => 'TWAE_WPML_TRANSLATION',
		); // Add WPML translation support for the timeline widget addon.
		$widget['twae-post-timeline-widget'] = array(
			'conditions' => array( 'widgetType' => 'twae-post-timeline-widget' ),
			'fields'     => array(
				array(
					'field'       => 'twae_post_page_text_change',
					'type'        => __( 'Timeline: Pagination Text', 'twae' ),
					'editor_type' => 'LINK',
				),
				array(
					'field'       => 'twae_post_of_text_change',
					'type'        => __( 'Timeline: Pagination Of Text', 'twae' ),
					'editor_type' => 'LINK',
				),
				array(
					'field'       => 'twae_post_load_more_change',
					'type'        => __( 'Timeline: Load More', 'twae' ),
					'editor_type' => 'LINK',
				),
				array(
					'field'       => 'twae_post_readmore_text',
					'type'        => __( 'Timeline: Button Text', 'twae' ),
					'editor_type' => 'LINK',
				),
			),
		); // Add WPML translation support for the post timeline widget.

		return $widget;
	}
}
