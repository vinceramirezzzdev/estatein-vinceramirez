<?php
/**
 * Team member card (About › Meet the Estatein Team).
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_id     = get_the_ID();
$estatein_social = estatein_field( 'social_url', $estatein_id );
?>
<li class="team-card">
	<div class="team-card__media">
		<?php
		echo get_the_post_thumbnail(
			$estatein_id,
			'estatein-card',
			array(
				'class'   => 'team-card__img',
				'sizes'   => '(min-width: 1440px) 317px, (min-width: 768px) 40vw, 90vw',
				'alt'     => get_the_title(),
				'loading' => 'lazy',
			)
		);
		?>
		<?php if ( $estatein_social ) : ?>
			<?php /* translators: %s: team member name. */ ?>
			<a class="team-card__social" href="<?php echo esc_url( $estatein_social ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( sprintf( __( '%s on X (Twitter)', 'estatein' ), get_the_title() ) ); ?>"><?php echo estatein_icon( 'team-twitter' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
		<?php endif; ?>
	</div>
	<div class="team-card__text">
		<h3 class="team-card__name"><?php the_title(); ?></h3>
		<p class="team-card__role"><?php echo esc_html( estatein_field( 'role', $estatein_id ) ); ?></p>
	</div>
	<a class="say-hello" href="<?php echo esc_url( estatein_page_url( 'contact', 'contact-form' ) ); ?>"><?php esc_html_e( 'Say Hello 👋', 'estatein' ); ?><span class="say-hello__btn"><?php echo estatein_icon( 'send-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></a>
</li>
