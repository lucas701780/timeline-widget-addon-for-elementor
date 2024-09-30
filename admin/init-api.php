<?php
namespace TimelineWidgetAddonForElementorPro;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class TWAE_ApiConf {
	const PLUGIN_NAME    = 'Timeline Widget Pro For Elementor';
	const PLUGIN_VERSION = TWAE_PRO_VERSION;
	const PLUGIN_PREFIX  = 'twae';
	const PLUGIN_URL     = TWAE_PRO_URL;
}

	require_once 'class.settings-api.php';
	require_once 'TimelineProAddonForElementorBase.php';
	require_once 'api-auth-settings.php';

	new TWAE_Settings();
