<?php
/**
 * Customizer: site-wide copy and settings (Appearance › Customize › Estatein).
 * Settings and defaults come from estatein_global_fields() in inc/content.php.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the Estatein panel, sections and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 */
function estatein_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'estatein',
		array(
			'title'    => __( 'Estatein', 'estatein' ),
			'priority' => 30,
		)
	);

	$sections = array(
		'header'   => __( 'Header & banner', 'estatein' ),
		'features' => __( 'Feature tiles (Home / Services)', 'estatein' ),
		'faq'      => __( 'FAQ section', 'estatein' ),
		'details'  => __( 'Property details page', 'estatein' ),
		'cta'      => __( 'Call to action band', 'estatein' ),
		'contact'  => __( 'Contact details & enquiries', 'estatein' ),
		'social'   => __( 'Social links', 'estatein' ),
		'footer'   => __( 'Footer', 'estatein' ),
	);
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section(
			'estatein_' . $id,
			array(
				'title' => $title,
				'panel' => 'estatein',
			)
		);
	}

	foreach ( estatein_global_fields() as $key => $field ) {
		list( $type, $label, $default, $section ) = $field;

		$sanitize = 'sanitize_text_field';
		if ( 'checkbox' === $type ) {
			$sanitize = 'rest_sanitize_boolean';
		} elseif ( 'url' === $type ) {
			$sanitize = 'esc_url_raw';
		} elseif ( 'textarea' === $type ) {
			$sanitize = 'estatein_kses';
		} elseif ( 'enquiry_email' === $key ) {
			$sanitize = 'sanitize_email';
		}

		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $default,
				'sanitize_callback' => $sanitize,
				'transport'         => 'refresh',
			)
		);

		$control = array(
			'label'   => $label,
			'section' => 'estatein_' . $section,
			'type'    => 'url' === $type ? 'url' : $type,
		);
		if ( false !== strpos( $key, '_link' ) ) {
			$control['description'] = __( 'A URL, or a theme shortcut such as @properties or @contact#contact-form.', 'estatein' );
		}
		if ( 'enquiry_email' === $key ) {
			$control['description'] = __( 'Leave empty to use the site admin email.', 'estatein' );
		}
		$wp_customize->add_control( $key, $control );
	}
}
add_action( 'customize_register', 'estatein_customize_register' );
