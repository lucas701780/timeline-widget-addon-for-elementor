<?php
/**
 * Elementor presets control.
 *
 * A control for displaying a select field with the ability to choose presets.
 *
 * @since 1.0.0
 */
class Twae_Presets_Control extends \Elementor\Base_Data_Control {

	/**
	 * Get presets control type.
	 *
	 * Retrieve the control type, in this case `preset`.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Control type.
	 */
	public function get_type() {
		return 'twae_preset_style';
	}

	/**
	 * Get Default Settings.
	 *
	 * @since 1.35.1
	 * @access public
	 *
	 * @return array Settings.
	 */
	protected function get_default_settings() {
		return array(
			'label_block' => false,
			'multiple'    => false,
			'options'     => array(),
		);
	}

	public function enqueue() {
		$src = TWAE_PRO_URL . 'admin/preset/preset.main.js';
		wp_register_script(
			'twae-preset-design',
			esc_url( $src ),
			array( 'jquery' ),
			TWAE_PRO_VERSION,
			false
		);

		wp_enqueue_script( 'twae-preset-design' );
		wp_localize_script(
			'twae-preset-design',
			'twae_preset',
			array(
				'ajaxUrl' => esc_url( admin_url( 'admin-ajax.php' ) ),
				'nonce'   => wp_create_nonce( 'twae_prset_nonce' ),
			)
		);
	}

	/**
	 * Render presets control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>
		<div class="elementor-control-field">
			<label for="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<# var multiple = ( data.multiple ) ? 'multiple' : ''; #>
				<select id="<?php echo esc_attr( $control_uid ); ?>" class="elementor-select2" type="select2" {{ multiple }} data-setting="{{ data.name }}">
					<# _.each( data.options, function( option_title, option_value ) {
						var value = data.controlValue ? data.default : data.controlValue;
						if ( typeof value == 'string' ) {
							var selected = ( option_value === value ) ? 'selected' : '';
						} else if ( null !== value ) {
							var value = _.values( value );
							var selected = ( -1 !== value.indexOf( option_value ) ) ? 'selected' : '';
						}
						#>
					<option {{ selected }} value="{{ option_value }}">{{{ option_title }}}</option>
					<# } ); #>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}
}
