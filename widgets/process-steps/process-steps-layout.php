<?php

$data = $settings['cps_icon_list'];
echo '<!-- ========= Process Steps Widget For Elementor ' . esc_html( TWAE_PRO_VERSION ) . ' ========= -->';
if ( $settings['cps_process_layout'] == 'Horizontal' ) {
	$preset             = $settings['twae_preset_hr_style'];
	$pswfe_steps_layout = 'pswfe-steps';
} else {
	$preset             = $settings['twae_preset_vertical_style'];
	$pswfe_steps_layout = 'pswfe-vertical-steps';
}
 echo '<ul class="' . esc_attr( $pswfe_steps_layout ) . '  pswfe-process ' . esc_attr( $preset ) . '">';
if ( is_array( $data ) ) {

	if ( $layout == 'Horizontal' ) {
		$has_arrow  = ( $settings['cps_enable_connector'] == 'cps-connector-arrow' ) ? 'pswfe-has-arrow' : '';
		$has_gap    = ( $settings['cps_show_gap'] === 'yes' ) ? 'pswfe-has-gap' : '';
		$icon_badge = isset( $settings['cps_selected_badge'] ) ? $settings['cps_selected_badge'] : '1';

	} else {
		$has_arrow  = ( $settings['cps_enable_connector'] == 'cps-connector-arrow' ) ? 'pswfe-vertical-has-arrow' : '';
		$has_gap    = ( $settings['cps_show_gap'] === 'yes' ) ? 'pswfe-vertical-has-gap' : '';
		$icon_badge = isset( $settings['cps_selected_badge'] ) ? $settings['cps_selected_badge'] : '1';
	}
}//close array if condition

foreach ( $data as $key => $item ) {
	$open_new_tab = '_self';
	if ( ! empty( $item['cps_website_link']['url'] ) && $item['cps_website_link']['is_external'] == 'on' ) {
		$open_new_tab = '_blank';

	}

	$icon_type  = isset( $item['cps_selected_icon'] ) ? $item['cps_selected_icon'] : 'icon';
	$title_link = ( $item['cps_enable_link'] === 'yes' && ! empty( $item['cps_website_link']['url'] ) ) ? '<a href="' . esc_url( $item['cps_website_link']['url'] ) . '" target="' . esc_attr( $open_new_tab ) . '">' . wp_kses_post( $item['cps_title'] ) . '</a>' : wp_kses_post( $item['cps_title'] );

	$title_key       = $this->get_repeater_setting_key( 'cps_title', 'cps_icon_list', $key );
	$description_key = $this->get_repeater_setting_key( 'cps_description', 'cps_icon_list', $key );

	// inline attributes
	$this->add_inline_editing_attributes( $title_key, 'none' );
	$this->add_inline_editing_attributes( $description_key, 'advanced' );

	if ( $layout == 'Horizontal' ) {
		$psefe_title_var         = array( 'pswfe-title' );
		$pswfe_description_var   = array( 'pswfe-content-desc' );
		$pswfe_step_segment      = 'pswfe-steps-segment';
		$pswfe_badge             = 'pswfe-badge';
		$pswfe_step_marker       = 'pswfe-steps-marker';
		$pswfe_step_marker_text  = 'pswfe-marker-text ';
		$pswfe_step_marker_image = 'pswfe-marker-image ';
		$pswfe_step_content      = 'pswfe-steps-content';

	} else {
		$psefe_title_var         = array( 'pswfe-vertical-title' );
		$pswfe_description_var   = array( 'pswfe-vertical-content-desc' );
		$pswfe_step_segment      = 'pswfe-vertical-steps-segment';
		$pswfe_badge             = 'pswfe-vertical-badge';
		$pswfe_step_marker       = 'pswfe-vertical-steps-marker';
		$pswfe_step_marker_text  = 'pswfe-vertical-marker-text ';
		$pswfe_step_marker_image = 'pswfe-vertical-marker-image ';
		$pswfe_step_content      = 'pswfe-vertical-steps-content';

	}

	// class for title and description
	$this->add_render_attribute( $title_key, array( 'class' => $psefe_title_var ) );
	$this->add_render_attribute( $description_key, array( 'class' => $pswfe_description_var ) );

	echo '<li class="' . esc_attr( $pswfe_step_segment ) . ' pswfe-animation elementor-repeater-item-' . esc_attr( $item['_id'] ) . ' ' . esc_attr( $has_arrow ) . ' ' . esc_attr( $has_gap ) . '">';

	echo '<div class="' . esc_attr( $pswfe_step_marker ) . '">';

	if ( $icon_type == 'icon' ) {
		echo '<span class="' . esc_attr( $pswfe_step_marker_text ) . '">';
		\Elementor\Icons_Manager::render_icon( $item['cps_story_icon'], array( 'aria-hidden' => 'true' ) );
		echo '</span>';
	} elseif ( $icon_type == 'image' ) {

		echo '<img src="' . esc_url( $item['cps_icon_image']['url'] ) . '" class="' . esc_attr( $pswfe_step_marker_image ) . '">';
	} else {
		if ( $icon_type == 'customtext' ) {
			echo '<span class="' . esc_attr( $pswfe_step_marker_text ) . '">' . wp_kses_post( $item['cps_icon_text'] ) . '</span>';

		}
	}

	if ( $icon_badge == 'badge-customtext' ) {
		if ( isset( $item['cps_badge'] ) && ! empty( $item['cps_badge'] ) ) {
			$steps = ( strlen( $item['cps_badge'] ) > '2' ) ? 'steps' : '';
			echo '<span class="' . esc_attr( $pswfe_badge ) . ' ' . esc_attr( $steps ) . ' ' . esc_attr( $settings['cps_badge_position'] ) . '">' . wp_kses_post( $item['cps_badge'] ) . '</span>';
		}
	} else {
		echo '<span></span>';
	}

	echo '<div class="pswfe-hover-animation ' . esc_attr( $settings['cps_hover_animation'] ) . '"></div>';
	echo '</div>';


	if ( $item['cps_title'] != '' || $item['cps_description'] != '' ) {
		echo '<div class="' . esc_attr( $pswfe_step_content ) . '">';

		echo '<div ' . $this->get_render_attribute_string( $title_key ) . '>' . wp_kses_post( $title_link ) . '</div>';
		echo '<div ' . $this->get_render_attribute_string( $description_key ) . '>' . wp_kses_post( $item['cps_description'] ) . '</div>';

		echo '</div>';
	}
	echo '</li>';
}
echo '</ul>';
