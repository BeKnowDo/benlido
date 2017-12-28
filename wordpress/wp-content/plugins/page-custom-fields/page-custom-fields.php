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
include_once ( plugin_dir_path(__FILE__) . '/acf-travel-kit.php');
