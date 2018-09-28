<?php
// we're using this page for both bags and kits... so we are checking if we have a kit Id
global $kit_id;
global $bl_custom_kit_id;

$data = array();
$bags = array();
$kits = array();

if (function_exists('get_field')) {
    $selectable_bags = get_field('selectable_bags');

    //print_r ($selectable_bags);
    if(function_exists('bl_process_bags_list')) {
        $bags = bl_process_bags_list($selectable_bags);
    }

    $selectable_kits = get_field('selectable_kits');

    //print_r ($selectable_kits);
    if(function_exists('bl_process_bags_list')) {
        $kits = bl_process_bags_list($selectable_kits);
    }
    //print_r ($kits);

}
//print_r ($bags);

if (empty($bags) && !empty($kit_id)) {
    if (function_exists('bl_get_kit_bag') && $kit_id != $bl_custom_kit_id) {
        $bags = bl_get_kit_bag($kit_id);
    }


    if (!empty($bags) && is_object($bags) && function_exists('bl_process_kit_bag')) {
        $bags = bl_process_kit_bag($bags,$kit_id);
    }

    // this means that it is not a real prebuilt kit
    if ($kit_id == $bl_custom_kit_id) {
        $bags = null;
    }

}
if (empty($bags) && empty(bl_get_product_swap()) && bl_is_kit_add() == false) {
    $bag = bl_get_bag_from_cart();
    if (!empty($bag) && isset($bag['id'])) {
        $item = wc_get_product( $bag['id'] );
        if (get_class($item) == 'WC_Product_Variation') {
            $parent_id = $item->get_parent_id();
        }
        if ($parent_id > 0) {
            $item = wc_get_product($parent_id);
        }
        $bags = bl_process_kit_bag($item);
    }
    // actually, because this bag is from the bags
}
//print_r ($bags);
// override message
// if we are in the

// echo '<br/><br/><pre>' . var_export($kits, true) . '</pre>';

$data = array('products' => $bags, 'kits'=>$kits);

//print_r ($data);

Timber::render( 'common/hero/hero-product-compact-list.twig', $data);