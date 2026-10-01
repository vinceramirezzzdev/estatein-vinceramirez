<?php
/**
 * Form handling (no plugin needed).
 *
 * All forms post to the same "estatein_form" action — via fetch() to
 * admin-ajax.php when JavaScript runs, or a normal POST to admin-post.php
 * without it. Each submission is
 *   1. checked (nonce, honeypot, required fields, email format),
 *   2. saved as an "Enquiry" post (visible under Enquiries in the dashboard),
 *   3. emailed to the address set in Customizer › Estatein › Contact details.
 * Saving first means nothing is lost on hosts where PHP mail is disabled.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Fields accepted per form type: name => label. Required fields are listed separately.
 *
 * @return array
 */
function estatein_form_types() {
	$person = array(
		'first_name' => __( 'First name', 'estatein' ),
		'last_name'  => __( 'Last name', 'estatein' ),
		'email'      => __( 'Email', 'estatein' ),
		'phone'      => __( 'Phone', 'estatein' ),
	);
	return array(
		'contact'          => array(
			'label'    => __( 'Contact form', 'estatein' ),
			'fields'   => $person + array(
				'inquiry_type' => __( 'Inquiry type', 'estatein' ),
				'source'       => __( 'How did you hear about us?', 'estatein' ),
				'message'      => __( 'Message', 'estatein' ),
			),
			'required' => array( 'first_name', 'last_name', 'email', 'message', 'agree' ),
		),
		'property_inquiry' => array(
			'label'    => __( 'Property search request', 'estatein' ),
			'fields'   => $person + array(
				'location'       => __( 'Preferred location', 'estatein' ),
				'property_type'  => __( 'Property type', 'estatein' ),
				'bathrooms'      => __( 'Bathrooms', 'estatein' ),
				'bedrooms'       => __( 'Bedrooms', 'estatein' ),
				'budget'         => __( 'Budget', 'estatein' ),
				'contact_method' => __( 'Preferred contact method', 'estatein' ),
				'contact_phone'  => __( 'Contact phone', 'estatein' ),
				'contact_email'  => __( 'Contact email', 'estatein' ),
				'message'        => __( 'Message', 'estatein' ),
			),
			'required' => array( 'first_name', 'last_name', 'email', 'agree' ),
		),
		'property_enquiry' => array(
			'label'    => __( 'Property enquiry', 'estatein' ),
			'fields'   => $person + array(
				'property' => __( 'Selected property', 'estatein' ),
				'message'  => __( 'Message', 'estatein' ),
			),
			'required' => array( 'first_name', 'last_name', 'email', 'agree' ),
		),
		'newsletter'       => array(
			'label'    => __( 'Newsletter sign-up', 'estatein' ),
			'fields'   => array( 'email' => __( 'Email', 'estatein' ) ),
			'required' => array( 'email' ),
		),
	);
}

/**
 * Hidden fields every theme form needs (action, type, nonce, honeypot, return URL).
 *
 * @param string $type Form type key from estatein_form_types().
 */
function estatein_form_hidden_fields( $type ) {
	printf( '<input type="hidden" name="action" value="estatein_form">' );
	printf( '<input type="hidden" name="form_type" value="%s">', esc_attr( $type ) );
	printf( '<input type="hidden" name="redirect_to" value="%s">', esc_url( home_url( add_query_arg( array() ) ) ) );
	wp_nonce_field( 'estatein_form', 'estatein_nonce', false );
	// Honeypot: invisible to people, tempting to bots.
	echo '<div class="visually-hidden" aria-hidden="true"><label>' . esc_html__( 'Leave this field empty', 'estatein' ) . '<input type="text" name="company_site" tabindex="-1" autocomplete="off"></label></div>';
}

/**
 * Attributes for a theme <form> (no-JS action + AJAX endpoint for js/forms.js).
 *
 * @param string $type Form type.
 * @return string
 */
function estatein_form_attrs( $type ) {
	return sprintf(
		'action="%1$s" method="post" data-form="%2$s" data-endpoint="%3$s" novalidate',
		esc_url( admin_url( 'admin-post.php' ) ),
		esc_attr( $type ),
		esc_url( admin_url( 'admin-ajax.php' ) )
	);
}

/**
 * Status message shown after a no-JS submission (?enquiry=sent|error).
 *
 * @param string $type Form type rendered here.
 */
