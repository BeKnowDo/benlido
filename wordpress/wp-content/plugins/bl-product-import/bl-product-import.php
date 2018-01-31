<?php
/*
Plugin Name: Ben Lido Product Import
Plugin URI: http://www.benlido.com/
Description: Import products from a spreadsheet and match to ASIN
Version: 1.0
*/

global $bl_product_import_admin_slug;
$bl_product_import_admin_slug = 'bl-product-import';
global $bl_stored_product_array;
$bl_stored_product_array = 'bl-stored-products';
global $bl_product_import_api_slug;
$bl_product_import_api_slug = '/bl-product-import-api';

function bl_product_import_settings() {
    global $bl_product_import_admin_slug;
    global $bl_product_import_api_slug;
    global $bl_stored_product_array;
    $message = '';
    $script = '';
    
    if (!empty($_POST)) {
        $data = array();
        $tmp_name = $_FILES['bl_inv_import']['tmp_name'];
        if (!empty($tmp_name)) {
            if (function_exists('bl_parse_csv')) {
                $data = bl_parse_csv($tmp_name);
                //print_r ($data);
                //die;

            }
        }
        if (!empty($data) && is_array($data)) {
            // saving data for ajax
            update_option($bl_stored_product_array,$data);
            $script = '<script>
                function bl_ajax_import_item() {

                    jQuery.post(
                        "' . $bl_product_import_api_slug . '",
                        function(res) {
                            var message = res.message + " : " + res.name;
                            jQuery("ul.line-results").append("<li>"+message+"</li>");
                            if (res.has_more == true) {
                                bl_ajax_import_item();
                            }
                        },
                        "json"
                    );
                }

                jQuery("document").ready(
                    function($) {
                        bl_ajax_import_item();
                    }
                );

            </script>';
        }
    } // end $_POST

?>
    <form method="post" enctype="multipart/form-data" action="admin.php?page=<?php echo $bl_product_import_admin_slug?>">
    <h3><span>Product Import</span></h3>
    <div class="postbox">
      <div class="inside">
        <?php echo $message ?>
        <p>NOTE: 
            Please use the "Save as" functionality in Excel, and save the spreadsheet as a "csv". If the spreadsheet is not saved as a "csv", the import will not work.
        </p>
        <label for="blush_inv_import">Import Products:</label><br />
        <input type="file" id="bl_inv_import" name="bl_inv_import" /><br />
        <input type="submit" name="upload" value="Import" />
      </div>
    </div>
    <div class="postbox">
        <div class="inside">
        <ul class="line-results">
        </ul>
        </div>
    </div>
    <?php echo $script;?>
</form>
<?php

} // end bl_product_import_settings()

function bl_product_import_admin() {
    global $bl_product_import_admin_slug;
    add_submenu_page('edit.php?post_type=product', 'Product Import',  'Product Import', 'manage_options', $bl_product_import_admin_slug, 'bl_product_import_settings');
}
  
add_action('admin_menu', 'bl_product_import_admin');

if (!function_exists('bl_parse_csv')) {


function bl_parse_csv($csv)
{
        $row = 0;
        $keys = array();
        $csvData = array();
        ini_set('auto_detect_line_endings',TRUE);
        if (($handle = fopen($csv, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 2500, ",")) !== FALSE) {
                        $row++;
                        if ($row == 1) {
                                $keys = $data;
                                foreach ($keys as $keyIdx => $key) {
                                    $key = bl_remove_utf8_bom($key);
                                    $key = str_replace('.','',strtolower($key));
                                    $keys[$keyIdx] = str_replace(' ','_',trim(strtolower($key)));
                                }
                                continue;
                        }

                        $num = count($data);

                        if (empty($data[0]))
                                continue;

                        $rowData = array();
                        for ($c = 0; $c < $num; $c++) {
                                $rowData[$keys[$c]] = $data[$c];
                        }

                        $csvData[] = $rowData;
                }
                fclose($handle);
        }

        return $csvData;


} // end bl_parse_csv()

function bl_remove_utf8_bom($text)
{
    $bom = pack('H*','EFBBBF');
    $text = preg_replace("/^$bom/", '', $text);
    return $text;
}

} // end if not function_exists

