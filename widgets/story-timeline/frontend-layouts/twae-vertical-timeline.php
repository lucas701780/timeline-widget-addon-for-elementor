<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$widget_id           = $this->get_id();
$navigation_style    = isset( $settings['twae_navigation_style'] ) ? $settings['twae_navigation_style'] : 'style-1';
$navigation_position = isset( $settings['twae_navigation_position'] ) ? $settings['twae_navigation_position'] : 'right';
$space               = isset( $settings['twae_space_between']['size'] ) ? $settings['twae_space_between']['size'] : '20';
$count_item          = 1;
$multicolor          = 1; // set multicolor position.
$line_filling        = '';
$html                = '';
if ( isset( $settings['center_line_filling'] ) && 'yes' === $settings['center_line_filling'] ) {
	$line_filling = 'on';
}

if ( isset( $settings['twae_cbox_connector_style'] ) && ( 'default' === $settings['twae_cbox_connector_style'] || empty( $settings['twae_cbox_connector_style'] ) ) ) {
	if ( 'style-2' === $timeline_style ) {
		$twae_cbox_connector_style = 'twae-arrow-line';
	} else {
		$twae_cbox_connector_style = 'twae-arrow';
	}
} else {
	$twae_cbox_connector_style = isset( $settings['twae_cbox_connector_style'] ) ? $settings['twae_cbox_connector_style'] : 'twae-arrow';
}

$connector_html = '<div class="' . esc_attr( $twae_cbox_connector_style ) . '" ></div>';

// Background Type
if ( isset( $settings['twae_cbox_background_type'] ) && 'multicolor' === $settings['twae_cbox_background_type'] ) {
	$twae_bg_type = 'twae-bg-multicolor';
} elseif ( isset( $settings['twae_cbox_background_type'] ) && 'gradient' === $settings['twae_cbox_background_type'] ) {
	$twae_bg_type = 'twae-bg-gradient';
} else {
	$twae_bg_type = 'twae-bg-simple';
}

// Background Hover Type
if ( isset( $settings['twae_cbox_background_type_hover'] ) && 'simple' === $settings['twae_cbox_background_type_hover'] ) {
	$twae_bg_hover = 'twae-bg-hover';
} else {
	$twae_bg_hover = '';
}
$label_content_top    = 'no' !== $settings['twae_label_content_top'] ? $settings['twae_label_content_top'] : '';
$label_content_inside = 'no' !== $settings['twae_label_inside'] ? $settings['twae_label_inside'] : '';
// image outside condition
$image_outside = 'no' !== $settings['twae_image_outside_box'] && 'yes' !== $settings['twae_content_in_popup'] ? $settings['twae_image_outside_box'] : '';

// label content inside if image outside on
if ( 'twae-label-content-inside' !== $label_content_inside && 'twae-label-content-top' !== $label_content_top && 'twae_image_outside' === $image_outside ) {
	$label_content_inside = 'twae-label-content-inside';
};

$image_lightbox     = isset( $settings['twae_lightbox_settings'] ) && 'yes' !== $settings['twae_content_in_popup'] ? $settings['twae_lightbox_settings'] : '';
$image_hover_effect = isset( $settings['twae_image_hover_effect'] ) && 'yes' !== $settings['twae_content_in_popup'] ? $settings['twae_image_hover_effect'] : '';

$container_cls = '';

if ( 'compact' === $layout ) {
	$container_cls = 'twae-compact';
}

// added render attributes start.
$this->add_render_attribute(
	'navigation-bar',
	array(
		'id'    => 'twae-navigationBar-' . esc_attr( $widget_id ),
		'class' => array( 'twae-navigationBar', 'twae-out-viewport', 'twae-navigation-' . esc_attr( $navigation_position ), esc_attr( $navigation_style ) ),
	)
);

$twae_wrapper_attr = array(
	'id'    => 'twae-wrapper-' . esc_attr( $widget_id ),
	'class' => array( 'twae-vertical', 'twae-wrapper' ),
);

! empty( $timeline_style ) && $twae_wrapper_attr['data-style']      = esc_attr( $timeline_style );
! empty( $enable_popup ) && $twae_wrapper_attr['data-enable-popup'] = esc_attr( $enable_popup );
! empty( $line_filling ) && $twae_wrapper_attr['data-line-filling'] = esc_attr( $line_filling );
! empty( $timeline_layout_wrapper ) && array_push( $twae_wrapper_attr['class'], esc_attr( $timeline_layout_wrapper ) );
! empty( $timeline_style ) && array_push( $twae_wrapper_attr['class'], esc_attr( $timeline_style ) );
! empty( $twae_bg_type ) && array_push( $twae_wrapper_attr['class'], esc_attr( $twae_bg_type ) );

$this->add_render_attribute(
	'twae-wrapper',
	$twae_wrapper_attr
);

$twae_line_attr = array(
	'class' => array( 'twae-line', 'twae-timeline' ),
);
! empty( $container_cls ) && array_push( $twae_line_attr['class'], esc_attr( $container_cls ) );
$this->add_render_attribute(
	'twae-line',
	$twae_line_attr
);
// added render attributes end.

// Year Navigation start.
if ( 'compact' !== $layout ) {
	if ( 'yes' === $enable_navigation ) {
		$navbar_html  = '';
		$navbar_html .= '<nav ' . $this->get_render_attribute_string( 'navigation-bar' ) . '>';

		if ( 'style-3' === $navigation_style ) {
			$navbar_html .= '<div class="twae-nav-icon">
                <span></span>
                <span></span>
                <span></span>
            </div>';
		}
		$navbar_html .= '</nav>';

		$html .= $navbar_html;
	}
}
// Year Navigation start.

