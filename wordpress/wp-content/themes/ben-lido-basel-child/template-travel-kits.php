<?php

/* Template name: Travel Kits */

get_header();

?>
<div class="shop-loop-head">
    <?php
    basel_current_breadcrumbs( 'shop' );
    ?>
</div>
<div class="page-title-section">
    <h1><?= get_the_title() ?></h1>
</div>
<div class="site-content" role="main">
    <div class="travel-kits-container">
        <div class="row travel-kits-grid">
            <?php
            global $post;
            $args = array(
                'post_type' => 'travel_kit',
            );
            $travel_kits = new WP_Query($args);

            if ($travel_kits->have_posts()):
                while ($travel_kits->have_posts()):
                    $travel_kits->the_post();
                    ?>
                    <div class="col-xs-12 col-sm-6 product-grid-single <?= (($travel_kits->current_post +1) == ($travel_kits->post_count)?'last_kit':'') ?>">
                        <a class="kit-image-link" href="<?= get_permalink() ?>" ><?= get_the_post_thumbnail() ?></a>
                        <div class="row">
                            <div class="col-xs-6 description-left-side">
                                <span class="kit-info">Travel Pack for Man</span>
                                <h2><?= get_the_title() ?></h2>
                                <div class="products-price">
                                    <span class="total-price"><?= get_woocommerce_currency_symbol() ?><?php if (function_exists('bl_get_kit_price')) echo bl_get_kit_price($post->ID); ?> </span>
                                    <span class="total-products">
                                <?php if (function_exists('bl_get_current_kit_items')) {
                                    $kit_products = bl_get_current_kit_items($kit_id);
                                    echo count($kit_products). ' Products';
                                } ?>
                            </span>
                                </div>

                            </div>
                            <div class="col-xs-6 description-right-side">
                                <div class="cart-button-container">
                                    <a href="#" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="<?= $post->ID ?>" rel="nofollow">
                                        Add to cart
                                    </a>
                                </div>
                                <a class="kit-details" href="<?= get_permalink($post->ID) ?>">View details</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile;
                wp_reset_postdata();
                ?>
                <div class="col-xs-12 col-sm-6 product-grid-single create-kit">
                    <?php if (function_exists('bl_create_your_own_kit_button')) {
                        bl_create_your_own_kit_button();
                    } ?>
                </div>
            <?php else: ?>
                <p>No travel kits found</p>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php get_footer(); ?>