function bl_create_product($data) {
    //print_r ($data);
    //error_log(json_encode($data));
    $match_by = 'upc';
    $product_id = 0;
    $res = array();
    $sku = $data['id'];
    $upc_code = $data['upc_code'];
    // clean UPC
    $upc_code = bl_distill_upc($upc_code);
    $amazon_asin = $data['amazon_asin'];
    $name = $data['name'];
    if (!empty($data['display_name'])) {
        $name = $data['display_name'];
    }
    
    $size_display_label = $data['disp_size_style'];
    $in_stock = $data['stocked'];
    $description = '';
    $short_description = '';
    $product_type = 'simple';
    $tsa_compliant = $data['tsa_compliant'];
    if ($tsa_compliant == 'Y') {
        $tsa_compliant = 'Yes';
    } else {
        $tsa_compliant = 'No';
    }
    $product_width = $data['product_width'];
    $product_height = $data['product_height'];
    $price = $data['unit_sell_price'];
    
    $weight = $data['weight'];
    $categories = $data['categories'];
    //$sku = $data['sku'];
    // let's see if the SKU exists
    $test_prod = wc_get_product_id_by_sku($sku);
    if (!empty($test_prod)) {
        $res['product_id'] = $test_prod;
        $res['error'] = 'NO UPDATE: Product Exists.';
    }
    if (empty($test_prod)) {
        // look for ASIN
        if (!empty($amazon_asin)) {
            $match_by = 'asin';
            $aws_prod = bl_search_aws_by_asin($amazon_asin);
        } elseif (!empty($upc_code)) {
            $aws_prod = bl_search_aws_by_upc($upc_code);
        } else {
            $match_by = 'search';
            $aws_prod = bl_search_aws_by_name_and_size($name,$size_display_label);
        }
        //print_r ($aws_prod);
        //die;
        // process things out
        // we use the description from Amazon
        $description = $aws_prod['description'];
        $image = $aws_prod['image'];
        $brand = $aws_prod['brand'];
        $aws_upc = $aws_prod['upc'];
        $product_width = $aws_prod['width'];
        $product_depth = $aws_prod['length'];
        $product_height = $aws_prod['height'];
        if ($price <= 0) {
            $price = $aws_prod['price'];
        }
        
        // sanity check
        $should_proceed = true;
        if ($match_by == 'upc' && !empty($aws_upc) && $aws_upc != $upc_code) {
            $res['error'] = 'UPC MisMatch! Closest Product: <a href="' . $aws_prod['url'] . '" target="_blank">' . $aws_upc . '</a>';
            $should_proceed = false;
        }
        if (!empty($aws_prod['error'])) {
            $should_proceed = false;
            $res['missed'] = true;
            $res['error'] = $aws_prod['error'];
            $res['success'] = false;
        }
        if ($product_type == 'simple' && $should_proceed !== false) {
            $product_id = bl_create_simple_product($name,$sku,$price,$description,$short_description);
            $res['product_id'] = $product_id;
            $res['missed'] = false;
            $res['success'] = true;
        } else {
            $res['missed'] = true;
            $res['success'] = false;
        }
    } else {
        $product_id = $test_prod;
        $res['product_id'] = $product_id;
    }
    // import image
    if ($product_id > 0 && strlen($image)>0 && !empty($sku)) {
        // we'll import only if there's no image
        $has_image = get_the_post_thumbnail_url($product_id);
        if ($has_image == false) {
            $id = bl_import_image($sku,$image,$product_id);
            $res['image_id'] = $id;
            $res['name'] = $name;
        }
    }
    


    if ($product_id > 0) {
        if (empty($amazon_asin)) {
            $amason_asin = $aws_prod['asin'];
        }
        if (!empty($brand)) {
            bl_product_update_brand($brand,$product_id);
        }
        $amazon_product_url = $aws_prod['url'];
        bl_insert_product_acf('upc_code',$upc_code,$product_id);
        bl_insert_product_acf('size_display_label',$size_display_label,$product_id);
        bl_insert_product_acf('amazon_asin',$amazon_asin,$product_id);
        bl_insert_product_acf('amazon_product_url',$amazon_product_url,$product_id);
        bl_product_update_tsa_compliant($tsa_compliant,$product_id);
        // create product categories
        $cats = $aws_prod['categories'];
        if (!empty($cats) && is_array($cats)) {
            $term_ids = array();
            foreach ($cats as $cat) {
                //print_r ($cat);
                // adding to make sure we have the term
                $term_ids[] = bl_product_update_category($cat,$product_id);
            }
            // the real adding of the terms
            /*
            if (!empty($term_ids)) {
                wp_set_object_terms($product_id,$term_ids,'prod_cat');
            }
            */
        }
        $prod = wc_get_product($product_id);
        if ($prod) {
            // width, height, 
            if (!empty($weight)) {
                // we're going to say the smallest is 0.1lb
                if ($weight < 0.1) {
                    $weight = 0.1;
                }
                $prod->set_weight($weight);
            }
            if (!empty($product_width)) {
                $prod->set_height($product_width);
            }
            if (!empty($product_depth)) {
                $prod->set_length($product_depth);
            }
            if (!empty($product_height)) {
                $prod->set_width($product_height);
            }
            $prod->save();
        }
    }
    return $res;
} // end bl_create_product()

