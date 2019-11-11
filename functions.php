<?php
/**
 * Plugin Name: Popular Posts
 * Description: Popular Posts get by shortcodes
 * Author:      anonymous
 * Version:     1.0
 */

require_once __DIR__ . '/page-settings.php';
require_once __DIR__ . '/posts-popularity-options.php';
require_once __DIR__ . '/shortcode.php';

/**
 * Stylesheets and Scripts
 */
add_action( 'admin_enqueue_scripts', 'pp_admin_styles_and_scripts' );
function pp_admin_styles_and_scripts(){
	wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'pp-scripts&styles', plugin_dir_url( __FILE__ ) . 'scripts.js', array( 'jquery' ) );
}