<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
|-----------------------
|  Common functions for both timelines ( Story Timeline & Post Timeline ).
|-----------------------
*/

/**
 * Class Twae_Functions
 */
class Twae_Functions {

	/**
	 * Get animation array
	 *
	 * This method returns an array of animation types available for use.
	 *
	 * @return array The array of animation types
	 */
	public static function twae_pro_animation_array() {

		$animations = array(
			'none'            => 'none',
			'fade'            => 'fade',
			'zoom-in'         => 'zoom-in',
			'flip-right'      => 'flip-right',
			'zoom-out'        => 'zoom-out',
			'fade-up'         => 'fade-up',
			'fade-down'       => 'fade-down',
			'fade-left'       => 'fade-left',
			'fade-right'      => 'fade-right',
			'fade-up-right'   => 'fade-up-right',
			'fade-up-left'    => 'fade-up-left',
			'fade-down-right' => 'fade-down-right',
			'fade-down-left'  => 'fade-down-left',
			'flip-up'         => 'flip-up',
			'flip-down'       => 'flip-down',
			'flip-left'       => 'flip-left',
			'slide-up'        => 'slide-up',
			'slide-left'      => 'slide-left',
			'slide-right'     => 'slide-right',
			'zoom-in-up'      => 'zoom-in-up',
			'zoom-in-down'    => 'zoom-in-down',
			'slide-down'      => 'slide-down',
			'zoom-in-left'    => 'zoom-in-left',
			'zoom-in-right'   => 'zoom-in-right',
			'zoom-out-up'     => 'zoom-out-up',
			'zoom-out-down'   => 'zoom-out-down',
			'zoom-out-left'   => 'zoom-out-left',
			'zoom-out-right'  => 'zoom-out-right',
		);

		return $animations;

	}

	/**
	 * Retrieve an array of available post types.
	 *
	 * @return array The array of post types
	 */
	public static function twae_pro_get_post_types() {
		$post_types = get_post_types(
			array(
				'public'            => true,
				'show_in_nav_menus' => true,
			),
			'objects'
		);
		$post_types = wp_list_pluck( $post_types, 'label', 'name' );

		return array_diff_key( $post_types, array( 'elementor_library', 'attachment' ) );
	}

	/**
	 * Get query arguments for post timeline
	 *
	 * This method constructs and returns an array of query arguments for the post timeline.
	 *
	 * @param array  $settings The settings for the post timeline.
	 * @param string $query The type of query.
	 * @param string $page_no The page number.
	 * @return array The array of query arguments.
	 */
	public static function twae_pro_query_args( $settings, $query = 'simple', $page_no = '' ) {
		// Set the post type, order, and limit based on the provided settings.
		$post_type  = sanitize_text_field( $settings['post_type'] );
		$order      = sanitize_text_field( $settings['order'] );
		$show_posts = intval( $settings['show_posts'] );
		$limit      = $show_posts;

		// Determine the paged and offset values based on the query type.
		if ( 'before_ajax' === $query ) {
			$paged  = 1;
			$offset = 0;
		} elseif ( 'ajax' === $query ) {
			$paged  = ! empty( $page_no ) ? intval( $page_no ) : 1;
			$offset = $page_no * $limit;
		} else {
			$paged  = ( get_query_var( 'paged' ) ) ? intval( get_query_var( 'paged' ) ) : 1;
			$offset = ( $limit * $paged ) - $limit;
		}

		// Set the default arguments for the query.
		$args = array(
			'orderby'             => 'date',
			'order'               => $order,
			'ignore_sticky_posts' => 1,
			'post_status'         => 'publish',
			'post_type'           => esc_attr( $post_type ),
			'posts_ids'           => array(),
			'posts_per_page'      => $show_posts,
			'offset'              => $offset,
			'post__not_in'        => array(),
			'paged'               => $paged,
		);

		// Add tax query if the post type is not 'page'.
		if ( 'page' !== $args['post_type'] ) {
			$args['tax_query'] = array();

			$taxonomies = get_object_taxonomies( $post_type, 'objects' );
			foreach ( $taxonomies as $object ) {
				$tax_key = 'twae_post_' . sanitize_key( $object->name );
				if ( ! empty( $settings[ $tax_key ] ) ) {
					$args['tax_query'][] = array(
						'taxonomy' => sanitize_key( $object->name ),
						'field'    => 'term_id',
						'terms'    => $settings[ $tax_key ],
					);
				}
			}
			if ( ! empty( $args['tax_query'] ) ) {
				$args['tax_query']['relation'] = 'AND';
			}
		}
		return $args;
	}

