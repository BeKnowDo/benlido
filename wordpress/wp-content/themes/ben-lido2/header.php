<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package storefront
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php do_action( 'storefront_before_site' ); ?>

	<?php do_action( 'storefront_before_header' ); ?>

	<?php
		/**
		 * NOTE: unhooked storefront_header action
		 *
		 * @UNHOOKED storefront_skip_links                       - 0
		 * @UNHOOKED storefront_social_icons                     - 10
		 * @UNHOOKED storefront_site_branding                    - 20
		 * @UNHOOKED storefront_secondary_navigation             - 30
		 * @UNHOOKED storefront_product_search                   - 40
		 * @UNHOOKED storefront_primary_navigation_wrapper       - 42
		 * @UNHOOKED storefront_primary_navigation               - 50
		 * @UNHOOKED storefront_header_cart                      - 60
		 * @UNHOOKED storefront_primary_navigation_wrapper_close - 68
		 */

		// instead, the header is replaced by the template part
		get_template_part('template-parts/common/header');
	?>
	
	<?php
	/**
	 * Functions hooked in to storefront_before_content
	 *
	 * @hooked storefront_header_widget_region - 10
	 */
	do_action( 'storefront_before_content' ); ?>

	<div id="content" class="" tabindex="-1">

		<?php
		/**
		 * Functions hooked in to storefront_content_top
		 *
		 * @hooked woocommerce_breadcrumb - 10
		 */
		do_action( 'storefront_content_top' );
