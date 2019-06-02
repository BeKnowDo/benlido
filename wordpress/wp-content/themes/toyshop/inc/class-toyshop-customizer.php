<?php
/**
 * Toyshop_Customizer Class
 * Makes adjustments to Storefront cores Customizer implementation.
 *
 * @author   WooThemes
 * @since    1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Toyshop_Customizer' ) ) {

class Toyshop_Customizer {

	/**
	 * Setup class.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		$theme 					= wp_get_theme( 'storefront' );
		$storefront_version 	= $theme['Version'];

		add_action( 'wp_enqueue_scripts',	array( $this, 'add_customizer_css' ),					1000 );
		add_action( 'customize_register',	array( $this, 'edit_default_controls' ),				99 );
		add_filter( 'storefront_setting_default_values', array( $this, 'get_toyshop_defaults' ) );

		/**
		 * The following can be removed when Storefront 2.1 lands
		 */
		add_action( 'init',					array( $this, 'default_theme_mod_values' )				);
		add_action( 'customize_register',	array( $this, 'edit_default_customizer_settings' ),		99 );
		if ( version_compare( $storefront_version, '2.0.0', '<' ) ) {
			add_action( 'init',				array( $this, 'default_theme_settings' ) );
		}
	}

	/**
	 * Returns an array of the desired default Storefront options
	 * @return array
	 */
	public function get_toyshop_defaults() {
		return apply_filters( 'toyshop_default_settings', $args = array(
			'background_color'                       => 'ecfaf9',
			'storefront_heading_color'               => '#d9582b',
			'storefront_footer_heading_color'        => '#494d4d',
			'storefront_header_background_color'     => '#ffffff',
			'storefront_header_link_color'           => '#00958b',
			'storefront_header_text_color'           => '#647070',
			'storefront_text_color'                  => '#647070',
			'storefront_accent_color'                => '#00958b',
			'storefront_button_background_color'     => '#00958b',
			'storefront_button_text_color'           => '#ffffff',
			'storefront_button_alt_background_color' => '#d9582b',
			'storefront_button_alt_text_color'       => '#ffffff',
			'storefront_footer_background_color'     => '#9ecf65',
			'storefront_footer_text_color'           => '#ffffff',
			'storefront_footer_link_color'           => '#ffffff',
			'storefront_footer_heading_color'        => '#5e8a2b',
		) );
	}

	/**
	 * Set default Customizer settings based on Toyshop design.
	 * @uses get_toyshop_defaults()
	 * @return void
	 */
	public function edit_default_customizer_settings( $wp_customize ) {
		foreach ( Toyshop_Customizer::get_toyshop_defaults() as $mod => $val ) {
			$setting = $wp_customize->get_setting( $mod );

			if ( is_object( $setting ) ) {
				$setting->default = $val;
			}
		}
	}

	/**
	 * Returns a default theme_mod value if there is none set.
	 * @uses get_toyshop_defaults()
	 * @return void
	 */
	public function default_theme_mod_values() {
		foreach ( Toyshop_Customizer::get_toyshop_defaults() as $mod => $val ) {
			add_filter( 'theme_mod_' . $mod, function( $setting ) use ( $val ) {
				return $setting ? $setting : $val;
			});
		}
	}

	/**
	 * Sets default theme color filters for storefront color values.
	 * This function is required for Storefront < 2.0.0 support
	 * @uses get_toyshop_defaults()
	 * @return void
	 */
	public function default_theme_settings() {
		$prefix_regex = '/^storefront_/';
		foreach ( self::get_toyshop_defaults() as $mod => $val) {
			if ( preg_match( $prefix_regex, $mod ) ) {
				$filter = preg_replace( $prefix_regex, 'storefront_default_', $mod );
				add_filter( $filter, function( $_ ) use ( $val ) {
					return $val;
				}, 99 );
			}
		}
	}

	/**
	 * Modify the default controls
	 * @return void
	 */
	public function edit_default_controls( $wp_customize ) {
		$wp_customize->get_setting( 'background_color' )->transport 	= 'refresh';

		$wp_customize->add_setting( 'toyshop_season', array(
			'default'           => 'summer',
			'sanitize_callback' => 'storefront_sanitize_choices',
		) );

		$wp_customize->add_control( 'toyshop_season', array(
			'label'         => esc_html__( 'Season', 'toyshop' ),
			'description'	=> esc_html__( 'Apply a color scheme to the footer design details', 'toyshop' ),
			'section'       => 'storefront_footer',
			'type'          => 'select',
			'choices'       => array(
				'summer' => esc_html__( 'Summer', 'toyshop' ),
				'fall'   => esc_html__( 'Fall', 'toyshop' ),
				'winter' => esc_html__( 'Winter', 'toyshop' ),
			),
		) );

		/**
		 * We have to add the section back in because the `custom-header` theme feature is removed in this theme.
		 */
		$wp_customize->add_section( 'header_image' , array(
			'title'      => __( 'Header', 'toyshop' ),
			'priority'   => 45,
		) );
	}

	/**
	 * Add CSS using settings obtained from the theme options.
	 * @return void
	 */
	public function add_customizer_css() {
		$bg_color                       = storefront_get_content_background_color();
		$heading_color		 			= get_theme_mod( 'storefront_heading_color' );
		$header_bg_color 				= get_theme_mod( 'storefront_header_background_color' );
		$accent_color					= get_theme_mod( 'storefront_accent_color' );
		$header_link_color 				= get_theme_mod( 'storefront_header_link_color' );
		$text_color 					= get_theme_mod( 'storefront_text_color' );
		$button_text_color 				= get_theme_mod( 'storefront_button_text_color' );
		$button_background_color 		= get_theme_mod( 'storefront_button_background_color' );
		$button_alt_background_color 	= get_theme_mod( 'storefront_button_alt_background_color' );
		$button_alt_text_color 			= get_theme_mod( 'storefront_button_alt_text_color' );

		$brighten_factor 				= apply_filters( 'storefront_brighten_factor', 25 );
		$darken_factor 					= apply_filters( 'storefront_darken_factor', -25 );

		$style = '
			.onsale {
				background-color: ' . $button_alt_background_color . ';
				color: ' . $button_alt_text_color . ';
			}

			#payment .payment_methods li:hover {
				background-color: ' . storefront_adjust_color_brightness( $bg_color, 7 ) . ';
			}

			table th {
				background-color: ' . storefront_adjust_color_brightness( '#ffffff', -7 ) . ';
			}

			table tbody td {
				background-color: ' . storefront_adjust_color_brightness( '#ffffff', -2 ) . ';
			}

			table tbody tr:nth-child(2n) td {
				background-color: ' . storefront_adjust_color_brightness( '#ffffff', -4 ) . ';
			}

			#order_review,
			#payment .payment_methods li .payment_box {
				background-color: #ffffff;
			}

			#payment .payment_methods li,
			#payment .place-order {
				background-color: ' . storefront_adjust_color_brightness( '#ffffff', -5 ) . ';
			}

			#payment .payment_methods li:hover {
				background-color: ' . storefront_adjust_color_brightness( '#ffffff', -10 ) . ';
			}

			.page-template-template-homepage-php ul.tabs li a.active {
				color: ' . $heading_color . ';
			}

			.page-title,
			.site-content .widget-area .widget-title,
			.comments-title,
			#reply-title,
			.site-header .secondary-navigation .menu a:hover {
				color: ' . $accent_color . ';
			}

			.site-branding h1 a,
			.site-branding h1 a:hover,
			.site-branding .site-title a,
			.site-branding .site-title a:hover {
				color: ' . $button_alt_background_color . ';
			}

			.entry-title a,
			.entry-title a:visited {
				color: ' . $heading_color . ';
			}

			button,
			input[type="button"],
			input[type="reset"],
			input[type="submit"],
			.button,
			.added_to_cart,
			.widget-area .widget a.button,
			.site-header-cart .widget_shopping_cart a.button {
				border-color: ' . $button_background_color . ';
			}

			.widget-area .widget ul li:before,
			.woocommerce-breadcrumb:before {
				color: ' . $accent_color . ';
			}

			.woocommerce-pagination .page-numbers li .page-numbers.current {
				background-color: ' . $button_alt_background_color . ';
				color: ' . $button_alt_text_color . ';
			}

			@media screen and (min-width: 768px) {

				.site-main ul.products li.product .button {
					color: ' . $button_background_color . ';
				}

				ul.products li.product-category a {
					background-color: ' . $header_bg_color . ';
				}

				ul.products li.product-category .toyshop-product-title h3,
				ul.products li.product-category .toyshop-product-title h2,
				ul.products li.product-category .toyshop-product-title .woocommerce-loop-product__title {
					color: ' . $header_link_color . ';
				}

				.main-navigation ul.menu > li:first-child:before,
				.main-navigation ul.menu > li:last-child:after,
				.main-navigation ul.nav-menu > li:first-child:before,
				.main-navigation ul.nav-menu > li:last-child:after {
					color: ' . $header_link_color . ';
				}

				.site-header .toyshop-primary-navigation,
				.footer-widgets,
				.site-footer,
				.main-navigation ul.menu ul.sub-menu,
				.main-navigation ul.nav-menu ul.sub-menu,
				.site-header .toyshop-top-bar {
					border-color: ' . $header_link_color . ';
				}

				.site-header .site-branding {
					border-bottom-color: ' . $header_link_color . ';
				}

				.site-header .toyshop-top-bar {
					background-color: ' . $header_bg_color . ';
				}
				.site-header .toyshop-top-bar:after {
					background-image: -webkit-radial-gradient( ' . $header_bg_color . ', ' . $header_bg_color . ' 65%, rgba(255,255,255,0) 70%, rgba(255,255,255,0));
					background-image: radial-gradient( ' . $header_bg_color . ', ' . $header_bg_color . ' 65%, rgba(255,255,255,0) 70%, rgba(255,255,255,0));
				}

			}';

		wp_add_inline_style( 'storefront-child-style', $style );
	}
}

}

return new Toyshop_Customizer();