<?php
namespace TimelineWidgetAddonForElementorPro;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
|--------------------------------------
|   API DATA VERIFICATION SETTINGS PAGE
|--------------------------------------
*/
class TWAE_Settings {

		private $verification_status;
		private $PREFIX;
		private $PLUGIN_NAME;
		private $PLUGIN_VER;
		private $PLUGIN_URL;
		private $settings_api;
		private $licenseMessage;
		private $Response;
		private $Base_File;
		private $plugin_purchase_url;
		private $plugin_documentation_url;
	public function __construct() {
		 $this->Base_File  = TWAE_PRO_FILE;
		$this->PLUGIN_NAME = TWAE_ApiConf::PLUGIN_NAME;
		$this->PREFIX      = TWAE_ApiConf::PLUGIN_PREFIX;
		$this->PLUGIN_URL  = TWAE_ApiConf::PLUGIN_URL;
		$this->PLUGIN_VER  = TWAE_ApiConf::PLUGIN_VERSION;
		// $this->settings_api = new CCPWP_Settings_API;
		$this->plugin_purchase_url      = 'https://coolplugins.net/product/timeline-widget-pro-addon-for-elementor/';
		$this->plugin_documentation_url = 'https://docs.coolplugins.net/';
		$this->verification_status      = 'License is not verified yet! ';

		$this->settings_api = \cool_plugins_timeline_registration_Settings::init();
		$this->settings_api->add_registration_page();
		add_action( 'admin_enqueue_scripts', array( $this, 'load_settings_scripts' ) );

		add_action( 'admin_init', array( $this, 'admin_init' ) );

		$this->settings_api->add_section( $this->PREFIX . '_license_registration', __( 'Timeline Widget Pro For Elementor - License', 'cmb2' ) );
		$this->settings_api->add_field(
			$this->PREFIX . '_license_registration',
			array(
				array(
					'name'        => $this->PREFIX . '-purchase-code',
					'id'          => $this->PREFIX . '-purchase-code',
					'class'       => $this->PREFIX . '-settings-field required',
					'label'       => 'Enter License Key',
					// 'desc'  => $this->save_purchase_code(),
					'placeholder' => __( 'Your Purchase / License Code', 'cmb2' ),
					'type'        => 'text',
					'default'     => '',
				),
				array(
					'name'        => $this->PREFIX . '-client-emailid',
					'id'          => $this->PREFIX . '-client-emailid',
					'class'       => $this->PREFIX . '-settings-field required',
					'label'       => 'Enter Email Id',
					'desc'        => $this->save_purchase_code(),
					'placeholder' => get_option( 'admin_email' ),
					'type'        => 'text',
					'default'     => get_option( 'admin_email' ),
				),
				array(
					'name'    => $this->PREFIX . '-validate-purchase-code',
					'id'      => $this->PREFIX . '-validate-purchase-code',
					'class'   => $this->PREFIX . '-settings-field',
					'desc'    => $this->ValidatePurchase(),
					'type'    => 'html',
					'default' => '',
				),
				array(
					'name'    => $this->PREFIX . '-issue-with-registration',
					'id'      => $this->PREFIX . '-issue-with-registration',
					'class'   => $this->PREFIX . '-settings-field',
					'label'   => 'Important Points',
					'desc'    => $this->find_purchase_code(),
					'type'    => 'html',
					'default' => '',
				),
			)
		);

		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_notices', array( $this, 'admin_registration_notice' ) );

		add_action( 'wp_ajax_' . $this->PREFIX . '_uninstall_license', array( $this, 'uninstall_license' ) );

			// send ticket from wp-backend
			// add_action('wp_ajax_submit_ticket', array($this, 'submit_ticket') );

			// add_action('wsa_form_top_coolpluginslicense_registration', array($this, 'thankyou_note'));
			// add_action('wsa_form_bottom_coolpluginslicense_registration', array($this, 'ValidatePurchase'));
	}

	/*
	|---------------------------------------------------
	|   Initialize settings
	|---------------------------------------------------
	*/
	public function admin_init() {
		  // initialize settings
			$this->settings_api->admin_init();
	}

