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
    $product_category = $product_category_obj->name;
}
$args['class'] = 'btn btn-lg btn-block btn-primary add-to-cart';
$default_text = 'Add to kit';
$cart_text = ' in kit';

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

echo apply_filters( 'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
    sprintf( '<button href="%s" data-quantity="%s" class="%s" %s>
    <i class="far fa-minus-circle  hidden" data-name="%s" data-sku="%s" data-category="%s"></i>
    <span class="add-to-cart-text" data-default-text="%s" data-cart-text="%s">%s</span>
    <i class="fal fa-plus-circle" data-name="%s" data-sku="%s" data-category="%s"></i>
    </button>',
		esc_url( $product->add_to_cart_url() ), // for href=
		esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ), // for data-quantity=
		esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ), // for class=
        isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '', // for attributes
        esc_html($product_name), // for data-name=
        esc_html($product_sku), // for data-sku=
        esc_html($product_category), // for data-category=
        esc_html( $default_text), // for data-default-text=
        esc_html($cart_text), // for data-cart-text=
        $default_text, // for span text
        esc_html($product_name), // for data-name=
        esc_html($product_sku), // for data-sku=
        esc_html($product_category) // for data-category=
	),
$product, $args );