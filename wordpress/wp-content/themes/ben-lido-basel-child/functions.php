<?php

require_once 'inc/template-functions.php';
require_once 'inc/basel-overrides.php';
require_once 'inc/woocommerce-overrides.php';
add_action( 'wp_enqueue_scripts', 'basel_child_enqueue_styles', 1000 );

function basel_child_enqueue_styles() {
	$version = '1.0';

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
	
	wp_register_style('ben-lido-style', get_stylesheet_directory_uri() . '/assets/css/style.css', array(), $version, 'all');
	wp_enqueue_style('ben-lido-style'); // Enqueue it!
	wp_enqueue_script('ben-lido-script');

}

function bl_storefront_overrides() {
	if (function_exists('bl_add_to_cart_hook')) {
		add_action('woocommerce_add_to_cart', 'bl_add_to_cart_hook',99,6);
	}

	if (function_exists('bl_add_add_kit_button')) {
		add_action('woocommerce_widget_shopping_cart_buttons','bl_add_add_kit_button',99);
	}
	
}

add_action( 'wp', 'bl_storefront_overrides' );