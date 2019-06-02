<?php
$data = array();
if (function_exists('get_field')) {
    $add_item_block_header = get_field('add_item_block_header','option');
    $add_item_block_subheader = get_field('add_item_block_subheader','option');
    $add_item_button_copy = get_field('add_item_button_copy','option');
    $add_item_button_copy_esc = esc_attr($add_item_button_copy);
    $shop_page_url = get_field('shop_page_url','option');
    if (is_object($shop_page_url) && isset($shop_page_url->ID)) {
        $shop_page_url = get_permalink($shop_page_url->ID);
    }
    $data = array('header'=>$add_item_block_header,'subheader'=>$add_item_block_subheader,'link_title_esc'=>$add_item_button_copy_esc,'link_title'=>$add_item_button_copy,'url'=>$shop_page_url);
}
Timber::render( 'common/product/add-empty-product.twig', $data);