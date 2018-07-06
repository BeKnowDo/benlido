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
  $index = 0;
  if (!empty($items) && is_array($items)) {
    foreach ($items as $el) {
      $image = '';
      $prod = null;
      $coming_soon = '';
      $skip = false;
      $href = '#';
      $swatches = array();
      $category_id = 0;
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
        $selected_copy = $el['selected_copy'];
        
        switch ($type) {
          case 'travel_kit':
            $css = 'prebuilt-kit';
            // the price of the kit is the total of all the products
            if (function_exists('bl_get_kit_price')) {
              $price = wc_price(bl_get_kit_price($item_id));
            }
            if (function_exists('get_field')) {
              // we need to get the kit page
              $kitting_page = get_field('kitting_page','option');
              // need the bag associated with the kit
              if ($kitting_page) {
                $href = get_permalink($kitting_page) . '?id=' . $item_id;
              }
              
            }
          break;
          default:
            $css = 'self-kit';
            $prod = null;
            $available_variations = null;
            $category_id = 0;
            // note: if we have already picked a bag, we will have the variation ID
            $product_id = $item_id;
            $product = wc_get_product($product_id);
            
            if (function_exists('bl_get_product_category')) {
              $product_cat = bl_get_product_category($product_id);
            }
            if (!empty($product_cat) && is_object($product_cat) && isset($product_cat->term_id)) {
              $category_id = $product_cat->term_id;
            }
            
            $swatches = bl_get_variable_product_swatches($product_id,$variation_id);
            if (!empty($swatches)) {
              $css .= ' has-variations';
            }
            $url = get_permalink($product_id);
            $href = get_permalink( woocommerce_get_page_id( 'shop' ) );
          break;
        }
        

        
        
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
            'index' => $index,
            'feature'=>$feature,
            'logo'=>$logo,
            'css' => $css,
            'header'=>$title,
            'copy'=>$description,
            'price'=>$price,
            'href'=>$href,
            'category_id'=>$category_id,
            'product_id'=>$product_id,
            'swatches'=>$swatches,
            'button_copy'=>$button_copy,
            'selected_copy'=>$selected_copy,
            'image'=>$image,
            'image_retina'=>$image_retina,
            'bagURL'=>$url,
            'coming_soon' => $coming_soon,
            'disabled'=>$disabled
          );
        }
        $index++;
      } // end $item
    } // end foreach
  }
  return $results;
} // end bl_process_bags_list()

// NOTE: this method relies heavily on the woocommerce-variation-swatches-and-photos plugin. 
function bl_get_variable_product_swatches($product_id,$selected_variation_id=null) {
  $variations = array();
    if (function_exists('wc_get_product')) {
      $prod = wc_get_product($product_id);
    }
    if (!empty($prod)) {
      $price = $prod->get_price_html();
      $available_variations = $prod->get_available_variations();
      //print_r ($available_variations);
    }

    if (!empty($available_variations) && is_array($available_variations)) {
      $swatch_type_options = maybe_unserialize( get_post_meta( $product_id, '_swatch_type_options', true ) );
      $available_color_swatches = array();
      $term_color_hash = md5(sanitize_title('Custom Colors and images'));
      //echo $term_color_hash;
      foreach ($swatch_type_options as $key => $swatch_types) {
        //if ($key == $term_color_hash) {
          $available_color_swatches = $swatch_types['attributes'];
        //}
        break;
      }
      //print_r ($swatch_type_options);
      foreach ($available_variations as $variation) {
        // we need variation ID, attribute name, color swatch, image
        //print_r ($variation);
        $attributes = $variation['attributes'];
        $swatch_obj = array();
        $swatch_image = null;
        $swatch_type = null;
        $swatch_color = null;
        $selected = false;
        if (!empty($attributes) && is_array($attributes) && isset($attributes['attribute_pa_color'])) {
          $color_slug = $attributes['attribute_pa_color'];
          $color_hash = md5(sanitize_title($color_slug));
        }
        $variation_id = $variation['variation_id'];
        $color = get_term_by('slug',$color_slug,'pa_color');
        if (!empty($color) && is_object($color) && isset($color->name)) {
          $color_name = $color->name;
        }
        //print_r ($available_color_swatches);
        if (!empty($color_hash) && !empty($available_color_swatches)) {
          $swatch_obj = $available_color_swatches[$color_hash];
          //print_r ($swatch_obj);
        }
        if (!empty($swatch_obj) && is_array($swatch_obj) && isset($swatch_obj['type'])) {
          $swatch_type = $swatch_obj['type'];
        }
        if (!empty($swatch_obj) && is_array($swatch_obj) && isset($swatch_obj['image'])) {
          $swatch_image = wp_get_attachment_url($swatch_obj['image']);
        }
        if (!empty($swatch_obj) && is_array($swatch_obj) && isset($swatch_obj['color'])) {
          $swatch_color = $swatch_obj['color'];
        }
        if ($variation_id == $selected_variation_id) {
          $selected = true;
        }
        
        $variations[] = array(
          'title' => $color_name,
          'id' => $variation_id,
          'type' => $swatch_type,
          'color' => $swatch_color,
          'selected' => $selected,
          'image' => $swatch_image
        );
      }
    }
    return $variations;
} // end bl_get_variable_product_swatches()

