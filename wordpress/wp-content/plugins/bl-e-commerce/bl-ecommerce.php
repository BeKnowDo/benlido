<?php
/*
Plugin Name: Ben Lido Custom E-Commerce Plugin
Plugin URI: http://www.benlido.com/
Description: provides custom e-commerce functionality for the site
Version: 1.0
*/

// key function:
// bl_get_kit_list(); // gets all the items in the kit (including bag)
// bl_set_kit_list($kit_id,$bag,$items,$kit_name); // sets the kit list

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

function bl_generate_kit_cart_item($index,$product_id,$variation_id,$quantity) {
    // structure is:
    // first, find the product from the kit
    $product_object = null;
    $item = bl_get_item_in_kit($index,$product_id,$variation_id);
    if (!empty($item)) {
        $category_id = $item['category'];
        $data = wc_get_product($product_id);
    }
    $cart_item = array('category'=>$category_id,'key'=>$item_key,'product_id'=>$product_id,'variation_id'=>$variation_id,'variation'=>array(),'quantity'=>$quantity,'data'=>$product_object);
//    print_r ($cart_item);
    return $cart_item;
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
        // lets make sure we're not swapping before we set $is_kit_add to true
        $is_swap = bl_is_swap();
        if ($is_swap == false) {
            // get $is_kit_add as tru because we do not allow for regular purchase of items into the cart
            $is_kit_add = true;
        }
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
     * @param int $current_kit_index this is the index in a number of kits
     * @param int $kit_id the id of the kit
     * @param mixed $bag array('bag'=>int,'variation'=>int)
     * @param mixed $items array of the product/category items: array('category'=>int, 'product'=>int, 'variation'=>int, 'quantity'=>int)
     * @param String $kit_name the name of the kit (for future use)
     */
    function bl_set_kit_list($current_kit_index,$kit_id,$bag,$items,$kit_name='') {
        // let's see if we have this $kit_id
        //error_log('begin set kit list items: ' . json_encode($items));
        $kit_name_index = $current_kit_index + 1;

        $kits = bl_get_cart_kits();
        if (is_array($kits)) {
            $test_kit = $kits[$current_kit_index];
        }
        if (empty($kit_name)) {
            $kit_name = 'Travel Kit ' . $kit_name_index;
        }

        $kits[$current_kit_index] = array('kit_id'=>$kit_id,'kit_name'=>$kit_name,'bag'=>$bag,'items'=>$items);

        //error_log('setting kits: ' . json_encode($kits));
        WC()->session->set_customer_session_cookie(true);
        WC()->session->set( 'bl_kits', $kits);
        WC()->session->set( 'current_kit', $current_kit_index);
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
    function bl_get_kit_list($index=999999) {
        if ($index > 9999) {
            $index = bl_get_active_kit_index();
        }
        $kits = bl_get_cart_kits();
        $current_kit = array();
        if (!empty($kits) && is_array($kits)) {
            $current_kit = $kits[$index];
        }
        //error_log('current kit list: '.json_encode($current_kit));
        // NOTE: sometimes, people add a bag directly into the shopping cart. We will also see if there is a bag in the cart
        if (empty($current_kit['bag'])) {
            $bag = bl_get_bag_from_cart();
            //print_r ($bag);
            if ($bag) {
                $current_kit['bag'] = $bag;
            }
        }

        return $current_kit;
    }
}

if (!function_exists('bl_get_item_in_kit')) {
    function bl_get_item_in_kit($index,$product_id,$variation_id) {
        $item = array();
        $kits = bl_get_cart_kits();
        if (empty($index)) {
            $index = 0;
        }
//        print_r ($kits);
        $kit_list = $kits[$index];
        if (!empty($kit_list) && isset($kit_list['items'])) {
            // look for product
            $items = $kit_list['items'];
            if (!empty($items) && is_array($items)) {
                foreach ($items as $item) {
                    if (!empty($variation_id) && $item['variation'] == $variation_id) {
                        return $item;
                    }
                    if (!empty($product_id) && $item['product'] == $product_id) {
                        return $item;
                    }
                }
            }
        }
        return $item;
    }
}

if (!function_exists('bl_get_cart_kits')) {
    function bl_get_cart_kits() {
        $kits = array();
        if (function_exists('WC')) {
            $kits = WC()->session->get('bl_kits');
        }
        
        if (empty($kits)) {
            $kits = array();
        }
        return $kits;
    }
}

if (!function_exists('bl_get_cart_count')) {
    function bl_get_cart_count() {
        $count = 0;
        $kits = bl_get_cart_kits();
        if (!empty($kits) && is_array($kits)) {
            foreach ($kits as $kit) {
                $bag = $kit['bag'];
                $items = $kit['items'];
                if (!empty($bag) && isset($bag['bag'])) {
                    $count++;
                }
                if (!empty($items) && is_array($items)) {
                    foreach ($items as $item) {
                        $quantity = $item['quantity'];
                        $count += $quantity;
                    }
                }
            }
        }
        return $count;
    }
}

if (!function_exists('bl_get_subtotal')) {
    function bl_get_subtotal() {
        $subtotal = 0.0;
        $kits = bl_get_cart_kits();
        //print_r ($kits);
        if (!empty($kits) && is_array($kits)) {
            foreach ($kits as $kit) {
                $bag = $kit['bag'];
                $items = $kit['items'];
                if (!empty($bag)) {
                    $bag_id = $bag['bag'];
                    $prod = wc_get_product( $bag_id );
                    if (!empty($prod)) {
                        $subtotal += floatval($prod->get_price());
                    }
                }
                if (!empty($items) && is_array($items)) {
                    foreach ($items as $item) {
                        $quantity = $item['quantity'];
                        if (!empty($item['variation']) && is_numeric($item['variation'])) {
                            $prod = wc_get_product($item['variation']);
                        }
                        else if (!empty($item['product']) && is_numeric($item['product'])) {
                            $prod = wc_get_product($item['product']);
                        }

                        if (!empty($prod)) {
                            $subtotal += ($prod->get_price() * $quantity);
                        }
                    }
                }
            }
        }

        return wc_price($subtotal);
    }
}

if (!function_exists('bl_set_product_swap')) {
    function bl_set_product_swap($kit_id,$category_id,$product_id) {
        $kit_id = intval(trim($kit_id));
        $kit_list = bl_get_kit_list();
        if (empty($kit_list)) {
            bl_save_current_kit($kit_id);
        }
        
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
        $index = 0;
        $kits = bl_get_cart_kits();
        if (!empty($kits) && is_array($kits)) {
            $index = count($kits);
        }
        //$kit_list = bl_get_kit_list();
        $bag_obj = bl_get_bag_from_cart();
        if (!empty($bag_obj)) {
            $bag = array('bag'=>$bag_obj['id'],'variation'=>$bag_obj['variation_id']);
        }
        $kit_id = $bl_custom_kit_id;
        bl_set_kit_list($index,$kit_id,$bag,$holder);
        bl_set_kit_add($kit_id,true);
        //wp_redirect(wc_get_page_permalink( 'shop' ));

    }

}

if (!function_exists('bl_get_minicart')) {
    function bl_get_minicart() {
                		// Get messages
		ob_start();

		wc_print_notices();

		$notices = ob_get_clean();


		// Get mini cart
		ob_start();

		woocommerce_mini_cart();

		$mini_cart = ob_get_clean();

		// Fragments and mini cart are returned
		$data = array(
			'notices' => $notices,
			'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array(
					'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>'
				)
			),
			'cart_hash' => apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5( json_encode( WC()->cart->get_cart_for_session() ) ) : '', WC()->cart->get_cart_for_session() )
		);

		return $data;
    }
}

