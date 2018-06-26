<?php
/*
Plugin Name: Ben Lido Custom E-Commerce Plugin
Plugin URI: http://www.benlido.com/
Description: provides custom e-commerce functionality for the site
Version: 1.0
*/

global $bl_ecommerce_api_slug;
$bl_ecommerce_api_slug = '/bl-api';

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
add_action( 'wp_loaded', 'bl_add_frequency_to_session', 100);

// we will always have a category with the product added
function bl_add_cart_item_category( $cart_item_data, $product_id, $variation_id ) {
    $category_id = filter_input( INPUT_POST, 'category_id' );
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

add_filter( 'woocommerce_get_checkout_url', 'bl_process_checkout_url', 50 );

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

if (!function_exists('bl_get_kit_list')) {
    function bl_get_kit_list() {
        $current_kit = WC()->session->get( 'current_kit' );
        return $current_kit;
    }
}

if (!function_exists('bl_set_product_swap')) {
    function bl_set_product_swap($kit_id,$category_id,$product_id) {
        $swapping = array('kit_id'=>$kit_id,'category_id'=>$category_id,'product_id'=>$product_id);
        WC()->session->set_customer_session_cookie(true);
        WC()->session->set( 'current_product_swap', $swapping);
    }
}

if (!function_exists('bl_get_product_swap')) {
    function bl_get_product_swap() {
        $current_product_swap = WC()->session->get( 'current_product_swap' );
        return $current_product_swap;
    }
}

if (!function_exists(bl_get_cart)) {
    function bl_get_cart() {
        // this gets the shopping cart and displays in a format that the front end likes
        $cart = WC()->cart->get_cart();
        $holder = array();
        if (!empty($cart) && is_array($cart)) {
            foreach ($cart as $hash => $item) {
                // just need sku, category, name, count, and image
                if (isset($item['data'])) {
                    //print_r ($item['data']);
                    $id = $item['data']->get_id();
                    $name = $item['data']->get_title();
                    $sku = $item['data']->get_sku();
                    //$image = $item['data']->get_image();
                    $image_obj = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'single-post-thumbnail' );
                    if (!empty($image_obj) && is_array($image_obj)) {
                        $image = $image_obj[0];
                    }
                    $category_ids = $item['data']->get_category_ids();
                    $quantity = $item['quantity'];
                    if (is_array($category_ids) && !empty($category_ids)) {
                        $cat = get_term($category_ids[0]);
                        if (!empty($cat) && isset($cat->name)) {
                            $category_name = $cat->name;
                        }
                    }
                }
                if (isset($item['meta_data']) && !empty($item['meta_data'])) {
                    $category = $item['meta_data'];
                }
                if (empty($category) && !empty($category_name)) {
                    $category = $category_name;
                }
                $holder[] = array('sku'=>$sku,'category'=>$category,'name'=>$name,'count'=>$quantity,'image'=>$image);
            }
        }
        return $holder;
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
// some supporting functions
// gets the current session kit ID
function bl_get_current_kit_id() {
    $kit_id = 0;
    $kit_list = bl_get_kit_list();
    if (!empty($kit_list)) {
        $kit_id = $kit_list['kit_id'];
    }
    return $kit_id;
}

function bl_save_current_kit($id) {
    // first, get the products of the kit
    if (get_post_type($id) == 'travel_kit') {
        $bag = null;
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
    } // if travel kit

} // bl_save_current_kit()

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
            case 'cart':
                switch ($action) {
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
                    case 'set':
                        if (!empty($id)) {
                            $kit_id = bl_get_current_kit_id();
                            if (empty($kit_id)) {
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
