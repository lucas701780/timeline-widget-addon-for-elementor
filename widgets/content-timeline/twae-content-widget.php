<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Controls_Manager;
// use Elementor\Scheme_Color;
use Elementor\Core\Schemes\Global_Colors;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
// use Elementor\Scheme_Typography;

class TWAE_PRO_Post_Widget extends \Elementor\Widget_Base {


	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		// run hook on page save and update status
		add_action( 'elementor/editor/after_save', array( $this, 'twae_update_migration_status' ), 10, 2 );

		$min_v   = true;
		$css_ext = '.css';
		$js_ext  = '.js';
		if ( $min_v == true ) {
			$css_ext = '.min.css';
			$js_ext  = '.min.js';
		}

		$js_common_dep = array( 'elementor-frontend' );
		
		if ( !\Elementor\Plugin::$instance->preview->is_preview_mode() && is_user_logged_in()) {
			$js_common_dep = array( 'elementor-common', 'elementor-frontend' );
		}

		// Common styles
		wp_register_style( 'twae-common-css', TWAE_PRO_URL . 'assets/css/twae-common-styles' . $css_ext, array(), TWAE_PRO_VERSION, 'all' );

		// Vertical Timeline
		wp_register_style( 'twae-vertical-css', TWAE_PRO_URL . 'assets/css/twae-vertical-timeline' . $css_ext, array(), TWAE_PRO_VERSION, 'all' );
		wp_register_script( 'twae-vertical-timeline-js', TWAE_PRO_URL . 'assets/js/twae-vertical-timeline' . $js_ext, $js_common_dep, TWAE_PRO_VERSION, true ); // for AOS animation

		// Compact Layout
		wp_register_script( 'twae-masonry-js', TWAE_PRO_URL . 'assets/js/twae-masonry.min.js', $js_common_dep, TWAE_PRO_VERSION, true );
		// Images loaded
		wp_register_script( 'twae-images-loaded-js', TWAE_PRO_URL . 'assets/js/twae-imagesloaded.min.js', $js_common_dep, TWAE_PRO_VERSION, true );

		wp_register_script( 'twae-vertical-compact-js', TWAE_PRO_URL . 'assets/js/twae-vertical-timeline-compact' . $js_ext, $js_common_dep, TWAE_PRO_VERSION, true );

		wp_register_style( 'twae-vertical-compact', TWAE_PRO_URL . 'assets/css/twae-vertical-compact' . $css_ext, array(), TWAE_PRO_VERSION, 'all' );

		// Horizontal Timeline
		wp_register_style( 'twae-horizontal-css', TWAE_PRO_URL . 'assets/css/twae-horizontal-timeline' . $css_ext, array(), TWAE_PRO_VERSION, 'all' );
		wp_register_script( 'twae-horizontal-js', TWAE_PRO_URL . 'assets/js/twae-horizontal-timeline' . $js_ext, $js_common_dep, TWAE_PRO_VERSION, true );

		// popup
		wp_register_script( 'twae-popup-js', TWAE_PRO_URL . 'assets/js/twae-popup' . $js_ext, $js_common_dep, TWAE_PRO_VERSION, true );

		// AOS animation
		wp_register_script( 'twae-aos-js', TWAE_PRO_URL . 'assets/js/twae-aos.min.js', $js_common_dep, TWAE_PRO_VERSION, true );
		wp_register_style( 'twae-aos-css', TWAE_PRO_URL . 'assets/css/twae-aos.min.css', array(), TWAE_PRO_VERSION, 'all' );
		wp_register_style( 'twae-post-pagination', TWAE_PRO_URL . 'assets/css/twae-post-timeline.min.css', array(), TWAE_PRO_VERSION, 'all' );

		// Images slideshow for both vertical and horizontal timeline
		wp_register_script( 'twae-slideshow-js', TWAE_PRO_URL . 'assets/js/twae-slideshow' . $js_ext, $js_common_dep, TWAE_PRO_VERSION, true );

		// Fontello CSS for both vertical and horizontal timeline
		wp_register_style( 'twae-fontello-css', TWAE_PRO_URL . 'assets/css/twae-fontello.css', array(), TWAE_PRO_VERSION, 'all' );

		// Images loaded
		wp_register_script( 'twae-images-loaded-js', TWAE_PRO_URL . 'assets/js/twae-imagesloaded.min.js', $js_common_dep, TWAE_PRO_VERSION, true );

