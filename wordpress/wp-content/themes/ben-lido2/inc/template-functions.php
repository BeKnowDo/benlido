<?php
// this file contains functions to get general display elements
// like nav menu, social media, etc.

function bl_get_site_logo($is_diamond=false) {
  $logo = null;
  if ($is_diamond == true && function_exists('get_field')) {
    $diamond_logo = get_field('diamond_shaped_logo','option');
    if (!empty($diamond_logo) && isset($diamond_logo['url'])) {
      $logo = '<a href="' . get_home_url() . '" title="' . esc_attr(get_bloginfo( 'name') ) . '" ><img src="' . $diamond_logo['url'] . '" alt="Ben Lido Logo"/></a>';
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

function bl_get_brands_contact_nav() {
  $final_nav = array();
  $locations = get_nav_menu_locations();
  $menu_id = $locations[ 'for-brands' ];
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
                  if (empty($product)) {
                    continue;
                  }
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
      // echo '<br/><br/><pre>' . var_export($el, true) . '</pre>';

      $image = '';
      $is_kit = false;
      $prod = null;
      $coming_soon = '';
      $skip = false;
      $href = '#';
      $swatches = array();
      $category_id = 0;
      $products = array();
      $hover_details = '';
      $price = '';
      $pre_header = '';
      $is_new_kit = false; // this shows me if we are talking about  a new system or not
      //print_r ($el);
      // get if the item is published or coming soon
      if (!empty($el) && is_array($el)) {
        $item = $el['product'];
        if (empty($item)) {
          $item = $el['kit'];
          if (!empty($item)) {
            $is_new_kit = true;
          }
        }
        if (!empty($item) && is_object($item)) {
          $item_id = $item->ID;
          $title = $item->post_title;
          $description = $item->post_content;
          $status = $item->post_status;
          $type = $item->post_type;
        }
        $description_override = $el['description_override'];
        $coming_soon = $el['coming_soon_copy'];
        $button_copy = $el['button_copy'];
        $selected_copy = $el['selected_copy'];
        $hover_details = $el['hover_details'];

        if (function_exists('get_field')) {
          // we need to get the kit page
          $kitting_page = get_field('kitting_page','option');
          $price = get_field('price_override',$item_id);

        }

        if (!empty($price)) {
          $args = array('decimals'=>0);
          $price = wc_price($price,$args);
        }

        switch ($type) {
          case 'travel_kit':
            $is_kit = true;
            $css = 'prebuilt-kit';
            $disabled = false;
            $products = array();
            //print_r ($el);
            // the price of the kit is the total of all the products

            if (empty($price) && function_exists('bl_get_kit_price')) {
              $args = array('decimals'=>0);
              $price = wc_price(bl_get_kit_price($item_id),$args);
              $price =  ' <span class="hero-product-callout">about </span>' . $price . ' <span class="hero-product-callout">as shown</span>';
            }

            if ($is_new_kit == true) {
              // get image
              $image = wp_get_attachment_image_url( get_post_thumbnail_id( $item_id ), 'full' ); // NOTE: we'll need to figure out a good image size for here
            }


            if (function_exists('get_field')) {
              // we need to get the kit page
              $kitting_page = get_field('kitting_page','option');
              // get the bag for the kit
              $bag_for_this_kit = get_field('bag_for_this_kit',$item_id);
              $color_variation_image_overrides = get_field('color_variation_image_overrides',$item_id);
              $product_categories = get_field('product_categories',$item_id);
              //print_r ($product_categories);
              if (!empty($product_categories) && is_array($product_categories)) {
                foreach ($product_categories as $prod_cat) {
                  //print_r ($prod_cat);
                  $prod = $prod_cat['featured_product'];
                  if ($prod) {
                    $products[] = $prod->post_title;
                  }
                }
              }
              // need the bag associated with the kit
              if ($kitting_page) {
                $href = get_permalink($kitting_page) . '?id=' . $item_id;
                $pre_header = get_field('pre_header', $item_id);
              }

            }

            if (!empty($bag_for_this_kit) && is_object($bag_for_this_kit) && isset($bag_for_this_kit->ID)) {
                $product_id = $bag_for_this_kit->ID;
                $product = wc_get_product($product_id);
                // see if we have selected this same product
                $bag_in_cart = bl_get_bag_from_cart();
                if (function_exists('bl_get_product_category')) {
                  $product_cat = bl_get_product_category($product_id);
                }
            }

            if (!empty($product_cat) && is_object($product_cat) && isset($product_cat->term_id)) {
              $category_id = $product_cat->term_id;
            }

            if (!empty($product_id) && !empty($color_variation_image_overrides) && !empty($product_cat)) {
              $swatches = bl_get_bag_product_swatch_overrides($product_id,$category_id,$color_variation_image_overrides);
            }

            if (!empty($swatches) && !empty($bag_in_cart)) {
              $swatches_holder = array();
              foreach ($swatches as $swatch) {
                if ($swatch['id']==$bag_in_cart['id']) {
                  $swatch['selected'] = true;
                  $css .= ' hero-product-picked';
                  $picked = true;
                }
                $swatches_holder[] = $swatch;
              }
              $swatches = $swatches_holder;
            }
            //$swatches = bl_get_variable_product_swatches($product_id,$variation_id);
            if (!empty($swatches)) {
              $css .= ' has-variations';
            }
          break;
          default:
            //$css = 'self-kit';
            // NOTE: we will create a prebuilt kit with no items in it.
            $css = 'prebuilt-kit';
            $is_kit = false;
            $disabled = false;
            $prod = null;
            $available_variations = null;
            $category_id = 0;
            $picked = false;
            // note: if we have already picked a bag, we will have the variation ID
            $product_id = $item_id;
            $product = wc_get_product($product_id);
            // see if we have selected this same product
            $bag_in_cart = bl_get_bag_from_cart();

            if (!empty($product) && is_object($product)) {
              $price = $product->get_price_html();
            }

            if (function_exists('bl_get_product_category')) {
              $product_cat = bl_get_product_category($product_id);
            }
            if (!empty($product_cat) && is_object($product_cat) && isset($product_cat->term_id)) {
              $category_id = $product_cat->term_id;
            }

            // NOTE: swatches come from the bags page because we need to have a custom image
            //print_r ($el);
            $color_variation_image_overrides = $el['color_variation_image_overrides'];
            $swatches = bl_get_bag_product_swatch_overrides($product_id,$category_id,$color_variation_image_overrides);
            if (!empty($swatches) && !empty($bag_in_cart)) {
              $swatches_holder = array();
              foreach ($swatches as $swatch) {
                if ($swatch['id']==$bag_in_cart['id']) {
                  $swatch['selected'] = true;
                  $css .= ' hero-product-picked';
                  $picked = true;
                }
                $swatches_holder[] = $swatch;
              }
              $swatches = $swatches_holder;
            }
            //$swatches = bl_get_variable_product_swatches($product_id,$variation_id);
            if (!empty($swatches)) {
              $css .= ' has-variations';
            }
            $url = get_permalink($product_id);
            //$href = get_permalink( woocommerce_get_page_id( 'shop' ) );
            if ($kitting_page) {
              $href = get_permalink($kitting_page);
            }
          break;
        }

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
        // description override
        if (!empty($description_override)) {
          $description = $description_override;
        }
        if ($skip != true) {
          $results[] = array(
            'index' => $index,
            'is_kit' => $is_kit,
            'pre_header' => $pre_header,
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
            'hover_details' => $hover_details,
            'products' => $products,
            'picked'=>$picked,
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

// takes the ACF
function bl_get_bag_product_swatch_overrides($product_id,$category_id,$overrides,$selected_id=0) {
  //print_r ($overrides);
  $res = array();
  $holder = array();
  if (empty($overrides)) {
    $overrides = get_field('color_variation_image_overrides',$product_id);
  }
  if (is_array($overrides)) {
    foreach ($overrides as $override) {
      // should have a variation and image override
      //print_r ($override);
      $title = $id = $type  = $image = $image_retina = null;
      $variation = $override['variation'];
      $image_override = $override['image_override'];
      $image_override_retina = $override['image_override_retina'];
      $default_selected_color = $override['default_selected_color'];
      $type = 'image';
      if (!empty($variation) && is_object($variation)) {
        $id = $variation->ID;
        $title = $variation->post_title;
      }
      if (!empty($image_override) && is_array($image_override)) {
        $image = $image_override['url'];
      }
      if (!empty($image_override_retina) && is_array($image_override_retina)) {
        $image_retina = $image_override_retina['url'];
      }
      $holder[] = array('id'=>$id,'category_id'=>$category_id,'title'=>$title,'type'=>$type,'hero_image'=>$image,'hero_image_retina'=>$image_retina,'default_selected_color'=>$default_selected_color);
    } // end foreach
  }
  $swatches = bl_get_variable_product_swatches($product_id,$selected_id);
  if (is_array($swatches) && is_array($holder)) {
    foreach ($swatches as $swatch) {
      $swatch_id = $swatch['id'];
      foreach ($holder as $el) {
        $test_id = $el['id'];
        if ($swatch_id == $test_id) {
          $swatch['hero_image'] = $el['hero_image'];
          $swatch['hero_image_retina'] = $el['hero_image_retina'];
          $swatch['default_selected_color'] = $el['default_selected_color'];
        }
      }

      $res[] = $swatch;
    }
  }

  return $res;
}

// NOTE: this method relies heavily on the woocommerce-variation-swatches-and-photos plugin.
function bl_get_variable_product_swatches($product_id,$selected_variation_id=null) {
  $variations = array();
    if (function_exists('wc_get_product')) {
      $prod = wc_get_product($product_id);
    }
    if (!empty($prod)) {
      $product_name = $prod->get_name();
      $sku = $prod->get_sku();
      $price = $prod->get_price_html();
      $available_variations = $prod->get_available_variations();
      $category_ids = $prod->get_category_ids();
      //print_r ($available_variations);
    }
    if (!empty($category_ids) && is_array($category_ids)) {
      $cat_id = $category_ids[0];
    }
    if (!empty($cat_id)) {
      $cat_obj = get_term($cat_id);
    }
    if ($cat_obj && is_object($cat_obj)) {
      $product_category_name = $cat_obj->name;
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
        $price = $variation['display_price'];
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
          'image' => $swatch_image,
          'product_name' => $product_name,
          'sku' => $sku,
          'product_category_name' => $product_category_name,
          'price' => $price
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

function bl_process_kit_bag($item,$kit_id=null) {

  $results = array();
  $css = ' in-kit-detail ';
  // this is always an array of arrays
  $product_id = 0;
  $index = 0;
  $pre_header = '';
  //print_r ($item);
  if (function_exists('get_field') && !empty($kit_id)) {
    $color_variation_image_overrides = get_field('color_variation_image_overrides',$kit_id);
  }
  if (!empty($item) && is_object($item) && method_exists($item,'get_name')) {
    $product_id = $item->get_id();
    $name = $item->get_name();
    $logo = ''; // need to get the brand logo
    $description = $item->get_description();
    $href = get_permalink($item);
    if (function_exists('bl_get_product_category')) {
      $product_cat = bl_get_product_category($product_id);
    }
    $image = wp_get_attachment_image_src(get_post_thumbnail_id($item->get_id()),'woocommerce_single');
    if (!empty($image) && is_array($image)) {
      $image = $image[0];
    }
  }
  if (!empty($kit_id)) {
    $kit = get_post($kit_id);
    if ($kit) {
      $image = wp_get_attachment_image_src(get_post_thumbnail_id($kit_id),'full');
      if (!empty($image) && is_array($image)) {
        $image = $image[0];
      }
      $name = $kit->post_title;
      $description = $kit->post_content;
      if (function_exists('get_field')) {
        // we need the pre header
        $pre_header = get_field('pre_header',$kit_id);
      }
    }

  }
  if (empty($kit_id) && empty($color_variation_image_overrides) && !empty($product_id) && function_exists('get_field')) {

    $color_variation_image_overrides = get_field('color_variation_image_overrides',$product_id);
    //print_r($color_variation_image_overrides) ;
    // we're also going to replace the featured image with the first color one.
    if (!empty($color_variation_image_overrides) && is_array($color_variation_image_overrides)) {
      $first_item = $color_variation_image_overrides[0];
      //print_r ($first_item);
      if (!empty($first_item) && is_array($first_item) && isset($first_item['image_override'])) {
        $image = $first_item['image_override']['url'];
      }
    }
  }
  if (!empty($product_cat) && is_object($product_cat) && isset($product_cat->term_id)) {
    $category_id = $product_cat->term_id;
  }

  if (!empty($product_id) && !empty($color_variation_image_overrides) && !empty($product_cat)) {
    $swatches = bl_get_bag_product_swatch_overrides($product_id,$category_id,$color_variation_image_overrides);
  }
  $bag_in_cart = bl_get_bag_from_cart();
  if(!empty($bag_in_cart) && is_array($bag_in_cart)) {
    $bag_id = $bag_in_cart['id'];
    $href = get_permalink($bag_id);
  }
  if (!empty($swatches) && !empty($bag_in_cart)) {
    $swatches_holder = array();
    foreach ($swatches as $swatch) {
      if ($swatch['id']==$bag_in_cart['id']) {
        $swatch['selected'] = true;
        $css .= ' hero-product-picked';
        $removecss = 'show';
        $picked = true;
      }
      $swatches_holder[] = $swatch;
    }
    $swatches = $swatches_holder;
  }
  //$swatches = bl_get_variable_product_swatches($product_id,$variation_id);
  if (!empty($swatches)) {
    $css .= ' has-variations';
  }
  $results[] = array(
        'preHeader' => $pre_header,
        'index' => $index,
        'product_id'=>$product_id,
        'category_id'=>$category_id,
        'triangleBackground' => true,
        'removecss' => $removecss,
        'css' => $css,
        'logo' => $logo,
        'header' => $name,
        'copy' => $description,
        'picked' => true,
        'href' => $href,
        'image' => $image,
        'bagURL' => $href,
        'button_copy' => 'Change Bag',
        'selected_copy' => 'Change Bag',
        'hover_details' => $hover_details,
        'disabled' => false,
        'swatches' => $swatches
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
