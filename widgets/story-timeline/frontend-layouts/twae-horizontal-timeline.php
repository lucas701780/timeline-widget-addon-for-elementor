<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
$html                   = '';
$widget_id              = $this->get_id();
$autoplay               = $settings['twae_autoplay'];
$sides_height           = $settings['twae_slides_height'];
$multicolor             = 1; // set multicolor position.
$navigation_hr_position = isset( $settings['twae_hr_navigation_position'] ) ? $settings['twae_hr_navigation_position'] : 'right';
$label_ht_show          = '';
if ( 'horizontal-highlighted' === $layout ) {
	$label_ht_show = 'yes';
}
$thumb_content          = isset( $settings['twae_content_side_by_side'] ) ? $settings['twae_content_side_by_side'] : '';
$label_content_inside   = 'no' !== $settings['twae_label_inside'] && 'horizontal-highlighted' !== $layout ? $settings['twae_label_inside'] : '';
$label_content_main_cls = ! empty( $label_content_inside ) ? 'label_content_top' : '';
$thumb                  = '';
if ( 'yes' === $thumb_content ) {
	$thumb = 'thumb';
};
$image_lightbox     = isset( $settings['twae_lightbox_settings'] ) && 'yes' !== $settings['twae_content_in_popup'] ? $settings['twae_lightbox_settings'] : '';
$image_hover_effect = isset( $settings['twae_image_hover_effect'] ) && 'yes' !== $settings['twae_content_in_popup'] ? $settings['twae_image_hover_effect'] : '';
$sides_to_show      = '';
if ( isset( $settings['twae_slides_to_show']['size'] ) && ! empty( $settings['twae_slides_to_show']['size'] ) ) {
	$sides_to_show = $settings['twae_slides_to_show']['size'];
} else {
	$sides_to_show = isset( $settings['twae_slides_to_show'] ) ? $settings['twae_slides_to_show'] : 2;
}

$hightlighted_showslides = isset( $settings['twae_highlighted_to_show'] ) ? $settings['twae_highlighted_to_show'] : 3;
$autoplay_stop_hover     = isset( $settings['twae_autoplaystop_mousehover'] ) ? $settings['twae_autoplaystop_mousehover'] : '';
$sides_to_show           = 'horizontal-highlighted' !== $layout ? $sides_to_show : $hightlighted_showslides;

$space_bw = isset( $settings['twae_h_space_bw']['size'] ) && ! empty( $settings['twae_h_space_bw']['size'] ) ? $settings['twae_h_space_bw']['size'] : 60;


$infinite_loop = isset( $settings['twae_infinite_loop'] ) ? $settings['twae_infinite_loop'] : 'false';
$twae_speed    = isset( $settings['twae_speed'] ) ? $settings['twae_speed'] : 1000;

if ( isset( $settings['navigation_control_icon'] ) ) {
	$control_icon    = $settings['navigation_control_icon'];
	$navi_left_icon  = Twae_Functions::get_navi_control_icon( $control_icon );
	$right_index     = str_replace( 'left', 'right', $control_icon );
	$navi_right_icon = Twae_Functions::get_navi_control_icon( $right_index );
} else {
	$navi_left_icon  = '<i class="fas fa-chevron-left"></i>';
	$navi_right_icon = '<i class="fas fa-chevron-right"></i>';
}

// Connector Type
if ( isset( $settings['twae_cbox_connector_style'] ) && ( 'default' === $settings['twae_cbox_connector_style'] || '' === $settings['twae_cbox_connector_style'] ) ) {
	if ( 'style-2' === $timeline_style || 'style-4' === $timeline_style ) {
		$twae_cbox_connector_style = 'twae-arrow-line';
	} else {
		$twae_cbox_connector_style = 'twae-arrow';
	}
} else {
	$twae_cbox_connector_style = isset( $settings['twae_cbox_connector_style'] ) ? $settings['twae_cbox_connector_style'] : 'twae-arrow';
}

// Horizontal Icon Position.
$twae_icon_position = '';
if ( 'horizontal-highlighted' !== $layout ) {
	if ( $settings['twae_icon_position']['size'] < 40 && $settings['twae_icon_position']['size'] >= 1 ) {
		$twae_icon_position = 'twae-position-40-minus';
	} elseif ( $settings['twae_icon_position']['size'] > 50 && $settings['twae_icon_position']['size'] <= 60 ) {
		$twae_icon_position = 'twae-position-50-60';
	} elseif ( $settings['twae_icon_position']['size'] > 60 && $settings['twae_icon_position']['size'] <= 100 ) {
		$twae_icon_position = 'twae-position-60-plus';
	} else {
		$twae_icon_position = 'twae-position-40-50';
	}
};

