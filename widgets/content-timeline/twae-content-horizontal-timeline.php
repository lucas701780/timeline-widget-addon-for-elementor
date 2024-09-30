<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$query           = new \WP_Query( $args );
$total_pages     = $query->max_num_pages;
$wrapper         = '';
$content         = '';
$slider_top      = '';
$slider_bottom   = '';
$pagination      = '';
$ajax_pagination = '';
$height_settings = isset( $settings['twae_slides_height'] ) ? $settings['twae_slides_height'] : 'default-height';
$sides_height    = $settings['twae_slides_height'];

	$label_ht_show = '';
if ( 'horizontal-highlighted' === $layout ) {
	$label_ht_show = 'yes';
}
	$label_content_top_class = ! empty( $post_settings['twae_post_label_inside'] ) && ( 'horizontal' === $post_settings['layout'] || 'horizontal-bottom' === $post_settings['layout'] ) ? 'label_content_top' : '';
	$thumb_content           = isset( $settings['twae_post_content_side_by_side'] ) ? $settings['twae_post_content_side_by_side'] : '';
	$thumb                   = '';
if ( 'yes' === $thumb_content ) {
	$thumb = 'thumb';
};

if ( isset( $settings['twae_post_slides_to_show']['size'] ) && ! empty( $settings['twae_post_slides_to_show']['size'] ) ) {
	$sides_to_show = $settings['twae_post_slides_to_show']['size'];
} else {
	$sides_to_show = isset( $settings['twae_post_slides_to_show'] ) ? $settings['twae_post_slides_to_show'] : 2;
}

$hightlighted_showslides  = isset( $settings['twae_highlighted_slides_to_show'] ) ? $settings['twae_highlighted_slides_to_show'] : 3;
$autoplay_post_stop_hover = isset( $settings['twae_post_autoplaystop_mousehover'] ) ? $settings['twae_post_autoplaystop_mousehover'] : '';
$space_bw                 = isset( $settings['twae_h_space_bw']['size'] ) && ! empty( $settings['twae_h_space_bw']['size'] ) ? $settings['twae_h_space_bw']['size'] : 20;
$infinite_loop            = isset( $settings['twae_infinite_loop'] ) ? $settings['twae_infinite_loop'] : 'false';
$sides_to_show            = 'horizontal-highlighted' !== $layout ? $sides_to_show : $hightlighted_showslides;

if ( 'horizontal-highlighted' === $layout ) {
	if ( 'yes' === $settings['twae_post_hr_ajax_loadmore'] ) {
		$infinite_loop = 'false';
	}
};
	$twae_speed = isset( $settings['twae_speed'] ) ? $settings['twae_speed'] : 1000;


// Horizontal Center Line Filler.
if ( isset( $settings['center_line_filling'] ) && 'yes' === $settings['center_line_filling'] ) {
	$twae_line_filler = 'twae-line-filler';
} else {
	$twae_line_filler = '';
}


