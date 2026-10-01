<?php
/**
 * Property gallery meta box.
 *
 * ACF's Gallery field is a Pro feature, so the gallery uses the native media
 * modal instead. Image IDs are stored in the "gallery" post meta as a
 * comma-separated list, in display order.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the meta box on properties.
 */
function estatein_add_gallery_meta_box() {
	add_meta_box( 'estatein-gallery', __( 'Property gallery', 'estatein' ), 'estatein_render_gallery_meta_box', 'property', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'estatein_add_gallery_meta_box' );

/**
 * Image IDs of a property's gallery.
 *
 * @param int $post_id Property ID.
 * @return int[]
 */
function estatein_gallery_ids( $post_id ) {
	$raw = (string) get_post_meta( $post_id, 'gallery', true );
	return array_values( array_filter( array_map( 'absint', explode( ',', $raw ) ) ) );
}

/**
 * Meta box markup.
 *
 * @param WP_Post $post Current property.
 */
function estatein_render_gallery_meta_box( $post ) {
	$ids = estatein_gallery_ids( $post->ID );
	wp_nonce_field( 'estatein_gallery', 'estatein_gallery_nonce' );
	?>
	<div class="estatein-gallery" data-estatein-gallery>
		<p class="description"><?php esc_html_e( 'The first two images are shown large; all images appear as thumbnails. Drag to reorder.', 'estatein' ); ?></p>
		<ul class="estatein-gallery__list" data-gallery-list>
			<?php foreach ( $ids as $id ) : ?>
				<li data-id="<?php echo esc_attr( $id ); ?>">
					<?php echo wp_get_attachment_image( $id, 'thumbnail' ); ?>
					<button type="button" class="estatein-gallery__remove" aria-label="<?php esc_attr_e( 'Remove image', 'estatein' ); ?>">&times;</button>
				</li>
			<?php endforeach; ?>
		</ul>
		<input type="hidden" name="estatein_gallery" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>" data-gallery-input>
		<p><button type="button" class="button" data-gallery-add><?php esc_html_e( 'Add images', 'estatein' ); ?></button></p>
	</div>
	<?php
}

/**
 * Save the gallery.
 *
 * @param int $post_id Property ID.
 */
function estatein_save_gallery( $post_id ) {
	if ( ! isset( $_POST['estatein_gallery_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['estatein_gallery_nonce'] ) ), 'estatein_gallery' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$ids = isset( $_POST['estatein_gallery'] ) ? array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['estatein_gallery'] ) ) ) ) ) : array();
	update_post_meta( $post_id, 'gallery', implode( ',', $ids ) );
}
add_action( 'save_post_property', 'estatein_save_gallery' );
