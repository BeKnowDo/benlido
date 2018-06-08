<?php
$data = array();
$bags = array();
if (function_exists('get_field')) {
    $selectable_bags = get_field('selectable_bags');
    //print_r ($selectable_bags);
    if(function_exists('bl_process_bags_list')) {
        $bags = bl_process_bags_list($selectable_bags);
    }  
}
$data = array('products'=>$bags);
//print_r ($data);
Timber::render( 'common/hero/hero-product-list.twig', $data);