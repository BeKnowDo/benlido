<?php
// this is how we're passing the featured product into the sub templates
global $shop_landing_featured_product;

$data = array('product'=>$shop_landing_featured_product,'category'=>$category);
Timber::render( 'common/product/product-tile.twig', $data);