function bl_insert_product_acf($field_name,$field_value,$product_id) {
    // for ACF if you are creating the field for the first, time you need to use the field key
    // so, this looks for the field key and uses that to create the field
    
    $product_group = 'group_5a6e2c1bcf057';
    // NOTE: there is a chance that the product gorup hash might change
    if (function_exists('get_field_object')) {
        //error_log('getting acf field object');
        $key = bl_get_product_acf_field_key($field_name,$product_group);
        if (!empty($key)) {
            update_field($key,$field_value,$product_id);
        }
    }

}

function bl_get_product_acf_field_key($field_name,$group) {
    $local_groups = acf_local();
    $acf_fields = $local_groups->fields;
    foreach ($acf_fields as $field_key => $obj) {
        
        if ($obj['parent'] == $group) {
            //error_log(json_encode($obj));
            if ($obj['name'] == $field_name) {
                return $obj['key'];
            }
        }
    }
}

function bl_create_simple_product($name,$sku,$price,$description,$short_description) {
    $post_id = 0;
    if (!empty($name) && !empty($sku)) {
        $args = array(	   
            'post_author' => 1, 
            'post_content' => $description,
            'post_excerpt' => $short_description,
            'post_status' => "publish", // (Draft | Pending | Publish)
            'post_title' => $name,
            'post_parent' => '',
            'post_type' => "product"
        ); 
        //print_r ($args);
        // Create a simple WooCommerce product
        $post_id = wp_insert_post( $args );
        if (!$post_id) {
            return 0;
        }
        update_post_meta($post_id,'_sku',$sku);
        // Setting the product type
        wp_set_object_terms( $post_id, 'simple', 'product_type' );
        // Setting the product price
        update_post_meta( $post_id, '_price', floatval($price) );
        update_post_meta( $post_id, '_regular_price', floatval($price) );


    }

    return $post_id;
}

function bl_product_update_tsa_compliant($term,$product_id) {
    $obj = get_term_by('name', $term, 'tsa_compliant');
    $term_id = 0;
    if (empty($obj)) {
        $arr = wp_insert_term( $term, 'tsa_compliant');
        if (is_array($arr) && !empty($arr['term_id'])) {
            $term_id = $arr['term_id'];
        }
    } else {
        $term_id = $obj->term_id;
    }

    if ($term_id > 0) {
        wp_set_object_terms($product_id, $term_id, 'tsa_compliant');
    }
}

function bl_product_update_category($term,$product_id) {
    $obj = get_term_by('name', trim($term), 'product_cat');
    $term_id = 0;
    if (empty($obj)) {
        $arr = wp_insert_term( $term, 'product_cat');
        if (is_array($arr) && !empty($arr['term_id'])) {
            $term_id = $arr['term_id'];
        }
    } else {
        $term_id = $obj->term_id;
    }

    if ($term_id > 0) {
        wp_set_object_terms($product_id, $term_id, 'product_cat',true);
    }
    return $term_id;
}

function bl_product_update_brand($term,$product_id) {
    $obj = get_term_by('name', trim($term), 'product_brand');
    $term_id = 0;
    if (empty($obj)) {
        $arr = wp_insert_term( $term, 'product_brand');
        if (is_array($arr) && !empty($arr['term_id'])) {
            $term_id = $arr['term_id'];
        }
    } else {
        $term_id = $obj->term_id;
    }

    if ($term_id > 0) {
        wp_set_object_terms($product_id, $term_id, 'product_brand',false);
    }
    return $term_id;
}

