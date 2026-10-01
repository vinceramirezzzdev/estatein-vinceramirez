<?php
/**
 * All FAQs with their full answers ("View All FAQ's" / "Read More" target).
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main">
	<section class="section section--first" id="faqs" aria-labelledby="faqs-title">
		<div class="container">
			<?php
			get_template_part(
				'template-parts/section-head',
				null,
				array(
					'id'    => 'faqs-title',
					'title' => estatein_option( 'faq_title' ),
					'copy'  => estatein_option( 'faq_copy' ),
					'tag'   => 'h1',
				)
			);
			?>
			<?php if ( have_posts() ) : ?>
				<ul class="card-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<li id="faq-<?php echo esc_attr( get_post_field( 'post_name' ) ); ?>">
							<article class="faq-card">
								<h2 class="faq-card__question"><?php the_title(); ?></h2>
								<div class="faq-card__answer entry-content"><?php the_content(); ?></div>
							</article>
						</li>
					<?php endwhile; ?>
				</ul>
				<?php the_posts_pagination( array( 'class' => 'pagination' ) ); ?>
			<?php else : ?>
				<p class="section-copy"><?php esc_html_e( 'No questions yet.', 'estatein' ); ?></p>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
