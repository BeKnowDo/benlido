<?php
global $rec;
$data = array('product'=>$rec);
//print_r ($data);
Timber::render( 'common/product/product-recommendation.twig', $data);