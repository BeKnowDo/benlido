<?php

if (function_exists('get_field')) {
  $no_bag_hero_title = get_field('no_bag_hero_title','option');
  $no_bag_hero_button_url = get_field('no_bag_hero_button_url','option');
  $no_bag_hero_button_copy = get_field('no_bag_hero_button_copy','option');
}

if (!empty($no_bag_hero_button_copy) && !empty($no_bag_hero_button_url)) {
  $heroNoBag = array(
    "header" => $no_bag_hero_title,
    "url" => $no_bag_hero_button_url,
    "actionText" => $no_bag_hero_button_copy
  );
}

if (empty($heroNoBag)) {
  $heroNoBag = array(
    "header" => "NO THANKS, I ALREADY HAVE A BAG",
    "url" => '/shop?shop_now=true',
    "actionText" => 'Shop Products'
  );
}


$data["heroNoBag"] = $heroNoBag;

Timber::render( 'common/hero/hero-no-bag.twig',$data);
