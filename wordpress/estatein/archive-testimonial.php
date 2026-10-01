<?php
/**
 * All testimonials ("View All Testimonials"), using the Home section heading and cards.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();
$estatein_front = (int) get_option( 'page_on_front' );
?>

<main id="main">
	<section class="section section--first" id="testimonials" aria-labelledby="testimonials-title">
		<div class="container">
			<?php
			get_template_part(
				'template-parts/section-head',
				null,
				array(
					'id'    => 'testimonials-title',
					'title' => estatein_field( 'home_testimonials_title', $estatein_front ),
					'copy'  => estatein_field( 'home_testimonials_copy', $estatein_front ),
					'tag'   => 'h1',
				)
			);
			?>
			<?php if ( have_posts() ) : ?>
				<ul class="card-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						echo '<li id="testimonial-' . esc_attr( get_post_field( 'post_name' ) ) . '">';
						get_template_part( 'template-parts/cards/testimonial' );
						echo '</li>';
					endwhile;
					?>
				</ul>
				<?php the_posts_pagination( array( 'class' => 'pagination' ) ); ?>
			<?php else : ?>
				<p class="section-copy"><?php esc_html_e( 'No testimonials yet.', 'estatein' ); ?></p>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