if (!function_exists('bl_create_new_kit')) {
    function bl_create_new_kit() {
        global $bl_custom_kit_id;
        $kits = bl_get_cart_kits();
        $kit_id = $bl_custom_kit_id;
        if (!empty($kits) && is_array($kits)) {
            $index = count($kits);
        }
        if (empty($index)) {
            $index = 0;
        }
        $kit_name = 'Travel Kit ' . ($index + 1);
        error_log('creating new kit: ' . $kit_name . ' -  ' . $index);
        bl_set_kit_list($index,$kit_id,array(),array(),$kit_name);


        		// Get messages
		ob_start();

		wc_print_notices();

		$notices = ob_get_clean();


		// Get mini cart
		ob_start();

		woocommerce_mini_cart();

		$mini_cart = ob_get_clean();

		// Fragments and mini cart are returned
		$data = array(
			'notices' => $notices,
			'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array(
					'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>'
				)
			),
			'cart_hash' => apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5( json_encode( WC()->cart->get_cart_for_session() ) ) : '', WC()->cart->get_cart_for_session() )
		);

		return $data;
    }
}

function bl_rename_kit_name_in_cart($original_name,$new_name) {
    //error_log('renaming from ' . $original_name . ' to ' . $new_name);
    $cart = array();
    if (function_exists('WC')) {
        $cart = WC()->cart->get_cart();
    }
    if (!empty($cart) && is_array($cart)) {
        foreach ($cart as $hash => $item) {
            if ($item['kit_name'] == $original_name) {
                $item['kit_name'] = $new_name;
            }
            WC()->cart->cart_contents[$hash] = $item;
        }

    }
    WC()->cart->set_session();
}

