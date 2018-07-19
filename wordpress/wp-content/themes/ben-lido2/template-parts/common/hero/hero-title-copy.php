<?php
$data = array();
// this is the header area
if (is_product_category()) {
    $category = get_queried_object();
    if (!empty($category) && isset($category->name)) {
        $heroData = array(
            array(
                'header' => $category->name
            )
        );
    }
}
if (is_shop()) {
    // let's see if it's a addition to a kit
    $is_kit_add = false;
    if (function_exists('bl_is_kit_add')) {
    $is_kit_add = bl_is_kit_add();
    }
    if ($is_kit_add == true) {
        
    }
}
if (empty($heroData) && function_exists('get_field')) {
    $hero_title_header = get_field('hero_title_header');
    $hero_title_copy = get_field('hero_title_copy');
    if (!empty($hero_title_header) || !empty($hero_title_copy)) {
        $heroData = array(
            array(
                'header'=>$hero_title_header,
                'copy'=>$hero_title_copy
            )
        );
    }
}
if (empty($heroData)) {
    $heroData = array(array(
        'header'=> 'PICK YOUR TRAVEL COMPANION',
        'copy'=> 'No shipping costs and all personal care items up to 75% free with bag purchase'
    ));
}
$data = array('heroData'=>$heroData);
Timber::render( 'common/hero/hero-title-copy.twig', $data);