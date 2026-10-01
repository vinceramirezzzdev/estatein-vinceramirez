<?php
/**
 * Navigation menus rendered with the design's markup.
 *
 * Primary: <ul class="navbar__menu"><li><a class="navbar__link" aria-current="page">
 * Footer:  top-level items become column titles, their children the links.
 * Both fall back to the design's links when no menu is assigned.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add the design class to primary menu links (WordPress already adds aria-current).
 *
 * @param array    $atts Link attributes.
 * @param WP_Post  $item Menu item.
 * @param stdClass $args Menu args.
 * @return array
 */
function estatein_primary_link_atts( $atts, $item, $args ) {
	if ( isset( $args->theme_location ) && 'primary' === $args->theme_location ) {
		$atts['class'] = 'navbar__link';
		// A single property belongs to the "Properties" section of the menu.
		if ( is_singular( 'property' ) && untrailingslashit( (string) $item->url ) === untrailingslashit( estatein_page_url( 'properties' ) ) ) {
			$atts['aria-current'] = 'page';
		}
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'estatein_primary_link_atts', 10, 3 );

/**
 * Keep menu <li> elements free of the many WordPress classes (CSS doesn't need them).
 *
 * @param string[] $classes Classes.
 * @param WP_Post  $item    Item.
 * @param stdClass $args    Args.
 * @return string[]
 */
function estatein_menu_item_classes( $classes, $item, $args ) {
	if ( isset( $args->theme_location ) && in_array( $args->theme_location, array( 'primary', 'footer' ), true ) ) {
		return array_intersect( $classes, array( 'current-menu-item' ) );
	}
	return $classes;
}
add_filter( 'nav_menu_css_class', 'estatein_menu_item_classes', 10, 3 );

/**
 * The design's primary links, used when no menu is assigned.
 *
 * @return array label => url
 */
function estatein_default_primary_links() {
	return array(
		__( 'Home', 'estatein' )       => home_url( '/' ),
		__( 'About Us', 'estatein' )   => estatein_page_url( 'about' ),
		__( 'Properties', 'estatein' ) => estatein_page_url( 'properties' ),
		__( 'Services', 'estatein' )   => estatein_page_url( 'services' ),
	);
}

/**
 * Print the primary menu.
 */
function estatein_primary_menu() {
	if ( has_nav_menu( 'primary' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'navbar__menu',
				'depth'          => 1,
				'fallback_cb'    => false,
			)
		);
		return;
	}

	$current = trailingslashit( strtok( home_url( add_query_arg( array() ) ), '?' ) );
	echo '<ul class="navbar__menu">';
	foreach ( estatein_default_primary_links() as $label => $url ) {
		printf(
			'<li><a class="navbar__link" href="%1$s"%2$s>%3$s</a></li>',
			esc_url( $url ),
			trailingslashit( $url ) === $current || ( is_singular( 'property' ) && estatein_page_url( 'properties' ) === $url ) ? ' aria-current="page"' : '',
			esc_html( $label )
		);
	}
	echo '</ul>';
}

/**
 * Footer columns from the design, used when no footer menu is assigned.
 *
 * @return array title => array( label => link token )
 */
function estatein_default_footer_columns() {
	return array(
		__( 'Home', 'estatein' )       => array(
			__( 'Hero Section', 'estatein' ) => '@home#hero',
			__( 'Features', 'estatein' )     => '@home#features',
			__( 'Properties', 'estatein' )   => '@home#featured-properties',
			__( 'Testimonials', 'estatein' ) => '@home#testimonials',
			__( 'FAQ’s', 'estatein' )        => '@home#faqs',
		),
		__( 'About Us', 'estatein' )   => array(
			__( 'Our Story', 'estatein' )    => '@about#our-story',
			__( 'Our Works', 'estatein' )    => '@about#our-works',
			__( 'How It Works', 'estatein' ) => '@about#how-it-works',
			__( 'Our Team', 'estatein' )     => '@about#our-team',
			__( 'Our Clients', 'estatein' )  => '@about#our-clients',
		),
		__( 'Properties', 'estatein' ) => array(
			__( 'Portfolio', 'estatein' )  => '@properties#portfolio',
			__( 'Categories', 'estatein' ) => '@properties#search',
		),
		__( 'Services', 'estatein' )   => array(
			__( 'Valuation Mastery', 'estatein' )    => '@services#valuation-mastery',
			__( 'Strategic Marketing', 'estatein' )  => '@services#strategic-marketing',
			__( 'Negotiation Wizardry', 'estatein' ) => '@services#negotiation-wizardry',
			__( 'Closing Success', 'estatein' )      => '@services#closing-success',
			__( 'Property Management', 'estatein' )  => '@services#property-management',
		),
		__( 'Contact Us', 'estatein' ) => array(
			__( 'Contact Form', 'estatein' ) => '@contact#contact-form',
			__( 'Our Offices', 'estatein' )  => '@contact#offices',
		),
	);
}

/**
 * Footer columns as title => array( label => url ), from the footer menu or the defaults.
 *
 * @return array
 */
function estatein_footer_columns() {
	$locations = get_nav_menu_locations();
	if ( ! empty( $locations['footer'] ) ) {
		$items = wp_get_nav_menu_items( $locations['footer'] );
		if ( $items ) {
			$columns = array();
			$titles  = array();
			foreach ( $items as $item ) {
				if ( ! $item->menu_item_parent ) {
					$titles[ $item->ID ]          = $item->title;
					$columns[ $item->title ]      = array();
				}
			}
			foreach ( $items as $item ) {
				if ( $item->menu_item_parent && isset( $titles[ $item->menu_item_parent ] ) ) {
					$columns[ $titles[ $item->menu_item_parent ] ][ $item->title ] = $item->url;
				}
			}
			return $columns;
		}
	}

	$columns = array();
	foreach ( estatein_default_footer_columns() as $title => $links ) {
		foreach ( $links as $label => $token ) {
			$columns[ $title ][ $label ] = estatein_link( $token );
		}
	}
	return $columns;
}
