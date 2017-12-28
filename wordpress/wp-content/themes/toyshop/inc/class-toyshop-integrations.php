<?php
/**
 * Toyshop_Integrations Class
 * Provides integrations with Storefront extensions by removing/changing incompatible controls/settings. Also adjusts default values
 * if they need to differ from the original setting.
 *
 * @author   WooThemes
 * @since    2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Toyshop_Integrations' ) ) {

class Toyshop_Integrations {

	/**
	 * Setup class.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'customize_register',		array( $this, 'edit_controls' ),						99 );
		add_action( 'customize_register',		array( $this, 'set_extension_default_settings' ),		99 );
		add_action( 'init',						array( $this, 'default_theme_mod_values' )				);
		add_action( 'after_switch_theme',		array( $this, 'edit_theme_mods' )						);
		add_action( 'wp',						array( $this, 'storefront_woocommerce_customiser' ),	99 );
		add_action( 'wp',						array( $this, 'storefront_checkout_customiser' ),		99 );
	}

	/**
	 * Returns an array of the desired Storefront extension settings
	 * @return array
	 */
	public function get_toyshop_extension_defaults() {
		return apply_filters( 'toyshop_default_extension_settings', $args = array(
		) );
	}

	/**
	 * Set default settings for Storefront extensions to provide compatibility with Toyshop.
	 * @uses get_toyshop_extension_defaults()
	 * @return void
	 */
	public function set_extension_default_settings( $wp_customize ) {
		foreach ( Toyshop_Integrations::get_toyshop_extension_defaults() as $mod => $val ) {
			$setting = $wp_customize->get_setting( $mod );

			if ( is_object( $setting ) ) {
				$setting->default = $val;
			}
		}
	}

	/**
	 * Returns a default theme_mod value if there is none set.
	 * @uses get_toyshop_extension_defaults()
	 * @return void
	 */
	public function default_theme_mod_values() {
		foreach ( Toyshop_Integrations::get_toyshop_extension_defaults() as $mod => $val ) {
			add_filter( 'theme_mod_' . $mod, function( $setting ) use ( $val ) {
				return $setting ? $setting : $val;
			});
		}
	}

    /**
	 * Remove unused/incompatible controls from the Customizer to avoid confusion
	 * @return void
	 */
	public function edit_controls( $wp_customize ) {
		$wp_customize->remove_control( 'sd_header_layout' );
		$wp_customize->remove_control( 'sd_header_layout_divider_after' );
		$wp_customize->remove_control( 'sd_button_rounded' );
		$wp_customize->remove_control( 'sd_button_flat' );
		$wp_customize->remove_control( 'sd_button_size' );
		$wp_customize->remove_control( 'sd_button_background_style' );
		$wp_customize->remove_control( 'storefront_header_image_heading' );
		$wp_customize->remove_control( 'header_image' );
		$wp_customize->remove_control( 'swc_shop_alignment' );
		$wp_customize->get_setting( 'storefront_header_background_color' )->transport = 'refresh';
	}

	/**
	 * Remove any pre-existing theme mods for settings that are incompatible with Toyshop.
	 * @return void
	 */
	public function edit_theme_mods() {
		remove_theme_mod( 'sd_header_layout' );
		remove_theme_mod( 'sd_button_rounded' );
		remove_theme_mod( 'sd_button_flat' );
		remove_theme_mod( 'sd_button_size' );
		remove_theme_mod( 'sd_button_background_style' );
		remove_theme_mod( 'header_image' );
		remove_theme_mod( 'swc_shop_alignment' );
	}

	/**
	 * Storefront WooCommerce Customiser compatibility tweaks
	 * @return void
	 */
	public function storefront_woocommerce_customiser() {
		if ( class_exists( 'Storefront_WooCommerce_Customiser' ) ) {
			$cart_link 			= get_theme_mod( 'swc_header_cart', true );
			$search 			= get_theme_mod( 'swc_header_search', true );
			$archive_price 		= get_theme_mod( 'swc_product_archive_price', true );
			$archive_rating 	= get_theme_mod( 'swc_product_archive_rating', true );

			if ( false == $cart_link ) {
				remove_action( 'storefront_header', 'storefront_header_cart', 4 );
			} else {
				add_action( 'storefront_header', 'storefront_header_cart', 4 );
			}
			if ( false == $search ) {
				remove_action( 'storefront_header', 'storefront_product_search', 3 );
			} else {
				add_action( 'storefront_header', 'storefront_product_search', 3 );
			}

			if ( false == $archive_price ) {
				remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 1 );
			}

			if ( false == $archive_rating ) {
				remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 2 );
			}
		}
	}

	/**
	 * Storefront Checkout Customiser compatibility tweaks
	 * @return void
	 */
	public function storefront_checkout_customiser() {
		if ( class_exists( 'Storefront_Checkout_Customiser' ) ) {
			$distraction_free 	= get_theme_mod( 'scc_distraction_free_checkout', false );

			if ( true == $distraction_free && is_checkout() ) {
				remove_action( 'storefront_header', 'storefront_product_search', 3 );
				remove_action( 'storefront_header', 'storefront_header_cart', 4 );
				remove_action( 'storefront_header', 'storefront_secondary_navigation', 6 );
			}
		}
	}
}

}

return new Toyshop_Integrations();