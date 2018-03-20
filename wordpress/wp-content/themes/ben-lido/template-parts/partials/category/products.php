<?php
    $products = array();
    if (function_exists('get_field')) {
        $kitting_page = get_field('kitting_page','option');
    }
    if (!empty($kitting_page) && is_object($kitting_page)) {
        $kitting_page = get_permalink($kitting_page->ID);
    } 
    $category = $_REQUEST['id'];
    if (!empty($category) && is_numeric($category)) {
        $return_url = $kitting_page . '?cat=' . $category;
        $args = array(
            'post_type'             => 'product',
            'post_status'           => 'publish',
            'ignore_sticky_posts'   => 1,
            'posts_per_page'        => -1,
            'tax_query'             => array(
                array(
                    'taxonomy'      => 'product_cat',
                    'field' => 'term_id', //This is optional, as it defaults to 'term_id'
                    'terms'         => $category,
                    'operator'      => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
                ),
                array(
                    'taxonomy'      => 'product_visibility',
                    'field'         => 'slug',
                    'terms'         => 'exclude-from-catalog', // Possibly 'exclude-from-search' too
                    'operator'      => 'NOT IN'
                )
            )
        );
        $products = get_posts($args);
    }

?>
<div class="category-container">
    <div class="grid-container">
        <ul class="category-items row wrap no-margin">
            <?php if (!empty($products) && is_array($products)):?>
            <?php foreach ($products as $product):?>
            <?php
            //print_r ($product);
                $prod = new WC_Product($product->ID);
                $product_url = get_permalink($product->ID);
                $image = wp_get_attachment_image_src(get_post_thumbnail_id($product->ID),'medium');
                $image_full = wp_get_attachment_image_src(get_post_thumbnail_id($product->ID),'full');
                if (!empty($image)) {
                    $image = $image[0];
                    $image_full = $image_full[0];
                }
            ?>
            <li class="item column hd-3">

                    <div class="item-details" data-url="<?php echo $product_url;?>">
                        <div class="item-image">
                            <img src="<?php echo $image;?>" srcset="<?php echo $image_full;?> 2x" alt="">
                        </div>
                        <div class="item-info">
                            <p><?php echo $product->post_title;?></p>
                        </div>
                        <div class="item-buttom">
                        <a href="<?php echo $return_url . '&prod=' . $product->ID;?>" class="generic-button center"><span class="separator">Add | $<?php echo money_format('%!n', $prod->get_price()); ?></span></a>
                        </div>
                    </div>

            </li>
            <?php endforeach;?>
            <?php endif;?>
        </ul>
    </div>
</div>