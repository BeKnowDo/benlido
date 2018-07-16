<?php
// we start the function by making sure we have Timber and twig set up
if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
		echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php') ) . '</a></p></div>';
	});
	
	return;
}
Timber::$dirname = array('twig-templates', 'views');

// NOTE: woocommerce and storefront add_action and remove_action calls are in inc/storefront-overrides.php
require_once 'inc/storefront-overrides.php'; // overriding the storefront parent theme
require_once 'inc/product-overrides.php'; // overriding the product, product-detail, and category displays
require_once 'inc/twig-template-pivots.php'; // the pivot file to modify variables from woocommerce to Twig
require_once 'inc/template-functions.php'; // functions for displaying things: like navigation, etc.


function bl_child_theme_init()
{
    global $storefront_version;
    $version = '1.5';
    wp_dequeue_style( 'storefront-style' );
    wp_dequeue_style( 'storefront-woocommerce-style' );
    wp_dequeue_style( 'storefront-woocommerce-brands-style' );
    wp_register_style( 'parent-storefront-style',get_template_directory_uri() . '/style.css');
    wp_style_add_data( 'parent-storefront-style', 'rtl', 'replace' );

    wp_enqueue_style( 'parent-storefront-woocommerce-style', get_template_directory_uri() . '/assets/sass/woocommerce/woocommerce.css', array(), $storefront_version );
    wp_style_add_data( 'parent-storefront-woocommerce-style', 'rtl', 'replace' );

    wp_register_style('bl-fonts', '//cloud.typography.com/7086216/6631592/css/fonts.css', array(), $version, 'all');
    //wp_register_style('bl-style-libs', get_stylesheet_directory_uri() . '/css/bl.libs.css', array(), $version, 'all');
    wp_register_style('bl-style', get_stylesheet_directory_uri() . '/assets/styles/styles.css', array('bl-fonts'), $version, 'all');
    //wp_enqueue_script('bl-scripts-libs', get_stylesheet_directory_uri() . '/js/bl.libs.js', array('jquery'),$version);
    wp_enqueue_script('bl-scripts', get_stylesheet_directory_uri() . '/assets/javascript/main.js', array('jquery'), $version,true);

    // removing default storefront styles.. this may cause issues later on with inherited dropdowns, error messages, etc. We will need to investigate this.
    //wp_enqueue_style('parent-storefront-style');
    wp_enqueue_style('parent-storefront-woocommerce-style');
    wp_enqueue_style('bl-style-libs'); // Enqueue it!
    wp_enqueue_style('bl-fonts'); // Enqueue it!
    wp_enqueue_style('bl-style'); // Enqueue it!

    // This theme uses wp_nav_menu() in one location.

    remove_theme_support( 'wc-product-gallery-zoom' );
    remove_theme_support( 'wc-product-gallery-lightbox' );
    remove_theme_support( 'wc-product-gallery-slider' );


}
add_action('wp_enqueue_scripts', 'bl_child_theme_init', 90);

function bl_after_theme_setup() {
    register_nav_menus( array(
        'social-menu' => esc_html__( 'Social Media Menu - Header', 'benlido'),
        'social-menu-footer' => esc_html__( 'Social Media Menu - Footer', 'benlido'),
        'search-left' => esc_html__( 'Search Menu Left', 'benlido'),
        'search-right' => esc_html__( 'Search Menu Right', 'benlido'),
        'footer' => esc_html__( 'Footer Menu', 'benlido'),
        'contact' => esc_html__( 'Contact Menu', 'benlido'),
    ) );
    add_action('after_setup_theme','bl_after_theme_setup_deferred'); // this is the trick to override after_setup_theme in the parent theme
    
}
function bl_after_theme_setup_deferred() {
    remove_theme_support( 'custom-logo' );
    add_theme_support( 'custom-logo', array(
        'flex-width'  => true,
        'flex-height'  => true,
    ) );
    add_action('widgets_init','bl_remove_widget_areas',100); // removing the footer widgets
    unregister_nav_menu( 'handheld' );
    unregister_nav_menu( 'secondary' );
}

function bl_remove_widget_areas() {
    unregister_sidebar('footer-1');
    unregister_sidebar('footer-2');
    unregister_sidebar('footer-3');
    unregister_sidebar('footer-4');
}

function removebadsticky_woocommerce_scripts() {
	wp_deregister_script( 'storefront-sticky-payment');
}

add_action( 'wp_enqueue_scripts', 'removebadsticky_woocommerce_scripts' , 90 );
add_action('after_setup_theme','bl_after_theme_setup',0);
//add_action('childtheme_sidebars','bl_remove_widget_areas');