if (!function_exists('bl_rename_kit')) {
    function bl_rename_kit($index,$kit_name) {
        $original_name = '';
        $kit_name = stripslashes(strip_tags($kit_name));
        $kits = bl_get_cart_kits();
        $kit_list = $kits[$index];
        if (!empty($kit_list)) {
            $kit_id = $kit_list['kit_id'];
            $bag = $kit_list['bag'];
            $items = $kit_list['items'];
            $orignal_kit_name = $kit_list['kit_name'];
            if (!empty($kit_name)) {
                $kit_name = $kit_name;
            }
            bl_set_kit_list($index,$kit_id,$bag,$items,$kit_name);
            // we need to rename the items that have this kit name
            bl_rename_kit_name_in_cart($orignal_kit_name,$kit_name);
            
        }
        // Get mini cart
		ob_start();
		woocommerce_mini_cart();
        $mini_cart = ob_get_clean();
        
        // Fragments and mini cart are returned
		$data = array(
			'notices' => $notices,
			'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array(
					'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>'
				)
			),
			'cart_hash' => apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5( json_encode( WC()->cart->get_cart_for_session() ) ) : '', WC()->cart->get_cart_for_session() )
		);

		return $data;
    }
}

if (!function_exists('bl_select_item_as_swapped')) {
    // this is to pick the item to swap with the one that was chosen before
    function bl_select_item_as_swapped($kit_id,$prod_id,$cat_id) {
        $index = bl_get_active_kit_index();
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
            bl_set_kit_list($index,$kit_id,$kit_list['bag'],$holder);
            bl_clear_product_swap();
            return true;
        }

        //print_r ($kit_list);
        return false;
    }
}

if (!function_exists('bl_get_basel_cart')) {
    function bl_get_basel_cart() {
        $final_kits = array();
        // kits
        $kits = bl_get_cart_kits();
        $final_kits = $kits;
        //$cart = WC()->cart->get_cart();

        // theoretically, the number of items in the cart should match up to the number of items in the kits
        return $final_kits;
    }
}

// gets the item from the kit cart from the real cart
// this is so we can get an item key for default mini-cart functions
if (!function_exists('bl_get_kit_item_from_cart')) {
    function bl_get_kit_item_from_cart($product_id,$variation_id) {
        $cart = array();
        if (function_exists('WC')) {
            $cart = WC()->cart->get_cart();
        }
        if (!empty($cart)) {
            if (!empty($cart) && is_array($cart)) {
                foreach ($cart as $hash => $item) {
                    $tmp_variation_id = 0;
                    $tmp_product_id = $item['data']->get_id();
                    $quantity = $item['quantity'];
                    if (isset($item['variation_id'])) {
                        $tmp_variation_id = $item['variation_id'];
                    }
                    if (!empty($variation_id) && $variation_id == $tmp_variation_id) {
                        return array('cart_item_key'=>$hash,'cart_item'=>$item,'quantity'=>$quantity,'is_variable'=>true);
                    }
                    else if (empty($variation_id) && $product_id == $tmp_product_id ) {
                        return array('cart_item_key'=>$hash,'cart_item'=>$item,'quantity'=>$quantity,'is_variable'=>false);
                    }
                    
                }
            }
        }
        return array();
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

                    //die;
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
        //print_r ($holder);
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
        bl_remove_cart_items(); // remove everything in the cart first before adding
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
        // we're going to set a session variable so that we know to fire off add-to-cart Google Analytics events on the cart page
        if (function_exists('bl_show_add_to_cart')) {
            bl_show_add_to_cart();
        }
        wp_redirect(wc_get_cart_url());
        exit;
        //return true;
        
    }
}

if (!function_exists('bl_add_to_cart')) {
    function bl_add_to_cart($index,$product_id,$category_id,$quantity=1,$variation_id=0) {
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
        if (empty($index)) {
            $index = 0;
        }
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
                    bl_remove_from_cart($index,$bag_product_id,$bag_variation_id,$bag_quantity);
                }
            }
        }
        $res = WC()->cart->add_to_cart($product_id,$quantity,$variation_id,array(),$meta);
        return $res;
    }
} // end bl_add_to_cart()

