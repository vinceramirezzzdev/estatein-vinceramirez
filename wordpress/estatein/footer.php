<?php
/**
 * Call-to-action band + site footer.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_template_part( 'template-parts/cta' );

$estatein_socials = array(
	'facebook' => __( 'Facebook', 'estatein' ),
	'linkedin' => __( 'LinkedIn', 'estatein' ),
	'twitter'  => __( 'X (Twitter)', 'estatein' ),
	'youtube'  => __( 'YouTube', 'estatein' ),
);
?>
<footer class="site-footer">
	<div class="container site-footer__main">
		<div class="site-footer__brand">
			<a class="site-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' — ' . __( 'home', 'estatein' ) ); ?>">
				<?php
				if ( has_custom_logo() ) {
					echo wp_get_attachment_image( (int) get_theme_mod( 'custom_logo' ), 'full', false, array( 'class' => 'logo logo--footer', 'alt' => '' ) );
				} else {
					echo estatein_symbol( 'logo', 'logo logo--footer' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</a>
			<form class="newsletter" <?php echo estatein_form_attrs( 'newsletter' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php estatein_form_hidden_fields( 'newsletter' ); ?>
				<label class="visually-hidden" for="newsletter-email"><?php esc_html_e( 'Email address', 'estatein' ); ?></label>
				<?php echo estatein_icon( 'mail', 'newsletter__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<input class="newsletter__input" id="newsletter-email" type="email" name="email" placeholder="<?php esc_attr_e( 'Enter Your Email', 'estatein' ); ?>" autocomplete="email" required>
				<button class="newsletter__submit" type="submit" aria-label="<?php esc_attr_e( 'Subscribe', 'estatein' ); ?>">
					<?php echo estatein_icon( 'send' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
				<?php estatein_form_status( 'newsletter' ); ?>
			</form>
		</div>

		<nav class="site-footer__links" aria-label="<?php esc_attr_e( 'Footer', 'estatein' ); ?>">
			<?php foreach ( estatein_footer_columns() as $estatein_title => $estatein_links ) : ?>
				<div class="footer-col">
					<h2 class="footer-col__title"><?php echo esc_html( $estatein_title ); ?></h2>
					<ul class="footer-col__list">
						<?php foreach ( $estatein_links as $estatein_label => $estatein_url ) : ?>
							<li><a href="<?php echo esc_url( $estatein_url ); ?>"><?php echo esc_html( $estatein_label ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		</nav>
	</div>

	<div class="site-footer__bar">
		<div class="container site-footer__bar-inner">
			<div class="site-footer__legal">
				<p><?php echo esc_html( estatein_option( 'footer_copyright' ) ); ?></p>
				<a href="<?php echo esc_url( estatein_terms_url() ); ?>"><?php echo esc_html( estatein_option( 'footer_terms_label' ) ); ?></a>
			</div>
			<ul class="social" aria-label="<?php esc_attr_e( 'Estatein on social media', 'estatein' ); ?>">
				<?php foreach ( $estatein_socials as $estatein_key => $estatein_label ) : ?>
					<?php
					$estatein_url = estatein_option( 'social_' . $estatein_key );
					if ( ! $estatein_url ) {
						continue;
					}
					?>
					<li><a class="social__link" href="<?php echo esc_url( $estatein_url ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $estatein_label ); ?>"><?php echo estatein_icon( $estatein_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
