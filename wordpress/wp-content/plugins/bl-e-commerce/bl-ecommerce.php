<?php
/*
Plugin Name: Ben Lido Custom E-Commerce Plugin
Plugin URI: http://www.benlido.com/
Description: provides custom e-commerce functionality for the site
Version: 1.0
*/


/**
 * NOTE: if we want to see if the category is attached to the cart item, we can turin it on in here: bl_get_cart_item_meta()... set bypass to true
 */
global $bl_ecommerce_api_slug;
$bl_ecommerce_api_slug = '/bl-api';
global $bl_custom_kit_id;
$bl_custom_kit_id = 99999999;

// adding frequency to the session for checkout
if (!function_exists('bl_add_frequency_to_session')) {
	function bl_add_frequency_to_session() {
		if (isset($_POST['frequency'])) {
			$frequency = intval($_POST['frequency']);
			if( function_exists('WC')) {
                WC()->session->set_customer_session_cookie(true);
				WC()->session->set( 'frequency', $frequency);
				global $woocommerce;
				$checkout_url = $woocommerce->cart->get_checkout_url();
				wp_redirect($checkout_url);
			}
		}
	 }
}

// for adding frequency to session
add_action( 'wp_loaded', 'bl_add_frequency_to_session', 500);

// we will always have a category with the product added
function bl_add_cart_item_category( $cart_item_data, $product_id, $variation_id ) {
    if (isset($cart_item_data['category'])) {
        $category_id = $cart_item_data['category'];
    }
    if (empty($category_id)) {
        $category_id = filter_input( INPUT_POST, 'category_id' );
    }
    
    if (empty($category_id)) {
        // get the default category of this product using Yoast SEO
        if (class_exists('WPSEO_Primary_Term')) {
            $primary_term = new WPSEO_Primary_Term( 'product_cat', $product_id );
            $category_id = $primary_term->get_primary_term();
        }
    }



	if ( empty( $category_id ) ) {
		return $cart_item_data;
	}

	$cart_item_data['category'] = $category_id;
	return $cart_item_data;
}

add_filter( 'woocommerce_add_cart_item_data', 'bl_add_cart_item_category', 10, 3 );

// this is to maintain the category value throughout the lifecycle of the shopping cart
function bl_get_cart_item_from_session( $cart_item, $values ) {
 
    if ( isset( $values['category'] ) ){
        $cart_item['category'] = $values['category'];
    }
 
    return $cart_item;
 
}
add_filter( 'woocommerce_get_cart_item_from_session', 'bl_get_cart_item_from_session', 20, 2 );

function bl_add_order_item_meta( $item_id, $values ) {
 
    if ( ! empty( $values['category'] ) ) {
        woocommerce_add_order_item_meta( $item_id, 'category', $values['category'] );           
    }
}
add_action( 'woocommerce_add_order_item_meta', 'bl_add_order_item_meta', 10, 2 );

// check to see if we have a frequency selected
function bl_check_frequency() {
    $frequency = WC()->session->get( 'frequency' );
    return $frequency;
}

function bl_get_cart_item_meta($data, $cartItem) {
    $bypass = true;
    if ( isset( $cartItem['category'] )  && $bypass == false) {
        $data[] = array(
            'name' => 'Category',
            'value' => $cartItem['category']
        );
    }
    return $data;
}

function bl_set_frequency($user_id,$order_id,$frequency=0) {
    $kits = array();
    $holder = array();
    if (function_exists('get_field')) {
        $kits = get_field('recurring_orders','user_'.$user_id);
    }
    if (!empty($kits) && is_array($kits)) {
        foreach ($kits as $kit) {
            if ($kit['order_id'] == $order_id) {
                $kit['frequency'] = $frequency;
                // calculate next shipment
                $last_send_date_string = $kit['last_send_date'];
                $last_send_date = strtotime($last_send_date_string);
                $next_send_date = $last_send_date + ( intval($frequency) * 24 * 60 * 60 );
                $kit['next_send_date'] = date('Ymd',$next_send_date);
            }
            $holder[] = $kit;
        }
    }
    if (function_exists('update_field')) {
        update_field('recurring_orders',$holder,'user_'.$user_id);
    }
    return true;
}

add_filter( 'woocommerce_get_item_data', 'bl_get_cart_item_meta', 10, 2 );

if (!function_exists('bl_is_swap')) {
    function bl_is_swap() {
        // if we're on the kitting page, then display the swap button instead
        $kitting_page = null;
        if (function_exists('get_field')) {
            $kitting_page = get_field('kitting_page','option');
        }
        if (!empty($kitting_page) && isset($kitting_page->ID)) {
            $kitting_page_id = $kitting_page->ID;
            global $post;
            // if we are on the kitting page, then we display the swap button instead of add to cart
            if (!empty($post) && $post->post_type == 'page' && $post->ID == $kitting_page_id) {
                return true;
            }
        }
        $product_swap = bl_get_product_swap();
        if (!empty($product_swap) && !empty($product_swap['kit_id']) && !empty($product_swap['product_id'])) {
            return true;
        }
    }
}

if (!function_exists('bl_is_kit_add')) {
    function bl_is_kit_add() {
        $is_kit_add = WC()->session->get( 'is_kit_add' );
        return $is_kit_add;
    }
}