if (!function_exists('bl_remove_from_cart')) {
    function bl_remove_from_cart($index,$product_id,$variation_id=0,$quantity=1,$cart_hash='') {
        // NOTE: sometimes, the product_id is the same as variation ID because some products returns the variation_id as the product_id as well.
        //       so, we need to work from trying to get 
        $removed_item = array();

        // first, remove from the kit
        if (function_exists('bl_remove_from_kit')) {
            $kit_id = '';
            // we need to get the removed item because we need to see the quantity
            $removed_item = bl_remove_from_kit($index,$kit_id,$product_id,$category_id,$quantity);
        }
        $response = false;
        $cart = WC()->cart->get_cart();
        $holder = array();
        $match_key = '';
        $match_quantity = 0;

        if (!empty($removed_item) && !empty($cart) && is_array($cart)) {
            $product_id = $removed_item['product'];
            $variation_id = $removed_item['variation'];
            if (!empty($removed_item['quantity'])) {
                $item_quantity = $removed_item['quantity'];
            } else {
                $item_quantity = $quantity;
            }
            foreach ($cart as $hash => $item) {
                $temp_product_id = $item['product_id'];
                $temp_variation_id = $item['variation_id'];
                $temp_quantity = $item['quantity'];
                
                if ($product_id == $temp_product_id && $product_id != $variation_id) {
                    $match_key = $hash;
                    $match_quantity = $temp_quantity;
                    $quantity = $item_quantity;
                }
                if ($variation_id > 0 && $variation_id == $temp_variation_id) {
                    $match_key = $hash;
                    $match_quantity = $temp_quantity;
                    $quantity = $item_quantity;
                }
                // maybe product_id is the variation ID
                if ($temp_variation_id == $product_id && $variation_id == 0) {
                    $match_key = $hash;
                    $match_quantity = $temp_quantity;
                    $quantity = $item_quantity;
                }
                
            }
        }
        if (!empty($match_key)) {
            // we're just going to remove that whole line for now
            
            $final_quantity = intval($match_quantity) - $quantity;
            if ($final_quantity > 0) {
                WC()->cart->set_quantity($match_key, $final_quantity);
            } else {
                WC()->cart->remove_cart_item($match_key);
            }
            
        }

        $response = array();
        if (function_exists('bl_get_minicart')) {
            $response = bl_get_minicart();
        }
        
        return $response;
    }
}

// removes a recommendation
function bl_remove_recommendation($cat_id) {
    if( function_exists('WC')) {
        $current_removal = WC()->session->get('removed_recos');
    }
    if (empty($current_removal)) {
        $current_removal = array();
    }
    if (!in_array($cat_id,$current_removal)) {
        $current_removal[] = $cat_id;
    }
    //print_r ($current_removal);
    if( function_exists('WC')) {
        WC()->session->set_customer_session_cookie(true);
        WC()->session->set( 'removed_recos', $current_removal);
    }
    return true;
}

// check the cat_id against saved session info like removed recos or items added to kit
function bl_check_cat_against_session($cat_id) {
    if( function_exists('WC')) {
        $current_removal = WC()->session->get('removed_recos');
    }
    //print_r ($current_removal);
    if (!empty($current_removal) && is_array($current_removal)) {
        if (in_array($cat_id,$current_removal)) {
            return true;
        }
    }
    $kit_list = bl_get_kit_list();
    //print_r ($kit_list);
    if (!empty($kit_list) && is_array($kit_list) && !empty($kit_list['items'])) {
        $items = $kit_list['items'];
        foreach ($items as $item) {
            //print_r ($item);
            $item_cat_id = $item['category'];
            if ($item_cat_id == $cat_id) {
                return true;
            }
        }
    }
    return false;
} // end bl_check_cat_against_session()

