<?php
/**
 * Toyshop_Template Class
 *
 * @author   WooThemes
 * @since    1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Toyshop_Template' ) ) {

class Toyshop_Template {

	/**
	 * Setup class.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'toyshop_layout_adjustments' ) );
		add_filter( 'storefront_products_per_page', array( $this, 'toyshop_products_per_page' ) );
		add_filter( 'woocommerce_breadcrumb_defaults', array( $this, 'toyshop_change_breadcrumb_delimiter' ) );
	}

	/**
	 * Layout adjustments
	 * @return rearrange markup through add_action and remove_action
	 */
	public function toyshop_layout_adjustments() {
		if ( class_exists( 'WooCommerce' ) ) {
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
			add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'toyshop_product_loop_title_price_wrap' ), 11 );
			add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 2 );
			add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 1 );
			add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'toyshop_product_loop_title_price_wrap_close' ), 2 );

			add_action( 'woocommerce_before_subcategory_title', array( $this, 'toyshop_product_loop_title_price_wrap' ), 11 );
			add_action( 'woocommerce_after_subcategory_title', array( $this, 'toyshop_product_loop_title_price_wrap_close' ), 2 );

			remove_action( 'storefront_header', 'storefront_header_cart', 60 );
			add_action( 'storefront_header', 'storefront_header_cart', 4 );

			remove_action( 'storefront_header', 'storefront_product_search', 40 );
			add_action( 'storefront_header', 'storefront_product_search', 3 );
		}

		remove_action( 'storefront_header', 'storefront_secondary_navigation', 30 );
		add_action( 'storefront_header', 'storefront_secondary_navigation', 6 );

		remove_action( 'storefront_header', 'storefront_site_branding', 20 );
		add_action( 'storefront_header', 'storefront_site_branding', 5 );

		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
		add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display', 30 );

		add_action( 'storefront_header', array( $this, 'toyshop_primary_navigation_wrapper' ), 49 );
		add_action( 'storefront_header', array( $this, 'toyshop_primary_navigation_wrapper_close' ), 61 );

		add_action( 'storefront_header', array( $this, 'toyshop_top_bar_wrapper' ), 1 );
		add_action( 'storefront_header', array( $this, 'toyshop_top_bar_wrapper_close' ), 6 );

		$season = get_theme_mod( 'toyshop_season', 'summer' );

		if ( 'winter' == $season ) {
			add_action( 'storefront_before_footer',  array( $this, 'toyshop_before_footer_winter' ), 1 );
		}
		else {
			add_action( 'storefront_before_footer',  array( $this, 'toyshop_before_footer_summer' ), 1 );
		}

		add_action( 'storefront_before_content', array( $this, 'toyshop_before_content' ), 1 );
	}

	/**
	 * Product title wrapper
	 * @return void
	 */
	public function toyshop_product_loop_title_price_wrap() {
		echo '<section class="toyshop-product-title">';
	}

	/**
	 * Product title wrapper close
	 * @return void
	 */
	public function toyshop_product_loop_title_price_wrap_close() {
		echo '</section>';
	}

	/**
	 * Primary navigation wrapper
	 * @return void
	 */
	public function toyshop_primary_navigation_wrapper() {
		echo '<section class="toyshop-primary-navigation">';
	}

	/**
	 * Primary navigation wrapper close
	 * @return void
	 */
	public function toyshop_primary_navigation_wrapper_close() {
		echo '</section>';
	}

	/**
	 * Top bar wrapper
	 * @return void
	 */
	public function toyshop_top_bar_wrapper() {
		echo '<section class="toyshop-top-bar">';
	}

	/**
	 * Top bar wrapper close
	 * @return void
	 */
	public function toyshop_top_bar_wrapper_close() {
		echo '</section>';
	}

	/**
	 * Products per page
	 * @return int products to display per page
	 */
	public function toyshop_products_per_page( $per_page ) {
		$per_page = 19;
		return intval( $per_page );
	}

	public function toyshop_change_breadcrumb_delimiter( $defaults ) {
		$defaults['delimiter'] = ' <span>/</span> ';
		return $defaults;
	}

	/**
	 * Add some clouds to the header
	 */
	public function toyshop_before_content() { ?>
		<div class="clouds">
			<div class="cloud"></div>
			<div class="cloud"></div>
			<div class="cloud"></div>
			<div class="cloud"></div>
			<div class="cloud"></div>
		</div>
	<?php }

	/**
	 * Summer/fall footer
	 */
	public function toyshop_before_footer_summer() { ?>
	<div class="forest">
		<div class="tree">
			<div class="branch"></div>
			<div class="branch"></div>
			<div class="trunk"></div>
		</div>
		<div class="tree">
			<div class="branch"></div>
			<div class="branch"></div>
			<div class="trunk"></div>
		</div>
		<div class="tree">
			<div class="branch"></div>
			<div class="branch"></div>
			<div class="trunk"></div>
		</div>
		<div class="tree">
			<div class="branch"></div>
			<div class="branch"></div>
			<div class="trunk"></div>
		</div>
		<div class="sun">
			<div class="ray"></div>
			<div class="ray"></div>
			<div class="ray"></div>
			<div class="ray"></div>
			<div class="ray"></div>
			<div class="ray"></div>
			<div class="ray"></div>
			<div class="ray"></div>
		</div>
	</div>
	<?php }

	/**
	 * Winter footer
	 */
	public function toyshop_before_footer_winter() { ?>
	<div class="forest">
		<div class="tree">
			<div class="branch"></div>
			<div class="branch"></div>
			<div class="branch"></div>
			<div class="trunk"></div>
		</div>
		<div class="tree">
			<div class="branch"></div>
			<div class="branch"></div>
			<div class="branch"></div>
			<div class="trunk"></div>
		</div>
		<div class="tree">
			<div class="branch"></div>
			<div class="branch"></div>
			<div class="branch"></div>
			<div class="trunk"></div>
		</div>
		<div class="tree">
			<div class="branch"></div>
			<div class="branch"></div>
			<div class="branch"></div>
			<div class="trunk"></div>
		</div>
		<div class="snowman">
			<div class="hat"></div>
				<div class="head">
					<div class="eye"></div>
					<div class="eye"></div>
					<div class="nose"></div>
					<div class="mouth"></div>
					<div class="mouth"></div>
					<div class="scarf"></div>
				</div>
				<div class="body">
					<div class="arm"></div>
					<div class="arm"></div>
					<div class="coal"></div>
				</div>
				<div class="body">
					<div class="coal"></div>
				</div>
		</div>
	</div>
	<?php }
}

}

return new Toyshop_Template();