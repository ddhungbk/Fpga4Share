<?php
/* --- Wordpress Hooks Implementations --- */

/**
 * Initialization of the plugin 
 */
function os_popup_init() {
	os_popup_initialize_data();
	register_uninstall_hook(OS_POPUP_WIDGET_UNIQUE_LOCATION, 'os_popup_uninstall');
}

/**
 * Removal of the plugin data
 */
function os_popup_uninstall() {
    delete_option(OS_POPUP_OPTIONS_KEY);
}

/**
 * Initialization of the data options
 */
function os_popup_initialize_data() {
	$os_options = (array) get_option(OS_POPUP_OPTIONS_KEY);
	$os_options['version'] = OS_POPUP_WIDGET_VERSION;
	
	// For backward compatibility
	if (!isset($os_options['sidebar_placement_active'])) {
		$os_options['sidebar_placement_active'] = 'false';
	}
	
	update_option(OS_POPUP_OPTIONS_KEY, $os_options);
}

/**
 * Admin sidebar menu
 */
function os_popup_poll_menu() {
	if (function_exists('add_menu_page')) {
		add_menu_page(__(OS_POPUP_WIDGET_MENU_NAME, OS_POPUP_WIDGET_UNIQUE_ID), __(OS_POPUP_WIDGET_MENU_NAME, OS_POPUP_WIDGET_MENU_NAME), 'edit_posts', OS_POPUP_WIDGET_UNIQUE_LOCATION, 'os_popup_admin_page', 
			plugins_url(OS_POPUP_WIDGET_UNIQUE_ID.'/images/os.png'), '25.234323221');
		add_submenu_page(null, __('', OS_POPUP_WIDGET_MENU_NAME), __('', OS_POPUP_WIDGET_MENU_NAME), 'edit_posts', OS_POPUP_WIDGET_UNIQUE_ID.'/os-popup-callback.php');
	}
}
/**
 * Load the js script
 */
function os_popup_load_scripts() {
	wp_enqueue_script( 'ospopup', plugins_url(OS_POPUP_WIDGET_UNIQUE_ID.'/os_popup_plugin.js'), array( 'jquery'), '3' );
}

/**
 * Style file loading
 */
function os_popup_add_stylesheet() {
	wp_register_style( 'os-popup-style', plugins_url('osstyle.css', __FILE__) );
	wp_register_style( 'os-popup-font-style', plugins_url('osfont.css', __FILE__) );
	wp_enqueue_style( 'os-popup-style' );
	wp_enqueue_style( 'os-popup-font-style' );	
}

/**
 * Generates a link for editing the popup settings on Opinion Stage site
 */
function os_popup_edit_url() {
	$os_options = (array) get_option(OS_POPUP_OPTIONS_KEY);
	return 'http://'.OS_POPUP_SERVER_BASE.'/containers/'.$os_options['fly_id'].'/edit';
}

/**
 * Generates a to the callback page used to connect the plugin to the Opinion Stage account
 */
function os_popup_callback_url() {
	return get_admin_url('', '', 'admin') . 'admin.php?page='.OS_POPUP_WIDGET_UNIQUE_ID.'/os-popup-callback.php';
}

/**
 * Take the received data and parse it
 * 
 * Returns the newly updated widgets parameters.
*/
function os_popup_parse_client_data($raw_data) {
	$os_options = array('uid' => $raw_data['uid'], 
						   'email' => $raw_data['email'],
						   'fly_id' => $raw_data['fly_id'],
						   'article_placement_id' => $raw_data['article_placement_id'],
						   'sidebar_placement_id' => $raw_data['sidebar_placement_id'],
						   'version' => OS_POPUP_WIDGET_VERSION,
						   'fly_out_active' => 'false',
						   'article_placement_active' => 'false',
						   'sidebar_placement_active' => 'false',
						   'token' => $raw_data['token']);
							   
	update_option(OS_POPUP_OPTIONS_KEY, $os_options);
}

/**
 * Add the flyout embed code to the page header
 */