function bl_process_checkout_url($url) {
    $frequency = bl_check_frequency();
    if (empty($frequency) && function_exists('get_field')) {
        // we forward over to the frequency 
        $delivery_frequency_page = get_field('delivery_frequency_page','option');
        if (!empty($delivery_frequency_page)) {
            $url = get_permalink($delivery_frequency_page);
        }
    }
    return $url;
}

//add_filter( 'woocommerce_get_checkout_url', 'bl_process_checkout_url', 50 );

if (!function_exists('bl_set_purchase_flow')) {
    function bl_set_purchase_flow($flow=1) {
        // flow => 1 // create your own kit
        // flow => 2 // prebuilt kit
        WC()->session->set_customer_session_cookie(true);
        WC()->session->set( 'purchase_flow', $flow);
    }
}

if (!function_exists('bl_get_purchase_flow')) {
    function bl_get_purchase_flow() {
        $purchase_flow = WC()->session->get( 'purchase_flow' );
        return $purchase_flow;
    }
}

if (!function_exists('bl_set_kit_list')) {
    /**
     * Set Kit List
     * @param int $kit_id the id of the kit
     * @param mixed $bag array('bag'=>int,'variation'=>int)
     * @param mixed $items array of the product/category items: array('category'=>int, 'product'=>int, 'variation'=>int, 'quantity'=>int)
     */
    function bl_set_kit_list($kit_id,$bag,$items) {
        $kit = array('kit_id'=>$kit_id,'bag'=>$bag,'items'=>$items);
        WC()->session->set_customer_session_cookie(true);
        WC()->session->set( 'current_kit', $kit);
    }
}

if (!function_exists('bl_clear_all_kit_data')) {
    function bl_clear_all_kit_data() {
        WC()->session->set( 'current_kit', null);
        WC()->session->set( 'current_product_swap', null);
        WC()->session->set( 'purchase_flow', null);
        WC()->session->set( 'frequency', null);
        WC()->session->set( 'is_kit_add', null);
    }
}

if (!function_exists('bl_get_kit_list')) {
    function bl_get_kit_list() {
        $current_kit = WC()->session->get( 'current_kit' );
        return $current_kit;
    }
}

if (!function_exists('bl_set_product_swap')) {
    function bl_set_product_swap($kit_id,$category_id,$product_id) {
        $kit_id = intval(trim($kit_id));
        bl_save_current_kit($kit_id);
        $category_id = intval(trim($category_id));
        $product_id = intval(trim($product_id));
        if (is_numeric($kit_id) && is_numeric($product_id)) {
            if (!empty($category_id)) {
                $category_id = intval($category_id);
            } 
            $swapping = array('kit_id'=>$kit_id,'category_id'=>$category_id,'product_id'=>$product_id);
            WC()->session->set_customer_session_cookie(true);
            WC()->session->set( 'current_product_swap', $swapping);
        }
    }
}

if (!function_exists('bl_get_product_swap')) {
    function bl_get_product_swap() {
        $current_product_swap = WC()->session->get( 'current_product_swap' );
        return $current_product_swap;
    }
}

if (!function_exists('bl_clear_product_swap')) {
    function bl_clear_product_swap() {
        WC()->session->set_customer_session_cookie(true);
        WC()->session->set( 'current_product_swap',null );
    }
}

if (!function_exists('bl_set_kit_add')) {
    // sets whether we are adding to a kit or not
    function bl_set_kit_add($kit_id,$active=1) {
        $kit_id = intval(trim($kit_id));
        // we also need to make sure we have a kit
        $current_kit_id = bl_get_current_kit_id();
        if ($current_kit_id > 0 && $active == 1 && $kit_id > 0 && $current_kit_id != $kit_id) {
            bl_save_current_kit($kit_id);
        }
        if ($current_kit_id < 1 && $active == 1 && $kit_id > 0) {
            bl_save_current_kit($kit_id);
        }
        
        if ($active != 1) {
            $active = 0;
        }
        WC()->session->set_customer_session_cookie(true);
        WC()->session->set( 'is_kit_add', $active );
    }
}

if (!function_exists('bl_start_swap')) {
    // starting the swap process. register the item that is being swapped, waiting for the new item to be chosen
    function bl_start_swap($kit_id,$prod_id,$cat_id) {
        $url = '';
        bl_set_product_swap($kit_id,$cat_id,$prod_id);
        // then, we will redirect to the category
        if (!empty($cat_id) && is_numeric($cat_id)) {
            $url = get_term_link(intval($cat_id),'product_cat');
        }
        if (empty($url) || is_wp_error( $url )) {
            $url = get_permalink(woocommerce_get_page_id( 'shop' ));
        }

        return $url;
    }
}

if (!function_exists('bl_create_custom_kit')) {
    function bl_create_custom_kit() {
        global $bl_custom_kit_id;
        $holder = array();
        //$kit_list = bl_get_kit_list();
        $bag_obj = bl_get_bag_from_cart();
        if (!empty($bag_obj)) {
            $bag = array('bag'=>$bag_obj['id'],'variation'=>$bag_obj['variation_id']);
        }
        $kit_id = $bl_custom_kit_id;
        bl_set_kit_list($kit_id,$bag,$holder);
        bl_set_kit_add($kit_id,true);
        wp_redirect(wc_get_page_permalink( 'shop' ));

    }

}

