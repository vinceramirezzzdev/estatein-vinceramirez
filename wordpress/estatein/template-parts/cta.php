<?php
/**
 * "Start Your Real Estate Journey Today" band shown above the footer.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="cta" aria-labelledby="cta-title">
	<div class="cta__pattern cta__pattern--left" aria-hidden="true"></div>
	<div class="cta__pattern cta__pattern--right" aria-hidden="true"></div>
	<div class="container cta__inner">
		<div class="cta__text">
			<h2 class="cta__title" id="cta-title"><?php echo esc_html( estatein_option( 'cta_title' ) ); ?></h2>
			<p class="cta__copy"><?php echo estatein_kses( estatein_option( 'cta_copy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		</div>
		<a class="btn btn--primary cta__btn" href="<?php echo esc_url( estatein_link( estatein_option( 'cta_btn_link' ) ) ); ?>"><?php echo esc_html( estatein_option( 'cta_btn_label' ) ); ?></a>
	</div>
</section>
