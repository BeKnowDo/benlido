<?php
global $kit_id;
global $current_order;
if (!empty($kit_id) && function_exists('get_field')) {
    // this is the kitting page
    $header = get_field('middle_header');
    $subheader = get_field('middle_subheader');
    $categoryHeader = array(
        array('text'=>$header,'subText'=>$subheader)
    );
}
elseif (!empty($current_order) && is_object($current_order) && function_exists('get_field')) {
    // this is either the thank you page or the order history page
    $super_header = get_field('super_header','option');
    $header = get_field('header','option');
    $categoryHeader = array('text'=>$super_header,'subText'=>$header);
}
$data = array(
    'categoryHeader' => array($categoryHeader)
);
Timber::render( 'common/category-header.twig', $data);