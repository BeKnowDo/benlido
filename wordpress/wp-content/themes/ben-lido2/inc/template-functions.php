<?php
// this file contains functions to get general display elements
// like nav menu, social media, etc.
function bl_get_top_nav() {
    $final_nav = array();
    $locations = get_nav_menu_locations();
    $menu_id = $locations[ 'primary' ] ;
    $nav = wp_get_nav_menu_items($menu_id);
    foreach ($nav as $item) {
      $res = array('id'=>$item->ID,'title'=>$item->title,'alt_title'=>esc_attr($item->title),'post_name'=>$item->post_name,'url'=>$item->url,'target'=>$item->target,'classes'=>$item->classes);
      $final_nav[] = $res;
    }
    return $final_nav;
}

function bl_get_social_media_nav() {
    $final_nav = array();
    $locations = get_nav_menu_locations();
    $menu_id = $locations[ 'social-menu' ] ;
    $nav = wp_get_nav_menu_items($menu_id);
    foreach ($nav as $item) {
      $res = array('id'=>$item->ID,'title'=>$item->title,'alt_title'=>esc_attr($item->title),'post_name'=>$item->post_name,'url'=>$item->url,'target'=>$item->target,'classes'=>$item->classes);
      $final_nav[] = $res;
    }
    return $final_nav;
}