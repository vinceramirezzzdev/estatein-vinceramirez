<?php
/**
 * Estatein theme bootstrap.
 *
 * Every feature lives in its own file under /inc:
 *   setup.php        theme supports, menus, image sizes
 *   assets.php       styles, scripts, preloads
 *   helpers.php      template helpers (icons, images, fields, links)
 *   content.php      default copy from the Figma design (single source of truth)
 *   post-types.php   custom post types + taxonomies
 *   fields.php       ACF field groups (with graceful fallback when ACF is inactive)
 *   meta-boxes.php   property gallery picker (works without ACF Pro)
 *   customizer.php   site-wide settings (banner, CTA, contact details, socials)
 *   menus.php        nav walkers that output the design's markup
 *   forms.php        contact / enquiry / newsletter handling
 *   form-fields.php  field markup helpers used by the form templates
 *   seo.php          meta description, Open Graph, JSON-LD
 *   admin.php        admin columns and notices
 *   demo-content.php one-time import of the design content on activation
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

define( 'ESTATEIN_VERSION', '1.0.0' );
define( 'ESTATEIN_DIR', get_template_directory() );
define( 'ESTATEIN_URI', get_template_directory_uri() );

$estatein_includes = array(
	'setup',
	'helpers',
	'content',
	'assets',
	'post-types',
	'fields',
	'meta-boxes',
	'customizer',
	'menus',
	'forms',
	'form-fields',
	'seo',
	'admin',
	'demo-content',
);

foreach ( $estatein_includes as $estatein_file ) {
	require_once ESTATEIN_DIR . '/inc/' . $estatein_file . '.php';
}
