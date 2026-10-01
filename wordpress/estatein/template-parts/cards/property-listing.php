<?php
/**
 * Property card — Properties page variant (category pill, no feature tags).
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_id   = get_the_ID();
$estatein_url  = get_permalink();
$estatein_text = estatein_field( 'listing_text', $estatein_id );
$estatein_text = $estatein_text ? $estatein_text : get_the_excerpt();
$estatein_cats = get_the_terms( $estatein_id, 'property_category' );
$estatein_cat  = $estatein_cats && ! is_wp_error( $estatein_cats ) ? $estatein_cats[0] : null;
?>
<article class="property-card property-card--listing">
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
		<div class="property-card__intro">
			<?php if ( $estatein_cat ) : ?>
				<p class="tag tag--category"><?php echo esc_html( $estatein_cat->name . ( $estatein_cat->description ? ' - ' . $estatein_cat->description : '' ) ); ?></p>
			<?php endif; ?>
			<div class="property-card__text">
				<h3 class="property-card__title"><?php the_title(); ?></h3>
				<p class="property-card__excerpt"><?php echo esc_html( $estatein_text ); ?> <a href="<?php echo esc_url( $estatein_url ); ?>"><?php esc_html_e( 'Read More', 'estatein' ); ?></a></p>
			</div>
		</div>
		<div class="property-card__footer">
			<p class="price"><span class="price__label"><?php esc_html_e( 'Price', 'estatein' ); ?></span> <span class="price__value"><?php echo esc_html( estatein_price( estatein_field( 'price', $estatein_id ) ) ); ?></span></p>
			<a class="btn btn--primary property-card__btn" href="<?php echo esc_url( $estatein_url ); ?>"><?php esc_html_e( 'View Property Details', 'estatein' ); ?></a>
		</div>
	</div>
</article>
