<?php

require_once 'inc/storefront-overrides.php';

function bl_child_theme_init()
{
    global $storefront_version;
    $version = '1.0';
    wp_dequeue_style( 'storefront-style' );
    wp_dequeue_style( 'storefront-woocommerce-style' );
    wp_dequeue_style( 'storefront-woocommerce-brands-style' );
    wp_register_style( 'parent-storefront-style',get_template_directory_uri() . '/style.css');
    wp_style_add_data( 'parent-storefront-style', 'rtl', 'replace' );

    wp_enqueue_style( 'parent-storefront-woocommerce-style', get_template_directory_uri() . '/assets/sass/woocommerce/woocommerce.css', array(), $storefront_version );
    wp_style_add_data( 'parent-storefront-woocommerce-style', 'rtl', 'replace' );

    wp_register_style('bl-fonts', '//cloud.typography.com/7086216/6631592/css/fonts.css', array(), $version, 'all');
    wp_register_style('bl-style', get_stylesheet_directory_uri() . '/css/style.css', array(), $version, 'all');
    //wp_enqueue_script('bl-scripts-libs', get_stylesheet_directory_uri() . '/js/bl.libs.js', array('jquery'),$version);
    wp_enqueue_script('bl-no-uglify-scripts-libs', get_stylesheet_directory_uri() . '/js/bl.main.js', array('jquery'),$version,true);
    wp_enqueue_script('bl-scripts', get_stylesheet_directory_uri() . '/js/bl.min.js', array('jquery'),$version);
    wp_enqueue_style('parent-storefront-style');
    wp_enqueue_style('parent-storefront-woocommerce-style');
    wp_enqueue_style('bl-fonts'); // Enqueue it!
    wp_enqueue_style('bl-style'); // Enqueue it!

    // This theme uses wp_nav_menu() in one location.



}
add_action('wp_enqueue_scripts', 'bl_child_theme_init', 90);

function bl_after_theme_setup() {
    register_nav_menus( array(
        'social-menu' => esc_html__( 'Social Media Menu', 'benlido'),
        'search-left' => esc_html__( 'Search Menu Left', 'benlido'),
        'search-right' => esc_html__( 'Search Menu Right', 'benlido'),
        'footer' => esc_html__( 'Footer Menu', 'benlido'),
    ) );
    add_action('after_setup_theme','bl_after_theme_setup_deferred'); // this is the trick to override after_setup_theme in the parent theme
}
function bl_after_theme_setup_deferred() {
    remove_theme_support( 'custom-logo' );
    add_theme_support( 'custom-logo', array(
        'flex-width'  => true,
        'flex-height'  => true,
    ) );
}

add_action('after_setup_theme','bl_after_theme_setup',0);
