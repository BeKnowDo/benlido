<?php
    
    $kit_id = null;
    $categories = array();
    $kitting_page = '';
    $overrides = array();
    setlocale(LC_MONETARY, 'en_US');
    if (function_exists('bl_get_category_overrides')) {
        $overrides = bl_get_category_overrides();
    }

    if (function_exists('get_field')) {
        $selected_kit = get_field('selected_kit');
        $kitting_page = get_field('kitting_page','option');
        $category_page = get_field('category_page','option');
    }
    if (!empty($category_page) && is_object($category_page)) {
        $category_page = get_permalink($category_page->ID);
    }
    if (!empty($kitting_page) && is_object($kitting_page)) {
        $kitting_page = get_permalink($kitting_page->ID);
    }
    if (!empty($selected_kit) && is_object($selected_kit)) {
        //print_r ($selected_kit);
        $kit_id = $selected_kit->ID;
        $categories = get_field('product_categories',$kit_id);
    }

?>
<form method="post" id="add-items-to-kit-form" action="<?php echo $kitting_page ;?>">
<ul id="store-item-container">
    <a name="items"></a>
    <?php if (!empty($categories) && is_array($categories)):?>
    <?php foreach ($categories as $category):?>
    <?php
        //print_r ($category);
        $product_image = null;
        $product = null;
        $category_id = $category['category'];
        $maximum_number_of_items_in_this_category = $category['maximum_number_of_items_in_this_category'];
        $featured_product = $category['featured_product'];
        $product_price = '$0.00';
        $category_name = '';
        $category_url = '';
        if (!empty($featured_product) && is_object($featured_product)) {
            $product_id = $featured_product->ID;
            $product = wc_get_product( $product_id);
            $product_image =  wp_get_attachment_image_src( get_post_thumbnail_id($product_id),'full');
        }
        if (!empty($overrides) && !empty($overrides[$category_id])) {
            $product_id = $overrides[$category_id];
            $product = wc_get_product( $product_id);
            $product_image =  wp_get_attachment_image_src( get_post_thumbnail_id($product_id),'full');
        }

        if (!empty($product_image) && is_array($product_image)) {
            $product_image = $product_image[0];
        }

        if (!empty($product)) {
            $product_name = $product->get_name();
            $product_description = $product->get_description();
            $product_price = money_format('%!n', $product->get_price()); 
        }
        if (!empty($category_id)) {
            $cat_obj = get_term_by('id',$category_id,'product_cat');
            //$category_url = get_term_link( $category_id, 'product_cat' );
            $category_url = $category_page . '?id=' . $category_id;
        }
        if (!empty($cat_obj) && is_object($cat_obj)) {
            $category_name = $cat_obj->name;
        }
        //print_r ($cat_obj);
        
    ?>  
    <li class="product">
        <input type="hidden" name="bl_category[]" value="<?php echo $category_id;?>" />
        <input type="hidden" name="bl_product[]" value="<?php echo $product_id;?>" />
        <div class="grid-container">
            <div class="row no-margin reverse">
                <div class="product-buttons column hd-3 no-margin-right">
                    <a href="" class="product-button middle left">
                        <img src="<?php echo get_stylesheet_directory_uri();?>/assets/images/icon-remove.svg" alt="">
                        <span>Remove</span>
                    </a>
                    <a href="<?php echo $category_url;?>" title="<?php echo esc_attr($category_url);?>" class="product-button middle right">
                        <img src="<?php echo get_stylesheet_directory_uri();?>/assets/images/icon-edit.svg" alt="">
                        <span>Edit</span>
                    </a>
                </div>
                <div class="product-image column hd-6">
                    <img src="<?php echo $product_image;?>" srcset="" alt="">
                </div>
                <div class="product-details-container column hd-3 no-margin-left">
                    <div class="product-details">
                        <div class="product-category">
                            <?php echo $category_name;?>
                        </div>
                        <div class="product-name">
                            <?php echo $product_name;?>
                        </div>
                        <div class="product-cost">
                            $<?php echo $product_price;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </li>
    <?php endforeach;?>
    <?php endif;?>
</ul>
</form>
    