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

    ?>
    <div class="mini-cart-kit-container" data-id="<?php echo $index;?>">
        <div class="kit-header">
            <h3><?php echo $kit_name;?> <a href="#" data-index="<?php echo $index;?>" class="kit-rename">(rename it)</a></h3>
            <a href="#" data-index="<?php echo $index;?>" class="kit-hide-link">Hide</a>
        </div>
        <div class="kit-contents">
            <?php if (!empty($kit) && is_array($kit)):?>
                <?php $items = $kit['items'];?>
                <?php if (!empty($items) && is_array($items)):?>
                    <ul>
                    <?php foreach ($items as $item):?>
                        <?php
                            //print_r ($item);
                            $product_id = $item['product'];
                            $variation_id = $item['variation'];
                            $cart_match = array();
                            if (function_exists('bl_get_kit_item_from_cart')) {
                                $cart_match = bl_get_kit_item_from_cart($product_id,$variation_id);
                                //print_r ($cart_match);
                            }
                            if (!empty($cart_match)) {
                                $cart_item = $cart_match['cart_item'];
                                $cart_item_key = $cart_match['cart_item_key'];
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
							'<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">&times;</a>',
							esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
							__( 'Remove this item', 'woocommerce' ),
							esc_attr( $product_id ),
							esc_attr( $cart_item_key ),
							esc_attr( $_product->get_sku() )
						), $cart_item_key );
						?>
						<?php if ( empty( $product_permalink ) ) : ?>
							<?php echo $thumbnail . $product_name; ?>
						<?php else : ?>
							<a href="<?php echo esc_url( $product_permalink ); ?>">
								<?php echo $thumbnail . $product_name; ?>
							</a>
						<?php endif; ?>
						<?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
						<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); ?>
                        </li>
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
        collapsible: true
    });
});
</script>