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

<a name="bags"></a>
<div id="store-item-container">
    <div class="grid-container">
        <?php if (!empty($bags) && is_array($bags)):?>
        <?php $loop = 0 ?>
        <?php foreach ($bags as $bag):?>
            <?php
                $bag_product = $bag['product'];
                if (!empty($bag_product) && isset($bag_product->ID)) {
                    $product_id = $bag_product->ID;
                    $bag_product = wc_get_product( $product_id);
                    $add_to_cart_url = $kitting_page . '?add-to-cart=' . $product_id;
                    $product_name = $bag_product->get_name();
                    $product_image =  wp_get_attachment_image_src( get_post_thumbnail_id($product_id),'full');
                    if (!empty($product_image) && is_array($product_image)) {
                        $product_image = $product_image[0];
                    }
                }
                //print_r ($bag_product);
                $product_image = $product_image_retina = '';
                $image_override = $bag['image_override'];
                $image_override_retina = $bag['image_override_retina'];
                if (!empty($image_override) && isset($image_override['url'])) {
                    $product_image = $image_override['url'];
                }
                if (!empty($image_override_retina) && isset($image_override_retina['url'])) {
                    $product_image_retina = $image_override_retina['url'];
                }
            ?>
            <?php if (!empty($bag_product)):?>
                <?php if($loop%2 ==0): ?><div class="row"><?php endif;?>
                    <div class="col-sm-6 bag-item">
                        <div class="card">
                            <div class="card-body">
                                <div class="bag-details-link"><a href="#">Details</a></div>
                                <div class="bag-image hd-8 center">
                                    <img src="<?php echo $product_image;?>" srcset="<?php if (!empty($product_image_retina)) { echo $product_image_retina . ' 2x ';} ?>" alt="<?php echo esc_attr($product_name);?>">
                                </div>
                                <div class="bag-content">
                                    <h1><?php echo $product_name;?></h1>
                                    <?php echo apply_filters( 'the_content', $bag_product->get_description() );?>
                                </div>
                            </div>

                            <div class="card-footer">
                                <span class="price">$<?php echo money_format('%!n', $bag_product->get_price()); ?></span>
                                <span class="brown">Free</span> shipping and % off products with purchase
                                <div class="right">
                                    <div class="bag-colors">
                                        <a href="#"><i class="fa fa-circle brown"></i></a>
                                        <a href="#"><i class="fa fa-circle blue"></i></a>
                                    </div>
                                    <a href="<?php echo $add_to_cart_url;?>" class="right"><i class="fa fa-plus-circle brown"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php $loop++;?>
                <?php if($loop%2 ==0): ?></div><?php endif;?>
            <?php endif;?>
        <?php endforeach;?>
        <?php endif;?>
    </div>
</div>