	/**
	 * Get video type
	 *
	 * Retrieves and returns the type of video.
	 *
	 * @param string $url The URL of the video.
	 * @return string The type of video
	 */
	public static function twae_video_type( $url ) {
		$url = esc_url( $url );
		if ( strpos( $url, 'youtube' ) !== false || strpos( $url, 'youtu.be' ) !== false ) {
			return 'youtube';
		} elseif ( strpos( $url, 'vimeo' ) !== false ) {
			return 'vimeo';
		} else {
			return 'unknown';
		}
	}

	/**
	 * Retrieve and return the icons for a post.
	 *
	 * @param string $icon_type The type of icon.
	 * @param string $post_custom_icon The custom icon for the post.
	 * @return string The post icons
	 */
	public static function twae_render_icon_html( $icon_name ) {
		ob_start();
		\Elementor\Icons_Manager::render_icon( $icon_name, array( 'aria-hidden' => 'true' ) );
		$render_icon = ob_get_contents();
		ob_end_clean();
		return $render_icon;
	}

	/**
	 * Get post timeline settings
	 *
	 * This method retrieves and returns the settings for the post timeline.
	 *
	 * @param array $settings The settings for the post timeline.
	 * @return array The post settings
	 */
	public static function twae_post_timeline_settings( $settings ) {
		$layout                  = isset( $settings['twae_post_layout'] ) ? sanitize_text_field( $settings['twae_post_layout'] ) : 'centered';
		$post_type               = isset( $settings['twae_post_post_type'] ) ? sanitize_text_field( $settings['twae_post_post_type'] ) : 'post';
		$timeline_style          = '';
		$enable_hr_ajax_loadmore = 'no';
		$pagination_type         = 'default';

		if ( 'horizontal' === $layout || 'horizontal-bottom' === $layout || 'horizontal-highlighted' === $layout ) {
			$timeline_style          = isset( $settings['twae_post_hr_style'] ) ? sanitize_text_field( $settings['twae_post_hr_style'] ) : 'style-1';
			$enable_hr_ajax_loadmore = isset( $settings['twae_post_hr_ajax_loadmore'] ) && ! empty( $settings['twae_post_hr_ajax_loadmore'] ) ? sanitize_text_field( $settings['twae_post_hr_ajax_loadmore'] ) : 'no';
		} else {
			$timeline_style = isset( $settings['twae_post_vertical_style'] ) ? sanitize_text_field( $settings['twae_post_vertical_style'] ) : 'style-1';
		}
		if ( 'horizontal' !== $layout || 'horizontal-bottom' !== $layout ) {
			$pagination_type = isset( $settings['twae_post_pagination_type'] ) ? sanitize_text_field( $settings['twae_post_pagination_type'] ) : 'default';
		}

		$sides_to_show = isset( $settings['twae_post_slides_to_show']['size'] ) && ! empty( $settings['twae_post_slides_to_show']['size'] ) ? intval( $settings['twae_post_slides_to_show']['size'] ) : 2;
		// slides to show for clean design.
		$clean_sides_to_show = isset( $settings['twae_post_clean_slides_to_show']['size'] ) && ! empty( $settings['twae_post_clean_slides_to_show']['size'] ) ? intval( $settings['twae_post_clean_slides_to_show']['size'] ) : 3;

		$image_width = isset( $settings['twae_post_image_size'] ) ? sanitize_text_field( $settings['twae_post_image_size'] ) : 'large';

		$date_format = isset( $settings['twae_post_date_format'] ) ? sanitize_text_field( $settings['twae_post_date_format'] ) : 'j F';

		if ( 'custom' === $date_format ) {
			$custom_date_format = isset( $settings['twae_post_custom_date_format'] ) ? wp_kses_post( $settings['twae_post_custom_date_format'] ) : 'j M';
			$date_format        = $custom_date_format;
		}

		$no_post_msg = __( 'No Post Found', 'twae' );

		$timeline_layout_wrapper = 'twae-both-sided';
		$timeline_layout_class   = '';
		if ( 'one-sided' === $layout ) {
			$timeline_layout_class   = 'twae-one-sided';
			$timeline_layout_wrapper = 'twae-one-sided';
		} elseif ( 'horizontal' === $layout ) {
			$timeline_layout_class   = 'twae-items-wrapper';
			$timeline_layout_wrapper = 'twae-horizontal-wrapper';
		}

		$show_posts = ! empty( $settings['twae_post_show_posts'] ) ? intval( $settings['twae_post_show_posts'] ) : 10;
		$space      = isset( $settings['twae_post_space_between']['size'] ) ? intval( $settings['twae_post_space_between']['size'] ) : 20;

		// Background Hover Type.
		if ( isset( $settings['twae_cbox_background_type_hover'] ) && 'simple' === $settings['twae_cbox_background_type_hover'] ) {
			$twae_bg_hover = 'twae-bg-hover';
		} else {
			$twae_bg_hover = '';
		}

		$enable_popup = '';
		if ( ( isset( $settings['twae_enable_popup'] ) && 'yes' === $settings['twae_enable_popup'] ) || 'style-4' === $timeline_style ) {
			$enable_popup = 'yes';
		} else {
			$enable_popup = 'no';
		}

		if ( isset( $settings['twae_cbox_connector_style'] ) && 'default' === $settings['twae_cbox_connector_style'] ) {
			if ( 'style-2' === $timeline_style || ( 'style-4' === $timeline_style ) && 'horizontal' === $layout ) {
				$connector_style = 'twae-arrow-line';
			} else {
				$connector_style = 'twae-arrow';
			}
		} else {
			$connector_style = isset( $settings['twae_cbox_connector_style'] ) ? sanitize_text_field( $settings['twae_cbox_connector_style'] ) : 'twae-arrow';
		}

		$post_settings = array(
			'layout'                            => $layout,
			'autoplay'                          => isset( $settings['twae_post_autoplay'] ) ? sanitize_text_field( $settings['twae_post_autoplay'] ) : 'false',
			'sidesHeight'                       => isset( $settings['twae_post_slides_height'] ) ? sanitize_text_field( $settings['twae_post_slides_height'] ) : 'default-height',
			'post_type'                         => $post_type,
			'date_format'                       => $date_format,
			'order'                             => isset( $settings['twae_post_order'] ) ? sanitize_text_field( $settings['twae_post_order'] ) : 'DESC',
			'desc'                              => isset( $settings['twae_post_desc'] ) ? sanitize_text_field( $settings['twae_post_desc'] ) : 'summary',
			'image_size'                        => $image_width,
			'animation'                         => isset( $settings['twae_post_animation'] ) ? sanitize_text_field( $settings['twae_post_animation'] ) : 'none',
			'post_date'                         => isset( $settings['twae_post_date'] ) ? sanitize_text_field( $settings['twae_post_date'] ) : 'published',
			'custom_metakey'                    => isset( $settings['twae_post_custom_metakey'] ) ? sanitize_text_field( $settings['twae_post_custom_metakey'] ) : '',
			'display_icon'                      => isset( $settings['twae_post_show_icon'] ) ? esc_html( $settings['twae_post_show_icon'] ) : '',
			'icon_type'                         => isset( $settings['twae_post_icon_type'] ) ? sanitize_text_field( $settings['twae_post_icon_type'] ) : 'custom',
			'post_custom_icon'                  => isset( $settings['twae_post_custom_icon'] ) ? $settings['twae_post_custom_icon'] : 'far fa-clock',
			'no_post_msg'                       => $no_post_msg,
			'sidesToShow'                       => $sides_to_show,
			'enable_hr_ajax_loadmore'           => $enable_hr_ajax_loadmore,
			'clean_sidesToShow'                 => $clean_sides_to_show, // only for clean style.
			'timeline_style'                    => $timeline_style,
			'timeline_layout_class'             => $timeline_layout_class,
			'timeline_layout_wrapper'           => $timeline_layout_wrapper,
			'show_posts'                        => $show_posts,
			'space'                             => $space,
			'vertical_pagination_type'          => $pagination_type,
			'twae_bg_hover'                     => $twae_bg_hover,
			'enable_popup'                      => $enable_popup,
			'connector_style'                   => $connector_style,
			'image_size'                        => $image_width,
			'twae_post_read_more'               => isset( $settings['twae_post_read_more'] ) ? sanitize_text_field( $settings['twae_post_read_more'] ) : '',
			'twae_post_readmore_text'           => isset( $settings['twae_post_readmore_text'] ) ? sanitize_text_field( $settings['twae_post_readmore_text'] ) : '',
			'twae_post_desc_length'             => isset( $settings['twae_post_desc_length'] ) ? intval( $settings['twae_post_desc_length'] ) : '',
			'page_text'                         => isset( $settings['twae_post_page_text_change'] ) ? sanitize_text_field( $settings['twae_post_page_text_change'] ) : '',
			'of_text'                           => isset( $settings['twae_post_of_text_change'] ) ? sanitize_text_field( $settings['twae_post_of_text_change'] ) : '',
			'ajax_button'                       => isset( $settings['twae_post_load_more_change'] ) ? sanitize_text_field( $settings['twae_post_load_more_change'] ) : '',
			'twae_label_background'             => isset( $settings['twae_label_background'] ) ? sanitize_text_field( $settings['twae_label_background'] ) : '',
			'twae_label_connector_style'        => isset( $settings['twae_label_connector_style'] ) ? sanitize_text_field( $settings['twae_label_connector_style'] ) : '',
			'twae_post_image_lightbox_settings' => isset( $settings['twae_post_image_lightbox_settings'] ) ? sanitize_text_field( $settings['twae_post_image_lightbox_settings'] ) : '',
			'twae_post_image_hover_effect'      => 'yes' === $settings['twae_post_image_hover_effect'] && 'yes' !== $enable_popup ? ' twae-img-effect' : '',
			'twae_post_label_content_top'       => 'no' !== $settings['twae_post_label_content_top'] ? sanitize_text_field( $settings['twae_post_label_content_top'] ) : '',
			'twae_post_label_inside'            => 'no' !== $settings['twae_post_label_inside'] ? sanitize_text_field( $settings['twae_post_label_inside'] ) : '',
			'twae_post_image_outside_box'       => 'no' !== $settings['twae_post_image_outside_box'] && 'yes' !== $enable_popup ? sanitize_text_field( $settings['twae_post_image_outside_box'] ) : '',
			'twae_background_type'              => isset( $settings['twae_cbox_background_type'] ) ? sanitize_text_field( $settings['twae_cbox_background_type'] ) : 'simple',
		);

		// Post taxonomies
		if ( 'page' !== $post_type ) {
			$args['tax_query'] = array();

			$taxonomies = get_object_taxonomies( $post_type, 'objects' );
			foreach ( $taxonomies as $object ) {
				$tax_key = 'twae_post_' . sanitize_key( $object->name );
				if ( isset( $settings[ $tax_key ] ) && ! empty( $settings[ $tax_key ] ) ) {
					$post_settings[ $tax_key ] = $settings[ $tax_key ];
				}
			}
		}

		return $post_settings;

	}

