<?php
/*
Plugin Name: UPCo Performance Plugin
Plugin URI: http://www.urbanpixels.com
Description: This plugin provides developers to cache data no matter whether a memory caching service like memcache or redis is activated or not
Version: 0.0.2
Author: Urban Pixels
Author URI: http://www.urbanpixels.com
*/

// use this instead of transient ol wp_cache_get directly
global $upco_performance_admin_slug;
$upco_performance_admin_slug = 'upco-performance';

function upco_cache_key($key,$group='exp') {
    return 'upco_' . $group . $key;
}

// this gets the default cache group
// if you define UPCO_CACHE_GROUP in your wp-config.php, then the cache group will become that value
function upco_cache_group($group='upco_cache') {
    if (defined(UPCO_CACHE_GROUP)) {
      return UPCO_CACHE_GROUP;
    }
    return $group;
}

function upco_get_cache($key,$group='upco_cache',$force=false,&$found=null) {
  // first, see if we do have object cache
  // create a transient key with the group word
  $transient_key = upco_cache_group($group) . $key;
  if (defined('WP_CACHE') && WP_CACHE == true) {
    global $wp_object_cache;
    if (!empty($wp_object_cache->cache)) {
        return wp_cache_get($key,$group,$force,$found);
    } else {
      // going down to transient cache
      return get_transient($transient_key);
    } // end else object cache
  } else {
    return get_transient($transient_key);
  } // end if caching
  return null;
} // end upco_get_cache()

// use this instead of transient or wp_cache_set directly
function upco_set_cache($key, $data, $group='upco_cache', $expire=0) {
  // first, see if we do have object cache
  // create a transient key with the group word
  $transient_key = upco_cache_group($group) . $key;
  if (defined('WP_CACHE') && WP_CACHE == true) {
    // let's confirm caching
    global $wp_object_cache;
    if (!empty($wp_object_cache->cache)) {
        return wp_cache_set($key,$data,$group,$expire);
    } else {
      // going down to transient cache
      return set_transient($transient_key,$data,$expire);
    }
  } else {
    return set_transient($transient_key,$data,$expire);
  }
  return null;
} // end upco_set_cache()

function upco_delete_cache($key,$group='upco_cache') {
  // first, see if we do have object cache
  // create a transient key with the group word
  $transient_key = upco_cache_group($group) . $key;
  if (defined('WP_CACHE') && WP_CACHE == true) {
    // let's confirm caching
    global $wp_object_cache;
    if (!empty($wp_object_cache->cache)) {
        wp_cache_delete($key,$group);
    } else {
      // going down to transient cache
      delete_transient($transient_key);
    }
  } else {
    delete_transient($transient_key);
  }
  return null;
}

function upco_flush_cache($group='upco_cache') {
  if (defined('WP_CACHE') && WP_CACHE == true) {
    global $wp_object_cache;
    if (!empty($wp_object_cache->cache)) {
      wp_cache_flush();
    } else {
      global $wpdb;
      $sql = "delete from {$wpdb->options} where option_name like '_transient_" . upco_cache_group($group) . "%' or option_name like '_transient_timeout_" . $group . "%' ";
      $wpdb->query($sql);
    }
  } else {
      global $wpdb;
      $sql = "delete from {$wpdb->options} where option_name like '_transient_" . upco_cache_group($group) . "%' or option_name like '_transient_timeout_" . $group . "%' ";
      $wpdb->query($sql);
  }
}

function upco_performance_admin() {
  global $upco_performance_admin_slug;
  add_options_page('Performance Admin', 'Performance Admin', 'manage_options', $upco_performance_admin_slug, 'upco_performance_edit');
}
add_action('admin_menu', 'upco_performance_admin');


function upco_performance_edit() {
  global $upco_performance_admin_slug;
  $message = '';

  if (isset($_POST['clear_bad_items'])) {
    if (function_exists('upco_flush_cache')) {
      upco_flush_cache();
    }
    $message = 'DONE';
  } // end if post

?>
<form method="post" enctype="multipart/form-data" action="admin.php?page=<?php echo $upco_performance_admin_slug?>">
    <h3><span>Performance Admin</span></h3>
    <div class="postbox">
      <div class="message"><?php echo $message?></div>
      <div class="inside">
        <h4>Clear Cache and bad product variations</h4>
        <input type="submit" name="clear_bad_items" value="Clear" />
      </div>
    </div>
</form>
<?php
}
