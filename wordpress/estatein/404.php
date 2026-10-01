<?php
/**
 * 404 page.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="main">
	<?php
	get_template_part(
		'template-parts/page-hero',
		null,
		array(
			'title' => __( 'Page not found', 'estatein' ),
			'copy'  => __( 'The page you are looking for may have moved. Try a search or head back to the properties.', 'estatein' ),
		)
	);
	?>
	<section class="section">
		<div class="container container--narrow entry-content">
			<?php get_search_form(); ?>
			<p><a class="btn btn--primary" href="<?php echo esc_url( estatein_page_url( 'properties' ) ); ?>"><?php esc_html_e( 'Browse Properties', 'estatein' ); ?></a></p>
		</div>
	</section>
</main>

<?php
get_footer();
