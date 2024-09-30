<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$title_tag    = 'div';
$line_filling = '';
$layout       = $post_settings['layout'];

if ( isset( $settings['center_line_filling'] ) && 'yes' === $settings['center_line_filling'] ) {
	$line_filling = 'on';
}

$container_cls = '';
if ( 'compact' === $layout ) {
	$container_cls = 'twae-compact';
}

$space           = '';
$pagination      = '';
$ajax_pagination = '';

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

$vertical_pagination_type = $post_settings['vertical_pagination_type'];
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
