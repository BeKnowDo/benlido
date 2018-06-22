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

function bl_get_featured_categories() {
  $results = array();
  $key = 'bl_featured_cats';
  // this is where we fetch data from cache
  if (empty($results)) {
    if (function_exists('get_field')) {
      $categories = get_field('featured_categories', get_option( 'woocommerce_shop_page_id' ));
      if (!empty($categories) && is_array($categories)) {
        foreach ($categories as $category) {
          //print_r ($category);
          if (!empty($category) && isset($category['category'])) {
            $cat_id = $category['category'];
            $cat_obj = get_term_by('id',$cat_id,'product_cat');
            $featured_products = $category['featured_products'];
            //print_r ($cat_obj);
            if (!empty($cat_obj) && is_object($cat_obj)) {
              $name = $cat_obj->name;
              $cat_url = get_term_link($cat_id,'product_cat'); 
            }
            $prods = array();
            if (!empty($featured_products) && is_array($featured_products)) {
              foreach ($featured_products as $prod_array) {
                $prod = $prod_array['product'];
                if (!empty($prod)) {
                  $product_id = $prod->ID;
                  $product = wc_get_product($product_id);
                  $sku = $product->get_sku();
                  $product_url = get_permalink( $product_id);
                  $product_title = $prod->post_title;
                  $prod_description = $prod->post_content;
                  $image = wp_get_attachment_image_url( get_post_thumbnail_id( $product_id ), 'full' ); // NOTE: we should come up with a good size for the product tiles
                  $price = $product->get_price();
                  $product_taxonomy = $cat_id;
                  $prods[] = array('id'=>$product_id,'href'=>$product_url,'name'=>esc_attr($product_title),'description'=>$product_title,'categoryTitle'=>$name,'price'=>$price,'productCategoryID'=>$cat_id,'image'=>$image);
                }
              } // end foreach
              $results[] = array('id'=>$cat_id,'name'=>$name,'href'=>$cat_url,'featured'=>$prods);
            }
          }
        }
      }
    } // end get_field
  } // end no results
  return $results;
} // end bl_get_featured_categories()

// this processes the list of things in the "bags" page template
// we need to see if it's a product or a kit
function bl_process_bags_list($items) {
  $results = array();
  $now = time();
  if (!empty($items) && is_array($items)) {
    foreach ($items as $el) {
      $image = '';
      $prod = null;
      $coming_soon = '';
      $skip = false;
      $href = '#';
      //print_r ($el);
      // get if the item is published or coming soon
      if (!empty($el) && is_array($el)) {
        $item = $el['product'];
        if (!empty($item) && is_object($item)) {
          $item_id = $item->ID;
          $title = $item->post_title;
          $description = $item->post_content;
          $status = $item->post_status;
          $type = $item->post_type;
        }
        $coming_soon = $el['coming_soon_copy'];
        $button_copy = $el['button_copy'];
        
        switch ($type) {
          case 'travel_kit':
            // the price of the kit is the total of all the products
            if (function_exists('bl_get_kit_price')) {
              $price = wc_price(bl_get_kit_price($item_id));
            }
            if (function_exists('get_field')) {
              // we need to get the kit page
              
            }
          break;
          default:

            if (function_exists('wc_get_product')) {
              $prod = wc_get_product($item_id);
            }
            if (!empty($prod)) {
              $price = $prod->get_price_html();
            }

          break;
        }
        $url = get_permalink($item_id);

        
        
        $disabled = false;
        if ($status == 'draft' || $status == 'future') {
          // see when it's available
          $post_date = $item->post_date;
          $timestamp = strtotime($post_date);
          if ($timestamp > $time) {
            $disabled = true;
          } else {
            // we're going to remove it
            $skip = true;
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
        if ($skip != true) {
          $results[] = array(
            'feature'=>$feature,
            'logo'=>$logo,
            'header'=>$title,
            'copy'=>$description,
            'price'=>$price,
            'href'=>'#'.$item_id,
            'button_copy'=>$button_copy,
            'image'=>$image,
            'image_retina'=>$image_retina,
            'bagURL'=>$url,
            'coming_soon' => $coming_soon,
            'disabled'=>$disabled
          );
        }

      } // end $item
    }
  }
  return $results;
} // end bl_process_bags_list()

// gets the name of this particular category, no matter it's from a category page, a shop landing page, or the primary category of the product
function bl_get_this_category() {
  if (is_product_category()) {
      $category = get_queried_object();
      if (!empty($category) && isset($category->name)) {
        return $category;
      }
  }
  global $product;
  global $product_override;
  if (!empty($product_override) && isset($product_override['id'])) {
    $override_id = $product_override['id'];
    if (!empty($product) && is_object($product)) {
      $product_id = $product->get_id();
    }
    if ($override_id > 0 && $override_id == $product_id) {
      if (isset($product_override['categoryTitle'])) {
          $category = $product_override['categoryTitle'];
          return $category; // just returning the category name
      }
    }
  }
}