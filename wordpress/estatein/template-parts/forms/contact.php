<?php
/**
 * Contact page form ("Let's Connect").
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

?>
<form class="form-card form-card--wide form-card--contact" <?php echo estatein_form_attrs( 'contact' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php estatein_form_hidden_fields( 'contact' ); ?>
	<div class="form-grid form-grid--3">
		<?php
		estatein_person_fields( 'c' );
		estatein_select(
			'c-inquiry',
			'inquiry_type',
			__( 'Inquiry Type', 'estatein' ),
			__( 'Select Inquiry Type', 'estatein' ),
			apply_filters( 'estatein_inquiry_types', array( __( 'Buying a Property', 'estatein' ), __( 'Selling a Property', 'estatein' ), __( 'Property Management', 'estatein' ), __( 'Investment Advisory', 'estatein' ) ) )
		);
		estatein_select(
			'c-source',
			'source',
			__( 'How Did You Hear About Us?', 'estatein' ),
			__( 'Select', 'estatein' ),
			apply_filters( 'estatein_referral_sources', array( 'Instagram', 'LinkedIn', 'Facebook', __( 'Referral', 'estatein' ) ) )
		);
		?>
	</div>
	<?php estatein_message( 'c-message', true ); ?>
	<?php get_template_part( 'template-parts/forms/submit-row', null, array( 'type' => 'contact' ) ); ?>
</form>
