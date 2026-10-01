<?php
/**
 * Template Name: Contact
 *
 * Hero + contact tiles, the "Let's Connect" form, office locations (Office post
 * type, filtered by Office Type tabs) and the "Explore Estatein's World" gallery.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();
$estatein_page  = get_the_ID();
$estatein_email = estatein_option( 'contact_email' );
$estatein_phone = estatein_option( 'contact_phone' );

$estatein_tiles = array(
	array(
		'title' => $estatein_email,
		'url'   => 'mailto:' . $estatein_email,
		'icon'  => 'contact-mail',
	),
	array(
		'title' => $estatein_phone,
		'url'   => 'tel:' . preg_replace( '/[^0-9+]/', '', $estatein_phone ),
		'icon'  => 'contact-phone',
	),
	array(
		'title' => estatein_option( 'contact_hq_label' ),
		'url'   => '#offices',
		'icon'  => 'contact-location',
	),
	array(
		'icon'  => 'contact-social',
		'links' => array_filter(
			array(
				'Instagram' => estatein_option( 'social_instagram' ),
				'LinkedIn'  => estatein_option( 'social_linkedin' ),
				'Facebook'  => estatein_option( 'social_facebook' ),
			)
		),
	),
);

$estatein_gallery = array(
	1 => array( 'office-gallery-01', 'a', __( 'Estatein office workspace with desks and monitors', 'estatein' ), '(min-width: 768px) 37vw, 90vw', 709, 236, 760 ),
	2 => array( 'office-gallery-02', 'b', __( 'Team members collaborating around a table', 'estatein' ), '(min-width: 768px) 37vw, 90vw', 709, 236, 760 ),
	3 => array( 'office-gallery-03', 'c', __( 'Estatein team portrait', 'estatein' ), '(min-width: 768px) 37vw, 90vw', 709, 236, 760 ),
	4 => array( 'office-gallery-04', 'd', __( 'Estatein agents in business suits', 'estatein' ), '(min-width: 768px) 18vw, 44vw', 344, 236, 360 ),
	5 => array( 'office-gallery-05', 'e', __( 'Smiling Estatein team members', 'estatein' ), '(min-width: 768px) 18vw, 44vw', 344, 236, 360 ),
	6 => array( 'office-gallery-06', 'f', __( 'An agent shaking hands with a client', 'estatein' ), '(min-width: 768px) 37vw, 90vw', 709, 280, 760 ),
);

/**
 * One gallery image (media library image if set, otherwise the design image).
 *
 * @param int $n Image number 1–6.
 * @return string
 */
$estatein_gallery_image = function ( $n ) use ( $estatein_gallery, $estatein_page ) {
	list( $fallback, $area, $alt, $sizes, $width, $height, $src ) = $estatein_gallery[ $n ];
	return estatein_image(
		estatein_field( "contact_gallery_image{$n}", $estatein_page ),
		$fallback,
		array(
			'class'     => 'office-gallery__img office-gallery__img--' . $area,
			'alt'       => $alt,
			'sizes'     => $sizes,
			'width'     => $width,
			'height'    => $height,
			'src_width' => $src,
		)
	);
};

$estatein_office_types = get_terms( array( 'taxonomy' => 'office_type', 'hide_empty' => false, 'orderby' => 'term_id' ) );
$estatein_office_types = is_wp_error( $estatein_office_types ) ? array() : $estatein_office_types;
?>

<main id="main">
	<?php
	get_template_part(
		'template-parts/page-hero',
		null,
		array(
			'title'    => estatein_field( 'contact_hero_title', $estatein_page ),
			'copy'     => estatein_field( 'contact_hero_copy', $estatein_page ),
			'modifier' => 'strip',
			'after'    => function () use ( $estatein_tiles ) {
				get_template_part(
					'template-parts/feature-strip',
					null,
					array(
						'label' => __( 'Ways to reach us', 'estatein' ),
						'items' => $estatein_tiles,
					)
				);
			},
		)
	);
	?>

	<section class="section" id="contact-form" aria-labelledby="connect-title">
		<div class="container">
			<?php
			get_template_part(
				'template-parts/section-head',
				null,
				array(
					'id'       => 'connect-title',
					'title'    => estatein_field( 'contact_form_title', $estatein_page ),
					'copy'     => estatein_field( 'contact_form_copy', $estatein_page ),
					'modifier' => 'narrow',
				)
			);
			get_template_part( 'template-parts/forms/contact' );
			?>
		</div>
	</section>

	<section class="section" id="offices" aria-labelledby="offices-title">
		<div class="container">
			<?php
			get_template_part(
				'template-parts/section-head',
				null,
				array(
					'id'       => 'offices-title',
					'title'    => estatein_field( 'contact_offices_title', $estatein_page ),
					'copy'     => estatein_field( 'contact_offices_copy', $estatein_page ),
					'modifier' => 'narrow',
				)
			);
			?>
			<div class="offices" data-tabs>
				<div class="tabs" role="tablist" aria-label="<?php esc_attr_e( 'Filter offices', 'estatein' ); ?>">
					<button class="tabs__btn" type="button" role="tab" aria-selected="true" data-tab="all"><?php esc_html_e( 'All', 'estatein' ); ?></button>
					<?php foreach ( $estatein_office_types as $estatein_type ) : ?>
						<?php
						if ( 'headquarters' === $estatein_type->slug ) {
							continue; // Headquarters are always listed under "All".
						}
						?>
						<button class="tabs__btn" type="button" role="tab" aria-selected="false" data-tab="<?php echo esc_attr( $estatein_type->slug ); ?>"><?php echo esc_html( $estatein_type->name ); ?></button>
					<?php endforeach; ?>
				</div>
				<ul class="offices__grid" role="tabpanel" aria-label="<?php esc_attr_e( 'Offices', 'estatein' ); ?>">
					<?php
					foreach ( estatein_get_items( 'office' ) as $post ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						setup_postdata( $post );
						get_template_part( 'template-parts/cards/office' );
					endforeach;
					wp_reset_postdata();
					?>
				</ul>
				<p class="offices__empty" data-tab-empty hidden><?php esc_html_e( 'There are no offices to show in this category yet.', 'estatein' ); ?></p>
			</div>
		</div>
	</section>

	<section class="section" aria-labelledby="gallery-title">
		<div class="container">
			<div class="office-gallery">
				<div class="office-gallery__pattern" aria-hidden="true"></div>
				<div class="office-gallery__grid">
					<?php
					for ( $estatein_i = 1; $estatein_i <= 5; $estatein_i++ ) {
						echo $estatein_gallery_image( $estatein_i ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
				<div class="office-gallery__footer">
					<div class="office-gallery__text">
						<?php echo estatein_symbol( 'sparkles-surface', 'sparkles' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<h2 class="section-title" id="gallery-title"><?php estatein_the_text( 'contact_gallery_title', $estatein_page ); ?></h2>
						<p class="section-copy"><?php estatein_the_html( 'contact_gallery_copy', $estatein_page ); ?></p>
					</div>
					<?php echo $estatein_gallery_image( 6 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
