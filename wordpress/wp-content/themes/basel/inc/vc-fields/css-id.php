<?php

if ( ! defined( 'BASEL_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
* CSS id
*/
if ( ! function_exists( 'basel_get_css_id_param' ) && function_exists( 'vc_add_shortcode_param' ) ) {
	function basel_get_css_id_param( $settings, $value ) {
	    return '<input type="hidden" name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value basel-css-id" value="' . esc_attr( uniqid() ) . '">';
    }
	vc_add_shortcode_param( 'basel_css_id', 'basel_get_css_id_param' );
}