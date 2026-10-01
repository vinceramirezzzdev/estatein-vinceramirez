<?php
/**
 * Front-end and admin assets.
 *
 * The front end loads exactly one stylesheet and one deferred script — the
 * same minified files the static build uses — plus a preload for the
 * self-hosted Urbanist font.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Cache-busting version for a theme asset.
 *
 * @param string $path Path relative to /assets.
 * @return string
 */
function estatein_asset_version( $path ) {
	$file = ESTATEIN_DIR . '/assets/' . $path;
	return file_exists( $file ) ? (string) filemtime( $file ) : ESTATEIN_VERSION;
}

/**
 * Enqueue the theme stylesheet and script.
 */
function estatein_enqueue_assets() {
	$debug  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
	$css    = $debug ? 'css/main.css' : 'css/main.min.css';
	$script = $debug ? 'js/main.js' : 'js/main.min.js';

	wp_enqueue_style( 'estatein', estatein_asset( $css ), array(), estatein_asset_version( $css ) );

	// GSAP (self-hosted) for the scroll animations in js/animations.js.
	$defer = array( 'in_footer' => false, 'strategy' => 'defer' );
	wp_enqueue_script( 'gsap', estatein_asset( 'js/vendor/gsap.min.js' ), array(), '3.13.0', $defer );
	wp_enqueue_script( 'gsap-scrolltrigger', estatein_asset( 'js/vendor/ScrollTrigger.min.js' ), array( 'gsap' ), '3.13.0', $defer );

	wp_enqueue_script(
		'estatein',
		estatein_asset( $script ),
		array( 'gsap-scrolltrigger' ),
		estatein_asset_version( $script ),
		array(
			'in_footer' => false,
			'strategy'  => 'defer',
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// The design never uses the block library styles on its classic templates.
	if ( ! is_singular( 'post' ) && ! is_page_template( 'default' ) && ! is_home() && ! is_archive() ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
	}
}
add_action( 'wp_enqueue_scripts', 'estatein_enqueue_assets', 20 );

/**
 * Preload the latin Urbanist subset (used above the fold on every page).
 */
function estatein_preload_font() {
	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( estatein_asset( 'fonts/urbanist-latin.woff2' ) )
	);
}
add_action( 'wp_head', 'estatein_preload_font', 1 );

/**
 * Theme colour + SVG favicon (unless a Site Icon is set in the Customizer).
 */
function estatein_head_meta() {
	echo '<meta name="theme-color" content="#141414">' . "\n";
	echo '<meta name="color-scheme" content="dark">' . "\n";
	if ( ! has_site_icon() ) {
		printf( '<link rel="icon" href="%s" type="image/svg+xml">' . "\n", esc_url( estatein_asset( 'icons/favicon.svg' ) ) );
	}
}
add_action( 'wp_head', 'estatein_head_meta', 2 );

/**
 * Admin: media picker for the property gallery meta box.
 *
 * @param string $hook Current admin page.
 */
function estatein_admin_assets( $hook ) {
	$screen = get_current_screen();
	if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) && $screen && 'property' === $screen->post_type ) {
		wp_enqueue_media();
		wp_enqueue_script( 'estatein-admin-gallery', estatein_asset( 'admin/gallery.js' ), array(), estatein_asset_version( 'admin/gallery.js' ), true );
		wp_enqueue_style( 'estatein-admin', estatein_asset( 'admin/admin.css' ), array(), estatein_asset_version( 'admin/admin.css' ) );
	}
}
add_action( 'admin_enqueue_scripts', 'estatein_admin_assets' );