// this is to get the recommendations
function bl_get_kit_recommendations($kit_id=0) {
    $result = array();
    $categories = array();
    $is_svg = '';
    $image_is_svg = '';
    if (function_exists('get_field')) {
        $build_your_own_title = get_field('build_your_own_title','option');
        $build_your_own_description = get_field('build_your_own_description','option');
        $build_your_own_icon = get_field('build_your_own_icon','option');
        $build_your_own_image = get_field('build_your_own_image','option');
        $build_your_own_id = get_field('build_your_own_id','option');
        $build_your_own_categories = get_field('build_your_own_categories','option');
        $kitting_page = get_field('kitting_page','option');

    }
    if (!empty($build_your_own_icon) && is_array($build_your_own_icon)) {
        $mimetype = $build_your_own_icon['mime_type'];
        //print_r ($mimetype);
        if (strlen(stristr($mimetype,'svg'))> 0) {
            $is_svg = 'svg';
        }
        $build_your_own_icon = $build_your_own_icon['url'];
    }
    if (!empty($build_your_own_image) && is_array($build_your_own_image)) {
        $mimetype = $build_your_own_image['mime_type'];
        //print_r ($mimetype);
        if (strlen(stristr($mimetype,'svg'))> 0) {
            $image_is_svg = 'svg';
        }
        $build_your_own_image = $build_your_own_image['url'];
    }

    if (!empty($build_your_own_categories) && is_array($build_your_own_categories)) {
        foreach ($build_your_own_categories as $cat) {
            //print_r ($cat);
            $cat_id = $cat['category'];
            $icon = $cat['icon'];
            $cat_name = '';
            $icon_is_svg = '';
            if (!empty($cat_id) && is_numeric($cat_id)) {
                $term = get_term($cat['category']);
                if (!empty($term) && is_object($term)) {
                    $cat_name = $term->name;
                }
            }
            if (!empty($icon) && is_array($icon)) {
                $mimetype = $icon['mime_type'];
                //print_r ($mimetype);
                if (strlen(stristr($mimetype,'svg'))> 0) {
                    $icon_is_svg = 'svg';
                }
                $icon = $icon['url'];
            }
            if ($cat_id) {
                $href = get_term_link($cat_id);
            }
            $should_remove = bl_check_cat_against_session($cat_id);
            if ($should_remove != true) {
                $categories[] = array('kit_id'=>$kit_id,'cat_id'=>$cat_id,'cat_name'=>$cat_name,'image'=>$icon,'image_css'=>$icon_is_svg, 'href'=>$href);
            }
            
            
        }
    }
    
    if (!empty($kitting_page)) {
        $href = get_permalink($kitting_page) . '?id=' . $build_your_own_id;
    }

    $result = array(
        'is_kit' => true,
        'css' => 'prebuilt-kit',
        'header' => $build_your_own_title,
        'copy' => $build_your_own_description,
        'href' => $href,
        'image' => $build_your_own_image,
        'image_css' => $image_is_svg,
        'lifestyle_icon' => $build_your_own_icon,
        'lifestyle_icon_css' => $is_svg,
        'categories' => $categories,
    );

    return $result;
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
        // just in case someone added a bag from regular add products
        foreach ($items as $item) {
            // see if it's a bag
            $id = $item['id'];
            $variation_id = $item['variation_id'];
            if ($id == $variation_id) {
                $id = wp_get_post_parent_id( $id );
            }
            $cats = wp_get_post_terms($id,'product_cat');
            if (!empty($cats) && is_array($cats)) {
                //print_r ($cats);
                foreach ($cats as $cat) {
                    //print_r ($cat);
                    if ($cat->term_id == $bag_category) {
                        //echo "MATCH!!!!";
                        $item['category_id'] = $bag_category;
                        return $item;
                    }
                }
            }
        }

    }
    
//die;
    return array();
}

function bl_get_active_kit_index() {
    $index = WC()->session->get( 'current_kit' );
    if (is_numeric($index)) {
        return $index;
    }
    else {
        return 0;
    }
}

function bl_set_active_kit_index($index) {
    if (empty($index)) {
        $index = 0;
    }
    if (is_numeric($index)) {
        WC()->session->set('current_kit',$index);
    }
    
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
    $bypass_kit_check = false;
    global $bl_custom_kit_id;
    if ($bl_custom_kit_id == $id) {
        $bypass_kit_check = true;
    }
    if (function_exists('get_field')) {
        $build_your_own_id = get_field('build_your_own_id','option');
    }
    if ($build_your_own_id == $id) {
        $bypass_kit_check = true;
    }
    $index = bl_get_active_kit_index();
    // let's see if it's a build your own or
    if (get_post_type($id) == 'travel_kit' || $bypass_kit_check == true) {
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
        bl_set_kit_list($index,$id,$bag,$items);
        if (function_exists('bl_remove_cart_items')) {
            bl_remove_cart_items();
        }   
        
    } // if travel kit

} // bl_save_current_kit()

