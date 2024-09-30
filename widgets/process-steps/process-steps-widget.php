<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class steps_process_widget extends \Elementor\Widget_Base {



	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		// Ensure that the URLs are properly escaped to prevent XSS vulnerabilities
		wp_register_style( 'cps-horizontal-process', esc_url( TWAE_PRO_URL . 'assets/css/horizontal-process.css' ), array(), esc_attr( TWAE_PRO_VERSION ), 'all' );
		wp_register_style( 'cps-vertical-process', esc_url( TWAE_PRO_URL . 'assets/css/vertical-process.css' ), array(), esc_attr( TWAE_PRO_VERSION ), 'all' );
		wp_register_style( 'cps-hover-style', esc_url( TWAE_PRO_URL . 'assets/css/hover.css' ), array(), esc_attr( TWAE_PRO_VERSION ), 'all' );
		wp_register_style( 'cps-font-awesome-5-all', esc_url( ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/all.css' ), array(), esc_attr( TWAE_PRO_VERSION ), 'all' ); // load elementor fontawesome
	}

	public function get_style_depends() {
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			return array( 'cps-vertical-process', 'cps-horizontal-process', 'cps-hover-style', 'cps-font-awesome-5-all' );
		}
		$settings = $this->get_settings_for_display();
		$layout   = $settings['cps_process_layout'];
		$styles   = array( 'cps-hover-style' );
		if ( $layout == 'Horizontal' ) {
			array_push( $styles, 'cps-horizontal-process' );
		} else {
			array_push( $styles, 'cps-vertical-process' );
		}
		return $styles;
	}

	public function get_name() {
		return 'timeline-process-steps-widget';
	}

	public function get_title() {
		return esc_html__( 'Process Steps', 'pswfe' );
	}

	public function get_icon() {
		return 'eicon-twae-process-steps';
		return 'eicon-sitemap';
	}

	public function get_categories() {
		return array( 'twae' );
	}

	public function get_keywords() {
		return array( 'process', 'steps', 'timeline' );
	}

	protected function register_controls() {
		$this->content_control_section();
		$this->icon_style_control_section();
		$this->Badge_style_control_section();
		$this->title_desc_style_control_section();
		$this->direction_style_control_section();
		// $this->style_controls_for_active_color();
		// $this->image_controls_style();
	}

	protected function content_control_section() {
		// Content Tab Start
		$this->start_controls_section(
			'cps_section_icon',
			array(
				'label' => esc_html__( 'Process Steps', 'process-steps' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		// //repeater start
		$repeater = new \Elementor\Repeater();

		$repeater->start_controls_tabs(
			'cps_steps_tabs'
		);

		$repeater->start_controls_tab(
			'cps_steps_content_tabs',
			array(
				'label' => __( 'Steps', 'process-steps' ),
			)
		);

		 // //Badge field
		$repeater->add_control(
			'cps_badge',
			array(
				'label'       => __( 'Badge Text', 'process-steps' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Badge', 'process-steps' ),
				'default'     => __( '1', 'process-steps' ),
				'description' => 'Left it blank, if you want to remove the Badge',
			)
		);
		// //Choose Field for Text and Icon
		$repeater->add_control(
			'cps_selected_icon',
			array(
				'label'   => esc_html__( 'Icon', 'process-steps' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => array(
					'icon'       => array(
						'title' => __( 'Icon', 'process-steps' ),
						'icon'  => 'fab fa-font-awesome',
					),
					'image'      => array(
						'title' => __( 'Image', 'twae' ),
						'icon'  => 'fa fa-images',
					),
					'customtext' => array(
						'title' => __( 'Text', 'process-steps' ),
						'icon'  => 'fa fa-list-ol',
					),
					'cps-none'   => array(
						'title' => esc_html__( 'None', 'pswfe' ),
						'icon'  => 'eicon-ban',
					),

				),
				'default' => 'icon',
				'toggle'  => true,

			)
		);

		// //choose field
		$repeater->add_control(
			'cps_story_icon',
			array(
				'label'     => __( 'FontAwesome Icon', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'cps_selected_icon' => 'icon',
				),
			)
		);
		// Icon Type Image
		$repeater->add_control(
			'cps_icon_image',
			array(
				'label'     => __( 'Icon Image', 'twae' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'cps_selected_icon' => 'image',
				),
			)
		);

		// Icon text
		$repeater->add_control(
			'cps_icon_text',
			array(
				'label'     => __( 'Icon Text', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => '1',
				'condition' => array(
					'cps_selected_icon' => 'customtext',
				),
			)
		);

		// //Text Field
		$repeater->add_control(
			'cps_title',
			array(
				'label'       => esc_html__( 'Title', 'process-steps' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => esc_html__( 'Steps', 'process-steps' ),
				'default'     => esc_html__( 'Steps', 'process-steps' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		// //Description Field
		$repeater->add_control(
			'cps_description',
			array(
				'label'       => esc_html__( 'Description', 'process-steps' ),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'rows'        => 10,
				'default'     => esc_html__( 'Default description', 'process-steps' ),
				'placeholder' => esc_html__( 'Type your description here', 'process-steps' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);
		$repeater->add_control(
			'cps_enable_link',
			array(
				'label'        => esc_html__( 'Enable Title Link', 'process-steps' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Enable', 'process-steps' ),
				'label_off'    => esc_html__( 'Disable', 'process-steps' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		$repeater->add_control(
			'cps_website_link',
			array(
				'label'       => esc_html__( 'Title Link', 'plugin-name' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'plugin-name' ),
				'condition'   => array(
					'cps_enable_link' => 'yes',
				),
				'label_block' => true,
			)
		);

		$repeater->end_controls_tab();

		// second repeater
		// colors section
		$repeater->start_controls_tab(
			'cps_steps_colors_tabs',
			array(
				'label' => __( 'Colors', 'process-steps' ),
			)
		);

		$repeater->add_control(
			'cps_show_notice',
			array(
				'label'     => __( 'These Color Settings will override the global Color Settings', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$repeater->add_control(
			'cps_icon_color',
			array(
				'label'     => __( 'Icon color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .pswfe-marker-text,
                    {{WRAPPER}}  {{CURRENT_ITEM}} .pswfe-vertical-marker-text' => 'color: {{VALUE}};',
				),
			)
		);

		$repeater->add_control(
			'cps_icon_background_color',
			array(
				'label'     => __( 'Icon Background Color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .pswfe-steps-marker,
                    {{WRAPPER}} {{CURRENT_ITEM}} .pswfe-vertical-steps-marker' => 'background-color: {{VALUE}};',

				),

			)
		);

		$repeater->add_control(
			'cps_badge_color',
			array(
				'label'     => __( 'Badge Color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .pswfe-badge,
                    {{WRAPPER}} {{CURRENT_ITEM}} .pswfe-vertical-badge' => '--badge-color: {{VALUE}};
                                                                    --active-badge-color: {{VALUE}}',

				),

			)
		);

		$repeater->add_control(
			'cps_badge_background_color',
			array(
				'label'     => __( 'Badge Background Color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .pswfe-badge,
                    {{WRAPPER}} {{CURRENT_ITEM}} .pswfe-vertical-badge' => '--badge-background-color: {{VALUE}};
                                                                     --active-bg-badge-color: {{VALUE}}',

				),

			)
		);

		$repeater->add_control(
			'cps_title_color',
			array(
				'label'     => __( 'Title Color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .pswfe-title,
                    {{WRAPPER}} {{CURRENT_ITEM}} .pswfe-vertical-title' => '--content-title-color: {{VALUE}};',

				),

			)
		);

		$repeater->add_control(
			'cps_description_color',
			array(
				'label'     => __( 'Description Color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .pswfe-content-desc,
                    {{WRAPPER}} {{CURRENT_ITEM}} .pswfe-vertical-content-desc' => '--content-desc-color: {{VALUE}};',

				),

			)
		);
		$repeater->add_control(
			'cps_line_direction_color',
			array(
				'label'     => __( 'Line Color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}  .pswfe-steps-segment:not(:last-child){{CURRENT_ITEM}}:after,
					{{WRAPPER}}  .pswfe-vertical-steps-segment:not(:last-child){{CURRENT_ITEM}}:after,
					{{WRAPPER}} .pswfe-steps .pswfe-has-arrow:not(:last-child){{CURRENT_ITEM}}:before,
                    {{WRAPPER}}  .pswfe-vertical-steps .pswfe-vertical-has-arrow:not(:last-child){{CURRENT_ITEM}}:before' => '--bar-color: {{VALUE}};
                                                                                                                                --active-bar-color: {{VALUE}}',

				),

			)
		);

		$repeater->end_controls_tab(); // second repeater end

		$repeater->end_controls_tab(); // main
		// //arrow class

		// // field of repeater title , description , icons
		$this->add_control(
			'cps_icon_list',
			array(
				'label'       => esc_html__( 'Steps', 'elementor' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'cps_title'       => esc_html__( 'Place order', 'process-steps' ),
						'cps_description' => esc_html__( 'Online order Place by the user', 'process-steps' ),
						'cps_icon_text'   => '1',
						'cps_story_icon'  => array(
							'value' => 'fas fa-hand-point-up',

						),
						'cps_badge'       => '1',
					),
					array(
						'cps_title'       => esc_html__( 'Check payment', 'process-steps' ),
						'cps_description' => esc_html__( 'Check payment', 'process-steps' ),
						'cps_icon_text'   => '2',
						'cps_story_icon'  => array(
							'value' => 'fas fa-dollar-sign',
						),
						'cps_badge'       => '2',

					),
					array(
						'cps_title'       => esc_html__( 'Gather items', 'process-steps' ),
						'cps_description' => esc_html__( 'Collect all item placed by the user', 'process-steps' ),
						'cps_icon_text'   => '3',
						'cps_story_icon'  => array(
							'value' => 'fas fa-shopping-cart',
						),
						'cps_badge'       => '3',
					),
					array(
						'cps_title'       => esc_html__( 'Ship order', 'process-steps' ),
						'cps_description' => esc_html__( 'Send order to user address', 'process-steps' ),
						'cps_icon_text'   => '4',
						'cps_story_icon'  => array(
							'value' => 'fas fa-shipping-fast',
						),
						'cps_badge'       => '4',
					),

				),
				'title_field' => '{{{ cps_title }}}',
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'cps_general_settings',
			array(
				'label' => __( 'Settings', 'process-steps' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,

			)
		);
		$this->add_control(
			'cps_process_layout',
			array(
				'label'   => __( 'Process Layout', 'process-steps' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'Horizontal',
				'options' => array(
					'Vertical'   => 'Vertical',
					'Horizontal' => 'Horizontal',
				),
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
					'v-style-1' => 'Style 1',
					'v-style-2' => 'Style 2',
					'v-style-3' => 'Style 3',
					'v-style-4' => 'Style 4',
				),
				'condition'   => array(
					'cps_process_layout' => 'Vertical',
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
					'h-style-1' => 'Style 1',
					'h-style-2' => 'Style 2',
					'h-style-3' => 'Style 3',
					'h-style-4' => 'Style 4',
				),
				'condition'   => array(
					'cps_process_layout' => 'Horizontal',
				),
				// 'render_type' => 'none'
			)
		);

		// Display badge
		$this->add_control(
			'cps_selected_badge',
			array(
				'label'   => esc_html__( 'Step Badge', 'process-steps' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => array(
					'badge-customtext' => array(
						'title' => __( 'On Icon', 'process-steps' ),
						'icon'  => 'fa fa-list-ol',
					),
					// 'badge-customcontent' => array(
					// 'title' => __('On content', 'process-steps'),
					// 'icon' => 'fa fa-list-ol',
					// ),
					'cps-none'         => array(
						'title' => esc_html__( 'None', 'pswfe' ),
						'icon'  => 'eicon-ban',
					),
				),
				'default' => 'badge-customtext',
				'toggle'  => true,

			)
		);
		$this->add_control(
			'cps_badge_position',
			array(
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'label'     => esc_html__( 'Badge Position', 'process-steps' ),
				'options'   => array(
					'top-left'     => array(
						'title' => esc_html__( 'Top Left', 'process-steps' ),
						'icon'  => 'eicon-text-align-left',
					),
					'top-right'    => array(
						'title' => esc_html__( 'Top Right', 'process-steps' ),
						'icon'  => 'eicon-text-align-right',
					),
					'bottom-left'  => array(
						'title' => esc_html__( 'Bottom Left', 'process-steps' ),
						'icon'  => 'eicon-text-align-left',
					),
					'bottom-right' => array(
						'title' => esc_html__( 'Bottom Right', 'process-steps' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'top-right',
				'condition' => array(
					'cps_selected_badge' => array( 'badge-customtext', 'badge-customcontent' ),
				),
				/*
				 'selectors' => array(
			'{{WRAPPER}} .pswfe-badge,
			{{WRAPPER}} .pswfe-vertical-badge' => '--content-desc-align:{{value}};
			--content-heading-align:{{value}};',
			), */
			)
		);

		$this->add_control(
			'cps_hide_show_line',
			array(
				'label'     => esc_html__( 'Hide/Show Line', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'block' => array(
						'title' => __( 'Show', 'process-steps' ),
						'icon'  => 'eicon-circle-o',
					),
					'none'  => array(
						'title' => __( 'Hide', 'process-steps' ),
						'icon'  => 'eicon-ban',
					),
				),

				'default'   => 'block',
				'toggle'    => true,
				'selectors' => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps .pswfe-vertical-steps-segment:before,{{WRAPPER}} ul.pswfe-vertical-steps .pswfe-vertical-steps-segment:after,
                {{WRAPPER}} ul.pswfe-steps .pswfe-steps-segment:before,{{WRAPPER}} ul.pswfe-steps .pswfe-steps-segment:after' => 'display: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'cps_enable_connector',
			array(
				'label'     => esc_html__( 'Connector', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'cps-connector-arrow' => array(
						'title' => __( 'Arrow', 'process-steps' ),
						'icon'  => 'fa fa-arrow-right',
					),
					'cps-none'            => array(
						'title' => __( 'None', 'process-steps' ),
						'icon'  => 'eicon-ban',
					),
				),
				'condition' => array(
					'cps_hide_show_line' => 'block',
				),
				'default'   => 'cps-connector-arrow',
				'toggle'    => true,
			)
		);

		$this->add_control(
			'cps_show_gap',
			array(
				'label'        => esc_html__( 'Show Gap', 'process-steps' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'process-steps' ),
				'label_off'    => esc_html__( 'Hide', 'process-steps' ),
				'return_value' => 'yes',
				'condition'    => array(
					'cps_hide_show_line' => 'block',
				),
				'default'      => '',
			)
		);

		$this->end_controls_section();

	}

	// icon style controls
	protected function icon_style_control_section() {
		$this->start_controls_section(
			'cps_icon_style',
			array(
				'label' => __( 'Steps Icon', 'process-steps' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,

			)
		);

		// icon color
		$this->add_control(
			'cps_icon_color',
			array(
				'label'     => __( 'Color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps,
                {{WRAPPER}} ul.pswfe-steps' => '--marker-color: {{VALUE}};',

				),
			)
		);
		// icon bg color
		$this->add_control(
			'cps_icon_background_color',
			array(
				'label'     => __( 'Background Color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps,
                {{WRAPPER}} ul.pswfe-steps' => '--marker-background-color: {{VALUE}};',
				),
			)
		);

		// icon size
		$this->add_responsive_control(
			'cps_icon_size',
			array(
				'label'     => __( 'Size', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 1,
						'max' => 15,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps,
                {{WRAPPER}} ul.pswfe-steps' => '--marker-size: {{SIZE}}',
				),
			)
		);

		// icon padding
		$this->add_responsive_control(
			'cps_icon_padding',
			array(
				'label'     => __( 'Icon/Text size', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 2,
						'max' => 15,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps,
                {{WRAPPER}} ul.pswfe-steps' => '--marker-text-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		// icon typography
		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'icon_box_shadow',
				'selector' => '{{WRAPPER}} .pswfe-steps-marker, {{WRAPPER}} .pswfe-vertical-steps-marker',

			)
		);

		// icon border
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'icon_border',
				'label'    => __( 'Border', 'process-steps' ),
				'selector' => '{{WRAPPER}} .pswfe-steps-marker, {{WRAPPER}} .pswfe-vertical-steps-marker',
			)
		);

		// icon border radius
		$this->add_responsive_control(
			'cps_icon_border_radius',
			array(
				'label'      => __( 'Border Radius', 'process-steps' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),

				'selectors'  => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps,
                {{WRAPPER}} ul.pswfe-steps' => '--marker-border-radius:  {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	// badge style controls
	protected function Badge_style_control_section() {
		$this->start_controls_section(
			'cps_badge_style_section',
			array(
				'label'     => __( 'Badge Style', 'process-steps' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'cps_selected_badge!' => 'cps-none',
				),

			)
		);

		   // badge color
		$this->add_control(
			'cps_badge_color',
			array(
				'label'     => __( 'Color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps,
                {{WRAPPER}} ul.pswfe-steps' => '--badge-color: {{VALUE}};',
				),
				'condition' => array(
					'cps_selected_badge!' => 'cps-none',
				),
			)
		);
		// badge bg color
		$this->add_control(
			'cps_badge_background_color',
			array(
				'label'     => __( 'Background Color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps,
                    {{WRAPPER}} ul.pswfe-steps' => '--badge-background-color: {{VALUE}};',
				),
				'condition' => array(
					'cps_selected_badge!' => 'cps-none',
				),
			)
		);

		// badge typography
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'      => 'badge_typography',
				'selector'  => '{{WRAPPER}} .pswfe-badge, {{WRAPPER}} .pswfe-vertical-badge',
				'condition' => array(
					'cps_selected_badge!' => 'cps-none',
				),

			)
		);
		// badge border
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'      => 'badge_border',
				'label'     => __( 'Border', 'process-steps' ),
				'selector'  => '{{WRAPPER}} .pswfe-badge, {{WRAPPER}} .pswfe-vertical-badge',
				'condition' => array(
					'cps_selected_badge!' => 'cps-none',
				),

			)
		);
		// badge border radius
		$this->add_responsive_control(
			'cps_badge_border_radius',
			array(
				'label'      => __( 'Border Radius', 'process-steps' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps,
                {{WRAPPER}} ul.pswfe-steps' => '--badge-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};
                                                --badge-text-bd-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'cps_selected_badge!' => 'cps-none',
				),
			)
		);
		 // badge margin
		$this->add_responsive_control(
			'cps_badge_margin',
			array(

				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'process-steps' ),
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pswfe-badge'          => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .pswfe-vertical-badge' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'cps_selected_badge!' => 'cps-none',
				),

			)
		);

		// badge padding
		$this->add_responsive_control(
			'cps_badge_padding',
			array(

				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'process-steps' ),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps,
                    {{WRAPPER}} ul.pswfe-steps' => '--badge-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'cps_selected_badge!' => 'cps-none',
				),

			)
		);

		$this->end_controls_section();

	}

	// title style controls
	protected function title_desc_style_control_section() {
		$this->start_controls_section(
			'cps_title_desc_style_section',
			array(
				'label' => __( 'Title/Description Style', 'process-steps' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'cps_heading_title',
			array(
				'type'  => \Elementor\Controls_Manager::HEADING,
				'label' => __( 'Title', 'process-steps' ),
			)
		);
		// title color
		$this->add_control(
			'cps_title_color',
			array(
				'label'     => __( 'Color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps,
                    {{WRAPPER}} ul.pswfe-steps' => '--content-title-color: {{VALUE}};',
				),
			)
		);
		  // title shadow
		$this->add_group_control(
			\Elementor\Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'title_shadow',
				'selector' => '{{WRAPPER}} .pswfe-title, {{WRAPPER}} .pswfe-vertical-title',
			)
		);
		// title typography
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .pswfe-title, {{WRAPPER}} .pswfe-vertical-title',

			)
		);

		// title margin
		$this->add_responsive_control(
			'cps_title_margin',
			array(
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'process-steps' ),
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps .pswfe-vertical-title,
                    {{WRAPPER}} ul.pswfe-steps .pswfe-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

				),

			)
		);

		// start description
		$this->add_control(
			'cps__heading_description',
			array(
				'type'      => \Elementor\Controls_Manager::HEADING,
				'label'     => __( 'Description', 'process-steps' ),
				'separator' => 'before',
			)
		);
		// description color
		$this->add_control(
			'cps_description_color',
			array(
				'label'     => __( 'Color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps,
                    {{WRAPPER}} ul.pswfe-steps' => '--content-desc-color: {{VALUE}};',

				),
			)
		);
		// description shadow
		$this->add_group_control(
			\Elementor\Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'description_shadow',
				'selector' => '{{WRAPPER}} .pswfe-content-desc, {{WRAPPER}} .pswfe-vertical-content-desc',
			)
		);
		// description typography
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .pswfe-content-desc, {{WRAPPER}} .pswfe-vertical-content-desc',

			)
		);
		// description margin
		$this->add_responsive_control(
			'cps_description_margin',
			array(
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'process-steps' ),
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps .pswfe-vertical-content-desc,
                    {{WRAPPER}} ul.pswfe-steps .pswfe-content-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

				),

			)
		);

		// start content
		$this->add_control(
			'cps_content_common',
			array(
				'type'      => \Elementor\Controls_Manager::HEADING,
				'label'     => __( 'Content', 'process-steps' ),
				'separator' => 'before',
				'condition' => array(
					'cps_process_layout' => 'Vertical',
				),
			)
		);
		// content bg color
		$this->add_control(
			'cps_content_background_color',
			array(
				'label'     => __( 'Background Color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps' => '--content-background-color: {{VALUE}};',

				),
				'condition' => array(
					'cps_process_layout' => 'Vertical',
				),
			)
		);
		// content border
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'      => 'content_border',
				'label'     => __( 'Border', 'process-steps' ),
				'condition' => array(
					'cps_process_layout' => 'Vertical',
				),
				'selector'  => '{{WRAPPER}} .pswfe-vertical-steps-content',

			)
		);
		// content border radius
		$this->add_responsive_control(
			'cps_contenet_border_radius',
			array(
				'label'     => __( 'Border Radius', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'condition' => array(
					'cps_process_layout' => 'Vertical',
				),

				'range'     => array(
					'%' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps' => '--content-border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);
		// content padding
		$this->add_responsive_control(
			'cps_contenet_padding',
			array(

				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'process-steps' ),
				'size_units' => array( 'px' ),
				'condition'  => array(
					'cps_process_layout' => 'Vertical',
				),
				'selectors'  => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps' => '--step-content-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

				),

			)
		);

		$this->end_controls_section();

		// start container style
		$this->start_controls_section(
			'cps_container_style',
			array(
				'label'     => __( 'Container Style', 'process-steps' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'cps_process_layout' => 'Vertical',
				),
			)
		);

		// bottom spacing
		$this->add_responsive_control(
			'cps_container_bottom_spacing',
			array(
				'label'      => __( 'Bottom Spacing', 'process-steps' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'condition'  => array(
					'cps_process_layout' => 'Vertical',
				),
				'selectors'  => array(
					'{{WRAPPER}} .pswfe-vertical-steps .pswfe-vertical-steps-segment:not(:last-child)' => 'padding-bottom: {{SIZE}}{{UNIT}};',

				),
			)
		);

		$this->end_controls_section();

		// start hover settings
		$this->start_controls_section(
			'cps_Hover_style',
			array(
				'label' => __( 'Hover Style', 'process-steps' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		// hover title color
		$this->add_control(
			'cps_title_hvr_color',
			array(
				'label'     => __( 'Title Color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps .pswfe-vertical-steps-segment:hover .pswfe-vertical-steps-content .pswfe-vertical-title,
                    {{WRAPPER}} ul.pswfe-steps .pswfe-steps-segment:hover .pswfe-steps-content .pswfe-title' => 'color: {{VALUE}};',
				),
			)
		);
		// hover des color
		$this->add_control(
			'cps_desc_hvr_color',
			array(
				'label'     => __( 'Description Color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps .pswfe-vertical-steps-segment:hover .pswfe-vertical-steps-content .pswfe-vertical-content-desc,
                    {{WRAPPER}} ul.pswfe-steps .pswfe-steps-segment:hover .pswfe-steps-content .pswfe-content-desc' => 'color: {{VALUE}};',
				),
			)
		);
		// hover icon color
		$this->add_control(
			'cps_icon_hvr_color',
			array(
				'label'     => __( 'Icon Color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps .pswfe-vertical-steps-segment:hover .pswfe-vertical-marker-text,
                {{WRAPPER}} ul.pswfe-steps .pswfe-steps-segment:hover .pswfe-marker-text' => 'color: {{VALUE}};',
				),
			)
		);
		// hover bg color
		$this->add_control(
			'cps_icon_background_hvr_color',
			array(
				'label'     => __( 'Icon Background Color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps .pswfe-vertical-steps-segment:hover .pswfe-vertical-steps-marker ,
                {{WRAPPER}} ul.pswfe-steps .pswfe-steps-segment:hover .pswfe-steps-marker' => 'background-color: {{VALUE}};
                                                                                                box-shadow: 0 0 15px {{VALUE}};',
				),
			)
		);
		// hover badge color
		$this->add_control(
			'cps_badge_hvr_color',
			array(
				'label'     => __( 'Badge Color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps .pswfe-vertical-steps-segment:hover .pswfe-vertical-badge,
                {{WRAPPER}} ul.pswfe-steps .pswfe-steps-segment:hover .pswfe-badge' => 'color: {{VALUE}};',
				),
			)
		);
		// hover badge bg color
		$this->add_control(
			'cps_badge_background_hvr_color',
			array(
				'label'     => __( 'Badge Background Color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps .pswfe-vertical-steps-segment:hover .pswfe-vertical-badge ,
                {{WRAPPER}} ul.pswfe-steps .pswfe-steps-segment:hover .pswfe-badge' => 'background-color: {{VALUE}};',
				),
			)
		);
		// hover line color
		$this->add_control(
			'cps_line_background_hvr_color',
			array(
				'label'     => __( 'Line Color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps .pswfe-vertical-steps-segment:hover:after,
                    {{WRAPPER}} ul.pswfe-steps .pswfe-steps-segment:hover:after' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} ul.pswfe-vertical-steps .pswfe-vertical-steps-segment:hover:before' => ' --bar-color:{{VALUE}}',
					'{{WRAPPER}} ul.pswfe-steps .pswfe-steps-segment:hover:before' => '     border-right: var(--bar-size) solid {{VALUE}};
                                                                                            border-top: var(--bar-size) solid {{VALUE}};',
				),
			)
		);

		// hover animation
		$this->add_control(
			'cps_hover_animation',
			array(
				'label'   => __( 'Hover Animation', 'twae' ),
				'type'    => 'select',
				'default' => 'none',
				'options' => array(
					'none'                           => 'None',
					'cps-hvr-sweep-to-right'         => 'Sweep To Right',
					'cps-hvr-sweep-to-left'          => 'Sweep To Left',
					'cps-hvr-sweep-to-bottom'        => 'Sweep To Bottom',
					'cps-hvr-sweep-to-top'           => 'Sweep To Top',
					'cps-hvr-shutter-in-horizontal'  => 'Shutter In Horizontal',
					'cps-hvr-shutter-out-horizontal' => 'Shutter Out Horizontal',
					'cps-hvr-shutter-in-vertical'    => 'Shutter In Vertical',
					'cps-hvr-shutter-out-vertical'   => 'Shutter Out Vertical',

				),
			)
		);
		// hover animation color
		$this->add_control(
			'cps_animation_hvr_color',
			array(
				'label'     => __( 'Hover Animation Color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps,
                            {{WRAPPER}} ul.pswfe-steps' => '--step-animation-hover-color: {{VALUE}};',

				),
			)
		);

		$this->end_controls_section();

	}

	// Arrow style controls
	protected function direction_style_control_section() {
		$this->start_controls_section(
			'cps_arrow_style_section',
			array(
				'label'     => __( 'Line Style', 'process-steps' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'cps_hide_show_line!' => 'none',
				),
			)
		);
		// line width
		$this->add_control(
			'cps_line_width',
			array(
				'label'      => __( 'Line width', 'process-steps' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,

				'range'      => array(
					'px' => array(
						'min' => 2,
						'max' => 25,
					),
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} ul.pswfe-vertical-steps,
                    {{WRAPPER}} ul.pswfe-steps' => '--bar-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'cps_hide_show_line!' => 'none',
				),

			)
		);
		// line color
		$this->add_control(
			'cps_line_direction_color',
			array(
				'label'     => __( 'Color', 'process-steps' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pswfe-steps-segment:not(:last-child):after,
                    {{WRAPPER}} .pswfe-vertical-steps-segment:not(:last-child):after,
                    {{WRAPPER}} .pswfe-steps .pswfe-has-arrow:not(:last-child):before,
                    {{WRAPPER}}  .pswfe-vertical-steps .pswfe-vertical-has-arrow:not(:last-child):before' => '--bar-color: {{VALUE}};',
				),
				'condition' => array(
					'cps_hide_show_line!' => 'none',
				),
			)
		);
		// line border
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'      => 'line_border',
				'label'     => __( 'Border', 'process-steps' ),
				'selector'  => '{{WRAPPER}} .pswfe-steps-segment:not(:last-child):after, {{WRAPPER}} .pswfe-vertical-steps-segment:not(:last-child):after',
				'condition' => array(
					'cps_hide_show_line!' => 'none',
				),
			)
		);

		$this->end_controls_section();

	}

	// render control for horizontal and vertical page

	protected function render() {
		$settings = $this->get_settings_for_display();

		$layout = $settings['cps_process_layout'];
		require TWAE_PRO_PATH . 'widgets/process-steps/process-steps-layout.php';

	}

	protected function content_template() {         ?>
	<#
	var layout = settings.cps_process_layout;

		#>
		
		<?php require TWAE_PRO_PATH . 'widgets/process-steps/editor-layouts/process-steps-editor-layout.php'; ?>
		<#
	#>
		<?php

	}

}
