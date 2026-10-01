<?php
/**
 * Template Name: Services
 *
 * Hero + feature strip, then three groups of Service posts (Service Group
 * taxonomy: unlock-property-value, property-management, smart-investments),
 * each closed by a promo card edited on this page.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();
$estatein_page = get_the_ID();

/**
 * Services in a group, in drag order.
 *
 * @param string $group Service Group slug.
 * @return WP_Post[]
 */
$estatein_services = function ( $group ) {
	return estatein_get_items(
		'service',
		array(
			'tax_query' => array( array( 'taxonomy' => 'service_group', 'field' => 'slug', 'terms' => $group ) ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		)
	);
};

/**
 * Print service cards.
 *
 * @param WP_Post[] $posts  Services.
 * @param bool      $filled Filled card style.
 */
$estatein_cards = function ( $posts, $filled = false ) {
	foreach ( $posts as $post ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		setup_postdata( $GLOBALS['post'] = $post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		get_template_part( 'template-parts/cards/service', null, array( 'filled' => $filled ) );
	}
	wp_reset_postdata();
};

/**
 * Promo card closing a group.
 *
 * @param int    $n       Promo number (1–3).
 * @param string $variant 'wide' or 'stacked'.
 */
$estatein_promo = function ( $n, $variant ) use ( $estatein_page ) {
	$tag    = 'wide' === $variant ? 'li' : 'div';
	$button = sprintf(
		'<a class="btn btn--darker%1$s" href="%2$s">%3$s</a>',
		'stacked' === $variant ? ' btn--block' : '',
		esc_url( estatein_link( estatein_field( "services_promo{$n}_link", $estatein_page ) ) ),
		esc_html( estatein_field( "services_promo{$n}_btn", $estatein_page ) )
	);
	?>
	<<?php echo esc_html( $tag ); ?> class="promo-card promo-card--<?php echo esc_attr( $variant ); ?>">
		<div class="promo-card__pattern" aria-hidden="true"></div>
		<?php if ( 'wide' === $variant ) : ?>
			<div class="promo-card__head">
				<h3 class="promo-card__title"><?php estatein_the_text( "services_promo{$n}_title", $estatein_page ); ?></h3>
				<?php echo $button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<p class="promo-card__copy"><?php estatein_the_html( "services_promo{$n}_copy", $estatein_page ); ?></p>
		<?php else : ?>
			<h3 class="promo-card__title promo-card__title--sm"><?php estatein_the_text( "services_promo{$n}_title", $estatein_page ); ?></h3>
			<p class="promo-card__copy promo-card__copy--light"><?php estatein_the_html( "services_promo{$n}_copy", $estatein_page ); ?></p>
			<?php echo $button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php endif; ?>
	</<?php echo esc_html( $tag ); ?>>
	<?php
};
?>

<main id="main">
	<?php
	get_template_part(
		'template-parts/page-hero',
		null,
		array(
			'title'    => estatein_field( 'services_hero_title', $estatein_page ),
			'copy'     => estatein_field( 'services_hero_copy', $estatein_page ),
			'modifier' => 'strip',
			'after'    => function () {
				get_template_part( 'template-parts/feature-strip' );
			},
		)
	);

	$estatein_groups = array(
		'unlock-property-value' => array( 'selling', 1 ),
		'property-management'   => array( 'management', 2 ),
	);
	foreach ( $estatein_groups as $estatein_slug => $estatein_group ) :
		list( $estatein_key, $estatein_n ) = $estatein_group;
		?>
		<section class="section" id="<?php echo esc_attr( $estatein_slug ); ?>" aria-labelledby="<?php echo esc_attr( $estatein_key ); ?>-title">
			<div class="container">
				<?php
				get_template_part(
					'template-parts/section-head',
					null,
					array(
						'id'       => $estatein_key . '-title',
						'title'    => estatein_field( "services_{$estatein_key}_title", $estatein_page ),
						'copy'     => estatein_field( "services_{$estatein_key}_copy", $estatein_page ),
						'modifier' => 'narrow',
					)
				);
				?>
				<ul class="service-grid">
					<?php
					$estatein_cards( $estatein_services( $estatein_slug ) );
					$estatein_promo( $estatein_n, 'wide' );
					?>
				</ul>
			</div>
		</section>
	<?php endforeach; ?>

	<section class="section" id="smart-investments" aria-labelledby="investment-title">
		<div class="container investments">
			<div class="investments__intro">
				<div class="investments__text">
					<?php echo estatein_symbol( 'sparkles', 'sparkles' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<h2 class="section-title" id="investment-title"><?php estatein_the_text( 'services_investment_title', $estatein_page ); ?></h2>
					<p class="section-copy"><?php estatein_the_html( 'services_investment_copy', $estatein_page ); ?></p>
				</div>
				<?php $estatein_promo( 3, 'stacked' ); ?>
			</div>
			<ul class="investments__grid">
				<?php $estatein_cards( $estatein_services( 'smart-investments' ), true ); ?>
			</ul>
		</div>
	</section>
</main>

<?php
get_footer();
