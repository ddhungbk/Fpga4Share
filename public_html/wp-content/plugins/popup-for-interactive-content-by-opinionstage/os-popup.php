<?php
/*
Plugin Name: Popup a Poll, Survey, Quiz or Form by OpinionStage
Plugin URI: http://www.opinionstage.com
Description: Popup interactive content to your users. The popup boosts time on site and is a great way to communicate with your users.
Version: 2.5.0
Author: OpinionStage.com
Author URI: http://www.opinionstage.com
Text Domain: interactive-content-popup-by-opinionstage
*/

/* --- Constants --- */

define('OS_POPUP_SERVER_BASE', "www.opinionstage.com"); /* Don't include the protocol, added dynamically */
define('OS_POPUP_WIDGET_VERSION', '2.5.0');
define('OS_POPUP_WIDGET_PLUGIN_NAME', 'Popup a Poll, Survey, Quiz or Form by OpinionStage');
define('OS_POPUP_WIDGET_API_KEY', '758aa9');
define('OS_POPUP_OPTIONS_KEY', 'opinionstage_popup');
define('OS_POPUP_WIDGET_UNIQUE_ID', 'popup-for-interactive-content-by-opinionstage');
define('OS_POPUP_WIDGET_UNIQUE_LOCATION', __FILE__);
define('OS_POPUP_WIDGET_MENU_NAME', 'Popup a Poll, Survey, Quiz');
define('OS_POPUP_LOGIN_PATH', OS_POPUP_SERVER_BASE."/integrations/wordpress/new");
define('OS_POPUP_API_PATH', OS_POPUP_SERVER_BASE."/api/v1");
define('OS_POPUP_POLL_SHORTCODE', 'socialpoll');
define('OS_POPUP_WIDGET_SHORTCODE', 'os-widget');
define('OS_POPUP_PLACEMENT_SHORTCODE', 'osplacement');
require_once(WP_PLUGIN_DIR."/".OS_POPUP_WIDGET_UNIQUE_ID."/os-popup-functions.php");
require_once(WP_PLUGIN_DIR."/".OS_POPUP_WIDGET_UNIQUE_ID."/os-popup-ajax-functions.php");

// Check if OpinionStage plugin already installed.
if (os_popup_check_plugin_available('opinionstage_widget')) {
	add_action('admin_notices', 'os_popup_other_plugin_installed_warning');
} else {
	/* --- Wordpress hooks initialization --- */

	add_shortcode(OS_POPUP_POLL_SHORTCODE, 'os_popup_add_poll_or_set');
	add_shortcode(OS_POPUP_WIDGET_SHORTCODE, 'os_popup_add_widget');
	add_shortcode(OS_POPUP_PLACEMENT_SHORTCODE, 'os_popup_add_placement');

	// Plugin loaded callback
	add_action('plugins_loaded', 'os_popup_init');

	// Add the interactive content popup to the header
	add_action('wp_head', 'os_add_popup');

	// Side menu
	add_action('admin_menu', 'os_popup_poll_menu');
	add_action('admin_enqueue_scripts', 'os_popup_load_scripts');
}
?>