function os_add_popup() {
	$os_options = (array) get_option(OS_POPUP_OPTIONS_KEY);
	
	if (!empty($os_options['fly_id']) && $os_options['fly_out_active'] == 'true' && !is_admin() ) {
		// Will be added to the head of the page
		?>
		 <script type="text/javascript">//<![CDATA[
			window.AutoEngageSettings = {
			  id : '<?php echo $os_options['fly_id']; ?>'
			};
			(function(d, s, id){
			var js,
				fjs = d.getElementsByTagName(s)[0],
				p = (('https:' == d.location.protocol) ? 'https://' : 'http://'),
				r = Math.floor(new Date().getTime() / 1000000);
			if (d.getElementById(id)) {return;}
			js = d.createElement(s); js.id = id; js.async=1;
			js.src = p + '<?php echo OS_POPUP_SERVER_BASE; ?>' + '/assets/autoengage.js?' + r;
			fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'os-jssdk'));
			
		//]]></script>
		
		<?php
	}
}

/**
 * Instructions page for adding a poll 
 */
function os_popup_admin_page() {
	os_popup_add_stylesheet();
	$os_options = (array) get_option(OS_POPUP_OPTIONS_KEY);
	if (empty($os_options["uid"])) {
		$first_time = true;
	} else {
		$first_time = false;
	}
	?>
	<script type='text/javascript'>
		jQuery(document).ready(function($) {
		    var callbackURL = "<?php echo os_popup_callback_url()?>";
			var toggleSettingsAjax = function(currObject, action) {	
				$.post(ajaxurl, {action: action, activate: currObject.is(':checked')}, function(response) { });
			};

			$('#os-start-login').click(function(){
				var emailInput = $('#os-email');
				var email = $(emailInput).val();
				if (email == emailInput.data('watermark')) {
					email = "";
				}
				var new_location = "http://" + "<?php echo OS_POPUP_LOGIN_PATH.'?o='.OS_POPUP_WIDGET_API_KEY.'&callback=' ?>" + encodeURIComponent(callbackURL) + "&email=" + email;
				window.location = new_location;
			});
			
			$('#os-switch-email').click(function(){
				var new_location = "http://" + "<?php echo OS_POPUP_LOGIN_PATH.'?o='.OS_POPUP_WIDGET_API_KEY.'&callback=' ?>" + encodeURIComponent(callbackURL);
				window.location = new_location;
			});
			
			$('#os-email').keypress(function(e){
				if (e.keyCode == 13) {
					$('#os-start-login').click();
				}
			});

			$('#fly-out-switch').change(function(){
				toggleSettingsAjax($(this), "os_popup_ajax_toggle_flyout");
			});
		});
		
	</script>  		
	<div id="opinionstage-content">
		<div id="opinionstage-frame">
			<div class="opinionstage-header-wrapper">				
				<div class="opinionstage-menu-wrapper">
					<div class="opinionstage-logo-wrapper">
						<div class="opinionstage-logo"></div>
					</div>				
				</div>				
				<div class="opinionstage-status-wrapper">
					<div class="opinionstage-status-content">
						<?php if($first_time) {?>
							<div class='opinionstage-status-title'>Connect WordPress with Opinion Stage to start using the Popup</div>
							<div class="os-icon icon-os-poll-client"></div>
							<input id="os-email" type="text" value="" class="watermark" data-watermark="Enter Your Email"/>
							<a href="javascript:void(0)" class="opinionstage-blue-btn" id="os-start-login">CONNECT</a>
						<?php } else { ?>
							<div class='opinionstage-status-title'><b>You are connected</b> to Opinion Stage with the following email</div>
							<div class="os-icon icon-os-form-success"></div>
							<label class="checked" for="user-email"></label>
							<input id="os-email" type="text" disabled="disabled" value="<?php echo($os_options["email"]) ?>"/>
							<a href="javascript:void(0)" id="os-switch-email" >Switch account</a>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="opinionstage-dashboard">									
				<div class="opinionstage-dashboard-left">
					<div id="opinionstage-section-placements" class="opinionstage-dashboard-section <?php echo($first_time ? "opinionstage-disabled-section" : "")?>">
						<div class="opinionstage-section-header">						
							<div class="opinionstage-section-title">Popup</div>
						</div>					
						<div class="opinionstage-section-content-wrapper">
							<div class="opinionstage-section-content">
								<div class="opinionstage-section-raw">
									<div class="opinionstage-section-cell opinionstage-toggle-cell">
										<div class="opinionstage-onoffswitch <?php echo($first_time ? "disabled" : "")?>">
											<input type="checkbox" name="fly-out-switch" class="opinionstage-onoffswitch-checkbox" <?php echo($first_time ? "disabled" : "")?> id="fly-out-switch" <?php echo(!$first_time && $os_options['fly_out_active'] == 'true' ? "checked" : "") ?>>
											<label class="opinionstage-onoffswitch-label" for="fly-out-switch">
												<div class="opinionstage-onoffswitch-inner"></div>
												<div class="opinionstage-onoffswitch-switch"></div>
											</label>
										</div>
									</div>						
									<div class="opinionstage-section-cell opinionstage-description-cell">
										<div class="title">Popup</div>
										<div class="example">Add a content popup to your site</div>
									</div>													
									<div class="opinionstage-section-cell opinionstage-btns-cell">
										<a href="<?php echo ($first_time ? "javascript:void(0)" : os_popup_flyout_edit_url('content')) ?>" class='opinionstage-blue-bordered-btn opinionstage-edit-content <?php echo($first_time ? "disabled" : "")?>' target="_blank">EDIT CONTENT</a>
										<a href="<?php echo ($first_time ? "javascript:void(0)" : os_popup_flyout_edit_url('settings')) ?>" class='opinionstage-blue-bordered-btn opinionstage-edit-settings <?php echo($first_time ? "disabled" : "")?>' target="_blank">
											<div class="os-icon icon-os-common-settings"></div>													
										</a>
									</div>																				
								</div>
							</div>
						</div>
					</div>
				</div>	

				<div class="opinionstage-dashboard-left">
					<div id="opinionstage-section-create" class="opinionstage-dashboard-section">
						<div class="opinionstage-section-header">
							<div class="opinionstage-section-title">Content</div>
							<?php if(!$first_time) {?>
								<a href="<?php echo 'http://'.OS_POPUP_SERVER_BASE.'/dashboard/content'; ?>" target="_blank" class="opinionstage-section-action opinionstage-blue-bordered-btn">VIEW MY CONTENT</a>
							<?php } ?>
						</div>
						<div class="opinionstage-section-content">
							<div class="opinionstage-section-raw">
								<div class="opinionstage-section-cell opinionstage-icon-cell">
									<div class="os-icon icon-os-reports-polls"></div>
								</div>						
								<div class="opinionstage-section-cell opinionstage-description-cell">
									<div class="title">Poll</div>
									<div class="example">e.g. What's your favorite color?</div>
								</div>													
								<div class="opinionstage-section-cell opinionstage-btn-cell">
									<?php echo os_popup_create_poll_link('opinionstage-blue-btn'); ?>
								</div>																				
							</div>						
							<div class="opinionstage-section-raw">
								<div class="opinionstage-section-cell opinionstage-icon-cell">
									<div class="os-icon icon-os-reports-set"></div>
								</div>						
								<div class="opinionstage-section-cell opinionstage-description-cell">
									<div class="title">Survey</div>
									<div class="example">e.g. Help us improve our site</div>
								</div>													
								<div class="opinionstage-section-cell opinionstage-btn-cell">
									<?php echo os_popup_create_widget_link('survey', 'opinionstage-blue-btn'); ?>
								</div>																				
							</div>																			
							<div class="opinionstage-section-raw">
								<div class="opinionstage-section-cell opinionstage-icon-cell">
									<div class="os-icon icon-os-reports-trivia"></div>													
								</div>						
								<div class="opinionstage-section-cell opinionstage-description-cell">
									<div class="title">Trivia Quiz</div>
									<div class="example">e.g. How well do you know dogs?</div>
								</div>													
								<div class="opinionstage-section-cell opinionstage-btn-cell">
									<?php echo os_popup_create_widget_link('quiz', 'opinionstage-blue-btn'); ?>
								</div>																										
							</div>
							<div class="opinionstage-section-raw">
								<div class="opinionstage-section-cell opinionstage-icon-cell">
									<div class="os-icon icon-os-reports-personality"></div>													
								</div>						
								<div class="opinionstage-section-cell opinionstage-description-cell">
									<div class="title">Personality Quiz</div>
									<div class="example">e.g. What's your most dominant trait?</div>
								</div>													
								<div class="opinionstage-section-cell opinionstage-btn-cell">
									<?php echo os_popup_create_widget_link('personality', 'opinionstage-blue-btn'); ?>
								</div>																										
							</div>
							<div class="opinionstage-section-raw">
								<div class="opinionstage-section-cell opinionstage-icon-cell">
									<div class="os-icon icon-os-reports-list"></div>
								</div>						
								<div class="opinionstage-section-cell opinionstage-description-cell">
									<div class="title">Form</div>
									<div class="example">e.g. Collect email addresses</div>
								</div>													
								<div class="opinionstage-section-cell opinionstage-btn-cell">
									<?php echo os_popup_create_widget_link('contact_form', 'opinionstage-blue-btn'); ?>
								</div>																										
							</div>						
							<div class="opinionstage-section-raw">
								<div class="opinionstage-section-cell opinionstage-icon-cell">
									<div class="os-icon icon-os-reports-list"></div>
								</div>						
								<div class="opinionstage-section-cell opinionstage-description-cell">
									<div class="title">List</div>
									<div class="example">e.g. Top 10 movies of all times</div>
								</div>													
								<div class="opinionstage-section-cell opinionstage-btn-cell">
									<?php echo os_popup_create_widget_link('list', 'opinionstage-blue-btn'); ?>
								</div>																										
							</div>													
						</div>						
					</div>				
				</div>						
				<div class="opinionstage-dashboard-left">
					<div id="opinionstage-section-help" class="opinionstage-dashboard-section">
						<div class="opinionstage-section-header">						
							<div class="opinionstage-section-title">Help</div>
						</div>					
						<div class="opinionstage-section-content-wrapper">
							<div class="opinionstage-section-content">
								<div class="opinionstage-section-raw">
									<div class="opinionstage-section-cell">	
										<a href="http://blog.opinionstage.com/popup-for-interactive-content-wordpress-plugin/?o=758aa9" target="_blank">How to use this plugin</a>
									</div>
								</div>
								<div class="opinionstage-section-raw">
									<div class="opinionstage-section-cell">	
										<?php echo os_popup_create_link('Poll examples', 'showcase'); ?>
									</div>
								</div>
								<div class="opinionstage-section-raw">
									<div class="opinionstage-section-cell">	
										<?php echo os_popup_create_link('Quiz & List examples', 'discover'); ?>
									</div>
								</div>
								<div class="opinionstage-section-raw">
									<div class="opinionstage-section-cell">	
										<?php echo os_popup_logged_in_link('Monetize your traffic', "http://".OS_POPUP_SERVER_BASE."/advanced-solutions"); ?>
									</div>
								</div>						
								<div class="opinionstage-section-raw">
									<div class="opinionstage-section-cell">	
										<a href="https://opinionstage.zendesk.com/anonymous_requests/new" target="_blank">Contact Us</a>
									</div>
								</div>																				
							</div>
						</div>
					</div>				
				</div>	
			</div>
		</div>
	</div>		
	<?php
}

/**
 * Check if the requested plugin is already available
 */
function os_popup_check_plugin_available($plugin_key) {
	$other_widget = (array) get_option($plugin_key); // Check the key of the other plugin

	// Check if OpinionStage plugin already installed.
	return (isset($other_widget['email']) || 
		    isset($other_widget['uid']));
}
/** 
 * Notify about other OpinionStage plugin already available
 */ 
function os_popup_other_plugin_installed_warning() {
	echo "<div id='opinionstage-warning' class='error'><p><B>".__("Opinion Stage Plugin is already installed")."</B>".__(', please remove "<B>Popup for Interactive Content by Opinion Stage</B>" and use the available "<B>Poll & Quiz tools by Opinion Stage</B>" plugin')."</p></div>";
}

/**
 * Generates a link for creating a poll
 */
function os_popup_create_poll_link($css_class) {
	$os_options = (array) get_option(OS_POPUP_OPTIONS_KEY);
	if (empty($os_options["uid"])) {	
		return os_popup_create_link(
			'CREATE',   // Text
			'new_poll', // path
			'',         // Args
			$css_class);
	} else {
		return os_popup_create_link('CREATE', 'new_poll', '', $css_class);
	}	
}
/**
 * Generates a link for editing the flyout placement on Opinion Stage site
 */
function os_popup_flyout_edit_url($tab) {
	$os_options = (array) get_option(OS_POPUP_OPTIONS_KEY);
	if (empty($os_options["uid"])) {	
		return 'http://'.OS_POPUP_SERVER_BASE.'/registrations/new';
	}	
	return 'http://'.OS_POPUP_SERVER_BASE.'/containers/'.$os_options['fly_id'].'/edit?selected_tab='.$tab;
}
/**
 * Utility function to create a link with the correct host and all the required information.
 */
function os_popup_create_link($caption, $page, $params = "", $css_class = '') {
	$params_prefix = empty($params) ? "" : "&";	
	$link = "http://".OS_POPUP_SERVER_BASE."/".$page."?" . "o=".OS_POPUP_WIDGET_API_KEY.$params_prefix.$params;
	return "<a href=\"".$link."\" target='_blank' class=\"".$css_class."\">".$caption."</a>";
}

/**
 * Generates a link to Opinion Stage that requires registration
 */
function os_popup_logged_in_link($text, $link) {
	return os_popup_create_link($text, 'registrations/new', 'return_to='.$link);
}
/**
 * Generates a link for creating a trivia quiz
 */
function os_popup_create_widget_link($w_type, $css_class) {
	$os_options = (array) get_option(OS_POPUP_OPTIONS_KEY);
	if (empty($os_options["uid"])) {	
		return os_popup_create_link(
			'CREATE',          // Text 
			'widgets/new',     // Path
			'w_type='.$w_type, // Args
			$css_class);
	} else {
		return os_popup_create_link('CREATE', 'widgets/new', 'w_type='.$w_type, $css_class);
	}	
}

/**
 * Main function for creating the poll html representation.
 * Transforms the shortcode parameters to the desired iframe call.
 *
 * Syntax as follows:
 * shortcode name - OS_POPUP_POLL_SHORTCODE
 *
 * Arguments:
 * @param  id - Id of the poll
 *
 */
function os_popup_add_poll_or_set($atts) {
	extract(shortcode_atts(array('id' => 0, 'type' => 'poll', 'width' => ''), $atts));
	if(!is_feed()) {
		$id = intval($id);
		return os_popup_create_poll_embed_code($id, $type, $width);
	} else {
		return __('Note: There is a poll embedded within this post, please visit the site to participate in this post\'s poll.', OS_POPUP_WIDGET_UNIQUE_ID);
	}
}

/**
 * Main function for creating the widget html representation.
 * Transforms the shortcode parameters to the desired iframe call.
 *
 * Syntax as follows:
 * shortcode name - OS_POPUP_WIDGET_SHORTCODE
 *
 * Arguments:
 * @param  path - Path of the widget
 *
 */
function os_popup_add_widget($atts) {
	extract(shortcode_atts(array('path' => 0, 'comments' => 'true', 'sharing' => 'true', 'recommendations' => 'false', 'width' => ''), $atts));

	if(!is_feed()) {		
		return os_popup_create_widget_embed_code($path, $comments, $sharing, $recommendations, $width);
	} else {
		return __('Note: There is a widget embedded within this post, please visit the site to participate in this post\'s widget.', OS_POPUP_WIDGET_UNIQUE_ID);
	}
}

/**
 * Main function for creating the placement html representation.
 * Transforms the shortcode parameters to the desired code.
 *
 * Syntax as follows:
 * shortcode name - OS_POPUP_PLACEMENT_SHORTCODE
 *
 * Arguments:
 * @param  id - Id of the placement
 *
 */
function os_popup_add_placement($atts) {
	extract(shortcode_atts(array('id' => 0), $atts));
	if(!is_feed()) {
		$id = intval($id);
		return os_popup_create_placement_embed_code($id);
	} 
}
/**
 * Create the The iframe HTML Tag according to the given parameters.
 * Either get the embed code or embeds it directly in case 
 *
 * Arguments:
 * @param  id - Id of the poll
 */
function os_popup_create_poll_embed_code($id, $type, $width) {
    
    // Only present if id is available 
    if (isset($id) && !empty($id)) {        		
		// Load embed code from the cache if possible
		$is_homepage = is_home();
		$transient_name = 'embed_code' . $id . '_' . $type . '_' . ($is_homepage ? "1" : "0") .'_' . $width;
		$code = get_transient($transient_name);
		if ( false === $code || '' === $code ) {
			if ($type == 'set') {
				$embed_code_url = "http://".OS_POPUP_API_PATH."/sets/" . $id . "/code.json";			
			} else {
				$embed_code_url = "http://".OS_POPUP_API_PATH."/polls/" . $id . "/code.json?width=".$width;
			}
			
			if ($is_homepage) {
				$embed_code_url .= "?h=1";
			}
		
			extract(os_popup_get_contents($embed_code_url));
			$data = json_decode($raw_data);
			if ($success) {
				$code = $data->{'code'};			
				// Set the embed code to be cached for an hour
				set_transient($transient_name, $code, 3600);
			}
		}
    }
	return $code;
}
/**
 * Create the The iframe HTML Tag according to the given parameters.
 * Either get the embed code or embeds it directly in case 
 *
 * Arguments:
 * @param  path - Path of the widget
 */
function os_popup_create_widget_embed_code($path, $comments, $sharing, $recommendations, $width) {
    
    // Only present if path is available 
    if (isset($path) && !empty($path)) {        		
		// Load embed code from the cache if possible
		$transient_name = 'embed_code' . $path . '.' . $comments . '.' . $sharing . $recommendations . $width;
		$code = get_transient($transient_name);
		if ( false === $code || '' === $code ) {
			$embed_code_url = "http://".OS_POPUP_API_PATH."/widgets" . $path . "/code.json?comments=".$comments."&sharing=".$sharing."&recommendations=".$recommendations."&width=".$width;
			
			extract(os_popup_get_contents($embed_code_url));
			$data = json_decode($raw_data);
			if ($success) {
				$code = $data->{'code'};			
				// Set the embed code to be cached for an hour
				set_transient($transient_name, $code, 3600);
			}
		}
    }
	return $code;
}
/**
 * Returns the embed code of a placement by fetching it from Opinion Stage api
 */
function os_popup_create_placement_embed_code($id) {
    
    // Only present if id is available 
    if (isset($id) && !empty($id)) {        		
		// Load embed code from the cache if possible
		$is_homepage = is_home();
		$transient_name = 'embed_code' . $id . '_' . 'placement';
		$code = get_transient($transient_name);
		if ( false === $code || '' === $code ) {
			$embed_code_url = "http://".OS_POPUP_API_PATH."/placements/" . $id . "/code.json";
			extract(os_popup_get_contents($embed_code_url));
			$data = json_decode($raw_data);
			if ($success) {
				$code = $data->{'code'};			
				// Set the embed code to be cached for an hour
				set_transient($transient_name, $code, 3600);
			}
		}
    }
	return $code;
}

/**
 * Perform an HTTP GET Call to retrieve the data for the required content.
 * 
 * Arguments:
 * @param $url
 * @return array - raw_data and a success flag
 */
function os_popup_get_contents($url) {
    $response = wp_remote_get($url, array('header' => array('Accept' => 'application/json; charset=utf-8')));

    return os_popup_parse_response($response);
}

/**
 * Parse the HTTP response and return the data and if was successful or not.
 */
function os_popup_parse_response($response) {
    $success = false;
    $raw_data = "Unknown error";
    
    if (is_wp_error($response)) {
        $raw_data = $response->get_error_message();
    
    } elseif (!empty($response['response'])) {
        if ($response['response']['code'] != 200) {
            $raw_data = $response['response']['message'];
        } else {
            $success = true;
            $raw_data = $response['body'];
        }
    }
    
    return compact('raw_data', 'success');
}
?>