if (!function_exists('bl_select_item_as_swapped')) {
    // this is to pick the item to swap with the one that was chosen before
    function bl_select_item_as_swapped($kit_id,$prod_id,$cat_id) {
        $kit_id = intval(trim($kit_id));
        $prod_id = intval(trim($prod_id));
        $cat_id = intval(trim($cat_id));
        $orig = bl_get_product_swap();
        $items = array();
        $holder = array();
        $kit_list = bl_get_kit_list();
        //print_r ($kit_list);
        if (!empty($kit_list)) {
            $items = $kit_list['items'];
        }
        if (is_array($items)) {
            foreach ($items as $item) {
                //print_r ($orig);
                if ($item['product'] == $orig['product_id']) {
                    // not adding this product back
                } else {
                    $holder[] = $item;
                }
            }
        }
        if (!empty($holder) && is_array($holder) && !empty($prod_id)) {
            $new = array('category'=>$cat_id,'product'=>$prod_id,'variation'=>null,'quantity'=>1);
            array_unshift($holder,$new);
        }
        if (empty($holder)) {
            $holder[] = array('category'=>$cat_id,'product'=>$prod_id,'variation'=>null,'quantity'=>1);
        }
        if (!empty($holder)) {
            bl_set_kit_list($kit_id,$kit_list['bag'],$holder);
            bl_clear_product_swap();
            return true;
        }

        //print_r ($kit_list);
        return false;
    }
}

if (!function_exists('bl_get_cart')) {
    function bl_get_cart() {
        // this gets the shopping cart and displays in a format that the front end likes
        $cart = WC()->cart->get_cart();
        $holder = array();
        if (!empty($cart) && is_array($cart)) {
            foreach ($cart as $hash => $item) {
                // just need sku, category, name, count, and image
                
                //print_r ($test);
                $variation_id = 0;
                if (isset($item['data'])) {
                    //print_r ($item['data']);
                    $mini_cart_max_num_words = 5;
                    if (function_exists('get_field')) {
                        $mini_cart_max_num_words = get_field('mini_cart_max_num_words','option');
                    }
                    $id = $item['data']->get_id();
                    $name = wp_trim_words($item['data']->get_title(),$mini_cart_max_num_words);
                    $sku = $item['data']->get_sku();
                    //$image = $item['data']->get_image();
                    $image_obj = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'woocommerce_thumbnail' );
                    if (!empty($image_obj) && is_array($image_obj)) {
                        $image = $image_obj[0];
                    }
                    $category_ids = $item['data']->get_category_ids();
                    $quantity = $item['quantity'];
                    if (isset($item['variation_id'])) {
                        $variation_id = $item['variation_id'];
                    }
                    if (is_array($category_ids) && !empty($category_ids)) {
                        $cat = get_term($category_ids[0]);
                        if (!empty($cat) && isset($cat->name)) {
                            $category_name = $cat->name;
                            $category_id =$cat->term_id;
                        }
                    }
                    $category = $category_name;
                }
                if (empty($category)) {
                    $item_detail = WC()->cart->get_cart_item($hash);
                    //print_r ($item_detail);
                    if (!empty($item_detail) && isset($item_detail['category'])) {
                        $category_id = $item_detail['category'];
                    }
                    if (!empty($category_id)) {
                        $term = get_term($category_id);
                        if (!empty($term)) {
                            $category = $term->name;
                        }
                    }
                }
                if (empty($category) && isset($item['meta_data']) && !empty($item['meta_data'])) {
                    $category = $item['meta_data'];
                }
                if (empty($category) && !empty($category_name)) {
                    $category = $category_name;
                }

                $holder[] = array('id'=>$id,'variation_id'=>$variation_id,'sku'=>$sku,'category'=>$category,'category_id'=>$category_id,'name'=>$name,'count'=>$quantity,'image'=>$image);
            }
        }
        return $holder;
    }
}

if (!function_exists('bl_remove_cart_items')) {
    function bl_remove_cart_items() {
        // NOTE: when we clear cart items, we always leave the bag
        // first, get the bag, then clear the cart, then add the bag back
        $bag = bl_get_bag_from_cart();
        WC()->cart->empty_cart();
        if ($bag) {
            $product_id = $bag['id'];
            $category_id = $bag['category_id'];
            $quantity = $bag['count'];
            $variation_id = null; // NOTE: the product_id is the variation ID
            bl_add_to_cart($product_id,$category_id,$quantity,$variation_id);
        }
    }
}

if (!function_exists('bl_add_current_kit_to_cart')) {
    function bl_add_current_kit_to_cart() {
        $kit_list = bl_get_kit_list();
        // bag
        $bag = $kit_list['bag'];

        if (!empty($bag)) {
            $bag_id = $bag['bag'];
            if (function_exists('get_field')) {
                $bag_cat_id = get_field('bag_category','option');
            }
            $meta = array('category'=>$bag_cat_id); 
            //$res = WC()->cart->add_to_cart($bag_id,1,0,array(),$meta);

        }
        $items = $kit_list['items'];
        if (!empty($items) && is_array($items)) {
            foreach ($items as $item) {
                $product_id = $item['product'];
                $variation_id = $item['variation'];
                $category_id = $item['category'];
                $quantity = $item['quantity'];
                $meta = array('category'=>$category_id);
                WC()->cart->add_to_cart($product_id,$quantity,$variation_id,array(),$meta);
            }
        }
        //print_r ($items);
        //die;
        /*
        if (function_exists('get_field')) {
            $delivery_frequency_page = get_field('delivery_frequency_page','option');
        }
        if (!empty($delivery_frequency_page) && is_object($delivery_frequency_page)) {
            $delivery_frequency_page_url = get_permalink($delivery_frequency_page);
        }
        if (!empty($delivery_frequency_page_url)) {
            wp_redirect($delivery_frequency_page_url);
        }
        */
        // redirect to the cart page
        wp_redirect(wc_get_cart_url());
        exit;
        //return true;
        
    }
}

