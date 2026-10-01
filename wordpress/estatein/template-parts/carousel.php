<?php
/**
 * Carousel wrapper: track of slides + "01 of 10" footer with arrows.
 *
 * The design shows a fixed total ("01 of 10", "01 of 60") while drawing three
 * cards, so js/carousel.js repeats the slides up to data-carousel-total.
 *
 * @package Estatein
 *
 * @var array $args {
 *     @type WP_Post[] $posts     Posts to render.
 *     @type string    $card      Card template part (template-parts/cards/{card}.php).
 *     @type int       $total     Total shown in the counter (design value).
 *     @type string    $modifier  Extra carousel class (e.g. "carousel--two").
 *     @type array     $button    Optional mobile "View All" button: array( 'label', 'url' ).
 *     @type array     $attrs     Extra attributes for the carousel element.
 *     @type callable  $slide_attrs Callback returning extra <li> attributes for a post.
 *     @type string    $empty     Optional "no results" message (shown by js/search.js).
 * }
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args,
	array(
		'posts'       => array(),
		'card'        => '',
		'total'       => 10,
		'modifier'    => '',
		'button'      => null,
		'attrs'       => '',
		'slide_attrs' => null,
		'empty'       => '',
		'card_args'   => array(),
	)
);
$count = max( (int) $args['total'], count( $args['posts'] ) );
?>
<div class="carousel<?php echo $args['modifier'] ? ' ' . esc_attr( $args['modifier'] ) : ''; ?>" data-carousel data-carousel-total="<?php echo esc_attr( $count ); ?>"<?php echo $args['attrs']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attribute string from templates. ?>>
	<ul class="carousel__track" data-carousel-track>
		<?php foreach ( $args['posts'] as $post ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
			<?php setup_postdata( $GLOBALS['post'] = $post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
			<li class="carousel__slide"<?php echo is_callable( $args['slide_attrs'] ) ? call_user_func( $args['slide_attrs'], $post ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php get_template_part( 'template-parts/cards/' . $args['card'], null, $args['card_args'] ); ?>
			</li>
		<?php endforeach; ?>
		<?php wp_reset_postdata(); ?>
	</ul>
	<?php if ( $args['empty'] ) : ?>
		<p class="property-search__empty" data-property-empty<?php echo $args['posts'] ? ' hidden' : ''; ?>><?php echo esc_html( $args['empty'] ); ?></p>
	<?php endif; ?>
	<div class="carousel__footer<?php echo $args['button'] ? ' carousel__footer--with-btn' : ''; ?>"<?php echo $args['posts'] ? '' : ' hidden'; ?>>
		<?php if ( $args['button'] ) : ?>
			<a class="btn btn--dark carousel__footer-btn" href="<?php echo esc_url( $args['button']['url'] ); ?>"><?php echo esc_html( $args['button']['label'] ); ?></a>
		<?php endif; ?>
		<p class="carousel__count" aria-live="polite"><span class="carousel__current" data-carousel-current>01</span> <?php esc_html_e( 'of', 'estatein' ); ?> <span data-carousel-count><?php echo esc_html( estatein_pad( $count ) ); ?></span></p>
		<div class="carousel__controls">
			<button class="arrow-btn" type="button" data-carousel-prev aria-label="<?php esc_attr_e( 'Previous slide', 'estatein' ); ?>" disabled>
				<?php echo estatein_icon( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
			<button class="arrow-btn" type="button" data-carousel-next aria-label="<?php esc_attr_e( 'Next slide', 'estatein' ); ?>">
				<?php echo estatein_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
		</div>
	</div>
</div>