// Horizontal Center Line Filler.
if ( isset( $settings['center_line_filling'] ) && $settings['center_line_filling'] == 'yes' ) {
	$twae_line_filler = 'twae-line-filler';
} else {
	$twae_line_filler = '';
}

// Background Type.
if ( isset( $settings['twae_cbox_background_type'] ) && $settings['twae_cbox_background_type'] == 'multicolor' ) {
	$twae_bg_type = 'twae-bg-multicolor';
} elseif ( isset( $settings['twae_cbox_background_type'] ) && $settings['twae_cbox_background_type'] == 'gradient' ) {
	$twae_bg_type = 'twae-bg-gradient';
} else {
	$twae_bg_type = 'twae-bg-simple';
}

// Background Hover Type.
if ( isset( $settings['twae_cbox_background_type_hover'] ) && $settings['twae_cbox_background_type_hover'] == 'simple' ) {
	$twae_bg_hover = 'twae-bg-hover';
} else {
	$twae_bg_hover = '';
}

$connector_html = '<div class="' . esc_attr( $twae_cbox_connector_style ) . '" ></div>';

// Navigation bar attributes.
$this->add_render_attribute(
	'navigation-horizontal-bar',
	array(
		'id'    => 'twae-horizontal-navigationBar-' . esc_attr( $widget_id ),
		'class' => array( 'twae-horizontal-navigationBar', 'twae-horizontal-navigation-' . esc_attr( $navigation_hr_position ) ),
	)
);

$twae_wrapper_attr = array(
	'id'    => 'twae-wrapper-' . esc_attr( $widget_id ),
	'class' => array( 'twae-horizontal-timeline', 'twae-wrapper' ),
);

! empty( $timeline_style ) && $twae_wrapper_attr['data-style']      = esc_attr( $timeline_style );
! empty( $enable_popup ) && $twae_wrapper_attr['data-enable-popup'] = esc_attr( $enable_popup );
! empty( $timeline_layout_wrapper ) && array_push( $twae_wrapper_attr['class'], esc_attr( $timeline_layout_wrapper ) );
! empty( $timeline_style ) && array_push( $twae_wrapper_attr['class'], esc_attr( $timeline_style ) );
! empty( $twae_bg_type ) && array_push( $twae_wrapper_attr['class'], esc_attr( $twae_bg_type ) );
! empty( $label_content_main_cls ) && array_push( $twae_wrapper_attr['class'], esc_attr( $label_content_main_cls ) );

$twae_container_attr = array(
	'id'    => 'twae-slider-container',
	'class' => array( 'twae-slider-container', 'swiper-container' ),
);

! empty( $dir ) && $twae_container_attr['data-dir']                                   = esc_attr( $dir );
! empty( $sides_to_show ) && $twae_container_attr['data-slidestoshow']                = esc_attr( $sides_to_show );
! empty( $space_bw ) && $twae_container_attr['data-spacebw']                          = esc_attr( $space_bw );
! empty( $autoplay ) && $twae_container_attr['data-autoplay']                         = esc_attr( $autoplay );
! empty( $timeline_style ) && $twae_container_attr['data-style']                      = esc_attr( $timeline_style );
! empty( $infinite_loop ) && $twae_container_attr['data-infinite-loop']               = esc_attr( $infinite_loop );
! empty( $twae_speed ) && $twae_container_attr['data-speed']                          = esc_attr( $twae_speed );
! empty( $autoplay_stop_hover ) && $twae_container_attr['data-stop-autoplay-onhover'] = esc_attr( $autoplay_stop_hover );
! empty( $twae_line_filler ) && array_push( $twae_container_attr['class'], esc_attr( $twae_line_filler ) );
! empty( $thumb ) && array_push( $twae_container_attr['class'], esc_attr( $thumb ) );

// Main wrapper attributes.
$this->add_render_attribute(
	'twae-wrapper',
	$twae_wrapper_attr
);

// Storie slider container attributes.
$this->add_render_attribute(
	'twae-slider-container',
	$twae_container_attr
);

// Year slider container attributes.
$this->add_render_attribute(
	'twae-year-slider-container',
	array(
		'id'    => 'year-swiper-container',
		'class' => array( 'year-swiper-container', 'swiper-container' ),
	)
);

