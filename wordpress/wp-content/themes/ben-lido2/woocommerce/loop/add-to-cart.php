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
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// literally, just change the version number to match the latest.. there is no way to really update this file.
global $product;
global $kit_id;
$is_swap = false;
$mid_swap = false;
$is_kit_add = false;
$kit_id = null;

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
$args['class'] = 'btn btn-lg btn-block btn-primary add-to-cart';
$default_text = 'Add to kit';
$cart_text = ' in kit';

if ($is_swap == true) {
    // if we're in the category or shop landing pages, then we are in mid swap
    if (is_shop() || is_product_category()) {
        $mid_swap = true;
    } else {
        $args['class'] = 'btn btn-lg btn-block btn-primary';
    }
    
}

if ($mid_swap == true) {
    $default_text = 'Swap';
}

if (get_class($product) == 'WC_Product_Variable') {
    // NOTE: everything changes here.
    $default_text = 'View Product Detail';
    $args['class'] = 'btn btn-lg btn-block btn-primary add-to-cart has-variations';
}

/*
<button class="btn btn-lg btn-block btn-primary add-to-cart
    in-cart
    " data-name="Intelligent Soft Hat" data-sku="2f5ebca0-da29-413a-a87b-c58025047b84" data-category="3254ff94-10bd-4a17-9da5-1f0f7cbb6090">
            <i class="far fa-minus-circle  " data-name="Intelligent Soft Hat" data-sku="2f5ebca0-da29-413a-a87b-c58025047b84" data-category="3254ff94-10bd-4a17-9da5-1f0f7cbb6090"></i>
            <span class="add-to-cart-text" data-default-text="Add to kit" data-cart-text=" in kit">
                1 in kit
            </span>
            <i class="fal fa-plus-circle" data-name="Intelligent Soft Hat" data-sku="2f5ebca0-da29-413a-a87b-c58025047b84" data-category="3254ff94-10bd-4a17-9da5-1f0f7cbb6090"></i>
        </button>
*/
// swap
if ($is_swap == true && $mid_swap == false) {
    
    echo apply_filters( 'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
    sprintf( '<span class="btn btn-lg btn-block btn-primary btn-selected" data-product_id="%s" data-name="%s" data-sku="%s" data-category="%s" data-category_id="%s">
    <span class="btn-selected-remove remove-from-cart" data-product_id="%s" data-name="%s" data-sku="%s" data-category="%s" data-category_id="%s">Remove</span>
    <a href="%s" title="%s" class="btn-selected-swap swap-from-cart" data-name="%s" data-sku="%s" data-category="%s" data-kit_id="%s" data-prod_id="%s" data-cat_id="%s">Swap</a>
</span>',
        esc_html($product_id), // for data-product_id=
        esc_html($product_name), // for data-name=
        esc_html($product_sku), // for data-sku=
        esc_html($product_category), // for data-category=
        esc_html($category_id), // for data-category_id=
        esc_html($product_id), // for data-product_id=
        esc_html($product_name), // for data-name=
        esc_html($product_sku), // for data-sku=
        esc_html($product_category), // for data-category=
        esc_html($category_id), // for data-category_id=
        esc_url( $url ), // for href=
        esc_html($title),
        esc_html($product_name), // for data-name=
        esc_html($product_sku), // for data-sku=
        esc_html($product_category), // for data-category=
        esc_html($kit_id), // for the data-kit_id=
        esc_html($product_id), // for the data-prod_id=
        esc_html($category_id) // for the data-cat_id=
	),
    $product, $args );
}
else {
// add to kit
echo apply_filters( 'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
    sprintf( '<a href="%s" data-quantity="%s" class="%s" %s>
    <i class="far fa-minus-circle  hidden" data-name="%s" data-sku="%s" data-category="%s" data-product_id="%s" data-variation_id="%s"></i>
    <span class="add-to-cart-text" data-default-text="%s" data-cart-text="%s">%s</span>
    <i class="fal fa-plus-circle" data-name="%s" data-sku="%s" data-category="%s" data-kit_id="%s" data-prod_id="%s" data-cat_id="%s" data-swap="%s"></i>
    </a>',
		esc_url( $product->add_to_cart_url() ), // for href=
		esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ), // for data-quantity=
		esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ), // for class=
        isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '', // for attributes
        esc_html($product_name), // for data-name=
        esc_html($product_sku), // for data-sku=
        esc_html($product_category), // for data-category=
        esc_attr($product_id), // for data-product_id=
        esc_attr($variation_id), // for data_variation_id=
        esc_html( $default_text), // for data-default-text=
        esc_html($cart_text), // for data-cart-text=
        $default_text, // for span text
        esc_html($product_name), // for data-name=
        esc_html($product_sku), // for data-sku=
        esc_html($product_category), // for data-category=
        esc_html($kit_id), // for the data-kit_id=
        esc_html($product_id), // for the data-prod_id=
        esc_html($category_id), // for the data-cat_id=
        esc_html($mid_swap) // for the data-swap=
	),
    $product, $args );
}
