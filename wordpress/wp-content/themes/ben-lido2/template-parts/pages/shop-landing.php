<?php
$featured_categories = array();
?>
<div id="shop-landing-featured-products" class="column col-xs-12 col-sm-12 col-md-12 col-9 shop-landing-featured-products">

          <?php foreach ($featured_categories as $featured_cat): ?>
            <div class="columns">
              <h3 class="column col-12 shop-landing-featured-header" id="category-{{category.id}}">{{category.name}}</h3>

                <?php foreach ($featured_products as $featured_product):?>
                    <div class="column col-xs-12 col-sm-12 col-md-6 col-4">
                    <?php get_template_part('template-parts/product/product','tile');?>
                    </div>
                <?php endforeach;?>
            </div>

            <div class="columns shop-landing-featured-view-all">
              <div class="column col-mx-auto text-center">
                <a href="{{category.href}}" class="btn btn-lg">View all
                  {{category.name}}
                </a>
              </div>
            </div>

          <?php endforeach;?>

</div>