<?php
/**
 * Property Details page.
 *
 * Gallery (Property gallery meta box), description (post content), specs,
 * key features, enquiry form, pricing breakdown (ACF "Pricing details" tab)
 * and the FAQ section.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$estatein_id       = get_the_ID();
	$estatein_name     = get_the_title();
	$estatein_location = get_the_terms( $estatein_id, 'property_location' );
	$estatein_location = $estatein_location && ! is_wp_error( $estatein_location ) ? $estatein_location[0]->name : '';
	$estatein_price    = estatein_field( 'price', $estatein_id );

	$estatein_images = estatein_gallery_ids( $estatein_id );
	if ( ! $estatein_images && has_post_thumbnail() ) {
		$estatein_images = array( get_post_thumbnail_id() );
	}

	$estatein_features = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) estatein_field( 'features', $estatein_id ) ) ) );
	?>

<main id="main">
	<section class="section section--first property-overview" aria-labelledby="property-title">
		<div class="container">
			<div class="property-head">
				<div class="property-head__title">
					<h1 class="property-head__name" id="property-title"><?php the_title(); ?></h1>
					<?php if ( $estatein_location ) : ?>
						<p class="property-head__location"><?php echo estatein_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $estatein_location ); ?></p>
					<?php endif; ?>
				</div>
				<p class="price property-head__price"><span class="price__label"><?php esc_html_e( 'Price', 'estatein' ); ?></span> <span class="price__value"><?php echo esc_html( estatein_price( $estatein_price ) ); ?></span></p>
			</div>

			<?php if ( $estatein_images ) : ?>
				<div class="gallery" data-gallery>
					<ul class="gallery__thumbs" aria-label="<?php esc_attr_e( 'Choose a photo', 'estatein' ); ?>">
						<?php foreach ( $estatein_images as $estatein_i => $estatein_image ) : ?>
							<?php /* translators: %d: photo number. */ ?>
							<li><button class="gallery__thumb" type="button" aria-label="<?php echo esc_attr( sprintf( __( 'Show photo %d', 'estatein' ), $estatein_i + 1 ) ); ?>"<?php echo 0 === $estatein_i ? ' aria-current="true"' : ''; ?> data-gallery-thumb="<?php echo esc_attr( $estatein_i ); ?>"><?php echo wp_get_attachment_image( $estatein_image, 'estatein-thumb', false, array( 'alt' => '', 'loading' => 'lazy' ) ); ?></button></li>
						<?php endforeach; ?>
					</ul>

					<div class="gallery__viewport" data-gallery-track tabindex="0" aria-label="<?php esc_attr_e( 'Property photos', 'estatein' ); ?>" role="region">
						<?php
						foreach ( $estatein_images as $estatein_i => $estatein_image ) {
							echo wp_get_attachment_image(
								$estatein_image,
								'estatein-wide',
								false,
								array(
									'class'         => 'gallery__image',
									'sizes'         => '(min-width: 768px) 40vw, 90vw',
									'loading'       => $estatein_i < 2 ? 'eager' : 'lazy',
									'fetchpriority' => $estatein_i < 2 ? 'high' : 'auto',
								)
							);
						}
						?>
					</div>

					<div class="gallery__controls">
						<button class="arrow-btn" type="button" data-gallery-prev aria-label="<?php esc_attr_e( 'Previous photo', 'estatein' ); ?>" disabled><?php echo estatein_icon( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
						<div class="gallery__dots" data-gallery-dots aria-hidden="true"></div>
						<button class="arrow-btn" type="button" data-gallery-next aria-label="<?php esc_attr_e( 'Next photo', 'estatein' ); ?>"><?php echo estatein_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
					</div>
				</div>
			<?php endif; ?>

			<div class="property-info">
				<div class="panel property-info__description">
					<div class="property-info__text">
						<h2 class="panel__title"><?php esc_html_e( 'Description', 'estatein' ); ?></h2>
						<p class="section-copy"><?php echo esc_html( wp_strip_all_tags( get_the_content() ) ); ?></p>
					</div>
					<dl class="specs">
						<div class="specs__item">
							<dt><?php echo estatein_icon( 'spec-bed' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Bedrooms', 'estatein' ); ?></dt>
							<dd><?php echo esc_html( estatein_pad( estatein_field( 'bedrooms', $estatein_id ) ) ); ?></dd>
						</div>
						<div class="specs__item">
							<dt><?php echo estatein_icon( 'spec-bath' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Bathrooms', 'estatein' ); ?></dt>
							<dd><?php echo esc_html( estatein_pad( estatein_field( 'bathrooms', $estatein_id ) ) ); ?></dd>
						</div>
						<div class="specs__item">
							<dt><?php echo estatein_icon( 'area' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Area', 'estatein' ); ?></dt>
							<dd><?php echo esc_html( estatein_field( 'area', $estatein_id ) ); ?></dd>
						</div>
					</dl>
				</div>

				<?php if ( $estatein_features ) : ?>
					<div class="panel property-info__features">
						<h2 class="panel__title"><?php esc_html_e( 'Key Features and Amenities', 'estatein' ); ?></h2>
						<ul class="amenities">
							<?php foreach ( $estatein_features as $estatein_feature ) : ?>
								<li class="amenity"><?php echo estatein_icon( 'bolt' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $estatein_feature ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="section" id="inquire" aria-labelledby="inquire-title">
		<div class="container inquire">
			<div class="inquire__text">
				<?php echo estatein_symbol( 'sparkles', 'sparkles' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<h2 class="section-title" id="inquire-title"><?php echo esc_html( str_replace( '{property}', $estatein_name, estatein_option( 'details_inquire_title' ) ) ); ?></h2>
				<p class="section-copy"><?php echo estatein_kses( estatein_option( 'details_inquire_copy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			</div>
			<?php get_template_part( 'template-parts/forms/property-enquiry' ); ?>
		</div>
	</section>

	<section class="section" id="pricing" aria-labelledby="pricing-title">
		<div class="container">
			<?php
			get_template_part(
				'template-parts/section-head',
				null,
				array(
					'id'       => 'pricing-title',
					'title'    => estatein_option( 'details_pricing_title' ),
					'copy'     => str_replace( '{property}', esc_html( $estatein_name ), estatein_option( 'details_pricing_copy' ) ),
					'modifier' => 'pricing',
				)
			);
			?>
			<div class="pricing">
				<p class="pricing__note"><strong class="pricing__note-title"><?php esc_html_e( 'Note', 'estatein' ); ?></strong><span class="pricing__note-text"><?php echo esc_html( estatein_option( 'details_note' ) ); ?></span></p>

				<div class="pricing__body">
					<p class="pricing__listing"><span class="pricing__listing-label"><?php esc_html_e( 'Listing Price', 'estatein' ); ?></span><span class="pricing__listing-value"><?php echo esc_html( estatein_price( $estatein_price ) ); ?></span></p>

					<div class="pricing__cards">
						<?php foreach ( estatein_pricing_sections() as $estatein_section ) : ?>
							<?php
							$estatein_rows = array();
							foreach ( $estatein_section['rows'] as $estatein_row ) {
								$estatein_cells = array();
								foreach ( $estatein_row as $estatein_key => $estatein_label ) {
									$estatein_value = estatein_field( "cost_{$estatein_key}_value", $estatein_id );
									if ( 'initial_listing' === $estatein_key && '' === (string) $estatein_value ) {
										$estatein_value = estatein_price( $estatein_price );
									}
									if ( '' !== (string) $estatein_value ) {
										$estatein_cells[] = array( $estatein_label, $estatein_value, estatein_field( "cost_{$estatein_key}_note", $estatein_id ) );
									}
								}
								if ( $estatein_cells ) {
									$estatein_rows[] = $estatein_cells;
								}
							}
							if ( ! $estatein_rows ) {
								continue;
							}
							?>
							<div class="cost-card">
								<div class="cost-card__head">
									<h3 class="cost-card__title"><?php echo esc_html( $estatein_section['title'] ); ?></h3>
									<a class="btn btn--dark" href="#inquire"><?php esc_html_e( 'Learn More', 'estatein' ); ?></a>
								</div>
								<dl class="cost-card__rows">
									<?php foreach ( $estatein_rows as $estatein_cells ) : ?>
										<div class="cost-card__row">
											<?php foreach ( $estatein_cells as $estatein_cell ) : ?>
												<?php list( $estatein_label, $estatein_value, $estatein_note ) = $estatein_cell; ?>
												<div class="cost">
													<dt><?php echo esc_html( $estatein_label ); ?></dt>
													<dd>
														<span class="cost__value<?php echo mb_strlen( (string) $estatein_value ) > 12 ? ' cost__value--text' : ''; ?>"><?php echo esc_html( $estatein_value ); ?></span>
														<?php if ( $estatein_note ) : ?>
															<span class="cost__note"><?php echo esc_html( $estatein_note ); ?></span>
														<?php endif; ?>
													</dd>
												</div>
											<?php endforeach; ?>
										</div>
									<?php endforeach; ?>
								</dl>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<?php get_template_part( 'template-parts/faq-section' ); ?>
</main>

	<?php
endwhile;

get_footer();
