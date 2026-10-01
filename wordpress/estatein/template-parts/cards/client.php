<?php
/**
 * Client card (About › Our Valued Clients).
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_id      = get_the_ID();
$estatein_website = estatein_field( 'website', $estatein_id );
?>
<article class="client-card">
	<div class="client-card__head">
		<div class="client-card__name">
			<p class="client-card__since"><?php echo esc_html( estatein_field( 'since', $estatein_id ) ); ?></p>
			<h3 class="client-card__title"><?php the_title(); ?></h3>
		</div>
		<?php if ( $estatein_website ) : ?>
			<a class="btn btn--dark client-card__btn" href="<?php echo esc_url( $estatein_website ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Visit Website', 'estatein' ); ?></a>
		<?php endif; ?>
	</div>
	<dl class="client-card__facts">
		<div class="client-card__fact">
			<dt><?php echo estatein_icon( 'domain' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Domain', 'estatein' ); ?></dt>
			<dd><?php echo esc_html( estatein_field( 'domain', $estatein_id ) ); ?></dd>
		</div>
		<div class="client-card__fact">
			<dt><?php echo estatein_icon( 'category' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Category', 'estatein' ); ?></dt>
			<dd><?php echo esc_html( estatein_field( 'category', $estatein_id ) ); ?></dd>
		</div>
	</dl>
	<blockquote class="client-card__quote">
		<p class="client-card__quote-label"><?php esc_html_e( 'What They Said 🤗', 'estatein' ); ?></p>
		<p><?php echo esc_html( estatein_field( 'quote', $estatein_id ) ); ?></p>
	</blockquote>
</article>
