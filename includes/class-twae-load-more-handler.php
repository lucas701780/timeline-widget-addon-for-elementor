<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
use Elementor\Utils;
use Elementor\Controls_Stack;

/**
 *
 * This file is responsible for handling all AJAX requests
 */
class twae_loadmore_handler {

	public $settings;

	public function __construct() {
		add_action( 'wp_ajax_twae_post_load_more', array( $this, 'twae_post_load_more' ) );
		add_action( 'wp_ajax_nopriv_twae_post_load_more', array( $this, 'twae_post_load_more' ) );
		add_action( 'wp_ajax_twae_preset_feat', array( $this, 'twae_preset_feat' ) );
		add_action( 'wp_ajax_twae_process_ixport', array( $this, 'twae_process_media_import' ) );
	}

	/**
	 * This function is used to import media
	 */
	public function twae_process_media_import() {
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'twae_process_ixport' ) ) {
			wp_send_json_error(
				__( 'You are not allowed to complete this task, thank you.', 'twae' ),
				403
			);
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error(
				__( 'Not a valid user.', 'twae' ),
				403
			);
		}

		$content = isset( $_POST['content'] ) ? wp_unslash( $_POST['content'] ) : '';
		if ( empty( $content ) ) {
			wp_send_json_error( __( 'Looks like content is empty. Cannot be processed.', 'twae' ) );
		}

		$media = array( json_decode( $content, true ) );
		$media = $this->twae_replace_elements_ids( $media );
		$media = $this->twae_import_media_content( $media );

		wp_send_json_success( $media );
	}

	/**
	 * This function is used to Replace media items IDs.
	 */
	public function twae_replace_elements_ids( $media ) {
		return Elementor\Plugin::instance()->db->iterate_data(
			$media,
			function( $element ) {
				$element['id'] = Utils::generate_random_string();
				return $element;
			}
		);
	}

	/**
	 * This function is used to import media process.
	 */
	public function twae_import_media_content( $media ) {
		return Elementor\Plugin::instance()->db->iterate_data(
			$media,
			function( $element_instance ) {
				$element = Elementor\Plugin::instance()->elements_manager->create_element_instance( $element_instance );

				if ( ! $element ) {
					return null;
				}

				return $this->twae_import_content_process( $element );
			}
		);
	}

	/**
	 * Process element content for import.
	 */
	public function twae_import_content_process( Controls_Stack $element ) {
		$element_instance = $element->get_data();
		$method           = 'on_import';

		if ( method_exists( $element, $method ) ) {
			$element_instance = $element->{$method}( $element_instance );
		}

		foreach ( $element->get_controls() as $control ) {
			$control_class = Elementor\Plugin::instance()->controls_manager->get_control( $control['type'] );
			$control_name  = $control['name'];

			if ( ! $control_class ) {
				return $element_instance;
			}

			if ( method_exists( $control_class, $method ) ) {
				$element_instance['settings'][ $control_name ] = $control_class->{$method}( $element->get_settings( $control_name ), $control );
			}
		}

		return $element_instance;
	}

	public function twae_preset_feat() {
		if ( ! check_ajax_referer( 'twae_prset_nonce', 'nonce' ) ) {
			wp_send_json_error( __( 'Invalid surprise request', 'twae' ), 403 );
		}
		$design_name = isset( $_POST['widget'] ) ? sanitize_text_field( $_POST['widget'] ) : '';
		$design      = $this->twae_get_designs( $design_name );
		wp_send_json_success( $design, 200 );
	}

	public function twae_get_designs( $design_name ) {
		$design = TWAE_PRO_PATH . 'admin/preset/designs/' . $design_name . '.json';
		if ( ! is_readable( $design ) ) {
			return false;
		}
		return file_get_contents( $design );
	}

	/**
	 *
	 * This is a callback function response back with HTML for post timeline infinite scrolling load more
	 */
	public function twae_post_load_more() {
		if ( check_ajax_referer( 'twae_ajax_pagination', 'private_key' ) ) {
			$page_no = ! isset( $_POST['page_no'] ) ? 1 : filter_var( $_POST['page_no'], FILTER_SANITIZE_NUMBER_INT );

			$settings                 = isset( $_POST['settings'] ) ? $_POST['settings'] : array();
			$settings                 = $this->twae_attr_filter( $settings );
			$post_settings            = $settings;
			$query_args               = array();
			$post_per_page            = $settings['show_posts'];
			$query_args['post_type']  = $settings['post_type'];
			$query_args['order']      = $settings['order'];
			$query_args['show_posts'] = $post_per_page;
			$post_types               = isset( $settings['post_type'] ) ? $settings['post_type'] : 'post';
			$taxonomies               = get_object_taxonomies( $post_types );

			foreach ( $taxonomies as $taxonomy => $object ) {
				if ( 'post_format' === $object ) {
					continue;
				}
				$query_args[ 'twae_post_' . $object . '' ] = ! empty( $settings[ 'twae_post_' . $object . '' ] ) ? $settings[ 'twae_post_' . $object . '' ] : '';
			}
			$args = Twae_Functions::twae_pro_query_args( $query_args, $query = 'ajax', $page_no );
			unset( $args['offset'] );
			$args['paged'] = $page_no + 1;
			$query         = new WP_Query( $args );
			$index         = ( $post_per_page * $page_no ) + 1;

			// Twae content loop file for all layouts.
			require_once TWAE_PRO_PATH . 'widgets/content-timeline/twae-content-loop.php';

			// Make Twae_content_loop class object for getting loop content html.
			$loop_post_obj = new Twae_Content_Loop( $query, $post_settings );

			// Timeline loop content.
			$post_data = $loop_post_obj->twae_post_loop( esc_attr( $index ) );

			$highlighted_html = isset( $post_data['highlighted_content'] ) ? $post_data['highlighted_content'] : '';
			$post_html        = isset( $post_data['post_html'] ) ? $post_data['post_html'] : '';
			die(
				json_encode(
					array(
						'response'           => 'success',
						'html'               => $post_html,
						'highlightedcontent' => $highlighted_html,
					)
				)
			);
		}
	}

	public function twae_attr_filter( $attr ) {
		$symbols = array( '*', '(', ')', '[', ']', '{', '}', '"', "'", '\\', '/', ';', '$', '<', '>', '.', '”' );
		if ( is_array( $attr ) ) {
			$attributes = array();
			foreach ( $attr as $key => $values ) {
				if ( is_array( $values ) ) {
					$attributes[ $key ] = $this->nested_attr_filter( $values );
				} elseif ( 'date_format' === $key ) {
					$attributes[ $key ] = wp_kses_post( $values );
				} else {
					$value              = str_replace( $symbols, '', $values );
					$value              = esc_html( $value );
					$value              = preg_replace( ' / \s + / ', '', $value );
					$attributes[ $key ] = $value;
				}
			}
			return $attributes;
		} else {
			$attr = str_replace( $symbols, '', $attr );
			$attr = esc_html( $attr );
			$attr = preg_replace( ' / \s + / ', '', $attr );
			return esc_html( $attr );
		}
	}

	public function nested_attr_filter( $attr ) {
		$attribute = array();

		foreach ( $attr as $key => $value ) {
			if ( is_array( $value ) ) {
				$attribute[ $key ] = $this->nested_attr_filter( $value );
			} else {
				$attribute[ $key ] = $this->twae_attr_filter( $value );
			}
		}

		return $attribute;
	}
}
new twae_loadmore_handler();