function bl_process_kit_list($items) {

  $results = array();
  if (!empty($items) && is_array($items)) {
    foreach ($items as $item) {
      //print_r ($item);
      $cat_id = $item['category'];
      $category = get_term($cat_id);
      if (!empty($category)) {
        $category_name = $category->name;
      }
      $prod = $item['featured_product'];
      if (!empty($prod)) {
        $product = wc_get_product($prod);
      }
      if (!empty($product)) {

      }

    }
  }
  die;
} // end bl_process_kit_list()

function bl_process_kit_bag($item) {

  $results = array();
  // this is always an array of arrays
  if (!empty($item) && is_object($item) && method_exists($item,'get_name')) {
    $name = $item->get_name();
    $logo = ''; // need to get the brand logo
    $description = $item->get_description();
    $href = get_permalink($item);
    $image = wp_get_attachment_image_src(get_post_thumbnail_id($item->get_id()),'woocommerce_single');
    if (!empty($image) && is_array($image)) {
      $image = $image[0];
    }
  }
  $results[] = array(
        'triangleBackground' => true,
        'logo' => $logo,
        'header' => $name,
        'copy' => $description,
        'picked' => true,
        'href' => $href,
        'image' => $image,
        'bagURL' => $href
  );
  return $results;
}

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
  //print_r ($product_override);
  
  if (!empty($product_override) && isset($product_override['id'])) {
    $override_id = $product_override['id'];
    if (!empty($product) && is_object($product)) {
      $product_id = $product->get_id();
    }
    if ($override_id > 0 && $override_id == $product_id) {
      if (isset($product_override['categoryTitle']) || isset($product_override['productCategoryID'])) {
        // returning the object
        $cat_id = $product_override['productCategoryID'];
        if (!empty($cat_id) && is_numeric($cat_id)) {
          $category = get_term($cat_id);
          return $category;
        }
      }
    }
  }

  if (!empty($product_override) && isset($product_override['category'])) {
    $category = $product_override['category'];
    if (is_numeric($category)) {
      $category_obj = get_term($category);
      return $category_obj;
    }
  }
  $terms = get_the_terms( $product->get_id(), 'product_cat' );
  foreach ($terms as $term) {
      $product_cat_id = $term->term_id;
      break;
  }
  if ($product_cat_id > 0) {
    $category_obj = get_term($product_cat_id);
    return $category_obj;
  }
}

if (!function_exists('bl_get_product_category')) {
  function bl_get_product_category($product_id) {
    $terms = get_the_terms( $product_id, 'product_cat' );
    foreach ($terms as $term) {
        $product_cat_id = $term->term_id;
        break;
    }
    if ($product_cat_id > 0) {
      $category_obj = get_term($product_cat_id);
      return $category_obj;
    }
  }
}