function bl_import_image($sku, $image_url,$product_id) {
    $attach_id = 0;
    $timeout_seconds = 5;
    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    require_once(ABSPATH . "wp-admin" . '/includes/media.php');
    $temp_file = download_url( $image_url, $timeout_seconds );
    //error_log(json_encode(parse_url($image_url)));
    if ( !is_wp_error( $temp_file ) ) {
        // Array based on $_FILE as seen in PHP file uploads
        $new_name = sanitize_title($sku);
        $fname = basename($image_url);
        $filetype = wp_check_filetype( basename( $image_url ), null );
        $new_filename = $new_name . '.' . $filetype['ext']; 
        $file = array(
            'name'     => $new_filename, // ex: wp-header-logo.png
            'type'     => $filetype['type'],
            'tmp_name' => $temp_file,
            'error'    => 0,
            'size'     => filesize($temp_file),
        );

        $overrides = array(
            // Tells WordPress to not look for the POST form
            // fields that would normally be present as
            // we downloaded the file from a remote server, so there
            // will be no form fields
            // Default is true
            'test_form' => false,

            // Setting this to false lets WordPress allow empty files, not recommended
            // Default is true
            'test_size' => true,
        );

        $results = wp_handle_sideload( $file, $overrides );

        if ( !empty( $results['error'] ) ) {
            // Insert any error handling here
        } else {
    
            $filename  = $results['file']; // Full path to the file
            $local_url = $results['url'];  // URL to the file in the uploads dir
            $type      = $results['type']; // MIME type of the file
    
            // Perform any actions here based in the above results
            $args = array(
                'post_title'     => $sku,
                'guid'           => $local_url, 
                'post_content'   => '',
                'post_mime_type' => $type,
                'post_status'    => 'inherit'
            );
            $attach_id = wp_insert_attachment( $args, $filename, $product_id );
            //error_log('new image id: ' . $attach_id);
            $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
            wp_update_attachment_metadata( $attach_id, $attach_data );
            set_post_thumbnail( $product_id, $attach_id );
        }


    } // end if no error

    return $attach_id;
}

// this is because there are dirty characters in the UPC field
function bl_distill_upc($upc) {
    $upc = trim($upc);
    $upc = str_replace("'","",$upc);
    return $upc;
}

function bl_search_aws_by_asin($asin) {
    require_once ( plugin_dir_path(__FILE__) . '/BLApaio.php' );
    if (function_exists('get_field')) {
        $access_key = get_field('amazon_access_key','option');
        $secret_key = get_field('amazon_secret_key','option');
        $associate_tag = get_field('amazon_associate_tag','option');
    }
    $aws = new BLApaio($access_key,$secret_key,$associate_tag);
    $prod = $aws->_getByASIN($asin);
    $item = bl_distill_aws_product($prod);
    return $item;
}

function bl_search_aws_by_upc($upc) {
    $final_prod = array();
    require_once ( plugin_dir_path(__FILE__) . '/BLApaio.php' );
    if (function_exists('get_field')) {
        $access_key = get_field('amazon_access_key','option');
        $secret_key = get_field('amazon_secret_key','option');
        $associate_tag = get_field('amazon_associate_tag','option');
    }
    $aws = new BLApaio($access_key,$secret_key,$associate_tag);
    $prod = $aws->_getByUPC($upc);
    // clean up for WooCommerce
    //print_r ($prod);
    $item = bl_distill_aws_product($prod);
    //print_r ($item);
    //die;
    return $item;
}

function bl_search_aws_by_name_and_size($name,$size_display_label) {
    require_once ( plugin_dir_path(__FILE__) . '/BLApaio.php' );
    if (function_exists('get_field')) {
        $access_key = get_field('amazon_access_key','option');
        $secret_key = get_field('amazon_secret_key','option');
        $associate_tag = get_field('amazon_associate_tag','option');
    }
    $aws = new BLApaio($access_key,$secret_key,$associate_tag);
    $prod = $aws->_search($name,$size);
    //print_r ($prod);
    $item = bl_distill_aws_product($prod);
    return $item;
}

function bl_get_parent_cats($obj) {
    if (!empty($obj['BrowseNode'])) {
        $obj = $obj['BrowseNode'];
    }
    $res = array('more'=> false,'name'=>'','parent'=>array());
    $name = $obj['Name'];
    $parent = $obj['Ancestors'];
    if (!empty($parent)) {
        $res['more'] = true;
    }
    $res['parent'] = $parent;
    $res['name'] = $name;
    return $res;
}