if (!function_exists('bl_add_to_cart')) {
    function bl_add_to_cart($product_id,$category_id,$quantity=1,$variation_id=0) {
        $meta = array('category'=>$category_id);
        /*
        echo "\n";
        echo $product_id;
        echo "\n";
        echo $variation_id;
        echo "\n";
        echo $category_id;
        echo "\n";
        echo $quantity;
        echo "\n";
        print_r ($meta);
        */
        // if it is a bag, we'll switch with another one
        $bag_category = '';
        if (function_exists('get_field')) {
            $bag_category = get_field('bag_category','option');
            //print_r($bag_category);
        }
        if ($bag_category == $category_id) {
            $bag = bl_get_bag_from_cart();
            // we're going to remove the existing bag before adding the new one.
            if ($bag) {
                $bag_product_id = $bag['id'];
                $bag_category_id = $bag['category_id'];
                $bag_quantity = $bag['count'];
                $bag_variation_id = null; // NOTE: the product_id is the variation ID
                if (function_exists('WC') && !empty(WC()->cart)) {
                    bl_remove_from_cart($bag_product_id,$bag_variation_id,$bag_quantity);
                }
            }
        }
        $res = WC()->cart->add_to_cart($product_id,$quantity,$variation_id,array(),$meta);
        return $res;
    }
} // end bl_add_to_cart()

if (!function_exists('bl_remove_from_cart')) {
    function bl_remove_from_cart($product_id,$variation_id=0,$quantity=1) {
        // NOTE: sometimes, the product_id is the same as variation ID because some products returns the variation_id as the product_id as well.
        //       so, we need to work from trying to get 
        $response = false;
        $cart = WC()->cart->get_cart();
        $holder = array();
        $match_key = '';
        $match_quantity = 0;
        if (!empty($cart) && is_array($cart)) {
            foreach ($cart as $hash => $item) {
                $temp_product_id = $item['product_id'];
                $temp_variation_id = $item['variation_id'];
                $temp_quantity = $item['quantity'];
                if ($product_id == $temp_product_id && $product_id != $variation_id) {
                    $match_key = $hash;
                    $match_quantity = $temp_quantity;
                }
                if ($variation_id > 0 && $variation_id == $temp_variation_id) {
                    $match_key = $hash;
                    $match_quantity = $temp_quantity;
                }
                // maybe product_id is the variation ID
                if ($temp_variation_id == $product_id && $variation_id == 0) {
                    $match_key = $hash;
                    $match_quantity = $temp_quantity;
                }
                
            }
        }
        if (!empty($match_key) && $match_quantity > 0) {
            // we're just going to remove that whole line for now
            
            $final_quantity = intval($match_quantity) - $quantity;
            if ($final_quantity > 0) {
                $response = WC()->cart->set_quantity($match_key, $final_quantity);
            } else {
                $response = WC()->cart->remove_cart_item($match_key);
            }
            
        }
        return $response;
    }
}

/** 
 * this gets the current kit items, whether it's a new kit page, or if there is an existing kit in session
 * @param int $kit the custom post type ID for travel_kit
*/
function bl_get_current_kit_items($kit) {
    // see if we have the a kit in session
    $kit_list = bl_get_kit_list();
    $items = array();
    if (!empty($kit_list) && isset($kit_list['kit_id']) && $kit == $kit_list['kit_id']) {
        $items = $kit_list['items'];
        $items_holder = array();
        // we'll need to process this for the page.
        if (!empty($items) && is_array($items)) {
            foreach ($items as $item) {
                //print_r ($item);
                $tmp_category = $item['category'];
                $tmp_product = $item['product'];
                $items_holder[] = array('category'=>$tmp_category,'featured_product'=>get_post($tmp_product));
            }
            $items = $items_holder;
        }
    } else {
        $items = bl_get_kit_items($kit);
    }
    return $items;
}

// gets the product_id, the variation_id from the shopping cart so that we can populate the front end on which one is selected
function bl_get_bag_from_cart() {
    if (function_exists('get_field')) {
        $bag_category = get_field('bag_category','option');
    }
    if ($bag_category > 0) {
        $items = bl_get_cart();
    }
    if (!empty($items) && is_array($items)) {
        foreach ($items as $item) {
            if ($item['category_id'] == $bag_category) {
                return $item;
            }
        }
    }
    return array();
}

