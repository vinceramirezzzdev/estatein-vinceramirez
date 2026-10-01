<?php
/**
 * Inner-page hero (Properties, Services, Contact, archives and blog).
 * The optional "after" callback prints content inside the section (e.g. the feature strip).
 *
 * @package Estatein
 *
 * @var array $args {
 *     @type string   $title    Page title (h1).
 *     @type string   $copy     Intro copy.
 *     @type string   $modifier 'search' or 'strip' (spacing variants).
 *     @type callable $after    Optional callback rendered after the text block.
 * }
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args,
	array(
		'title'    => '',
		'copy'     => '',
		'modifier' => '',
		'after'    => null,
	)
);
?>
<section class="page-hero<?php echo $args['modifier'] ? ' page-hero--' . esc_attr( $args['modifier'] ) : ''; ?>" aria-labelledby="page-title">
	<div class="page-hero__inner">
		<div class="container page-hero__text">
			<h1 class="section-title" id="page-title"><?php echo esc_html( $args['title'] ); ?></h1>
			<?php if ( $args['copy'] ) : ?>
				<p class="section-copy"><?php echo estatein_kses( $args['copy'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
		</div>
	</div>
	<?php
	if ( is_callable( $args['after'] ) ) {
		call_user_func( $args['after'] );
	}
	?>
</section>
