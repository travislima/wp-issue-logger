<?php
/*
Plugin Name:  WP Issue Logger
Description:  Basic WordPress Plugin to log issues
Version:      0.1
Author:       Travis Lima, Johannes Floor
Author URI:   travislima.com
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  wpil
Domain Path:  /languages
*/

/*
//EXit if accessed directly
if ( ! defined('ABSPATH')) {
    exit;
}
*/

function wpil_register_post_type() {

    $args = array( 'public' => true, 'label' => 'WP Issue Logger' );

    register_post_type( 'issue', $args);

}

add_action( 'init', 'wpil_register_post_type');
