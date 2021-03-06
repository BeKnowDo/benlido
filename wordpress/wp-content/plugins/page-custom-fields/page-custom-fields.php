<?php
/*
Plugin Name: ACF for Ben Lido Pages
Plugin URI: http://www.benlido.com/
Description: ACF for Pages Plugin
Version: 1.0
*/

if( function_exists('acf_add_options_page') ) {
        acf_add_options_page();
}

include_once ( plugin_dir_path(__FILE__) . '/acf-options.php');
include_once ( plugin_dir_path(__FILE__) . '/acf-page.php');
include_once ( plugin_dir_path(__FILE__) . '/acf-shop-landing.php');
include_once ( plugin_dir_path(__FILE__) . '/acf-travel-kit.php');
//include_once ( plugin_dir_path(__FILE__) . '/acf-coming-soon.php');
include_once ( plugin_dir_path(__FILE__) . '/acf-product.php');
include_once ( plugin_dir_path(__FILE__) . '/acf-user.php');
include_once ( plugin_dir_path(__FILE__) . '/acf-home.php');
include_once ( plugin_dir_path(__FILE__) . '/acf-bags.php');
include_once ( plugin_dir_path(__FILE__) . '/acf-kitting.php');
include_once ( plugin_dir_path(__FILE__) . '/acf-frequency.php');
include_once ( plugin_dir_path(__FILE__) . '/acf-about.php');
include_once ( plugin_dir_path(__FILE__) . '/acf-help.php');
