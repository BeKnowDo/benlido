<?php
// this file contains all the overrides that we needed for styling the default product, product detail, and category displays

function bl_product_display_overrides() {
    // this is to recreate the product block for shop landing, category landing, etc pages.
    add_action('woocommerce_before_shop_loop_item','bl_loop_product_enclosure',1); // main product enclosure
    add_action('woocommerce_before_shop_loop_item','bl_loop_product_top',5); // the top 1/3 of the product: category or product name,
    add_action('woocommerce_before_shop_loop_item_title','bl_loop_product_detail_span',10);
    add_action('woocommerce_before_shop_loop_item_title','woocommerce_template_loop_product_link_close',20);
    add_action('woocommerce_before_shop_loop_item_title','bl_loop_product_top_close',999);
    add_action('woocommerce_before_shop_loop_item_title','bl_loop_product_offers',999);

    add_action('woocommerce_after_shop_loop_item_title','bl_loop_product_offers_close',1000);
    add_action('woocommerce_shop_loop_item_title','woocommerce_template_loop_price',9);

    remove_action('woocommerce_after_shop_loop_item_title','woocommerce_template_loop_price',10);
    remove_action('woocommerce_after_shop_loop_item','woocommerce_template_loop_product_link_close',5);
}

add_action( 'wp', 'bl_product_display_overrides' );
add_filter( 'post_class', 'bl_product_loop_classes', 100, 3 );

function bl_loop_product_enclosure() {
    ?>
    <div class="product-tile bg-white">
    <?php
} // end bl_loop_product_enclosure()

function bl_loop_product_enclosure_close() {
    ?>
    </div>
    <?php
} // end bl_loop_product_enclosure_close()

function bl_loop_product_detail_span() {
    ?>
    <span class="product-tile-hover">VIEW DETAILS</span>
    <?php
} // end bl_loop_product_detail_span()

function bl_loop_product_top() {
    // the beginning of the product loop
    global $product;
    global $product_override;
    
    // get the category header
    $category = '';
    if (!is_product_category()) {
        $override_id = 0;
        if (isset($product_override['id'])) {
            $override_id = $product_override['id'];
        }
        if (isset($product_override['featured_product'])) {
            $override_prod = $product_override['featured_product'];
            if ($override_prod) {
                $override_id = $override_prod->ID;
            }
            
        }
        
        if ($product->get_id() == $override_id) {
            if (isset($product_override['categoryTitle'])) {
                $category = $product_override['categoryTitle'];
            }

            if (empty($category) && isset($product_override['category'])) {
                $cat_id = $product_override['category'];
                if (is_numeric($cat_id) && $cat_id > 0) {
                    $category = get_term($cat_id);
                    if (!empty($category) && is_object($category) && isset($category->name)) {
                        $category = $category->name;
                    }
                }
            }
        }
    }
    ?>
    <div class="text-center">
    <h3 class="product-tile-header"><?php echo $category;?></h3>
    <?php
} // end bl_loop_product_top()

function bl_loop_product_top_close() {
    ?>
    </div>
    <?php
} // end bl_woocommerce_after_shop_loop_item()

function bl_loop_product_offers() {
    ?>
    <div class="offers">
    <?php
} // end bl_loop_product_offers()

function bl_loop_product_offers_close() {
    ?>
    </div>
    <?php
} // end bl_loop_product_offers_close()



function bl_product_loop_classes($classes, $class, $post_id) {
    global $woocommerce_loop;
    if (get_post_type($post_id) == 'product') {
        if (is_shop() || is_product_category()) {
            $product_loop = array('column', 'col-xs-12', 'col-sm-12', 'col-md-6', 'col-4', 'product-tile-column');
            $classes = array_merge($classes,$product_loop);
        }
        if (is_product() && $woocommerce_loop['name'] == 'related' ) {
            $product_loop = array('column', 'col-4', 'col-xs-11', 'col-sm-10', 'col-md-4', 'col-mx-auto');
            $classes = array_merge($classes,$product_loop);
        }
    }

    return $classes;
} // end bl_product_loop_classes()

if ( ! function_exists( 'woocommerce_template_loop_product_title' ) ) {

	/**
	 * Show the product title in the product loop. By default this is an H2.
	 */
	function woocommerce_template_loop_product_title() {
        global $product;
		echo '<h2 class="woocommerce-loop-product__title">' . $product->get_name() . '</h2>';
	}
}


if ( ! function_exists( 'woocommerce_template_loop_product_link_open' ) ) {
    /**
     * Insert the opening anchor tag for products in the loop.
     */
    function woocommerce_template_loop_product_link_open() {
        global $product;
        if (is_object($product)) {
            $link = apply_filters( 'woocommerce_loop_product_link', get_permalink($product->get_id()), $product );
        }
        echo '<a href="' . esc_url( $link ) . '" class="product-tile-link woocommerce-LoopProduct-link woocommerce-loop-product__link">';
    }
}

if ( ! function_exists( 'woocommerce_get_product_thumbnail' ) ) {

	/**
	 * Get the product thumbnail, or the placeholder if not set.
	 *
	 * @param string $size (default: 'woocommerce_thumbnail').
	 * @param int    $deprecated1 Deprecated since WooCommerce 2.0 (default: 0).
	 * @param int    $deprecated2 Deprecated since WooCommerce 2.0 (default: 0).
	 * @return string
	 */
	function woocommerce_get_product_thumbnail( $size = 'woocommerce_thumbnail', $deprecated1 = 0, $deprecated2 = 0 ) {
		global $product;

        $image_size = apply_filters( 'single_product_archive_thumbnail_size', $size );
        $attr = array('class'=>'product-tile-image');

		return $product ? $product->get_image( $image_size, $attr ) : '';
	}
}