<?php
if ( ! function_exists( 'woocommerce_template_loop_add_to_cart' ) ) {

	/**
	 * Get the add to cart template for the loop.
	 *
	 * @param array $args Arguments.
	 */
	function woocommerce_template_loop_add_to_cart( $args = array() ) {
		global $product;

		if ( $product ) {

            // get a default category ID for this product
            $category_id = 0;
            $category = null;
            if (function_exists('bl_get_product_category')) {
                $category = bl_get_product_category($product->get_id());
            }

            if (!empty($category)) {
                $category_id = $category->term_id;
            }

			$defaults = array(
				'quantity'   => 1,
				'class'      => implode(
					' ',
					array_filter(
						array(
							'button',
							'product_type_' . $product->get_type(),
							$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
							$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
						)
					)
				),
				'attributes' => array(
					'data-product_id'  => $product->get_id(),
                    'data-product_sku' => $product->get_sku(),
                    'data-category_id' => $category_id,
					'aria-label'       => $product->add_to_cart_description(),
					'rel'              => 'nofollow',
				),
			);

			$args = apply_filters( 'woocommerce_loop_add_to_cart_args', wp_parse_args( $args, $defaults ), $product );

			if ( isset( $args['attributes']['aria-label'] ) ) {
				$args['attributes']['aria-label'] = wp_strip_all_tags( $args['attributes']['aria-label'] );
			}

			wc_get_template( 'loop/add-to-cart.php', $args );
		}
	}
}

if ( ! function_exists( 'woocommerce_mini_cart' ) ) {

	/**
	 * Output the Mini-cart - used by cart widget.
	 *
	 * @param array $args Arguments.
	 */
	function woocommerce_mini_cart( $args = array() ) {

		$defaults = array(
			'list_class' => '',
		);
		add_action('woocommerce_widget_shopping_cart_buttons','bl_add_add_kit_button',99);
		$args = wp_parse_args( $args, $defaults );

		wc_get_template( 'cart/mini-cart.php', $args, '', get_stylesheet_directory().'/woocommerce/' );
	}
}

function bl_add_to_cart_hook($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
	// using bl_add_to_kit_cart()
	if (function_exists('bl_add_to_kit_cart')) {
		// first, let's see if there is a category passed:
		$category_id = 0;
		$index = bl_get_active_kit_index();
		if (isset($_POST['category_id'])) {
			$category_id = $_POST['category_id'];
		}
		if (isset($_POST['variation_id'])) {
		    $product_id = $_POST['variation_id'];
		}
		if (isset($_POST['index'])) {
			$index = $_POST['index']; // TODO: make this the latest kit index
		}
		bl_add_to_kit_cart($product_id,$quantity,$category_id,$index);
	}
}

// this function is to replace the woocommerce ajax add to cart because it doesn't allow for custom meta for items
function bl_replace_ajax_add_to_cart() {

	ob_start();
	// this adds the hook to add meta info to the cart items
	if (function_exists('bl_add_to_cart_hook')) {
		add_action('woocommerce_add_to_cart', 'bl_add_to_cart_hook',99,6);
	}

	// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
	if ( ! isset( $_POST['product_id'] ) ) {
		return;
	}

	$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
	$product           = wc_get_product( $product_id );
	$quantity          = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_POST['quantity'] ) );
	$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
	$product_status    = get_post_status( $product_id );
	$variation_id      = 0;
	$variation         = array();

	if ( $product && 'variation' === $product->get_type() ) {
		$variation_id = $product_id;
		$product_id   = $product->get_parent_id();
		$variation    = $product->get_variation_attributes();
	}

	// for $meta, we just want the kit name
	$index = $_POST['index'];
	$category_id = $_POST['category_id'];
	$kit = array();
	if (empty($index)) {
		$index = 0;
	}
	if (function_exists('bl_get_cart_kits')) {
		$kits = bl_get_cart_kits();
	}
	// maybe this is the first time we're adding something
	$kit_name = 'Travel Kit 1';
	if (empty($kit)) {
		global $bl_custom_kit_id;
		bl_set_kit_list($index,$bl_custom_kit_id,array(),array(),$kit_name);
	}
	if (!empty($kit)) {
		$kit_name = $kit['kit_name'];
	}

	if (empty($kit_name)) {
		$kit_name = 'Travel Kit 1';
	}

	// check to see if it's a bag
	// see if it's a bag
    if (function_exists('get_field')) {
        $bags_product_category = get_field('bags_product_category','option');
	}
	if ($category_id == $bags_product_category) {
		// we will remove the existing bag and add this one.
		$cart = WC()->cart->get_cart();
		if (!empty($cart) && is_array($cart)) {
			foreach ($cart as $hash => $item) {
				if ($item['kit_name'] == $kit_name && $item['category_id'] == $category_id) {
					WC()->cart->remove_cart_item($hash);
				}
			}
		}
	}

	$meta = array(
		'category_id' => $category_id,
		'kit_name' => $kit_name
	);

	if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation, $meta ) && 'publish' === $product_status ) {

		do_action( 'woocommerce_ajax_added_to_cart', $product_id );

		if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
			wc_add_to_cart_message( array( $product_id => $quantity ), true );
		}

		// send mini cart
		$data = bl_get_minicart();
		wp_send_json($data);

	} else {

		// If there was an error adding to the cart, redirect to the product page to show any errors.
		$data = array(
			'error'       => true,
			'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
		);

		wp_send_json( $data );
	}
	// phpcs:enable

}

function bl_display_kit_name_cart( $item_data, $cart_item ) {
 
    $item_data[] = array(
        'key'     => 'Kit Name',
        'value'   => wc_clean( $cart_item['kit_name'] ),
        'display' => '',
    );
 
    return $item_data;
}
 


// define the woocommerce_cart_subtotal callback 
function bl_woocommerce_cart_subtotal( $subtotal, $compound, $cart )  { 
	// Need to get the subtotal from all the kits
	if (function_exists('bl_get_subtotal')) {
		$subtotal = bl_get_subtotal();
	}
	return $subtotal; 
}; 
             

function bl_ajax_remove_from_cart() {
	$cart_item_key = wc_clean( isset( $_POST['cart_item_key'] ) ? wp_unslash( $_POST['cart_item_key'] ) : '' );
	$kit_index = wc_clean( isset( $_POST['kit_index'] ) ? wp_unslash( $_POST['kit_index'] ) : '0' );
	$product_id = wc_clean( isset( $_POST['product_id'] ) ? wp_unslash( $_POST['product_id'] ) : '0' );
	$variation_id = wc_clean( isset( $_POST['variation_id'] ) ? wp_unslash( $_POST['variation_id'] ) : '0' );
	$quantity = wc_clean( isset( $_POST['quantity'] ) ? wp_unslash( $_POST['quantity'] ) : '1' );
	// first, remove item from kit
	if (empty($kit_index)) {
		$kit_index = 0;
	}
	if (function_exists('bl_remove_from_cart')) {
		 $response = bl_remove_from_cart($kit_index,$product_id,$variation_id,$quantity,$cart_item_key);
	}
	wp_send_json( $response );
}
