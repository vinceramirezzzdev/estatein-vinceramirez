<?php
/**
 * Custom post types and taxonomies.
 *
 *   property     → Properties page, Featured Properties, Property Details (single)
 *   testimonial  → "What Our Clients Say" + /testimonials/ archive
 *   faq          → "Frequently Asked Questions" + /faqs/ archive
 *   team_member  → About › Meet the Estatein Team
 *   client       → About › Our Valued Clients
 *   service      → Services page cards (grouped by the Service Group taxonomy)
 *   office       → Contact › Office locations (filtered by Office Type)
 *   enquiry      → form submissions (admin only)
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Labels helper.
 *
 * @param string $singular Singular name.
 * @param string $plural   Plural name.
 * @return array
 */
function estatein_post_type_labels( $singular, $plural ) {
	return array(
		'name'               => $plural,
		'singular_name'      => $singular,
		/* translators: %s: post type singular name. */
		'add_new_item'       => sprintf( __( 'Add New %s', 'estatein' ), $singular ),
		/* translators: %s: post type singular name. */
		'edit_item'          => sprintf( __( 'Edit %s', 'estatein' ), $singular ),
		/* translators: %s: post type singular name. */
		'new_item'           => sprintf( __( 'New %s', 'estatein' ), $singular ),
		/* translators: %s: post type singular name. */
		'view_item'          => sprintf( __( 'View %s', 'estatein' ), $singular ),
		/* translators: %s: post type plural name. */
		'search_items'       => sprintf( __( 'Search %s', 'estatein' ), $plural ),
		/* translators: %s: post type plural name. */
		'not_found'          => sprintf( __( 'No %s found', 'estatein' ), strtolower( $plural ) ),
		/* translators: %s: post type plural name. */
		'all_items'          => sprintf( __( 'All %s', 'estatein' ), $plural ),
		'menu_name'          => $plural,
	);
}

/**
 * Register post types and taxonomies.
 */
function estatein_register_post_types() {
	register_post_type(
		'property',
		array(
			'labels'        => estatein_post_type_labels( __( 'Property', 'estatein' ), __( 'Properties', 'estatein' ) ),
			'public'        => true,
			'has_archive'   => false, // The "Properties" page lists them, with search + filters.
			'rewrite'       => array( 'slug' => 'property' ),
			'menu_icon'     => 'dashicons-building',
			'menu_position' => 5,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
			'show_in_rest'  => true,
		)
	);

	register_post_type(
		'testimonial',
		array(
			'labels'        => estatein_post_type_labels( __( 'Testimonial', 'estatein' ), __( 'Testimonials', 'estatein' ) ),
			'public'        => true,
			'has_archive'   => 'testimonials',
			'rewrite'       => array( 'slug' => 'testimonials' ),
			'menu_icon'     => 'dashicons-format-quote',
			'menu_position' => 6,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			'show_in_rest'  => true,
		)
	);

	register_post_type(
		'faq',
		array(
			'labels'        => estatein_post_type_labels( __( 'FAQ', 'estatein' ), __( 'FAQs', 'estatein' ) ),
			'public'        => true,
			'has_archive'   => 'faqs',
			'rewrite'       => array( 'slug' => 'faqs' ),
			'menu_icon'     => 'dashicons-editor-help',
			'menu_position' => 7,
			'supports'      => array( 'title', 'editor', 'excerpt', 'page-attributes' ),
			'show_in_rest'  => true,
		)
	);

	$content_types = array(
		'team_member' => array( __( 'Team Member', 'estatein' ), __( 'Team', 'estatein' ), 'dashicons-groups', array( 'title', 'thumbnail', 'page-attributes' ) ),
		'client'      => array( __( 'Client', 'estatein' ), __( 'Clients', 'estatein' ), 'dashicons-businessperson', array( 'title', 'page-attributes' ) ),
		'service'     => array( __( 'Service', 'estatein' ), __( 'Services', 'estatein' ), 'dashicons-admin-tools', array( 'title', 'editor', 'page-attributes' ) ),
		'office'      => array( __( 'Office', 'estatein' ), __( 'Offices', 'estatein' ), 'dashicons-location', array( 'title', 'editor', 'page-attributes' ) ),
	);
	$position      = 8;
	foreach ( $content_types as $type => $args ) {
		register_post_type(
			$type,
			array(
				'labels'              => estatein_post_type_labels( $args[0], $args[1] ),
				'public'              => false,
				'show_ui'             => true,
				'show_in_rest'        => true,
				'exclude_from_search' => true,
				'menu_icon'           => $args[2],
				'menu_position'       => $position++,
				'supports'            => $args[3],
			)
		);
	}

	register_post_type(
		'enquiry',
		array(
			'labels'          => estatein_post_type_labels( __( 'Enquiry', 'estatein' ), __( 'Enquiries', 'estatein' ) ),
			'public'          => false,
			'show_ui'         => true,
			'menu_icon'       => 'dashicons-email-alt',
			'menu_position'   => 25,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		)
	);

	register_taxonomy(
		'property_category',
		'property',
		array(
			'labels'            => array(
				'name'          => __( 'Categories', 'estatein' ),
				'singular_name' => __( 'Category', 'estatein' ),
			),
			'description'       => __( 'Shown on the property cards as "Name - Description".', 'estatein' ),
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'property-category' ),
		)
	);

	register_taxonomy(
		'property_location',
		'property',
		array(
			'labels'            => array(
				'name'          => __( 'Locations', 'estatein' ),
				'singular_name' => __( 'Location', 'estatein' ),
			),
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'location' ),
		)
	);

	register_taxonomy(
		'service_group',
		'service',
		array(
			'labels'            => array(
				'name'          => __( 'Service Groups', 'estatein' ),
				'singular_name' => __( 'Service Group', 'estatein' ),
			),
			'public'            => false,
			'show_ui'           => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
		)
	);

	register_taxonomy(
		'office_type',
		'office',
		array(
			'labels'            => array(
				'name'          => __( 'Office Types', 'estatein' ),
				'singular_name' => __( 'Office Type', 'estatein' ),
			),
			'public'            => false,
			'show_ui'           => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
		)
	);
}
add_action( 'init', 'estatein_register_post_types' );

/**
 * Flush rewrite rules once after the theme is activated (new CPT slugs).
 */
function estatein_flush_rewrites() {
	estatein_register_post_types();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'estatein_flush_rewrites' );

/**
 * Order content types by menu order (drag order in the admin) on archives.
 *
 * @param WP_Query $query Main query.
 */
function estatein_archive_order( $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_post_type_archive( array( 'testimonial', 'faq' ) ) ) {
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'ASC' ) );
		$query->set( 'posts_per_page', 30 );
	}
}
add_action( 'pre_get_posts', 'estatein_archive_order' );

/**
 * Posts of a content type in display order.
 *
 * @param string $type Post type.
 * @param array  $args Extra WP_Query args.
 * @return WP_Post[]
 */
function estatein_get_items( $type, $args = array() ) {
	return get_posts(
		array_merge(
			array(
				'post_type'        => $type,
				'posts_per_page'   => 50,
				'orderby'          => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
				'suppress_filters' => false,
			),
			$args
		)
	);
}
