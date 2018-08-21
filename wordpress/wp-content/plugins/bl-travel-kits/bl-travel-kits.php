<?php
/*
Plugin Name: Ben Lido Travel Kits
Plugin URI: http://www.benlido.com/
Description: The Travel Kit kitting admin
Version: 1.0
*/
if ( ! function_exists('benlido_kits') ) {

// Register Custom Post Type
function benlido_kits() {

	$labels = array(
		'name'                  => _x( 'Travel Kits', 'Post Type General Name', 'benlido' ),
		'singular_name'         => _x( 'Travel Kit', 'Post Type Singular Name', 'benlido' ),
		'menu_name'             => __( 'Travel Kits', 'benlido' ),
		'name_admin_bar'        => __( 'Travel Kit', 'benlido' ),
		'archives'              => __( 'Travel Kit Archives', 'benlido' ),
		'attributes'            => __( 'Item Attributes', 'benlido' ),
		'parent_item_colon'     => __( 'Parent Item:', 'benlido' ),
		'all_items'             => __( 'All Items', 'benlido' ),
		'add_new_item'          => __( 'Add New Item', 'benlido' ),
		'add_new'               => __( 'Add New', 'benlido' ),
		'new_item'              => __( 'New Item', 'benlido' ),
		'edit_item'             => __( 'Edit Item', 'benlido' ),
		'update_item'           => __( 'Update Item', 'benlido' ),
		'view_item'             => __( 'View Item', 'benlido' ),
		'view_items'            => __( 'View Items', 'benlido' ),
		'search_items'          => __( 'Search Item', 'benlido' ),
		'not_found'             => __( 'Not found', 'benlido' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'benlido' ),
		'featured_image'        => __( 'Featured Image', 'benlido' ),
		'set_featured_image'    => __( 'Set featured image', 'benlido' ),
		'remove_featured_image' => __( 'Remove featured image', 'benlido' ),
		'use_featured_image'    => __( 'Use as featured image', 'benlido' ),
		'insert_into_item'      => __( 'Insert into item', 'benlido' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'benlido' ),
		'items_list'            => __( 'Items list', 'benlido' ),
		'items_list_navigation' => __( 'Items list navigation', 'benlido' ),
		'filter_items_list'     => __( 'Filter items list', 'benlido' ),
	);
	$args = array(
		'label'                 => __( 'Travel Kit', 'benlido' ),
		'description'           => __( 'Travel Kit Description', 'benlido' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes', 'post-formats' ),
		'taxonomies'            => array( 'product_cat', 'product_tag' ),
		'hierarchical'          => true,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-products',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
		'show_in_rest'          => true,
	);
	register_post_type( 'travel_kit', $args );

}
add_action( 'init', 'benlido_kits', 0 );

}

function bl_list_kits() {
	$args = array(

	);
}

function bl_get_kit_bag($id) {
	$bag = null;
	if (function_exists('get_field')) {
		$bag = get_field('bag_for_this_kit',$id);
	}
	if (is_object($bag)) {
		$bag = wc_get_product($bag);
	}
	return $bag;
}

function bl_get_kit_items($id) {
	$items = array();
	if (function_exists('get_field')) {
		$items = get_field('product_categories',$id);
	}
	return $items;
}

function bl_override_category_default($cat_id,$product_id) {
	$session = array();
	$session = WC()->session->get( 'bl_kitting_overrides' );
	if (empty($session)) {
		$session = array();
	}
	$session[$cat_id] = $product_id;
	WC()->session->set('bl_kitting_overrides' , $session );
}

function bl_get_category_overrides() {
	$session = WC()->session->get( 'bl_kitting_overrides' );
	return $session;
}

function bl_get_kit_price($id) {
	$price = 0;
	if (!is_numeric($id) || empty($id)) {
		return $price;
	}
	$key = 'bl-kit-price-total-' . $id;

	// get cache
	if (function_exists('upco_get_cache')) {
		//$price = upco_get_cache($key);
	}
	if (!empty($price)) {
		return $price;
	}
	// get the price of the whole kit
	if (function_exists('get_field')) {
		$items = get_field('product_categories',$id);
	}
	if (!empty($items) && is_array($items)) {
		foreach ($items as $item) {
			$prod = $item['featured_product'];
			if (!empty($prod) && is_object($prod)) {
				$prod_id = $prod->ID;
				$product = wc_get_product( $prod_id );
			}
			if (!empty($product)) {
				$price += $product->get_price();
			}
		} // end foreach
		// also need to add the bag price
		if (function_exists('get_field')) {
			$bag_for_this_kit = get_field('bag_for_this_kit',$id);
		}
		if (!empty($bag_for_this_kit) && is_object($bag_for_this_kit)) {
			$bag = wc_get_product($bag_for_this_kit->ID);
			if (!empty($bag) && is_object($bag)) {
				$bag_price = $bag->get_price();
			}
			if (!empty($bag_price) && is_numeric($bag_price)) {
				$price += $bag_price;
			}
		}
		// need to subtract discounts
		if (function_exists('get_field')) {
			$coupon_for_this_kit = get_field('coupon_for_this_kit',$id);
		}
		if (!empty($coupon_for_this_kit)) {
			//print_r ($coupon_for_this_kit);
			$coupon_amount = get_post_meta('coupon_amount',$coupon_for_this_kit->ID,true);
			if (is_numeric($coupon_amount)) {
				$price = $price - floatval($coupon_amount);
			}
		}
		if (function_exists('upco_set_cache')) {
			$group = upco_cache_group();
			upco_set_cache($key,$price,$group,60*60*24); // 24 hour cache
		}
	}
	return $price;
} // end bl_get_kit_price()