// some supporting functions
// gets the current session kit ID
function bl_get_current_kit_id() {
    $kit_id = 0;
    $current_page = get_post();
    $type = '';
    if (!empty($current_page) && is_object($current_page)) {
        $type = get_post_type($current_page);
    }
    $current_page_id = 0;
    $kitting_page_id = 0;
    if (function_exists('get_field')) {
        $kitting_page = get_field('kitting_page','option');
        if (!empty($kitting_page) && is_array($kitting_page) && isset($kitting_page['id'])) {
            $kitting_page_id = $kitting_page['id'];
        }
        if (!empty($kitting_page) && is_object($kitting_page) && isset($kitting_page->ID)) {
            $kitting_page_id = $kitting_page->ID;
        }
    }
    if (!empty($current_page) && is_object($current_page) && isset($current_page->ID) && $type == 'page') {
        $current_page_id = $current_page->ID;
    }

    if (isset($_REQUEST['id']) && $current_page_id > 0 && $current_page_id == $kitting_page_id) {
        $kit_id = intval($_REQUEST['id']);
    }
    
    $kit_list = bl_get_kit_list();

    if (!empty($kit_list) && $kit_id < 1) {
        $kit_id = $kit_list['kit_id'];
    }
    if (!is_numeric($kit_id)) {
        $kit_id = 0;
    }
    return $kit_id;
}

function bl_get_kit_page_url($id) {
    global $bl_custom_kit_id;
    if (function_exists('get_field')) {
        $kitting_page = get_field('kitting_page','option');
    }
    if (!empty($kitting_page) && is_object($kitting_page)) {
        $kitting_page = get_permalink($kitting_page->ID);
    }
    $url = $kitting_page;
    if ($id != $bl_custom_kit_id) {
        $url = $kitting_page . '?id=' . $id;
    }
    
    
    return $url;
}

function bl_save_current_kit($id) {
    // first, get the products of the kit
    if (get_post_type($id) == 'travel_kit') {
        $bag = null;
        // see if we have a bag already in cart
        $test_bag = bl_get_bag_from_cart();
        $product_categories = array();
        $items = array();
        if (function_exists('get_field')) {
            $bag = get_field('bag_for_this_kit',$id);
            $product_categories = get_field('product_categories',$id);
        }
        if (!empty($bag)) {
            // just saving bag ID
            $bag_id = 0;
            if (isset($bag->ID)) {
                $bag_id = $bag->ID;
            }
            $bag = array('bag'=>$bag_id,'variation'=>null);
        }
        if (!empty($product_categories) && is_array($product_categories)) {
            foreach ($product_categories as $item) {
                // array('category'=>int, 'product'=>int, 'variation'=>int, 'quantity'=>int)
                $category_id = $item['category'];
                $prod = $item['featured_product'];
                if (isset($prod->ID)) {
                    $product_id = $prod->ID;
                }    
                // always 1 at first
                $quantity = 1;

                // variation is a placeholder for now
                $variation = null;
                $items[] = array('category'=>$category_id,'product'=>$product_id,'variation'=>$variation,'quantity'=>$quantity);
            }
        }
        //$kit = array('kit_id'=>$id,'bag'=>$bag,'items'=>$items);
        bl_set_kit_list($id,$bag,$items);
        if (function_exists('bl_remove_cart_items')) {
            bl_remove_cart_items();
        }   
        
    } // if travel kit

} // bl_save_current_kit()

function bl_add_to_kit($kit_id,$product_id,$category_id) {
    // array('kit_id'=>$kit_id,'bag'=>$bag,'items'=>$items);
    $kit_list = bl_get_kit_list();
    $current_kit_id = $kit_list['kit_id'];
    $holder = array();
    $added = false;
    if ($kit_id == $current_kit_id) {
        $items = $kit_list['items'];
        if (!empty($items) && is_array($items)) {
            
            foreach ($items as $item) {
                // we're just going to use the existing category
                if ($item['product'] == $product_id) {
                    $item['quantity']++;
                    $added = true;
                }
                $holder[] = $item;
            }
        }
        if ($added == false) {
            array_unshift($holder,array('category'=>$category_id,'product'=>$product_id,'variation'=>null,'quantity'=>1));
        }
    }
    if (!empty($holder)) {
        bl_set_kit_list($kit_id,$kit_list['bag'],$holder);
        return true;
    }
    return false;
}

// removes the item from the kit
function bl_remove_from_kit($kit_id,$product_id,$category_id) {
    // first see if we have a kit
    $has_category_id = true;
    $items_holder = array();
    if (empty($kit_id) || empty($product_id)) {
        return false;
    }
    if (empty($category_id)) {
        $has_category_id = false;
    }
    $current_kit_id = bl_get_current_kit_id();
    if (empty($current_kit_id)) {
        bl_save_current_kit($kit_id);
    }
    $kit_list = bl_get_kit_list();
    // first, match kit ID
    //print_r ($kit_list);
    if (!empty($kit_list)) {
        if ($kit_list['kit_id'] != $kit_id) {
            // this means that the person has navigated to a new kit
            bl_save_current_kit($kit_id); // now, we are in a new kit
            $kit_list = bl_get_kit_list();
        }
        // now, let's see which to remove
        $items = $kit_list['items'];
        if (!empty($items) && is_array($items)) {
            foreach ($items as $item) {
                if ($has_category_id == true) {
                    if ($item['category'] == $category_id && $item['product'] == $product_id) {
                        // this is how we remove
                        // we might want to do something else here.
                    } else {
                        $items_holder[] = $item;
                    }
                } else {
                    if ($item['product'] == $product_id) {
                        // this is how we remove
                        // we might want to do something else here.
                    } else {
                        $items_holder[] = $item;
                    }
                }
                
            }
        }
        bl_set_kit_list($kit_list['kit_id'],$kit_list['bag'],$items_holder);
        $kit_list = bl_get_kit_list();
        //print_r ($kit_list);
        return true;
    }
    return false;
} // end bl_remove_from_kit()