function estatein_form_status( $type ) {
	$status = isset( $_GET['enquiry'], $_GET['form'] ) && sanitize_key( wp_unslash( $_GET['form'] ) ) === $type ? sanitize_key( wp_unslash( $_GET['enquiry'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$text   = '';
	if ( 'sent' === $status ) {
		$text = estatein_form_success_message( $type );
	} elseif ( 'error' === $status ) {
		$text = __( 'Please check the highlighted fields and try again.', 'estatein' );
	}
	printf(
		'<p class="form-status" role="status" aria-live="polite"%1$s>%2$s</p>',
		$status ? ' data-state="' . ( 'sent' === $status ? 'success' : 'error' ) . '"' : '',
		esc_html( $text )
	);
}

/**
 * @param string $type Form type.
 * @return string
 */
function estatein_form_success_message( $type ) {
	return 'newsletter' === $type
		? __( 'Thanks for subscribing!', 'estatein' )
		: __( 'Thank you! Your message has been sent — our team will get back to you shortly.', 'estatein' );
}

/**
 * Handle a submission (AJAX or regular POST).
 */
function estatein_handle_form() {
	$ajax  = wp_doing_ajax();
	$types = estatein_form_types();
	$type  = isset( $_POST['form_type'] ) ? sanitize_key( wp_unslash( $_POST['form_type'] ) ) : '';

	$respond = function ( $ok, $message, $field_errors = array() ) use ( $ajax, $type ) {
		if ( $ajax ) {
			if ( $ok ) {
				wp_send_json_success( array( 'message' => $message ) );
			}
			wp_send_json_error( array( 'message' => $message, 'fields' => $field_errors ), 422 );
		}
		$back = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : home_url( '/' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		wp_safe_redirect( add_query_arg( array( 'enquiry' => $ok ? 'sent' : 'error', 'form' => $type ), wp_validate_redirect( $back, home_url( '/' ) ) ) );
		exit;
	};

	if ( ! isset( $_POST['estatein_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['estatein_nonce'] ) ), 'estatein_form' ) ) {
		$respond( false, __( 'Your session has expired. Please reload the page and try again.', 'estatein' ) );
	}
	if ( ! isset( $types[ $type ] ) ) {
		$respond( false, __( 'Unknown form.', 'estatein' ) );
	}
	// Bots fill every field: accept silently and drop the submission.
	if ( ! empty( $_POST['company_site'] ) ) {
		$respond( true, estatein_form_success_message( $type ) );
	}

	$config = $types[ $type ];
	$data   = array();
	foreach ( $config['fields'] as $name => $label ) {
		$raw           = isset( $_POST[ $name ] ) ? wp_unslash( $_POST[ $name ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$data[ $name ] = 'message' === $name ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw );
	}

	$errors = array();
	foreach ( $config['required'] as $name ) {
		if ( 'agree' === $name ) {
			if ( empty( $_POST['agree'] ) ) {
				$errors[] = 'agree';
			}
		} elseif ( '' === $data[ $name ] ) {
			$errors[] = $name;
		}
	}
	if ( '' !== $data['email'] && ! is_email( $data['email'] ) ) {
		$errors[] = 'email';
	}
	if ( $errors ) {
		$respond( false, __( 'Please check the highlighted fields.', 'estatein' ), array_values( array_unique( $errors ) ) );
	}

	$name = trim( ( isset( $data['first_name'] ) ? $data['first_name'] : '' ) . ' ' . ( isset( $data['last_name'] ) ? $data['last_name'] : '' ) );
	$post = wp_insert_post(
		array(
			'post_type'   => 'enquiry',
			'post_status' => 'publish',
			/* translators: 1: form name, 2: sender name or email. */
			'post_title'  => sprintf( __( '%1$s — %2$s', 'estatein' ), $config['label'], $name ? $name : $data['email'] ),
		),
		true
	);
	if ( is_wp_error( $post ) ) {
		$respond( false, __( 'Something went wrong. Please try again later.', 'estatein' ) );
	}
	update_post_meta( $post, '_form_type', $type );
	foreach ( $data as $key => $value ) {
		update_post_meta( $post, $key, $value );
	}
	update_post_meta( $post, '_page_url', isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : '' );

	// Email notification (best effort — the enquiry is already stored).
	$to    = estatein_option( 'enquiry_email' );
	$to    = is_email( $to ) ? $to : get_option( 'admin_email' );
	$lines = array();
	foreach ( $config['fields'] as $key => $label ) {
		if ( '' !== $data[ $key ] ) {
			$lines[] = $label . ': ' . $data[ $key ];
		}
	}
	$lines[] = '';
	$lines[] = admin_url( 'post.php?post=' . $post . '&action=edit' );
	wp_mail(
		$to,
		/* translators: 1: site name, 2: form name. */
		sprintf( __( '[%1$s] New %2$s', 'estatein' ), wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), strtolower( $config['label'] ) ),
		implode( "\n", $lines ),
		array( 'Reply-To: ' . ( $name ? $name : $data['email'] ) . ' <' . $data['email'] . '>' )
	);

	$respond( true, estatein_form_success_message( $type ) );
}
add_action( 'admin_post_estatein_form', 'estatein_handle_form' );
add_action( 'admin_post_nopriv_estatein_form', 'estatein_handle_form' );
add_action( 'wp_ajax_estatein_form', 'estatein_handle_form' );
add_action( 'wp_ajax_nopriv_estatein_form', 'estatein_handle_form' );
