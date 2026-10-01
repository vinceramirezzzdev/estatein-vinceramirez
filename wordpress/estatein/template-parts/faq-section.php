<?php
/**
 * "Frequently Asked Questions" section (Home and Property Details).
 * Copy from Customizer › Estatein › FAQ section; questions from the FAQ post type.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_all = array(
	'label' => estatein_option( 'faq_btn' ),
	'url'   => (string) get_post_type_archive_link( 'faq' ),
);
?>
<section class="section" id="faqs" aria-labelledby="faqs-title">
	<div class="container">
		<?php
		get_template_part(
			'template-parts/section-head',
			null,
			array(
				'id'     => 'faqs-title',
				'title'  => estatein_option( 'faq_title' ),
				'copy'   => estatein_option( 'faq_copy' ),
				'button' => $estatein_all,
			)
		);
		get_template_part(
			'template-parts/carousel',
			null,
			array(
				'posts'  => estatein_get_items( 'faq' ),
				'card'   => 'faq',
				'total'  => 10,
				'button' => $estatein_all,
			)
		);
		?>
	</div>
</section>
