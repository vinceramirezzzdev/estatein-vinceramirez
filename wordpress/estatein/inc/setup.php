<?php
/**
 * Theme setup: supports, menus, image sizes and small front-end clean-ups.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme supports and menu locations.
 */
function estatein_setup() {
	load_theme_textdomain( 'estatein', ESTATEIN_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 48,
			'width'       => 160,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary navigation', 'estatein' ),
			'footer'  => __( 'Footer columns (top-level items become column titles)', 'estatein' ),
		)
	);

	// Card images in the design: 432 x 318 (desktop), 2x for retina.
	add_image_size( 'estatein-card', 864, 636, true );
	add_image_size( 'estatein-wide', 1466, 1166, true );
	add_image_size( 'estatein-thumb', 288, 188, true );
}
add_action( 'after_setup_theme', 'estatein_setup' );

/**
 * Content width for embeds (the design's content column is 1596px).
 */
function estatein_content_width() {
	$GLOBALS['content_width'] = 1596;
}
add_action( 'after_setup_theme', 'estatein_content_width', 0 );

/**
 * The design does not use emoji scripts or the block library on classic templates;
 * dropping them keeps the front end to one stylesheet and one deferred script.
 */
function estatein_cleanup() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
}
add_action( 'init', 'estatein_cleanup' );

/**
 * Add the page slug to the body classes so the CSS page modifiers
 * (.page-home, .page-about …) used by the static build also apply here.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function estatein_body_classes( $classes ) {
	if ( is_front_page() ) {
		$classes[] = 'page-home';
	} elseif ( is_page_template( 'page-templates/about.php' ) ) {
		$classes[] = 'page-about';
	} elseif ( is_page_template( 'page-templates/properties.php' ) ) {
		$classes[] = 'page-properties';
	} elseif ( is_page_template( 'page-templates/services.php' ) ) {
		$classes[] = 'page-services';
	} elseif ( is_page_template( 'page-templates/contact.php' ) ) {
		$classes[] = 'page-contact';
	} elseif ( is_singular( 'property' ) ) {
		$classes[] = 'page-property';
	}
	return $classes;
}
add_filter( 'body_class', 'estatein_body_classes' );

/**
 * Shorter "Read more" style excerpts that match the card copy length.
 *
 * @return int
 */
function estatein_excerpt_length() {
	return 22;
}
add_filter( 'excerpt_length', 'estatein_excerpt_length' );

/**
 * @return string
 */
function estatein_excerpt_more() {
	return '...';
}
add_filter( 'excerpt_more', 'estatein_excerpt_more' );
