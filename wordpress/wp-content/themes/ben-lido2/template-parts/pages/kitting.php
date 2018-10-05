<?php
// see if we have a kit id
global $kit_id;
global $bl_custom_kit_id;
$build_your_own_id = 0;
$show_reco = false;

if (function_exists('get_field')) {
    $build_your_own_id = get_field('build_your_own_id','option');
}
$recommendations = array();
if (empty($kit_id) && !empty($_REQUEST['id'])) {
    $kit_id = $_REQUEST['id'];
}

get_template_part( 'template-parts/common/step','navigation');

if (function_exists('bl_get_current_kit_items')) {
    $kit_products = bl_get_current_kit_items($kit_id);
    $product_count = count($kit_products);
}

if ($kit_id == $build_your_own_id) {
    $show_reco = true;
}

if ($kit_id == $bl_custom_kit_id) {
    $show_reco = true;
}

if ($show_reco == true && function_exists('bl_get_kit_recommendations')) {
    $reco = bl_get_kit_recommendations($kit_id);
    if (!empty($reco) && isset($reco['categories'])) {
        $recommendations = $reco['categories'];
    }
}
?>

<?php get_template_part('template-parts/common/hero/hero-title','copy');?>

<input type="hidden" id="bl_kit_id" name="bl_kit_id" value="<?php echo $kit_id;?>" />

<div class="bg-white">
  <div class="max-width-xl kitting-page">

      <?php
          get_template_part( 'template-parts/common/category','header');
      ?>

      <div class="columns">

        <div class="column col-xs-6 col-sm-6 col-md-6 col-3 product-tile-column product-tile-empty-product">
            <?php get_template_part('template-parts/common/product/add-empty','product'); ?>
        </div>

          <?php if (!empty($kit_products) && is_array($kit_products)):?>

          

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

            <div class="column col-xs-6 col-sm-6 col-md-6 col-3 product-tile-column">
              <ul class="product">
                <?php wc_get_template_part( 'content', 'product' );?>
              </ul>
            </div>

          <?php endforeach;?>
          <?php endif;?>


          <?php if (!empty($recommendations) && is_array($recommendations)):?>
                <?php foreach ($recommendations as $recommendation):?>
                <?php
                    global $rec;
                    $rec = $recommendation;
                ?>
                <div class="column col-xs-6 col-sm-6 col-md-6 col-3 product-tile-column recommendations">
                    <?php get_template_part('template-parts/common/product/product','recommendation'); ?>
                </div>
                <?php endforeach;?>
          <?php endif;?>

          

      </div>
  </div>
</div>

<div class="bg-gray">
    <div class="max-width-xl kitting-page">
        <?php
            get_template_part( 'template-parts/common/hero/hero-product','list');
        ?>
    </div>
</div>