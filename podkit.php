<?php

/**
 * Plugin Name: Podkit
 * Description: Custom plugin for Gutenberg blocks
 * Version: 1.0.0
 * Author: Vladimir Kamuz
 */

defined( 'ABSPATH' ) || exit;

/**
 * Load translations (if any) for the plugin from the /languages/ folder.
 */
add_action( 'init', 'podkit_load_textdomain' );

function podkit_load_textdomain() {
	load_plugin_textdomain( 'podkit', false, basename( __DIR__ ) . '/languages' );
}

/** 
 * Add custom image size for block featured image.
 */
add_action( 'init', 'podkit_add_image_size' );

function podkit_add_image_size() {
	add_image_size( 'podkitFeatImg', 250, 250, array( 'center', 'center' ) ); 
}

/** 
 * Register custom image size with sizes list to make it available.
 */
add_filter( 'image_size_names_choose', 'podkit_custom_sizes' );

function podkit_custom_sizes( $sizes ) {
	return array_merge( $sizes, array(
		'podkitFeatImg' => __('Podkit Featured Image'),
	) );
}

/** 
 * Add custom "Podkit" block category
 */
add_filter( 'block_categories', 'podkit_block_categories', 10, 2 );

function podkit_block_categories( $categories, $post ) {
	if ( $post->post_type !== 'post' ) {
		return $categories;
	}
	return array_merge(
		$categories,
		array(
			array(
				'slug' => 'podkit',
				'title' => __( 'Podkit', 'podkit' ),
				'icon'  => 'microphone',
			),
		)
	);
}

/**
 * Registers all block assets so that they can be enqueued through the Block Editor in the corresponding context.
 */
add_action( 'init', 'podkit_register_blocks' );

function podkit_register_blocks() {

	// If Block Editor is not active, bail.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	// Retister the block editor script.
	wp_register_script(
		'podkit-editor-script', // label
		plugins_url( 'build/index.js', __FILE__ ), // script file
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ), // dependencies
		filemtime( plugin_dir_path( __FILE__ ) . 'build/index.js' ) // set version as file last modified time
	);

	// Register the block editor stylesheet.
	wp_register_style(
		'podkit-editor-styles',// label
		plugins_url( 'build/editor.css', __FILE__ ), // CSS file
		array( 'wp-edit-blocks' ), // dependencies
		filemtime( plugin_dir_path( __FILE__ ) . 'build/editor.css' ) // set version as file last modified time
	);

	// Register the front-end stylesheet.
	wp_register_style(
		'podkit-front-end-styles', // label
		plugins_url( 'build/style.css', __FILE__ ), // CSS file
		array( ), // dependencies
		filemtime( plugin_dir_path( __FILE__ ) . 'build/style.css' ) // set version as file last modified time
	);

	// Array of block created in this plugin.
	$blocks = [
		'podkit/static'
	];
	
	// Loop through $blocks and register each block with the same script and styles.
	foreach( $blocks as $block ) {
		register_block_type( $block, array(
			'editor_script' => 'podkit-editor-script', // Calls registered script above
			'editor_style' => 'podkit-editor-styles', // Calls registered stylesheet above
			'style' => 'podkit-front-end-styles', // Calls registered stylesheet above
		) );
	}

	// Adds internationalization support. 
	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'podkit-editor-script', 'podkit', plugin_dir_path( __FILE__ ) . '/languages' );
	}

}
