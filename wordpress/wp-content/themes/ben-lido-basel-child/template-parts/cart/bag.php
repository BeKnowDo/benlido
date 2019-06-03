<?php
global $current_mini_cart_bag;
global $current_mini_cart_index;
$product_id = $current_mini_cart_bag['product_id'];
$variation_id = $current_mini_cart_bag['variation_id'];
$_product = wc_get_product($product_id);

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
    $cart_item = bl_generate_kit_cart_item($current_mini_cart_index,$product_id,$variation_id,$quantity);
}

if ($_product) {
    $product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
    $thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
    $product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
    $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
}
?>
<li class="woocommerce-mini-cart-item">
<?php
    echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
    '<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s"
    data-kit_index="%s" data-variation_id="%s"
    >&times;</a>',
    esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
    __( 'Remove this item', 'woocommerce' ),
    esc_attr( $product_id ),
    esc_attr( $cart_item_key ),
    esc_attr( $current_mini_cart_index),
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
<?php endif;?>
</li>