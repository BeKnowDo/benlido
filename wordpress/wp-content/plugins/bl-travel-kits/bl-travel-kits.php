<?php
/*
Plugin Name: Ben Lido Travel Kits
Plugin URI: http://www.benlido.com/
Description: The Travel Kit kitting admin
Version: 1.0
*/
if ( ! function_exists('benlido_kits') ) {

// Register Custom Post Type
function benlido_kits() {

	$labels = array(
		'name'                  => _x( 'Travel Kits', 'Post Type General Name', 'benlido' ),
		'singular_name'         => _x( 'Travel Kit', 'Post Type Singular Name', 'benlido' ),
		'menu_name'             => __( 'Travel Kits', 'benlido' ),
		'name_admin_bar'        => __( 'Travel Kit', 'benlido' ),
		'archives'              => __( 'Travel Kit Archives', 'benlido' ),
		'attributes'            => __( 'Item Attributes', 'benlido' ),
		'parent_item_colon'     => __( 'Parent Item:', 'benlido' ),
		'all_items'             => __( 'All Items', 'benlido' ),
		'add_new_item'          => __( 'Add New Item', 'benlido' ),
		'add_new'               => __( 'Add New', 'benlido' ),
		'new_item'              => __( 'New Item', 'benlido' ),
		'edit_item'             => __( 'Edit Item', 'benlido' ),
		'update_item'           => __( 'Update Item', 'benlido' ),
		'view_item'             => __( 'View Item', 'benlido' ),
		'view_items'            => __( 'View Items', 'benlido' ),
		'search_items'          => __( 'Search Item', 'benlido' ),
		'not_found'             => __( 'Not found', 'benlido' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'benlido' ),
		'featured_image'        => __( 'Featured Image', 'benlido' ),
		'set_featured_image'    => __( 'Set featured image', 'benlido' ),
		'remove_featured_image' => __( 'Remove featured image', 'benlido' ),
		'use_featured_image'    => __( 'Use as featured image', 'benlido' ),
		'insert_into_item'      => __( 'Insert into item', 'benlido' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'benlido' ),
		'items_list'            => __( 'Items list', 'benlido' ),
		'items_list_navigation' => __( 'Items list navigation', 'benlido' ),
		'filter_items_list'     => __( 'Filter items list', 'benlido' ),
	);
	$args = array(
		'label'                 => __( 'Travel Kit', 'benlido' ),
		'description'           => __( 'Travel Kit Description', 'benlido' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes', 'post-formats' ),
		'taxonomies'            => array( 'product_cat', 'product_tag' ),
		'hierarchical'          => true,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-products',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
		'show_in_rest'          => true,
	);
	register_post_type( 'travel_kit', $args );

}
add_action( 'init', 'benlido_kits', 0 );

}
