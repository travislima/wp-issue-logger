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

require_once ( plugin_dir_path(__FILE__) . 'wp-issue-logger-shortcode.php');


function wpil_register_post_type() {


		$labels = array(
        'name'                => __( 'Issues' ),
        'singular_name'       => __( 'Issue' ),
        'menu_name'           => ( 'WP Issue Logger' ),
        'parent_item_colon'   => ( 'Main Issue'),
        'all_items'           => ( 'All Issues' ),
        'view_item'           => ( 'View Issue'),
        'add_new_item'        => ( 'Add New Issue' ),
        'add_new'             => ( 'Add New' ),
        'edit_item'           => ( 'Edit Issue' ),
        'update_item'         => ( 'Update Issue' ),
        'search_items'        => ( 'Search Issue' ),
        'not_found'           => ( 'Not Found' ),
        'not_found_in_trash'  => ( 'Not found in Trash' ),
    );

    $args = array( 

    	'public' => true, 
    	'label' => 'WP Issue Logger',
    	'labels' => $labels,
        'show_in_admin_bar' => true,
        'menu_icon' => 'dashicons-image-filter',
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'has_archive' => true,
        'register_meta_box_cb' => 'wpt_add_event_metaboxes',
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
          //  'thumbnail',
          //  'custom-fields',
           // 'post-formats'
        )
    );

    register_post_type( 'issue', $args);

}

add_action( 'init', 'wpil_register_post_type');

// Flush Permalink on Plugin activation.

function wpil_rewrite_flush() {
    // First, we "add" the custom post type via the above written function.
    // Note: "add" is written with quotes, as CPTs don't get added to the DB,
    // They are only referenced in the post_type column with a post entry, 
    // when you add a post of this CPT.
    wpil_register_post_type();

    // ATTENTION: This is *only* done during plugin activation hook in this example!
    // You should *NEVER EVER* do this on every page load!!
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'wpil_rewrite_flush' );
register_deactivation_hook( __FILE__, 'wpil_rewrite_flush' );



//override archive-issue page with custom page from plugin folder

/** Enqueue CSS */

 // ******** ADD THIS IN ONLY IF YOU ARE GOING TO STYLE FRONT-END CPT PAGES/ELEMENTS ********* //
// add_action( 'wp_enqueue_scripts', 'prefix_add_my_stylesheet' );

// function prefix_add_my_stylesheet() {
//    wp_register_style( 'wpil-cpt-style', plugins_url( 'css/style.css', __FILE__) );
//    wp_enqueue_style( 'cpt-style' );
// }

// force use of templates from plugin folder
function cpte_force_template( $template )
{	
    if( is_archive( 'issue' ) ) {
        $template = WP_PLUGIN_DIR .'/'. plugin_basename( dirname(__FILE__) ) .'/archive-issue.php';
	}

	/* if( is_singular( 'issue' ) ) {
        $template = WP_PLUGIN_DIR .'/'. plugin_basename( dirname(__FILE__) ) .'/single-issue.php';
	}
 */
    return $template;
}
// add_filter( 'template_include', 'cpte_force_template' );

/**
 * Add issue status taxonomy
*/
add_action( 'init', 'create_issueCategory_tax' );

function create_issueCategory_tax() {



    $taxlabels = array(
		'name'              => __( 'Issue Categories', 'wp-issue-logger' ),
		'singular_name'     => ( 'Issue Category'  ),
		'search_items'      => ( 'Search Categories'  ),
		'all_items'         => ( 'All Categories'),
		'parent_item'       => ( 'Parent Category'),
		'parent_item_colon' => ( 'Parent Category:'),
		'edit_item'         => ( 'Edit Category'),
		'update_item'       => ( 'Update Category'),
		'add_new_item'      => ( 'Add New Category'),
		'new_item_name'     => ( 'New Category Name'),
        'menu_name'         => ( 'Categories'),
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
/*===================================================================================*/

/**
 * Adds a metabox to the right side of the screen under the Publish box
 */
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
    // Nonce field to validate form request came from current site
    // wp_nonce_field( basename( __FILE__ ), 'event_fields' );
    // Get the location data if it's already been entered
    $issue_status = get_post_meta( $post->ID, 'wpil_status', true );
    ?>

    <select name="wpil-issue-status" id="wpil-issue-status">
        <option value="new" <?php selected( 'new', $issue_status, true); ?> >New</option>
        <option value="in-progress" <?php selected( 'in-progress', $issue_status, true); ?> >In Progress</option>
        <option value="completed" <?php selected( 'completed', $issue_status, true); ?> >Completed</option>
    </select>
    <?php
}
add_action( 'add_meta_boxes', 'wpil_add_meta_box' );

/* Save post meta on the 'save_post' hook. */
add_action( 'save_post', 'smashing_save_post_class_meta', 10, 2 );

/* Save the meta box's post metadata. */
function smashing_save_post_class_meta( $post_id, $post ) {

    // check to see if post_type is 'issue', if not return $post_id;

    $status = $_REQUEST['wpil-issue-status'];

    if( !empty( $status ) ){
        update_post_meta( $post_id, 'wpil_status', $status );
    }else{
        delete_post_meta( $post_id, 'wpil_status' );
    }

}

