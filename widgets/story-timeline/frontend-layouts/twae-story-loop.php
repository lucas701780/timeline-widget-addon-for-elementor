<?php
if ( ! class_exists( 'Twae_Story_Loop' ) ) {
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}
	/**
	 * Class Twae_Story_Loop
	 *
	 * This class handles the story loop functionality.
	 */
	class Twae_Story_Loop {
		/**
		 * The story content.
		 *
		 * @var array
		 */
		private $story_data;

		/**
		 * The render attributes for the story loop repeater.
		 *
		 * @var array
		 */
		private $render_repeater_attr = array();

		/**
		 * The repeater key for the story loop.
		 *
		 * @var array
		 */
		private $repeater_key = array();

		/**
		 * The settings for the story loop.
		 *
		 * @var array
		 */
		private $settings = array();

		/**
		 * The story settings.
		 *
		 * @var array
		 */
		private $story_settings = array();

		/**
		 * The enable popup flag.
		 *
		 * @var string
		 */
		private $enable_popup = '';

		/**
		 * The title tag.
		 *
		 * @var string
		 */
		private $title_tag = '';

		/**
		 * Constructor for Twae_Story_Loop class.
		 *
		 * @param array $settings The settings for the story loop.
		 */
		public function __construct( $settings ) {
			// Set the settings for the story loop.
			$this->settings  = $settings;
			$this->title_tag = 'div';
		}

		/**
		 * Handles the story loop functionality.
		 *
		 * @param array  $content               The content for the story loop.
		 * @param array  $story_settings        The story settings for the story loop.
		 * @param array  $repeater_key          The repeater key for the story loop.
		 * @param array  $render_repeater_attr  The render attributes for the story loop.
		 * @param string $enable_popup          The enable popup flag.
		 */
		public function twae_story_loop( $content, $story_settings, $repeater_key, $render_repeater_attr, $enable_popup ) {
			$this->story_data           = $content;
			$this->repeater_key         = $repeater_key;
			$this->render_repeater_attr = $render_repeater_attr;
			$this->story_settings       = $story_settings;
			$this->enable_popup         = $enable_popup;
		}

		/**
		 * Returns the HTML for the story title.
		 *
		 * @param bool $popup Whether the story title is a popup.
		 * @return string The HTML for the story title.
		 */
		public function twae_story_title( $popup ) {
			$title_html     = '';
			$story_settings = $this->story_settings;
			$title_key      = $this->repeater_key['title_key'];
			$title_attr     = isset( $this->render_repeater_attr[ $title_key ] ) ? $this->render_repeater_attr[ $title_key ] : '';
			// Check if the story link is enabled and not a popup, then add the link tag.
			if ( ! empty( $story_settings['timeline_story_title'] ) ) {
				$title_html .= '<' . esc_html( $this->title_tag ) . ' ' . $title_attr . '>';
				if ( 'yes' === $story_settings['enable_link'] && ! empty( $story_settings['story_link'] ) && ! $popup ) {
					$title_html .= '<a  href="' . esc_url( $story_settings['story_link'] ) . '" ' . $story_settings['story_link_target'] . ' ' . $story_settings['story_link_nofollow'] . '>';
				} elseif ( $popup && 'yes' === $this->enable_popup ) {
					$title_html .= '<a href="#twae-popup-' . esc_attr( $this->story_data['_id'] ) . '" class="twae-popup-links">';
				}

				// Add the timeline story title to the title HTML.
				$title_html .= wp_kses_post( $story_settings['timeline_story_title'] );
				// Check if the story link is enabled and not a popup, then close the link tag.
				if ( ( 'yes' === $story_settings['enable_link'] && ! empty( $story_settings['story_link'] ) && 'no' === $this->enable_popup ) || 'yes' === $this->enable_popup ) {
					$title_html .= '</a>';
				}
				$title_html .= '</' . esc_html( $this->title_tag ) . '>';
			}
			return $title_html;
		}

		/**
		 * Returns the HTML for the story description.
		 *
		 * @return string The HTML for the story description.
		 */
		public function twae_story_desc() {
			$description_key  = $this->repeater_key['desc_key'];
			$description_attr = isset( $this->render_repeater_attr[ $description_key ] ) ? $this->render_repeater_attr[ $description_key ] : '';
			$description_html = '<div ' . $description_attr . '>' . wp_kses_post( $this->story_settings['timeline_description'] ) . $this->twae_story_btn() . '</div>';
			return $description_html;
		}

		/**
		 * Returns the HTML for the story icon.
		 *
		 * @return array The HTML and class for the story icon.
		 */
		public function twae_story_icon() {
			$icon_html     = '';
			$icon_cls      = '';
			$display_icons = isset( $this->settings['twae_display_icons'] ) ? $this->settings['twae_display_icons'] : 'displayicons';
			$icon_type     = $this->story_settings['icon_type'];
			$display_icon  = $this->story_settings['display_icon'];
			$story_icon    = $this->story_settings['story_icon'];
			if ( 'none' === $icon_type ) {
				$icon_cls = 'twae-story-no-dot';
			} elseif ( 'dot' === $icon_type || ( empty( $display_icon ) && 'icon' !== $icon_type && 'image' !== $icon_type && 'customtext' !== $icon_type && 'none' !== $icon_type ) ) {
				$icon_cls  = 'twae-story-no-icon';
				$icon_html = '<div class="twae-icondot"></div>';
			} elseif ( ( empty( $icon_type ) && 'far fa-clock' !== $story_icon && ! empty( $story_icon ) ) || 'icon' === $icon_type || 'image' === $icon_type || 'customtext' === $icon_type ) {
				$icon_cls       = 'twae-story-icon';
				$icon_html     .= '<div class="twae-icon">';
					$icon       = Twae_Functions::twae_get_icon_placeholder_content( $this->story_data );
					$icon_html .= $icon;
				$icon_html     .= '</div>';
			} elseif ( 'displaynone' === $display_icons ) {
				$icon_cls = 'twae-story-no-dot';
			} elseif ( 'displaydots' === $display_icons ) {
				$icon_cls  = 'twae-story-no-icon';
				$icon_html = '<div class="twae-icondot"></div>';
			} else {
				$icon_cls   = 'twae-story-icon';
				$icon_html .= '<div class="twae-icon">';
					ob_start();
					\Elementor\Icons_Manager::render_icon( $this->settings['twae_story_icons'], array( 'aria-hidden' => 'true' ) );
					$render_icon = ob_get_contents();
					ob_end_clean();
					$icon_html .= $render_icon;
				$icon_html     .= '</div>';
			}

			$icon = array(
				'icon_html' => $icon_html,
				'icon_cls'  => $icon_cls,
			);

			return $icon;
		}

		/**
		 * Returns the HTML for the story label.
		 *
		 * @param string $animation The animation type for the story label.
		 * @return string The HTML for the story label.
		 */
		public function twae_story_label( $animation ) {
			$label_html           = '';
			$story_settings       = $this->story_settings;
			$date_label_key       = $this->repeater_key['date_label_key'];
			$date_label_attr      = isset( $this->render_repeater_attr[ $date_label_key ] ) ? $this->render_repeater_attr[ $date_label_key ] : '';
			$twae_label_enable    = isset( $this->settings['twae_label_background'] ) ? $this->settings['twae_label_background'] : '';
			$date_label_html      = '<div ' . $date_label_attr . '>' . wp_kses_post( $story_settings['story_date_label'] ) . '</div>';
			$twae_label_connector = isset( $this->settings['twae_label_connector_style'] ) ? $this->settings['twae_label_connector_style'] : '';
			$label_content_top    = ! in_array( $this->settings['twae_layout'], array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ), true ) ? ( isset( $this->settings['twae_label_content_top'] ) && 'no' !== $this->settings['twae_label_content_top'] ? $this->settings['twae_label_content_top'] : '' ) : '';
			$sub_label_html       = '';
			$twae_label_bg_class  = '';
			$twae_label_bg        = '';
			if ( 'yes' === $twae_label_enable ) {
				$twae_label_bg_class = 'twae-label-bg ';
				if ( 'default' === $twae_label_connector ) {
					$twae_label_bg .= 'twae-lbl-arrow';
				} elseif ( 'twae-arrow-line' === $twae_label_connector ) {
					$twae_label_bg .= 'twae-lbl-arrow-line';
				}
			}

			if ( ! empty( $this->story_settings['story_sub_label'] ) && empty( $label_content_top ) ) {
				$sub_label_key   = $this->repeater_key['sublabel_key'];
				$sub_label_attr  = isset( $this->render_repeater_attr[ $sub_label_key ] ) ? $this->render_repeater_attr[ $sub_label_key ] : '';
				$sub_label_html .= '<div ' . $sub_label_attr . '>' . wp_kses_post( $this->story_settings['story_sub_label'] ) . '</div>';
			}
			if ( ! empty( $this->story_settings['story_date_label'] ) || ! empty( $this->story_settings['story_sub_label'] ) ) {
				$label_html .= '<div class="twae-labels ' . esc_attr( $twae_label_bg_class ) . esc_attr( $twae_label_bg ) . '" data-aos="' . esc_attr( $animation ) . '">';
				if ( 'yes' === $twae_label_enable ) {
					$label_html .= '<div class="twae-inner-label">';
				};
				$label_html .= $date_label_html;
				$label_html .= $sub_label_html;
				if ( 'yes' === $twae_label_enable ) {
					$label_html .= '</div>';
				};
				$label_html .= '</div>';
			};
			return $label_html;
		}

		/**
		 * Returns the HTML for the story year label.
		 *
		 * @return string The HTML for the story year label.
		 */
		public function twae_story_year_label() {
			$year_label_html   = '';
			$horizontal_layout = in_array( $this->settings['twae_layout'], array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ), true );

			$wrp_cls = $horizontal_layout ? 'twae-year' : 'twae-year twae-year-container elementor-repeater-item-' . esc_attr( $this->story_data['_id'] );
			if ( 'yes' === $this->story_settings['show_year_label'] ) {
				$year_key         = $this->repeater_key['year_key'];
				$year_attr        = isset( $this->render_repeater_attr[ $year_key ] ) ? $this->render_repeater_attr[ $year_key ] : '';
				$year_label_html .= '<div class="' . esc_attr( $wrp_cls ) . '">
						<div ' . $year_attr . ' >' . wp_kses_post( $this->story_settings['timeline_year'] ) . '</div>
					</div>';
			}
			return $year_label_html;
		}

		/**
		 * Returns the HTML for the story button.
		 *
		 * @return string The HTML for the story button.
		 */
		public function twae_story_btn() {
			$button_html    = '';
			$story_settings = $this->story_settings;
			if ( 'yes' === $story_settings['enable_link'] && ! empty( $story_settings['story_link'] ) ) {
				$button_txt   = $story_settings['button_txt'];
				$button_html  = '<div class="twae-button"><a class="elementor-button" href="' . esc_url( $story_settings['story_link'] ) . '" ' . $story_settings['story_link_target'] . ' ' . $story_settings['story_link_nofollow'] . '>';
				$button_html .= wp_kses_post( $button_txt ) . '</a></div>';
			}
			return $button_html;
		}

		/**
		 * Returns the HTML for the story popup including media.
		 *
		 * @param string $media The media for the story popup.
		 * @return string The HTML for the story popup.
		 */
		public function twae_story_popup( $media ) {
			$twae_popup_html  = '';
			$content          = $this->story_data;
			$twae_popup_html .= '<div id="twae-popup-' . esc_attr( $content['_id'] ) . '" class="twae-popup-content elementor-repeater-item-' . esc_attr( $content['_id'] ) . '" style="display:none;">';
			$twae_popup_html .= '<div class="twae-content">';
			$img              = isset( $content['twae_image']['url'] ) ? $content['twae_image']['url'] : '';
			if ( ! empty( $img ) ) {
				if ( ! str_contains( $content['twae_image']['url'], 'assets/images/placeholder.png' ) ) {
					$twae_popup_html .= '<div class="story_media">';
					$twae_popup_html .= $media;
					$twae_popup_html .= '</div>';
				}
			} else {
				$twae_popup_html .= '<div class="story_media">';
				$twae_popup_html .= $media;
				$twae_popup_html .= '</div>';
			}
			$twae_popup_html .= '<div class="story_content"> ';
			$twae_popup_html .= $this->twae_story_title( false );
			$twae_popup_html .= $this->twae_story_desc();
			$twae_popup_html .= '</div> </div> </div>';
			return $twae_popup_html;
		}

		/**
		 * Returns the custom color for the story.
		 *
		 * @return string The custom color for the story.
		 */
		public function twae_story_custom_color( $widget_id, $repeater_style, $index = 0 ) {

			$background_type = isset( $this->settings['twae_cbox_background_type'] ) ? $this->settings['twae_cbox_background_type'] : 'simple';
			$style           = '';

			// Table Device CSS Wrapper.
			$tablet_css_wrapper = '@media (max-width: 1024px){ .twae-vertical.twae-wrapper{';
			// Mobile Device CSS Wrapper.
			$mobile_css_wrapper = '@media (max-width: 767px){ .twae-vertical.twae-wrapper{';
			// Table Device CSS style.
			$tablet_css = '';
			// Mobile Device CSS style.
			$mobile_css = '';

			// Story Style
			$content = $this->story_data;
			if ( 'multicolor' === isset( $background_type ) && $repeater_style ) {
				$hr_layout              = in_array( $this->settings['twae_layout'], array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ), true );
				$custom_bg_color        = $content['twae_custom_story_bgcolor'];
				$custom_connector_color = $content['twae_custom_cbox_connector_bg_color'];
				if ( ! empty( $custom_bg_color ) || ! empty( $story_styles ) ) {
					$articel_id                                   = $hr_layout ? ' #twae-article-' . $content['_id'] : ' #twae-' . $content['_id'];
					$style                                       .= '.twae-wrapper#twae-wrapper-' . $widget_id . $articel_id . '.twae-story{';
					! empty( $custom_bg_color ) && $style        .= '--tw-cbx-bg' . $index . ': ' . $custom_bg_color . ';';
					! empty( $custom_connector_color ) && $style .= '--tw-arw-bg' . $index . ': ' . $custom_connector_color . ';';
					$style                                       .= '}';
				}
			}
			if ( ! $repeater_style && ! in_array( $this->settings['twae_layout'], array( 'compact', 'horizontal-highlighted' ), true ) ) {
				if ( in_array( $this->settings['twae_preset_vertical_style'], array( 'v-style-0', 'v-style-1', 'v-style-5' ), true )
				|| in_array( $this->settings['twae_preset_hr_style'], array( 'h-style-0', 'h-style-1', 'h-style-5' ), true ) ) {
					if ( ! isset( $this->settings['twae_year_size_tablet'] ) ) {
						$tablet_css .= '--tw-ybx-size: 80px !important;';
					};
					if ( ! isset( $this->settings['twae_year_size_mobile'] ) ) {
						$mobile_css .= '--tw-ybx-size: 80px !important';
					}
				}
			}

			// Table Device CSS Wrapper close.
			$tablet_css_wrapper .= $tablet_css . '}}';
			// Mobile Device CSS Wrapper close.
			$mobile_css_wrapper .= $mobile_css . '}}';

			// Table Device CSS style concatinate into $style.
			if ( ! empty( $tablet_css ) ) {
				$style .= $tablet_css_wrapper;
			}
			// Mobile Device CSS style concatinate into $style.
			if ( ! empty( $mobile_css ) ) {
				$style .= $mobile_css_wrapper;
			}

			return $style;
		}
	}
}


