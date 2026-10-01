<?php
/**
 * Advanced Custom Fields (free) field groups, registered in code so they are
 * version-controlled and need no import.
 *
 * Without ACF the theme keeps working: values are read from post meta
 * (estatein_field()) and fall back to the design copy in inc/content.php.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * The "Comprehensive Pricing Details" cards on a property, as rows of costs.
 * Each cost stores two fields: cost_{key}_value and cost_{key}_note.
 *
 * @return array
 */
function estatein_pricing_sections() {
	return array(
		'additional' => array(
			'title' => __( 'Additional Fees', 'estatein' ),
			'rows'  => array(
				array( 'transfer_tax' => __( 'Property Transfer Tax', 'estatein' ), 'legal_fees' => __( 'Legal Fees', 'estatein' ) ),
				array( 'home_inspection' => __( 'Home Inspection', 'estatein' ), 'insurance' => __( 'Property Insurance', 'estatein' ) ),
				array( 'mortgage_fees' => __( 'Mortgage Fees', 'estatein' ) ),
			),
		),
		'monthly'    => array(
			'title' => __( 'Monthly Costs', 'estatein' ),
			'rows'  => array(
				array( 'monthly_tax' => __( 'Property Taxes', 'estatein' ) ),
				array( 'monthly_hoa' => __( "Homeowners' Association Fee", 'estatein' ) ),
			),
		),
		'initial'    => array(
			'title' => __( 'Total Initial Costs', 'estatein' ),
			'rows'  => array(
				array( 'initial_listing' => __( 'Listing Price', 'estatein' ), 'initial_fees' => __( 'Additional Fees', 'estatein' ) ),
				array( 'down_payment' => __( 'Down Payment', 'estatein' ), 'mortgage_amount' => __( 'Mortgage Amount', 'estatein' ) ),
			),
		),
		'expenses'   => array(
			'title' => __( 'Monthly Expenses', 'estatein' ),
			'rows'  => array(
				array( 'expense_tax' => __( 'Property Taxes', 'estatein' ), 'expense_hoa' => __( "Homeowners' Association Fee", 'estatein' ) ),
				array( 'expense_mortgage' => __( 'Mortgage Payment', 'estatein' ), 'expense_insurance' => __( 'Property Insurance', 'estatein' ) ),
			),
		),
	);
}

/**
 * Icons that can be picked for a service card (symbols in the sprite).
 *
 * @return array
 */
function estatein_service_icons() {
	return array(
		'service-valuation'   => __( 'Chart (valuation / market insight)', 'estatein' ),
		'service-marketing'   => __( 'Pie chart (marketing)', 'estatein' ),
		'service-negotiation' => __( 'Coins (negotiation)', 'estatein' ),
		'service-closing'     => __( 'Megaphone (closing)', 'estatein' ),
		'service-tenant'      => __( 'Grid (tenants)', 'estatein' ),
		'service-maintenance' => __( 'Tools (maintenance)', 'estatein' ),
		'service-finance'     => __( 'Sparkles (finance)', 'estatein' ),
		'service-roi'         => __( 'Flame (ROI)', 'estatein' ),
		'service-strategy'    => __( 'Light bulb (strategy)', 'estatein' ),
		'feature-investment'  => __( 'Sun (legal / diversification)', 'estatein' ),
	);
}

/**
 * Build one ACF field array.
 *
 * @param string $name  Field name.
 * @param string $type  ACF type.
 * @param string $label Label.
 * @param array  $extra Extra ACF settings.
 * @return array
 */
function estatein_acf_field( $name, $type, $label, $extra = array() ) {
	$field = array_merge(
		array(
			'key'   => 'field_estatein_' . $name,
			'name'  => $name,
			'label' => $label,
			'type'  => $type,
		),
		$extra
	);
	if ( 'image' === $type ) {
		$field['return_format'] = 'id';
		$field['preview_size']  = 'medium';
	}
	if ( 'textarea' === $type ) {
		$field['rows']      = 3;
		$field['new_lines'] = '';
	}
	return $field;
}

/**
 * Register all field groups.
 */
