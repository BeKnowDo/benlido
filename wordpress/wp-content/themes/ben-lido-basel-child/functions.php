<?php

require_once 'inc/template-functions.php';
require_once 'inc/basel-overrides.php';
require_once 'inc/woocommerce-overrides.php';
add_action( 'wp_enqueue_scripts', 'basel_child_enqueue_styles', 1000 );

function basel_child_enqueue_styles() {
	$version = '1.3';

	wp_register_script('ben-lido-script', get_stylesheet_directory_uri() . '/assets/js/benlido.min.js', array('jquery'),$version);
	wp_enqueue_script( 'jquery-ui-core', false, array('jquery') );
	wp_enqueue_script( 'jquery-ui-accordion', false, array('jquery') );
	$wp_scripts = wp_scripts();
    wp_enqueue_style(
      'jquery-ui-theme-smoothness',
      sprintf(
        '//ajax.googleapis.com/ajax/libs/jqueryui/%s/themes/smoothness/jquery-ui.css', // working for https as well now
        $wp_scripts->registered['jquery-ui-core']->ver
      )
    );
	
	if( basel_get_opt( 'minified_css' ) ) {
		wp_enqueue_style( 'basel-style', get_template_directory_uri() . '/style.min.css', array('bootstrap'), $version );
	} else {
		wp_enqueue_style( 'basel-style', get_template_directory_uri() . '/style.css', array('bootstrap'), $version );
	}
	
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('bootstrap'), $version );

	wp_dequeue_script('wc-add-to-cart');
	wp_deregister_script('wc-add-to-cart');
	wp_register_script('wc-add-to-cart', get_stylesheet_directory_uri() . '/assets/js/add-to-cart.min.js' , array( 'jquery',  'jquery-blockui' ), $version, TRUE);
	wp_enqueue_script('wc-add-to-cart');
	
	wp_register_style('ben-lido-style', get_stylesheet_directory_uri() . '/assets/css/style.css', array(), $version, 'all');
	wp_enqueue_style('ben-lido-style'); // Enqueue it!
	wp_enqueue_script('ben-lido-script');

}


function bl_storefront_overrides() {
	// this replaces the AJAX add to cart functions with our own
	if (function_exists('bl_replace_ajax_add_to_cart')) {
		// first, remove existing action
		remove_action( 'wp_ajax_woocommerce_add_to_cart', array( 'WC_AJAX', 'add_to_cart' ) );
		remove_action( 'wp_ajax_nopriv_woocommerce_add_to_cart', array( 'WC_AJAX', 'add_to_cart' ) );

		// WC AJAX can be used for frontend ajax requests.
		remove_action( 'wc_ajax_add_to_cart', array( 'WC_AJAX', 'add_to_cart' ) );
		bl_replace_ajax_add_to_cart();
	}

	// this adds the hook to add meta info to the cart items
	if (function_exists('bl_add_to_cart_hook')) {
		add_action('woocommerce_add_to_cart', 'bl_add_to_cart_hook',99,6);
	}

	if (function_exists('bl_add_add_kit_button')) {
		add_action('woocommerce_widget_shopping_cart_buttons','bl_add_add_kit_button',99);
	}

	// replace wc_ajax for removing cart item with our own
	remove_action('wc_ajax_remove_from_cart',array('WC_AJAX','remove_from_cart'));
	add_action('wc_ajax_remove_from_cart','bl_ajax_remove_from_cart');

	// customize subtotals to come from the kits and not the shopping cart 
	add_filter( 'woocommerce_cart_subtotal', 'bl_woocommerce_cart_subtotal', 10, 3 ); 

	if (function_exists('bl_display_kit_name_cart')) {
		add_filter( 'woocommerce_get_item_data', 'bl_display_kit_name_cart', 10, 2 );
	}
	
	
}

add_action( 'wp', 'bl_storefront_overrides' );