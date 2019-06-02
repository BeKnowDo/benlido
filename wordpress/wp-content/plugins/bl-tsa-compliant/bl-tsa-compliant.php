<?php
/*
Plugin Name: Ben Lido TSA Compliance 
Plugin URI: http://www.benlido.com/
Description: Whether the product is TSA compliant or not
Version: 1.0
*/
if ( ! function_exists( 'bl_tsa_compliant_taxonomy' ) ) {

    // Register Custom Taxonomy
    function bl_tsa_compliant_taxonomy() {
    
        $labels = array(
            'name'                       => _x( 'TSA Compliant', 'Taxonomy General Name', 'benlido' ),
            'singular_name'              => _x( 'TSA Compliant', 'Taxonomy Singular Name', 'benlido' ),
            'menu_name'                  => __( 'TSA Compliant', 'benlido' ),
            'all_items'                  => __( 'All Items', 'benlido' ),
            'parent_item'                => __( 'Parent Item', 'benlido' ),
            'parent_item_colon'          => __( 'Parent Item:', 'benlido' ),
            'new_item_name'              => __( 'New Item Name', 'benlido' ),
            'add_new_item'               => __( 'Add New Item', 'benlido' ),
            'edit_item'                  => __( 'Edit Item', 'benlido' ),
            'update_item'                => __( 'Update Item', 'benlido' ),
            'view_item'                  => __( 'View Item', 'benlido' ),
            'separate_items_with_commas' => __( 'Separate items with commas', 'benlido' ),
            'add_or_remove_items'        => __( 'Add or remove items', 'benlido' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'benlido' ),
            'popular_items'              => __( 'Popular Items', 'benlido' ),
            'search_items'               => __( 'Search Items', 'benlido' ),
            'not_found'                  => __( 'Not Found', 'benlido' ),
            'no_terms'                   => __( 'No items', 'benlido' ),
            'items_list'                 => __( 'Items list', 'benlido' ),
            'items_list_navigation'      => __( 'Items list navigation', 'benlido' ),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
        );
        register_taxonomy( 'tsa_compliant', array( 'product' ), $args );
    
    }
    add_action( 'init', 'bl_tsa_compliant_taxonomy', 0 );
    
    }