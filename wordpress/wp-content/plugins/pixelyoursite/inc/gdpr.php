<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @link https://wordpress.org/plugins/gdpr/
 */
//function pys_is_gdpr_plugin_activated() {
//
//	if ( ! function_exists( 'is_plugin_active' ) ) {
//		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
//	}
//
//	return is_plugin_active( 'gdpr/gdpr.php' );
//
//}

/**
 * @link https://wordpress.org/plugins/ginger/
 */
function pys_is_ginger_plugin_activated() {
	
	if ( ! function_exists( 'is_plugin_active' ) ) {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	
	return is_plugin_active( 'ginger/ginger-eu-cookie-law.php' );
	
}

/**
 * @link https://wordpress.org/plugins/cookiebot/
 */
function pys_is_cookiebot_plugin_activated() {

	if ( ! function_exists( 'is_plugin_active' ) ) {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}

	return is_plugin_active( 'cookiebot/cookiebot.php' );

}

add_action( 'wp_ajax_pys_get_gdpr_filter_value', 'pys_get_gdpr_filter_value' );
add_action( 'wp_ajax_nopriv_pys_get_gdpr_filter_value', 'pys_get_gdpr_filter_value' );
function pys_get_gdpr_filter_value() {
    wp_send_json_success( array(
        'disable' => apply_filters( 'pys_disable_by_gdpr', false ),
    ) );
}