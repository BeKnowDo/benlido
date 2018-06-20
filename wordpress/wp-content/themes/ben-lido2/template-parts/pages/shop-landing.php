<?php
global $shop_landing_featured_product;
global $shop_landing_category;

$featured_categories = array();
if (function_exists('bl_get_featured_categories')) {
  $featured_categories = bl_get_featured_categories();
}
?>
<?php if (!empty($featured_categories) && is_array($featured_categories)):?>
<div >

          <?php foreach ($featured_categories as $featured_cat): ?>
          <?php
            $category_name = $featured_cat['name'];
            $category_id = $featured_cat['id'];
            $category_href = $featured_cat['href'];
            $featured_products = $featured_cat['featured'];
          ?>
            <div class="columns">
              <h3 class="column col-12 shop-landing-featured-header" id="category-<?php echo $category_id;?>"><?php echo $category_name;?></h3>
                <ul class="columns">
                <?php foreach ($featured_products as $featured_product):?>
                    <?php
                      //print_r ($featured_product);
                      global $product;
                      if ($featured_product['id']) {
                        $product = wc_get_product( $featured_product['id'] );
                      }
                      global $product_override;
                      $product_override = $featured_product;
                    ?>
                    <?php $shop_landing_featured_product = $featured_product;?>
                    <?php //get_template_part('template-parts/product/product','tile');?>
                    <?php wc_get_template_part( 'content', 'product' );?>
                <?php endforeach;?>
                </ul>
            </div>

            <div class="columns shop-landing-featured-view-all">
              <div class="column col-mx-auto text-center">
                <a href="<?php echo $category_href;?>" class="btn btn-lg">View all
                  <?php echo $category_name;?>
                </a>
              </div>
            </div>

          <?php endforeach;?>

</div>
<?php endif;?>