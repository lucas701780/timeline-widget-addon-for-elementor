<?php
if ( ! class_exists( 'Twae_Content_Loop' ) ) {
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}
	/**
	 * Class Twae_Story_Loop
	 *
	 * This class handles the post loop functionality.
	 */
	class Twae_Content_Loop {
		/**
		 * The post content.
		 *
		 * @var object
		 */
		private $post_data = '';

		/**
		 * The post settings.
		 *
		 * @var array
		 */
		private $post_settings = array();

		/**
		 * The title tag.
		 *
		 * @var string
		 */
		private $title_tag = '';

		/**
		 * Constructor for Twae_Story_Loop class.
		 *
		 * @param array $post_data Post loop data.
		 * @param array $post_settings The settings for the post.
		 */
		public function __construct( $post_data, $post_settings ) {
			// Set the settings for the post loop.
			$this->post_data     = $post_data;
			$this->post_settings = $post_settings;
			$this->title_tag     = 'div';
		}

		/**
		 * Displays the post loop content.
		 *
		 * @param int $index The index of the post loop.
		 * @return array The post loop content.
		 */
		public function twae_post_loop( $index ) {
			$count_item           = $index;
			$multicolor           = 4 >= $index ? 1 : $index; // set multicolor position.
			$layout               = $this->post_settings['layout'];
			$post_settings        = $this->post_settings;
			$post_image_outside   = $post_settings['twae_post_image_outside_box'];
			$label_content_top    = $post_settings['twae_post_label_content_top'];
			$label_content_inside = 'twae-label-content-top' !== $label_content_top && 'twae_image_outside' === $post_image_outside ? 'twae-label-content-inside' : $post_settings['twae_post_label_inside'];
			$post_html            = '';
			$higlighted_content   = '';
			$icon_data            = $this->twae_post_icon();
			$twae_bg_type         = $post_settings['twae_background_type'];
			$animation_attribute  = '';
			if ( 'centered' === $layout || 'one-sided' === $layout || 'left-sided' === $layout ) {
				$animation_attribute = 'data-aos="' . esc_attr( $post_settings['animation'] ) . '"';
			}
			while ( $this->post_data->have_posts() ) {
				$this->post_data->the_post();
				$post_html      .= '';
				$dynamic_cls     = '';
				$twae_data_index = '';
				if ( 'horizontal' === $layout || 'horizontal-bottom' === $layout || 'horizontal-highlighted' === $layout ) {
					$dynamic_cls     = 'swiper-slide';
					$twae_data_index = ' data-index="' . esc_attr( $count_item ) . '"';
				} elseif ( 'one-sided' === $layout ) {
					$dynamic_cls = 'twae-story-right';
				} else {
					$dynamic_cls = 'twae-story-left';
					if ( 'centered' === $layout || 'compact' === $layout ) {
						if ( $count_item % 2 == 0 ) {
							$dynamic_cls = 'twae-story-right';
						}
					}
				}

				// timeline repeater class.
				$twae_repeater_cls = array(
					'twae-story',
					'twae-repeater-item',
				);
				// Icon class style class.
				! empty( $icon_data['class'] ) && array_push( $twae_repeater_cls, esc_attr( $icon_data['class'] ) );
				// Timeline position class.
				! empty( $dynamic_cls ) && array_push( $twae_repeater_cls, esc_attr( $dynamic_cls ) );
				// Lable content inside class.
				! empty( $label_content_inside ) && array_push( $twae_repeater_cls, esc_attr( $label_content_inside ) );
				// label content top class.
				! empty( $label_content_top ) && array_push( $twae_repeater_cls, esc_attr( $label_content_top ) );
				// image out of the box class.
				! empty( $post_image_outside ) && array_push( $twae_repeater_cls, esc_attr( $post_image_outside ) );
				// Background hover class.
				isset( $post_settings['twae_bg_hover'] ) && ! empty( $post_settings['twae_bg_hover'] ) && array_push( $twae_repeater_cls, esc_attr( $post_settings['twae_bg_hover'] ) );
				$twae_repeater_cls = implode( ' ', $twae_repeater_cls );

				// Timeline multicolor date attribute.
				$twae_multicolor_attr = '';
				if ( 'multicolor' === $twae_bg_type && ! in_array( $layout, array( 'horizontal', 'horizontal-bottom', 'horizontal-highlighted' ) ) ) {
					$twae_multicolor_attr = ' data-multicolor="' . esc_attr( $multicolor ) . '"';
				};

				// Timeline content class.
				$twae_content_cls = array( 'twae-content' );
				( 'horizontal-highlighted' === $layout && empty( $this->twae_post_image() ) ) && array_push( $twae_content_cls, 'twae-hg-image-not' );
				$twae_content_cls = implode( ' ', $twae_content_cls );

				// Timeline repeater wrapper start.
				$post_html .= '<div  class="' . esc_attr( $twae_repeater_cls ) . '"' . $twae_multicolor_attr . $twae_data_index . '>';

				if ( 'horizontal' === $layout || 'horizontal-bottom' === $layout || 'horizontal-highlighted' === $layout ) {
					$post_html .= '<div class="twae-story-line"></div>';
				}

				// label outside the article.
				if ( 'horizontal-highlighted' !== $layout ) {
					if ( empty( $label_content_top ) && empty( $label_content_inside ) ) {
						$post_html .= $this->twae_post_label();
					}
					$post_html .= $icon_data['html'];
				}
				$post_html .= '<div class="' . esc_attr( $post_settings['connector_style'] ) . '" ></div>';
				$post_html .= ' <div class="' . esc_attr( $twae_content_cls ) . '"' . $animation_attribute . '>';

				// label inside or top the article.
				if ( ( ! empty( $label_content_top ) || ! empty( $label_content_inside ) ) && 'horizontal-highlighted' !== $layout ) {
					$post_html .= $this->twae_post_label();
				}

				// post title.
				$post_html .= $this->twae_post_title();

				// image and description in popup disabel.
				if ( 'no' === $post_settings['enable_popup'] ) {
					$post_html .= $this->twae_post_image();
					$post_html .= $this->twae_post_desc();
				}

				// content div close.
				$post_html .= '</div>';

				// popup content.
				if ( 'yes' === $post_settings['enable_popup'] ) {
					$post_html .= $this->twae_post_popup();
				}

				// article div close.
				$post_html .= '</div>';

				if ( 'yes' === $post_settings['enable_popup'] ) {
					$post_html .= $this->twae_post_popup();
				}

				if ( 'horizontal-highlighted' === $layout ) {
					$higlighted_content .= $this->twae_highlighted_content( $icon_data );
				}

				// Story multicolor index.
				4 === $multicolor ? $multicolor = 1 : $multicolor++;

				$count_item++;
			}
			$data = array( 'post_html' => $post_html );
			if ( 'horizontal-highlighted' === $layout ) {
				$data['highlighted_content'] = $higlighted_content;
			}
			return $data;
		}

		/**
		 * Returns the HTML for the post title.
		 *
		 * @return string The HTML for the post title.
		 */
		public function twae_post_title() {
			// Get the current post ID.
			$post_id = get_the_ID();
			// Initialize the title HTML.
			$title_html = '';
			// Get the permalink for the post.
			$title_link = get_the_permalink( $post_id );
			// Set the default title class.
			$title_cls = 'twea-post-link';
			// Check if popup is enabled and update the title link and class accordingly.
			if ( 'yes' === $this->post_settings['enable_popup'] ) {
				$title_link = '#twae-popup-' . esc_attr( $post_id );
				$title_cls  = 'twae-popup-links';
			}
			// Check if the post title is not empty and construct the title HTML.
			if ( ! empty( get_the_title() ) ) {
				$title_html .= '<' . esc_html( $this->title_tag ) . ' class="twae-title">';
				$title_html .= '<a class="' . esc_attr( $title_cls ) . '" href="' . esc_url( $title_link ) . '">' . esc_html( get_the_title() ) . '</a>';
				$title_html .= '</' . esc_html( $this->title_tag ) . '>';
			}

			return $title_html;
		}

		/**
		 * Returns the HTML for the post description.
		 *
		 * @return string The HTML for the post description.
		 */
		public function twae_post_desc() {
			$post_description = '';
			$post_settings    = $this->post_settings;
			$post_id          = get_the_id();
			if ( isset( $post_settings['desc'] ) && 'summary' === $post_settings['desc'] ) {
				$desc_length       = null !== ( $post_settings['twae_post_desc_length'] ) ? $post_settings['twae_post_desc_length'] : '0';
				$post_desc_content = has_excerpt( $post_id ) ? get_the_excerpt() : get_the_content();
				$post_description  = wp_trim_words( wp_strip_all_tags( $post_desc_content ), $desc_length );
				$post_description .= $this->twae_post_btn();
			} else {
				$post_description = get_the_content();
			}

			$description_html = '<div class="twae-description">' . wp_kses_post( $post_description ) . '</div>';

			return $description_html;
		}

		/**
		 * Returns the HTML for the post image.
		 *
		 * @return string The HTML for the post image.
		 */
		public function twae_post_image() {
			$post_settings           = $this->post_settings;
			$imagepopbox             = $post_settings['twae_post_image_lightbox_settings'];
			$image_post_hover_effect = $post_settings['twae_post_image_hover_effect'];
			$image_html              = '';
			$lightbox_open           = '';
			$lightbox_close          = '';
			if ( 'yes' === $imagepopbox && 'yes' !== $post_settings['enable_popup'] ) {
				$lightbox_open  = '<a class="wplightbox" href="' . esc_url( get_the_post_thumbnail_url() ) . '">';
				$lightbox_close = '</a>';
			}

			if ( has_post_thumbnail() && ! empty( get_the_post_thumbnail() ) ) {
				$image_wrp_cls = array( 'twae-media' );
				! empty( $post_settings['image_size'] ) && array_push( $image_wrp_cls, esc_attr( $post_settings['image_size'] ) );
				! empty( $image_post_hover_effect ) && array_push( $image_wrp_cls, esc_attr( $image_post_hover_effect ) );

				$image_wrp_cls = implode( ' ', $image_wrp_cls );
				$image_html   .= '<div class="' . esc_attr( $image_wrp_cls ) . '">' . $lightbox_open . get_the_post_thumbnail( null, esc_attr( $post_settings['image_size'] ) ) . $lightbox_close . '</div>';
			}

			return $image_html;
		}

		/**
		 * Returns the HTML for the post icon.
		 *
		 * @return array The HTML and class for the post icon.
		 */
		public function twae_post_icon() {
			$post_settings = $this->post_settings;
			$display_icon  = $post_settings['display_icon']; // Older Version Support.
			$icon_type     = $post_settings['icon_type'];
			$icon_html     = '';
			if ( 'displaydots' === $icon_type || ( empty( $display_icon ) && 'custom' !== $icon_type && 'displaynone' !== $icon_type ) ) {
				$icon_html .= '<div class="twae-icondot"></div>';
				$icon_cls   = 'twae-story-no-icon';
			} elseif ( 'displaynone' === $icon_type ) {
				$icon_cls = 'twae-story-no-dot';
			} else {
				$icon_cls   = 'twae-story-icon';
				$icon_html .= '<div class="twae-icon">';
				$icon       = Twae_Functions::twae_get_post_icons( $post_settings['post_custom_icon'] );
				$icon_html .= $icon;
				$icon_html .= '</div>';
			}

			$icon_data = array(
				'html'  => $icon_html,
				'class' => $icon_cls,
			);
			return $icon_data;

		}

		/**
		 * Returns the HTML for the post label.
		 *
		 * @return string The HTML for the post label.
		 */
		public function twae_post_label() {
			$post_id              = get_the_ID();
			$post_settings        = $this->post_settings;
			$twae_label_enable    = $post_settings['twae_label_background'];
			$twae_label_connector = $post_settings['twae_label_connector_style'];
			if ( ! empty( $post_settings['custom_metakey'] ) ) {
				$story_date_label1 = wp_kses_post( get_post_meta( $post_id, $post_settings['custom_metakey'], 'true' ) );
				$story_date_label  = date( $post_settings['date_format'], strtotime( $story_date_label1 ) );
			} else {
				$story_date_label = get_the_date( $post_settings['date_format'] );
			}

			$twae_label_bg_class = 'yes' === $twae_label_enable ? 'twae-label-bg' : '';
			$twae_label_arrow    = '';
			if ( 'yes' === $twae_label_enable && 'default' === $twae_label_connector ) {
				$twae_label_arrow = 'twae-lbl-arrow';
			} elseif ( 'yes' === $twae_label_enable && 'twae-arrow-line' === $twae_label_connector ) {
				$twae_label_arrow = 'twae-lbl-arrow-line';
			};

			$label         = '';
			$label_wrp_cls = array( 'twae-labels' );
			! empty( $twae_label_bg_class ) && array_push( $label_wrp_cls, $twae_label_bg_class );
			! empty( $twae_label_arrow ) && array_push( $label_wrp_cls, $twae_label_arrow );

			$label_wrp_cls = implode( ' ', $label_wrp_cls );

			$label .= '<div class="' . esc_attr( $label_wrp_cls ) . '">';
			if ( 'yes' === $twae_label_enable ) {
				$label .= '<div class="twae-inner-label">';
			};
			$label .= '<div class="twae-label-big">' . esc_html( $story_date_label ) . '</div>';
			if ( 'yes' === $twae_label_enable ) {
				$label .= '</div>';
			};
			$label .= '</div>';

			return $label;
		}

		/**
		 * Returns the HTML for the post button.
		 *
		 * @return string The HTML for the post button.
		 */
		public function twae_post_btn() {
			$post_settings  = $this->post_settings;
			$html           = '';
			$readmore_text  = isset( $post_settings['twae_post_readmore_text'] ) ? $post_settings['twae_post_readmore_text'] : '';
			$show_read_more = isset( $post_settings['twae_post_read_more'] ) ? $post_settings['twae_post_read_more'] : 'yes';
			if ( 'yes' === $show_read_more ) {
					$html .= sprintf(
						'<div class="twae-button"><a class="elementor-button" href="%1$s">%2$s</a></div>',
						esc_url( get_permalink( get_the_ID() ) ),
						__( $readmore_text, 'twae' )
					);
			}
			return $html;
		}

		/**
		 * Returns the HTML for the post popup including media.
		 *
		 * @return string The HTML for the post popup.
		 */
		public function twae_post_popup() {
			$post_id        = get_the_ID();
			$popup_content  = '<div id="twae-popup-' . esc_attr( $post_id ) . '" class="twae-popup-content elementor-repeater-item-' . esc_attr( $post_id ) . '" style="display:none;">';
			$popup_content .= '<div class="twae-content">
                        <div class="story_media">';
			$popup_content .= $this->twae_post_image();
			$popup_content .= '</div>
                    <div class="story_content"> ';
			$popup_content .= $this->twae_post_title();
			$popup_content .= $this->twae_post_desc();
			$popup_content .= '</div> </div> </div>';

			return $popup_content;
		}

		/**
		 * Returns the HTML for the highlighted content.
		 *
		 * @param array $icon_data The data for the icon.
		 * @return string The HTML for the highlighted content.
		 */
		public function twae_highlighted_content( $icon_data ) {
			$post_settings = $this->post_settings;

			$highlighted_cls = array(
				'twae-highlighted-hr',
				'swiper-slide',
			);

			! empty( $post_settings['twae_bg_hover'] ) && array_push( $highlighted_cls, $post_settings['twae_bg_hover'] );
			$highlighted_article = implode( ' ', $highlighted_cls );

			$html  = '<div class="' . esc_attr( $highlighted_article ) . '">';
			$html .= $this->twae_post_label();
			$html .= $icon_data['html'];
			$html .= '</div>';
			return $html;
		}
	}
}


