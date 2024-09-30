<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;


/**
 * This class is used to create a widget in Elementor.
 * The widget is used to display a timeline of stories.
 * It extends the Widget_Base class from the Elementor plugin.
 * It includes several methods for controlling the widget's features and output.
 */
class TWAE_PRO_Widget extends \Elementor\Widget_Base {

	/**
	 * Constructor for the TWAE_PRO_Widget class.
	 *
	 * This constructor initializes the widget, sets up hooks, and defines scripts and styles.
	 *
	 * @param array $data Optional. Widget data. Default is an empty array.
	 * @param array $args Optional. Widget arguments. Default is null.
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		// run hook on page save and update status.
		add_action( 'elementor/editor/after_save', array( $this, 'twae_update_migration_status' ), 10, 2 );

		$min_v   = true;
		$css_ext = '.css';
		$js_ext  = '.js';
		if ( true === $min_v ) {
			$css_ext = '.min.css';
			$js_ext  = '.min.js';
		}

		$js_common_dep = array( 'elementor-frontend' );

		if ( !\Elementor\Plugin::$instance->preview->is_preview_mode() && is_user_logged_in()) {
			$js_common_dep = array( 'elementor-common', 'elementor-frontend' );
		}

		// Common styles.
		wp_register_style( 'twae-common-css', esc_url( TWAE_PRO_URL . 'assets/css/twae-common-styles' . $css_ext ), array(), esc_attr( TWAE_PRO_VERSION ), 'all' );

		// Vertical Timeline.
		wp_register_style( 'twae-vertical-css', esc_url( TWAE_PRO_URL . 'assets/css/twae-vertical-timeline' . $css_ext ), array(), esc_attr( TWAE_PRO_VERSION ), 'all' );
		wp_register_script( 'twae-vertical-timeline-js', esc_url( TWAE_PRO_URL . 'assets/js/twae-vertical-timeline' . $js_ext ), $js_common_dep, esc_attr( TWAE_PRO_VERSION ), true ); // for AOS animation

		// Compact Layout & Images loaded..
		wp_register_script( 'twae-masonry-js', esc_url( TWAE_PRO_URL . 'assets/js/twae-masonry.min.js' ), $js_common_dep, esc_attr( TWAE_PRO_VERSION ), true );
		wp_register_script( 'twae-images-loaded-js', esc_url( TWAE_PRO_URL . 'assets/js/twae-imagesloaded.min.js' ), $js_common_dep, esc_attr( TWAE_PRO_VERSION ), true );
		wp_register_script( 'twae-vertical-compact-js', esc_url( TWAE_PRO_URL . 'assets/js/twae-vertical-timeline-compact' . $js_ext ), $js_common_dep, esc_attr( TWAE_PRO_VERSION ), true );
		wp_register_style( 'twae-vertical-compact', esc_url( TWAE_PRO_URL . 'assets/css/twae-vertical-compact' . $css_ext ), array(), esc_attr( TWAE_PRO_VERSION ), 'all' );

		// Horizontal Timeline.
		wp_register_style( 'twae-horizontal-css', esc_url( TWAE_PRO_URL . 'assets/css/twae-horizontal-timeline' . $css_ext ), array(), esc_attr( TWAE_PRO_VERSION ), 'all' );
		wp_register_script( 'twae-horizontal-js', esc_url( TWAE_PRO_URL . 'assets/js/twae-horizontal-timeline' . $js_ext ), $js_common_dep, esc_attr( TWAE_PRO_VERSION ), true );

		// popup.
		wp_register_script( 'twae-popup-js', esc_url( TWAE_PRO_URL . 'assets/js/twae-popup' . $js_ext ), $js_common_dep, esc_attr( TWAE_PRO_VERSION ), true );

		// AOS animation.
		wp_register_script( 'twae-aos-js', esc_url( TWAE_PRO_URL . 'assets/js/twae-aos.min.js' ), $js_common_dep, esc_attr( TWAE_PRO_VERSION ), true );
		wp_register_style( 'twae-aos-css', esc_url( TWAE_PRO_URL . 'assets/css/twae-aos.min.css' ), array(), esc_attr( TWAE_PRO_VERSION ), 'all' );

		// Images slideshow for both vertical and horizontal timeline.
		wp_register_script( 'twae-slideshow-js', esc_url( TWAE_PRO_URL . 'assets/js/twae-slideshow' . $js_ext ), $js_common_dep, esc_attr( TWAE_PRO_VERSION ), true );

		// Fontello CSS for both vertical and horizontal timeline.
		wp_register_style( 'twae-fontello-css', esc_url( TWAE_PRO_URL . 'assets/css/twae-fontello.css' ), array(), esc_attr( TWAE_PRO_VERSION ), 'all' );

		// Images loaded.
		wp_register_script( 'twae-images-loaded-js', esc_url( TWAE_PRO_URL . 'assets/js/twae-imagesloaded.min.js' ), $js_common_dep, esc_attr( TWAE_PRO_VERSION ), true );

		// Year Navigation bar.
		wp_register_style( 'twae-year-navigation', esc_url( TWAE_PRO_URL . 'assets/css/twae-navigation' . $css_ext ), array(), esc_attr( TWAE_PRO_VERSION ), 'all' );
		wp_register_script( 'twae-year-navigation-js', esc_url( TWAE_PRO_URL . 'assets/js/twae-year-navigation' . $js_ext ), $js_common_dep, esc_attr( TWAE_PRO_VERSION ), true );

	}

	/**
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the element requires.
	 *
	 * @access public
	 *
	 * @return array Element scripts dependencies.
	 */
	public function get_script_depends() {
		$script = array( 'twae-images-loaded-js' );

		// Check if in edit mode or preview mode
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			return array_merge( $script, array( 'twae-horizontal-js', 'twae-aos-js', 'twae-vertical-timeline-js', 'twae-slideshow-js', 'twae-popup-js', 'twae-year-navigation-js', 'twae-masonry-js', 'twae-vertical-compact-js' ) );
		}

		$settings = $this->get_settings_for_display();
		$layout = isset( $settings['twae_layout'] ) ? sanitize_text_field( $settings['twae_layout'] ) : '';
		$animation = isset( $settings['twae_animation'] ) ? sanitize_text_field( $settings['twae_animation'] ) : 'none';
		$navigation_bar = isset( $settings['twae_navigation_bar'] ) ? sanitize_text_field( $settings['twae_navigation_bar'] ) : 'no';

