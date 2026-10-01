<?php
/**
 * Property search bar + filters (submits a GET request to the Properties page).
 *
 * @package Estatein
 *
 * @var array $args { @type array $values Current filter values. }
 */

defined( 'ABSPATH' ) || exit;

$estatein_values = isset( $args['values'] ) ? $args['values'] : array();
$estatein_value  = function ( $key ) use ( $estatein_values ) {
	return isset( $estatein_values[ $key ] ) ? (string) $estatein_values[ $key ] : '';
};

$estatein_terms = function ( $taxonomy ) {
	$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => true ) );
	return is_wp_error( $terms ) ? array() : wp_list_pluck( $terms, 'name', 'slug' );
};

global $wpdb;
$estatein_years = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	"SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
	 WHERE pm.meta_key = 'build_year' AND pm.meta_value <> '' AND p.post_type = 'property' AND p.post_status = 'publish' ORDER BY pm.meta_value DESC"
);

$estatein_filters = array(
	'location' => array( __( 'Location', 'estatein' ), 'location', $estatein_terms( 'property_location' ) ),
	'type'     => array( __( 'Property Type', 'estatein' ), 'property-type', $estatein_terms( 'property_category' ) ),
	'price'    => array( __( 'Pricing Range', 'estatein' ), 'pricing', estatein_price_ranges() ),
	'size'     => array(
		__( 'Property Size', 'estatein' ),
		'size',
		array(
			'0-2000' => __( 'Up to 2,000 Square Feet', 'estatein' ),
			'2000-'  => __( '2,000+ Square Feet', 'estatein' ),
		),
	),
	'year'     => array( __( 'Build Year', 'estatein' ), 'calendar', array_combine( $estatein_years, $estatein_years ) ),
);
?>
<form class="property-search" id="search" role="search" action="<?php echo esc_url( get_permalink() ); ?>#portfolio" method="get" data-property-search="server">
	<div class="property-search__bar">
		<label class="visually-hidden" for="search-keyword"><?php esc_html_e( 'Search for a property', 'estatein' ); ?></label>
		<input class="property-search__input" id="search-keyword" type="search" name="keyword" value="<?php echo esc_attr( $estatein_value( 'keyword' ) ); ?>" placeholder="<?php echo esc_attr( estatein_field( 'props_search_placeholder' ) ); ?>" autocomplete="off">
		<button class="btn btn--primary property-search__btn" type="submit">
			<?php echo estatein_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span class="property-search__btn-label"><?php estatein_the_text( 'props_search_btn' ); ?></span>
		</button>
	</div>
	<div class="property-search__filters">
		<?php foreach ( $estatein_filters as $estatein_name => $estatein_filter ) : ?>
			<div class="filter">
				<?php echo estatein_icon( $estatein_filter[1], 'filter__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<label class="visually-hidden" for="filter-<?php echo esc_attr( $estatein_name ); ?>"><?php echo esc_html( $estatein_filter[0] ); ?></label>
				<select class="filter__select" id="filter-<?php echo esc_attr( $estatein_name ); ?>" name="<?php echo esc_attr( $estatein_name ); ?>">
					<option value=""><?php echo esc_html( $estatein_filter[0] ); ?></option>
					<?php foreach ( $estatein_filter[2] as $estatein_option_value => $estatein_option_label ) : ?>
						<option value="<?php echo esc_attr( $estatein_option_value ); ?>"<?php selected( $estatein_value( $estatein_name ), (string) $estatein_option_value ); ?>><?php echo esc_html( $estatein_option_label ); ?></option>
					<?php endforeach; ?>
				</select>
				<span class="filter__chevron" aria-hidden="true"><?php echo estatein_icon( 'chevron-down' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</form>
