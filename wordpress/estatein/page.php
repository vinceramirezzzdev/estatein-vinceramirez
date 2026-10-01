<?php
/**
 * Default page template (e.g. Terms & Conditions, Privacy Policy): page hero + editor content.
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
				'copy'  => has_excerpt() ? get_the_excerpt() : '',
			)
		);
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'section' ); ?>>
			<div class="container container--narrow">
				<div class="entry-content">
					<?php
					the_content();
					wp_link_pages();
					?>
				</div>
				<?php
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
