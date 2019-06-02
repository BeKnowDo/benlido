<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// replaced WC()->cart->is_empty()
// so that we can check our BL cart and not WooCommerce Cart
$is_empty = WC()->cart->is_empty();
$subtotal = WC()->cart->get_cart_subtotal();
$kits = array();
if (function_exists('bl_get_cart_kits')) {
	$kits = bl_get_cart_kits();
	if (!empty($kits)) {
		$is_empty = false;
	}
}
//print_r ($kits);

if (function_exists('bl_get_subtotal')) {
	$subtotal = bl_get_subtotal();
}


do_action( 'woocommerce_before_mini_cart' ); ?>

<?php if ( ! $is_empty ) : ?>
	<ul class="woocommerce-mini-cart cart_list product_list_widget <?php echo esc_attr( $args['list_class'] ); ?>">
		<?php
			do_action( 'woocommerce_before_mini_cart_contents' );

            // this is where we have our custom cart
			get_template_part('template-parts/cart/kits');
			
			do_action( 'woocommerce_mini_cart_contents' );
		?>
	</ul>

	<p class="woocommerce-mini-cart__total total"><strong><?php _e( 'Subtotal', 'woocommerce' ); ?>:</strong> <?php echo $subtotal;  ?></p>

	<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

	<p class="woocommerce-mini-cart__buttons buttons"><?php do_action( 'woocommerce_widget_shopping_cart_buttons' ); ?></p>

<?php else : ?>

	<p class="woocommerce-mini-cart__empty-message"><?php _e( 'No products in the cart.', 'woocommerce' ); ?></p>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
