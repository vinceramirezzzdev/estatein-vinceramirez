<?php
/**
 * Single blog post.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main">
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part(
			'template-parts/page-hero',
			null,
			array(
				'title' => get_the_title(),
				/* translators: 1: date, 2: author. */
				'copy'  => sprintf( esc_html__( '%1$s · by %2$s', 'estatein' ), esc_html( get_the_date() ), esc_html( get_the_author() ) ),
			)
		);
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'section' ); ?>>
			<div class="container container--narrow">
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="entry-thumbnail"><?php the_post_thumbnail( 'estatein-wide', array( 'sizes' => '(min-width: 1024px) 900px, 100vw' ) ); ?></figure>
				<?php endif; ?>
				<div class="entry-content">
					<?php
					the_content();
					wp_link_pages(
						array(
							'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Page', 'estatein' ) . '">',
							'after'  => '</nav>',
						)
					);
					?>
				</div>
				<?php if ( has_tag() ) : ?>
					<p class="entry-tags"><?php the_tags( '', ' ' ); ?></p>
				<?php endif; ?>
				<?php
				the_post_navigation(
					array(
						'class'     => 'post-navigation',
						'prev_text' => '<span class="post-navigation__label">' . esc_html__( 'Previous', 'estatein' ) . '</span>%title',
						'next_text' => '<span class="post-navigation__label">' . esc_html__( 'Next', 'estatein' ) . '</span>%title',
					)
				);
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
			</div>
		</article>
	<?php endwhile; ?>
</main>

<?php
get_footer();
