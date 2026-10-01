<?php
/**
 * FAQ card: question (title), short answer (excerpt), "Read More" to the full
 * answer on the FAQ archive.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_url = (string) get_post_type_archive_link( 'faq' ) . '#faq-' . get_post_field( 'post_name' );
?>
<article class="faq-card">
	<h3 class="faq-card__question"><?php the_title(); ?></h3>
	<p class="faq-card__answer"><?php echo esc_html( get_the_excerpt() ); ?></p>
	<a class="btn btn--dark btn--sm faq-card__btn" href="<?php echo esc_url( $estatein_url ); ?>"><?php esc_html_e( 'Read More', 'estatein' ); ?></a>
</article>
