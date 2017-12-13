<?php
/*
Plugin Name:  WP Issue Logger
Description:  Basic WordPress Plugin to log issues
Version:      0.1
Author:       Travis Lima, Johannes Floor
Author URI:   travislima.com
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  wp-issue-logger
Domain Path:  /languages
*/

//EXit if accessed directly
if ( ! defined('ABSPATH')) {
    exit;
}

function wpil_register_post_type() {
		$labels = array(
        'name'                => __( 'Issues', 'wp-issue-logger' ),
        'singular_name'       => __( 'Issue', 'wp-issue-logger' ),
        'menu_name'           => __( 'WP Issue Logger', 'wp-issue-logger' ),
        'parent_item_colon'   => __( 'Main Issue', 'wp-issue-logger' ),
        'all_items'           => __( 'All Issues', 'wp-issue-logger' ),
        'view_item'           => __( 'View Issue', 'wp-issue-logger' ),
        'add_new_item'        => __( 'Add New Issue', 'wp-issue-logger' ),
        'add_new'             => __( 'Add New', 'wp-issue-logger' ),
        'edit_item'           => __( 'Edit Issue', 'wp-issue-logger' ),
        'update_item'         => __( 'Update Issue', 'wp-issue-logger' ),
        'search_items'        => __( 'Search Issue', 'wp-issue-logger' ),
        'not_found'           => __( 'Not Found', 'wp-issue-logger' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'wp-issue-logger' ),
    );

    $args = array( 
    	'public' => true, 
    	'label' => __( 'WP Issue Logger', 'wp-issue-logger' ),
    	'labels' => $labels,
        'show_in_admin_bar' => true,
        'menu_icon' => 'dashicons-image-filter',
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'has_archive' => true,
        'register_meta_box_cb' => 'wpil_add_meta_box',
        'rewrite'     => array (
            'slug' => 'issues',
            'with_front' => true,
            'pages' => true,
            'feeds' => true
        
        ),
      
        'supports' => array(
            'title',
            'editor',
            'comments',
            'revisions',
            'author', 
            'page-attributes',
        )
    );
    register_post_type( 'issue', $args);
}

add_action( 'init', 'wpil_register_post_type');


// Flush Permalink on Plugin activation.
function wpil_rewrite_flush() {
    wpil_register_post_type();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'wpil_rewrite_flush' );
register_deactivation_hook( __FILE__, 'wpil_rewrite_flush' );


/**
 * Add issue status taxonomy
*/
add_action( 'init', 'create_issueCategory_tax' );
function create_issueCategory_tax() {
    $taxlabels = array(
		'name'              => __( 'Issue Categories', 'wp-issue-logger' ),
		'singular_name'     => __( 'Issue Category', 'wp-issue-logger'  ),
		'search_items'      => __( 'Search Categories', 'wp-issue-logger'  ),
		'all_items'         => __( 'All Categories', 'wp-issue-logger' ),
		'parent_item'       => __( 'Parent Category', 'wp-issue-logger' ),
		'parent_item_colon' => __( 'Parent Category:', 'wp-issue-logger' ),
		'edit_item'         => __( 'Edit Category', 'wp-issue-logger' ),
		'update_item'       => __( 'Update Category', 'wp-issue-logger' ),
		'add_new_item'      => __( 'Add New Category', 'wp-issue-logger' ),
		'new_item_name'     => __( 'New Category Name', 'wp-issue-logger' ),
        'menu_name'         => __( 'Categories', 'wp-issue-logger' ),
    );

	register_taxonomy(
		'type',
		'issue',
		array(
            'label' => 'Issue Category',
            'labels' => $taxlabels,
			'rewrite' => array( 'slug' => 'issue-category' ),
            'hierarchical' => true,
            'description' => 'Categorise your issues with different tags suchs as "New Features", "Bug", "Errors"'
		)
	);
}

/*============Adds Metabox to the right side of the CPT Editor=========*/


function wpil_add_meta_box() {
    add_meta_box(
        'wpil_issue_status',
        __( 'Issue Status', 'wp-issue-logger' ),
        'wpil_issue_status_show_metabox',
        'issue',
        'side',
        'default'
    );
}

/**
 * Output the HTML for the metabox.
 */
function wpil_issue_status_show_metabox() {
    global $post;
    // **** CREATE NONCE AND CHECK IT / NOT NEEDED ATM THOUGH ****
    // Get the location data if it's already been entered
    $issue_status = get_post_meta( $post->ID, 'wpil_status', true );
    ?>

<!-- Selection options for Metabox -->
    <select name="wpil-issue-status" id="wpil-issue-status">
        <option value="new" <?php selected( 'new', $issue_status, true); ?> >New</option>
        <option value="in-progress" <?php selected( 'in-progress', $issue_status, true); ?> >In Progress</option>
        <option value="completed" <?php selected( 'completed', $issue_status, true); ?> >Completed</option>
    </select>
    <?php
}

/*============Saves METABOX Data=========*/

add_action( 'add_meta_boxes', 'wpil_add_meta_box' );
/* Save post meta on the 'save_post' hook. */
add_action( 'save_post', 'wpil_save_post_class_meta', 10, 2 );
/* Save the meta box's post metadata. */
function wpil_save_post_class_meta( $post_id, $post ) {
    // check to see if post_type is 'issue', if not return $post_id;
    $status = $_REQUEST['wpil-issue-status'];
    if( !empty( $status ) ){
        update_post_meta( $post_id, 'wpil_status', $status );
    }else{
        delete_post_meta( $post_id, 'wpil_status' );
    }
}