function bl_distill_aws_product($prod) {
    //print_r ($prod);
    //die;
    $error = '';
    $asin = $prod['ASIN'];
    $image = $prod['LargeImage'];
    $amazon_url = $prod['DetailPageURL'];
    if (!empty($prod['ImageSets']['ImageSet']['HiResImage'])) {
        $image = $prod['ImageSets']['ImageSet']['HiResImage'];
    }
    if (!empty($image['URL'])) {
        $image = $image['URL'];
    }
    if (!empty($prod['ItemAttributes'])) {
        $ItemAttributes = $prod['ItemAttributes'];
        $ListPrice = $ItemAttributes['ListPrice'];
        $Manufacturer = $ItemAttributes['Manufacturer'];
        $UPC = $ItemAttributes['UPC'];
        $Feature = $ItemAttributes['Feature'];
        $PackageDimensions = $ItemAttributes['PackageDimensions'];
    }
    if (!empty($prod['BrowseNodes'])) {
        $BrowseNodes = $prod['BrowseNodes'];
        $BrowseNode = $BrowseNodes['BrowseNode'];
        // current category

        $current_cat = $BrowseNode['Name'];
        $parent_cats = $BrowseNode['Ancestors'];
    }
    if (!empty($ListPrice) && !empty($ListPrice['Amount'])) {
        $price = floatval($ListPrice['Amount']/100);
    }
    if (!empty($Feature) && is_array($Feature)) {
        $description = implode('<br />',$Feature);
    }

    if (!empty($PackageDimensions)) {
        // hundredths of an inch
        // hundredths of a pound
        $Height = $PackageDimensions['Height'];
        $Length = $PackageDimensions['Length'];
        $Weight = $PackageDimensions['Weight'];
        $Width = $PackageDimensions['Width'];
        if ($Height > 0) {
            $height = $Height / 100;
        }
        if ($Length > 0) {
            $length = $Length / 100;
        }
        if ($Width > 0) {
            $width = $Width / 100;
        }
        if ($Weight > 0) {
            $weight = $Weight / 100;
        }
    }

    if (!empty($prod['error'])) {
        $error = $prod['error'];
    }

    // Category stuff... we're going to make them all parent categories for now.
    $categories = array();
    if (!empty($current_cat)) {
        $current_cat = trim($current_cat);
        $categories[] = $current_cat;
    }
    if (!empty($parent_cats)) {
        $limit = 100; // maximum of 100 loops
        while ($limit != 0) {
            //print_r ($parent_cats);
            $res = bl_get_parent_cats($parent_cats);
            //print_r ($res);
            //die;
            $categories[] = trim($res['name']);
            if ($res['more'] == true) {
                $parent_cats = $res['parent'];
            } else {
                break;
            }
        } // end while
    }
    if (empty($parent_cats) && empty($BrowseNode['Name']) && !empty($BrowseNode[0]['Name'])) {

        // we need to iterate to get the parents
        foreach ($BrowseNode as $single_node) {
            if ($single_node['Name']) {
                $categories[] = trim($single_node['Name']);
            }
            $ancestors = $single_node['Ancestors'];
            $limit = 100;
            while($limit != 0) {
                $res = bl_get_parent_cats($ancestors);
                $categories[] = trim($res['name']);
                if ($res['more'] == true) {
                    $ancestors = $res['parent'];
                } else {
                    break;
                }
            } // end while
            
        } // end foreach

        $categories = array_unique($categories);
    }

    $final_prod = array(
        'asin' => $asin,
        'image' => $image,
        'price' => $price,
        'url' => $amazon_url,
        'brand' => $Manufacturer,
        'upc' => $UPC,
        'description' => $description,
        'width' => $width,
        'height' => $height,
        'length' => $length,
        'weight' => $weight,
        'categories' => $categories,
        'error' => $error
    );
    //print_r ($final_prod);
    return $final_prod;
}

function bl_product_import_url_intercept()
{
  global $bl_product_import_api_slug;
  global $bl_stored_product_array;
    // salesforce API URL match
    if (strlen(@stristr($_SERVER['REQUEST_URI'], $bl_product_import_api_slug)) > 0) {

        if (isset($_POST)) {
            $resp = array('success'=>false,'has_more'=>false,'message'=>'','name'=>'');
            //error_log('POST');
            $data = get_option($bl_stored_product_array);
            if (!empty($data) && is_array($data)) {
                $row = array_shift($data);
                update_option($bl_stored_product_array,$data);
            }
            if (!empty($row)) {
                sleep(0.5);
                $resp = bl_create_product($row);
                $resp['name'] = $row['name'];
            }
            if (!empty($data)) {
                $resp['has_more'] = true;
            }
            if ($resp['missed'] == true) {
                $resp['message'] = 'MISSED ' . $resp['error'];
                $resp['success'] = false;
            } else {
                $resp['message'] = 'IMPORTED ';
                $resp['success'] = true;
            }
            header('Content-Type: application/json');
            
            print_r (json_encode($resp));
        }

      die;
    } else {
    }

}

add_action('parse_request', 'bl_product_import_url_intercept');