<?php

$heroNoBag = array(
  "header" => "NO THANKS, I DON'T NEED A BAG",
  "url" => '/shop',
  "actionText" => 'Shop Products'
);

$data["heroNoBag"] = $heroNoBag;

Timber::render( 'common/hero/hero-no-bag.twig',$data);