		if ( in_array( $layout, array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ), true ) ) {
			$script = array_merge( $script, array( 'twae-horizontal-js', 'twae-slideshow-js' ) );
		} elseif ( $layout === 'compact' ) {
			$script = array_merge( $script, array( 'twae-masonry-js', 'twae-vertical-compact-js', 'twae-slideshow-js' ) );
		} else {
			if ( $animation !== 'none' ) {
				$script[] = 'twae-aos-js';
			}
			if ( $navigation_bar === 'yes' ) {
				$script[] = 'twae-year-navigation-js';
			}
			$script = array_merge( $script, array( 'twae-vertical-timeline-js', 'twae-slideshow-js' ) );
		}

		if ( isset( $settings['twae_vertical_style'] ) && $settings['twae_vertical_style'] === 'style-4' ||
		     isset( $settings['twae_hr_style'] ) && $settings['twae_hr_style'] === 'style-4' ||
		     isset( $settings['twae_content_in_popup'] ) && $settings['twae_content_in_popup'] === 'yes' ) {
			$script[] = 'twae-popup-js';
		}

		return array_unique( $script );
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the element requires.
	 *
	 * @access public
	 *
	 * @return array Element styles dependencies.
	 */
	public function get_style_depends() {
		$styles = array( 'twae-common-css', 'twae-fontello-css' );

		// Check if in edit mode or preview mode
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			return array_merge( $styles, array( 'twae-vertical-css', 'twae-horizontal-css', 'twae-aos-css', 'twae-year-navigation', 'twae-vertical-compact' ) );
		}

		$settings = $this->get_settings_for_display();
		$layout = isset( $settings['twae_layout'] ) ? sanitize_text_field( $settings['twae_layout'] ) : '';
		$animation = isset( $settings['twae_animation'] ) ? sanitize_text_field( $settings['twae_animation'] ) : 'none';
		$navigation_bar = isset( $settings['twae_navigation_bar'] ) ? sanitize_text_field( $settings['twae_navigation_bar'] ) : 'no';

		if ( in_array( $layout, array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ), true ) ) {
			$styles[] = 'twae-horizontal-css';
		} elseif ( $layout === 'compact' ) {
			$styles[] = 'twae-vertical-css';
			$styles[] = 'twae-vertical-compact';
		} else {
			if ( $animation !== 'none' ) {
				$styles[] = 'twae-aos-css';
			}
			if ( $navigation_bar === 'yes' ) {
				$styles[] = 'twae-year-navigation';
			}
			$styles[] = 'twae-vertical-css';
		}

		return array_unique( $styles );
	}

	/**
	 * Get the name of the widget.
	 *
	 * @return string The name of the widget.
	 */
	public function get_name() {
		return 'timeline-widget-addon';
	}

	/**
	 * Get the title of the widget.
	 *
	 * @return string The title of the widget.
	 */
	public function get_title() {
		return esc_html__( 'Story Timeline', 'twae' );
	}

	/**
	 * Get the icon of the widget.
	 *
	 * @return string The icon of the widget.
	 */
	public function get_icon() {
		return 'eicon-twae-timeline-story';
	}

	/**
	 * Get the categories of the widget.
	 *
	 * @return array The categories of the widget.
	 */
	public function get_categories() {
		return array( 'twae' );
	}

	/**
	 * Register controls for the widget.
	 * This function is responsible for adding controls to the widget, including the content controls and layout settings.
	 */
	protected function register_controls() {

		$this->content_controls();
		/* ----------------------------- Layout Settings ---------------------------- */
		$this->start_controls_section(
			'twae_layout_section',
			array(
				'label' => __( 'Layout Settings', 'twae' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		// Select Layout
		$this->add_control(
			'twae_layout',
			array(
				'label'   => __( 'Layout', 'twae' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'centered',
				'options' => array(
					'centered'               => 'Vertical (Right / Left)',
					'one-sided'              => 'Vertical (Right Only)',
					'left-sided'             => 'Vertical (Left Only)',
					'compact'                => 'Vertical (Compact)',
					'horizontal'             => 'Horizontal (Top)',
					'horizontal-bottom'      => 'Horizontal (Bottom)',
					'horizontal-highlighted' => 'Horizontal (Highlighted)',
				),
			)
		);
		// Vertical Layout Styles (OLD VERSION)
		$this->add_control(
			'twae_vertical_style',
			array(
				'type'    => \Elementor\Controls_Manager::HIDDEN,
				'default' => 'style-1',
			)
		);
		// Horizontal Layout Styles (OLD VERSION)
		$this->add_control(
			'twae_hr_style',
			array(
				'type'    => \Elementor\Controls_Manager::HIDDEN,
				'default' => 'style-1',
			)
		);
		// Vertical Preset Styles
		$this->add_control(
			'twae_preset_vertical_style',
			array(
				'label'       => __( 'Preset Styles', 'twae' ),
				'description' => __( '!! Preset styles will completely change your current style settings, if you have already selected any style settings.', 'twae' ),
				'type'        => 'twae_preset_style',
				'default'     => 'v-style-0',
				// 'options' => $options,
				'options'     => array(
					'v-style-0' => 'Default',
					'v-style-1' => 'Classic',
					'v-style-2' => 'Elegant',
					'v-style-3' => 'Clean',
					'v-style-4' => 'Minimal',
					'v-style-5' => 'Bold',
					'v-style-6' => 'Flat',
				),
				'condition'   => array(
					'twae_layout!' => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);
		// Horizontal Preset Styles

		$this->add_control(
			'twae_preset_hr_style',
			array(
				'label'       => __( 'Preset Style', 'twae' ),
				'description' => __( '!! Preset styles will completely change your current style settings, if you have already selected any style settings.', 'twae' ),
				'type'        => 'twae_preset_style',
				'default'     => 'h-style-0',
				// 'options' => $options,
				'options'     => array(
					'h-style-0' => 'Default',
					'h-style-1' => 'Classic',
					'h-style-2' => 'Elegant',
					'h-style-3' => 'Clean',
					'h-style-4' => 'Minimal',
					'h-style-5' => 'Flat',
				),
				'condition'   => array(
					'twae_layout' => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
				// 'render_type' => 'none'
			)
		);
		// Horizontal Slides Settings
		$this->add_control(
			'twae_horizontal_slides',
			array(
				'label'     => esc_html__( '🔶 Horizontal Slides', 'twae' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'twae_layout' => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);

		$this->add_control(
			'twae_slides_to_show',
			array(
				'label'     => esc_html__( 'Slides To Show', 'twea1' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => '2',
				'options'   => array(
					'1' => esc_html__( '1 Slide', 'twae' ),
					'2' => esc_html__( '2 Slides', 'twae' ),
					'3' => esc_html__( '3 Slides', 'twae' ),
					'4' => esc_html__( '4 Slides', 'twae' ),
					'5' => esc_html__( '5 Slides', 'twae' ),
					'6' => esc_html__( '6 Slides', 'twae' ),
				),
				'condition' => array(
					'twae_layout' => array( 'horizontal', 'horizontal-bottom' ),
				),

			)
		);
		$this->add_control(
			'twae_highlighted_to_show',
			array(
				'label'     => esc_html__( 'Story Show', 'twea1' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => '3',
				'options'   => array(
					'1' => esc_html__( '1 Slide', 'twae' ),
					'2' => esc_html__( '2 Slides', 'twae' ),
					'3' => esc_html__( '3 Slides', 'twae' ),
					'4' => esc_html__( '4 Slides', 'twae' ),
					'5' => esc_html__( '5 Slides', 'twae' ),
					'6' => esc_html__( '6 Slides', 'twae' ),
				),
				'condition' => array(
					'twae_layout' => array( 'horizontal-highlighted' ),
				),

			)
		);
		$this->add_control(
			'twae_highlighted_active_color',
			array(
				'label'     => esc_html__( 'Story Active Color', 'twea1' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ff8571',
				'selectors' => array(
					'{{WRAPPER}} .twae-horizontal-highlighted-timeline' => '--tw-highlighted-color: {{VALUE}}',
				),
				'condition' => array(
					'twae_layout' => array( 'horizontal-highlighted' ),
				),
			)
		);
		// Horizontal Space b/w Stories
		$this->add_control(
			'twae_h_space_bw',
			array(
				'label'       => __( 'Space b/w Slides', 'twae' ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'render_type' => 'template',
				'range'       => array(
					'px' => array(
						'min'  => 10,
						'max'  => 120,
						'step' => 1,
					),
				),
				'default'     => array(
					'unit' => 'px',
					'size' => 20,
				),
				'condition'   => array(
					'twae_layout' => array( 'horizontal', 'horizontal-bottom' ),
				),
				'selectors'   => array(
					'{{WRAPPER}} .twae-wrapper ' => '--tw-cbx-space: {{SIZE}};',
				),
			)
		);
		// Horizontal Nav Icon
		$this->add_control(
			'navigation_control_icon',
			array(
				'label'     => esc_html__( 'Navigation Icon', 'twea' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'fas fa-chevron-left',
				'options'   => array(
					'fas fa-angle-left'            => esc_html__( 'Angle', 'twea' ),
					'fas fa-angle-double-left'     => esc_html__( 'Angle Double', 'twea' ),
					'fas fa-arrow-left'            => esc_html__( 'Arrow', 'twea' ),
					'fas fa-arrow-alt-circle-left' => esc_html__( 'Arrow Circle', 'twea' ),
					'far fa-arrow-alt-circle-left' => esc_html__( 'Arrow Circle Alt', 'twea' ),
					'fas fa-long-arrow-alt-left'   => esc_html__( 'Long Arrow', 'twea' ),
					'fas fa-chevron-left'          => esc_html__( 'Chevron', 'twea' ),
					'fas fa-caret-left'            => esc_html__( 'Caret', 'twea' ),
					'fas fa-caret-square-left'     => esc_html__( 'Caret Square', 'twea' ),
					'fas fa-hand-point-left'       => esc_html__( 'Hand', 'twea' ),
				),
				'condition' => array(
					'twae_layout' => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
				// 'separator' => 'before',
			)
		);
		// Horizontal Slides Autoplay
		$this->add_control(
			'twae_autoplay',
			array(
				'label'     => __( 'Autoplay', 'twae' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'false',
				'options'   => array(
					'true'  => 'True',
					'false' => 'False',
				),
				'condition' => array(
					'twae_layout' => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);
		$this->add_control(
			'twae_autoplaystop_mousehover',
			array(
				'label'        => __( 'Autoplay Pause On Hover', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'on', 'twae' ),
				'label_off'    => __( 'off', 'twae' ),
				'return_value' => 'true',
				'default'      => 'false',
				'condition'    => array(
					'twae_autoplay' => array( 'true' ),
					'twae_layout'   => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);
		// Horizontal Slides Loop
		$this->add_control(
			'twae_infinite_loop',
			array(
				'label'     => esc_html__( 'Infinite Loop?', 'twea1' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'false',
				'options'   => array(
					'true'  => esc_html__( 'Yes', 'twae' ),
					'false' => esc_html__( 'No', 'twae' ),
				),
				'condition' => array(
					'twae_layout' => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);
		// Horizontal Slides Animation Speed
		$this->add_control(
			'twae_speed',
			array(
				'label'     => esc_html__( 'Slide Speed (100 to 10000)', 'twea1' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 100,
				'max'       => 10000,
				'step'      => 100,
				'default'   => 1000,
				'condition' => array(
					'twae_layout' => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);
		// Horizontal Equal Height Slides
		$this->add_control(
			'twae_slides_height',
			array(
				'label'       => __( 'Slides Height', 'twae' ),
				'description' => __( 'Make all slides the same height based on the tallest slide', 'twae' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'default-height',
				'options'     => array(
					'equal-height-slides' => 'Equal Height',
					'default-height'      => 'Auto',
				),
				'condition'   => array(
					'twae_layout' => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);
		// Story Content Alignment
		$this->add_control(
			'content-alignment',
			array(
				'label'     => esc_html__( 'Content Alignment', 'twae' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'separator' => 'before',
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'twae' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'twae' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'twae' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'toggle'    => true,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-cbx-text-align: {{VALUE}};',
				),
			)
		);
		// Story Content Alternate Alignment
		$this->add_control(
			'content-alignment_alternate',
			array(
				'label'     => esc_html__( 'Content Alignment (Left)', 'twae' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'twae' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'twae' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'twae' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'left',
				'toggle'    => true,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-cbx-text-align-alternate: {{VALUE}};',
				),
				'condition' => array(
					'twae_layout' => array(
						'centered',
						'compact',
					),
				),
			)
		);
		// Display Connector Type
		$this->add_control(
			'twae_cbox_connector_style',
			array(
				'label'   => esc_html__( 'Connector Style', 'twae' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => array(
					'default'         => array(
						'title' => esc_html__( 'Arrow', 'twae' ),
						'icon'  => 'eicon-chevron-left',
					),
					'twae-arrow-line' => array(
						'title' => esc_html__( 'Line', 'twae' ),
						'icon'  => 'eicon-h-align-left',
					),
					'twae-arrow-none' => array(
						'title' => esc_html__( 'None', 'twae' ),
						'icon'  => 'eicon-ban',
					),
				),
				'default' => 'default',
				'toggle'  => true,
			)
		);
		// Display Icons, Dot or None
		$this->add_control(
			'twae_display_icons',
			array(
				'label'   => esc_html__( 'Display Icons', 'twae' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => array(
					'displayicons' => array(
						'title' => esc_html__( 'Icons', 'twae' ),
						'icon'  => 'eicon-clock',
					),
					'displaydots'  => array(
						'title' => esc_html__( 'Dots', 'twae' ),
						'icon'  => 'eicon-circle',
					),
					'displaynone'  => array(
						'title' => esc_html__( 'None', 'twae' ),
						'icon'  => 'eicon-ban',
					),
				),
				'default' => 'displayicons',
				'toggle'  => false,
			)
		);
		// Select FontAwesome Icon
		$this->add_control(
			'twae_story_icons',
			array(
				'label'     => __( 'FontAwesome Icon', 'twae' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'far fa-clock',
					'library' => 'fa-regular',
				),
				'condition' => array(
					'twae_display_icons' => 'displayicons',
				),
			)
		);
		// Icon Box Position
		$this->add_responsive_control(
			'twae_icon_position',
			array(
				'label'       => __( 'Icon / Labels Position', 'twae' ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'render_type' => 'template',
				'range'       => array(
					'px' => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					),
				),
				'devices'     => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'   => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-ibx-position: {{SIZE}};',
				),
				'condition'   => array(
					'twae_layout!' => array( 'compact', 'horizontal-highlighted' ),
				),
			)
		);
		// Content Media / Image Position
		$this->add_control(
			'story_media_order',
			array(
				'label'     => esc_html__( 'Media Position', 'twea' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array(
					'0' => esc_html__( 'Top', 'twea' ),
					'2' => esc_html__( 'Below Title', 'twea' ),
					'4' => esc_html__( 'Below Description', 'twea' ),
					// '6' => esc_html__( 'Bottom', 'twea' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper ' => '--tw-cbx-img-order: {{VALUE}};',
				),
				'condition' => array(
					'twae_content_in_popup'      => 'no',
					'twae_content_side_by_side!' => 'yes',
				),
			)
		);
		// Story Content in Popup
		$this->add_control(
			'twae_content_in_popup',
			array(
				'label'   => __( 'Content In Popup', 'twae' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'no',
				'options' => array(
					'no'  => 'No',
					'yes' => 'Yes',
				),
			)
		);

		$this->add_control(
			'twae_content_side_by_side',
			array(
				'label'        => __( 'Content Side By Side', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'twae' ),
				'label_off'    => __( 'Hide', 'twae' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'twae_layout' => array(
						'horizontal-highlighted',
					),
				),
			)
		);
		$this->add_control(
			'twae_lightbox_settings',
			array(
				'label'        => esc_html__( 'Image Pop Up', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'twae' ),
				'label_off'    => __( 'Hide', 'twae' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => array(
					'twae_content_in_popup' => 'no',
				),
			)
		);
		// Vertical Animations
		$animations = Twae_Functions::twae_pro_animation_array();
		$this->add_control(
			'twae_animation',
			array(
				'label'     => __( 'Animations', 'twae' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => $animations,
				'condition' => array(
					'twae_layout!' => array(
						'horizontal',
						'compact',
						'horizontal-bottom',
						'horizontal-highlighted',
					),
				),
			)
		);

		// image outside condition
		$this->add_control(
			'twae_image_outside_box',
			array(
				'label'        => esc_html__( 'Image Out Of The Box', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'twae' ),
				'label_off'    => __( 'Hide', 'twae' ),
				'return_value' => 'twae_image_outside',
				'default'      => 'no',
				'condition'    => array(
					'twae_layout'           => array( 'centered' ),
					'twae_content_in_popup' => 'no',
				),
			)
		);
		// Vertical Navigation Bar
		$this->add_control(
			'twae_navigation_bar',
			array(
				'label'        => __( 'Navigation / Menu', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'twae' ),
				'label_off'    => __( 'Hide', 'twae' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => array(
					'twae_layout!' => array(
						'compact',
						'horizontal-highlighted',
					),
				),
			)
		);
		// Vertical Navigation Notice
		$this->add_control(
			'twae_navigation_notice',
			array(
				'label'     => __( 'You must add Year / Label (Story Top Position) in order to show navigation menu.', 'twae' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'condition' => array(
					'twae_navigation_bar' => 'yes',
					'twae_layout!'        => array(
						'compact',
						'horizontal-highlighted',
					),

				),
			)
		);
		// Vertical Navigation Style
		$this->add_control(
			'twae_navigation_style',
			array(
				'label'     => __( 'Navigation Style', 'twae' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'style-1',
				'options'   => array(
					'style-1' => 'Style 1',
					'style-2' => 'Style 2',
					'style-3' => 'Style 3',
				),
				'condition' => array(
					'twae_layout!'        => array(
						'horizontal',
						'compact',
						'horizontal-bottom',
						'horizontal-highlighted',
					),
					'twae_navigation_bar' => 'yes',
				),
			)
		);
		// Vertical Navigation Position
		$this->add_control(
			'twae_navigation_position',
			array(
				'label'     => __( 'Navigation Position', 'twae' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'right',
				'options'   => array(
					'right' => 'Right',
					'left'  => 'Left',
				),
				'condition' => array(
					'twae_navigation_bar' => 'yes',
					'twae_layout!'        => array(
						'compact',
						'horizontal-bottom',
						'horizontal',
						'horizontal-highlighted',
					),
				),
			)
		);
		// horizontal Navigation Position
		$this->add_control(
			'twae_hr_navigation_position',
			array(
				'label'     => __( 'Navigation Position', 'twae' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'left',
				'options'   => array(
					'right'  => 'Right',
					'center' => 'center',
					'left'   => 'Left',
				),
				'condition' => array(
					'twae_navigation_bar' => 'yes',
					'twae_layout'         => array(
						'horizontal-bottom',
						'horizontal',
					),
				),
			)
		);
		// Layout Section End
		$this->end_controls_section();

		$this->style_controls();

	}

	/**
	 * Update some settings when user saves Elementor data.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param int   $post_id     The ID of the post.
	 * @param array $editor_data The editor data.
	 */
	public function twae_update_migration_status( $post_id, $editor_data ) {

		if ( get_option( 'twae-v' ) !== false ) {
			if ( get_post_meta( intval( $post_id ), 'twae_exists', true ) ) {
				update_post_meta( intval( $post_id ), 'twae_style_migration', 'done' );
				update_option( 'twae-migration-status', 'done' );
				return;
			}
		}
	}

	/**
	 * Apply specific styles to each story in the timeline.
	 *
	 * @param int    $post_id        The ID of the post.
	 * @param array  $settings       The settings of the widget.
	 * @param string $story_key      The key of the story.
	 * @param string $timeline_style The style of the timeline.
	 *
	 * @return string|bool The custom styles or false if there are no custom styles.
	 */
	public function specific_story_style( $post_id, $settings, $story_key, $timeline_style ) {
		$widgetID      = '.elementor-' . esc_attr( $post_id ) . ' .elementor-element.elementor-element-' . esc_attr( $this->get_id() );
		$selector      = $widgetID . ' .twae-wrapper' . ' .' . esc_attr( $story_key );
		$custom_styles = '';
		if ( isset( $settings['twae_custom_story_title_color'] ) && ! empty( $settings['twae_custom_story_title_color'] ) ) {
			$custom_styles .= $selector . '{--tw-cbx-title-color:' . esc_attr( $settings['twae_custom_story_title_color'] ) . '}';
			$custom_styles .= $selector . '{--tw-ibx-bg:' . esc_attr( $settings['twae_custom_story_title_color'] ) . '}';
			$custom_styles .= $selector . '{--tw-arw-bd-color:' . esc_attr( $settings['twae_custom_story_title_color'] ) . '}';
			$custom_styles .= $selector . '{--tw-cbx-bd-color:' . esc_attr( $settings['twae_custom_story_title_color'] ) . '}';
		}

		if ( isset( $settings['twae_custom_title_icon_bgc'] ) && ! empty( $settings['twae_custom_title_icon_bgc'] ) && $timeline_style == 'style-2' ) {
			$custom_styles .= $selector . '{--tw-cbx-title-bg:' . esc_attr( $settings['twae_custom_title_icon_bgc'] ) . '}';

			$custom_styles .= $selector . ' .twae-icon{background-color:' . esc_attr( $settings['twae_custom_title_icon_bgc'] ) . ' !important}';

			$custom_styles .= $selector . ' .twae-icondot{background-color:' . esc_attr( $settings['twae_custom_title_icon_bgc'] ) . ' !important}';
			$custom_styles .= $selector . ' .twae-icon{--tw-ibx-color:' . esc_attr( $settings['twae_custom_story_title_color'] ) . '}';
			$custom_styles .= $selector . '{--tw-arw-line-background:' . esc_attr( $settings['twae_custom_title_icon_bgc'] ) . '}';
		}

		if ( isset( $settings['twae_custom_label_color'] ) && ! empty( $settings['twae_custom_label_color'] ) ) {
			$custom_styles .= $selector . '{--tw-lbl-big-color:' . esc_attr( $settings['twae_custom_label_color'] ) . ';}';
			$custom_styles .= $selector . '{--tw-lbl-small-color:' . esc_attr( $settings['twae_custom_label_color'] ) . ';}';
		}
		if ( isset( $settings['twae_custom_sublabel_color'] ) && ! empty( $settings['twae_custom_sublabel_color'] ) ) {
			$custom_styles .= $selector . '{--tw-lbl-small-color:' . esc_attr( $settings['twae_custom_sublabel_color'] ) . ';}';
		}
		if ( isset( $settings['twae_custom_description_color'] ) && ! empty( $settings['twae_custom_description_color'] ) ) {
			$custom_styles .= $selector . '{--tw-cbx-des-color:' . esc_attr( $settings['twae_custom_description_color'] ) . ';}';
		}
		if ( isset( $settings['twae_custom_year_label_bgcolor'] ) && ! empty( $settings['twae_custom_year_label_bgcolor'] ) ) {
			$custom_styles .= $selector . '{--tw-ybx-bg:' . esc_attr( $settings['twae_custom_year_label_bgcolor'] ) . '}';
		}
		if ( isset( $settings['twae_custom_story_bgcolor'] ) && ! empty( $settings['twae_custom_story_bgcolor'] ) ) {
			$custom_styles .= $selector . '{--tw-cbx-bg:' . esc_attr( $settings['twae_custom_story_bgcolor'] ) . '}';
			$custom_styles .= $selector . '{--tw-arw-bg:' . esc_attr( $settings['twae_custom_story_bgcolor'] ) . '}';
			$custom_styles .= $selector . '{--tw-ibx-color:' . esc_attr( $settings['twae_custom_story_bgcolor'] ) . '}';
		}

		if ( ! empty( $custom_styles ) ) {
			return $custom_styles;
		} else {
			return false;
		}
	}

	/**
	 * This function is for compatibility with versions less than 1.3.
	 * It generates custom styles based on the settings provided.
	 *
	 * @param int    $post_id The post ID.
	 * @param array  $settings The settings array.
	 * @param string $timeline_style The timeline style.
	 * @return string|bool The custom styles or false if there are no custom styles.
	 */
	public function older_v_compatibility( $post_id, $settings, $timeline_style ) {
			$custom_styles = '';
			$widgetID      = '.elementor-' . intval( $post_id ) . ' .elementor-element.elementor-element-' . $this->get_id();
			$selector      = $widgetID . ' .twae-wrapper';
			$typo_index    = '_typography';

			$custom_styles .= $selector . '.twae-vertical .twae-story{margin-bottom:50px!important}';

		if ( isset( $settings['twae_story_title_color'] ) && !empty( $settings['twae_story_title_color'] ) ) {
			$custom_styles .= $selector . '{--tw-cbx-title-color:' . esc_attr( $settings['twae_story_title_color'] ) . ';}';
		}
		if ( isset( $settings['twae_date_label_color'] ) && !empty( $settings['twae_date_label_color'] ) ) {
			$custom_styles .= $selector . '{--tw-lbl-big-color:' . esc_attr( $settings['twae_date_label_color'] ) . ';}';
		}
		if ( isset( $settings['twae_extra_label_color'] ) && !empty( $settings['twae_extra_label_color'] ) ) {
			$custom_styles .= $selector . '{--tw-lbl-small-color:' . esc_attr( $settings['twae_extra_label_color'] ) . ';}';
		}
		if ( isset( $settings['twae_description_color'] ) && !empty( $settings['twae_description_color'] ) ) {
			$custom_styles .= $selector . '{--tw-cbx-des-color:' . esc_attr( $settings['twae_description_color'] ) . ';}';
		}
		if ( isset( $settings['twae_icon_bgcolor'] ) && !empty( $settings['twae_icon_bgcolor'] ) ) {
			$custom_styles .= $selector . '{--tw-ibx-bg:' . esc_attr( $settings['twae_icon_bgcolor'] ) . '}';
			$custom_styles .= $selector . '{--tw-cbx-bd-color:' . esc_attr( $settings['twae_icon_bgcolor'] ) . ';--tw-arw-bd-color:' . esc_attr( $settings['twae_icon_bgcolor'] ) . ';}';
		}
		if ( isset( $settings['twae_year_label_color'] ) && !empty( $settings['twae_year_label_color'] ) ) {
			$custom_styles .= $selector . '{--tw-ybx-text-color:' . esc_attr( $settings['twae_year_label_color'] ) . '}';
		}
		if ( isset( $settings['twae_year_label_bgcolor'] ) && !empty( $settings['twae_year_label_bgcolor'] ) ) {
			$custom_styles .= $selector . '{--tw-ybx-bg:' . esc_attr( $settings['twae_year_label_bgcolor'] ) . '}';
		}
		if ( isset( $settings['twae_story_bgcolor'] ) && !empty( $settings['twae_story_bgcolor'] ) ) {
			$custom_styles .= $selector . '{--tw-cbx-bg:' . esc_attr( $settings['twae_story_bgcolor'] ) . '}';
		}
		if ( isset( $settings['twae_line_color'] ) && !empty( $settings['twae_line_color'] ) ) {
			$custom_styles .= $selector . '{--tw-line-bg:' . esc_attr( $settings['twae_line_color'] ) . '}';
		}
		if ( isset( $settings['twae_icon_size'] ) && !empty( $settings['twae_icon_size'] ) ) {
			$custom_styles .= $selector . '{--tw-ibx-text-size:' . esc_attr( $settings['twae_icon_size']['size'] ) . esc_attr( $settings['twae_icon_size']['unit'] ) . '}';
		}

		if ( $timeline_style == 'style-2' ) {
			if ( isset( $settings['twae_el_story_title_color'] ) && !empty( $settings['twae_el_story_title_color'] ) ) {
				$custom_styles .= $selector . '{--tw-cbx-title-color:' . esc_attr( $settings['twae_el_story_title_color'] ) . '}';
				$custom_styles .= $selector . ' .twae-icon{--tw-ibx-color:' . esc_attr( $settings['twae_el_story_title_color'] ) . '}';
				$custom_styles .= $selector . '{--tw-cbx-bd-color:' . esc_attr( $settings['twae_el_story_title_color'] ) . ';--tw-arw-bd-color:' . esc_attr( $settings['twae_el_story_title_color'] ) . ';}';
			}
		}
		$custom_styles .= $selector . ' .twae-year-label{font-size:22px !Important;}';
		$custom_styles .= $selector . ' .twae-year{background:none!important;}';

		$title_key = 'twae_title_typography';
		if ( isset( $settings[ $title_key . $typo_index ] ) &&
		 $settings[ $title_key . $typo_index ] == 'custom' ) {
			$title_styles   = $this->get_typography_settings( $title_key, $settings );
			$custom_styles .= $widgetID . ' .twae-title{' . $title_styles . '}';

		}
		$label_key = 'twae_label_typography';
		if ( isset( $settings[ $label_key . $typo_index ] ) &&
		$settings[ $label_key . $typo_index ] == 'custom' ) {
			$label_styles   = $this->get_typography_settings( $label_key, $settings );
			$custom_styles .= $widgetID . ' .twae-label-big{' . $label_styles . '}';
			if ( isset( $settings[ $label_key . '_font_size' ]['size'] ) ) {
				$custom_styles .= $widgetID . ' .twae-wrapper{--tw-lbl-big-size:' . esc_attr( $settings[ $label_key . '_font_size' ]['size'] ) . esc_attr( $settings[ $label_key . '_font_size' ]['unit'] ) . '}';
			}
		}
		$sub_label_key = 'twae_extra_label_typography';
		if ( isset( $settings[ $sub_label_key . $typo_index ] ) &&
		$settings[ $sub_label_key . $typo_index ] == 'custom' ) {
			$sublabel_styles = $this->get_typography_settings( $sub_label_key, $settings );
			$custom_styles  .= $widgetID . ' .twae-label-small{' . $sublabel_styles . '}';

			if ( isset( $settings[ $sub_label_key . '_font_size' ]['size'] ) ) {
				$custom_styles .= $widgetID . ' .twae-wrapper{--tw-lbl-small-size:' . esc_attr( $settings[ $sub_label_key . '_font_size' ]['size'] ) . esc_attr( $settings[ $sub_label_key . '_font_size' ]['unit'] ) . '}';
			}
		}
		$desc_key = 'twae_description_typography';
		if ( isset( $settings[ $desc_key . $typo_index ] ) &&
		$settings[ $desc_key . $typo_index ] == 'custom' ) {
			$desc_styles    = $this->get_typography_settings( $desc_key, $settings );
			$custom_styles .= $widgetID . ' .twae-description{' . $desc_styles . '}';
		}
		$year_key = 'twae_year_typography';
		if ( isset( $settings[ $year_key . $typo_index ] ) &&
		$settings[ $year_key . $typo_index ] == 'custom' ) {
			$desc_styles    = $this->get_typography_settings( $year_key, $settings );
			$custom_styles .= $widgetID . '.twae-year{' . $desc_styles . '}';
		}
		if ( ! empty( $custom_styles ) ) {
			return $custom_styles;

		} else {
			return false;
		}
	}

	/**
	 * Get typography settings for older version style settings
	 *
	 * @param string $key The key of the typography setting
	 * @param array  $all_settings All settings
	 * @return string The CSS for the typography setting
	 */
	public function get_typography_settings( $key, $all_settings ) {
		$fields    = array(
			'font_family',
			'font_size',
			'font_weight',
			'text_transform',
			'font_style',
			'text_decoration',
			'line_height',
			'letter_spacing',
			'word_spacing',
		);
		$field_css = '';
		foreach ( $fields as $field ) {
			$index     = $key . '_' . $field;
			$attribute = str_replace( '_', '-', $field );
			if ( isset( $all_settings[ $index ] ) && $all_settings[ $index ] !== '' ) {
				if ( is_array( $all_settings[ $index ] ) ) {
					if ( isset( $all_settings[ $index ]['size'] ) && $all_settings[ $index ]['size'] !== '' ) {
						$unit       = isset( $all_settings[ $index ]['unit'] ) ? esc_attr( $all_settings[ $index ]['unit'] ) : 'px';
						$size       = esc_attr( $all_settings[ $index ]['size'] );
						$field_css .= $attribute . ':' . $size . $unit . ';';
					}
				} else {
					$field_css .= $attribute . ':' . esc_attr( $all_settings[ $index ] ) . ';';
				}
			}
		}
		return $field_css;
	}

	/**
	 * Render the widget on frontend
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$data     = isset($settings['twae_list']) ? $settings['twae_list'] : [];
		$layout   = isset($settings['twae_layout']) ? $settings['twae_layout'] : 'default';

		$animation            = isset($settings['twae_animation']) ? $settings['twae_animation'] : 'none';
		$enable_navigation    = isset($settings['twae_navigation_bar']) ? $settings['twae_navigation_bar'] : 'no';
		$compatibility_styles = '';
		$story_styles         = '';
		$timeline_style       = 'style-1'; // Default value

		if ( in_array($layout, ['horizontal', 'horizontal-bottom', 'horizontal-highlighted']) ) {
			$timeline_style = isset($settings['twae_hr_style']) ? sanitize_text_field($settings['twae_hr_style']) : $timeline_style;
		} elseif ( $layout == 'compact' ) {
			$timeline_style = isset($settings['twae_vertical_style']) ? sanitize_text_field($settings['twae_vertical_style']) : $timeline_style;
			$animation      = 'none';
		} else {
			$timeline_style = isset($settings['twae_vertical_style']) ? sanitize_text_field($settings['twae_vertical_style']) : $timeline_style;
		}

		// run code only for old users
		if ( get_option('twae-v') !== false ) {
			global $post;
			if ( isset($post->ID) ) {
				$post_id = $post->ID;
				if ( ! get_post_meta($post_id, 'twae_style_migration', true) ) {
					update_post_meta($post_id, 'twae_exists', 'yes');
					$compatibility_styles .= $this->older_v_compatibility($post_id, $settings, $timeline_style);
				}
			}
		}

		$isRTL = is_rtl();
		$dir   = $isRTL ? 'rtl' : '';

		$enable_popup = ( isset($settings['twae_content_in_popup']) && $settings['twae_content_in_popup'] == 'yes' ) || $timeline_style == 'style-4' ? 'yes' : 'no';

		require TWAE_PRO_PATH . 'widgets/story-timeline/frontend-layouts/twae-story-loop.php';

		switch ($layout) {
			case 'horizontal':
				$timeline_layout_wrapper = 'twae-horizontal-wrapper';
				require TWAE_PRO_PATH . 'widgets/story-timeline/frontend-layouts/twae-horizontal-timeline.php';
				break;
			case 'horizontal-bottom':
				$timeline_layout_wrapper = 'twae-horizontal-bottom';
				require TWAE_PRO_PATH . 'widgets/story-timeline/frontend-layouts/twae-horizontal-timeline.php';
				break;
			case 'horizontal-highlighted':
				$timeline_layout_wrapper = 'twae-horizontal-highlighted-timeline';
				require TWAE_PRO_PATH . 'widgets/story-timeline/frontend-layouts/twae-horizontal-timeline.php';
				break;
			default:
				$timeline_layout_wrapper = 'twae-both-sided';
				if ( $layout == 'one-sided' ) {
					$timeline_layout_wrapper = 'twae-vertical-right';
				} elseif ( $layout == 'left-sided' ) {
					$timeline_layout_wrapper = 'twae-vertical-left';
				}
				require TWAE_PRO_PATH . 'widgets/story-timeline/frontend-layouts/twae-vertical-timeline.php';
		}

		$compatibility_styles .= $story_styles;
		if ( ! empty($compatibility_styles) ) {
			echo '<style type="text/css">' . wp_kses_post($compatibility_styles) . '</style>';
		}
	}

	/**
	 * Render the widget in live editor
	 */
	protected function content_template() {
		?>
	<#
		if( settings.twae_list ) {	
			
			#>
				<?php
				$isRTL = is_rtl();
				$dir   = '';
				if ( $isRTL ) {
					$dir = 'rtl';
				}
				?>
			<#	var enable_navigation = settings.twae_navigation_bar;
			if(settings.twae_layout == 'horizontal'){	
				var timeline_style = settings.twae_hr_style;
				var timeline_layout_wrapper = 'twae-horizontal-wrapper';
				 #>	
				<?php require TWAE_PRO_PATH . 'widgets/story-timeline/editor-layouts/horizontal-template.php'; ?>
		<#  }
			else if(settings.twae_layout == 'horizontal-bottom'){
				var timeline_style = settings.twae_hr_style;
				var timeline_layout_wrapper = 'twae-horizontal-bottom';
				#>
				<?php require TWAE_PRO_PATH . 'widgets/story-timeline/editor-layouts/horizontal-template.php'; ?>
				<#
			}	
			else if(settings.twae_layout == 'horizontal-highlighted'){
				var timeline_style = settings.twae_hr_style;
				var timeline_layout_wrapper = 'twae-horizontal-highlighted-timeline';
				#>
				<?php require TWAE_PRO_PATH . 'widgets/story-timeline/editor-layouts/horizontal-template.php'; ?>
				<#
			}	
		else{
				var timeline_style = settings.twae_vertical_style;
				var animation = 'none';
				var space =	settings.twae_space_between;
				space = space.size;
				#>			
				<?php require TWAE_PRO_PATH . 'widgets/story-timeline/editor-layouts/vertical-template.php'; ?>
			<# }		
		}#>
		<?php

	}


	/* --------------------------- Add Story Repeater --------------------------- */

	/**
	 * This function is used to control the content of the story.
	 * It includes the addition of Timeline Stories Section, Story Repeater, Story Tabs, and Story Tab - Content.
	 * It also controls the visibility of the Story Year / Label.
	 */
	public function content_controls() {
		// Add Timeline Stories Section
		$this->start_controls_section(
			'twae_content_section',
			array(
				'label' => __( 'Timeline Stories', 'twae' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		// Story Repeater
		$repeater = new \Elementor\Repeater();
		// Story Tabs
		$repeater->start_controls_tabs(
			'twae_story_tabs'
		);
		// Story Tab - Content
		$repeater->start_controls_tab(
			'twae_content_tab',
			array(
				'label' => __( 'Content', 'twae' ),
			)
		);
		// Story Year / Label Show/Hide
		$repeater->add_control(
			'twae_show_year_label',
			array(
				'label'        => __( 'Year / Label (Top)', 'twae' ),
				'description'  => __( 'Year not for compact/Horizontal Highlighted layout', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'separator'    => 'before',
				'label_on'     => __( 'Show', 'twae' ),
				'label_off'    => __( 'Hide', 'twae' ),
				'return_value' => 'yes',
				'default'      => 'no',

			)
		);
		// Story Year / Label Text
		$repeater->add_control(
			'twae_year',
			array(
				'label'     => __( 'Year / Label Text', 'twae' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => '2022',
				'condition' => array(
					'twae_show_year_label' => array(
						'yes',
					),
				),
			)
		);
		// Story Label / Date
		$repeater->add_control(
			'twae_date_label',
			array(
				'label'   => __( 'Label / Date', 'twae' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'Jan 2020',
			)
		);
		// Story Sub Label
		$repeater->add_control(
			'twae_extra_label',
			array(
				'label'   => __( 'Sub Label', 'twae' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'Sub Label',
			)
		);
		// Story Title
		$repeater->add_control(
			'twae_story_title',
			array(
				'label'       => __( 'Title', 'twae' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'Add Title Here',
				'label_block' => true,
				'separator'   => 'before',
			)
		);
		// Story Media
		$repeater->add_control(
			'twae_media',
			array(
				'label'     => __( 'Choose Media', 'twae' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'separator' => 'before',
				'options'   => array(
					'image'     => array(
						'title' => __( 'Image', 'twae' ),
						'icon'  => 'fa fa-image',
					),
					'video'     => array(
						'title' => __( 'Video', 'twae' ),
						'icon'  => 'fa fa-video',
					),
					'slideshow' => array(
						'title' => __( 'Slideshow', 'twae' ),
						'icon'  => 'fa fa-images',
					),
				),
				'default'   => 'image',
				'toggle'    => true,
			)
		);
		// Story Media - Slideshow
		$repeater->add_control(
			'twae_slideshow',
			array(
				'label'     => __( 'Add Slideshow Images', 'plugin-domain' ),
				'type'      => \Elementor\Controls_Manager::GALLERY,
				'default'   => array(),
				'condition' => array(
					'twae_media' => array(
						'slideshow',
					),
				),
			)
		);
		// Story Media - Slideshow Autoplay
		$repeater->add_control(
			'twae_slideshow_autoplay',
			array(
				'label'        => __( 'Slideshow Autoplay', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'True', 'twae' ),
				'label_off'    => __( 'False', 'twae' ),
				'return_value' => 'true',
				'default'      => 'true',
				'condition'    => array(
					'twae_media' => array(
						'slideshow',
					),
				),
			)
		);
		// Story Media - Image
		$repeater->add_control(
			'twae_image',
			array(
				'label'       => __( 'Choose Image', 'twae' ),
				'type'        => \Elementor\Controls_Manager::MEDIA,
				'description' => __( 'Image Size will not work with dummy image', 'twae' ),
				'default'     => array(
					'url' => esc_url( \Elementor\Utils::get_placeholder_image_src() ),
				),
				'condition'   => array(
					'twae_media' => array(
						'image',
					),
				),
			)
		);
		// Story Media - Image Size
		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'        => 'twae_thumbnail', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
				'separator'   => 'none',
				'default'     => 'full',
				'description' => __( 'Image Size will not work with default image', 'twae' ),
				'exclude'     => array( 'custom' ),
				'condition'   => array(
					'twae_media' => array(
						'image',
					),
				),
			)
		);
		// Story Media - Video
		$repeater->add_control(
			'twae_video_url',
			array(
				'label'       => __( 'Youtube Video Link', 'twae' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '',
				'condition'   => array(
					'twae_media' => array(
						'video',
					),
				),
			)
		);
		// Story Description
		$repeater->add_control(
			'twae_description',
			array(
				'label'     => __( 'Description', 'twae' ),
				'type'      => \Elementor\Controls_Manager::WYSIWYG,
				'default'   => 'Add Description Here',
				'separator' => 'before',
			)
		);
		// Story Tab - Content - END
		$repeater->end_controls_tab();
		// Story Tab - Advanced
		$repeater->start_controls_tab(
			'twae_advanced_tab',
			array(
				'label' => __( 'Advanced', 'twae' ),
			)
		);
		// Story Custom Icon Display (OLD VERSION)
		$repeater->add_control(
			'twae_display_icon',
			array(
				'type'    => \Elementor\Controls_Manager::HIDDEN,
				'default' => 'yes',
			)
		);
		// Story Icon Type
		$repeater->add_control(
			'twae_icon_type',
			array(
				'label'     => __( 'Icon Type', 'twae' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'separator' => 'before',
				'options'   => array(
					'icon'       => array(
						'title' => __( 'Icon', 'twae' ),
						'icon'  => 'fab fa-font-awesome',
					),
					'customtext' => array(
						'title' => __( 'Text', 'twae' ),
						'icon'  => 'fa fa-list-ol',
					),
					'image'      => array(
						'title' => __( 'Image', 'twae' ),
						'icon'  => 'fa fa-images',
					),
					'dot'        => array(
						'title' => __( 'Dot', 'twae' ),
						'icon'  => 'eicon-circle',
					),
					'none'       => array(
						'title' => __( 'None', 'twae' ),
						'icon'  => 'eicon-ban',
					),
				),
				'toggle'    => true,
			)
		);
		// Story Icon Type FontAwesome
		$repeater->add_control(
			'twae_story_icon',
			array(
				'label'     => __( 'Choose Font Awesome Icon', 'twae' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'far fa-clock',
					'library' => 'solid',
				),
				'condition' => array(
					'twae_icon_type!' => array( 'customtext', 'image', 'dot', 'none' ),
				),
			)
		);
		// Story Icon Type Text
		$repeater->add_control(
			'twae_icon_text',
			array(
				'label'     => __( 'Icon Text', 'twae' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => '1',
				'condition' => array(
					'twae_icon_type' => 'customtext',
				),
			)
		);
		// Story Icon Type Image
		$repeater->add_control(
			'twae_icon_image',
			array(
				'label'     => __( 'Icon Image', 'twae' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => esc_url( \Elementor\Utils::get_placeholder_image_src() ),
				),
				'condition' => array(
					'twae_icon_type' => 'image',
				),
			)
		);
		// Story Read More Show/Hide
		$repeater->add_control(
			'twae_title_link',
			array(
				'label'        => __( 'Read More Button', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'separator'    => 'before',
				'label_on'     => __( 'Show', 'twae' ),
				'label_off'    => __( 'Hide', 'twae' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);
		// Story Read More Text
		$repeater->add_control(
			'twae_button_txt',
			array(
				'label'       => esc_html__( 'Button Text', 'twae' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Read More', 'twae' ),
				'placeholder' => esc_html__( 'Read More', 'twae' ),
				'condition'   => array(
					'twae_title_link' => array(
						'yes',
					),
				),
			)
		);
		// Story Read More Link
		$repeater->add_control(
			'twae_story_link',
			array(
				'label'         => __( 'Button Link', 'twae' ),
				'type'          => \Elementor\Controls_Manager::URL,
				// 'placeholder' => __( 'https://your-link.com', 'twae' ),
				'show_external' => true,
				'default'       => array(
					'url' => '',
					// 'is_external' => true,
					// 'nofollow' => true,
				),
				'condition'     => array(
					'twae_title_link' => array(
						'yes',
					),
				),
			)
		);
		// Story Tab - Advanced - END
		$repeater->end_controls_tab();
		// Story Tab - Styles
		$repeater->start_controls_tab(
			'twae_style_tab',
			array(
				'label' => __( 'Colors', 'twae' ),
			)
		);
		// Story Styles Notice
		$repeater->add_control(
			'twae_notice',
			array(
				'label'     => __( 'Change colors for this single story.', 'twae' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		// Story Box Background
		$repeater->add_control(
			'twae_custom_story_bgcolor',
			array(
				'label'       => __( 'Background Color', 'twae' ),
				'type'        => \Elementor\Controls_Manager::COLOR,
				'separator'   => 'before',
				'render_type' => 'template',
				'selectors'   => array(
					'{{WRAPPER}} .twae-wrapper {{CURRENT_ITEM}}' => '--tw-cbx-bg: {{VALUE}};
					--tw-cbx-bg-gradient: {{VALUE}};
					--tw-arw-bg: {{VALUE}};',
				),
			)
		);
		// Story Box Border Color
		$repeater->add_control(
			'twae_custom_story_bdcolor',
			array(
				'label'     => esc_html__( 'Border Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper {{CURRENT_ITEM}}' => '--tw-cbx-bd-color: {{VALUE}};
					--tw-cbx-bd-color: {{VALUE}};
					--tw-arw-bd-color: {{VALUE}};',
				),
			)
		);
		// Story Title Color
		$repeater->add_control(
			'twae_custom_story_title_color',
			array(
				'label'     => __( 'Title Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper {{CURRENT_ITEM}}' => '--tw-cbx-title-color: {{VALUE}}',
				),
			)
		);
		// Story Title BG Color
		$repeater->add_control(
			'twae_custom_title_icon_bgc',
			array(
				'label'     => __( 'Title Background', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper {{CURRENT_ITEM}}' => '--tw-cbx-title-bg: {{VALUE}};
					--tw-arw-bg: {{VALUE}};',
				),
			)
		);
		// Story Description Color
		$repeater->add_control(
			'twae_custom_description_color',
			array(
				'label'     => __( 'Description Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper {{CURRENT_ITEM}}' => '--tw-cbx-des-color: {{VALUE}}',
				),
			)
		);
		// Story Label Color
		$repeater->add_control(
			'twae_custom_label_color',
			array(
				'label'     => __( 'Label / Date Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper {{CURRENT_ITEM}}' => '--tw-lbl-big-color: {{VALUE}}',
				),
			)
		);
		// Story Sub Label Color
		$repeater->add_control(
			'twae_custom_sublabel_color',
			array(
				'label'     => __( 'Sub Label Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper {{CURRENT_ITEM}}' => '--tw-lbl-small-color: {{VALUE}}',
				),
			)
		);
		// Story Icon Color
		$repeater->add_control(
			'twae_custom_icon_color',
			array(
				'label'     => __( 'Icon Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper {{CURRENT_ITEM}}' => '--tw-ibx-color: {{VALUE}}',
				),
			)
		);
		// Story Icon Background
		$repeater->add_control(
			'twae_custom_icon_bgcolor',
			array(
				'label'     => __( 'Icon Background', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper {{CURRENT_ITEM}}' => '--tw-ibx-bg: {{VALUE}}',
				),
			)
		);
		// Story Year Color
		$repeater->add_control(
			'twae_custom_year_label_color',
			array(
				'label'     => __( 'Year / Label Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper {{CURRENT_ITEM}}' => '--tw-ybx-text-color: {{VALUE}}',
				),
				'condition' => array(
					'twae_show_year_label' => array(
						'yes',
					),
				),
			)
		);

		// Story Year Background
		$repeater->add_control(
			'twae_custom_year_label_bgcolor',
			array(
				'label'     => __( 'Year / Label Background', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper {{CURRENT_ITEM}}' => '--tw-ybx-bg: {{VALUE}}',
				),
				'condition' => array(
					'twae_show_year_label' => array(
						'yes',
					),
				),
			)
		);
		// Story Connector Color
		$repeater->add_control(
			'twae_custom_cbox_connector_bg_color',
			array(
				'label'       => esc_html__( 'Connector / Arrow Color', 'twae' ),
				'type'        => \Elementor\Controls_Manager::COLOR,
				'render_type' => 'template',
				'selectors'   => array(
					'{{WRAPPER}} .twae-wrapper {{CURRENT_ITEM}}' => '--tw-arw-bg: {{VALUE}};--tw-arw-line-background: {{VALUE}};',
				),
			)
		);
		// Story Tab - Styles - END
		$repeater->end_controls_tab();
		// Story Tabs - END
		$repeater->end_controls_tabs();
		// Story Dummy Content
		$this->add_control(
			'twae_list',
			array(
				'label'       => __( 'Content', 'twae' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'twae_story_title' => __( 'Amazon is born', 'twae' ),
						'twae_description' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Erat enim res aperta. Ne discipulum abducam, times. Primum quid tu dicis breve? An haec ab eo non dicuntur?', 'twae' ),
						'twae_year'        => __( '1994', 'twae' ),
						'twae_date_label'  => __( 'July 5', 'twae' ),
						'twae_extra_label' => __( 'Introduced', 'twae' ),
						'twae_image'       => array(
							'url' => esc_url( TWAE_PRO_URL . 'assets/images/amazon1.jpg' ),
							'id'  => '',
						),
						'twae_video_url'   => '',
					),
					array(
						'twae_story_title' => __( 'Amazon Prime debuts', 'twae' ),
						'twae_description' => __( 'Aliter homines, aliter philosophos loqui putas oportere? Sin aliud quid voles, postea. Mihi enim satis est, ipsis non satis. Negat enim summo bono afferre incrementum diem. Quod ea non occurrentia fingunt, vincunt Aristonem.', 'twae' ),
						'twae_year'        => __( '2005', 'twae' ),
						'twae_date_label'  => __( 'February 2', 'twae' ),
						'twae_extra_label' => __( 'Expanded', 'twae' ),
						'twae_image'       => array(
							'url' => esc_url( TWAE_PRO_URL . 'assets/images/amazon2.jpg' ),
							'id'  => '',
						),
						'twae_video_url'   => '',
					),
					array(
						'twae_story_title' => __( 'Amazon acquires Audible', 'twae' ),
						'twae_description' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 'twae' ),
						'twae_year'        => __( '2007', 'twae' ),
						'twae_date_label'  => __( 'January 31', 'twae' ),
						'twae_extra_label' => __( 'Expanded', 'twae' ),
						'twae_image'       => array(
							'url' => esc_url( TWAE_PRO_URL . 'assets/images/amazon3.png' ),
							'id'  => '',
						),
						'twae_video_url'   => '',
					),
				),
				'title_field' => '{{{ twae_story_title }}}',
			)
		);
		// Add Timeline Stories Section - END
		$this->end_controls_section();
	}
	/* ------------------------ Add Story Repeater - END ------------------------ */


	/* ------------------------------ Line Settings ----------------------------- */

	/**
	 * Line settings function.
	 * This function is used to start the controls section for line settings and add responsive control for line width.
	 */
	public function twae_line_settings() {
		// Start the controls section for line settings.
		$this->start_controls_section(
			'twae_line_section',
			array(
				'label' => __( '📍 Line Settings', 'twae' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		// Add responsive control for line width.
		$this->add_responsive_control(
			'twae_line_width',
			array(
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'label'     => esc_html__( 'Line Width', 'twae' ),
				'default'   => array(
					'size' => '4',
					'unit' => 'px',
				),
				'range'     => array(
					'px' => array(
						'min'  => 2,
						'max'  => 24,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-line-width: {{SIZE}}px',
				),
			)
		);
		// Line Color
		$this->add_control(
			'twae_line_color',
			array(
				'label'     => __( 'Line Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}} .twae-navigationBar' => '--tw-line-bg: {{VALUE}}',
				),
			)
		);
		// Line Filling Show/Hide
		$this->add_control(
			'center_line_filling',
			array(
				'label'        => esc_html__( 'Line Filling (On Scroll)', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'render_type'  => 'template',
				'label_on'     => esc_html__( 'Show', 'twae' ),
				'label_off'    => esc_html__( 'Hide', 'twae' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);
		// Line Filling Color
		$this->add_control(
			'center_line_filling_color',
			array(
				'label'     => esc_html__( 'Line Filling Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-line-filling-color: {{VALUE}}',
				),
				'condition' => array(
					'center_line_filling' => 'yes',
				),
			)
		);
		// Line Border Show/Hide
		$this->add_control(
			'twae_line_border',
			array(
				'label'        => esc_html__( 'Line Border', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'twae' ),
				'label_off'    => esc_html__( 'Hide', 'twae' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);
		// Line Border Style
		$this->add_control(
			'twae_line_border_style',
			array(
				'label'     => esc_html__( 'Line Border Style', 'twea' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => array(
					'solid'  => esc_html__( 'Solid', 'twea' ),
					'dashed' => esc_html__( 'Dashed', 'twea' ),
					'dotted' => esc_html__( 'Dotted', 'twea' ),
					'double' => esc_html__( 'Double', 'twea' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-line-bd-style: {{VALUE}}',
				),
				'condition' => array(
					'twae_line_border' => 'yes',
				),
			)
		);
		// Line Border Width
		$this->add_responsive_control(
			'twae_line_border_width',
			array(
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'label'     => esc_html__( 'Line Border Width', 'twae' ),
				'default'   => array(
					'size' => '1',
					'unit' => 'px',
				),
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 6,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-line-bd-width: {{SIZE}};--tw-line-bd-width-inpx: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'twae_line_border' => 'yes',
				),
			)
		);
		// Line Border Color
		$this->add_control(
			'twae_line_border_color',
			array(
				'label'     => esc_html__( 'Line Border Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#222222',
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-line-bd-color: {{VALUE}}',
				),
				'condition' => array(
					'twae_line_border' => 'yes',
				),
			)
		);
		// Line Section End
		$this->end_controls_section();
	}
	/* --------------------------- Line Settings - END -------------------------- */


	/* ---------------------------- Icon Box Settings --------------------------- */
	function story_icon_style_settings() {
		// Icon Box Section
		$this->start_controls_section(
			'twae_icon_section',
			array(
				'label' => __( '🔵 Icon Box / Dot', 'twae' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		// Icon Box BG Color
		$this->add_control(
			'twae_icon_bgcolor',
			array(
				'label'     => __( 'Icon / Dot Background', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-ibx-bg: {{VALUE}}',
				),
			)
		);
		// Icon Box Color
		$this->add_control(
			'twae_icon_color',
			array(
				'label'     => __( 'Icon / Text Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-ibx-color: {{VALUE}}',
				),
			)
		);
		// Icon Box Border Pop Over
		$this->add_control(
			'twae_icon_border_popover',
			array(
				'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label'        => esc_html__( 'Border', 'twae' ),
				'label_off'    => esc_html__( 'Default', 'twae' ),
				'label_on'     => esc_html__( 'Custom', 'twae' ),
				'return_value' => 'yes',
			)
		);
		$this->start_popover();
		// Icon Box Border Type
		$this->add_control(
			'twae_icon_border_type',
			array(
				'label'     => esc_html__( 'Border Type', 'twea' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'separator' => 'before',
				'default'   => 'solid',
				'options'   => array(
					'none'   => esc_html__( 'None', 'twea' ),
					'solid'  => esc_html__( 'Solid', 'twea' ),
					'dashed' => esc_html__( 'Dashed', 'twea' ),
					'dotted' => esc_html__( 'Dotted', 'twea' ),
					'double' => esc_html__( 'Double', 'twea' ),
					'groove' => esc_html__( 'Groove', 'twea' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-ibx-bd-style: {{VALUE}}',
				),
				'condition' => array(
					'twae_icon_border_popover' => 'yes',
				),
			)
		);
		// Icon Box Border Color
		$this->add_control(
			'twae_icon_border_color',
			array(
				'label'     => __( 'Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-ibx-bd-color: {{VALUE}}',
				),
				'condition' => array(
					'twae_icon_border_popover' => 'yes',
				),
			)
		);
		// Icon Box Border Width
		$this->add_control(
			'twae_icon_border_width',
			array(
				'label'     => __( 'Border Width', 'twae' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-ibx-bd-width: {{SIZE}}px',
				),
				'condition' => array(
					'twae_icon_border_type!'   => 'none',
					'twae_icon_border_popover' => 'yes',
				),
			)
		);
		// Icon Box Border Pop Over - END
		$this->end_popover();
		// Icon Box Size
		$this->add_responsive_control(
			'twae_icon_boxsize',
			array(
				'type'           => \Elementor\Controls_Manager::SLIDER,
				'label'          => esc_html__( 'Icon Box Size', 'twae' ),
				'range'          => array(
					'px' => array(
						'min' => 32,
						'max' => 78,
					),
				),
				'devices'        => array( 'desktop', 'tablet', 'mobile' ),
				'default'        => array(
					'size' => 48,
					'unit' => 'px',
				),
				'tablet_default' => array(
					'size' => 38,
					'unit' => 'px',
				),
				'mobile_default' => array(
					'size' => 32,
					'unit' => 'px',
				),
				'selectors'      => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-ibx-size: {{SIZE}}{{UNIT}};',
				),
			)
		);
		// Icon Box Font Size
		$this->add_responsive_control(
			'twae_icon_size',
			array(
				'type'           => \Elementor\Controls_Manager::SLIDER,
				'label'          => esc_html__( 'Icon Text Size', 'twae' ),
				'range'          => array(
					'px' => array(
						'min' => 12,
						'max' => 52,
					),
				),
				'devices'        => array( 'desktop', 'tablet', 'mobile' ),
				'default'        => array(
					'size' => 22,
					'unit' => 'px',
				),
				'tablet_default' => array(
					'size' => 18,
					'unit' => 'px',
				),
				'mobile_default' => array(
					'size' => 14,
					'unit' => 'px',
				),
				'selectors'      => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-ibx-text-size: {{SIZE}}{{UNIT}}',
				),
			)
		);
		// Icon Box Radius
		$this->add_control(
			'twae_icon_radius',
			array(
				'label'     => __( 'Icon Box Radius', 'twae' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-ibx-radius: {{SIZE}}',
				),
			)
		);
		// Icon Box Section - END
		$this->end_controls_section();
	}
	/* ------------------------- Icon Box Settings - END ------------------------ */


	/* ---------------------- Year / Labels / Date Settings --------------------- */

	/**
	 * Story Year / Labels / Date Settings
	 * This function is used to start the controls section for Year / Labels / Date settings and add control for Labels.
	 */
	public function story_yld_settings() {
		$this->start_controls_section(
			'twae_yld_section',
			array(
				'label' => __( '📢 Labels / Date / Year Box', 'twae' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		// Adding control for Labels
		$this->add_control(
			'twae_yld_labels',
			array(
				'label' => __( '🔶 Label / Sub Label / Date', 'twae' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);
		// Labels Gap
		$this->add_responsive_control(
			'twae_yld_labels_gap',
			array(
				'type'           => \Elementor\Controls_Manager::SLIDER,
				'label'          => esc_html__( 'Gap b/w Label & Sub Label', 'twae' ),
				'separator'      => 'before',
				'range'          => array(
					'px' => array(
						'min' => 0,
						'max' => 48,
					),
				),
				'devices'        => array( 'desktop', 'tablet', 'mobile' ),
				'default'        => array(
					'size' => 6,
					'unit' => 'px',
				),
				'tablet_default' => array(
					'size' => 4,
					'unit' => 'px',
				),
				'mobile_default' => array(
					'size' => 2,
					'unit' => 'px',
				),
				'selectors'      => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-lbl-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);
		// Primary Label Color
		$this->add_control(
			'twae_date_label_color',
			array(
				'label'     => __( 'Label Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-lbl-big-color: {{VALUE}}',
				),
			)
		);
		// Primary Label Typo
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'twae_label_typography',
				'label'    => __( 'Label Typography', 'twae' ),
				'selector' => '{{WRAPPER}} .twae-label-big,{{WRAPPER}} .twae_icon_text',
				'exclude'  => array( 'line_height', 'font_size', 'letter_spacing', 'word_spacing' ),
			)
		);
		// Primary Label Size
		$this->add_responsive_control(
			'twae_yld_label_size',
			array(
				'type'           => \Elementor\Controls_Manager::SLIDER,
				'label'          => esc_html__( 'Label Size', 'twae' ),
				'range'          => array(
					'px' => array(
						'min' => 8,
						'max' => 64,
					),
				),
				'devices'        => array( 'desktop', 'tablet', 'mobile' ),
				'default'        => array(
					'size' => 22,
					'unit' => 'px',
				),
				'tablet_default' => array(
					'size' => 20,
					'unit' => 'px',
				),
				'mobile_default' => array(
					'size' => 18,
					'unit' => 'px',
				),
				'selectors'      => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-lbl-big-size: {{SIZE}}{{UNIT}};',
				),
			)
		);
		// Sub Label Color
		$this->add_control(
			'twae_extra_label_color',
			array(
				'label'     => __( 'Sub Label Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-lbl-small-color: {{VALUE}}',
				),
			)
		);
		// Sub Label Typo
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'twae_extra_label_typography',
				'label'    => __( 'Sub Label Typography', 'twae' ),
				'selector' => '{{WRAPPER}} .twae-label-small',
				'exclude'  => array( 'line_height', 'font_size', 'letter_spacing', 'word_spacing' ),
			)
		);
		// Sub Label Size
		$this->add_responsive_control(
			'twae_yld_sublabel_size',
			array(
				'type'           => \Elementor\Controls_Manager::SLIDER,
				'label'          => esc_html__( 'Sub Label Size', 'twae' ),
				'range'          => array(
					'px' => array(
						'min' => 8,
						'max' => 64,
					),
				),
				'devices'        => array( 'desktop', 'tablet', 'mobile' ),
				'default'        => array(
					'size' => 16,
					'unit' => 'px',
				),
				'tablet_default' => array(
					'size' => 14,
					'unit' => 'px',
				),
				'mobile_default' => array(
					'size' => 14,
					'unit' => 'px',
				),
				'selectors'      => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-lbl-small-size: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'twae_label_content_top',
			array(
				'label'        => __( 'Label Top Of The Content', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_bg_on'  => __( 'On', 'twae' ),
				'label_bg_off' => __( 'Off', 'twae' ),
				'return_value' => 'twae-label-content-top',
				'default'      => 'no',
				'condition'    => array(
					'twae_label_inside!' => 'twae-label-content-inside',
					'twae_layout!'       => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);
		$this->add_control(
			'twae_label_inside',
			array(
				'label'        => __( 'Label Inside The Content', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_bg_on'  => __( 'On', 'twae' ),
				'label_bg_off' => __( 'Off', 'twae' ),
				'return_value' => 'twae-label-content-inside',
				'default'      => 'no',
				'conditions'   => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'twae_label_content_top',
							'operator' => '!=',
							'value'    => 'twae-label-content-top',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'twae_layout',
									'operator' => '!=',
									'value'    => 'horizontal-highlighted',
								),
							),
						),
					),
				),
			)
		);
		$this->add_control(
			'twae_label_background',
			array(
				'label'        => __( 'Label Background', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_bg_on'  => __( 'Show', 'twae' ),
				'label_bg_off' => __( 'Hide', 'twae' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);
		$this->add_control(
			'twae_label_connector_style',
			array(
				'label'     => esc_html__( 'Connector Style', 'twae' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'default'         => array(
						'title' => esc_html__( 'Arrow', 'twae' ),
						'icon'  => 'eicon-chevron-left',
					),
					'twae-arrow-line' => array(
						'title' => esc_html__( 'Line', 'twae' ),
						'icon'  => 'eicon-h-align-left',
					),
					'twae-arrow-none' => array(
						'title' => esc_html__( 'None', 'twae' ),
						'icon'  => 'eicon-ban',
					),
				),
				'default'   => 'default',
				'toggle'    => true,
				'condition' => array(
					'twae_label_background'   => 'yes',
					'twae_label_inside!'      => 'twae-label-content-inside',
					'twae_label_content_top!' => 'twae-label-content-top',
				),
			)
		);
		$this->add_control(
			'twae_label_bg_color',
			array(
				'label'     => __( 'Label Background Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--lbl-bk-color: {{VALUE}};',
				),
				'condition' => array(
					'twae_label_background' => 'yes',
				),
			)
		);
		$this->add_control(
			'twae_label_bd',
			array(
				'label'        => __( 'Label Border', 'twae' ),
				'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => esc_html__( 'Default', 'twae' ),
				'label_on'     => esc_html__( 'Custom', 'twae' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => array(
					'twae_label_background' => 'yes',
				),
			)
		);
		$this->start_popover();

		$this->add_control(
			'label-bd-type',
			array(
				'label'     => __( 'Border Type', 'twae' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => array(
					'solid'  => esc_html__( 'Solid', 'plugin-name' ),
					'dashed' => esc_html__( 'Dashed', 'plugin-name' ),
					'dotted' => esc_html__( 'Dotted', 'plugin-name' ),
					'double' => esc_html__( 'Double', 'plugin-name' ),
					'none'   => esc_html__( 'None', 'plugin-name' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--lbl-bd-style: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'label-bd-width',
			array(
				'label'     => __( 'Border Width', 'twae' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'defalut'   => array(
					'size' => '1',
					'unit' => 'px',
				),
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 20,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--lbl-bd-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'label-bd-color',
			array(
				'label'     => __( 'Border Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--lbl-bd-color: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'twae_lablel_bd_radius',
			array(
				'label'      => __( 'Border Radius', 'twae' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'default'    => array(
					'top'      => '5',
					'right'    => '5',
					'bottom'   => '5',
					'left'     => '5',
					'unit'     => 'px',
					'isLinked' => 'false',
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .twae-wrapper' =>
					'--lbl-bd-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_popover();
		// Year Box
		$this->add_control(
			'twae_year_label_section',
			array(
				'label'       => __( '🔶 Year/Label (On Line)', 'twae' ),
				'description' => __( 'Year not for compact layout', 'twea' ),
				'type'        => \Elementor\Controls_Manager::HEADING,
				'separator'   => 'before',
				'condition'   => array(
					'twae_layout!' => array( 'horizontal-highlighted', 'compact' ),
				),
			)
		);
		// Year Box Font Color
		$this->add_control(
			'twae_year_label_color',
			array(
				'label'     => __( 'Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-ybx-text-color: {{VALUE}}',
				),
				'condition' => array(
					'twae_layout!' => array( 'horizontal-highlighted', 'compact' ),
				),

			)
		);
		// Year Box BG Color
		$this->add_control(
			'twae_year_label_bgcolor',
			array(
				'label'     => __( 'Background Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-ybx-bg: {{VALUE}}',
				),
				'condition' => array(
					'twae_layout!' => array( 'horizontal-highlighted', 'compact' ),
				),
			)
		);
		// Year Box Typo
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'twae_year_typography',
				'label'     => __( 'Typography', 'twae' ),
				'exclude'   => array( 'line_height' ),
				'selector'  => '{{WRAPPER}} .twae-year-text',
				'condition' => array(
					'twae_layout!' => array( 'horizontal-highlighted', 'compact' ),
				),
			)
		);
		// Year Box Border Pop Over
		$this->add_control(
			'twae_yld_border_popover',
			array(
				'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label'        => esc_html__( 'Border', 'twae' ),
				'label_off'    => esc_html__( 'Default', 'twae' ),
				'label_on'     => esc_html__( 'Custom', 'twae' ),
				'return_value' => 'yes',
				'condition'    => array(
					'twae_layout!' => array( 'horizontal-highlighted', 'compact' ),
				),
			)
		);
		$this->start_popover();
		// Year Box Border Type
		$this->add_control(
			'twae_yld_border_type',
			array(
				'label'     => esc_html__( 'Border Type', 'twea' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'separator' => 'before',
				'default'   => 'solid',
				'options'   => array(
					'none'   => esc_html__( 'None', 'twea' ),
					'solid'  => esc_html__( 'Solid', 'twea' ),
					'dashed' => esc_html__( 'Dashed', 'twea' ),
					'dotted' => esc_html__( 'Dotted', 'twea' ),
					'double' => esc_html__( 'Double', 'twea' ),
					'groove' => esc_html__( 'Groove', 'twea' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-ybx-bd-style: {{VALUE}}',
				),
				'condition' => array(
					'twae_yld_border_popover' => 'yes',
					'twae_layout!'            => array( 'horizontal-highlighted', 'compact' ),
				),
			)
		);
		// Year Box Border Color
		$this->add_control(
			'twae_yld_border_color',
			array(
				'label'     => __( 'Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-ybx-bd-color: {{VALUE}}',
				),
				'condition' => array(
					'twae_yld_border_popover' => 'yes',
					'twae_layout!'            => array( 'horizontal-highlighted', 'compact' ),
				),
			)
		);
		// Year Box Border Width
		$this->add_control(
			'twae_yld_border_width',
			array(
				'label'     => __( 'Border Width', 'twae' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-ybx-bd-width: {{SIZE}}px',
				),
				'condition' => array(
					'twae_yld_border_type!'   => 'none',
					'twae_yld_border_popover' => 'yes',
					'twae_layout!'            => array( 'horizontal-highlighted', 'compact' ),
				),
			)
		);
		$this->end_popover();
		// Year Box Size
		$this->add_responsive_control(
			'twae_year_size',
			array(
				'type'           => \Elementor\Controls_Manager::SLIDER,
				'label'          => esc_html__( 'Year Box Size', 'twae' ),
				'range'          => array(
					'px' => array(
						'min' => 36,
						'max' => 128,
					),
				),
				'devices'        => array( 'desktop', 'tablet', 'mobile' ),
				'default'        => array(
					'size' => 90,
					'unit' => 'px',
				),
				'tablet_default' => array(
					'size' => 66,
					'unit' => 'px',
				),
				'mobile_default' => array(
					'size' => 58,
					'unit' => 'px',
				),
				'selectors'      => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-ybx-size: {{SIZE}}{{UNIT}};',
				),
				'condition'      => array(
					'twae_layout!' => array( 'horizontal-highlighted', 'compact' ),
				),
			)
		);
		// Year / Labels / Date Section - END

		// Year navigation color settings
		$this->add_control(
			'twae_year_popup_label_section',
			array(
				'label'     => __( '🔶 Year Navigation', 'twae' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'twae_navigation_bar' => 'yes',
				),
			)
		);

		// Year Box Font Color
		$this->add_control(
			'twae_year_popup_label_color',
			array(
				'label'     => __( 'Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .twae-navigationBar,
					{{WRAPPER}} .twae-horizontal-navigationBar,
					{{WRAPPER}} .twae-hor-nav-wrapper .twae-nav-next i, 
					{{WRAPPER}} .twae-hor-nav-wrapper .twae-nav-prev i' => '--tw-ybx-text-color: {{VALUE}}',
				),
				'condition' => array(
					'twae_navigation_bar' => 'yes',
				),
			)
		);

		// Year Box BG Color
		$this->add_control(
			'twae_year_popup_label_bgcolor',
			array(
				'label'     => __( 'Background Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-navigationBar' => '--tw-ybx-bg: {{VALUE}}',
				),
				'condition' => array(
					'twae_navigation_bar' => 'yes',
					'twae_layout!'        => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);

		// Active color
		$this->add_control(
			'twae_year_popup_label_active color',
			array(
				'label'     => __( 'Active Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-navigationBar' => '--tw-ibx-bg: {{VALUE}}',
					'{{WRAPPER}} nav.twae-navigationBar.style-2 ul.twae-navigation-items li.current:after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} nav.twae-navigationBar.style-2 ul.twae-navigation-items li a.current:before' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .twae-navigationBar .current,
					 {{WRAPPER}} .twae-horizontal-navigationBar' => '--tw-ibx-color: {{VALUE}}',
				),
				'condition' => array(
					'twae_navigation_bar' => 'yes',
				),
			)
		);

		// Year text Font Color for style 3
		$this->add_control(
			'twae_text_popup_label_color',
			array(
				'label'     => __( 'Active Text Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .twae-navigationBar.style-3 .current' => '--tw-ibx-color: {{VALUE}}',
				),
				'condition' => array(
					'twae_navigation_bar'   => 'yes',
					'twae_navigation_style' => 'style-3',
					'twae_layout!'          => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);
		$this->end_controls_section();

			/** ------------------- Year navigation color settings end. -------------------*/

	}
	/* ------------------- Year / Labels / Date Settings - END ------------------ */


	/* --------------------------- Content Box Settings --------------------------- */

	/**
	 * Content Box Settings
	 * This function is used to start the controls section for content box settings and add control for padding.
	 */
	public function twae_cbox_settings() {
		// Start the controls section for content box settings.
		$this->start_controls_section(
			'twae_cbox_section',
			array(
				'label' => __( '🔳 Content Background / Border', 'twae' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		// Add control for content box padding.
		$this->add_control(
			'twae_cbox_padding',
			array(
				'label'      => __( 'Padding', 'twae' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .twae-wrapper' =>
					'--tw-cbx-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .twae-wrapper .twae-label-content-top' => '--tw-label-inside-margin: {{LEFT}}{{UNIT}}',
				),
			)
		);
		// Content Box Bottom Margin
		$this->add_responsive_control(
			'twae_space_between',
			array(
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'label'       => esc_html__( 'Bottom Spacing', 'twae' ),
				'default'     => array(
					'size' => '60',
					'unit' => 'px',
				),
				'range'       => array(
					'px' => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					),
				),
				'render_type' => 'template',
				'selectors'   => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-cbx-bottom-margin: {{SIZE}}{{UNIT}}',
				),
				'condition'   => array(
					'twae_layout!' => array( 'horizontal-bottom' ),
				),
			)
		);
		// Content Box Background
		$this->add_control(
			'twae_cbox_background',
			array(
				'label'     => __( '🔶 Content Box Background', 'twae' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		// Content Box Background Tabs
		$this->start_controls_tabs(
			'twae_cbox_background_tabs',
			array(
				'separator' => 'before',
			)
		);
		// Content Box Background Normal Tab
		$this->start_controls_tab(
			'twae_cbox_background_normal',
			array(
				'label' => esc_html__( 'Normal', 'twae' ),
			)
		);
		// Content Box Background Type Normal
		$this->add_control(
			'twae_cbox_background_type',
			array(
				'label'   => esc_html__( 'Background Type', 'twae' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'default' => 'simple',
				'options' => array(
					'simple'     => array(
						'title' => esc_html__( 'Simple', 'twae' ),
						'icon'  => 'eicon-paint-brush',
					),
					'gradient'   => array(
						'title' => esc_html__( 'Gradient', 'twae' ),
						'icon'  => 'eicon-barcode',
					),
					'multicolor' => array(
						'title' => esc_html__( 'Multi Color', 'twae' ),
						'icon'  => 'eicon-plus-square',
					),
				),
				'toggle'  => true,
			)
		);
		// Content Box Background Color1 Normal
		$this->add_control(
			'twae_story_bgcolor',
			array(
				'label'     => esc_html__( 'Background Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-cbx-bg: {{VALUE}}',
				),
				'condition' => array(
					'twae_cbox_background_type' => array( 'simple', 'gradient', 'multicolor' ),
				),
			)
		);
		// Content Box Background Gradient Color2 Normal
		$this->add_control(
			'twae_cbox_background_color_gradient',
			array(
				'label'     => esc_html__( 'Gradient Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-cbx-bg-gradient: {{VALUE}}',
				),
				'condition' => array(
					'twae_cbox_background_type' => 'gradient',
				),
			)
		);
		// Content Box Background Multi Color2 Normal
		$this->add_control(
			'twae_cbox_background_color2',
			array(
				'label'     => esc_html__( 'Second Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-cbx-bg2: {{VALUE}}',
				),
				'condition' => array(
					'twae_cbox_background_type' => 'multicolor',
				),
			)
		);
		// Content Box Background Multi Color3 Normal
		$this->add_control(
			'twae_cbox_background_color3',
			array(
				'label'     => esc_html__( 'Third Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-cbx-bg3: {{VALUE}}',
				),
				'condition' => array(
					'twae_cbox_background_type' => 'multicolor',
				),
			)
		);
		// Content Box Background Multi Color4 Normal
		$this->add_control(
			'twae_cbox_background_color4',
			array(
				'label'     => esc_html__( 'Fourth Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-cbx-bg4: {{VALUE}}',
				),
				'condition' => array(
					'twae_cbox_background_type' => 'multicolor',
				),
			)
		);
		$this->end_controls_tab();
		// Content Box Background Hover Tab
		$this->start_controls_tab(
			'twae_cbox_background_hover',
			array(
				'label' => esc_html__( 'Hover', 'twae' ),
			)
		);
		// Content Box Background Type Hover
		$this->add_control(
			'twae_cbox_background_type_hover',
			array(
				'label'   => esc_html__( 'Background Type', 'twae' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => array(
					'simple' => array(
						'title' => esc_html__( 'Simple', 'twae' ),
						'icon'  => 'eicon-paint-brush',
					),
					/*
					'gradient' => [
						'title' => esc_html__( 'Gradient', 'twae' ),
						'icon' => 'eicon-barcode',
					],
					'multicolor' => [
						'title' => esc_html__( 'Multi Color', 'twae' ),
						'icon' => 'eicon-plus-square',
					],*/
				),
				'toggle'  => true,
			)
		);
		// Content Box Background Color1 Hover
		$this->add_control(
			'twae_story_bgcolor_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#FFE3DC',
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-cbx-bg-hover: {{VALUE}}',
				),
				'condition' => array(
					'twae_cbox_background_type_hover' => array( 'simple' ),
				),
			)
		);
		$this->end_controls_tab();
		// Content Box Background Tabs END
		$this->end_controls_tabs();
		// Content Box Border
		$this->add_control(
			'twae_cbox_border',
			array(
				'label'     => __( '🔶 Content Box Border', 'twae' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		// Content Box Border Tabs
		$this->start_controls_tabs(
			'twae_cbox_border_tabs',
			array(
				'separator' => 'before',
			)
		);
		// Content Box Border Normal Tab
		$this->start_controls_tab(
			'twae_cbox_border_normal',
			array(
				'label' => esc_html__( 'Normal', 'twae' ),
			)
		);
		// Content Box Border Type
		$this->add_control(
			'twae_cbox_border_type',
			array(
				'label'     => esc_html__( 'Border Type', 'twea' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => array(
					'none'   => esc_html__( 'None', 'twea' ),
					'solid'  => esc_html__( 'Solid', 'twea' ),
					'dashed' => esc_html__( 'Dashed', 'twea' ),
					'dotted' => esc_html__( 'Dotted', 'twea' ),
					'double' => esc_html__( 'Double', 'twea' ),
					'groove' => esc_html__( 'Groove', 'twea' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-cbx-bd-style: {{VALUE}}',
				),
			)
		);
		// Content Box Border Width
		$this->add_control(
			'twae_cbox_border_width',
			array(
				'label'      => __( 'Width', 'twae' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .twae-wrapper' =>
					'--tw-cbx-bd-top-width: {{TOP}}{{UNIT}};
					--tw-cbx-bd-right-width: {{RIGHT}}{{UNIT}};
					--tw-cbx-bd-bottom-width: {{BOTTOM}}{{UNIT}};
					--tw-cbx-bd-left-width: {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'twae_cbox_border_type!' => 'none',
				),
			)
		);
		// Content Box Border Color
		$this->add_control(
			'twae_cbox_border_color',
			array(
				'label'     => esc_html__( 'Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-cbx-bd-color: {{VALUE}} !Important',
				),
				'condition' => array(
					'twae_cbox_border_type!' => 'none',
				),
			)
		);
		// Content Box Border Radius
		$this->add_control(
			'twae_cbox_border_radius',
			array(
				'label'      => __( 'Border Radius', 'twae' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .twae-wrapper' =>
					'--tw-cbx-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};
					--tw-cbx-radius-left: {{RIGHT}}{{UNIT}} {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}}',
				),
			)
		);
		// Content Box Border Shadow Pop Over
		$this->add_control(
			'twae_cbox_border_shadow_popover',
			array(
				'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label'        => esc_html__( 'Box Shadow', 'twae' ),
				'label_off'    => esc_html__( 'Default', 'twae' ),
				'label_on'     => esc_html__( 'Custom', 'twae' ),
				'return_value' => 'yes',
			)
		);
		$this->start_popover();
		// Content Box Border Shadow
		$this->add_control(
			'twae_cbox_border_shadow',
			array(
				'label'     => esc_html__( 'Box Shadow', 'twae' ),
				'type'      => \Elementor\Controls_Manager::BOX_SHADOW,
				'default'   => array(
					'horizontal' => 0,
					'vertical'   => 2,
					'blur'       => 8,
					'spread'     => -2,
					'color'      => 'rgba(0,0,0,0.3)',
				),
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-cbx-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}};
					--tw-cbx-shadow-left: -{{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}};',
				),
				'condition' => array(
					'twae_cbox_border_shadow_popover' => 'yes',
				),
			)
		);
		$this->end_popover();
		$this->end_controls_tab();
		// Content Box Border Hover Tab
		$this->start_controls_tab(
			'twae_cbox_border_hover',
			array(
				'label' => esc_html__( 'Hover', 'twae' ),
			)
		);
		// Content Box Border Radius Hover
		$this->add_control(
			'twae_cbox_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'twae' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .twae-wrapper' =>
					'--tw-cbx-radius-hover: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};
					--tw-cbx-radius-left-hover: {{RIGHT}}{{UNIT}} {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}};',
				),
			)
		);
		// Content Box Border Shadow Pop Over Hover
		$this->add_control(
			'twae_cbox_border_shadow_popover_hover',
			array(
				'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label'        => esc_html__( 'Box Shadow', 'twae' ),
				'label_off'    => esc_html__( 'Default', 'twae' ),
				'label_on'     => esc_html__( 'Custom', 'twae' ),
				'return_value' => 'yes',
			)
		);
		$this->start_popover();
		// Content Box Border Shadow Hover
		$this->add_control(
			'twae_cbox_border_shadow_hover',
			array(
				'label'     => esc_html__( 'Box Shadow', 'twae' ),
				'type'      => \Elementor\Controls_Manager::BOX_SHADOW,
				'default'   => array(
					'horizontal' => 0,
					'vertical'   => 2,
					'blur'       => 8,
					'spread'     => -2,
					'color'      => 'rgba(0,0,0,0.3)',
				),
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-cbx-shadow-hover: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}};
					--tw-cbx-shadow-left-hover: -{{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}};',
				),
				'condition' => array(
					'twae_cbox_border_shadow_popover_hover' => 'yes',
				),
			)
		);
		$this->end_popover();
		$this->end_controls_tab();
		// Content Box Border Tabs END
		$this->end_controls_tabs();
		// Content Box Connector
		$this->add_control(
			'twae_cbox_connector',
			array(
				'label'     => __( '🔶 Content Box Connector', 'twae' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);
		// Content Box Connector Border Color
		$this->add_control(
			'twae_cbox_connector_bd_color',
			array(
				'label'     => esc_html__( 'Border Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-arw-bd-color: {{VALUE}};--tw-arw-line-border-color: {{VALUE}}',
				),
			)
		);
		// Content Box Connector Color
		$this->add_control(
			'twae_cbox_connector_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-arw-bg: {{VALUE}};--tw-arw-line-background: {{VALUE}};',
				),
				'condition' => array(
					'twae_cbox_background_type!' => 'multicolor',
				),
			)
		);
		// Content Box Section End
		$this->end_controls_section();
	}
	/* ------------------------ Content Box Settings - END ------------------------ */


	/* ---------------- Content Settings - Title/Img/Desc/Button ---------------- */

	/**
	 * Story content settings.
	 */
	public function twae_storycontent_settings() {
		// Title Section Start
		$this->start_controls_section(
			'twae_title_section',
			array(
				'label' => __( '✍ Title', 'twae' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		// Title Color For Elegent Style (OLD VERSION)
		$this->add_control(
			'twae_el_story_title_color',
			array(
				'type'    => \Elementor\Controls_Manager::HIDDEN,
				'default' => '#ffffff',
			)
		);
		// Title Color
		$this->add_control(
			'twae_story_title_color',
			array(
				'label'     => __( 'Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-cbx-title-color: {{VALUE}}',
				),
			)
		);
		// Title Background
		$this->add_control(
			'twae_story_title_bgcolor',
			array(
				'label'     => __( 'Background Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-cbx-title-bg: {{VALUE}}',
				),
			)
		);
		// Title Typography
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'               => 'twae_title_typography',
				'label'              => __( 'Typography', 'twae' ),
				'selector'           => '{{WRAPPER}} .twae-title',
				'frontend_available' => true,
				// 'exclude'            => array( 'line_height' ),
			)
		);
		// Title Padding
		$this->add_control(
			'twae_story_title_padding',
			array(
				'label'      => __( 'Padding', 'twae' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .twae-wrapper' =>
					'--tw-cbx-title-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		// Title Bottom Margin
		$this->add_responsive_control(
			'twae_story_title_margin',
			array(
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'label'     => esc_html__( 'Bottom Spacing', 'twae' ),
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-cbx-title-margin: 0 0 {{SIZE}}{{UNIT}} 0',
				),
			)
		);
		// Title Section End
		$this->end_controls_section();
		// Description Section Start
		$this->start_controls_section(
			'twae_description_section',
			array(
				'label' => __( '📝 Description', 'twae' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		// Description Color
		$this->add_control(
			'twae_description_color',
			array(
				'label'     => __( 'Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-cbx-des-color: {{VALUE}}',
				),
			)
		);
		// Description Background
		$this->add_control(
			'twae_story_description_bgcolor',
			array(
				'label'     => __( 'Background Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper,
					{{WRAPPER}} .twae-vertical.style-2' => '--tw-cbx-des-background: {{VALUE}}',
					'{{WRAPPER}} .twae-horizontal .twae-title',

				),
			)
		);
		// Description Typo
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'twae_description_typography',
				'label'    => __( 'Typography', 'twae' ),
				'selector' => '{{WRAPPER}} .twae-description, {{WRAPPER}} .twae-button a',
				// 'exclude'  => array( 'line_height' ),
			)
		);
		// Description Padding
		$this->add_control(
			'twae_story_description_padding',
			array(
				'label'      => __( 'Padding', 'twae' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .twae-wrapper' =>
					'--tw-cbx-des-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		// Description Bottom Margin
		$this->add_responsive_control(
			'space_between_story_desc',
			array(
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'label'     => esc_html__( 'Bottom Spacing', 'twae' ),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'devices'   => array( 'desktop', 'tablet', 'mobile' ),
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-cbx-des-margin: 0 0 {{SIZE}}{{UNIT}} 0',
				),
			)
		);
		// Description Section End
		$this->end_controls_section();
		// Image Section Start
		$this->start_controls_section(
			'twae_image_section',
			array(
				'label' => __( '📺 Image / Media', 'twae' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		// Image Width
		$this->add_responsive_control(
			'twae_image_width',
			array(
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'label'      => esc_html__( 'Image Width', 'twae' ),
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 100,
						'max' => 1000,
					),
					'%'  => array(
						'min' => 5,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}}-popup .twae-popup-content .twae-media img' => '--tw-cbx-img-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		// Image Height
		$this->add_responsive_control(
			'twae_image_height',
			array(
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'label'       => esc_html__( 'Image Height (Max)', 'twae' ),
				'description' => esc_html__( 'Delete value to auto adjust image maximum height.', 'twae' ),
				'size_units'  => array( 'px' ),
				'range'       => array(
					'px' => array(
						'min' => 100,
						'max' => 1000,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}}-popup .twae-popup-content .twae-media img' => '--tw-cbx-img-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		// Image Alignment
		$this->add_control(
			'twae_image_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'twae' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'twae' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'twae' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'twae' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'toggle'    => true,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}}-popup .twae-popup-content .twae-media' => '--tw-cbx-img-align: {{VALUE}};',
				),
			)
		);

		// Image Padding
		$this->add_responsive_control(
			'twae_image_padding',
			array(
				'label'      => __( 'Padding', 'twae' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}}-popup .twae-popup-content .twae-media img' =>
					'--tw-cbx-img-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),

			)
		);

		// Image Margin
		$this->add_responsive_control(
			'twae_image_margin',
			array(
				'label'      => __( 'Margin', 'twae' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}}-popup .twae-popup-content .twae-media img' =>
					'--tw-cbx-img-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};
					--tw-cbx-img-margin-left: {{LEFT}}{{UNIT}};
					--tw-cbx-img-margin-right: {{RIGHT}}{{UNIT}};',
					'{{WRAPPER}} .twae_image_outside' =>
					'--tw-image-outside-top-margin: {{TOP}}{{UNIT}};
					--tw-image-outside-bottom-margin: {{BOTTOM}}{{UNIT}};',
				),
			)
		);

		// Image Border Pop Over
		$this->add_control(
			'twae_image_border_popover',
			array(
				'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label'        => esc_html__( 'Border', 'twae' ),
				'label_off'    => esc_html__( 'Default', 'twae' ),
				'label_on'     => esc_html__( 'Custom', 'twae' ),
				'return_value' => 'yes',
			)
		);
		$this->start_popover();

		// Image Border Type
		$this->add_control(
			'twae_image_border_type',
			array(
				'label'     => esc_html__( 'Border Type', 'twea' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'separator' => 'before',
				'default'   => 'solid',
				'options'   => array(
					'none'   => esc_html__( 'None', 'twea' ),
					'solid'  => esc_html__( 'Solid', 'twea' ),
					'dashed' => esc_html__( 'Dashed', 'twea' ),
					'dotted' => esc_html__( 'Dotted', 'twea' ),
					'double' => esc_html__( 'Double', 'twea' ),
					'groove' => esc_html__( 'Groove', 'twea' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}}-popup .twae-popup-content .twae-media img' => '--tw-cbx-img-border-style: {{VALUE}}',
				),
				'condition' => array(
					'twae_image_border_popover' => 'yes',
				),
			)
		);

		// Image Border Color
		$this->add_control(
			'twae_image_border_color',
			array(
				'label'     => __( 'Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}}-popup .twae-popup-content .twae-media img' => '--tw-cbx-img-border-color: {{VALUE}}',
				),
				'condition' => array(
					'twae_image_border_popover' => 'yes',
				),
			)
		);

		// Image Border Width
		$this->add_control(
			'twae_image_border_width',
			array(
				'label'     => __( 'Border Width', 'twae' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}}-popup .twae-popup-content .twae-media img' => '--tw-cbx-img-border-width: {{SIZE}}px',
				),
				'condition' => array(
					'twae_image_border_type!'   => 'none',
					'twae_image_border_popover' => 'yes',
				),
			)
		);
		$this->end_popover();
		// -----------------------pop border end--------------------------

		// Image Border Shadow Pop Over
		$this->add_control(
			'twae_image_border_shadow_popover',
			array(
				'type'         => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label'        => esc_html__( 'Box Shadow', 'twae' ),
				'label_off'    => esc_html__( 'Default', 'twae' ),
				'label_on'     => esc_html__( 'Custom', 'twae' ),
				'return_value' => 'yes',
				'condition'    => array(
					'twae_content_in_popup' => 'no',
				),
			)
		);

		$this->start_popover();
		// Image Border Shadow
		$this->add_control(
			'twae_image_border_shadow',
			array(
				'label'     => esc_html__( 'Box Shadow', 'twae' ),
				'type'      => \Elementor\Controls_Manager::BOX_SHADOW,
				'default'   => array(
					'horizontal' => 0,
					'vertical'   => 2,
					'blur'       => 8,
					'spread'     => -2,
					'color'      => 'rgba(0,0,0,0.3)',
				),
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-cbx-img-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}}',
				),
				'condition' => array(
					'twae_image_border_shadow_popover' => 'yes',
					'twae_content_in_popup'            => 'no',
				),
			)
		);
		$this->end_popover();
		$this->add_control(
			'twae_image_hover_effect',
			array(
				'label'        => esc_html__( 'Image Effect', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_bg_on'  => __( 'Show', 'twae' ),
				'label_bg_off' => __( 'Hhow', 'twae' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => array(
					'twae_content_in_popup' => 'no',
				),
			)
		);
		$this->end_controls_section();
			// Image Section End

		// Button Section Start
		$this->start_controls_section(
			'twae_button_section',
			array(
				'label' => __( '🅱 Button (Read More)', 'twae' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		// Button BG Color
		$this->add_control(
			'twae_button_bgcolor',
			array(
				'label'     => __( 'Background', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}}-popup .twae-popup-content' => '--tw-cbx-btn-bgcolor: {{VALUE}}',
				),
			)
		);
		// Button Color
		$this->add_control(
			'twae_button_color',
			array(
				'label'     => __( 'Text Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}}-popup .twae-popup-content' => '--tw-cbx-btn-color: {{VALUE}}',
				),
			)
		);
		// Button Alignment
		$this->add_control(
			'twae_button_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'twae' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'twae' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'twae' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'twae' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'toggle'    => true,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}}-popup .twae-popup-content' => '--tw-cbx-btn-align: {{VALUE}};',
				),
			)
		);
		// Button Width
		$this->add_responsive_control(
			'twae_button_width',
			array(
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'label'      => esc_html__( 'Button Width', 'twae' ),
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 50,
						'max' => 600,
					),
					'%'  => array(
						'min' => 5,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}}-popup .twae-popup-content' => '--tw-cbx-btn-width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		// Button Height
		$this->add_responsive_control(
			'twae_button_height',
			array(
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'label'       => esc_html__( 'Button Height', 'twae' ),
				'description' => esc_html__( 'Delete value to auto adjust button height.', 'twae' ),
				'size_units'  => array( 'px' ),
				'range'       => array(
					'px' => array(
						'min' => 20,
						'max' => 120,
					),
				),
				/*
				'default' => [
					'size' => 100,
					'unit' => '%',
				],*/
				'selectors'   => array(
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}}-popup .twae-popup-content' => '--tw-cbx-btn-height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		// Button Margin
		$this->add_responsive_control(
			'twae_button_margin',
			array(
				'label'      => __( 'Margin', 'twae' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}}-popup .twae-popup-content' =>
					'--tw-cbx-btn-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};
					--tw-cbx-btn-margin-left: {{LEFT}}{{UNIT}};
					--tw-cbx-btn-margin-right: {{RIGHT}}{{UNIT}};',
				),
			)
		);
		// Button Padding
		$this->add_responsive_control(
			'twae_button_padding',
			array(
				'label'      => __( 'Padding', 'twae' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}}-popup .twae-popup-content' =>
					'--tw-cbx-btn-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		// Button Section End
		$this->end_controls_section();
	}
	/* ------------- Content Settings - Title/Img/Desc/Button - END ------------- */


	/**
	 * Start of pop up settings.
	 * This function handles the color settings for the pop up.
	 */
	public function twae_popup_settings() {
		$this->start_controls_section(
			'twae_popup_style_settings',
			array(
				'label'     => __( '📜 Pop Up', 'twae' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'twae_content_in_popup' => 'yes',

				),
			)
		);

		// popup Styles Notice
		$this->add_control(
			'twae_popup_notice',
			array(
				'label'     => __( 'Before applying these styles, Please click on any story and open popup.', 'twae' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		 // story background color
		$this->add_control(
			'twae_popup_story_description_bgcolor',
			array(
				'label'     => __( 'Background Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}-popup .twae-popup-content'
					 => '--tw-cbx-bg: {{VALUE}}',
				),
				'condition' => array(
					'twae_content_in_popup' => 'yes',
				),
			)
		);
		// Title color
		$this->add_control(
			'twae_popup_title_color',
			array(
				'label'     => __( 'Title Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}-popup .twae-popup-content .twae-title' => '--tw-cbx-title-color: {{VALUE}}',
				),
				'condition' => array(
					'twae_content_in_popup' => 'yes',
				),
			)
		);

		// Title Typography
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'twae_popup_title_typography',
				'label'     => __( 'Title Typography', 'twae' ),
				'selector'  => '{{WRAPPER}}-popup .twae-popup-content .twae-title',
				'condition' => array(
					'twae_content_in_popup' => 'yes',
				),
			)
		);

		// Description color
		$this->add_control(
			'twae_popup_description_color',
			array(
				'label'     => __( 'Description Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}-popup .twae-popup-content .twae-description' => '--tw-cbx-des-color: {{VALUE}}',
				),
				'condition' => array(
					'twae_content_in_popup' => 'yes',
				),
			)
		);

		// Description typography
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'twae_popup_description_typography',
				'label'     => __( 'Description Typography', 'twae' ),
				'selector'  => '{{WRAPPER}}-popup .twae-popup-content .twae-description',
				'condition' => array(
					'twae_content_in_popup' => 'yes',
				),
			)
		);

			// alignment
		$this->add_control(
			'twae_popup_content_alignment',
			array(
				'label'     => esc_html__( 'Content Alignment', 'twae' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'twae' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'twae' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'twae' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'toggle'    => true,
				'selectors' => array(
					'{{WRAPPER}}-popup .twae-popup-content' => '--tw-cbx-text-align: {{VALUE}};',
				),
				'condition' => array(
					'twae_content_in_popup' => 'yes',
				),
			)
		);

		// container padding
		$this->add_control(
			'twae_popup_content_container_alignment',
			array(
				'label'      => __( 'Padding', 'twae' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}}-popup .twae-popup-content' =>
					'--tw-cbx-cont-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'twae_content_in_popup' => 'yes',
				),
			)
		);

		$this->end_controls_section();
		/** pop up settings end */
	}

	/* ----------------------------- Style Controls ----------------------------- */
	/**
	 * Style controls function.
	 * This function handles the style settings for the widget.
	 */
	public function style_controls() {
		// Call the line settings function
		$this->twae_line_settings();
		// Call the story icon style settings function
		$this->story_icon_style_settings();
		// Call the story year/label/date settings function
		$this->story_yld_settings();
		// Call the content box settings function
		$this->twae_cbox_settings();
		// Call the story content settings function
		$this->twae_storycontent_settings();
		// Call the pop up settings function
		$this->twae_popup_settings();
	}

}
