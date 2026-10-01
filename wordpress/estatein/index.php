<?php
/**
 * Blog index and the fallback template for every other listing
 * (the WordPress Loop with the design's card styles).
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_home() && ! is_front_page() ) {
	$estatein_title = single_post_title( '', false );
	$estatein_copy  = get_post_field( 'post_excerpt', (int) get_option( 'page_for_posts' ) );
} elseif ( is_search() ) {
	/* translators: %s: search query. */
	$estatein_title = sprintf( __( 'Search results for “%s”', 'estatein' ), get_search_query() );
	$estatein_copy  = '';
} elseif ( is_archive() ) {
	$estatein_title = wp_strip_all_tags( get_the_archive_title() );
	$estatein_copy  = wp_strip_all_tags( get_the_archive_description() );
} else {
	$estatein_title = __( 'Blog', 'estatein' );
	$estatein_copy  = '';
}
?>

<main id="main">
	<?php
	get_template_part(
		'template-parts/page-hero',
		null,
		array(
			'title' => $estatein_title,
			'copy'  => $estatein_copy,
		)
	);
	?>

	<section class="section" aria-label="<?php esc_attr_e( 'Posts', 'estatein' ); ?>">
		<div class="container">
			<?php if ( have_posts() ) : ?>
				<div class="card-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/cards/post' );
					endwhile;
					?>
				</div>
				<?php
				the_posts_pagination(
					array(
						'class'     => 'pagination',
						'prev_text' => estatein_icon( 'arrow-left' ) . '<span class="visually-hidden">' . esc_html__( 'Previous page', 'estatein' ) . '</span>',
						'next_text' => '<span class="visually-hidden">' . esc_html__( 'Next page', 'estatein' ) . '</span>' . estatein_icon( 'arrow-right' ),
					)
				);
				?>
			<?php else : ?>
				<div class="entry-content">
					<p><?php esc_html_e( 'Nothing found. Try another search.', 'estatein' ); ?></p>
					<?php get_search_form(); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
