<?php
/**
 * Dashboard helpers: list columns, enquiry viewer, ACF notice.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Property list columns.
 *
 * @param string[] $columns Columns.
 * @return string[]
 */
function estatein_property_columns( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		if ( 'title' === $key ) {
			$new['thumbnail'] = __( 'Image', 'estatein' );
		}
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['price']    = __( 'Price', 'estatein' );
			$new['featured'] = __( 'Featured', 'estatein' );
		}
	}
	return $new;
}
add_filter( 'manage_property_posts_columns', 'estatein_property_columns' );

/**
 * @param string $column  Column key.
 * @param int    $post_id Property ID.
 */
function estatein_property_column_content( $column, $post_id ) {
	if ( 'thumbnail' === $column ) {
		echo get_the_post_thumbnail( $post_id, array( 60, 60 ), array( 'style' => 'border-radius:6px;object-fit:cover' ) );
	} elseif ( 'price' === $column ) {
		echo esc_html( estatein_price( estatein_field( 'price', $post_id ) ) );
	} elseif ( 'featured' === $column ) {
		echo estatein_field( 'featured', $post_id ) ? '★' : '—';
	}
}
add_action( 'manage_property_posts_custom_column', 'estatein_property_column_content', 10, 2 );

/**
 * Enquiry list columns.
 *
 * @param string[] $columns Columns.
 * @return string[]
 */
function estatein_enquiry_columns( $columns ) {
	return array(
		'cb'        => $columns['cb'],
		'title'     => __( 'Enquiry', 'estatein' ),
		'email'     => __( 'Email', 'estatein' ),
		'form_type' => __( 'Form', 'estatein' ),
		'date'      => $columns['date'],
	);
}
add_filter( 'manage_enquiry_posts_columns', 'estatein_enquiry_columns' );

/**
 * @param string $column  Column key.
 * @param int    $post_id Enquiry ID.
 */
function estatein_enquiry_column_content( $column, $post_id ) {
	if ( 'email' === $column ) {
		$email = (string) get_post_meta( $post_id, 'email', true );
		printf( '<a href="mailto:%1$s">%1$s</a>', esc_html( $email ) );
	} elseif ( 'form_type' === $column ) {
		$types = estatein_form_types();
		$type  = (string) get_post_meta( $post_id, '_form_type', true );
		echo esc_html( isset( $types[ $type ] ) ? $types[ $type ]['label'] : $type );
	}
}
add_action( 'manage_enquiry_posts_custom_column', 'estatein_enquiry_column_content', 10, 2 );

/**
 * Read-only view of a submission on the enquiry edit screen.
 */
function estatein_enquiry_meta_box() {
	add_meta_box(
		'estatein-enquiry',
		__( 'Submission', 'estatein' ),
		function ( $post ) {
			$types  = estatein_form_types();
			$type   = (string) get_post_meta( $post->ID, '_form_type', true );
			$fields = isset( $types[ $type ] ) ? $types[ $type ]['fields'] : array();
			echo '<table class="widefat striped"><tbody>';
			foreach ( $fields as $key => $label ) {
				$value = (string) get_post_meta( $post->ID, $key, true );
				if ( '' !== $value ) {
					printf( '<tr><th style="width:200px">%s</th><td>%s</td></tr>', esc_html( $label ), nl2br( esc_html( $value ) ) );
				}
			}
			$page = (string) get_post_meta( $post->ID, '_page_url', true );
			if ( $page ) {
				printf( '<tr><th>%s</th><td><a href="%2$s">%2$s</a></td></tr>', esc_html__( 'Sent from', 'estatein' ), esc_url( $page ) );
			}
			echo '</tbody></table>';
		},
		'enquiry',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_enquiry', 'estatein_enquiry_meta_box' );

/**
 * Suggest ACF for the content editors (the site works without it).
 */
function estatein_acf_notice() {
	if ( function_exists( 'acf_add_local_field_group' ) || ! current_user_can( 'install_plugins' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->base, array( 'dashboard', 'themes', 'edit', 'post' ), true ) ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p>%1$s <a href="%2$s">%3$s</a></p></div>',
		esc_html__( 'Estatein: install the free "Advanced Custom Fields" plugin to edit page sections, property details and pricing from the dashboard.', 'estatein' ),
		esc_url( admin_url( 'plugin-install.php?s=advanced-custom-fields&tab=search&type=term' ) ),
		esc_html__( 'Install ACF', 'estatein' )
	);
}
add_action( 'admin_notices', 'estatein_acf_notice' );
