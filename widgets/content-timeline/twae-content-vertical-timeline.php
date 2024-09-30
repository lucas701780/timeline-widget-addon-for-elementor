<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$title_tag    = 'div';
$line_filling = '';
$layout       = isset( $post_settings['layout'] ) ? sanitize_text_field( $post_settings['layout'] ) : '';

if ( isset( $settings['center_line_filling'] ) && 'yes' === $settings['center_line_filling'] ) {
	$line_filling = 'on';
}

$container_cls = '';
if ( 'compact' === $layout ) {
	$container_cls = 'twae-compact';
}

$space                = '';
$pagination           = '';
$ajax_pagination      = '';
$label_content_top    = isset( $post_settings['twae_post_label_content_top'] ) ? sanitize_text_field( $post_settings['twae_post_label_content_top'] ) : '';
$post_image_outside   = isset( $post_settings['twae_post_image_outside_box'] ) ? sanitize_text_field( $post_settings['twae_post_image_outside_box'] ) : '';
$label_content_inside = 'twae-label-content-top' !== $label_content_top && 'twae_image_outside' === $post_image_outside ? 'twae-label-content-inside' : ( isset( $post_settings['twae_post_label_inside'] ) ? sanitize_text_field( $post_settings['twae_post_label_inside'] ) : '' );

$wrp_attr = array(
	'id'    => 'twae-' . esc_attr( $widget_id ),
	'class' => array( 'twae-vertical', 'twae-wrapper', 'twae-post-timeline' ),
);

! empty( $timeline_style ) && $wrp_attr['data-style']      = esc_attr( $timeline_style );
! empty( $enable_popup ) && $wrp_attr['data-enable-popup'] = esc_attr( $enable_popup );
! empty( $line_filling ) && $wrp_attr['data-line-filling'] = esc_attr( $line_filling );
! empty( $timeline_layout_wrapper ) && array_push( $wrp_attr['class'], esc_attr( $timeline_layout_wrapper ) );
! empty( $timeline_style ) && array_push( $wrp_attr['class'], esc_attr( $timeline_style ) );
! empty( $twae_bg_type ) && array_push( $wrp_attr['class'], esc_attr( $twae_bg_type ) );
! empty( $pagination ) && array_push( $wrp_attr['class'], esc_attr( $pagination ) );
// Label content inside class.
! empty( $label_content_inside ) && array_push( $wrp_attr['class'], esc_attr( $label_content_inside ) );
// Label content top class.
! empty( $label_content_top ) && array_push( $wrp_attr['class'], esc_attr( $label_content_top ) );
// Image out of the box class.
! empty( $post_image_outside ) && array_push( $wrp_attr['class'], esc_attr( $post_image_outside ) );
// Background hover class.
isset( $post_settings['twae_bg_hover'] ) && ! empty( $post_settings['twae_bg_hover'] ) && array_push( $wrp_attr['class'], esc_attr( $post_settings['twae_bg_hover'] ) );

$vertical_pagination_type = isset( $post_settings['vertical_pagination_type'] ) ? sanitize_text_field( $post_settings['vertical_pagination_type'] ) : '';
$query                    = new \WP_Query( $args );
$total_pages              = $query->max_num_pages;
$wrapper                  = '';
$content                  = '';
$slider_top               = '';
$slider_bottom            = '';

if ( $query->have_posts() ) {

	$post_month  = '';
	$total_pages = $query->max_num_pages;

	if ( 'ajax_load_more' === $vertical_pagination_type ) {
		$infinite_pagi_attr = array(
			'data-ajax-pagination' => 'yes',
			'data-page-no'         => esc_attr( $args['paged'] ),
			'data-total-pages'     => esc_attr( $total_pages ),
			'data-widget-id'       => esc_attr( $widget_id ),
		);
		$wrp_attr           = array_merge( $wrp_attr, $infinite_pagi_attr );

	}

	$this->add_render_attribute(
		$widget_id,
		$wrp_attr
	);
	$this->add_render_attribute(
		'twae-line',
		array(
			'class' => array( 'twae-line', 'twae-timeline', esc_attr( $pagination ), esc_attr( $container_cls ) ),
		)
	);

	// Make Twae_content_loop class object for getting loop content html.
	$loop_post_obj = new Twae_Content_Loop( $query, $post_settings );
	// Timeline loop content.
	$post_data = $loop_post_obj->twae_post_loop( $count_item );

	$wrapper .= '<!-- ========= Timeline Widget Pro For Elementor ' . TWAE_PRO_VERSION . ' ========= -->
        <div ' . $this->get_render_attribute_string( $widget_id ) . '> 
        <div class="twae-start"></div> 
        <div ' . $this->get_render_attribute_string( 'twae-line' ) . '>';

	// Post Timeline Loop content html.
	$wrapper .= $post_data['post_html'];

	if ( isset( $settings['center_line_filling'] ) && 'yes' === $settings['center_line_filling'] ) {
		$wrapper .= '<div class="twae-inner-line"></div>';
	}
		$wrapper .= '</div>
            <div class="twae-end"></div>';

		$wrapper .= '<div class="twea-pagination">' . Twae_Functions::twae_post_pagination( $total_pages, $post_settings ) . '</div>';
		$wrapper .= '</div>';
} else {
	$wrapper .= '<!-- ========= Timeline Widget Pro For Elementor ' . TWAE_PRO_VERSION . ' ========= -->
                    <h3 class="twae-no-post">' . esc_html( $post_settings['no_post_msg'] ) . '</h3>';
}

	echo $wrapper;
	/* Restore original Post Data */
	wp_reset_postdata();
