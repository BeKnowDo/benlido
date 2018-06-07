<?php
// this file contains functions to get general display elements
// like nav menu, social media, etc.

function bl_get_site_logo($is_diamond=false) {
  $logo = null;
  if ($is_diamond == true && function_exists('get_field')) {
    $diamond_logo = get_field('diamond_shaped_logo','option');
    if (!empty($diamond_logo) && isset($diamond_logo['url'])) {
      $logo = '<a href="' . get_home_url() . '" title="' . esc_attr(get_bloginfo( 'name') ) . '" ><img src="' . $diamond_logo['url'] . '" /></a>';
    }
  }
  elseif ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
    $logo = get_custom_logo();
  }
  return $logo;
}

function bl_get_top_nav() {
    $final_nav = array();
    $locations = get_nav_menu_locations();
    $menu_id = $locations[ 'primary' ] ;
    $nav = wp_get_nav_menu_items($menu_id);
    foreach ($nav as $item) {
      $res = bl_generate_nav_links($item);
      $final_nav[] = $res;
    }
    return $final_nav;
}

function bl_get_footer_nav() {
  $final_nav = array();
  $locations = get_nav_menu_locations();
  $menu_id = $locations[ 'footer' ] ;
  $nav = wp_get_nav_menu_items($menu_id);
  foreach ($nav as $item) {
    $res = bl_generate_nav_links($item);
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
      $res = bl_generate_nav_links($item);
      $final_nav[] = $res;
    }
    return $final_nav;
}

function bl_get_contact_nav() {
  $final_nav = array();
  $locations = get_nav_menu_locations();
  $menu_id = $locations[ 'contact' ] ;
  $nav = wp_get_nav_menu_items($menu_id);
  foreach ($nav as $item) {
    $res = bl_generate_nav_links($item);
    $final_nav[] = $res;
  }
  return $final_nav;
}

function bl_generate_nav_links($item) {
  $res = array();
  if (!empty($item) && is_object($item)) {
    $res = array(
      'id'=>$item->ID,
      'title'=>$item->title,
      'alt_title'=>esc_attr($item->title),
      'post_name'=>$item->post_name,
      'url'=>$item->url,
      'target'=>$item->target,
      'classes'=>implode(' ', $item->classes)
    );
  }
  
  return $res;
}