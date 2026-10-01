<?php
/**
 * Office card (Contact › Discover Our Office Locations).
 * data-tab-item lists the office types so js/tabs.js can filter All / Regional / International.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_id      = get_the_ID();
$estatein_types   = wp_get_post_terms( $estatein_id, 'office_type', array( 'fields' => 'slugs' ) );
$estatein_types   = is_wp_error( $estatein_types ) ? array() : $estatein_types;
$estatein_address = estatein_field( 'address', $estatein_id );
$estatein_map     = estatein_field( 'map_url', $estatein_id );
$estatein_map     = $estatein_map ? $estatein_map : 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $estatein_address );
$estatein_email   = estatein_field( 'email', $estatein_id );
$estatein_phone   = estatein_field( 'phone', $estatein_id );
$estatein_city    = estatein_field( 'city', $estatein_id );
?>
<li class="office-card" data-tab-item="<?php echo esc_attr( implode( ' ', array_merge( array( 'all' ), $estatein_types ) ) ); ?>">
	<div class="office-card__text">
		<p class="office-card__type"><?php echo esc_html( estatein_field( 'office_label', $estatein_id ) ); ?></p>
		<h3 class="office-card__address"><?php echo esc_html( $estatein_address ); ?></h3>
		<p class="office-card__copy"><?php echo esc_html( wp_strip_all_tags( get_the_content() ) ); ?></p>
	</div>
	<ul class="office-card__contacts">
		<?php if ( $estatein_email ) : ?>
			<li><a class="pill" href="<?php echo esc_url( 'mailto:' . $estatein_email ); ?>"><?php echo estatein_icon( 'mail-solid' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $estatein_email ); ?></a></li>
		<?php endif; ?>
		<?php if ( $estatein_phone ) : ?>
			<li><a class="pill" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $estatein_phone ) ); ?>"><?php echo estatein_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $estatein_phone ); ?></a></li>
		<?php endif; ?>
		<?php if ( $estatein_city ) : ?>
			<li><span class="pill"><?php echo estatein_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $estatein_city ); ?></span></li>
		<?php endif; ?>
	</ul>
	<a class="btn btn--primary btn--block btn--sm" href="<?php echo esc_url( $estatein_map ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Get Direction', 'estatein' ); ?></a>
</li>
