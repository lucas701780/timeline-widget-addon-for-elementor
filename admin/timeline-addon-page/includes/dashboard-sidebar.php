<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 *
 * Addon dashboard sidebar.
 */

if ( ! isset( $this->main_menu_slug ) ) {
	return false;
}

$cool_support_email = esc_url( 'https://coolplugins.net/support/' );
?>

<div class="cool-body-right">
	<a href="https://coolplugins.net" target="_blank"><img src="<?php echo esc_url( plugin_dir_url( $this->addon_file ) ) . 'assets/coolplugins-logo.png'; ?>" alt="<?php esc_attr__( 'Cool Plugins Logo', 'twae' ); ?>"></a>
	<ul>
	<li><?php echo esc_html__( 'Cool Plugins develops best timeline plugins for WordPress.', 'twae' ); ?></li>
	  <li><?php printf( esc_html__( 'Our timeline plugins have %1$s50000+%2$s active installs.', 'twae' ), '<b>', '</b>' ); ?></li>
	  <li><?php echo esc_html__( 'For any query or support, please contact plugin support team.', 'twae' ); ?>
	  <br><br>
	  <a href="<?php echo esc_url( $cool_support_email ); ?>" target="_blank" class="button button-secondary"><?php echo esc_html__( 'Premium Plugin Support', 'twae' ); ?></a>
	  <br><br>
	  </li>
	  <li><?php printf( esc_html__( 'We also provide %1$stimeline plugins customization%2$s services.', 'twae' ), '<b>', '</b>' ); ?> 
	  <br><br>
	  <a href="<?php echo esc_url( $cool_support_email ); ?>" target="_blank" class="button button-primary"><?php echo esc_html__( 'Hire Developer', 'twae' ); ?></a>
	  <br><br>
	  </li>
   </ul>
</div>

</div><!-- End of main container-->
