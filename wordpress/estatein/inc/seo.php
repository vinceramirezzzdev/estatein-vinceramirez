<?php
/**
 * Basic SEO: meta description, Open Graph / Twitter cards and JSON-LD.
 * Skipped automatically when an SEO plugin (Yoast, Rank Math, SEOPress, AIOSEO) is active.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether a dedicated SEO plugin handles the head tags.
 *
 * @return bool
 */
function estatein_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'AIOSEO_VERSION' );
}

/**
 * Meta descriptions for the designed pages (taken from each page's intro copy).
 *
 * @return string
 */
function estatein_meta_description() {
	$text = '';
	if ( is_front_page() ) {
		$text = estatein_field( 'home_hero_copy', (int) get_option( 'page_on_front' ) );
	} elseif ( is_page_template( 'page-templates/about.php' ) ) {
		$text = estatein_field( 'about_journey_copy' );
	} elseif ( is_page_template( 'page-templates/properties.php' ) ) {
		$text = estatein_field( 'props_hero_copy' );
	} elseif ( is_page_template( 'page-templates/services.php' ) ) {
		$text = estatein_field( 'services_hero_copy' );
	} elseif ( is_page_template( 'page-templates/contact.php' ) ) {
		$text = estatein_field( 'contact_hero_copy' );
	} elseif ( is_singular() ) {
		$post = get_queried_object();
		$text = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( (string) $post->post_content ), 30, '' );
	} elseif ( is_post_type_archive() || is_tax() || is_category() || is_tag() ) {
		$text = get_the_archive_description();
	}
	if ( ! $text ) {
		$text = get_bloginfo( 'description' );
	}
	$text = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( (string) $text ) ) );
	return mb_strlen( $text ) > 160 ? rtrim( mb_substr( $text, 0, 157 ) ) . '…' : $text;
}

/**
 * Print description, canonical-friendly Open Graph and Twitter tags.
 */
function estatein_seo_head() {
	if ( estatein_seo_plugin_active() ) {
		return;
	}
	$description = estatein_meta_description();
	$title       = wp_get_document_title();
	$url         = is_singular() ? get_permalink() : home_url( add_query_arg( array() ) );
	$image       = is_singular() && has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'large' ) : estatein_asset( 'images/hero-buildings-1080.webp' );

	if ( $description ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
	}
	printf( '<meta property="og:type" content="%s">' . "\n", is_singular( 'post' ) ? 'article' : 'website' );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	if ( $description ) {
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
	}
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
}
add_action( 'wp_head', 'estatein_seo_head', 3 );

/**
 * JSON-LD: the agency on every page, plus a listing on property pages.
 */
function estatein_json_ld() {
	if ( estatein_seo_plugin_active() ) {
		return;
	}
	$graph = array(
		array(
			'@type'     => 'RealEstateAgent',
			'@id'       => home_url( '/#organization' ),
			'name'      => get_bloginfo( 'name' ),
			'url'       => home_url( '/' ),
			'logo'      => estatein_asset( 'icons/favicon.svg' ),
			'email'     => estatein_option( 'contact_email' ),
			'telephone' => estatein_option( 'contact_phone' ),
			'sameAs'    => array_values( array_filter( array( estatein_option( 'social_facebook' ), estatein_option( 'social_linkedin' ), estatein_option( 'social_twitter' ), estatein_option( 'social_youtube' ) ) ) ),
		),
	);

	if ( is_singular( 'property' ) ) {
		$id       = get_queried_object_id();
		$location = get_the_terms( $id, 'property_location' );
		$listing  = array(
			'@type'       => 'RealEstateListing',
			'name'        => get_the_title( $id ),
			'url'         => get_permalink( $id ),
			'description' => estatein_meta_description(),
			'offers'      => array(
				'@type'         => 'Offer',
				'price'         => (float) estatein_field( 'price', $id ),
				'priceCurrency' => 'USD',
			),
		);
		if ( has_post_thumbnail( $id ) ) {
			$listing['image'] = get_the_post_thumbnail_url( $id, 'large' );
		}
		if ( $location && ! is_wp_error( $location ) ) {
			$listing['contentLocation'] = array(
				'@type' => 'Place',
				'name'  => $location[0]->name,
			);
		}
		$graph[] = $listing;
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
	);
}
add_action( 'wp_head', 'estatein_json_ld', 20 );

/**
 * Add the theme's content post types to the core XML sitemap (wp-sitemap.xml)
 * and keep the private ones out.
 *
 * @param WP_Post_Type[] $post_types Post types in the sitemap.
 * @return WP_Post_Type[]
 */
function estatein_sitemap_post_types( $post_types ) {
	foreach ( array( 'team_member', 'client', 'service', 'office', 'enquiry' ) as $private ) {
		unset( $post_types[ $private ] );
	}
	return $post_types;
}
add_filter( 'wp_sitemaps_post_types', 'estatein_sitemap_post_types' );
