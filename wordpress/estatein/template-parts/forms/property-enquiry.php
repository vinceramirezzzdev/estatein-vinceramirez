<?php
/**
 * Property details form ("Inquire About …"), with the property pre-selected.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_locations = get_the_terms( get_the_ID(), 'property_location' );
$estatein_selected  = get_the_title() . ( $estatein_locations && ! is_wp_error( $estatein_locations ) ? ', ' . $estatein_locations[0]->name : '' );
?>
<form class="form-card" <?php echo estatein_form_attrs( 'property_enquiry' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php estatein_form_hidden_fields( 'property_enquiry' ); ?>
	<div class="form-grid form-grid--2">
		<?php estatein_person_fields( 'pe' ); ?>
	</div>

	<div class="field">
		<label class="field__label" for="pe-property"><?php esc_html_e( 'Selected Property', 'estatein' ); ?></label>
		<div class="field__selected">
			<input class="field__control field__control--selected" id="pe-property" name="property" type="text" value="<?php echo esc_attr( $estatein_selected ); ?>" readonly>
			<?php echo estatein_icon( 'pin-soft' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>

	<?php estatein_message( 'pe-message' ); ?>
	<?php get_template_part( 'template-parts/forms/submit-row', null, array( 'type' => 'property_enquiry' ) ); ?>
</form>
