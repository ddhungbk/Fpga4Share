<?php

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 */

if ( ! defined('MESMERIZE_THEME_REQUIRED_PHP_VERSION')) {
    define('MESMERIZE_THEME_REQUIRED_PHP_VERSION', '5.3.0');
}

add_action('after_switch_theme', 'mesmerize_check_php_version');

function mesmerize_check_php_version()
{
    // Compare versions.
    if (version_compare(phpversion(), MESMERIZE_THEME_REQUIRED_PHP_VERSION, '<')) :
        // Theme not activated info message.
        add_action('admin_notices', 'mesmerize_php_version_notice');
        
        
        // Switch back to previous theme.
        switch_theme(get_option('theme_switched'));
        
        return false;
    endif;
}

function mesmerize_php_version_notice()
{
    ?>
    <div class="notice notice-alt notice-error notice-large">
        <h4><?php _e('Mesmerize theme activation failed!', 'mesmerize'); ?></h4>
        <p>
            <?php _e('You need to update your PHP version to use the <strong>Mesmerize</strong>.', 'mesmerize'); ?> <br/>
            <?php _e('Current php version is:', 'mesmerize') ?> <strong>
                <?php echo phpversion(); ?></strong>, <?php _e('and the minimum required version is ', 'mesmerize') ?>
            <strong><?php echo MESMERIZE_THEME_REQUIRED_PHP_VERSION; ?></strong>
        </p>
    </div>
    <?php
}

if (version_compare(phpversion(), MESMERIZE_THEME_REQUIRED_PHP_VERSION, '>=')) {
    require_once get_template_directory() . "/inc/functions.php";
    
     
    
    if ( ! mesmerize_can_show_cached_value("mesmerize_cached_kirki_style_mesmerize")) {
        
        if ( ! mesmerize_skip_customize_register()) {
            do_action("mesmerize_customize_register_options");
        }
    }
    
} else {
    add_action('admin_notices', 'mesmerize_php_version_notice');
}

function register_secondary_menu() {
    register_nav_menu('seconddary',__( 'Secondary Menu' ));
}
add_action( 'init', 'register_secondary_menu' );

function replace_secondary_menu_class($menu) {    
    $menu = preg_replace('/ class="menu"/','/ class="menu dropdown-menu" /',$menu);        
    return $menu;      
}
add_filter('wp_nav_menu','replace_secondary_menu_class'); 

function add_scroll_to_top_script() {
    wp_enqueue_script( 'custom-script', get_stylesheet_directory_uri() . '/customizer/js/scroll-to-top.js', array( 'jquery' ) );
}
add_action( 'wp_enqueue_scripts', 'add_scroll_to_top_script' );