	/**
	 * Retrieve and return the content for the post timeline pagination.
	 *
	 * This method generates and returns the HTML content for the pagination of the post timeline.
	 *
	 * @param int   $total_pages The total number of pages.
	 * @param array $post_settings The settings for the post timeline.
	 * @return string The HTML content for the pagination
	 */
	public static function twae_post_pagination( $total_pages, $post_settings ) {
		$content = '';

		if ( $total_pages > 1 && 'ajax_load_more' === $post_settings['vertical_pagination_type'] ) {
			$content .= '<div class="twae-ajax-load-more twae-button"><button class="elementor-button">
			<span class="lm_active_state" style="display:none">';
			$content .= self::get_navi_control_icon( 'fas fa-spinner' );
			$content .= ' ' . __( 'Loading', 'twae' ) . '</span><span class="lm_default_state">' . __( esc_html( $post_settings['ajax_button'] ), 'twae' ) . '</span></button></div>';
		} else {
			if ( $total_pages > 1 ) {
				$current_page = max( 1, get_query_var( 'paged' ) );

				$big       = 999999999;
				$pagelinks = paginate_links(
					array(
						'base'         => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
						'format'       => '?paged=%#%',
						'current'      => esc_html( $current_page ),
						'total'        => esc_html( $total_pages ),
						'show_all'     => false,
						'type'         => 'plain',
						'show_all'     => false,
						'end_size'     => 1,
						'prev_next'    => true,
						'prev_text'    => __( '&laquo;' ),
						'next_text'    => __( '&raquo;' ),
						'type'         => 'plain',
						'add_args'     => false,
						'add_fragment' => '',
					)
				);

				$of_lbl   = __( $post_settings['of_text'], 'twae' );
				$page_lbl = __( $post_settings['page_text'], 'twae' );

				if ( $pagelinks ) {
					$content .= '<nav class="' . esc_attr( $post_settings['timeline_layout_class'] ) . ' twae-custom-pagination">
					<span class="twae-page-numbers twae-page-num"> ' . esc_html( $page_lbl ) . ' ' . esc_html( $current_page ) . ' ' . esc_html( $of_lbl ) . ' ' . esc_html( $total_pages ) . '</span> 
					' . $pagelinks . '
					 </nav>';

				}
			}
		}
		return $content;
	}