// Year Navigation html.
if ( 'yes' === $enable_navigation ) {
	$navbar_html  = '';
	$navbar_html .= '<div class="twae-hor-nav-wrapper"><div ' . $this->get_render_attribute_string( 'navigation-horizontal-bar' ) . '>';
	$navbar_html .= '  </div><div class="swiper-button-next twae-nav-next">
                            <i class="fas fa-chevron-right"></i></div>
                            <div class="swiper-button-prev twae-nav-prev">
                            <i class="fas fa-chevron-left"></i></div>';
	$navbar_html .= '</div>';
	$html        .= $navbar_html;
}

$twae_loop_obj = new Twae_Story_Loop( $settings );

// Default Style.
$html            .= '<!-- ========= Timeline Widget Pro For Elementor ' . TWAE_PRO_VERSION . ' ========= -->';
$html            .= '<div ' . $this->get_render_attribute_string( 'twae-wrapper' ) . '>';
$html            .= '<div class="twae-wrapper-inside">';
$highlighted_html = '';
if ( 'horizontal-highlighted' === $layout ) {
	$highlighted_html .= '<div class="twae-year-slider-section"><div ' . $this->get_render_attribute_string( 'twae-year-slider-container' ) . ' ><div class="twae-slider-wrapper swiper-wrapper">';
};
$story_html  = '<div ' . $this->get_render_attribute_string( 'twae-slider-container' ) . '>';
$story_html .= '<div  class="twae-slider-wrapper swiper-wrapper ' . esc_attr( $sides_height ) . '">';
if ( is_array( $data ) ) {
	foreach ( $data as $index => $content ) {
		$story_settings  = Twae_Functions::twae_story_content_variables( $content );
		$title_key       = $this->get_repeater_setting_key( 'twae_story_title', 'twae_list', $index );
		$year_key        = $this->get_repeater_setting_key( 'twae_year', 'twae_list', $index );
		$date_label_key  = $this->get_repeater_setting_key( 'twae_date_label', 'twae_list', $index );
		$sub_label_key   = $this->get_repeater_setting_key( 'twae_extra_label', 'twae_list', $index );
		$description_key = $this->get_repeater_setting_key( 'twae_description', 'twae_list', $index );

		$article_key       = 'twae-article-' . $content['_id'];
		$repeator_item_key = 'elementor-repeater-item-' . $content['_id'];

		$this->add_render_attribute( $title_key, array( 'class' => 'twae-title' ) );
		$this->add_render_attribute( $year_key, array( 'class' => 'twae-year-text' ) );
		$this->add_render_attribute( $date_label_key, array( 'class' => 'twae-label-big' ) );
		$this->add_render_attribute( $sub_label_key, array( 'class' => 'twae-label-small' ) );
		$this->add_render_attribute( $description_key, array( 'class' => 'twae-description' ) );

		$twae_repeater_attributes[ $title_key ]       = $this->get_render_attribute_string( $title_key );
		$twae_repeater_attributes[ $year_key ]        = $this->get_render_attribute_string( $year_key );
		$twae_repeater_attributes[ $date_label_key ]  = $this->get_render_attribute_string( $date_label_key );
		$twae_repeater_attributes[ $sub_label_key ]   = $this->get_render_attribute_string( $sub_label_key );
		$twae_repeater_attributes[ $description_key ] = $this->get_render_attribute_string( $description_key );


		$repeater_key                   = array();
		$repeater_key['title_key']      = $title_key;
		$repeater_key['year_key']       = $year_key;
		$repeater_key['date_label_key'] = $date_label_key;
		$repeater_key['sublabel_key']   = $sub_label_key;
		$repeater_key['desc_key']       = $description_key;

		$twae_loop_obj->twae_story_loop( $content, $story_settings, $repeater_key, $twae_repeater_attributes, $enable_popup );
		$media = Twae_Functions::twae_get_story_media( $content, $dir, $image_lightbox, $image_hover_effect );

		$icon_data = $twae_loop_obj->twae_story_icon();
		$icon_cls  = $icon_data['icon_cls'];
		$icon_html = $icon_data['icon_html'];

		$article_key_attr = array(
			'id'         => esc_attr( $article_key ),
			'class'      => array(
				'twae-story',
				'swiper-slide',
				esc_attr( $repeator_item_key ),
			),
			'data-index' => esc_attr( $index ),
		);

		! empty( $icon_cls ) && array_push( $article_key_attr['class'], esc_attr( $icon_cls ) );
		! empty( $twae_icon_position ) && array_push( $article_key_attr['class'], esc_attr( $twae_icon_position ) );
		! empty( $twae_bg_hover ) && array_push( $article_key_attr['class'], esc_attr( $twae_bg_hover ) );
		! empty( $label_content_inside ) && array_push( $article_key_attr['class'], esc_attr( $label_content_inside ) );
		'twae-bg-multicolor' === $twae_bg_type && $article_key_attr['data-multicolor'] = esc_attr( $multicolor );
		$this->add_render_attribute(
			$article_key,
			$article_key_attr
		);

		$title_html       = $twae_loop_obj->twae_story_title( 'yes' === $enable_popup );
		$description_html = $twae_loop_obj->twae_story_desc();
		$label_html       = 'style-4' !== $timeline_style ? $twae_loop_obj->twae_story_label( $animation ) : '';

		// run code only for old users.
		if ( false !== get_option( 'twae-v' ) ) {
			$post_id = $post->ID;
			if ( ! get_post_meta( $post_id, 'twae_style_migration', true ) ) {
				$story_styles .= $this->specific_story_style( $post_id, $content, $repeator_item_key, $timeline_style );
			}
		}

		// Horizontal highlighted content start.
		if ( 'horizontal-highlighted' === $layout ) {
			$ht_label_key = 'twae-year-main-div-' . $content['_id'];
			$this->add_render_attribute(
				$ht_label_key,
				array(
					'id'    => esc_attr( $ht_label_key ),
					'class' => array( 'twae-highlighted-hr', 'swiper-slide' ),
				)
			);

			$highlighted_html .= '<div ' . $this->get_render_attribute_string( $ht_label_key ) . '">';
			$highlighted_html .= $twae_loop_obj->twae_story_label( $animation );
			$highlighted_html .= $icon_html;
			$highlighted_html .= '</div>';
		};
		// Horizontal highlighted content end.

		// Story content html start.
		$story_html .= '<div  ' . $this->get_render_attribute_string( $article_key ) . '>';
		$story_html .= '<div class="twae-story-line"></div>';

		if ( 'horizontal-highlighted' !== $layout ) {
			$story_html .= $twae_loop_obj->twae_story_year_label();
			if ( empty( $label_content_inside ) ) {
				$story_html .= $label_html;
			};
			$story_html .= $icon_html;
		};

		$story_html            .= $connector_html;
		$image_highlighted_show = '';

		if ( 'horizontal-highlighted' === $layout && empty( $media ) ) {
			$image_highlighted_show .= ' twae-hg-image-not';
		}

		$story_html .= '<div class="twae-content' . esc_attr( $image_highlighted_show ) . '">';

		if ( ! empty( $label_content_inside ) ) {
			$story_html .= $label_html;
		};

		if ( 'yes' === $enable_popup ) {
			if ( empty( $label_content_inside ) ) {
				$story_html .= '<div class="twae-labels minimal-labels" data-aos="' . esc_attr( $animation ) . '">';
				$story_html .= $label_html;
				$story_html .= '</div>';
			};
			$story_html .= $title_html;
		} else {
			$story_html .= $title_html;
			$story_html .= $media;
			$story_html .= $description_html;
		}

		$story_html .= '</div>';

		// Story popup content start.
		if ( 'yes' === $enable_popup ) {
			$story_html .= $twae_loop_obj->twae_story_popup( $media );
		}
		// Story popup content end.

		$story_html .= '</div>';
		// Story content html end.

		// Story custom color styles for mutlicolor background.
		'twae-bg-multicolor' === $twae_bg_type && $story_styles .= $twae_loop_obj->twae_story_custom_color( $widget_id, $multicolor );

		// Story multicolor index.
		4 === $multicolor ? $multicolor = 1 : $multicolor++;
	}
}

if ( 'horizontal-highlighted' === $layout ) {
	$highlighted_html .= '</div></div></div>';
};
$story_html .= '</div></div>';
$html       .= $highlighted_html;
$html       .= $story_html;
$html       .= '</div>';
$html       .= ' <!-- Add Arrows -->
<div class="twae-button-prev">' . $navi_left_icon . '</div>
<div class="twae-button-next">' . $navi_right_icon . '</div>
<div class="twae-h-line"></div>
<div class="twae-line-fill"></div>
</div>';

echo $html;
