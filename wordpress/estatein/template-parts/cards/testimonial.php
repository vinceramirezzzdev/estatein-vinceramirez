<?php
/**
 * Testimonial card: star rating, headline (post title), quote (content), client.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_id     = get_the_ID();
$estatein_rating = max( 1, min( 5, (int) estatein_field( 'rating', $estatein_id ) ) );
?>
<figure class="testimonial-card">
	<?php /* translators: %d: rating out of 5. */ ?>
	<ul class="stars" aria-label="<?php echo esc_attr( sprintf( __( 'Rated %d out of 5', 'estatein' ), $estatein_rating ) ); ?>">
		<?php for ( $estatein_i = 0; $estatein_i < $estatein_rating; $estatein_i++ ) : ?>
			<li class="stars__item"><?php echo estatein_icon( 'star' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></li>
		<?php endfor; ?>
	</ul>
	<blockquote class="testimonial-card__quote">
		<h3 class="testimonial-card__title"><?php the_title(); ?></h3>
		<p><?php echo esc_html( wp_strip_all_tags( get_the_content() ) ); ?></p>
	</blockquote>
	<figcaption class="person">
		<?php
		echo get_the_post_thumbnail(
			$estatein_id,
			'thumbnail',
			array(
				'class'   => 'person__avatar',
				'sizes'   => '60px',
				'alt'     => '',
				'loading' => 'lazy',
			)
		);
		?>
		<span class="person__text"><span class="person__name"><?php echo esc_html( estatein_field( 'client_name', $estatein_id ) ); ?></span><span class="person__meta"><?php echo esc_html( estatein_field( 'client_location', $estatein_id ) ); ?></span></span>
	</figcaption>
</figure>
