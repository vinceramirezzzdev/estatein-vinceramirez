<?php
/**
 * Template Name: Properties
 *
 * Search + filters (server side, via GET), the property listing carousel and
 * the "Let's Make it Happen" request form.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();
$estatein_page = get_the_ID();

// Read the filters from the query string.
$estatein_filters = array();
foreach ( array( 'keyword', 'location', 'type', 'price', 'size', 'year' ) as $estatein_key ) {
	$estatein_filters[ $estatein_key ] = isset( $_GET[ $estatein_key ] ) ? sanitize_text_field( wp_unslash( $_GET[ $estatein_key ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}
$estatein_active = (bool) array_filter( $estatein_filters );

/**
 * meta_query clause for a "min-max" range value such as "500000-1000000" or "1000000-".
 */
$estatein_range = function ( $key, $range ) {
	$parts = array_map( 'trim', explode( '-', $range, 2 ) );
	$min   = '' !== $parts[0] ? (float) $parts[0] : null;
	$max   = isset( $parts[1] ) && '' !== $parts[1] ? (float) $parts[1] : null;
	if ( null !== $min && null !== $max ) {
		return array( 'key' => $key, 'value' => array( $min, $max ), 'compare' => 'BETWEEN', 'type' => 'NUMERIC' );
	}
	if ( null !== $min ) {
		return array( 'key' => $key, 'value' => $min, 'compare' => '>=', 'type' => 'NUMERIC' );
	}
	return array( 'key' => $key, 'value' => $max, 'compare' => '<=', 'type' => 'NUMERIC' );
};

$estatein_query = array(
	'posts_per_page' => 60,
	'tax_query'      => array(), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	'meta_query'     => array(), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
);
if ( $estatein_filters['keyword'] ) {
	$estatein_query['s'] = $estatein_filters['keyword'];
}
if ( $estatein_filters['location'] ) {
	$estatein_query['tax_query'][] = array( 'taxonomy' => 'property_location', 'field' => 'slug', 'terms' => $estatein_filters['location'] );
}
if ( $estatein_filters['type'] ) {
	$estatein_query['tax_query'][] = array( 'taxonomy' => 'property_category', 'field' => 'slug', 'terms' => $estatein_filters['type'] );
}
if ( $estatein_filters['price'] ) {
	$estatein_query['meta_query'][] = $estatein_range( 'price', $estatein_filters['price'] );
}
if ( $estatein_filters['size'] ) {
	$estatein_query['meta_query'][] = $estatein_range( 'size_sqft', $estatein_filters['size'] );
}
if ( $estatein_filters['year'] ) {
	$estatein_query['meta_query'][] = array( 'key' => 'build_year', 'value' => (int) $estatein_filters['year'], 'type' => 'NUMERIC' );
}
$estatein_properties = estatein_get_items( 'property', $estatein_query );
?>

<main id="main">
	<?php
	get_template_part(
		'template-parts/page-hero',
		null,
		array(
			'title'    => estatein_field( 'props_hero_title', $estatein_page ),
			'copy'     => estatein_field( 'props_hero_copy', $estatein_page ),
			'modifier' => 'search',
		)
	);
	get_template_part( 'template-parts/forms/property-search', null, array( 'values' => $estatein_filters ) );
	?>

	<section class="section" id="portfolio" aria-labelledby="portfolio-title">
		<div class="container">
			<?php
			get_template_part(
				'template-parts/section-head',
				null,
				array(
					'id'       => 'portfolio-title',
					'title'    => estatein_field( 'props_portfolio_title', $estatein_page ),
					'copy'     => estatein_field( 'props_portfolio_copy', $estatein_page ),
					'modifier' => 'wide',
				)
			);
			get_template_part(
				'template-parts/carousel',
				null,
				array(
					'posts' => $estatein_properties,
					'card'  => 'property-listing',
					// Matches the design's "01 of 10"; filtered results show their real count.
					'total' => $estatein_active ? 0 : 10,
					'empty' => __( 'No properties match your search. Try a different keyword or clear a filter.', 'estatein' ),
				)
			);
			?>
		</div>
	</section>

	<section class="section" id="inquiry" aria-labelledby="inquiry-title">
		<div class="container">
			<?php
			get_template_part(
				'template-parts/section-head',
				null,
				array(
					'id'       => 'inquiry-title',
					'title'    => estatein_field( 'props_form_title', $estatein_page ),
					'copy'     => estatein_field( 'props_form_copy', $estatein_page ),
					'modifier' => 'narrow',
				)
			);
			get_template_part( 'template-parts/forms/property-inquiry' );
			?>
		</div>
	</section>
</main>

<?php
get_footer();