	/**
	 * Retrieve and return the content for an icon placeholder.
	 *
	 * This method retrieves and returns the content for an icon placeholder based on the provided $content array.
	 *
	 * @param array $content The content array containing information about the icon.
	 * @return string The content for the icon placeholder
	 */
	public static function twae_get_icon_placeholder_content( $content ) {
		$twae_icon_type = isset( $content['twae_icon_type'] ) ? sanitize_text_field( $content['twae_icon_type'] ) : 'icon';
		if ( 'image' === $twae_icon_type ) {
			$icon_image = wp_get_attachment_image( intval( $content['twae_icon_image']['id'] ), array( 40, 40 ), true );
			return $icon_image;
		} elseif ( 'customtext' === $twae_icon_type ) {
			$twae_icon_text = isset( $content['twae_icon_text'] ) ? sanitize_text_field( $content['twae_icon_text'] ) : '';
			return '<span class="twae_icon_text">' . esc_html( $twae_icon_text ) . '</span>';
		} else {
			if ( isset( $content['twae_story_icon'] ) ) {
				ob_start();
				\Elementor\Icons_Manager::render_icon( $content['twae_story_icon'], array( 'aria-hidden' => 'true' ) );
				$render_icon = ob_get_contents();
				ob_end_clean();
				return $render_icon;
			} else {
				ob_start();
				\Elementor\Icons_Manager::render_icon(
					array(
						'value'   => 'far fa-clock',
						'library' => 'fa-regular',
					),
					array( 'aria-hidden' => 'true' )
				);
				$render_icon = ob_get_contents();
				ob_end_clean();
				return $render_icon;
			}
		}
	}

