<?php
/**
 * Home page (Settings › Reading › "A static page" → Home).
 *
 * Sections: hero, feature strip, Featured Properties, Testimonials, FAQs.
 * Copy is edited under "Home page content" (ACF) on the Home page;
 * properties, testimonials and FAQs come from their post types.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

$estatein_page = get_the_ID();
$estatein_featured = estatein_get_items(
	'property',
	array(
		'meta_key'   => 'featured', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		'meta_value' => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
	)
);
if ( ! $estatein_featured ) {
	$estatein_featured = estatein_get_items( 'property', array( 'posts_per_page' => 6 ) );
}
?>

<main id="main">
	<section class="hero" id="hero" aria-labelledby="hero-title">
		<div class="hero__grid">
			<div class="hero__content">
				<div class="hero__text">
					<h1 class="hero__title" id="hero-title"><?php estatein_the_text( 'home_hero_title', $estatein_page ); ?></h1>
					<p class="hero__copy"><?php estatein_the_html( 'home_hero_copy', $estatein_page ); ?></p>
					<a class="hero-badge" href="<?php echo esc_url( estatein_page_url( 'properties' ) ); ?>" aria-label="<?php esc_attr_e( 'Discover your dream property', 'estatein' ); ?>">
						<svg class="hero-badge__ring" viewBox="0 0 144 144" aria-hidden="true">
							<defs><path id="hero-badge-path" d="M72,130 A58,58 0 1,1 72,14 A58,58 0 1,1 72,130"></path></defs>
							<text textLength="364" lengthAdjust="spacing"><textPath href="#hero-badge-path" textLength="364" lengthAdjust="spacing"><?php estatein_the_text( 'home_badge_text', $estatein_page ); ?></textPath></text>
						</svg>
						<span class="hero-badge__core"><?php echo estatein_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</a>
				</div>
				<div class="hero__actions">
					<a class="btn btn--outline" href="<?php echo esc_url( estatein_link( estatein_field( 'home_btn1_link', $estatein_page ) ) ); ?>"><?php estatein_the_text( 'home_btn1_label', $estatein_page ); ?></a>
					<a class="btn btn--primary" href="<?php echo esc_url( estatein_link( estatein_field( 'home_btn2_link', $estatein_page ) ) ); ?>"><?php estatein_the_text( 'home_btn2_label', $estatein_page ); ?></a>
				</div>
				<ul class="stats">
					<?php for ( $estatein_i = 1; $estatein_i <= 3; $estatein_i++ ) : ?>
						<li class="stat"><strong class="stat__value"><?php estatein_the_text( "home_stat{$estatein_i}_value", $estatein_page ); ?></strong><span class="stat__label"><?php estatein_the_text( "home_stat{$estatein_i}_label", $estatein_page ); ?></span></li>
					<?php endfor; ?>
				</ul>
			</div>
			<div class="hero__media">
				<div class="hero__pattern" aria-hidden="true"></div>
				<?php
				echo estatein_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					estatein_field( 'home_hero_image', $estatein_page ),
					'hero-buildings',
					array(
						'class'         => 'hero__img',
						'alt'           => __( 'Modern blue glass high-rise buildings', 'estatein' ),
						'sizes'         => '(min-width: 768px) 48vw, 100vw',
						'width'         => 920,
						'height'        => 814,
						'src_width'     => 1080,
						'loading'       => 'eager',
						'fetchpriority' => 'high',
					),
					'full'
				);
				?>
			</div>
		</div>

		<?php get_template_part( 'template-parts/feature-strip', null, array( 'id' => 'features' ) ); ?>
	</section>

	<section class="section" id="featured-properties" aria-labelledby="featured-title">
		<div class="container">
			<?php
			$estatein_all = array(
				'label' => estatein_field( 'home_featured_btn', $estatein_page ),
				'url'   => estatein_page_url( 'properties' ),
			);
			get_template_part(
				'template-parts/section-head',
				null,
				array(
					'id'     => 'featured-title',
					'title'  => estatein_field( 'home_featured_title', $estatein_page ),
					'copy'   => estatein_field( 'home_featured_copy', $estatein_page ),
					'button' => $estatein_all,
				)
			);
			get_template_part(
				'template-parts/carousel',
				null,
				array(
					'posts'  => $estatein_featured,
					'card'   => 'property',
					'total'  => 60,
					'button' => $estatein_all,
				)
			);
			?>
		</div>
	</section>

	<section class="section" id="testimonials" aria-labelledby="testimonials-title">
		<div class="container">
			<?php
			$estatein_all = array(
				'label' => estatein_field( 'home_testimonials_btn', $estatein_page ),
				'url'   => (string) get_post_type_archive_link( 'testimonial' ),
			);
			get_template_part(
				'template-parts/section-head',
				null,
				array(
					'id'     => 'testimonials-title',
					'title'  => estatein_field( 'home_testimonials_title', $estatein_page ),
					'copy'   => estatein_field( 'home_testimonials_copy', $estatein_page ),
					'button' => $estatein_all,
				)
			);
			get_template_part(
				'template-parts/carousel',
				null,
				array(
					'posts'  => estatein_get_items( 'testimonial' ),
					'card'   => 'testimonial',
					'total'  => 10,
					'button' => $estatein_all,
				)
			);
			?>
		</div>
	</section>

	<?php get_template_part( 'template-parts/faq-section' ); ?>
</main>

<?php
get_footer();
