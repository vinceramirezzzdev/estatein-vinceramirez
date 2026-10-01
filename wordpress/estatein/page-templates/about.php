<?php
/**
 * Template Name: About Us
 *
 * Sections: Our Journey, Our Values, Our Achievements, How It Works (steps),
 * Meet the Estatein Team (Team post type), Our Valued Clients (Client post type).
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();
$estatein_page = get_the_ID();
?>

<main id="main">
	<section class="section section--first" id="our-story" aria-labelledby="journey-title">
		<div class="container journey">
			<div class="journey__content">
				<div class="journey__text">
					<?php echo estatein_symbol( 'sparkles', 'sparkles' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<h1 class="section-title" id="journey-title"><?php estatein_the_text( 'about_journey_title', $estatein_page ); ?></h1>
					<p class="section-copy"><?php estatein_the_html( 'about_journey_copy', $estatein_page ); ?></p>
				</div>
				<ul class="stats stats--roomy">
					<?php for ( $estatein_i = 1; $estatein_i <= 3; $estatein_i++ ) : ?>
						<li class="stat"><strong class="stat__value"><?php estatein_the_text( "about_stat{$estatein_i}_value", $estatein_page ); ?></strong><span class="stat__label"><?php estatein_the_text( "about_stat{$estatein_i}_label", $estatein_page ); ?></span></li>
					<?php endfor; ?>
				</ul>
			</div>
			<div class="journey__media">
				<div class="journey__pattern" aria-hidden="true"></div>
				<?php
				echo estatein_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					estatein_field( 'about_journey_image', $estatein_page ),
					'about-journey',
					array(
						'class'         => 'journey__img',
						'alt'           => __( 'A hand presenting a miniature model of a modern house', 'estatein' ),
						'sizes'         => '(min-width: 1440px) 755px, (min-width: 768px) 48vw, 100vw',
						'width'         => 755,
						'height'        => 546,
						'src_width'     => 760,
						'loading'       => 'eager',
						'fetchpriority' => 'high',
					),
					'large'
				);
				?>
			</div>
		</div>
	</section>

	<section class="section" id="our-values" aria-labelledby="values-title">
		<div class="container values">
			<div class="values__text">
				<?php echo estatein_symbol( 'sparkles', 'sparkles' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<h2 class="section-title" id="values-title"><?php estatein_the_text( 'about_values_title', $estatein_page ); ?></h2>
				<p class="section-copy"><?php estatein_the_html( 'about_values_copy', $estatein_page ); ?></p>
			</div>
			<ul class="values__grid">
				<?php foreach ( array( 1 => 'value-trust', 2 => 'value-excellence', 3 => 'value-client', 4 => 'value-trust' ) as $estatein_i => $estatein_icon ) : ?>
					<li class="value">
						<div class="value__head">
							<span class="value__icon"><?php echo estatein_icon( $estatein_icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<h3 class="value__title"><?php estatein_the_text( "about_value{$estatein_i}_title", $estatein_page ); ?></h3>
						</div>
						<p class="value__copy"><?php estatein_the_html( "about_value{$estatein_i}_copy", $estatein_page ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<section class="section" id="our-works" aria-labelledby="achievements-title">
		<div class="container">
			<?php
			get_template_part(
				'template-parts/section-head',
				null,
				array(
					'id'       => 'achievements-title',
					'title'    => estatein_field( 'about_achievements_title', $estatein_page ),
					'copy'     => estatein_field( 'about_achievements_copy', $estatein_page ),
					'modifier' => 'narrow',
				)
			);
			?>
			<ul class="achievements">
				<?php for ( $estatein_i = 1; $estatein_i <= 3; $estatein_i++ ) : ?>
					<li class="achievement">
						<h3 class="achievement__title"><?php estatein_the_text( "about_achievement{$estatein_i}_title", $estatein_page ); ?></h3>
						<p class="achievement__copy"><?php estatein_the_html( "about_achievement{$estatein_i}_copy", $estatein_page ); ?></p>
					</li>
				<?php endfor; ?>
			</ul>
		</div>
	</section>

	<section class="section" id="how-it-works" aria-labelledby="steps-title">
		<div class="container">
			<?php
			get_template_part(
				'template-parts/section-head',
				null,
				array(
					'id'       => 'steps-title',
					'title'    => estatein_field( 'about_steps_title', $estatein_page ),
					'copy'     => estatein_field( 'about_steps_copy', $estatein_page ),
					'modifier' => 'narrow',
				)
			);
			?>
			<ol class="steps">
				<?php for ( $estatein_i = 1; $estatein_i <= 6; $estatein_i++ ) : ?>
					<li class="step">
						<?php /* translators: %s: two-digit step number. */ ?>
						<p class="step__number"><?php echo esc_html( sprintf( __( 'Step %s', 'estatein' ), estatein_pad( $estatein_i ) ) ); ?></p>
						<div class="step__body">
							<h3 class="step__title"><?php estatein_the_text( "about_step{$estatein_i}_title", $estatein_page ); ?></h3>
							<p class="step__copy"><?php estatein_the_html( "about_step{$estatein_i}_copy", $estatein_page ); ?></p>
						</div>
					</li>
				<?php endfor; ?>
			</ol>
		</div>
	</section>

	<section class="section" id="our-team" aria-labelledby="team-title">
		<div class="container">
			<?php
			get_template_part(
				'template-parts/section-head',
				null,
				array(
					'id'       => 'team-title',
					'title'    => estatein_field( 'about_team_title', $estatein_page ),
					'copy'     => estatein_field( 'about_team_copy', $estatein_page ),
					'modifier' => 'narrow',
				)
			);
			?>
			<ul class="team">
				<?php
				foreach ( estatein_get_items( 'team_member' ) as $post ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					setup_postdata( $post );
					get_template_part( 'template-parts/cards/team' );
				endforeach;
				wp_reset_postdata();
				?>
			</ul>
		</div>
	</section>

	<section class="section" id="our-clients" aria-labelledby="clients-title">
		<div class="container">
			<?php
			get_template_part(
				'template-parts/section-head',
				null,
				array(
					'id'       => 'clients-title',
					'title'    => estatein_field( 'about_clients_title', $estatein_page ),
					'copy'     => estatein_field( 'about_clients_copy', $estatein_page ),
					'modifier' => 'narrow',
				)
			);
			get_template_part(
				'template-parts/carousel',
				null,
				array(
					'posts'    => estatein_get_items( 'client' ),
					'card'     => 'client',
					'total'    => 10,
					'modifier' => 'carousel--two',
				)
			);
			?>
		</div>
	</section>
</main>

<?php
get_footer();