		// Year Navigation bar
		wp_register_script( 'twae-year-navigation-js', TWAE_PRO_URL . 'assets/js/twae-year-navigation' . $js_ext, $js_common_dep, TWAE_PRO_VERSION, true );
		wp_register_style( 'twae-year-navigation', TWAE_PRO_URL . 'assets/css/twae-navigation' . $css_ext, array(), TWAE_PRO_VERSION, 'all' );
	}

	/**
	 * update some settings when user saves Elementor data.
	 *
	 * @since 1.0.0
	 * @param int   $post_id     The ID of the post.
	 * @param array $editor_data The editor data.
	 */
	function twae_update_migration_status( $post_id, $editor_data ) {

		if ( get_option( 'twae-v' ) != false ) {
			if ( get_post_meta( $post_id, 'twae_exists', true ) ) {
				update_post_meta( $post_id, 'twae_post_migration', 'done' );
				update_option( 'twae-post-migration-status', 'done' );
				return;
			}
		}
	}

	public function get_script_depends() {
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			return array( 'twae-horizontal-js', 'twae-aos-js', 'twae-vertical-timeline-js', 'twae-popup-js' );
		}
		$settings  = $this->get_settings_for_display();
		$layout    = $settings['twae_post_layout'];
		$animation = isset( $settings['twae_post_animation'] ) ? $settings['twae_post_animation'] : 'none';

		$script = array( 'twae-images-loaded-js' );

		if ( $layout == 'horizontal' || $layout == 'horizontal-bottom' ) {
			array_push( $script, 'twae-horizontal-js' );
		} elseif ( $layout == 'horizontal-highlighted' ) {
			array_push( $script, 'twae-horizontal-js' );
		} elseif ( $layout == 'compact' ) {
			array_push( $script, 'twae-masonry-js', 'twae-images-loaded-js', 'twae-vertical-compact-js' );
		} else {
			array_push( $script, 'twae-aos-js' );
			array_push( $script, 'twae-vertical-timeline-js' );
		}

		if ( $settings['twae_post_vertical_style'] == 'style-4' || $settings['twae_post_hr_style'] == 'style-4' || $settings['twae_enable_popup'] == 'yes' ) {
			array_push( $script, 'twae-popup-js' );
		}
		return $script;
	}

	public function get_style_depends() {

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			return array( 'twae-common-css', 'twae-vertical-css', 'twae-horizontal-css', 'twae-fontello-css', 'twae-post-timeline-css', 'twae-aos-css', 'twae-post-pagination' );
		}
		$settings  = $this->get_settings_for_display();
		$layout    = $settings['twae_post_layout'];
		$animation = isset( $settings['twae_post_animation'] ) ? $settings['twae_post_animation'] : 'none';

		$styles = array( 'twae-common-css', 'twae-fontello-css', 'twae-post-timeline-css' );

		if ( $layout == 'horizontal' || $layout == 'horizontal-bottom' || $layout == 'horizontal-highlighted' ) {
			array_push( $styles, 'twae-horizontal-css' );
		} elseif ( $layout == 'compact' ) {
			array_push( $styles, 'twae-vertical-css' );
			array_push( $styles, 'twae-vertical-compact' );
			array_push( $styles, 'twae-post-pagination' );
		} else {
			if ( $animation != 'none' ) {
				array_push( $styles, 'twae-aos-css' );
			}
			array_push( $styles, 'twae-vertical-css' );
			array_push( $styles, 'twae-post-pagination' );

		}

		return $styles;
	}


	public function get_name() {
		return 'twae-post-timeline-widget';
	}

	public function get_title() {
		return __( 'Post Timeline', 'twae' );
	}

	public function get_icon() {
		// return 'eicon-time-line';
		return 'eicon-twae-timeline-story';
	}

	public function get_categories() {
		return array( 'twae' );
	}

	// compatibility for < 1.3 versions
	function older_v_compatibility( $post_id, $settings, $timeline_style ) {
		$custom_styles  = '';
		$widgetID       = '.elementor-' . $post_id . ' .elementor-element.elementor-element-' . $this->get_id();
		$selector       = $widgetID . ' .twae-wrapper';
		$custom_styles .= $selector . '.twae-vertical .twae-story{margin-bottom:50px!important}';

		$typo_index = '_typography';
		if ( isset( $settings['twae_post_story_title_color'] ) && $settings['twae_post_story_title_color'] != '' ) {
			$custom_styles .= $selector . '{--tw-cbx-title-color:' . $settings['twae_post_story_title_color'] . ';}';
		}
		if ( isset( $settings['twae_post_date_color'] ) && $settings['twae_post_date_color'] != '' ) {
			$custom_styles .= $selector . '{--tw-lbl-big-color:' . $settings['twae_post_date_color'] . ';}';
		}
		if ( isset( $settings['twae_post_description_color'] ) && $settings['twae_post_description_color'] != '' ) {
			$custom_styles .= $selector . '{--tw-cbx-des-color:' . $settings['twae_post_description_color'] . ';}';
		}
		if ( isset( $settings['twae_post_icon_bgcolor'] ) && $settings['twae_post_icon_bgcolor'] != '' ) {
			$custom_styles .= $selector . '{--tw-ibx-bg:' . $settings['twae_post_icon_bgcolor'] . ';}';
		}
		if ( isset( $settings['twae_post_story_bgcolor'] ) && $settings['twae_post_story_bgcolor'] != '' ) {
			$custom_styles .= $selector . '{--tw-cbx-bg:' . $settings['twae_post_story_bgcolor'] . '}';
		}
		if ( isset( $settings['twae_post_line_color'] ) && $settings['twae_post_line_color'] != '' ) {
			$custom_styles .= $selector . '{--tw-line-bg:' . $settings['twae_post_line_color'] . '}';
		}
		// if(isset($settings['twae_year_label_bgcolor']) && $settings['twae_year_label_bgcolor']!=""){
		// $custom_styles.=$selector.'{--tw-ybx-background:'.$settings['twae_year_label_bgcolor'].'}';
		// }
		if ( $timeline_style == 'style-2' ) {
			if ( isset( $settings['twae_post_el_story_title_color'] ) && $settings['twae_post_el_story_title_color'] != '' ) {
				$custom_styles .= $selector . '{--tw-cbx-title-color:' . $settings['twae_post_el_story_title_color'] . '}';
				$custom_styles .= $selector . ' .twae-icon{--tw-ibx-color:' . $settings['twae_post_el_story_title_color'] . '}';
				$custom_styles .= $selector . '{--tw-cbx-bd-color:' . $settings['twae_post_el_story_title_color'] . ';--tw-arw-bd-color:' . $settings['twae_post_el_story_title_color'] . ';}';
			}
		}

		$title_key = 'twae_post_title_typography';
		if ( isset( $settings[ $title_key . $typo_index ] ) &&
		 $settings[ $title_key . $typo_index ] == 'custom' ) {
			$title_styles   = $this->get_typography_settings( $title_key, $settings );
			$custom_styles .= $widgetID . ' .twae-title{' . $title_styles . '}';
		}
		$label_key = 'twae_post_date_typography';
		if ( isset( $settings[ $label_key . $typo_index ] ) &&
		$settings[ $label_key . $typo_index ] == 'custom' ) {
			$label_styles   = $this->get_typography_settings( $label_key, $settings );
			$custom_styles .= $widgetID . ' .twae-label-big{' . $label_styles . '}';
		}

		$desc_key = 'twae_post_description_typography';
		if ( isset( $settings[ $desc_key . $typo_index ] ) &&
		$settings[ $desc_key . $typo_index ] == 'custom' ) {
			$desc_styles    = $this->get_typography_settings( $desc_key, $settings );
			$custom_styles .= $widgetID . ' .twae-description{' . $desc_styles . '}';
		}

		if ( ! empty( $custom_styles ) ) {
			return '<style type="text/css" id="comp-custom">' . $custom_styles . '</style>';

		} else {
			return false;
		}

	}
	// get an older version style settings
	function get_typography_settings( $key, $all_settings ) {
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
					if ( $all_settings[ $index ]['size'] !== '' ) {
						$unit       = $all_settings[ $index ]['unit'];
						$size       = $all_settings[ $index ]['size'];
						$field_css .= $attribute . ':' . $size . $unit . ';';
					}
				} else {
					$field_css .= $attribute . ':' . $all_settings[ $index ] . ';';
				}
			}
		}
		return $field_css;
	}


		// for frontend
	protected function render() {

		$settings      = $this->get_settings_for_display();
		$post_settings = Twae_Functions::twae_post_timeline_settings( $settings );
		$widget_id     = $this->get_id();
		$count_item    = 1;
		$layout        = $settings['twae_post_layout'];

		$isRTL = is_rtl();
		$dir   = '';
		if ( $isRTL ) {
			$dir = 'rtl';
		}
		if ( $layout == 'horizontal' || $layout == 'horizontal-bottom' || $layout == 'horizontal-highlighted' ) {
			$timeline_style = isset( $settings['twae_post_hr_style'] ) ? $settings['twae_post_hr_style'] : 'style-1';
		} elseif ( $layout == 'compact' ) {
			$timeline_style = isset( $settings['twae_post_vertical_style'] ) ? $settings['twae_post_vertical_style'] : 'style-1';
		} else {
			$timeline_style = isset( $settings['twae_post_vertical_style'] ) ? $settings['twae_post_vertical_style'] : 'style-1';
		}

		$twae_wp_nonce = wp_create_nonce( 'twae_ajax_pagination' );

		if ( $layout == 'horizontal' || $layout == 'horizontal-bottom' || $layout == 'horizontal-highlighted' ) {
			wp_localize_script(
				'twae-horizontal-js',
				"post_timeline_$widget_id",
				array(
					'url'         => admin_url( 'admin-ajax.php' ),
					'private_key' => $twae_wp_nonce,
					'attribute'   => $post_settings,
				)
			);
		} elseif ( $layout == 'compact' ) {
			wp_localize_script(
				'twae-vertical-compact-js',
				"post_timeline_$widget_id",
				array(
					'url'         => admin_url( 'admin-ajax.php' ),
					'private_key' => $twae_wp_nonce,
					'attribute'   => $post_settings,
				)
			);
		} else {
			wp_localize_script(
				'twae-vertical-timeline-js',
				"post_timeline_$widget_id",
				array(
					'url'         => admin_url( 'admin-ajax.php' ),
					'private_key' => $twae_wp_nonce,
					'attribute'   => $post_settings,
				)
			);
		}

		if ( $post_settings['vertical_pagination_type'] == 'ajax_load_more' ) {
			$query = 'before_ajax';
		} else {
			$query = 'simple';
		}

			$enable_popup = '';
		if ( ( isset( $settings['twae_enable_popup'] ) && $settings['twae_enable_popup'] == 'yes' )
			|| $timeline_style == 'style-4' ) {
			$enable_popup = 'yes';
		} else {
			$enable_popup = 'no';
		}
		$query_args = array();

		$query_args['post_type']          = isset( $settings['twae_post_post_type'] ) ? $settings['twae_post_post_type'] : 'post';
		$query_args['order']              = isset( $settings['twae_post_order'] ) ? $settings['twae_post_order'] : 'DESC';
		$query_args['twae_post_category'] = isset( $settings['twae_post_category'] ) ? $settings['twae_post_category'] : '';

		$query_args['twae_post_post_tag'] = isset( $settings['twae_post_post_tag'] ) ? $settings['twae_post_post_tag'] : '';
		$query_args['show_posts']         = ! empty( $settings['twae_post_show_posts'] ) ? $settings['twae_post_show_posts'] : '10';
		$post_types                       = isset( $settings['twae_post_post_type'] ) ? $settings['twae_post_post_type'] : 'post';
		$taxonomies                       = get_object_taxonomies( $post_types );
		foreach ( $taxonomies as $taxonomy => $object ) {
			if ( $object == 'post_format' ) {
				continue;
			}
			$query_args[ 'twae_post_' . $object . '' ] = ! empty( $settings[ 'twae_post_' . $object . '' ] ) ? $settings[ 'twae_post_' . $object . '' ] : '';
		}
			$args                 = Twae_Functions::twae_pro_query_args( $query_args, $query, $page_no = '' );
			$compatibility_styles = '';
		if ( get_option( 'twae-v' ) != false ) {
			global $post;
			$post_id = $post->ID;
			if ( ! get_post_meta( $post_id, 'twae_post_migration', true ) ) {
				update_post_meta( $post_id, 'twae_exists', 'yes' );
				$compatibility_styles .= $this->older_v_compatibility( $post_id, $settings, $timeline_style );
			}
		}
			$twae_bg_type = '';
			// Background Type
		if ( isset( $settings['twae_cbox_background_type'] ) && $settings['twae_cbox_background_type'] == 'multicolor' ) {
			$twae_bg_type = 'twae-bg-multicolor';
		} elseif ( isset( $settings['twae_cbox_background_type'] ) && $settings['twae_cbox_background_type'] == 'gradient' ) {
			$twae_bg_type = 'twae-bg-gradient';
		} else {
			$twae_bg_type = 'twae-bg-simple';
		}

		// Twae content loop file for all layouts.
		require_once TWAE_PRO_PATH . 'widgets/content-timeline/twae-content-loop.php';

		if ( $layout == 'horizontal' ) {
			$timeline_layout_wrapper = 'twae-horizontal-wrapper';

			require TWAE_PRO_PATH . 'widgets/content-timeline/twae-content-horizontal-timeline.php';
		}
				// horizontal bottom timeline conditon
		elseif ( $layout == 'horizontal-bottom' ) {
			$timeline_layout_wrapper = 'twae-horizontal-bottom';
			require TWAE_PRO_PATH . 'widgets/content-timeline/twae-content-horizontal-timeline.php';

		}

				// horizontal highlighted timeline conditon
		elseif ( $layout == 'horizontal-highlighted' ) {
			$timeline_layout_wrapper = 'twae-horizontal-highlighted-timeline';
			require TWAE_PRO_PATH . 'widgets/content-timeline/twae-content-horizontal-timeline.php';

		} else {
				$timeline_layout_wrapper = 'twae-both-sided';
			if ( $layout == 'one-sided' ) {
				$timeline_layout_wrapper = 'twae-vertical-right';
			}

				// add left vertical layout
			if ( $layout == 'left-sided' ) {
				$timeline_layout_wrapper = 'twae-vertical-left';
			}
			require TWAE_PRO_PATH . 'widgets/content-timeline/twae-content-vertical-timeline.php';
		}

		if ( ! empty( $compatibility_styles ) ) {
			echo $compatibility_styles;
		}
	}

		// for live editor
	protected function content_template() {

	}

	protected function register_controls() {

		$this->post_query_settings();

		$this->start_controls_section(
			'twae_post_layout_section',
			array(
				'label' => __( 'Layout Settings', 'twae' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'twae_post_layout',
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
			'twae_post_vertical_style',
			array(
				'type'    => \Elementor\Controls_Manager::HIDDEN,
				'default' => 'style-1',
			)
		);
		// Horizontal Layout Styles (OLD VERSION)
		$this->add_control(
			'twae_post_hr_style',
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
				 'options'    => array(
					 'v-style-0' => 'Default',
					 'v-style-1' => 'Classic',
					 'v-style-2' => 'Elegant',
					 'v-style-3' => 'Clean',
					 'v-style-4' => 'Minimal',
					 'v-style-5' => 'Bold',
					 'v-style-6' => 'Flat',
				 ),
				'condition'   => array(
					'twae_post_layout!' => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);
		// Horizontal Preset Styles
		$this->add_control(
			'twae_preset_hr_style',
			array(
				'label'       => __( 'Preset Styles', 'twae' ),
				'description' => __( '!! Preset styles will completely change your current style settings, if you have already selected any style settings.', 'twae' ),
				'type'        => 'twae_preset_style',
				'default'     => 'h-style-0',
				// 'options' => $options,
				 'options'    => array(
					 'h-style-0' => 'Default',
					 'h-style-1' => 'Classic',
					 'h-style-2' => 'Elegant',
					 'h-style-3' => 'Clean',
					 'h-style-4' => 'Minimal',
					 'h-style-5' => 'Flat',
				 ),
				'condition'   => array(
					'twae_post_layout' => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
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
					'twae_post_layout' => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);

		$this->add_control(
			'twae_post_slides_to_show',
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
					'twae_post_layout' => array( 'horizontal', 'horizontal-bottom' ),
				),

			)
		);
		$this->add_control(
			'twae_highlighted_slides_to_show',
			array(
				'label'     => esc_html__( 'Show Post', 'twea1' ),
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
					'twae_post_layout' => array( 'horizontal-highlighted' ),
				),

			)
		);
		$this->add_control(
			'twae_post_highlighted_active_color',
			array(
				'label'     => esc_html__( 'Post Active Color', 'twea1' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ff8571',
				'selectors' => array(
					'{{WRAPPER}} .twae-horizontal-highlighted-timeline' => '--tw-highlighted-color: {{VALUE}}',
				),
				'condition' => array(
					'twae_post_layout' => array( 'horizontal-highlighted' ),
				),
			)
		);
		// Horizontal Slides To Show

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
					'twae_post_layout' => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
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
					'twae_post_layout' => array( 'horizontal', 'horizontal-bottom' ),
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
					'twae_post_layout' => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
				// 'separator' => 'before',
			)
		);
		// Horizontal Slides Autoplay
		$this->add_control(
			'twae_post_autoplay',
			array(
				'label'     => __( 'Autoplay', 'twae' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'false',
				'options'   => array(
					'true'  => 'True',
					'false' => 'False',
				),
				'condition' => array(
					'twae_post_layout' => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);
		$this->add_control(
			'twae_post_autoplaystop_mousehover',
			array(
				'label'        => __( 'Autoplay Pause On Hover', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'on', 'twae' ),
				'label_off'    => __( 'off', 'twae' ),
				'return_value' => 'true',
				'default'      => 'false',
				'condition'    => array(
					'twae_post_autoplay' => array( 'true' ),
					'twae_post_layout'   => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);
		$this->add_control(
			'twae_speed',
			array(
				'label'       => esc_html__( 'Animation Speed', 'twea1' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'min'         => 900,
				'max'         => 10000,
				'step'        => 100,
				'default'     => 1000,
				'description' => 'Slide speed in milliseconds',
				'condition'   => array(
					'twae_post_layout' => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
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
					'twae_post_layout' => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
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
					'twae_post_layout' => array(
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
		// Show Icon (OLD VERSION)
		$this->add_control(
			'twae_post_show_icon',
			array(
				'type'    => \Elementor\Controls_Manager::HIDDEN,
				'default' => 'yes',
			)
		);
		// Display Icons, Dot or None
		$this->add_control(
			'twae_post_icon_type',
			array(
				'label'   => esc_html__( 'Display Icons', 'twae' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => array(
					'custom'      => array(
						'title' => esc_html__( 'Icons', 'twae' ),
						'icon'  => 'eicon-clock',
					),
					'displaydots' => array(
						'title' => esc_html__( 'Dots', 'twae' ),
						'icon'  => 'eicon-circle',
					),
					'displaynone' => array(
						'title' => esc_html__( 'None', 'twae' ),
						'icon'  => 'eicon-ban',
					),
				),
				'toggle'  => true,
			)
		);
		$this->add_control(
			'twae_post_custom_icon',
			array(
				'label'     => __( 'Font Awesome Icon', 'twae' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'far fa-clock',
					'library' => 'fa-regular',
				),
				'condition' => array(
					'twae_post_icon_type!' => array( 'displaydots', 'displaynone' ),
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
					'twae_post_layout!' => array( 'compact', 'horizontal-highlighted' ),
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
			)
		);
		// Image Size
		$this->add_control(
			'twae_post_image_size',
			array(
				'label'   => __( 'Image Size', 'twae' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'large',
				'options' => array(
					'thumbnail' => 'Thumbnail',
					'medium'    => 'Medium',
					'large'     => 'Large',
					'full'      => 'Full (original size)',
				),
			)
		);
		// Show Content in Popup
		$this->add_control(
			'twae_enable_popup',
			array(
				'label'   => __( 'Content In Popup', 'twae' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'no',
				'options' => array(
					'yes' => 'Yes',
					'no'  => 'No',
				),
			)
		);
		// Show Animations
		$animations = Twae_Functions::twae_pro_animation_array();
		$this->add_control(
			'twae_post_animation',
			array(
				'label'     => __( 'Animations', 'twae' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => $animations,
				'condition' => array(
					'twae_post_layout!' => array(
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
			'twae_post_image_outside_box',
			array(
				'label'        => esc_html__( 'Image Out Of The Box', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'twae' ),
				'label_off'    => __( 'Hide', 'twae' ),
				'return_value' => 'twae_image_outside',
				'default'      => 'no',
				'condition'    => array(
					'twae_post_layout'  => array( 'centered' ),
					'twae_enable_popup' => 'no',
				),
			)
		);
		// Read More Button
		$this->add_control(
			'twae_post_read_more',
			array(
				'label'        => __( 'Read More Button', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'twae' ),
				'label_off'    => __( 'Hide', 'twae' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'twae_post_desc' => array(
						'summary',
					),
				),
			)
		);
		// Read More Button Text
		$this->add_control(
			'twae_post_readmore_text',
			array(
				'label'     => __( 'Read More Text', 'twae' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => 'Read More',
				'condition' => array(
					'twae_post_desc'      => 'summary',
					'twae_post_read_more' => 'yes',
				),
			)
		);

		$this->add_control(
			'twae_post_content_side_by_side',
			array(
				'label'        => __( 'Content Side By Side', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'twae' ),
				'label_off'    => __( 'Hide', 'twae' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'twae_post_layout' => array(
						'horizontal-highlighted',
					),
				),
			)
		);

		$this->add_control(
			'twae_post_image_lightbox_settings',
			array(
				'label'        => esc_html__( 'Image Pop Up', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'twae' ),
				'label_off'    => __( 'Hide', 'twae' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);
		$this->end_controls_section();

		$this->twae_line_settings();
		$this->story_icon_style_settings();
		$this->story_yld_settings();
		$this->twae_cbox_settings();
		$this->twae_storycontent_settings();
		$this->twae_popup_post_setting();
		$this->twae_pagination_setting();

	}
	/* ------------------- Post controls ------------------ */


	public function post_query_settings() {
		$this->start_controls_section(
			'twae_post_general_section',
			array(
				'label' => __( 'Timeline Content Query', 'twae' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'twae_post_post_type',
			array(
				'label'   => __( 'Post Type', 'twae' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'post',
				'options' => Twae_Functions::twae_pro_get_post_types(),
			)
		);

		$post_types = Twae_Functions::twae_pro_get_post_types();

		$taxonomies = get_taxonomies( array(), 'objects' );

		foreach ( $taxonomies as $taxonomy => $object ) {
			if ( ! isset( $object->object_type[0] ) || ! in_array( $object->object_type[0], array_keys( $post_types ) ) ) {
				continue;
			}
			if ( $taxonomy == 'post_format' ) {
				continue;
			}

			$this->add_control(
				'twae_post_' . $taxonomy . '',
				array(
					'label'       => $object->label,
					'type'        => \Elementor\Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple'    => true,
					'object_type' => $taxonomy,
					'options'     => wp_list_pluck( get_terms( $taxonomy ), 'name', 'term_id' ),
					'condition'   => array(
						'twae_post_post_type' => $object->object_type,
					),
				)
			);
		}

		$this->add_control(
			'twae_post_date',
			array(
				'label'   => __( 'Post date/ </br>Custom Meta content', 'twae' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'published',
				'options' => array(
					'published' => 'Published Date',
					'custom'    => 'Custom',
				),
			)
		);

		$this->add_control(
			'twae_post_custom_metakey',
			array(
				'label'     => __( 'Custom Meta Key', 'twae' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				// 'default' => '',
				'condition' => array(
					'twae_post_date' => 'custom',
				),
			)
		);

		$this->add_control(
			'twae_post_order',
			array(
				'label'   => __( 'Order', 'twae' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => array(
					'DESC' => 'DESC',
					'ASC'  => 'ASC',
				),
			)
		);

		$this->add_control(
			'twae_post_date_format',
			array(
				'label'   => __( 'Date Formats', 'twae' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'j F',
				'options' => array(
					'j F'         => date_i18n( 'j F' ),
					'F j'         => date_i18n( 'F j' ),
					'F j Y'       => date_i18n( 'F j Y' ),
					'Y-m-d'       => date_i18n( 'Y-m-d' ),
					'm/d/Y'       => date_i18n( 'm/d/Y' ),
					'd/m/Y'       => date_i18n( 'd/m/Y' ),
					'F j Y g:i A' => date_i18n( 'F j Y g:i A' ),
					'Y'           => date_i18n( 'Y' ),
					'custom'      => __( 'Custom', 'twae' ),
				),
			)
		);

		$this->add_control(
			'twae_post_custom_date_format',
			array(
				'label'       => __( 'Custom Date Format', 'twae' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'j M',
				'condition'   => array(
					'twae_post_date_format' => array(
						'custom',
					),
				),
				'description' => 'Please check custom <a target="_blank" href="https://wordpress.org/support/article/formatting-date-and-time/"> Date and Time Formats</a>',
			)
		);

		$this->add_control(
			'twae_post_desc',
			array(
				'label'   => __( 'Post Description', 'twae' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'summary',
				'options' => array(
					'summary' => 'Summary',
					'full'    => 'Full',
				),
			)
		);

		$this->add_control(
			'twae_post_desc_length',
			array(
				'label'     => __( 'Description Length', 'twae' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'default'   => 55,
				'condition' => array(
					'twae_post_desc' => array(
						'summary',
					),
				),
			)
		);

		$this->add_control(
			'twae_post_show_posts',
			array(
				'label'   => __( 'Posts Per Page', 'twae' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'step'    => 1,
				'default' => 10,
			)
		);

		$this->add_control(
			'twae_post_imp_note',
			array(
				'label'     => __( 'The field value (Posts Per Page) has to be greater than the value in Slides To Show option.', 'twae' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'condition' => array(
					'twae_post_layout' => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);
		$this->add_control(
			'twae_post_hr_ajax_loadmore',
			array(
				'label'        => __( 'Enable Ajax Load More', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'twae' ),
				'label_off'    => __( 'No', 'twae' ),
				'return_value' => 'yes',
				'description'  => __( 'Disabling this option will load all items at the time of page load.', 'twae' ),
				'default'      => 'no',
				'condition'    => array(
					'twae_post_layout' => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);

		$this->add_control(
			'twae_post_pagination_type',
			array(
				'label'       => __( 'Pagination Type', 'twae' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'default',
				'description' => __( 'Ajax Load More button will not work on editor side, please check on frontend', 'twae' ),
				'options'     => array(
					'default'        => 'Default',
					'ajax_load_more' => 'Ajax load More',
				),
				'condition'   => array(
					'twae_post_layout!' => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);

		// default pagination

		$this->add_control(
			'twae_post_page_text_change',
			array(
				'label'     => __( 'Page Text', 'twae' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => 'Page ',
				'condition' => array(
					'twae_post_pagination_type' => 'default',
					'twae_post_layout!'         => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),

				),
			)
		);

		$this->add_control(
			'twae_post_of_text_change',
			array(
				'label'     => __( 'Of Text', 'twae' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => 'of ',
				'condition' => array(
					'twae_post_pagination_type' => 'default',
					'twae_post_layout!'         => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);

		// default pagination end

		// ajax load more

		$this->add_control(
			'twae_post_load_more_change',
			array(
				'label'     => __( 'Load More Text', 'twae' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => 'Load More',
				'condition' => array(
					'twae_post_pagination_type' => 'ajax_load_more',
					'twae_post_layout!'         => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),

				),
			)
		);

		// ajax load more text end

		$this->end_controls_section();

	}

	/*
	Common controls
	*/



	/* ------------------------------ Line Settings ----------------------------- */
	function twae_line_settings() {
		// Line Section Start
		$this->start_controls_section(
			'twae_line_section',
			array(
				'label' => __( '📍 Line Settings', 'twae' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		// Line Width
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
			'twae_post_line_color',
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
			'twae_post_icon_bgcolor',
			array(
				'label'     => __( 'Icon / Dot Background', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}} .twae-navigationBar' => '--tw-ibx-bg: {{VALUE}}',
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
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}} .twae-navigationBar' => '--tw-ibx-color: {{VALUE}}',
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


	/* ---------------------- Date / Year Settings --------------------- */
	function story_yld_settings() {
		// Date / Year Section
		$this->start_controls_section(
			'twae_yld_section',
			array(
				'label' => __( '📆 Date / Year', 'twae' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		// Primary Label Color / Date Color
		$this->add_control(
			'twae_post_date_color',
			array(
				'label'     => __( 'Label Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-wrapper' => '--tw-lbl-big-color: {{VALUE}}',
				),
			)
		);
		// Primary Label Typo / Date Typo
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'twae_post_date_typography',
				'label'    => __( 'Label Typography', 'twae' ),
				'selector' => '{{WRAPPER}} .twae-label-big,{{WRAPPER}} .twae_icon_text',
				'exclude'  => array( 'line_height', 'font_size', 'letter_spacing', 'word_spacing' ),
			)
		);
		// Primary Label Size / Date Size
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
		$this->add_control(
			'twae_post_label_content_top',
			array(
				'label'        => __( 'Label Top Of The Content', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_bg_on'  => __( 'On', 'twae' ),
				'label_bg_off' => __( 'Off', 'twae' ),
				'return_value' => 'twae-label-content-top',
				'default'      => 'no',
				'condition'    => array(
					'twae_post_label_inside!' => 'twae-label-content-inside',
					'twae_post_layout!'       => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);
		$this->add_control(
			'twae_post_label_inside',
			array(
				'label'        => __( 'Label Inside The Content', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_bg_on'  => __( 'On', 'twae' ),
				'label_bg_off' => __( 'Off', 'twae' ),
				'return_value' => 'twae-label-content-inside',
				'default'      => 'no',
				'conditions'   => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'twae_post_label_content_top',
							'operator' => '!=',
							'value'    => 'twae-label-content-top',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'twae_post_layout',
									'operator' => '==',
									'value'    => 'horizontal',
								),
								array(
									'name'     => 'twae_post_layout',
									'operator' => '==',
									'value'    => 'horizontal-bottom',
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
				'label_bg_off' => __( 'Hhow', 'twae' ),
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
					'twae_label_background' => 'yes',
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
			'twae_post_lablel_bd_radius',
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
		// Date / Year Section - END
		$this->end_controls_section();
	}
	/* ----------------------- Date / Year Settings - END ----------------------- */


	/* --------------------------- Content Box Settings --------------------------- */
	function twae_cbox_settings() {
		// Content Box Section Start
		$this->start_controls_section(
			'twae_cbox_section',
			array(
				'label' => __( '🔳 Content Background / Border', 'twae' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		// Content Box Padding
		$this->add_control(
			'twae_cbox_padding',
			array(
				'label'      => __( 'Padding', 'twae' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .twae-wrapper' =>
					'--tw-cbx-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
			'twae_post_story_bgcolor',
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
	function twae_storycontent_settings() {
		// Title Section Start
		$this->start_controls_section(
			'twae_title_section',
			array(
				'label' => __( '✍ Title', 'twae' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		// Title Tag
		/*
		$this->add_control(
			'title_tag',
			[
				'label' => __('Title Tag', 'twae'),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'default' => 'div',
				'options' => [
					'div' => [
						'title' => __('Div', 'twae'),
						'icon' => 'DIV'
					],
					'h1' => [
						'title' => __('H1', 'twae'),
						'icon' => 'eicon-editor-h1'
					],
					'h2' => [
						'title' => __('H2', 'twae'),
						'icon' => 'eicon-editor-h2'
					],
					'h3' => [
						'title' => __('H3', 'twae'),
						'icon' => 'eicon-editor-h3'
					],
					'h4' => [
						'title' => __('H4', 'twae'),
						'icon' => 'eicon-editor-h4'
					],
					'h5' => [
						'title' => __('H5', 'twae'),
						'icon' => 'eicon-editor-h5'
					],
					'h6' => [
						'title' => __('H6', 'twae'),
						'icon' => 'eicon-editor-h6'
					]

				],
				'toggle' => false,
			]
		);*/
		// Title Color For Elegent Style (OLD VERSION)
		$this->add_control(
			'twae_post_el_story_title_color',
			array(
				'type'    => \Elementor\Controls_Manager::HIDDEN,
				'default' => '#ffffff',
			)
		);
		// Title Color
		$this->add_control(
			'twae_post_story_title_color',
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
			'twae_post_story_title_bgcolor',
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
				'name'               => 'twae_post_title_typography',
				'label'              => __( 'Typography', 'twae' ),
				'selector'           => '{{WRAPPER}} .twae-title',
				'frontend_available' => true,
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
			'twae_post_description_color',
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
				'name'     => 'twae_post_description_typography',
				'label'    => __( 'Typography', 'twae' ),
				'selector' => '{{WRAPPER}} .twae-description, {{WRAPPER}} .twae-button a, {{WRAPPER}} .twae-button button.elementor-button',
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
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}}-popup .twae-media img' => '--tw-cbx-img-width: {{SIZE}}{{UNIT}};',
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
				/*
				'default' => [
					'size' => 100,
					'unit' => '%',
				],*/
				'selectors'   => array(
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}}-popup .twae-media img' => '--tw-cbx-img-height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}}-popup .twae-media img' =>
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
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}}-popup .twae-media img' =>
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
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}}-popup .twae-media img' => '--tw-cbx-img-border-style: {{VALUE}}',
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
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}}-popup .twae-media img' => '--tw-cbx-img-border-color: {{VALUE}}',
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
					'{{WRAPPER}} .twae-wrapper, {{WRAPPER}}-popup .twae-media img' => '--tw-cbx-img-border-width: {{SIZE}}px',
				),
				'condition' => array(
					'twae_image_border_type!'   => 'none',
					'twae_image_border_popover' => 'yes',
				),
			)
		);
		$this->end_popover();

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
					'twae_enable_popup' => 'no',
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
					'twae_enable_popup'                => 'no',
				),
			)
		);
		$this->end_popover();
		$this->add_control(
			'twae_post_image_hover_effect',
			array(
				'label'        => esc_html__( 'Image Effect', 'twae' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_bg_on'  => __( 'Show', 'twae' ),
				'label_bg_off' => __( 'Hhow', 'twae' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);
		// image box shadow box end
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



	/*------------popup content settings start ----------------------- */
	function twae_popup_post_setting() {
		$this->start_controls_section(
			'twae_popup_content_style_settings',
			array(
				'label'     => __( '📜 Pop Up', 'twae' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'twae_enable_popup' => 'yes',
				),
			)
		);

			// popup Styles Notice
			$this->add_control(
				'twae_popup_content_notice',
				array(
					'label'     => __( 'Before applying these styles, Please click on any story and open popup.', 'twae' ),
					'type'      => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

		   // Description bg color
		$this->add_control(
			'twae_popup_content_story_description_bgcolor',
			array(
				'label'     => __( 'Background Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}-popup .twae-popup-content' => '--tw-cbx-bg: {{VALUE}}',
				),
				'condition' => array(
					'twae_enable_popup' => 'yes',
				),
			)
		);

		// Title color
		$this->add_control(
			'twae_popup_content_title_color',
			array(
				'label'     => __( 'Title Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}-popup .twae-popup-content .twae-title' => '--tw-cbx-title-color: {{VALUE}} !important',
				),
				'condition' => array(
					'twae_enable_popup' => 'yes',
				),
			)
		);

		// Title Typography
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'twae_popup_content_title_typography',
				'label'     => __( 'Title Typography', 'twae' ),
				'selector'  => '{{WRAPPER}}-popup .twae-popup-content .twae-title',
				'condition' => array(
					'twae_enable_popup' => 'yes',
				),
			)
		);

		// Description color
		$this->add_control(
			'twae_popup_content_description_color',
			array(
				'label'     => __( 'Description Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}-popup .twae-popup-content .twae-description' => '--tw-cbx-des-color: {{VALUE}}',
				),
				'condition' => array(
					'twae_enable_popup' => 'yes',
				),
			)
		);

		// Description typography
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'twae_popup_content_description_typography',
				'label'     => __( 'Description Typography', 'twae' ),
				'selector'  => '{{WRAPPER}}-popup .twae-popup-content .twae-description',
				'condition' => array(
					'twae_enable_popup' => 'yes',
				),

			)
		);
		 // alignment
		$this->add_control(
			'twae_post_popup_content_alignment',
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
					'twae_enable_popup' => 'yes',
				),
			)
		);

		// container padding
		$this->add_control(
			'twae_popup_post_content_container_alignment',
			array(
				'label'      => __( 'Padding', 'twae' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}}-popup .twae-popup-content' =>
					'--tw-cbx-cont-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'twae_enable_popup' => 'yes',
				),
			)
		);

		$this->end_controls_section();

	}
	/**--------popup content settings end----------- */


	function twae_pagination_setting() {

		$this->start_controls_section(
			'twae_default_and_load_more_pagination_section',
			array(
				'label'      => __( '🏴 Pagination / Load More', 'twae' ),
				'tab'        => \Elementor\Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'twae_post_layout',
							'operator' => '!==',
							'value'    => 'horizontal',
						),
						array(
							'name'     => 'twae_post_layout',
							'operator' => '!==',
							'value'    => 'horizontal-bottom',
						),
						array(
							'name'     => 'twae_post_layout',
							'operator' => '!==',
							'value'    => 'horizontal-highlighted',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'twae_post_pagination_type',
									'operator' => '==',
									'value'    => 'ajax_load_more',
								),
								array(
									'name'     => 'twae_post_pagination_type',
									'operator' => '==',
									'value'    => 'default',
								),
							),
						),
					),
				),
			)
		);

		// heading for default pagination
		$this->add_control(
			'twae_default_pagination_heading',
			array(
				'label'     => __( '🔶 Default Pagination', 'twae' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				// 'separator' => 'before',
				'condition' => array(
					'twae_post_pagination_type' => 'default',
					'twae_post_layout!'         => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);

		// default pagination Tab
		$this->start_controls_tabs(
			'twae_default_pagination_tab',
			array(
				'separator' => 'before',
				'condition' => array(
					'twae_post_pagination_type' => 'default',
					'twae_post_layout!'         => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);
		// default pagination Normal Tab
		$this->start_controls_tab(
			'twae_default_pagination_normal_tab',
			array(
				'label'     => esc_html__( 'Normal', 'twae' ),
				'condition' => array(
					'twae_post_pagination_type' => 'default',
					'twae_post_layout!'         => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);

		// outide text color of page 1 of 3 .
		$this->add_control(
			'twae_post_outside_text_color',
			array(
				'label'     => __( 'Outside Text Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-page-numbers.twae-page-num' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'twae_post_pagination_type' => 'default',
					'twae_post_layout!'         => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);
		// default text color
		$this->add_control(
			'twae_post_page_color',
			array(
				'label'     => __( 'Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-custom-pagination a.page-numbers' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'twae_post_pagination_type' => 'default',
					'twae_post_layout!'         => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);

		// default bg color
		$this->add_control(
			'twae_post_page_bg_color',
			array(
				'label'     => __( 'Background Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-custom-pagination a.page-numbers' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'twae_post_pagination_type' => 'default',
					'twae_post_layout!'         => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);

		$this->end_controls_tab();

		// default pagination  Hover Tab

		$this->start_controls_tab(
			'twae_defaul_pagination_hover_tab',
			array(
				'label'     => esc_html__( 'Hover', 'twae' ),
				'condition' => array(
					'twae_post_pagination_type' => 'default',
					'twae_post_layout!'         => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);

			// outide text hover color of page 1 of 3 .
			$this->add_control(
				'twae_post_outside_text_hover_color',
				array(
					'label'     => __( 'Outside Text Color', 'twae' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .twae-page-numbers.twae-page-num:hover' => 'color: {{VALUE}}',
					),
					'condition' => array(
						'twae_post_pagination_type' => 'default',
						'twae_post_layout!'         => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
					),
				)
			);

		// default pagination active color text
		$this->add_control(
			'twae_post_active_page_color',
			array(
				'label'     => __( 'Active/Hover Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-custom-pagination span.current,
				{{WRAPPER}} .twae-custom-pagination a.page-numbers:hover' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'twae_post_pagination_type' => 'default',
					'twae_post_layout!'         => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);

		// default pagination bg active color
		$this->add_control(
			'twae_post_page_active_bg_color',
			array(
				'label'     => __( 'Active/Hover Background Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-custom-pagination span.current,
				{{WRAPPER}} .twae-custom-pagination a.page-numbers:hover' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'twae_post_pagination_type' => 'default',
					'twae_post_layout!'         => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		/**  load more pagination start */

		// load more heading
		$this->add_control(
			'twae_load_more_heading',
			array(
				'label'     => __( '🔶 Load More', 'twae' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				// 'separator' => 'before',
				'condition' => array(
					'twae_post_pagination_type' => 'ajax_load_more',
					'twae_post_layout!'         => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);

		// tabs start
		$this->start_controls_tabs(
			'twae_load_more_pagination_tabs',
			array(
				'separator' => 'before',
				'condition' => array(
					'twae_post_pagination_type' => 'ajax_load_more',
					'twae_post_layout!'         => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),

				),
			)
		);

		// start normal tab
		$this->start_controls_tab(
			'twae_load_more_normal_tab',
			array(
				'label'     => esc_html__( 'Normal', 'twae' ),
				'condition' => array(
					'twae_post_pagination_type' => 'ajax_load_more',
					'twae_post_layout!'         => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);

		// load more text color
		$this->add_control(
			'twae_post_ajax_text_color',
			array(
				'label'     => __( 'Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-ajax-load-more.twae-button' => '    --tw-cbx-btn-color: {{VALUE}}',
				),
				'condition' => array(
					'twae_post_pagination_type' => 'ajax_load_more',
					'twae_post_layout!'         => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);

		// load more bg color
		$this->add_control(
			'twae_post_ajax_bg_color',
			array(
				'label'     => __( 'Background Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-ajax-load-more.twae-button' => '--tw-cbx-btn-bgcolor: {{VALUE}}',
				),
				'condition' => array(
					'twae_post_pagination_type' => 'ajax_load_more',
					'twae_post_layout!'         => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);

		// load more border type
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'      => 'border',
				'selector'  => '{{WRAPPER}} .twae-ajax-load-more.twae-button button',
				'condition' => array(
					'twae_post_pagination_type' => 'ajax_load_more',
					'twae_post_layout!'         => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);

		$this->end_controls_tab();

		// start hover tab
		$this->start_controls_tab(
			'twae_load_more_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'twae' ),
			)
		);

		// load more hover text color
		$this->add_control(
			'twae_post_ajax_text_hover_color',
			array(
				'label'     => __( 'Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-ajax-load-more.twae-button:hover' => '    --tw-cbx-btn-color: {{VALUE}}',
				),
				'condition' => array(
					'twae_post_pagination_type' => 'ajax_load_more',
					'twae_post_layout!'         => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);

		// load more hover bg color
		$this->add_control(
			'twae_post_ajax_bg_hover_color',
			array(
				'label'     => __( 'Background Color', 'twae' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .twae-ajax-load-more.twae-button:hover' => '--tw-cbx-btn-bgcolor: {{VALUE}}',
				),
				'condition' => array(
					'twae_post_pagination_type' => 'ajax_load_more',
					'twae_post_layout!'         => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);

		// load more hover border type
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'      => 'border-hover',
				'selector'  => '{{WRAPPER}} .twae-ajax-load-more.twae-button button:hover',
				'condition' => array(
					'twae_post_pagination_type' => 'ajax_load_more',
					'twae_post_layout!'         => array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ),
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
		/**--------------- load more pagination end ---------------------*/

	}






}

