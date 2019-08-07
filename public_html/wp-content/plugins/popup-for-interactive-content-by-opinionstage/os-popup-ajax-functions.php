<?php

add_action( 'wp_ajax_os_popup_ajax_toggle_flyout', 'os_popup_ajax_toggle_flyout');
// Toggle the flyout placement activation flag
function os_popup_ajax_toggle_flyout() {
	$os_options = (array) get_option(OS_POPUP_OPTIONS_KEY);
	$os_options['fly_out_active'] = $_POST['activate'];
	update_option(OS_POPUP_OPTIONS_KEY, $os_options);
}
?>