$twae_loop_obj = new Twae_Story_Loop( $settings );


$html      .= '<!-- ========= Timeline Widget Pro For Elementor ' . TWAE_PRO_VERSION . ' ========= -->';
$html      .= '<div ' . $this->get_render_attribute_string( 'twae-wrapper' ) . '>';
$html      .= '    <div class="twae-start"></div>';
$html      .= '    <div ' . $this->get_render_attribute_string( 'twae-line' ) . ' >';
$story_html = '';

if ( is_array( $data ) ) {
	foreach ( $data as $index => $content ) {
		$left_aligned = 'twae-story-right';
		if ( 'centered' === $layout || 'compact' === $layout ) {
			if ( 0 === $count_item % 2 ) {
				$left_aligned = 'twae-story-left';
			}
		}

		$story_settings  = Twae_Functions::twae_story_content_variables( $content );
		$title_key       = $this->get_repeater_setting_key( 'twae_story_title', 'twae_list', $index );
		$year_key        = $this->get_repeater_setting_key( 'twae_year', 'twae_list', $index );
		$date_label_key  = $this->get_repeater_setting_key( 'twae_date_label', 'twae_list', $index );
		$sub_label_key   = $this->get_repeater_setting_key( 'twae_extra_label', 'twae_list', $index );
		$description_key = $this->get_repeater_setting_key( 'twae_description', 'twae_list', $index );

		$article_key = 'twae-article-' . $content['_id'];

		if ( 'compact' !== $layout ) {
			if ( 'yes' !== $enable_popup ) {
				$this->add_inline_editing_attributes( $title_key, 'none' );
			}
			$this->add_inline_editing_attributes( $year_key, 'none' );
			$this->add_inline_editing_attributes( $date_label_key, 'none' );
			$this->add_inline_editing_attributes( $sub_label_key, 'none' );
			$this->add_inline_editing_attributes( $description_key, 'none' );
		}

		$this->add_render_attribute( $title_key, array( 'class' => 'twae-title' ) );
		$this->add_render_attribute( $year_key, array( 'class' => 'twae-year-label twae-year-text' ) );
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

		$repeator_item_key = 'elementor-repeater-item-' . $content['_id'];

		$twae_loop_obj->twae_story_loop( $content, $story_settings, $repeater_key, $twae_repeater_attributes, $enable_popup );
		$media = Twae_Functions::twae_get_story_media( $content, $dir, $image_lightbox, $image_hover_effect );

		$icon_data = $twae_loop_obj->twae_story_icon();
		$icon_cls  = $icon_data['icon_cls'];
		$icon_html = $icon_data['icon_html'];

		$article_key_attr = array(
			'id'    => 'twae-' . esc_attr( $content['_id'] ),
			'class' => array(
				'twae-story',
				esc_attr( $repeator_item_key ),
				'twae-repeater-item',
			),
		);

		! empty( $left_aligned ) && array_push( $article_key_attr['class'], esc_attr( $left_aligned ) );
		! empty( $icon_cls ) && array_push( $article_key_attr['class'], esc_attr( $icon_cls ) );
		! empty( $twae_bg_hover ) && array_push( $article_key_attr['class'], esc_attr( $twae_bg_hover ) );
		! empty( $label_content_top ) && array_push( $article_key_attr['class'], esc_attr( $label_content_top ) );
		! empty( $label_content_inside ) && array_push( $article_key_attr['class'], esc_attr( $label_content_inside ) );
		! empty( $image_outside ) && array_push( $article_key_attr['class'], esc_attr( $image_outside ) );
		'twae-bg-multicolor' === $twae_bg_type && $article_key_attr['data-multicolor'] = esc_attr( $multicolor );
		$this->add_render_attribute(
			$article_key,
			$article_key_attr
		);

		// run code only for old users.
		if ( false !== get_option( 'twae-v' ) ) {
			$post_id = $post->ID;
			if ( ! get_post_meta( $post_id, 'twae_style_migration', true ) ) {
				$story_styles .= $this->specific_story_style( $post_id, $content, $repeator_item_key, $timeline_style );

			}
		}

		$title_html       = $twae_loop_obj->twae_story_title( 'yes' === $enable_popup );
		$description_html = $twae_loop_obj->twae_story_desc();
		$label_html       = 'style-4' !== $timeline_style ? $twae_loop_obj->twae_story_label( $animation ) : '';

		if ( 'compact' !== $layout ) {
			$story_html .= $twae_loop_obj->twae_story_year_label();
		}

		$story_html .= '<div ' . $this->get_render_attribute_string( $article_key ) . '>';

		if ( empty( $label_content_top ) && empty( $label_content_inside ) ) {
			$story_html .= $label_html;
		}

		$story_html .= $icon_html;

		$story_html .= $connector_html;

		$story_html .= '<div class="twae-content"  data-aos="' . esc_attr( $animation ) . '">';

		if ( ! empty( $label_content_top ) || ! empty( $label_content_inside ) ) {
			$story_html .= $label_html;
		}

		if ( 'yes' === $enable_popup ) {
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

		// Story custom color styles for mutlicolor background.
		$story_styles .= $twae_loop_obj->twae_story_custom_color( $widget_id, true, $multicolor );

		// Story multicolor index.
		4 === $multicolor ? $multicolor = 1 : $multicolor++;

		$count_item++;
	}
	if ( isset( $settings['center_line_filling'] ) && 'yes' === $settings['center_line_filling'] ) {
		$story_html .= '<div class="twae-inner-line"></div>';
	}
}
$story_styles .= $twae_loop_obj->twae_story_custom_color( $widget_id, false );
$html         .= $story_html;
$html         .= '</div>
<div class="twae-end"></div>   
</div>';

echo $html;

