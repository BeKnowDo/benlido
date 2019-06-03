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

// see how many kits we have
$kits = array();
$has_variations = false;
if (function_exists('bl_get_cart_kits')) {
    $kits = bl_get_cart_kits();
}

if ($product->is_type( 'variable' )) {
    $has_variations = true;
}

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
            <a class="product-title-link" href="<?php echo get_permalink() ?>" >
                <h2><?php echo get_the_title() ?></h2>
            </a>
            <div class="products-price">
                <span class="total-price"><?php echo get_woocommerce_currency_symbol() ?><?= $product->get_price() ?></span>
            </div>
        </div>
        <div class="col-xs-6 description-right-side">
            <div class="cart-button-container">
                <div class="button-wrapper">
                    <a href="/shop/?add-to-cart=<?php echo $product->get_id() ?>" data-quantity="1" id="bag-<?php echo $product->get_id() ?>" class="button product_type_simple add_to_cart_button ajax_add_to_cart p-relative"
                       data-product_id="<?php echo $product->get_id() ?>"
                       data-product_sku="<?php echo $product->get_sku() ?>"
                       data-category_id="<?php echo $product->get_category_ids()[0] ?>"
                       data-variation_id=""
                       data-has_variations="<?php echo $has_variations;?>"
                    >Add to travel kit</a>
                    <?php if (!empty($kits) && count($kits) > 1):?>
                    <div class="choices-container">
                        <ul class="bl-tooltip-menu">
                            
                            <?php foreach ($kits as $index => $kit):?>
                            <?php
                            ?>
                            <li><a href="#" onclick="bl_add_item_to_kit(this);return false" data-index="<?php echo $index;?>"
                            data-product_id="<?= $product->get_id() ?>"
                            data-product_sku="<?= $product->get_sku() ?>"
                            data-category_id="<?= $product->get_category_ids()[0] ?>"
                            data-variation_id=""
                            data-has_variations="<?php echo $has_variations;?>"
                            ><?php echo $kit['kit_name'];?></a></li>
                            <?php endforeach;?>
                            
                        </ul>
                    </div>
                    <?php endif;?>
                </div>
            </div>
            <a class="kit-details" href="<?= get_permalink($product->get_id()) ?>">View details</a>
        </div>
    </div>
</div>
