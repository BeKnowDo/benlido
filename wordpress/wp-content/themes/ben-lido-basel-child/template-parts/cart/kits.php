<?php
// we need to have this in an li because the main mini-cart.php has a ul in it.
$cart = array();
if (function_exists('bl_get_basel_cart')) {
    $cart = bl_get_basel_cart();
}

?>
<li>
    <div class="widget bl-kits-container">
    <?php //print_r ($cart);?>
    <?php foreach ($cart as $index => $kit) :?>
    <!-- kit -->
    <?php
        //print_r ($kit);
        $kit_name = $kit['kit_name'];
        $bag = $kit['bag'];
    ?>
    <div class="mini-cart-kit-container" data-id="<?php echo $index;?>">
        <div class="kit-header">
            <div class="main-title">
            <h3>
                <span class="display-title">    
                    <?php echo stripslashes($kit_name);?> 
                    <a href="#" data-index="<?php echo $index;?>" class="kit-rename" onclick="bl_rename_kit_start(event,this);return false;">(rename it)</a>
                </span>
                <span class="edit-title">
                    <input type="text" name="kit_title" class="kit-title" value="<?php echo esc_attr($kit_name);?>" />
                    <a href="#" data-index="<?php echo $index;?>" class="rename-link" onclick="bl_rename_kit(event,this);return false;">update</a>
                    <a href="#" data-index="<?php echo $index;?>" class="cancel-link" onclick="bl_rename_cancel(event,this);return false;">cancel</a>
                </span>
            </h3>
            <a href="#" data-index="<?php echo $index;?>" class="kit-hide-link">Hide</a>
            </div>
            <div class="bag-area">
                <?php if (!empty($bag)):?>
                
                <?php
                $bag_product_id = $bag['bag'];
                $bag_variation_id = $bag['variation'];
                $cart_match = array();
                
                if (function_exists('bl_get_kit_item_from_cart')) {
                    $cart_match = bl_get_kit_item_from_cart($bag_product_id,$bag_variation_id);
                }
                if (!empty($cart_match)) {
                    $cart_item = $cart_match['cart_item'];
                    $cart_item_key = $cart_match['cart_item_key'];
                }
                // NOTE: we still need to reconstruct cart_item for displaying the product
                if (empty($cart_item) && function_exists('bl_generate_kit_cart_item')) {
                    $cart_item = bl_generate_kit_cart_item($index,$bag_product_id,$bag_variation_id,1);
                }
                $_bag = wc_get_product($bag_product_id);

                if ($_bag) {
                    $bag_product_name      = apply_filters( 'woocommerce_cart_item_name', $_bag->get_name(), $cart_item, $cart_item_key );
                    $bag_thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_bag->get_image(), $cart_item, $cart_item_key );
                    $bag_product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_bag ), $cart_item, $cart_item_key );
                    $bag_product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_bag->is_visible() ? $_bag->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                }

                ?>
                <ul>
                <li class="woocommerce-mini-cart-item">
                <?php
                    echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
                    '<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s"
                    data-kit_index="%s" data-variation_id="%s"
                    >&times;</a>',
                    esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                    __( 'Remove this item', 'woocommerce' ),
                    esc_attr( $bag_product_id ),
                    esc_attr( $cart_item_key ),
                    esc_attr( $index),
                    esc_attr($bag_variation_id)
                ), $cart_item_key );
                ?>
                <?php if ( empty( $bag_product_permalink ) ) : ?>
                    <?php echo $bag_thumbnail . $bag_product_name; ?>
                <?php else : ?>
                    <a href="<?php echo esc_url( $bag_product_permalink ); ?>">
                        <?php echo $bag_thumbnail . $bag_product_name; ?>
                    </a>
                <?php endif; ?>
                <?php if (!empty($cart_item['data'])):?>
                    <?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
                    <?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', 1, $bag_product_price ) . '</span>', $cart_item, $cart_item_key ); ?>
                <?php endif;?>
                </li>
                </ul>


                <?php else: ?>
                    <a class="button add-bag-button" href="#" data-index="<?php echo $index;?>" onclick="bl_start_add_bag(event,this);return false;">+ add a bag</a>
                <?php endif;?>
            </div>
        </div>
        <div class="kit-contents">
            <?php if (!empty($kit) && is_array($kit)):?>
                <?php $items = $kit['items'];?>
                <?php if (!empty($items) && is_array($items)):?>
                    <ul>
                    <?php foreach ($items as $item):?>
                        <?php
                    if (!empty($item['product'])):
                            $product_id = $item['product'];
                            $variation_id = $item['variation'];
                            $quantity = $item['quantity'];
                            $category = $item['category'];
                            $cart_match = array();
                            if (function_exists('bl_get_kit_item_from_cart')) {
                                $cart_match = bl_get_kit_item_from_cart($product_id,$variation_id);
                            }
                            if (!empty($cart_match)) {
                                $cart_item = $cart_match['cart_item'];
                                $cart_item_key = $cart_match['cart_item_key'];
                            }
                            // NOTE: we still need to reconstruct cart_item for displaying the product
                            if (empty($cart_item) && function_exists('bl_generate_kit_cart_item')) {
                                $cart_item = bl_generate_kit_cart_item($index,$product_id,$variation_id,$quantity);
                            }

                            $_product = wc_get_product($product_id);
                            if ($_product) {
                                $product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
                                $thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
                                $product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
                                $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                            }
                        ?>
                        <li class="woocommerce-mini-cart-item <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">
                        <?php
						    echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
                            '<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s"
                            data-kit_index="%s" data-variation_id="%s"
                            >&times;</a>',
							esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
							__( 'Remove this item', 'woocommerce' ),
							esc_attr( $product_id ),
							esc_attr( $cart_item_key ),
                            esc_attr( $_product->get_sku() ),
                            esc_attr( $index),
                            esc_attr($variation_id)
						), $cart_item_key );
						?>
						<?php if ( empty( $product_permalink ) ) : ?>
							<?php echo $thumbnail . $product_name; ?>
						<?php else : ?>
							<a href="<?php echo esc_url( $product_permalink ); ?>">
								<?php echo $thumbnail . $product_name; ?>
							</a>
						<?php endif; ?>
                        <?php if (!empty($cart_item['data'])):?>
                            <?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
                            <?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $quantity, $product_price ) . '</span>', $cart_item, $cart_item_key ); ?>
                            <a href="#" class="bl-swap-item" data-category_id="<?php echo $category;?>" data-product_id="<?php echo $product_id;?>" data-variation_id="<?php echo $variation_id;?>" data-index="<?php echo $index;?>" onclick="bl_swap_product(this);return false;">Swap <i class="fa fa-refresh" aria-hidden="true"></i></a>
                        <?php endif;?>
                        </li>
                    <?php endif; ?>
                    <?php endforeach;?>
                    </ul>
                <?php endif;?>
            <?php endif;?>
        </div>
    </div>
    <?php endforeach;?>
    </div>
</li>

<script type="text/javascript">
jQuery('document').ready(function($) {
    $('.bl-kits-container .mini-cart-kit-container').accordion({
        collapsible: true,
        heightStyle: 'content'
    });
    $(".ui-accordion [role=tab]").unbind('keydown');
    $('.mini-cart-kit-container .main-title input').on('click', function(e) {
        e.stopPropagation();
    });
});
</script>
