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

function bl_process_acf_buttons($items) {
  $results = array();
  if (!empty($items) && is_array($items)) {
    foreach ($items as $item) {
      // NOTE: we should have the same names for iterations
      $title = $item['button_name'];
      $url = $item['button_url'];
      $classes = $item['button_css_class'];
      $target = $item['open_in_a_new_tab'];
      $results[] = array('title'=>$title,'url'=>$url,'classes'=>$classes,'target'=>$target);
    }
  }
  return $results;
}

function bl_process_bags_list($items) {
  $results = array();
  $now = time();
  if (!empty($items) && is_array($items)) {
    foreach ($items as $el) {
      $image = '';
      $prod = null;
      //print_r ($el);
      // get if the item is published or coming soon
      if (!empty($el) && is_array($el)) {
        $item = $el['product'];
        if (!empty($item) && is_object($item)) {
          $item_id = $item->ID;
          $title = $item->post_title;
          $description = $item->post_content;
          $status = $item->post_status;
        }
        
        $url = get_permalink($item_id);
        if (function_exists('wc_get_product')) {
          $prod = wc_get_product($item_id);
        }
        if (!empty($prod)) {
          $price = $prod->get_price_html();
        }
        
        $disabled = false;
        if ($status == 'draft') {
          // see when it's available
          $post_date = $item->post_date;
          $timestamp = strtotime($post_date);
          if ($timestamp > $time) {
            $disabled = true;
          }
        }
        // get image
        $image = wp_get_attachment_image_url( get_post_thumbnail_id( $item_id ), 'full' ); // NOTE: we'll need to figure out a good image size for here
        if (empty($image)) {

        }
        // image overrides
        if (!empty($el['image_override']) && isset($el['image_override']['url'])) {
          $image = $el['image_override']['url'];
        }
        if (!empty($el['image_override_retina']) && isset($el['image_override_retina']['url'])) {
          $image_retina = $el['image_override_retina']['url'];
        }
        $results[] = array(
          'feature'=>$feature,
          'logo'=>$logo,
          'header'=>$title,
          'copy'=>$description,
          'price'=>$price,
          'href'=>'#'.$item_id,
          'image'=>$image,
          'image_retina'=>$image_retina,
          'bagURL'=>$url,
          'disabled'=>$disabled
        );
      } // end $item
    }
  }
  return $results;
} // end bl_process_bags_list()