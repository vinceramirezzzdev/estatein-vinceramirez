<?php
/**
 * Properties page form ("Let's Make it Happen").
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_rooms = array( '1', '2', '3', '4+' );
?>
<form class="form-card form-card--wide" <?php echo estatein_form_attrs( 'property_inquiry' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php estatein_form_hidden_fields( 'property_inquiry' ); ?>
	<div class="form-grid form-grid--4">
		<?php
		estatein_person_fields( 'pi' );
		estatein_select( 'pi-location', 'location', __( 'Preferred Location', 'estatein' ), __( 'Select Location', 'estatein' ), estatein_term_names( 'property_location' ) );
		estatein_select( 'pi-type', 'property_type', __( 'Property Type', 'estatein' ), __( 'Select Property Type', 'estatein' ), estatein_term_names( 'property_category' ) );
		estatein_select( 'pi-bathrooms', 'bathrooms', __( 'No. of Bathrooms', 'estatein' ), __( 'Select no. of Bathrooms', 'estatein' ), $estatein_rooms );
		estatein_select( 'pi-bedrooms', 'bedrooms', __( 'No. of Bedrooms', 'estatein' ), __( 'Select no. of Bedrooms', 'estatein' ), $estatein_rooms );
		?>
	</div>

	<div class="form-grid form-grid--2">
		<?php estatein_select( 'pi-budget', 'budget', __( 'Budget', 'estatein' ), __( 'Select Budget', 'estatein' ), array_values( estatein_price_ranges() ) ); ?>
		<fieldset class="field">
			<legend class="field__label"><?php esc_html_e( 'Preferred Contact Method', 'estatein' ); ?></legend>
			<div class="contact-methods">
				<label class="contact-method">
					<?php echo estatein_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<input class="contact-method__input" type="tel" name="contact_phone" placeholder="<?php esc_attr_e( 'Enter Your Number', 'estatein' ); ?>" aria-label="<?php esc_attr_e( 'Your phone number', 'estatein' ); ?>" autocomplete="tel">
					<input class="contact-method__radio" type="radio" name="contact_method" value="phone" checked aria-label="<?php esc_attr_e( 'Contact me by phone', 'estatein' ); ?>">
				</label>
				<label class="contact-method">
					<?php echo estatein_icon( 'mail-solid' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<input class="contact-method__input" type="email" name="contact_email" placeholder="<?php esc_attr_e( 'Enter Your Email', 'estatein' ); ?>" aria-label="<?php esc_attr_e( 'Your email address', 'estatein' ); ?>" autocomplete="email">
					<input class="contact-method__radio" type="radio" name="contact_method" value="email" aria-label="<?php esc_attr_e( 'Contact me by email', 'estatein' ); ?>">
				</label>
			</div>
		</fieldset>
	</div>

	<?php estatein_message( 'pi-message' ); ?>
	<?php get_template_part( 'template-parts/forms/submit-row', null, array( 'type' => 'property_inquiry' ) ); ?>
</form>
