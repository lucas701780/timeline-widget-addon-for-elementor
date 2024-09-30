<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'WPML_Elementor_Module_With_Items' ) && ! class_exists( 'TWAE_WPML_PROCESS_STEPS' ) ) {
	/**
	 * Wpml translation widget register.
	 */
	class TWAE_WPML_PROCESS_STEPS extends WPML_Elementor_Module_With_Items {
		/**
		 * Return widget repeater fields name.
		 *
		 * @return string
		 */
		public function get_items_field() {
			return 'cps_icon_list';
		}

		/**
		 * Return widget fields name.
		 *
		 * @return array
		 */
		public function get_fields() {
			return array( 'cps_badge', 'cps_icon_text', 'cps_title', 'cps_description' );
		}

		/**
		 * @param string $field
		 *
		 * @return string
		 */
		protected function get_title( $field ) {
			$field = sanitize_text_field( $field ); // Sanitize input to prevent XSS
			switch ( $field ) {
				case 'cps_badge':
					return esc_html__( 'Process Step: Badge Text', 'twae' );

				case 'cps_icon_text':
					return esc_html__( 'Process Step: Icon Text', 'twae' );

				case 'cps_title':
					return esc_html__( 'Process Step: Title', 'twae' );

				case 'cps_description':
					return esc_html__( 'Process Step: Description', 'twae' );

				default:
					return '';
			}
		}

		/**
		 * @param string $field
		 *
		 * @return string
		 */
		protected function get_editor_type( $field ) {
			$field = sanitize_text_field( $field ); // Sanitize input to prevent XSS
			switch ( $field ) {
				case 'cps_badge':
					return 'LINE';

				case 'cps_description':
					return 'VISUAL';

				case 'cps_icon_text':
					return 'LINE';

				case 'cps_title':
					return 'LINE';

				default:
					return '';
			}
		}

	}
}
