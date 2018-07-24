<?php 
// see if we have a kit id
global $kit_id;

if (empty($kit_id) && !empty($_REQUEST['id'])) {
    $kit_id = $_REQUEST['id'];
}

get_template_part( 'template-parts/common/step','navigation'); 

if (function_exists('bl_get_current_kit_items')) {
    $kit_products = bl_get_current_kit_items($kit_id);
    $product_count = count($kit_products);
}
?>

<input type="hidden" id="bl_kit_id" name="bl_kit_id" value="<?php echo $kit_id;?>" />

<div class="bg-gray">
    <div class="max-width-xl kitting-page">
        <?php
            get_template_part( 'template-parts/common/hero/hero-product','list');
        ?>
    </div>
</div>

<div class="bg-white">
  <div class="max-width-xl kitting-page">
      
      <?php
          get_template_part( 'template-parts/common/category','header');
      ?>
      
      <div class="columns">
          
          <?php if (!empty($kit_products) && is_array($kit_products)):?>

          <div class="column col-xs-12 col-sm-6 col-md-6 col-3 product-tile-column">
            <?php get_template_part('template-parts/common/product/add-empty','product'); ?>
          </div>
                    
          <?php foreach ($kit_products as $kit_product):?>
            
            <?php
                global $product;
                global $product_override;
                $featured_product = $kit_product['featured_product'];
                $product_override = $kit_product;
                
                if (!empty($featured_product) && is_object($featured_product)) {
                    $product = wc_get_product($featured_product->ID);
                }  
                
            ?>
      
            <div class="column col-xs-12 col-sm-6 col-md-6 col-3 product-tile-column">
              <ul class="product">
                <?php wc_get_template_part( 'content', 'product' );?>
              </ul>
            </div>
        
          <?php endforeach;?>
          
          <?php endif;?>

      </div>
  </div>
</div>