<?php
    if (function_exists('get_field')) {
        $bags = get_field('selectable_bags');
        $kitting_page = get_field('kitting_page','option');
        if (is_object($kitting_page)) {
            $kitting_page = get_permalink($kitting_page->ID);
        }
    }
    setlocale(LC_MONETARY, 'en_US');
?>
<ul id="store-item-container">
    <?php if (!empty($bags) && is_array($bags)):?>
    <?php foreach ($bags as $bag):?>
        <?php
            $bag_product = $bag['product'];
            if (!empty($bag_product) && isset($bag_product->ID)) {
                $product_id = $bag_product->ID;
                $bag_product = wc_get_product( $product_id);
                $add_to_cart_url = $kitting_page . '?add-to-cart=' . $product_id;
            }
            //print_r ($bag_product);

            $image_override = $bag['image_override'];
            $image_override_retina = $bag['image_override_retina'];
        ?>
        <?php if (!empty($bag_product)):?>
        <li class="bag">
            <div class="grid-container">
                <div class="bag-image hd-8 center">
                    <img src="/_images/image-bag-option1.png" srcset="/_images/image-bag-option1@2x.png 2x" alt="">
                    <img src="<?php echo $hero_image['url'];?>" srcset="<?php if (!empty($hero_image_retina)) { echo $hero_image_retina['url'] . ' 2x ';} ?>" alt="<?php echo esc_attr($hero_image['title']);?>">
                </div>
                <div class="bag-content">
                    <h1><?php echo $bag_product->get_name();?></h1>
                    <?php echo apply_filters( 'the_content', $bag_product->get_description() );?>
                </div>
                <div class="bag-colors"></div>
                <a href="<?php echo $add_to_cart_url;?>" class="generic-button center"><span class="separator">Add | $<?php echo money_format('%!n', $bag_product->get_price()); ?></span></a>
            </div>
        </li>
        <?php endif;?>
    <?php endforeach;?>
    <?php endif;?>
</ul>