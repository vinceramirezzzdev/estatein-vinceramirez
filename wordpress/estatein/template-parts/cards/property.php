<?php
/**
 * Property card — Home "Featured Properties" variant (bed / bath / type tags).
 * Expects the global $post to be a property (set by template-parts/carousel.php).
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_id   = get_the_ID();
$estatein_url  = get_permalink();
$estatein_text = estatein_field( 'card_text', $estatein_id );
$estatein_text = $estatein_text ? $estatein_text : get_the_excerpt();
$estatein_tags = array_filter(
	array(
		'bed'   => estatein_field( 'bedrooms', $estatein_id ) ? sprintf( /* translators: %d: number of bedrooms. */ __( '%d-Bedroom', 'estatein' ), (int) estatein_field( 'bedrooms', $estatein_id ) ) : '',
		'bath'  => estatein_field( 'bathrooms', $estatein_id ) ? sprintf( /* translators: %d: number of bathrooms. */ __( '%d-Bathroom', 'estatein' ), (int) estatein_field( 'bathrooms', $estatein_id ) ) : '',
		'villa' => estatein_field( 'property_type', $estatein_id ),
	)
);
?>
<article class="property-card">
	<?php
	echo get_the_post_thumbnail(
		$estatein_id,
		'estatein-card',
		array(
			'class'   => 'property-card__img',
			'sizes'   => '(min-width: 1440px) 432px, (min-width: 768px) 40vw, 90vw',
			'loading' => 'lazy',
		)
	);
	?>
	<div class="property-card__body">
		<div class="property-card__text">
			<h3 class="property-card__title"><?php the_title(); ?></h3>
			<p class="property-card__excerpt"><?php echo esc_html( $estatein_text ); ?> <a href="<?php echo esc_url( $estatein_url ); ?>"><?php esc_html_e( 'Read More', 'estatein' ); ?></a></p>
		</div>
		<?php if ( $estatein_tags ) : ?>
			<ul class="tags" aria-label="<?php esc_attr_e( 'Property features', 'estatein' ); ?>">
				<?php foreach ( $estatein_tags as $estatein_icon => $estatein_label ) : ?>
					<li class="tag"><?php echo estatein_icon( $estatein_icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $estatein_label ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<div class="property-card__footer">
			<p class="price"><span class="price__label"><?php esc_html_e( 'Price', 'estatein' ); ?></span> <span class="price__value"><?php echo esc_html( estatein_price( estatein_field( 'price', $estatein_id ) ) ); ?></span></p>
			<a class="btn btn--primary property-card__btn" href="<?php echo esc_url( $estatein_url ); ?>"><?php esc_html_e( 'View Property Details', 'estatein' ); ?></a>
		</div>
	</div>
</article>
