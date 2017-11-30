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


		$labels = array(
        'name'                => _x( 'Issues', 'Post Type General Name' ),
        'singular_name'       => _x( 'Issue', 'Post Type Singular Name' ),
        'menu_name'           => __( 'WP Issue Logger' ),
        'parent_item_colon'   => __( 'Main Issue'),
        'all_items'           => __( 'All Issues' ),
        'view_item'           => __( 'View Issue'),
        'add_new_item'        => __( 'Add New Issue' ),
        'add_new'             => __( 'Add New' ),
        'edit_item'           => __( 'Edit Issue' ),
        'update_item'         => __( 'Update Issue' ),
        'search_items'        => __( 'Search Issue' ),
        'not_found'           => __( 'Not Found' ),
        'not_found_in_trash'  => __( 'Not found in Trash' ),
    );

    $args = array( 

    	'public' => true, 
    	'label' => 'WP Issue Logger',
    	'labels' => $labels,
        'menu_icon' => 'dashicons-image-filter'
      

    );

    register_post_type( 'issue', $args);

}

add_action( 'init', 'wpil_register_post_type');