	/**
	 * Get story media
	 *
	 * Retrieves and returns the media for a story.
	 *
	 * @param array       $content The content for the story.
	 * @param string      $dir The directory for the story.
	 * @param string|null $image_lightbox Whether to use lightbox for images.
	 * @param string|null $image_effect Whether to apply image effect.
	 * @return string The story media
	 */
	public static function twae_get_story_media( $content, $dir, $image_lightbox = null, $image_effect = null ) {
		$media          = '';
		$story_settings = self::twae_story_content_variables( $content );
		$random_number  = rand( 10, 100 );
		$lightbox_open  = '';
		$lightbox_close = 'yes' === $image_lightbox ? '</a>' : '';
		$image_effect   = 'yes' === $image_effect ? ' twae-img-effect' : '';
		if ( isset( $content['twae_media'] ) && 'image' === $content['twae_media'] ) {

			if ( isset( $content['twae_media']['id'] ) && 0 === $content['twae_image']['id'] ) {
				$story_settings['thumbnail_size'] = 'large';
			}

			if ( ! empty( $content['twae_image']['id'] ) ) {
				$lightbox_open .= 'yes' === $image_lightbox ? '<a class="wplightbox" href="' . esc_url( wp_get_attachment_url( intval( $content['twae_image']['id'] ), 'full' ) ) . '">' : '';

				if ( isset( $content['thumbnail_size'] ) && 'custom' === $story_settings['thumbnail_size'] ) {
					$custom_size = array( intval( $story_settings['thumbnail_custom_dimension']['width'] ), intval( $story_settings['thumbnail_custom_dimension']['height'] ) );
					$image       = wp_get_attachment_image( intval( $content['twae_image']['id'] ), $custom_size, true );
				} else {
					$image = wp_get_attachment_image( intval( $content['twae_image']['id'] ), $story_settings['thumbnail_size'], true );
				}
				$media = '<div class="twae-media ' . esc_attr( $story_settings['thumbnail_size'] ) . esc_attr( $image_effect ) . '">' . $lightbox_open . $image . $lightbox_close . '</div>';
			} elseif ( ! empty( $content['twae_image']['url'] ) ) {
				$lightbox_open .= $image_lightbox == 'yes' ? '<a class="wplightbox" href="' . esc_url( $content['twae_image']['url'] ) . '">' : '';
				$media          = '<div class="twae-media ' . esc_attr( $story_settings['thumbnail_size'] ) . esc_attr( $image_effect ) . '">' . $lightbox_open . '<img src="' . esc_url( $content['twae_image']['url'] ) . '"></img>' . $lightbox_close . '</div>';
			}
		} elseif ( 'video' === $content['twae_media'] ) {
			if ( ! empty( $story_settings['video_url'] ) ) {
				if ( 'youtube' === self::twae_video_type( $story_settings['video_url'] ) ) {
					preg_match( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', esc_url( $story_settings['video_url'] ), $matches );

					$start_time       = '';
					$end_time         = '';
					$start_time_value = preg_split( '/t=|start=/', $story_settings['video_url'] );
					if ( count( $start_time_value ) > 1 ) {
						$start_time_val = (int) preg_split( '/s|&/', $start_time_value[1] )[0];
						$start_time    .= 0 !== $start_time_val ? '?start=' . $start_time_val . '' : '';
					}
					$end_time_value = preg_split( '/end=/', $story_settings['video_url'] );
					if ( count( $end_time_value ) > 1 && count( $start_time_value ) > 1 ) {
						$end_time_val = (int) preg_split( '/s|&/', $end_time_value[1] )[0];
						$end_time    .= 0 !== $end_time_val ? '&end=' . $end_time_val . '' : '';
					}

					if ( isset( $matches[1] ) ) {
						$id    = $matches[1];
						$media = '<div class="twae-media"><iframe width="100%" 
						src="https://www.youtube.com/embed/' . esc_attr( $id ) . esc_attr( $start_time ) . esc_attr( $end_time ) . '" 
						frameborder="0" allowfullscreen></iframe></div>';
					}
				} else {
					$media = '<div class="twae-media">Wrong Url</div>';
				}
			}
		} else {
			if ( null !== $story_settings['slideshow'] && is_array( $story_settings['slideshow'] ) && count( $story_settings['slideshow'] ) ) {
				$twae_slideshow_autoplay = ! empty( $content['twae_slideshow_autoplay'] ) ? sanitize_text_field( $content['twae_slideshow_autoplay'] ) : 'false';

				$media .= '<div class="twae-media' . esc_attr( $image_effect ) . '"><div  id="twae-slideshow-' . esc_attr( $content['_id'] ) . esc_attr( $random_number ) . '" class="twae-slideshow swiper-container" dir="' . esc_attr( $dir ) . '" data-slideshow_autoplay ="' . esc_attr( $twae_slideshow_autoplay ) . '"><div class="swiper-wrapper">';
				foreach ( $story_settings['slideshow'] as $image ) {
					$lightbox_open = '';

					if ( 'yes' === $image_lightbox ) {
						$lightbox       = wp_get_attachment_image_url( intval( $image['id'] ), 'full' );
						$lightbox_open .= '<a class="twae-slideshow-lightbox" data-elementor-lightbox-slideshow="twae_img_lightbox' . esc_attr( $content['_id'] ) . '" href="' . esc_url( $lightbox ) . '">';
					};

					$image  = wp_get_attachment_image( intval( $image['id'] ), 'large', false );
					$media .= '<div class="swiper-slide">' . $lightbox_open . $image . $lightbox_close . '</div>';
				}
				$media .= '</div>
					<!-- Add Arrows -->                    
						<div class="twae-icon-left-open"></div>
						<div class="twae-icon-right-open"></div>
					</div></div>';
			}
		}

		return $media;

	}

	/**
	 * Get story content variables
	 *
	 * Retrieves and returns the variables for story content.
	 *
	 * @param array $content The settings for the story content.
	 * @return array The array of story content variables
	 */
	public static function twae_story_content_variables( $content ) {

		$thumbnail_size = isset( $content['twae_thumbnail_size'] ) ? sanitize_text_field( $content['twae_thumbnail_size'] ) : 'large';
		if ( 'medium_large' === $thumbnail_size || 'large' === $thumbnail_size ) {
			$image_width = 'full';
		} else {
			$image_width = 'large';
		}
		$story_settings = array(
			'timeline_description'       => isset( $content['twae_description'] ) ? wp_kses_post( $content['twae_description'] ) : '',
			'show_year_label'            => isset( $content['twae_show_year_label'] ) ? wp_kses_post( $content['twae_show_year_label'] ) : 'no',
			'timeline_year'              => isset( $content['twae_year'] ) ? esc_html( $content['twae_year'] ) : '',
			'story_date_label'           => isset( $content['twae_date_label'] ) ? wp_kses_post( $content['twae_date_label'] ) : '',
			'story_sub_label'            => isset( $content['twae_extra_label'] ) ? esc_html( $content['twae_extra_label'] ) : '',
			'timeline_story_title'       => isset( $content['twae_story_title'] ) ? esc_html( $content['twae_story_title'] ) : '',
			'story_icon'                 => isset( $content['twae_story_icon'] ) ? $content['twae_story_icon']['value'] : '',
			'thumbnail_size'             => isset( $content['twae_thumbnail_size'] ) ? sanitize_text_field( $content['twae_thumbnail_size'] ) : '',
			'thumbnail_custom_dimension' => isset( $content['twae_thumbnail_custom_dimension'] ) ? $content['twae_thumbnail_custom_dimension'] : '',
			'video_url'                  => isset( $content['twae_video_url'] ) ? esc_url( $content['twae_video_url'] ) : '',
			'slideshow'                  => isset( $content['twae_slideshow'] ) ? $content['twae_slideshow'] : '',
			'display_icon'               => isset( $content['twae_display_icon'] ) ? esc_html( $content['twae_display_icon'] ) : null,
			'icon_type'                  => isset( $content['twae_icon_type'] ) ? sanitize_text_field( $content['twae_icon_type'] ) : '',
			'image_width'                => $image_width,
			'story_link'                 => isset( $content['twae_story_link']['url'] ) ? esc_url( $content['twae_story_link']['url'] ) : '',
			'story_link_target'          => isset( $content['twae_story_link']['is_external'] ) && ! empty( $content['twae_story_link']['is_external'] ) ? ' target="_blank"' : '',
			'story_link_nofollow'        => isset( $content['twae_story_link']['nofollow'] ) && ! empty( $content['twae_story_link']['nofollow'] ) ? ' rel="nofollow"' : '',
			'enable_link'                => isset( $content['twae_title_link'] ) && ! empty( $content['twae_title_link'] ) ? $content['twae_title_link'] : 'no',
			'button_txt'                 => isset( $content['twae_button_txt'] ) ? sanitize_text_field( $content['twae_button_txt'] ) : __( 'Read more', 'twea' ),
		);

		return $story_settings;

	}

	/**
	 * Generate slider arrow navigation HTML dynamically.
	 *
	 * @param string $icon_name The name of the icon.
	 * @param string $position The position of the arrow.
	 * @return string The HTML for the arrow navigation.
	 */
	public static function get_navi_control_icon( $icon_name, $position = 'left' ) {
		if ( false !== strpos( $icon_name, 'fa-' ) ) {

			$icon_type = strpos( $icon_name, 'far' ) === 0 || strpos( $icon_name, 'far' ) !== false ? 'fa-regular' : 'fa-solid';

			$svg_library = get_option( 'elementor_experiment-e_font_icon_svg', 'default' );

			// Enqueue nav arrow font awesome style if style not enqueued
			if ( ! wp_style_is( 'elementor-icons-' . $icon_type, 'enqueued' ) && wp_style_is( 'elementor-icons-' . $icon_type, 'registered' ) && 'inactive' === $svg_library ) {
				wp_enqueue_style( 'elementor-icons-' . $icon_type, 'enqueued' );
			}

			if ( 'right' === $position ) {
				$icon_name = str_replace( 'left', 'right', $icon_name );
			}

			$icon_data = array(
				'value'   => $icon_name,
				'library' => $icon_type,
			);

			$icon = self::twae_render_icon_html( $icon_data );

			return $icon;
		} else {
			return '';
		}
	}
}
