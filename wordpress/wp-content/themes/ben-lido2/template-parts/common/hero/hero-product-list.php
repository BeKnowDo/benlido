<?php
// we're using this page for both bags and kits... so we are checking if we have a kit Id
global $kit_id;

$data = array();
$bags = array();

if (function_exists('get_field')) {
    $selectable_bags = get_field('selectable_bags');
    //print_r ($selectable_bags);
    if(function_exists('bl_process_bags_list')) {
        $bags = bl_process_bags_list($selectable_bags);
    }  
}
//print_r ($bags);

if (empty($bags) && !empty($kit_id)) {
    if (function_exists('bl_get_kit_bag')) {
        $bags = bl_get_kit_bag($kit_id);
    }

    if (!empty($bags) && is_object($bags) && function_exists('bl_process_kit_bag')) {
        $bags = bl_process_kit_bag($bags,$kit_id);
    }
}

$data = array('products'=>$bags);
//print_r ($data);
Timber::render( 'common/hero/hero-product-list.twig', $data);