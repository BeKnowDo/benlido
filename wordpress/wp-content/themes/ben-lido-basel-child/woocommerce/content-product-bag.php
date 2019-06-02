<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

?>
<div class="col-xs-12 col-sm-6 product-grid-single ">
    <div class="row">
        <div class="col-xs-10 bag-image">
            <a class="bag-image-link" href="<?= get_permalink() ?>" ><?= get_the_post_thumbnail() ?></a>
        </div>
        <div class="col-xs-2 bag-colors">
            <?php bl_list_bag_color_variation($product); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6 description-left-side">
            <span class="kit-info"><?php bl_list_product_brands($product->get_id()); ?></span>
            <a class="product-title-link" href="<?= get_permalink() ?>" >
                <h2><?= get_the_title() ?></h2>
            </a>
            <div class="products-price">
                <span class="total-price"><?= get_woocommerce_currency_symbol() ?><?= $product->get_price() ?></span>
            </div>
        </div>
        <div class="col-xs-6 description-right-side">
            <div class="cart-button-container">
                <div class="button-wrapper">
                    <a href="/shop/?add-to-cart=<?= $product->get_id() ?>" data-quantity="1" id="bag-<?= $product->get_id() ?>" class="button product_type_simple add_to_cart_button ajax_add_to_cart p-relative"
                       data-product_id="<?= $product->get_id() ?>"
                       data-product_sku="<?= $product->get_sku() ?>"
                       data-category_id="<?= $product->get_category_ids()[0] ?>"
                       data-variation_id=""
                    >Add to travel kit</a>
                    <div class="choices-container">
                        <ul class="bl-tooltip-menu">
                            <li><a href="#">Travel Kit 1</a></li>
                            <li><a href="#">Travel Kit 2</a></li>
                            <li><a href="#">Travel Kit 3</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <a class="kit-details" href="<?= get_permalink($product->get_id()) ?>">View details</a>
        </div>
    </div>
</div>