// this function updates the actuall woocommerce cart with the item
function bl_update_cart($current_kit,$product_id,$category_id,$variation_id,$quantity,$is_bag=false) {

}

// this function adds the item to the kit and also to the cart
// NOTE: should only be used by bl_add_to_kit_cart()
function bl_add_to_kit($index,$kit_id,$product_id,$category_id,$quantity=1) {
    // array('kit_id'=>$kit_id,'bag'=>$bag,'items'=>$items);
    global $bl_custom_kit_id;
    $kits = bl_get_cart_kits();
    //$index = bl_get_active_kit_index();
    if (empty($index)) {
        $index = 0;
    }
    $kit_list = $kits[$index];
    $kit_name = $kit_list['kit_name'];
    $bags_product_category = 0;
    $is_bag = false;
    if (!empty($kit_list)) {
        $current_kit_id = $kit_list['kit_id'];
    } else {
        $current_kit_id = $bl_custom_kit_id;
    }

    // see if it's a bag
    if (function_exists('get_field')) {
        $bags_product_category = get_field('bags_product_category','option');
    }

    // see if this is a bag
    if ($bags_product_category > 0 && $bags_product_category == $category_id) {
        // see if we have an existing bag
        $is_bag = true;
        // now, let's replace the bag
        $kit_list['bag'] = array('bag'=>$product_id,'variation'=>$product_id);
    }

    $holder = array();
    $added = false;

    $items = $kit_list['items'];
    if (!empty($items) && is_array($items)) {
        
        foreach ($items as $item) {
            // we're just going to use the existing category
            if ($item['product'] == $product_id && $is_bag == false) {
                $item['quantity']++;
                $added = true;
            }
            if ($is_bag == true) {
                $added = true;
            }
            $holder[] = $item;
        }
    }

    if ($added == false && !empty($holder) && $is_bag == false) {
        array_unshift($holder,array('category'=>$category_id,'product'=>$product_id,'variation'=>null,'quantity'=>1));
    }
    else if (empty($holder) && $is_bag == false) {
        $holder[] = array('category'=>$category_id,'product'=>$product_id,'variation'=>null,'quantity'=>1);
    }

    if (!empty($holder) || $is_bag == true) {
        //print_r ($holder);
        error_log('adding item: index:' . $index . 'holder:' . json_encode($holder) . ' bag:' . json_encode($kit_list['bag']) );
        bl_set_kit_list($index,$kit_id,$kit_list['bag'],$holder,$kit_name);
        return true;
    }
    return false;
}

// 
function bl_add_to_kit_cart($product_id,$quantity,$category_id=0,$index=0) {
    global $bl_custom_kit_id;
    $kitting_page = '';

    if (empty($index) && $index !== 0) {
        $index = bl_get_active_kit_index();
    }
    if (function_exists('get_field')) {
        $kitting_page = get_field('kitting_page','option');
    }
    if (!empty($kitting_page) && is_object($kitting_page)) {
        $kitting_page = get_permalink($kitting_page->ID);
    }
    $result = array('success'=>false,'message'=>'error','href'=>$kitting_page);
    // let's see if we have a kit
    $kit_id = bl_get_current_kit_id();
    //echo $current_kit_id;
    //echo $product_id;
    //echo $quantity;
    if (empty($kit_id)) {
        $kit_id = $bl_custom_kit_id;
    }
    // we need to get the main category from this product
    if ($category_id < 1) {
        $cats = get_the_terms( $product_id, 'product_cat' );
        if (!empty($cats) && is_array($cats)) {
            $category = $cats[0];
        }
        if (!empty($category) && is_object($category) && isset($category->term_id)) {
            $category_id = $category->term_id;
        }
    }

    //print_r ($cats);
    $success = bl_add_to_kit($index,$kit_id,$product_id,$category_id);
    if ($success == true) {
        $result['success'] = true;
        if ($kit_id != $bl_custom_kit_id) {
            $kitting_page = $kitting_page . '?id=' . $kit_id;
        }
        $result['href'] = $kitting_page;
    }
    return $result;
}