function bl_insert_user_acf($field_name,$field_value,$user_id) {
    // for ACF if you are creating the field for the first, time you need to use the field key
    // so, this looks for the field key and uses that to create the field
    
    $user_group = 'group_5a6b4aa83bd63';
    // NOTE: there is a chance that the product gorup hash might change
    if (function_exists('bl_get_product_acf_field_key')) {
        //error_log('getting acf field object');
        $key = bl_get_product_acf_field_key($field_name,$user_group);
        if (!empty($key)) {
            update_field($key,$field_value,'user_'.$user_id);
        }
    }

}

function bl_save_purchased_kit($kit_list,$frequency,$order_id=0) {
    // set the right timezone
    $timezone = date_default_timezone_get();
    if ($timezone == 'UTC') {
        // setting as East coase
        date_default_timezone_set('America/New_York');
    }
    $user_id = get_current_user_id();
    $is_new = false;
    $skip_add = false;
    $kit_id = 0;
    $recurring_name = 'Kit Order: ' . date('l F j, Y g:ia');
    $items = array();
    $hash_match_new = ''; // this is to match up the products to see if we shold save kit
    $hash_match_existing = '';
    $items_holder = array();
    $bag = array();
    $last_send_date = date('Ymd');
    $bag_id = 0;
    $bag_color_variation = 0;
    if (empty($frequency) || !is_numeric($frequency)) {
        $frequency = 0;
    }
    $next_send_date = date('Ymd',time() + ($frequency * 24 * 60 * 60));
    $current_recurring = array();
    //print_r($frequency);
    //echo "Kit List";
    //print_r ($kit_list);
    if (is_array($kit_list) && isset($kit_list['kit_id'])) {
        $kit_id = $kit_list['kit_id'];
        $items_holder = $kit_list['items'];
        $bag = $kit_list['bag'];
        if (function_exists('get_field')) {
            $recurring_orders = get_field('recurring_orders','user_'.$user_id);
            //echo "Recurring Orders:";
            //print_r ($recurring_orders);
            //echo "<br />";
        }
        if (empty($recurring_orders)) {
            $is_new = true;
        }
    }
    if (!empty($bag) && isset($bag['bag'])) {
        $bag_id = $bag['bag'];
        $bag_color_variation = $bag['variation'];
    }
    if (!empty($items_holder) && is_array($items_holder)) {
        foreach ($items_holder as $item) {
            $category = $item['category'];
            $product = $item['product'];
            $variation = $item['variation'];
            $quantity = $item['quantity'];
            $items[] = array(
                'category' => $category,
                'product' => $product,
                'product_variation' => $variation,
                'quantity' => $quantity
            );

        }
    }
    $current_recurring = array(
        'recurring_name' => $recurring_name,
        'frequency' => $frequency,
        'order_id' => $order_id,
        'last_send_date' => $last_send_date,
        'next_send_date' => $next_send_date,
        'bag' => $bag_id,
        'bag_color_variation' => $bag_color_variation,
        'items' => $items
    );
    //echo "current recurring:";
    //print_r ($current_recurring);
    //echo "<br />";
    if (!empty($recurring_orders) && is_array($recurring_orders)) {
        // we're going to iterate through it to see if we have a of products. If so, we don't create a new one.
        $hash_match_new = bl_process_items_for_hash_match($items);
        
        foreach ($recurring_orders as $recurring_order) {
            $test_items_holder = array();
            $test_items = $recurring_order['items'];
            if (is_array($test_items)) {
                foreach ($test_items as $test_item) {
                    $test_category = $test_item['category'];
                    $test_product = $test_item['product'];
                    $test_product_variation = $test_item['product_variation'];
                    $test_quantity = $test_item['quantity'];
                    $test_items_holder[] = array(
                        'category' => $test_category,
                        'product' => $test_product,
                        'product_variation' => $test_product_variation,
                        'quantity' => $test_quantity
                    );
                }
            }
            
        }

        $hash_match_existing = bl_process_items_for_hash_match($test_items_holder);
        if ($hash_match_existing == $hash_match_new) {
            $skip_add = true;
        }
    }
    if ($skip_add == false && $is_new == false) {
        $recurring_orders[] = $current_recurring;
    }
    if ($skip_add == false && $is_new == true) {
        $recurring_orders = array($current_recurring);
    }
    //print_r ($recurring_orders);
    if ($skip_add == false) {
        bl_insert_user_acf('recurring_orders',$recurring_orders,$user_id);
    }
    if (function_exists('bl_clear_all_kit_data')) {
        bl_clear_all_kit_data();
    }
} // bl_save_purchased_kit()

function bl_process_items_for_hash_match($items) {
    // NOTE: eventually, we'll need to match more than just the products. This is in case the customer wants to have multiple of the same kit for their children
    $holder = array();
    foreach ($items as $item) {
        $holder[] = $item['product'];
    }
    @sort($holder);
    //echo "test hash:";
    //print_r ($holder);
    //echo "<br />";
    return (md5(json_encode($holder)));
}