function estatein_register_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	// Page content groups, one per page template (tabs mirror the page sections).
	foreach ( estatein_page_fields() as $slug => $group ) {
		$fields = array();
		foreach ( $group['tabs'] as $tab => $tab_fields ) {
			$fields[] = estatein_acf_field( $slug . '_tab_' . sanitize_key( $tab ), 'tab', $tab );
			foreach ( $tab_fields as $name => $field ) {
				$fields[] = estatein_acf_field(
					$name,
					$field[0],
					$field[1],
					array(
						'default_value' => 'image' === $field[0] ? '' : $field[2],
						'instructions'  => 'text' === $field[0] && false !== strpos( $name, '_link' ) ? __( 'A URL, or a theme shortcut such as @properties, @about#our-team, @contact#contact-form.', 'estatein' ) : '',
					)
				);
			}
		}
		acf_add_local_field_group(
			array(
				'key'        => 'group_estatein_' . $slug,
				'title'      => $group['title'],
				'fields'     => $fields,
				'location'   => array( array( array( 'param' => $group['location'][0], 'operator' => '==', 'value' => $group['location'][1] ) ) ),
				'position'   => 'acf_after_title',
				'menu_order' => 0,
			)
		);
	}

	// Property details.
	$property_fields = array(
		estatein_acf_field( 'property_tab_details', 'tab', __( 'Details', 'estatein' ) ),
		estatein_acf_field( 'price', 'number', __( 'Price (USD)', 'estatein' ), array( 'required' => 1 ) ),
		estatein_acf_field( 'featured', 'true_false', __( 'Show in "Featured Properties" on the Home page', 'estatein' ), array( 'ui' => 1 ) ),
		estatein_acf_field( 'bedrooms', 'number', __( 'Bedrooms', 'estatein' ) ),
		estatein_acf_field( 'bathrooms', 'number', __( 'Bathrooms', 'estatein' ) ),
		estatein_acf_field( 'property_type', 'text', __( 'Type label on the Home card (e.g. Villa)', 'estatein' ) ),
		estatein_acf_field( 'area', 'text', __( 'Area label (e.g. 2,500 Square Feet)', 'estatein' ) ),
		estatein_acf_field( 'size_sqft', 'number', __( 'Size in square feet (used by the size filter)', 'estatein' ) ),
		estatein_acf_field( 'build_year', 'number', __( 'Build year (used by the year filter)', 'estatein' ) ),
		estatein_acf_field( 'card_text', 'textarea', __( 'Card text on the Home page', 'estatein' ), array( 'instructions' => __( 'Defaults to the excerpt.', 'estatein' ) ) ),
		estatein_acf_field( 'listing_text', 'textarea', __( 'Card text on the Properties page', 'estatein' ), array( 'instructions' => __( 'Defaults to the excerpt.', 'estatein' ) ) ),
		estatein_acf_field( 'features', 'textarea', __( 'Key features and amenities (one per line)', 'estatein' ), array( 'rows' => 6 ) ),
		estatein_acf_field( 'property_tab_pricing', 'tab', __( 'Pricing details', 'estatein' ) ),
	);
	foreach ( estatein_pricing_sections() as $section ) {
		$property_fields[] = estatein_acf_field( 'pricing_msg_' . sanitize_key( $section['title'] ), 'message', $section['title'], array( 'message' => '' ) );
		foreach ( $section['rows'] as $row ) {
			foreach ( $row as $key => $label ) {
				$property_fields[] = estatein_acf_field( "cost_{$key}_value", 'text', $label, array( 'wrapper' => array( 'width' => 40 ) ) );
				$property_fields[] = estatein_acf_field( "cost_{$key}_note", 'text', __( 'Note', 'estatein' ), array( 'wrapper' => array( 'width' => 60 ) ) );
			}
		}
	}
	acf_add_local_field_group(
		array(
			'key'      => 'group_estatein_property',
			'title'    => __( 'Property details', 'estatein' ),
			'fields'   => $property_fields,
			'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'property' ) ) ),
			'position' => 'acf_after_title',
		)
	);

	$simple_groups = array(
		'testimonial' => array(
			__( 'Testimonial', 'estatein' ),
			array(
				estatein_acf_field( 'client_name', 'text', __( 'Client name', 'estatein' ) ),
				estatein_acf_field( 'client_location', 'text', __( 'Location (e.g. USA, California)', 'estatein' ) ),
				estatein_acf_field( 'rating', 'number', __( 'Rating (1–5)', 'estatein' ), array( 'min' => 1, 'max' => 5, 'default_value' => 5 ) ),
			),
		),
		'team_member' => array(
			__( 'Team member', 'estatein' ),
			array(
				estatein_acf_field( 'role', 'text', __( 'Role', 'estatein' ) ),
				estatein_acf_field( 'social_url', 'url', __( 'X (Twitter) profile URL', 'estatein' ) ),
			),
		),
		'client'      => array(
			__( 'Client', 'estatein' ),
			array(
				estatein_acf_field( 'since', 'text', __( '"Since" label (e.g. Since 2019)', 'estatein' ) ),
				estatein_acf_field( 'website', 'url', __( 'Website', 'estatein' ) ),
				estatein_acf_field( 'domain', 'text', __( 'Domain', 'estatein' ) ),
				estatein_acf_field( 'category', 'text', __( 'Category', 'estatein' ) ),
				estatein_acf_field( 'quote', 'textarea', __( 'What they said', 'estatein' ) ),
			),
		),
		'service'     => array(
			__( 'Service', 'estatein' ),
			array(
				estatein_acf_field( 'icon', 'select', __( 'Icon', 'estatein' ), array( 'choices' => estatein_service_icons() ) ),
			),
		),
		'office'      => array(
			__( 'Office', 'estatein' ),
			array(
				estatein_acf_field( 'office_label', 'text', __( 'Label (e.g. Main Headquarters)', 'estatein' ) ),
				estatein_acf_field( 'address', 'text', __( 'Address', 'estatein' ) ),
				estatein_acf_field( 'email', 'email', __( 'Email', 'estatein' ) ),
				estatein_acf_field( 'phone', 'text', __( 'Phone', 'estatein' ) ),
				estatein_acf_field( 'city', 'text', __( 'City', 'estatein' ) ),
				estatein_acf_field( 'map_url', 'url', __( 'Directions URL (defaults to Google Maps for the address)', 'estatein' ) ),
			),
		),
	);
	foreach ( $simple_groups as $type => $group ) {
		acf_add_local_field_group(
			array(
				'key'      => 'group_estatein_' . $type,
				'title'    => $group[0],
				'fields'   => $group[1],
				'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => $type ) ) ),
				'position' => 'acf_after_title',
			)
		);
	}
}
add_action( 'acf/init', 'estatein_register_fields' );

/**
 * ACF field key for a theme field name (used by the demo importer so imported
 * values show up in the ACF editor).
 *
 * @param string $name Field name.
 * @return string
 */
function estatein_field_key( $name ) {
	return 'field_estatein_' . $name;
}
