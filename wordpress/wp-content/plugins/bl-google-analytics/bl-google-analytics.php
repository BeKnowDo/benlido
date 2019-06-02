<?php
/*
Plugin Name: Ben Lido Google Analytics
Plugin URI: http://www.benlido.com/
Description: Custom Google Analytics tracking because of custom code
Version: 1.0
*/



function bl_google_analytics_cart_page() {
    $html = '';
    $should_show = bl_should_show_add_to_cart();
    $items = array();
    if ($should_show == true) {
        $kit_list = bl_get_kit_list();
        //print_r ($kit_list);
        if (!empty($kit_list) && is_array($kit_list)) {
            $items = $kit_list['items'];
        }
        bl_clear_add_to_cart();
    }
    if (!empty($items) && is_array($items)) {
        foreach ($items as $item) {
            //print_r ($item);
            $prod = array();
            $brand_name = '';
            $sku = '';
            $category_name = '';
            $prod_id = $item['product'];
            $price = 0;
            $quantity = $item['quantity'];
            if (function_exists('wc_get_product')) {
                $prod_obj = wc_get_product($prod_id);
                if ($prod_obj) {
                    $sku = $prod_obj->get_sku();
                    $price = $prod_obj->get_price();
                }
            }

            if ($prod_id) {
                $brand_ids = wp_get_post_terms($prod_id,'product_brand');
            }

            if (!empty($brand_ids) && is_array($brand_ids)) {
                $brand_obj = $brand_ids[0];
                if (is_object($brand_obj)) {
                    $brand_name = $brand_obj->name;
                }
            }
            
            $category_id = $item['category'];
            $variation_id = $item['variation'];
            $quantity = $item['quantity'];
            $name = get_the_title($prod_id);
            $category = get_term($category_id);
            if ($category) {
                $category_name = $category->name;
            }
            $prod = array(
                'sku' => $sku,
                'name' => $name,
                'category' => $category_name,
                'brand' => $brand_name,
                'variant' => $variation_id,
                'price' => $price,
                'quantity' => $quantity
            );

            $html .= bl_ga_add_to_cart($prod);
        }
    }
    echo $html;
}

// set session so that show add to cart events on the car page
function bl_show_add_to_cart() {

    if( function_exists('WC')) {
        WC()->session->set_customer_session_cookie(true);
        WC()->session->set( 'bl_show_add_to_cart', 1);
    }

}

function bl_should_show_add_to_cart() {
    $should_add = WC()->session->get( 'bl_show_add_to_cart' );
    return $should_add;
}

function bl_clear_add_to_cart() {
    WC()->session->set_customer_session_cookie(true);
    WC()->session->set( 'bl_show_add_to_cart', null);
}

function bl_ga_add_to_cart($product) {
    // for each of the items, we will generate a custom set of javacript
    $script = '';
    if (!empty($product)) {
        $script = '<script type="text/javascript">';
        $script .= ' if (typeof ga == "function") {';
        $script .= ' ga("ec:addProduct", {';

        $script_alt = '<script type="text/javascript">';
        $script_alt .= ' if (typeof __gaTracker == "function") {';
        $script_alt .= ' __gaTracker("ec:addProduct", {';
    
    
        // see what we got
        // sku
        if ($product['sku']) {
            $script .= ' "id" : "' . $product['sku'] . '", ';
            $script_alt .= ' "id" : "' . $product['sku'] . '", ';
        }
    
        // name
        if ($product['name']) {
            $script .= ' "name" : "' . esc_attr($product['name']) . '", ';
            $script_alt .= ' "name" : "' . esc_attr($product['name']) . '", ';
        }
    
        // category
        if ($product['category']) {
            $script .= ' "category" : "' . esc_attr($product['category']) . '", ';
            $script_alt .= ' "category" : "' . esc_attr($product['category']) . '", ';
        }
    
        // brand
        if ($product['brand']) {
            $script .= ' "brand" : "' . esc_attr($product['brand']) . '", ';
            $script_alt .= ' "brand" : "' . esc_attr($product['brand']) . '", ';
        }
    
        // variant
        if ($product['variant']) {
            $script .= ' "variant" : "' . $product['variant'] . '", ';
            $script_alt .= ' "variant" : "' . $product['variant'] . '", ';
        }
    
        // price
        if ($product['price']) {
            $script .= ' "price" : "' . $product['price'] . '", ';
            $script_alt .= ' "price" : "' . $product['price'] . '", ';
        }
    
        // sku
        if ($product['quantity']) {
            $script .= ' "quantity" : "' . $product['quantity'] . '", ';
            $script_alt .= ' "quantity" : "' . $product['quantity'] . '", ';
        }
    
        $script .= ' });';
        $script .= 'ga("ec:setAction", "add");';
        $script .= 'ga("send", "event", "UX", "click", "add to cart"); ';
        $script .= '}';
        $script .= '</script>';

        $script_alt .= ' });';
        $script_alt .= '__gaTracker("ec:setAction", "add");';
        $script_alt .= '__gaTracker("send", "event", "UX", "click", "add to cart"); ';
        $script_alt .= '}';
        $script_alt .= '</script>';

    }

    return $script . $script_alt;
}