	/*
	|--------------------------------------------------------------------
	|   Create multiple section in settings page using array in $sections
	|--------------------------------------------------------------------
	*/
	public function get_settings_sections() {
		   $sections = array(

			   array(
				   'id'    => $this->PREFIX . '_license_welcome',
				   'title' => __( 'Welcome', 'cmb2' ),
			   ),
			   array(
				   'id'    => $this->PREFIX . '_license_registration',
				   'title' => __( 'Registration', 'cmb2' ),
			   ),
			/*
				 array(
					'id' => $this->PREFIX.'_license_support',
					'title' => __('Support', 'cmb2'),
				) */
		   );
			return $sections;
	}
	function find_purchase_code() {
		// keep this function for backward compatibility
		$html = '
		<h4 class="cool-license-q">Q1) Where can I find my license key?</h4>
		<p class="cool-license-a">You can find license key inside your purchase order email or /my-account section in the website from where you purchased the plugin.</p>

		<h4 class="cool-license-q">Q2) Can I use single site license on another domain?</h4>
		<p class="cool-license-a">You need to deactivate license from current active site to use it on another domain. Remember to deactivate license before moving your site to another domain or server.</p>

		<h4 class="cool-license-q">Q3) Having trouble in license activation?</h4>
		<p class="cool-license-a">Please contact support at <a href="mailto:contact@coolplugins.net?subject=License Activation Issue" target="_blank">contact@coolplugins.net</a> along with your license key and domain url.</p>
		';
		return $html;
	}
	function trouble_with_activation() {

		$html = '<div id="' . $this->PREFIX . '_registration_help_notice">Please contact support along with your license key and domain url at <a href="mailto:contact@coolplugins.net;">contact@coolplugins.net</a>.</div>';

		return $html;
	}

	function save_purchase_code() {
		$html = "<div id='" . $this->PREFIX . "-verify-permission'><span class='" . $this->PREFIX . "-notice-red'>&#9989; I agree to share my purchase code and email for plugin verification and to receive future updates notifications!</span></div><div id='" . $this->PREFIX . "-activation-button'>" . $this->settings_api->_return_submit_button( 'Verify Key' ) . "</div>
            <div id='" . $this->PREFIX . "-deactivation-button'><a id='" . $this->PREFIX . "-uninstall-license' class='button button-secondary button-hero'>Uninstall Licence</a><br/><span class='" . $this->PREFIX . "-notice-red uninstall'>(* Uninstall license to use it on other website or hosting.)</span></div>";
		return $html;
	}

	/*
	|------------------------------------------------
	|   Create custom wrapper div for settings page
	|------------------------------------------------
	*/
	public function auth_settings_page() {
		  // $html =
			// "<div class='wrap'>
			// <div id='message' class='notice top'>
					// <p><strong>Server info:</strong></p>
					// <p><strong>Domain:</strong> ".get_site_url()."</p>
					// <p><strong>Email Id:</strong> ".get_option('admin_email')."</p>
				// </div>";

			// $html .="</div>";
			// echo $html;

			$this->settings_api->show_navigation();
			$this->settings_api->show_forms( 'Save', false );

	}

	/*
	|---------------------------------------------------------------
	| This function generate custom message on loading the settings
	|---------------------------------------------------------------
	*/
	public function ValidatePurchase() {
		$purchase    = $this->ce_get_option( $this->PREFIX . '-purchase-code' );
		$admin_email = $this->ce_get_option( $this->PREFIX . '-client-emailid' );
		if ( isset( $_GET['settings-updated'] ) || ! empty( $purchase ) ) {
			if ( ! empty( $purchase ) ) {
				$registration = "<div class='wrap'>";
				$registration = "<div class='" . $this->PREFIX . "-verification-notice'>
                    <p><strong>License Verification Status:</strong>";
				$verified     = TimelineProAddonForElementorBase::CheckWPPlugin( $purchase, $admin_email, $this->licenseMessage, $this->Response, $this->Base_File );
				if ( $verified && $this->Response->is_valid ) {
					$this->verification_status = 'Verified!';
					set_transient( $this->PREFIX . '_api_data_verification', 'done', 0 );
					$registration .= "<span class='" . $this->PREFIX . "_verification_enable'>&nbsp; &#9989; &nbsp;</span>";
				} else {
					$registration .= "<span class='" . $this->PREFIX . "_verification_disable'>&nbsp; &#10060; &nbsp;</span>";
					$this->flush_cache();
					$this->verification_status .= $this->licenseMessage;
				}
				$registration .= $this->verification_status;
				$registration .= '</p></div>';
				$registration .= "<p><strong>Developer's Support Validity Status:</strong>";
				$support_end   = ! empty( $this->Response->support_end ) ? $this->Response->support_end : '';
				if ( $support_end == 'Unlimited' && $support_end != '' ) {
					$registration .= "<span class='" . $this->PREFIX . "_verification_enable'>&nbsp; &#9989; Unlimited!</span>";
				} elseif ( $support_end == 'No Support' ) {
					$registration .= "<span class='" . $this->PREFIX . "_verification_disable'>&nbsp; &#10060; &nbsp; N/A</span>";
				} elseif ( $verified && strcmp( $support_end, 'No support' ) > 0 || $support_end != '' ) {
					$date    = new \DateTime( $this->Response->support_end );
					$nowDate = new \DateTime();
					$diff    = $date->diff( $nowDate );
					if ( ( $diff->y > 0 || $diff->m > 0 || $diff->d > 0 || $diff->h > 0 || $diff->i > 0 ) && $diff->invert == 0 ) {
						$registration .= "<span class='" . $this->PREFIX . "_verification_disable'>&nbsp; &#10060; &nbsp;</span>";
					} else {
						$registration .= "<span class='" . $this->PREFIX . "_verification_enable'>&nbsp; &#9989; &nbsp;</span>";
					}
					$registration .= $support_end . '</p></div>';
				} else {
					$registration .= "<span class='" . $this->PREFIX . "_verification_disable'>&nbsp; &#10060; &nbsp; N/A</span>";
				}
				return $registration;
			} else {
				$empty_code = "<span class='" . $this->PREFIX . "-notice-red'>**Purchase code can not be empty!</span>";
				return $empty_code;
			}
		} else {
			$empty_code = "<span class='" . $this->PREFIX . "-notice-red'>&#9785; Don't have a license? <a href='" . esc_url( $this->plugin_purchase_url ) . "' target='_blank'>Check Here To Purchase</a></span>";
			return $empty_code;
		}
	}

	/*
	|---------------------------------------------------------
	|   Gather settings field-values like get_options()
	|---------------------------------------------------------
	*/
	public function ce_get_option( $option, $default = '' ) {
		$section = $this->PREFIX . '_license_registration';
		$options = get_option( $section );

		if ( isset( $options[ $option ] ) ) {
			return $options[ $option ];
		}

		return $default;
	}

	/*
	|---------------------------------------------------------
	|   Display shortcode at the bottom of settings page.
	|---------------------------------------------------------
	|   An API token must be generated to use the shortcode
	|---------------------------------------------------------
	*/
	function display_support_form() {
		$html = "<div class='notice alightleft'>
                <h3>Submit a ticket</h3>
                <textarea id='ccpa_plugin_support' rows='8' cols='50'></textarea><br/>
                <a class='button-primary' id='support_btn'>Submit</a></div>";
		echo $html;
	}

	/*
	|---------------------------------------------------------
	|   Function accessed through AJAX
	|---------------------------------------------------------
	|   uninstall license
	|---------------------------------------------------------
	*/
	function uninstall_license() {
		unset( $settings[ $this->PREFIX . '-purchase-code' ] );
		update_option( $this->PREFIX . '_license_registration', '' );
		delete_transient( $this->PREFIX . '_api_data_verification' );
		die(json_encode(array('Response' => '200', 'Message' => 'License Successfully Uninstalled.')));		
		$message = '';
		if ( wp_verify_nonce( $_REQUEST['_password'], 'purchase-verify' ) == true ) {
			$response = TimelineProAddonForElementorBase::RemoveLicenseKey( $this->Base_File, $message );

			if ( $response == false ) {
				die(
					json_encode(
						array(
							'Response' => '403',
							'Message'  => 'Unable to contact to the server at the moment.',
						)
					)
				);
			}
		} else {
			die(
				json_encode(
					array(
						'Response' => '403',
						'Message'  => 'Access denied due to expired/unauthorized url access.',
					)
				)
			);
		}

		$this->flush_cache();
		die(
			json_encode(
				array(
					'Response' => '200',
					'Message'  => $message,
				)
			)
		);
	}

	/*
	|---------------------------------------------------------
	|   Submit ticket from WordPress back-end
	|---------------------------------------------------------
	*/
	/*
	function submit_ticket(){
		$email_from = get_option('admin_email');
		$email_to = "contact@cooltimeline.com";

		$message = '<html><body>';
		$message .= '<h3>A ticket is received from WordPress admin support form.</h3>';
		$message .= '<p><strong>URL</strong>:'.get_site_url().'</p>';
		$message .= '<p><strong>Admin Email</strong>:'.$email_from. '</p>';
		$message .= '<p>Message: '.$_POST['request'].'<p>';
		$message .= '</body></html>';

		$subject = '['.get_site_url().']Support ticket from WordPress back-end';

		$headers  = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= "From:<".$email_from."> \r\n";
		$headers .= "Reply-To: ".$email_to."\r\n";

		$mail=wp_mail( $email_to, $subject, $message, $headers);
		echo 'Ticket submited successfully';
		exit();
	}
	*/

	/*
	|----------------------------------------------------------------
	|   Admin registration notice for un-registered admin users only
	|----------------------------------------------------------------
	*/
	function admin_registration_notice() {
		if ( ! current_user_can( 'manage_options' ) || get_transient( $this->PREFIX . '_api_data_verification' ) == 'done' ) {
			return;
		}
		$current_user = wp_get_current_user();
		$user_name    = $current_user->display_name;
		?>
				<div class="license-warning notice notice-error is-dismissible">
					<p>Hi, <strong><?php echo esc_html( ucwords( $user_name ) ); ?></strong>! Please <strong><a href="<?php echo esc_url( get_admin_url( null, 'admin.php?page=timeline-addons-license#twae_license_registration' ) ); ?>">enter and activate</a></strong> your license key for <strong><?php echo $this->PLUGIN_NAME; ?></strong> plugin for unrestricted and full access of all premium features.</p>
				</div>
			<?php
	}

	/*
	|------------------------------------------------------------
	|   Load css/js script(s) file(s) for settings admin page
	|------------------------------------------------------------
	*/
	function load_settings_scripts() {

		if ( isset( $_GET['page'] ) && ( htmlspecialchars( $_GET['page'], ENT_QUOTES, 'UTF-8' ) == 'timeline-addons-license' ) ) {

			wp_enqueue_style( $this->PREFIX . '-settings-style', $this->PLUGIN_URL . 'assets/css/api-auth-settings.css', null, $this->PLUGIN_VER );
			wp_enqueue_script( $this->PREFIX . '-settings-script', $this->PLUGIN_URL . 'assets/js/api-auth-settings.js', array( 'jquery' ), $this->PLUGIN_VER );
			wp_localize_script(
				$this->PREFIX . '-settings-script',
				'ajax_object',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'verify'   => wp_create_nonce( 'purchase-verify' ),
				)
			);
		}

	}

	/*
	|-----------------------------------------------------------|
	|   Flush cache: All Home sweeping code must be here        |
	|   Run after license uninstall or failed verification      |
	|-----------------------------------------------------------|
	*/
	function flush_cache() {
		$settings = get_option( $this->PREFIX . '_license_registration' );
		unset( $settings[ $this->PREFIX . '-purchase-code' ] );
		update_option( $this->PREFIX . '_license_registration', $settings );
		delete_transient( $this->PREFIX . '_api_data_verification' );
	}

}   // end of class