// removes the item from the kit
function bl_remove_from_kit($index,$kit_id,$product_id,$category_id,$quantity=1) {
    // first see if we have a kit
    // see if the product is a bag
    $removed_item = array();
    $is_bag = bl_check_if_bag($product_id);
    $has_category_id = true;
    $items_holder = array();
    // The right kit index is parameter $index, we should not overwrite it
    //$index = bl_get_active_kit_index();
    if ( empty($product_id)) {
        return false;
    }
    if (empty($category_id)) {
        $has_category_id = false;
    }
    // setting has_category_id to false for now
    $has_category_id = false;

    $kits = bl_get_cart_kits();



    
    // first, match kit ID
    //print_r ($kits);
    if (!empty($kits)) {
        $selected_kit = $kits[$index];
        if (empty($selected_kit)) {
            return false;
        }

        // we will remove the bag from the kit
        if ($is_bag == true) {

        }

        // now, let's see which to remove
        $items = $selected_kit['items'];
        if (!empty($items) && is_array($items)) {
            foreach ($items as $item) {
                if ($has_category_id == true) {
                    if ($item['category'] == $category_id && $item['product'] == $product_id) {
                        // this is how we remove
                        // we might want to do something else here.
                        $removed_item = $item;
                    } else {
                        $items_holder[] = $item;
                    }
                } else {
                    if ($item['product'] == $product_id) {
                        // this is how we remove
                        // we might want to do something else here.
                        $removed_item = $item;
                    } else {
                        $items_holder[] = $item;
                    }
                }
                
            }
        }
        bl_set_kit_list($index,$kit_list['kit_id'],$kit_list['bag'],$items_holder);
        //$kit_list = bl_get_kit_list();
        //print_r ($kit_list);
    }
    return $removed_item;
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

// sets the name of the saved kit
function bl_set_order_kit_name($order_id,$name) {
    $user_id = get_current_user_id();
    $kits = array();
    if ($user_id > 0 && function_exists('get_field')) {
        $recurring_orders = get_field('recurring_orders','user_'.$user_id);
    }

    if (is_array($recurring_orders)) {
        foreach ($recurring_orders as $recurring_order) {
            if ($recurring_order['order_id'] == $order_id) {
                $recurring_order['recurring_name'] = $name;
            }
            $kits[] = $recurring_order;
        }
        update_field('recurring_orders',$kits,'user_'.$user_id);
    }
    return true;
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

// start the process of adding the bag
function bl_start_add_bag_to_kit($index) {
    $bags_url = '';
    $resp = array();
    if (function_exists('get_field')) {
        $bags_category_slug = get_field('bags_product_category','option');
        if (!empty($bags_category_slug)) {
            $bags_category = get_term_by( 'id', $bags_category_slug, 'product_cat' );
            $bags_category_link = get_term_link( $bags_category->term_id, 'product_cat' );
            if (!is_object($bags_category_link)) {
                $bags_url = $bags_category_link . '?is_adding=1';
            }   
            
            $resp['redirect_url'] = $bags_url;
        }
    }
    bl_set_active_kit_index($index);
    return $resp;

}

function bl_change_bag($index,$product_id,$category_id,$variation_id,$personal_kit_id=0) {
    $kit = bl_get_kit_list();
    if (empty($index)) {
        $index = bl_get_active_kit_index();
    }
    
    $kit_bag = $kit['bag'];
    $kit_product_id = 0; // NOTE: kit product ID is currently the same as variation id
    $kit_variation_id = 0; 
    if ($kit_bag['bag'] == $kit_bag['variation'] && $kit_bag['variation'] > 0) {
        $kit_variation_id = $kit_bag['variation'];
    }
    // first, remove the item from the cart
    if ($variation_id != $kit_variation_id) {

        bl_remove_from_cart($index,$kit_variation_id,$kit_variation_id,1);
        bl_add_to_cart($product_id,$category_id,1,$variation_id);

        $kit_bag = array(
            'bag' => $variation_id,
            'variation' => $variation_id
        );
        $kit_id = $kit['kit_id'];
        $items = $kit['items'];
        $kit_name = $kit['kit_name'];
        if (!empty($kit_id)) {
            bl_set_kit_list($index,$kit_id,$kit_bag,$items,$kit_name);
        }
        

    }
}

// see if the product is a bag
function bl_check_if_bag($product_id) {
    // first, let's see if this is a variation
    // let's get the taxonomy for this product
    $bag_category = 0;
    $type = get_post_type($product_id);
    if ($type == 'product_variation') {
        $product_id = wp_get_post_parent_id( $product_id );
    }
    $categories = wp_get_object_terms($product_id,'product_cat',array('fields'=>'ids'));

    if (function_exists('get_field')) {
        $bag_category = get_field('bag_category','option');
    }
    if (!empty($categories) && is_array($categories)) {
        if (in_array($bag_category,$categories)) {
            return true;
        }
    }
    return false;
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
                        // /bl-api/product/get/{product_id}/{variation_id}
                        if (isset($api_parts[5])) {
                            $variation_id = $api_parts[5];
                        }
                        $prod = bl_get_product_data($id,$variation_id);
                        header('Content-Type: application/json');
                        print_r (json_encode($prod));
                        die;
                        break;
                    default:
                        header('Content-Type: application/json');
                        print_r (json_encode(array()));
                        die;
                        break;
                }
                break;
            case 'bag':
                switch ($action) {
                    case 'swap':
                        if (isset($api_parts[5])) {
                            $category_id = $api_parts[5];
                        }
                        if (isset($api_parts[6])) {
                            $variation_id = $api_parts[6];
                        }
                        $personal_kit_id = 0;
                        $res = bl_change_bag($id,$category_id,$variation_id,$personal_kit_id);
                        die;
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
                        $index = $_REQUEST['index'];
                        if (empty($index)) {
                            $index = $_POST['index'];
                        }
                        if (empty($index)) {
                            $index = 0;
                        }
                        if (isset($api_parts[5])) {
                            $variation_id = $api_parts[5];
                        }
                        if (isset($api_parts[6])) {
                            $quantity = $api_parts[6];
                        }
                        $res = bl_remove_from_cart($index,$id,$variation_id,$quantity);
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
                    case 'start-add-bag':
                        // this starts the "add bag to kit" process
                        // we need to set the kit index so we know which bad to add to. then, we'll need to redirect them to the bag page, and then allow them to add the bag
                        $index = $_POST['index'];
                        if (empty($index)) {
                            $index = 0;
                        }
                        $resp = bl_start_add_bag_to_kit($index);
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
                    case 'kit-add':
                        //  /bl-api/kit/kit-add/{product_id}/{quantity}
                        // this is to replace the add to cart button so that it always tries to find a kit to add it to.
                        // if there is no kit, it will create a custom kit with no bag.
                        if (isset($api_parts[5])) {
                            $qty = $api_parts[5];
                        }
                        $resp = bl_add_to_kit_cart($id,$qty);
                        header('Content-Type: application/json');
                        print_r (json_encode($resp));
                        die;
                        break;
                    case 'create':
                            if (!empty($_POST['kit'])) {
                                if (function_exists('bl_create_new_kit')) {
                                    $data = bl_create_new_kit();
                                }
                            }
                            header('Content-Type: application/json');
                            print_r (json_encode($data));
                            die;
                        break;
                    case 'reco-remove':
                        // /bl-api/kit/reco-remove/{cat_id}/
                        if (!empty($id) && is_numeric($id)) {
                            $resp = bl_remove_recommendation($id);
                        }
                        break;
                    case 'remove':
                        // removing an item from a kit
                        // should have 3 IDs: Kit ID, product ID, category ID
                        // /bl-api/kit/remove/{kit_id}/{product_id}/{category_id}
                        $success = false;
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
                            $removed_item = bl_remove_from_kit($index,$id,$prod,$cat);
                            if (!empty($removed_item)) {
                                $success = true;
                            }
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
                    case 'rename':
                        // /bl-api/kit/rename/
                        $index = $_POST['index'];
                        $kit_name = strip_tags($_POST['kit_name']);
                        $response = array();
                        if (!empty($kit_name)) {
                            if (empty($index)) {
                                $index = 0;
                            }
                            $response = bl_rename_kit($index,$kit_name);
                        }
                        header('Content-Type: application/json');
                        print_r(json_encode($response));
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
            case 'order':
                switch ($action) {
                    case 'kit-name':
                        if (!empty($id)) {
                            // just get the number
                            $real_id = 0;
                            $id_parts = explode('id-',$id);
                            if (is_array($id_parts)) {
                                $real_id = $id_parts[1];
                                
                            }
                            if (is_numeric($real_id) && $real_id > 0) {
                                $inputJSON = file_get_contents('php://input');
                                $input = json_decode($inputJSON, TRUE); //convert JSON into array
                                if (is_array($input) && isset($input['name'])) {
                                    $name = strip_tags($input['name']);
                                }
                                bl_set_order_kit_name($real_id,$name);
                            }

                        }
                        header('Content-Type: application/json');
                            $result = array('success'=>true,'parent'=>'delivery-frequency-tile-main-'.$real_id);
                            print_r (json_encode($result));
                        die;
                        break;
                    default:
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
