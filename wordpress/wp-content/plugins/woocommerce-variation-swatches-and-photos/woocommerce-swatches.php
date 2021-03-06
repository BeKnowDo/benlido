<?php

/*
  Plugin Name: WooCommerce Variation Swatches and Photos
  Plugin URI: http://woothemes.com/woocommerce/
  Description: WooCommerce Swatches and Photos allows you to configure colors and photos for shoppers on your site to use when picking variations. Requires WooCommerce 1.5.7+
  Version: 2.1.4
  Author: Lucas Stark
  Author URI: http://lucasstark.com
  Requires at least: 3.5
  Tested up to: 4.4

  Copyright: © 2009-2016 Lucas Stark.
  License: GNU General Public License v3.0
  License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 * Required functions
 */
if ( !function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '37bea8d549df279c8278878d081b062f', '18697' );



if ( is_woocommerce_active() ) {

	require 'classes/class-wc-swatches-compatibility.php';

	add_action( 'init', 'wc_swatches_and_photos_load_textdomain', 0 );

	function wc_swatches_and_photos_load_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wc_swatches_and_photos' );
		load_textdomain( 'wc_swatches_and_photos', WP_LANG_DIR . '/woocommerce/wc_swatches_and_photos-' . $locale . '.mo' );
		load_plugin_textdomain( 'wc_swatches_and_photos', false, plugin_basename( dirname( __FILE__ ) ) . '/i18n/languages' );
	}

	add_action( 'plugins_loaded', 'wc_swatches_on_plugin_loaded' );

	function wc_swatches_on_plugin_loaded() {
		if ( (apply_filters( 'woocommerce_swatches_load_previous_version', false ) === false) && WC_Swatches_Compatibility::is_wc_version_gte_2_4() ) {

			class WC_SwatchesPlugin {

				private $product_attribute_images;

				public function __construct() {

					define( 'WC_SWATCHES_VERSION', '2.0.0' );

					require 'woocommerce-swatches-template-functions.php';

					require 'classes/class-wc-swatch-term.php';
					require 'classes/class-wc-swatch-product-term.php';
					require 'classes/class-wc-swatches-product-attribute-images.php';
					require 'classes/class-wc-ex-product-data-tab.php';
					require 'classes/class-wc-swatches-product-data-tab.php';
					require 'classes/class-wc-swatch-attribute-configuration.php';

					require 'classes/class-wc-swatches-ajax-handler.php';

					add_action( 'init', array(&$this, 'on_init') );

					add_action( 'wc_quick_view_enqueue_scripts', array($this, 'on_enqueue_scripts') );
					add_action( 'wp_enqueue_scripts', array(&$this, 'on_enqueue_scripts') );


					add_action( 'admin_head', array(&$this, 'on_enqueue_scripts') );

					$this->product_attribute_images = new WC_Swatches_Product_Attribute_Images( 'swatches_id', 'swatches_image_size' );
					$this->product_data_tab = new WC_Swatches_Product_Data_Tab();

					//Swatch Image Size Settings
					add_filter( 'woocommerce_catalog_settings', array(&$this, 'swatches_image_size_setting') ); // pre WC 2.1
					add_filter( 'woocommerce_product_settings', array(&$this, 'swatches_image_size_setting') ); // WC 2.1+
					add_filter( 'woocommerce_get_image_size_swatches', array($this, 'get_image_size_swatches') );
				}

				public function on_init() {
					global $woocommerce;
					$image_size = get_option( 'swatches_image_size', array() );
					$size = array();

					$size['width'] = isset( $image_size['width'] ) && !empty( $image_size['width'] ) ? $image_size['width'] : '32';
					$size['height'] = isset( $image_size['height'] ) && !empty( $image_size['height'] ) ? $image_size['height'] : '32';
					$size['crop'] = isset( $image_size['crop'] ) ? $image_size['crop'] : 1;

					$image_size = apply_filters( 'woocommerce_get_image_size_swatches_image_size', $size );

					add_image_size( 'swatches_image_size', apply_filters( 'woocommerce_swatches_size_width_default', $image_size['width'] ), apply_filters( 'woocommerce_swatches_size_height_default', $image_size['height'] ), $image_size['crop'] );
				}

				public function on_enqueue_scripts() {
					global $pagenow, $wp_scripts;

					if ( !is_admin() ) {
						wp_enqueue_style( 'swatches-and-photos', $this->plugin_url() . '/assets/css/swatches-and-photos.css' );
						wp_enqueue_script( 'swatches-and-photos', $this->plugin_url() . '/assets/js/swatches-and-photos.js', array('jquery'), '1.5.0', true );

						$data = array(
						    'ajax_url' => admin_url( 'admin-ajax.php' )
						);

						wp_localize_script( 'swatches-and-photos', 'wc_swatches_params', $data );
					}

					if ( is_admin() && ( $pagenow == 'post-new.php' || $pagenow == 'post.php' || $pagenow == 'edit.php' || 'edit-tags.php') ) {
						wp_enqueue_media();
						wp_enqueue_style( 'swatches-and-photos', $this->plugin_url() . '/assets/css/swatches-and-photos.css' );
						wp_enqueue_script( 'swatches-and-photos-admin', $this->plugin_url() . '/assets/js/swatches-and-photos-admin.js', array('jquery'), '1.0', true );

						wp_enqueue_style( 'colourpicker', $this->plugin_url() . '/assets/css/colorpicker.css' );
						wp_enqueue_script( 'colourpicker', $this->plugin_url() . '/assets/js/colorpicker.js', array('jquery') );


						$data = array(
						    'placeholder_img_src' => WC()->plugin_url() . '/assets/images/placeholder.png'
						);

						wp_localize_script( 'swatches-and-photos-admin', 'wc_swatches_params', $data );
					}
				}

				public function plugin_url() {
					return untrailingslashit( plugin_dir_url( __FILE__ ) );
				}

				public function plugin_dir() {
					return plugin_dir_path( __FILE__ );
				}

				public function swatches_image_size_setting( $settings ) {
					$setting = array(
					    'name' => __( 'Swatches and Photos', 'wc_swatches_and_photos' ),
					    'desc' => __( 'The default size for color swatches and photos.', 'wc_swatches_and_photos' ),
					    'id' => 'swatches_image_size',
					    'css' => '',
					    'type' => 'image_width',
					    'std' => '32',
					    'desc_tip' => true,
					    'default' => array(
						'crop' => true,
						'width' => 32,
						'height' => 32
					    )
					);

					$index = count( $settings ) - 1;

					$settings[$index + 1] = $settings[$index];
					$settings[$index] = $setting;
					return $settings;
				}

				public function get_image_size_swatches( $size ) {
					$image_size = get_option( 'swatches_image_size', array() );
					$size = array();

					$size['width'] = isset( $image_size['width'] ) && !empty( $image_size['width'] ) ? $image_size['width'] : '32';
					$size['height'] = isset( $image_size['height'] ) && !empty( $image_size['height'] ) ? $image_size['height'] : '32';
					$size['crop'] = isset( $image_size['crop'] ) ? 1 : 0;

					$image_size = apply_filters( 'woocommerce_get_image_size_swatches_image_size', $size );

					//Need to remove the filter because woocommerce will disable the input field. 
					remove_filter( 'woocommerce_get_image_size_swatches', array($this, 'get_image_size_swatches') );

					return $image_size;
				}

			}

			$GLOBALS['woocommerce_swatches'] = new WC_SwatchesPlugin();
		} else {
			require 'back_compat_less_24/woocommerce-swatches.php';
		}
	}

}
	