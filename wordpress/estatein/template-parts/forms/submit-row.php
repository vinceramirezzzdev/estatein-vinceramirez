<?php
/**
 * Terms checkbox + submit button + status line shared by the large forms.
 *
 * @package Estatein
 *
 * @var array $args { @type string $type Form type (for the no-JS status message). }
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="form-card__footer">
	<label class="checkbox">
		<input class="checkbox__input" type="checkbox" name="agree" value="1" required>
		<span class="checkbox__box" aria-hidden="true"></span>
		<span class="checkbox__label">
			<?php
			printf(
				/* translators: 1: Terms of Use link, 2: Privacy Policy link. */
				esc_html__( 'I agree with %1$s and %2$s', 'estatein' ),
				'<a href="' . esc_url( estatein_terms_url() ) . '">' . esc_html__( 'Terms of Use', 'estatein' ) . '</a>',
				'<a href="' . esc_url( estatein_privacy_url() ) . '">' . esc_html__( 'Privacy Policy', 'estatein' ) . '</a>'
			);
			?>
		</span>
	</label>
	<button class="btn btn--primary btn--submit" type="submit"><?php esc_html_e( 'Send Your Message', 'estatein' ); ?></button>
</div>
<?php estatein_form_status( isset( $args['type'] ) ? $args['type'] : '' ); ?>
