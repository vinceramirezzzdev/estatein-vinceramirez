<?php
/**
 * Section heading: sparkles, title, intro and an optional "View All" button.
 *
 * @package Estatein
 *
 * @var array $args {
 *     @type string $id       Heading id (for aria-labelledby).
 *     @type string $title    Title.
 *     @type string $copy     Intro (may contain the breakpoint <span> markup).
 *     @type string $modifier '', 'narrow', 'wide' or 'pricing' (right padding variants).
 *     @type string $tag      Heading tag, default h2.
 *     @type array  $button   Optional array( 'label' => …, 'url' => … ).
 * }
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args,
	array(
		'id'       => '',
		'title'    => '',
		'copy'     => '',
		'modifier' => '',
		'tag'      => 'h2',
		'button'   => null,
	)
);
$tag  = in_array( $args['tag'], array( 'h1', 'h2' ), true ) ? $args['tag'] : 'h2';
?>
<div class="section-head<?php echo $args['modifier'] ? ' section-head--' . esc_attr( $args['modifier'] ) : ''; ?>">
	<div class="section-head__text">
		<?php echo estatein_symbol( 'sparkles', 'sparkles' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<<?php echo esc_html( $tag ); ?> class="section-title"<?php echo $args['id'] ? ' id="' . esc_attr( $args['id'] ) . '"' : ''; ?>><?php echo esc_html( $args['title'] ); ?></<?php echo esc_html( $tag ); ?>>
		<?php if ( $args['copy'] ) : ?>
			<p class="section-copy"><?php echo estatein_kses( $args['copy'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		<?php endif; ?>
	</div>
	<?php if ( ! empty( $args['button']['label'] ) ) : ?>
		<a class="btn btn--dark section-head__btn" href="<?php echo esc_url( $args['button']['url'] ); ?>"><?php echo esc_html( $args['button']['label'] ); ?></a>
	<?php endif; ?>
</div>
