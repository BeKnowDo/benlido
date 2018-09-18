<?php

$heroNoBag = array(
  "header" => "NO THANKS, I ALREADY HAVE A BAG",
  "url" => '/shop?shop_now=true',
  "actionText" => 'Shop Products'
);

$data["heroNoBag"] = $heroNoBag;

Timber::render( 'common/hero/hero-no-bag.twig',$data);
