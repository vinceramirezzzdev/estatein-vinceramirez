<?php
/**
 * Blog post card (index / archive / search), built from the property card styles.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'property-card post-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'estatein-card', array( 'class' => 'property-card__img', 'sizes' => '(min-width: 1440px) 432px, (min-width: 768px) 40vw, 90vw' ) ); ?>
		</a>
	<?php endif; ?>
	<div class="property-card__body">
		<div class="property-card__text">
			<p class="post-card__meta"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
			<h2 class="property-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<p class="property-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
		</div>
		<div class="property-card__footer">
			<a class="btn btn--primary property-card__btn" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', 'estatein' ); ?><span class="visually-hidden"> — <?php the_title(); ?></span></a>
		</div>
	</div>
</article>
