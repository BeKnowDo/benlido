<?php
/**
 * Plugin Name: Ben Lido Gutenberg Blocks
 * Author: Urban Pixels
 * Version: 1.0.0
 */


function bl_gutenberg_block_category( $categories, $post ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug' => 'bl-basel-gutenberg-blocks',
				'title' => __( 'Basel Blocks', 'basel-blocks' ),
			),
		)
	);
}
add_filter( 'block_categories', 'bl_gutenberg_block_category', 10, 2);

// using https://github.com/ahmadawais/create-guten-block

// guten blocks:
//include_once ( plugin_dir_path(__FILE__) . '/blocks/bl-basel-header/src/init.php');
include_once ( plugin_dir_path(__FILE__) . '/blocks/bl-basel-header-plain/src/init.php');
