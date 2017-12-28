<?php
/**
 * Toyshop Class
 *
 * @author   WooThemes
 * @since    1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Toyshop' ) ) {

class Toyshop {
	/**
	 * Setup class.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_child_styles' ), 99 );
		add_action( 'storefront_loop_columns', array( $this, 'loop_columns' ) );
		add_action( 'swc_product_columns_default', array( $this, 'loop_columns' ) );
		add_filter( 'storefront_related_products_args', array( $this, 'related_products_args' ) );
		add_action( 'init',	array( $this, 'remove_theme_support' ), 99 );
		add_filter( 'body_class', array( $this, 'body_classes' ) );
	}

	/**
	 * Enqueue Storefront Styles
	 * @return void
	 */
	public function enqueue_styles() {
		global $storefront_version;

		wp_enqueue_style( 'storefront-style', get_template_directory_uri() . '/style.css', $storefront_version );
	}

	/**
	 * Enqueue Storechild Styles
	 * @return void
	 */
	public function enqueue_child_styles() {
		global $storefront_version, $toyshop_version;

		/**
		 * Styles
		 */
		wp_style_add_data( 'storefront-child-style', 'rtl', 'replace' );
		wp_enqueue_style( 'maiden-orange', 'https://fonts.googleapis.com/css?family=Maiden+Orange', array( 'storefront-child-style' ) );
		wp_enqueue_style( 'roboto', 'https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic&subset=latin,latin-ext', array( 'storefront-child-style' ) );

		/**
		 * Javascript
		 */
		wp_enqueue_script( 'modernizr', get_stylesheet_directory_uri() . '/assets/js/modernizr.min.js', array( 'jquery' ), '2.8.3' );
		wp_enqueue_script( 'toyshop', get_stylesheet_directory_uri() . '/assets/js/toyshop.min.js', array( 'jquery' ), $toyshop_version, true );
		wp_enqueue_script( 'masonry', array( 'jquery' ) );
	}

	/**
	 * Shop columns
	 * @return int number of columns
	 */
	public function loop_columns( $columns ) {
		$columns = 4;
		return $columns;
	}

	/**
	 * Adjust related products columns
	 * @return array $args the modified arguments
	 */
	public function related_products_args( $args ) {
		$args['posts_per_page'] = 4;
		$args['columns']		= 4;

		return $args;
	}

	/**
	 * Adds custom seasonal classes to the array of body classes.
	 *
	 * @param array $classes Classes for the body element.
	 * @return array
	 */
	public function body_classes( $classes ) {
		$season = get_theme_mod( 'toyshop_season', 'summer' );

		if ( 'fall' == $season ) {
			$classes[] = 'fall';
		}
		else if ( 'winter' == $season ) {
			$classes[] = 'winter';
		}
		else {
			$classes[] = 'summer';
		}

		return $classes;
	}

	/**
	 * Removes theme support to disable specific features
	 * @return void
	 */
	public function remove_theme_support() {
		remove_theme_support( 'custom-header' );
	}
}

}

return new Toyshop();