function bl_order_complete($order_id) {
    // let's see about getting the kit
    $kit_list = bl_get_kit_list();
    $frequency =  bl_check_frequency();
    $bag = array();
    
    if (!empty($kit_list) && $frequency > 0) {
        bl_save_purchased_kit($kit_list,$frequency,$order_id);
    } else {
        $items = array();
        $order = wc_get_order($order_id);
        // we're going to generate a kit list and save it
        if ($order && is_object($order) && get_class($order) == 'WC_Order') {
            $items = $order->get_items();
            $bag_cat_id = 0;
            if (function_exists('get_field')) {
                $bag_cat_id = get_field('bag_category','option');
            }
            
        }
        if (!empty($items) && is_array($items)) {
            $purchased_items = array();
            foreach ($items as $item_id => $item) {
                $category_id = $product_id = $variation_id = $quantity = 0;
                
                if (is_object($item)) {
                    $category_id = wc_get_order_item_meta($item_id,'category');
                }
                $item_data = $item->get_data();
                //print_r ($item_data);
                if (is_array($item_data)) {
                    $product_id = $item_data['product_id'];
                    $variation_id = $item_data['variation_id'];
                    $quantity = $item_data['quantity'];
                }
                if (is_numeric($category_id) && $category_id > 0 && $bag_cat_id == $category_id && $product_id > 0) {
                    // get the bag item if there is
                    $bag['bag'] = $product_id;
                    $bag['variation'] = $variation_id;
                }
                elseif (is_numeric($product_id) && $product_id > 0 && $quantity > 0) {
                    $purchased_items[] = array('category'=>$category_id, 'product'=>$product_id, 'variation'=>$variation_id, 'quantity'=>$quantity);
                }
                
            }
            if (!empty($purchased_items)) {
                $kit_list = array('kit_id'=>0,'bag'=>$bag,'items'=>$purchased_items);
                bl_save_purchased_kit($kit_list,$frequency,$order_id);
            }
        }
        
    }
    // we're going to clear all session data no matter what
    bl_clear_all_kit_data();
    return $order_id;
}

add_action('woocommerce_thankyou', 'bl_order_complete', 10, 1);

function bl_get_product_data($product_id,$variation_id) {
    // for now, we just need the image
    $res = array();
    if (function_exists('wc_get_product')) {
        $prod = wc_get_product($product_id);
    }
    if ($prod) {
        $variations = $prod->get_available_variations();
    }

    if (!empty($variations) && is_array($variations)) {
        foreach ($variations as $variation) {
            if ($variation_id == $variation['variation_id']) {
                $res = $variation;
            }
        }
    }
    return $res;
}

// uses an array of kits to see which ones have future shipping dates
function bl_get_kit_future_shippings($kits) {
    $time = time(); // so maybe we only allow the editing of recurring with a few days of buffer
    $today = date('Ymd');
    $future_kits = array();
    if (!empty($kits) && is_array($kits)) {
        foreach ($kits as $kit) {
            if ($kit['next_send_date'] >= $today) {
                $future_kits[] = $kit;
            }
        }
    }
    return $future_kits;
}