if ( isset( $settings['navigation_control_icon'] ) ) {
	$control_icon    = $settings['navigation_control_icon'];
	$navi_left_icon  = Twae_Functions::get_navi_control_icon( $control_icon );
	$right_index     = str_replace( 'left', 'right', $control_icon );
	$navi_right_icon = Twae_Functions::get_navi_control_icon( $right_index );
} else {
	$navi_left_icon  = '<i class="fas fa-chevron-left"></i>';
	$navi_right_icon = '<i class="fas fa-chevron-right"></i>';
}

	$container_attr = array(
		'id'    => 'twae-slider-container',
		'class' => array( 'twae-slider-container', 'swiper-container', 'swiper-container-horizontal' ),
	);

	! empty( $dir ) && $container_attr['data-dir']                                        = esc_attr( $dir );
	! empty( $sides_to_show ) && $container_attr['data-slidestoshow']                     = esc_attr( $sides_to_show );
	! empty( $space_bw ) && $container_attr['data-spacebw']                               = esc_attr( $space_bw );
	! empty( $post_settings['autoplay'] ) && $container_attr['data-autoplay']             = esc_attr( $post_settings['autoplay'] );
	! empty( $infinite_loop ) && $container_attr['data-infinite-loop']                    = esc_attr( $infinite_loop );
	! empty( $twae_speed ) && $container_attr['data-speed']                               = esc_attr( $twae_speed );
	! empty( $autoplay_post_stop_hover ) && $container_attr['data-stop-autoplay-onhover'] = esc_attr( $autoplay_post_stop_hover );
	! empty( $twae_line_filler ) && array_push( $container_attr['class'], esc_attr( $twae_line_filler ) );
	! empty( $thumb ) && array_push( $container_attr['class'], esc_attr( $thumb ) );

	$highlighted_attr = array(
		'id'    => 'year-swiper-container',
		'class' => array( 'year-swiper-container', 'swiper-container' ),
	);

	if ( $query->have_posts() ) {

		$post_month  = '';
		$total_pages = $query->max_num_pages;
		if ( 'yes' === $post_settings['enable_hr_ajax_loadmore'] ) {
			$infinite_pagi_attr = array(
				'id'                   => 'twae-' . esc_attr( $widget_id ),
				'data-ajax-pagination' => 'yes',
				'data-page-no'         => esc_attr( $args['paged'] ),
				'data-total-pages'     => esc_attr( $total_pages ),
				'data-widget-id'       => esc_attr( $widget_id ),
			);
			$container_attr     = array_merge( $container_attr, $infinite_pagi_attr );
			$highlighted_attr   = array_merge( $highlighted_attr );
		}

		$twae_wrapper_attr = array(
			'id'    => 'twae-wrapper-' . esc_attr( $widget_id ),
			'class' => array( 'twae-horizontal-timeline', 'twae-wrapper', 'twae-post-timeline' ),
		);

		! empty( $timeline_style ) && $twae_wrapper_attr['data-style']      = esc_attr( $timeline_style );
		! empty( $enable_popup ) && $twae_wrapper_attr['data-enable-popup'] = esc_attr( $enable_popup );
		! empty( $timeline_layout_wrapper ) && array_push( $twae_wrapper_attr['class'], esc_attr( $timeline_layout_wrapper ) );
		! empty( $timeline_style ) && array_push( $twae_wrapper_attr['class'], esc_attr( $timeline_style ) );
		! empty( $twae_bg_type ) && array_push( $twae_wrapper_attr['class'], esc_attr( $twae_bg_type ) );
		! empty( $height_settings ) && array_push( $twae_wrapper_attr['class'], esc_attr( $height_settings ) );
		! empty( $label_content_top_class ) && array_push( $twae_wrapper_attr['class'], esc_attr( $label_content_top_class ) );

		$this->add_render_attribute(
			'twae-wrapper',
			$twae_wrapper_attr
		);

		$this->add_render_attribute(
			'twae-slider-container',
			$container_attr
		);
		$this->add_render_attribute(
			'twae-year-slider-container',
			$highlighted_attr
		);

		// Make Twae_content_loop class object for getting loop content html.
		$loop_post_obj = new Twae_Content_Loop( $query, $post_settings );
		// Timeline loop content.
		$post_data = $loop_post_obj->twae_post_loop( $count_item );

		$wrapper .= '<!-- ========= Timeline Widget Pro For Elementor ' . TWAE_PRO_VERSION . ' ========= -->
        <div ' . $this->get_render_attribute_string( 'twae-wrapper' ) . '>
        <div class="twae-wrapper-inside">';

		// Horizontal highlighted layout.
		if ( 'horizontal-highlighted' === $layout ) {
			$wrapper .= '<div class="twae-year-slider-section"><div ' . $this->get_render_attribute_string( 'twae-year-slider-container' ) . ' ><div class="twae-slider-wrapper swiper-wrapper">';
			$wrapper .= isset( $post_data['highlighted_content'] ) ? $post_data['highlighted_content'] : '';
			$wrapper .= '</div></div></div>';
		};

		// Timeline wrapper.
		$wrapper .= '<div ' . $this->get_render_attribute_string( 'twae-slider-container' ) . '>
            <div  class="twae-slider-wrapper swiper-wrapper ' . esc_attr( $sides_height ) . '">';

		// Post Timeline Loop content html.
		$wrapper .= $post_data['post_html'];

		$wrapper .= '</div>';
		$wrapper .= '</div></div>';
		$wrapper .= ' <!-- Add Arrows -->
                 <div class="twae-button-prev">' . $navi_left_icon . '</div>
                 <div class="twae-button-next">' . $navi_right_icon . ' <span class="lm_active_state" style="display:none"><i class="fas fa-spinner fa-spin"></i></span></div>
                 <div class="twae-h-line"></div>
                 <div class="twae-line-fill"></div>
        </div>';

	} else {
		$wrapper .= '<!-- ========= Timeline Widget Pro For Elementor ' . TWAE_PRO_VERSION . ' ========= -->
                     <h3 class="twae-no-post">' . esc_html( $post_settings['no_post_msg'] ) . '</h3>';
	}
	echo $wrapper;
	/* Restore original Post Data */
	wp_reset_postdata();
