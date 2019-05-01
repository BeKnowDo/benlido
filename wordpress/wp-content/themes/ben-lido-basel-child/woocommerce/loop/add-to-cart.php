<?php
/**
 * Loop Add to Cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/add-to-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

// literally, just change the version number to match the latest.. there is no way to really update this file.
global $product;
global $kit_id;
$is_swap = false;
$mid_swap = false;
$is_kit_add = false;
$kit_id = null;

// NOTE: setting $mid_swap as true means that we are always adding items into the kit and not the cart
//$mid_swap = true;

if (function_exists('bl_is_swap')) {
    $is_swap = bl_is_swap();
}
if (function_exists('bl_is_kit_add')) {
    $is_kit_add = bl_is_kit_add();
}


if ($is_kit_add == true || $is_swap == true) {
    // getting the kit ID so that we can add the item to the kit and then redirect back to the kit
    if (function_exists('bl_get_current_kit_id')) {
        $kit_id = bl_get_current_kit_id();
    }
}

$product_id = $product->get_id();

$product_name = $product->get_name();
$product_sku = $product->get_sku();
$product_category = '';
if (function_exists('bl_get_this_category')) {
    $product_category_obj = bl_get_this_category();
}
if (is_string($product_category_obj)) {
    $product_category = $product_category_obj;
}
if (is_object($product_category_obj) && isset($product_category_obj->name)) {
    $category_id = $product_category_obj->term_id;
    $product_category = $product_category_obj->name;
}
$args['class'] = 'btn btn-lg btn-block btn-primary add-to-cart add_to_cart_button';
$default_text = 'Add to kit';
$cart_text = ' in kit';

if ($is_swap == true) {
    // if we're in the category or shop landing pages, then we are in mid swap
    if (is_shop() || is_product_category()) {
        $mid_swap = true;
    } else {
        //$args['class'] = 'btn btn-lg btn-block btn-primary';
    }

}

if ($mid_swap == true) {
    $default_text = 'Select';
}

if (get_class($product) == 'WC_Product_Variable') {
    // NOTE: everything changes here.
    $default_text = 'View Product Detail';
    $args['class'][] = 'has-variations';
    $variation_id = 0;
}


if ($is_swap == true && $mid_swap == false) {
} else {
    echo apply_filters( 'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
    sprintf( '<a href="%s" data-quantity="%s" class="%s" 
        data-name="%s" data-sku="%s" data-category="%s" data-kit_id="%s" 
        data-prod_id="%s" data-cat_id="%s" data-var_id="%s" data-swap="%s"
         %s>%s</a>',
		esc_url( $product->add_to_cart_url() ),
		esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
        esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
        esc_html($product_name), // for data-name=
        esc_html($product_sku), // for data-sku=
        esc_html($product_category), // for data-category=
        esc_html($kit_id), // for the data-kit_id=
        esc_html($product_id), // for the data-prod_id=
        esc_html($category_id), // for the data-cat_id=
        esc_html($variation_id), // for data-var_id=
        esc_html($mid_swap), // for the data-swap=
		isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
		esc_html( $product->add_to_cart_text() )
	),
$product, $args );
}