// this is the API endpoint parsing
function bl_ecommerce_url_intercept() {
    global $bl_ecommerce_api_slug;

    if (strlen(@stristr($_SERVER['REQUEST_URI'], $bl_ecommerce_api_slug)) > 0) {
        $section = $action = $id = null;
        $api_parts = explode('/',$_SERVER['REQUEST_URI']);
        //print_r ($api_parts);
        // parts: 0 => nothing, 1 => url keyword, 2 => section, 3 => action
        if (is_array($api_parts)) {
            if (isset($api_parts[2])) {
                $section = $api_parts[2];
            }
            if (isset($api_parts[3])) {
                $action = $api_parts[3];
            }
            if (isset($api_parts[4])) {
                $id = $api_parts[4];
            }
        }
        switch ($section) {
            case 'frequency':
                switch ($action) {
                    case 'set':
                        if (isset($api_parts[5])) {
                            $frequency = $api_parts[5];
                        }
                        // get the current user id
                        $user_id = get_current_user_id();
                        $res = bl_set_frequency($user_id,$id,$frequency);
                        if ($res == true) {
                            $response = array('success'=>true,'frequency'=>$frequency);
                            header('Content-Type: application/json');
                            print_r (json_encode($response));
                            die;
                        }
                        break;
                }
                break;
            case 'product':
                switch ($action) {
                    case 'get':
                        // /bl-api/cart/get/{product_id}/{variation_id}
                        if (isset($api_parts[5])) {
                            $variation_id = $api_parts[5];
                        }
                        $prod = bl_get_product_data($id,$variation_id);
                        header('Content-Type: application/json');
                        print_r (json_encode($prod));
                        
                        break;
                    default:
                        header('Content-Type: application/json');
                        print_r (json_encode(array()));
                        break;
                }
                break;
            case 'cart':
                switch ($action) {
                    case 'add':
                        // /bl-api/cart/add/{product_id}/{category_id}/{variation_id}/{qty}
                        $category_id=$variation_id=0;
                        if (isset($api_parts[5])) {
                            $category_id = $api_parts[5];
                        }
                        if (isset($api_parts[6])) {
                            $variation_id = $api_parts[6];
                        }
                        if (isset($api_parts[7])) {
                            $quantity = $api_parts[7];
                        }
                        if (empty($quantity)) {
                            $quantity = 1;
                        }
                        $response = array();
                        $res = bl_add_to_cart($id,$category_id,$quantity,$variation_id);
                        if ($res) {
                            $response['items'] = bl_get_cart();
                        }
                        header('Content-Type: application/json');
                        print_r (json_encode($response));
                        break;
                    case 'remove':
                        // /bl-api/cart/remove/{product_id}/{variation_id}/{qty}
                        $variation_id = 0;
                        $quantity = 1;
                        if (isset($api_parts[5])) {
                            $variation_id = $api_parts[5];
                        }
                        if (isset($api_parts[6])) {
                            $quantity = $api_parts[6];
                        }
                        $res = bl_remove_from_cart($id,$variation_id,$quantity);
                        if (!empty($res)) {
                            header('Content-Type: application/json');
                            $response = array();
                            $cart = bl_get_cart();
                            $response['items'] = $cart;
                            print_r (json_encode($response));
                        }
                        break;
                    default:
                        // gets the cart
                        header('Content-Type: application/json');
                        $cart = bl_get_cart();
                        print_r (json_encode($cart));
                        die;
                        break;
                }
                die;
                break;
            case 'kit':
                switch ($action) {
                    case 'select':
                        // this is when you select a product for swapping
                        //  /bl-api/kit/select/{kit_id}/{product_id}/{cat_id}
                        if (isset($api_parts[5])) {
                            $prod = $api_parts[5];
                        }
                        if (isset($api_parts[6])) {
                            $cat = $api_parts[6];
                        }
                        $res = bl_select_item_as_swapped($id,$prod,$cat);
                        if ($res == true) {
                            // after we've swapped, we go back to the kit page
                            $url = bl_get_kit_page_url($id);
                            header('Content-Type: application/json');
                            print_r (json_encode(array('url'=>$url)));
                            die;
                        }
                        break;
                    case 'state':
                        // sets whether we are adding an item to the kit, or are we done
                        if (isset($api_parts[5])) {
                            $state = $api_parts[5];
                        }
                        if (!empty($id) && !empty($state)) {
                            bl_set_kit_add($id,$state);
                        }
                        header('Content-Type: application/json');
                        // we always return success
                        $resp = array('success'=>true);
                        print_r (json_encode($resp));
                        die;
                        break;
                    case 'add':
                        //  /bl-api/kit/add/{kit_id}/{product_id}/{cat_id}
                        if (isset($api_parts[5])) {
                            $prod = $api_parts[5];
                        }
                        if (isset($api_parts[6])) {
                            $cat = $api_parts[6];
                        }
                        $resp = bl_add_to_kit($id,$prod,$cat);
                        if ($resp == true) {
                            // because it's the kit, we will redirect people back there
                            if (function_exists('bl_get_current_kit_id')) {
                                $kit_id = bl_get_current_kit_id();
                                bl_set_kit_add($kit_id,0); // reset add item to kit state
                            }
                            $url = bl_get_kit_page_url($kit_id);
                            header('Content-Type: application/json');
                            print_r (json_encode(array('url'=>$url)));
                            die;
                        }
                        die;
                        break;
                    case 'remove':
                        // removing an item from a kit
                        // should have 3 IDs: Kit ID, product ID, category ID
                        // /bl-api/kit/remove/{kit_id}/{product_id}/{category_id}
                        if (isset($api_parts[5])) {
                            $prod = $api_parts[5];
                        }
                        if (isset($api_parts[6])) {
                            $cat = $api_parts[6];
                        }
                        // maybe it's from the body
                        // TODO: need to finish this
                        $contents = file_get_contents('php://input');
                        if (!empty($contents)) {
                            $res = json_decode($contents,TRUE);
                        }
                        if (!empty($id)) {
                            $success = bl_remove_from_kit($id,$prod,$cat);
                        }
                        header('Content-Type: application/json');
                        // we always return empty
                        $cart = array();
                        print_r (json_encode($cart));
                        die;
                        break;
                    case 'swap':
                        //  /bl-api/kit/swap/{kit_id}/{product_id}/{cat_id}
                        if (isset($api_parts[5])) {
                            $prod = $api_parts[5];
                        }
                        if (isset($api_parts[6])) {
                            $cat = $api_parts[6];
                        }
                        $resp = bl_start_swap($id,$prod,$cat);
                        if (!empty($resp) && is_string($resp)) {
                            header('Content-Type: application/json');
                            print_r (json_encode(array('url'=>$resp)));
                        }
                        break;
                    case 'set':
                        if (!empty($id)) {
                            $kit_id = bl_get_current_kit_id();
                            if (empty($kit_id)) {
                                bl_save_current_kit($id);
                            }
                            if (!empty($kit_id) && $kit_id != $id) {
                                bl_save_current_kit($id);
                            }
                        }
                        header('Content-Type: application/json');
                        print_r(json_encode(array('success'=>true))); // always return true
                        die;
                        break;
                    default:
                        // returns the kit ID
                        $kit_id = bl_get_current_kit_id();
                        header('Content-Type: application/json');
                        print_r(json_encode(array('id'=>$kit_id)));
                        die;
                        break;
                }
                break;
            default:
                die;
                break;
        }
        die;
    }

} // end bl_ecommerce_url_intercept()
add_action('parse_request', 'bl_ecommerce_url_intercept');
