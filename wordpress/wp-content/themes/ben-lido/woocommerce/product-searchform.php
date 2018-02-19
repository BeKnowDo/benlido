<?php
/**
 * The template for displaying product search form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/product-searchform.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<form id="search-container" role="search" method="get" class="row no-margin mpush-1 no-margin-left hd-9 push-2" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <input id="search-field-button" class="column hd-1" type="image" src="<?php echo get_stylesheet_directory_uri();?>/assets/images/icon-search.svg" value="<?php echo esc_attr_x( 'Search', 'submit button', 'woocommerce' ); ?>" />
	<input type="search" id="woocommerce-product-search-field-<?php echo isset( $index ) ? absint( $index ) : 0; ?>" class="search-field column hd-11" placeholder="Enter item or brand name" value="<?php echo get_search_query(); ?>" name="s" />
	<input type="hidden" name="post_type" value="product" />
</form>
