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

function bl_add_to_cart_hook($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
	// using bl_add_to_kit_cart()
	if (function_exists('bl_add_to_kit_cart')) {
		// first, let's see if there is a category passed:
		$category_id = 0;
		if (isset($_POST['category_id'])) {
			$category_id = $_POST['category_id'];
		}
		bl_add_to_kit_cart($product_id,$quantity,$category_id);
	}
}