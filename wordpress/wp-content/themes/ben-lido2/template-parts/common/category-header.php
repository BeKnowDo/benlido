<?php
global $kit_id;
if (!empty($kit_id) && function_exists('get_field')) {
    // this is the kitting page
    $header = get_field('middle_header');
    $subheader = get_field('middle_subheader');
    $categoryHeader = array(
        array('text'=>$header,'subText'=>$subheader)
    );
}
$data = array(
    'categoryHeader' => $categoryHeader
);
Timber::render( 'common/category-header.twig', $data);